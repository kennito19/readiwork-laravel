@extends('layouts.app')

@php
$delinquency  = $crb['delinquency']  ?? [];
$scrub_data   = $crb['scrub']        ?? [];
$credit_info  = $crb['credit_info']  ?? [];

$full_name   = $scrub_data['names'][0] ?? $req->full_name ?? 'Applicant';
$id_number   = $req->national_id;
$dob         = $scrub_data['date_of_being'][0] ?? null;
$gender_raw  = $scrub_data['gender'][0] ?? null;
$gender      = $gender_raw ? match(strtoupper((string)$gender_raw)) { 'M' => 'Male', 'F' => 'Female', default => $gender_raw } : null;
$scrub_phones  = $scrub_data['phone']            ?? [];
$scrub_emails  = $scrub_data['email']            ?? [];
$scrub_employ  = $scrub_data['employment']       ?? [];

$delinquency_code    = (string)($delinquency['delinquency_code'] ?? $delinquency['deliquency_code'] ?? '');
$delinquency_summary = $delinquency['delinquency_summary'] ?? null;
$outstanding_bal     = (float)($delinquency['outstanding_balance'] ?? $credit_info['total_outstanding_balance'] ?? 0);
$no_facilities       = (int)($delinquency['no_of_facilities'] ?? $credit_info['total_accounts'] ?? 0);
$overdue_amount      = (float)($credit_info['total_overdue_amount'] ?? $delinquency['overdue_amount'] ?? 0);
$npa_accounts        = (int)($credit_info['npa_accounts'] ?? $credit_info['total_npa'] ?? 0);
$performing_accounts = (int)($credit_info['performing_accounts'] ?? max(0, $no_facilities - $npa_accounts));
$accounts            = $credit_info['account_info'] ?? $credit_info['credit_accounts'] ?? $credit_info['accounts'] ?? [];
if (!empty($accounts)) {
    usort($accounts, function($a, $b) {
        $a_active = !in_array($a['account_status'] ?? '', ['Closed','Settled','closed','settled']);
        $b_active = !in_array($b['account_status'] ?? '', ['Closed','Settled','closed','settled']);
        if ($a_active !== $b_active) return $b_active <=> $a_active;
        // Within same group: most recent date_opened first
        $a_date = $a['date_opened'] ?? $a['loaded_at'] ?? '';
        $b_date = $b['date_opened'] ?? $b['loaded_at'] ?? '';
        return strcmp($b_date, $a_date);
    });
}

// Metropol codes: 001=ID not found, 002=no credit info, 003=no delinquency (clean), 004=currently delinquent, 005=historical delinquency
$code_info = [
    '001' => ['Not in CRB Database',              '#6b7280', 'fa-circle-question',     false],
    '002' => ['No Credit History',                '#0ea5e9', 'fa-circle-info',          false],
    '003' => ['CRB Clear — No Delinquency',       '#16a34a', 'fa-circle-check',         false],
    '004' => ['Currently Blacklisted',            '#dc2626', 'fa-triangle-exclamation', true],
    '005' => ['Historical Delinquency on Record', '#d97706', 'fa-clock-rotate-left',    false],
];
[$code_label, $code_color, $code_icon, $code_bad] = $code_info[$delinquency_code] ?? ['Unknown Status','#6b7280','fa-question',false];
$is_blacklisted  = $code_bad;
$is_historical   = ($delinquency_code === '005');

$explanations = [
    '001' => 'Your National ID was not found in the CRB database. This could mean you have never taken a formal loan, or your ID has not yet been registered with any lender.',
    '002' => 'Your ID is recognised by the CRB but you have no credit history on record. You have not been blacklisted — you simply have no loan activity reported.',
    '003' => 'Your credit record is clean. You have credit history and all your accounts are performing. No active or historical blacklisting found.',
    '004' => 'You are currently blacklisted by the CRB. One or more of your loans are non-performing right now. This is blocking new loan approvals. Contact the lender to settle the debt.',
    '005' => 'You had a non-performing loan at some point in the past, but it appears to have been resolved. Some lenders may still view this cautiously. Consider running a full credit report for the complete picture.',
];

$report_date = now()->format('j F Y, g:i A');
$rid = $req->id;
@endphp

@section('title', 'CRB Blacklist Status Report — ' . $full_name . ' - Readiwork')

