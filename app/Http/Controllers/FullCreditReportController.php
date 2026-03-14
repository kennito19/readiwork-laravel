<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesVerifiedName;
use App\Models\Setting;
use App\Models\VerificationRequest;
use App\Services\MetropolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FullCreditReportController extends Controller
{
    use ResolvesVerifiedName;

    public function index()
    {
        $price = Setting::servicePrice('full-credit-report');
        return view('full-credit-report', compact('price'));
    }

    public function confirm(Request $request)
    {
        $idNumber = trim($request->input('id_number', ''));
        if (!preg_match('/^\d{6,10}$/', $idNumber) || preg_match('/^0+$/', $idNumber)) {
            return redirect()->route('full-credit-report', ['error' => 'invalid_id']);
        }
        $price        = Setting::servicePrice('full-credit-report');
        $verifiedName = $this->resolveVerifiedName($idNumber, $request->input('verified_name', ''));
        [$recordId, $demoName] = $this->resolveRecord('full-credit-report', $idNumber, $price, $verifiedName);
        $firstName = explode(' ', trim($demoName))[0] ?: 'there';
        return view('full-credit-report-confirm', compact('idNumber', 'price', 'recordId', 'demoName', 'firstName'));
    }

    public function result(Request $request)
    {
        $rid = (int) $request->query('rid', 0);
        if (!$rid) return redirect()->route('full-credit-report');

        $req = VerificationRequest::find($rid);
        if (!$req || !in_array($req->status, ['paid', 'completed'])) {
            return redirect()->route('full-credit-report');
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
                $rawResult = $metropol->fullCreditReport($req->national_id);

                // fullCreditReport() returns ['credit_info' => <Type 12 response>]
                $ciData   = $rawResult['credit_info'] ?? [];
                $apiCode  = $ciData['api_code']  ?? null;
                $hasError = $ciData['has_error']  ?? true;

                Log::info('[FULL-CREDIT] fresh API call', [
                    'rid'              => $rid,
                    'national_id'      => $req->national_id,
                    'has_error'        => $hasError,
                    'delinquency_code' => $ciData['delinquency_code'] ?? 'KEY_MISSING',
                    'credit_score'     => $ciData['credit_score']     ?? 'KEY_MISSING',
                    'api_code'         => $apiCode ?? 'none',
                ]);

                $isSuccess = ($apiCode === 200 || $apiCode === null) && $hasError === false;

                if ($isSuccess) {
                    $identVerif = $ciData['identity_verification'] ?? [];
                    $scrubData  = $ciData['identity_scrub']        ?? [];

                    $saveFields = [
                        'result' => json_encode($rawResult),
                        'status' => 'completed',
                    ];

                    // Persist name: prefer identity_verification, fall back to scrub
                    $nameParts    = array_filter([
                        $identVerif['first_name'] ?? '',
                        $identVerif['other_name'] ?? '',
                        $identVerif['surname']    ?? '',
                    ]);
                    $verifiedName = trim(implode(' ', $nameParts));
                    if (!$verifiedName && !empty($scrubData['names'][0])) {
                        $verifiedName = $scrubData['names'][0];
                    }
                    if ($verifiedName) {
                        $saveFields['full_name'] = $verifiedName;
                    }
                    if (!empty($identVerif['dob']) && empty($req->dob)) {
                        $saveFields['dob'] = $identVerif['dob'];
                    }
                    if (!empty($identVerif['gender']) && empty($req->gender)) {
                        $saveFields['gender'] = $identVerif['gender'];
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
                        $crb        = json_decode($req->result, true) ?? [];
                        $pollUpdate = [];
                        if ($req->status !== 'completed') {
                            $pollUpdate['status'] = 'completed';
                        }
                        if (empty($req->full_name) || $req->full_name === 'Verified') {
                            $ciPoll    = $crb['credit_info'] ?? [];
                            $ivPoll    = $ciPoll['identity_verification'] ?? [];
                            $nameParts = array_filter([
                                $ivPoll['first_name'] ?? '',
                                $ivPoll['other_name'] ?? '',
                                $ivPoll['surname']    ?? '',
                            ]);
                            $pollName = trim(implode(' ', $nameParts))
                                     ?: ($ciPoll['identity_scrub']['names'][0] ?? null);
                            if ($pollName) $pollUpdate['full_name'] = $pollName;
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
                Log::error('[FULL-CREDIT] exception', [
                    'rid'   => $rid,
                    'error' => $e->getMessage(),
                ]);
                $crb = [];
            }
        }

        if ($crb === null) $crb = [];

        return view('full-credit-report-result', compact('req', 'crb', 'crbError'));
    }

    public function downloadPdf(int $rid)
    {
        $req = VerificationRequest::findOrFail($rid);
        $crb = $req->result ? json_decode($req->result, true) : [];
        return view('full-credit-report-pdf', compact('req', 'crb'));
    }
}
