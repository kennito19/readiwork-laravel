<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Readiwork – Loan Eligibility Report</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    @page { size: A4; margin: 0; }
    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }

    body {
      font-family: "Helvetica Neue", Arial, Helvetica, sans-serif;
      font-size: 11.5px;
      color: #1e293b;
      line-height: 1.5;
      background: #fff;
      width: 210mm;
    }

    /* ─── HEADER ─────────────────────────────── */
    .hdr {
      background: #071629;
      position: relative;
      overflow: hidden;
    }
    .hdr::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: radial-gradient(circle, rgba(255,255,255,.025) 1px, transparent 1px);
      background-size: 18px 18px;
    }
    .hdr::after {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 4px;
      background: linear-gradient(180deg, #0FA958, #10b981);
    }
    .hdr-inner {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 1.6rem 2rem 1.6rem 2.4rem;
    }
    .logo { font-size: 1.8rem; font-weight: 900; color: #fff; line-height: 1; letter-spacing: -0.5px; }
    .logo span { color: #0FA958; }
    .logo-sub { font-size: .62rem; color: rgba(255,255,255,.35); text-transform: uppercase; letter-spacing: .8px; font-weight: 600; margin-top: 5px; }
    .hdr-meta { display: flex; flex-direction: column; gap: .4rem; text-align: right; }
    .meta-row { display: flex; flex-direction: column; align-items: flex-end; gap: 1px; }
    .meta-lbl { font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: rgba(255,255,255,.28); }
    .meta-val { font-size: .82rem; font-weight: 700; color: #fff; }
    .meta-val.mono { font-family: "Courier New", monospace; letter-spacing: 1.5px; font-size: .75rem; }
    .hdr-stripe { height: 3px; background: linear-gradient(90deg, #0FA958 0%, #10b981 40%, rgba(15,169,88,.1) 100%); }

    /* ─── VERDICT BANNER ─────────────────────── */
    .verdict { display: flex; align-items: stretch; border-bottom: 1px solid #e8eef6; }
    .vl { flex: 1; padding: 1.5rem 1.8rem; }
    .verdict.eligible   .vl { background: linear-gradient(135deg, #f0fdf9, #ecfdf5); }
    .verdict.ineligible .vl { background: linear-gradient(135deg, #fef9f9, #fef2f2); }
    .v-eyebrow {
      font-size: .6rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: .9px; margin-bottom: 5px;
    }
    .verdict.eligible   .v-eyebrow { color: #059669; }
    .verdict.ineligible .v-eyebrow { color: #dc2626; }
    .v-name { font-size: 1.6rem; font-weight: 900; letter-spacing: -.5px; line-height: 1.1; margin-bottom: 4px; }
    .verdict.eligible   .v-name { color: #064e3b; }
    .verdict.ineligible .v-name { color: #7f1d1d; }
    .v-sub { font-size: .77rem; opacity: .75; }
    .verdict.eligible   .v-sub { color: #065f46; }
    .verdict.ineligible .v-sub { color: #991b1b; }
    .vr {
      width: 190px; flex-shrink: 0;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 1.3rem 1rem; gap: 7px;
      border-left: 1px solid;
    }
    .verdict.eligible   .vr { background: #052e16; border-color: #065f46; }
    .verdict.ineligible .vr { background: #3b0f0f; border-color: #7f1d1d; }
    .v-badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 5px 13px; border-radius: 8px;
      font-size: .75rem; font-weight: 800;
    }
    .verdict.eligible   .v-badge { background: #0FA958; color: #fff; }
    .verdict.ineligible .v-badge { background: #ef4444; color: #fff; }
    .v-amt-lbl { font-size: .56rem; font-weight: 700; text-transform: uppercase; letter-spacing: .9px; color: rgba(255,255,255,.3); }
    .v-amt { font-size: 1.35rem; font-weight: 900; letter-spacing: -1px; line-height: 1; }
    .verdict.eligible   .v-amt { color: #4ade80; }
    .verdict.ineligible .v-amt { color: #f87171; }

    /* ─── STATS STRIP ────────────────────────── */
    .stats { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid #e8eef6; }
    .stat { padding: 11px 14px; border-right: 1px solid #e8eef6; text-align: center; }
    .stat:last-child { border-right: 0; }
    .stat-val { font-size: 1.15rem; font-weight: 900; line-height: 1; margin-bottom: 3px; }
    .stat-lbl { font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #94a3b8; }

    /* ─── BODY ───────────────────────────────── */
    .body { padding: 1.4rem 2rem; }

    /* Section header */
    .sh { display: flex; align-items: center; gap: 7px; margin-bottom: 9px; padding-bottom: 7px; border-bottom: 1.5px solid #e8eef6; }
    .sh-icon { width: 20px; height: 20px; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .sh-title { font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; color: #475569; flex: 1; }
    .sh-badge { display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 999px; font-size: .62rem; font-weight: 700; }

    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .section  { margin-bottom: 14px; }

    /* Identity */
    .id-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }
    .id-cell { background: #f8fafc; border: 1px solid #e8eef6; border-left: 3px solid #e8eef6; border-radius: 5px; padding: 8px 10px; }
    .id-cell.hi { border-left-color: #0FA958; }
    .id-lbl { font-size: .57rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #94a3b8; margin-bottom: 2px; }
    .id-val { font-size: .84rem; font-weight: 700; color: #0f172a; }
    .id-val.mono { font-family: "Courier New", monospace; letter-spacing: 1px; font-size: .76rem; }

    /* Score panel */
    .score-panel { background: #f8fafc; border: 1px solid #e8eef6; border-radius: 7px; padding: 12px; }
    .score-top { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .score-circle {
      width: 64px; height: 64px; border-radius: 50%; flex-shrink: 0;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      border: 4px solid;
    }
    .score-num { font-size: 1.25rem; font-weight: 900; line-height: 1; }
    .score-den { font-size: .52rem; color: #94a3b8; margin-top: 1px; }
    .score-tier {
      display: inline-block; padding: 2px 9px; border-radius: 999px;
      font-size: .67rem; font-weight: 700; margin-bottom: 5px;
    }
    .score-desc { font-size: .72rem; color: #475569; line-height: 1.55; }
    .rainbow-track {
      position: relative; height: 9px; border-radius: 999px; margin-bottom: 4px;
      background: linear-gradient(90deg, #dc2626 0%, #d97706 28%, #0891b2 52%, #2563eb 74%, #16a34a 100%);
    }
    .rainbow-thumb {
      position: absolute; top: 50%; transform: translate(-50%,-50%);
      width: 15px; height: 15px; background: #fff; border-radius: 50%;
      border: 2.5px solid #0f172a; box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    .rainbow-labels { display: flex; justify-content: space-between; font-size: .55rem; color: #94a3b8; }

    /* Risk meters */
    .risk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 10px; }
    .risk-card { background: #fff; border: 1px solid #e8eef6; border-radius: 6px; padding: 9px; }
    .risk-lbl { font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #94a3b8; margin-bottom: 4px; }
    .risk-val { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 3px; }
    .risk-sub { font-size: .65rem; color: #64748b; margin-bottom: 6px; }
    .risk-track { background: #e5e7eb; border-radius: 999px; height: 4px; overflow: hidden; }
    .risk-bar   { height: 100%; border-radius: 999px; }

    /* Stat boxes */
    .mini-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-bottom: 9px; }
    .mini-stat { background: #f8fafc; border: 1px solid #e8eef6; border-radius: 6px; padding: 9px; text-align: center; }
    .mini-val { font-size: .92rem; font-weight: 800; line-height: 1; margin-bottom: 2px; }
    .mini-lbl { font-size: .57rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; }

    /* Status box */
    .status-box { border-radius: 7px; padding: 10px 12px; display: flex; align-items: flex-start; gap: 10px; margin-bottom: 9px; }
    .status-box.clear   { background: #f0fdf4; border: 1px solid #bbf7d0; }
    .status-box.flagged { background: #fef2f2; border: 1px solid #fecaca; }
    .status-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: .85rem; flex-shrink: 0; font-weight: 900; }
    .status-box.clear   .status-icon { background: #0FA958; color: #fff; }
    .status-box.flagged .status-icon { background: #ef4444; color: #fff; }
    .status-title { font-size: .82rem; font-weight: 800; margin-bottom: 2px; }
    .status-box.clear   .status-title { color: #064e3b; }
    .status-box.flagged .status-title { color: #7f1d1d; }
    .status-desc { font-size: .71rem; line-height: 1.55; }
    .status-box.clear   .status-desc { color: #065f46; }
    .status-box.flagged .status-desc { color: #991b1b; }

    /* Tables */
    .tbl { width: 100%; border-collapse: collapse; font-size: .77rem; }
    .tbl th { padding: 6px 7px; background: #f8fafc; border-bottom: 1.5px solid #e8eef6; font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #64748b; text-align: left; }
    .tbl td { padding: 7px 7px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    .tbl tr:last-child td { border-bottom: 0; }

    .pill { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: .6rem; font-weight: 700; }
    .pill-green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .pill-red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .pill-gray   { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
    .pill-yellow { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

    .delinq-box { background: #fff5f5; border: 1px solid #fecaca; border-radius: 6px; padding: 9px 11px; margin-bottom: 9px; }
    .delinq-head { font-size: .67rem; font-weight: 800; color: #991b1b; margin-bottom: 6px; }

    /* Bar */
    .bar-wrap { display: inline-block; width: 55px; height: 4px; background: #e5e7eb; border-radius: 999px; vertical-align: middle; margin-right: 4px; overflow: hidden; }
    .bar-fill  { height: 100%; border-radius: 999px; background: #0FA958; }

    /* Tips */
    .tips-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
    .tip-item  { display: flex; gap: 8px; align-items: flex-start; background: #f8fafc; border: 1px solid #e8eef6; border-radius: 6px; padding: 9px; }
    .tip-num   { width: 18px; height: 18px; border-radius: 5px; background: #0FA958; color: #fff; font-size: .6rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .tip-title { font-size: .72rem; font-weight: 700; color: #0f172a; margin-bottom: 1px; }
    .tip-desc  { font-size: .65rem; color: #475569; line-height: 1.5; }

    /* Comp row */
    .comp-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 5px; margin-top: 9px; }
    .comp-cell { text-align: center; background: #fff; border: 1px solid #e8eef6; border-radius: 5px; padding: 5px 3px; }
    .comp-val  { font-size: .85rem; font-weight: 800; color: #0f172a; }
    .comp-lbl  { font-size: .53rem; color: #94a3b8; margin-top: 1px; }

    /* Hash */
    .hash-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 9px 11px; margin-bottom: 14px; }
    .hash-lbl { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #64748b; margin-bottom: 4px; }
    .hash-val { font-family: "Courier New", monospace; font-size: .72rem; color: #334155; word-break: break-all; background: #fff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 9px; line-height: 1.6; }

    .source-row  { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 7px; }
    .source-pill { display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 999px; padding: 2px 8px; font-size: .6rem; font-weight: 600; color: #475569; }
    .source-dot  { width: 5px; height: 5px; border-radius: 50%; background: #0FA958; }

    .no-break { page-break-inside: avoid; }
    .page-break { page-break-before: always; }

    /* ─── FOOTER ─────────────────────────────── */
    .footer { background: #f8fafc; border-top: 1px solid #e8eef6; padding: 9px 2rem; display: flex; justify-content: space-between; align-items: center; margin-top: 1.4rem; }
    .footer-brand { font-size: .9rem; font-weight: 900; color: #0f172a; }
    .footer-brand span { color: #0FA958; }
    .footer-note  { font-size: .58rem; color: #94a3b8; max-width: 260px; line-height: 1.5; margin-top: 2px; }
    .footer-right { text-align: right; font-size: .65rem; color: #94a3b8; line-height: 1.8; }
    .footer-right strong { color: #475569; }
  </style>
</head>
<body>

@php
/* ── Unpack CRB response (same logic as result page) ── */
$ci    = isset($crb['credit_info']) ? $crb['credit_info'] : $crb;
$idv   = $ci['identity_verification'] ?? [];
$scrub = $ci['identity_scrub']        ?? [];

$parts      = array_filter([$idv['first_name'] ?? '', $idv['other_name'] ?? '', $idv['surname'] ?? '']);
$full_name  = trim(implode(' ', $parts)) ?: ($scrub['names'][0] ?? $req->full_name ?? 'Applicant');
$id_number  = $req->national_id;
$dob        = $idv['dob']        ?? null;
$gender_raw = $idv['gender']     ?? null;
$gender_label = $gender_raw === 'M' ? 'Male' : ($gender_raw === 'F' ? 'Female' : ($gender_raw ?? 'N/A'));
$citizenship  = $idv['citizenship'] ?? 'Kenyan';
$district     = $idv['district']    ?? null;

$credit_score = (int)($ci['credit_score'] ?? 0);
$ppi          = null;
$pod          = null;
$ppi_text     = 'N/A'; $ppi_color = '#9ca3af'; $ppi_pct = 0;
$pod_val      = 'N/A'; $pod_pct   = 0;         $pod_color = '#9ca3af';
$score_3m = $score_6m = $score_12m = $score_best = $score_worst = 0;

$delinquency_code = $ci['delinquency_code'] ?? null;
$is_defaulting    = ($delinquency_code === 'D' || $delinquency_code === '1' || $delinquency_code === 1);

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

$is_eligible   = !$is_defaulting && $credit_score >= 500;
$pre_qualified = 0;
if ($is_eligible) {
    if ($credit_score >= 800)     $pre_qualified = 1000000;
    elseif ($credit_score >= 700) $pre_qualified = 500000;
    elseif ($credit_score >= 600) $pre_qualified = 200000;
    else                          $pre_qualified = 100000;
}
$report_date = now()->format('j F Y, g:i A');

/* Delinquent accounts */
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

{{-- ══ HEADER ══ --}}
<div class="hdr">
  <div class="hdr-inner">
    <div>
      <div class="logo">Readi<span>work</span></div>
      <div class="logo-sub">Official Loan Eligibility Report</div>
    </div>
    <div class="hdr-meta">
      <div class="meta-row">
        <span class="meta-lbl">Generated</span>
        <span class="meta-val">{{ $report_date }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-lbl">National ID</span>
        <span class="meta-val mono">{{ $id_number }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-lbl">Reference</span>
        <span class="meta-val mono">#RW-LE-{{ $req->id }}</span>
      </div>
    </div>
  </div>
</div>
<div class="hdr-stripe"></div>

{{-- ══ VERDICT BANNER ══ --}}
<div class="verdict {{ $is_eligible ? 'eligible' : 'ineligible' }}">
  <div class="vl">
    <div class="v-eyebrow">{{ $is_eligible ? '✓ Loan Eligible — Credit Profile Confirmed' : '✗ Not Eligible — Credit Criteria Not Met' }}</div>
    <div class="v-name">{{ strtoupper($full_name) }}</div>
    <div class="v-sub">Metropol CRB · Kenya National Registry · Score: {{ $credit_score ?: 'N/A' }} / 900 · {{ $score_label }}</div>
  </div>
  <div class="vr">
    <div class="v-badge">{{ $is_eligible ? '✓ Pre-Approved' : '✗ Not Approved' }}</div>
    <div class="v-amt-lbl">{{ $is_eligible ? 'Max Pre-Qualified' : 'Pre-Qualified' }}</div>
    <div class="v-amt">{{ $is_eligible ? 'KES ' . number_format($pre_qualified) : 'None' }}</div>
  </div>
</div>

{{-- ══ STATS STRIP ══ --}}
<div class="stats">
  <div class="stat">
    <div class="stat-val" style="color:{{ $score_color }};">{{ $credit_score ?: 'N/A' }}</div>
    <div class="stat-lbl">Credit Score</div>
  </div>
  <div class="stat">
    <div class="stat-val" style="color:{{ $is_defaulting ? '#dc2626' : '#16a34a' }};">{{ $is_defaulting ? 'Listed' : 'Clear' }}</div>
    <div class="stat-lbl">CRB Status</div>
  </div>
  <div class="stat">
    <div class="stat-val" style="color:{{ $npa_accounts > 0 ? '#dc2626' : '#16a34a' }};">{{ $npa_accounts }}</div>
    <div class="stat-lbl">NPA Accounts</div>
  </div>
  <div class="stat">
    <div class="stat-val" style="color:{{ $outstanding_bal > 0 ? '#d97706' : '#0f172a' }};">{{ $outstanding_bal > 0 ? 'KES ' . number_format($outstanding_bal) : 'None' }}</div>
    <div class="stat-lbl">Outstanding Balance</div>
  </div>
</div>

{{-- ══ BODY ══ --}}
<div class="body">

  {{-- ROW 1: Identity + Score ──────────────────── --}}
  <div class="two-col no-break">

    <div>
      <div class="sh">
        <div class="sh-icon" style="background:#f0f9ff;">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
        <span class="sh-title">Personal Identity</span>
        <span class="sh-badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">✓ Verified</span>
      </div>
      <div class="id-grid">
        <div class="id-cell hi"><div class="id-lbl">Full Name</div><div class="id-val">{{ $full_name ?: 'N/A' }}</div></div>
        <div class="id-cell hi"><div class="id-lbl">National ID</div><div class="id-val mono">{{ $id_number }}</div></div>
        <div class="id-cell"><div class="id-lbl">Date of Birth</div><div class="id-val">{{ $dob ?: 'N/A' }}</div></div>
        <div class="id-cell"><div class="id-lbl">Gender</div><div class="id-val">{{ $gender_label }}</div></div>
        <div class="id-cell"><div class="id-lbl">Citizenship</div><div class="id-val">{{ $citizenship }}</div></div>
        <div class="id-cell"><div class="id-lbl">District</div><div class="id-val">{{ $district ?: 'N/A' }}</div></div>
      </div>
    </div>

    <div>
      <div class="sh">
        <div class="sh-icon" style="background:#f5f3ff;">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
        </div>
        <span class="sh-title">Credit Score Analysis</span>
      </div>
      <div class="score-panel">
        <div class="score-top">
          <div class="score-circle" style="border-color:{{ $score_color }};background:{{ $score_bg }};">
            <div class="score-num" style="color:{{ $score_color }};">{{ $credit_score ?: '–' }}</div>
            <div class="score-den">/ 900</div>
          </div>
          <div>
            <span class="score-tier" style="background:{{ $score_bg }};color:{{ $score_color }};border:1px solid {{ $score_border }};">{{ $score_label }} Credit</span>
            <div class="score-desc">
              @if($credit_score >= 700) <strong>Low-risk</strong> borrower. Most lenders will approve.
              @elseif($credit_score >= 500) <strong>Moderate-risk</strong>. Some lenders will approve.
              @elseif($credit_score > 0) <strong>High-risk</strong>. Unlikely to be approved.
              @else Score unavailable from CRB. @endif
            </div>
          </div>
        </div>
        <div class="rainbow-track">
          <div class="rainbow-thumb" style="left:{{ $score_pct }}%;"></div>
        </div>
        <div class="rainbow-labels">
          <span>200</span><span>350</span><span>500</span><span>650</span><span>800</span><span>900</span>
        </div>
        @if($ppi || $pod)
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
            <div class="risk-sub">Likelihood of default</div>
            <div class="risk-track"><div class="risk-bar" style="width:{{ $pod_pct }}%;background:{{ $pod_color }};"></div></div>
          </div>
        </div>
        @endif
        @if($score_3m || $score_6m || $score_12m || $score_best || $score_worst)
        <div class="comp-row">
          <div class="comp-cell"><div class="comp-val">{{ $score_3m  ?: '–' }}</div><div class="comp-lbl">3 Mo</div></div>
          <div class="comp-cell"><div class="comp-val">{{ $score_6m  ?: '–' }}</div><div class="comp-lbl">6 Mo</div></div>
          <div class="comp-cell"><div class="comp-val">{{ $score_12m ?: '–' }}</div><div class="comp-lbl">12 Mo</div></div>
          <div class="comp-cell"><div class="comp-val" style="color:#16a34a;">{{ $score_best  ?: '–' }}</div><div class="comp-lbl">Best</div></div>
          <div class="comp-cell"><div class="comp-val" style="color:#dc2626;">{{ $score_worst ?: '–' }}</div><div class="comp-lbl">Worst</div></div>
        </div>
        @endif
      </div>
    </div>

  </div>

  {{-- Loan Payment Status ───────────────────────── --}}
  <div class="section no-break">
    <div class="sh">
      <div class="sh-icon" style="background:{{ $is_defaulting ? '#fef2f2' : '#f0fdf4' }};">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $is_defaulting ? '#ef4444' : '#0FA958' }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          @if($is_defaulting)<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
          @else<polyline points="20 6 9 17 4 12"/>@endif
        </svg>
      </div>
      <span class="sh-title">Loan Payment Status &amp; CRB Standing</span>
    </div>

    <div class="status-box {{ $is_defaulting ? 'flagged' : 'clear' }}">
      <div class="status-icon">{{ $is_defaulting ? '✗' : '✓' }}</div>
      <div>
        <div class="status-title">{{ $is_defaulting ? 'CRB Flagged — Loan Default Detected' : 'Clean CRB Record — No Defaults Detected' }}</div>
        <div class="status-desc">
          @if($is_defaulting)
            One or more loans have been flagged as delinquent with Metropol CRB. Contact the reporting lender and clear the outstanding debt before applying for new credit.
          @else
            Your loan repayment history is clean. No lender has reported you for missed or unpaid loans. This is a strong positive signal to potential lenders.
          @endif
        </div>
      </div>
    </div>

    <div class="mini-stats">
      <div class="mini-stat"><div class="mini-val" style="color:{{ $no_facilities > 0 ? '#0f172a' : '#94a3b8' }};">{{ $no_facilities }}</div><div class="mini-lbl">Total Facilities</div></div>
      <div class="mini-stat"><div class="mini-val" style="color:{{ $npa_accounts > 0 ? '#dc2626' : '#16a34a' }};">{{ $npa_accounts }}</div><div class="mini-lbl">NPA Accounts</div></div>
      <div class="mini-stat"><div class="mini-val" style="color:{{ $outstanding_bal > 0 ? '#d97706' : '#0f172a' }};">KES {{ number_format($outstanding_bal) }}</div><div class="mini-lbl">Outstanding</div></div>
      <div class="mini-stat"><div class="mini-val" style="color:{{ $overdue_amount > 0 ? '#dc2626' : '#16a34a' }};">KES {{ number_format($overdue_amount) }}</div><div class="mini-lbl">Overdue</div></div>
    </div>
  </div>

  {{-- Delinquent accounts ──────────────────────── --}}
  @if(!empty($delinq_accounts))
  <div class="section no-break">
    <div class="delinq-box">
      <div class="delinq-head">⚠ Outstanding Debts — What You Owe &amp; To Whom</div>
      <table class="tbl">
        <thead><tr><th>Lender</th><th>Type</th><th>Balance</th><th>Overdue</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($delinq_accounts as $da)
        <tr>
          <td style="font-weight:700;color:#0f172a;">{{ $da['name'] }}</td>
          <td style="color:#64748b;">{{ $da['type'] }}</td>
          <td style="font-weight:600;color:#d97706;">KES {{ number_format($da['balance']) }}</td>
          <td style="font-weight:700;color:#dc2626;">{{ $da['overdue'] > 0 ? 'KES ' . number_format($da['overdue']) : '—' }}</td>
          <td><span class="pill pill-red">{{ $da['status'] }}</span></td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- Loan Payment History ─────────────────────── --}}
  @if(!empty($accounts))
  <div class="section no-break">
    <div class="sh">
      <div class="sh-icon" style="background:#f0f9ff;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
      </div>
      <span class="sh-title">Loan Payment History</span>
      <span class="sh-badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">{{ count($accounts) }} Account{{ count($accounts) > 1 ? 's' : '' }}</span>
    </div>
    <table class="tbl">
      <thead>
        <tr><th>Lender / Account</th><th>Type</th><th>Original</th><th>Balance</th><th>Installment</th><th>Opened</th><th>Status</th></tr>
      </thead>
      <tbody>
      @foreach($accounts as $acc)
      @php
      $acc_name    = $acc['institution_name'] ?? $acc['lender_name'] ?? 'Unknown Lender';
      $acc_num     = $acc['account_number'] ?? null;
      $lender_type = $acc['sector'] ?? $acc['account_type'] ?? '–';
      $orig_amount = (float)($acc['loan_amount'] ?? $acc['original_amount'] ?? 0);
      $current_bal = (float)($acc['outstanding_balance'] ?? $acc['current_balance'] ?? 0);
      $installment = (float)($acc['scheduled_payment_amount'] ?? $acc['installment_amount'] ?? 0);
      $currency    = $acc['currency'] ?? 'KES';
      $date_opened = $acc['opening_date'] ?? $acc['date_account_opened'] ?? null;
      $status_raw  = strtolower($acc['account_status'] ?? '');
      $acc_dcode   = $acc['delinquency_code'] ?? null;
      $acc_delinq  = ($acc_dcode === 'D' || $acc_dcode === '1' || $acc_dcode === 1);
      $status_disp = match($status_raw) { 'a' => 'Active', 'c' => 'Closed', 'w' => 'Written Off', default => ucfirst($acc['account_status'] ?? '–') };
      $pill_class  = match(true) {
          in_array($status_raw, ['a','active'])  => 'pill-green',
          in_array($status_raw, ['c','closed'])  => 'pill-gray',
          $status_raw === 'w' || $acc_delinq     => 'pill-red',
          default                                 => 'pill-yellow',
      };
      @endphp
      <tr>
        <td>
          <div style="font-weight:700;color:#0f172a;">{{ $acc_name }}</div>
          @if($acc_num)<div style="font-size:.63rem;color:#64748b;font-family:monospace;">{{ $acc_num }}</div>@endif
        </td>
        <td style="color:#64748b;">{{ $lender_type }}</td>
        <td>{{ $orig_amount > 0 ? $currency . ' ' . number_format($orig_amount) : '—' }}</td>
        <td style="color:{{ $current_bal > 0 ? '#d97706' : '#16a34a' }};font-weight:700;">{{ $currency }} {{ number_format($current_bal) }}</td>
        <td>{{ $installment > 0 ? $currency . ' ' . number_format($installment) : '—' }}</td>
        <td style="color:#64748b;">{{ $date_opened ?: '—' }}</td>
        <td>
          <span class="pill {{ $pill_class }}">{{ $status_disp }}</span>
          @if($acc_delinq)<br><span class="pill pill-red" style="margin-top:2px;">Delinquent</span>@endif
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @endif

  {{-- Sector Performance ───────────────────────── --}}
  @if(!empty($sector_data))
  <div class="section no-break">
    <div class="sh">
      <div class="sh-icon" style="background:#fffbeb;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="18" y="3" width="4" height="18"/><rect x="10" y="8" width="4" height="13"/><rect x="2" y="13" width="4" height="8"/></svg>
      </div>
      <span class="sh-title">Lending Sector Performance</span>
    </div>
    @php $max_s = max(array_column($sector_data, 'total') ?: [1]); @endphp
    <table class="tbl">
      <thead><tr><th>Sector</th><th>Total</th><th>Performing</th><th>NPA</th><th>Distribution</th></tr></thead>
      <tbody>
      @foreach($sector_data as $s)
      @if($s['total'])
      @php $pct = $max_s > 0 ? round($s['total'] / $max_s * 100) : 0; @endphp
      <tr>
        <td style="font-weight:600;color:#0f172a;">{{ $s['sector'] }}</td>
        <td>{{ $s['total'] }}</td>
        <td style="color:#16a34a;font-weight:600;">{{ ($s['perf'] ?? 0) + ($s['clean'] ?? 0) }}</td>
        <td style="color:{{ ($s['npa'] ?? 0) > 0 ? '#dc2626' : '#6b7280' }};font-weight:600;">{{ $s['npa'] ?? 0 }}</td>
        <td><span class="bar-wrap"><span class="bar-fill" style="width:{{ $pct }}%;"></span></span>{{ $pct }}%</td>
      </tr>
      @endif
      @endforeach
      </tbody>
    </table>
  </div>
  @endif

  {{-- Lenders / Tips ───────────────────────────── --}}
  @if($is_eligible)
  <div class="section no-break">
    <div class="sh">
      <div class="sh-icon" style="background:#f0fdf4;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0FA958" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/><path d="M12 6v6l4 2"/></svg>
      </div>
      <span class="sh-title">Lenders Ready to Approve (up to KES {{ number_format($pre_qualified) }})</span>
    </div>
    <table class="tbl">
      <thead><tr><th>#</th><th>Lender</th><th>Max Loan</th><th>Interest Rate</th><th>Term</th></tr></thead>
      <tbody>
      @foreach([
        ['M-Shwari',        'KES 100,000',   '7.5% p.m.',   '30 days'],
        ['KCB M-Pesa',      'KES 1,000,000', '8.64% p.a.',  '12 months'],
        ['Tala',            'KES 50,000',    '11–15% p.m.', '30 days'],
        ['Branch',          'KES 70,000',    '17% p.m.',    '62 days'],
        ['Fuliza (M-Pesa)', 'KES 70,000',    '1% per day',  'On-demand'],
        ['Stawi',           'KES 250,000',   '9% p.a.',     '12 months'],
        ['Equity EazzyLoan','KES 3,000,000', '14% p.a.',    '36 months'],
        ['Hustler Fund',    'KES 50,000',    '8% p.a.',     '14 days'],
        ['Co-op mCo-opCash','KES 100,000',   '12% p.a.',    '12 months'],
        ['Zenka',           'KES 30,000',    '9–30% p.m.',  '61 days'],
      ] as $i => $l)
      <tr>
        <td style="color:#94a3b8;font-weight:600;">{{ $i+1 }}</td>
        <td style="font-weight:700;color:#0f172a;">{{ $l[0] }}</td>
        <td style="font-weight:600;color:#0FA958;">{{ $l[1] }}</td>
        <td style="color:#475569;">{{ $l[2] }}</td>
        <td style="color:#475569;">{{ $l[3] }}</td>
      </tr>
      @endforeach
      </tbody>
    </table>
    <div style="font-size:.58rem;color:#94a3b8;margin-top:5px;">* Rates are indicative. Confirm terms directly with the lender before applying.</div>
  </div>
  @else
  <div class="section no-break">
    <div class="sh">
      <div class="sh-icon" style="background:#f5f3ff;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      </div>
      <span class="sh-title">How to Improve Your Credit Score</span>
    </div>
    <div class="tips-grid">
      @foreach([
        ['Repay Outstanding Debts',       'Pay off any defaulted or overdue loans immediately. This is the single biggest factor affecting your score.'],
        ['Make Payments On Time',         'Set reminders for all loan repayments. Even one missed payment lowers your score significantly.'],
        ['Reduce Credit Utilization',     'Keep your credit usage below 30% of your total limit across all accounts.'],
        ['Negotiate with Lenders',        'Contact lenders for a repayment plan. Settling debts — even partially — improves your CRB status.'],
        ['Allow Time to Rebuild',         'A clean repayment record over 6–12 months can significantly raise your score.'],
        ['Limit New Credit Applications', 'Multiple loan applications in a short time signal financial distress to lenders.'],
      ] as $i => $t)
      <div class="tip-item">
        <div class="tip-num">{{ $i + 1 }}</div>
        <div>
          <div class="tip-title">{{ $t[0] }}</div>
          <div class="tip-desc">{{ $t[1] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Hash + Sources ───────────────────────────── --}}
  <div class="hash-box no-break">
    <div class="hash-lbl">Report Integrity Hash (SHA-256)</div>
    <div class="hash-val">{{ hash('sha256', $req->id . $id_number . $report_date) }}</div>
  </div>
  <div class="source-row">
    <div class="source-pill"><div class="source-dot"></div>Metropol CRB</div>
    <div class="source-pill"><div class="source-dot"></div>Kenya IPRS Registry</div>
    <div class="source-pill"><div class="source-dot"></div>Encrypted &amp; Confidential</div>
    <div class="source-pill"><div class="source-dot"></div>Readiwork Verified</div>
  </div>

</div>{{-- /body --}}

{{-- ══ FOOTER ══ --}}
<div class="footer">
  <div>
    <div class="footer-brand">Readi<span>work</span></div>
    <div class="footer-note">Generated automatically. Readiwork is not liable for decisions made based on this report. Always verify loan terms directly with the lender.</div>
  </div>
  <div class="footer-right">
    <strong>Report ID:</strong> #RW-LE-{{ $req->id }}<br>
    <strong>Generated:</strong> {{ $report_date }}<br>
    <strong>Source:</strong> Metropol CRB / IPRS
  </div>
</div>

</body>
</html>