@extends('layouts.app')

@php
// ── Unpack Type-12 credit_info ──────────────────────────────────────────────
$ci    = isset($crb['credit_info']) ? $crb['credit_info'] : $crb;
$idv   = $ci['identity_verification'] ?? [];
$scrub = $ci['identity_scrub']        ?? [];

// ── Full Name ──────────────────────────────────────────────────────────────
$parts      = array_filter([$idv['first_name'] ?? '', $idv['other_name'] ?? '', $idv['surname'] ?? '']);
$full_name  = trim(implode(' ', $parts)) ?: ($scrub['names'][0] ?? $req->full_name ?? 'Applicant');
$first_name = explode(' ', $full_name)[0];

// ── Identity fields ────────────────────────────────────────────────────────
$dob         = $idv['dob']         ?? ($req->dob    ?? null);
$gender      = $idv['gender']      ?? ($req->gender ?? null);
$citizenship = $idv['citizenship'] ?? 'Kenyan';
$district    = $idv['district']    ?? null;
$sub_county  = $idv['sub_county']  ?? null;
$id_number   = $req->national_id;

// ── Credit Score ──────────────────────────────────────────────────────────
$credit_score  = (int)($ci['credit_score'] ?? 0);
$ppi           = null;
$pod           = null;
$score_3m = $score_6m = $score_12m = $score_best = $score_worst = 0;

// ── Delinquency ──────────────────────────────────────────────────────────
$delinquency_code = $ci['delinquency_code'] ?? null;
$is_defaulting    = ($delinquency_code === 'D' || $delinquency_code === '1' || $delinquency_code === 1);

// ── Accounts ──────────────────────────────────────────────────────────────
$accounts = $ci['account_info'] ?? [];
usort($accounts, function($a, $b) {
    $aA = in_array(strtolower($a['account_status'] ?? ''), ['a','active']);
    $bA = in_array(strtolower($b['account_status'] ?? ''), ['a','active']);
    if ($aA !== $bA) return $bA <=> $aA;
    return strcmp($b['opening_date'] ?? '', $a['opening_date'] ?? '');
});
$outstanding_bal = array_sum(array_column($accounts, 'outstanding_balance'));
$overdue_amount  = array_sum(array_column($accounts, 'arrears_amount'));
$no_facilities   = count($accounts);
$npa_accounts    = count(array_filter($accounts, fn($a) =>
    ($a['delinquency_code'] ?? null) === 'D' || strtolower($a['account_status'] ?? '') === 'w'
));
$credit_events = [];
$institutions  = [];

// ── Sector data ────────────────────────────────────────────────────────────
$lender_sector_raw = $ci['lender_sector'] ?? [];
$sector_data = [];
foreach ($lender_sector_raw as $s) {
    $sname = $s['sector'] ?? $s['lender_sector'] ?? null;
    if (!$sname) continue;
    $sector_data[] = [
        'sector'  => $sname,
        'total'   => (int)($s['no_of_accounts'] ?? $s['count'] ?? 0),
        'npa'     => 0, 'perf' => 0, 'clean' => 0,
        'balance' => (float)($s['outstanding_balance'] ?? $s['balance'] ?? 0),
    ];
}

// ── Score helpers ──────────────────────────────────────────────────────────
$score_tiers = [
    [800, 'Excellent',    '#16a34a', '#f0fdf4', '#bbf7d0'],
    [700, 'Good',         '#2563eb', '#eff6ff', '#bfdbfe'],
    [600, 'Satisfactory', '#0891b2', '#ecfeff', '#a5f3fc'],
    [500, 'Fair',         '#d97706', '#fffbeb', '#fde68a'],
    [400, 'Poor',         '#dc2626', '#fef2f2', '#fecaca'],
    [0,   'Very Poor',    '#7f1d1d', '#fef2f2', '#fca5a5'],
];
$score_label = 'N/A'; $score_color = '#9ca3af'; $score_bg = '#f9fafb'; $score_border = '#e5e7eb';
if ($credit_score >= 200) {
    foreach ($score_tiers as [$min, $lbl, $col, $bg, $brd]) {
        if ($credit_score >= $min) { $score_label = $lbl; $score_color = $col; $score_bg = $bg; $score_border = $brd; break; }
    }
}
$score_pct = ($credit_score >= 200) ? min(100, round(($credit_score - 200) / 700 * 100)) : 0;
$ppi_text = 'N/A'; $ppi_color = '#9ca3af'; $ppi_pct = 0;
$pod_val = 'N/A'; $pod_pct = 0; $pod_color = '#9ca3af';

// ── Eligibility verdict ────────────────────────────────────────────────────
$is_eligible   = !$is_defaulting && $credit_score >= 500;
$pre_qualified = 0;
if ($is_eligible) {
    if ($credit_score >= 800)     $pre_qualified = 1000000;
    elseif ($credit_score >= 700) $pre_qualified = 500000;
    elseif ($credit_score >= 600) $pre_qualified = 200000;
    else                          $pre_qualified = 100000;
}
$report_date = now()->format('j F Y, g:i A');

// ── PDF & Share URLs ──────────────────────────────────────────────────────
$pdfUrl = route('loan-eligibility.pdf', $req->id);
$waText = "My Loan Eligibility Report is ready via Readiwork.\nName: {$full_name}\nNational ID: {$id_number}\nVerdict: " . ($is_eligible ? 'Eligible' : 'Not Eligible') . "\n\nDownload the official report:\n{$pdfUrl}";

// ── Delinquency description ────────────────────────────────────────────────
$desc_key   = $is_defaulting ? 'D' : ($delinquency_code ? 'clean' : null);
$desc_entry = match($desc_key) {
    'D'     => ['Loan Problem', 'chip-red',   'One or more of your loans have been flagged as delinquent. Contact the lender to resolve this before applying for new credit.'],
    'clean' => ['Safe',         'chip-green', 'Your record is clean. No lender has flagged you for missed or unpaid loans.'],
    default => null,
};
@endphp

@section('title', 'Loan Eligibility Report — ' . $full_name . ' | Readiwork')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<style>
/* ═══════════════════════════════════════════════════
   DESIGN SYSTEM
═══════════════════════════════════════════════════ */
:root {
    --navy:       #071629;
    --navy-mid:   #0d2649;
    --green:      #0FA958;
    --green-dark: #0a8745;
    --green-glow: rgba(15,169,88,.2);
    --border:     #e8eef6;
    --bg:         #f4f7fb;
    --text:       #1e2d42;
    --muted:      #64748b;
    --light:      #94a3b8;
    --white:      #ffffff;
    --radius-lg:  18px;
    --radius-md:  12px;
    --radius-sm:  8px;
    --shadow-sm:  0 2px 8px rgba(11,31,59,.05);
    --shadow-md:  0 4px 20px rgba(11,31,59,.08);
    --shadow-lg:  0 12px 40px rgba(11,31,59,.12);
    --font-head:  'Syne', sans-serif;
    --font-body:  'DM Sans', sans-serif;
}

* { box-sizing: border-box; }

body {
    font-family: var(--font-body);
    background: var(--bg);
    color: var(--text);
}

/* ── Hero ──────────────────────────────────────────── */
.r-hero {
    background: linear-gradient(145deg, #041022 0%, #0b2040 50%, #071629 100%);
    padding: 52px 0 56px;
    position: relative;
    overflow: hidden;
}
.r-hero::before {
    content: '';
    position: absolute;
    top: -60%; right: -10%;
    width: 60%; height: 200%;
    background: radial-gradient(ellipse, rgba(15,169,88,.1) 0%, transparent 60%);
    pointer-events: none;
}
.r-hero::after {
    content: '';
    position: absolute;
    bottom: -1px; left: 0; right: 0;
    height: 32px;
    background: linear-gradient(to bottom, transparent, var(--bg));
}
.r-hero-tag {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(15,169,88,.15);
    color: #6ee7a8;
    border: 1px solid rgba(15,169,88,.3);
    border-radius: 999px;
    padding: 5px 14px;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
    margin-bottom: 16px;
    font-family: var(--font-body);
}
.r-hero-name {
    font-family: var(--font-head);
    font-size: 2.6rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    line-height: 1.1;
}
.r-hero-meta {
    color: rgba(255,255,255,.45);
    font-size: .83rem;
    margin-bottom: 22px;
}
.r-hero-meta strong { color: rgba(255,255,255,.7); }
.r-hero-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.r-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 999px;
    padding: 8px 18px;
    font-size: .83rem;
    font-weight: 700;
    font-family: var(--font-body);
}
.r-hero-badge.eligible {
    background: rgba(74,222,128,.15);
    color: #4ade80;
    border: 1px solid rgba(74,222,128,.3);
}
.r-hero-badge.not-eligible {
    background: rgba(239,68,68,.15);
    color: #f87171;
    border: 1px solid rgba(239,68,68,.3);
}
.r-hero-badge.action {
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.75);
    border: 1px solid rgba(255,255,255,.15);
    cursor: pointer;
    text-decoration: none;
    transition: background .2s;
}
.r-hero-badge.action:hover { background: rgba(255,255,255,.14); }

