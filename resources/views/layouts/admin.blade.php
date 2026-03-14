<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $siteName = \App\Models\Setting::get('site_name', 'Readiwork'); @endphp
    <title>@yield('title', 'Admin') — {{ $siteName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #1e293b; display: flex; min-height: 100vh; }
        .adm-sidebar { width: 240px; background: #0B1F3B; flex-shrink: 0; display: flex; flex-direction: column; min-height: 100vh; position: sticky; top: 0; }
        .adm-logo { padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,.08); display: flex; align-items: center; gap: 10px; }
        .adm-logo-mark { width: 34px; height: 34px; background: #0FA958; border-radius: 9px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .9rem; flex-shrink: 0; }
        .adm-logo span { font-size: .98rem; font-weight: 800; color: #fff; }
        .adm-logo small { font-size: .65rem; color: rgba(255,255,255,.4); display: block; font-weight: 400; }
        .adm-nav { padding: 16px 12px; flex: 1; display: flex; flex-direction: column; gap: 2px; }
        .adm-nav-label { font-size: .62rem; font-weight: 700; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: .6px; padding: 8px 10px 4px; }
        .adm-nav a { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 9px; color: rgba(255,255,255,.6); font-size: .86rem; font-weight: 500; text-decoration: none; transition: all .15s; }
        .adm-nav a i { width: 16px; text-align: center; font-size: .85rem; }
        .adm-nav a:hover { background: rgba(255,255,255,.07); color: #fff; }
        .adm-nav a.active { background: rgba(15,169,88,.18); color: #6ee7a8; }
        .adm-nav a.active i { color: #0FA958; }
        .adm-sidebar-footer { padding: 14px 12px; border-top: 1px solid rgba(255,255,255,.08); }
        .adm-user { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 9px; }
        .adm-avatar { width: 32px; height: 32px; background: #0FA958; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 700; color: #fff; flex-shrink: 0; }
        .adm-user-info { flex: 1; min-width: 0; }
        .adm-user-info strong { font-size: .82rem; color: #fff; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .adm-user-info small { font-size: .7rem; color: rgba(255,255,255,.4); }
        .adm-logout { color: rgba(255,255,255,.35); font-size: .8rem; text-decoration: none; padding: 4px; transition: color .15s; }
        .adm-logout:hover { color: #f87171; }
        .adm-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .adm-topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; }
        .adm-topbar h1 { font-size: 1.1rem; font-weight: 700; color: #0B1F3B; }
        .adm-topbar-actions { display: flex; gap: 10px; align-items: center; }
        .adm-content { padding: 28px; flex: 1; }
        .adm-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; }
        .stat-card-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .95rem; margin-bottom: 12px; }
        .stat-card-num { font-size: 1.7rem; font-weight: 800; color: #0B1F3B; line-height: 1; margin-bottom: 4px; }
        .stat-card-label { font-size: .78rem; color: #64748b; font-weight: 500; }
        .adm-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin-bottom: 20px; }
        .adm-card-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; }
        .adm-card-head h2 { font-size: .95rem; font-weight: 700; color: #0B1F3B; }
        .adm-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
        .adm-table th { padding: 10px 14px; text-align: left; font-size: .7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .4px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
        .adm-table td { padding: 11px 14px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
        .adm-table tr:last-child td { border-bottom: 0; }
        .adm-table tr:hover td { background: #fafafa; }
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 999px; font-size: .7rem; font-weight: 700; }
        .badge-green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-yellow { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-gray   { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
        .badge-blue   { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .btn-adm { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 8px; font-size: .83rem; font-weight: 600; cursor: pointer; border: none; font-family: inherit; text-decoration: none; transition: all .15s; }
        .btn-adm-primary { background: #0FA958; color: #fff; }
        .btn-adm-primary:hover { background: #0d9048; }
        .btn-adm-outline { background: #fff; color: #0B1F3B; border: 1px solid #e2e8f0; }
        .btn-adm-outline:hover { background: #f8fafc; }
        .btn-adm-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .btn-adm-red:hover { background: #fee2e2; }
        .adm-form-group { margin-bottom: 18px; }
        .adm-form-group label { display: block; font-size: .83rem; font-weight: 600; color: #0B1F3B; margin-bottom: 6px; }
        .adm-form-group input, .adm-form-group select, .adm-form-group textarea { width: 100%; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: .88rem; font-family: inherit; color: #1e293b; outline: none; transition: border-color .2s; }
        .adm-form-group input:focus, .adm-form-group select:focus, .adm-form-group textarea:focus { border-color: #0FA958; }
        .adm-form-group .hint { font-size: .73rem; color: #94a3b8; margin-top: 4px; }
        .adm-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .adm-alert { border-radius: 10px; padding: 11px 16px; font-size: .85rem; margin-bottom: 18px; display: flex; gap: 10px; align-items: flex-start; }
        .adm-alert-green { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .adm-alert-red { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .page-btn { padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 7px; font-size: .78rem; font-weight: 600; color: #64748b; background: #fff; text-decoration: none; transition: all .15s; }
        .page-btn:hover { border-color: #0FA958; color: #0FA958; }
        .page-btn.active { background: #0FA958; color: #fff; border-color: #0FA958; }
        @media (max-width: 1100px) { .adm-stats { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 768px) { .adm-sidebar { display: none; } .adm-content { padding: 16px; } }
    </style>
    @stack('head')
</head>
<body>

@php $adminSession = session('readiwork_admin', []); $adminName = $adminSession['name'] ?? 'Admin'; $adminEmail = $adminSession['email'] ?? ''; @endphp

<!-- ══ SIDEBAR ══ -->
<aside class="adm-sidebar">
    <div class="adm-logo">
        <div class="adm-logo-mark"><i class="fa-solid fa-shield-halved"></i></div>
        <div><span>{{ $siteName }}</span><small>Admin Panel</small></div>
    </div>
    <nav class="adm-nav">
        <div class="adm-nav-label">Main</div>
        <a href="{{ route('admin.dashboard') }}"      @class(['active' => Request::routeIs('admin.dashboard')])><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="{{ route('admin.requests') }}"       @class(['active' => Request::routeIs('admin.requests*')])><i class="fa-solid fa-list-check"></i> All Requests</a>
        <a href="{{ route('admin.transactions') }}"   @class(['active' => Request::routeIs('admin.transactions')])><i class="fa-solid fa-money-bill-transfer"></i> Transactions</a>
        <div class="adm-nav-label">Reports</div>
        <a href="{{ route('admin.analytics') }}"     @class(['active' => Request::routeIs('admin.analytics')])><i class="fa-solid fa-chart-line"></i> Analytics</a>
        <a href="{{ route('admin.export') }}"         @class(['active' => Request::routeIs('admin.export')])><i class="fa-solid fa-file-arrow-down"></i> Export Data</a>
        <div class="adm-nav-label">Management</div>
        <a href="{{ route('admin.admins') }}"         @class(['active' => Request::routeIs('admin.admins*')])><i class="fa-solid fa-user-shield"></i> Admin Users</a>
        <a href="{{ route('admin.announcements') }}"  @class(['active' => Request::routeIs('admin.announcements*')])><i class="fa-solid fa-bullhorn"></i> Announcements</a>
        <div class="adm-nav-label">Logs & Security</div>
        <a href="{{ route('admin.logs') }}"           @class(['active' => Request::routeIs('admin.logs')])><i class="fa-solid fa-scroll"></i> Activity Logs</a>
        <a href="{{ route('admin.login-history') }}"  @class(['active' => Request::routeIs('admin.login-history')])><i class="fa-solid fa-clock-rotate-left"></i> Login History</a>
        <div class="adm-nav-label">Config</div>
        <a href="{{ route('admin.settings') }}"       @class(['active' => Request::routeIs('admin.settings*')])><i class="fa-solid fa-sliders"></i> Settings & Pricing</a>
        <a href="{{ route('home') }}" target="_blank" style="margin-top:4px;"><i class="fa-solid fa-arrow-up-right-from-square"></i> View Site</a>
    </nav>
    <div class="adm-sidebar-footer">
        <div class="adm-user">
            <div class="adm-avatar">{{ strtoupper(substr($adminName, 0, 1)) }}</div>
            <div class="adm-user-info">
                <strong>{{ $adminName }}</strong>
                <small>{{ $adminEmail }}</small>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="adm-logout" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button>
            </form>
        </div>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="adm-main">
    <div class="adm-topbar">
        <h1>@yield('page-title', 'Dashboard')</h1>
        <div class="adm-topbar-actions">@yield('topbar-actions')</div>
    </div>
    <div class="adm-content">
        @if(session('success'))
        <div class="adm-alert adm-alert-green"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="adm-alert adm-alert-red"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
