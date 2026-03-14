@extends('layouts.app')
@section('title', 'Pricing  Readiwork')

@push('head')
<style>
.pri-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:72px 0 56px;text-align:center;}
.pri-hero h1{font-size:clamp(2rem,4vw,2.8rem);font-weight:800;color:#fff;margin-bottom:14px;}
.pri-hero p{font-size:1.05rem;color:rgba(255,255,255,.6);max-width:520px;margin:0 auto 28px;}
.pri-badges{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;}
.pri-badge{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.75);font-size:.82rem;font-weight:600;padding:7px 16px;border-radius:999px;display:inline-flex;align-items:center;gap:7px;}
.pri-badge i{color:#0FA958;}
.how-strip{background:#0FA958;padding:18px 0;}
.how-inner{display:flex;justify-content:center;align-items:center;gap:10px;flex-wrap:wrap;}
.how-step{display:flex;align-items:center;gap:9px;color:#fff;font-size:.88rem;font-weight:600;}
.how-step i{font-size:1.1rem;}
.how-arrow{color:rgba(255,255,255,.5);font-size:.8rem;}
.pri-section{padding:64px 0;}
.checks-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:36px;}
.check-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:26px 22px;position:relative;transition:box-shadow .15s,border-color .15s;}
.check-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.08);border-color:#0FA958;}
.check-card.popular{border:2px solid #0FA958;}
.pop-badge{position:absolute;top:-11px;left:50%;transform:translateX(-50%);background:#0FA958;color:#fff;font-size:.68rem;font-weight:800;padding:3px 12px;border-radius:999px;white-space:nowrap;text-transform:uppercase;letter-spacing:.4px;}
.check-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:14px;}
.check-icon.green{background:rgba(15,169,88,.1);color:#0FA958;}
.check-icon.blue{background:rgba(37,99,235,.1);color:#2563eb;}
.check-icon.orange{background:rgba(234,88,12,.1);color:#ea580c;}
.check-icon.purple{background:rgba(124,58,237,.1);color:#7c3aed;}
.check-icon.teal{background:rgba(20,184,166,.1);color:#14b8a6;}
.check-name{font-size:.97rem;font-weight:700;color:#0B1F3B;margin-bottom:6px;}
.check-desc{font-size:.8rem;color:#64748b;line-height:1.5;margin-bottom:18px;}
.check-price{font-size:1.7rem;font-weight:800;color:#0B1F3B;line-height:1;}
.check-price span{font-size:.8rem;font-weight:400;color:#94a3b8;}
.check-link{margin-top:14px;display:block;text-align:center;background:#f0fdf4;color:#16a34a;font-size:.82rem;font-weight:700;padding:9px;border-radius:9px;text-decoration:none;transition:background .15s;}
.check-link:hover{background:#dcfce7;}
.vat-note{margin-top:28px;font-size:.82rem;color:#94a3b8;text-align:center;}
.vat-note i{color:#0FA958;}
.pri-info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:48px;}
.pri-info-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:24px 20px;text-align:center;}
.pri-info-card i{font-size:1.5rem;color:#0FA958;margin-bottom:12px;display:block;}
.pri-info-card h3{font-size:.97rem;font-weight:700;color:#0B1F3B;margin-bottom:6px;}
.pri-info-card p{font-size:.83rem;color:#64748b;line-height:1.6;}
@media(max-width:700px){.pri-info-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="pri-hero">
    <div class="container">
        <h1>Simple, Pay Per Check Pricing</h1>
        <p>No subscriptions. No setup fees. Pay only when you run a check, via M-Pesa, any time.</p>
        <div class="pri-badges">
            <span class="pri-badge"><i class="fa-solid fa-circle-check"></i> No monthly fees</span>
            <span class="pri-badge"><i class="fa-solid fa-circle-check"></i> Credits never expire</span>
            <span class="pri-badge"><i class="fa-solid fa-circle-check"></i> Results in seconds</span>
            <span class="pri-badge"><i class="fa-solid fa-circle-check"></i> M-Pesa payment</span>
        </div>
    </div>
</section>

{{-- HOW IT WORKS STRIP --}}
<div class="how-strip">
    <div class="container how-inner">
        <div class="how-step"><i class="fa-solid fa-id-card"></i> Enter Your ID</div>
        <i class="fa-solid fa-arrow-right how-arrow"></i>
        <div class="how-step"><i class="fa-solid fa-mobile-screen-button"></i> Pay via M-Pesa</div>
        <i class="fa-solid fa-arrow-right how-arrow"></i>
        <div class="how-step"><i class="fa-solid fa-file-circle-check"></i> Get Results Instantly</div>
    </div>
</div>

{{-- CHECKS --}}
<section class="pri-section" style="background:#f8fafc;">
    <div class="container">
        <div style="text-align:center;margin-bottom:0;">
            <p class="section-eyebrow">Our Services</p>
            <h2 class="section-title">What Each Check Costs</h2>
            <p style="font-size:.97rem;color:#64748b;max-width:480px;margin:0 auto;">Pay per check, no subscriptions needed. Top up your wallet and use services as you need them.</p>
        </div>

        <div class="checks-grid">
            <div class="check-card">
                <div class="check-icon green"><i class="fa-solid fa-id-card"></i></div>
                <div class="check-name">Identity Verification</div>
                <div class="check-desc">Confirm name, ID number &amp; date of birth against government records instantly.</div>
                <div class="check-price">KSh {{ number_format($prices['identity-verification'] ?? 199) }}<span> / check</span></div>
                <a href="{{ route('identity-verification') }}" class="check-link">Run This Check &rarr;</a>
            </div>

            <div class="check-card">
                <div class="check-icon orange"><i class="fa-solid fa-ban"></i></div>
                <div class="check-name">CRB Blacklist Check</div>
                <div class="check-desc">Is the person listed for unpaid loans? Get a clear Yes/No plus the outstanding amount.</div>
                <div class="check-price">KSh {{ number_format($prices['crb-blacklist-check'] ?? 249) }}<span> / check</span></div>
                <a href="{{ route('crb-blacklist-check') }}" class="check-link">Run This Check &rarr;</a>
            </div>

            <div class="check-card popular">
                <div class="pop-badge">&#9733; Best Value</div>
                <div class="check-icon teal"><i class="fa-solid fa-chart-line"></i></div>
                <div class="check-name">Credit Score</div>
                <div class="check-desc">Borrower's AI powered credit score on a 200 to 900 scale with risk tier and repayment history.</div>
                <div class="check-price">KSh {{ number_format($prices['credit-score-check'] ?? 299) }}<span> / check</span></div>
                <a href="{{ route('credit-score-check') }}" class="check-link">Run This Check &rarr;</a>
            </div>

            <div class="check-card">
                <div class="check-icon purple"><i class="fa-solid fa-scale-balanced"></i></div>
                <div class="check-name">Loan Eligibility</div>
                <div class="check-desc">Identity + blacklist combined in one call. Get a clear Eligible / Not Eligible decision.</div>
                <div class="check-price">KSh {{ number_format($prices['loan-eligibility'] ?? 349) }}<span> / check</span></div>
                <a href="{{ route('loan-eligibility') }}" class="check-link">Run This Check &rarr;</a>
            </div>

            <div class="check-card">
                <div class="check-icon blue"><i class="fa-solid fa-file-lines"></i></div>
                <div class="check-name">Full Credit Report</div>
                <div class="check-desc">Complete loan history, all accounts, and guarantor details, as a detailed report.</div>
                <div class="check-price">From KSh {{ number_format($prices['full-credit-report'] ?? 499) }}<span> / check</span></div>
                <a href="{{ route('full-credit-report') }}" class="check-link">Run This Check &rarr;</a>
            </div>
        </div>

        <p class="vat-note"><i class="fa-solid fa-circle-info"></i> All prices shown are pre-VAT. VAT at 16% is added at checkout.</p>

        {{-- Info cards --}}
        <div class="pri-info-grid">
            <div class="pri-info-card">
                <i class="fa-solid fa-wallet"></i>
                <h3>Top Up Any Amount</h3>
                <p>Add credits to your wallet via M-Pesa, bank transfer, or card. There is no minimum top-up amount. Credits never expire.</p>
            </div>
            <div class="pri-info-card">
                <i class="fa-solid fa-rotate"></i>
                <h3>Pay Only When You Check</h3>
                <p>Each check deducts the exact price from your wallet. No recurring charges, no surprises. Run as many or as few checks as you need.</p>
            </div>
            <div class="pri-info-card">
                <i class="fa-solid fa-building-columns"></i>
                <h3>Volume Discounts for Business</h3>
                <p>Running more than 500 checks per month? Contact us for volume pricing from KSh 149 per check with dedicated support.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="cta-glow"></div>
    <div class="container cta-content">
        <span class="cta-badge">Start in minutes</span>
        <h2>No setup fee. No commitment.</h2>
        <p>Pay only for the checks you run. No subscriptions, no hidden fees.</p>
        <div class="cta-btns">
            <a href="{{ route('get-started') }}" class="cta-primary-btn">Run a Check Now</a>
            <a href="{{ route('contact') }}" class="cta-outline-btn">Talk to Us</a>
        </div>
    </div>
</section>

@endsection
