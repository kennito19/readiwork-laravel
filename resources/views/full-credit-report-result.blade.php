@extends('layouts.app')

@php
/* ── Unpack Type-12 credit_info (fall back to root for legacy cached records) ── */
$ci      = isset($crb['credit_info']) ? $crb['credit_info'] : $crb;
$idv     = $ci['identity_verification'] ?? [];
$scrub   = $ci['identity_scrub']        ?? [];
$accts   = $ci['account_info']          ?? [];
$trend   = $ci['metro_score_trend']     ?? [];
$sectors = $ci['lender_sector']         ?? [];

/* ── Identity ── */
$fullName  = trim(implode(' ', array_filter([$idv['first_name'] ?? '', $idv['other_name'] ?? '', $idv['surname'] ?? ''])));
if (!$fullName && !empty($scrub['names'][0])) $fullName = $scrub['names'][0];
if (!$fullName) $fullName = $req->full_name ?? 'N/A';
$firstName = explode(' ', $fullName)[0] ?: 'there';
$idNum     = $req->national_id ?? ($idv['id_number'] ?? 'N/A');
$dob       = $idv['dob']    ?? ($req->dob    ?? null);
$gender    = $idv['gender'] ?? ($req->gender ?? null);

/* ── Score & delinquency ── */
$score     = $ci['credit_score']      ?? null;
$deliqCode = $ci['delinquency_code']  ?? null;

$scoreColor = '#6b7280'; $scoreLabel = 'No Score';
if ($score !== null) {
    if ($score >= 700)     { $scoreColor = '#16a34a'; $scoreLabel = 'Excellent'; }
    elseif ($score >= 600) { $scoreColor = '#65a30d'; $scoreLabel = 'Good'; }
    elseif ($score >= 500) { $scoreColor = '#d97706'; $scoreLabel = 'Fair'; }
    elseif ($score >= 400) { $scoreColor = '#ea580c'; $scoreLabel = 'Poor'; }
    else                   { $scoreColor = '#dc2626'; $scoreLabel = 'Very Poor'; }
}
$isDelinquent = ($deliqCode === 'D' || $deliqCode === '1' || $deliqCode === 1);

/* ── Accounts — active first, then newest ── */
usort($accts, function($a, $b) {
    $aA = in_array(strtolower($a['account_status'] ?? ''), ['a','active']);
    $bA = in_array(strtolower($b['account_status'] ?? ''), ['a','active']);
    if ($aA !== $bA) return $bA <=> $aA;
    return strcmp($b['opening_date'] ?? '', $a['opening_date'] ?? '');
});
$activeAccts      = array_values(array_filter($accts, fn($a) => in_array(strtolower($a['account_status'] ?? ''), ['a','active'])));
$totalAccts       = count($accts);
$totalActive      = count($activeAccts);
$totalOutstanding = array_sum(array_column($accts, 'outstanding_balance'));
$totalArrears     = array_sum(array_column($accts, 'arrears_amount'));

/* ── Stats (Metropol may return these as arrays or scalars) ── */
$_enqRaw          = $ci['no_of_enquiries']           ?? 0;
$_appRaw          = $ci['no_of_credit_applications'] ?? 0;
$_bcRaw           = $ci['no_of_bounced_cheques']     ?? 0;
$noEnquiries      = is_array($_enqRaw) ? count($_enqRaw) : (int)$_enqRaw;
$noApplications   = is_array($_appRaw) ? count($_appRaw) : (int)$_appRaw;
$noBouncedCheques = is_array($_bcRaw)  ? count($_bcRaw)  : (int)$_bcRaw;
$isGuarantor      = $ci['is_guarantor']  ?? false;
$hasFraud         = $ci['has_fraud']     ?? false;
$trxId            = $ci['trx_id']        ?? null;

/* ── Trend ── */
$trendLabels = []; $trendScores = [];
foreach ($trend as $t) {
    $trendLabels[] = $t['month'] ?? ($t['period'] ?? '');
    $trendScores[] = $t['metro_score'] ?? ($t['score'] ?? 0);
}

/* ── Sector max for bar widths ── */
$maxSectorBal = !empty($sectors)
    ? max(array_map(fn($s) => (float)($s['outstanding_balance'] ?? $s['balance'] ?? 0), $sectors))
    : 1;
if ($maxSectorBal <= 0) $maxSectorBal = 1;
@endphp

