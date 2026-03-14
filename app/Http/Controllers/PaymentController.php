<?php

namespace App\Http\Controllers;

use App\Models\VerificationRequest;
use App\Services\MpesaService;
use App\Services\MetropolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function stkPush(Request $request)
    {
        $data     = $request->json()->all();
        $phone    = trim($data['phone']     ?? '');
        $amount   = (int)($data['amount']   ?? 0);
        $service  = trim($data['service']   ?? 'loan-eligibility');
        $idNo     = trim($data['id']        ?? '');
        $recordId = (int)($data['record_id'] ?? 0);

        if (!$phone || !$amount || !$idNo) {
            return response()->json(['success' => false, 'message' => 'Missing required fields'], 400);
        }

        $mpesa = new MpesaService();
        $phone = $mpesa->normalizePhone($phone);
        if (!preg_match('/^254[17]\d{8}$/', $phone)) {
            return response()->json(['success' => false, 'message' => 'Invalid phone number']);
        }

        // Reuse existing record or create new
        $requestId = 0;
        if ($recordId > 0) {
            $updated = VerificationRequest::where('id', $recordId)
                ->where('national_id', $idNo)
                ->update(['phone' => $phone, 'payment_phone' => $phone, 'status' => 'pending_payment']);
            if ($updated) $requestId = $recordId;
        }

        if (!$requestId) {
            $rec = VerificationRequest::create([
                'service'    => $service,
                'national_id'=> $idNo,
                'phone'      => $phone,
                'price'      => $amount,
                'status'     => 'pending_payment',
            ]);
            $requestId = $rec->id;
        }

        $result = $mpesa->stkPush($phone, $amount, $requestId);

        if ($result['success']) {
            VerificationRequest::where('id', $requestId)->update([
                'checkout_request_id' => $result['checkout_request_id'] ?? null,
                'merchant_request_id' => $result['merchant_request_id'] ?? null,
            ]);
            return response()->json(['success' => true, 'request_id' => $requestId, 'message' => 'STK Push sent. Enter your M-Pesa PIN.']);
        }

        VerificationRequest::where('id', $requestId)->update([
            'status'        => 'payment_failed',
            'payment_error' => $result['message'],
        ]);
        return response()->json(['success' => false, 'message' => $result['message']]);
    }

    public function checkStatus(Request $request)
    {
        $rid = $request->query('rid');
        if (!$rid || !ctype_digit((string)$rid)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid request']);
        }

        $row = VerificationRequest::select(
            'id', 'status', 'result', 'mpesa_receipt_number', 'payment_error',
            'checkout_request_id', 'national_id'
        )->find((int)$rid);

        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Not found']);
        }

        // 'processing' = payment confirmed, Metropol call in progress — tell frontend to keep waiting
        if ($row->status === 'processing') {
            return response()->json([
                'status'        => 'paid',
                'has_result'    => false,
                'has_receipt'   => !empty($row->mpesa_receipt_number),
                'payment_error' => null,
            ]);
        }

        if ($row->status === 'pending_payment' && $row->checkout_request_id) {
            $mpesa = new MpesaService();
            $stkStatus = $mpesa->queryStkStatus($row->checkout_request_id);
            if ($stkStatus !== null) {
                $resultCode = (int)$stkStatus['ResultCode'];
                if ($resultCode === 0) {
                    $row->update(['status' => 'paid', 'mpesa_receipt_number' => 'STK-CONFIRMED-' . time()]);
                    $row->status = 'paid';
                } elseif ($resultCode !== 4999) {
                    // Any code that isn't 0 (success) or 4999 (still processing) is a failure
                    $errMap = [
                        1    => 'Insufficient funds',
                        17   => 'Not registered for M-Pesa',
                        1032 => 'Cancelled — please try again',
                        1037 => 'No PIN entered — please try again',
                        1025 => 'Phone unreachable — please try again',
                        2001 => 'Wrong PIN entered',
                    ];
                    $errMsg = $errMap[$resultCode] ?? 'Payment not completed (code ' . $resultCode . ')';
                    $row->update(['status' => 'payment_failed', 'payment_error' => $errMsg]);
                    $row->status        = 'payment_failed';
                    $row->payment_error = $errMsg;
                }
            }
        }

        return response()->json([
            'status'        => $row->status,
            'has_result'    => !empty($row->result),
            'has_receipt'   => !empty($row->mpesa_receipt_number),
            'payment_error' => $row->payment_error,
        ]);
    }

    public function callback(Request $request)
    {
        $raw = $request->getContent();
        Log::info('[CALLBACK] ' . $raw);

        try {
            $data = json_decode($raw, true);

            if (!isset($data['Body']['stkCallback']['CheckoutRequestID'])) {
                return response('{"ResultCode":0,"ResultDesc":"Accepted"}')->header('Content-Type', 'application/json');
            }

            $checkout = $data['Body']['stkCallback']['CheckoutRequestID'];
            $code     = (int)$data['Body']['stkCallback']['ResultCode'];

            $req = VerificationRequest::where('checkout_request_id', $checkout)->first();
            if (!$req) {
                return response('{"ResultCode":0,"ResultDesc":"Accepted"}')->header('Content-Type', 'application/json');
            }

            if ($code === 0) {
                $receipt   = 'PAID-' . time();
                $payPhone  = null;
                $payAmount = null;
                $transDate = null;

                foreach ($data['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [] as $item) {
                    match($item['Name']) {
                        'MpesaReceiptNumber' => ($receipt    = $item['Value']),
                        'PhoneNumber'        => ($payPhone   = $item['Value']),
                        'Amount'             => ($payAmount  = $item['Value']),
                        'TransactionDate'    => ($transDate  = preg_replace(
                            '/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/',
                            '$1-$2-$3 $4:$5:$6',
                            $item['Value']
                        )),
                        default => null,
                    };
                }

                $req->update([
                    'status'               => 'paid',
                    'mpesa_receipt_number' => $receipt,
                    'payment_phone'        => $payPhone,
                    'payment_amount'       => $payAmount,
                    'payment_date'         => $transDate,
                ]);

                // Fetch CRB report
                try {
                    $service    = $req->service;
                    $metropol   = new MetropolService();
                    $crb        = match($service) {
                        'identity-verification'  => $metropol->identityVerification($req->national_id),
                        'credit-score-check'     => $metropol->creditScoreCheck($req->national_id),
                        'crb-blacklist-check'    => $metropol->blacklistCheck($req->national_id),
                        'full-credit-report'     => $metropol->fullCreditReport($req->national_id),
                        'credit-account-history' => $metropol->creditAccountHistory($req->national_id),
                        default                  => $metropol->loanEligibility($req->national_id),
                    };

                    $identity = $crb['identity'] ?? $crb['verify'] ?? [];
                    $parts = array_filter([
                        $identity['first_name'] ?? '',
                        $identity['other_name'] ?? $identity['other_names'] ?? '',
                        $identity['surname']    ?? $identity['last_name']   ?? '',
                    ]);
                    $fullName = trim(implode(' ', $parts)) ?: null;

                    $req->update([
                        'status'    => 'completed',
                        'result'    => json_encode($crb),
                        'full_name' => $req->full_name ?: $fullName,
                        'dob'       => $req->dob    ?: ($identity['dob'] ?? $identity['date_of_birth'] ?? null),
                        'gender'    => $req->gender ?: ($identity['gender'] ?? null),
                    ]);
                } catch (\Exception $e) {
                    Log::error('[CALLBACK CRB] ' . $e->getMessage());
                }

            } else {
                $errors = [1=>'Insufficient funds', 17=>'Not registered for M-PESA', 1032=>'Cancelled by user', 2001=>'Wrong PIN'];
                $msg    = $errors[$code] ?? "Payment failed (code $code)";
                $req->update(['status' => 'payment_failed', 'payment_error' => $msg]);
            }

        } catch (\Exception $e) {
            Log::error('[CALLBACK] ' . $e->getMessage());
        }

        return response('{"ResultCode":0,"ResultDesc":"Accepted"}')->header('Content-Type', 'application/json');
    }
}
