@extends('layouts.app')
@section('title', 'Readiwork - Instant Credit Checks for Kenyan Businesses')

@push('head')
<style>
.sv2-scroll-wrap{position:relative;padding:0 22px;}
.sv2-grid{display:flex!important;flex-wrap:nowrap!important;gap:16px;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;padding-bottom:10px;scrollbar-width:none;}
.sv2-grid::-webkit-scrollbar{display:none;}
.sv2-grid .sv2-card{flex:0 0 calc((100% - 40px) / 5.3)!important;min-width:170px;max-width:260px;scroll-snap-align:start;}
@media(max-width:1100px){.sv2-grid .sv2-card{flex:0 0 calc((100% - 32px) / 3.2)!important;}}
@media(max-width:640px){.sv2-grid .sv2-card{flex:0 0 calc((100% - 16px) / 1.3)!important;}}
/* scroll arrows */
.sv2-arrow{position:absolute;top:50%;transform:translateY(-60%);z-index:10;width:36px;height:36px;border-radius:50%;background:#fff;border:1.5px solid #e2e8f0;box-shadow:0 2px 12px rgba(11,31,59,.12);cursor:pointer;display:flex;align-items:center;justify-content:center;color:#0b1f3b;font-size:.85rem;transition:background .15s,box-shadow .15s,opacity .2s;opacity:0;pointer-events:none;}
.sv2-arrow.visible{opacity:1;pointer-events:auto;}
.sv2-arrow:hover{background:#0FA958;border-color:#0FA958;color:#fff;box-shadow:0 4px 16px rgba(15,169,88,.3);}
.sv2-arrow--prev{left:0;}
.sv2-arrow--next{right:0;}
.sv2-arrow--next::after{content:'';position:absolute;top:0;right:36px;bottom:0;width:80px;background:linear-gradient(to right,transparent,var(--bg-light,#f5f7fa));pointer-events:none;}
@media(max-width:640px){.sv2-scroll-wrap{padding:0 18px;}.sv2-arrow{width:28px;height:28px;font-size:.7rem;}.sv2-arrow--prev{left:0;}.sv2-arrow--next{right:0;}}
</style>
@endpush

@section('content')
    <!-- ===== HERO ===== -->
    <section class="hero-wrap hero-wrap--dark">
        <div class="container hero-split">
            <div class="hero-left">
                <div class="hero-badge"><span class="badge-dot"></span>Powered by AI</div>
                <h1 class="hero-h1">Instant <span class="hero-rotating-wrapper"><span class="hero-rotating-word" id="rotatingWord">Credit Checks</span></span></h1>
                <p class="hero-p">Run identity verifications, credit scores, loan default checks, and full credit reports, all through one simple API. No direct CRB integration needed.</p>
                <div class="hero-btns">
                    <a href="{{ route('services') }}" class="btn btn-primary btn-lg">Get Started Free</a>
                    <a href="{{ route('services') }}" class="btn btn-outline btn-lg">View All Services</a>
                </div>
                <div class="hero-trust">
                    <span><i class="fa-solid fa-circle-check"></i> No setup fee</span>
                    <span><i class="fa-solid fa-circle-check"></i> Results in seconds</span>
                    <span><i class="fa-solid fa-circle-check"></i> Pay per check</span>
                </div>
            </div>
            <div class="hero-right">
                <div class="result-card">
                    <div class="rc-header">
                        <span class="rc-title"><i class="fa-solid fa-circle-check rc-verified-icon"></i> Verification Complete</span>
                        <span class="rc-time">0.8s</span>
                    </div>
                    <div class="rc-id-row"><span class="rc-label">National ID</span><span class="rc-mono">3485 &middot;&middot;&middot;&middot; &middot;&middot;09</span></div>
                    <div class="rc-divider"></div>
                    <div class="rc-row"><span class="rc-label">Identity</span><span class="rc-badge green"><i class="fa-solid fa-check"></i> Verified</span></div>
                    <div class="rc-row">
                        <span class="rc-label">Credit Score</span>
                        <div class="rc-score-wrap"><div class="rc-score-bar"><div class="rc-score-fill" style="width:74%"></div></div><span class="rc-score-num">742 <small>/ 1000</small></span></div>
                    </div>
                    <div class="rc-row"><span class="rc-label">Loan Default</span><span class="rc-badge green"><i class="fa-solid fa-check"></i> Clear</span></div>
                    <div class="rc-row"><span class="rc-label">Loan Eligibility</span><span class="rc-badge green"><i class="fa-solid fa-check"></i> Eligible</span></div>
                    <div class="rc-footer"><span class="rc-provider"><i class="fa-solid fa-database"></i> AI</span></div>
                </div>
                <div class="hero-float-label" style="top: -18px; right: 30px;"><i class="fa-solid fa-bolt"></i> Sub-second response</div>
                <div class="hero-float-label" style="bottom: -18px; left: 20px;"><i class="fa-solid fa-lock"></i> Encrypted &amp; secure</div>
            </div>
        </div>
        <div class="stats-band">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item"><div class="stat-num" data-target="2000">0</div><div class="stat-plus">+</div><div class="stat-label">Daily Verifications</div></div>
                    <div class="stat-divider"></div>
                    <div class="stat-item"><div class="stat-num" data-target="99">0</div><div class="stat-plus">.9%</div><div class="stat-label">Platform Uptime</div></div>
                    <div class="stat-divider"></div>
                    <div class="stat-item"><div class="stat-num" data-target="1250">0</div><div class="stat-plus"></div><div class="stat-label">Businesses Served</div></div>
                    <div class="stat-divider"></div>
                    <div class="stat-item"><div class="stat-num" data-target="14">0</div><div class="stat-plus"></div><div class="stat-label">Report Types</div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SERVICES ===== -->
    <section class="services-section-v2">
        <div class="container">
            <div class="section-header">
                <p class="section-eyebrow">What We Offer</p>
                <h2 class="section-title">Everything in One Place</h2>
                <p class="section-subtitle">Six core checks, all pulling live data directly from AI through a single API key.</p>
            </div>
            <div class="sv2-scroll-wrap">
            <button class="sv2-arrow sv2-arrow--prev" id="sv2Prev" onclick="sv2Scroll(-1)" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="sv2-arrow sv2-arrow--next visible" id="sv2Next" onclick="sv2Scroll(1)" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
            <div class="sv2-grid" id="sv2Grid">
                <a href="{{ route('identity-verification') }}" class="sv2-card">
                    <div class="sv2-icon-wrap" style="background: rgba(15,169,88,0.1);"><svg width="32" height="32" viewBox="0 0 48 48" fill="none"><path d="M12 16C12 14.9 12.9 14 14 14H34C35.1 14 36 14.9 36 16V32C36 33.1 35.1 34 34 34H14C12.9 34 12 33.1 12 32V16Z" stroke="#0FA958" stroke-width="2.5"/><circle cx="19" cy="22" r="3" fill="#0FA958"/><path d="M26 20H32M26 25H32M15 30H23" stroke="#0FA958" stroke-width="2" stroke-linecap="round"/></svg></div>
                    <div class="sv2-provider-badge" style="background:#eff6ff; color:#0B1F3B;">AI</div>
                    <h3>Identity Verification</h3>
                    <p>Confirm a person's identity against AI records using their National ID number.</p>
                    <span class="sv2-link">Get started <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="{{ route('credit-score-check') }}" class="sv2-card">
                    <div class="sv2-icon-wrap" style="background: rgba(11,31,59,0.07);"><svg width="32" height="32" viewBox="0 0 48 48" fill="none"><path d="M10 30C10 22.3 16.3 16 24 16C31.7 16 38 22.3 38 30" stroke="#0B1F3B" stroke-width="4" stroke-linecap="round"/><path d="M10 30C10 24 14 18 20 16" stroke="#f59e0b" stroke-width="4" stroke-linecap="round"/><circle cx="24" cy="30" r="4" fill="#0B1F3B"/><path d="M24 30L30 22" stroke="#0B1F3B" stroke-width="3" stroke-linecap="round"/></svg></div>
                    <div class="sv2-provider-badge" style="background:#eff6ff; color:#0B1F3B;">AI</div>
                    <h3>Credit Score</h3>
                    <p>Retrieve AI's credit risk score to assess a borrower's creditworthiness before lending.</p>
                    <span class="sv2-link">Get started <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="{{ route('crb-blacklist-check') }}" class="sv2-card">
                    <div class="sv2-icon-wrap" style="background: rgba(239,68,68,0.08);"><svg width="32" height="32" viewBox="0 0 48 48" fill="none"><path d="M24 6L10 12V22C10 30 18 36 24 38C30 36 38 30 38 22V12L24 6Z" stroke="#ef4444" stroke-width="2.5" fill="none"/><path d="M24 16V26" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/><circle cx="24" cy="31" r="2" fill="#ef4444"/></svg></div>
                    <div class="sv2-provider-badge" style="background:#fef2f2; color:#ef4444;">AI</div>
                    <h3>Loan Default Status</h3>
                    <p>Check whether an individual has outstanding unpaid loans or a history of defaults on record.</p>
                    <span class="sv2-link">Get started <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="{{ route('full-credit-report') }}" class="sv2-card">
                    <div class="sv2-icon-wrap" style="background: rgba(99,102,241,0.08);"><svg width="32" height="32" viewBox="0 0 48 48" fill="none"><path d="M14 10H30L36 16V38H14V10Z" stroke="#6366f1" stroke-width="2.5" stroke-linejoin="round" fill="none"/><path d="M18 20H30M18 26H30M18 32H26" stroke="#6366f1" stroke-width="2" stroke-linecap="round"/><path d="M30 10V16H36" stroke="#6366f1" stroke-width="2" stroke-linejoin="round"/></svg></div>
                    <div class="sv2-provider-badge" style="background:#eef2ff; color:#6366f1;">AI</div>
                    <h3>Credit Reports</h3>
                    <p>Pull full, enhanced, summary, or minified credit bureau reports in PDF or JSON, your choice of format.</p>
                    <span class="sv2-link">Get started <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="{{ route('loan-eligibility') }}" class="sv2-card sv2-highlight">
                    <div class="sv2-icon-wrap" style="background: rgba(15,169,88,0.15);"><svg width="32" height="32" viewBox="0 0 48 48" fill="none"><rect x="8" y="14" width="32" height="20" rx="4" stroke="#0FA958" stroke-width="2.5" fill="none"/><path d="M16 24L21 29L32 19" stroke="#0FA958" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <div class="sv2-provider-badge" style="background:rgba(15,169,88,0.15); color:#0FA958;">AI</div>
                    <h3>Loan Eligibility Check <span class="sv2-new-badge">New</span></h3>
                    <p>One call combines identity verification, Credit Score, and loan default data to give a clear eligible or not eligible result.</p>
                    <span class="sv2-link">Get started <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="{{ route('credit-account-history') }}" class="sv2-card">
                    <div class="sv2-icon-wrap" style="background: rgba(99,102,241,0.08);"><svg width="32" height="32" viewBox="0 0 48 48" fill="none"><path d="M12 14H36M12 22H30M12 30H24" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round"/><circle cx="38" cy="32" r="6" stroke="#6366f1" stroke-width="2.5" fill="none"/><path d="M36 30L38 32L42 28" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <div class="sv2-provider-badge" style="background:#eef2ff; color:#6366f1;">AI</div>
                    <h3>Complete Financial Check <span class="sv2-new-badge">New</span></h3>
                    <p>Full 12-month payment history for every credit account, score trends, overdue amounts, and lender breakdown.</p>
                    <span class="sv2-link">Get started <i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </div>
            </div>{{-- end sv2-scroll-wrap --}}
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass" style="color:#9ca3af; margin-left:20px;"></i>
                <input type="text" id="quickIdInput" class="search-input" placeholder="Enter a National ID number to run a quick check" inputmode="numeric" maxlength="10">
                <button class="search-btn" id="checkNowBtn" onclick="openServicePicker()">Check Now</button>
            </div>
        </div>
    </section>

    <!-- ===== POWERED BY ===== -->
    <div class="powered-section">
        <div class="container">
            <p class="partners-title">Powered by</p>
            <div class="powered-by-single">
                <div class="powered-by-icon" style="background:rgba(15,169,88,0.1); color:#0FA958;"><i class="fa-solid fa-database"></i></div>
                <div class="powered-by-info">
                    <strong>AI</strong>
                    <span>Kenya's largest credit reference bureau, providing Credit Scores, loan default checks, identity verification, and credit reports in PDF &amp; JSON through Readiwork's single API.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== HOW IT WORKS ===== -->
    <div class="how-it-works">
        <div class="container">
            <div class="section-header">
                <p class="section-eyebrow">Simple Process</p>
                <h2 class="section-title">Up and Running in Minutes</h2>
                <p class="section-subtitle">No lengthy direct integration with AI. Readiwork handles that. You just call our API.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card"><div class="step-number">01</div><div class="step-icon"><i class="fa-solid fa-user-plus"></i></div><h3>Create an Account</h3><p>Sign up in under 2 minutes. Get your API key instantly. No contracts, no setup fees.</p><div class="step-connector"></div></div>
                <div class="step-card"><div class="step-number">02</div><div class="step-icon"><i class="fa-solid fa-plug"></i></div><h3>Make an API Call</h3><p>Send a National ID number to our endpoint. Choose which check to run, one line of code.</p><div class="step-connector"></div></div>
                <div class="step-card"><div class="step-number">03</div><div class="step-icon"><i class="fa-solid fa-circle-check"></i></div><h3>Get the Result</h3><p>Receive a clear, structured JSON result in seconds. Identity, score, loan default, all in one response.</p></div>
            </div>
        </div>
    </div>

    <!-- ===== USE CASES ===== -->
    <div class="use-cases-section">
        <div class="container">
            <div class="section-header">
                <p class="section-eyebrow">Who It's For</p>
                <h2 class="section-title">Built for Every Industry</h2>
                <p class="section-subtitle">If your business needs to trust who it's dealing with, Readiwork gives you that confidence.</p>
            </div>
            <div class="use-cases-slider-wrapper">
                <button class="uc-btn uc-prev" onclick="slideUseCases(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="uc-btn uc-next" onclick="slideUseCases(1)"><i class="fa-solid fa-chevron-right"></i></button>
                <div class="use-cases-track" id="useCasesTrack">
                    <div class="use-case-slide active">
                        <div class="uc-visual" style="background:linear-gradient(135deg,#0d2649 0%,#0a3d2b 100%);">
                            <div class="uc-visual-icon" style="background:rgba(15,169,88,0.2); color:#0FA958;"><i class="fa-solid fa-building-columns"></i></div>
                            <p class="uc-visual-label">Banks &amp; Microfinance</p>
                            <div class="uc-visual-card">
                                <div class="uc-visual-row"><span>Identity</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> Verified</span></div>
                                <div class="uc-visual-row"><span>Credit Score</span><span class="uc-badge green">742 / 900</span></div>
                                <div class="uc-visual-row"><span>Loan Defaults</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> None</span></div>
                                <div class="uc-visual-row"><span>Eligibility</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> Approved</span></div>
                            </div>
                            <span class="uc-visual-meta"><i class="fa-solid fa-database"></i> AI &middot; 0.8s</span>
                        </div>
                        <div class="use-case-content">
                            <span class="use-case-tag">Banks &amp; Microfinance</span>
                            <h3>Know Your Customer Before You Lend</h3>
                            <p>Verify customer identity, check their AI credit score, and flag delinquencies, all before opening an account or approving a loan.</p>
                            <ul class="use-case-list">
                                <li><i class="fa-solid fa-check"></i> Instant identity verification</li>
                                <li><i class="fa-solid fa-check"></i> Credit Score &amp; credit history</li>
                                <li><i class="fa-solid fa-check"></i> Loan default check</li>
                                <li><i class="fa-solid fa-check"></i> Full credit report on demand</li>
                            </ul>
                            <a href="{{ route('services') }}" class="btn btn-primary">Get Started Free</a>
                        </div>
                    </div>
                    <div class="use-case-slide">
                        <div class="uc-visual" style="background:linear-gradient(135deg,#0B1F3B 0%,#1a3560 100%);">
                            <div class="uc-visual-icon" style="background:rgba(255,255,255,0.1); color:#93c5fd;"><i class="fa-solid fa-handshake"></i></div>
                            <p class="uc-visual-label">Digital Lenders &amp; SACCOs</p>
                            <div class="uc-visual-card">
                                <div class="uc-visual-row"><span>Eligibility Check</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> Eligible</span></div>
                                <div class="uc-visual-row"><span>Credit Score</span><span class="uc-badge green">810 / 900</span></div>
                                <div class="uc-visual-row"><span>Active Loans</span><span class="uc-badge green">0 Defaults</span></div>
                                <div class="uc-visual-row"><span>Decision</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> Auto-Approved</span></div>
                            </div>
                            <span class="uc-visual-meta"><i class="fa-solid fa-bolt"></i> Loan decision in 1 API call</span>
                        </div>
                        <div class="use-case-content">
                            <span class="use-case-tag">Digital Lenders &amp; SACCOs</span>
                            <h3>Approve Loans in Seconds, Not Days</h3>
                            <p>Automate your credit decisioning. Our Loan Eligibility Check combines identity, Credit Score, and loan default data into a single pass/fail response.</p>
                            <ul class="use-case-list">
                                <li><i class="fa-solid fa-check"></i> Loan Eligibility Check (automated)</li>
                                <li><i class="fa-solid fa-check"></i> Credit Score lookup</li>
                                <li><i class="fa-solid fa-check"></i> Credit Information Summary</li>
                                <li><i class="fa-solid fa-check"></i> Credit Information History</li>
                            </ul>
                            <a href="{{ route('services') }}" class="btn btn-primary">Get Started Free</a>
                        </div>
                    </div>
                    <div class="use-case-slide">
                        <div class="uc-visual" style="background:linear-gradient(135deg,#78350f 0%,#92400e 100%);">
                            <div class="uc-visual-icon" style="background:rgba(245,158,11,0.25); color:#fbbf24;"><i class="fa-solid fa-users"></i></div>
                            <p class="uc-visual-label">Cooperatives &amp; Chamas</p>
                            <div class="uc-visual-card">
                                <div class="uc-visual-row"><span>Member ID</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> Confirmed</span></div>
                                <div class="uc-visual-row"><span>Loan Defaults</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> None</span></div>
                                <div class="uc-visual-row"><span>Credit Summary</span><span class="uc-badge green">Good Standing</span></div>
                                <div class="uc-visual-row"><span>Membership</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> Approved</span></div>
                            </div>
                            <span class="uc-visual-meta"><i class="fa-solid fa-shield-halved"></i> Protect your group from bad debt</span>
                        </div>
                        <div class="use-case-content">
                            <span class="use-case-tag">Cooperatives &amp; Chamas</span>
                            <h3>Protect Your Members from Bad Debt</h3>
                            <p>Before admitting a new member or approving a guarantee, run a quick check. Verify who they are, whether they have existing defaults.</p>
                            <ul class="use-case-list">
                                <li><i class="fa-solid fa-check"></i> Identity verification</li>
                                <li><i class="fa-solid fa-check"></i> Loan Default status</li>
                                <li><i class="fa-solid fa-check"></i> Credit Information Summary</li>
                                <li><i class="fa-solid fa-check"></i> Loan Eligibility Check</li>
                            </ul>
                            <a href="{{ route('services') }}" class="btn btn-primary">Get Started Free</a>
                        </div>
                    </div>
                    <div class="use-case-slide">
                        <div class="uc-visual" style="background:linear-gradient(135deg,#312e81 0%,#4338ca 100%);">
                            <div class="uc-visual-icon" style="background:rgba(99,102,241,0.3); color:#a5b4fc;"><i class="fa-solid fa-briefcase"></i></div>
                            <p class="uc-visual-label">HR &amp; Employers</p>
                            <div class="uc-visual-card">
                                <div class="uc-visual-row"><span>Identity Scrub</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> No Flags</span></div>
                                <div class="uc-visual-row"><span>Financial History</span><span class="uc-badge green">Clean</span></div>
                                <div class="uc-visual-row"><span>Credit Report</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> PDF Ready</span></div>
                                <div class="uc-visual-row"><span>Background</span><span class="uc-badge green"><i class="fa-solid fa-check"></i> Cleared</span></div>
                            </div>
                            <span class="uc-visual-meta"><i class="fa-solid fa-user-shield"></i> Background checks before hiring</span>
                        </div>
                        <div class="use-case-content">
                            <span class="use-case-tag">HR &amp; Employers</span>
                            <h3>Hire With Confidence</h3>
                            <p>Run a background check on every new hire before they join. Confirm their identity and review financial standing for roles that handle money.</p>
                            <ul class="use-case-list">
                                <li><i class="fa-solid fa-check"></i> Identity verification</li>
                                <li><i class="fa-solid fa-check"></i> Identity Scrub (AI Report 6)</li>
                                <li><i class="fa-solid fa-check"></i> Credit report (PDF)</li>
                                <li><i class="fa-solid fa-check"></i> Loan Default status</li>
                            </ul>
                            <a href="{{ route('services') }}" class="btn btn-primary">Get Started Free</a>
                        </div>
                    </div>
                </div>
                <div class="uc-dots" id="ucDots">
                    <span class="uc-dot active" onclick="goToSlide(0)"></span>
                    <span class="uc-dot" onclick="goToSlide(1)"></span>
                    <span class="uc-dot" onclick="goToSlide(2)"></span>
                    <span class="uc-dot" onclick="goToSlide(3)"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FEATURES ===== -->
    <div class="features-section">
        <div class="container">
            <div class="section-header">
                <p class="section-eyebrow">Why Readiwork</p>
                <h2 class="section-title">Everything You Need to Build Trust</h2>
                <p class="section-subtitle">One integration that gives you full access to AI's suite of checks, without a direct CRB contract or complex setup.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card"><div class="feature-icon"><i class="fa-solid fa-database"></i></div><h3>AI Data</h3><p>Direct access to Kenya's largest credit bureau, Credit Scores, loan default records, and credit reports in PDF &amp; JSON.</p></div>
                <div class="feature-card"><div class="feature-icon"><i class="fa-solid fa-id-card"></i></div><h3>Identity Scrub</h3><p>Cross-reference an individual against AI's full database to flag duplicate identities and discrepancies.</p></div>
                <div class="feature-card"><div class="feature-icon"><i class="fa-solid fa-layer-group"></i></div><h3>One API, Both Providers</h3><p>No need to sign a direct AI contract. One Readiwork API key, one dashboard, one bill, access to every report type.</p></div>
                <div class="feature-card"><div class="feature-icon"><i class="fa-solid fa-file-lines"></i></div><h3>Multiple Report Formats</h3><p>Get credit data as Full, Enhanced, Summary, or Minified reports in PDF or JSON, whatever your workflow needs.</p></div>
                <div class="feature-card"><div class="feature-icon"><i class="fa-solid fa-code"></i></div><h3>Clean REST API</h3><p>Simple endpoints, clear documentation, sandbox testing. Send an ID number, get a result. No complexity.</p></div>
                <div class="feature-card"><div class="feature-icon"><i class="fa-solid fa-upload"></i></div><h3>Data Submission</h3><p>Submit credit data back to AI to contribute to Kenya's credit ecosystem and improve borrower scoring.</p></div>
            </div>
        </div>
    </div>

    <!-- ===== TESTIMONIALS ===== -->
    <div class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <p class="section-eyebrow">Customer Stories</p>
                <h2 class="section-title">What Our Customers Say</h2>
            </div>
            <div style="position:relative; max-width:1200px; margin:0 auto;">
                <button class="slider-btn prev-btn" onclick="slideTestimonials(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="slider-btn next-btn" onclick="slideTestimonials(1)"><i class="fa-solid fa-chevron-right"></i></button>
                <div class="testimonial-slider" id="testimonialSlider">
                    <div class="testimonial-card"><div class="testimonial-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div><p class="testimonial-text">"Readiwork gave us instant access to AI Credit Scores and credit reports. Our loan approval process went from days to under a minute."</p><div class="user-profile"><div class="user-avatar" style="background:#0B1F3B; color:white;">JM</div><div class="user-info"><h4>James Mwenda</h4><span>Credit Manager, Nairobi MFI</span></div></div></div>
                    <div class="testimonial-card"><div class="testimonial-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div><p class="testimonial-text">"Readiwork made AI accessible without the usual paperwork. We now verify every new member's identity before they access any SACCO services."</p><div class="user-profile"><div class="user-avatar" style="background:#0FA958; color:white;">SW</div><div class="user-info"><h4>Sarah Wanjiku</h4><span>CEO, Unity SACCO</span></div></div></div>
                    <div class="testimonial-card"><div class="testimonial-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div><p class="testimonial-text">"The AI loan default check alone has saved us from dozens of bad loans. Readiwork makes it easy to query the CRB without a direct integration."</p><div class="user-profile"><div class="user-avatar" style="background:#0B1F3B; color:white;">DK</div><div class="user-info"><h4>David Kariuki</h4><span>Head of Risk, Eastleigh Finance</span></div></div></div>
                    <div class="testimonial-card"><div class="testimonial-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div><p class="testimonial-text">"We use Readiwork's AI Identity Scrub to clean our applicant database. It's flagged several duplicate identity cases we would have missed completely."</p><div class="user-profile"><div class="user-avatar" style="background:#0FA958; color:white;">AN</div><div class="user-info"><h4>Agnes Njeri</h4><span>Compliance Lead, Digital Lender</span></div></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CTA BANNER ===== -->
    <div class="cta-banner">
        <div class="cta-glow"></div>
        <div class="container cta-content">
            <div class="cta-badge"><i class="fa-solid fa-rocket"></i> Get Started Today</div>
            <h2>Ready to Verify Smarter?</h2>
            <p>Join hundreds of Kenyan businesses using Readiwork to make faster, safer lending decisions. Start free, no credit card required.</p>
            <div class="cta-btns">
                <a href="{{ route('services') }}" class="btn cta-primary-btn">Create Free Account</a>
                <a href="{{ route('documentation') }}" class="btn cta-outline-btn"><i class="fa-solid fa-code"></i> View API Docs</a>
            </div>
        </div>
    </div>

    <!-- ===== SERVICE PICKER MODAL ===== -->
    <div id="servicePickerModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(7,22,41,.75);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:22px;max-width:720px;width:100%;box-shadow:0 24px 80px rgba(0,0,0,.35);overflow:hidden;">
            <div style="background:linear-gradient(135deg,#071629,#0d2649);padding:24px 28px 20px;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:.78rem;font-weight:600;color:#6ee7a8;letter-spacing:.4px;text-transform:uppercase;margin-bottom:4px;">National ID: <span id="pickerIdDisplay" style="color:#fff;font-family:monospace;"></span><span id="pickerVerifiedName" style="display:none;color:#a3e6c4;font-family:inherit;text-transform:none;letter-spacing:0;margin-left:4px;"></span></div>
                    <div style="font-size:1.2rem;font-weight:800;color:#fff;" id="pickerTitle">Choose a Service to Run</div>
                </div>
                <button onclick="closeServicePicker()" id="pickerCloseBtn" style="background:rgba(255,255,255,.1);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
            </div>
            <div style="padding:20px 28px 28px;display:grid;gap:10px;" id="servicePickerCards"></div>
            <div id="servicePickerLoader" style="display:none;padding:40px 28px;text-align:center;">
                <div id="pickerLoaderIcon" style="width:64px;height:64px;border-radius:50%;background:rgba(15,169,88,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:1.8rem;color:#0FA958;"></i></div>
                <div style="font-size:1rem;font-weight:700;color:#0B1F3B;margin-bottom:6px;" id="loaderHeadline">Verifying Identity…</div>
                <div style="font-size:.85rem;color:#64748b;" id="loaderSub">Connecting to Kenya National Registry</div>
                <div style="margin-top:22px;height:4px;background:#f1f5f9;border-radius:99px;overflow:hidden;"><div id="loaderBar" style="height:100%;width:0%;background:linear-gradient(90deg,#0FA958,#16a34a);border-radius:99px;transition:width .5s ease;"></div></div>
                <div id="pickerLoaderError" style="display:none;margin-top:14px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#dc2626;font-size:.83rem;font-weight:600;"></div>
                <button id="pickerRetryBtn" onclick="resetServicePicker()" style="display:none;margin-top:12px;padding:9px 20px;background:#0B1F3B;color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.83rem;cursor:pointer;font-family:inherit;">← Try Another ID</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('protect.js') }}"></script>
<script src="{{ asset('interactive-bg.js') }}"></script>
<script>
(function(){
    var grid=document.getElementById('sv2Grid');
    var prev=document.getElementById('sv2Prev');
    var next=document.getElementById('sv2Next');
    if(!grid||!prev||!next) return;
    function updateArrows(){
        var atStart=grid.scrollLeft<=8;
        var atEnd=grid.scrollLeft+grid.clientWidth>=grid.scrollWidth-8;
        prev.classList.toggle('visible',!atStart);
        next.classList.toggle('visible',!atEnd);
    }
    window.sv2Scroll=function(dir){
        var card=grid.querySelector('.sv2-card');
        var step=card?(card.offsetWidth+16)*2:320;
        grid.scrollBy({left:dir*step,behavior:'smooth'});
    };
    grid.addEventListener('scroll',updateArrows,{passive:true});
    updateArrows();
})();
</script>
<script>
    const SERVICES = [
        { slug:'identity-verification',  confirm:'{{ route('identity-verification.confirm') }}',  name:'Identity Verification', desc:'Confirm full name, DOB, gender & district from the national registry.', icon:'fa-id-card',             color:'#0FA958', price:{{ \App\Models\Setting::servicePrice('identity-verification') }}  },
        { slug:'credit-score-check',     confirm:'{{ route('credit-score-check.confirm') }}',     name:'Credit Score Check',    desc:'Get your exact CRB credit score (200 to 900) with tier & risk analysis.', icon:'fa-gauge-high',          color:'#6366f1', price:{{ \App\Models\Setting::servicePrice('credit-score-check') }} },
        { slug:'crb-blacklist-check',    confirm:'{{ route('crb-blacklist-check.confirm') }}',    name:'CRB Blacklist Check',   desc:'Find out if you have unpaid or defaulted loans listed on the CRB.',     icon:'fa-triangle-exclamation',color:'#ef4444', price:{{ \App\Models\Setting::servicePrice('crb-blacklist-check') }} },
        { slug:'loan-eligibility',       confirm:'{{ route('loan-eligibility.confirm') }}',       name:'Loan Eligibility Check',desc:'Combined check: identity + score + blacklist → Eligible or Not.',        icon:'fa-circle-check',        color:'#0FA958', price:{{ \App\Models\Setting::servicePrice('loan-eligibility') }} },
        { slug:'full-credit-report',     confirm:'{{ route('full-credit-report.confirm') }}',     name:'Full Credit Report',    desc:'Complete report: identity, score, all accounts, sectors & institutions.', icon:'fa-file-lines',          color:'#0ea5e9', price:{{ \App\Models\Setting::servicePrice('full-credit-report') }} },
        { slug:'credit-account-history', confirm:'{{ route('credit-account-history.confirm') }}', name:'Complete Financial Check', desc:'12-month payment history for every account, score trends and arrears detail.', icon:'fa-clock-rotate-left', color:'#6366f1', price:{{ \App\Models\Setting::servicePrice('credit-account-history') }} },
    ];

    let _verifiedId = '', _verifiedName = '';

    function openServicePicker() {
        const inp = document.getElementById('quickIdInput');
        const raw = inp.value.trim().replace(/\D/g,'');
        if (!raw || raw.length < 6 || raw.length > 10 || /^0+$/.test(raw)) {
            inp.style.borderColor = '#ef4444';
            inp.style.boxShadow   = '0 0 0 3px rgba(239,68,68,.15)';
            inp.focus();
            const origPH = inp.placeholder;
            inp.placeholder = 'Enter a valid 6 to 10 digit National ID';
            setTimeout(() => { inp.style.borderColor=''; inp.style.boxShadow=''; inp.placeholder=origPH; }, 3000);
            return;
        }
        document.getElementById('pickerIdDisplay').textContent = raw;
        document.getElementById('pickerVerifiedName').style.display = 'none';
        document.getElementById('servicePickerCards').style.display = 'none';
        document.getElementById('servicePickerLoader').style.display = 'block';
        document.getElementById('pickerCloseBtn').style.display = 'none';
        document.getElementById('pickerTitle').textContent = 'Searching Registry…';
        document.getElementById('pickerLoaderIcon').innerHTML = '<i class="fa-solid fa-circle-notch fa-spin" style="font-size:1.8rem;color:#0FA958;"></i>';
        document.getElementById('loaderBar').style.background = 'linear-gradient(90deg,#0FA958,#16a34a)';
        document.getElementById('pickerLoaderError').style.display = 'none';
        document.getElementById('pickerRetryBtn').style.display = 'none';
        document.getElementById('servicePickerModal').style.display = 'flex';
        const headline = document.getElementById('loaderHeadline');
        const sub      = document.getElementById('loaderSub');
        const bar      = document.getElementById('loaderBar');
        const searchSteps = [
            { pct:18, h:'Connecting to National Registry…', s:'Establishing secure connection' },
            { pct:40, h:'Searching for ID ' + raw + '…',    s:'Querying Kenya National Registry database' },
            { pct:65, h:'Fetching Identity Details…',       s:'Retrieving name, DOB and location data' },
            { pct:85, h:'Verifying Record…',                s:'Cross-checking identity data' },
        ];
        let si = 0;
        headline.textContent = searchSteps[0].h; sub.textContent = searchSteps[0].s; bar.style.width = searchSteps[0].pct + '%'; si = 1;
        const ticker = setInterval(() => { if (si < searchSteps.length) { const s=searchSteps[si++]; bar.style.width=s.pct+'%'; headline.textContent=s.h; sub.textContent=s.s; } }, 900);
        const fd = new FormData(); fd.append('national_id', raw);
        const startTs = Date.now();
        fetch('{{ route('verify-id') }}', {
            method: 'POST', body: fd,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            clearInterval(ticker);
            const elapsed = Date.now() - startTs;
            const wait = Math.max(0, 3000 - elapsed);
            if (!data.valid) {
                setTimeout(() => {
                    bar.style.width = '100%'; bar.style.background = '#ef4444';
                    document.getElementById('pickerLoaderIcon').innerHTML = '<i class="fa-solid fa-circle-xmark" style="font-size:1.8rem;color:#ef4444;"></i>';
                    headline.textContent = 'ID Not Found'; sub.textContent = 'No record found in the national registry';
                    const errEl = document.getElementById('pickerLoaderError');
                    errEl.textContent = data.error || 'National ID not found. Please check the number.'; errEl.style.display = 'block';
                    document.getElementById('pickerRetryBtn').style.display = 'inline-block';
                    document.getElementById('pickerCloseBtn').style.display = 'flex';
                }, wait);
                return;
            }
            _verifiedId = raw; _verifiedName = data.name || '';
            setTimeout(() => {
                bar.style.width = '100%'; headline.textContent = 'Identity Found ✓';
                sub.textContent = _verifiedName ? ('Welcome, ' + _verifiedName) : 'Record confirmed, choose a service below';
                document.getElementById('pickerLoaderIcon').innerHTML = '<i class="fa-solid fa-circle-check" style="font-size:1.8rem;color:#0FA958;"></i>';
                setTimeout(() => {
                    const cards = document.getElementById('servicePickerCards');
                    cards.innerHTML = SERVICES.map(s => `
                        <div onclick="pickService('${s.slug}','${raw}')" style="display:flex;align-items:center;gap:14px;padding:14px 16px;border:1.5px solid #e5e7eb;border-radius:12px;cursor:pointer;transition:all .18s;" onmouseover="this.style.borderColor='${s.color}';this.style.background='#f8fafc';" onmouseout="this.style.borderColor='#e5e7eb';this.style.background='';">
                            <div style="width:44px;height:44px;border-radius:12px;background:${s.color}20;color:${s.color};display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;"><i class="fa-solid ${s.icon}"></i></div>
                            <div style="flex:1;"><div style="font-size:.92rem;font-weight:700;color:#0B1F3B;">${s.name}</div><div style="font-size:.78rem;color:#6b7280;margin-top:2px;">${s.desc}</div></div>
                            <div style="text-align:right;flex-shrink:0;"><div style="font-size:1rem;font-weight:800;color:${s.color};">KSh ${s.price.toLocaleString()}</div><div style="font-size:.7rem;color:#9ca3af;">per check</div></div>
                        </div>
                    `).join('');
                    document.getElementById('pickerTitle').textContent = 'Choose a Service to Run';
                    document.getElementById('servicePickerLoader').style.display = 'none';
                    document.getElementById('servicePickerCards').style.display = 'grid';
                    document.getElementById('pickerCloseBtn').style.display = 'flex';
                    const nameEl = document.getElementById('pickerVerifiedName');
                    if (_verifiedName) { nameEl.textContent = '· ' + _verifiedName; nameEl.style.display = 'inline'; }
                }, 800);
            }, wait);
        })
        .catch(() => {
            clearInterval(ticker);
            bar.style.width = '100%'; bar.style.background = '#ef4444';
            document.getElementById('pickerLoaderIcon').innerHTML = '<i class="fa-solid fa-circle-xmark" style="font-size:1.8rem;color:#ef4444;"></i>';
            headline.textContent = 'Service Unavailable'; sub.textContent = 'Could not reach verification service';
            const errEl = document.getElementById('pickerLoaderError');
            errEl.textContent = 'Verification service temporarily unavailable. Please try again.'; errEl.style.display = 'block';
            document.getElementById('pickerRetryBtn').style.display = 'inline-block';
            document.getElementById('pickerCloseBtn').style.display = 'flex';
        });
    }

    function pickService(slug, id) {
        const svc = SERVICES.find(s => s.slug === slug);
        if (!svc) return;
        document.getElementById('servicePickerCards').style.display = 'none';
        document.getElementById('pickerCloseBtn').style.display = 'none';
        document.getElementById('servicePickerLoader').style.display = 'block';
        document.getElementById('pickerTitle').textContent = 'Preparing ' + svc.name + '…';
        const headline = document.getElementById('loaderHeadline');
        const sub      = document.getElementById('loaderSub');
        const bar      = document.getElementById('loaderBar');
        const displayName = _verifiedName || id;
        const steps = [
            { pct: 25, h: 'Identity Confirmed ✓',         s: 'Welcome, ' + displayName },
            { pct: 55, h: 'Setting up ' + svc.name + '…', s: 'Preparing your service request' },
            { pct: 80, h: 'Almost ready…',                 s: 'Loading payment confirmation' },
            { pct: 100,h: 'Ready! Redirecting…',           s: 'Taking you to payment confirmation' },
        ];
        let stepIdx = 0;
        function advanceStep() { if (stepIdx < steps.length) { const st = steps[stepIdx++]; bar.style.width=st.pct+'%'; headline.textContent=st.h; sub.textContent=st.s; } }
        advanceStep();
        const ticker = setInterval(advanceStep, 700);
        setTimeout(() => {
            clearInterval(ticker); bar.style.width = '100%';
            const form = document.createElement('form');
            form.method = 'POST'; form.action = svc.confirm;
            const csrfInput = document.createElement('input'); csrfInput.type='hidden'; csrfInput.name='_token'; csrfInput.value='{{ csrf_token() }}'; form.appendChild(csrfInput);
            [['id_number', id], ['verified_name', _verifiedName]].forEach(([k, v]) => {
                const inp = document.createElement('input'); inp.type='hidden'; inp.name=k; inp.value=v; form.appendChild(inp);
            });
            document.body.appendChild(form); form.submit();
        }, 2800);
    }

    function closeServicePicker() { document.getElementById('servicePickerModal').style.display = 'none'; }
    function resetServicePicker() {
        document.getElementById('servicePickerCards').style.display = 'none';
        document.getElementById('servicePickerLoader').style.display = 'none';
        document.getElementById('pickerCloseBtn').style.display = 'flex';
        document.getElementById('pickerTitle').textContent = 'Choose a Service to Run';
        document.getElementById('pickerLoaderIcon').innerHTML = '<i class="fa-solid fa-circle-notch fa-spin" style="font-size:1.8rem;color:#0FA958;"></i>';
        document.getElementById('loaderBar').style.background = 'linear-gradient(90deg,#0FA958,#16a34a)';
        document.getElementById('loaderBar').style.width = '0%';
        document.getElementById('pickerLoaderError').style.display = 'none';
        document.getElementById('pickerRetryBtn').style.display = 'none';
        document.getElementById('loaderHeadline').textContent = 'Verifying Identity…';
        document.getElementById('loaderSub').textContent = 'Connecting to Kenya National Registry';
        document.getElementById('pickerVerifiedName').style.display = 'none';
        document.getElementById('servicePickerModal').style.display = 'none';
        _verifiedId = ''; _verifiedName = '';
    }
    document.getElementById('quickIdInput').addEventListener('keydown', e => { if (e.key === 'Enter') openServicePicker(); });
    document.getElementById('servicePickerModal').addEventListener('click', function(e){ if(e.target===this) closeServicePicker(); });

    // Counter animation
    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-target')), duration = 1800, start = performance.now();
        function update(now) {
            const progress = Math.min((now - start) / duration, 1), eased = 1 - Math.pow(1 - progress, 3), current = Math.floor(eased * target);
            el.textContent = target >= 1000 ? current.toLocaleString() : current;
            if (progress < 1) requestAnimationFrame(update); else el.textContent = target >= 1000 ? target.toLocaleString() : target;
        }
        requestAnimationFrame(update);
    }
    const statsObserver = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.querySelectorAll('.stat-num').forEach(animateCounter); statsObserver.unobserve(e.target); } });
    }, { threshold: 0.3 });
    const statsBand = document.querySelector('.stats-band');
    if (statsBand) statsObserver.observe(statsBand);

    // Testimonial slider
    let testimonialPos = 0;
    function slideTestimonials(dir) {
        const slider = document.getElementById('testimonialSlider'), cards = slider.querySelectorAll('.testimonial-card');
        const cardWidth = cards[0].offsetWidth + 20, maxPos = (cards.length - Math.floor(slider.offsetWidth / cardWidth)) * cardWidth;
        testimonialPos = Math.max(0, Math.min(testimonialPos + dir * cardWidth, maxPos));
        slider.scrollTo({ left: testimonialPos, behavior: 'smooth' });
    }

    // Use cases slider
    let ucIndex = 0;
    const ucSlides = document.querySelectorAll('.use-case-slide');
    const ucDotEls = document.querySelectorAll('.uc-dot');
    function goToSlide(index) {
        ucSlides[ucIndex].classList.remove('active'); ucDotEls[ucIndex].classList.remove('active');
        ucIndex = (index + ucSlides.length) % ucSlides.length;
        ucSlides[ucIndex].classList.add('active'); ucDotEls[ucIndex].classList.add('active');
    }
    function slideUseCases(dir) { goToSlide(ucIndex + dir); }
    setInterval(() => slideUseCases(1), 5500);

    // Rotating hero word
    const heroWords = ['Credit Checks', 'Identity Checks', 'CRB Blacklist Checks', 'Loan Eligibility Checks', 'Borrower History Checks'];
    let heroWordIndex = 0;
    const rotatingWord = document.getElementById('rotatingWord');
    function rotateHeroWord() {
        rotatingWord.classList.add('fade-out');
        setTimeout(() => {
            heroWordIndex = (heroWordIndex + 1) % heroWords.length;
            rotatingWord.textContent = heroWords[heroWordIndex];
            rotatingWord.classList.remove('fade-out');
            rotatingWord.classList.add('fade-in');
            setTimeout(() => rotatingWord.classList.remove('fade-in'), 400);
        }, 300);
    }
    setInterval(rotateHeroWord, 2500);
</script>
@endpush
