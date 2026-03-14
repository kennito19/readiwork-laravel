<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    private string $consumerKey;
    private string $consumerSecret;
    private string $shortcode;
    private string $passkey;
    private string $callbackUrl;
    private string $apiUrl;

    public function __construct()
    {
        $this->consumerKey    = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortcode      = config('mpesa.shortcode');
        $this->passkey        = config('mpesa.passkey');
        $this->callbackUrl    = config('mpesa.callback_url');
        $this->apiUrl         = config('mpesa.api_url');
    }

    public function getToken(): ?string
    {
        $creds = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        $ch    = curl_init($this->apiUrl . '/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['Authorization: Basic ' . $creds],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            Log::error('[MPESA] Token error HTTP ' . $httpCode);
            return null;
        }
        return json_decode($response, true)['access_token'] ?? null;
    }

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) === 10 && $phone[0] === '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (strlen($phone) === 9 && ($phone[0] === '7' || $phone[0] === '1')) {
            $phone = '254' . $phone;
        }
        return $phone;
    }

    public function stkPush(string $phone, int $amount, int $requestId): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate with M-Pesa'];
        }

        $phone = $this->normalizePhone($phone);
        if (!preg_match('/^254[17]\d{8}$/', $phone)) {
            return ['success' => false, 'message' => 'Invalid phone number format'];
        }

        $timestamp = date('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $payload   = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $this->shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->callbackUrl,
            'AccountReference'  => 'READIWORK-' . $requestId,
            'TransactionDesc'   => 'Readiwork Credit Check',
        ];

        $ch = curl_init($this->apiUrl . '/mpesa/stkpush/v1/processrequest');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $token],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response) {
            return ['success' => false, 'message' => 'Failed to connect to M-Pesa'];
        }

        $result = json_decode($response, true);
        Log::info('[MPESA STK] HTTP:' . $httpCode, $result ?? []);

        if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
            return [
                'success'             => true,
                'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                'merchant_request_id' => $result['MerchantRequestID'] ?? null,
                'message'             => 'STK Push sent',
            ];
        }

        return [
            'success' => false,
            'message' => $result['errorMessage'] ?? $result['ResponseDescription'] ?? 'Payment request failed',
        ];
    }

    public function queryStkStatus(string $checkoutRequestId): ?array
    {
        $token = $this->getToken();
        if (!$token) return null;

        $timestamp = date('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $payload   = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $ch = curl_init($this->apiUrl . '/mpesa/stkpushquery/v1/query');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $token],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response || $httpCode !== 200) return null;

        $result = json_decode($response, true);
        Log::info('[STK_QUERY]', $result ?? []);

        if (isset($result['errorCode']) || !isset($result['ResultCode'])) return null;
        return $result;
    }
}
