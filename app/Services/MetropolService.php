<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class MetropolService
{
    private string $baseUrl;
    private string $port;
    private string $version;
    private string $publicKey;
    private string $privateKey;

    public function __construct()
    {
        $this->baseUrl    = config('metropol.base_url');
        $this->port       = config('metropol.port');
        $this->version    = config('metropol.version');
        $this->publicKey  = config('metropol.public_key');
        $this->privateKey = config('metropol.private_key');
    }

    private function timestamp(): string
    {
        $micro = microtime(true);
        return gmdate('YmdHis', (int) $micro) . sprintf('%06d', ($micro - floor($micro)) * 1000000);
    }

    public function request(string $endpoint, array $payload): array
    {
        $timestamp = $this->timestamp();
        $jsonBody  = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $hash      = hash('sha256', $this->privateKey . $jsonBody . $this->publicKey . $timestamp);
        $url       = $this->baseUrl . ':' . $this->port . '/' . $this->version . $endpoint;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-METROPOL-REST-API-KEY: '       . $this->publicKey,
                'X-METROPOL-REST-API-HASH: '      . $hash,
                'X-METROPOL-REST-API-TIMESTAMP: ' . $timestamp,
            ],
            CURLOPT_POSTFIELDS     => $jsonBody,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Metropol connection error: ' . $err);
        }

        $decoded = json_decode($response, true);
        if ($decoded === null) {
            throw new Exception('Metropol returned invalid JSON: ' . substr($response, 0, 200));
        }

        Log::info('[METROPOL] ' . $endpoint, $decoded);
        return $decoded;
    }

    public function loanEligibility(string $idNumber): array
    {
        $credit_info = [];
        try {
            $credit_info = $this->request('/report/credit_info', [
                'report_type'     => 12,
                'identity_number' => $idNumber,
                'identity_type'   => '001',
                'loan_amount'     => 50000,
                'report_reason'   => 4,
            ]);
        } catch (Exception $e) {
            Log::error('[METROPOL] /report/credit_info (loan-eligibility) failed: ' . $e->getMessage());
        }
        return compact('credit_info');
    }

    public function identityVerification(string $idNumber): array
    {
        $verify = $this->request('/identity/verify', [
            'report_type' => 1, 'identity_number' => $idNumber, 'identity_type' => '001',
        ]);
        $scrub = [];
        try {
            $scrub = $this->request('/identity/scrub', [
                'report_type' => 6, 'identity_number' => $idNumber, 'identity_type' => '001',
            ]);
        } catch (Exception $e) {
            Log::error('[METROPOL] /identity/scrub failed: ' . $e->getMessage());
        }
        return compact('verify', 'scrub');
    }

    public function creditScoreCheck(string $idNumber): array
    {
        $score = $this->request('/score/consumer', [
            'report_type'     => 3,
            'identity_number' => $idNumber,
            'identity_type'   => '001',
            'mobile_score'    => false,
        ]);
        $scrub = [];
        try {
            $scrub = $this->request('/identity/scrub', [
                'report_type' => 6, 'identity_number' => $idNumber, 'identity_type' => '001',
            ]);
        } catch (Exception $e) {
            Log::error('[METROPOL] /identity/scrub (credit score) failed: ' . $e->getMessage());
        }
        return compact('score', 'scrub');
    }

    public function blacklistCheck(string $idNumber): array
    {
        $delinquency = $this->request('/delinquency/status', [
            'report_type' => 2, 'identity_number' => $idNumber, 'identity_type' => '001', 'loan_amount' => 50000,
        ]);
        $scrub = [];
        try {
            $scrub = $this->request('/identity/scrub', [
                'report_type' => 6, 'identity_number' => $idNumber, 'identity_type' => '001',
            ]);
        } catch (Exception $e) {
            Log::error('[METROPOL] /identity/scrub (blacklist) failed: ' . $e->getMessage());
        }
        $credit_info = [];
        try {
            $credit_info = $this->request('/report/credit_info', [
                'report_type' => 12, 'identity_number' => $idNumber, 'identity_type' => '001',
                'loan_amount' => 50000, 'report_reason' => 1,
            ]);
        } catch (Exception $e) {
            Log::error('[METROPOL] /report/credit_info (blacklist) failed: ' . $e->getMessage());
        }
        return compact('delinquency', 'scrub', 'credit_info');
    }

    public function fullCreditReport(string $idNumber): array
    {
        $credit_info = [];
        try {
            $credit_info = $this->request('/report/credit_info', [
                'report_type'     => 12,
                'identity_number' => $idNumber,
                'identity_type'   => '001',
                'loan_amount'     => 50000,
                'report_reason'   => 1,
            ]);
        } catch (Exception $e) {
            Log::error('[METROPOL] /report/credit_info (full-credit) failed: ' . $e->getMessage());
        }
        return compact('credit_info');
    }
}
