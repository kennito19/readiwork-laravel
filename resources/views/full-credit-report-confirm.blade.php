@extends('layouts.app')
@section('title', 'Confirm Full Credit Report - Readiwork')

@push('head')
<style>
.confirm-hero {
    background: linear-gradient(135deg, #071629 0%, #0d2649 55%, #091e3a 100%);
    padding: 48px 0 40px;
    color: white;
}
.confirm-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(15,169,88,0.15);
    color: #6ee7a8;
    border: 1px solid rgba(15,169,88,0.3);
    border-radius: 999px;
    padding: 5px 14px;
    font-size: .82rem;
    font-weight: 600;
    margin-bottom: 12px;
}
.confirm-hero h1 {
    font-size: 2.1rem;
    font-weight: 800;
    margin-bottom: 8px;
    line-height: 1.2;
}
.confirm-hero p {
    color: rgba(255,255,255,0.7);
    font-size: 1rem;
    max-width: 580px;
}
.confirm-main {
    padding: 48px 0 80px;
    background: var(--bg-light);
}
.confirm-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 32px;
    align-items: start;
}
.info-card-l {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 6px 24px rgba(11,31,59,0.06);
}
.order-card {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 24px rgba(11,31,59,0.06);
}
.order-card-head {
    background: var(--primary-navy);
    padding: 20px 24px;
    color: white;
}
.order-card-head h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 4px;
}
.order-card-head p {
    font-size: .85rem;
    opacity: 0.7;
}
.order-card-body {
    padding: 20px 24px;
}
.order-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
    font-size: .9rem;
}
.order-row:last-child {
    border-bottom: none;
}
.order-label {
    color: var(--text-light);
    font-weight: 500;
}
.order-val {
    color: var(--text-dark);
    font-weight: 600;
    text-align: right;
}
.order-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0 16px;
    border-top: 2px solid var(--border-color);
    margin-top: 8px;
}
.order-total-label {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-navy);
}
.order-total-val {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--primary-green);
}
.mpesa-section {
    margin: 24px 0 16px;
}
.mpesa-label {
    display: block;
    font-size: .9rem;
    font-weight: 700;
    color: var(--primary-navy);
    margin-bottom: 8px;
}
.mpesa-input-wrap {
    position: relative;
}
.mpesa-prefix {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-weight: 700;
    color: #16a34a;
    font-size: 1rem;
}
.mpesa-input {
    width: 100%;
    padding: 14px 14px 14px 60px;
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    font-size: 1rem;
    transition: border-color .2s;
    box-sizing: border-box;
}
.mpesa-input:focus {
    border-color: #16a34a;
    outline: none;
}
.stk-pending {
    display: none;
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    margin: 16px 0;
}
.stk-pending.show {
    display: block;
}
.stk-spinner {
    width: 44px;
    height: 44px;
    border: 4px solid #bbf7d0;
    border-top-color: #16a34a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 12px;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
.stk-pending p {
    font-weight: 600;
    color: #166534;
    margin-bottom: 6px;
}
.stk-pending small {
    color: #4ade80;
    font-size: .85rem;
}
.unlock-btn {
    width: 100%;
    background: #16a34a;
    color: white;
    border: none;
    border-radius: 12px;
    padding: 16px;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all .2s;
}
.unlock-btn:hover:not(:disabled) {
    background: #15803d;
    transform: translateY(-1px);
}
.unlock-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}
@media (max-width: 980px) {
    .confirm-layout { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<section class="confirm-hero">
    <div class="container text-center text-md-start">
        <div class="confirm-hero-eyebrow">
            <i class="fa-solid fa-file-lines"></i> Full Credit Report &nbsp;·&nbsp; Step 2 of 2
        </div>
        <h1>Confirm &amp; Pay for Your Report</h1>
        <p>Review the details below and complete payment via M-Pesa to instantly receive your full credit report from Metropol CRB.</p>
    </div>
</section>

<section class="confirm-main">
    <div class="container">
        <div class="confirm-layout">
            <!-- Left: What you get -->
            <div class="info-card-l">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:64px;height:64px;border-radius:14px;background:linear-gradient(135deg,#0FA958,#16a34a);color:white;font-size:1.8rem;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div>
                        <h2 style="font-size:1.35rem;font-weight:800;color:var(--primary-navy);margin-bottom:4px;">Full Enhanced Credit Report</h2>
                        <p style="color:var(--text-regular);font-size:.95rem;">National ID: <strong>{{ $idNumber }}</strong></p>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach([
                        ['fa-id-card',          '#0FA958', 'Verified Identity',       'Full name, DOB, gender & registration details from IPRS & CRB'],
                        ['fa-gauge-high',        '#6366f1', 'Credit Score + PPI',      'Current score (200–900) and 12-month Payment Performance trend'],
                        ['fa-building-columns',  '#0ea5e9', 'All Credit Accounts',     'Complete list — active, closed, balances, arrears & history'],
                        ['fa-chart-line',        '#d97706', 'Score & PPI Trend',       'Last 12 months Metro Score & PPI movement'],
                        ['fa-chart-bar',         '#16a34a', 'Sector Breakdown',        'Exposure across banks, MFIs, SACCOs & digital lenders'],
                        ['fa-shield-halved',     '#dc2626', 'Enquiries & Defaults',    'Recent checks, applications, bounced cheques & listings'],
                    ] as $item)
                    <div class="col-12">
                        <div class="d-flex align-items-start gap-3 p-3 bg-light border rounded-3">
                            <div style="width:44px;height:44px;border-radius:10px;background:{{ $item[1] }}20;color:{{ $item[1] }};display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                                <i class="fa-solid {{ $item[0] }}"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;color:var(--primary-navy);">{{ $item[2] }}</div>
                                <div style="font-size:.85rem;color:var(--text-light);">{{ $item[3] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 bg-success-subtle border border-success-subtle rounded-3 d-flex align-items-center gap-3">
                    <i class="fa-solid fa-lock fa-lg" style="color:#16a34a;"></i>
                    <div style="font-size:.9rem;color:#166534;">
                        Your report is encrypted and delivered instantly after payment. Data sourced directly from Metropol CRB.
                    </div>
                </div>
            </div>

            <!-- Right: Order & Payment -->
            <div class="order-card">
                <div class="order-card-head">
                    <h3>Order Summary</h3>
                    <p>Review &amp; pay securely</p>
                </div>
                <div class="order-card-body">
                    <div class="order-row"><span class="order-label">Service</span><span class="order-val">Full Enhanced Credit Report</span></div>
                    <div class="order-row"><span class="order-label">National ID</span><span class="order-val">{{ $idNumber }}</span></div>
                    <div class="order-row"><span class="order-label">Provider</span><span class="order-val">Metropol CRB</span></div>
                    <div class="order-row"><span class="order-label">Delivery</span><span class="order-val">Instant after payment</span></div>
                    <div class="order-row"><span class="order-label">Format</span><span class="order-val">Detailed Digital Report</span></div>

                    <div class="order-total">
                        <span class="order-total-label">Total to Pay</span>
                        <span class="order-total-val">KES {{ number_format($price) }}</span>
                    </div>

                    <div class="mpesa-section">
                        <label class="mpesa-label" for="mpesa_phone">Your M-Pesa Number</label>
                        <div class="mpesa-input-wrap">
                            <span class="mpesa-prefix">+254</span>
                            <input type="tel" id="mpesa_phone" class="mpesa-input" placeholder="7XX XXX XXX" maxlength="9" pattern="[17][0-9]{8}" inputmode="numeric" required>
                        </div>
                        <small class="d-block mt-2 text-muted">You'll receive an STK push. Enter your PIN to confirm.</small>
                    </div>

                    <div class="stk-pending" id="stkPending">
                        <div class="stk-spinner"></div>
                        <p>STK Push Sent</p>
                        <small>Please complete payment on your phone</small>
                    </div>

                    <button type="button" class="unlock-btn" id="payBtn" onclick="initiateStk()">
                        <i class="fa-solid fa-mobile-screen-button"></i> Pay KES {{ number_format($price) }}
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted">Secured by <strong style="color:#16a34a;">M-PESA</strong></small>
                    </div>

                    <p class="text-center mt-4">
                        <a href="{{ route('full-credit-report') }}" class="text-decoration-none" style="color:var(--primary-green);">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to start
                        </a>
                    </p>
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
const PRICE     = {{ (int)$price }};
let pollInterval = null;

function initiateStk() {
    const phoneInput = document.getElementById('mpesa_phone');
    const raw = phoneInput.value.trim().replace(/\s/g, '');

    if (!/^[17][0-9]{8}$/.test(raw)) {
        phoneInput.style.borderColor = '#ef4444';
        phoneInput.focus();
        return;
    }

    phoneInput.style.borderColor = '#16a34a';

    const btn     = document.getElementById('payBtn');
    const pending = document.getElementById('stkPending');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending STK Push\u2026';
    pending.classList.add('show');

    fetch('{{ route('stk-push') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ phone: raw, amount: PRICE, service: 'full-credit-report', id: ID_NUMBER, record_id: RECORD_ID }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { pollPayment(data.request_id); }
        else              { stkError(data.message || 'Failed to initiate payment.'); }
    })
    .catch(() => stkError('Network error. Please check your connection.'));
}

function pollPayment(stkRequestId) {
    let attempts = 0;
    pollInterval = setInterval(() => {
        attempts++;
        fetch('{{ route('check-payment-status') }}?rid=' + stkRequestId)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'completed' || data.status === 'paid') {
                    clearInterval(pollInterval);
                    showSuccess();
                } else if (data.status === 'payment_failed') {
                    clearInterval(pollInterval);
                    stkError(data.payment_error || 'Payment was not completed.');
                } else if (attempts >= 40) {
                    clearInterval(pollInterval);
                    stkError('Payment timed out. Please try again.');
                }
            })
            .catch(() => {});
    }, 3000);
}

function showSuccess() {
    const pending = document.getElementById('stkPending');
    pending.style.borderColor = '#4ade80';
    pending.querySelector('.stk-spinner').style.display = 'none';
    pending.querySelector('p').textContent = 'Payment Successful!';
    pending.querySelector('small').textContent = 'Loading your full credit report\u2026';
    setTimeout(() => {
        window.location.href = '{{ route('full-credit-report.result') }}?rid=' + RECORD_ID;
    }, 1500);
}

function stkError(message) {
    clearInterval(pollInterval);
    const pending = document.getElementById('stkPending');
    const btn     = document.getElementById('payBtn');
    pending.classList.remove('show');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-mobile-screen-button"></i> Pay KES ' + PRICE.toLocaleString();
    let errEl = document.getElementById('stkErr');
    if (!errEl) {
        errEl = document.createElement('p');
        errEl.id = 'stkErr';
        errEl.style.cssText = 'color:#dc2626;font-size:.9rem;margin:12px 0 0;text-align:center;font-weight:500;';
        btn.parentNode.insertBefore(errEl, btn.nextSibling);
    }
    errEl.textContent = message;
}

document.getElementById('mpesa_phone').addEventListener('input', function() {
    this.style.borderColor = '';
    const err = document.getElementById('stkErr');
    if (err) err.textContent = '';
});
</script>
@endpush
