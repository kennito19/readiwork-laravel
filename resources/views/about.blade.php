@extends('layouts.app')
@section('title', 'About Us - Readiwork')

@push('head')
<style>
.abt-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:80px 0 64px;text-align:center;position:relative;overflow:hidden;}
.abt-hero::before{content:'';position:absolute;top:-20%;right:-5%;width:50%;height:180%;background:radial-gradient(ellipse,rgba(15,169,88,.08) 0%,transparent 65%);pointer-events:none;}
.abt-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(15,169,88,.14);color:#6ee7a8;border:1px solid rgba(15,169,88,.28);border-radius:999px;padding:5px 16px;font-size:.82rem;font-weight:600;margin-bottom:20px;}
.abt-hero h1{font-size:clamp(2rem,4.5vw,3rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:18px;letter-spacing:-.5px;}
.abt-hero h1 span{color:#0FA958;}
.abt-hero p{font-size:1.05rem;color:rgba(255,255,255,.62);max-width:560px;margin:0 auto 32px;line-height:1.7;}
.abt-hero-btns{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;}
.abt-btn-g{background:#0FA958;color:#fff;padding:13px 28px;border-radius:10px;font-weight:700;font-size:.92rem;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:background .15s;}
.abt-btn-g:hover{background:#0d9048;color:#fff;}
.abt-btn-o{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.2);color:#fff;padding:13px 28px;border-radius:10px;font-weight:700;font-size:.92rem;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:background .15s;}
.abt-btn-o:hover{background:rgba(255,255,255,.14);color:#fff;}
.abt-section{padding:64px 0;}
.abt-section--light{background:#f8fafc;}
.abt-mission-text{max-width:740px;margin:0 auto;}
.abt-mission-text p{font-size:.97rem;color:#475569;line-height:1.8;margin-bottom:18px;}
.abt-checks{display:flex;flex-wrap:wrap;gap:10px;margin-top:24px;}
.abt-check{display:flex;align-items:center;gap:8px;font-size:.87rem;color:#0B1F3B;font-weight:600;background:#f0fdf4;border:1px solid #bbf7d0;padding:8px 14px;border-radius:8px;}
.abt-check i{color:#0FA958;}
.abt-values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:36px;}
.abt-value-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:26px 22px;transition:box-shadow .15s;position:relative;overflow:hidden;}
.abt-value-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.07);}
.abt-value-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;}
.abt-value-card.green::after{background:#0FA958;}
.abt-value-card.blue::after{background:#2563eb;}
.abt-value-card.purple::after{background:#7c3aed;}
.abt-value-card.orange::after{background:#ea580c;}
.abt-value-card.teal::after{background:#0d9488;}
.abt-value-card.red::after{background:#dc2626;}
.abt-value-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;margin-bottom:16px;}
.abt-value-card h3{font-size:.98rem;font-weight:700;color:#0B1F3B;margin-bottom:8px;}
.abt-value-card p{font-size:.83rem;color:#64748b;line-height:1.6;}
.abt-team-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:36px;max-width:640px;margin-left:auto;margin-right:auto;}
.abt-team-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:32px 24px;text-align:center;transition:box-shadow .15s;}
.abt-team-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.07);}
.abt-avatar{width:72px;height:72px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;color:#fff;}
.abt-team-card h3{font-size:.97rem;font-weight:700;color:#0B1F3B;margin-bottom:3px;}
.abt-team-role{font-size:.78rem;color:#0FA958;font-weight:600;margin-bottom:10px;}
.abt-team-card p{font-size:.82rem;color:#64748b;line-height:1.55;}
@media(max-width:900px){.abt-values-grid{grid-template-columns:repeat(2,1fr);}.abt-team-grid{grid-template-columns:repeat(2,1fr);max-width:100%;}}
@media(max-width:600px){.abt-values-grid{grid-template-columns:1fr;}.abt-team-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="abt-hero">
    <div class="container" style="position:relative;z-index:1;">
        <div class="abt-eyebrow"><i class="fa-solid fa-building"></i> About Readiwork</div>
        <h1>Building <span>Trust</span> in Kenya's<br>Credit Economy</h1>
        <p>We make credit data accessible, affordable, and instant, helping Kenyans and lenders make better financial decisions through technology.</p>
        <div class="abt-hero-btns">
            <a href="{{ route('services') }}" class="abt-btn-g"><i class="fa-solid fa-bolt"></i> Our Services</a>
            <a href="{{ route('contact') }}" class="abt-btn-o"><i class="fa-solid fa-envelope"></i> Get in Touch</a>
        </div>
    </div>
</section>

{{-- MISSION --}}
<section class="abt-section">
    <div class="container">
        <div style="text-align:center;margin-bottom:32px;">
            <p class="section-eyebrow">Our Mission</p>
            <h2 class="section-title">Democratising Credit Data for Every Kenyan</h2>
        </div>
        <div class="abt-mission-text">
            <p>Readiwork was built to solve a real problem: credit information in Kenya was fragmented, expensive, and hard to access. A borrower couldn't easily check their own standing, and lenders spent days verifying what should take seconds.</p>
            <p>We built a single platform that connects directly to Metropol Credit Reference Bureau, enabling instant identity checks, credit scores, CRB blacklist lookups, and full credit reports, all paid via M-Pesa, available to anyone with a National ID.</p>
            <p>Whether you're an individual checking your credit before approaching a bank, or a lender processing hundreds of applications a day, Readiwork gives you the data you need, instantly and affordably.</p>
            <div class="abt-checks">
                <div class="abt-check"><i class="fa-solid fa-circle-check"></i> Powered by Metropol CRB</div>
                <div class="abt-check"><i class="fa-solid fa-circle-check"></i> M-Pesa payments</div>
                <div class="abt-check"><i class="fa-solid fa-circle-check"></i> No hidden fees</div>
                <div class="abt-check"><i class="fa-solid fa-circle-check"></i> Results in seconds</div>
                <div class="abt-check"><i class="fa-solid fa-circle-check"></i> Kenya Data Protection Act compliant</div>
            </div>
        </div>
    </div>
</section>

{{-- VALUES --}}
<section class="abt-section abt-section--light" style="padding-top:0;">
    <div class="container">
        <div class="section-header" style="text-align:center;margin-bottom:0;">
            <p class="section-eyebrow">What We Stand For</p>
            <h2 class="section-title">Our Core Values</h2>
        </div>
        <div class="abt-values-grid">
            <div class="abt-value-card green">
                <div class="abt-value-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Data Integrity</h3>
                <p>Every check connects directly to official Metropol CRB data. No third party scraping, no stale databases. If it says "verified," it is verified.</p>
            </div>
            <div class="abt-value-card blue">
                <div class="abt-value-icon" style="background:rgba(37,99,235,.1);color:#2563eb;"><i class="fa-solid fa-lock"></i></div>
                <h3>Security First</h3>
                <p>Bank grade HTTPS encryption on all data. We comply with Kenya's Data Protection Act 2019 and never sell personal information to third parties.</p>
            </div>
            <div class="abt-value-card orange">
                <div class="abt-value-icon" style="background:rgba(234,88,12,.1);color:#ea580c;"><i class="fa-solid fa-hand-holding-heart"></i></div>
                <h3>Affordability</h3>
                <p>Credit information should be accessible to everyone, not just banks. Our pay per check model means you only pay for what you use, starting from KSh 199.</p>
            </div>
            <div class="abt-value-card purple">
                <div class="abt-value-icon" style="background:rgba(124,58,237,.1);color:#7c3aed;"><i class="fa-solid fa-bolt"></i></div>
                <h3>Speed &amp; Reliability</h3>
                <p>Results in under one second. Our platform is available around the clock with 99% uptime so you can make decisions whenever you need to.</p>
            </div>
            <div class="abt-value-card teal">
                <div class="abt-value-icon" style="background:rgba(20,184,166,.1);color:#0d9488;"><i class="fa-solid fa-eye"></i></div>
                <h3>Transparency</h3>
                <p>No hidden fees. No confusing bundles. Prices are published openly, VAT is shown upfront, and every transaction comes with a clear receipt.</p>
            </div>
            <div class="abt-value-card red">
                <div class="abt-value-icon" style="background:rgba(220,38,38,.08);color:#dc2626;"><i class="fa-solid fa-users"></i></div>
                <h3>Customer Focus</h3>
                <p>We exist to serve Kenyan borrowers and lenders. Every feature we build starts with the question: does this make life easier for our users?</p>
            </div>
        </div>
    </div>
</section>

{{-- TEAM --}}
<section class="abt-section">
    <div class="container">
        <div class="section-header" style="text-align:center;margin-bottom:0;">
            <p class="section-eyebrow">The People</p>
            <h2 class="section-title">Meet the Team</h2>
            <p class="section-subtitle">A passionate team of fintech builders and credit experts working to make credit data accessible for all Kenyans.</p>
        </div>
        <div class="abt-team-grid">
            <div class="abt-team-card">
                <div class="abt-avatar" style="background:linear-gradient(135deg,#0FA958,#0d9048);">AR</div>
                <h3>Abdul Rahman</h3>
                <div class="abt-team-role">Co-Founder</div>
                <p>Passionate about financial inclusion and making credit data accessible and actionable for every Kenyan.</p>
            </div>
            <div class="abt-team-card">
                <div class="abt-avatar" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">KO</div>
                <h3>Kennedy</h3>
                <div class="abt-team-role">Co-Founder &amp; Developer</div>
                <p>Full stack developer leading all product development and platform infrastructure, with a focus on fast, secure fintech solutions.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="cta-glow"></div>
    <div class="container cta-content">
        <span class="cta-badge">Join Thousands of Kenyans</span>
        <h2>Ready to know your credit standing?</h2>
        <p>Run your first credit check in minutes. No setup fee, no subscription. Just pay when you need it.</p>
        <div class="cta-btns">
            <a href="{{ route('services') }}" class="cta-primary-btn">Explore Services</a>
            <a href="{{ route('contact') }}" class="cta-outline-btn">Contact Us</a>
        </div>
    </div>
</section>

@endsection
