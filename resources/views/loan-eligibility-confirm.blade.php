@extends('layouts.app')
@section('title', 'Confirm Loan Eligibility - Readiwork')

@push('head')
<style>
.confirm-hero { background: linear-gradient(135deg, #071629 0%, #0d2649 55%, #091e3a 100%); padding: 40px 0 36px; }
.confirm-hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; background: rgba(15,169,88,0.15); color: #6ee7a8; border: 1px solid rgba(15,169,88,0.3); border-radius: 999px; padding: 4px 14px; font-size: 0.8rem; font-weight: 600; margin-bottom: 12px; }
.confirm-hero h1 { font-size: 1.9rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
.confirm-hero p  { color: rgba(255,255,255,0.6); font-size: 0.95rem; }
.confirm-main { padding: 40px 0 64px; background: var(--bg-light); }
.confirm-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
.elig-card { background: #fff; border: 1px solid var(--border-color); border-radius: 18px; overflow: hidden; box-shadow: 0 4px 24px rgba(11,31,59,0.07); }
.elig-card-top { background: linear-gradient(135deg, #052e16 0%, #064e1d 100%); padding: 32px 28px 24px; text-align: center; }
.elig-emoji { font-size: 2.8rem; margin-bottom: 10px; display: block; }
.elig-congrats { font-size: 1.35rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
.elig-sub { color: rgba(255,255,255,0.65); font-size: 0.9rem; margin-bottom: 20px; }
.elig-amount-label { color: rgba(255,255,255,0.5); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.elig-amount { font-size: 3rem; font-weight: 800; color: #4ade80; line-height: 1; margin-bottom: 16px; letter-spacing: -1px; }
.elig-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(74,222,128,0.18); color: #4ade80; border: 1px solid rgba(74,222,128,0.35); border-radius: 999px; padding: 6px 18px; font-size: 0.85rem; font-weight: 700; }
.elig-card-body { padding: 24px 28px; }
.locked-title { font-size: 0.82rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px; }
.locked-list { display: grid; gap: 10px; margin-bottom: 20px; }
.locked-item { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 10px; }
.locked-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(15,169,88,0.1); color: var(--primary-green); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; }
.locked-item span { font-size: 0.88rem; color: var(--text-dark); font-weight: 500; flex: 1; }
.locked-item .lock-icon { color: #d1d5db; font-size: 0.75rem; }
.locked-cta-note { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 13px 16px; font-size: 0.85rem; color: #166534; line-height: 1.6; display: flex; gap: 10px; align-items: flex-start; }
.locked-cta-note i { color: #16a34a; margin-top: 2px; flex-shrink: 0; }
.order-card { background: #fff; border: 1px solid var(--border-color); border-radius: 18px; overflow: hidden; box-shadow: 0 4px 24px rgba(11,31,59,0.07); }
.order-card-head { background: var(--primary-navy); padding: 20px 24px; }
.order-card-head h3 { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 2px; }
.order-card-head p  { font-size: 0.82rem; color: rgba(255,255,255,0.5); }
.order-card-body { padding: 20px 24px; }
.order-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 11px 0; border-bottom: 1px solid var(--border-color); font-size: 0.88rem; gap: 12px; }
.order-row:last-of-type { border-bottom: 0; }
.order-label { color: var(--text-light); font-weight: 500; white-space: nowrap; }
.order-val   { color: var(--text-dark);  font-weight: 600; text-align: right; }
.order-total { display: flex; justify-content: space-between; align-items: center; padding: 16px 0 12px; border-top: 2px solid var(--border-color); margin-top: 4px; }
.order-total-label { font-size: 1rem; font-weight: 700; color: var(--primary-navy); }
.order-total-val   { font-size: 1.45rem; font-weight: 800; color: var(--primary-green); }
.order-note { font-size: 0.78rem; color: var(--text-light); line-height: 1.6; margin-bottom: 18px; }
.order-note i { margin-right: 4px; color: var(--primary-green); }
.mpesa-section { margin-bottom: 16px; }
.mpesa-label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 8px; }
.mpesa-input-wrap { position: relative; }
.mpesa-prefix { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.9rem; font-weight: 700; color: #16a34a; }
.mpesa-input { width: 100%; padding: 13px 14px 13px 52px; border: 1.5px solid var(--border-color); border-radius: 10px; font-size: 0.98rem; font-family: inherit; color: var(--text-dark); outline: none; transition: border-color 0.2s; box-sizing: border-box; }
.mpesa-input:focus { border-color: #16a34a; }
.mpesa-hint { font-size: 0.76rem; color: var(--text-light); margin-top: 5px; }
.stk-pending { display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 18px 16px; text-align: center; margin-bottom: 14px; }
.stk-pending.show { display: block; }
.stk-spinner { width: 40px; height: 40px; border: 3px solid #bbf7d0; border-top-color: #16a34a; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }
.stk-pending p { font-size: 0.88rem; color: #166534; font-weight: 600; margin-bottom: 4px; }
.stk-pending small { font-size: 0.78rem; color: #4ade80; }
.unlock-btn { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; background: #16a34a; color: #fff; border: none; border-radius: 12px; padding: 16px 20px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: background 0.2s, transform 0.15s; text-decoration: none; }
.unlock-btn:hover { background: #15803d; transform: translateY(-1px); }
.unlock-btn:disabled { background: #6b7280; cursor: not-allowed; transform: none; }
.mpesa-logo-row { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 12px; margin-bottom: 4px; }
.back-link { display: block; text-align: center; margin-top: 14px; font-size: 0.85rem; color: var(--text-light); }
.back-link a { color: var(--primary-green); font-weight: 600; }
@media (max-width: 900px) { .confirm-layout { grid-template-columns: 1fr; } .confirm-hero h1 { font-size: 1.55rem; } .elig-amount { font-size: 2.4rem; } }
@media (max-width: 500px) { .elig-card-top { padding: 24px 20px 20px; } .elig-card-body { padding: 20px; } .order-card-body { padding: 16px 18px; } .order-card-head { padding: 16px 18px; } .elig-card-top [style*="repeat(3,1fr)"] { grid-template-columns: 1fr !important; } }
</style>
@endpush

@section('content')
<section class="confirm-hero">
    <div class="container">
        <div class="confirm-hero-eyebrow"><i class="fa-solid fa-circle-check"></i> Step 2 of 2 &nbsp;·&nbsp; Identity Verified</div>
        <h1>Welcome, {{ $demoName }}</h1>
        <p>Review your pre-qualification details below before unlocking your eligibility report.</p>
    </div>
</section>

<section class="confirm-main">
    <div class="container">
        <div class="confirm-layout">
            <!-- LEFT: Eligibility teaser -->
            <div class="elig-card">
                <div class="elig-card-top">
                    <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(74,222,128,.12);border:1px solid rgba(74,222,128,.3);border-radius:999px;padding:4px 12px;font-size:.76rem;font-weight:600;color:#4ade80;margin-bottom:12px;"><i class="fa-solid fa-circle-check"></i> Identity Verified</div>
                    <div class="elig-congrats">Congratulations, {{ $demoName }}!</div>
                    <div class="elig-sub">Your National ID <strong style="color:rgba(255,255,255,.8)">{{ $idNumber }}</strong> has been successfully verified. Based on your credit profile, you are pre-approved for:</div>
                    <div class="elig-amount-label">Maximum Pre-Qualified Loan Amount</div>
                    <div class="elig-amount">KES {{ number_format($demoAmount) }}</div>
                    <div style="font-size:.78rem;color:rgba(255,255,255,.45);margin-bottom:12px;">This offer is reserved for you — unlock your eligibility report to claim it</div>
                    <div class="elig-badge"><i class="fa-solid fa-lock-open"></i> UNLOCK YOUR ELIGIBILITY REPORT FOR KES 1</div>
                    <div style="margin-top:16px;display:grid;grid-template-columns:repeat(3,1fr);gap:10px;text-align:center;">
                        <div style="background:rgba(255,255,255,.06);border-radius:10px;padding:10px 6px;"><div style="font-size:1.1rem;font-weight:800;color:#4ade80;">5+</div><div style="font-size:.68rem;color:rgba(255,255,255,.45);margin-top:2px;">Lenders Ready</div></div>
                        <div style="background:rgba(255,255,255,.06);border-radius:10px;padding:10px 6px;"><div style="font-size:1.1rem;font-weight:800;color:#4ade80;">&lt;5s</div><div style="font-size:.68rem;color:rgba(255,255,255,.45);margin-top:2px;">Report Delivery</div></div>
                        <div style="background:rgba(255,255,255,.06);border-radius:10px;padding:10px 6px;"><div style="font-size:1.1rem;font-weight:800;color:#4ade80;">CRB</div><div style="font-size:.68rem;color:rgba(255,255,255,.45);margin-top:2px;">Verified Data</div></div>
                    </div>
                </div>
                <div class="elig-card-body">
                    <div class="locked-title">What's included in your eligibility report</div>
                    <div class="locked-list">
                        <div class="locked-item"><div class="locked-icon" style="background:rgba(99,102,241,0.12);color:#6366f1;"><i class="fa-solid fa-gauge-high"></i></div><div style="flex:1;"><div style="font-size:.88rem;font-weight:600;color:var(--primary-navy);">Instant Approval Probability Score</div><div style="font-size:.76rem;color:var(--text-light);margin-top:2px;">AI-computed likelihood of loan approval</div></div><i class="fa-solid fa-lock lock-icon"></i></div>
                        <div class="locked-item"><div class="locked-icon" style="background:rgba(14,165,233,0.12);color:#0ea5e9;"><i class="fa-solid fa-building-columns"></i></div><div style="flex:1;"><div style="font-size:.88rem;font-weight:600;color:var(--primary-navy);">5+ Pre-Matched Lenders Ready to Approve</div><div style="font-size:.76rem;color:var(--text-light);margin-top:2px;">Matched to your profile &amp; credit score</div></div><i class="fa-solid fa-lock lock-icon"></i></div>
                        <div class="locked-item"><div class="locked-icon" style="background:rgba(245,158,11,0.12);color:#d97706;"><i class="fa-solid fa-percent"></i></div><div style="flex:1;"><div style="font-size:.88rem;font-weight:600;color:var(--primary-navy);">Personalized Interest Rates &amp; Plans</div><div style="font-size:.76rem;color:var(--text-light);margin-top:2px;">Tailored repayment schedules for your income</div></div><i class="fa-solid fa-lock lock-icon"></i></div>
                        <div class="locked-item"><div class="locked-icon" style="background:rgba(15,169,88,0.12);color:#0FA958;"><i class="fa-solid fa-arrow-trend-up"></i></div><div style="flex:1;"><div style="font-size:.88rem;font-weight:600;color:var(--primary-navy);">Tips to Increase Your Eligible Amount</div><div style="font-size:.76rem;color:var(--text-light);margin-top:2px;">Actionable steps to boost your credit score</div></div><i class="fa-solid fa-lock lock-icon"></i></div>
                    </div>
                    <div class="locked-cta-note"><i class="fa-solid fa-circle-info"></i><span>Pay <strong>KES 1</strong> once to unlock your complete eligibility report — your Credit Score, all lenders who can approve you today, and a step-by-step plan to borrow up to <strong>KES {{ number_format($demoAmount) }}</strong>.</span></div>
                    <div style="margin-top:14px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:11px 14px;font-size:.82rem;color:#92400e;display:flex;gap:8px;align-items:center;"><i class="fa-solid fa-clock" style="color:#d97706;flex-shrink:0;"></i>Your pre-qualification quote of <strong>KES {{ number_format($demoAmount) }}</strong> is saved. Complete payment now to lock in this amount.</div>
                    <div style="display:flex;flex-wrap:wrap;gap:14px;margin-top:18px;padding-top:16px;border-top:1px solid var(--border-color);">
                        <span style="display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--text-light);"><i class="fa-solid fa-shield-halved" style="color:#16a34a;"></i> CRB-verified data</span>
                        <span style="display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--text-light);"><i class="fa-solid fa-bolt" style="color:#6366f1;"></i> Instant results</span>
                        <span style="display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--text-light);"><i class="fa-solid fa-lock" style="color:#0ea5e9;"></i> Encrypted &amp; private</span>
                        <span style="display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--text-light);"><i class="fa-solid fa-rotate-left" style="color:#d97706;"></i> One-time payment</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Order summary -->
            <div class="order-card">
                <div class="order-card-head"><h3>Order Summary</h3><p>Review before you pay</p></div>
                <div class="order-card-body">
                    <div class="order-row"><span class="order-label">Service</span><span class="order-val">Loan Eligibility Check AI</span></div>
                    <div class="order-row"><span class="order-label">Full Name</span><span class="order-val">{{ $demoName }}</span></div>
                    <div class="order-row"><span class="order-label">National ID</span><span class="order-val">{{ $idNumber }}</span></div>
                    <div class="order-row"><span class="order-label">Report Type</span><span class="order-val">Loan Eligibility Assessment</span></div>
                    <div class="order-row"><span class="order-label">Delivery</span><span class="order-val">Instant <span style="color:#16a34a; font-size:0.78rem;">(after payment)</span></span></div>
                    <div class="order-total"><span class="order-total-label">Total Payable</span><span class="order-total-val">KES 299</span></div>
                    <p class="order-note"><i class="fa-solid fa-shield-halved"></i> AI-powered loan eligibility and risk assessment.<br><i class="fa-solid fa-lock"></i> Confidential and instant delivery after payment.</p>
                    <div class="mpesa-section">
                        <label class="mpesa-label" for="mpesa_phone">M-Pesa Phone Number</label>
                        <div class="mpesa-input-wrap">
                            <span class="mpesa-prefix">+254</span>
                            <input type="tel" id="mpesa_phone" class="mpesa-input" placeholder="7XX XXX XXX" maxlength="9" pattern="[17][0-9]{8}" inputmode="numeric" autocomplete="tel">
                        </div>
                        <p class="mpesa-hint">Enter the number registered with M-Pesa. You will receive an STK push to confirm payment.</p>
                    </div>
                    <div class="stk-pending" id="stkPending"><div class="stk-spinner"></div><p>STK Push Sent!</p><small>Check your phone and enter your M-Pesa PIN to complete payment.</small></div>
                    <button type="button" class="unlock-btn" id="payBtn" onclick="initiateStk()"><i class="fa-solid fa-mobile-screen-button"></i> Pay KES 1 via M-Pesa</button>
                    <div class="mpesa-logo-row"><span style="font-size:0.78rem; color:#6b7280;">Secured by</span><span style="font-weight:800; color:#16a34a; font-size:0.92rem; letter-spacing:-0.3px;">M-PESA</span><span style="font-size:0.78rem; color:#6b7280;">&middot; Safaricom</span></div>
                    <p class="back-link"><a href="{{ route('loan-eligibility') }}"><i class="fa-solid fa-arrow-left"></i> Run a different check</a></p>
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
    const raw = phoneInput.value.replace(/\s/g, '');
    if (!/^[17][0-9]{8}$/.test(raw)) {
        phoneInput.style.borderColor = '#ef4444'; phoneInput.focus();
        phoneInput.placeholder = 'Enter a valid number e.g. 712345678'; return;
    }
    phoneInput.style.borderColor = '#16a34a';
    const btn = document.getElementById('payBtn'), pending = document.getElementById('stkPending');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending STK Push&hellip;';
    pending.classList.add('show');
    fetch('{{ route('stk-push') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ phone: raw, amount: 1, service: 'loan-eligibility', id: ID_NUMBER, record_id: RECORD_ID }),
    })
    .then(r => r.json())
    .then(data => { if (data.success) { pollPayment(data.request_id); } else { stkError(data.message || 'Payment request failed. Please try again.'); } })
    .catch(() => stkError('Network error. Please check your connection and try again.'));
}

function pollPayment(stkRequestId) {
    let attempts = 0;
    pollInterval = setInterval(() => {
        attempts++;
        fetch('{{ route('check-payment-status') }}?rid=' + stkRequestId)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'completed' || data.status === 'paid') { clearInterval(pollInterval); showSuccess(); }
                else if (data.status === 'payment_failed') { clearInterval(pollInterval); stkError(data.payment_error || 'Payment was not completed.'); }
                else if (attempts >= 36) { clearInterval(pollInterval); stkError('Payment timed out. Please try again or contact support.'); }
            })
            .catch(() => {});
    }, 3000);
}

function showSuccess() {
    const pending = document.getElementById('stkPending');
    pending.style.borderColor = '#4ade80';
    pending.querySelector('.stk-spinner').style.display = 'none';
    pending.querySelector('p').textContent = 'Payment Confirmed!';
    pending.querySelector('small').textContent = 'Redirecting to your report\u2026';
    setTimeout(() => { window.location.href = '{{ route('loan-eligibility.result') }}?rid=' + RECORD_ID; }, 1200);
}

function stkError(msg) {
    clearInterval(pollInterval);
    const pending = document.getElementById('stkPending'), btn = document.getElementById('payBtn');
    pending.classList.remove('show'); btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-mobile-screen-button"></i> Pay KES 1 via M-Pesa';
    let err = document.getElementById('stkErr');
    if (!err) { err = document.createElement('p'); err.id = 'stkErr'; err.style.cssText = 'color:#dc2626;font-size:0.82rem;margin:8px 0 0;text-align:center;'; btn.parentNode.insertBefore(err, btn.nextSibling); }
    err.textContent = msg;
}
document.getElementById('mpesa_phone').addEventListener('input', function() { this.style.borderColor = ''; const err = document.getElementById('stkErr'); if (err) err.textContent = ''; });
</script>
@endpush
