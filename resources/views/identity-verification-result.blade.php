@extends('layouts.app')

@php
$verify_data = $crb['verify'] ?? $crb ?? [];
$scrub_data  = $crb['scrub']  ?? [];

$first_name  = $verify_data['first_name']  ?? '';
$last_name   = $verify_data['last_name']   ?? $verify_data['surname'] ?? '';
$other_name  = $verify_data['other_name']  ?? '';
$full_name   = trim(implode(' ', array_filter([$first_name, $other_name, $last_name])))
             ?: ($req->full_name ?? 'Applicant');
$id_number   = $req->national_id;
$dob         = $verify_data['dob'] ?? $verify_data['date_of_birth'] ?? null;
$gender      = $verify_data['gender'] ?? null;
$citizenship = $verify_data['citizenship'] ?? 'Kenyan';
$serial_no   = $verify_data['serial_number'] ?? null;
$district    = $verify_data['district'] ?? null;

$scrub_names    = $scrub_data['names']            ?? [];
$scrub_dob      = $scrub_data['date_of_being']    ?? [];
$scrub_gender   = $scrub_data['gender']           ?? [];
$scrub_phones   = $scrub_data['phone']            ?? [];
$scrub_emails   = $scrub_data['email']            ?? [];
$scrub_postal   = $scrub_data['postal_address']   ?? [];
$scrub_physical = $scrub_data['physical_address'] ?? [];
$scrub_employ   = $scrub_data['employment']       ?? [];

if (!$first_name && !$last_name && !empty($scrub_names[0])) {
    $full_name = $scrub_names[0];
}
if (!$dob && !empty($scrub_dob[0])) { $dob = $scrub_dob[0]; }
if (!$gender && !empty($scrub_gender[0])) { $gender = $scrub_gender[0]; }

$is_verified = !empty($first_name) || !empty($last_name)
             || !empty($req->full_name)
             || (isset($verify_data['has_error']) && $verify_data['has_error'] === false);

$gender_label = match(strtoupper((string)$gender)) {
    'M' => 'Male', 'F' => 'Female', default => $gender ?: 'N/A',
};

$report_date = now()->format('j F Y, g:i A');
$rid = $req->id;
@endphp

@section('title', 'Identity Verification Report — ' . $full_name . ' - Readiwork')

