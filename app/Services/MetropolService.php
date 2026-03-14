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
    private bool   $sandbox;

    public function __construct()
    {
        $this->baseUrl    = config('metropol.base_url');
        $this->port       = config('metropol.port');
        $this->version    = config('metropol.version');
        $this->publicKey  = config('metropol.public_key');
        $this->privateKey = config('metropol.private_key');
        $this->sandbox    = (bool) config('metropol.sandbox', false);
    }

    private function timestamp(): string
    {
        $micro = microtime(true);
        return gmdate('YmdHis', (int) $micro) . sprintf('%06d', ($micro - floor($micro)) * 1000000);
    }

    // ── Sandbox mock — returns realistic fake data without hitting the API ──
    private function sandboxResponse(string $endpoint, array $payload): array
    {
        $id = $payload['identity_number'] ?? '00000000';
        Log::info('[METROPOL SANDBOX] ' . $endpoint, ['identity_number' => $id]);

        $baseVerify = [
            'has_error'     => false,
            'api_code'      => 200,
            'first_name'    => 'JOHN',
            'other_name'    => 'KAMAU',
            'surname'       => 'MWANGI',
            'last_name'     => null,
            'dob'           => '1990-05-14',
            'gender'        => 'M',
            'citizenship'   => 'Kenyan',
            'serial_number' => 'N' . substr($id, 0, 6),
            'district'      => 'NAIROBI',
            'identity_number' => $id,
            'identity_type' => '001',
        ];

        $baseScrub = [
            'has_error'       => false,
            'api_code'        => 200,
            'names'           => ['JOHN KAMAU MWANGI'],
            'phone'           => ['+254712345678'],
            'email'           => ['john.mwangi@example.co.ke'],
            'postal_address'  => ['P.O. Box 1234-00100, Nairobi'],
            'physical_address'=> ['Westlands, Nairobi'],
            'employment'      => ['Software Developer - Readiwork Ltd'],
            'gender'          => ['M'],
            'date_of_being'   => ['1990-05-14'],
        ];

        $baseScore = [
            'has_error'        => false,
            'api_code'         => 200,
            'score'            => 742,
            'grade'            => 'A',
            'recommendation'   => 'LEND',
            'positive_factors' => ['No delinquent accounts', 'Regular payment history'],
            'negative_factors' => [],
            'identity_number'  => $id,
        ];

        $baseDelinquency = [
            'has_error'           => false,
            'api_code'            => 200,
            'is_delinquent'       => false,
            'total_npas'          => 0,
            'listed_institutions' => [],
            'identity_number'     => $id,
        ];

        $baseCreditInfo = [
            'has_error'            => false,
            'api_code'             => 200,
            'identity'             => $baseVerify,
            'summary'              => [
                'performing_accounts'    => 3,
                'non_performing_accounts'=> 0,
                'total_outstanding'      => 450000,
                'total_limit'            => 1200000,
            ],
            'accounts'             => [
                [
                    'institution'   => 'KCB Bank Kenya',
                    'product'       => 'Personal Loan',
                    'status'        => 'Performing',
                    'outstanding'   => 250000,
                    'limit'         => 500000,
                    'opened_date'   => '2022-03-01',
                    'last_payment'  => '2026-02-28',
                ],
                [
                    'institution'   => 'Equity Bank',
                    'product'       => 'Overdraft',
                    'status'        => 'Performing',
                    'outstanding'   => 200000,
                    'limit'         => 700000,
                    'opened_date'   => '2021-07-15',
                    'last_payment'  => '2026-02-25',
                ],
            ],
            'identity_number'      => $id,
        ];

        return match(true) {
            str_contains($endpoint, '/identity/verify')     => $baseVerify,
            str_contains($endpoint, '/identity/scrub')      => $baseScrub,
            str_contains($endpoint, '/score/consumer')      => $baseScore,
            str_contains($endpoint, '/delinquency/status')  => $baseDelinquency,
            str_contains($endpoint, '/report/credit_info')  => $baseCreditInfo,
            default                                         => ['has_error' => false, 'api_code' => 200],
        };
    }

    public function request(string $endpoint, array $payload): array
    {
        if ($this->sandbox) {
            return $this->sandboxResponse($endpoint, $payload);
        }

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
