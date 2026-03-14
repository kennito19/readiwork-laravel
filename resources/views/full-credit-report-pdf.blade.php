@php
/* ── Unpack (same as result page) ── */
$ci      = isset($crb['credit_info']) ? $crb['credit_info'] : $crb;
$idv     = $ci['identity_verification'] ?? [];
$scrub   = $ci['identity_scrub']        ?? [];
$accts   = $ci['account_info']          ?? [];
$trend   = $ci['metro_score_trend']     ?? [];
$sectors = $ci['lender_sector']         ?? [];

/* ── Identity ── */
$fullName = trim(implode(' ', array_filter([$idv['first_name'] ?? '', $idv['other_name'] ?? '', $idv['surname'] ?? ''])));
if (!$fullName && !empty($scrub['names'][0])) $fullName = $scrub['names'][0];
if (!$fullName) $fullName = $req->full_name ?? 'N/A';
$idNum  = $req->national_id ?? ($idv['id_number'] ?? 'N/A');
$dob    = $idv['dob']    ?? ($req->dob    ?? null);
$gender = $idv['gender'] ?? ($req->gender ?? null);

/* ── Score ── */
$score     = $ci['credit_score']     ?? null;
$deliqCode = $ci['delinquency_code'] ?? null;
$isDelinquent = ($deliqCode === 'D' || $deliqCode === '1' || $deliqCode === 1);
$trxId    = $ci['trx_id']           ?? null;

$scoreColor = '#6b7280'; $scoreLabel = 'No Score'; $scoreBand = '';
if ($score !== null) {
    if ($score >= 700)     { $scoreColor = '#16a34a'; $scoreLabel = 'Excellent'; $scoreBand = 'Low credit risk'; }
    elseif ($score >= 600) { $scoreColor = '#65a30d'; $scoreLabel = 'Good';      $scoreBand = 'Below average risk'; }
    elseif ($score >= 500) { $scoreColor = '#d97706'; $scoreLabel = 'Fair';      $scoreBand = 'Average credit risk'; }
    elseif ($score >= 400) { $scoreColor = '#ea580c'; $scoreLabel = 'Poor';      $scoreBand = 'Above average risk'; }
    else                   { $scoreColor = '#dc2626'; $scoreLabel = 'Very Poor'; $scoreBand = 'High credit risk'; }
}

/* ── Score scale needle (200–900) ── */
$needlePct = $score !== null ? min(100, max(0, round(($score - 200) / 700 * 100))) : 0;

/* ── Accounts sort: active first ── */
usort($accts, function($a, $b) {
    $aA = in_array(strtolower($a['account_status'] ?? ''), ['a','active']);
    $bA = in_array(strtolower($b['account_status'] ?? ''), ['a','active']);
    if ($aA !== $bA) return $bA <=> $aA;
    return strcmp($b['opening_date'] ?? '', $a['opening_date'] ?? '');
});
$totalAccts       = count($accts);
$totalActive      = count(array_filter($accts, fn($a) => in_array(strtolower($a['account_status'] ?? ''), ['a','active'])));
$totalOutstanding = array_sum(array_column($accts, 'outstanding_balance'));
$totalArrears     = array_sum(array_column($accts, 'arrears_amount'));

/* ── Stats ── */
$_enqR  = $ci['no_of_enquiries']           ?? 0;
$_appR  = $ci['no_of_credit_applications'] ?? 0;
$_bcR   = $ci['no_of_bounced_cheques']     ?? 0;
$noEnq  = is_array($_enqR) ? count($_enqR) : (int)$_enqR;
$noApp  = is_array($_appR) ? count($_appR) : (int)$_appR;
$noBnc  = is_array($_bcR)  ? count($_bcR)  : (int)$_bcR;
$isGuar = $ci['is_guarantor'] ?? false;
$hasFrd = $ci['has_fraud']    ?? false;

/* ── Sector max ── */
$maxSecBal = !empty($sectors)
    ? max(array_map(fn($s) => (float)($s['outstanding_balance'] ?? $s['balance'] ?? 0), $sectors))
    : 1;
