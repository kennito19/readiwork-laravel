<?php

namespace App\Http\Controllers\Concerns;

use App\Models\VerificationRequest;
use App\Services\MetropolService;

trait ResolvesVerifiedName
{
    /**
     * Get verified name from POST data, DB history, or Metropol API.
     * Returns an empty string if the name cannot be resolved.
     */
    protected function resolveVerifiedName(string $idNumber, ?string $fromPost = null): string
    {
        $name = trim($fromPost ?? '');

        if (!$name) {
            $name = VerificationRequest::where('national_id', $idNumber)
                ->whereNotNull('full_name')->where('full_name', '!=', '')
                ->latest()->value('full_name') ?? '';
        }

        if (!$name) {
            try {
                $metropol = new MetropolService();
                $ir = $metropol->request('/identity/verify', [
                    'report_type'     => 1,
                    'identity_number' => $idNumber,
                    'identity_type'   => '001',
                ]);
                if (empty($ir['has_error'])) {
                    $fn = $ir['identity']['first_name'] ?? $ir['first_name'] ?? '';
                    $on = $ir['identity']['other_name'] ?? $ir['identity']['other_names']
                        ?? $ir['other_name'] ?? $ir['other_names'] ?? '';
                    $sn = $ir['identity']['surname'] ?? $ir['identity']['last_name']
                        ?? $ir['surname'] ?? $ir['last_name'] ?? '';
                    $name = trim(implode(' ', array_filter([$fn, $on, $sn])));
                }
            } catch (\Exception $e) {
                // ignore; name stays empty
            }
        }

        return $name;
    }

    /**
     * Reuse an existing pending_payment record or create a new one.
     * Returns [$recordId, $demoName].
     */
    protected function resolveRecord(
        string $service,
        string $idNumber,
        int    $price,
        string $verifiedName = ''
    ): array {
        $existing = VerificationRequest::where('national_id', $idNumber)
            ->where('service', $service)
            ->whereIn('status', ['pending_payment', 'pending', 'payment_failed'])
            ->latest()->first();

        $demoName = $verifiedName ?: 'Valued Customer';

        if ($existing) {
            $recordId = $existing->id;
            if ($verifiedName && $verifiedName !== ($existing->full_name ?? '')) {
                $existing->update(['full_name' => $verifiedName]);
            }
        } else {
            $rec = VerificationRequest::create([
                'service'       => $service,
                'national_id'   => $idNumber,
                'full_name'     => $verifiedName ?: null,
                'price'         => $price,
                'quoted_amount' => $price,
                'status'        => 'pending_payment',
            ]);
            $recordId = $rec->id;
        }

        return [$recordId, $demoName];
    }
}