/* ── Layout ───────────────────────────────────────── */
.r-main { padding: 40px 0 80px; }
.r-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    align-items: start;
}
.r-left { display: grid; gap: 20px; }

/* ── Cards ────────────────────────────────────────── */
.rcard {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}
.rcard-head {
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fafcff;
}
.rcard-head h2 {
    font-family: var(--font-head);
    font-size: .95rem;
    font-weight: 700;
    color: var(--navy);
    margin: 0;
    flex: 1;
}
.rcard-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
}
.rcard-body { padding: 22px; }

/* ── Verdict Banner ───────────────────────────────── */
.verdict-wrap { position: relative; overflow: hidden; }
.verdict-wrap.eligible {
    background: linear-gradient(145deg, #031a0e, #052e16 40%, #041f10 100%);
}
.verdict-wrap.not-eligible {
    background: linear-gradient(145deg, #1a0303, #3b0f0f 40%, #1c0505 100%);
}
.verdict-glow {
    position: absolute;
    top: -30%; left: 50%;
    transform: translateX(-50%);
    width: 70%; height: 150%;
    border-radius: 50%;
    pointer-events: none;
}
.eligible .verdict-glow   { background: radial-gradient(ellipse, rgba(74,222,128,.12) 0%, transparent 70%); }
.not-eligible .verdict-glow { background: radial-gradient(ellipse, rgba(248,113,113,.1) 0%, transparent 70%); }
.verdict-inner {
    position: relative;
    z-index: 1;
    padding: 40px 32px;
    text-align: center;
}
.verdict-emoji {
    font-size: 3.5rem;
    display: block;
    margin-bottom: 14px;
    filter: drop-shadow(0 0 20px rgba(74,222,128,.4));
}
.verdict-title {
    font-family: var(--font-head);
    font-size: 1.65rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 8px;
    line-height: 1.2;
}
.verdict-sub {
    color: rgba(255,255,255,.55);
    font-size: .88rem;
    max-width: 440px;
    margin: 0 auto 28px;
    line-height: 1.7;
}
.verdict-amount-label {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: rgba(255,255,255,.35);
    margin-bottom: 8px;
}
.verdict-amount {
    font-family: var(--font-head);
    font-size: 4rem;
    font-weight: 800;
    color: #4ade80;
    line-height: 1;
    margin-bottom: 18px;
    letter-spacing: -2px;
    text-shadow: 0 0 40px rgba(74,222,128,.4);
}
.verdict-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 999px;
    padding: 8px 22px;
    font-size: .88rem;
    font-weight: 700;
    font-family: var(--font-body);
}
.verdict-pill.green {
    background: rgba(74,222,128,.18);
    color: #4ade80;
    border: 1px solid rgba(74,222,128,.35);
}
.verdict-pill.red {
    background: rgba(239,68,68,.18);
    color: #f87171;
    border: 1px solid rgba(239,68,68,.35);
}
.verdict-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 28px;
}
.v-stat {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: var(--radius-md);
    padding: 14px 10px;
    text-align: center;
}
.v-stat-val {
    font-family: var(--font-head);
    font-size: 1.4rem;
    font-weight: 800;
    color: #4ade80;
    line-height: 1;
}
.v-stat-lbl {
    font-size: .65rem;
    color: rgba(255,255,255,.35);
    margin-top: 5px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

/* ── Score Section ────────────────────────────────── */
.score-layout {
    display: flex;
    gap: 28px;
    align-items: flex-start;
    flex-wrap: wrap;
    margin-bottom: 22px;
}
.score-donut-wrap {
    position: relative;
    width: 136px;
    height: 136px;
    flex-shrink: 0;
}
.score-donut-center {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    text-align: center;
}
.score-donut-num {
    font-family: var(--font-head);
    font-size: 1.85rem;
    font-weight: 800;
    line-height: 1;
}
.score-donut-denom { font-size: .6rem; color: var(--light); margin-top: 3px; }
.score-detail { flex: 1; min-width: 180px; }
.score-tier-badge {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 700;
    margin-bottom: 12px;
    font-family: var(--font-body);
}
.score-track {
    background: #e8eef6;
    border-radius: 999px;
    height: 9px;
    margin-bottom: 5px;
    overflow: hidden;
}
.score-fill {
    height: 100%;
    border-radius: 999px;
    transition: width 1.4s cubic-bezier(.22,1,.36,1);
}
.score-scale {
    display: flex;
    justify-content: space-between;
    font-size: .65rem;
    color: var(--light);
}
.score-summary {
    margin-top: 14px;
    font-size: .83rem;
    color: var(--muted);
    line-height: 1.7;
    padding: 12px 14px;
    background: #f8fafc;
    border-radius: var(--radius-sm);
    border-left: 3px solid var(--green);
}

/* ── Score progress bar (full width) ──────────────── */
.score-rainbow-wrap { margin: 6px 0 20px; }
.score-rainbow-track {
    position: relative;
    height: 14px;
    background: linear-gradient(90deg, #dc2626 0%, #d97706 30%, #0891b2 55%, #2563eb 75%, #16a34a 100%);
    border-radius: 999px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
}
.score-rainbow-thumb {
    position: absolute;
    top: 50%;
    transform: translate(-50%,-50%);
    width: 22px; height: 22px;
    background: var(--white);
    border-radius: 50%;
    border: 3px solid var(--navy);
    box-shadow: 0 2px 10px rgba(0,0,0,.2);
    transition: left 1.4s cubic-bezier(.22,1,.36,1);
}
.score-rainbow-labels {
    display: flex;
    justify-content: space-between;
    font-size: .64rem;
    color: var(--light);
    margin-top: 6px;
}

/* ── Risk Meters ──────────────────────────────────── */
.risk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px; }
.risk-card {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 16px;
}
.risk-lbl {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--light);
    margin-bottom: 8px;
}
.risk-val {
    font-family: var(--font-head);
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 4px;
}
.risk-sub { font-size: .72rem; color: var(--muted); margin-bottom: 8px; }
.risk-track { background: #e5e7eb; border-radius: 999px; height: 5px; overflow: hidden; }
.risk-bar   { height: 100%; border-radius: 999px; }

/* ── Comp grid ────────────────────────────────────── */
.comp-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
    margin-top: 18px;
}
.comp-cell {
    text-align: center;
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 10px 4px;
}
.comp-val { font-size: 1.05rem; font-weight: 800; color: var(--navy); font-family: var(--font-head); }
.comp-lbl { font-size: .6rem; color: var(--light); margin-top: 3px; }

/* ── Identity grid ────────────────────────────────── */
.id-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
.id-cell {
    background: linear-gradient(145deg, #f8fafc, #f0f5ff);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 14px;
    transition: border-color .2s;
}
.id-cell:hover { border-color: #c7d8f0; }
.id-cell-lbl {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--light);
    margin-bottom: 6px;
}
.id-cell-val {
    font-size: .92rem;
    font-weight: 700;
    color: var(--navy);
}
.id-cell-val.mono { font-family: 'Courier New', monospace; letter-spacing: 1px; }

/* ── Info rows ────────────────────────────────────── */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: .86rem;
    gap: 12px;
}
.info-row:last-child { border-bottom: 0; padding-bottom: 0; }
.info-lbl { color: var(--muted); font-weight: 500; white-space: nowrap; }
.info-val { color: var(--text); font-weight: 600; text-align: right; }

