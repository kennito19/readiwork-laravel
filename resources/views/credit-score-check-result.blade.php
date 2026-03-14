@extends('layouts.app')

@php
$score_data   = $crb['score'] ?? $crb ?? [];
$scrub_data   = $crb['scrub'] ?? [];

// ── Score fields ──
$credit_score = (int)($score_data['credit_score'] ?? 0);
$category     = $score_data['category']     ?? $score_data['score_band'] ?? null;
$as_at        = $score_data['as_at']        ?? null;
$comparatives = $score_data['score_comparatives'] ?? $score_data['comparatives'] ?? [];
$score_3m     = (int)($comparatives['last_3_months']  ?? 0);
$score_6m     = (int)($comparatives['last_6_months']  ?? 0);
$score_12m    = (int)($comparatives['last_12_months'] ?? 0);
$score_best   = (int)($comparatives['best']  ?? 0);
$score_worst  = (int)($comparatives['worst'] ?? 0);
$ppi          = $score_data['ppi']  ?? $score_data['payment_performance_index'] ?? null;
$pod          = $score_data['probability_of_default'] ?? $score_data['pod'] ?? null;

// ── Scrub / identity fields ──
$scrub_names    = $scrub_data['names']            ?? [];
$scrub_dob      = $scrub_data['date_of_being']    ?? [];
$scrub_gender   = $scrub_data['gender']           ?? [];
$scrub_phones   = $scrub_data['phone']            ?? [];
$scrub_emails   = $scrub_data['email']            ?? [];
$scrub_postal   = $scrub_data['postal_address']   ?? [];
$scrub_physical = $scrub_data['physical_address'] ?? [];
$scrub_employ   = $scrub_data['employment']       ?? [];

$full_name = $scrub_names[0] ?? $req->full_name ?? 'Applicant';
$dob_raw   = $scrub_dob[0]   ?? null;
$gender    = $scrub_gender[0] ?? null;
$id_number = $req->national_id;

try {
    $dob_fmt = $dob_raw ? \Carbon\Carbon::parse($dob_raw)->format('j F Y') : null;
} catch (\Exception $e) {
    $dob_fmt = $dob_raw;
}

$gender_label = match(strtoupper((string)$gender)) {
    'M' => 'Male', 'F' => 'Female', default => $gender ?: null,
};

$has_scrub = !empty($scrub_names) || !empty($scrub_dob) || !empty($scrub_phones);

// ── Score helpers ──
$score_tier = function(int $s): array {
    if ($s >= 700) return ['Excellent', '#16a34a', '#f0fdf4', '#bbf7d0'];
    if ($s >= 600) return ['Good',      '#0ea5e9', '#eff6ff', '#bfdbfe'];
    if ($s >= 500) return ['Fair',      '#d97706', '#fffbeb', '#fde68a'];
    if ($s >= 400) return ['Poor',      '#f97316', '#fff7ed', '#fed7aa'];
    if ($s > 0)    return ['Very Poor', '#dc2626', '#fef2f2', '#fecaca'];
    return ['No Score', '#6b7280', '#f8fafc', '#e2e8f0'];
};
[$tier_label, $tier_color, $tier_bg, $tier_border] = $score_tier($credit_score);
$score_pct = $credit_score > 0 ? round(($credit_score - 200) / 700 * 100) : 0;

$ppi_map = ['M1'=>['#16a34a',90],'M2'=>['#2563eb',78],'M3'=>['#0891b2',65],'M4'=>['#d97706',50],'M5'=>['#f97316',35],'M6'=>['#dc2626',22],'M7'=>['#b91c1c',12],'M8'=>['#7f1d1d',5],'M9'=>['#450a0a',2]];
$ppi_key   = strtoupper((string)$ppi);
$ppi_color = $ppi_map[$ppi_key][0] ?? '#6b7280';
$ppi_pct   = $ppi_map[$ppi_key][1] ?? 50;