@push('head')
<style>
.result-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:48px 0 44px;position:relative;overflow:hidden;}
.result-hero::before{content:'';position:absolute;top:-40%;right:-5%;width:55%;height:180%;background:radial-gradient(ellipse,rgba(15,169,88,.09) 0%,transparent 65%);pointer-events:none;}
.result-main{padding:36px 0 80px;background:var(--bg-light);}
.result-grid{display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;}
.result-left{display:grid;gap:20px;}
.rcard{background:#fff;border:1px solid var(--border-color);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(11,31,59,.06);}
.rcard-head{padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.rcard-head h2{font-size:.95rem;font-weight:700;color:var(--primary-navy);margin:0;flex:1;}
.rcard-icon{width:32px;height:32px;border-radius:9px;flex-shrink:0;background:rgba(15,169,88,.1);color:var(--primary-green);display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;}
.rcard-body{padding:20px;}
.chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:600;}
.chip-green{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;}
.chip-red{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;}
.chip-blue{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;}
.status-banner{padding:36px 28px;text-align:center;}
.status-banner.clear{background:linear-gradient(135deg,#052e16,#064e1d);}
.status-banner.caution{background:linear-gradient(135deg,#451a03,#7c2d12);}
.status-banner.blacklist{background:linear-gradient(135deg,#1c0a0a,#3b0f0f);}
.status-emoji{font-size:3.2rem;display:block;margin-bottom:12px;}
.status-name{font-size:.88rem;color:rgba(255,255,255,.5);margin-bottom:6px;}
.status-title{font-size:1.65rem;font-weight:800;color:#fff;margin-bottom:8px;}
.status-sub{color:rgba(255,255,255,.6);font-size:.88rem;max-width:420px;margin:0 auto 20px;line-height:1.6;}
.status-code-badge{display:inline-flex;align-items:center;gap:8px;border-radius:999px;padding:8px 22px;font-size:.92rem;font-weight:700;}
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
.stat-cell{text-align:center;background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:14px;}
.stat-cell .val{font-size:1.3rem;font-weight:800;color:var(--primary-navy);margin-bottom:4px;}
.stat-cell .lbl{font-size:.68rem;color:var(--text-light);}
.alert{border-radius:12px;padding:14px 16px;display:flex;gap:12px;align-items:flex-start;margin-bottom:16px;}
.alert-green{background:#f0fdf4;border:1px solid #bbf7d0;}
.alert-red{background:#fef2f2;border:1px solid #fecaca;}
.alert-yellow{background:#fffbeb;border:1px solid #fde68a;}
.alert i{margin-top:1px;flex-shrink:0;}
.alert-text{font-size:.85rem;line-height:1.6;}
.code-table{width:100%;border-collapse:collapse;font-size:.83rem;}
.code-table th{padding:8px 12px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--text-light);background:#f8fafc;border-bottom:2px solid var(--border-color);text-align:left;}
.code-table td{padding:10px 12px;border-bottom:1px solid var(--border-color);}
.code-table tr:last-child td{border-bottom:0;}
.code-table tr.active-row td{background:#f8fafc;font-weight:600;}
.recovery-step{display:flex;gap:14px;align-items:flex-start;padding:14px;background:#fff5f5;border:1px solid #fecaca;border-radius:10px;margin-bottom:10px;}
.recovery-step:last-child{margin-bottom:0;}
.recovery-num{width:28px;height:28px;border-radius:50%;background:#dc2626;color:#fff;font-size:.82rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.recovery-step h4{font-size:.88rem;font-weight:700;color:var(--primary-navy);margin-bottom:2px;}
.recovery-step p{font-size:.78rem;color:var(--text-regular);line-height:1.5;}
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
@media print{.sidebar,nav,footer,.no-print{display:none !important;}.result-grid{grid-template-columns:1fr;}}
@media(max-width:1000px){.result-grid{grid-template-columns:1fr;}.sidebar{grid-template-columns:repeat(2,1fr);}}
@media(max-width:640px){.sidebar{grid-template-columns:1fr;}.stats-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:480px){.result-hero{padding:28px 0 24px;}.result-hero h1{font-size:1.4rem;}}
</style>
@endpush

@section('content')
<section class="result-hero">
    <div class="container">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.3);border-radius:999px;padding:4px 14px;font-size:.78rem;font-weight:600;margin-bottom:14px;">
            <i class="fa-solid fa-triangle-exclamation"></i> CRB Blacklist Status Report
        </div>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <h1 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:4px;">{{ $full_name }}</h1>
                <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;">National ID: {{ $id_number }} &nbsp;·&nbsp; Generated: {{ $report_date }}</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                @if($is_blacklisted)
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-circle-xmark"></i> CRB Listed
                </span>
                @elseif($is_historical)
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Historical Delinquency
                </span>
                @else
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-circle-check"></i> CRB Clear
                </span>
                @endif
                <button onclick="printPdfReport()" class="no-print" style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);border:1px solid rgba(255,255,255,.2);border-radius:999px;padding:7px 16px;font-size:.82rem;font-weight:600;cursor:pointer;">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
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

{{-- 1. STATUS BANNER --}}
<div class="rcard">
    @if($delinquency_code === '003')
    <div class="status-banner clear">
        <span class="status-emoji">✅</span>
        <div class="status-name">{{ $full_name }}</div>
        <div class="status-title">CRB Clear</div>
        <div class="status-sub">You are NOT blacklisted. You have credit history and all your accounts are performing with no delinquency.</div>
        <div class="status-code-badge" style="background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);">
            <i class="fa-solid fa-circle-check"></i> {{ $code_label }}
        </div>
    </div>
    @elseif(in_array($delinquency_code, ['001','002']))
    <div class="status-banner clear">
        <span class="status-emoji">✅</span>
        <div class="status-name">{{ $full_name }}</div>
        <div class="status-title">Not Blacklisted</div>
        <div class="status-sub">{{ $delinquency_code === '001' ? 'Your National ID was not found in the CRB database — you have no blacklisting record.' : 'Your ID is on record but you have no credit history. You have not been blacklisted.' }}</div>
        <div class="status-code-badge" style="background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);">
            <i class="fa-solid {{ $code_icon }}"></i> {{ $code_label }}
        </div>
    </div>
    @elseif($is_historical)
    <div class="status-banner caution">
        <span class="status-emoji">⚠️</span>
        <div class="status-name">{{ $full_name }}</div>
        <div class="status-title">Historical Delinquency</div>
        <div class="status-sub">You had a non-performing loan in the past. It appears resolved, but the history remains on record. Some lenders may still flag this.</div>
        <div class="status-code-badge" style="background:rgba(251,191,36,.18);color:#fbbf24;border:1px solid rgba(251,191,36,.35);">
            <i class="fa-solid {{ $code_icon }}"></i> {{ $code_label }}
        </div>
    </div>
    @elseif($is_blacklisted)
    <div class="status-banner blacklist">
        <span class="status-emoji">🚨</span>
        <div class="status-name">{{ $full_name }}</div>
        <div class="status-title">CRB Listed</div>
        <div class="status-sub">You are currently blacklisted by the CRB. One or more loans are non-performing right now. This is blocking your loan approvals. You must settle the debt to clear your record.</div>
        <div class="status-code-badge" style="background:rgba(239,68,68,.18);color:#f87171;border:1px solid rgba(239,68,68,.35);">
            <i class="fa-solid {{ $code_icon }}"></i> {{ $code_label }}
        </div>
    </div>
    @else
    <div class="status-banner" style="background:linear-gradient(135deg,#1a1a2e,#16213e);">
        <span class="status-emoji">ℹ️</span>
        <div class="status-name">{{ $full_name }}</div>
        <div class="status-title">Status Retrieved</div>
        <div class="status-sub">{{ $delinquency_code ? 'Code: ' . $delinquency_code . '. ' : '' }}{{ $code_label }}</div>
    </div>
    @endif
</div>

{{-- 2. STATUS DETAILS --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;"><i class="fa-solid fa-chart-bar"></i></div>
        <h2>Status Details</h2>
    </div>
    <div class="rcard-body">
        <div class="stats-grid" style="margin-bottom:20px;">
            <div class="stat-cell">
                <div class="val">{{ $no_facilities ?: '0' }}</div>
                <div class="lbl">Total Credit Facilities</div>
            </div>
            <div class="stat-cell">
                <div class="val" style="color:{{ $outstanding_bal > 0 ? '#d97706' : '#16a34a' }};font-size:{{ $outstanding_bal > 0 ? '1rem' : '1.3rem' }};">
                    {{ $outstanding_bal > 0 ? 'KES ' . number_format($outstanding_bal) : 'None' }}
                </div>
                <div class="lbl">Outstanding Balance</div>
            </div>
            <div class="stat-cell">
                <div class="val" style="font-size:1rem;font-weight:700;color:{{ $code_color }};">{{ $delinquency_code ?: '—' }}</div>
                <div class="lbl">Delinquency Code</div>
            </div>
        </div>

        @if($npa_accounts > 0 || $performing_accounts > 0 || $overdue_amount > 0)
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px;">
            @if($performing_accounts > 0)
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:11px;text-align:center;">
                <div style="font-size:1.2rem;font-weight:800;color:#16a34a;">{{ $performing_accounts }}</div>
                <div style="font-size:.66rem;color:#166534;margin-top:2px;">Performing</div>
            </div>
            @endif
            @if($npa_accounts > 0)
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:11px;text-align:center;">
                <div style="font-size:1.2rem;font-weight:800;color:#dc2626;">{{ $npa_accounts }}</div>
                <div style="font-size:.66rem;color:#991b1b;margin-top:2px;">Non-Performing</div>
            </div>
            @endif
            @if($overdue_amount > 0)
            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:11px;text-align:center;">
                <div style="font-size:.95rem;font-weight:800;color:#c2410c;">KES {{ number_format($overdue_amount) }}</div>
                <div style="font-size:.66rem;color:#9a3412;margin-top:2px;">Overdue Amount</div>
            </div>
            @endif
        </div>
        @endif

        @if($delinquency_summary)
        <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:14px;margin-bottom:16px;">
            <div style="font-size:.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Delinquency Summary</div>
            <div style="font-size:.88rem;color:var(--text-regular);line-height:1.6;">{{ $delinquency_summary }}</div>
        </div>
        @endif

        <div class="alert {{ $is_blacklisted ? 'alert-red' : ($delinquency_code === '003' ? 'alert-yellow' : 'alert-green') }}">
            <i class="fa-solid {{ $code_icon }}" style="color:{{ $code_color }};"></i>
            <div class="alert-text">
                <strong>{{ $code_label }}</strong> &mdash;
                {{ $explanations[$delinquency_code] ?? 'Your CRB status has been retrieved. Please review the details below.' }}
            </div>
        </div>
    </div>
</div>

{{-- PERSONAL IDENTITY --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
        <h2>Personal Identity Details</h2>
        <span class="chip chip-green"><i class="fa-solid fa-circle-check"></i> Verified</span>
    </div>
    <div class="rcard-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
            @foreach([['Full Name',$full_name,'fa-user'],['National ID',$id_number,'fa-id-badge'],['Date of Birth',$dob ? \Carbon\Carbon::parse($dob)->format('d M Y') : null,'fa-cake-candles'],['Gender',$gender,'fa-venus-mars'],['Phone',$scrub_phones[0] ?? null,'fa-phone'],['Email',$scrub_emails[0] ?? null,'fa-envelope'],['Employer',$scrub_employ[0] ?? null,'fa-briefcase']] as [$label,$val,$icon])
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

{{-- LOAN ACCOUNTS --}}
@if(!empty($accounts))
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-building-columns"></i></div>
        <h2>Credit Account Details</h2>
        <span class="chip chip-blue">{{ count($accounts) }} account{{ count($accounts) !== 1 ? 's' : '' }}</span>
    </div>
    <div class="rcard-body" style="padding:0;">
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid var(--border-color);">
                    <th style="padding:10px 14px;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Lender / Institution</th>
                    <th style="padding:10px 14px;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Loan Type</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Balance</th>
                    <th style="padding:10px 14px;text-align:right;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Arrears</th>
                    <th style="padding:10px 14px;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Status</th>
                    <th style="padding:10px 14px;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--text-light);">Opened</th>
                </tr>
            </thead>
            <tbody>
            @foreach($accounts as $acc)
            @php
            $lender_raw  = $acc['subscriber_name'] ?? $acc['lender'] ?? $acc['institution'] ?? $acc['lender_name'] ?? null;
            $lender      = $lender_raw ?: ('Acct: ' . ($acc['account_number'] ?? '—'));
            $acc_type    = $acc['account_type'] ?? $acc['product_type'] ?? $acc['facility_type'] ?? '—';
            $acc_bal     = (float)($acc['current_balance'] ?? $acc['outstanding_balance'] ?? $acc['balance'] ?? 0);
            $acc_overdue = (float)($acc['overdue_balance'] ?? $acc['arrears'] ?? $acc['overdue_amount'] ?? 0);
            $acc_status  = $acc['account_status'] ?? $acc['status'] ?? $acc['performance_status'] ?? '';
            $acc_opened  = $acc['date_opened'] ?? $acc['open_date'] ?? $acc['account_date'] ?? null;
            $is_npa      = stripos($acc_status,'npa')!==false || stripos($acc_status,'default')!==false || stripos($acc_status,'loss')!==false || stripos($acc_status,'write')!==false || $acc_overdue > 0;
            $status_color = $is_npa ? '#dc2626' : '#16a34a';
            $status_bg    = $is_npa ? '#fef2f2' : '#f0fdf4';
            $status_label = $acc_status ?: ($is_npa ? 'Non-Performing' : 'Performing');
            @endphp
            <tr style="border-bottom:1px solid var(--border-color);{{ $is_npa ? 'background:#fffafa;' : '' }}">
                <td style="padding:12px 14px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($is_npa)<i class="fa-solid fa-triangle-exclamation" style="color:#dc2626;font-size:.75rem;"></i>@endif
                        <div style="font-weight:700;color:var(--primary-navy);font-size:.84rem;">{{ $lender }}</div>
                    </div>
                </td>
                <td style="padding:12px 14px;color:var(--text-regular);">{{ $acc_type }}</td>
                <td style="padding:12px 14px;text-align:right;font-weight:700;color:{{ $acc_bal > 0 ? 'var(--primary-navy)' : '#6b7280' }};">
                    {{ $acc_bal > 0 ? 'KES ' . number_format($acc_bal) : '—' }}
                </td>
                <td style="padding:12px 14px;text-align:right;font-weight:700;color:{{ $acc_overdue > 0 ? '#dc2626' : '#6b7280' }};">
                    {{ $acc_overdue > 0 ? 'KES ' . number_format($acc_overdue) : '—' }}
                </td>
                <td style="padding:12px 14px;text-align:center;">
                    <span style="background:{{ $status_bg }};color:{{ $status_color }};border:1px solid {{ $status_color }}44;padding:3px 9px;border-radius:999px;font-size:.72rem;font-weight:700;white-space:nowrap;">
                        {{ ucwords(strtolower($status_label)) }}
                    </span>
                </td>
                <td style="padding:12px 14px;text-align:center;color:var(--text-light);font-size:.78rem;white-space:nowrap;">
                    {{ $acc_opened ? \Carbon\Carbon::parse($acc_opened)->format('M Y') : '—' }}
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @if($outstanding_bal > 0 || $overdue_amount > 0)
        <div style="padding:14px 16px;background:#fffbeb;border-top:2px solid #fde68a;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            @if($overdue_amount > 0)
            <div><span style="font-size:.75rem;font-weight:600;color:#92400e;">Total Overdue</span><span style="font-size:1rem;font-weight:800;color:#dc2626;margin-left:10px;">KES {{ number_format($overdue_amount) }}</span></div>
            @endif
            @if($outstanding_bal > 0)
            <div><span style="font-size:.75rem;font-weight:600;color:#92400e;">Total Outstanding</span><span style="font-size:1rem;font-weight:800;color:#d97706;margin-left:10px;">KES {{ number_format($outstanding_bal) }}</span></div>
            @endif
        </div>
        @endif
    </div>
</div>
@elseif($is_blacklisted)
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(239,68,68,.1);color:#dc2626;"><i class="fa-solid fa-building-columns"></i></div>
        <h2>Loan Account Details</h2>
    </div>
    <div class="rcard-body">
        <div class="alert alert-yellow">
            <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i>
            <div class="alert-text">
                <strong>Detailed account list not available for this record.</strong> Your delinquency status indicates a problem on your credit record. To get the full list of which specific loans and lenders have listed you, upgrade to a <a href="{{ route('full-credit-report') }}" style="color:#d97706;font-weight:700;">Full Credit Report</a> — it includes a complete breakdown of every account, lender, outstanding amount, and repayment history.
            </div>
        </div>
    </div>
</div>
@endif

{{-- PAYMENT RECEIPT --}}
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
        @if($req->payment_phone)
        <div style="margin-top:10px;padding:10px 14px;background:#f8fafc;border:1px solid var(--border-color);border-radius:8px;font-size:.82rem;color:#64748b;">
            <i class="fa-solid fa-phone" style="color:#0FA958;margin-right:6px;"></i>
            Payment from: <strong style="color:#0B1F3B;">{{ $req->payment_phone }}</strong>
        </div>
        @endif
    </div>
</div>

{{-- DELINQUENCY CODE REFERENCE --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-table-list"></i></div>
        <h2>Delinquency Code Reference</h2>
    </div>
    <div class="rcard-body" style="padding:0;overflow-x:auto;">
        <table class="code-table">
            <thead>
                <tr><th style="width:60px;">Code</th><th>Status Label</th><th>Plain-English Meaning</th><th style="width:80px;">Your Status</th></tr>
            </thead>
            <tbody>
                @foreach(['001'=>['#6b7280','Not in CRB Database','National ID not found in the CRB database. No credit record exists.'],'002'=>['#0ea5e9','No Credit History','ID found but no credit information on record. Not blacklisted.'],'003'=>['#16a34a','CRB Clear','Has credit history with no delinquency. All accounts performing.'],'004'=>['#dc2626','Currently Blacklisted','Has at least one active non-performing loan right now.'],'005'=>['#d97706','Historical Delinquency','Had a non-performing loan in the past. May be resolved.']] as $code=>[$color,$label,$meaning])
                @php $is_current = ($code === $delinquency_code); @endphp
                <tr {{ $is_current ? 'class=active-row' : '' }}>
                    <td style="font-weight:800;color:{{ $color }};font-family:monospace;font-size:.95rem;">{{ $code }}</td>
                    <td>
                        <span style="background:{{ $color }}22;color:{{ $color }};border:1px solid {{ $color }}44;padding:2px 8px;border-radius:999px;font-size:.78rem;font-weight:700;">{{ $label }}</span>
                    </td>
                    <td style="color:var(--text-regular);font-size:.82rem;">{{ $meaning }}</td>
                    <td style="text-align:center;">
                        @if($is_current)
                        <span style="background:{{ $color }};color:#fff;padding:3px 8px;border-radius:6px;font-size:.72rem;font-weight:700;">YOU</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- RECOVERY PLAN --}}
@if($is_blacklisted)
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(239,68,68,.1);color:#dc2626;"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <h2>Your Recovery Plan</h2>
        <span class="chip chip-red">Action Required</span>
    </div>
    <div class="rcard-body">
        <div class="alert alert-red" style="margin-bottom:18px;">
            <i class="fa-solid fa-triangle-exclamation" style="color:#dc2626;"></i>
            <div class="alert-text">Follow these steps in order to clear your CRB listing and restore your ability to access credit.</div>
        </div>
        @foreach([['Contact the Lender Directly','Reach out to the institution that listed you and ask for a full account statement. Confirm the outstanding amount and request to negotiate a repayment plan.'],['Get a Written Settlement Letter','Once you have cleared the debt (or agreed a settlement amount), request a formal written letter from the lender confirming that the debt has been fully settled.'],['Request the Lender to Update Your CRB Listing','Ask the lender to submit an update to the Credit Reference Bureau removing or revising your listing. This process can take up to 30 days. Follow up if necessary.'],['Follow Up with the CRB Directly','Contact Metropol CRB or the relevant bureau to confirm that your record has been updated. You can dispute any inaccurate listings directly with the bureau.'],['Run Another Blacklist Check After 60 Days','Once you believe the listing has been cleared, run a new CRB blacklist check on Readiwork to confirm your status is CRB Clear before applying for any new credit.']] as $i=>$step)
        <div class="recovery-step">
            <div class="recovery-num">{{ $i+1 }}</div>
            <div>
                <h4>{{ $step[0] }}</h4>
                <p>{{ $step[1] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

</div>{{-- end result-left --}}

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-card">
        <div class="sidebar-card-head"><i class="fa-solid fa-shield-halved" style="color:{{ $code_color }};margin-right:6px;"></i> CRB Status</div>
        <div class="sidebar-card-body" style="text-align:center;">
            <div style="font-size:3rem;margin:12px 0 8px;">{{ $is_blacklisted ? '🚨' : ($is_historical ? '⚠️' : '✅') }}</div>
            <div style="font-size:1rem;font-weight:800;color:{{ $code_color }};margin-bottom:4px;">
                {{ $is_blacklisted ? 'CRB LISTED' : ($is_historical ? 'HISTORICAL DELINQUENCY' : 'CRB CLEAR') }}
            </div>
            <div style="font-size:.75rem;color:var(--text-light);margin-bottom:14px;">Delinquency Code {{ $delinquency_code ?: '—' }}</div>
            <div style="background:{{ $code_color }}18;border:1px solid {{ $code_color }}44;border-radius:10px;padding:8px 12px;font-size:.8rem;color:{{ $code_color }};font-weight:600;text-align:left;">{{ $code_label }}</div>
        </div>
    </div>

    <div class="sidebar-card">
        <div class="sidebar-card-head"><i class="fa-solid fa-chart-pie" style="color:var(--primary-green);margin-right:6px;"></i> Quick Summary</div>
        <div class="sidebar-card-body">
            <div class="stat-row"><span class="stat-label">CRB Status</span><span class="stat-val" style="color:{{ $code_color }};">{{ $is_blacklisted ? 'Listed' : ($is_historical ? 'Historical' : 'Clear') }}</span></div>
            <div class="stat-row"><span class="stat-label">Delinquency Code</span><span class="stat-val">{{ $delinquency_code ?: '—' }}</span></div>
            <div class="stat-row"><span class="stat-label">Facilities</span><span class="stat-val">{{ $no_facilities }}</span></div>
            @if($npa_accounts > 0)<div class="stat-row"><span class="stat-label">Non-Performing</span><span class="stat-val" style="color:#dc2626;">{{ $npa_accounts }}</span></div>@endif
            @if($overdue_amount > 0)<div class="stat-row"><span class="stat-label">Overdue</span><span class="stat-val" style="color:#dc2626;font-size:.78rem;">KES {{ number_format($overdue_amount) }}</span></div>@endif
            @if($outstanding_bal > 0)<div class="stat-row"><span class="stat-label">Outstanding</span><span class="stat-val" style="color:#d97706;font-size:.78rem;">KES {{ number_format($outstanding_bal) }}</span></div>@endif
            <div class="stat-row"><span class="stat-label">Report Date</span><span class="stat-val" style="font-size:.72rem;">{{ $report_date }}</span></div>
        </div>
    </div>

    <div class="sidebar-card no-print">
        <div class="sidebar-card-head"><i class="fa-solid fa-share-nodes" style="color:#0ea5e9;margin-right:6px;"></i> Save &amp; Share</div>
        <div class="sidebar-card-body">
            <button onclick="printPdfReport()" class="action-btn"><i class="fa-solid fa-print" style="color:#6366f1;"></i> Print Report</button>
            <a href="https://wa.me/?text={{ urlencode('My CRB Status: ' . ($is_blacklisted ? 'CRB Listed' : 'CRB Clear') . ' (Code ' . $delinquency_code . '). Checked via Readiwork — ' . route('crb-blacklist-check.pdf', $req->id)) }}" target="_blank" class="action-btn" style="color:inherit;"><i class="fa-brands fa-whatsapp" style="color:#16a34a;"></i> Share via WhatsApp</a>
            <a href="{{ route('crb-blacklist-check.pdf', $req->id) }}" target="_blank" class="action-btn" style="border:1px solid #bbf7d0;background:#f0fdf4;color:inherit;">
                <i class="fa-solid fa-file-pdf" style="color:#16a34a;"></i> <strong style="color:#15803d;">Download PDF Report</strong>
            </a>
            <a href="{{ route('crb-blacklist-check') }}" class="action-btn" style="color:inherit;"><i class="fa-solid fa-rotate-left" style="color:#d97706;"></i> Run Another Check</a>
        </div>
    </div>
</div>

</div>{{-- end result-grid --}}
</div>
</section>
@endsection

@push('scripts')
<script>
function printPdfReport() {
    var win = window.open('{{ route('crb-blacklist-check.pdf', $req->id) }}', '_blank');
    if (win) { win.onload = function() { try { win.print(); } catch(e) {} }; }
}
</script>
@endpush
