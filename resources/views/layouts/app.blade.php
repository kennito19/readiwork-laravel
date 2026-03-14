<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteName       = \App\Models\Setting::get('site_name', 'Readiwork');
        $brandColor     = \App\Models\Setting::get('brand_color', '#0FA958');
        $secondaryColor = \App\Models\Setting::get('secondary_color', '#0B1F3B');
        $logoPath       = \App\Models\Setting::get('logo_path', '');
        $logoSrc        = $logoPath ? asset($logoPath) : asset('logo.png');
        $footerTagline  = \App\Models\Setting::get('footer_tagline', 'Instant credit checks for Kenyan individuals — identity, defaults, score, and full reports in seconds.');
        $contactEmail   = \App\Models\Setting::get('contact_email', 'hello@readiwork.co.ke');
        $supportPhone   = \App\Models\Setting::get('support_phone', '+254722175570');
        $whatsapp       = \App\Models\Setting::get('support_whatsapp', '254722175570');
        $officeAddress  = \App\Models\Setting::get('office_address', 'Nairobi, Kenya');
        $socialFb       = \App\Models\Setting::get('social_facebook', '#');
        $socialTw       = \App\Models\Setting::get('social_twitter', '#');
        $socialIg       = \App\Models\Setting::get('social_instagram', '#');
        $socialLi       = \App\Models\Setting::get('social_linkedin', '#');
        $socialTt       = \App\Models\Setting::get('social_tiktok', '#');
        $metaDesc       = \App\Models\Setting::get('meta_description', '');
    @endphp
    <title>@yield('title', $siteName . ' - Instant Credit Checks for Kenyan Businesses')</title>
    @if($metaDesc)
    <meta name="description" content="{{ $metaDesc }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ $logoSrc }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <style>:root{--primary-green:{{ $brandColor }};--primary-green-hover:{{ $brandColor }}dd;--primary-navy:{{ $secondaryColor }};}</style>
    @stack('head')
</head>
<body>

<!-- ===== HEADER ===== -->
<header>
    <div class="container header-container">
        <a class="logo" href="{{ route('home') }}"><img src="{{ $logoSrc }}" alt="{{ $siteName }}"></a>
        <nav class="nav-links" id="navLinks">
            <a href="{{ route('home') }}"              @class(['active' => Request::routeIs('home')])>Home</a>
            <a href="{{ route('services') }}"          @class(['active' => Request::routeIs('services')])>Services</a>
            <a href="{{ route('pricing') }}"           @class(['active' => Request::routeIs('pricing')])>Pricing</a>
            <a href="{{ route('about') }}"             @class(['active' => Request::routeIs('about')])>About</a>
            <a href="{{ route('business') }}"          @class(['active' => Request::routeIs('business')])>Business</a>
            <a href="{{ route('faq') }}"               @class(['active' => Request::routeIs('faq')])>FAQ</a>
            <a href="{{ route('contact') }}"           @class(['active' => Request::routeIs('contact')])>Contact</a>
            <a class="start-btn mobile-only" href="{{ route('get-started') }}">Get Started</a>
        </nav>
        <a class="start-btn desktop-only" href="{{ route('get-started') }}">Get Started</a>
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    </div>
</header>
<div class="nav-overlay" id="navOverlay" onclick="closeNav()"></div>

@yield('content')

<!-- ===== FOOTER ===== -->
<footer>
    <div class="container footer-top">
        <div class="footer-brand">
            <a class="logo" href="{{ route('home') }}"><img src="{{ $logoSrc }}" alt="{{ $siteName }}" style="height:28px;"></a>
            <p class="footer-tagline">{{ $footerTagline }}</p>
            <div class="footer-socials">
                <a href="{{ $socialFb }}" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="{{ $socialTw }}" aria-label="X / Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="{{ $socialIg }}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="{{ $socialLi }}" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="{{ $socialTt }}" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                <a href="https://wa.me/{{ $whatsapp }}" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
        </div>
        <div class="footer-links-group">
            <h4>Services</h4>
            <ul>
                <li><a href="{{ route('identity-verification') }}">Identity Verification</a></li>
                <li><a href="{{ route('crb-blacklist-check') }}">Loan Default Status</a></li>
                <li><a href="{{ route('credit-score-check') }}">Credit Score Check</a></li>
                <li><a href="{{ route('full-credit-report') }}">Credit Reports</a></li>
                <li><a href="{{ route('loan-eligibility') }}">Loan Eligibility Check</a></li>
            </ul>
        </div>
        <div class="footer-links-group">
            <h4>Company</h4>
            <ul>
                <li><a href="{{ route('about') }}">About Us</a></li>
                <li><a href="{{ route('faq') }}">FAQ</a></li>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
                <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                <li><a href="{{ route('cookies') }}">Cookies</a></li>
            </ul>
        </div>
        <div class="footer-links-group">
            <h4>For Business</h4>
            <ul>
                <li><a href="{{ route('business') }}">Business Overview</a></li>
                <li><a href="{{ route('pricing') }}">Pricing</a></li>
                <li><a href="{{ route('get-started') }}">Get API Key</a></li>
            </ul>
        </div>
        <div class="footer-links-group">
            <h4>Contact</h4>
            <ul>
                <li><a href="mailto:{{ $contactEmail }}"><i class="fa-solid fa-envelope"></i> {{ $contactEmail }}</a></li>
                <li><a href="tel:{{ $supportPhone }}"><i class="fa-solid fa-phone"></i> {{ $supportPhone }}</a></li>
                <li><a href="#"><i class="fa-solid fa-location-dot"></i> {{ $officeAddress }}</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span>&copy; {{ date('Y') }} {{ $siteName }} Limited. All rights reserved.</span>
            <span class="footer-bottom-links">
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('cookies') }}">Cookies</a>
            </span>
        </div>
    </div>
