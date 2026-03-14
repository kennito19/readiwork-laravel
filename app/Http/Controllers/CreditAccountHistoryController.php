<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesVerifiedName;
use App\Models\Setting;
use App\Models\VerificationRequest;
use App\Services\MetropolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreditAccountHistoryController extends Controller
{
    use ResolvesVerifiedName;

    public function index()
    {
        $price = Setting::servicePrice('credit-account-history');
        return view('credit-account-history', compact('price'));
    }

    public function confirm(Request $request)
    {
        $idNumber = trim($request->input('id_number', ''));
        if (!preg_match('/^\d{6,10}$/', $idNumber) || preg_match('/^0+$/', $idNumber)) {
            return redirect()->route('credit-account-history', ['error' => 'invalid_id']);
        }
        $price        = Setting::servicePrice('credit-account-history');
        $verifiedName = $this->resolveVerifiedName($idNumber, $request->input('verified_name', ''));
        [$recordId, $demoName] = $this->resolveRecord('credit-account-history', $idNumber, $price, $verifiedName);
        $firstName = explode(' ', trim($demoName))[0] ?: 'there';
        return view('credit-account-history-confirm', compact('idNumber', 'price', 'recordId', 'demoName', 'firstName'));
    }

    public function result(Request $request)
    {
        $rid = (int) $request->query('rid', 0);
        if (!$rid) return redirect()->route('credit-account-history');

        $req = VerificationRequest::find($rid);
        if (!$req || !in_array($req->status, ['paid', 'completed'])) {
            return redirect()->route('credit-account-history');
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
                $rawResult = $metropol->creditAccountHistory($req->national_id);

                // Primary success signal: accounts_info (report_type 22)
                $aiData   = $rawResult['accounts_info'] ?? $rawResult;
                $apiCode  = $aiData['api_code']  ?? null;
                $hasError = $aiData['has_error']  ?? true;

                Log::info('[CREDIT-ACCOUNT-HISTORY] fresh API call', [
                    'rid'              => $rid,
                    'national_id'      => $req->national_id,
                    'has_error'        => $hasError,
                    'delinquency_code' => $aiData['delinquency_code'] ?? 'KEY_MISSING',
                    'api_code'         => $apiCode ?? 'none',
                ]);

                $isSuccess = ($apiCode === 200 || $apiCode === null) && $hasError === false;

                if ($isSuccess) {
                    $scrubData = $rawResult['scrub'] ?? [];
                    $saveFields = [
                        'result' => json_encode($rawResult),
                        'status' => 'completed',
                    ];
                    // Persist name from scrub or accounts_info
                    $summaryName = $aiData['customer_name'] ?? null;
                    $scrubName   = $scrubData['names'][0] ?? null;
                    $resolvedName = $summaryName ?: $scrubName;
                    if ($resolvedName) {
                        $saveFields['full_name'] = $resolvedName;
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
                            $pollAi   = $crb['accounts_info'] ?? [];
                            $pollName = $pollAi['customer_name'] ?? ($crb['scrub']['names'][0] ?? null);
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
                Log::error('[CREDIT-ACCOUNT-HISTORY] exception', [
                    'rid'   => $rid,
                    'error' => $e->getMessage(),
                ]);
                $crb = [];
            }
        }

        if ($crb === null) $crb = [];

        return view('credit-account-history-result', compact('req', 'crb', 'crbError'));
    }

    public function downloadPdf(int $rid)
    {
        $req = VerificationRequest::findOrFail($rid);

        if (!in_array($req->status, ['paid', 'completed'])) {
            abort(403, 'Report not available.');
        }

        $crb = $req->result ? json_decode($req->result, true) : [];
        if (!is_array($crb)) $crb = [];

        $ai        = $crb['accounts_info']   ?? [];
        $summary   = $crb['accounts_summary'] ?? [];
        $scrub     = $crb['scrub']            ?? [];

        $full_name  = $ai['customer_name'] ?? $scrub['names'][0] ?? $req->full_name ?? 'Applicant';
        $id_number  = $req->national_id;

        // Verified name from summary
        $vn         = $summary['verified_name'] ?? [];
        if (!$full_name || $full_name === 'Applicant') {
            $full_name = trim(implode(' ', array_filter([$vn['first_name'] ?? '', $vn['other_name'] ?? '', $vn['surname'] ?? ''])))
                      ?: $full_name;
        }

        $dob_raw      = $scrub['date_of_being'][0] ?? null;
        $gender_raw   = $scrub['gender'][0] ?? null;
        $gender_label = $gender_raw ? match(strtoupper((string)$gender_raw)) {
            'M' => 'Male', 'F' => 'Female', default => $gender_raw
        } : 'N/A';
        try {
            $dob_fmt = $dob_raw ? \Carbon\Carbon::parse($dob_raw)->format('j F Y') : 'N/A';
        } catch (\Exception $e) {
            $dob_fmt = $dob_raw ?? 'N/A';
        }

        $delinquency_code      = (string)($ai['delinquency_code'] ?? '');
        $total_outstanding     = (float)($ai['total_outstanding_amount'] ?? 0);
        $total_outstanding_npa = (float)($ai['total_outstanding_npa'] ?? 0);
        $total_overdue         = (float)($ai['total_overdue_amount'] ?? 0);
        $highest_dias          = (int)($ai['highest_days_in_arrears'] ?? 0);
        $max_credit_score      = (int)($ai['max_credit_score'] ?? 0);
        $min_credit_score      = (int)($ai['min_credit_score'] ?? 0);
        $monthly_score         = $ai['monthly_score'] ?? [];

        $accounts = $ai['account_info'] ?? [];
        usort($accounts, function ($a, $b) {
            $aA = strtolower($a['account_status_name'] ?? '') === 'active';
            $bA = strtolower($b['account_status_name'] ?? '') === 'active';
            if ($aA !== $bA) return $bA <=> $aA;
            return strcmp($b['date_opened'] ?? '', $a['date_opened'] ?? '');
        });

        // Summary credit_info
        $ci = $summary['credit_info'] ?? [];
        $active_generic    = (int)($ci['generic_account_count'] ?? 0);
        $closed_generic    = (int)($ci['generic_account_count_closed'] ?? 0);
        $active_mobile     = (int)($ci['mobile_account_count_active'] ?? 0);
        $closed_mobile     = (int)($ci['mobile_account_count_closed'] ?? 0);
        $monthly_instalment = (float)($ci['total_monthly_instalment_generic'] ?? 0);

        $code_info = [
            '001' => ['No Adverse History',       '#16a34a', false],
            '002' => ['Good Standing',             '#0e7c7c', false],
            '003' => ['Good, Some Late Payments',  '#d97706', false],
            '004' => ['Delinquent Account(s)',      '#dc2626', true],
            '005' => ['Written Off',               '#991b1b', true],
        ];
        [$code_label, $code_color, $is_delinquent] = $code_info[$delinquency_code] ?? ['Status Unknown', '#6b7280', false];

        $report_date = now()->format('j F Y, g:i A');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('credit-account-history-pdf', compact(
            'req', 'full_name', 'id_number', 'dob_fmt', 'gender_label',
            'delinquency_code', 'code_label', 'code_color', 'is_delinquent',
            'total_outstanding', 'total_outstanding_npa', 'total_overdue',
            'highest_dias', 'max_credit_score', 'min_credit_score', 'monthly_score',
            'accounts', 'active_generic', 'closed_generic', 'active_mobile', 'closed_mobile',
            'monthly_instalment', 'report_date'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'dpi'                  => 96,
        ]);

        $filename = 'readiwork-account-history-' . $id_number . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}
