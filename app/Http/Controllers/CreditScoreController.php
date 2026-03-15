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

        if (!in_array($req->status, ['paid', 'completed', 'processing'])) {
            abort(403, 'Report not available.');
        }

        $crb = $req->result ? json_decode($req->result, true) : [];
        if (!is_array($crb)) $crb = [];

        $identity    = $crb['identity']    ?? $crb ?? [];
        $score_data  = $crb['score']       ?? [];
        $delinquency = $crb['delinquency'] ?? [];
        $report_data = $crb['report']      ?? [];
        $scrub_data  = $crb['scrub']       ?? [];

        $ident_data = $identity['identity'] ?? $identity;
        $first_name = trim($ident_data['first_name'] ?? $identity['first_name'] ?? '');
        $last_name  = trim($ident_data['surname']    ?? $ident_data['last_name'] ?? $identity['last_name'] ?? $identity['surname'] ?? '');
        $other_name = trim($ident_data['other_name'] ?? $identity['other_name'] ?? '');
        $full_name  = trim(implode(' ', array_filter([$first_name, $other_name, $last_name])))
                    ?: ($req->full_name ?? 'Applicant');

        if (!$full_name && !empty(($scrub_data['names'] ?? [])[0])) {
            $full_name = $scrub_data['names'][0];
        }

        $id_number    = $req->national_id;
        $credit_score = (int)($score_data['credit_score'] ?? 0);
        $category     = $score_data['category']     ?? $score_data['score_band'] ?? null;
        $as_at        = $score_data['as_at']         ?? null;
        $comparatives = $score_data['score_comparatives'] ?? $score_data['comparatives'] ?? [];
        $score_3m     = (int)($comparatives['last_3_months']  ?? 0);
        $score_6m     = (int)($comparatives['last_6_months']  ?? 0);
        $score_12m    = (int)($comparatives['last_12_months'] ?? 0);
        $score_best   = (int)($comparatives['best']  ?? 0);
        $score_worst  = (int)($comparatives['worst'] ?? 0);
        $ppi          = $score_data['ppi']  ?? $score_data['payment_performance_index'] ?? null;
        $pod          = $score_data['probability_of_default'] ?? $score_data['pod'] ?? null;

        $delinquency_code = (string)($delinquency['delinquency_code'] ?? $delinquency['deliquency_code'] ?? '');
        $outstanding_bal  = (float)($delinquency['outstanding_balance'] ?? $report_data['total_outstanding_balance'] ?? 0);
        $no_facilities    = (int)($delinquency['no_of_facilities'] ?? $report_data['total_accounts'] ?? 0);
        $overdue_amount   = (float)($report_data['total_overdue_amount'] ?? $delinquency['overdue_amount'] ?? 0);

        $code_info = [
            '001' => ['No Adverse Credit History',         '#16a34a', '#edfaf3', '#a3d9b8', false],
            '002' => ['Good Standing, All Loans Serviced', '#0e7c7c', '#e6f3f3', '#a3c4c4', false],
            '003' => ['Caution, Some Late Payments',       '#d97706', '#fffbeb', '#fde68a', false],
            '004' => ['Delinquent, Defaulted Loan(s)',      '#dc2626', '#fef2f2', '#fca5a5', true],
            '005' => ['Written Off, Debt Written Off',      '#991b1b', '#fef2f2', '#fca5a5', true],
        ];
        [$delinq_label, $delinq_color, $delinq_bg, $delinq_border, $delinq_bad] =
            $code_info[$delinquency_code] ?? ['Status Unknown', '#6b7280', '#f8fafc', '#d1dce6', false];

        $score_tiers = [
            [700, 'Excellent', '#16a34a'], [600, 'Good', '#0e7c7c'],
            [500, 'Fair', '#d97706'],      [400, 'Poor', '#ea580c'],
            [0,   'Very Poor', '#dc2626'],
        ];
        $tier_label = 'No Score'; $tier_color = '#7a8fa6';
        if ($credit_score >= 200) {
            foreach ($score_tiers as [$min, $lbl, $col]) {
                if ($credit_score >= $min) { $tier_label = $lbl; $tier_color = $col; break; }
            }
        }
        $score_pct = $credit_score > 0 ? round(($credit_score - 200) / 700 * 100) : 0;

        $ppi_map   = ['M1'=>['#16a34a',90],'M2'=>['#2563eb',78],'M3'=>['#0891b2',65],'M4'=>['#d97706',50],'M5'=>['#f97316',35],'M6'=>['#dc2626',22],'M7'=>['#b91c1c',12],'M8'=>['#7f1d1d',5],'M9'=>['#450a0a',2]];
        $ppi_key   = strtoupper((string)$ppi);
        $ppi_color = $ppi_map[$ppi_key][0] ?? '#7a8fa6';
        $ppi_pct   = $ppi_map[$ppi_key][1] ?? 50;
        $pod_val   = is_numeric($pod) ? round((float)$pod * 100, 1) . '%' : ($pod ?? 'N/A');
        $pod_pct   = is_numeric($pod) ? min(100, (float)$pod * 100) : 0;
        $pod_color = $pod_pct < 20 ? '#16a34a' : ($pod_pct < 50 ? '#d97706' : '#dc2626');

        $delinq_expl_text = [
            '001' => 'No lender has ever reported a problem with your repayments. Your CRB record is completely clean.',
            '002' => 'All your credit facilities are being serviced on time. You have an excellent repayment track record.',
            '003' => 'Some late or missed payments appear on your record. Not yet blacklisted but this is hurting your score.',
            '004' => 'One or more of your loans are unpaid. A lender has flagged you on the CRB. This blocks new loan approvals.',
            '005' => 'A lender has written off your debt as uncollectable. This is the most severe CRB listing.',
        ][$delinquency_code] ?? 'Your CRB status has been retrieved.';

        $report_date = now()->format('j F Y, g:i A');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('credit-score-check-pdf', compact(
            'req', 'full_name', 'id_number', 'report_date',
            'credit_score', 'category', 'as_at', 'score_pct',
            'tier_label', 'tier_color',
            'score_3m', 'score_6m', 'score_12m', 'score_best', 'score_worst',
            'delinquency_code', 'delinq_label', 'delinq_color', 'delinq_bg',
            'delinq_border', 'delinq_bad', 'delinq_expl_text',
            'outstanding_bal', 'no_facilities', 'overdue_amount',
            'ppi', 'ppi_color', 'ppi_pct', 'pod', 'pod_val', 'pod_pct', 'pod_color'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'dpi'                  => 96,
        ]);

        $filename = 'readiwork-credit-score-' . $id_number . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
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
        if (!$req || !in_array($req->status, ['paid', 'completed', 'processing'])) {
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