@section('title', 'Full Credit Report – {{ $fullName }} – Readiwork')

@push('head')
<style>
/* ── Layout ── */
.result-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:44px 0 36px;color:white}
.result-hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(15,169,88,.15);color:#6ee7a8;border:1px solid rgba(15,169,88,.3);border-radius:999px;padding:5px 14px;font-size:.82rem;font-weight:600;margin-bottom:12px}
.result-hero h1{font-size:2rem;font-weight:800;margin-bottom:6px;line-height:1.2}
.result-hero .meta{color:rgba(255,255,255,.65);font-size:.95rem}
.result-body{background:var(--bg-light);padding:44px 0 80px}
.result-layout{display:grid;grid-template-columns:1fr 340px;gap:28px;align-items:start}

/* ── Cards ── */
.rcard{background:white;border:1px solid var(--border-color);border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(11,31,59,.06);margin-bottom:24px}
.rcard:last-child{margin-bottom:0}
.rcard-title{font-size:1rem;font-weight:700;color:var(--primary-navy);margin-bottom:18px;display:flex;align-items:center;gap:10px}
.rcard-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0}

/* ── Summary banner ── */
.summary-banner{background:white;border:1px solid var(--border-color);border-radius:16px;padding:22px 28px;box-shadow:0 4px 20px rgba(11,31,59,.06);margin-bottom:28px;display:flex;overflow:hidden}
.summary-stat{flex:1;text-align:center;padding:0 14px;border-right:1px solid var(--border-color)}
.summary-stat:last-child{border-right:none}
.summary-stat-val{font-size:1.55rem;font-weight:800;line-height:1.1;margin-bottom:4px}
.summary-stat-lbl{font-size:.72rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em}

/* ── Score donut ── */
.score-wrap{display:flex;align-items:center;gap:32px;flex-wrap:wrap}
.score-chart-wrap{position:relative;width:160px;height:160px;flex-shrink:0}
.score-chart-wrap canvas{width:160px!important;height:160px!important}
.score-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none}
.score-num{font-size:2.1rem;font-weight:900;line-height:1}
.score-sub{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-top:2px}
.score-scale{flex:1;min-width:200px}
.scale-row{display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:.82rem}
.scale-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.scale-bar-wrap{flex:1;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden}
.scale-bar{height:100%;border-radius:3px}