if ($maxSecBal <= 0) $maxSecBal = 1;

/* ── Trend ── */
$trendLabels = []; $trendScores = [];
foreach ($trend as $t) {
    $trendLabels[] = $t['month'] ?? ($t['period'] ?? '');
    $trendScores[] = $t['metro_score'] ?? ($t['score'] ?? 0);
}

$reportDate = $req->updated_at->format('j F Y, g:i A');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Full Credit Report – {{ $idNum }}</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }

/* ── Page layout ── */
.page { padding: 24px 30px; }

/* ── Header ── */
.pdf-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #1e3a5f; padding-bottom: 14px; margin-bottom: 18px; }
.brand { font-size: 22px; font-weight: 800; color: #1e3a5f; letter-spacing: -0.5px; }
.brand span { color: #f97316; }
.header-sub { font-size: 11px; color: #64748b; margin-top: 2px; }
.report-meta { text-align: right; font-size: 11px; color: #64748b; line-height: 1.7; }
.report-meta strong { color: #1e3a5f; font-size: 14px; font-weight: 700; display: block; }

/* ── Status banner ── */
.status-banner { border-radius: 6px; padding: 9px 14px; font-size: 11.5px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.status-banner.clean  { background: #dcfce7; color: #15803d; border-left: 4px solid #16a34a; }
.status-banner.danger { background: #fee2e2; color: #991b1b; border-left: 4px solid #dc2626; }

/* ── Score hero ── */
.score-hero { display: flex; align-items: center; gap: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin-bottom: 16px; }
.score-circle { width: 88px; height: 88px; border-radius: 50%; border: 7px solid; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0; }
.score-circle .sv { font-size: 24px; font-weight: 800; line-height: 1; }
.score-circle .sm { font-size: 9px; color: #94a3b8; }
.score-details { flex: 1; }
.score-details .sb { font-size: 18px; font-weight: 700; }
.score-details .sd { font-size: 11px; color: #64748b; margin-top: 3px; line-height: 1.5; }
.scale-bar-outer { height: 8px; border-radius: 4px; background: linear-gradient(to right, #dc2626 0%, #ea580c 25%, #d97706 45%, #65a30d 65%, #16a34a 100%); position: relative; margin: 10px 0 3px; }
.scale-needle { position: absolute; top: -3px; width: 3px; height: 14px; background: #1e3a5f; border-radius: 2px; transform: translateX(-50%); }
.scale-labels { display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }

/* ── Summary boxes ── */
.summary-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-bottom: 16px; }
.sbox { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 8px; text-align: center; }
.sbox-val { font-size: 16px; font-weight: 800; line-height: 1; margin-bottom: 3px; }
.sbox-lbl { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; }

/* ── Section titles ── */
.sec-title { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin: 16px 0 10px; }

/* ── Two-column layout ── */
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px; }

/* ── Info rows ── */
.info-row { display: flex; flex-direction: column; margin-bottom: 10px; }
.info-row .lbl { font-size: 9.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
.info-row .val { font-size: 12px; font-weight: 600; color: #1e293b; }

/* ── Sectors ── */
.sector-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
.sector-row:last-child { border-bottom: none; }
.sec-bar-outer { flex: 1; height: 5px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
.sec-bar-inner { height: 100%; border-radius: 3px; }

/* ── Flags ── */
.flag-row { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
.flag { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px; font-size: 10.5px; font-weight: 700; }
.flag.good  { background: #f0fdf4; color: #15803d; }
.flag.warn  { background: #fef3c7; color: #92400e; }
.flag.bad   { background: #fee2e2; color: #991b1b; }

/* ── Stat boxes (enquiries) ── */
.stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 10px; }
.stat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; text-align: center; }
.stat-val { font-size: 20px; font-weight: 800; line-height: 1; margin-bottom: 3px; }
.stat-lbl { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; }

/* ── Accounts table ── */
.acct-table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
.acct-table th { padding: 7px 10px; text-align: left; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; white-space: nowrap; }
.acct-table td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.acct-table tr:last-child td { border-bottom: none; }
.inst { font-weight: 600; color: #1e293b; }
.acct-num { font-size: 9.5px; color: #94a3b8; font-family: monospace; }
.badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: 700; white-space: nowrap; }

/* ── Names tags ── */
.name-tag { display: inline-flex; padding: 3px 8px; border-radius: 4px; background: #f1f5f9; color: #475569; font-size: 10px; font-weight: 500; margin: 2px; }

/* ── Footer ── */
.pdf-footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; font-size: 9.5px; color: #94a3b8; }
.pdf-footer .disclaimer { max-width: 55%; line-height: 1.5; }

/* ── Page break ── */
.page-break { page-break-before: always; }

@media print {
    body { font-size: 11px; }
    @page { margin: 14mm 12mm; size: A4 portrait; }
    .no-print { display: none !important; }
}
</style>
</head>
<body>
<div class="page">

{{-- ══════════════════ HEADER ══════════════════ --}}
<div class="pdf-header">
    <div>
        <div class="brand">Readi<span>work</span></div>
        <div class="header-sub">Credit Bureau Services · Powered by Metropol CRB</div>
    </div>
    <div class="report-meta">
        <strong>Full Credit Report</strong>
        ID: {{ $idNum }} &nbsp;·&nbsp; {{ $fullName }}<br>
        Report Date: {{ $reportDate }}<br>
        @if($trxId)Ref: {{ $trxId }}<br>@endif
        Doc: #{{ str_pad($req->id, 6, '0', STR_PAD_LEFT) }}
    </div>
</div>

{{-- ══════════════════ STATUS BANNER ══════════════════ --}}
@if($isDelinquent)
<div class="status-banner danger">
    &#9888; CRB LISTED — This profile has a delinquency flag. Lenders are advised to exercise caution.
</div>
@else
<div class="status-banner clean">
    &#10004; CRB CLEAR — No active delinquency listing detected on this profile.
</div>
@endif

{{-- ══════════════════ SCORE HERO ══════════════════ --}}
<div class="score-hero">
    <div class="score-circle" style="border-color:{{ $scoreColor }};color:{{ $scoreColor }}">
        <span class="sv">{{ $score ?? '—' }}</span>
        <span class="sm">/ 900</span>
    </div>
    <div class="score-details" style="flex:1">
        <div class="sb" style="color:{{ $scoreColor }}">{{ $scoreLabel }}</div>
        @if($scoreBand)<div class="sd">{{ $scoreBand }}</div>@endif
        <div class="scale-bar-outer">
            @if($score !== null)<div class="scale-needle" style="left:{{ $needlePct }}%"></div>@endif
        </div>
        <div class="scale-labels">
            <span>200</span><span>400</span><span>500</span><span>600</span><span>700</span><span>900</span>
        </div>
    </div>
    <div style="text-align:center;flex-shrink:0;padding-left:16px;border-left:1px solid #e2e8f0">
        <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em">Accounts</div>
        <div style="font-size:20px;font-weight:800;color:#1e3a5f">{{ $totalAccts }}</div>
        <div style="font-size:9px;color:#94a3b8;margin-top:8px;text-transform:uppercase">Active</div>
        <div style="font-size:16px;font-weight:700;color:#16a34a">{{ $totalActive }}</div>
    </div>
</div>

{{-- ══════════════════ SUMMARY BOXES ══════════════════ --}}
<div class="summary-row">
    <div class="sbox">
        <div class="sbox-val" style="color:{{ $scoreColor }}">{{ $score ?? '—' }}</div>
        <div class="sbox-lbl">Credit Score</div>
    </div>
    <div class="sbox">
        <div class="sbox-val" style="color:{{ $isDelinquent ? '#dc2626' : '#16a34a' }}">{{ $isDelinquent ? 'Listed' : 'Clean' }}</div>
        <div class="sbox-lbl">CRB Status</div>
    </div>
    <div class="sbox">
        <div class="sbox-val" style="color:{{ $totalArrears > 0 ? '#dc2626' : '#1e3a5f' }}">KES {{ number_format($totalOutstanding) }}</div>
        <div class="sbox-lbl">Total Exposure</div>
    </div>
    <div class="sbox">
        <div class="sbox-val" style="color:{{ $totalArrears > 0 ? '#dc2626' : '#1e3a5f' }}">@if($totalArrears > 0)KES {{ number_format($totalArrears) }}@else —@endif</div>
        <div class="sbox-lbl">Total Arrears</div>
    </div>
    <div class="sbox">
        <div class="sbox-val" style="color:{{ $noEnq > 5 ? '#d97706' : '#1e3a5f' }}">{{ $noEnq }}</div>
        <div class="sbox-lbl">Enquiries</div>
    </div>
</div>

{{-- ══════════════════ TWO-COLUMN: IDENTITY + ENQUIRIES ══════════════════ --}}
<div class="two-col">

    {{-- Identity --}}
    <div>
        <div class="sec-title">Identity Details</div>
        <div class="info-row"><span class="lbl">Full Name</span><span class="val">{{ strtoupper($fullName) }}</span></div>
        <div class="info-row"><span class="lbl">National ID</span><span class="val">{{ $idNum }}</span></div>
        @if($dob)
        <div class="info-row">
            <span class="lbl">Date of Birth</span>
            <span class="val">@php try { echo \Carbon\Carbon::parse($dob)->format('j M Y'); } catch(\Exception $e){ echo $dob; } @endphp</span>
        </div>
        @endif
        @if($gender)
        <div class="info-row">
            <span class="lbl">Gender</span>
            <span class="val">@php $g = strtolower($gender ?? ''); echo ($g==='m'||$g==='male') ? 'Male' : (($g==='f'||$g==='female') ? 'Female' : $gender); @endphp</span>
        </div>
        @endif
        @if(!empty($scrub['phones'][0]))
        <div class="info-row"><span class="lbl">Phone</span><span class="val">{{ $scrub['phones'][0] }}</span></div>
        @endif
        @if(!empty($scrub['emails'][0]))
        <div class="info-row"><span class="lbl">Email</span><span class="val" style="font-size:11px">{{ $scrub['emails'][0] }}</span></div>
        @endif
        @if(!empty($scrub['names']) && count($scrub['names']) > 1)
        <div class="info-row">
            <span class="lbl">Other Names on Record</span>
            <span class="val" style="font-size:11px">
                @foreach(array_slice($scrub['names'], 0, 6) as $n)<span class="name-tag">{{ $n }}</span>@endforeach
            </span>
        </div>
        @endif
    </div>

    {{-- Enquiries & Flags --}}
    <div>
        <div class="sec-title">Enquiries, Applications &amp; Flags</div>
        <div class="stat-row">
            <div class="stat-box">
                <div class="stat-val" style="color:{{ $noEnq > 5 ? '#d97706' : '#1e3a5f' }}">{{ $noEnq }}</div>
                <div class="stat-lbl">Enquiries</div>
            </div>
            <div class="stat-box">
                <div class="stat-val" style="color:{{ $noApp > 3 ? '#d97706' : '#1e3a5f' }}">{{ $noApp }}</div>
                <div class="stat-lbl">Applications</div>
            </div>
            <div class="stat-box">
                <div class="stat-val" style="color:{{ $noBnc > 0 ? '#dc2626' : '#1e3a5f' }}">{{ $noBnc }}</div>
                <div class="stat-lbl">Bounced Chqs</div>
            </div>
        </div>
        <div class="flag-row">
            <span class="flag {{ $isGuar ? 'warn' : 'good' }}">
                &#9679; {{ $isGuar ? 'Is a Guarantor' : 'Not a Guarantor' }}
            </span>
            <span class="flag {{ $hasFrd ? 'bad' : 'good' }}">
                &#9679; {{ $hasFrd ? 'Fraud Flag Present' : 'No Fraud Flag' }}
            </span>
            <span class="flag {{ $isDelinquent ? 'bad' : 'good' }}">
                &#9679; {{ $isDelinquent ? 'CRB Listed' : 'No CRB Listing' }}
            </span>
        </div>
        @if(!empty($sectors))
        <div class="sec-title" style="margin-top:14px">Lender Sector Breakdown</div>
        @foreach(array_slice($sectors,0,6) as $sec)
        @php
            $sn     = strtolower($sec['sector'] ?? $sec['lender_sector'] ?? 'Other');
            $secBal = (float)($sec['outstanding_balance'] ?? $sec['balance'] ?? 0);
            $barPct = max(4, round(($secBal / $maxSecBal) * 100));
            if (str_contains($sn,'bank'))                              { $clr = '#0ea5e9'; }
            elseif (str_contains($sn,'mfi')||str_contains($sn,'micro')){ $clr = '#8b5cf6'; }
            elseif (str_contains($sn,'sacco'))                         { $clr = '#16a34a'; }
            elseif (str_contains($sn,'digital')||str_contains($sn,'mobile')){ $clr = '#f59e0b'; }
            else                                                        { $clr = '#6b7280'; }
        @endphp
        <div class="sector-row">
            <div style="font-size:11px;font-weight:600;min-width:90px;color:#1e293b">{{ $sec['sector'] ?? $sec['lender_sector'] ?? 'Other' }}</div>
            <div class="sec-bar-outer"><div class="sec-bar-inner" style="width:{{ $barPct }}%;background:{{ $clr }}"></div></div>
            <div style="font-size:11px;font-weight:700;min-width:70px;text-align:right;padding-left:8px">KES {{ number_format($secBal) }}</div>
        </div>
        @endforeach
        @endif
    </div>

</div>

{{-- ══════════════════ CREDIT ACCOUNTS ══════════════════ --}}
<div class="sec-title">Credit Accounts ({{ $totalAccts }} total · {{ $totalActive }} active)</div>

@if(count($accts) === 0)
<p style="color:#94a3b8;font-size:11px;padding:8px 0">No account records found.</p>
@else
<table class="acct-table">
    <thead>
        <tr>
            <th>Lender / Account</th>
            <th>Type</th>
            <th>Status</th>
            <th style="text-align:right">Outstanding</th>
            <th style="text-align:right">Arrears</th>
            <th>Opened</th>
        </tr>
    </thead>
    <tbody>
    @foreach($accts as $acct)
    @php
        $st      = strtolower($acct['account_status'] ?? '');
        $isAct   = in_array($st, ['a','active']);
        $sLbl    = $isAct ? 'Active' : (in_array($st,['c','closed']) ? 'Closed' : (str_contains($st,'writ') ? 'Written Off' : ucfirst($acct['account_status'] ?? '—')));
        $sClr    = $isAct ? '#16a34a' : (str_contains($st,'writ') ? '#dc2626' : '#6b7280');
        $sBg     = $isAct ? 'rgba(22,163,74,.12)' : (str_contains($st,'writ') ? 'rgba(220,38,38,.1)' : 'rgba(107,114,128,.1)');
        $outBal  = (float)($acct['outstanding_balance'] ?? 0);
        $arrears = (float)($acct['arrears_amount']      ?? 0);
        $openD   = !empty($acct['opening_date']) ? \Carbon\Carbon::parse($acct['opening_date'])->format('M Y') : '—';
        $delflag = in_array($acct['delinquency_code'] ?? null, ['D','1',1]);
    @endphp
    <tr>
        <td>
            <div class="inst">{{ $acct['institution_name'] ?? 'Unknown' }}</div>
            @if(!empty($acct['account_number']))<div class="acct-num">{{ $acct['account_number'] }}</div>@endif
            @if($delflag)<span class="badge" style="background:rgba(220,38,38,.1);color:#dc2626;margin-top:2px">Delinquent</span>@endif
        </td>
        <td style="color:#64748b;font-size:10px">
            {{ $acct['account_type'] ?? '—' }}
            @if(!empty($acct['sector']))<br>{{ $acct['sector'] }}@endif
        </td>
        <td><span class="badge" style="background:{{ $sBg }};color:{{ $sClr }}">{{ $sLbl }}</span></td>
        <td style="text-align:right;font-weight:600">{{ $acct['currency'] ?? 'KES' }} {{ number_format($outBal) }}</td>
        <td style="text-align:right;font-weight:600;color:{{ $arrears > 0 ? '#dc2626' : '#94a3b8' }}">
            @if($arrears > 0){{ number_format($arrears) }}@else —@endif
        </td>
        <td style="color:#64748b;font-size:10px">{{ $openD }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif

{{-- ══════════════════ TREND (if data available) ══════════════════ --}}
@if(count($trendLabels) > 1)
<div class="sec-title" style="margin-top:20px">12-Month Metro Score Trend</div>
<div style="height:120px;position:relative;margin-bottom:6px">
    <canvas id="trendChartPdf" height="120"></canvas>
</div>
@endif

{{-- ══════════════════ PAYMENT & FOOTER ══════════════════ --}}
<div class="two-col" style="margin-top:20px;margin-bottom:8px">
    <div>
        <div class="sec-title">Payment Details</div>
        <div class="info-row"><span class="lbl">Amount Paid</span><span class="val" style="color:#16a34a">KES {{ number_format((float)($req->payment_amount ?? $req->price ?? 0)) }}</span></div>
        <div class="info-row"><span class="lbl">M-Pesa Receipt</span><span class="val" style="font-family:monospace">{{ $req->mpesa_receipt_number ?: '—' }}</span></div>
        <div class="info-row"><span class="lbl">Payment Date</span>
            <span class="val">{{ $req->payment_date ? \Carbon\Carbon::parse($req->payment_date)->format('d M Y H:i') : $req->created_at->format('d M Y H:i') }}</span>
        </div>
    </div>
    <div>
        <div class="sec-title">Report Details</div>
        <div class="info-row"><span class="lbl">Generated</span><span class="val">{{ $reportDate }}</span></div>
        @if($trxId)<div class="info-row"><span class="lbl">Metropol Ref</span><span class="val" style="font-family:monospace">{{ $trxId }}</span></div>@endif
        <div class="info-row"><span class="lbl">Document ID</span><span class="val" style="font-family:monospace">#{{ str_pad($req->id, 6, '0', STR_PAD_LEFT) }}</span></div>
        <div class="info-row"><span class="lbl">Security Hash</span><span class="val" style="font-family:monospace;font-size:9.5px;word-break:break-all">{{ substr(hash('sha256', $req->id . $idNum . $reportDate), 0, 32) }}…</span></div>
    </div>
</div>

<div class="pdf-footer">
    <div class="disclaimer">
        This report is generated from Metropol CRB data and is intended solely for the named individual.
        Readiwork is a licensed credit information reseller. For disputes contact Metropol CRB directly.
        Do not share without the subject's consent.
    </div>
    <div style="text-align:right">
        <strong style="color:#1e3a5f;font-size:13px">Readi<span style="color:#f97316">work</span></strong><br>
        readi.work<br>
        {{ $reportDate }}
    </div>
</div>

</div>{{-- .page --}}

@if(count($trendLabels) > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('trendChartPdf');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Metro Score',
                data: @json($trendScores),
                fill: true,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,.08)',
                borderWidth: 2,
                pointBackgroundColor: '#16a34a',
                pointRadius: 3,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            scales: {
                y: { min: 200, max: 900, ticks: { stepSize: 100, font: { size: 9 } }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 9 } }, grid: { display: false } }
            },
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' Score: ' + c.parsed.y } } }
        }
    });
    // Small delay to allow chart render before print dialog
    setTimeout(() => window.print(), 600);
})();
</script>
@else
<script>window.onload = function(){ window.print(); };</script>
@endif

</body>
</html>
