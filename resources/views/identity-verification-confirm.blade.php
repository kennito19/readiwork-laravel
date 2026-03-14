@extends('layouts.app')
@section('title', 'Confirm Identity Verification - Readiwork')

@push('head')
<style>
.confirm-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:40px 0 36px;}
.confirm-hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(15,169,88,0.15);color:#6ee7a8;border:1px solid rgba(15,169,88,0.3);border-radius:999px;padding:4px 14px;font-size:.8rem;font-weight:600;margin-bottom:12px;}
.confirm-hero h1{font-size:1.9rem;font-weight:800;color:#fff;margin-bottom:6px;}
.confirm-hero p{color:rgba(255,255,255,0.6);font-size:.95rem;}
.confirm-main{padding:40px 0 64px;background:var(--bg-light);}
.confirm-layout{display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;}
.info-card-l{background:#fff;border:1px solid var(--border-color);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(11,31,59,.07);padding:28px;}
.order-card{background:#fff;border:1px solid var(--border-color);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(11,31,59,.07);}
.order-card-head{background:var(--primary-navy);padding:20px 24px;}
.order-card-head h3{font-size:1rem;font-weight:700;color:#fff;margin-bottom:2px;}
.order-card-head p{font-size:.82rem;color:rgba(255,255,255,.5);}
.order-card-body{padding:20px 24px;}
.order-row{display:flex;justify-content:space-between;align-items:flex-start;padding:11px 0;border-bottom:1px solid var(--border-color);font-size:.88rem;gap:12px;}
.order-row:last-of-type{border-bottom:0;}
.order-label{color:var(--text-light);font-weight:500;white-space:nowrap;}
.order-val{color:var(--text-dark);font-weight:600;text-align:right;}
.order-total{display:flex;justify-content:space-between;align-items:center;padding:16px 0 12px;border-top:2px solid var(--border-color);margin-top:4px;}
.order-total-label{font-size:1rem;font-weight:700;color:var(--primary-navy);}
.order-total-val{font-size:1.45rem;font-weight:800;color:var(--primary-green);}
.mpesa-section{margin-bottom:16px;}
.mpesa-label{display:block;font-size:.85rem;font-weight:700;color:var(--primary-navy);margin-bottom:8px;}
.mpesa-input-wrap{position:relative;}
.mpesa-prefix{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:.9rem;font-weight:700;color:#16a34a;}
.mpesa-input{width:100%;padding:13px 14px 13px 52px;border:1.5px solid var(--border-color);border-radius:10px;font-size:.98rem;font-family:inherit;color:var(--text-dark);outline:none;transition:border-color .2s;box-sizing:border-box;}
.mpesa-input:focus{border-color:#16a34a;}
.stk-pending{display:none;background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:18px 16px;text-align:center;margin-bottom:14px;}
.stk-pending.show{display:block;}
.stk-spinner{width:40px;height:40px;border:3px solid #bbf7d0;border-top-color:#16a34a;border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 12px;}
@keyframes spin{to{transform:rotate(360deg);}}
.stk-pending p{font-size:.88rem;color:#166534;font-weight:600;margin-bottom:4px;}
.stk-pending small{font-size:.78rem;color:#4ade80;}
.unlock-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;background:#16a34a;color:#fff;border:none;border-radius:12px;padding:16px 20px;font-size:1.05rem;font-weight:700;cursor:pointer;transition:background .2s,transform .15s;}
.unlock-btn:hover{background:#15803d;transform:translateY(-1px);}
.unlock-btn:disabled{background:#6b7280;cursor:not-allowed;transform:none;}
@media(max-width:900px){.confirm-layout{grid-template-columns:1fr;}.order-card{order:-1;}}
</style>
@endpush

@section('content')
<section class="confirm-hero">
    <div class="container">
        <div class="confirm-hero-eyebrow"><i class="fa-solid fa-id-card"></i> Identity Verification &nbsp;·&nbsp; Step 2 of 2</div>
      
        {{-- REPLACE WITH --}}
<h1>Hi there 👋, ID Located</h1>
<p>National ID <strong style="color:#4ade80;">{{ $idNumber }}</strong> has been found. Complete payment below to unlock the full identity details.</p>
        
        
        
    </div>





</section>

<section class="confirm-main">
    <div class="container">
        <div class="confirm-layout">
            <div class="info-card-l">
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
                    <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#0FA958,#16a34a);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;flex-shrink:0;">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h2 style="font-size:1.2rem;font-weight:800;color:var(--primary-navy);margin-bottom:2px;">Identity Verification</h2>
                        <p style="font-size:.88rem;color:var(--text-regular);">National ID: {{ $idNumber }}</p>
                    </div>
                </div>
                <div style="display:grid;gap:10px;">
                    @foreach([['fa-circle-check','#0FA958','ID Validity Status','Confirms the National ID is valid and active in the Kenya IPRS registry.'],['fa-user','#6366f1','Full Registered Name','Returns first name, other name, and surname as registered with the government.'],['fa-fingerprint','#0ea5e9','Identity Match','Verifies the ID matches a real person, protecting against fraud.']] as $f)
                    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:{{ $f[1] }}18;color:{{ $f[1] }};display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;"><i class="fa-solid {{ $f[0] }}"></i></div>
                        <div><div style="font-size:.88rem;font-weight:600;color:var(--primary-navy);margin-bottom:2px;">{{ $f[2] }}</div><div style="font-size:.76rem;color:var(--text-light);">{{ $f[3] }}</div></div>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:20px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:13px 16px;font-size:.84rem;color:#166534;display:flex;gap:10px;align-items:center;">
                    <i class="fa-solid fa-shield-halved" style="color:#16a34a;flex-shrink:0;"></i>
                    Your result is delivered instantly after payment confirmation. Data is encrypted and used solely for this verification.
                </div>
            </div>

            <div class="order-card">
                <div class="order-card-head"><h3>Order Summary</h3><p>Review before you pay</p></div>
                <div class="order-card-body">
                    <div class="order-row"><span class="order-label">Service</span><span class="order-val">Identity Verification</span></div>
                   
                  
                    
                    <div class="order-row">
    <span class="order-label">Full Name</span>
    <span class="order-val" style="color:#9ca3af;display:flex;align-items:center;gap:6px;justify-content:flex-end;">
        <i class="fa-solid fa-lock" style="font-size:.72rem;"></i> Revealed after payment
    </span>
</div>
                    
                    
                    
                    <div class="order-row"><span class="order-label">National ID</span><span class="order-val">{{ $idNumber }}</span></div>
                    <div class="order-row"><span class="order-label">Report Type</span><span class="order-val">Kenya IPRS Registry Check</span></div>
                    <div class="order-row"><span class="order-label">Delivery</span><span class="order-val">Instant <span style="color:#16a34a;font-size:.78rem;">(after payment)</span></span></div>
                    <div class="order-total"><span class="order-total-label">Total Payable</span><span class="order-total-val">KES {{ number_format($price) }}</span></div>
                    <p style="font-size:.78rem;color:var(--text-light);line-height:1.6;margin-bottom:18px;"><i class="fa-solid fa-shield-halved" style="color:var(--primary-green);"></i> Instant identity verification via Kenya CRB &amp; IPRS.</p>
                    <div class="mpesa-section">
                        <label class="mpesa-label" for="mpesa_phone">M-Pesa Phone Number</label>
                        <div class="mpesa-input-wrap">
                            <span class="mpesa-prefix">+254</span>
                            <input type="tel" id="mpesa_phone" class="mpesa-input" placeholder="7XX XXX XXX" maxlength="9" pattern="[17][0-9]{8}" inputmode="numeric">
                        </div>
                        <p style="font-size:.76rem;color:var(--text-light);margin-top:5px;">You will receive an STK push to confirm payment.</p>
                    </div>
                    <div class="stk-pending" id="stkPending"><div class="stk-spinner"></div><p>STK Push Sent!</p><small>Check your phone and enter your M-Pesa PIN.</small></div>
                    <button type="button" class="unlock-btn" id="payBtn" onclick="initiateStk()"><i class="fa-solid fa-mobile-screen-button"></i> Pay KES {{ number_format($price) }} via M-Pesa</button>
                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:12px;"><span style="font-size:.78rem;color:#6b7280;">Secured by</span><span style="font-weight:800;color:#16a34a;font-size:.92rem;">M-PESA</span></div>
                    <p style="text-align:center;margin-top:14px;font-size:.85rem;"><a href="{{ route('identity-verification') }}" style="color:var(--primary-green);font-weight:600;"><i class="fa-solid fa-arrow-left"></i> Run a different check</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const ID_NUMBER = '{{ $idNumber }}';
const RECORD_ID = {{ (int)$recordId }};
let pollInterval = null;

function initiateStk() {
    const phoneInput = document.getElementById('mpesa_phone');
    const raw = phoneInput.value.replace(/\s/g,'');
    if (!/^[17][0-9]{8}$/.test(raw)) { phoneInput.style.borderColor='#ef4444'; phoneInput.focus(); return; }
    phoneInput.style.borderColor = '#16a34a';
    const btn = document.getElementById('payBtn'), pending = document.getElementById('stkPending');
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending STK Push&hellip;';
    pending.classList.add('show');
    fetch('{{ route('stk-push') }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({ phone:raw, amount:{{ (int)$price }}, service:'identity-verification', id:ID_NUMBER, record_id:RECORD_ID }),
    })
    .then(r => r.json())
    .then(data => { if (data.success) { pollPayment(data.request_id); } else { stkError(data.message || 'Payment request failed.'); } })
    .catch(() => stkError('Network error. Please try again.'));
}

function pollPayment(rid) {
    let attempts = 0, secs = 0;
    const pEl = document.getElementById('stkPending');
    const msgEl = pEl.querySelector('p'), subEl = pEl.querySelector('small');
    const tick = setInterval(() => {
        secs++;
        if (secs === 12) { msgEl.textContent = 'Processing Payment…'; subEl.textContent = 'M-Pesa is confirming. This takes 1–2 minutes — keep this page open.'; }
        if (secs >= 12) { subEl.textContent = 'Confirming with M-Pesa… ' + secs + 's'; }
        if (secs === 35) { subEl.innerHTML = 'Taking a moment. Already entered PIN? <a href="{{ route('identity-verification.result') }}?rid=' + rid + '" style="color:#16a34a;font-weight:700;">Check my result →</a>'; }
    }, 1000);
    pollInterval = setInterval(() => {
        attempts++;
        fetch('{{ route('check-payment-status') }}?rid=' + rid)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'completed' || data.status === 'paid' || data.status === 'processing') { clearInterval(tick); clearInterval(pollInterval); showSuccess(rid); }
                else if (data.status === 'payment_failed') { clearInterval(tick); clearInterval(pollInterval); stkError(data.payment_error || 'Payment not completed.'); }
                else if (attempts >= 100) { clearInterval(tick); clearInterval(pollInterval); stkTimeout(rid); }
            }).catch(() => {});
    }, 5000);
}

function showSuccess(rid) {
    const p = document.getElementById('stkPending');
    p.style.borderColor = '#4ade80';
    p.querySelector('.stk-spinner').style.display = 'none';
    p.querySelector('p').textContent = 'Payment Confirmed!';
    p.querySelector('small').textContent = 'Redirecting to your result…';
    setTimeout(() => { window.location.href = '{{ route('identity-verification.result') }}?rid=' + rid; }, 1200);
}

function stkTimeout(rid) {
    const p = document.getElementById('stkPending'), btn = document.getElementById('payBtn');
    p.style.background = '#fffbeb'; p.style.borderColor = '#fcd34d';
    p.querySelector('.stk-spinner').style.display = 'none';
    p.querySelector('p').textContent = 'Taking longer than usual…';
    p.querySelector('small').innerHTML = 'If you already paid, <a href="{{ route('identity-verification.result') }}?rid=' + rid + '" style="color:#16a34a;font-weight:700;">click here to view your result</a> — or we\'ll keep checking.';
    btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-mobile-screen-button"></i> Pay KES {{ number_format($price) }} via M-Pesa';
    pollInterval = setInterval(() => { fetch('{{ route('check-payment-status') }}?rid=' + rid).then(r => r.json()).then(data => { if (data.status === 'completed' || data.status === 'paid' || data.status === 'processing') { clearInterval(pollInterval); showSuccess(rid); } }); }, 10000);
}

function stkError(msg) {
    clearInterval(pollInterval);
    const p = document.getElementById('stkPending'), btn = document.getElementById('payBtn');
    p.classList.remove('show'); btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-mobile-screen-button"></i> Pay KES {{ number_format($price) }} via M-Pesa';
    let err = document.getElementById('stkErr');
    if (!err) { err=document.createElement('p'); err.id='stkErr'; err.style.cssText='color:#dc2626;font-size:.82rem;margin:8px 0 0;text-align:center;'; btn.parentNode.insertBefore(err, btn.nextSibling); }
    err.textContent = msg;
}
document.getElementById('mpesa_phone').addEventListener('input', function() { this.style.borderColor=''; const e=document.getElementById('stkErr'); if(e) e.textContent=''; });
</script>
@endpush
