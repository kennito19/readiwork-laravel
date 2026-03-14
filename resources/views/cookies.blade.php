@extends('layouts.app')
@section('title', 'Cookie Policy - Readiwork')

@section('content')
<section class="svc-page-hero">
    <div class="container">
        <h1 class="svc-page-title">Cookie Policy</h1>
        <p class="svc-page-sub">How we use cookies and similar technologies on our platform.</p>
        <p style="color:rgba(255,255,255,0.45);font-size:.85rem;margin-top:10px;">Last updated: March 2026</p>
    </div>
</section>

<section class="legal-section">
    <div class="container legal-layout">
        <nav class="legal-toc" id="legalToc">
            <p class="legal-toc-heading">On this page</p>
            <a href="#ck-1">1. What Are Cookies?</a>
            <a href="#ck-2">2. Cookies We Use</a>
            <a href="#ck-3">3. Essential Cookies</a>
            <a href="#ck-4">4. Analytics Cookies</a>
            <a href="#ck-5">5. Preference Cookies</a>
            <a href="#ck-6">6. Third-Party Cookies</a>
            <a href="#ck-7">7. Managing Cookies</a>
            <a href="#ck-8">8. Cookie Retention</a>
            <a href="#ck-9">9. Updates to This Policy</a>
            <a href="#ck-10">10. Contact Us</a>
        </nav>

        <div class="legal-body">
            <div class="legal-intro-box">
                <i class="fa-solid fa-cookie-bite"></i>
                <p>This Cookie Policy explains how Readiwork Limited uses cookies and similar tracking technologies on our website and platform. By continuing to use our services, you consent to our use of cookies as described in this policy.</p>
            </div>

            <div class="legal-section-block" id="ck-1">
                <h2><span class="legal-num">1</span> What Are Cookies?</h2>
                <p>Cookies are small text files placed on your device when you visit a website. They are widely used to make websites work more efficiently, provide a better user experience, and give website operators information about how users interact with their site.</p>
                <p>Similar technologies include web beacons, pixel tags, and local storage — we refer to all of these collectively as "cookies" in this policy.</p>
            </div>

            <div class="legal-section-block" id="ck-2">
                <h2><span class="legal-num">2</span> Cookies We Use</h2>
                <p>Readiwork uses the following categories of cookies:</p>
                <ul>
                    <li><strong>Essential cookies</strong> — necessary for the platform to function correctly.</li>
                    <li><strong>Analytics cookies</strong> — to understand how visitors use our site.</li>
                    <li><strong>Preference cookies</strong> — to remember your settings and choices.</li>
                </ul>
                <p>We do not use advertising or tracking cookies for third-party marketing purposes.</p>
            </div>

            <div class="legal-section-block" id="ck-3">
                <h2><span class="legal-num">3</span> Essential Cookies</h2>
                <p>These cookies are strictly necessary for the website and platform to function. Without them, services like user authentication and secure forms cannot operate. They cannot be disabled.</p>
                <table style="width:100%;border-collapse:collapse;margin-top:16px;font-size:.85rem;">
                    <thead><tr style="background:#f8fafc;"><th style="padding:10px;text-align:left;border-bottom:2px solid #e2e8f0;">Cookie Name</th><th style="padding:10px;text-align:left;border-bottom:2px solid #e2e8f0;">Purpose</th><th style="padding:10px;text-align:left;border-bottom:2px solid #e2e8f0;">Duration</th></tr></thead>
                    <tbody>
                        <tr><td style="padding:10px;border-bottom:1px solid #f1f5f9;font-weight:600;">readiwork_session</td><td style="padding:10px;border-bottom:1px solid #f1f5f9;">Maintains your login session</td><td style="padding:10px;border-bottom:1px solid #f1f5f9;">Session</td></tr>
                        <tr><td style="padding:10px;border-bottom:1px solid #f1f5f9;font-weight:600;">XSRF-TOKEN</td><td style="padding:10px;border-bottom:1px solid #f1f5f9;">Security token to prevent cross-site request forgery</td><td style="padding:10px;border-bottom:1px solid #f1f5f9;">2 hours</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="legal-section-block" id="ck-4">
                <h2><span class="legal-num">4</span> Analytics Cookies</h2>
                <p>We use analytics cookies to understand how visitors interact with our platform. This helps us improve user experience and identify popular features. All analytics data is aggregated and anonymised — we cannot identify you personally from analytics data.</p>
                <ul>
                    <li>Pages visited and time spent on each page</li>
                    <li>Device type and browser information</li>
                    <li>Geographic region (country/city level only)</li>
                    <li>Referral source (how you arrived at our site)</li>
                </ul>
            </div>

            <div class="legal-section-block" id="ck-5">
                <h2><span class="legal-num">5</span> Preference Cookies</h2>
                <p>Preference cookies allow our website to remember choices you have made, such as language settings or display preferences. These cookies enhance your experience but are not strictly necessary for the platform to function.</p>
            </div>

            <div class="legal-section-block" id="ck-6">
                <h2><span class="legal-num">6</span> Third-Party Cookies</h2>
                <p>Some pages on our platform may include content from third-party services (such as embedded maps or payment processors). These services may set their own cookies. Readiwork does not control these third-party cookies. Please review the cookie policies of any third-party services you interact with.</p>
                <p>We use M-Pesa's Daraja API for payment processing. Safaricom may set session-related cookies during the payment flow.</p>
            </div>

            <div class="legal-section-block" id="ck-7">
                <h2><span class="legal-num">7</span> Managing Cookies</h2>
                <p>You can manage or delete cookies through your browser settings. Most browsers allow you to:</p>
                <ul>
                    <li>View cookies stored on your device</li>
                    <li>Delete all or specific cookies</li>
                    <li>Block cookies from specific websites</li>
                    <li>Block all third-party cookies</li>
                    <li>Clear all cookies when you close the browser</li>
                </ul>
                <p>Please note that disabling essential cookies may prevent you from using key features of our platform, including logging in and accessing your dashboard.</p>
                <p>Browser-specific instructions for managing cookies:</p>
                <ul>
                    <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener">Google Chrome</a></li>
                    <li><a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer" target="_blank" rel="noopener">Mozilla Firefox</a></li>
                    <li><a href="https://support.apple.com/en-ke/guide/safari/sfri11471/mac" target="_blank" rel="noopener">Safari</a></li>
                </ul>
            </div>

            <div class="legal-section-block" id="ck-8">
                <h2><span class="legal-num">8</span> Cookie Retention</h2>
                <p>Session cookies are deleted when you close your browser. Persistent cookies remain on your device for the duration specified in their settings. You can delete persistent cookies at any time through your browser settings.</p>
            </div>

            <div class="legal-section-block" id="ck-9">
                <h2><span class="legal-num">9</span> Updates to This Policy</h2>
                <p>We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated "Last updated" date. We encourage you to review this policy periodically.</p>
            </div>

            <div class="legal-section-block" id="ck-10">
                <h2><span class="legal-num">10</span> Contact Us</h2>
                <p>If you have any questions about our use of cookies, please contact us:</p>
                <div class="legal-contact-grid">
                    <div class="legal-contact-item"><i class="fa-solid fa-envelope"></i><div><strong>Email</strong><a href="mailto:hello@readiwork.co.ke">hello@readiwork.co.ke</a></div></div>
                    <div class="legal-contact-item"><i class="fa-solid fa-location-dot"></i><div><strong>Address</strong><span>Nairobi, Kenya</span></div></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