/* ── Alert ── */
.deliq-alert{border-radius:12px;padding:13px 16px;margin-bottom:18px;display:flex;align-items:center;gap:12px;font-size:.9rem;font-weight:600}
.deliq-alert.clean{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.deliq-alert.dirty{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}

/* ── Accounts table ── */
.acct-table-wrap{overflow-x:auto}
.acct-table{width:100%;border-collapse:collapse;font-size:.85rem}
.acct-table th{padding:10px 12px;text-align:left;font-size:.72rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;background:#f8fafc;border-bottom:2px solid var(--border-color);white-space:nowrap}
.acct-table td{padding:11px 12px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.acct-table tr:last-child td{border-bottom:none}
.acct-table tr:hover td{background:#fafbfd}
.badge-pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:.72rem;font-weight:700;white-space:nowrap}
.inst-name{font-weight:600;color:var(--primary-navy)}
.acct-num{font-size:.76rem;color:var(--text-light);margin-top:1px;font-family:monospace}

/* ── Trend chart ── */
.trend-chart-wrap{height:190px;position:relative}

/* ── Sector rows ── */
.sector-row{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid #f1f5f9}
.sector-row:last-child{border-bottom:none}
.sector-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.95rem}
.sector-bar-wrap{flex:1;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden}
.sector-bar{height:100%;border-radius:3px}
.sector-name{font-size:.85rem;font-weight:600;color:var(--primary-navy);margin-bottom:2px}
.sector-count{font-size:.75rem;color:var(--text-light)}

/* ── Stats grid ── */
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.stat-box{background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:16px;text-align:center}
.stat-box-val{font-size:1.75rem;font-weight:800;line-height:1;margin-bottom:4px}
.stat-box-lbl{font-size:.72rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em}

/* ── Flag badges ── */
.flag-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;font-size:.78rem;font-weight:700;margin:3px}

/* ── Contact list ── */
.contact-list{list-style:none;padding:0;margin:0}
.contact-list li{display:flex;align-items:center;gap:9px;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:.88rem}
.contact-list li:last-child{border-bottom:none}

/* ── Sidebar cards ── */
.sidebar-card{background:#fff;border:1px solid var(--border-color);border-radius:14px;overflow:hidden;box-shadow:0 4px 16px rgba(11,31,59,.05);margin-bottom:14px}
.sidebar-card:last-child{margin-bottom:0}
.sidebar-card-head{padding:12px 16px;border-bottom:1px solid var(--border-color);font-size:.85rem;font-weight:700;color:var(--primary-navy)}
.sidebar-card-body{padding:14px 16px}
.action-btn{display:flex;align-items:center;gap:8px;width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:10px;background:#fff;color:var(--primary-navy);font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:8px;transition:background .15s,border-color .15s;text-decoration:none}
.action-btn:last-child{margin-bottom:0}
.action-btn:hover{background:#f8fafc;border-color:#c0e8d5}
.action-btn i{width:20px;text-align:center}
/* ── sidebar summary rows ── */
.sr{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f1f5f9;font-size:.83rem}
.sr:last-child{border-bottom:none}
.sr-l{color:var(--text-light);font-weight:500}
.sr-v{color:var(--text-dark);font-weight:600;text-align:right;max-width:58%}
/* ── sidebar avatar ── */
.sidebar-avatar-wrap{padding:18px;text-align:center;background:var(--primary-navy);color:white}
.sidebar-avatar{width:56px;height:56px;border-radius:50%;background:rgba(15,169,88,.2);border:2px solid rgba(15,169,88,.4);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#6ee7a8;margin:0 auto 8px;text-transform:uppercase}
.sidebar-avatar-wrap h3{font-size:.95rem;font-weight:700;margin-bottom:2px}
.sidebar-avatar-wrap p{font-size:.78rem;opacity:.65;margin:0}
.error-card{background:white;border:1.5px solid #fecaca;border-radius:16px;padding:40px;text-align:center}
/* ── service link buttons ── */
.svc-btn{display:flex;align-items:center;gap:9px;padding:9px 12px;border:1px solid var(--border-color);border-radius:10px;background:#fff;color:var(--primary-navy);font-size:.82rem;font-weight:600;text-decoration:none;transition:background .15s}
.svc-btn:hover{background:#f8fafc;color:var(--primary-navy)}

@media(max-width:980px){.result-layout{grid-template-columns:1fr}.summary-banner{flex-wrap:wrap}.summary-stat{border-right:none;border-bottom:1px solid var(--border-color);padding:10px 0}.summary-stat:last-child{border-bottom:none}}
@media(max-width:600px){.stats-grid{grid-template-columns:1fr 1fr}}
@media print{.result-hero .result-hero-eyebrow,.sidebar-actions,.secured-note{display:none!important}.result-body{padding:0!important;background:white!important}.rcard{box-shadow:none!important}.result-layout{grid-template-columns:1fr!important}}
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<section class="result-hero">
    <div class="container">
        <div class="result-hero-eyebrow">
            <i class="fa-solid fa-file-lines"></i> Full Credit Report &nbsp;&middot;&nbsp; Metropol CRB
        </div>
        <h1>Credit Report for {{ $fullName }}</h1>
        <p class="meta">
            National ID: <strong>{{ $idNum }}</strong>
            @if($trxId) &nbsp;&middot;&nbsp; Ref: <strong>{{ $trxId }}</strong>@endif
            &nbsp;&middot;&nbsp; Generated: <strong>{{ now()->format('d M Y, H:i') }}</strong>
        </p>
    </div>
</section>

{{-- ── Body ── --}}
<section class="result-body">
    <div class="container">

        @if($crbError && empty($ci))
        <div class="error-card">
            <i class="fa-solid fa-triangle-exclamation fa-2x text-danger mb-3 d-block"></i>
            <h3 class="fw-bold text-danger mb-2">Could not load report</h3>
            <p class="text-muted mb-4">{{ $crbError }}</p>
            <a href="{{ route('full-credit-report') }}" class="btn btn-primary">Try Again</a>
        </div>
        @else

        {{-- ── Summary banner ── --}}
        <div class="summary-banner">
            <div class="summary-stat">
                <div class="summary-stat-val" style="color:{{ $scoreColor }}">{{ $score ?? '—' }}</div>
                <div class="summary-stat-lbl">Credit Score</div>
            </div>
            <div class="summary-stat">
                <div class="summary-stat-val" style="color:{{ $isDelinquent ? '#dc2626' : '#16a34a' }}">
                    {{ $isDelinquent ? 'Listed' : 'Clean' }}
                </div>
                <div class="summary-stat-lbl">CRB Status</div>
            </div>
            <div class="summary-stat">
                <div class="summary-stat-val" style="color:var(--primary-navy)">{{ $totalActive }}</div>
                <div class="summary-stat-lbl">Active Accounts</div>
            </div>
            <div class="summary-stat">
                <div class="summary-stat-val" style="color:{{ $totalArrears > 0 ? '#dc2626' : 'var(--primary-navy)' }}">
                    KES {{ number_format($totalOutstanding) }}
                </div>
                <div class="summary-stat-lbl">Total Exposure</div>
            </div>
            @if($totalArrears > 0)
            <div class="summary-stat">
                <div class="summary-stat-val" style="color:#dc2626">KES {{ number_format($totalArrears) }}</div>
                <div class="summary-stat-lbl">Total Arrears</div>
            </div>
            @endif
        </div>

        <div class="result-layout">

            {{-- ═══ LEFT COLUMN ═══ --}}
            <div>

                {{-- Credit Score --}}
                <div class="rcard">
                    <div class="rcard-title">
                        <span class="rcard-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="fa-solid fa-gauge-high"></i></span>
                        Credit Score &amp; Rating
                    </div>

                    @if($isDelinquent)
                    <div class="deliq-alert dirty">
                        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                        This profile has an active <strong>CRB listing (Delinquent)</strong>. This may affect access to credit facilities.
                    </div>
                    @else
                    <div class="deliq-alert clean">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                        No active CRB listings detected. This profile is <strong>clean</strong> with Metropol CRB.
                    </div>
                    @endif

                    <div class="score-wrap">
                        <div class="score-chart-wrap">
                            <canvas id="scoreDonut"></canvas>
                            <div class="score-center">
                                <div class="score-num" style="color:{{ $scoreColor }}">{{ $score ?? '—' }}</div>
                                <div class="score-sub" style="color:{{ $scoreColor }}">{{ $scoreLabel }}</div>
                            </div>
                        </div>
                        <div class="score-scale">
                            @foreach([
                                ['Very Poor', 200, 399, '#dc2626'],
                                ['Poor',      400, 499, '#ea580c'],
                                ['Fair',      500, 599, '#d97706'],
                                ['Good',      600, 699, '#65a30d'],
                                ['Excellent', 700, 900, '#16a34a'],
                            ] as [$lbl,$lo,$hi,$clr])
                            @php $active = ($score !== null && $score >= $lo && $score <= $hi); @endphp
                            <div class="scale-row" style="{{ $active ? 'font-weight:700' : '' }}">
                                <span class="scale-dot" style="background:{{ $clr }}"></span>
                                <span style="width:72px;font-size:.8rem;color:{{ $active ? $clr : 'var(--text-light)' }}">{{ $lbl }}</span>
                                <div class="scale-bar-wrap">
                                    <div class="scale-bar" style="width:{{ round((($hi-$lo+1)/700)*100) }}%;background:{{ $active ? $clr : '#e2e8f0' }}"></div>
                                </div>
                                <span style="font-size:.76rem;color:var(--text-light);min-width:62px;text-align:right">{{ $lo }}–{{ $hi }}</span>
                            </div>
                            @endforeach
                            <p style="margin-top:12px;font-size:.8rem;color:var(--text-light)">Range: 200 (lowest) to 900 (highest). Source: Metropol CRB.</p>
                        </div>
                    </div>
                </div>

                {{-- Metro Score Trend --}}
                @if(count($trendLabels) > 1)
                <div class="rcard">
                    <div class="rcard-title">
                        <span class="rcard-icon" style="background:rgba(217,119,6,.12);color:#d97706"><i class="fa-solid fa-chart-line"></i></span>
                        12-Month Metro Score Trend
                    </div>
                    <div class="trend-chart-wrap">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
                @endif

                {{-- Lender Sector Breakdown --}}
                @if(count($sectors) > 0)
                <div class="rcard">
                    <div class="rcard-title">
                        <span class="rcard-icon" style="background:rgba(22,163,74,.12);color:#16a34a"><i class="fa-solid fa-chart-bar"></i></span>
                        Lender Sector Breakdown
                    </div>
                    @foreach($sectors as $sec)
                    @php
                        $secName  = $sec['sector'] ?? $sec['lender_sector'] ?? 'Other';
                        $secBal   = (float)($sec['outstanding_balance'] ?? $sec['balance'] ?? 0);
                        $secCount = $sec['no_of_accounts'] ?? $sec['count'] ?? null;
                        $barPct   = $maxSectorBal > 0 ? round(($secBal / $maxSectorBal) * 100) : 0;
                        $sn = strtolower($secName);
                        if (str_contains($sn,'bank'))                               { $ico = 'fa-building-columns';     $clr = '#0ea5e9'; }
                        elseif (str_contains($sn,'mfi') || str_contains($sn,'micro')){ $ico = 'fa-hand-holding-dollar'; $clr = '#8b5cf6'; }
                        elseif (str_contains($sn,'sacco'))                           { $ico = 'fa-people-group';         $clr = '#16a34a'; }
                        elseif (str_contains($sn,'digital') || str_contains($sn,'mobile')){ $ico = 'fa-mobile-screen-button'; $clr = '#f59e0b'; }
                        elseif (str_contains($sn,'hire') || str_contains($sn,'vehicle')){ $ico = 'fa-car';               $clr = '#ec4899'; }
                        elseif (str_contains($sn,'insur'))                           { $ico = 'fa-shield-halved';        $clr = '#64748b'; }
                        else                                                         { $ico = 'fa-landmark';             $clr = '#6b7280'; }
                    @endphp
                    <div class="sector-row">
                        <div class="sector-icon" style="background:{{ $clr }}20;color:{{ $clr }}">
                            <i class="fa-solid {{ $ico }}"></i>
                        </div>
                        <div style="flex:1">
                            <div class="sector-name">{{ $secName }}</div>
                            @if($secCount)<div class="sector-count">{{ $secCount }} account{{ $secCount != 1 ? 's' : '' }}</div>@endif
                            <div class="sector-bar-wrap mt-1">
                                <div class="sector-bar" style="width:{{ max(4,$barPct) }}%;background:{{ $clr }}"></div>
                            </div>
                        </div>
                        <div style="font-weight:700;font-size:.88rem;color:var(--text-dark);text-align:right;min-width:95px;padding-left:12px">
                            KES {{ number_format($secBal) }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Enquiries, Applications & Flags --}}
                <div class="rcard">
                    <div class="rcard-title">
                        <span class="rcard-icon" style="background:rgba(220,38,38,.1);color:#dc2626"><i class="fa-solid fa-shield-halved"></i></span>
                        Enquiries, Applications &amp; Flags
                    </div>
                    <div class="stats-grid mb-4">
                        <div class="stat-box">
                            <div class="stat-box-val" style="color:{{ $noEnquiries > 5 ? '#d97706' : 'var(--primary-navy)' }}">{{ $noEnquiries }}</div>
                            <div class="stat-box-lbl">Credit Enquiries</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-box-val" style="color:{{ $noApplications > 3 ? '#d97706' : 'var(--primary-navy)' }}">{{ $noApplications }}</div>
                            <div class="stat-box-lbl">Applications</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-box-val" style="color:{{ $noBouncedCheques > 0 ? '#dc2626' : 'var(--primary-navy)' }}">{{ $noBouncedCheques }}</div>
                            <div class="stat-box-lbl">Bounced Cheques</div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap">
                        <span class="flag-badge" style="background:{{ $isGuarantor ? 'rgba(245,158,11,.12)' : '#f0fdf4' }};color:{{ $isGuarantor ? '#b45309' : '#16a34a' }}">
                            <i class="fa-solid {{ $isGuarantor ? 'fa-user-check' : 'fa-user-xmark' }}"></i>
                            {{ $isGuarantor ? 'Is a Guarantor' : 'Not a Guarantor' }}
                        </span>
                        <span class="flag-badge" style="background:{{ $hasFraud ? 'rgba(220,38,38,.1)' : '#f0fdf4' }};color:{{ $hasFraud ? '#991b1b' : '#16a34a' }}">
                            <i class="fa-solid {{ $hasFraud ? 'fa-triangle-exclamation' : 'fa-shield-check' }}"></i>
                            {{ $hasFraud ? 'Fraud Flag Present' : 'No Fraud Flag' }}
                        </span>
                        <span class="flag-badge" style="background:{{ $isDelinquent ? 'rgba(220,38,38,.1)' : '#f0fdf4' }};color:{{ $isDelinquent ? '#991b1b' : '#16a34a' }}">
                            <i class="fa-solid {{ $isDelinquent ? 'fa-circle-exclamation' : 'fa-circle-check' }}"></i>
                            {{ $isDelinquent ? 'CRB Listed' : 'No CRB Listing' }}
                        </span>
                    </div>
                </div>

                {{-- Identity & Contact Details --}}
                <div class="rcard">
                    <div class="rcard-title">
                        <span class="rcard-icon" style="background:rgba(15,169,88,.12);color:#0FA958"><i class="fa-solid fa-id-card"></i></span>
                        Identity &amp; Contact Details
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Full Name</div>
                            <div style="font-weight:700;color:var(--primary-navy)">{{ $fullName }}</div>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">National ID</div>
                            <div style="font-weight:700;color:var(--primary-navy)">{{ $idNum }}</div>
                        </div>
                        @if($dob)
                        <div class="col-md-6">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Date of Birth</div>
                            <div style="font-weight:600;color:var(--text-dark)">{{ \Carbon\Carbon::parse($dob)->format('d M Y') }}</div>
                        </div>
                        @endif
                        @if($gender)
                        <div class="col-md-6">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Gender</div>
                            <div style="font-weight:600;color:var(--text-dark)">{{ ucfirst(strtolower($gender)) }}</div>
                        </div>
                        @endif
                        @if(!empty($scrub['names']) && count($scrub['names']) > 1)
                        <div class="col-12">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Other Names on Record</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(array_slice($scrub['names'], 1, 8) as $n)
                                <span class="badge-pill" style="background:#f1f5f9;color:var(--text-dark);font-size:.82rem;font-weight:500">{{ $n }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if(!empty($scrub['phones']))
                        <div class="col-md-6">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Phone Numbers</div>
                            <ul class="contact-list">
                                @foreach(array_slice($scrub['phones'], 0, 4) as $p)
                                <li><i class="fa-solid fa-phone fa-sm" style="color:#16a34a;width:16px"></i> {{ $p }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(!empty($scrub['emails']))
                        <div class="col-md-6">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Email Addresses</div>
                            <ul class="contact-list">
                                @foreach(array_slice($scrub['emails'], 0, 3) as $e)
                                <li><i class="fa-solid fa-envelope fa-sm" style="color:#0ea5e9;width:16px"></i> {{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(!empty($scrub['addresses']))
                        <div class="col-12">
                            <div style="font-size:.73rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Addresses</div>
                            <ul class="contact-list">
                                @foreach(array_slice($scrub['addresses'], 0, 3) as $addr)
                                <li>
                                    <i class="fa-solid fa-location-dot fa-sm" style="color:#d97706;width:16px"></i>
                                    {{ is_array($addr) ? implode(', ', array_filter($addr)) : $addr }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Credit Accounts --}}
                <div class="rcard">
                    <div class="rcard-title">
                        <span class="rcard-icon" style="background:rgba(14,165,233,.12);color:#0ea5e9"><i class="fa-solid fa-building-columns"></i></span>
                        Credit Accounts
                        <span class="badge-pill ms-auto" style="background:#f1f5f9;color:var(--text-light)">
                            {{ $totalAccts }} total &middot; {{ $totalActive }} active
                        </span>
                    </div>

                    @if(count($accts) === 0)
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                        No account records found for this profile.
                    </div>
                    @else
                    <div class="acct-table-wrap">
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
                                $status    = strtolower($acct['account_status'] ?? '');
                                $isActive  = in_array($status, ['a','active']);
                                $sLabel    = $isActive ? 'Active' : (in_array($status, ['c','closed']) ? 'Closed' : (str_contains($status,'writ') ? 'Written Off' : ucfirst($acct['account_status'] ?? '—')));
                                $sColor    = $isActive ? '#16a34a' : (str_contains($status,'writ') ? '#dc2626' : '#6b7280');
                                $sBg       = $isActive ? 'rgba(22,163,74,.1)' : (str_contains($status,'writ') ? 'rgba(220,38,38,.1)' : 'rgba(107,114,128,.1)');
                                $dCode     = $acct['delinquency_code'] ?? null;
                                $showDeliq = ($dCode === 'D' || $dCode === '1' || $dCode === 1);
                                $outBal    = (float)($acct['outstanding_balance'] ?? 0);
                                $arrears   = (float)($acct['arrears_amount'] ?? 0);
                                $openDate  = !empty($acct['opening_date']) ? \Carbon\Carbon::parse($acct['opening_date'])->format('M Y') : '—';
                            @endphp
                            <tr>
                                <td>
                                    <div class="inst-name">{{ $acct['institution_name'] ?? 'Unknown' }}</div>
                                    @if(!empty($acct['account_number']))<div class="acct-num">{{ $acct['account_number'] }}</div>@endif
                                    @if($showDeliq)<span class="badge-pill mt-1" style="background:rgba(220,38,38,.1);color:#dc2626">Delinquent</span>@endif
                                </td>
                                <td style="font-size:.82rem;color:var(--text-light)">
                                    {{ $acct['account_type'] ?? '—' }}
                                    @if(!empty($acct['sector']))<br><span style="font-size:.74rem">{{ $acct['sector'] }}</span>@endif
                                </td>
                                <td>
                                    <span class="badge-pill" style="background:{{ $sBg }};color:{{ $sColor }}">{{ $sLabel }}</span>
                                </td>
                                <td style="text-align:right;font-weight:600;color:var(--text-dark)">
                                    {{ $acct['currency'] ?? 'KES' }} {{ number_format($outBal) }}
                                </td>
                                <td style="text-align:right;font-weight:600;color:{{ $arrears > 0 ? '#dc2626' : 'var(--text-light)' }}">
                                    @if($arrears > 0)
                                        {{ number_format($arrears) }}
                                        @if(!empty($acct['arrears_type']))<br><span style="font-size:.72rem">{{ $acct['arrears_type'] }}</span>@endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="font-size:.82rem;color:var(--text-light)">
                                    {{ $openDate }}
                                    @if(!empty($acct['repayment_period']))<br><span style="font-size:.74rem">{{ $acct['repayment_period'] }}m term</span>@endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

            </div>{{-- end left --}}

            {{-- ═══ SIDEBAR ═══ --}}
            <div>

                {{-- Quick Summary --}}
                <div class="sidebar-card">
                    <div class="sidebar-avatar-wrap">
                        <div class="sidebar-avatar">{{ strtoupper(substr($fullName, 0, 1)) }}</div>
                        <h3>{{ $fullName }}</h3>
                        <p>ID: {{ $idNum }}</p>
                    </div>
                    <div class="sidebar-card-body">
                        <div class="sr">
                            <span class="sr-l">Credit Score</span>
                            <span class="sr-v" style="color:{{ $scoreColor }}">{{ $score ?? '—' }} · {{ $scoreLabel }}</span>
                        </div>
                        <div class="sr">
                            <span class="sr-l">CRB Status</span>
                            <span class="sr-v" style="color:{{ $isDelinquent ? '#dc2626' : '#16a34a' }}">{{ $isDelinquent ? 'Listed' : 'Clean' }}</span>
                        </div>
                        <div class="sr">
                            <span class="sr-l">Accounts</span>
                            <span class="sr-v">{{ $totalAccts }} total · {{ $totalActive }} active</span>
                        </div>
                        <div class="sr">
                            <span class="sr-l">Total Exposure</span>
                            <span class="sr-v">KES {{ number_format($totalOutstanding) }}</span>
                        </div>
                        @if($totalArrears > 0)
                        <div class="sr">
                            <span class="sr-l">Total Arrears</span>
                            <span class="sr-v" style="color:#dc2626">KES {{ number_format($totalArrears) }}</span>
                        </div>
                        @endif
                        <div class="sr">
                            <span class="sr-l">Enquiries</span>
                            <span class="sr-v">{{ $noEnquiries }}</span>
                        </div>
                        @if($dob)
                        <div class="sr">
                            <span class="sr-l">Date of Birth</span>
                            <span class="sr-v">{{ \Carbon\Carbon::parse($dob)->format('d M Y') }}</span>
                        </div>
                        @endif
                        @if($gender)
                        <div class="sr">
                            <span class="sr-l">Gender</span>
                            <span class="sr-v">{{ ucfirst(strtolower($gender)) }}</span>
                        </div>
                        @endif
                        @if($trxId)
                        <div class="sr">
                            <span class="sr-l">Report Ref</span>
                            <span class="sr-v" style="font-family:monospace;font-size:.76rem">{{ $trxId }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Save & Share --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-head">
                        <i class="fa-solid fa-share-nodes" style="color:#0FA958;margin-right:6px;"></i> Save &amp; Share
                    </div>
                    <div class="sidebar-card-body">
                        @php
                            $pdfUrl = route('full-credit-report.pdf', $req->id);
                            $waText = "Full Credit Report via Readiwork.\nName: {$fullName}\nNational ID: {$idNum}\nScore: " . ($score ?? 'N/A') . " ({$scoreLabel})\n\nDownload official report:\n{$pdfUrl}";
                        @endphp

                        <button onclick="printFullReport()" class="action-btn no-print">
                            <i class="fa-solid fa-print" style="color:#6366f1;"></i> Print Report
                        </button>

                        <a href="{{ $pdfUrl }}" class="action-btn" style="color:inherit;" target="_blank">
                            <i class="fa-solid fa-file-pdf" style="color:#dc2626;"></i> Download PDF Report
                        </a>

                        <a href="https://wa.me/?text={{ urlencode($waText) }}"
                           target="_blank" class="action-btn" style="color:inherit;">
                            <i class="fa-brands fa-whatsapp" style="color:#16a34a;"></i> Send PDF via WhatsApp
                        </a>

                        <a href="{{ route('full-credit-report') }}" class="action-btn" style="color:inherit;">
                            <i class="fa-solid fa-rotate-left" style="color:#d97706;"></i> Run Another Report
                        </a>
                    </div>
                </div>

                {{-- Other Services --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-head">
                        <i class="fa-solid fa-grid-2" style="color:#0FA958;margin-right:6px;"></i> Other Services
                    </div>
                    <div class="sidebar-card-body" style="display:grid;gap:8px;">
                        <a href="{{ route('credit-score-check') }}" class="svc-btn">
                            <i class="fa-solid fa-gauge-high" style="color:#6366f1;width:18px;text-align:center"></i> Credit Score Check
                        </a>
                        <a href="{{ route('loan-eligibility') }}" class="svc-btn">
                            <i class="fa-solid fa-coins" style="color:#d97706;width:18px;text-align:center"></i> Loan Eligibility
                        </a>
                        <a href="{{ route('crb-blacklist-check') }}" class="svc-btn">
                            <i class="fa-solid fa-ban" style="color:#dc2626;width:18px;text-align:center"></i> Blacklist Check
                        </a>
                        <a href="{{ route('identity-verification') }}" class="svc-btn">
                            <i class="fa-solid fa-id-card" style="color:#0ea5e9;width:18px;text-align:center"></i> Identity Verification
                        </a>
                    </div>
                </div>

                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:8px;font-size:.8rem;color:#166534;margin-top:4px">
                    <i class="fa-solid fa-lock" style="color:#16a34a;flex-shrink:0"></i>
                    Data sourced directly from Metropol CRB. Encrypted &amp; confidential.
                </div>

            </div>{{-- end sidebar --}}

        </div>{{-- end result-layout --}}
        @endif

    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
function printFullReport() {
    var pdfWin = window.open('{{ route('full-credit-report.pdf', $req->id) }}', '_blank');
}
</script>
<script>
/* ── Score donut ── */
(function () {
    const score      = {{ $score ?? 'null' }};
    const scoreColor = '{{ $scoreColor }}';
    const ctx        = document.getElementById('scoreDonut');
    if (!ctx) return;

    const filled = score !== null
        ? Math.max(0, Math.min(100, ((score - 200) / 700) * 100))
        : 0;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [filled, 100 - filled],
                backgroundColor: [scoreColor, '#f1f5f9'],
                borderWidth: 0,
                circumference: 270,
                rotation: 225,
            }]
        },
        options: {
            cutout: '78%',
            responsive: false,
            animation: { duration: 900, easing: 'easeOutQuart' },
            plugins: { tooltip: { enabled: false }, legend: { display: false } }
        }
    });
})();

/* ── Score trend line ── */
(function () {
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    const labels = @json($trendLabels);
    const scores = @json($trendScores);
    if (!labels.length) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Metro Score',
                data: scores,
                fill: true,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#16a34a',
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 700, easing: 'easeOutQuart' },
            scales: {
                y: {
                    min: 200, max: 900,
                    ticks: { stepSize: 100, font: { size: 11 } },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ' Score: ' + c.parsed.y } }
            }
        }
    });
})();
</script>
@endpush
