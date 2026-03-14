@extends('layouts.app')
@section('title', 'Contact Us - Readiwork')

@push('head')
<style>
.contact-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 60%,#0a1f40 100%);padding:64px 0 48px;text-align:center;}
.contact-hero h1{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:800;color:#fff;margin-bottom:12px;}
.contact-hero p{color:rgba(255,255,255,.65);font-size:1.05rem;max-width:480px;margin:0 auto;}
.contact-grid{display:grid;grid-template-columns:1fr 1.5fr;gap:40px;align-items:start;padding:64px 0;}
@media(max-width:800px){.contact-grid{grid-template-columns:1fr;}}
.contact-info h2{font-size:1.2rem;font-weight:700;color:#0B1F3B;margin-bottom:20px;}
.contact-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px 20px;margin-bottom:14px;display:flex;gap:14px;align-items:flex-start;}
.contact-card-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
.contact-card-icon.green{background:rgba(15,169,88,.1);color:#0FA958;}
.contact-card-icon.blue{background:rgba(37,99,235,.1);color:#2563eb;}
.contact-card-icon.orange{background:rgba(234,88,12,.1);color:#ea580c;}
.contact-card h3{font-size:.9rem;font-weight:700;color:#0B1F3B;margin-bottom:3px;}
.contact-card p,.contact-card a{font-size:.84rem;color:#64748b;text-decoration:none;display:block;line-height:1.5;}
.contact-card a:hover{color:#0FA958;}
.hours-block{background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-top:4px;}
.hours-block h3{font-size:.88rem;font-weight:700;color:#0B1F3B;margin-bottom:12px;}
.hours-row{display:flex;justify-content:space-between;font-size:.82rem;color:#475569;padding:5px 0;border-bottom:1px solid #e2e8f0;}
.hours-row:last-child{border:0;}
.contact-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:36px;box-shadow:0 4px 30px rgba(0,0,0,.06);}
.contact-form-card h2{font-size:1.2rem;font-weight:700;color:#0B1F3B;margin-bottom:22px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:560px){.form-row{grid-template-columns:1fr;}}
.form-group{margin-bottom:18px;}
.form-group label{display:block;font-size:.84rem;font-weight:600;color:#0B1F3B;margin-bottom:7px;}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:.9rem;font-family:inherit;color:#1e293b;outline:none;transition:border-color .2s;background:#fff;}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#0FA958;}
.form-group textarea{resize:vertical;min-height:130px;}
.submit-btn{width:100%;background:#0FA958;color:#fff;border:none;border-radius:10px;padding:14px;font-size:1rem;font-weight:700;cursor:pointer;font-family:inherit;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:9px;}
.submit-btn:hover{background:#0d9048;}
.alert{border-radius:10px;padding:13px 16px;font-size:.87rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;}
.alert-green{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;}
.alert-red{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
.map-placeholder{background:linear-gradient(135deg,#f0fdf4,#ecfdf5);border:1px solid #bbf7d0;border-radius:14px;padding:40px 20px;text-align:center;color:#16a34a;margin-top:0;}
.map-placeholder i{font-size:2rem;margin-bottom:10px;display:block;}
.map-placeholder p{font-size:.88rem;color:#15803d;}
</style>
@endpush

@section('content')
<section class="contact-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Have a question, need support, or want to explore a business partnership? We're here to help.</p>
    </div>
</section>

<section>
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <div class="contact-card">
                    <div class="contact-card-icon green"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <h3>Email</h3>
                        <a href="mailto:hello@readiwork.co.ke">hello@readiwork.co.ke</a>
                        <a href="mailto:support@readiwork.co.ke">support@readiwork.co.ke</a>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon blue"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <h3>Phone / WhatsApp</h3>
                        <a href="tel:+254722175570">+254 722 175 570</a>
                        <a href="https://wa.me/254722175570">WhatsApp us</a>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon orange"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h3>Office</h3>
                        <p>Nairobi, Kenya</p>
                        <p>Virtual appointments available</p>
                    </div>
                </div>
                <div class="hours-block">
                    <h3><i class="fa-regular fa-clock" style="color:#0FA958;margin-right:7px;"></i> Support Hours</h3>
                    <div class="hours-row"><span>Mon – Fri</span><span>8:00 AM – 6:00 PM</span></div>
                    <div class="hours-row"><span>Saturday</span><span>9:00 AM – 1:00 PM</span></div>
                    <div class="hours-row"><span>Sunday</span><span>Closed</span></div>
                    <div style="margin-top:10px;font-size:.78rem;color:#94a3b8;">All times East Africa Time (EAT)</div>
                </div>
                <div class="map-placeholder" style="margin-top:14px;">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <p>Nairobi, Kenya<br><small style="color:#6b7280;">Serving clients across East Africa</small></p>
                </div>
            </div>

            <div class="contact-form-card">
                <h2><i class="fa-solid fa-paper-plane" style="color:#0FA958;margin-right:9px;"></i> Send Us a Message</h2>

                @if(session('success'))
                    <div class="alert alert-green"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                    <div style="text-align:center;padding:20px 0;">
                        <a href="{{ route('contact') }}" class="submit-btn" style="display:inline-flex;text-decoration:none;width:auto;padding:12px 28px;">
                            <i class="fa-solid fa-arrow-left"></i> Send another message
                        </a>
                    </div>
                @else
                    @if($errors->any())
                        <div class="alert alert-red"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name <span style="color:#dc2626;">*</span></label>
                                <input type="text" id="name" name="name" required placeholder="Jane Wanjiku" value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span style="color:#dc2626;">*</span></label>
                                <input type="email" id="email" name="email" required placeholder="jane@example.com" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject">
                                <option value="">Select a topic…</option>
                                <option value="General Enquiry" {{ old('subject')==='General Enquiry'?'selected':'' }}>General Enquiry</option>
                                <option value="Technical Support" {{ old('subject')==='Technical Support'?'selected':'' }}>Technical Support</option>
                                <option value="Payment / Billing" {{ old('subject')==='Payment / Billing'?'selected':'' }}>Payment / Billing</option>
                                <option value="API & Business" {{ old('subject')==='API & Business'?'selected':'' }}>API &amp; Business</option>
                                <option value="Refund Request" {{ old('subject')==='Refund Request'?'selected':'' }}>Refund Request</option>
                                <option value="Data & Privacy" {{ old('subject')==='Data & Privacy'?'selected':'' }}>Data &amp; Privacy</option>
                                <option value="Partnership" {{ old('subject')==='Partnership'?'selected':'' }}>Partnership</option>
                                <option value="Other" {{ old('subject')==='Other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Message <span style="color:#dc2626;">*</span></label>
                            <textarea id="message" name="message" required placeholder="Tell us how we can help…">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="submit-btn">
                            <i class="fa-solid fa-paper-plane"></i> Send Message
                        </button>
                        <p style="margin-top:14px;font-size:.76rem;color:#94a3b8;text-align:center;">We usually reply within a few hours during business hours.</p>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
