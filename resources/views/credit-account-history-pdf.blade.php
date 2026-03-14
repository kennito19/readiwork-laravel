<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1a2940; background: #fff; }
table { border-collapse: collapse; }

/* ── PAGE LAYOUT ── */
.page { width: 100%; padding: 0; }
.hdr-table { width: 100%; background: #071629; }
.hdr-table td { padding: 20pt 28pt; vertical-align: middle; }
.logo-cell { width: 120pt; }
.logo-text { font-size: 18pt; font-weight: 700; color: #fff; letter-spacing: -0.5pt; }
.logo-dot  { color: #0FA958; }
.hdr-title { font-size: 13pt; font-weight: 700; color: #fff; margin-bottom: 3pt; }
.hdr-sub   { font-size: 8pt; color: rgba(255,255,255,0.55); }
.hdr-meta  { text-align: right; }
.hdr-badge { display: inline-block; background: rgba(99,102,241,0.2); color: #a5b4fc; border: 1pt solid rgba(99,102,241,0.4); border-radius: 4pt; padding: 3pt 10pt; font-size: 7.5pt; font-weight: 700; }

/* ── SUBJECT BANNER ── */
.banner-table { width: 100%; background: #0d1f3c; }
.banner-table td { padding: 14pt 28pt; }
.banner-name { font-size: 14pt; font-weight: 700; color: #fff; margin-bottom: 3pt; }
.banner-meta { font-size: 8pt; color: rgba(255,255,255,0.5); }

/* ── SECTION HEADERS ── */
.section-hdr { width: 100%; margin-top: 16pt; margin-bottom: 8pt; }
.section-hdr td { font-size: 8pt; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: 0.8pt; padding: 0 28pt; border-left: 3pt solid #6366f1; }

/* ── CARDS / BOXES ── */
.card { width: calc(100% - 56pt); margin: 0 28pt 12pt; background: #fff; border: 1pt solid #e2e8f0; border-radius: 6pt; overflow: hidden; }
.card-head { background: #f8fafc; border-bottom: 1pt solid #e2e8f0; padding: 8pt 14pt; font-size: 8.5pt; font-weight: 700; color: #0b1f3b; }

/* ── STATS GRID ── */
.stats-outer { width: calc(100% - 56pt); margin: 0 28pt 12pt; }
.stat-box { border: 1pt solid #e2e8f0; border-radius: 5pt; padding: 10pt 8pt; text-align: center; }
.stat-val { font-size: 13pt; font-weight: 700; color: #0b1f3b; }
.stat-lbl { font-size: 7pt; color: #94a3b8; margin-top: 2pt; }

/* ── TABLES ── */
.data-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
.data-table th { padding: 7pt 10pt; text-align: left; font-size: 7pt; font-weight: 700; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1.5pt solid #e2e8f0; }
.data-table td { padding: 8pt 10pt; border-bottom: 1pt solid #e2e8f0; color: #334155; }
.data-table tr:last-child td { border-bottom: none; }

/* ── ACCOUNT CARD ── */
.acc-card { width: calc(100% - 56pt); margin: 0 28pt 14pt; border: 1pt solid #e2e8f0; border-radius: 6pt; overflow: hidden; }
.acc-head { background: #f1f5f9; padding: 9pt 14pt; border-bottom: 1pt solid #e2e8f0; }
.acc-inst { font-size: 10pt; font-weight: 700; color: #0b1f3b; }
.acc-meta { font-size: 7.5pt; color: #64748b; margin-top: 2pt; }

/* ── BADGES ── */
.badge-green  { background: #f0fdf4; color: #16a34a; border: 1pt solid #bbf7d0; border-radius: 3pt; padding: 2pt 7pt; font-size: 7pt; font-weight: 700; }
.badge-red    { background: #fef2f2; color: #dc2626; border: 1pt solid #fecaca; border-radius: 3pt; padding: 2pt 7pt; font-size: 7pt; font-weight: 700; }
.badge-gray   { background: #f8fafc; color: #64748b; border: 1pt solid #e2e8f0; border-radius: 3pt; padding: 2pt 7pt; font-size: 7pt; font-weight: 700; }
.badge-purple { background: #f0f4ff; color: #6366f1; border: 1pt solid #c7d2fe; border-radius: 3pt; padding: 2pt 7pt; font-size: 7pt; font-weight: 700; }

/* ── STATUS DOT ── */
.dot-green { display: inline-block; width: 8pt; height: 8pt; background: #16a34a; border-radius: 2pt; }
.dot-red   { display: inline-block; width: 8pt; height: 8pt; background: #dc2626; border-radius: 2pt; }

/* ── RECEIPT ── */
.receipt-box { width: calc(100% - 56pt); margin: 0 28pt 12pt; background: #f0fdf4; border: 1pt solid #bbf7d0; border-radius: 5pt; padding: 12pt 14pt; }

/* ── FOOTER ── */
.footer-table { width: 100%; background: #071629; margin-top: 20pt; }
.footer-table td { padding: 12pt 28pt; font-size: 7.5pt; color: rgba(255,255,255,0.4); }
</style>
</head>
<body>
<div class="page">

{{-- HEADER --}}
<table class="hdr-table"><tr>
    <td class="logo-cell"><div class="logo-text">Readi<span class="logo-dot">.</span>work</div><div style="font-size:7pt;color:rgba(255,255,255,0.4);margin-top:2pt;">Kenya Credit Intelligence</div></td>
    <td><div class="hdr-title">Complete Financial Check Report</div><div class="hdr-sub">Report Type 22 &amp; 16 &nbsp;·&nbsp; Kenya Credit Reference Bureau</div></td>
    <td class="hdr-meta">
        <div class="hdr-badge"><i>12-Month History</i></div>
        <div style="font-size:7.5pt;color:rgba(255,255,255,0.5);margin-top:6pt;">{{ $report_date }}</div>
    </td>
</tr></table>

{{-- SUBJECT BANNER --}}
<table class="banner-table"><tr>
    <td style="width:60%;">
        <div class="banner-name">{{ $full_name }}</div>
        <div class="banner-meta">National ID: {{ $id_number }} &nbsp;·&nbsp; DOB: {{ $dob_fmt }} &nbsp;·&nbsp; Gender: {{ $gender_label }}</div>
    </td>
    <td style="text-align:right;vertical-align:middle;">
        @if($is_delinquent)
        <table style="display:inline-table;background:rgba(220,38,38,0.15);border:1pt solid rgba(220,38,38,0.4);border-radius:5pt;padding:8pt 16pt;">
        <tr><td style="color:#f87171;font-size:9pt;font-weight:700;text-align:center;">DELINQUENT<br><span style="font-size:7pt;opacity:.7;">Code {{ $delinquency_code }}</span></td></tr>
        </table>
        @else
        <table style="display:inline-table;background:rgba(22,163,74,0.15);border:1pt solid rgba(22,163,74,0.4);border-radius:5pt;padding:8pt 16pt;">
        <tr><td style="color:#4ade80;font-size:9pt;font-weight:700;text-align:center;">GOOD STANDING<br><span style="font-size:7pt;opacity:.7;">Code {{ $delinquency_code ?: 'N/A' }}</span></td></tr>
        </table>
        @endif
    </td>
</tr></table>

{{-- OVERVIEW STATS --}}
<br>
<table class="section-hdr"><tr><td>Account Overview</td></tr></table>
<table class="stats-outer">
<tr>
    <td style="width:25%;padding-right:6pt;"><div class="stat-box"><div class="stat-val">{{ count($accounts) }}</div><div class="stat-lbl">Total Accounts</div></div></td>
    <td style="width:25%;padding-right:6pt;"><div class="stat-box"><div class="stat-val" style="font-size:{{ $total_outstanding > 100000 ? '9pt' : '13pt' }};color:{{ $total_outstanding > 0 ? '#d97706' : '#16a34a' }};">{{ $total_outstanding > 0 ? 'KES ' . number_format($total_outstanding) : 'None' }}</div><div class="stat-lbl">Total Outstanding</div></div></td>
    <td style="width:25%;padding-right:6pt;"><div class="stat-box"><div class="stat-val" style="color:{{ $total_overdue > 0 ? '#dc2626' : '#16a34a' }};">{{ $total_overdue > 0 ? 'KES ' . number_format($total_overdue) : 'None' }}</div><div class="stat-lbl">Total Overdue</div></div></td>
    <td style="width:25%;"><div class="stat-box"><div class="stat-val" style="color:{{ $highest_dias > 0 ? '#d97706' : '#16a34a' }};">{{ $highest_dias }}</div><div class="stat-lbl">Highest Days Arrears</div></div></td>
</tr>
</table>

<table class="stats-outer" style="margin-top:6pt;">
<tr>
    <td style="width:25%;padding-right:6pt;"><div class="stat-box"><div class="stat-val" style="color:#6366f1;">{{ $active_generic }}</div><div class="stat-lbl">Active Loans</div></div></td>
    <td style="width:25%;padding-right:6pt;"><div class="stat-box"><div class="stat-val" style="color:#64748b;">{{ $closed_generic }}</div><div class="stat-lbl">Closed Loans</div></div></td>
    <td style="width:25%;padding-right:6pt;"><div class="stat-box"><div class="stat-val" style="color:#16a34a;">{{ $active_mobile }}</div><div class="stat-lbl">Active Mobile</div></div></td>
    <td style="width:25%;"><div class="stat-box"><div class="stat-val" style="color:#64748b;">{{ $closed_mobile }}</div><div class="stat-lbl">Closed Mobile</div></div></td>
</tr>
</table>

@if($max_credit_score > 0)
<table class="section-hdr" style="margin-top:12pt;"><tr><td>Credit Score Trend (12 Months)</td></tr></table>
<div class="card">
    <table style="width:100%;padding:10pt 14pt;"><tr>
        <td style="width:33%;text-align:center;">
            <div style="font-size:18pt;font-weight:700;color:#16a34a;">{{ $max_credit_score }}</div>
            <div style="font-size:7pt;color:#64748b;">Peak Score</div>
        </td>
        <td style="width:33%;text-align:center;">
            <div style="font-size:10pt;font-weight:700;color:#0b1f3b;">{{ $min_credit_score }} to {{ $max_credit_score }}</div>
            <div style="font-size:7pt;color:#64748b;">Score Range</div>
        </td>
        <td style="width:33%;text-align:center;">
            <div style="font-size:18pt;font-weight:700;color:#6366f1;">{{ $min_credit_score }}</div>
            <div style="font-size:7pt;color:#64748b;">Lowest Score</div>
        </td>
    </tr></table>
    @if(!empty($monthly_score))
    <div style="padding:0 14pt 10pt;">
    <table style="width:100%;border-collapse:collapse;font-size:7.5pt;">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1pt solid #e2e8f0;">
                @foreach($monthly_score as $ms)
                <th style="padding:5pt 4pt;text-align:center;font-size:6.5pt;font-weight:700;color:#94a3b8;">{{ \Carbon\Carbon::parse($ms['month'])->format('M y') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($monthly_score as $ms)
                <td style="padding:5pt 4pt;text-align:center;font-weight:700;color:#6366f1;">{{ $ms['credit_score'] }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    </div>
    @endif
</div>
@endif

{{-- ACCOUNTS WITH HISTORY --}}
@if(!empty($accounts))
<table class="section-hdr" style="margin-top:12pt;"><tr><td>Credit Accounts &amp; Payment History</td></tr></table>

@foreach($accounts as $acc)
@php
$acStatus   = strtolower($acc['account_status_name'] ?? 'unknown');
$isActive   = $acStatus === 'active';
$acBalance  = (float)($acc['current_balance'] ?? 0);
$acOverdue  = (float)($acc['overdue_balance'] ?? 0);
$acDias     = (int)($acc['days_in_arrears'] ?? 0);
$acOriginal = (float)($acc['original_amount'] ?? 0);
$acOpened   = $acc['date_opened'] ?? null;
$acNumber   = $acc['account_number'] ?? 'N/A';
$acInst     = $acc['institution_name'] ?? 'Unknown';
$acProduct  = $acc['product_type_name'] ?? 'N/A';
$acHistory  = $acc['account_history'] ?? [];
$acBad      = $acOverdue > 0 || (int)($acc['days_in_arrears'] ?? 0) > 0;
@endphp
<div class="acc-card">
    <div class="acc-head">
        <table style="width:100%;"><tr>
            <td><div class="acc-inst">{{ $acInst }}</div><div class="acc-meta">{{ $acProduct }} &nbsp;·&nbsp; {{ $acNumber }} &nbsp;·&nbsp; Opened: {{ $acOpened ? \Carbon\Carbon::parse($acOpened)->format('M Y') : 'N/A' }}</div></td>
            <td style="text-align:right;vertical-align:middle;">
                @if($isActive)<span class="badge-green">Active</span>@else<span class="badge-gray">{{ ucfirst($acStatus) }}</span>@endif
                @if($acBad) &nbsp;<span class="badge-red">Overdue</span>@endif
            </td>
        </tr></table>
    </div>
    <table style="width:100%;padding:8pt 14pt;"><tr>
        <td style="width:25%;padding-right:8pt;"><div style="font-size:7pt;color:#94a3b8;text-transform:uppercase;margin-bottom:2pt;">Balance</div><div style="font-weight:700;color:#0b1f3b;">{{ $acBalance > 0 ? 'KES ' . number_format($acBalance) : '—' }}</div></td>
        <td style="width:25%;padding-right:8pt;"><div style="font-size:7pt;color:#94a3b8;text-transform:uppercase;margin-bottom:2pt;">Original</div><div style="font-weight:700;color:#0b1f3b;">{{ $acOriginal > 0 ? 'KES ' . number_format($acOriginal) : '—' }}</div></td>
        <td style="width:25%;padding-right:8pt;"><div style="font-size:7pt;color:{{ $acOverdue > 0 ? '#991b1b' : '#94a3b8' }};text-transform:uppercase;margin-bottom:2pt;">Overdue</div><div style="font-weight:700;color:{{ $acOverdue > 0 ? '#dc2626' : '#6b7280' }};">{{ $acOverdue > 0 ? 'KES ' . number_format($acOverdue) : 'None' }}</div></td>
        <td style="width:25%;"><div style="font-size:7pt;color:#94a3b8;text-transform:uppercase;margin-bottom:2pt;">Days Arrears</div><div style="font-weight:700;color:{{ $acDias > 0 ? '#dc2626' : '#16a34a' }};">{{ $acDias }}</div></td>
    </tr></table>

    @if(!empty($acHistory))
    <div style="padding:0 14pt 10pt;">
        <div style="font-size:7pt;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:.5pt;margin-bottom:6pt;">12-Month Payment History</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Last Payment</th>
                    <th style="text-align:right;">Overdue Balance</th>
                    <th style="text-align:center;">Days Arrears</th>
                </tr>
            </thead>
            <tbody>
            @foreach($acHistory as $i => $h)
            @php
            $hOverdue = (float)($h['overdue_balance'] ?? 0);
            $hDias    = (int)($h['days_in_arrears'] ?? 0);
            $hAmount  = (float)($h['last_payment_amount'] ?? 0);
            $hMonth   = $h['month'] ?? null;
            $hBad     = $hOverdue > 0 || $hDias > 0;
            $rowBg    = ($i % 2 === 1) ? '#f8fafc' : '#fff';
            @endphp
            <tr style="background:{{ $hBad ? '#fffafa' : $rowBg }};border-bottom:1pt solid #e2e8f0;">
                <td>{{ $hMonth ? \Carbon\Carbon::parse($hMonth)->format('M Y') : '—' }}</td>
                <td style="text-align:center;">
                    <table style="display:inline-table;"><tr><td style="padding:0;">
                        <span style="display:inline-block;width:8pt;height:8pt;background:{{ $hBad ? '#dc2626' : '#16a34a' }};border-radius:2pt;"></span>
                        &nbsp;<span style="font-size:7pt;color:{{ $hBad ? '#dc2626' : '#16a34a' }};font-weight:700;">{{ $hBad ? 'Late' : 'On Time' }}</span>
                    </td></tr></table>
                </td>
                <td style="text-align:right;color:{{ $hAmount > 0 ? '#0b1f3b' : '#9ca3af' }};font-weight:{{ $hAmount > 0 ? '700' : '400' }};">{{ $hAmount > 0 ? 'KES ' . number_format($hAmount) : '—' }}</td>
                <td style="text-align:right;color:{{ $hOverdue > 0 ? '#dc2626' : '#9ca3af' }};font-weight:{{ $hOverdue > 0 ? '700' : '400' }};">{{ $hOverdue > 0 ? 'KES ' . number_format($hOverdue) : '—' }}</td>
                <td style="text-align:center;color:{{ $hDias > 0 ? '#dc2626' : '#16a34a' }};font-weight:700;">{{ $hDias }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endforeach
@endif

{{-- PAYMENT RECEIPT --}}
<table class="section-hdr" style="margin-top:12pt;"><tr><td>Payment Receipt</td></tr></table>
<div class="receipt-box">
    <table style="width:100%;"><tr>
        <td style="width:33%;"><div style="font-size:7pt;color:#16a34a;font-weight:700;text-transform:uppercase;margin-bottom:3pt;">Amount Paid</div><div style="font-size:12pt;font-weight:700;color:#16a34a;">KES {{ number_format((float)($req->payment_amount ?? $req->price ?? 0)) }}</div></td>
        <td style="width:33%;"><div style="font-size:7pt;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:3pt;">M-Pesa Receipt</div><div style="font-size:9pt;font-weight:700;color:#0b1f3b;font-family:monospace;">{{ $req->mpesa_receipt_number ?: '—' }}</div></td>
        <td style="width:33%;"><div style="font-size:7pt;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:3pt;">Payment Date</div><div style="font-size:8.5pt;font-weight:700;color:#0b1f3b;">{{ $req->payment_date ? \Carbon\Carbon::parse($req->payment_date)->format('d M Y H:i') : $req->created_at->format('d M Y H:i') }}</div></td>
    </tr></table>
</div>

{{-- FOOTER --}}
<table class="footer-table"><tr>
    <td>Generated by Readiwork &nbsp;·&nbsp; readiwork.co.ke &nbsp;·&nbsp; Credit data sourced from Kenya Credit Reference Bureau &nbsp;·&nbsp; {{ $report_date }}</td>
    <td style="text-align:right;">This report is confidential and intended solely for the subject named above.</td>
</tr></table>

</div>
</body>
</html>
