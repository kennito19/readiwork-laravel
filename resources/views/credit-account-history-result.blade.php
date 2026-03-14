@extends('layouts.app')

@php
$ai      = $crb['accounts_info']    ?? [];
$summary = $crb['accounts_summary'] ?? [];
$scrub   = $crb['scrub']            ?? [];

$full_name  = $ai['customer_name'] ?? $scrub['names'][0] ?? $req->full_name ?? 'Applicant';
$vn         = $summary['verified_name'] ?? [];
if ((!$full_name || $full_name === 'Applicant') && !empty($vn)) {
    $full_name = trim(implode(' ', array_filter([$vn['first_name'] ?? '', $vn['other_name'] ?? '', $vn['surname'] ?? '']))) ?: $full_name;
}
$id_number  = $req->national_id;
$dob        = $scrub['date_of_being'][0] ?? null;
$gender_raw = $scrub['gender'][0] ?? null;
$gender     = $gender_raw ? match(strtoupper((string)$gender_raw)) { 'M' => 'Male', 'F' => 'Female', default => $gender_raw } : null;
$phone      = $scrub['phone'][0] ?? null;
$email      = $scrub['email'][0] ?? null;
$employer   = $scrub['employment'][0] ?? null;

$delinquency_code      = (string)($ai['delinquency_code'] ?? '');
$total_outstanding     = (float)($ai['total_outstanding_amount'] ?? 0);
$total_outstanding_npa = (float)($ai['total_outstanding_npa'] ?? 0);
$total_outstanding_perf= (float)($ai['total_outstanding_performing'] ?? 0);
$total_overdue         = (float)($ai['total_overdue_amount'] ?? 0);
$highest_dias          = (int)($ai['highest_days_in_arrears'] ?? 0);
$lowest_dias           = (int)($ai['lowest_days_in_arrears'] ?? 0);
$max_credit_score      = (int)($ai['max_credit_score'] ?? 0);
$min_credit_score      = (int)($ai['min_credit_score'] ?? 0);
$monthly_score         = $ai['monthly_score'] ?? [];

$accounts = $ai['account_info'] ?? [];
if (!empty($accounts)) {
    usort($accounts, function($a, $b) {
        $aA = strtolower($a['account_status_name'] ?? '') === 'active';
        $bA = strtolower($b['account_status_name'] ?? '') === 'active';
        if ($aA !== $bA) return $bA <=> $aA;
        return strcmp($b['date_opened'] ?? '', $a['date_opened'] ?? '');
    });
}

$ci = $summary['credit_info'] ?? [];
$active_generic     = (int)($ci['generic_account_count'] ?? 0);
$closed_generic     = (int)($ci['generic_account_count_closed'] ?? 0);
$active_mobile      = (int)($ci['mobile_account_count_active'] ?? 0);
$closed_mobile      = (int)($ci['mobile_account_count_closed'] ?? 0);
$monthly_instalment = (float)($ci['total_monthly_instalment_generic'] ?? 0);
$mobile_npa         = (int)($ci['mobile_account_npa_count'] ?? 0);
$generic_npa        = (int)($ci['generic_account_npa_count'] ?? 0);

$code_info = [
    '001' => ['No Adverse History',      '#16a34a', 'fa-circle-check',         false],
    '002' => ['Good Standing',           '#0e7c7c', 'fa-circle-check',         false],
    '003' => ['Good, Some Late Payments','#d97706', 'fa-clock-rotate-left',    false],
    '004' => ['Delinquent Account(s)',   '#dc2626', 'fa-triangle-exclamation', true],
    '005' => ['Written Off',             '#991b1b', 'fa-triangle-exclamation', true],
];
[$code_label, $code_color, $code_icon, $is_delinquent] = $code_info[$delinquency_code] ?? ['Status Retrieved','#6b7280','fa-circle-info',false];

$report_date = now()->format('j F Y, g:i A');
$rid = $req->id;
@endphp

@section('title', 'Complete Financial Check Report, ' . $full_name . ' - Readiwork')

