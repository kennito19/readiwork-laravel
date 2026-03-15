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

        if (!in_array($req->status, ['paid', 'completed', 'processing'])) {
            abort(403, 'Report not available.');
        }

        $crb = $req->result ? json_decode($req->result, true) : [];
        if (!is_array($crb)) $crb = [];

        $delinquency  = $crb['delinquency']  ?? [];
        $scrub_data   = $crb['scrub']        ?? [];
        $credit_info  = $crb['credit_info']  ?? [];

        $full_name   = $scrub_data['names'][0] ?? $req->full_name ?? 'Applicant';
        $id_number   = $req->national_id;
        $dob_raw     = $scrub_data['date_of_being'][0] ?? null;
        $gender_raw  = $scrub_data['gender'][0] ?? null;
        $gender_label = $gender_raw ? match(strtoupper((string)$gender_raw)) {
            'M' => 'Male', 'F' => 'Female', default => $gender_raw
        } : 'N/A';
        $scrub_phones = $scrub_data['phone']      ?? [];
        $scrub_emails = $scrub_data['email']      ?? [];
        $scrub_employ = $scrub_data['employment'] ?? [];

        try {
            $dob_fmt = $dob_raw ? \Carbon\Carbon::parse($dob_raw)->format('j F Y') : 'N/A';
        } catch (\Exception $e) {
            $dob_fmt = $dob_raw ?? 'N/A';
        }

        $delinquency_code    = (string)($delinquency['delinquency_code'] ?? $delinquency['deliquency_code'] ?? '');
        $delinquency_summary = $delinquency['delinquency_summary'] ?? null;
        $outstanding_bal     = (float)($delinquency['outstanding_balance'] ?? $credit_info['total_outstanding_balance'] ?? 0);
        $no_facilities       = (int)($delinquency['no_of_facilities'] ?? $credit_info['total_accounts'] ?? 0);
        $overdue_amount      = (float)($credit_info['total_overdue_amount'] ?? $delinquency['overdue_amount'] ?? 0);
        $npa_accounts        = (int)($credit_info['npa_accounts'] ?? $credit_info['total_npa'] ?? 0);
        $performing_accounts = (int)($credit_info['performing_accounts'] ?? max(0, $no_facilities - $npa_accounts));
        $accounts            = $credit_info['account_info'] ?? $credit_info['credit_accounts'] ?? $credit_info['accounts'] ?? [];

        $code_info = [
            '001' => ['No Adverse Credit History',         '#16a34a', '#edfaf3', '#a3d9b8', false],
            '002' => ['Good Standing, All Loans Serviced', '#0e7c7c', '#e6f3f3', '#a3c4c4', false],
            '003' => ['Caution, Some Late Payments',       '#d97706', '#fffbeb', '#fde68a', false],
            '004' => ['Delinquent, Defaulted Loan(s)',      '#dc2626', '#fef2f2', '#fca5a5', true],
            '005' => ['Written Off, Debt Written Off',      '#991b1b', '#fef2f2', '#fca5a5', true],
        ];
        [$code_label, $code_color, $code_bg, $code_border, $is_blacklisted] =
            $code_info[$delinquency_code] ?? ['Status Unknown', '#6b7280', '#f8fafc', '#e2e8f0', false];

        $delinq_expl_text = [
            '001' => 'No lender has ever reported a problem with your repayments. Your CRB record is completely clean.',
            '002' => 'All your credit facilities are being serviced on time. You have an excellent repayment track record.',
            '003' => 'Some late or missed payments appear on your record. You are not yet blacklisted but this may affect loan approvals.',
            '004' => 'One or more of your loans are unpaid and a lender has flagged you on the CRB. This is blocking new loan approvals.',
            '005' => 'A lender has written off your debt as uncollectable. This is the most severe CRB listing and requires immediate attention.',
        ][$delinquency_code] ?? 'Your CRB delinquency status has been retrieved from Kenya CRB.';

        $report_date = now()->format('j F Y, g:i A');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('crb-blacklist-check-pdf', compact(
            'req', 'full_name', 'id_number', 'dob_fmt', 'gender_label',
            'scrub_phones', 'scrub_emails', 'scrub_employ',
            'delinquency_code', 'delinquency_summary', 'code_label', 'code_color',
            'code_bg', 'code_border', 'is_blacklisted', 'delinq_expl_text',
            'outstanding_bal', 'no_facilities', 'overdue_amount',
            'npa_accounts', 'performing_accounts', 'accounts', 'report_date'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'dpi'                  => 96,
        ]);

        $filename = 'readiwork-crb-blacklist-' . $id_number . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
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
        if (!$req || !in_array($req->status, ['paid', 'completed', 'processing'])) {
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
