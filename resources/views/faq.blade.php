@extends('layouts.app')
@section('title', 'FAQ - Readiwork')

@push('head')
<style>
.faq-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 60%,#0a1f40 100%);padding:72px 0 56px;text-align:center;}
.faq-hero h1{font-size:clamp(2rem,4vw,2.8rem);font-weight:800;color:#fff;margin-bottom:14px;}
.faq-hero p{font-size:1.08rem;color:rgba(255,255,255,.65);max-width:560px;margin:0 auto 28px;}
.faq-search-wrap{max-width:480px;margin:0 auto;position:relative;}
.faq-search-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;}
.faq-search-input{width:100%;padding:14px 16px 14px 42px;border-radius:12px;border:none;font-size:.97rem;font-family:inherit;outline:none;box-shadow:0 4px 20px rgba(0,0,0,.18);}
.faq-tabs{display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin:36px 0 40px;}
.faq-tab{padding:8px 20px;border-radius:999px;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .15s;}
.faq-tab.active,.faq-tab:hover{background:#0FA958;border-color:#0FA958;color:#fff;}
.faq-section{margin-bottom:48px;}
.faq-section-title{font-size:1rem;font-weight:700;color:#0B1F3B;margin-bottom:16px;display:flex;align-items:center;gap:9px;}
.faq-section-title i{color:#0FA958;}
.faq-item{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:10px;overflow:hidden;transition:box-shadow .15s;}
.faq-item:hover{box-shadow:0 4px 16px rgba(0,0,0,.07);}
.faq-q{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;cursor:pointer;gap:12px;}
.faq-q span{font-weight:600;color:#0B1F3B;font-size:.92rem;flex:1;}
.faq-q i{color:#0FA958;font-size:.8rem;flex-shrink:0;transition:transform .2s;}
.faq-item.open .faq-q i{transform:rotate(180deg);}
.faq-a{display:none;padding:14px 20px 18px;color:#475569;font-size:.88rem;line-height:1.7;border-top:1px solid #f1f5f9;}
.faq-item.open .faq-a{display:block;}
.faq-cta{background:linear-gradient(135deg,#071629,#0d3460);border-radius:20px;padding:48px 36px;text-align:center;margin:48px 0;}
.faq-cta h2{color:#fff;font-size:1.5rem;font-weight:700;margin-bottom:10px;}
.faq-cta p{color:rgba(255,255,255,.65);margin-bottom:24px;}
.faq-cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
.faq-cta-btns a{padding:12px 28px;border-radius:10px;font-weight:700;font-size:.92rem;text-decoration:none;}
.faq-cta-btns .btn-g{background:#0FA958;color:#fff;}
.faq-cta-btns .btn-o{background:transparent;border:1.5px solid rgba(255,255,255,.35);color:#fff;}
.faq-cta-btns .btn-g:hover{background:#0d9048;}
.faq-no-results{text-align:center;padding:60px 20px;color:#94a3b8;display:none;}
.faq-no-results i{font-size:2.5rem;margin-bottom:14px;display:block;}
</style>
@endpush

@section('content')
<section class="faq-hero">
    <div class="container">
        <h1>Frequently Asked Questions</h1>
        <p>Everything you need to know about credit checks, pricing, and how Readiwork works.</p>
        <div class="faq-search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" class="faq-search-input" id="faqSearch" placeholder="Search questions…" oninput="filterFAQ(this.value)">
        </div>
    </div>
</section>

<section style="padding:48px 0 64px;">
    <div class="container">
        <div class="faq-tabs" id="faqTabs">
            <button class="faq-tab active" onclick="filterByCategory('all',this)">All</button>
            <button class="faq-tab" onclick="filterByCategory('general',this)">General</button>
            <button class="faq-tab" onclick="filterByCategory('services',this)">Services</button>
            <button class="faq-tab" onclick="filterByCategory('pricing',this)">Pricing</button>
            <button class="faq-tab" onclick="filterByCategory('privacy',this)">Privacy &amp; Data</button>
            <button class="faq-tab" onclick="filterByCategory('api',this)">API &amp; Business</button>
        </div>

        <div class="faq-section" data-cat="general">
            <div class="faq-section-title"><i class="fa-solid fa-circle-info"></i> General</div>
            <div class="faq-item" data-cat="general"><div class="faq-q" onclick="toggleFAQ(this)"><span>What is Readiwork?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Readiwork is a Kenyan credit-data platform that lets individuals and lenders instantly verify identities, check CRB blacklist status, get credit scores, and download full credit reports — all powered by the Metropol Credit Reference Bureau. No paperwork. No queues. Results in seconds.</div></div>
            <div class="faq-item" data-cat="general"><div class="faq-q" onclick="toggleFAQ(this)"><span>Who can use Readiwork?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Anyone with a Kenyan National ID can run a self-check. Businesses and lenders can integrate via our API to automate checks for their customers. You need a valid National ID number and an M-Pesa number for payment.</div></div>
            <div class="faq-item" data-cat="general"><div class="faq-q" onclick="toggleFAQ(this)"><span>How fast are the results?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Most checks complete within 5–15 seconds of a successful M-Pesa payment. The data comes directly from Metropol CRB in real time, so results are always up to date. You'll see your results on screen and can download a PDF report immediately.</div></div>
            <div class="faq-item" data-cat="general"><div class="faq-q" onclick="toggleFAQ(this)"><span>Does Readiwork work on mobile?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Yes — Readiwork is fully mobile-responsive and works on any smartphone browser. M-Pesa STK Push is sent directly to your phone, so the entire process is seamless on mobile.</div></div>
            <div class="faq-item" data-cat="general"><div class="faq-q" onclick="toggleFAQ(this)"><span>Is Readiwork affiliated with Metropol CRB?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Readiwork is an authorised data reseller that accesses Metropol CRB data through their official API. We are not directly part of Metropol but use their licensed data infrastructure to power our credit checks.</div></div>
        </div>

        <div class="faq-section" data-cat="services">
            <div class="faq-section-title"><i class="fa-solid fa-bolt"></i> Services</div>
            <div class="faq-item" data-cat="services"><div class="faq-q" onclick="toggleFAQ(this)"><span>What is an Identity Verification check?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Identity Verification confirms that a National ID number belongs to a real person, matching their full name and date of birth against the government registry through Metropol's database. Lenders use this to prevent fraud before disbursing loans.</div></div>
            <div class="faq-item" data-cat="services"><div class="faq-q" onclick="toggleFAQ(this)"><span>What does the CRB Blacklist Check tell me?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">The CRB Blacklist Check tells you whether a person is listed for unpaid loans at the Credit Reference Bureau. You'll get a clear Yes/No answer plus the outstanding loan amount if listed. This is used by lenders as a first-pass screening step.</div></div>
            <div class="faq-item" data-cat="services"><div class="faq-q" onclick="toggleFAQ(this)"><span>What is a Credit Score and how is it calculated?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">A credit score (200–900 scale) is a number representing your creditworthiness. It is computed by Metropol CRB based on your loan repayment history, number of active accounts, outstanding balances, length of credit history, and delinquency records. A higher score means lower credit risk. Scores above 700 are generally considered good.</div></div>
            <div class="faq-item" data-cat="services"><div class="faq-q" onclick="toggleFAQ(this)"><span>What is a Full Credit Report?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">A Full Credit Report includes your complete loan history — all accounts (active and closed), lenders, outstanding balances, overdue amounts, guarantor details, and sector performance. It is available as a formatted PDF or a structured JSON response for API users.</div></div>
            <div class="faq-item" data-cat="services"><div class="faq-q" onclick="toggleFAQ(this)"><span>What is the Loan Eligibility Check?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">The Loan Eligibility Check bundles Identity Verification and CRB Blacklist Check into a single API call, giving you one combined answer: "Eligible" or "Not Eligible" for a loan. It is the fastest and most cost-effective way for lenders to screen applicants.</div></div>
            <div class="faq-item" data-cat="services"><div class="faq-q" onclick="toggleFAQ(this)"><span>Can I check someone else's credit with their permission?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Yes — lenders and businesses may check a customer's credit with their explicit written or digital consent. This is standard practice for credit assessment in Kenya. Individual self-checks (checking your own record) require no additional consent.</div></div>
        </div>

        <div class="faq-section" data-cat="pricing">
            <div class="faq-section-title"><i class="fa-solid fa-coins"></i> Pricing &amp; Payments</div>
            <div class="faq-item" data-cat="pricing"><div class="faq-q" onclick="toggleFAQ(this)"><span>How does Readiwork charge for checks?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Readiwork uses a pay-per-check model. You pay via M-Pesa for each check you run. There are no monthly subscriptions or hidden fees. Business API users can top up their credit balance and checks are deducted from that balance automatically.</div></div>
            <div class="faq-item" data-cat="pricing"><div class="faq-q" onclick="toggleFAQ(this)"><span>What payment methods are accepted?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Currently we accept M-Pesa (STK Push). When you initiate a check, you'll enter your M-Pesa number and receive a PIN prompt directly on your phone. Business accounts can also arrange bulk invoice payments — contact us for details.</div></div>
            <div class="faq-item" data-cat="pricing"><div class="faq-q" onclick="toggleFAQ(this)"><span>What happens if my M-Pesa payment fails?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">If you cancel the M-Pesa prompt, enter the wrong PIN, or the payment times out, no charge is made and you can try again. If money is deducted but the result page shows an error, please contact support at <a href="mailto:hello@readiwork.co.ke">hello@readiwork.co.ke</a> with your M-Pesa receipt number for a prompt refund.</div></div>
            <div class="faq-item" data-cat="pricing"><div class="faq-q" onclick="toggleFAQ(this)"><span>Do prices include VAT?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">VAT at 16% is added at checkout for individual checks. Prices shown on the pricing page are pre-VAT. Business API users should note that their invoices will include VAT as required by Kenya Revenue Authority (KRA) regulations.</div></div>
            <div class="faq-item" data-cat="pricing"><div class="faq-q" onclick="toggleFAQ(this)"><span>Are there discounts for high-volume users?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Yes. Business API users who top up KSh 20,000 get 10% bonus credits; KSh 50,000 gets 15% extra; KSh 100,000+ gets 20% extra. For enterprise volumes or custom pricing, please contact us at <a href="mailto:hello@readiwork.co.ke">hello@readiwork.co.ke</a>.</div></div>
        </div>

        <div class="faq-section" data-cat="privacy">
            <div class="faq-section-title"><i class="fa-solid fa-shield-halved"></i> Privacy &amp; Data Security</div>
            <div class="faq-item" data-cat="privacy"><div class="faq-q" onclick="toggleFAQ(this)"><span>Is my National ID number secure with Readiwork?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Yes. We transmit all data over encrypted HTTPS connections and never store your National ID number in plain text after the check is complete. Our servers are hosted in a secured environment and access is strictly controlled. We do not sell your personal data to third parties.</div></div>
            <div class="faq-item" data-cat="privacy"><div class="faq-q" onclick="toggleFAQ(this)"><span>How long do you keep my credit report data?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">We retain your credit check result for 30 days to allow you to re-download your report. After that, identifying personal data is purged from our systems. You can also request immediate deletion by emailing <a href="mailto:hello@readiwork.co.ke">hello@readiwork.co.ke</a>.</div></div>
            <div class="faq-item" data-cat="privacy"><div class="faq-q" onclick="toggleFAQ(this)"><span>Does running a credit check affect my credit score?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">No. A self-check (individual checking their own record) is a "soft inquiry" and does not affect your credit score. Only formal credit applications (lender-initiated "hard inquiries") may impact your score. Readiwork checks are designed as soft inquiries.</div></div>
            <div class="faq-item" data-cat="privacy"><div class="faq-q" onclick="toggleFAQ(this)"><span>Who can see my credit report?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Only you (and the entity that pays for the check) can see the report. Readiwork staff do not view your personal report data. If a business uses our API to check your credit, they must have your consent as required by the Kenya Data Protection Act 2019.</div></div>
            <div class="faq-item" data-cat="privacy"><div class="faq-q" onclick="toggleFAQ(this)"><span>What law governs how Readiwork handles my data?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Readiwork complies with the Kenya Data Protection Act 2019 (DPA 2019) and the guidelines issued by the Office of the Data Protection Commissioner (ODPC). Metropol CRB operations are also regulated under the Banking (Credit Reference Bureau) Regulations 2020.</div></div>
        </div>

        <div class="faq-section" data-cat="api">
            <div class="faq-section-title"><i class="fa-solid fa-code"></i> API &amp; Business Integration</div>
            <div class="faq-item" data-cat="api"><div class="faq-q" onclick="toggleFAQ(this)"><span>How do I get a Readiwork API key?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Visit our <a href="{{ route('get-started') }}">Get Started page</a>. Once registered, your API key is available in your dashboard. You get free test credits to explore all endpoints in sandbox mode before going live.</div></div>
            <div class="faq-item" data-cat="api"><div class="faq-q" onclick="toggleFAQ(this)"><span>Is there a sandbox/test environment?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Yes. All new accounts start in sandbox mode with test credits. In sandbox mode you can call all endpoints and receive realistic mock responses without being billed. Switch to live mode from your dashboard when you're ready to go live.</div></div>
            <div class="faq-item" data-cat="api"><div class="faq-q" onclick="toggleFAQ(this)"><span>What programming languages does the API support?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Readiwork's API is a standard REST API over HTTPS, so it works with any language — PHP, Python, JavaScript/Node.js, Java, Ruby, Go, etc. We provide code examples in PHP, Python, and cURL on our <a href="{{ route('documentation') }}">API Reference page</a>.</div></div>
            <div class="faq-item" data-cat="api"><div class="faq-q" onclick="toggleFAQ(this)"><span>What is the API rate limit?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Standard accounts can make up to 60 requests per minute. Enterprise accounts have higher limits. If you exceed the limit, you'll receive a 429 response and should retry after 1 second. Contact us if you need higher throughput for your use case.</div></div>
            <div class="faq-item" data-cat="api"><div class="faq-q" onclick="toggleFAQ(this)"><span>Can I submit my loan portfolio data to the CRB through Readiwork?</span><i class="fa-solid fa-chevron-down"></i></div><div class="faq-a">Yes. Licensed lenders can submit credit data (single or bulk) to Metropol CRB through our Data Submission API. This is a regulated service — contact us at <a href="mailto:hello@readiwork.co.ke">hello@readiwork.co.ke</a> to discuss pricing and compliance requirements.</div></div>
        </div>

        <div class="faq-no-results" id="faqNoResults">
            <i class="fa-solid fa-magnifying-glass"></i>
            <p>No questions matched your search.<br>Try different keywords or <a href="{{ route('contact') }}" style="color:#0FA958;">contact us</a>.</p>
        </div>

        <div class="faq-cta">
            <h2>Still have questions?</h2>
            <p>Our support team is happy to help. Reach out and we'll get back to you quickly.</p>
            <div class="faq-cta-btns">
                <a href="{{ route('contact') }}" class="btn-g"><i class="fa-solid fa-envelope"></i> Contact Support</a>
                <a href="{{ route('documentation') }}" class="btn-o"><i class="fa-solid fa-book"></i> Read the Docs</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function toggleFAQ(btn) {
    const item = btn.parentElement;
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!wasOpen) item.classList.add('open');
}
function filterByCategory(cat, btn) {
    document.querySelectorAll('.faq-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.faq-section').forEach(sec => {
        sec.style.display = (cat === 'all' || sec.dataset.cat === cat) ? '' : 'none';
    });
    document.getElementById('faqSearch').value = '';
    document.getElementById('faqNoResults').style.display = 'none';
}
function filterFAQ(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('.faq-tab').forEach(t => t.classList.remove('active'));
    document.querySelector('.faq-tab').classList.add('active');
    document.querySelectorAll('.faq-section').forEach(sec => sec.style.display = '');
    if (!q) {
        document.querySelectorAll('.faq-item').forEach(el => el.style.display = '');
        document.getElementById('faqNoResults').style.display = 'none';
        return;
    }
    let visible = 0;
    document.querySelectorAll('.faq-item').forEach(item => {
        const show = item.textContent.toLowerCase().includes(q);
        item.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('faqNoResults').style.display = visible === 0 ? 'block' : 'none';
}
</script>
@endpush