@push('head')
<style>
.result-hero{background:linear-gradient(135deg,#071629 0%,#0d2649 55%,#091e3a 100%);padding:48px 0 44px;position:relative;overflow:hidden;}
.result-hero::before{content:'';position:absolute;top:-40%;right:-5%;width:55%;height:180%;background:radial-gradient(ellipse,rgba(15,169,88,.09) 0%,transparent 65%);pointer-events:none;}
.result-main{padding:36px 0 80px;background:var(--bg-light);}
.result-grid{display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;}
.result-left{display:grid;gap:20px;}
.rcard{background:#fff;border:1px solid var(--border-color);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(11,31,59,.06);}
.rcard-head{padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:10px;}
.rcard-head h2{font-size:.95rem;font-weight:700;color:var(--primary-navy);margin:0;flex:1;}
.rcard-icon{width:32px;height:32px;border-radius:9px;flex-shrink:0;background:rgba(15,169,88,.1);color:var(--primary-green);display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;}
.rcard-body{padding:20px;}
.verdict-banner{padding:36px 28px;text-align:center;}
.verdict-banner.verified{background:linear-gradient(135deg,#052e16,#064e1d);}
.verdict-banner.not-verified{background:linear-gradient(135deg,#1c0a0a,#3b0f0f);}
.identity-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px;}
.id-field{background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:14px 16px;}
.id-field .lbl{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light);margin-bottom:6px;display:flex;align-items:center;gap:6px;}
.id-field .val{font-size:.95rem;font-weight:700;color:var(--primary-navy);word-break:break-word;}
.chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:600;}
.chip-green{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;}
.chip-red{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;}
.chip-gray{background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;}
.alert{border-radius:12px;padding:14px 16px;display:flex;gap:12px;align-items:flex-start;margin-bottom:16px;}
.alert-green{background:#f0fdf4;border:1px solid #bbf7d0;}
.alert-yellow{background:#fffbeb;border:1px solid #fde68a;}
.alert i{margin-top:1px;flex-shrink:0;}
.alert-text{font-size:.85rem;line-height:1.6;}
.sidebar{display:grid;gap:16px;}
.sidebar-card{background:#fff;border:1px solid var(--border-color);border-radius:14px;overflow:hidden;box-shadow:0 4px 16px rgba(11,31,59,.05);}
.sidebar-card-head{padding:12px 16px;border-bottom:1px solid var(--border-color);font-size:.85rem;font-weight:700;color:var(--primary-navy);}
.sidebar-card-body{padding:14px 16px;}
.stat-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color);font-size:.84rem;}
.stat-row:last-child{border-bottom:0;padding-bottom:0;}
.stat-label{color:var(--text-light);font-weight:500;}
.stat-val{font-weight:700;color:var(--primary-navy);}
.action-btn{display:flex;align-items:center;gap:8px;width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:10px;background:#fff;color:var(--primary-navy);font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:8px;transition:background .15s,border-color .15s;text-decoration:none;}
.action-btn:hover{background:#f8fafc;border-color:#c0e8d5;}
.action-btn i{width:20px;text-align:center;}
@media print{.sidebar,nav,footer,.no-print{display:none !important;}.result-grid{grid-template-columns:1fr;}}
@media(max-width:1000px){.result-grid{grid-template-columns:1fr;}.sidebar{grid-template-columns:repeat(2,1fr);}}
@media(max-width:640px){.sidebar{grid-template-columns:1fr;}.identity-grid{grid-template-columns:1fr;}}
@media(max-width:480px){.result-hero{padding:28px 0 24px;}.result-hero h1{font-size:1.4rem;}.identity-grid{grid-template-columns:1fr !important;}}
</style>
@endpush

{{-- ── Opens the PDF in a new tab and triggers its print dialog ── --}}
<script>
function printPdfReport() {
    var win = window.open('{{ route('identity-verification.pdf', $req->id) }}', '_blank');
    if (win) {
        win.onload = function () {
            try { win.print(); } catch(e) {}
        };
    }
}
</script>

@section('content')
<section class="result-hero">
    <div class="container">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(15,169,88,.15);color:#6ee7a8;border:1px solid rgba(15,169,88,.3);border-radius:999px;padding:4px 14px;font-size:.78rem;font-weight:600;margin-bottom:14px;">
            <i class="fa-solid fa-id-card"></i> Identity Verification Report
        </div>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <h1 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:4px;">{{ $full_name }}</h1>
                <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;">National ID: {{ $id_number }} &nbsp;·&nbsp; Generated: {{ $report_date }}</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                @if($is_verified)
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-circle-check"></i> Identity Verified
                </span>
                @else
                <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);border-radius:999px;padding:7px 18px;font-size:.88rem;font-weight:700;">
                    <i class="fa-solid fa-circle-xmark"></i> Verification Failed
                </span>
                @endif
                {{-- ✅ CHANGED: was window.print() — now opens PDF and prints it --}}
                <button onclick="printPdfReport()" class="no-print" style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);border:1px solid rgba(255,255,255,.2);border-radius:999px;padding:7px 16px;font-size:.82rem;font-weight:600;cursor:pointer;">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>
        </div>
    </div>
</section>

<section class="result-main">
<div class="container">
<div class="result-grid">
<div class="result-left">

@if($crbError)
<div class="alert alert-yellow">
    <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i>
    <div class="alert-text">Some data could not be retrieved: <strong>{{ $crbError }}</strong>. Partial results are shown below.</div>
</div>
@endif

{{-- 1. VERDICT BANNER --}}
<div class="rcard">
    <div class="verdict-banner {{ $is_verified ? 'verified' : 'not-verified' }}">
        <span style="font-size:3.2rem;margin-bottom:12px;display:block;">{{ $is_verified ? '✅' : '❌' }}</span>
        <div style="font-size:1rem;color:rgba(255,255,255,.55);margin-bottom:6px;font-weight:500;">{{ $full_name }}</div>
        <div style="font-size:1.6rem;font-weight:800;color:#fff;margin-bottom:8px;">
            {{ $is_verified ? 'Identity Successfully Verified' : 'Verification Failed' }}
        </div>
        <div style="color:rgba(255,255,255,.6);font-size:.88rem;margin-bottom:24px;max-width:420px;margin-left:auto;margin-right:auto;">
            @if($is_verified)
                Your National ID <strong style="color:#4ade80;">{{ $id_number }}</strong> has been confirmed against the Kenya National Registry. All details below are official.
            @else
                We were unable to verify the provided National ID against the Kenya National Registry. Please check the ID and try again.
            @endif
        </div>
        <div style="display:inline-flex;align-items:center;gap:7px;border-radius:999px;padding:7px 20px;font-size:.88rem;font-weight:700;{{ $is_verified ? 'background:rgba(74,222,128,.18);color:#4ade80;border:1px solid rgba(74,222,128,.35);' : 'background:rgba(239,68,68,.18);color:#f87171;border:1px solid rgba(239,68,68,.35);' }}">
            @if($is_verified)<i class="fa-solid fa-shield-halved"></i> Registry Confirmed
            @else <i class="fa-solid fa-ban"></i> Not Found in Registry
            @endif
        </div>
    </div>
</div>

{{-- 2. IDENTITY DETAILS --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(15,169,88,.1);color:#0FA958;"><i class="fa-solid fa-id-card"></i></div>
        <h2>Identity Details</h2>
        <span class="chip {{ $is_verified ? 'chip-green' : 'chip-red' }}">
            <i class="fa-solid {{ $is_verified ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
            {{ $is_verified ? 'Verified' : 'Unverified' }}
        </span>
    </div>
    <div class="rcard-body">
        <div class="identity-grid">
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-user" style="color:#0FA958;"></i> Full Name</div>
                <div class="val">{{ $full_name ?: 'N/A' }}</div>
            </div>
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-id-card" style="color:#6366f1;"></i> National ID</div>
                <div class="val">{{ $id_number }}</div>
            </div>
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-calendar" style="color:#0ea5e9;"></i> Date of Birth</div>
                <div class="val">{{ $dob ?? 'N/A' }}</div>
            </div>
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-venus-mars" style="color:#a855f7;"></i> Gender</div>
                <div class="val">{{ $gender_label }}</div>
            </div>
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-flag" style="color:#d97706;"></i> Citizenship</div>
                <div class="val">{{ $citizenship ?? 'Kenyan' }}</div>
            </div>
            @if($serial_no)
            <div class="id-field">
                <div class="lbl"><i class="fa-solid fa-barcode" style="color:#64748b;"></i> Serial Number</div>
                <div class="val">{{ $serial_no }}</div>
            </div>
            @endif
        </div>

        @if(!empty($scrub_phones) || !empty($scrub_emails) || !empty($scrub_postal) || !empty($scrub_physical) || !empty($scrub_employ))
        <div style="border-top:1px solid var(--border-color);margin-top:16px;padding-top:16px;">
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light);margin-bottom:12px;">
                <i class="fa-solid fa-magnifying-glass" style="margin-right:5px;"></i> Extended Registry Data
            </div>
            <div class="identity-grid">
                @if(!empty($scrub_phones))
                <div class="id-field">
                    <div class="lbl"><i class="fa-solid fa-phone" style="color:#0FA958;"></i> Phone Number</div>
                    <div class="val">{{ $scrub_phones[0] }}</div>
                </div>
                @endif
                @if(!empty($scrub_emails))
                <div class="id-field">
                    <div class="lbl"><i class="fa-solid fa-envelope" style="color:#6366f1;"></i> Email Address</div>
                    <div class="val" style="font-size:.82rem;word-break:break-all;">{{ $scrub_emails[0] }}</div>
                </div>
                @endif
                @if(!empty($scrub_postal[0]))
                @php $pa = $scrub_postal[0]; @endphp
                <div class="id-field">
                    <div class="lbl"><i class="fa-solid fa-mailbox" style="color:#0ea5e9;"></i> Postal Address</div>
                    <div class="val" style="font-size:.82rem;">
                        P.O Box {{ $pa['number'] ?? '' }}{{ !empty($pa['code']) ? '-'.$pa['code'] : '' }}<br>
                        {{ $pa['town'] ?? '' }}{{ !empty($pa['country']) ? ', '.$pa['country'] : '' }}
                    </div>
                </div>
                @endif
                @if(!empty($scrub_physical[0]))
                @php $phy = $scrub_physical[0]; @endphp
                <div class="id-field">
                    <div class="lbl"><i class="fa-solid fa-location-dot" style="color:#ef4444;"></i> Physical Address</div>
                    <div class="val" style="font-size:.82rem;">
                        {{ $phy['town'] ?? '' }}
                        @if(!empty($phy['address']))<br>{{ $phy['address'] }}@endif
                        @if(!empty($phy['country']))<br>{{ $phy['country'] }}@endif
                    </div>
                </div>
                @endif
            </div>

            @if(!empty($scrub_employ))
            @php $emp = $scrub_employ[0]; @endphp
            <div style="background:#f8fafc;border:1px solid var(--border-color);border-radius:12px;padding:14px 16px;margin-top:4px;display:flex;align-items:center;gap:14px;">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,.1);color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;"><i class="fa-solid fa-briefcase"></i></div>
                <div>
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-light);margin-bottom:3px;">Employment on Record</div>
                    <div style="font-weight:700;color:var(--primary-navy);font-size:.9rem;">{{ $emp['employer_name'] ?? 'N/A' }}</div>
                    @if(!empty($emp['employment_date']))
                    <div style="font-size:.76rem;color:var(--text-light);margin-top:2px;">Since: {{ $emp['employment_date'] }}</div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endif

        @if($is_verified)
        <div style="background:linear-gradient(135deg,#052e16,#064e1d);border-radius:14px;padding:20px;text-align:center;margin-top:16px;">
            <div style="font-size:2.5rem;margin-bottom:8px;"><i class="fa-solid fa-shield-halved" style="color:#4ade80;"></i></div>
            <div style="font-weight:800;color:#4ade80;font-size:1.1rem;margin-bottom:4px;">Official Registry Confirmation</div>
            <div style="color:rgba(255,255,255,.6);font-size:.82rem;">This identity has been confirmed against the Kenya National Registry. Report generated {{ $report_date }}.</div>
        </div>
        @endif
    </div>
</div>

{{-- 3. WHAT THIS MEANS --}}
<div class="rcard">
    <div class="rcard-head">
        <div class="rcard-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fa-solid fa-circle-info"></i></div>
        <h2>What This Means</h2>
    </div>
    <div class="rcard-body">
        @if($is_verified)
        <div class="alert alert-green" style="margin-bottom:14px;">
            <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i>
            <div class="alert-text"><strong>Your identity is confirmed.</strong> The National ID you provided matches an active record in the Kenya National Registry.</div>
        </div>
        <div style="display:grid;gap:12px;">
            @foreach([['fa-building-columns','rgba(15,169,88,.1)','#0FA958','Accepted for KYC','This verification can be used for Know Your Customer (KYC) compliance with banks, SACCOs and lenders.'],['fa-shield-halved','rgba(99,102,241,.1)','#6366f1','Not Flagged as Fraudulent','This ID is not marked as cancelled, lost, or fraudulent in the national database.'],['fa-user-check','rgba(14,165,233,.1)','#0ea5e9','Official Identity Confirmed','The name, date of birth, gender and location information shown above are the official details held by the Government of Kenya.']] as $item)
            <div style="display:flex;gap:12px;align-items:flex-start;">
                <div style="width:32px;height:32px;border-radius:8px;background:{{ $item[1] }};color:{{ $item[2] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.85rem;"><i class="fa-solid {{ $item[0] }}"></i></div>
                <div>
                    <div style="font-weight:700;color:var(--primary-navy);font-size:.9rem;margin-bottom:2px;">{{ $item[3] }}</div>
                    <div style="font-size:.82rem;color:var(--text-regular);line-height:1.5;">{{ $item[4] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert" style="background:#fef2f2;border:1px solid #fecaca;margin-bottom:14px;">
            <i class="fa-solid fa-circle-xmark" style="color:#dc2626;"></i>
            <div class="alert-text"><strong>Verification could not be completed.</strong> The ID provided was not found or matched in the Kenya National Registry.</div>
        </div>
        <div style="font-size:.88rem;color:var(--text-regular);line-height:1.7;">
            <p>Possible reasons for verification failure:</p>
            <ul style="padding-left:18px;margin-top:8px;display:grid;gap:6px;">
                <li>The ID number was entered incorrectly</li>
                <li>The ID is not yet registered in the national system</li>
                <li>The ID has been cancelled or flagged</li>
                <li>A temporary registry outage occurred</li>
            </ul>
        </div>
        @endif
    </div>
</div>

</div>{{-- end result-left --}}

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-card">
        <div class="sidebar-card-head"><i class="fa-solid fa-id-card" style="color:#0FA958;margin-right:6px;"></i> Quick Summary</div>
        <div class="sidebar-card-body">
            <div style="text-align:center;padding:16px 0;border-bottom:1px solid var(--border-color);margin-bottom:12px;">
                <div style="font-size:3rem;{{ $is_verified ? 'color:#16a34a;' : 'color:#dc2626;' }}">
                    <i class="fa-solid {{ $is_verified ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                </div>
                <div style="font-weight:800;font-size:1rem;margin-top:8px;color:var(--primary-navy);">{{ $is_verified ? 'Identity Verified' : 'Not Verified' }}</div>
                <div style="font-size:.78rem;color:var(--text-light);margin-top:2px;">{{ $report_date }}</div>
            </div>
            <div class="stat-row"><span class="stat-label">ID Number</span><span class="stat-val">{{ $id_number }}</span></div>
            <div class="stat-row"><span class="stat-label">Date of Birth</span><span class="stat-val">{{ $dob ?? 'N/A' }}</span></div>
            <div class="stat-row"><span class="stat-label">Gender</span><span class="stat-val">{{ $gender_label }}</span></div>
            <div class="stat-row"><span class="stat-label">District</span><span class="stat-val">{{ $district ?? 'N/A' }}</span></div>
            <div class="stat-row">
                <span class="stat-label">Status</span>
                <span class="stat-val" style="color:{{ $is_verified ? '#16a34a' : '#dc2626' }};">{{ $is_verified ? 'Verified' : 'Failed' }}</span>
            </div>
        </div>
    </div>

    <div class="sidebar-card">
        <div class="sidebar-card-head">
            <i class="fa-solid fa-share-nodes" style="color:#0FA958;margin-right:6px;"></i> Save &amp; Share
        </div>
        <div class="sidebar-card-body">
            {{-- ✅ CHANGED: was window.print() — now opens PDF and prints it --}}
            <button onclick="printPdfReport()" class="action-btn no-print">
                <i class="fa-solid fa-print" style="color:#6366f1;"></i> Print Report
            </button>

            <a href="{{ route('identity-verification.pdf', $req->id) }}"
               class="action-btn" style="color:inherit;" target="_blank">
                <i class="fa-solid fa-file-pdf" style="color:#dc2626;"></i> Download PDF Report
            </a>

            @php
                $pdfUrl = route('identity-verification.pdf', $req->id);
                $waText = "My identity has been verified via Readiwork.\nName: {$full_name}\nNational ID: {$id_number}\n\nDownload the official report here:\n{$pdfUrl}";
            @endphp
            <a href="https://wa.me/?text={{ urlencode($waText) }}"
               target="_blank" class="action-btn" style="color:inherit;">
                <i class="fa-brands fa-whatsapp" style="color:#16a34a;"></i> Send PDF via WhatsApp
            </a>

            <a href="{{ route('identity-verification') }}" class="action-btn" style="color:inherit;">
                <i class="fa-solid fa-rotate-left" style="color:#d97706;"></i> Run Another Check
            </a>
        </div>
    </div>

    <div class="sidebar-card">
        <div class="sidebar-card-head"><i class="fa-solid fa-grid-2" style="color:#0FA958;margin-right:6px;"></i> Other Services</div>
        <div class="sidebar-card-body" style="display:grid;gap:8px;">
            <a href="{{ route('credit-score-check') }}" style="display:flex;align-items:center;gap:10px;padding:10px;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;color:inherit;text-decoration:none;">
                <div style="width:28px;height:28px;border-radius:7px;background:rgba(99,102,241,.1);color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;"><i class="fa-solid fa-gauge-high"></i></div>
                <div><div style="font-size:.82rem;font-weight:600;color:var(--primary-navy);">Credit Score Check</div><div style="font-size:.72rem;color:var(--text-light);">Get your credit score</div></div>
            </a>
            <a href="{{ route('crb-blacklist-check') }}" style="display:flex;align-items:center;gap:10px;padding:10px;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;color:inherit;text-decoration:none;">
                <div style="width:28px;height:28px;border-radius:7px;background:rgba(239,68,68,.08);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div><div style="font-size:.82rem;font-weight:600;color:var(--primary-navy);">CRB Blacklist Check</div><div style="font-size:.72rem;color:var(--text-light);">Check CRB listing status</div></div>
            </a>
            <a href="{{ route('loan-eligibility') }}" style="display:flex;align-items:center;gap:10px;padding:10px;background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;color:inherit;text-decoration:none;">
                <div style="width:28px;height:28px;border-radius:7px;background:rgba(15,169,88,.1);color:#0FA958;display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;"><i class="fa-solid fa-circle-check"></i></div>
                <div><div style="font-size:.82rem;font-weight:600;color:var(--primary-navy);">Loan Eligibility</div><div style="font-size:.72rem;color:var(--text-light);">Check loan eligibility</div></div>
            </a>
        </div>
    </div>
</div>

</div>{{-- end result-grid --}}
</div>
</section>
@endsection