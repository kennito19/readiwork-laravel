@extends('layouts.app')
@section('title', 'Privacy Policy - Readiwork')

@section('content')
<section class="svc-page-hero">
    <div class="container">
        <h1 class="svc-page-title">Privacy Policy</h1>
        <p class="svc-page-sub">How we collect, use, and protect your information.</p>
        <p style="color:rgba(255,255,255,0.45);font-size:.85rem;margin-top:10px;">Last updated: March 2026</p>
    </div>
</section>

<section class="legal-section">
    <div class="container legal-layout">
        <nav class="legal-toc" id="legalToc">
            <p class="legal-toc-heading">On this page</p>
            <a href="#pp-1">1. Who We Are</a>
            <a href="#pp-2">2. Information We Collect</a>
            <a href="#pp-3">3. How We Use Your Information</a>
            <a href="#pp-4">4. Credit &amp; Identity Data</a>
            <a href="#pp-5">5. Legal Basis for Processing</a>
            <a href="#pp-6">6. How We Store Your Data</a>
            <a href="#pp-7">7. Who We Share Data With</a>
            <a href="#pp-8">8. Data Retention</a>
            <a href="#pp-9">9. Your Rights</a>
            <a href="#pp-10">10. Cookies</a>
            <a href="#pp-11">11. Third-Party Links</a>
            <a href="#pp-12">12. Changes to This Policy</a>
            <a href="#pp-13">13. Contact Us</a>
        </nav>

        <div class="legal-body">
            <div class="legal-intro-box">
                <i class="fa-solid fa-shield-halved"></i>
                <p>Readiwork Limited is committed to protecting your personal data. This Privacy Policy explains how we collect, use, store, and share information when you use our platform and API services. We operate in compliance with the <strong>Kenya Data Protection Act, 2019</strong> and all applicable regulations.</p>
            </div>

            <div class="legal-section-block" id="pp-1">
                <h2><span class="legal-num">1</span> Who We Are</h2>
                <p>Readiwork Limited ("Readiwork", "we", "us", "our") is a company registered in Kenya that provides businesses with API-based access to credit checking and identity verification services.</p>
                <p>For the purposes of data protection law, Readiwork acts as a <strong>data processor</strong> when handling End User credit data on behalf of our Clients (who are the data controllers), and as a <strong>data controller</strong> in relation to our Clients' own account and billing information.</p>
                <div class="legal-contact-grid" style="margin-top:16px;">
                    <div class="legal-contact-item"><i class="fa-solid fa-building"></i><div><strong>Data Controller</strong><span>Readiwork Limited</span></div></div>
                    <div class="legal-contact-item"><i class="fa-solid fa-envelope"></i><div><strong>Privacy Contact</strong><a href="mailto:privacy@readiwork.co.ke">privacy@readiwork.co.ke</a></div></div>
                    <div class="legal-contact-item"><i class="fa-solid fa-location-dot"></i><div><strong>Location</strong><span>Nairobi, Kenya</span></div></div>
                </div>
            </div>

            <div class="legal-section-block" id="pp-2">
                <h2><span class="legal-num">2</span> Information We Collect</h2>
                <h3>2.1 Information you provide to us</h3>
                <ul>
                    <li>Account registration details: name, email address, phone number, company name.</li>
                    <li>Billing information: top-up amounts and payment method details (payment details are processed by our payment provider,  we do not store card numbers).</li>
                    <li>Communications: emails or messages you send to our support team.</li>
                </ul>
                <h3>2.2 Information collected automatically</h3>
                <ul>
                    <li>API usage logs: timestamp, endpoint called, response code, credit balance change.</li>
                    <li>Device and access data: IP address, browser type, operating system.</li>
                    <li>Cookies and analytics data (see Section 10).</li>
                </ul>
                <h3>2.3 End User credit &amp; identity data</h3>
                <p>When our Clients use the API to run a check on an individual (End User), we temporarily process that individual's National ID number and the resulting credit or identity data returned by the bureau. We do not collect or store this data beyond the immediate API transaction, see Section 4.</p>
            </div>

            <div class="legal-section-block" id="pp-3">
                <h2><span class="legal-num">3</span> How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Create and manage your account on the Readiwork platform.</li>
                    <li>Process your credit top-ups and deduct credits per API call.</li>
                    <li>Provide customer support and respond to enquiries.</li>
                    <li>Send transactional emails (e.g. receipts, low-balance alerts, account notifications).</li>
                    <li>Monitor and maintain the security and performance of our platform.</li>
                    <li>Comply with legal and regulatory obligations.</li>
                    <li>Improve our services based on aggregated, anonymised usage data.</li>
                    <li>Generate AI-assisted risk indicators and decision-support insights for authorised Clients.</li>
                </ul>
                <p>We do <strong>not</strong> use your data for unsolicited marketing without your explicit consent.</p>
            </div>

            <div class="legal-section-block" id="pp-4">
                <h2><span class="legal-num">4</span> Credit &amp; Identity Data (End User Data)</h2>
                <div class="legal-consent-box">
                    <div class="legal-consent-icon" style="background:#0B1F3B;"><i class="fa-solid fa-rotate"></i></div>
                    <div class="legal-consent-body">
                        <p class="legal-consent-label">Our core data principle</p>
                        <p>Readiwork is a <strong>pass-through API service</strong>. When a Client makes an API call to check an individual's credit or identity, we transmit the request to the relevant licensed credit bureau and return the result directly to the Client. <strong>We do not store, retain, or re-process End User credit data after the API response is delivered.</strong></p>
                    </div>
                </div>
                <ul>
                    <li>National ID numbers submitted in API requests are used solely to retrieve the requested report and are not stored in our databases.</li>
                    <li>Credit reports, scores, blacklist results, and identity details are passed directly to the Client and not retained on our servers.</li>
                    <li>Short-lived API logs (for debugging and billing purposes) may record that a request was made to a specific endpoint, but do not store the content of the credit report or identity information returned.</li>
                    <li>Clients are responsible for how they store and use the data returned by the API.</li>
                </ul>
            </div>

            <div class="legal-section-block" id="pp-5">
                <h2><span class="legal-num">5</span> Legal Basis for Processing</h2>
                <p>Under the Kenya Data Protection Act, 2019, we process personal data on the following lawful grounds:</p>
                <ul>
                    <li><strong>Contract performance</strong>  to provide the API services you have signed up for.</li>
                    <li><strong>Legitimate interests</strong>  to maintain platform security, prevent fraud, and improve our services.</li>
                    <li><strong>Legal obligation</strong>  to comply with Kenyan law, CBK regulations, and regulatory directives.</li>
                    <li><strong>Consent</strong>  for any marketing communications, where applicable.</li>
                </ul>
                <p>For End User credit data, the lawful basis is the <strong>explicit consent</strong> obtained by the Client from the End User before the check is initiated.</p>
            </div>

            <div class="legal-section-block" id="pp-6">
                <h2><span class="legal-num">6</span> How We Store Your Data</h2>
                <ul>
                    <li>Account and billing data is stored on secure servers located in Kenya or within jurisdictions offering equivalent data protection standards.</li>
                    <li>All data in transit is encrypted using TLS (HTTPS).</li>
                    <li>Access to personal data is restricted to authorised Readiwork staff on a need-to-know basis.</li>
                    <li>We implement regular security reviews and access control audits.</li>
                    <li>In the event of a data breach affecting your personal data, we will notify you in accordance with the Kenya Data Protection Act.</li>
                </ul>
            </div>

            <div class="legal-section-block" id="pp-7">
                <h2><span class="legal-num">7</span> Who We Share Data With</h2>
                <p>We do not sell your personal data. We may share data only in the following circumstances:</p>
                <ul>
                    <li><strong>Licensed Credit Reference Bureaus</strong>  End User ID numbers are submitted to the bureau to retrieve the requested report, as authorised by the Client and End User.</li>
                    <li><strong>Payment processors</strong>  billing data is shared with our payment provider solely to process top-up transactions.</li>
                    <li><strong>Service providers</strong>  trusted third-party vendors (e.g. cloud hosting, email delivery) who process data on our behalf under strict data processing agreements.</li>
                    <li><strong>Legal authorities</strong>  where required by law, court order, or regulatory directive.</li>
                </ul>
            </div>

            <div class="legal-section-block" id="pp-8">
                <h2><span class="legal-num">8</span> Data Retention</h2>
                <ul>
                    <li><strong>Account data</strong> is retained for the duration of your account and for 7 years after closure, as required for financial and legal compliance.</li>
                    <li><strong>API usage logs</strong> (endpoint, timestamp, credit deduction) are retained for 12 months for billing and debugging purposes.</li>
                    <li><strong>End User credit &amp; identity data</strong> is not retained beyond the API transaction session.</li>
                    <li><strong>Support communications</strong> are retained for 3 years.</li>
                </ul>
            </div>

            <div class="legal-section-block" id="pp-9">
                <h2><span class="legal-num">9</span> Your Rights</h2>
                <p>Under the Kenya Data Protection Act, 2019, you have the following rights:</p>
                <ul>
                    <li><strong>Right of access</strong>  request a copy of the personal data we hold about you.</li>
                    <li><strong>Right to rectification</strong>  request correction of inaccurate or incomplete data.</li>
                    <li><strong>Right to erasure</strong>  request deletion of your data, subject to legal retention requirements.</li>
                    <li><strong>Right to restrict processing</strong>  request that we limit how we use your data.</li>
                    <li><strong>Right to data portability</strong>  receive your data in a structured, machine-readable format.</li>
                    <li><strong>Right to object</strong>  object to processing based on legitimate interests.</li>
                    <li><strong>Right to withdraw consent</strong>  where processing is based on consent, you may withdraw it at any time.</li>
                </ul>
                <p>To exercise any of these rights, contact us at <a href="mailto:privacy@readiwork.co.ke">privacy@readiwork.co.ke</a>. We will respond within 21 days.</p>
            </div>

            <div class="legal-section-block" id="pp-10">
                <h2><span class="legal-num">10</span> Cookies</h2>
                <p>Our website uses cookies to improve your experience. We use:</p>
                <ul>
                    <li><strong>Essential cookies</strong>  required for the platform to function (login sessions, security tokens).</li>
                    <li><strong>Analytics cookies</strong>  to understand how visitors use our site (page views, device type). Data is aggregated and anonymised.</li>
                    <li><strong>Preference cookies</strong> to remember your settings and preferences.</li>
                </ul>
                <p>You can manage cookie preferences through your browser settings. See our <a href="{{ route('cookies') }}">Cookie Policy</a> for more details.</p>
            </div>

            <div class="legal-section-block" id="pp-11">
                <h2><span class="legal-num">11</span> Third-Party Links</h2>
                <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of those sites. We encourage you to read the privacy policy of any external site you visit.</p>
            </div>

            <div class="legal-section-block" id="pp-12">
                <h2><span class="legal-num">12</span> Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. When we make significant changes, we will notify registered Clients by email and update the "Last updated" date at the top of this page.</p>
            </div>

            <div class="legal-section-block" id="pp-13">
                <h2><span class="legal-num">13</span> Contact Us</h2>
                <p>For any privacy-related queries, requests, or complaints, please reach out to us:</p>
                <div class="legal-contact-grid">
                    <div class="legal-contact-item"><i class="fa-solid fa-envelope"></i><div><strong>Privacy Enquiries</strong><a href="mailto:privacy@readiwork.co.ke">privacy@readiwork.co.ke</a></div></div>
                    <div class="legal-contact-item"><i class="fa-solid fa-phone"></i><div><strong>Phone</strong><a href="tel:+254722175570">+254 722 175 570</a></div></div>
                    <div class="legal-contact-item"><i class="fa-solid fa-location-dot"></i><div><strong>Address</strong><span>Nairobi, Kenya</span></div></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