/* ── Chips ────────────────────────────────────────── */
.chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: .76rem;
    font-weight: 600;
    font-family: var(--font-body);
}
.chip-green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.chip-red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.chip-blue   { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.chip-yellow { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.chip-gray   { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
.chip-cyan   { background: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }

/* ── Alerts ───────────────────────────────────────── */
.alert {
    border-radius: var(--radius-md);
    padding: 14px 16px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 18px;
    font-size: .84rem;
    line-height: 1.65;
}
.alert i { margin-top: 1px; flex-shrink: 0; }
.alert-green  { background: #f0fdf4; border: 1px solid #bbf7d0; }
.alert-red    { background: #fef2f2; border: 1px solid #fecaca; }
.alert-yellow { background: #fffbeb; border: 1px solid #fde68a; }

/* ── Account cards ────────────────────────────────── */
.acc-card {
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: 14px;
    transition: box-shadow .2s;
}
.acc-card:last-child { margin-bottom: 0; }
.acc-card:hover { box-shadow: var(--shadow-md); }
.acc-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.acc-head-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    flex-shrink: 0;
}
.acc-head-title { flex: 1; min-width: 140px; }
.acc-head-name  { font-size: .94rem; font-weight: 700; color: var(--navy); font-family: var(--font-head); }
.acc-head-num   { font-size: .7rem; color: var(--light); font-family: monospace; margin-top: 2px; }
.acc-body       { padding: 16px 18px; display: grid; gap: 14px; }
.acc-section-lbl {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--light);
    margin-bottom: 8px;
}
.acc-fields { display: grid; grid-template-columns: repeat(3, 1fr); gap: 9px; }
.acc-field {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 9px;
    padding: 10px 12px;
}
.acc-field-lbl { font-size: .63rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--light); margin-bottom: 4px; }
.acc-field-val { font-size: .87rem; font-weight: 700; color: var(--navy); word-break: break-word; }
.acc-field-val.red    { color: #dc2626; }
.acc-field-val.green  { color: #16a34a; }
.acc-field-val.amber  { color: #d97706; }
hr.divider { border: 0; border-top: 1px solid var(--border); margin: 0; }

/* ── Perf grid ────────────────────────────────────── */
.perf-grid { display: flex; gap: 4px; flex-wrap: wrap; align-items: flex-start; }
.perf-col  { display: flex; flex-direction: column; align-items: center; gap: 3px; }
.perf-box  {
    width: 26px; height: 26px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .6rem;
    font-weight: 700;
}
.perf-lbl { font-size: .5rem; color: var(--light); }

/* ── Sector table ─────────────────────────────────── */
.sector-table { width: 100%; border-collapse: collapse; font-size: .83rem; margin-top: 16px; }
.sector-table th {
    padding: 8px 10px;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--muted);
    border-bottom: 2px solid var(--border);
    background: #f8fafc;
    text-align: left;
}
.sector-table td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; }
.sector-table tr:last-child td { border-bottom: 0; }
.sect-bar-wrap {
    width: 80px; height: 5px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;
    margin-right: 6px;
}
.sect-bar { height: 100%; border-radius: 999px; background: var(--green); }

/* ── Delinquency table ────────────────────────────── */
.delinq-table { width: 100%; border-collapse: collapse; font-size: .83rem; margin-top: 8px; }
.delinq-table th { padding: 7px 10px; font-size: .68rem; font-weight: 700; text-transform: uppercase; background: #fff8f8; border-bottom: 2px solid #fecaca; text-align: left; color: var(--muted); }
.delinq-table td { padding: 9px 10px; border-bottom: 1px solid #fef2f2; }
.delinq-table tr:last-child td { border-bottom: 0; }

/* ── Lenders table ────────────────────────────────── */
.lenders-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.lenders-table th { padding: 8px 10px; text-align: left; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; border-bottom: 2px solid var(--border); background: #f8fafc; color: var(--muted); }
.lenders-table td { padding: 10px; border-bottom: 1px solid #f1f5f9; }
.lenders-table tr:last-child td { border-bottom: 0; }
.apply-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 7px;
    background: var(--green);
    color: #fff;
    font-size: .73rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s;
}
.apply-btn:hover { background: var(--green-dark); }

/* ═══════════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════════ */
.sidebar { display: grid; gap: 16px; }
.sb-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.sb-head {
    padding: 13px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 9px;
    background: #fafcff;
    font-family: var(--font-head);
    font-size: .86rem;
    font-weight: 700;
    color: var(--navy);
}
.sb-head-icon {
    width: 26px; height: 26px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    flex-shrink: 0;
}
.sb-body { padding: 16px 18px; }

/* Stat rows in sidebar */
.sb-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: .82rem;
}
.sb-stat:last-child { border-bottom: 0; padding-bottom: 0; }
.sb-stat-lbl { color: var(--muted); font-weight: 500; }
.sb-stat-val { font-weight: 700; color: var(--navy); }

/* Action buttons in sidebar */
.action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 12px 16px;
    border-radius: var(--radius-md);
    background: #f4f7fb;
    border: 1px solid var(--border);
    color: var(--text);
    font-size: .84rem;
    font-weight: 600;
    font-family: var(--font-body);
    cursor: pointer;
    text-decoration: none;
    transition: background .18s, border-color .18s, transform .15s, box-shadow .15s;
}
.action-btn:hover {
    background: #eaf0f8;
    border-color: #c8d8ec;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(11,31,59,.07);
}
.action-btn-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    flex-shrink: 0;
}
.action-btn-text { flex: 1; }
.action-btn-text .primary { display: block; font-weight: 700; color: var(--navy); font-size: .84rem; }
.action-btn-text .secondary { display: block; font-size: .71rem; color: var(--muted); margin-top: 1px; }
.action-btn-arrow { color: var(--light); font-size: .75rem; flex-shrink: 0; }

/* Score distribution sidebar */
.sb-donut-wrap {
    position: relative;
    width: 118px; height: 118px;
    margin: 0 auto 12px;
}
.sb-donut-center {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    text-align: center;
}
.sb-donut-num {
    font-family: var(--font-head);
    font-size: 1.3rem;
    font-weight: 800;
    line-height: 1;
}
.sb-donut-lbl { font-size: .55rem; color: var(--light); }

/* Score tier legend */
.tier-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: .76rem;
    padding: 5px 0;
    border-bottom: 1px solid #f8fafc;
}
.tier-row:last-child { border-bottom: 0; }
.tier-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.tier-range { color: var(--navy); font-weight: 700; }
.tier-label {
    padding: 2px 8px;
    border-radius: 999px;
    font-weight: 700;
    font-size: .7rem;
}

/* ── Chart ────────────────────────────────────────── */
.chart-wrap { position: relative; }

/* ── Timeline ─────────────────────────────────────── */
.tl-item {
    display: flex;
    gap: 14px;
    padding: 11px 0;
    border-bottom: 1px solid #f1f5f9;
}
.tl-item:last-child { border-bottom: 0; }
.tl-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
.tl-title { font-size: .84rem; font-weight: 600; color: var(--navy); margin-bottom: 2px; }
.tl-date  { font-size: .7rem; color: var(--light); }

/* ── Institutions ─────────────────────────────────── */
.inst-grid { display: grid; gap: 8px; }
.inst-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
}
.inst-logo {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: rgba(15,169,88,.1);
    color: var(--green);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
}

/* ── Improvement tips ─────────────────────────────── */
.tip-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 13px;
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    transition: border-color .2s;
}
.tip-item:hover { border-color: #c7d8f0; }
.tip-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    background: rgba(15,169,88,.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
}
.tip-title { font-size: .87rem; font-weight: 700; color: var(--navy); margin-bottom: 3px; font-family: var(--font-head); }
.tip-desc  { font-size: .77rem; color: var(--muted); line-height: 1.55; }

/* ── Responsive ───────────────────────────────────── */
@media print {
    .sidebar, nav, footer, .no-print { display: none !important; }
    .r-grid { grid-template-columns: 1fr; }
}
@media (max-width: 1020px) {
    .r-grid { grid-template-columns: 1fr; }
    .sidebar { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .sidebar { grid-template-columns: 1fr; }
    .comp-grid { grid-template-columns: repeat(3, 1fr); }
    .risk-grid { grid-template-columns: 1fr; }
    .verdict-stats { grid-template-columns: 1fr 1fr; }
    .verdict-amount { font-size: 3rem; }
    .r-hero-name { font-size: 1.9rem; }
}
@media (max-width: 480px) {
    .id-grid { grid-template-columns: repeat(2, 1fr); }
    .acc-fields { grid-template-columns: repeat(2, 1fr); }
    .verdict-stats { grid-template-columns: 1fr !important; }
    .comp-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<section class="r-hero">
    <div class="container">
        <div class="r-hero-tag">
            <i class="fa-solid fa-file-lines"></i> Loan Eligibility Report
        </div>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:18px;">
            <div>
                <h1 class="r-hero-name">{{ $full_name }}</h1>
                <p class="r-hero-meta">
                    National ID: <strong>{{ $id_number }}</strong>
                    &nbsp;·&nbsp; Generated: <strong>{{ $report_date }}</strong>
                </p>
            </div>
            <div class="r-hero-badges">
                @if($is_eligible)
                    <span class="r-hero-badge eligible">
                        <i class="fa-solid fa-circle-check"></i> Eligible for a Loan
                    </span>
                @else
                    <span class="r-hero-badge not-eligible">
                        <i class="fa-solid fa-circle-xmark"></i> Not Eligible
                    </span>
                @endif
                <a href="{{ $pdfUrl }}" target="_blank" class="r-hero-badge action no-print">
                    <i class="fa-solid fa-file-pdf"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══ MAIN ═══ --}}
<section class="r-main">
<div class="container">
<div class="r-grid">
<div class="r-left">

@if($crbError)
<div class="alert alert-yellow">
    <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i>
    <div>Some data could not be retrieved: <strong>{{ $crbError }}</strong>. Partial results are shown below.</div>
</div>
@endif

{{-- 1. VERDICT ──────────────────────────────────────── --}}
<div class="rcard">
    <div class="verdict-wrap {{ $is_eligible ? 'eligible' : 'not-eligible' }}">
        <div class="verdict-glow"></div>
        <div class="verdict-inner">
            <span class="verdict-emoji">{{ $is_eligible ? '🎉' : '⚠️' }}</span>
            <div class="verdict-title">
                {{ $is_eligible ? 'Congratulations! You Are Loan Eligible' : 'Currently Not Eligible for a Loan' }}
            </div>
            <div class="verdict-sub">
                @if($is_eligible)
                    Based on your credit profile, you qualify for loans from multiple lenders.
                    Your credit score of <strong style="color:#4ade80;">{{ $credit_score }}</strong>
                    places you in the <strong style="color:#4ade80;">{{ $score_label }}</strong> tier.
                @else
                    Your current credit profile does not meet the standard lending criteria.
                    {{ $is_defaulting ? 'You have active loan defaults that must be resolved first.' : 'Your credit score is below the minimum threshold.' }}
                @endif
            </div>

            @if($is_eligible)
                <div class="verdict-amount-label">Maximum Pre-Qualified Loan Amount</div>
                <div class="verdict-amount">KES {{ number_format($pre_qualified) }}</div>
                <div class="verdict-pill green"><i class="fa-solid fa-lock-open"></i> Pre-Approved</div>
            @else
                <div class="verdict-pill red"><i class="fa-solid fa-lock"></i> Not Pre-Approved</div>
            @endif

            <div class="verdict-stats">
                <div class="v-stat">
                    <div class="v-stat-val" style="color:{{ $score_color }};">{{ $credit_score ?: 'N/A' }}</div>
                    <div class="v-stat-lbl">Credit Score</div>
                </div>
                <div class="v-stat">
                    <div class="v-stat-val" style="color:{{ $is_defaulting ? '#f87171' : '#4ade80' }};">{{ $is_defaulting ? 'Listed' : 'Clear' }}</div>
                    <div class="v-stat-lbl">CRB Status</div>
                </div>
                <div class="v-stat">
                    <div class="v-stat-val">{{ $no_facilities ?: '0' }}</div>
                    <div class="v-stat-lbl">Credit Accounts</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. CREDIT SCORE ──────────────────────────────────── --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(99,102,241,.1);color:#6366f1;">
            <i class="fa-solid fa-gauge-high"></i>
        </div>
        <h2>Credit Score Analysis</h2>
        <span class="chip {{ $credit_score >= 700 ? 'chip-green' : ($credit_score >= 500 ? 'chip-yellow' : 'chip-red') }}">
            {{ $score_label }}
        </span>
    </div>
    <div class="rcard-body">
        <div class="score-layout">
            <div class="score-donut-wrap">
                <canvas id="scoreDonut" width="136" height="136"></canvas>
                <div class="score-donut-center">
                    <div class="score-donut-num" style="color:{{ $score_color }};">{{ $credit_score ?: '–' }}</div>
                    <div class="score-donut-denom">/ 900</div>
                </div>
            </div>
            <div class="score-detail">
                <span class="score-tier-badge" style="background:{{ $score_bg }};color:{{ $score_color }};border:1px solid {{ $score_border }};">
                    {{ $score_label }} Credit
                </span>
                <div class="score-track">
                    <div class="score-fill" id="scoreBar" style="width:0%;background:{{ $score_color }};"></div>
                </div>
                <div class="score-scale">
                    <span>200 Poor</span><span>550 Fair</span><span>700 Good</span><span>900 Excellent</span>
                </div>
                <div class="score-summary">
                    Score <strong style="color:{{ $score_color }};">{{ $credit_score }}</strong> — in the
                    <strong>{{ $score_label }}</strong> range (200–900 national scale).
                    @if($credit_score >= 700) Lenders view you as a <strong>low-risk</strong> borrower.
                    @elseif($credit_score >= 500) Lenders view you as a <strong>moderate-risk</strong> borrower.
                    @else Your score indicates <strong>high credit risk</strong> to lenders. @endif
                </div>
            </div>
        </div>

        <div class="score-rainbow-wrap">
            <div class="score-rainbow-track">
                <div class="score-rainbow-thumb" id="rainbowThumb" style="left:0%;"></div>
            </div>
            <div class="score-rainbow-labels">
                <span>200</span><span>350</span><span>500</span><span>650</span><span>800</span><span>900</span>
            </div>
        </div>

        <div class="risk-grid">
            <div class="risk-card">
                <div class="risk-lbl">Payment Performance (PPI)</div>
                <div class="risk-val" style="color:{{ $ppi_color }};">{{ $ppi ?: 'N/A' }}</div>
                <div class="risk-sub">{{ $ppi_text }}</div>
                <div class="risk-track"><div class="risk-bar" style="width:{{ $ppi_pct }}%;background:{{ $ppi_color }};"></div></div>
            </div>
            <div class="risk-card">
                <div class="risk-lbl">Probability of Default (POD)</div>
                <div class="risk-val" style="color:{{ $pod_color }};">{{ $pod_val }}</div>
                <div class="risk-sub">Likelihood of loan default</div>
                <div class="risk-track"><div class="risk-bar" style="width:{{ $pod_pct }}%;background:{{ $pod_color }};"></div></div>
            </div>
        </div>

        @if($score_3m || $score_6m || $score_12m || $score_best || $score_worst)
        <div style="margin-bottom:10px;font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">Historical Score Comparatives</div>
        <div class="comp-grid">
            <div class="comp-cell"><div class="comp-val">{{ $score_3m  ?: '–' }}</div><div class="comp-lbl">3 Months</div></div>
            <div class="comp-cell"><div class="comp-val">{{ $score_6m  ?: '–' }}</div><div class="comp-lbl">6 Months</div></div>
            <div class="comp-cell"><div class="comp-val">{{ $score_12m ?: '–' }}</div><div class="comp-lbl">12 Months</div></div>
            <div class="comp-cell"><div class="comp-val" style="color:#16a34a;">{{ $score_best  ?: '–' }}</div><div class="comp-lbl">Best Ever</div></div>
            <div class="comp-cell"><div class="comp-val" style="color:#dc2626;">{{ $score_worst ?: '–' }}</div><div class="comp-lbl">Worst Ever</div></div>
        </div>
        @if($score_3m || $score_6m || $score_12m)
        <div style="margin-top:22px;">
            <div style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Score Trend Over Time</div>
            <div class="chart-wrap" style="height:150px;"><canvas id="scoreTrendChart"></canvas></div>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- 3. PERSONAL PROFILE ──────────────────────────────── --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;">
            <i class="fa-solid fa-id-card"></i>
        </div>
        <h2>Personal Identity Profile</h2>
        <span class="chip chip-green"><i class="fa-solid fa-circle-check"></i> Verified</span>
    </div>
    <div class="rcard-body">
        <div class="id-grid">
            @foreach([['Full Name',$full_name,false],['National ID',$id_number,true],['Date of Birth',$dob,false],['Gender',$gender ? ucfirst(strtolower($gender==='M'?'Male':($gender==='F'?'Female':$gender))) : null,false],['Citizenship',$citizenship,false],['District',$district,false]] as [$lbl,$val,$mono])
            @if($val !== null && $val !== '')
            <div class="id-cell">
                <div class="id-cell-lbl">{{ $lbl }}</div>
                <div class="id-cell-val {{ $mono ? 'mono' : '' }}">{{ $val ?: '—' }}</div>
            </div>
            @endif
            @endforeach
        </div>
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;display:flex;gap:10px;align-items:center;">
            <i class="fa-solid fa-shield-halved" style="color:#16a34a;flex-shrink:0;"></i>
            <span style="font-size:.8rem;color:#166534;line-height:1.6;">Identity confirmed against the Kenya National Identity Registry. All data is encrypted and used solely for this credit check.</span>
        </div>
    </div>
</div>

{{-- 4. LOAN PAYMENT STATUS ──────────────────────────── --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:{{ $is_defaulting ? 'rgba(239,68,68,.1)' : 'rgba(15,169,88,.1)' }};color:{{ $is_defaulting ? '#dc2626' : 'var(--green)' }};">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2>Loan Payment Status</h2>
        <span class="chip {{ $is_defaulting ? 'chip-red' : 'chip-green' }}">
            {{ $is_defaulting ? '⚠ Flagged' : '✓ All Clear' }}
        </span>
    </div>
    <div class="rcard-body">
        @if($is_defaulting)
        <div class="alert alert-red">
            <i class="fa-solid fa-circle-xmark" style="color:#dc2626;"></i>
            <div><strong>Your loan history has a problem.</strong> One or more of your past loans were not fully repaid and have been reported to the credit bureau. To fix this, contact the lender that reported you and arrange to pay what you owe.</div>
        </div>
        @else
        <div class="alert alert-green">
            <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i>
            <div><strong>Your loan payments are clean!</strong> No lender has reported you for missed or unpaid loans. This is a strong green light for new loan applications.</div>
        </div>
        @endif

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:18px;">
            @foreach([[
                $no_facilities, 'Total Credit Facilities', $no_facilities > 0 ? 'var(--navy)' : '#9ca3af'
            ],[
                $npa_accounts, 'Non-Performing Accounts', $npa_accounts > 0 ? '#dc2626' : '#16a34a'
            ],[
                'KES ' . ($outstanding_bal > 0 ? number_format($outstanding_bal) : '0'), 'Outstanding Balance', $outstanding_bal > 0 ? '#d97706' : 'var(--navy)'
            ],[
                'KES ' . ($overdue_amount > 0 ? number_format($overdue_amount) : '0'), 'Overdue Amount', $overdue_amount > 0 ? '#dc2626' : '#16a34a'
            ]] as [$val, $lbl, $col])
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-md);padding:16px;text-align:center;">
                <div style="font-size:1.45rem;font-weight:800;color:{{ $col }};font-family:var(--font-head);">{{ $val }}</div>
                <div style="font-size:.72rem;color:var(--muted);margin-top:4px;">{{ $lbl }}</div>
            </div>
            @endforeach
        </div>

        @if($desc_entry)
        <div class="info-row" style="padding-top:0;">
            <span class="info-lbl">Status Code</span>
            <span><span class="chip {{ $desc_entry[1] }}">{{ $desc_entry[0] }}</span></span>
        </div>
        <div style="font-size:.8rem;color:var(--muted);padding:8px 0;line-height:1.65;">{{ $desc_entry[2] }}</div>
        @endif
    </div>
</div>

{{-- 5. LOAN PAYMENT HISTORY ──────────────────────────── --}}
@if(!empty($accounts))
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <h2>Loan Payment History</h2>
        <span class="chip chip-blue">{{ count($accounts) }} Account{{ count($accounts) > 1 ? 's' : '' }}</span>
    </div>
    <div class="rcard-body">
    @php
    $delinq_accounts = [];
    foreach ($accounts as $acc) {
        $overdue = (float)($acc['arrears_amount'] ?? $acc['overdue_balance'] ?? 0);
        $bal     = (float)($acc['outstanding_balance'] ?? 0);
        $status  = strtolower($acc['account_status'] ?? '');
        $dCode   = $acc['delinquency_code'] ?? null;
        if ($overdue > 0 || $dCode === 'D' || in_array($status, ['w','written off','npa','default'])) {
            $delinq_accounts[] = [
                'name'   => $acc['institution_name'] ?? $acc['lender_name'] ?? 'Unknown',
                'type'   => $acc['sector'] ?? $acc['account_type'] ?? '–',
                'balance'=> $bal,
                'overdue'=> $overdue,
                'status' => $acc['account_status'] ?? '–',
            ];
        }
    }
    @endphp

    @if(!empty($delinq_accounts))
    <div style="background:#fff5f5;border:1px solid #fecaca;border-radius:var(--radius-md);padding:14px 18px;margin-bottom:18px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
            <i class="fa-solid fa-circle-exclamation" style="color:#dc2626;"></i>
            <strong style="color:#991b1b;font-size:.87rem;">Outstanding Debts — What You Owe &amp; To Whom</strong>
        </div>
        <table class="delinq-table">
            <thead><tr><th>Lender</th><th>Type</th><th>Current Balance</th><th>Overdue Amount</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($delinq_accounts as $da)
            <tr>
                <td style="font-weight:700;color:var(--navy);">{{ $da['name'] }}</td>
                <td style="color:var(--muted);">{{ $da['type'] }}</td>
                <td style="font-weight:600;color:#d97706;">KES {{ number_format($da['balance']) }}</td>
                <td style="font-weight:700;color:#dc2626;">{{ $da['overdue'] > 0 ? 'KES ' . number_format($da['overdue']) : '—' }}</td>
                <td><span class="chip chip-red" style="font-size:.7rem;">{{ $da['status'] }}</span></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @foreach($accounts as $acc)
    @php
    $acc_name        = $acc['institution_name'] ?? $acc['lender_name'] ?? 'Unknown Lender';
    $acc_num         = $acc['account_number'] ?? null;
    $lender_type     = $acc['sector'] ?? $acc['account_type'] ?? '–';
    $loan_type       = $acc['account_type'] ?? '–';
    $reported_status = $acc['account_status'] ?? '–';
    $repay_period    = $acc['repayment_period'] ?? null;
    $installment     = (float)($acc['scheduled_payment_amount'] ?? $acc['installment_amount'] ?? 0);
    $orig_currency   = $acc['currency'] ?? 'KES';
    $orig_amount     = (float)($acc['loan_amount'] ?? $acc['original_amount'] ?? 0);
    $current_bal     = (float)($acc['outstanding_balance'] ?? $acc['current_balance'] ?? 0);
    $overdue_bal     = (float)($acc['arrears_amount'] ?? $acc['overdue_balance'] ?? 0);
    $date_opened     = $acc['opening_date'] ?? $acc['date_account_opened'] ?? null;
    $date_last_upd   = $acc['closing_date'] ?? null;
    $last_payment    = 0; $days_arrears = 0; $max_days_arrears = 0; $overdue_date = null;
    $date_first_rep  = null; $date_last_pay = null; $risk_current = null; $risk_worst = null; $risk_worst_date = null;
    $perf_months     = [];
    $acc_dcode       = $acc['delinquency_code'] ?? null;
    $acc_is_delinq   = ($acc_dcode === 'D' || $acc_dcode === '1' || $acc_dcode === 1);
    $status_raw      = strtolower($reported_status);
    $status_display  = match($status_raw) { 'a' => 'Active', 'c' => 'Closed', 'w' => 'Written Off', default => ucfirst($reported_status) };
    $status_chip     = match(true) {
        in_array($status_raw, ['a','active'])        => 'chip-green',
        in_array($status_raw, ['c','closed'])        => 'chip-gray',
        $status_raw === 'w' || $acc_is_delinq        => 'chip-red',
        default                                       => 'chip-yellow',
    };
    $icon_bg    = ($status_raw === 'c') ? 'rgba(107,114,128,.1)' : ($status_raw === 'w' || $acc_is_delinq ? 'rgba(239,68,68,.1)' : 'rgba(14,165,233,.1)');
    $icon_color = ($status_raw === 'c') ? '#6b7280' : ($status_raw === 'w' || $acc_is_delinq ? '#dc2626' : '#0ea5e9');
    @endphp
    <div class="acc-card">
        <div class="acc-head">
            <div class="acc-head-icon" style="background:{{ $icon_bg }};color:{{ $icon_color }};"><i class="fa-solid fa-building-columns"></i></div>
            <div class="acc-head-title">
                <div class="acc-head-name">{{ $acc_name }}</div>
                @if($acc_num)<div class="acc-head-num">{{ $acc_num }}</div>@endif
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                @if($lender_type !== '–')<span class="chip chip-cyan" style="font-size:.7rem;">{{ $lender_type }}</span>@endif
                <span class="chip {{ $status_chip }}" style="font-size:.7rem;">{{ $status_display }}</span>
                @if($acc_is_delinq)<span class="chip chip-red" style="font-size:.7rem;">Delinquent</span>@endif
            </div>
        </div>
        <div class="acc-body">
            <div>
                <div class="acc-section-lbl">Loan Terms</div>
                <div class="acc-fields">
                    <div class="acc-field"><div class="acc-field-lbl">Loan Type</div><div class="acc-field-val">{{ $loan_type }}</div></div>
                    @if($repay_period)<div class="acc-field"><div class="acc-field-lbl">Repayment Period</div><div class="acc-field-val">{{ $repay_period }}</div></div>@endif
                    @if($installment > 0)<div class="acc-field"><div class="acc-field-lbl">Monthly Installment</div><div class="acc-field-val">{{ $orig_currency }} {{ number_format($installment) }}</div></div>@endif
                </div>
            </div>
            <hr class="divider">
            <div>
                <div class="acc-section-lbl">Amounts</div>
                <div class="acc-fields">
                    <div class="acc-field"><div class="acc-field-lbl">Original Amount</div><div class="acc-field-val">{{ $orig_currency }} {{ $orig_amount > 0 ? number_format($orig_amount) : '—' }}</div></div>
                    <div class="acc-field"><div class="acc-field-lbl">Current Balance</div><div class="acc-field-val {{ $current_bal > 0 ? 'amber' : 'green' }}">{{ $orig_currency }} {{ number_format($current_bal) }}</div></div>
                    <div class="acc-field"><div class="acc-field-lbl">Last Payment</div><div class="acc-field-val">{{ $last_payment > 0 ? $orig_currency . ' ' . number_format($last_payment) : '—' }}</div></div>
                </div>
            </div>
            @if($days_arrears > 0 || $overdue_bal > 0 || $max_days_arrears > 0)
            <hr class="divider">
            <div>
                <div class="acc-section-lbl" style="color:#dc2626;">Arrears &amp; Overdue</div>
                <div class="acc-fields">
                    <div class="acc-field" style="border-color:#fecaca;background:#fff5f5;"><div class="acc-field-lbl">Days in Arrears</div><div class="acc-field-val red">{{ $days_arrears > 0 ? $days_arrears . ' days' : '—' }}</div></div>
                    <div class="acc-field" style="border-color:#fecaca;background:#fff5f5;"><div class="acc-field-lbl">Max Days in Arrears</div><div class="acc-field-val red">{{ $max_days_arrears > 0 ? $max_days_arrears . ' days' : '—' }}</div></div>
                    <div class="acc-field" style="border-color:#fecaca;background:#fff5f5;"><div class="acc-field-lbl">Overdue Balance</div><div class="acc-field-val red">{{ $overdue_bal > 0 ? $orig_currency . ' ' . number_format($overdue_bal) : '—' }}</div></div>
                    @if($overdue_date)<div class="acc-field" style="border-color:#fecaca;background:#fff5f5;"><div class="acc-field-lbl">Overdue Since</div><div class="acc-field-val red">{{ $overdue_date }}</div></div>@endif
                </div>
            </div>
            @endif
            <hr class="divider">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <div class="acc-section-lbl">Important Dates</div>
                    @foreach([['Date Opened',$date_opened],['First Reported',$date_first_rep],['Last Updated',$date_last_upd],['Last Payment',$date_last_pay]] as [$lbl,$val])
                    @if($val)<div class="info-row" style="padding:5px 0;border-color:#f1f5f9;"><span class="info-lbl" style="font-size:.76rem;">{{ $lbl }}</span><span class="info-val" style="font-size:.76rem;">{{ $val }}</span></div>@endif
                    @endforeach
                    @if(!$date_opened && !$date_first_rep && !$date_last_upd && !$date_last_pay)<div style="font-size:.76rem;color:var(--light);">No dates available</div>@endif
                </div>
                <div>
                    <div class="acc-section-lbl">Risk Classification</div>
                    @if($risk_current)<div class="info-row" style="padding:5px 0;border-color:#f1f5f9;"><span class="info-lbl" style="font-size:.76rem;">Current</span><span class="info-val"><span class="chip {{ in_array(strtolower($risk_current),['normal','performing'])?'chip-green':'chip-yellow' }}" style="font-size:.68rem;">{{ $risk_current }}</span></span></div>@endif
                    @if($risk_worst)<div class="info-row" style="padding:5px 0;border-color:#f1f5f9;"><span class="info-lbl" style="font-size:.76rem;">Worst Ever</span><span class="info-val"><span class="chip {{ in_array(strtolower($risk_worst),['normal','performing'])?'chip-green':'chip-red' }}" style="font-size:.68rem;">{{ $risk_worst }}</span></span></div>@endif
                    @if($risk_worst_date)<div class="info-row" style="padding:5px 0;border-color:#f1f5f9;"><span class="info-lbl" style="font-size:.76rem;">Worst As At</span><span class="info-val" style="font-size:.76rem;">{{ $risk_worst_date }}</span></div>@endif
                    @if(!$risk_current && !$risk_worst)<div style="font-size:.76rem;color:var(--light);">No classification data</div>@endif
                </div>
            </div>
            @if(!empty($perf_months))
            <hr class="divider">
            <div>
                <div class="acc-section-lbl" style="margin-bottom:10px;">Repayment Performance ({{ count($perf_months) }} months)</div>
                <div class="perf-grid">
                @foreach(array_slice($perf_months, 0, 12) as $pm)
                @php
                $pv = $pm['val']; $pml = $pm['month'];
                if (is_numeric($pv)) {
                    $pn = (int)$pv;
                    if ($pn===0)      { $pbg='#d1fae5';$pfc='#065f46';$pdisplay='✓'; }
                    elseif ($pn<=30)  { $pbg='#fef9c3';$pfc='#713f12';$pdisplay=$pn; }
                    elseif ($pn<=60)  { $pbg='#fed7aa';$pfc='#9a3412';$pdisplay=$pn; }
                    elseif ($pn<=90)  { $pbg='#fca5a5';$pfc='#7f1d1d';$pdisplay=$pn; }
                    else              { $pbg='#fee2e2';$pfc='#991b1b';$pdisplay=$pn; }
                } else {
                    $pv_up = strtoupper(trim($pv));
                    $lmap  = ['X'=>['#f3f4f6','#9ca3af','–'],'D'=>['#fee2e2','#991b1b','D'],'W'=>['#e0f2fe','#0369a1','W'],'N'=>['#f3f4f6','#9ca3af','N']];
                    if (isset($lmap[$pv_up])) [$pbg,$pfc,$pdisplay] = $lmap[$pv_up];
                    else { $pbg='#f3f4f6';$pfc='#9ca3af';$pdisplay=$pv_up?:'–'; }
                }
                @endphp
                <div class="perf-col">
                    <div class="perf-box" style="background:{{ $pbg }};color:{{ $pfc }};">{{ $pdisplay }}</div>
                    @if($pml)<div class="perf-lbl">{{ $pml }}</div>@endif
                </div>
                @endforeach
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;font-size:.66rem;color:var(--muted);">
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#d1fae5;vertical-align:middle;margin-right:3px;"></span>On time</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#fef9c3;vertical-align:middle;margin-right:3px;"></span>1–30 days</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#fed7aa;vertical-align:middle;margin-right:3px;"></span>31–60 days</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#fca5a5;vertical-align:middle;margin-right:3px;"></span>61–90 days</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#fee2e2;vertical-align:middle;margin-right:3px;"></span>90+ days</span>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
    </div>
</div>
@endif

{{-- 6. SECTOR PERFORMANCE ────────────────────────────── --}}
@if(!empty($sector_data))
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
            <i class="fa-solid fa-chart-bar"></i>
        </div>
        <h2>Lending Sector Performance</h2>
    </div>
    <div class="rcard-body">
        <div class="chart-wrap" style="height:200px;margin-bottom:16px;"><canvas id="sectorChart"></canvas></div>
        <table class="sector-table">
            <thead><tr><th>Sector</th><th>Total</th><th>Performing</th><th>NPA</th><th>Distribution</th></tr></thead>
            <tbody>
            @php $max_sector = max(array_column($sector_data, 'total') ?: [1]); @endphp
            @foreach($sector_data as $s)
            @if($s['total'])
            @php $pct = $max_sector > 0 ? round($s['total'] / $max_sector * 100) : 0; @endphp
            <tr>
                <td style="font-weight:600;color:var(--navy);">{{ $s['sector'] }}</td>
                <td>{{ $s['total'] }}</td>
                <td style="color:#16a34a;font-weight:600;">{{ ($s['perf'] ?? 0) + ($s['clean'] ?? 0) }}</td>
                <td style="color:{{ ($s['npa'] ?? 0) > 0 ? '#dc2626' : '#6b7280' }};font-weight:600;">{{ $s['npa'] ?? 0 }}</td>
                <td><span class="sect-bar-wrap"><span class="sect-bar" style="width:{{ $pct }}%;"></span></span>{{ $pct }}%</td>
            </tr>
            @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- 7. REPORTING INSTITUTIONS ────────────────────────── --}}
@if(!empty($institutions))
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(99,102,241,.1);color:#6366f1;">
            <i class="fa-solid fa-landmark"></i>
        </div>
        <h2>Reporting Institutions</h2>
        <span class="chip chip-blue">{{ count($institutions) }}</span>
    </div>
    <div class="rcard-body">
        <div class="inst-grid">
        @foreach($institutions as $inst)
        @php
        $iname = is_array($inst) ? ($inst['institution_name'] ?? $inst['name'] ?? 'Institution') : $inst;
        $idate = is_array($inst) ? ($inst['last_report_date'] ?? $inst['date'] ?? null) : null;
        @endphp
        <div class="inst-item">
            <div class="inst-logo"><i class="fa-solid fa-building-columns"></i></div>
            <span style="font-size:.85rem;font-weight:600;color:var(--navy);flex:1;">{{ $iname }}</span>
            @if($idate)<span style="font-size:.7rem;color:var(--light);">{{ $idate }}</span>@endif
        </div>
        @endforeach
        </div>
    </div>
</div>
@endif

{{-- 8. CREDIT EVENTS ──────────────────────────────────── --}}
@if(!empty($credit_events))
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(239,68,68,.1);color:#dc2626;">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <h2>Credit Events History</h2>
        <span class="chip chip-red">{{ count($credit_events) }}</span>
    </div>
    <div class="rcard-body">
        @foreach($credit_events as $ev)
        @php
        $ev_type   = is_array($ev) ? ($ev['event_type'] ?? $ev['type'] ?? 'Credit Event') : $ev;
        $ev_date   = is_array($ev) ? ($ev['event_date'] ?? $ev['date'] ?? null) : null;
        $ev_amt    = is_array($ev) ? ($ev['amount'] ?? null) : null;
        $ev_lender = is_array($ev) ? ($ev['lender_name'] ?? $ev['institution'] ?? null) : null;
        @endphp
        <div class="tl-item">
            <div class="tl-dot" style="background:#dc2626;"></div>
            <div>
                <div class="tl-title">{{ $ev_type }}{{ $ev_lender ? ' — ' . $ev_lender : '' }}</div>
                <div class="tl-date">{{ $ev_date }}{{ $ev_amt ? ' · KES ' . number_format((float)$ev_amt) : '' }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- 9. LENDERS / IMPROVEMENT TIPS ────────────────────── --}}