$pod_val   = is_numeric($pod) ? round((float)$pod * 100, 1) . '%' : $pod;
$pod_pct   = is_numeric($pod) ? min(100, (float)$pod * 100) : 0;
$pod_color = $pod_pct < 20 ? '#16a34a' : ($pod_pct < 50 ? '#d97706' : '#dc2626');

$report_date = now()->format('j F Y, g:i A');
$rid = $req->id;
@endphp

@section('title', 'Credit Score Report — ' . $full_name . ' - Readiwork')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
.result-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:48px 0 44px;position:relative;overflow:hidden;}
.result-hero::before{content:'';position:absolute;top:-40%;right:-5%;width:55%;height:180%;background:radial-gradient(ellipse,rgba(15,169,88,.09) 0%,transparent 65%);pointer-events:none;}
.result-main{padding:36px 0 80px;background:var(--bg-light);}
.result-grid{display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;}
.result-left{display:grid;gap:20px;}
.rcard{background:#fff;border:1px solid var(--border-color);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(11,31,59,.06);}
.rcard-head{padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.rcard-head h2{font-size:.95rem;font-weight:700;color:var(--primary-navy);margin:0;flex:1;}
.rcard-icon{width:32px;height:32px;border-radius:9px;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;}
.rcard-body{padding:20px;}
.chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:600;}
.chip-green{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;}
.chip-blue{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;}
.score-banner{padding:36px 28px;text-align:center;background:linear-gradient(135deg,#0b1f3a,#0d2649);}
.score-number{font-size:5rem;font-weight:800;line-height:1;margin-bottom:4px;}
.score-denom{font-size:1.2rem;color:rgba(255,255,255,.45);font-weight:600;margin-bottom:16px;}
.score-tier-badge{display:inline-flex;align-items:center;gap:7px;border-radius:999px;padding:6px 18px;font-size:.88rem;font-weight:700;margin-bottom:20px;}
.score-bar-track{position:relative;height:14px;background:linear-gradient(90deg,#dc2626 0%,#f97316 25%,#eab308 50%,#84cc16 75%,#22c55e 100%);border-radius:999px;margin:0 auto 8px;max-width:400px;}
.score-bar-needle{position:absolute;top:50%;transform:translate(-50%,-50%);width:20px;height:20px;background:#fff;border-radius:50%;border:3px solid var(--primary-navy);box-shadow:0 2px 8px rgba(0,0,0,.3);}
.score-bar-labels{display:flex;justify-content:space-between;font-size:.68rem;color:rgba(255,255,255,.35);max-width:400px;margin:0 auto 20px;}
.score-interpretation{font-size:.88rem;color:rgba(255,255,255,.65);max-width:380px;margin:0 auto;line-height:1.6;}
.score-donut-wrap{position:relative;width:200px;height:200px;margin:0 auto;}
.score-donut-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;}
.comp-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;}
.comp-cell{text-align:center;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:12px 4px;}
.comp-cell .val{font-size:1.1rem;font-weight:800;}
.comp-cell .lbl{font-size:.62rem;color:var(--text-light);margin-top:3px;}
.risk-meters{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.risk-meter{background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:14px;}
.risk-meter-label{font-size:.75rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;}
.risk-meter-val{font-size:1.25rem;font-weight:800;margin-bottom:6px;}
.risk-meter-bar-wrap{background:#e5e7eb;border-radius:999px;height:6px;overflow:hidden;}
.risk-meter-bar{height:100%;border-radius:999px;}
.id-field{background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:14px 16px;}
.id-field .lbl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light);margin-bottom:6px;display:flex;align-items:center;gap:6px;}
.id-field .val{font-size:.95rem;font-weight:700;color:var(--primary-navy);word-break:break-word;}
.tip-box{display:flex;align-items:flex-start;gap:12px;background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:14px 16px;}
.tip-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.tip-title{font-size:.88rem;font-weight:700;color:var(--primary-navy);margin-bottom:3px;}
.tip-text{font-size:.8rem;color:var(--text-regular);line-height:1.5;}
.sidebar{display:grid;gap:16px;}
.sidebar-card{background:#fff;border:1px solid var(--border-color);border-radius:14px;overflow:hidden;box-shadow:0 4px 16px rgba(11,31,59,.05);}
.sidebar-card-head{padding:12px 16px;border-bottom:1px solid var(--border-color);font-size:.85rem;font-weight:700;color:var(--primary-navy);}
.sidebar-card-body{padding:14px 16px;}
.stat-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color);font-size:.84rem;}
.stat-row:last-child{border-bottom:0;padding-bottom:0;}
.stat-label{color:var(--text-light);font-weight:500;}
.stat-val{font-weight:700;color:var(--primary-navy);}
.action-btn{display:flex;align-items:center;gap:8px;width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:10px;background:#fff;color:var(--primary-navy);font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:8px;transition:background .15s,border-color .15s;text-decoration:none;}
.action-btn:hover{background:#f8fafc;border-color:#c0e8d5;}
.action-btn i{width:20px;text-align:center;}
@media print{.sidebar,nav,footer,.no-print{display:none !important;}.result-grid{grid-template-columns:1fr;}}
@media(max-width:1000px){.result-grid{grid-template-columns:1fr;}.sidebar{grid-template-columns:repeat(2,1fr);}.comp-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:640px){.sidebar{grid-template-columns:1fr;}.risk-meters{grid-template-columns:1fr;}.comp-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:480px){.result-hero{padding:28px 0 24px;}.result-hero h1{font-size:1.4rem;}}
</style>
@endpush

@section('content')
<section class="result-hero">
    <div class="container">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(99,102,241,.15);color:#a5b4fc;border:1px solid rgba(99,102,241,.3);border-radius:999px;padding:4px 14px;font-size:.78rem;font-weight:600;margin-bottom:14px;">
            <i class="fa-solid fa-gauge-high"></i> Credit Score Report
        </div>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <h1 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:4px;">{{ $full_name }}</h1>
                <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;">National ID: {{ $id_number }} &nbsp;&middot;&nbsp; Generated: {{ $report_date }}</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                @if($credit_score > 0)
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(165,180,252,.15);color:#a5b4fc;border:1px solid rgba(165,180,252,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-gauge-high"></i> Score: {{ $credit_score }} / 900
                </span>
                @endif
                <button onclick="window.print()" class="no-print" style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);border:1px solid rgba(255,255,255,.2);border-radius:999px;padding:7px 16px;font-size:.82rem;font-weight:600;cursor:pointer;">
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
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px 16px;display:flex;gap:12px;align-items:flex-start;">
    <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;margin-top:1px;flex-shrink:0;"></i>
    <div style="font-size:.85rem;line-height:1.6;">Some data could not be retrieved: <strong>{{ $crbError }}</strong>. Partial results are shown below.</div>
</div>
@endif

{{-- 1. SCORE BANNER --}}
<div class="rcard">
    <div class="score-banner">
        <div class="score-number" style="color:{{ $tier_color }};">{{ $credit_score ?: '—' }}</div>
        <div class="score-denom">/ 900</div>
        <div class="score-tier-badge" style="background:{{ $tier_bg }};color:{{ $tier_color }};border:1px solid {{ $tier_border }};">
            <i class="fa-solid fa-gauge-high"></i> {{ $tier_label }} Credit
        </div>
        @if($credit_score > 0)
        <div class="score-bar-track">
            <div class="score-bar-needle" style="left:{{ $score_pct }}%;"></div>
        </div>
        <div class="score-bar-labels">
            <span>200 (Poor)</span><span>450 (Fair)</span><span>700 (Good)</span><span>900 (Excellent)</span>
        </div>
        @endif
        <div class="score-interpretation">
            @if($credit_score >= 700) Excellent standing! Lenders view you as a very low-risk borrower. You are pre-qualified for the best loan rates.
            @elseif($credit_score >= 600) Good credit standing. Most lenders will approve your loan applications with competitive interest rates.
            @elseif($credit_score >= 500) Fair credit. Some lenders will approve you, but you may face higher interest rates. Consider improving your score.
            @elseif($credit_score >= 400) Poor credit. Most lenders will decline your application. Clear outstanding debts to improve your score.
            @elseif($credit_score > 0) Very poor credit. You will face significant challenges getting loan approval. Urgent action is needed.
            @else No credit score data is available. This may mean you have no credit history yet.
            @endif
        </div>
    </div>
</div>

{{-- 2. SCORE GAUGE --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-gauge-high"></i></div>
        <h2>Credit Score Gauge</h2>
        <span class="chip" style="background:{{ $tier_bg }};color:{{ $tier_color }};border:1px solid {{ $tier_border }};">{{ $tier_label }}</span>
    </div>
    <div class="rcard-body" style="text-align:center;">
        <div class="score-donut-wrap">
            <canvas id="scoreGauge" width="200" height="200"></canvas>
            <div class="score-donut-center">
                <div style="font-size:2.2rem;font-weight:800;color:{{ $tier_color }};">{{ $credit_score ?: '—' }}</div>
                <div style="font-size:.7rem;color:var(--text-light);font-weight:600;">/ 900</div>
            </div>
        </div>
        <div style="margin-top:12px;font-size:.85rem;color:var(--text-regular);">
            Your score of <strong style="color:{{ $tier_color }};">{{ $credit_score }}</strong> is in the <strong>{{ $tier_label }}</strong> range on the national 200–900 credit scale.
        </div>
        @if($as_at)
        <div style="margin-top:8px;font-size:.78rem;color:var(--text-light);">Score as at: {{ $as_at }}</div>
        @endif
        @if($category)
        <div style="margin-top:6px;"><span class="chip chip-blue">Category: {{ $category }}</span></div>
        @endif
    </div>
</div>

{{-- 3. HISTORICAL COMPARATIVES --}}
@if($score_3m || $score_6m || $score_12m || $score_best || $score_worst)
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;"><i class="fa-solid fa-chart-line"></i></div>
        <h2>Historical Score Comparatives</h2>
    </div>
    <div class="rcard-body">
        <div class="comp-grid">
            @foreach([[$score_3m,'3-Month Score'],[$score_6m,'6-Month Score'],[$score_12m,'12-Month Score']] as $cv)
            <div class="comp-cell">
                <div class="val" style="color:{{ $score_tier($cv[0])[1] ?? '#6b7280' }};">{{ $cv[0] ?: '—' }}</div>
                <div class="lbl">{{ $cv[1] }}</div>
                @if($cv[0])<div style="font-size:.55rem;color:var(--text-light);">/ 900</div>@endif
            </div>
            @endforeach
            <div class="comp-cell">
                <div class="val" style="color:#16a34a;">{{ $score_best ?: '—' }}</div>
                <div class="lbl">Best Ever</div>
                @if($score_best)<div style="font-size:.55rem;color:var(--text-light);">/ 900</div>@endif
            </div>
            <div class="comp-cell">
                <div class="val" style="color:#dc2626;">{{ $score_worst ?: '—' }}</div>
                <div class="lbl">Worst Ever</div>
                @if($score_worst)<div style="font-size:.55rem;color:var(--text-light);">/ 900</div>@endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- 4. RISK METERS --}}
@if($ppi || $pod !== null)
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(245,158,11,.1);color:#d97706;"><i class="fa-solid fa-chart-bar"></i></div>
        <h2>Risk Analysis</h2>
    </div>
    <div class="rcard-body">
        <div class="risk-meters">
            @if($ppi)
            <div class="risk-meter">
                <div class="risk-meter-label">Payment Performance Index (PPI)</div>
                <div class="risk-meter-val" style="color:{{ $ppi_color }};">{{ $ppi }}</div>
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:6px;">Higher is better (M1=best, M9=worst)</div>
                <div class="risk-meter-bar-wrap"><div class="risk-meter-bar" style="width:{{ $ppi_pct }}%;background:{{ $ppi_color }};"></div></div>
            </div>
            @endif
            @if($pod !== null)
            <div class="risk-meter">
                <div class="risk-meter-label">Probability of Default (POD)</div>
                <div class="risk-meter-val" style="color:{{ $pod_color }};">{{ $pod_val }}</div>
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:6px;">Lower is better — likelihood of defaulting</div>
                <div class="risk-meter-bar-wrap"><div class="risk-meter-bar" style="width:{{ $pod_pct }}%;background:{{ $pod_color }};"></div></div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- 5. IDENTITY DETAILS FROM SCRUB --}}
@if($has_scrub)
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
        <h2>Personal Details</h2>
        <span class="chip chip-green"><i class="fa-solid fa-circle-check"></i> Verified</span>
    </div>
    <div class="rcard-body">
        {{-- Core identity fields --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:20px;">
            @if(!empty($scrub_names[0]))
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-user" style="color:#0FA958;"></i> Full Name</div>
                <div class="val">{{ $scrub_names[0] }}</div>
            </div>
            @endif
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-id-badge" style="color:#0FA958;"></i> National ID</div>
                <div class="val">{{ $id_number }}</div>
            </div>
            @if($dob_fmt)
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-cake-candles" style="color:#0FA958;"></i> Date of Birth</div>
                <div class="val">{{ $dob_fmt }}</div>
            </div>
            @endif
            @if($gender_label)
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-venus-mars" style="color:#0FA958;"></i> Gender</div>
                <div class="val">{{ $gender_label }}</div>
            </div>
            @endif
            @if(!empty($scrub_phones[0]))
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-phone" style="color:#0FA958;"></i> Phone</div>
                <div class="val">{{ $scrub_phones[0] }}</div>
            </div>
            @endif
            @if(!empty($scrub_emails[0]))
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-envelope" style="color:#0FA958;"></i> Email</div>
                <div class="val" style="font-size:.82rem;">{{ $scrub_emails[0] }}</div>
            </div>
            @endif
        </div>

        {{-- Address --}}
        @if(!empty($scrub_postal[0]) || !empty($scrub_physical[0]))
        <div style="margin-bottom:16px;">
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light);margin-bottom:10px;">Address Information</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;">
                @if(!empty($scrub_postal[0]))
                @php $p = $scrub_postal[0]; @endphp
                <div class="id-field">
                    <div class="lbl"><i class="fa-solid fa-mailbox" style="color:#6366f1;"></i> Postal Address</div>
                    <div class="val" style="font-size:.84rem;">
                        @if(!empty($p['number']))P.O. Box {{ $p['number'] }}@endif
                        @if(!empty($p['code'])) – {{ $p['code'] }}@endif
                        @if(!empty($p['town']))<br>{{ $p['town'] }}@endif
                        @if(!empty($p['country']))<br>{{ $p['country'] }}@endif
                    </div>
                </div>
                @endif
                @if(!empty($scrub_physical[0]))
                @php $ph = $scrub_physical[0]; @endphp
                <div class="id-field">
                    <div class="lbl"><i class="fa-solid fa-location-dot" style="color:#6366f1;"></i> Physical Address</div>
                    <div class="val" style="font-size:.84rem;">
                        @if(!empty($ph['town'])){{ $ph['town'] }}@endif
                        @if(!empty($ph['address']))<br>{{ $ph['address'] }}@endif
                        @if(!empty($ph['country']))<br>{{ $ph['country'] }}@endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Employment --}}
        @if(!empty($scrub_employ[0]) && !empty($scrub_employ[0]['employer_name']))
        <div>
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light);margin-bottom:10px;">Employment</div>
            <div style="display:grid;gap:8px;">
                @foreach($scrub_employ as $emp)
                @if(!empty($emp['employer_name']))
                <div style="display:flex;align-items:center;gap:10px;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:11px 14px;">
                    <div style="width:30px;height:30px;border-radius:8px;background:rgba(14,165,233,.1);color:#0ea5e9;display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;"><i class="fa-solid fa-briefcase"></i></div>
                    <div>
                        <div style="font-size:.88rem;font-weight:700;color:var(--primary-navy);">{{ $emp['employer_name'] }}</div>
                        @if(!empty($emp['employment_date']))<div style="font-size:.75rem;color:var(--text-light);">Since {{ $emp['employment_date'] }}</div>@endif
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- 6. SCORE TIPS --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-lightbulb"></i></div>
        <h2>What This Means for You</h2>
    </div>
    <div class="rcard-body" style="display:grid;gap:10px;">
        @if($credit_score >= 700)
            @foreach([
                ['fa-check',           'rgba(15,169,88,.1)',  '#16a34a', 'Eligible for Best Rates',   'You qualify for the lowest interest rates most lenders offer.'],
                ['fa-arrow-up',        'rgba(99,102,241,.1)', '#6366f1', 'Keep It Up',                 'Pay all loans on time and keep credit utilisation low.'],
                ['fa-building-columns','rgba(14,165,233,.1)', '#0ea5e9', 'Consider More Credit',       'Having a diverse credit mix can help maintain your score.'],
            ] as $tip)
            <div class="tip-box"><div class="tip-icon" style="background:{{ $tip[1] }};color:{{ $tip[2] }};"><i class="fa-solid {{ $tip[0] }}"></i></div><div><div class="tip-title">{{ $tip[3] }}</div><div class="tip-text">{{ $tip[4] }}</div></div></div>
            @endforeach
        @elseif($credit_score >= 500)
            @foreach([
                ['fa-calendar-check', 'rgba(245,158,11,.1)', '#d97706', 'Pay On Time',   'Payment history is the biggest factor in your score.'],
                ['fa-chart-line',     'rgba(99,102,241,.1)', '#6366f1', 'Reduce Debt',   'Keep credit usage below 30% of your limit.'],
                ['fa-clock',          'rgba(15,169,88,.1)',  '#16a34a', 'Allow Time',    '6 months of clean repayment can meaningfully improve your score.'],
            ] as $tip)
            <div class="tip-box"><div class="tip-icon" style="background:{{ $tip[1] }};color:{{ $tip[2] }};"><i class="fa-solid {{ $tip[0] }}"></i></div><div><div class="tip-title">{{ $tip[3] }}</div><div class="tip-text">{{ $tip[4] }}</div></div></div>
            @endforeach
        @elseif($credit_score > 0)
            @foreach([
                ['fa-money-bill-wave',  'rgba(239,68,68,.1)',  '#dc2626', 'Clear Debts',  'Paying defaulted loans improves your score fastest.'],
                ['fa-handshake',        'rgba(99,102,241,.1)', '#6366f1', 'Negotiate',    'Arrange repayment plans with your lenders.'],
                ['fa-clock-rotate-left','rgba(15,169,88,.1)',  '#16a34a', 'Be Patient',   '12 months of good payments can rebuild your credit.'],
            ] as $tip)
            <div class="tip-box"><div class="tip-icon" style="background:{{ $tip[1] }};color:{{ $tip[2] }};"><i class="fa-solid {{ $tip[0] }}"></i></div><div><div class="tip-title">{{ $tip[3] }}</div><div class="tip-text">{{ $tip[4] }}</div></div></div>
            @endforeach
        @else
            <div class="tip-box"><div class="tip-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-info-circle"></i></div><div><div class="tip-title">No Score Yet</div><div class="tip-text">You may not have any credit history. Taking a small loan and repaying on time is the fastest way to build a score.</div></div></div>
        @endif
    </div>
