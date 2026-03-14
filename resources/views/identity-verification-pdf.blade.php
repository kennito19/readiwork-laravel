<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Readiwork – Official Identity Verification Report</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Helvetica Neue", Arial, Helvetica, sans-serif;
      font-size: 15px;
      color: #1e293b;
      line-height: 1.48;
      background: #ffffff;
    }

    @page {
      size: A4;
      margin: 1.8cm 1.5cm;
    }

    /* HEADER */
    .header {
      background: #0f172a;
      color: white;
      padding: 1.8rem 2rem;
      border-bottom: 5px solid #10b981;
    }

    .header-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 2.1rem;
      font-weight: 800;
      letter-spacing: -0.5px;
    }

    .logo span {
      color: #10b981;
    }

    .header-meta {
      text-align: right;
      font-size: 0.92rem;
    }

    .meta-label {
      color: #94a3b8;
      text-transform: uppercase;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.4px;
      margin-bottom: 0.2rem;
      display: block;
    }

    .meta-value {
      font-weight: 700;
      font-size: 1.05rem;
      color: white;
    }

    /* STATUS BAR */
    .status-bar {
      background: #f0fdfa;
      border: 1px solid #a7f3d0;
      padding: 1.4rem 2rem;
      margin: 1.5rem 0;
      border-radius: 6px;
      position: relative;
    }

    .status-title {
      font-size: 1.85rem;
      font-weight: 800;
      color: #0f766e;
      margin-bottom: 0.35rem;
    }

    .status-subtitle {
      color: #0f766e;
      opacity: 0.9;
      font-size: 1.05rem;
    }

    .status-badge {
      position: absolute;
      top: 1.4rem;
      right: 2rem;
      background: #10b981;
      color: white;
      font-weight: 700;
      padding: 0.45rem 1.1rem;
      border-radius: 999px;
      font-size: 0.95rem;
      box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    }

    .not-verified {
      background: #ef4444;
      box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);
    }

    /* SECTION */
    .section {
      margin: 2.2rem 0;
    }

    .section-title {
      font-size: 1.35rem;
      font-weight: 800;
      color: #0f172a;
      padding-bottom: 0.6rem;
      border-bottom: 2px solid #e2e8f0;
      margin-bottom: 1.1rem;
    }

    /* GRID / TABLE */
    .info-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
    }

    .info-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 1.1rem 1.3rem;
    }

    .info-label {
      font-size: 0.78rem;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.35rem;
    }

    .info-value {
      font-size: 1.18rem;
      font-weight: 700;
      color: #0f172a;
    }

    /* CONFIRMATION */
    .confirmation {
      text-align: center;
      padding: 1.8rem 2rem;
      margin: 2rem 0;
      border-radius: 8px;
      font-size: 1.22rem;
      font-weight: 700;
    }

    .confirmation.verified {
      background: #ecfdf5;
      border: 2px solid #10b981;
      color: #065f46;
    }

    .confirmation.not-verified {
      background: #fef2f2;
      border: 2px solid #ef4444;
      color: #991b1b;
    }

    /* SECURITY */
    .security-box {
      margin: 1.8rem 0;
      padding: 1.3rem;
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      border-radius: 6px;
      font-size: 0.92rem;
    }

    .security-title {
      font-weight: 700;
      margin-bottom: 0.6rem;
      color: #334155;
    }

    .hash {
      font-family: "Courier New", Courier, monospace;
      background: #f1f5f9;
      padding: 0.7rem 1rem;
      border-radius: 4px;
      word-break: break-all;
      color: #1e293b;
      font-size: 0.9rem;
    }

    /* FOOTER */
    .footer {
      margin-top: 3rem;
      padding-top: 1.4rem;
      border-top: 3px solid #10b981;
      font-size: 0.88rem;
      color: #475569;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .footer-brand {
      font-size: 1.28rem;
      font-weight: 800;
    }

    .footer-brand span {
      color: #10b981;
    }

    .footer-right {
      text-align: right;
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <div class="header">
    <div class="header-inner">
      <div>
        <div class="logo">Readi<span>work</span></div>
        <div style="font-size:0.95rem; opacity:0.85; margin-top:0.3rem;">
          Official Identity Verification Report
        </div>
      </div>

      <div class="header-meta">
        <div>
          <span class="meta-label">Generated</span>
          <span class="meta-value">{{ $report_date }}</span>
        </div>
        <div style="margin:0.6rem 0;">
          <span class="meta-label">National ID</span>
          <span class="meta-value">{{ $id_number }}</span>
        </div>
        <div>
          <span class="meta-label">Reference</span>
          <span class="meta-value">#RW-ID-{{ $req->id }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- STATUS -->
  <div class="status-bar">
    <div class="status-badge {{ $is_verified ? '' : 'not-verified' }}">
      {{ $is_verified ? 'Registry Confirmed' : 'Not Found' }}
    </div>

    <div class="status-title">{{ strtoupper($full_name ?: 'N/A') }}</div>
    <div class="status-subtitle">Kenya National Registry Verification</div>
  </div>

  <!-- IDENTITY DETAILS -->
  <div class="section">
    <div class="section-title">Identity Details</div>

    <div class="info-grid">
      <div class="info-card">
        <div class="info-label">Full Name</div>
        <div class="info-value">{{ $full_name ?: 'N/A' }}</div>
      </div>
      <div class="info-card">
        <div class="info-label">National ID</div>
        <div class="info-value">{{ $id_number }}</div>
      </div>
      <div class="info-card">
        <div class="info-label">Date of Birth</div>
        <div class="info-value">{{ $dob_fmt ?: 'N/A' }}</div>
      </div>

      <div class="info-card">
        <div class="info-label">Gender</div>
        <div class="info-value">{{ $gender_label ?: 'N/A' }}</div>
      </div>
      <div class="info-card">
        <div class="info-label">Citizenship</div>
        <div class="info-value">{{ $citizenship ?? 'Kenyan' }}</div>
      </div>
      <div class="info-card">
        <div class="info-label">Serial</div>
        <div class="info-value">{{ $serial_no ?? 'N/A' }}</div>
      </div>

      <div class="info-card">
        <div class="info-label">District</div>
        <div class="info-value">{{ $district ?? 'N/A' }}</div>
      </div>
      <div class="info-card">
        <div class="info-label">Verification</div>
        <div class="info-value">{{ $is_verified ? 'Verified' : 'Not Verified' }}</div>
      </div>
      <div class="info-card">
        <div class="info-label">Generated</div>
        <div class="info-value">{{ $report_date }}</div>
      </div>
    </div>
  </div>

  <!-- CONFIRMATION -->
  <div class="confirmation {{ $is_verified ? 'verified' : 'not-verified' }}">
    {{ $is_verified ? 'Identity Successfully Verified' : 'Identity Could Not Be Verified' }}
  </div>

  <!-- SECURITY HASH -->
  <div class="security-box">
    <div class="security-title">Report Security Hash (SHA-256)</div>
    <div class="hash">{{ hash('sha256', $req->id . $id_number . $report_date) }}</div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    <div class="footer-brand">Readi<span>work</span></div>
    <div class="footer-right">
      Report ID: #RW-ID-{{ $req->id }}<br>
      Generated: {{ $report_date }}
    </div>
  </div>

</body>
</html>