<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\VerificationRequest;
use App\Services\MetropolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoanEligibilityController extends Controller
{
    public function index()
    {
        $price = Setting::servicePrice('loan-eligibility');
        return view('loan-eligibility', compact('price'));
    }

    public function confirm(Request $request)
    {
        $idNumber = trim($request->input('id_number', ''));
        if (!preg_match('/^\d{6,10}$/', $idNumber) || preg_match('/^0+$/', $idNumber)) {
            return redirect()->route('loan-eligibility', ['error' => 'invalid_id']);
        }

        $verifiedName = trim($request->input('verified_name', ''));
        $amountsPool  = [15000, 20000, 25000, 30000, 35000, 40000, 50000, 60000, 75000, 100000, 120000, 150000, 200000];

        // Auto-lookup name from existing records
        if (!$verifiedName) {
            $existing = VerificationRequest::where('national_id', $idNumber)
                ->whereNotNull('full_name')->where('full_name', '!=', '')
                ->latest()->value('full_name');
            $verifiedName = $existing ?? '';
        }

        // Fall back to live identity lookup
        if (!$verifiedName) {
            try {
                $metropol = new MetropolService();
                $ir = $metropol->request('/identity/verify', [
                    'report_type' => 1, 'identity_number' => $idNumber, 'identity_type' => '001',
                ]);
                if (empty($ir['has_error'])) {
                    $fn = $ir['identity']['first_name'] ?? $ir['first_name'] ?? '';
                    $on = $ir['identity']['other_name'] ?? $ir['other_name'] ?? '';
                    $sn = $ir['identity']['surname']    ?? $ir['last_name']  ?? '';
                    $verifiedName = trim(implode(' ', array_filter([$fn, $on, $sn])));
                }
            } catch (\Exception $e) {}
        }

        // Reuse or create record
        $existing = VerificationRequest::where('national_id', $idNumber)
            ->where('service', 'loan-eligibility')
            ->whereIn('status', ['pending_payment', 'pending', 'payment_failed'])
            ->latest()->first();

        if ($existing && (float)$existing->quoted_amount > 0) {
            $recordId   = $existing->id;
            $demoAmount = (float)$existing->quoted_amount;
            $demoName   = $verifiedName ?: ($existing->full_name ?: 'Valued Customer');
            if ($verifiedName && $verifiedName !== $existing->full_name) {
                $existing->update(['full_name' => $verifiedName]);
            }
        } else {
            $demoAmount = $amountsPool[array_rand($amountsPool)];
            $demoName   = $verifiedName ?: 'Valued Customer';
            $rec = VerificationRequest::create([
                'service'       => 'loan-eligibility',
                'national_id'   => $idNumber,
                'full_name'     => $demoName ?: null,
                'price'         => 1,
                'quoted_amount' => $demoAmount,
                'status'        => 'pending_payment',
            ]);
            $recordId = $rec->id;
        }

        $price     = Setting::servicePrice('loan-eligibility');
        $firstName = explode(' ', trim($demoName))[0] ?: 'there';

        return view('loan-eligibility-confirm', compact(
            'idNumber', 'demoName', 'demoAmount', 'price', 'recordId', 'firstName'
        ));
    }

    public function result(Request $request)
    {
        $rid = (int) $request->query('rid', 0);
        if (!$rid) return redirect()->route('loan-eligibility');

        $req = VerificationRequest::find($rid);
        if (!$req || !in_array($req->status, ['paid', 'completed'])) {
            return redirect()->route('loan-eligibility');
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
                $rawResult = $metropol->loanEligibility($req->national_id);

                // loanEligibility() returns ['credit_info' => <Type 12 response>]
                $ciData   = $rawResult['credit_info'] ?? [];
                $apiCode  = $ciData['api_code']  ?? null;
                $hasError = $ciData['has_error']  ?? true;

                Log::info('[LOAN-ELIGIBILITY] fresh API call', [
                    'rid'              => $rid,
                    'national_id'      => $req->national_id,
                    'has_error'        => $hasError,
                    'delinquency_code' => $ciData['delinquency_code'] ?? 'KEY_MISSING',
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
                    // ── E409: concurrent request won the race — poll DB for result ──
                    $waited = 0;
                    do {
                        usleep(400000); // 400ms
                        $waited += 400;
                        $req->refresh();
                    } while (!$req->result && $waited < 8000); // max 8s

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
                Log::error('[LOAN-ELIGIBILITY] exception', [
                    'rid'   => $rid,
                    'error' => $e->getMessage(),
                ]);
                $crb = [];
            }
        }

        if ($crb === null) $crb = [];

        // ── Extract display variables from credit_info (Type 12 structure) ──
        $ci         = $crb['credit_info'] ?? [];
        $identity   = $ci['identity_verification'] ?? [];
        $scrub_data = $ci['identity_scrub']        ?? [];
        $summary    = $ci;

        $nameParts = array_filter([
            $identity['first_name'] ?? '',
            $identity['other_name'] ?? '',
            $identity['surname']    ?? '',
        ]);
        $full_name = trim(implode(' ', $nameParts));
        if (!$full_name && !empty($scrub_data['names'][0])) {
            $full_name = $scrub_data['names'][0];
        }
        if (!$full_name) {
            $full_name = $req->full_name ?? 'Applicant';
        }

        $id_number     = $req->national_id;
        $quoted_amount = $req->quoted_amount ?? 0;
        $report_date   = $req->updated_at->format('j F Y, g:i A');

        return view('loan-eligibility-result', compact(
            'req', 'crb', 'crbError',
            'full_name', 'id_number', 'quoted_amount', 'report_date',
            'summary', 'identity', 'scrub_data'
        ));
    }

    public function downloadPdf(int $rid)
    {
        $req = VerificationRequest::findOrFail($rid);
        $crb = $req->result ? json_decode($req->result, true) : [];
        return view('loan-eligibility-pdf', compact('req', 'crb'));
    }
}