@push('head')
<style>
.result-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:48px 0 44px;position:relative;overflow:hidden;}
.result-hero::before{content:'';position:absolute;top:-40%;right:-5%;width:55%;height:180%;background:radial-gradient(ellipse,rgba(99,102,241,.09) 0%,transparent 65%);pointer-events:none;}
.result-main{padding:36px 0 80px;background:var(--bg-light);}
.result-grid{display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;}
.result-left{display:grid;gap:20px;}
.rcard{background:#fff;border:1px solid var(--border-color);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(11,31,59,.06);}
.rcard-head{padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.rcard-head h2{font-size:.95rem;font-weight:700;color:var(--primary-navy);margin:0;flex:1;}
.rcard-icon{width:32px;height:32px;border-radius:9px;flex-shrink:0;background:rgba(99,102,241,.1);color:#6366f1;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;}
.rcard-body{padding:20px;}
.chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:600;}
.chip-green{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;}
.chip-blue{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;}
.chip-purple{background:#f0f4ff;color:#6366f1;border:1px solid #c7d2fe;}
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
.stat-cell{text-align:center;background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:14px;}
.stat-cell .val{font-size:1.3rem;font-weight:800;color:var(--primary-navy);margin-bottom:4px;}
.stat-cell .lbl{font-size:.68rem;color:var(--text-light);}
.alert{border-radius:12px;padding:14px 16px;display:flex;gap:12px;align-items:flex-start;margin-bottom:16px;}
.alert-green{background:#f0fdf4;border:1px solid #bbf7d0;}
.alert-yellow{background:#fffbeb;border:1px solid #fde68a;}
.alert-red{background:#fef2f2;border:1px solid #fecaca;}
.alert i{margin-top:1px;flex-shrink:0;}
.alert-text{font-size:.85rem;line-height:1.6;}
.sidebar{display:grid;gap:16px;}
.sidebar-card{background:#fff;border:1px solid var(--border-color);border-radius:14px;overflow:hidden;box-shadow:0 4px 16px rgba(11,31,59,.05);}
.sidebar-card-head{padding:12px 16px;border-bottom:1px solid var(--border-color);font-size:.85rem;font-weight:700;color:var(--primary-navy);}
.sidebar-card-body{padding:14px 16px;}
.stat-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color);font-size:.84rem;}
.stat-row:last-child{border-bottom:0;padding-bottom:0;}
.stat-label{color:var(--text-light);font-weight:500;}
.stat-val{font-weight:700;color:var(--primary-navy);}
.action-btn{display:flex;align-items:center;gap:8px;width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:10px;background:#fff;color:var(--primary-navy);font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:8px;transition:background .15s;text-decoration:none;}
.action-btn:hover{background:#f8fafc;}
.action-btn i{width:20px;text-align:center;}
.hist-dot{display:inline-block;width:12px;height:12px;border-radius:3px;}
@media(max-width:1000px){.result-grid{grid-template-columns:1fr;}.sidebar{grid-template-columns:repeat(2,1fr);}}
@media(max-width:640px){.sidebar{grid-template-columns:1fr;}.stats-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:480px){.result-hero{padding:28px 0 24px;}.result-hero h1{font-size:1.4rem;}}
</style>
@endpush

@section('content')
<section class="result-hero">
    <div class="container">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(99,102,241,.15);color:#a5b4fc;border:1px solid rgba(99,102,241,.3);border-radius:999px;padding:4px 14px;font-size:.78rem;font-weight:600;margin-bottom:14px;">
            <i class="fa-solid fa-clock-rotate-left"></i> Complete Financial Check Report
        </div>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <h1 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:4px;">{{ $full_name }}</h1>
                <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;">National ID: {{ $id_number }} &nbsp;·&nbsp; Generated: {{ $report_date }}</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                @if($is_delinquent)
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Delinquent Account(s)
                </span>
                @else
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-circle-check"></i> Good Standing
                </span>
                @endif
                <a href="{{ route('credit-account-history.pdf', $req->id) }}" target="_blank" style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);border:1px solid rgba(255,255,255,.2);border-radius:999px;padding:7px 16px;font-size:.82rem;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-file-pdf"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</section>

<section class="result-main">
<div class="container">
<div class="result-grid">
<div class="result-left">

@if($crbError)
<div class="alert alert-yellow">
    <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i>
    <div class="alert-text">Some data could not be retrieved: <strong>{{ $crbError }}</strong>. Partial results are shown below.</div>
</div>
@endif

{{-- 1. OVERVIEW STATS --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon"><i class="fa-solid fa-chart-bar"></i></div>
        <h2>Account Overview</h2>
        @if($delinquency_code)
        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:700;background:{{ $code_color }}18;color:{{ $code_color }};border:1px solid {{ $code_color }}44;">
            <i class="fa-solid {{ $code_icon }}"></i> {{ $code_label }}
        </span>
        @endif
    </div>
    <div class="rcard-body">
        <div class="stats-grid" style="margin-bottom:18px;">
            <div class="stat-cell">
                <div class="val">{{ count($accounts) }}</div>
                <div class="lbl">Total Accounts</div>
            </div>
            <div class="stat-cell">
                <div class="val" style="color:{{ $total_outstanding > 0 ? '#d97706' : '#16a34a' }};font-size:{{ $total_outstanding > 100000 ? '.9rem' : '1.3rem' }};">
                    {{ $total_outstanding > 0 ? 'KES ' . number_format($total_outstanding) : 'None' }}
                </div>
                <div class="lbl">Total Outstanding</div>
            </div>
            <div class="stat-cell">
                <div class="val" style="color:{{ $total_overdue > 0 ? '#dc2626' : '#16a34a' }};">
                    {{ $total_overdue > 0 ? 'KES ' . number_format($total_overdue) : 'None' }}
                </div>
                <div class="lbl">Total Overdue</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;">
            <div style="text-align:center;background:#f0f4ff;border:1px solid #c7d2fe;border-radius:10px;padding:11px;">
                <div style="font-size:1.2rem;font-weight:800;color:#6366f1;">{{ $active_generic }}</div>
                <div style="font-size:.65rem;color:#4338ca;margin-top:2px;">Active Loans</div>
            </div>
            <div style="text-align:center;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:11px;">
                <div style="font-size:1.2rem;font-weight:800;color:#64748b;">{{ $closed_generic }}</div>
                <div style="font-size:.65rem;color:#94a3b8;margin-top:2px;">Closed Loans</div>
            </div>
            <div style="text-align:center;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:11px;">
                <div style="font-size:1.2rem;font-weight:800;color:#16a34a;">{{ $active_mobile }}</div>
                <div style="font-size:.65rem;color:#166534;margin-top:2px;">Active Mobile</div>
            </div>
            <div style="text-align:center;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:11px;">
                <div style="font-size:1.2rem;font-weight:800;color:#64748b;">{{ $closed_mobile }}</div>
                <div style="font-size:.65rem;color:#94a3b8;margin-top:2px;">Closed Mobile</div>
            </div>
        </div>

        @if($highest_dias > 0)
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:1.3rem;font-weight:800;color:#c2410c;">{{ $highest_dias }}</div>
                <div style="font-size:.68rem;color:#9a3412;margin-top:2px;">Highest Days in Arrears</div>
            </div>
            <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:1.3rem;font-weight:800;color:var(--primary-navy);">{{ $monthly_instalment > 0 ? 'KES ' . number_format($monthly_instalment) : '—' }}</div>
                <div style="font-size:.68rem;color:var(--text-light);margin-top:2px;">Monthly Instalment</div>
            </div>
        </div>
        @endif

        @if($max_credit_score > 0)
        <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:14px;">
            <div style="font-size:.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;"><i class="fa-solid fa-chart-line" style="color:#6366f1;margin-right:4px;"></i> Credit Score Range (12 Months)</div>
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="text-align:center;"><div style="font-size:1.4rem;font-weight:800;color:#16a34a;">{{ $max_credit_score }}</div><div style="font-size:.7rem;color:#64748b;">Peak Score</div></div>
                <div style="flex:1;height:6px;background:#e2e8f0;border-radius:999px;overflow:hidden;">
                    <div style="height:100%;width:{{ min(100, round(($max_credit_score - 200) / 700 * 100)) }}%;background:linear-gradient(90deg,#dc2626,#d97706,#16a34a);border-radius:999px;"></div>
                </div>
                <div style="text-align:center;"><div style="font-size:1.4rem;font-weight:800;color:#6366f1;">{{ $min_credit_score }}</div><div style="font-size:.7rem;color:#64748b;">Lowest Score</div></div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- 2. CREDIT ACCOUNTS WITH HISTORY --}}
@if(!empty($accounts))
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;"><i class="fa-solid fa-building-columns"></i></div>
        <h2>Credit Accounts &amp; Payment History</h2>
        <span class="chip chip-blue">{{ count($accounts) }} account{{ count($accounts) !== 1 ? 's' : '' }}</span>
    </div>
    <div class="rcard-body" style="padding:0;">
        @foreach($accounts as $acc)
        @php
        $acStatus   = strtolower($acc['account_status_name'] ?? 'unknown');
        $isActive   = $acStatus === 'active';
        $acBalance  = (float)($acc['current_balance'] ?? 0);
        $acOverdue  = (float)($acc['overdue_balance'] ?? 0);
        $acDias     = (int)($acc['days_in_arrears'] ?? 0);
        $acOriginal = (float)($acc['original_amount'] ?? 0);
        $acOpened   = $acc['date_opened'] ?? null;
        $acLoaded   = $acc['loaded_at'] ?? null;
        $acNumber   = $acc['account_number'] ?? 'N/A';
        $acInst     = $acc['institution_name'] ?? 'Unknown';
        $acProduct  = $acc['product_type_name'] ?? 'N/A';
        $acDelCode  = (string)($acc['delinquency_code'] ?? '');
        $acHistory  = $acc['account_history'] ?? [];
        $acBad      = $acOverdue > 0 || $acDelCode === '004' || $acDelCode === '005';
        $statusBg   = $isActive ? '#f0fdf4' : '#f8fafc';
        $statusColor= $isActive ? '#16a34a' : '#64748b';
        @endphp
        <div style="border-bottom:1px solid var(--border-color);padding:18px 20px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:.95rem;font-weight:800;color:var(--primary-navy);">{{ $acInst }}</div>
                    <div style="font-size:.8rem;color:var(--text-regular);margin-top:2px;">{{ $acProduct }} &nbsp;·&nbsp; <span style="font-family:monospace;">{{ $acNumber }}</span></div>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <span style="background:{{ $statusBg }};color:{{ $statusColor }};border:1px solid {{ $statusColor }}44;padding:3px 10px;border-radius:999px;font-size:.75rem;font-weight:700;">
                        {{ ucfirst($acStatus) }}
                    </span>
                    @if($acBad)
                    <span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:3px 10px;border-radius:999px;font-size:.75rem;font-weight:700;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Overdue
                    </span>
                    @endif
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;margin-bottom:14px;">
                <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:8px;padding:9px 12px;">
                    <div style="font-size:.62rem;color:var(--text-light);font-weight:700;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px;">Balance</div>
                    <div style="font-size:.88rem;font-weight:800;color:{{ $acBalance > 0 ? 'var(--primary-navy)' : '#6b7280' }};">{{ $acBalance > 0 ? 'KES ' . number_format($acBalance) : '—' }}</div>
                </div>
                <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:8px;padding:9px 12px;">
                    <div style="font-size:.62rem;color:var(--text-light);font-weight:700;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px;">Original</div>
                    <div style="font-size:.88rem;font-weight:800;color:var(--primary-navy);">{{ $acOriginal > 0 ? 'KES ' . number_format($acOriginal) : '—' }}</div>
                </div>
                <div style="background:{{ $acOverdue > 0 ? '#fef2f2' : '#f8fafc' }};border:1px solid {{ $acOverdue > 0 ? '#fecaca' : 'var(--border-color)' }};border-radius:8px;padding:9px 12px;">
                    <div style="font-size:.62rem;color:{{ $acOverdue > 0 ? '#991b1b' : 'var(--text-light)' }};font-weight:700;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px;">Overdue</div>
                    <div style="font-size:.88rem;font-weight:800;color:{{ $acOverdue > 0 ? '#dc2626' : '#6b7280' }};">{{ $acOverdue > 0 ? 'KES ' . number_format($acOverdue) : 'None' }}</div>
                </div>
                <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:8px;padding:9px 12px;">
                    <div style="font-size:.62rem;color:var(--text-light);font-weight:700;text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px;">Opened</div>
                    <div style="font-size:.88rem;font-weight:800;color:var(--primary-navy);">{{ $acOpened ? \Carbon\Carbon::parse($acOpened)->format('M Y') : '—' }}</div>
                </div>
            </div>

            @if(!empty($acHistory))
            <div>
                <div style="font-size:.72rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;"><i class="fa-solid fa-calendar-days" style="color:#6366f1;margin-right:4px;"></i> Payment History</div>
                <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:.78rem;min-width:480px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid var(--border-color);">
                            <th style="padding:6px 10px;text-align:left;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Month</th>
                            <th style="padding:6px 10px;text-align:center;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Status</th>
                            <th style="padding:6px 10px;text-align:right;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Last Payment</th>
                            <th style="padding:6px 10px;text-align:right;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Overdue</th>
                            <th style="padding:6px 10px;text-align:center;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Days Arrears</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($acHistory as $h)
                    @php
                    $hOverdue = (float)($h['overdue_balance'] ?? 0);
                    $hDias    = (int)($h['days_in_arrears'] ?? 0);
                    $hAmount  = (float)($h['last_payment_amount'] ?? 0);
                    $hMonth   = $h['month'] ?? null;
                    $hStatus  = strtolower($h['account_status'] ?? '');
                    $hBad     = $hOverdue > 0 || $hDias > 0;
                    @endphp
                    <tr style="border-bottom:1px solid var(--border-color);{{ $hBad ? 'background:#fffafa;' : '' }}">
                        <td style="padding:7px 10px;color:var(--text-regular);">{{ $hMonth ? \Carbon\Carbon::parse($hMonth)->format('M Y') : '—' }}</td>
                        <td style="padding:7px 10px;text-align:center;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:{{ $hBad ? '#dc2626' : '#16a34a' }};"></span>
                            <span style="font-size:.7rem;color:{{ $hBad ? '#dc2626' : '#16a34a' }};font-weight:600;margin-left:4px;">{{ $hBad ? 'Late' : 'On Time' }}</span>
                        </td>
                        <td style="padding:7px 10px;text-align:right;color:{{ $hAmount > 0 ? 'var(--primary-navy)' : '#9ca3af' }};font-weight:{{ $hAmount > 0 ? '700' : '400' }};">
                            {{ $hAmount > 0 ? 'KES ' . number_format($hAmount) : '—' }}
                        </td>
                        <td style="padding:7px 10px;text-align:right;color:{{ $hOverdue > 0 ? '#dc2626' : '#9ca3af' }};font-weight:{{ $hOverdue > 0 ? '700' : '400' }};">
                            {{ $hOverdue > 0 ? 'KES ' . number_format($hOverdue) : '—' }}
                        </td>
                        <td style="padding:7px 10px;text-align:center;color:{{ $hDias > 0 ? '#dc2626' : '#16a34a' }};font-weight:700;">
                            {{ $hDias > 0 ? $hDias : '0' }}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- 3. PERSONAL DETAILS --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
        <h2>Personal Identity Details</h2>
        <span class="chip chip-green"><i class="fa-solid fa-circle-check"></i> Verified</span>
    </div>
    <div class="rcard-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
            @foreach([['Full Name',$full_name,'fa-user'],['National ID',$id_number,'fa-id-badge'],['Date of Birth',$dob ? \Carbon\Carbon::parse($dob)->format('d M Y') : null,'fa-cake-candles'],['Gender',$gender,'fa-venus-mars'],['Phone',$phone,'fa-phone'],['Email',$email,'fa-envelope'],['Employer',$employer,'fa-briefcase']] as [$label,$val,$icon])
            @if($val)
            <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:12px;">
                <div style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">
                    <i class="fa-solid {{ $icon }}" style="color:#0FA958;margin-right:4px;"></i> {{ $label }}
                </div>
                <div style="font-size:.9rem;font-weight:700;color:#0B1F3B;">{{ $val }}</div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- 4. PAYMENT RECEIPT --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-receipt"></i></div>
        <h2>Payment Receipt</h2>
        <span class="chip chip-green"><i class="fa-solid fa-check"></i> Paid</span>
    </div>
    <div class="rcard-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:.68rem;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Amount Paid</div>
                <div style="font-size:1.3rem;font-weight:800;color:#16a34a;">KES {{ number_format((float)($req->payment_amount ?? $req->price ?? 0)) }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:.68rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">M-Pesa Receipt</div>
                <div style="font-size:.9rem;font-weight:800;color:#0B1F3B;font-family:monospace;">{{ $req->mpesa_receipt_number ?: '—' }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:.68rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Payment Date</div>
                <div style="font-size:.82rem;font-weight:700;color:#0B1F3B;">{{ $req->payment_date ? \Carbon\Carbon::parse($req->payment_date)->format('d M Y H:i') : $req->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

</div>{{-- end result-left --}}

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-card">
        <div class="sidebar-card-head"><i class="fa-solid fa-chart-pie" style="color:#6366f1;margin-right:6px;"></i> Summary</div>
        <div class="sidebar-card-body">
            <div class="stat-row"><span class="stat-label">Standing</span><span class="stat-val" style="color:{{ $code_color }};">{{ $code_label }}</span></div>
            <div class="stat-row"><span class="stat-label">Delinquency Code</span><span class="stat-val">{{ $delinquency_code ?: '—' }}</span></div>
            <div class="stat-row"><span class="stat-label">Total Accounts</span><span class="stat-val">{{ count($accounts) }}</span></div>
            <div class="stat-row"><span class="stat-label">Active Loans</span><span class="stat-val">{{ $active_generic }}</span></div>
            <div class="stat-row"><span class="stat-label">Active Mobile</span><span class="stat-val">{{ $active_mobile }}</span></div>
            @if($total_overdue > 0)<div class="stat-row"><span class="stat-label">Total Overdue</span><span class="stat-val" style="color:#dc2626;font-size:.78rem;">KES {{ number_format($total_overdue) }}</span></div>@endif
            @if($total_outstanding > 0)<div class="stat-row"><span class="stat-label">Outstanding</span><span class="stat-val" style="color:#d97706;font-size:.78rem;">KES {{ number_format($total_outstanding) }}</span></div>@endif
            <div class="stat-row"><span class="stat-label">Report Date</span><span class="stat-val" style="font-size:.72rem;">{{ $report_date }}</span></div>
        </div>
    </div>

    <div class="sidebar-card no-print">
        <div class="sidebar-card-head"><i class="fa-solid fa-share-nodes" style="color:#0ea5e9;margin-right:6px;"></i> Save &amp; Share</div>
        <div class="sidebar-card-body">
            <a href="{{ route('credit-account-history.pdf', $req->id) }}" target="_blank" class="action-btn" style="border:1px solid #c7d2fe;background:#f0f4ff;color:inherit;">
                <i class="fa-solid fa-file-pdf" style="color:#6366f1;"></i> <strong style="color:#4338ca;">Download PDF Report</strong>
            </a>
            <a href="{{ route('credit-account-history') }}" class="action-btn" style="color:inherit;"><i class="fa-solid fa-rotate-left" style="color:#d97706;"></i> Run Another Check</a>
            <a href="{{ route('full-credit-report') }}" class="action-btn" style="color:inherit;"><i class="fa-solid fa-file-lines" style="color:#0ea5e9;"></i> Full Credit Report</a>
        </div>
    </div>
</div>

</div>{{-- end result-grid --}}
</div>
</section>
@endsection
