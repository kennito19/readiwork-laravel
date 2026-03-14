@php
$delinquency  = $crb['delinquency']  ?? [];
$scrub_data   = $crb['scrub']        ?? [];
$credit_info  = $crb['credit_info']  ?? [];

$full_name   = $scrub_data['names'][0] ?? $req->full_name ?? 'Applicant';
$id_number   = $req->national_id;
$dob_raw     = $scrub_data['date_of_being'][0] ?? null;
$gender_raw  = $scrub_data['gender'][0] ?? null;
$gender_label = $gender_raw ? match(strtoupper((string)$gender_raw)) { 'M' => 'Male', 'F' => 'Female', default => $gender_raw } : 'N/A';
$scrub_phones = $scrub_data['phone'] ?? [];
$scrub_emails = $scrub_data['email'] ?? [];
$scrub_employ = $scrub_data['employment'] ?? [];

try { $dob_fmt = $dob_raw ? \Carbon\Carbon::parse($dob_raw)->format('j F Y') : 'N/A'; }
catch (\Exception $e) { $dob_fmt = $dob_raw ?? 'N/A'; }

$delinquency_code    = (string)($delinquency['delinquency_code'] ?? $delinquency['deliquency_code'] ?? '');
$delinquency_summary = $delinquency['delinquency_summary'] ?? null;
$outstanding_bal     = (float)($delinquency['outstanding_balance'] ?? $credit_info['total_outstanding_balance'] ?? 0);
$no_facilities       = (int)($delinquency['no_of_facilities'] ?? $credit_info['total_accounts'] ?? 0);
$overdue_amount      = (float)($credit_info['total_overdue_amount'] ?? $delinquency['overdue_amount'] ?? 0);
$npa_accounts        = (int)($credit_info['npa_accounts'] ?? $credit_info['total_npa'] ?? 0);
$performing_accounts = (int)($credit_info['performing_accounts'] ?? max(0, $no_facilities - $npa_accounts));
$accounts            = $credit_info['account_info'] ?? $credit_info['credit_accounts'] ?? $credit_info['accounts'] ?? [];

$code_info = [
    '001' => ['Safe — No adverse history',            '#16a34a', false],
    '002' => ['Good Standing — Loans paid on time',   '#0ea5e9', false],
    '003' => ['Needs Attention — Some late payments', '#d97706', false],
    '004' => ['Loan Problem — Unpaid/defaulted',      '#dc2626', true],
    '005' => ['Written Off — Debt written off',       '#991b1b', true],
];
[$code_label, $code_color, $is_blacklisted] = $code_info[$delinquency_code] ?? ['Unknown Status','#6b7280',false];

$status_emoji = $is_blacklisted ? '🚨' : ($delinquency_code === '003' ? '⚠️' : '✅');
$status_text  = $is_blacklisted ? 'CRB LISTED' : ($delinquency_code === '003' ? 'NEEDS ATTENTION' : 'CRB CLEAR');