@if($is_eligible)
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(15,169,88,.1);color:var(--green);">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <h2>Lenders Ready to Approve You</h2>
        <span class="chip chip-green">10 Lenders</span>
    </div>
    <div class="rcard-body">
        <div class="alert alert-green" style="margin-bottom:18px;">
            <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i>
            <div>Based on your credit score of <strong>{{ $credit_score }}</strong>, the following lenders are likely to approve your loan for up to <strong>KES {{ number_format($pre_qualified) }}</strong>.</div>
        </div>
        <div style="overflow-x:auto;">
        <table class="lenders-table">
            <thead><tr><th>#</th><th>Lender</th><th>Max Loan</th><th>Interest Rate</th><th>Term</th><th>Action</th></tr></thead>
            <tbody>
            @php
            $lenders = [
                ['M-Shwari',        'KES 100,000',   '7.5% p.m.',   '30 days',    'https://www.safaricom.co.ke/personal/m-pesa/m-shwari'],
                ['KCB M-Pesa',      'KES 1,000,000', '8.64% p.a.',  '12 months',  'https://ke.kcbgroup.com/personal/borrow/m-pesa-loan'],
                ['Tala',            'KES 50,000',    '11–15% p.m.', '30 days',    'https://tala.co.ke'],
                ['Branch',          'KES 70,000',    '17% p.m.',    '62 days',    'https://branch.co/ke'],
                ['Fuliza (M-Pesa)', 'KES 70,000',    '1% per day',  'On-demand',  'https://www.safaricom.co.ke/personal/m-pesa/fuliza-m-pesa'],
                ['Stawi',           'KES 250,000',   '9% p.a.',     '12 months',  'https://stawi.co.ke'],
                ['Equity EazzyLoan','KES 3,000,000', '14% p.a.',    '36 months',  'https://equitygroupholdings.com'],
                ['Co-op mCo-opCash','KES 100,000',   '12% p.a.',    '12 months',  'https://www.co-opbank.co.ke'],
                ['Hustler Fund',    'KES 50,000',    '8% p.a.',     '14 days',    'https://hustlerfund.go.ke'],
                ['Zenka',           'KES 30,000',    '9–30% p.m.',  '61 days',    'https://zenka.co.ke'],
            ];
            @endphp
            @foreach($lenders as $i => $l)
            <tr>
                <td style="color:var(--light);font-weight:600;width:30px;">{{ $i+1 }}</td>
                <td style="font-weight:700;color:var(--navy);">{{ $l[0] }}</td>
                <td style="font-weight:600;color:var(--green);">{{ $l[1] }}</td>
                <td style="color:var(--muted);">{{ $l[2] }}</td>
                <td style="color:var(--muted);">{{ $l[3] }}</td>
                <td><a href="{{ $l[4] }}" target="_blank" rel="noopener" class="apply-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i> Apply</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <p style="font-size:.7rem;color:var(--light);margin-top:12px;"><i class="fa-solid fa-circle-info"></i> Interest rates are indicative and may vary. Always confirm terms directly with the lender.</p>
    </div>
