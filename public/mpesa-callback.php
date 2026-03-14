<?php
/**
 * M-Pesa STK Callback Relay
 * Upload to: https://apply.readi.work/callback.php
 *
 * This file receives callbacks from Safaricom and forwards them
 * to your actual Laravel app (local via ngrok, or production).
 *
 * ⚠️  Update FORWARD_TO below whenever your ngrok URL changes.
 *     Run: php artisan ngrok:tunnel  → copy the https URL → paste below + /api/callback
 */

define('FORWARD_TO', 'https://YOUR-NGROK-URL.ngrok-free.app/api/callback');
// Example: 'https://abc123.ngrok-free.app/api/callback'
// For production server: 'https://readi.work/api/callback'

// ── Log helper ────────────────────────────────────────────────────────────────
function logMsg(string $msg): void
{
    $dir = __DIR__ . '/callback-logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    @file_put_contents(
        $dir . '/mpesa-' . date('Y-m-d') . '.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL,
        FILE_APPEND
    );
}

// ── Always respond OK to Safaricom immediately ────────────────────────────────
function respond(): void
{
    header('Content-Type: application/json');
    echo '{"ResultCode":0,"ResultDesc":"Accepted"}';
}

$raw = file_get_contents('php://input');
logMsg('RECEIVED: ' . $raw);

// ── Forward to Laravel ────────────────────────────────────────────────────────
$target = FORWARD_TO;

if (empty($target) || str_contains($target, 'YOUR-NGROK-URL')) {
    logMsg('ERROR: FORWARD_TO not configured. Edit callback.php and set the URL.');
    respond();
    exit;
}

$ch = curl_init($target);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $raw,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    logMsg("FORWARD ERROR: {$curlErr}");
} else {
    logMsg("FORWARDED to {$target} → HTTP {$httpCode} | Response: {$response}");
}

respond();
exit;
