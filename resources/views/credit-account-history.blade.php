@extends('layouts.app')
@section('title', 'Complete Financial Check - Readiwork')

@push('head')
<style>
.svc-hero-dark{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:54px 0 48px;position:relative;overflow:hidden;}
.svc-hero-dark::before{content:'';position:absolute;top:-40%;right:-5%;width:55%;height:180%;background:radial-gradient(ellipse,rgba(99,102,241,0.08) 0%,transparent 65%);pointer-events:none;}
.svc-hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(99,102,241,0.15);color:#a5b4fc;border:1px solid rgba(99,102,241,0.3);border-radius:999px;padding:5px 14px;font-size:.82rem;font-weight:600;margin-bottom:16px;}
.svc-hero-h1{font-size:2.4rem;font-weight:800;color:#fff;margin-bottom:12px;line-height:1.15;}
.svc-hero-sub{color:rgba(255,255,255,0.65);font-size:1.02rem;max-width:580px;line-height:1.6;margin-bottom:20px;}
.svc-hero-meta{display:flex;flex-wrap:wrap;gap:20px;}
.svc-hero-meta span{color:rgba(255,255,255,0.55);font-size:.88rem;display:flex;align-items:center;gap:7px;}
.svc-hero-meta i{color:#6366f1;}
.svc-main{padding:52px 0 64px;background:var(--bg-light);}
.svc-layout{display:grid;grid-template-columns:1fr 420px;gap:28px;align-items:start;}
.check-form-card{background:#fff;border:1px solid var(--border-color);border-radius:16px;padding:32px;box-shadow:0 4px 20px rgba(11,31,59,0.06);}
.id-input-wrap{position:relative;margin-bottom:18px;}
.id-input-label{display:block;font-weight:600;color:var(--primary-navy);font-size:.9rem;margin-bottom:7px;}
.id-input-field{width:100%;padding:14px 14px 14px 46px;border:1.5px solid var(--border-color);border-radius:10px;font-size:1rem;color:var(--text-dark);outline:none;transition:border-color .2s;font-family:inherit;}
.id-input-field:focus{border-color:#6366f1;}
.id-input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:1rem;}
.consent-box{background:#f3f4ff;border:1px solid #c7d2fe;border-radius:10px;padding:14px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;}
.consent-box input[type="checkbox"]{margin-top:3px;accent-color:#6366f1;width:16px;height:16px;flex-shrink:0;}
.consent-box label{font-size:.87rem;color:var(--text-regular);line-height:1.6;cursor:pointer;}
.run-btn{width:100%;background:#6366f1;color:#fff;border:none;border-radius:10px;padding:14px 20px;font-size:1rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;transition:background .2s,transform .15s;}
.run-btn:hover{background:#4f46e5;transform:translateY(-1px);}
.price-badge{display:flex;align-items:center;gap:10px;background:var(--bg-light);border:1px solid var(--border-color);border-radius:10px;padding:12px 16px;margin-top:16px;}
.info-panel{display:flex;flex-direction:column;gap:16px;}
.info-card{background:#fff;border:1px solid var(--border-color);border-radius:14px;padding:22px;}
.info-card-title{font-size:.95rem;font-weight:700;color:var(--primary-navy);margin-bottom:14px;display:flex;align-items:center;gap:9px;}
.gets-list{display:grid;gap:12px;}
.gets-item{display:flex;gap:12px;align-items:flex-start;}
.gets-icon{width:44px;height:44px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:1.15rem;flex-shrink:0;}
.gets-item h4{font-size:.9rem;font-weight:700;color:var(--primary-navy);margin-bottom:2px;}
.gets-item p{font-size:.82rem;color:var(--text-regular);line-height:1.5;}
.how-strip{background:#fff;border-top:1px solid var(--border-color);border-bottom:1px solid var(--border-color);padding:44px 0;}
.how-strip-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;}
.how-step{padding:0 32px;border-right:1px solid var(--border-color);display:flex;flex-direction:column;gap:10px;}
.how-step:last-child{border-right:0;}
.how-step-num{width:36px;height:36px;background:#6366f1;color:#fff;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;}
.how-step h3{font-size:1rem;font-weight:700;color:var(--primary-navy);}
.how-step p{font-size:.88rem;color:var(--text-regular);line-height:1.6;}
.other-svcs{padding:44px 0;background:var(--bg-light);}
.other-svcs-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:24px;}
.other-svc-card{background:#fff;border:1px solid var(--border-color);border-radius:12px;padding:18px;display:block;color:inherit;transition:transform .18s,box-shadow .18s,border-color .18s;}
.other-svc-card:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(11,31,59,.08);border-color:#c7d2fe;}
.other-svc-icon{width:40px;height:40px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;}
.other-svc-card h4{font-size:.92rem;font-weight:700;color:var(--primary-navy);margin-bottom:4px;}
.other-svc-card p{font-size:.8rem;color:var(--text-regular);}
@media(max-width:980px){.svc-layout{grid-template-columns:1fr;}.how-strip-grid{grid-template-columns:1fr;gap:28px;}.how-step{border-right:0;border-bottom:1px solid var(--border-color);padding:0 0 28px;}.how-step:last-child{border-bottom:0;}.other-svcs-grid{grid-template-columns:repeat(2,1fr);}.svc-hero-h1{font-size:1.9rem;}}
@media(max-width:560px){.other-svcs-grid{grid-template-columns:1fr;}.check-form-card{padding:20px;}}
</style>
@endpush

@section('content')
<section class="svc-hero-dark">
    <div class="container">
        <div class="svc-hero-eyebrow"><i class="fa-solid fa-clock-rotate-left"></i> Complete Financial Check</div>
        <h1 class="svc-hero-h1">Complete Financial Check<br>with Payment Trends.</h1>
        <p class="svc-hero-sub">See every credit account, its 12-month payment history, outstanding balances, and arrears. Know exactly how a borrower has been performing month by month.</p>
        <div class="svc-hero-meta">
            <span><i class="fa-solid fa-database"></i> Kenya Credit Bureau</span>
            <span><i class="fa-solid fa-bolt"></i> Results in &lt; 2 seconds</span>
            <span><i class="fa-solid fa-coins"></i> KSh {{ number_format($price) }} per check</span>
            <span><i class="fa-solid fa-shield-halved"></i> Consent required</span>
        </div>
    </div>
</section>

<section class="svc-main">
    <div class="container">
        <div class="svc-layout">
            <div>
                <div class="check-form-card">
                    <h2 style="font-size:1.25rem;font-weight:800;color:var(--primary-navy);margin-bottom:4px;">Run Complete Financial Check</h2>
                    <p style="color:var(--text-regular);font-size:.92rem;margin-bottom:24px;">Enter a National ID to retrieve all credit accounts and their full 12-month payment history from Kenya CRB.</p>

                    <form id="svcForm" method="POST" action="{{ route('credit-account-history.confirm') }}">
                        @csrf
                        <input type="hidden" id="verified_name" name="verified_name" value="">
                        <div class="id-input-wrap">
                            <label class="id-input-label" for="id_number">National ID Number</label>
                            <i class="fa-solid fa-id-card id-input-icon"></i>
                            <input type="text" id="id_number" name="id_number" class="id-input-field" required placeholder="e.g. 12345678" pattern="[0-9]{6,10}" autocomplete="off">
                            <p style="margin-top:6px;font-size:.8rem;color:var(--text-light);">Your 6 to 10 digit Kenyan National ID number.</p>
                        </div>
                        <div class="consent-box">
                            <input type="checkbox" id="consent" required>
                            <label for="consent">I consent to Readiwork running a Complete Financial Check in accordance with <a href="{{ route('terms') }}" style="color:#6366f1;">Terms of Service</a>. This check costs <strong>KSh {{ number_format($price) }}</strong> and will be collected via M-Pesa STK Push.</label>
                        </div>
                        <button type="submit" class="run-btn"><i class="fa-solid fa-clock-rotate-left"></i> Get Account History</button>
                    </form>

                    <div class="price-badge">
                        <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;font-size:1.05rem;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 3px 10px rgba(99,102,241,.3);"><i class="fa-solid fa-coins"></i></div>
                        <div style="font-size:.86rem;color:var(--text-regular);"><strong style="color:var(--primary-navy);">KSh {{ number_format($price) }} per check</strong> &nbsp;&middot;&nbsp; Paid via M-Pesa STK Push</div>
                    </div>
                </div>
            </div>

            <div class="info-panel">
                <div class="info-card">
                    <div class="info-card-title">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;font-size:.85rem;flex-shrink:0;"><i class="fa-solid fa-clock-rotate-left"></i></span>
                        What This Report Includes
                    </div>
                    <div class="gets-list">
                        <div class="gets-item">
                            <div class="gets-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 4px 12px rgba(99,102,241,.3);"><i class="fa-solid fa-calendar-days"></i></div>
                            <div><h4>12-Month Payment History</h4><p>Month by month status for each account: payments made, overdue amounts, and days in arrears.</p></div>
                        </div>
                        <div class="gets-item">
                            <div class="gets-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;box-shadow:0 4px 12px rgba(14,165,233,.3);"><i class="fa-solid fa-building-columns"></i></div>
                            <div><h4>All Credit Accounts</h4><p>Every bank, microfinance, and mobile loan account, with balances, original amounts, and current status.</p></div>
                        </div>
                        <div class="gets-item">
                            <div class="gets-icon" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;box-shadow:0 4px 12px rgba(22,163,74,.3);"><i class="fa-solid fa-chart-line"></i></div>
                            <div><h4>Credit Score Trend</h4><p>Monthly credit score over 12 months showing whether the score is improving or declining.</p></div>
                        </div>
                        <div class="gets-item">
                            <div class="gets-icon" style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;box-shadow:0 4px 12px rgba(217,119,6,.3);"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div><h4>Arrears and NPA Summary</h4><p>Total overdue amounts, highest days in arrears, and non-performing account totals.</p></div>
                        </div>
                    </div>
                </div>
                <div class="info-card" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div style="text-align:center;padding:8px;"><div style="font-size:1.6rem;font-weight:800;color:var(--primary-navy);">&lt;2s</div><div style="font-size:.8rem;color:var(--text-regular);">Response time</div></div>
                    <div style="text-align:center;padding:8px;border-left:1px solid var(--border-color);"><div style="font-size:1.6rem;font-weight:800;color:#6366f1;">KSh {{ number_format($price) }}</div><div style="font-size:.8rem;color:var(--text-regular);">Per check</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="how-strip">
    <div class="container">
        <div class="section-header" style="margin-bottom:36px;"><p class="section-eyebrow">How It Works</p><h2 class="section-title">3 Steps to Your Complete Financial Check</h2></div>
        <div class="how-strip-grid">
            <div class="how-step"><div class="how-step-num">01</div><h3>Enter National ID</h3><p>Type the Kenyan National ID you want to retrieve account history for into the form above.</p></div>
            <div class="how-step"><div class="how-step-num">02</div><h3>We Pull CRB Records</h3><p>Readiwork queries the Kenya CRB database for all registered credit accounts and their full payment timelines.</p></div>
            <div class="how-step"><div class="how-step-num">03</div><h3>Get the Full History</h3><p>Receive a structured report showing every account, lender, monthly payment status, score trend, and arrears detail.</p></div>
        </div>
    </div>
</div>

<div class="other-svcs">
    <div class="container">
        <p class="section-eyebrow">Other Services</p>
        <h2 class="section-title" style="margin-bottom:4px;">Explore Other Credit Checks</h2>
        <div class="other-svcs-grid">
            <a href="{{ route('loan-eligibility') }}" class="other-svc-card">
                <div class="other-svc-icon" style="background:rgba(15,169,88,0.1);color:#0FA958;"><i class="fa-solid fa-circle-check"></i></div>
                <h4>Loan Eligibility</h4>
                <p>KSh {{ \App\Models\Setting::servicePrice('loan-eligibility') }} &nbsp;&middot;&nbsp; Full eligibility verdict</p>
            </a>
            <a href="{{ route('crb-blacklist-check') }}" class="other-svc-card">
                <div class="other-svc-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <h4>CRB Blacklist Check</h4>
                <p>KSh {{ \App\Models\Setting::servicePrice('crb-blacklist-check') }} &nbsp;&middot;&nbsp; Loan default status</p>
            </a>
            <a href="{{ route('credit-score-check') }}" class="other-svc-card">
                <div class="other-svc-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;"><i class="fa-solid fa-gauge-high"></i></div>
                <h4>Credit Score Check</h4>
                <p>KSh {{ \App\Models\Setting::servicePrice('credit-score-check') }} &nbsp;&middot;&nbsp; AI credit risk score</p>
            </a>
            <a href="{{ route('full-credit-report') }}" class="other-svc-card">
                <div class="other-svc-icon" style="background:rgba(14,165,233,0.1);color:#0ea5e9;"><i class="fa-solid fa-file-lines"></i></div>
                <h4>Full Credit Report</h4>
                <p>KSh {{ \App\Models\Setting::servicePrice('full-credit-report') }} &nbsp;&middot;&nbsp; Complete report</p>
            </a>
        </div>
    </div>
</div>

<!-- ═══ LOADING OVERLAY ═══ -->
<div id="eligLoader" style="display:none;position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#071629 0%,#0d2649 60%,#071629 100%);flex-direction:column;align-items:center;justify-content:center;padding:24px;">
    <div id="eligDots" style="position:absolute;inset:0;overflow:hidden;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;width:100%;max-width:460px;text-align:center;">
        <div style="position:relative;width:88px;height:88px;margin:0 auto 28px;">
            <svg viewBox="0 0 88 88" style="position:absolute;inset:0;width:88px;height:88px;animation:spin 2s linear infinite;">
                <circle cx="44" cy="44" r="38" fill="none" stroke="rgba(99,102,241,.15)" stroke-width="4"/>
                <circle cx="44" cy="44" r="38" fill="none" stroke="#6366f1" stroke-width="4" stroke-dasharray="60 180" stroke-linecap="round"/>
            </svg>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                <div id="loaderIcon" style="width:52px;height:52px;border-radius:14px;background:rgba(99,102,241,.15);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#6366f1;transition:all .4s;"><i class="fa-solid fa-server"></i></div>
            </div>
        </div>
        <div id="loaderStep" style="font-size:.75rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#6366f1;margin-bottom:10px;opacity:.9;">Step 1 of 5</div>
        <div id="loaderTitle" style="font-size:1.45rem;font-weight:800;color:#fff;margin-bottom:8px;line-height:1.3;transition:opacity .35s,transform .35s;">Connecting to Credit Bureau</div>
        <div id="loaderSub" style="font-size:.88rem;color:rgba(255,255,255,.5);margin-bottom:32px;line-height:1.6;min-height:40px;transition:opacity .35s;">Establishing secure connection to the Credit Reference Bureau...</div>
        <div style="background:rgba(255,255,255,.08);border-radius:999px;height:5px;margin-bottom:12px;overflow:hidden;">
            <div id="loaderBar" style="height:100%;border-radius:999px;background:linear-gradient(90deg,#6366f1,#818cf8);width:0%;transition:width .7s cubic-bezier(.4,0,.2,1);"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.68rem;color:rgba(255,255,255,.25);margin-bottom:32px;"><span>Connecting</span><span>Verifying</span><span>Fetching</span><span>Ready</span></div>
        <div style="text-align:left;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:16px 20px;display:grid;gap:10px;">
            @foreach([['fa-server','Connecting to Credit Bureau'],['fa-id-card','Verifying Your Identity'],['fa-building-columns','Fetching Credit Accounts'],['fa-calendar-days','Loading 12-Month History'],['fa-file-lines','Preparing Account Report']] as $i=>$step)
            <div class="ld-step" id="ldStep{{ $i }}" style="display:flex;align-items:center;gap:12px;opacity:.3;transition:opacity .4s,transform .4s;transform:translateX(-6px);">
                <div style="width:26px;height:26px;border-radius:7px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.75rem;color:#818cf8;"><i class="fa-solid {{ $step[0] }}"></i></div>
                <span style="font-size:.82rem;color:rgba(255,255,255,.65);flex:1;">{{ $step[1] }}</span>
                <span class="ld-tick" style="color:#818cf8;font-size:.85rem;opacity:0;transition:opacity .3s;">&#10003;</span>
            </div>
            @endforeach
        </div>
        <div style="margin-top:20px;display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:999px;padding:6px 16px;font-size:.78rem;color:rgba(255,255,255,.4);">
            <i class="fa-solid fa-fingerprint" style="color:#6366f1;"></i>
            National ID: <span id="loaderIdVal" style="color:rgba(255,255,255,.7);font-weight:600;letter-spacing:.5px;"></span>
        </div>
    </div>
</div>
<style>@keyframes spin{to{transform:rotate(360deg);}}@keyframes floatDot{0%,100%{transform:translateY(0) scale(1);opacity:.35;}50%{transform:translateY(-22px) scale(1.1);opacity:.6;}}</style>

@endsection

@push('scripts')
<script>
(function() {
    const form  = document.getElementById('svcForm');
    const input = document.getElementById('id_number');
    const btn   = form ? form.querySelector('button[type="submit"]') : null;
    if (!form || !input || !btn) return;
    const origHTML  = btn.innerHTML;
    const csrfToken = '{{ csrf_token() }}';
    const verifyUrl = '{{ route("verify-id") }}';

    function showErr(msg) {
        let el = document.getElementById('id-err');
        if (!el) { el=document.createElement('p'); el.id='id-err'; el.style.cssText='color:#dc2626;font-size:.8rem;margin:6px 0 0;font-weight:500;'; input.parentNode.appendChild(el); }
        el.textContent=msg; input.style.borderColor='#ef4444'; input.focus();
    }
    function clearErr() { const e=document.getElementById('id-err'); if(e) e.textContent=''; }
    function localValidate(val) {
        if (!/^\d+$/.test(val)) return 'ID must contain digits only.';
        if (val.length < 6)    return 'ID is too short, minimum 6 digits.';
        if (val.length > 10)   return 'ID is too long, maximum 10 digits.';
        if (/^0+$/.test(val))  return 'Enter a valid National ID number.';
        return '';
    }
    input.addEventListener('input', function() {
        this.value=this.value.replace(/\D/g,''); clearErr();
        const ok=this.value.length>=6&&!localValidate(this.value.trim());
        this.style.borderColor=ok?'#6366f1':'';
    });

    const overlay    = document.getElementById('eligLoader');
    const loaderBar  = document.getElementById('loaderBar');
    const loaderTitle= document.getElementById('loaderTitle');
    const loaderSub  = document.getElementById('loaderSub');
    const loaderStep = document.getElementById('loaderStep');
    const loaderIcon = document.getElementById('loaderIcon');
    const loaderIdVal= document.getElementById('loaderIdVal');

    const STEPS = [{label:"Connecting to Credit Bureau",icon:"fa-server",pct:0},{label:"Verifying Your Identity",icon:"fa-id-card",pct:22},{label:"Fetching Credit Accounts",icon:"fa-building-columns",pct:45},{label:"Loading 12-Month History",icon:"fa-calendar-days",pct:68},{label:"Preparing Account Report",icon:"fa-file-lines",pct:90}];
    const STEP_SUBS = ["Establishing secure connection to the Credit Reference Bureau...","Checking your National ID against the national registry...","Retrieving all credit accounts from Kenya CRB...","Loading 12-month payment history for each account...","Almost done, compiling your full account history report..."];
    const STEP_TIMING = [0, 1700, 3400, 5100, 6800];

    function spawnDots() {
        const wrap=document.getElementById('eligDots');
        for (let i=0;i<18;i++) {
            const d=document.createElement('div'), size=3+Math.random()*6;
            d.style.cssText=`position:absolute;width:${size}px;height:${size}px;border-radius:50%;background:rgba(99,102,241,${0.08+Math.random()*0.18});left:${Math.random()*100}%;top:${Math.random()*100}%;animation:floatDot ${3+Math.random()*4}s ${Math.random()*3}s ease-in-out infinite;`;
            wrap.appendChild(d);
        }
    }
    function setStep(idx) {
        const s=STEPS[idx];
        loaderTitle.style.opacity='0'; loaderTitle.style.transform='translateY(-8px)'; loaderSub.style.opacity='0';
        setTimeout(()=>{
            loaderTitle.textContent=s.label; loaderSub.textContent=STEP_SUBS[idx]||'';
            loaderStep.textContent='Step '+(idx+1)+' of 5';
            loaderIcon.innerHTML=`<i class="fa-solid ${s.icon}"></i>`;
            loaderBar.style.width=s.pct+'%';
            loaderTitle.style.opacity='1'; loaderTitle.style.transform='translateY(0)'; loaderSub.style.opacity='1';
        }, 320);
        const stepEl=document.getElementById('ldStep'+idx);
        if (stepEl) {
            stepEl.style.opacity='1'; stepEl.style.transform='translateX(0)';
            setTimeout(()=>{
                const tick=stepEl.querySelector('.ld-tick'); if(tick) tick.style.opacity='1';
                for (let j=0;j<idx;j++) { const p=document.getElementById('ldStep'+j); if(p){const t=p.querySelector('.ld-tick');if(t) t.style.opacity='1';} }
            }, 700);
        }
    }
    function showLoader(idVal) {
        loaderIdVal.textContent=idVal; overlay.style.display='flex'; spawnDots(); setStep(0);
        STEP_TIMING.forEach((t,i)=>{ if(i===0) return; setTimeout(()=>setStep(i),t); });
    }
    function hideLoaderWithError() { overlay.style.display='none'; }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const val=input.value.trim(), le=localValidate(val);
        if (le) { showErr(le); return; }
        clearErr(); btn.disabled=true; btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
        showLoader(val);
        const minTime=8000, startTs=Date.now();
        const fd=new FormData(); fd.append('national_id', val);
        let fetchResult=null;
        fetch(verifyUrl,{method:'POST',body:fd,headers:{'X-CSRF-TOKEN':csrfToken}})
            .then(r=>r.json()).then(data=>{fetchResult={ok:true,data};}).catch(()=>{fetchResult={ok:false};});
        const poll=setInterval(()=>{
            if (fetchResult===null) return;
            const wait=Math.max(0,minTime-(Date.now()-startTs)); clearInterval(poll);
            setTimeout(()=>{
                if (!fetchResult.ok||!fetchResult.data.valid) {
                    hideLoaderWithError(); btn.disabled=false; btn.innerHTML=origHTML;
                    showErr(!fetchResult.ok?'Verification service temporarily unavailable. Please try again.':(fetchResult.data.error||'National ID could not be verified. Please check and try again.'));
                } else {
                    document.getElementById('verified_name').value=fetchResult.data.name||'';
                    loaderBar.style.width='100%';
                    setTimeout(()=>form.submit(), 800);
                }
            }, wait);
        }, 100);
    });
    (function(){ const u=new URLSearchParams(window.location.search).get('id'); if(u&&/^\d{6,10}$/.test(u)) input.value=u; })();
})();
</script>
@endpush