</div>
@else
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(99,102,241,.1);color:#6366f1;">
            <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
        <h2>How to Improve Your Credit Score</h2>
    </div>
    <div class="rcard-body">
        <div class="alert alert-yellow" style="margin-bottom:18px;">
            <i class="fa-solid fa-lightbulb" style="color:#d97706;"></i>
            <div>Here are actionable steps to improve your credit score and become loan-eligible.</div>
        </div>
        <div style="display:grid;gap:10px;">
        @foreach([
            ['fa-money-bill-wave','#16a34a','Repay Outstanding Debts','Pay off any defaulted or overdue loans immediately. This is the single biggest factor affecting your score.'],
            ['fa-calendar-check', '#2563eb','Make Payments On Time',  'Set reminders for all loan repayments. Even one missed payment lowers your score significantly.'],
            ['fa-chart-line',     '#d97706','Reduce Credit Utilization','Keep your credit usage below 30% of your total limit across all accounts.'],
            ['fa-handshake',      '#0891b2','Negotiate with Lenders', 'Contact your lenders to negotiate a repayment plan. Settling debts — even partially — improves your CRB status.'],
            ['fa-clock',          '#6366f1','Allow Time to Rebuild',  'A clean repayment record over 6–12 months can significantly improve your score.'],
        ] as $t)
        <div class="tip-item">
            <div class="tip-icon" style="color:{{ $t[1] }};"><i class="fa-solid {{ $t[0] }}"></i></div>
            <div>
                <div class="tip-title">{{ $t[2] }}</div>
                <div class="tip-desc">{{ $t[3] }}</div>
            </div>
        </div>
        @endforeach
        </div>
    </div>
