@extends('layouts.app')
@section('title', 'Services - Readiwork')

@push('head')
<style>
.svcs-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:64px 0 56px;position:relative;overflow:hidden;}
.svcs-hero::before{content:'';position:absolute;top:-30%;right:-8%;width:52%;height:200%;background:radial-gradient(ellipse,rgba(15,169,88,0.09) 0%,transparent 65%);pointer-events:none;}
.svcs-hero::after{content:'';position:absolute;bottom:-20%;left:-5%;width:38%;height:160%;background:radial-gradient(ellipse,rgba(99,102,241,0.07) 0%,transparent 65%);pointer-events:none;}
.svcs-hero-inner{position:relative;z-index:1;}
.svcs-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(15,169,88,0.15);color:#6ee7a8;border:1px solid rgba(15,169,88,0.3);border-radius:999px;padding:5px 16px;font-size:.82rem;font-weight:600;margin-bottom:20px;letter-spacing:.3px;}
.svcs-hero-h1{font-size:2.8rem;font-weight:800;color:#fff;margin-bottom:14px;line-height:1.13;letter-spacing:-.5px;}
.svcs-hero-h1 span{color:#0FA958;}
.svcs-hero-sub{color:rgba(255,255,255,.62);font-size:1.05rem;max-width:560px;line-height:1.65;margin-bottom:28px;}
.svcs-hero-trust{display:flex;flex-wrap:wrap;gap:22px;}
.svcs-hero-trust span{color:rgba(255,255,255,.5);font-size:.87rem;display:flex;align-items:center;gap:7px;}
.svcs-hero-trust i{color:#0FA958;font-size:.9rem;}
.svcs-body{background:var(--bg-light);padding:56px 0 72px;}
.svcs-intro{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:52px;}
.svcs-stat{background:#fff;border:1px solid var(--border-color);border-radius:14px;padding:20px 22px;display:flex;align-items:center;gap:14px;}
.svcs-stat-icon{width:44px;height:44px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.svcs-stat h4{font-size:1.45rem;font-weight:800;color:var(--primary-navy);line-height:1;margin-bottom:3px;}
.svcs-stat p{font-size:.8rem;color:var(--text-regular);}
.svcs-grid-label{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.svcs-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.svcs-card--wide{grid-column:span 2;}
.svcs-card{background:#fff;border:1px solid var(--border-color);border-radius:18px;padding:28px 28px 24px;display:flex;flex-direction:column;gap:0;transition:transform .2s,box-shadow .2s,border-color .2s;position:relative;overflow:hidden;}
.svcs-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(11,31,59,.09);border-color:#d1e9e1;}
.svcs-card::before{content:'';position:absolute;top:0;right:0;width:120px;height:120px;border-radius:0 18px 0 120px;opacity:0;transition:opacity .3s;pointer-events:none;}
.svcs-card:hover::before{opacity:1;}
.svcs-card--green::before{background:rgba(15,169,88,.06);}
.svcs-card--indigo::before{background:rgba(99,102,241,.06);}
.svcs-card--red::before{background:rgba(239,68,68,.05);}
.svcs-card--blue::before{background:rgba(14,165,233,.06);}
.svcs-new-pill{position:absolute;top:18px;right:18px;background:linear-gradient(135deg,#0FA958,#0a8a46);color:#fff;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:999px;letter-spacing:.4px;text-transform:uppercase;}
.svcs-card-icon{width:52px;height:52px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:1.25rem;margin-bottom:18px;flex-shrink:0;}
.svcs-card-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;gap:12px;}
.svcs-card-head h3{font-size:1.12rem;font-weight:800;color:var(--primary-navy);line-height:1.3;}
.svcs-price-tag{background:var(--bg-light);border:1px solid var(--border-color);border-radius:8px;padding:5px 11px;font-size:.82rem;font-weight:700;color:var(--primary-navy);white-space:nowrap;flex-shrink:0;}
.svcs-card-desc{font-size:.9rem;color:var(--text-regular);line-height:1.65;margin-bottom:18px;flex:1;}
.svcs-card-features{display:flex;flex-direction:column;gap:8px;margin-bottom:22px;}
.svcs-card-features li{display:flex;align-items:center;gap:9px;font-size:.86rem;color:var(--text-dark);}
.svcs-card-features li i{font-size:.78rem;flex-shrink:0;}
.svcs-card-cta{display:inline-flex;align-items:center;gap:8px;font-size:.9rem;font-weight:700;border-radius:10px;padding:11px 18px;transition:background .2s,transform .15s;align-self:flex-start;margin-top:auto;}
.svcs-card-cta:hover{transform:translateY(-1px);}
.svcs-card-cta--green{background:rgba(15,169,88,.1);color:#0FA958;}
.svcs-card-cta--indigo{background:rgba(99,102,241,.1);color:#6366f1;}
.svcs-card-cta--red{background:rgba(239,68,68,.08);color:#ef4444;}
.svcs-card-cta--blue{background:rgba(14,165,233,.1);color:#0ea5e9;}
.svcs-card-cta--green:hover{background:rgba(15,169,88,.18);}
.svcs-card-cta--indigo:hover{background:rgba(99,102,241,.18);}
.svcs-card-cta--red:hover{background:rgba(239,68,68,.15);}
.svcs-card-cta--blue:hover{background:rgba(14,165,233,.18);}
.cta-banner{background:linear-gradient(135deg,#071629 0%,#0d2649 100%);}
@media(max-width:1024px){.svcs-intro{grid-template-columns:repeat(2,1fr);}.svcs-grid{grid-template-columns:repeat(2,1fr);}.svcs-card--wide{grid-column:span 1;}.svcs-hero-h1{font-size:2.2rem;}}
@media(max-width:640px){.svcs-intro{grid-template-columns:1fr;}.svcs-grid{grid-template-columns:1fr;}.svcs-card--wide{grid-column:span 1;}.svcs-hero-h1{font-size:1.75rem;}.svcs-hero{padding:44px 0 40px;}}
</style>
@endpush

@section('content')
<section class="svcs-hero">
    <div class="container svcs-hero-inner">
        <div class="svcs-eyebrow"><i class="fa-solid fa-layer-group"></i> All Checks in One Place</div>
        <h1 class="svcs-hero-h1">Know Your <span>Credit Standing</span><br>Before Anyone Else Does.</h1>
        <p class="svcs-hero-sub">Run instant checks on your identity, credit score, loan defaults, and eligibility, all powered by AI. Pick what you need and pay per check.</p>
        <div class="svcs-hero-trust">
            <span><i class="fa-solid fa-bolt"></i> Results in under 1 second</span>
            <span><i class="fa-solid fa-coins"></i> Pay per check, no subscriptions</span>
            <span><i class="fa-solid fa-shield-halved"></i> Your data stays private</span>
            <span><i class="fa-solid fa-database"></i> AI powered verification</span>
        </div>
    </div>
</section>

<section class="svcs-body">
    <div class="container">
        <div class="svcs-intro">
            <div class="svcs-stat">
                <div class="svcs-stat-icon" style="background:rgba(15,169,88,.12);color:#0FA958;"><i class="fa-solid fa-bolt"></i></div>
                <div><h4>&lt;1s</h4><p>Average response time</p></div>
            </div>
            <div class="svcs-stat">
                <div class="svcs-stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-list-check"></i></div>
                <div><h4>5</h4><p>Individual checks available</p></div>
            </div>
            <div class="svcs-stat">
                <div class="svcs-stat-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;"><i class="fa-solid fa-coins"></i></div>
                <div><h4>KSh {{ number_format($prices['identity-verification'] ?? 199) }}+</h4><p>Starting price per check</p></div>
            </div>
            <div class="svcs-stat">
                <div class="svcs-stat-icon" style="background:rgba(15,169,88,.12);color:#0FA958;"><i class="fa-solid fa-shield-halved"></i></div>
                <div><h4>100%</h4><p>Private &amp; secure</p></div>
            </div>
        </div>

        <div class="svcs-grid-label">
            <div>
                <p class="section-eyebrow">Available Checks</p>
                <h2 class="section-title" style="margin-bottom:0;">Five Ways to Check Your Credit</h2>
            </div>
            <a href="{{ route('pricing') }}" style="font-size:.88rem;color:var(--primary-green);font-weight:600;">View full pricing <i class="fa-solid fa-arrow-right fa-xs"></i></a>
        </div>

        <div class="svcs-grid">
            <div class="svcs-card svcs-card--green">
                <div class="svcs-card-icon" style="background:rgba(15,169,88,.12);color:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
                <div class="svcs-card-head">
                    <h3>Identity Verification</h3>
                    <span class="svcs-price-tag">KSh {{ number_format($prices['identity-verification'] ?? 199) }}</span>
                </div>
                <p class="svcs-card-desc">Confirm your National ID details instantly, full name, date of birth, gender, and validity status, all checked in under a second.</p>
                <ul class="svcs-card-features">
                    <li><i class="fa-solid fa-circle-check" style="color:#0FA958;"></i> Full name as registered with NRB</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#0FA958;"></i> Date of birth &amp; gender confirmed</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#0FA958;"></i> ID validity status (active or flagged)</li>
                </ul>
                <a href="{{ route('identity-verification') }}" class="svcs-card-cta svcs-card-cta--green">Check Now <i class="fa-solid fa-arrow-right fa-xs"></i></a>
            </div>

            <div class="svcs-card svcs-card--indigo">
                <div class="svcs-card-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-gauge-high"></i></div>
                <div class="svcs-card-head">
                    <h3>Credit Score Check</h3>
                    <span class="svcs-price-tag">KSh {{ number_format($prices['credit-score-check'] ?? 299) }}</span>
                </div>
                <p class="svcs-card-desc">See your personal credit score, a number from 200 to 900 that tells lenders how reliable you are with money. Know your score before applying for any loan.</p>
                <ul class="svcs-card-features">
                    <li><i class="fa-solid fa-circle-check" style="color:#6366f1;"></i> Score range 200 (risky) to 900 (excellent)</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#6366f1;"></i> Based on your full credit history</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#6366f1;"></i> Know your standing before you apply</li>
                </ul>
                <a href="{{ route('credit-score-check') }}" class="svcs-card-cta svcs-card-cta--indigo">Check Now <i class="fa-solid fa-arrow-right fa-xs"></i></a>
            </div>

            <div class="svcs-card svcs-card--red">
                <div class="svcs-card-icon" style="background:rgba(239,68,68,.08);color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="svcs-card-head">
                    <h3>Loan Default Status</h3>
                    <span class="svcs-price-tag">KSh {{ number_format($prices['crb-blacklist-check'] ?? 249) }}</span>
                </div>
                <p class="svcs-card-desc">Find out if you have any defaulted or non performing loans listed against your name. Check before a lender does, no surprises at the bank.</p>
                <ul class="svcs-card-features">
                    <li><i class="fa-solid fa-circle-check" style="color:#ef4444;"></i> Listed or clear, instant answer</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#ef4444;"></i> Number of defaulted accounts on record</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#ef4444;"></i> Total outstanding bad debt figure</li>
                </ul>
                <a href="{{ route('crb-blacklist-check') }}" class="svcs-card-cta svcs-card-cta--red">Check Now <i class="fa-solid fa-arrow-right fa-xs"></i></a>
            </div>

            <div class="svcs-card svcs-card--blue svcs-card--wide">
                <div class="svcs-card-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;"><i class="fa-solid fa-file-lines"></i></div>
                <div class="svcs-card-head">
                    <h3>Full Credit Report</h3>
                    <span class="svcs-price-tag">From KSh {{ number_format($prices['full-credit-report'] ?? 499) }}</span>
                </div>
                <p class="svcs-card-desc">Get a complete picture of your credit life, every loan you've ever had, how you paid, any defaults, and who has checked you recently. Available as PDF or JSON. Ideal when a bank or SACCO asks for a full credit report before processing your application.</p>
                <ul class="svcs-card-features">
                    <li><i class="fa-solid fa-circle-check" style="color:#0ea5e9;"></i> All active and closed loan accounts</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#0ea5e9;"></i> Month-by-month repayment history</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#0ea5e9;"></i> Recent enquiries, who has checked your credit</li>
                </ul>
                <a href="{{ route('full-credit-report') }}" class="svcs-card-cta svcs-card-cta--blue">Get Report <i class="fa-solid fa-arrow-right fa-xs"></i></a>
            </div>

            <div class="svcs-card svcs-card--green">
                <span class="svcs-new-pill">New</span>
                <div class="svcs-card-icon" style="background:rgba(15,169,88,.12);color:#0FA958;"><i class="fa-solid fa-circle-check"></i></div>
                <div class="svcs-card-head">
                    <h3>Loan Eligibility Check</h3>
                    <span class="svcs-price-tag">KSh {{ number_format($prices['loan-eligibility'] ?? 349) }}</span>
                </div>
                <p class="svcs-card-desc">Our AI combines your credit score and default status to give you one clear answer, are you eligible for a loan? Walk into any lender knowing your position.</p>
                <ul class="svcs-card-features">
                    <li><i class="fa-solid fa-circle-check" style="color:#0FA958;"></i> Eligible or Not Eligible, plain answer</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#0FA958;"></i> Combines score + default check in one call</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#0FA958;"></i> AI generated insight on your risk band</li>
                </ul>
                <a href="{{ route('loan-eligibility') }}" class="svcs-card-cta svcs-card-cta--green">Check Now <i class="fa-solid fa-arrow-right fa-xs"></i></a>
            </div>

            <div class="svcs-card svcs-card--blue">
                <span class="svcs-new-pill">New</span>
                <div class="svcs-card-icon" style="background:rgba(99,102,241,.12);color:#6366f1;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="svcs-card-head">
                    <h3>Complete Financial Check</h3>
                    <span class="svcs-price-tag">KSh {{ number_format($prices['credit-account-history'] ?? 299) }}</span>
                </div>
                <p class="svcs-card-desc">See every credit account and its full 12-month payment history. Understand score trends, overdue amounts, and month by month behaviour before making a credit decision.</p>
                <ul class="svcs-card-features">
                    <li><i class="fa-solid fa-circle-check" style="color:#6366f1;"></i> 12-month payment history per account</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#6366f1;"></i> Monthly credit score trend (12 months)</li>
                    <li><i class="fa-solid fa-circle-check" style="color:#6366f1;"></i> Outstanding balances, overdue and NPA detail</li>
                </ul>
                <a href="{{ route('credit-account-history') }}" class="svcs-card-cta" style="background:#6366f1;color:#fff;">View Report <i class="fa-solid fa-arrow-right fa-xs"></i></a>
            </div>
        </div>
    </div>
</section>

<section style="background:#fff;padding:56px 0;border-top:1px solid #e2e8f0;">
    <div class="container">
        <div class="svcs-grid-label" style="margin-bottom:32px;">
            <div>
                <p class="section-eyebrow">For Developers &amp; Lenders</p>
                <h2 class="section-title" style="margin-bottom:0;">API &amp; Advanced Reports</h2>
                <p style="font-size:.9rem;color:#64748b;margin-top:6px;">Deeper data for businesses integrating credit checks into their systems.</p>
            </div>
            <a href="{{ route('documentation') }}" style="font-size:.88rem;color:var(--primary-green);font-weight:600;">View API Docs <i class="fa-solid fa-arrow-right fa-xs"></i></a>
        </div>

        <div class="svc-extra-grid">
            <div style="background:#f0fdfa;border:1px solid #99f6e4;border-radius:16px;padding:24px 20px;">
                <div style="width:44px;height:44px;background:rgba(20,184,166,.15);color:#0d9488;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:14px;"><i class="fa-solid fa-magnifying-glass-arrow-right"></i></div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:10px;">
                    <h3 style="font-size:1rem;font-weight:800;color:#0B1F3B;">Identity Scrub</h3>
                    <span style="background:#fff;border:1px solid #e2e8f0;border-radius:7px;padding:4px 10px;font-size:.8rem;font-weight:700;white-space:nowrap;">KSh {{ number_format($prices['identity-scrub'] ?? 199) }}</span>
                </div>
                <p style="font-size:.85rem;color:#475569;line-height:1.6;margin-bottom:12px;">Returns phone number, email address, physical address, and employment details linked to a National ID.</p>
                <div style="font-size:.78rem;color:#0d9488;font-weight:600;"><i class="fa-solid fa-circle-check"></i> Phone &amp; email &nbsp; <i class="fa-solid fa-circle-check"></i> Address &nbsp; <i class="fa-solid fa-circle-check"></i> Employer</div>
            </div>

            <div style="background:#faf5ff;border:1px solid #ddd6fe;border-radius:16px;padding:24px 20px;">
                <div style="width:44px;height:44px;background:rgba(139,92,246,.12);color:#7c3aed;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:14px;"><i class="fa-solid fa-code"></i></div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:10px;">
                    <h3 style="font-size:1rem;font-weight:800;color:#0B1F3B;">Full JSON Credit Report</h3>
                    <span style="background:#fff;border:1px solid #e2e8f0;border-radius:7px;padding:4px 10px;font-size:.8rem;font-weight:700;white-space:nowrap;">KSh {{ number_format($prices['full-json-credit-report'] ?? 420) }}</span>
                </div>
                <p style="font-size:.85rem;color:#475569;line-height:1.6;margin-bottom:12px;">Identity + scrub + full credit info + stakeholder data, everything in a single structured JSON response.</p>
                <div style="font-size:.78rem;color:#7c3aed;font-weight:600;"><i class="fa-solid fa-circle-check"></i> All-in-one &nbsp; <i class="fa-solid fa-circle-check"></i> Structured JSON &nbsp; <i class="fa-solid fa-circle-check"></i> API ready</div>
            </div>

            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:16px;padding:24px 20px;">
                <div style="width:44px;height:44px;background:rgba(245,158,11,.12);color:#d97706;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:14px;"><i class="fa-solid fa-chart-bar"></i></div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:10px;">
                    <h3 style="font-size:1rem;font-weight:800;color:#0B1F3B;">Enhanced Credit Info</h3>
                    <span style="background:#fff;border:1px solid #e2e8f0;border-radius:7px;padding:4px 10px;font-size:.8rem;font-weight:700;white-space:nowrap;">KSh {{ number_format($prices['enhanced-credit-info'] ?? 320) }}</span>
                </div>
                <p style="font-size:.85rem;color:#475569;line-height:1.6;margin-bottom:12px;">Full credit account data plus stakeholder and guarantor details, ideal for thorough lender due diligence.</p>
                <div style="font-size:.78rem;color:#d97706;font-weight:600;"><i class="fa-solid fa-circle-check"></i> Stakeholders &nbsp; <i class="fa-solid fa-circle-check"></i> Guarantors &nbsp; <i class="fa-solid fa-circle-check"></i> Sector data</div>
            </div>

            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:16px;padding:24px 20px;">
                <div style="width:44px;height:44px;background:rgba(16,185,129,.12);color:#059669;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:14px;"><i class="fa-solid fa-sack-dollar"></i></div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:10px;">
                    <h3 style="font-size:1rem;font-weight:800;color:#0B1F3B;">Credit Info + Income Estimation</h3>
                    <span style="background:#fff;border:1px solid #e2e8f0;border-radius:7px;padding:4px 10px;font-size:.8rem;font-weight:700;white-space:nowrap;">KSh {{ number_format($prices['credit-info-income'] ?? 380) }}</span>
                </div>
                <p style="font-size:.85rem;color:#475569;line-height:1.6;margin-bottom:12px;">Enhanced credit info with AI estimated income figures, helps lenders determine realistic loan limits.</p>
                <div style="font-size:.78rem;color:#059669;font-weight:600;"><i class="fa-solid fa-circle-check"></i> Income estimate &nbsp; <i class="fa-solid fa-circle-check"></i> Loan capacity &nbsp; <i class="fa-solid fa-circle-check"></i> Risk profile</div>
            </div>

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:16px;padding:24px 20px;grid-column:span 2;">
                <div style="display:flex;gap:20px;align-items:flex-start;">
                    <div style="width:44px;height:44px;background:rgba(37,99,235,.12);color:#2563eb;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;"><i class="fa-solid fa-star"></i></div>
                    <div style="flex:1;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;gap:10px;">
                            <div>
                                <h3 style="font-size:1.05rem;font-weight:800;color:#0B1F3B;">Full Enhanced Credit Info</h3>
                                <p style="font-size:.78rem;color:#2563eb;font-weight:600;margin-top:2px;">Complete everything, the most comprehensive report available</p>
                            </div>
                            <span style="background:#fff;border:1px solid #e2e8f0;border-radius:7px;padding:4px 10px;font-size:.8rem;font-weight:700;white-space:nowrap;">KSh {{ number_format($prices['full-enhanced-credit'] ?? 500) }}</span>
                        </div>
                        <p style="font-size:.85rem;color:#475569;line-height:1.6;margin-bottom:12px;">One call returns everything: identity verification, scrub data, 12-month credit score history, full credit accounts, stakeholders, guarantors, and income estimation.</p>
                        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:.78rem;color:#2563eb;font-weight:600;">
                            <span><i class="fa-solid fa-circle-check"></i> Identity + scrub</span>
                            <span><i class="fa-solid fa-circle-check"></i> 12 month score history</span>
                            <span><i class="fa-solid fa-circle-check"></i> All credit accounts</span>
                            <span><i class="fa-solid fa-circle-check"></i> Stakeholders &amp; guarantors</span>
                            <span><i class="fa-solid fa-circle-check"></i> Income estimation</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:20px;padding:14px 20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;font-size:.82rem;color:#64748b;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <i class="fa-solid fa-circle-info" style="color:#0FA958;"></i>
            API services require an API key. <a href="{{ route('get-started') }}" style="color:#0FA958;font-weight:600;">Get your free API key &rarr;</a>
            &nbsp;|&nbsp; All prices per API call. VAT at 16% applies.
            <a href="{{ route('pricing') }}" style="color:#0FA958;font-weight:600;margin-left:auto;">Full rate card &rarr;</a>
        </div>
    </div>
</section>

<div style="background:#fff;border-top:1px solid var(--border-color);border-bottom:1px solid var(--border-color);padding:52px 0;">
    <div class="container">
        <div class="section-header" style="margin-bottom:38px;text-align:center;">
            <p class="section-eyebrow">Simple Process</p>
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Three steps between you and knowing your credit standing.</p>
        </div>
        <div class="svc-hiw-grid">
            <div style="padding:0 36px 0 0;border-right:1px solid var(--border-color);">
                <div style="width:38px;height:38px;background:var(--primary-green);color:#fff;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;margin-bottom:14px;">01</div>
                <h3 style="font-size:1.05rem;font-weight:700;color:var(--primary-navy);margin-bottom:8px;">Enter Your National ID</h3>
                <p style="font-size:.9rem;color:var(--text-regular);line-height:1.65;">Pick the check you need and enter your National ID number. No setup, no account ; just a quick ID check to get started.</p>
            </div>
            <div style="padding:0 36px;border-right:1px solid var(--border-color);">
                <div style="width:38px;height:38px;background:var(--primary-green);color:#fff;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;margin-bottom:14px;">02</div>
                <h3 style="font-size:1.05rem;font-weight:700;color:var(--primary-navy);margin-bottom:8px;">Pay via M-Pesa</h3>
                <p style="font-size:.9rem;color:var(--text-regular);line-height:1.65;">Enter your M-Pesa number and approve the STK push on your phone. Payment is instant and secure. Each check is priced individually.</p>
            </div>
            <div style="padding:0 0 0 36px;">
                <div style="width:38px;height:38px;background:var(--primary-green);color:#fff;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;margin-bottom:14px;">03</div>
                <h3 style="font-size:1.05rem;font-weight:700;color:var(--primary-navy);margin-bottom:8px;">Get Your Results</h3>
                <p style="font-size:.9rem;color:var(--text-regular);line-height:1.65;">Your results appear instantly after payment confirmation. Download your report, share it with your lender, or keep it for your own records.</p>
            </div>
        </div>
    </div>
</div>

<section class="cta-banner">
    <div class="cta-glow"></div>
    <div class="container cta-content">
        <span class="cta-badge">Pay Per Check</span>
        <h2>Ready to check your credit?</h2>
        <p>No setup fee, no monthly commitment. Pay only for the checks you run.</p>
        <div class="cta-btns">
            <a href="{{ route('loan-eligibility') }}" class="cta-primary-btn">Run a Check Now</a>
            <a href="{{ route('pricing') }}" class="cta-outline-btn">View Pricing</a>
        </div>
    </div>
</section>
@endsection