$report_date = now()->format('j F Y, g:i A');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Readiwork – CRB Blacklist Status Report</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:"Helvetica Neue",Arial,Helvetica,sans-serif; font-size:15px; color:#1e293b; line-height:1.48; background:#fff; }
    @page { size:A4; margin:1.8cm 1.5cm; }

    .header { background:#0f172a; color:white; padding:1.8rem 2rem; border-bottom:5px solid #10b981; }
    .header-inner { display:flex; justify-content:space-between; align-items:center; }
    .logo { font-size:2.1rem; font-weight:800; letter-spacing:-0.5px; }
    .logo span { color:#10b981; }
    .header-meta { text-align:right; font-size:.92rem; }
    .meta-label { color:#94a3b8; text-transform:uppercase; font-size:.78rem; font-weight:600; letter-spacing:.4px; margin-bottom:.2rem; display:block; }
    .meta-value { font-weight:700; font-size:1.05rem; color:white; }

    .status-bar { padding:1.4rem 2rem; margin:1.5rem 0; border-radius:6px; display:flex; align-items:center; gap:1.5rem; }
    .status-bar.clear    { background:#f0fdf4; border:1px solid #a7f3d0; }
    .status-bar.caution  { background:#fffbeb; border:1px solid #fde68a; }
    .status-bar.listed   { background:#fef2f2; border:1px solid #fecaca; }
    .status-emoji { font-size:3rem; line-height:1; }
    .status-code-badge { display:inline-block; padding:.35rem 1rem; border-radius:999px; font-size:.9rem; font-weight:700; margin-top:.4rem; }

    .section { margin:2rem 0; }
    .section-title { font-size:1.25rem; font-weight:800; color:#0f172a; padding-bottom:.6rem; border-bottom:2px solid #e2e8f0; margin-bottom:1.1rem; }

    .info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
    .info-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:1rem 1.2rem; }
    .info-label { font-size:.78rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:.3rem; }
    .info-value { font-size:1.05rem; font-weight:700; color:#0f172a; }

    .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.2rem; }
    .stat-cell { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:1rem; text-align:center; }
    .stat-val { font-size:1.3rem; font-weight:800; color:#0f172a; margin-bottom:.3rem; }
    .stat-lbl { font-size:.72rem; color:#64748b; }

    table { width:100%; border-collapse:collapse; font-size:.83rem; }
    th { padding:8px 12px; font-size:.7rem; font-weight:700; text-transform:uppercase; color:#64748b; background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left; }
    td { padding:9px 12px; border-bottom:1px solid #e2e8f0; }
    tr:last-child td { border-bottom:0; }

    .security-box { margin:1.8rem 0; padding:1.3rem; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:6px; font-size:.92rem; }
    .security-title { font-weight:700; margin-bottom:.6rem; color:#334155; }
    .hash { font-family:"Courier New",monospace; background:#f1f5f9; padding:.7rem 1rem; border-radius:4px; word-break:break-all; color:#1e293b; font-size:.88rem; }

    .footer { margin-top:3rem; padding-top:1.4rem; border-top:3px solid #10b981; font-size:.88rem; color:#475569; display:flex; justify-content:space-between; align-items:center; }
    .footer-brand { font-size:1.28rem; font-weight:800; }
    .footer-brand span { color:#10b981; }
    .footer-right { text-align:right; }
  </style>
</head>
<body onload="window.print()">

  <div class="header">
    <div class="header-inner">
      <div>
        <div class="logo">Readi<span>work</span></div>
        <div style="font-size:.95rem;opacity:.85;margin-top:.3rem;">Official CRB Blacklist Status Report</div>
      </div>
      <div class="header-meta">
        <div><span class="meta-label">Generated</span><span class="meta-value">{{ $report_date }}</span></div>
        <div style="margin:.6rem 0;"><span class="meta-label">National ID</span><span class="meta-value">{{ $id_number }}</span></div>
        <div><span class="meta-label">Reference</span><span class="meta-value">#RW-CB-{{ $req->id }}</span></div>
      </div>
    </div>
  </div>

  <div class="status-bar {{ $is_blacklisted ? 'listed' : ($delinquency_code === '003' ? 'caution' : 'clear') }}">
    <div class="status-emoji">{{ $status_emoji }}</div>
    <div>
      <div style="font-size:1.6rem;font-weight:800;color:{{ $code_color }};">{{ $status_text }}</div>
      <div style="font-size:.9rem;color:#475569;margin-top:.2rem;">{{ $full_name }}</div>
      <div class="status-code-badge" style="background:{{ $code_color }}22;color:{{ $code_color }};border:1px solid {{ $code_color }}66;">
        Code {{ $delinquency_code ?: '—' }} — {{ $code_label }}
      </div>
    </div>
  </div>

  <div class="section">
    <div class="section-title">CRB Status Summary</div>
    <div class="stats-row">
      <div class="stat-cell">
        <div class="stat-val" style="color:{{ $code_color }};">{{ $delinquency_code ?: '—' }}</div>
        <div class="stat-lbl">Delinquency Code</div>
      </div>
      <div class="stat-cell">
        <div class="stat-val">{{ $no_facilities ?: '0' }}</div>
        <div class="stat-lbl">Total Facilities</div>
      </div>
      <div class="stat-cell">
        <div class="stat-val" style="color:{{ $outstanding_bal > 0 ? '#d97706' : '#16a34a' }};font-size:{{ $outstanding_bal > 0 ? '1rem' : '1.3rem' }};">
          {{ $outstanding_bal > 0 ? 'KES ' . number_format($outstanding_bal) : 'None' }}
        </div>
        <div class="stat-lbl">Outstanding Balance</div>
      </div>
    </div>
    @if($npa_accounts > 0 || $overdue_amount > 0 || $performing_accounts > 0)
    <div class="stats-row">
      @if($performing_accounts > 0)
      <div class="stat-cell" style="background:#f0fdf4;border-color:#a7f3d0;">
        <div class="stat-val" style="color:#16a34a;">{{ $performing_accounts }}</div>
        <div class="stat-lbl" style="color:#166534;">Performing</div>
      </div>
      @endif
      @if($npa_accounts > 0)
      <div class="stat-cell" style="background:#fef2f2;border-color:#fecaca;">
        <div class="stat-val" style="color:#dc2626;">{{ $npa_accounts }}</div>
        <div class="stat-lbl" style="color:#991b1b;">Non-Performing</div>
      </div>
      @endif
      @if($overdue_amount > 0)
      <div class="stat-cell" style="background:#fff7ed;border-color:#fed7aa;">
        <div class="stat-val" style="color:#c2410c;font-size:1rem;">KES {{ number_format($overdue_amount) }}</div>
        <div class="stat-lbl" style="color:#9a3412;">Total Overdue</div>
      </div>
      @endif
    </div>
    @endif
    @if($delinquency_summary)
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:14px;margin-top:.8rem;">
      <div style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Delinquency Summary</div>
      <div style="font-size:.88rem;color:#334155;line-height:1.6;">{{ $delinquency_summary }}</div>
    </div>
    @endif
  </div>

  <div class="section">
    <div class="section-title">Personal Details</div>
    <div class="info-grid">
      <div class="info-card"><div class="info-label">Full Name</div><div class="info-value">{{ $full_name }}</div></div>
      <div class="info-card"><div class="info-label">National ID</div><div class="info-value">{{ $id_number }}</div></div>
      <div class="info-card"><div class="info-label">Date of Birth</div><div class="info-value">{{ $dob_fmt }}</div></div>
      <div class="info-card"><div class="info-label">Gender</div><div class="info-value">{{ $gender_label }}</div></div>
      @if(!empty($scrub_phones[0]))<div class="info-card"><div class="info-label">Phone</div><div class="info-value">{{ $scrub_phones[0] }}</div></div>@endif
      @if(!empty($scrub_emails[0]))<div class="info-card"><div class="info-label">Email</div><div class="info-value" style="font-size:.85rem;">{{ $scrub_emails[0] }}</div></div>@endif
    </div>
  </div>

  @if(!empty($accounts))
  <div class="section">
    <div class="section-title">Credit Account Details</div>
    <table>
      <thead>
        <tr>
          <th>Lender / Institution</th>
          <th>Loan Type</th>
          <th style="text-align:right;">Balance</th>
          <th style="text-align:right;">Arrears</th>
          <th style="text-align:center;">Status</th>
          <th style="text-align:center;">Opened</th>
        </tr>
      </thead>
      <tbody>
      @foreach($accounts as $acc)
      @php
      $lender      = $acc['subscriber_name'] ?? $acc['lender'] ?? $acc['institution'] ?? $acc['lender_name'] ?? 'Unknown Lender';
      $acc_type    = $acc['account_type'] ?? $acc['product_type'] ?? $acc['facility_type'] ?? '—';
      $acc_bal     = (float)($acc['current_balance'] ?? $acc['outstanding_balance'] ?? $acc['balance'] ?? 0);
      $acc_overdue = (float)($acc['overdue_balance'] ?? $acc['arrears'] ?? $acc['overdue_amount'] ?? 0);
      $acc_status  = $acc['account_status'] ?? $acc['status'] ?? $acc['performance_status'] ?? '';
      $acc_opened  = $acc['date_opened'] ?? $acc['open_date'] ?? $acc['account_date'] ?? null;
      $is_npa      = stripos($acc_status,'npa')!==false || stripos($acc_status,'default')!==false || stripos($acc_status,'loss')!==false || stripos($acc_status,'write')!==false || $acc_overdue > 0;
      $status_color = $is_npa ? '#dc2626' : '#16a34a';
      $status_label = $acc_status ?: ($is_npa ? 'Non-Performing' : 'Performing');
      @endphp
      <tr>
        <td style="font-weight:700;">{{ $lender }}</td>
        <td style="color:#475569;">{{ $acc_type }}</td>
        <td style="text-align:right;font-weight:700;color:{{ $acc_bal > 0 ? '#0f172a' : '#6b7280' }};">{{ $acc_bal > 0 ? 'KES ' . number_format($acc_bal) : '—' }}</td>
        <td style="text-align:right;font-weight:700;color:{{ $acc_overdue > 0 ? '#dc2626' : '#6b7280' }};">{{ $acc_overdue > 0 ? 'KES ' . number_format($acc_overdue) : '—' }}</td>
        <td style="text-align:center;">
          <span style="background:{{ $status_color }}22;color:{{ $status_color }};border:1px solid {{ $status_color }}44;padding:2px 8px;border-radius:999px;font-size:.72rem;font-weight:700;white-space:nowrap;">
            {{ ucwords(strtolower($status_label)) }}
          </span>
        </td>
        <td style="text-align:center;color:#64748b;font-size:.78rem;">{{ $acc_opened ? \Carbon\Carbon::parse($acc_opened)->format('M Y') : '—' }}</td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @endif

  <div class="section">
    <div class="section-title">Payment Receipt</div>
    <div class="info-grid">
      <div class="info-card"><div class="info-label">Amount Paid</div><div class="info-value" style="color:#16a34a;">KES {{ number_format((float)($req->payment_amount ?? $req->price ?? 0)) }}</div></div>
      <div class="info-card"><div class="info-label">M-Pesa Receipt</div><div class="info-value" style="font-family:monospace;">{{ $req->mpesa_receipt_number ?: '—' }}</div></div>
      <div class="info-card"><div class="info-label">Payment Date</div><div class="info-value" style="font-size:.88rem;">{{ $req->payment_date ? \Carbon\Carbon::parse($req->payment_date)->format('d M Y H:i') : $req->created_at->format('d M Y H:i') }}</div></div>
    </div>
  </div>

  <div class="security-box">
    <div class="security-title">Report Security Hash (SHA-256)</div>
    <div class="hash">{{ hash('sha256', $req->id . $id_number . $report_date) }}</div>
  </div>

  <div class="footer">
    <div class="footer-brand">Readi<span>work</span></div>
    <div class="footer-right">Report ID: #RW-CB-{{ $req->id }}<br>Generated: {{ $report_date }}</div>
  </div>

</body>
</html>