</div>
@endif

</div>{{-- end r-left --}}

{{-- ══════════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════════ --}}
<div class="sidebar">

    {{-- Quick Summary --}}
    <div class="sb-card">
        <div class="sb-head">
            <div class="sb-head-icon" style="background:rgba(15,169,88,.12);color:var(--green);"><i class="fa-solid fa-chart-pie"></i></div>
            Quick Summary
        </div>
        <div class="sb-body">
            <div class="sb-stat"><span class="sb-stat-lbl">Verdict</span><span class="sb-stat-val" style="color:{{ $is_eligible ? '#16a34a' : '#dc2626' }};">{{ $is_eligible ? '✓ Eligible' : '✗ Not Eligible' }}</span></div>
            <div class="sb-stat"><span class="sb-stat-lbl">Credit Score</span><span class="sb-stat-val" style="color:{{ $score_color }};">{{ $credit_score ?: 'N/A' }} / 900</span></div>
            <div class="sb-stat"><span class="sb-stat-lbl">Score Tier</span><span class="sb-stat-val">{{ $score_label }}</span></div>
            <div class="sb-stat"><span class="sb-stat-lbl">CRB Status</span><span class="sb-stat-val" style="color:{{ $is_defaulting ? '#dc2626' : '#16a34a' }};">{{ $is_defaulting ? 'Listed' : 'Clear' }}</span></div>
            <div class="sb-stat"><span class="sb-stat-lbl">PPI Rating</span><span class="sb-stat-val">{{ $ppi ?: 'N/A' }}</span></div>
            <div class="sb-stat"><span class="sb-stat-lbl">Accounts</span><span class="sb-stat-val">{{ $no_facilities }}</span></div>
            <div class="sb-stat"><span class="sb-stat-lbl">NPAs</span><span class="sb-stat-val" style="color:{{ $npa_accounts > 0 ? '#dc2626' : '#16a34a' }};">{{ $npa_accounts }}</span></div>
            @if($outstanding_bal > 0)
            <div class="sb-stat"><span class="sb-stat-lbl">Outstanding</span><span class="sb-stat-val">KES {{ number_format($outstanding_bal) }}</span></div>
            @endif
            @if($is_eligible)
            <div class="sb-stat"><span class="sb-stat-lbl">Pre-Qualified</span><span class="sb-stat-val" style="color:#16a34a;">KES {{ number_format($pre_qualified) }}</span></div>
            @endif
        </div>
    </div>

    {{-- Score Distribution --}}
    <div class="sb-card">
        <div class="sb-head">
            <div class="sb-head-icon" style="background:rgba(99,102,241,.12);color:#6366f1;"><i class="fa-solid fa-circle-half-stroke"></i></div>
            Score Distribution
        </div>
        <div class="sb-body" style="text-align:center;">
            <div class="sb-donut-wrap">
                <canvas id="sidebarDonut" width="118" height="118"></canvas>
                <div class="sb-donut-center">
                    <div class="sb-donut-num" style="color:{{ $score_color }};">{{ $credit_score ?: '–' }}</div>
                    <div class="sb-donut-lbl">Score</div>
                </div>
            </div>
            <div style="font-size:.82rem;font-weight:700;color:{{ $score_color }};margin-bottom:2px;">{{ $score_label }} Credit</div>
            <div style="font-size:.7rem;color:var(--light);">Scale: 200 – 900</div>
        </div>
    </div>

    {{-- Save & Share --}}
    <div class="sb-card no-print">
        <div class="sb-head">
            <div class="sb-head-icon" style="background:rgba(14,165,233,.12);color:#0ea5e9;"><i class="fa-solid fa-share-nodes"></i></div>
            Save &amp; Share Report
        </div>
        <div class="sb-body" style="display:grid;gap:9px;">

            {{-- Print (opens PDF then prints) --}}
            <button onclick="printPdfReport()" class="action-btn no-print">
                <div class="action-btn-icon" style="background:rgba(99,102,241,.1);">
                    <i class="fa-solid fa-print" style="color:#6366f1;"></i>
                </div>
                <div class="action-btn-text">
                    <span class="primary">Print Report</span>
                    <span class="secondary">Opens PDF for printing</span>
                </div>
                <i class="fa-solid fa-chevron-right action-btn-arrow"></i>
            </button>

            {{-- Download PDF --}}
            <a href="{{ $pdfUrl }}" class="action-btn" target="_blank">
                <div class="action-btn-icon" style="background:rgba(220,38,38,.1);">
                    <i class="fa-solid fa-file-pdf" style="color:#dc2626;"></i>
                </div>
                <div class="action-btn-text">
                    <span class="primary">Download PDF Report</span>
                    <span class="secondary">Full report as PDF</span>
                </div>
                <i class="fa-solid fa-chevron-right action-btn-arrow"></i>
            </a>

            {{-- WhatsApp --}}
            <a href="https://wa.me/?text={{ urlencode($waText) }}" target="_blank" class="action-btn">
                <div class="action-btn-icon" style="background:rgba(22,163,74,.1);">
                    <i class="fa-brands fa-whatsapp" style="color:#16a34a;"></i>
                </div>
                <div class="action-btn-text">
                    <span class="primary">Send via WhatsApp</span>
                    <span class="secondary">Share PDF link</span>
                </div>
                <i class="fa-solid fa-chevron-right action-btn-arrow"></i>
            </a>

            {{-- Run another --}}
            <a href="{{ route('loan-eligibility') }}" class="action-btn">
                <div class="action-btn-icon" style="background:rgba(217,119,6,.1);">
                    <i class="fa-solid fa-rotate-left" style="color:#d97706;"></i>
                </div>
                <div class="action-btn-text">
                    <span class="primary">Run Another Check</span>
                    <span class="secondary">Check a different ID</span>
                </div>
                <i class="fa-solid fa-chevron-right action-btn-arrow"></i>
            </a>

        </div>
    </div>

    {{-- Score Guide --}}
    <div class="sb-card">
        <div class="sb-head">
            <div class="sb-head-icon" style="background:rgba(217,119,6,.12);color:#d97706;"><i class="fa-solid fa-circle-question"></i></div>
            Understanding Your Score
        </div>
        <div class="sb-body">
            @foreach([
                ['800–900', 'Excellent', '#16a34a'],
                ['700–799', 'Good',      '#2563eb'],
                ['600–699', 'Satisfactory', '#0891b2'],
                ['500–599', 'Fair',      '#d97706'],
                ['400–499', 'Poor',      '#dc2626'],
                ['200–399', 'Very Poor', '#7f1d1d'],
            ] as [$range, $label, $color])
            <div class="tier-row">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="tier-dot" style="background:{{ $color }};"></div>
                    <span class="tier-range">{{ $range }}</span>
                </div>
                <span class="tier-label" style="background:{{ $color }}22;color:{{ $color }};border:1px solid {{ $color }}44;">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>{{-- end sidebar --}}

