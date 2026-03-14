@extends('layouts.app')
@section('title', 'API Documentation - Readiwork')

@push('head')
<style>
.doc-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:64px 0 48px;}
.doc-hero h1{font-size:clamp(1.9rem,4vw,2.6rem);font-weight:800;color:#fff;margin-bottom:12px;}
.doc-hero p{color:rgba(255,255,255,.65);font-size:1rem;max-width:540px;margin-bottom:24px;}
.doc-hero-badges{display:flex;gap:10px;flex-wrap:wrap;}
.doc-hero-badge{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.75);font-size:.8rem;font-weight:600;padding:6px 14px;border-radius:999px;display:inline-flex;align-items:center;gap:7px;}
.doc-hero-badge i{color:#0FA958;}
.doc-layout{display:grid;grid-template-columns:240px 1fr;gap:32px;padding:48px 0 80px;align-items:start;}
@media(max-width:900px){.doc-layout{grid-template-columns:1fr;}}
.doc-sidebar{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;position:sticky;top:88px;}
.doc-sidebar-title{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:12px;}
.doc-sidebar a{display:block;font-size:.85rem;color:#475569;padding:6px 10px;border-radius:8px;text-decoration:none;transition:all .12s;margin-bottom:2px;}
.doc-sidebar a:hover,.doc-sidebar a.active{background:#f0fdf4;color:#0FA958;font-weight:600;}
.doc-sidebar-group{margin-bottom:20px;}
.doc-body{min-width:0;}
.doc-section{margin-bottom:48px;}
.doc-section h2{font-size:1.4rem;font-weight:800;color:#0B1F3B;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #f1f5f9;}
.doc-section h3{font-size:1rem;font-weight:700;color:#0B1F3B;margin:24px 0 10px;}
.doc-section p{font-size:.9rem;color:#475569;line-height:1.75;margin-bottom:12px;}
.doc-section ul li{font-size:.88rem;color:#475569;line-height:1.65;margin-bottom:6px;}
.endpoint-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;overflow:hidden;}
.endpoint-head{display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid #f1f5f9;}
.method-badge{font-size:.72rem;font-weight:800;padding:3px 9px;border-radius:6px;letter-spacing:.5px;}
.method-badge.get{background:#dcfce7;color:#16a34a;}
.method-badge.post{background:#dbeafe;color:#2563eb;}
.endpoint-path{font-family:monospace;font-size:.88rem;color:#0B1F3B;font-weight:600;}
.endpoint-desc{font-size:.82rem;color:#64748b;margin-left:auto;}
.endpoint-body{padding:16px 18px;}
.code-block{background:#1e293b;border-radius:10px;padding:16px 18px;font-family:monospace;font-size:.82rem;color:#e2e8f0;line-height:1.6;overflow-x:auto;margin:12px 0;}
.code-block .comment{color:#64748b;}
.code-block .key{color:#7dd3fc;}
.code-block .val{color:#86efac;}
.code-block .str{color:#fde68a;}
.param-table{width:100%;border-collapse:collapse;font-size:.84rem;margin-top:10px;}
.param-table th{text-align:left;padding:8px 12px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#64748b;background:#f8fafc;border-bottom:2px solid #e2e8f0;}
.param-table td{padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;}
.param-table tr:last-child td{border-bottom:0;}
.param-required{background:#fef2f2;color:#dc2626;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:4px;}
.param-optional{background:#f8fafc;color:#64748b;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:4px;}
.alert-info{background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;font-size:.87rem;color:#1d4ed8;margin:16px 0;display:flex;gap:10px;align-items:flex-start;}
</style>
@endpush

@section('content')
<section class="doc-hero">
    <div class="container">
        <h1>API Documentation</h1>
        <p>Integrate Readiwork's credit checking and identity verification services into your application. REST API with JSON responses.</p>
        <div class="doc-hero-badges">
            <span class="doc-hero-badge"><i class="fa-solid fa-circle-check"></i> REST API</span>
            <span class="doc-hero-badge"><i class="fa-solid fa-circle-check"></i> JSON responses</span>
            <span class="doc-hero-badge"><i class="fa-solid fa-circle-check"></i> Sandbox available</span>
            <span class="doc-hero-badge"><i class="fa-solid fa-circle-check"></i> 60 req/min</span>
        </div>
    </div>
</section>

<section style="background:#f8fafc;">
    <div class="container">
        <div class="doc-layout">
            <nav class="doc-sidebar">
                <div class="doc-sidebar-group">
                    <div class="doc-sidebar-title">Getting Started</div>
                    <a href="#authentication">Authentication</a>
                    <a href="#base-url">Base URL</a>
                    <a href="#errors">Error Handling</a>
                    <a href="#rate-limits">Rate Limits</a>
                </div>
                <div class="doc-sidebar-group">
                    <div class="doc-sidebar-title">Endpoints</div>
                    <a href="#identity-verify">Identity Verification</a>
                    <a href="#crb-check">CRB Blacklist Check</a>
                    <a href="#credit-score">Credit Score</a>
                    <a href="#loan-eligibility">Loan Eligibility</a>
                    <a href="#credit-report">Full Credit Report</a>
                </div>
                <div class="doc-sidebar-group">
                    <div class="doc-sidebar-title">Reference</div>
                    <a href="#response-codes">Response Codes</a>
                    <a href="#pricing-ref">Pricing</a>
                </div>
            </nav>

            <div class="doc-body">
                <div class="doc-section" id="authentication">
                    <h2>Authentication</h2>
                    <p>All API requests require an API key passed in the <code>X-API-Key</code> header. Get your API key by <a href="{{ route('contact') }}" style="color:#0FA958;">contacting us</a>.</p>
                    <div class="code-block"><span class="comment"># Include in every request</span>
X-API-Key: rw_live_your_api_key_here
Content-Type: application/json</div>
                    <div class="alert-info"><i class="fa-solid fa-circle-info"></i> <div>Use <code>rw_test_</code> prefixed keys for sandbox testing. Sandbox calls return realistic mock data and are not billed.</div></div>
                </div>

                <div class="doc-section" id="base-url">
                    <h2>Base URL</h2>
                    <p>All API endpoints are relative to the base URL:</p>
                    <div class="code-block"><span class="str">https://readi.work/api/v1</span></div>
                </div>

                <div class="doc-section" id="errors">
                    <h2>Error Handling</h2>
                    <p>Errors return a JSON object with <code>success: false</code> and a descriptive <code>message</code>:</p>
                    <div class="code-block">{
  <span class="key">"success"</span>: <span class="val">false</span>,
  <span class="key">"error"</span>: <span class="str">"invalid_id"</span>,
  <span class="key">"message"</span>: <span class="str">"National ID not found in registry"</span>
}</div>
                </div>

                <div class="doc-section" id="rate-limits">
                    <h2>Rate Limits</h2>
                    <p>Standard accounts: <strong>60 requests per minute</strong>. Exceeding the limit returns HTTP 429. Enterprise accounts have higher limits — <a href="{{ route('contact') }}" style="color:#0FA958;">contact us</a> for custom rates.</p>
                </div>

                <div class="doc-section" id="identity-verify">
                    <h2>Identity Verification</h2>
                    <p>Verify a National ID against the Kenya IPRS registry via Metropol CRB.</p>
                    <div class="endpoint-card">
                        <div class="endpoint-head">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/verify/identity</span>
                            <span class="endpoint-desc">Verify National ID</span>
                        </div>
                        <div class="endpoint-body">
                            <h3>Request Body</h3>
                            <table class="param-table">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td><code>national_id</code></td><td>string</td><td><span class="param-required">required</span></td><td>6–10 digit Kenyan National ID number</td></tr>
                                </tbody>
                            </table>
                            <h3>Example Request</h3>
                            <div class="code-block">curl -X POST https://readi.work/api/v1/verify/identity \
  -H <span class="str">"X-API-Key: rw_live_your_key"</span> \
  -H <span class="str">"Content-Type: application/json"</span> \
  -d '<span class="str">{"national_id": "12345678"}</span>'</div>
                            <h3>Example Response</h3>
                            <div class="code-block">{
  <span class="key">"success"</span>: <span class="val">true</span>,
  <span class="key">"data"</span>: {
    <span class="key">"first_name"</span>: <span class="str">"JANE"</span>,
    <span class="key">"other_name"</span>: <span class="str">"WANJIKU"</span>,
    <span class="key">"last_name"</span>: <span class="str">"KAMAU"</span>,
    <span class="key">"date_of_birth"</span>: <span class="str">"1990-05-15"</span>,
    <span class="key">"gender"</span>: <span class="str">"F"</span>,
    <span class="key">"citizenship"</span>: <span class="str">"Kenyan"</span>,
    <span class="key">"is_valid"</span>: <span class="val">true</span>
  },
  <span class="key">"credits_used"</span>: <span class="val">199</span>
}</div>
                        </div>
                    </div>
                </div>

                <div class="doc-section" id="crb-check">
                    <h2>CRB Blacklist Check</h2>
                    <p>Check if a National ID is listed for unpaid loans at Metropol CRB.</p>
                    <div class="endpoint-card">
                        <div class="endpoint-head">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/crb/blacklist</span>
                            <span class="endpoint-desc">Check CRB listing status</span>
                        </div>
                        <div class="endpoint-body">
                            <h3>Request Body</h3>
                            <table class="param-table">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td><code>national_id</code></td><td>string</td><td><span class="param-required">required</span></td><td>6–10 digit Kenyan National ID number</td></tr>
                                </tbody>
                            </table>
                            <h3>Example Response</h3>
                            <div class="code-block">{
  <span class="key">"success"</span>: <span class="val">true</span>,
  <span class="key">"data"</span>: {
    <span class="key">"is_listed"</span>: <span class="val">false</span>,
    <span class="key">"delinquency_code"</span>: <span class="str">"001"</span>,
    <span class="key">"total_outstanding"</span>: <span class="val">0</span>,
    <span class="key">"npa_accounts"</span>: <span class="val">0</span>
  },
  <span class="key">"credits_used"</span>: <span class="val">249</span>
}</div>
                        </div>
                    </div>
                </div>

                <div class="doc-section" id="credit-score">
                    <h2>Credit Score</h2>
                    <p>Retrieve a borrower's credit score (200–900 scale) with historical comparatives.</p>
                    <div class="endpoint-card">
                        <div class="endpoint-head">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/crb/score</span>
                            <span class="endpoint-desc">Get credit score</span>
                        </div>
                        <div class="endpoint-body">
                            <h3>Example Response</h3>
                            <div class="code-block">{
  <span class="key">"success"</span>: <span class="val">true</span>,
  <span class="key">"data"</span>: {
    <span class="key">"credit_score"</span>: <span class="val">720</span>,
    <span class="key">"score_band"</span>: <span class="str">"Good"</span>,
    <span class="key">"ppi"</span>: <span class="str">"M2"</span>,
    <span class="key">"probability_of_default"</span>: <span class="str">"8.2%"</span>,
    <span class="key">"score_3m"</span>: <span class="val">710</span>,
    <span class="key">"score_6m"</span>: <span class="val">695</span>,
    <span class="key">"score_12m"</span>: <span class="val">680</span>
  },
  <span class="key">"credits_used"</span>: <span class="val">299</span>
}</div>
                        </div>
                    </div>
                </div>

                <div class="doc-section" id="loan-eligibility">
                    <h2>Loan Eligibility</h2>
                    <p>Combined identity verification + CRB blacklist check in one call. Returns a single eligibility verdict.</p>
                    <div class="endpoint-card">
                        <div class="endpoint-head">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/check/eligibility</span>
                            <span class="endpoint-desc">Check loan eligibility</span>
                        </div>
                        <div class="endpoint-body">
                            <h3>Example Response</h3>
                            <div class="code-block">{
  <span class="key">"success"</span>: <span class="val">true</span>,
  <span class="key">"data"</span>: {
    <span class="key">"eligible"</span>: <span class="val">true</span>,
    <span class="key">"verdict"</span>: <span class="str">"ELIGIBLE"</span>,
    <span class="key">"identity_verified"</span>: <span class="val">true</span>,
    <span class="key">"crb_listed"</span>: <span class="val">false</span>,
    <span class="key">"full_name"</span>: <span class="str">"JANE WANJIKU KAMAU"</span>
  },
  <span class="key">"credits_used"</span>: <span class="val">349</span>
}</div>
                        </div>
                    </div>
                </div>

                <div class="doc-section" id="credit-report">
                    <h2>Full Credit Report</h2>
                    <p>Retrieve a comprehensive credit report including all accounts, repayment history, sector data, and institutions.</p>
                    <div class="endpoint-card">
                        <div class="endpoint-head">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/crb/report</span>
                            <span class="endpoint-desc">Full credit report</span>
                        </div>
                        <div class="endpoint-body">
                            <p>Returns identity data, credit score, all loan accounts, sector performance, reporting institutions, and credit events. See <a href="{{ route('pricing') }}" style="color:#0FA958;">pricing page</a> for report variants and costs.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-section" id="response-codes">
                    <h2>HTTP Response Codes</h2>
                    <table class="param-table">
                        <thead><tr><th>Code</th><th>Meaning</th></tr></thead>
                        <tbody>
                            <tr><td><strong>200</strong></td><td>Success — check completed and result returned</td></tr>
                            <tr><td><strong>400</strong></td><td>Bad request — invalid or missing parameters</td></tr>
                            <tr><td><strong>401</strong></td><td>Unauthorized — invalid or missing API key</td></tr>
                            <tr><td><strong>402</strong></td><td>Insufficient credits — top up your balance</td></tr>
                            <tr><td><strong>404</strong></td><td>Not found — National ID not found in registry</td></tr>
                            <tr><td><strong>429</strong></td><td>Too many requests — rate limit exceeded</td></tr>
                            <tr><td><strong>500</strong></td><td>Server error — contact support if this persists</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-section" id="pricing-ref">
                    <h2>Pricing</h2>
                    <p>API calls deduct credits from your balance. See the full <a href="{{ route('pricing') }}" style="color:#0FA958;">pricing page</a> for all rates. For questions or enterprise pricing, <a href="{{ route('contact') }}" style="color:#0FA958;">contact us</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
