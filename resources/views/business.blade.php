@extends('layouts.app')
@section('title', 'Business & API - Readiwork')

@push('head')
<style>
.biz-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:80px 0 64px;text-align:center;position:relative;overflow:hidden;}
.biz-hero::before{content:'';position:absolute;top:-20%;right:-5%;width:50%;height:180%;background:radial-gradient(ellipse,rgba(15,169,88,.08) 0%,transparent 65%);pointer-events:none;}
.biz-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(15,169,88,.15);color:#6ee7a8;border:1px solid rgba(15,169,88,.3);border-radius:999px;padding:5px 16px;font-size:.82rem;font-weight:600;margin-bottom:20px;}
.biz-hero h1{font-size:clamp(2rem,4.5vw,3rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:18px;letter-spacing:-.5px;}
.biz-hero h1 span{color:#0FA958;}
.biz-hero p{font-size:1.05rem;color:rgba(255,255,255,.62);max-width:580px;margin:0 auto 32px;line-height:1.7;}
.biz-hero-btns{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;}
.biz-btn-primary{background:#0FA958;color:#fff;padding:13px 28px;border-radius:10px;font-weight:700;font-size:.95rem;display:inline-flex;align-items:center;gap:9px;transition:background .15s;}
.biz-btn-primary:hover{background:#0d9048;color:#fff;}
.biz-btn-outline{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.2);color:#fff;padding:13px 28px;border-radius:10px;font-weight:700;font-size:.95rem;display:inline-flex;align-items:center;gap:9px;transition:background .15s;}
.biz-btn-outline:hover{background:rgba(255,255,255,.14);color:#fff;}
.biz-trust-bar{background:#0B1F3B;padding:32px 0;}
.biz-trust-inner{display:grid;grid-template-columns:repeat(4,1fr);}
.biz-trust-item{text-align:center;padding:0 24px;border-right:1px solid rgba(255,255,255,.1);}
.biz-trust-item:last-child{border-right:0;}
.biz-trust-item h4{font-size:1.9rem;font-weight:800;color:#fff;margin-bottom:4px;}
.biz-trust-item h4 span{color:#0FA958;}
.biz-trust-item p{font-size:.84rem;color:rgba(255,255,255,.5);}
.biz-section{padding:64px 0;}
.biz-section--light{background:#f8fafc;}
.biz-audience-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:36px;}
.biz-audience-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px 22px;text-align:center;transition:transform .2s,box-shadow .2s,border-color .2s;}
.biz-audience-card:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(11,31,59,.08);border-color:#c0e8d5;}
.biz-audience-icon{width:52px;height:52px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:1.2rem;margin:0 auto 14px;}
.biz-audience-card h3{font-size:1rem;font-weight:700;color:#0B1F3B;margin-bottom:8px;}
.biz-audience-card p{font-size:.84rem;color:#64748b;line-height:1.6;}
.biz-features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:36px;}
.biz-feature-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px 24px;transition:box-shadow .15s;}
.biz-feature-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.07);}
.biz-feature-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:16px;}
.biz-feature-card h3{font-size:1rem;font-weight:700;color:#0B1F3B;margin-bottom:8px;}
.biz-feature-card p{font-size:.84rem;color:#64748b;line-height:1.6;}
.biz-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin-top:36px;}
.biz-step{padding:0 28px;border-right:1px solid #e2e8f0;display:flex;flex-direction:column;gap:12px;}
.biz-step:first-child{padding-left:0;}
.biz-step:last-child{border-right:0;padding-right:0;}
.biz-step-num{width:38px;height:38px;background:#0B1F3B;color:#fff;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;}
.biz-step h3{font-size:1rem;font-weight:700;color:#0B1F3B;}
.biz-step p{font-size:.87rem;color:#64748b;line-height:1.6;}
.biz-pricing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:36px;}
.biz-pricing-card{background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:30px 26px 26px;display:flex;flex-direction:column;position:relative;transition:transform .2s,box-shadow .2s;}
.biz-pricing-card:hover{transform:translateY(-4px);box-shadow:0 18px 40px rgba(11,31,59,.1);}
.biz-pricing-card--featured{border-color:#0FA958;box-shadow:0 0 0 2px rgba(15,169,88,.2);}
.biz-pricing-badge{position:absolute;top:-13px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#0FA958,#0a8a46);color:#fff;font-size:.75rem;font-weight:700;padding:4px 16px;border-radius:999px;white-space:nowrap;}
.biz-plan-name{font-size:.85rem;font-weight:700;color:#0FA958;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;}
.biz-plan-price{font-size:2.2rem;font-weight:800;color:#0B1F3B;margin-bottom:4px;line-height:1;}
.biz-plan-price sup{font-size:1rem;font-weight:600;vertical-align:super;}
.biz-plan-period{font-size:.84rem;color:#64748b;margin-bottom:18px;}
.biz-plan-desc{font-size:.88rem;color:#64748b;line-height:1.6;margin-bottom:22px;}
.biz-plan-divider{border:0;border-top:1px solid #e2e8f0;margin:0 0 18px;}
.biz-plan-features{display:flex;flex-direction:column;gap:11px;margin-bottom:26px;flex:1;}
.biz-plan-features li{display:flex;align-items:flex-start;gap:10px;font-size:.87rem;color:#1e293b;}
.biz-plan-features li i{color:#0FA958;font-size:.78rem;flex-shrink:0;margin-top:3px;}
.biz-plan-cta{display:block;text-align:center;padding:13px 20px;border-radius:10px;font-weight:700;font-size:.95rem;transition:background .2s,transform .15s;margin-top:auto;}
.biz-plan-cta--solid{background:#0FA958;color:#fff;}
.biz-plan-cta--solid:hover{background:#0d9048;transform:translateY(-1px);color:#fff;}
.biz-plan-cta--outline{background:transparent;color:#0B1F3B;border:1.5px solid #e2e8f0;}
.biz-plan-cta--outline:hover{border-color:#0FA958;color:#0FA958;}
.biz-faq{display:flex;flex-direction:column;gap:12px;margin-top:36px;}
.biz-faq-item{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px 22px;}
.biz-faq-q{font-size:.97rem;font-weight:700;color:#0B1F3B;margin-bottom:10px;display:flex;align-items:flex-start;gap:12px;}
.biz-faq-q i{color:#0FA958;font-size:.85rem;margin-top:3px;flex-shrink:0;}
.biz-faq-a{font-size:.89rem;color:#64748b;line-height:1.7;padding-left:24px;}
@media(max-width:1100px){.biz-audience-grid{grid-template-columns:repeat(2,1fr);}.biz-features-grid{grid-template-columns:repeat(2,1fr);}.biz-pricing-grid{grid-template-columns:1fr;max-width:420px;margin-left:auto;margin-right:auto;}.biz-steps{grid-template-columns:repeat(2,1fr);gap:28px;}.biz-step{border-right:0;padding:0;border-bottom:1px solid #e2e8f0;padding-bottom:24px;}.biz-step:last-child,.biz-step:nth-child(2){border-bottom:0;}.biz-trust-inner{grid-template-columns:repeat(2,1fr);gap:24px;}.biz-trust-item{border-right:0;}}
@media(max-width:720px){.biz-features-grid{grid-template-columns:1fr;}.biz-audience-grid{grid-template-columns:1fr 1fr;}.biz-steps{grid-template-columns:1fr;}.biz-step{border-bottom:1px solid #e2e8f0;padding-bottom:22px;}.biz-step:last-child{border-bottom:0;}}
@media(max-width:480px){.biz-audience-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="biz-hero">
    <div class="container" style="position:relative;z-index:1;">
        <div class="biz-eyebrow"><i class="fa-solid fa-building-columns"></i> For Businesses &amp; Lenders</div>
        <h1>Credit Checks at Scale.<br><span>One Platform. Zero Friction.</span></h1>
        <p>Give your team instant access to verified credit data for every borrower. Reduce loan defaults, speed up approvals, and make decisions with confidence.</p>
        <div class="biz-hero-btns">
            <a href="{{ route('get-started') }}" class="biz-btn-primary"><i class="fa-solid fa-key"></i> Get Started</a>
            <a href="{{ route('contact') }}" class="biz-btn-outline"><i class="fa-solid fa-envelope"></i> Talk to Sales</a>
        </div>
    </div>
</section>

{{-- TRUST BAR --}}
<div class="biz-trust-bar">
    <div class="container">
        <div class="biz-trust-inner">
            <div class="biz-trust-item"><h4>5<span>+</span></h4><p>Credit check services</p></div>
            <div class="biz-trust-item"><h4>&lt;<span>1s</span></h4><p>Average response time</p></div>
            <div class="biz-trust-item"><h4>99.9<span>%</span></h4><p>Data accuracy</p></div>
            <div class="biz-trust-item"><h4>0</h4><p>Monthly fees — pay per check</p></div>
        </div>
    </div>
</div>

{{-- WHO IS THIS FOR --}}
<section class="biz-section biz-section--light">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <p class="section-eyebrow">Who It's For</p>
            <h2 class="section-title">Built for Kenyan Financial Institutions</h2>
            <p class="section-subtitle">Whether you process 10 or 10,000 loan applications a month, Readiwork fits your workflow.</p>
        </div>
        <div class="biz-audience-grid">
            <div class="biz-audience-card">
                <div class="biz-audience-icon" style="background:rgba(15,169,88,.12);color:#0FA958;"><i class="fa-solid fa-building-columns"></i></div>
                <h3>Banks &amp; MFIs</h3>
                <p>Automate KYC and credit decisioning at scale directly inside your loan origination workflow.</p>
            </div>
            <div class="biz-audience-card">
                <div class="biz-audience-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-people-group"></i></div>
                <h3>SACCOs &amp; Chamas</h3>
                <p>Run member credit checks before approving loans. Reduce defaults with data-driven decisions.</p>
            </div>
            <div class="biz-audience-card">
                <div class="biz-audience-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;"><i class="fa-solid fa-mobile-screen"></i></div>
                <h3>Fintechs &amp; Digital Lenders</h3>
                <p>Embed instant credit checks into your platform and make real-time loan decisions at any hour.</p>
            </div>
            <div class="biz-audience-card">
                <div class="biz-audience-icon" style="background:rgba(239,68,68,.08);color:#ef4444;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <h3>Microfinance Lenders</h3>
                <p>Verify identity and check default history for every new borrower before disbursement.</p>
            </div>
        </div>
    </div>
</section>

{{-- WHAT YOU GET --}}
<section class="biz-section">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <p class="section-eyebrow">What You Get</p>
            <h2 class="section-title">Everything Your Team Needs</h2>
            <p class="section-subtitle">A complete set of credit data tools — ready to use immediately, no technical setup required.</p>
        </div>
        <div class="biz-features-grid">
            <div class="biz-feature-card">
                <div class="biz-feature-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
                <h3>Identity Verification</h3>
                <p>Confirm a borrower's full name, ID number, and date of birth against Kenya's national registry in real time. Eliminate fraud at the point of application.</p>
            </div>
            <div class="biz-feature-card">
                <div class="biz-feature-icon" style="background:rgba(234,88,12,.1);color:#ea580c;"><i class="fa-solid fa-ban"></i></div>
                <h3>CRB Blacklist Check</h3>
                <p>Instantly find out if a borrower has been listed for unpaid loans. See the outstanding amount and which institution listed them — before you disburse.</p>
            </div>
            <div class="biz-feature-card">
                <div class="biz-feature-icon" style="background:rgba(20,184,166,.1);color:#0d9488;"><i class="fa-solid fa-chart-line"></i></div>
                <h3>Credit Score</h3>
                <p>Get a borrower's AI powered credit score on a 200 to 900 scale, with their risk tier and a 12 month repayment trend, all sourced from Metropol CRB.</p>
            </div>
            <div class="biz-feature-card">
                <div class="biz-feature-icon" style="background:rgba(124,58,237,.1);color:#7c3aed;"><i class="fa-solid fa-scale-balanced"></i></div>
                <h3>Loan Eligibility Decision</h3>
                <p>Combine identity verification and blacklist check into a single request. Get a clear Eligible or Not Eligible answer for every applicant, instantly.</p>
            </div>
            <div class="biz-feature-card">
                <div class="biz-feature-icon" style="background:rgba(37,99,235,.1);color:#2563eb;"><i class="fa-solid fa-file-lines"></i></div>
                <h3>Full Credit Report</h3>
                <p>Access a borrower's complete credit history — all loan accounts, guarantor records, sector performance, and outstanding obligations — in one detailed report.</p>
            </div>
            <div class="biz-feature-card">
                <div class="biz-feature-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-plug"></i></div>
                <h3>API Integration</h3>
                <p>All services are available via REST API with JSON responses. Plug directly into your existing loan management system or mobile app with a single key.</p>
            </div>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="biz-section biz-section--light">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <p class="section-eyebrow">Getting Started</p>
            <h2 class="section-title">Go Live in Four Steps</h2>
            <p class="section-subtitle">From sign-up to your first live credit check — in under a day.</p>
        </div>
        <div class="biz-steps">
            <div class="biz-step">
                <div class="biz-step-num">01</div>
                <h3>Create Your Account</h3>
                <p>Sign up in under two minutes. No setup fee, no contract required. You get your API key immediately after registration.</p>
            </div>
            <div class="biz-step">
                <div class="biz-step-num">02</div>
                <h3>Top Up Your Wallet</h3>
                <p>Add credits via M-Pesa, bank transfer, or card. Your credits never expire. Each check deducts the listed price automatically.</p>
            </div>
            <div class="biz-step">
                <div class="biz-step-num">03</div>
                <h3>Test Before Going Live</h3>
                <p>Use our sandbox environment with test ID numbers to validate your integration. No charges apply in sandbox mode.</p>
            </div>
            <div class="biz-step">
                <div class="biz-step-num">04</div>
                <h3>Go Live</h3>
                <p>Switch to production and start running real checks instantly. Our team is available if you need support at launch.</p>
            </div>
        </div>
    </div>
</section>

{{-- PRICING --}}
<section class="biz-section">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <p class="section-eyebrow">Business Pricing</p>
            <h2 class="section-title">Simple, Usage Based Pricing</h2>
            <p class="section-subtitle">No monthly fees. No contracts. Pay only for the checks you run. Volume discounts applied automatically.</p>
        </div>
        <div class="biz-pricing-grid" style="max-width:960px;margin-left:auto;margin-right:auto;">

            <div class="biz-pricing-card">
                <div class="biz-plan-name">Starter</div>
                <div class="biz-plan-price"><sup>KSh</sup> 199</div>
                <div class="biz-plan-period">per check &mdash; standard rate</div>
                <p class="biz-plan-desc">Best for SACCOs, chamas, and businesses running fewer than 500 checks per month.</p>
                <hr class="biz-plan-divider">
                <ul class="biz-plan-features">
                    <li><i class="fa-solid fa-circle-check"></i> All 5 services available</li>
                    <li><i class="fa-solid fa-circle-check"></i> Pay-as-you-go wallet</li>
                    <li><i class="fa-solid fa-circle-check"></i> Sandbox testing included</li>
                    <li><i class="fa-solid fa-circle-check"></i> Email support</li>
                </ul>
                <a href="{{ route('get-started') }}" class="biz-plan-cta biz-plan-cta--outline">Get Started</a>
            </div>

            <div class="biz-pricing-card biz-pricing-card--featured">
                <span class="biz-pricing-badge">Most Popular</span>
                <div class="biz-plan-name">Growth</div>
                <div class="biz-plan-price"><sup>KSh</sup> 149</div>
                <div class="biz-plan-period">per check &mdash; from 500 checks/month</div>
                <p class="biz-plan-desc">Ideal for digital lenders, microfinance institutions, and fintechs with growing volumes.</p>
                <hr class="biz-plan-divider">
                <ul class="biz-plan-features">
                    <li><i class="fa-solid fa-circle-check"></i> 25% volume discount applied</li>
                    <li><i class="fa-solid fa-circle-check"></i> All 5 services</li>
                    <li><i class="fa-solid fa-circle-check"></i> Usage dashboard &amp; audit logs</li>
                    <li><i class="fa-solid fa-circle-check"></i> Priority email + chat support</li>
                    <li><i class="fa-solid fa-circle-check"></i> Webhook notifications</li>
                </ul>
                <a href="{{ route('get-started') }}" class="biz-plan-cta biz-plan-cta--solid">Get Started</a>
            </div>

            <div class="biz-pricing-card">
                <div class="biz-plan-name">Enterprise</div>
                <div class="biz-plan-price" style="font-size:1.7rem;">Custom</div>
                <div class="biz-plan-period">negotiated rate &mdash; high volume</div>
                <p class="biz-plan-desc">For banks and large institutions running thousands of checks per day with SLA requirements.</p>
                <hr class="biz-plan-divider">
                <ul class="biz-plan-features">
                    <li><i class="fa-solid fa-circle-check"></i> Custom per-check pricing</li>
                    <li><i class="fa-solid fa-circle-check"></i> Dedicated account manager</li>
                    <li><i class="fa-solid fa-circle-check"></i> 99.9% uptime SLA</li>
                    <li><i class="fa-solid fa-circle-check"></i> Bulk batch submission</li>
                    <li><i class="fa-solid fa-circle-check"></i> On-site integration support</li>
                </ul>
                <a href="mailto:hello@readiwork.co.ke" class="biz-plan-cta biz-plan-cta--outline">Contact Sales</a>
            </div>

        </div>
        <p style="text-align:center;font-size:.84rem;color:#94a3b8;margin-top:22px;">All prices exclude 16% VAT. <a href="{{ route('pricing') }}" style="color:#0FA958;font-weight:600;">View full pricing table</a></p>
    </div>
</section>

{{-- FAQ --}}
<section class="biz-section biz-section--light">
    <div class="container" style="max-width:860px;">
        <div class="section-header" style="text-align:center;">
            <p class="section-eyebrow">Common Questions</p>
            <h2 class="section-title">Business FAQ</h2>
        </div>
        <div class="biz-faq">
            <div class="biz-faq-item">
                <div class="biz-faq-q"><i class="fa-solid fa-circle-question"></i> Do I need a technical team to get started?</div>
                <p class="biz-faq-a">No. Any team member can run checks directly from the Readiwork dashboard — no coding needed. For businesses that want to automate checks inside their own system, we provide a simple REST API with clear documentation.</p>
            </div>
            <div class="biz-faq-item">
                <div class="biz-faq-q"><i class="fa-solid fa-circle-question"></i> Is there a minimum commitment or monthly fee?</div>
                <p class="biz-faq-a">None at all. You top up your wallet and pay only for the checks you run. Credits never expire, and there is no subscription or retainer. You can start with as little as KSh 500.</p>
            </div>
            <div class="biz-faq-item">
                <div class="biz-faq-q"><i class="fa-solid fa-circle-question"></i> Is end-user consent required for business checks?</div>
                <p class="biz-faq-a">Yes. Kenyan data law requires that you obtain explicit written or digital consent from the individual before running a credit check on them. You must record and store this consent reference, which is submitted with each request for audit purposes.</p>
            </div>
            <div class="biz-faq-item">
                <div class="biz-faq-q"><i class="fa-solid fa-circle-question"></i> What happens if my wallet runs out mid-operation?</div>
                <p class="biz-faq-a">Any check that cannot be covered by your remaining balance is declined and not processed — no partial charges. You can enable auto top-up in the dashboard to avoid interruptions during busy periods.</p>
            </div>
            <div class="biz-faq-item">
                <div class="biz-faq-q"><i class="fa-solid fa-circle-question"></i> How is data security handled?</div>
                <p class="biz-faq-a">All data is encrypted in transit (TLS 1.3) and at rest. We operate under Kenya's Data Protection Act 2019 and do not sell or share individual borrower data with any third party. See our <a href="{{ route('privacy') }}" style="color:#0FA958;">Privacy Policy</a> for full details.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="cta-glow"></div>
    <div class="container cta-content">
        <span class="cta-badge">Start Today</span>
        <h2>Ready to reduce loan defaults?</h2>
        <p>Create a free account and run your first credit check in minutes. No monthly fee &mdash; pay only for what you use.</p>
        <div class="cta-btns">
            <a href="{{ route('get-started') }}" class="cta-primary-btn">Get Started</a>
            <a href="mailto:hello@readiwork.co.ke" class="cta-outline-btn">Talk to Sales</a>
        </div>
    </div>
</section>

@endsection