</div>{{-- end r-grid --}}
</div>
</section>
@endsection

@push('scripts')
<script>
const SCORE       = {{ $credit_score ?: 0 }};
const SCORE_PCT   = {{ $score_pct }};
const SCORE_COL   = '{{ $score_color }}';
const SCORE_3M    = {{ $score_3m }};
const SCORE_6M    = {{ $score_6m }};
const SCORE_12M   = {{ $score_12m }};
const SECTOR_DATA = {!! json_encode($sector_data) !!};
const PDF_URL     = '{{ $pdfUrl }}';

/* ── Animate score bar + rainbow thumb ── */
setTimeout(() => {
    const bar = document.getElementById('scoreBar');
    if (bar) bar.style.width = SCORE_PCT + '%';
    const thumb = document.getElementById('rainbowThumb');
    if (thumb) thumb.style.left = SCORE_PCT + '%';
}, 300);

/* ── Score donut (main) ── */
(function () {
    const ctx = document.getElementById('scoreDonut');
    if (!ctx || !SCORE) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: { datasets: [{ data: [Math.max(0, SCORE - 200), 700 - Math.max(0, SCORE - 200)], backgroundColor: [SCORE_COL, '#e8eef6'], borderWidth: 0, borderRadius: 5 }] },
        options: { cutout: '74%', animation: { animateRotate: true, duration: 1200 }, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
    });
})();