</div>

{{-- 7. PAYMENT RECEIPT --}}
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
                <div style="font-size:.95rem;font-weight:800;color:#0B1F3B;font-family:monospace;">{{ $req->mpesa_receipt_number ?: '—' }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;padding:14px;text-align:center;">
                <div style="font-size:.68rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Payment Date</div>
                <div style="font-size:.88rem;font-weight:700;color:#0B1F3B;">{{ $req->payment_date ? \Carbon\Carbon::parse($req->payment_date)->format('d M Y H:i') : $req->created_at->format('d M Y H:i') }}</div>
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

</div>{{-- end result-left --}}

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-card">
        <div class="sidebar-card-head"><i class="fa-solid fa-gauge-high" style="color:#6366f1;margin-right:6px;"></i> Quick Stats</div>
        <div class="sidebar-card-body">
            <div style="text-align:center;padding:16px 0;border-bottom:1px solid var(--border-color);margin-bottom:12px;">
                <div style="font-size:2.8rem;font-weight:800;color:{{ $tier_color }};">{{ $credit_score ?: '—' }}</div>
                <div style="font-size:.78rem;color:var(--text-light);margin-top:2px;">/ 900 Credit Score</div>
                <div style="margin-top:8px;display:inline-flex;align-items:center;gap:5px;background:{{ $tier_bg }};color:{{ $tier_color }};border:1px solid {{ $tier_border }};border-radius:999px;padding:3px 10px;font-size:.78rem;font-weight:700;">{{ $tier_label }}</div>
            </div>
            <div class="stat-row"><span class="stat-label">Name</span><span class="stat-val" style="font-size:.78rem;">{{ $full_name }}</span></div>
            <div class="stat-row"><span class="stat-label">National ID</span><span class="stat-val" style="font-size:.82rem;">{{ $id_number }}</span></div>
            @if($as_at)<div class="stat-row"><span class="stat-label">Score As At</span><span class="stat-val" style="font-size:.78rem;">{{ $as_at }}</span></div>@endif
            @if($ppi)<div class="stat-row"><span class="stat-label">PPI Rating</span><span class="stat-val" style="color:{{ $ppi_color }};">{{ $ppi }}</span></div>@endif
            @if($score_3m)<div class="stat-row"><span class="stat-label">3-Month Avg</span><span class="stat-val">{{ $score_3m }}</span></div>@endif
            @if($score_best)<div class="stat-row"><span class="stat-label">Best Score</span><span class="stat-val" style="color:#16a34a;">{{ $score_best }}</span></div>@endif
            @if($category)<div class="stat-row"><span class="stat-label">Category</span><span class="stat-val" style="font-size:.78rem;">{{ $category }}</span></div>@endif
            <div class="stat-row"><span class="stat-label">Report Date</span><span class="stat-val" style="font-size:.72rem;">{{ $report_date }}</span></div>
        </div>
    </div>

    <div class="sidebar-card no-print">
        <div class="sidebar-card-head"><i class="fa-solid fa-share-nodes" style="color:#0ea5e9;margin-right:6px;"></i> Save &amp; Share</div>
        <div class="sidebar-card-body">
            <button onclick="window.print()" class="action-btn"><i class="fa-solid fa-print" style="color:#6366f1;"></i> Print Report</button>
            <a href="https://wa.me/?text={{ urlencode('My CRB Credit Score: ' . $credit_score . '/900 (' . $tier_label . '). Checked via Readiwork — readi.work') }}" target="_blank" class="action-btn" style="color:inherit;"><i class="fa-brands fa-whatsapp" style="color:#16a34a;"></i> Share via WhatsApp</a>
            <button onclick="downloadPDF()" class="action-btn" id="pdfBtn" style="border:1px solid #bbf7d0;background:#f0fdf4;font-family:inherit;">
                <i class="fa-solid fa-file-pdf" style="color:#16a34a;"></i> <strong style="color:#15803d;">Download PDF Report</strong>
            </button>
            <a href="{{ route('credit-score-check') }}" class="action-btn" style="color:inherit;"><i class="fa-solid fa-rotate-left" style="color:#d97706;"></i> Run Another Check</a>
        </div>
    </div>

    <div class="sidebar-card">
        <div class="sidebar-card-head"><i class="fa-solid fa-circle-question" style="color:#d97706;margin-right:6px;"></i> Score Scale</div>
        <div class="sidebar-card-body" style="display:grid;gap:8px;">
            @foreach([['700–900','Excellent','#16a34a'],['600–699','Good','#0ea5e9'],['500–599','Fair','#d97706'],['400–499','Poor','#f97316'],['200–399','Very Poor','#dc2626']] as $t)
            @php $rng = explode('–', $t[0]); $isAct = $credit_score && $credit_score >= (int)$rng[0] && $credit_score <= (int)($rng[1] ?? 900); @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.78rem;{{ $isAct ? 'font-weight:800;' : '' }}">
                <span style="font-weight:700;color:var(--primary-navy);">{{ $t[0] }}</span>
                <span style="background:{{ $t[2] }}22;color:{{ $t[2] }};border:1px solid {{ $t[2] }}44;padding:2px 8px;border-radius:999px;font-weight:700;">{{ $t[1] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>{{-- end result-grid --}}
</div>
</section>
@endsection

@push('scripts')
<script>
const SCORE     = {{ $credit_score ?: 0 }};
const SCORE_COL = '{{ $tier_color }}';

(function() {
    const ctx = document.getElementById('scoreGauge');
    if (!ctx || !SCORE) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: { datasets: [{ data: [Math.max(0,SCORE-200), 700-Math.max(0,SCORE-200)], backgroundColor: [SCORE_COL, '#f1f5f9'], borderWidth: 0, borderRadius: 5 }] },
        options: { cutout: '75%', rotation: -90, circumference: 180, animation: { animateRotate: true, duration: 1200 }, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
    });
})();

async function downloadPDF() {
    const btn = document.getElementById('pdfBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating PDF\u2026';
    const hide = [...document.querySelectorAll('nav, footer, .no-print, .sidebar')];
    hide.forEach(el => { el.dataset.pdfdisplay = el.style.display; el.style.display = 'none'; });
    try {
        const { jsPDF } = window.jspdf;
        const canvas = await html2canvas(document.body, { scale: 2, useCORS: true, allowTaint: true, backgroundColor: '#f8fafc', scrollX: 0, scrollY: -window.scrollY, windowWidth: document.documentElement.scrollWidth, windowHeight: document.documentElement.scrollHeight });
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
        const pdfW = pdf.internal.pageSize.getWidth(), pdfH = pdf.internal.pageSize.getHeight();
        const ratio = pdfW / canvas.width, pageSliceH = Math.floor(pdfH / ratio);
        let y = 0, first = true;
        const tmp = document.createElement('canvas'), tmpCtx = tmp.getContext('2d');
        while (y < canvas.height) {
            const sliceH = Math.min(pageSliceH, canvas.height - y);
            tmp.width = canvas.width; tmp.height = sliceH;
            tmpCtx.drawImage(canvas, 0, y, canvas.width, sliceH, 0, 0, canvas.width, sliceH);
            if (!first) pdf.addPage();
            pdf.addImage(tmp.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, pdfW, sliceH * ratio);
            y += sliceH; first = false;
        }
        pdf.save('Readiwork-CreditScore-{{ $rid }}.pdf');
    } catch(e) { console.error(e); alert('Could not generate PDF. Please try again.'); }
    finally {
        hide.forEach(el => { el.style.display = el.dataset.pdfdisplay || ''; });
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-file-pdf" style="color:#16a34a;"></i> <strong style="color:#15803d;">Download PDF Report</strong>';
    }
}
</script>
@endpush
