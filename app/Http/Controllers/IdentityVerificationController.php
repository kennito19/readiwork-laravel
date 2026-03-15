<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesVerifiedName;
use App\Models\Setting;
use App\Models\VerificationRequest;
use App\Services\MetropolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IdentityVerificationController extends Controller
{
    use ResolvesVerifiedName;

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function index()
    {
        $price = Setting::servicePrice('identity-verification');
        return view('identity-verification', compact('price'));
    }

    // ─────────────────────────────────────────────
    // CONFIRM — format validation only, zero API calls
    // ─────────────────────────────────────────────
    public function confirm(Request $request)
    {
        $idNumber = trim($request->input('id_number', ''));

        if (!preg_match('/^\d{6,10}$/', $idNumber) || preg_match('/^0+$/', $idNumber)) {
            return redirect()->route('identity-verification', ['error' => 'invalid_id']);
        }

        $price = Setting::servicePrice('identity-verification');
        [$recordId, $demoName] = $this->resolveRecord('identity-verification', $idNumber, $price, '');

        $demoName  = 'Valued Customer';
        $firstName = 'there';

        return view('identity-verification-confirm', compact('idNumber', 'price', 'recordId', 'demoName', 'firstName'));
    }

    // ─────────────────────────────────────────────
    // RESULT — called after payment, runs API once
    // MetropolService->identityVerification() returns:
    //   [ 'verify' => [...identity fields...], 'scrub' => [...extended...] ]
    // ─────────────────────────────────────────────
    public function result(Request $request)
    {
        $rid = (int) $request->query('rid', 0);
        if (!$rid) return redirect()->route('identity-verification');

        // Always re-fetch fresh from DB — never trust a stale object
        $req = VerificationRequest::find($rid);
        if (!$req || !in_array($req->status, ['paid', 'completed', 'processing'])) {
            return redirect()->route('identity-verification');
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
            // Claim this record exclusively; if another request already claimed it, wait for its result
            $claimed = \DB::table('verification_requests')
                ->where('id', $rid)
                ->where('status', 'paid')
                ->update(['status' => 'processing']);

            if (!$claimed) {
                // Another concurrent request is processing — poll DB until it saves the result
                $waited = 0;
                do {
                    usleep(400000);
                    $waited += 400;
                    $req->refresh();
                } while (!$req->result && $waited < 10000);

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
                $rawResult = $metropol->identityVerification($req->national_id);

                $verify   = $rawResult['verify'] ?? [];
                $apiCode  = $verify['api_code'] ?? null;
                $hasError = $verify['has_error'] ?? true;

                Log::info('[ID-VERIFY] fresh API call', [
                    'rid'         => $rid,
                    'national_id' => $req->national_id,
                    'has_error'   => $hasError,
                    'first_name'  => $verify['first_name'] ?? 'KEY_MISSING',
                    'surname'     => $verify['surname']    ?? 'KEY_MISSING',
                    'api_code'    => $apiCode ?? 'none',
                ]);

                // api_code 200 (int) = success; string codes like 'E409' = errors
                $isSuccess = ($apiCode === 200 || $apiCode === null)
                          && $hasError === false;

                if ($isSuccess) {
                    $req->update([
                        'result' => json_encode($rawResult),
                        'status' => 'completed',
                    ]);

                    // ✅ Re-fetch from DB so $req reflects the saved result
                    $req->refresh();
                    $crb = json_decode($req->result, true);

                } elseif ($apiCode === 'E409') {
                    // ── E409: Metropol dedup window — another concurrent request
                    // already called the API. Poll DB until the winner saves the result.
                    $waited = 0;
                    do {
                        usleep(400000); // wait 400ms per iteration
                        $waited += 400;
                        $req->refresh();
                    } while (!$req->result && $waited < 2000); // max 2s total

                    if ($req->result) {
                        // Winner saved it — use it
                        $crb = json_decode($req->result, true) ?? [];
                        if ($req->status !== 'completed') {
                            $req->update(['status' => 'completed']);
                            $req->refresh();
                        }
                    } else {
                        // Still nothing after 2s — use whatever rawResult we got
                        // (scrub data is often still valid even when verify is E409)
                        $crb = $rawResult;
                        $req->update(['status' => 'completed', 'result' => json_encode($rawResult)]);
                        $req->refresh();
                    }

                } else {
                    // API returned an error — still mark completed so it doesn't retry forever
                    $req->update(['status' => 'completed', 'result' => json_encode($rawResult)]);
                    $req->refresh();
                    $crbError = 'The registry returned an unexpected response (code: ' . ($apiCode ?? 'unknown') . ').';
                    $crb = [];
                }

            } catch (\Exception $e) {
                $crbError = $e->getMessage();
                // Restore to paid so user can retry later
                \DB::table('verification_requests')->where('id', $rid)
                    ->where('status', 'processing')
                    ->update(['status' => 'paid']);
                Log::error('[ID-VERIFY] exception', [
                    'rid'   => $rid,
                    'error' => $e->getMessage(),
                ]);
                $crb = [];
            }
        }

        if ($crb === null) $crb = [];

        // ─────────────────────────────────────────────
        // Extract display variables from API result
        // NOTE: Metropol 'last_name' is always null — 'surname' is the real last name field
        // ─────────────────────────────────────────────
        $verify_data = $crb['verify'] ?? $crb ?? [];
        $scrub_data  = $crb['scrub']  ?? [];

        $first_name = trim($verify_data['first_name'] ?? '');
        $last_name  = trim($verify_data['surname']    ?? $verify_data['last_name'] ?? '');
        $other_name = trim($verify_data['other_name'] ?? '');
        $full_name  = trim(implode(' ', array_filter([$first_name, $other_name, $last_name])));

        // Fallback: scrub names array (populated even when verify returns E409)
        if (!$full_name && !empty(($scrub_data['names'] ?? [])[0])) {
            $full_name = $scrub_data['names'][0];
        }
        // Fallback: previously saved DB column
        if (!$full_name) {
            $full_name = $req->full_name ?? '';
        }

        $id_number   = $req->national_id;
        $dob_raw     = $verify_data['dob'] ?? $verify_data['date_of_birth'] ?? null;
        $gender      = $verify_data['gender'] ?? null;
        $citizenship = $verify_data['citizenship'] ?? 'Kenyan';
        $serial_no   = $verify_data['serial_number'] ?? null;
        $district    = $verify_data['district'] ?? null;

        // Scrub fallbacks (all scrub fields are arrays per Metropol API docs)
        if (!$dob_raw && !empty(($scrub_data['date_of_being'] ?? [])[0])) {
            $dob_raw = $scrub_data['date_of_being'][0];
        }
        if (!$gender && !empty(($scrub_data['gender'] ?? [])[0])) {
            $gender = $scrub_data['gender'][0];
        }

        try {
            $dob_fmt = $dob_raw ? \Carbon\Carbon::parse($dob_raw)->format('j F Y') : 'N/A';
        } catch (\Exception $e) {
            $dob_fmt = $dob_raw ?? 'N/A';
        }

        $gender_label = match(strtoupper((string) $gender)) {
            'M' => 'Male', 'F' => 'Female', default => $gender ?: 'N/A',
        };

        $is_verified = !empty($first_name)
                    || !empty($last_name)
                    || (isset($verify_data['has_error']) && $verify_data['has_error'] === false);

        $report_date = $req->updated_at->format('j F Y, g:i A');

        $scrub_phones   = $scrub_data['phone']            ?? [];
        $scrub_emails   = $scrub_data['email']            ?? [];
        $scrub_postal   = $scrub_data['postal_address']   ?? [];
        $scrub_physical = $scrub_data['physical_address'] ?? [];
        $scrub_employ   = $scrub_data['employment']       ?? [];

        // ── Persist individual columns to DB (only when not yet saved) ──
        if ($is_verified && !$req->first_name) {
            $req->update([
                'first_name' => $first_name  ?: null,
                'last_name'  => $last_name   ?: null,
                'full_name'  => $full_name   ?: null,
                'dob'        => $dob_raw     ?: null,
                'gender'     => $gender_label !== 'N/A' ? $gender_label : null,
            ]);
        }

        return view('identity-verification-result', compact(
            'req', 'crb', 'crbError',
            'full_name', 'id_number', 'dob_fmt', 'gender_label',
            'citizenship', 'serial_no', 'district',
            'is_verified', 'report_date',
            'scrub_phones', 'scrub_emails', 'scrub_postal', 'scrub_physical', 'scrub_employ'
        ));
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD PDF
    // ─────────────────────────────────────────────
    public function downloadPdf(Request $request, int $rid)
    {
        $req = VerificationRequest::find($rid);

        if (!$req || !in_array($req->status, ['paid', 'completed', 'processing'])) {
            abort(403, 'Report not available.');
        }

        $crb = $req->result ? json_decode($req->result, true) : [];
        if (!is_array($crb)) $crb = [];

        $verify_data = $crb['verify'] ?? $crb ?? [];
        $scrub_data  = $crb['scrub']  ?? [];

        // NOTE: Metropol 'last_name' is always null — 'surname' is the real last name field
        $first_name  = trim($verify_data['first_name'] ?? '');
        $last_name   = trim($verify_data['surname']    ?? $verify_data['last_name'] ?? '');
        $other_name  = trim($verify_data['other_name'] ?? '');
        $full_name   = trim(implode(' ', array_filter([$first_name, $other_name, $last_name])))
                     ?: ($req->full_name ?? 'Applicant');

        $id_number   = $req->national_id;
        $dob_raw     = $verify_data['dob'] ?? $verify_data['date_of_birth'] ?? null;
        $gender      = $verify_data['gender'] ?? null;
        $citizenship = $verify_data['citizenship'] ?? 'Kenyan';
        $serial_no   = $verify_data['serial_number'] ?? null;
        $district    = $verify_data['district'] ?? null;

        $scrub_phones   = $scrub_data['phone']            ?? [];
        $scrub_emails   = $scrub_data['email']            ?? [];
        $scrub_postal   = $scrub_data['postal_address']   ?? [];
        $scrub_physical = $scrub_data['physical_address'] ?? [];
        $scrub_employ   = $scrub_data['employment']       ?? [];

        // Fallbacks from scrub data
        if (!$full_name && !empty(($scrub_data['names'] ?? [])[0])) {
            $full_name = $scrub_data['names'][0];
        }
        if (!$dob_raw && !empty(($scrub_data['date_of_being'] ?? [])[0])) {
            $dob_raw = $scrub_data['date_of_being'][0];
        }
        if (!$gender && !empty(($scrub_data['gender'] ?? [])[0])) {
            $gender = $scrub_data['gender'][0];
        }

        try {
            $dob_fmt = $dob_raw ? \Carbon\Carbon::parse($dob_raw)->format('j F Y') : 'N/A';
        } catch (\Exception $e) {
            $dob_fmt = $dob_raw ?? 'N/A';
        }

        $gender_label = match(strtoupper((string) $gender)) {
            'M' => 'Male', 'F' => 'Female', default => $gender ?: 'N/A',
        };

        $is_verified = !empty($first_name)
                    || !empty($last_name)
                    || (isset($verify_data['has_error']) && $verify_data['has_error'] === false);

        $report_date = now()->format('j F Y, g:i A');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('identity-verification-pdf', compact(
            'req', 'full_name', 'id_number', 'dob_fmt', 'gender_label',
            'citizenship', 'serial_no', 'district', 'is_verified', 'report_date',
            'scrub_phones', 'scrub_emails', 'scrub_postal', 'scrub_physical', 'scrub_employ'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'dpi'                  => 96,
        ]);

        $filename = 'readiwork-identity-' . $id_number . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────────
    // DEBUG — remove after issue is resolved
    // Visit: /identity-verification/debug?rid=XX
    // Only works when APP_ENV != production
    // ─────────────────────────────────────────────
    public function debug(Request $request)
    {
        if (app()->isProduction()) abort(404);

        $rid = (int) $request->query('rid', 0);
        $req = VerificationRequest::find($rid);

        if (!$req) {
            return response()->json(['error' => 'VerificationRequest not found', 'rid' => $rid], 404);
        }

        $freshResult = null;
        $exception   = null;

        try {
            $metropol    = new MetropolService();
            $freshResult = $metropol->identityVerification($req->national_id);
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        return response()->json([
            'rid'             => $rid,
            'national_id'     => $req->national_id,
            'status'          => $req->status,
            'stored_result'   => $req->result ? json_decode($req->result, true) : null,
            'fresh_api_call'  => $freshResult,
            'exception'       => $exception,
            'parsed' => $freshResult ? [
                'has_error'  => $freshResult['verify']['has_error']  ?? 'missing',
                'first_name' => $freshResult['verify']['first_name'] ?? 'missing',
                'surname'    => $freshResult['verify']['surname']    ?? 'missing',
                'last_name'  => $freshResult['verify']['last_name']  ?? 'missing',
                'dob'        => $freshResult['verify']['dob']        ?? 'missing',
                'gender'     => $freshResult['verify']['gender']     ?? 'missing',
            ] : null,
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}