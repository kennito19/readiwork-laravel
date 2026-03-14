<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesVerifiedName;
use App\Models\Setting;
use App\Models\VerificationRequest;
use App\Services\MetropolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CrbBlacklistController extends Controller
{
    use ResolvesVerifiedName;

    public function downloadPdf(int $rid)
    {
        $req = VerificationRequest::findOrFail($rid);
        $crb = $req->result ? json_decode($req->result, true) : [];
        return view('crb-blacklist-check-pdf', compact('req', 'crb'));
    }

    public function index()
    {
        $price = Setting::servicePrice('crb-blacklist-check');
        return view('crb-blacklist-check', compact('price'));
    }

    public function confirm(Request $request)
    {
        $idNumber = trim($request->input('id_number', ''));
        if (!preg_match('/^\d{6,10}$/', $idNumber) || preg_match('/^0+$/', $idNumber)) {
            return redirect()->route('crb-blacklist-check', ['error' => 'invalid_id']);
        }
        $price        = Setting::servicePrice('crb-blacklist-check');
        $verifiedName = $this->resolveVerifiedName($idNumber, $request->input('verified_name', ''));
        [$recordId, $demoName] = $this->resolveRecord('crb-blacklist-check', $idNumber, $price, $verifiedName);
        $firstName = explode(' ', trim($demoName))[0] ?: 'there';
        return view('crb-blacklist-check-confirm', compact('idNumber', 'price', 'recordId', 'demoName', 'firstName'));
    }

    public function result(Request $request)
    {
        $rid = (int) $request->query('rid', 0);
        if (!$rid) return redirect()->route('crb-blacklist-check');

        $req = VerificationRequest::find($rid);
        if (!$req || !in_array($req->status, ['paid', 'completed'])) {
            return redirect()->route('crb-blacklist-check');
        }

        $crb      = null;
        $crbError = null;

        // ── Use cached result if already completed ──
        if ($req->status === 'completed' && $req->result) {
            $decoded = json_decode($req->result, true);
            if (is_array($decoded) && !empty($decoded)) {
                $crb = $decoded;
            }
        }

        // ── No valid cache — call API ──
        if (!$crb) {
            try {
                $metropol  = new MetropolService();
                $rawResult = $metropol->blacklistCheck($req->national_id);

                // Primary success signal comes from delinquency endpoint
                $delinqData = $rawResult['delinquency'] ?? $rawResult;
                $apiCode    = $delinqData['api_code']  ?? null;
                $hasError   = $delinqData['has_error'] ?? true;

                Log::info('[CRB-BLACKLIST] fresh API call', [
                    'rid'              => $rid,
                    'national_id'      => $req->national_id,
                    'has_error'        => $hasError,
                    'delinquency_code' => $delinqData['delinquency_code'] ?? 'KEY_MISSING',
                    'api_code'         => $apiCode ?? 'none',
                ]);

                $isSuccess = ($apiCode === 200 || $apiCode === null)
                          && $hasError === false;

                if ($isSuccess) {
                    $scrubData  = $rawResult['scrub'] ?? [];
                    $saveFields = [
                        'result' => json_encode($rawResult),
                        'status' => 'completed',
                    ];
                    if (!empty($scrubData['names'][0])) {
                        $saveFields['full_name'] = $scrubData['names'][0];
                    }
                    if (!empty($scrubData['date_of_being'][0]) && empty($req->dob)) {
                        $saveFields['dob'] = $scrubData['date_of_being'][0];
                    }
                    if (!empty($scrubData['gender'][0]) && empty($req->gender)) {
                        $saveFields['gender'] = $scrubData['gender'][0];
                    }
                    $req->update($saveFields);
                    $req->refresh();
                    $crb = json_decode($req->result, true);

                } elseif ($apiCode === 'E409') {
                    // ── E409: dedup window — poll until winner saves result ──
                    $waited = 0;
                    do {
                        usleep(400000);
                        $waited += 400;
                        $req->refresh();
                    } while (!$req->result && $waited < 8000);

                    if ($req->result) {
                        $crb = json_decode($req->result, true) ?? [];
                        $pollUpdate = [];
                        if ($req->status !== 'completed') {
                            $pollUpdate['status'] = 'completed';
                        }
                        if (empty($req->full_name) || $req->full_name === 'Verified') {
                            $scrubName = $crb['scrub']['names'][0] ?? null;
                            if ($scrubName) $pollUpdate['full_name'] = $scrubName;
                        }
                        if ($pollUpdate) { $req->update($pollUpdate); $req->refresh(); }
                    } else {
                        $crb = $rawResult;
                    }

                } else {
                    $crbError = 'The registry returned an unexpected response. Please try again.';
                    $crb = [];
                }

            } catch (\Exception $e) {
                $crbError = $e->getMessage();
                Log::error('[CRB-BLACKLIST] exception', [
                    'rid'   => $rid,
                    'error' => $e->getMessage(),
                ]);
                $crb = [];
            }
        }

        if ($crb === null) $crb = [];

        return view('crb-blacklist-check-result', compact('req', 'crb', 'crbError'));
    }
}