/* ── Score trend chart ── */
(function () {
    const ctx = document.getElementById('scoreTrendChart');
    if (!ctx) return;
    const labels = [], data = [];
    if (SCORE_12M) { labels.push('12mo ago'); data.push(SCORE_12M); }
    if (SCORE_6M)  { labels.push('6mo ago');  data.push(SCORE_6M); }
    if (SCORE_3M)  { labels.push('3mo ago');  data.push(SCORE_3M); }
    labels.push('Now'); data.push(SCORE);
    if (data.length < 2) return;
    new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label: 'Score', data, borderColor: SCORE_COL, backgroundColor: SCORE_COL + '18', borderWidth: 2.5, fill: true, tension: 0.4, pointBackgroundColor: SCORE_COL, pointRadius: 5 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { min: 200, max: 900, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
    });
})();

/* ── Sector chart ── */
(function () {
    const ctx = document.getElementById('sectorChart');
    if (!ctx || !SECTOR_DATA.length) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: SECTOR_DATA.map(s => s.sector),
            datasets: [
                { label: 'Performing', data: SECTOR_DATA.map(s => (s.perf||0)+(s.clean||0)), backgroundColor: '#0FA958', borderRadius: 4 },
                { label: 'NPA',        data: SECTOR_DATA.map(s => s.npa||0),                 backgroundColor: '#dc2626', borderRadius: 4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } } }, scales: { x: { stacked: true, grid: { display: false } }, y: { stacked: true, grid: { color: '#f1f5f9' } } } }
    });
})();

/* ── Sidebar donut ── */
(function () {
    const ctx = document.getElementById('sidebarDonut');
    if (!ctx || !SCORE) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: { datasets: [{ data: [Math.max(0, SCORE - 200), 700 - Math.max(0, SCORE - 200)], backgroundColor: [SCORE_COL, '#f1f5f9'], borderWidth: 0, borderRadius: 4 }] },
        options: { cutout: '72%', animation: { animateRotate: true, duration: 1400, delay: 300 }, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
    });
})();

/* ── Print via PDF (opens PDF tab then calls print) ── */
function printPdfReport() {
    const win = window.open(PDF_URL, '_blank');
    if (win) {
        win.addEventListener('load', () => {
            try { win.print(); } catch (e) { /* PDF viewer handles it */ }
        });
    }
}
</script>
@endpush