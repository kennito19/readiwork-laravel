@extends('layouts.admin')
@section('title', 'Settings')
@section('page-title', 'Site Settings')

@push('head')
<style>
.tabs-nav { display:flex; gap:4px; border-bottom:2px solid #e2e8f0; margin-bottom:24px; flex-wrap:wrap; }
.tabs-nav button { background:none; border:none; padding:10px 18px; font-size:.86rem; font-weight:600; color:#64748b; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all .15s; font-family:inherit; border-radius:6px 6px 0 0; }
.tabs-nav button:hover { color:#0B1F3B; background:#f8fafc; }
.tabs-nav button.active { color:#0FA958; border-bottom-color:#0FA958; background:#f0fdf4; }
.tab-pane { display:none; }
.tab-pane.active { display:block; }
.settings-section { margin-bottom:32px; }
.settings-section h3 { font-size:.82rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid #f1f5f9; }
.adm-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
.adm-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.color-wrap { display:flex; gap:8px; align-items:center; }
.color-wrap input[type=color] { width:44px; height:38px; padding:2px; border:1.5px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#fff; }
.color-wrap input[type=text] { flex:1; }
.logo-preview { width:120px; height:60px; object-fit:contain; border:1.5px solid #e2e8f0; border-radius:8px; padding:6px; background:#f8fafc; display:block; margin-bottom:10px; }
.logo-preview-placeholder { width:120px; height:60px; border:1.5px dashed #cbd5e1; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:.75rem; margin-bottom:10px; }
.pw-card { max-width:480px; }
@media(max-width:900px){ .adm-grid-3{ grid-template-columns:1fr 1fr; } }
@media(max-width:600px){ .adm-grid-3,.adm-grid-2{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

@php
$brand   = $grouped['branding']  ?? [];
$contact = $grouped['contact']   ?? [];
$social  = $grouped['social']    ?? [];
$content = $grouped['content']   ?? [];
$pricing = $grouped['pricing']   ?? [];
$general = $grouped['general']   ?? [];

$logoPath = $brand['logo_path']['value'] ?? '';
$logoSrc  = $logoPath ? asset($logoPath) : asset('logo.png');
@endphp

<!-- Tab Nav -->
<div class="tabs-nav" id="settingsTabs">
    <button class="active" onclick="switchTab('branding',this)"><i class="fa-solid fa-palette"></i> Branding</button>
    <button onclick="switchTab('contact',this)"><i class="fa-solid fa-address-book"></i> Contact & Social</button>
    <button onclick="switchTab('content',this)"><i class="fa-solid fa-pen-nib"></i> Content</button>
    <button onclick="switchTab('pricing',this)"><i class="fa-solid fa-tag"></i> Pricing</button>
    <button onclick="switchTab('security',this)" id="tab-security-btn"><i class="fa-solid fa-lock"></i> Security</button>
</div>

{{-- ══ BRANDING ══ --}}
<div class="tab-pane active" id="tab-branding">
    <div class="adm-card">
        <div class="adm-card-head"><h2>Branding</h2></div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="settings-section">
                    <h3>Identity</h3>
                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label>Site Name</label>
                            <input type="text" name="site_name" value="{{ $general['site_name']['value'] ?? $brand['site_name']['value'] ?? 'Readiwork' }}" maxlength="100">
                        </div>
                        <div class="adm-form-group">
                            <label>Site Tagline</label>
                            <input type="text" name="site_tagline" value="{{ $brand['site_tagline']['value'] ?? '' }}" maxlength="200">
                        </div>
                    </div>
                </div>
                <div class="settings-section">
                    <h3>Colours</h3>
                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label>Brand Colour (Green)</label>
                            <div class="color-wrap">
                                <input type="color" id="brand_color_picker" value="{{ $brand['brand_color']['value'] ?? '#0FA958' }}" oninput="document.getElementById('brand_color_text').value=this.value">
                                <input type="text" name="brand_color" id="brand_color_text" value="{{ $brand['brand_color']['value'] ?? '#0FA958' }}" maxlength="7" oninput="document.getElementById('brand_color_picker').value=this.value">
                            </div>
                            <span class="hint">Used for buttons, highlights and active states.</span>
                        </div>
                        <div class="adm-form-group">
                            <label>Secondary Colour (Navy)</label>
                            <div class="color-wrap">
                                <input type="color" id="secondary_color_picker" value="{{ $brand['secondary_color']['value'] ?? '#0B1F3B' }}" oninput="document.getElementById('secondary_color_text').value=this.value">
                                <input type="text" name="secondary_color" id="secondary_color_text" value="{{ $brand['secondary_color']['value'] ?? '#0B1F3B' }}" maxlength="7" oninput="document.getElementById('secondary_color_picker').value=this.value">
                            </div>
                            <span class="hint">Used for the header, footer and sidebar.</span>
                        </div>
                    </div>
                </div>
                <div class="settings-section">
                    <h3>SEO</h3>
                    <div class="adm-form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" rows="3" maxlength="300">{{ $content['meta_description']['value'] ?? '' }}</textarea>
                        <span class="hint">Shown in search engine results. Keep under 160 characters.</span>
                    </div>
                </div>
                <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-floppy-disk"></i> Save Branding</button>
            </form>

            <hr style="margin:28px 0;border:none;border-top:1px solid #f1f5f9;">

            <div class="settings-section">
                <h3>Logo</h3>
                <img src="{{ $logoSrc }}" alt="Logo" class="logo-preview" id="logoPreviewImg">
                <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="adm-form-group" style="max-width:400px;">
                        <label>Upload New Logo</label>
                        <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" onchange="previewLogo(this)">
                        <span class="hint">PNG, JPG, SVG or WebP. Max 2 MB. Recommended: transparent PNG, wide format.</span>
                        @error('logo')<span style="color:#dc2626;font-size:.8rem;">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-upload"></i> Upload Logo</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══ CONTACT & SOCIAL ══ --}}
<div class="tab-pane" id="tab-contact">
    <div class="adm-card">
        <div class="adm-card-head"><h2>Contact & Social</h2></div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="settings-section">
                    <h3>Contact Details</h3>
                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label>Contact Email</label>
                            <input type="email" name="contact_email" value="{{ $contact['contact_email']['value'] ?? '' }}" maxlength="150">
                        </div>
                        <div class="adm-form-group">
                            <label>Support Email</label>
                            <input type="email" name="support_email" value="{{ $contact['support_email']['value'] ?? '' }}" maxlength="150">
                        </div>
                        <div class="adm-form-group">
                            <label>Support Phone</label>
                            <input type="tel" name="support_phone" value="{{ $contact['support_phone']['value'] ?? '' }}" maxlength="20">
                        </div>
                        <div class="adm-form-group">
                            <label>WhatsApp Number <span class="hint" style="display:inline;">(digits only, e.g. 254722175570)</span></label>
                            <input type="tel" name="support_whatsapp" value="{{ $contact['support_whatsapp']['value'] ?? '' }}" maxlength="20">
                        </div>
                        <div class="adm-form-group">
                            <label>Office Address</label>
                            <input type="text" name="office_address" value="{{ $contact['office_address']['value'] ?? '' }}" maxlength="200">
                        </div>
                    </div>
                </div>
                <div class="settings-section">
                    <h3>Social Media Links</h3>
                    <div class="adm-grid-3">
                        <div class="adm-form-group">
                            <label><i class="fa-brands fa-facebook-f" style="color:#1877f2;margin-right:6px;"></i>Facebook URL</label>
                            <input type="url" name="social_facebook" value="{{ $social['social_facebook']['value'] ?? '' }}" maxlength="300" placeholder="https://facebook.com/...">
                        </div>
                        <div class="adm-form-group">
                            <label><i class="fa-brands fa-x-twitter" style="color:#000;margin-right:6px;"></i>Twitter / X URL</label>
                            <input type="url" name="social_twitter" value="{{ $social['social_twitter']['value'] ?? '' }}" maxlength="300" placeholder="https://x.com/...">
                        </div>
                        <div class="adm-form-group">
                            <label><i class="fa-brands fa-instagram" style="color:#e1306c;margin-right:6px;"></i>Instagram URL</label>
                            <input type="url" name="social_instagram" value="{{ $social['social_instagram']['value'] ?? '' }}" maxlength="300" placeholder="https://instagram.com/...">
                        </div>
                        <div class="adm-form-group">
                            <label><i class="fa-brands fa-linkedin-in" style="color:#0077b5;margin-right:6px;"></i>LinkedIn URL</label>
                            <input type="url" name="social_linkedin" value="{{ $social['social_linkedin']['value'] ?? '' }}" maxlength="300" placeholder="https://linkedin.com/...">
                        </div>
                        <div class="adm-form-group">
                            <label><i class="fa-brands fa-tiktok" style="color:#000;margin-right:6px;"></i>TikTok URL</label>
                            <input type="url" name="social_tiktok" value="{{ $social['social_tiktok']['value'] ?? '' }}" maxlength="300" placeholder="https://tiktok.com/...">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-floppy-disk"></i> Save Contact & Social</button>
            </form>
        </div>
    </div>
</div>

{{-- ══ CONTENT ══ --}}
<div class="tab-pane" id="tab-content">
    <div class="adm-card">
        <div class="adm-card-head"><h2>Site Content</h2></div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="settings-section">
                    <h3>Homepage Hero</h3>
                    <div class="adm-form-group">
                        <label>Hero Title</label>
                        <input type="text" name="hero_title" value="{{ $content['hero_title']['value'] ?? '' }}" maxlength="200">
                    </div>
                    <div class="adm-form-group">
                        <label>Hero Subtitle</label>
                        <textarea name="hero_subtitle" rows="3" maxlength="400">{{ $content['hero_subtitle']['value'] ?? '' }}</textarea>
                    </div>
                </div>
                <div class="settings-section">
                    <h3>Footer</h3>
                    <div class="adm-form-group">
                        <label>Footer Tagline</label>
                        <textarea name="footer_tagline" rows="2" maxlength="300">{{ $content['footer_tagline']['value'] ?? '' }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-floppy-disk"></i> Save Content</button>
            </form>
        </div>
    </div>
</div>

{{-- ══ PRICING ══ --}}
<div class="tab-pane" id="tab-pricing">
    <div class="adm-card">
        <div class="adm-card-head"><h2>Service Pricing (KES)</h2></div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="adm-grid-2">
                    @foreach($pricing as $key => $setting)
                    <div class="adm-form-group">
                        <label>{{ $setting['label'] }}</label>
                        <input type="number" name="{{ $key }}" value="{{ $setting['value'] }}" min="1" max="99999" step="1">
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-floppy-disk"></i> Save Pricing</button>
            </form>
        </div>
    </div>
</div>

{{-- ══ SECURITY ══ --}}
<div class="tab-pane" id="tab-security">
    <div class="adm-card pw-card">
        <div class="adm-card-head"><h2>Change Password</h2></div>
        <div style="padding:24px;">
            @if(session('pw_success'))
            <div class="adm-alert adm-alert-green"><i class="fa-solid fa-circle-check"></i> {{ session('pw_success') }}</div>
            @endif
            @if(session('pw_error'))
            <div class="adm-alert adm-alert-red"><i class="fa-solid fa-circle-xmark"></i> {{ session('pw_error') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.settings.password') }}">
                @csrf
                <div class="adm-form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" autocomplete="current-password" required>
                </div>
                <div class="adm-form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" autocomplete="new-password" required minlength="8">
                    <span class="hint">Minimum 8 characters.</span>
                </div>
                <div class="adm-form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-key"></i> Update Password</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tabs-nav button').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
    history.replaceState(null, '', '#tab-' + name);
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreviewImg').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-open tab from URL hash
document.addEventListener('DOMContentLoaded', function () {
    var hash = window.location.hash;
    if (hash) {
        var name = hash.replace('#tab-', '');
        var btn = document.querySelector('.tabs-nav button[onclick*="' + name + '"]');
        if (btn) switchTab(name, btn);
    }
});
</script>
@endpush
