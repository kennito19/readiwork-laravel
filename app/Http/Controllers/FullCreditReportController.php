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
        if (!$req || !in_array($req->status, ['paid', 'completed', 'processing'])) {
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
                        $req->update(['status' => 'completed', 'result' => json_encode($rawResult)]);
                        $req->refresh();
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

        if (!in_array($req->status, ['paid', 'completed', 'processing'])) {
            abort(403, 'Report not available.');
        }

        $crb = $req->result ? json_decode($req->result, true) : [];
        if (!is_array($crb)) $crb = [];

        $ci      = isset($crb['credit_info']) ? $crb['credit_info'] : $crb;
        $idv     = $ci['identity_verification'] ?? [];
        $scrub   = $ci['identity_scrub']        ?? [];
        $accts   = $ci['account_info']          ?? [];
        $sectors = $ci['lender_sector']         ?? [];

        $fullName = trim(implode(' ', array_filter([$idv['first_name'] ?? '', $idv['other_name'] ?? '', $idv['surname'] ?? ''])));
        if (!$fullName && !empty($scrub['names'][0])) $fullName = $scrub['names'][0];
        if (!$fullName) $fullName = $req->full_name ?? 'N/A';
        $idNum  = $req->national_id ?? ($idv['id_number'] ?? 'N/A');
        $dob    = $idv['dob']    ?? ($req->dob    ?? null);
        $gender = $idv['gender'] ?? ($req->gender ?? null);

        $score        = $ci['credit_score']     ?? null;
        $deliqCode    = $ci['delinquency_code'] ?? null;
        $isDelinquent = ($deliqCode === 'D' || $deliqCode === '1' || $deliqCode === 1);
        $trxId        = $ci['trx_id']           ?? null;

        $scoreColor = '#6b7280'; $scoreLabel = 'No Score'; $scoreBand = 'No credit history found';
        if ($score !== null) {
            if ($score >= 700)     { $scoreColor = '#16a34a'; $scoreLabel = 'Excellent'; $scoreBand = 'Very low credit risk, preferred borrower'; }
            elseif ($score >= 600) { $scoreColor = '#0e7c7c'; $scoreLabel = 'Good';      $scoreBand = 'Below average credit risk'; }
            elseif ($score >= 500) { $scoreColor = '#d97706'; $scoreLabel = 'Fair';      $scoreBand = 'Average credit risk'; }
            elseif ($score >= 400) { $scoreColor = '#ea580c'; $scoreLabel = 'Poor';      $scoreBand = 'Above average credit risk'; }
            else                   { $scoreColor = '#dc2626'; $scoreLabel = 'Very Poor'; $scoreBand = 'High credit risk'; }
        }

        usort($accts, function ($a, $b) {
            $aA = in_array(strtolower($a['account_status'] ?? ''), ['a', 'active']);
            $bA = in_array(strtolower($b['account_status'] ?? ''), ['a', 'active']);
            if ($aA !== $bA) return $bA <=> $aA;
            return strcmp($b['opening_date'] ?? '', $a['opening_date'] ?? '');
        });

        $totalAccts       = count($accts);
        $totalActive      = count(array_filter($accts, fn($a) => in_array(strtolower($a['account_status'] ?? ''), ['a', 'active'])));
        $totalOutstanding = array_sum(array_column($accts, 'outstanding_balance'));
        $totalArrears     = array_sum(array_column($accts, 'arrears_amount'));

        $_enqR = $ci['no_of_enquiries']         ?? 0;
        $_appR = $ci['no_of_credit_applications']?? 0;
        $_bcR  = $ci['no_of_bounced_cheques']   ?? 0;
        $noEnq = is_array($_enqR) ? count($_enqR) : (int)$_enqR;
        $noApp = is_array($_appR) ? count($_appR) : (int)$_appR;
        $noBnc = is_array($_bcR)  ? count($_bcR)  : (int)$_bcR;
        $isGuar = $ci['is_guarantor'] ?? false;
        $hasFrd = $ci['has_fraud']    ?? false;

        $maxSecBal = !empty($sectors) ? max(array_map(fn($s) => (float)($s['outstanding_balance'] ?? $s['balance'] ?? 0), $sectors)) : 1;
        if ($maxSecBal <= 0) $maxSecBal = 1;

        $reportDate = $req->updated_at->format('j F Y, g:i A');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('full-credit-report-pdf', compact(
            'req', 'fullName', 'idNum', 'dob', 'gender', 'scrub',
            'score', 'deliqCode', 'isDelinquent', 'trxId',
            'scoreColor', 'scoreLabel', 'scoreBand',
            'accts', 'totalAccts', 'totalActive', 'totalOutstanding', 'totalArrears',
            'noEnq', 'noApp', 'noBnc', 'isGuar', 'hasFrd',
            'sectors', 'maxSecBal', 'reportDate'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'dpi'                  => 96,
        ]);

        $filename = 'readiwork-full-credit-report-' . $idNum . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}