</footer>

<script>
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navLinks     = document.getElementById('navLinks');
    const navOverlay   = document.getElementById('navOverlay');
    function toggleNav() {
        hamburgerBtn.classList.toggle('open');
        navLinks.classList.toggle('open');
        navOverlay.classList.toggle('visible');
        document.body.classList.toggle('nav-open');
    }
    function closeNav() {
        hamburgerBtn.classList.remove('open');
        navLinks.classList.remove('open');
        navOverlay.classList.remove('visible');
        document.body.classList.remove('nav-open');
    }
    hamburgerBtn.addEventListener('click', toggleNav);
    navLinks.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', closeNav);
    });
</script>

@stack('scripts')
<!-- ══ READIWORK AI CHATBOT WIDGET ══ -->
<style>
#rwChat { position: fixed; bottom: 24px; right: 24px; z-index: 9999; font-family: 'Inter', sans-serif; }
#rwChatBtn {
    width: 56px; height: 56px; border-radius: 50%; background: #0FA958; border: none; cursor: pointer;
    box-shadow: 0 6px 24px rgba(15,169,88,.45); display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.4rem; transition: transform .2s, background .2s;
}
#rwChatBtn:hover { background: #0d9048; transform: scale(1.06); }
#rwChatBtn .rw-close { display: none; font-size: 1.2rem; }
#rwChat.open #rwChatBtn .rw-open  { display: none; }
#rwChat.open #rwChatBtn .rw-close { display: block; }
.rw-notif {
    position: absolute; top: -6px; right: -4px; background: #ef4444; color: #fff;
    font-size: .6rem; font-weight: 800; width: 18px; height: 18px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; border: 2px solid #fff;
}
#rwChatWindow {
    display: none; position: absolute; bottom: 70px; right: 0; width: 340px;
    background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,.18);
    overflow: hidden; flex-direction: column; max-height: 500px;
}
#rwChat.open #rwChatWindow { display: flex; }
.rw-head {
    background: linear-gradient(135deg,#071629,#0d3460); padding: 16px 18px;
    display: flex; align-items: center; gap: 11px;
}
.rw-avatar {
    width: 38px; height: 38px; border-radius: 50%; background: #0FA958;
    display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem; flex-shrink: 0;
}
.rw-head-info strong { color: #fff; font-size: .92rem; display: block; }
.rw-head-info small  { color: rgba(255,255,255,.5); font-size: .72rem; }
.rw-online { width: 8px; height: 8px; background: #0FA958; border-radius: 50%; display: inline-block; margin-right: 4px; box-shadow: 0 0 0 2px rgba(15,169,88,.25); }
.rw-msgs {
    flex: 1; overflow-y: auto; padding: 16px 14px; display: flex; flex-direction: column; gap: 10px; min-height: 260px; max-height: 320px;
}
.rw-msg { max-width: 82%; word-break: break-word; }
.rw-msg.bot .rw-bubble {
    background: #f1f5f9; color: #1e293b; border-radius: 4px 16px 16px 16px;
    padding: 10px 13px; font-size: .84rem; line-height: 1.6;
}
.rw-msg.user { align-self: flex-end; }
.rw-msg.user .rw-bubble {
    background: #0FA958; color: #fff; border-radius: 16px 4px 16px 16px;
    padding: 10px 13px; font-size: .84rem; line-height: 1.6;
}
.rw-typing { display: flex; gap: 4px; align-items: center; padding: 10px 13px; background: #f1f5f9; border-radius: 4px 16px 16px 16px; width: fit-content; }
.rw-typing span { width: 7px; height: 7px; border-radius: 50%; background: #94a3b8; animation: rwDot .9s infinite; }
.rw-typing span:nth-child(2) { animation-delay: .2s; }
.rw-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes rwDot { 0%,60%,100% { transform: translateY(0); } 30% { transform: translateY(-6px); } }
.rw-quick { padding: 8px 14px 6px; display: flex; gap: 6px; flex-wrap: wrap; border-top: 1px solid #f1f5f9; }
.rw-quick button {
    font-size: .72rem; padding: 5px 10px; border-radius: 20px;
    border: 1px solid #e2e8f0; background: #f8fafc; color: #475569;
    cursor: pointer; font-family: inherit; transition: all .12s;
}
.rw-quick button:hover { background: #0FA958; color: #fff; border-color: #0FA958; }
.rw-input-wrap { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-top: 1px solid #f1f5f9; }
#rwInput {
    flex: 1; border: 1.5px solid #e2e8f0; border-radius: 22px; padding: 9px 14px;
    font-size: .85rem; font-family: inherit; outline: none; transition: border-color .2s; resize: none;
}
#rwInput:focus { border-color: #0FA958; }
#rwSend {
    width: 36px; height: 36px; border-radius: 50%; background: #0FA958; border: none;
    color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: .85rem; transition: background .15s;
}
#rwSend:hover { background: #0d9048; }
#rwSend:disabled { background: #e2e8f0; cursor: default; }
</style>

<div id="rwChat">
    <div id="rwChatWindow">
        <div class="rw-head">
            <div class="rw-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="rw-head-info">
                <strong>Readiwork Assistant</strong>
                <small><span class="rw-online"></span>Online — replies instantly</small>
            </div>
        </div>
        <div class="rw-msgs" id="rwMsgs"></div>
        <div class="rw-quick" id="rwQuick">
            <button onclick="rwQuickSend('How much does a credit score check cost?')">Credit score price</button>
            <button onclick="rwQuickSend('How do I pay via M-Pesa?')">M-Pesa payment</button>
            <button onclick="rwQuickSend('What is a CRB blacklist check?')">CRB check</button>
            <button onclick="rwQuickSend('How do I improve my credit score?')">Improve score</button>
        </div>
        <div class="rw-input-wrap">
            <input type="text" id="rwInput" placeholder="Ask me anything…" maxlength="300" onkeydown="if(event.key==='Enter')rwSend()">
            <button id="rwSend" onclick="rwSend()" title="Send"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>
    <button id="rwChatBtn" onclick="rwToggle()" aria-label="Open chat">
        <i class="fa-solid fa-comments rw-open"></i>
        <i class="fa-solid fa-xmark rw-close"></i>
        <div class="rw-notif" id="rwNotif">1</div>
    </button>
</div>

<script>
(function(){
    const chatUrl = '/chatbot';
    const csrf = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
    const chat    = document.getElementById('rwChat');
    const msgs    = document.getElementById('rwMsgs');
    const input   = document.getElementById('rwInput');
    const sendBtn = document.getElementById('rwSend');
    const notif   = document.getElementById('rwNotif');
    const quick   = document.getElementById('rwQuick');
    let history   = [];
    let opened    = false;

    // Welcome message
    function addMsg(text, role) {
        quick.style.display = 'none';
        const wrap = document.createElement('div');
        wrap.className = 'rw-msg ' + role;
        const bub = document.createElement('div');
        bub.className = 'rw-bubble';
        bub.textContent = text;
        wrap.appendChild(bub);
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
        return wrap;
    }

    function showTyping() {
        const wrap = document.createElement('div');
        wrap.className = 'rw-msg bot';
        wrap.id = 'rwTyping';
        wrap.innerHTML = '<div class="rw-typing"><span></span><span></span><span></span></div>';
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function removeTyping() {
        const t = document.getElementById('rwTyping');
        if (t) t.remove();
    }

    window.rwToggle = function() {
        chat.classList.toggle('open');
        if (!opened) {
            opened = true;
            notif.style.display = 'none';
            addMsg("Hi! I'm the Readiwork AI assistant. I can answer questions about credit checks, pricing, M-Pesa payments, and more. How can I help you today?", 'bot');
        }
    };

    window.rwQuickSend = function(text) {
        input.value = text;
        rwSend();
    };

    window.rwSend = async function() {
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        sendBtn.disabled = true;
        addMsg(text, 'user');
        history.push({role:'user', content: text});
        showTyping();
        try {
            const res = await fetch(chatUrl, {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({message: text, history: history.slice(0,-1)})
            });
            const data = await res.json();
            removeTyping();
            const reply = data.reply || 'Sorry, something went wrong. Please try again.';
            addMsg(reply, 'bot');
            history.push({role:'assistant', content: reply});
        } catch(e) {
            removeTyping();
            addMsg('Sorry, I could not connect. Please try again or email {{ $contactEmail }}.', 'bot');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    };
})();
</script>

</body>
</html>
