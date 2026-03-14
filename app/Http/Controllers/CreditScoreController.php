<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesVerifiedName;
use App\Models\Setting;
use App\Models\VerificationRequest;
use App\Services\MetropolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreditScoreController extends Controller
{
    use ResolvesVerifiedName;

    public function downloadPdf(int $rid)
    {
        $req = VerificationRequest::findOrFail($rid);
        $crb = $req->result ? json_decode($req->result, true) : [];
        return view('credit-score-check-pdf', compact('req', 'crb'));
    }

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function index()
    {
        $price = Setting::servicePrice('credit-score-check');
        return view('credit-score-check', compact('price'));
    }

    // ─────────────────────────────────────────────
    // CONFIRM — format validation only, zero API calls
    // ─────────────────────────────────────────────
    public function confirm(Request $request)
    {
        $idNumber = trim($request->input('id_number', ''));
        if (!preg_match('/^\d{6,10}$/', $idNumber) || preg_match('/^0+$/', $idNumber)) {
            return redirect()->route('credit-score-check', ['error' => 'invalid_id']);
        }
        $price        = Setting::servicePrice('credit-score-check');
        $verifiedName = $this->resolveVerifiedName($idNumber, $request->input('verified_name', ''));
        [$recordId, $demoName] = $this->resolveRecord('credit-score-check', $idNumber, $price, $verifiedName);
        $firstName = explode(' ', trim($demoName))[0] ?: 'there';
        return view('credit-score-check-confirm', compact('idNumber', 'price', 'recordId', 'demoName', 'firstName'));
    }

    // ─────────────────────────────────────────────
    // RESULT — called after payment, runs ONE API call
    // MetropolService->creditScoreCheck() returns:
    //   [ 'score' => [...score fields...] ]
    // Metropol /score/consumer response fields:
    //   has_error, api_code, credit_score, category, as_at
    // ─────────────────────────────────────────────
    public function result(Request $request)
    {
        $rid = (int) $request->query('rid', 0);
        if (!$rid) return redirect()->route('credit-score-check');

        $req = VerificationRequest::find($rid);
        if (!$req || !in_array($req->status, ['paid', 'completed'])) {
            return redirect()->route('credit-score-check');
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

        // ── No valid cache — call API (atomic: only one concurrent request proceeds) ──
        if (!$crb) {
            $claimed = \DB::table('verification_requests')
                ->where('id', $rid)->where('status', 'paid')
                ->update(['status' => 'processing']);

            if (!$claimed) {
                $waited = 0;
                do { usleep(400000); $waited += 400; $req->refresh(); } while (!$req->result && $waited < 10000);
                if ($req->result) {
                    $crb = json_decode($req->result, true) ?? [];
                } else {
                    $crbError = 'Report is being processed. Please refresh in a moment.';
                    $crb = [];
                }
            }
        }

        if (!$crb && !$crbError) {
            $req->refresh();
            try {
                $metropol  = new MetropolService();
                $rawResult = $metropol->creditScoreCheck($req->national_id);

                $scoreData = $rawResult['score'] ?? $rawResult;
                $apiCode   = $scoreData['api_code'] ?? null;
                $hasError  = $scoreData['has_error'] ?? true;

                Log::info('[CREDIT-SCORE] fresh API call', [
                    'rid'          => $rid,
                    'national_id'  => $req->national_id,
                    'has_error'    => $hasError,
                    'credit_score' => $scoreData['credit_score'] ?? 'KEY_MISSING',
                    'category'     => $scoreData['category']     ?? 'KEY_MISSING',
                    'api_code'     => $apiCode ?? 'none',
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
                            $scrubNames = $crb['scrub']['names'][0] ?? null;
                            if ($scrubNames) $pollUpdate['full_name'] = $scrubNames;
                        }
                        if ($pollUpdate) { $req->update($pollUpdate); $req->refresh(); }
                    } else {
                        $crb = $rawResult;
                    }

                } else {
                    $req->update(['status' => 'completed', 'result' => json_encode($rawResult)]);
                    $req->refresh();
                    $crbError = 'The registry returned an unexpected response (code: ' . ($apiCode ?? 'unknown') . ').';
                    $crb = [];
                }

            } catch (\Exception $e) {
                $crbError = $e->getMessage();
                \DB::table('verification_requests')->where('id', $rid)
                    ->where('status', 'processing')->update(['status' => 'paid']);
                Log::error('[CREDIT-SCORE] exception', [
                    'rid'   => $rid,
                    'error' => $e->getMessage(),
                ]);
                $crb = [];
            }
        }

        if ($crb === null) $crb = [];

        // ─────────────────────────────────────────────
        // Extract display variables from API result
        // ─────────────────────────────────────────────
        $score_data   = $crb['score'] ?? $crb ?? [];
        $credit_score = $score_data['credit_score'] ?? null;
        $category     = $score_data['category']     ?? null;
        $as_at        = $score_data['as_at']         ?? null;
        $id_number    = $req->national_id;

        try {
            $score_date = $as_at ? \Carbon\Carbon::parse($as_at)->format('j F Y') : 'N/A';
        } catch (\Exception $e) {
            $score_date = $as_at ?? 'N/A';
        }

        $is_success  = !empty($credit_score) || (isset($score_data['has_error']) && $score_data['has_error'] === false);
        $report_date = $req->updated_at->format('j F Y, g:i A');

        return view('credit-score-check-result', compact(
            'req', 'crb', 'crbError',
            'id_number', 'credit_score', 'category',
            'score_date', 'is_success', 'report_date'
        ));
    }
}
