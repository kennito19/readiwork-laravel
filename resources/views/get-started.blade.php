@extends('layouts.app')
@section('title', 'Get Started — Readiwork Credit Bureau Services')

@push('head')
<style>
.gs-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:54px 0 48px;position:relative;overflow:hidden;}
.gs-hero::before{content:'';position:absolute;top:-40%;right:-5%;width:55%;height:180%;background:radial-gradient(ellipse,rgba(15,169,88,.08) 0%,transparent 65%);pointer-events:none;}
.gs-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(15,169,88,.15);color:#6ee7a8;border:1px solid rgba(15,169,88,.3);border-radius:999px;padding:5px 14px;font-size:.82rem;font-weight:600;margin-bottom:16px;}
.gs-h1{font-size:2.4rem;font-weight:800;color:#fff;margin-bottom:12px;line-height:1.15;}
.gs-sub{color:rgba(255,255,255,.65);font-size:1rem;max-width:560px;margin-bottom:28px;line-height:1.6;}
.gs-trust-row{display:flex;flex-wrap:wrap;gap:18px;}
.gs-trust-item{display:flex;align-items:center;gap:7px;color:rgba(255,255,255,.55);font-size:.82rem;}
.gs-trust-item i{color:#0FA958;font-size:.85rem;}
.gs-main{padding:48px 0 80px;background:#f8fafc;}
.gs-layout{display:grid;grid-template-columns:1fr 420px;gap:36px;align-items:start;}
@media(max-width:900px){.gs-layout{grid-template-columns:1fr;}}
.service-picker{margin-bottom:24px;}
.service-picker-label{font-size:.82rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;}
.service-cards{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
@media(max-width:600px){.service-cards{grid-template-columns:1fr;}}
.svc-card{border:2px solid #e2e8f0;border-radius:12px;padding:14px 14px 12px;cursor:pointer;transition:all .18s;background:#fff;display:flex;align-items:flex-start;gap:12px;}
.svc-card:hover{border-color:#0FA958;box-shadow:0 4px 16px rgba(15,169,88,.1);}
.svc-card.active{border-color:var(--svc-color,#0FA958);background:color-mix(in srgb,var(--svc-color,#0FA958) 6%,#fff);box-shadow:0 4px 20px rgba(15,169,88,.15);}
.svc-card-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#fff;flex-shrink:0;}
.svc-card-body{flex:1;min-width:0;}
.svc-card-name{font-size:.84rem;font-weight:700;color:#1e293b;margin-bottom:2px;}
.svc-card-price{font-size:.75rem;font-weight:600;color:#64748b;}
.svc-card-check{width:18px;height:18px;border-radius:50%;background:#0FA958;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.6rem;flex-shrink:0;opacity:0;transition:opacity .15s;margin-top:2px;}
.svc-card.active .svc-card-check{opacity:1;}
.form-card-gs{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px 28px 24px;box-shadow:0 4px 24px rgba(11,31,59,.07);}
.form-title{font-size:1.15rem;font-weight:800;color:#0B1F3B;margin-bottom:6px;}
.form-sub{font-size:.85rem;color:#64748b;margin-bottom:22px;line-height:1.5;}
.selected-svc-banner{display:flex;align-items:center;gap:10px;border-radius:10px;padding:12px 14px;margin-bottom:18px;background:#f0fdf4;border:1px solid #bbf7d0;transition:all .2s;}
.selected-svc-banner-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.85rem;color:#fff;flex-shrink:0;}
.selected-svc-banner-info{flex:1;}
.selected-svc-banner-name{font-size:.85rem;font-weight:700;color:#1e293b;}
.selected-svc-banner-price{font-size:.78rem;color:#64748b;}
.id-wrap{position:relative;margin-bottom:16px;}
.id-wrap label{display:block;font-size:.82rem;font-weight:600;color:#374151;margin-bottom:6px;}
.id-wrap .id-icon{position:absolute;left:13px;bottom:13px;color:#9ca3af;font-size:.9rem;}
.id-input{width:100%;padding:12px 12px 12px 38px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:1rem;font-family:inherit;color:#1e293b;outline:none;transition:border-color .15s;box-sizing:border-box;}
.id-input:focus{border-color:#0FA958;box-shadow:0 0 0 3px rgba(15,169,88,.1);}
.id-hint{font-size:.75rem;color:#94a3b8;margin-top:5px;}
.consent-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;margin-bottom:18px;display:flex;gap:10px;align-items:flex-start;}
.consent-box input[type=checkbox]{margin-top:2px;accent-color:#0FA958;flex-shrink:0;}
.consent-box label{font-size:.78rem;color:#64748b;line-height:1.5;cursor:pointer;}
.consent-box a{color:#0FA958;}
.gs-submit{width:100%;padding:14px;background:#0FA958;color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;transition:background .15s;}
.gs-submit:hover{background:#0d8f49;}
.gs-submit:disabled{opacity:.6;cursor:not-allowed;}
.price-badge-gs{display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;}
.price-badge-gs-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#0FA958,#16a34a);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.price-badge-gs-text{font-size:.78rem;color:#64748b;line-height:1.5;}
.price-badge-gs-text strong{color:#1e293b;}
.info-panel-gs{display:grid;gap:16px;}
.info-card-gs{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(11,31,59,.05);}
.info-card-gs-title{font-size:.9rem;font-weight:700;color:#0B1F3B;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.info-card-gs-title .ic-icon{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.8rem;color:#fff;}
.perks-list{display:grid;gap:8px;}
.perk-item{display:flex;align-items:center;gap:9px;font-size:.83rem;color:#374151;}
.perk-item i{color:#0FA958;font-size:.8rem;flex-shrink:0;}
.steps-list{display:grid;gap:12px;}
.step-item{display:flex;gap:12px;align-items:flex-start;}
.step-num{width:26px;height:26px;border-radius:50%;background:#0FA958;color:#fff;font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.step-body{font-size:.82rem;color:#374151;line-height:1.5;}
.step-body strong{color:#1e293b;display:block;font-size:.84rem;}
.security-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
@media(max-width:480px){.security-grid{grid-template-columns:1fr;}}
.security-item{display:flex;gap:9px;align-items:flex-start;}
.security-item i{color:#0FA958;font-size:.85rem;margin-top:2px;flex-shrink:0;}
.security-item span{font-size:.78rem;color:#64748b;line-height:1.4;}
.all-svcs-list{display:grid;gap:8px;}
.all-svc-row{display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;cursor:pointer;transition:background .12s;}
.all-svc-row:hover{background:#f8fafc;}
.all-svc-row.active-svc{background:#f0fdf4;}
.all-svc-left{display:flex;align-items:center;gap:9px;}
.all-svc-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.all-svc-name{font-size:.83rem;font-weight:600;color:#1e293b;}
.all-svc-price{font-size:.78rem;font-weight:700;color:#0FA958;}
</style>
@endpush

@section('content')
<section class="gs-hero">
    <div class="container">
        <div class="gs-eyebrow"><i class="fa-solid fa-bolt"></i> Instant CRB &amp; Identity Checks</div>
        <h1 class="gs-h1">Get Your Credit Report<br>in Under 2 Minutes</h1>
        <p class="gs-sub">Choose a service, enter your National ID, and pay securely via M-Pesa. Results delivered instantly — no paperwork, no queues.</p>
        <div class="gs-trust-row">
            <div class="gs-trust-item"><i class="fa-solid fa-shield-halved"></i> Metropol CRB Certified</div>
            <div class="gs-trust-item"><i class="fa-solid fa-lock"></i> 256-bit SSL Encrypted</div>
            <div class="gs-trust-item"><i class="fa-solid fa-bolt"></i> Results in &lt;60 seconds</div>
            <div class="gs-trust-item"><i class="fa-solid fa-mobile-screen"></i> Pay via M-Pesa</div>
        </div>
    </div>
</section>

<section class="gs-main">
    <div class="container">
        <div class="gs-layout">
            <div>
                <div class="service-picker">
                    <div class="service-picker-label">Choose a Service</div>
                    <div class="service-cards">
                        <div class="svc-card active" style="--svc-color:#0FA958" onclick="selectService('identity-verification')" id="svc-card-identity-verification">
                            <div class="svc-card-icon" style="background:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
                            <div class="svc-card-body">
                                <div class="svc-card-name">Identity Verification</div>
                                <div class="svc-card-price">KSh {{ number_format($prices['identity-verification'] ?? 199) }}</div>
                            </div>
                            <div class="svc-card-check"><i class="fa-solid fa-check"></i></div>
                        </div>
                        <div class="svc-card" style="--svc-color:#6366f1" onclick="selectService('credit-score-check')" id="svc-card-credit-score-check">
                            <div class="svc-card-icon" style="background:#6366f1;"><i class="fa-solid fa-gauge-high"></i></div>
                            <div class="svc-card-body">
                                <div class="svc-card-name">Credit Score Check</div>
                                <div class="svc-card-price">KSh {{ number_format($prices['credit-score-check'] ?? 299) }}</div>
                            </div>
                            <div class="svc-card-check"><i class="fa-solid fa-check"></i></div>
                        </div>
                        <div class="svc-card" style="--svc-color:#ef4444" onclick="selectService('crb-blacklist-check')" id="svc-card-crb-blacklist-check">
                            <div class="svc-card-icon" style="background:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div class="svc-card-body">
                                <div class="svc-card-name">CRB Blacklist Check</div>
                                <div class="svc-card-price">KSh {{ number_format($prices['crb-blacklist-check'] ?? 249) }}</div>
                            </div>
                            <div class="svc-card-check"><i class="fa-solid fa-check"></i></div>
                        </div>
                        <div class="svc-card" style="--svc-color:#0FA958" onclick="selectService('loan-eligibility')" id="svc-card-loan-eligibility">
                            <div class="svc-card-icon" style="background:#0FA958;"><i class="fa-solid fa-circle-check"></i></div>
                            <div class="svc-card-body">
                                <div class="svc-card-name">Loan Eligibility Check</div>
                                <div class="svc-card-price">KSh {{ number_format($prices['loan-eligibility'] ?? 349) }}</div>
                            </div>
                            <div class="svc-card-check"><i class="fa-solid fa-check"></i></div>
                        </div>
                        <div class="svc-card" style="--svc-color:#0ea5e9" onclick="selectService('full-credit-report')" id="svc-card-full-credit-report">
                            <div class="svc-card-icon" style="background:#0ea5e9;"><i class="fa-solid fa-file-lines"></i></div>
                            <div class="svc-card-body">
                                <div class="svc-card-name">Full Credit Report</div>
                                <div class="svc-card-price">KSh {{ number_format($prices['full-credit-report'] ?? 499) }}</div>
                            </div>
                            <div class="svc-card-check"><i class="fa-solid fa-check"></i></div>
                        </div>
                    </div>
                </div>

                <div class="form-card-gs">
                    <div class="form-title">Enter Your Details</div>
                    <div class="form-sub">Your information is encrypted and used only for this verification.</div>

                    <div class="selected-svc-banner" id="svcBanner">
                        <div class="selected-svc-banner-icon" id="svcBannerIcon" style="background:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
                        <div class="selected-svc-banner-info">
                            <div class="selected-svc-banner-name" id="svcBannerName">Identity Verification</div>
                            <div class="selected-svc-banner-price" id="svcBannerPrice">KSh {{ number_format($prices['identity-verification'] ?? 199) }} · via M-Pesa STK Push</div>
                        </div>
                    </div>

                    <form method="POST" id="gsForm" action="{{ route('identity-verification.confirm') }}">
                        @csrf
                        <div class="id-wrap">
                            <label for="id_number">National ID Number</label>
                            <i class="fa-solid fa-id-card id-icon"></i>
                            <input type="text" id="id_number" name="id_number" class="id-input"
                                placeholder="e.g. 12345678" required
                                pattern="[0-9]{6,10}" title="Enter a valid 6–10 digit Kenyan National ID"
                                autocomplete="off" inputmode="numeric">
                            <div class="id-hint">Your 6–10 digit Kenyan National ID number</div>
                        </div>

                        <div class="consent-box">
                            <input type="checkbox" id="consent" required>
                            <label for="consent">
                                I consent to Readiwork running this check using my National ID in accordance with the
                                <a href="{{ route('terms') }}">Terms of Service</a>.
                                The fee of <strong id="consentPrice">KSh {{ number_format($prices['identity-verification'] ?? 199) }}</strong> will be collected via M-Pesa STK Push.
                            </label>
                        </div>

                        <button type="submit" class="gs-submit" id="gsSubmit">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span id="submitLabel">Continue to Payment</span>
                        </button>
                    </form>

                    <div class="price-badge-gs">
                        <div class="price-badge-gs-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                        <div class="price-badge-gs-text">
                            <strong id="priceBadgeAmt">KSh {{ number_format($prices['identity-verification'] ?? 199) }}</strong> · Paid securely via M-Pesa STK Push · VAT 16% included
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-panel-gs">
                <div class="info-card-gs">
                    <div class="info-card-gs-title">
                        <span class="ic-icon" style="background:#0FA958;"><i class="fa-solid fa-list-check"></i></span>
                        What You Get
                    </div>
                    <div class="perks-list" id="perksList">
                        <div class="perk-item"><i class="fa-solid fa-circle-check"></i> IPRS verified name &amp; photo</div>
                        <div class="perk-item"><i class="fa-solid fa-circle-check"></i> Date of birth &amp; gender</div>
                        <div class="perk-item"><i class="fa-solid fa-circle-check"></i> Instant digital certificate</div>
                    </div>
                </div>

                <div class="info-card-gs">
                    <div class="info-card-gs-title">
                        <span class="ic-icon" style="background:#6366f1;"><i class="fa-solid fa-diagram-project"></i></span>
                        How It Works
                    </div>
                    <div class="steps-list">
                        <div class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-body"><strong>Choose &amp; Enter ID</strong>Pick a service and enter your National ID number.</div>
                        </div>
                        <div class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-body"><strong>Pay via M-Pesa</strong>You'll receive an STK Push on your phone to approve payment.</div>
                        </div>
                        <div class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-body"><strong>Instant Results</strong>Your report is displayed in seconds — download as PDF anytime.</div>
                        </div>
                    </div>
                </div>

                <div class="info-card-gs">
                    <div class="info-card-gs-title">
                        <span class="ic-icon" style="background:#0ea5e9;"><i class="fa-solid fa-grid-2"></i></span>
                        All Services &amp; Prices
                    </div>
                    <div class="all-svcs-list">
                        <div class="all-svc-row active-svc" onclick="selectService('identity-verification')" id="all-svc-identity-verification">
                            <div class="all-svc-left"><div class="all-svc-dot" style="background:#0FA958;"></div><div class="all-svc-name">Identity Verification</div></div>
                            <div class="all-svc-price">KSh {{ number_format($prices['identity-verification'] ?? 199) }}</div>
                        </div>
                        <div class="all-svc-row" onclick="selectService('credit-score-check')" id="all-svc-credit-score-check">
                            <div class="all-svc-left"><div class="all-svc-dot" style="background:#6366f1;"></div><div class="all-svc-name">Credit Score Check</div></div>
                            <div class="all-svc-price">KSh {{ number_format($prices['credit-score-check'] ?? 299) }}</div>
                        </div>
                        <div class="all-svc-row" onclick="selectService('crb-blacklist-check')" id="all-svc-crb-blacklist-check">
                            <div class="all-svc-left"><div class="all-svc-dot" style="background:#ef4444;"></div><div class="all-svc-name">CRB Blacklist Check</div></div>
                            <div class="all-svc-price">KSh {{ number_format($prices['crb-blacklist-check'] ?? 249) }}</div>
                        </div>
                        <div class="all-svc-row" onclick="selectService('loan-eligibility')" id="all-svc-loan-eligibility">
                            <div class="all-svc-left"><div class="all-svc-dot" style="background:#0FA958;"></div><div class="all-svc-name">Loan Eligibility Check</div></div>
                            <div class="all-svc-price">KSh {{ number_format($prices['loan-eligibility'] ?? 349) }}</div>
                        </div>
                        <div class="all-svc-row" onclick="selectService('full-credit-report')" id="all-svc-full-credit-report">
                            <div class="all-svc-left"><div class="all-svc-dot" style="background:#0ea5e9;"></div><div class="all-svc-name">Full Credit Report</div></div>
                            <div class="all-svc-price">KSh {{ number_format($prices['full-credit-report'] ?? 499) }}</div>
                        </div>
                    </div>
                </div>

                <div class="info-card-gs">
                    <div class="info-card-gs-title">
                        <span class="ic-icon" style="background:#d97706;"><i class="fa-solid fa-shield-halved"></i></span>
                        Security &amp; Privacy
                    </div>
                    <div class="security-grid">
                        <div class="security-item"><i class="fa-solid fa-lock"></i><span>256-bit SSL encryption on all data</span></div>
                        <div class="security-item"><i class="fa-solid fa-user-shield"></i><span>ID used only for this check</span></div>
                        <div class="security-item"><i class="fa-solid fa-database"></i><span>No data stored without consent</span></div>
                        <div class="security-item"><i class="fa-solid fa-certificate"></i><span>Metropol CRB licensed partner</span></div>
                        <div class="security-item"><i class="fa-solid fa-mobile-screen-button"></i><span>Secure M-Pesa STK Push payment</span></div>
                        <div class="security-item"><i class="fa-solid fa-rotate-left"></i><span>Refund policy on failed checks</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const SERVICES = {
    'identity-verification':  { label: 'Identity Verification',  icon: 'fa-id-card',            color: '#0FA958', route: '{{ route('identity-verification.confirm') }}',  perks: ['IPRS verified name & photo', 'Date of birth & gender', 'Instant digital certificate'], price: {{ $prices['identity-verification'] ?? 199 }} },
    'credit-score-check':     { label: 'Credit Score Check',     icon: 'fa-gauge-high',          color: '#6366f1', route: '{{ route('credit-score-check.confirm') }}',      perks: ['Credit score out of 900', 'Delinquency & default status', 'Full account history'], price: {{ $prices['credit-score-check'] ?? 299 }} },
    'crb-blacklist-check':    { label: 'CRB Blacklist Check',    icon: 'fa-triangle-exclamation', color: '#ef4444', route: '{{ route('crb-blacklist-check.confirm') }}',     perks: ['CRB listing status', 'List of defaulted loans', 'Lender breakdown'], price: {{ $prices['crb-blacklist-check'] ?? 249 }} },
    'loan-eligibility':       { label: 'Loan Eligibility Check', icon: 'fa-circle-check',        color: '#0FA958', route: '{{ route('loan-eligibility.confirm') }}',         perks: ['Pre-qualified loan amount', 'Eligibility verdict', 'Recommended lenders'], price: {{ $prices['loan-eligibility'] ?? 349 }} },
    'full-credit-report':     { label: 'Full Credit Report',     icon: 'fa-file-lines',          color: '#0ea5e9', route: '{{ route('full-credit-report.confirm') }}',        perks: ['Complete account history', 'Score + delinquency data', 'Downloadable PDF report'], price: {{ $prices['full-credit-report'] ?? 499 }} },
};

let currentService = 'identity-verification';

function selectService(slug) {
    currentService = slug;
    const svc = SERVICES[slug];
    const fmt = 'KSh ' + svc.price.toLocaleString();

    document.querySelectorAll('.svc-card').forEach(el => el.classList.remove('active'));
    const card = document.getElementById('svc-card-' + slug);
    if (card) { card.classList.add('active'); card.style.setProperty('--svc-color', svc.color); }

    document.querySelectorAll('.all-svc-row').forEach(el => el.classList.remove('active-svc'));
    const row = document.getElementById('all-svc-' + slug);
    if (row) row.classList.add('active-svc');

    const bannerIcon = document.getElementById('svcBannerIcon');
    bannerIcon.innerHTML = '<i class="fa-solid ' + svc.icon + '"></i>';
    bannerIcon.style.background = svc.color;
    document.getElementById('svcBannerName').textContent  = svc.label;
    document.getElementById('svcBannerPrice').textContent = fmt + ' · via M-Pesa STK Push';

    const banner = document.getElementById('svcBanner');
    banner.style.background  = svc.color + '0f';
    banner.style.borderColor = svc.color + '44';

    document.getElementById('consentPrice').textContent  = fmt;
    document.getElementById('priceBadgeAmt').textContent = fmt;
    document.getElementById('gsForm').action = svc.route;

    document.getElementById('perksList').innerHTML = svc.perks.map(p =>
        '<div class="perk-item"><i class="fa-solid fa-circle-check"></i> ' + p + '</div>'
    ).join('');
}
</script>
@endpush
