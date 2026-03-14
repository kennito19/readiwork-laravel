@extends('layouts.admin')
@section('title', 'Login History')
@section('page-title', 'Login History')

@section('content')
<div class="adm-stats" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-card-icon" style="background:rgba(15,169,88,.12);color:#0FA958;"><i class="fa-solid fa-right-to-bracket"></i></div>
        <div class="stat-card-num">{{ number_format($totalSuccess) }}</div>
        <div class="stat-card-label">Successful Logins</div>
        <div class="stat-card-delta" style="color:#16a34a;">{{ $todayLogins }} today</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:rgba(239,68,68,.12);color:#ef4444;"><i class="fa-solid fa-ban"></i></div>
        <div class="stat-card-num">{{ number_format($totalFailed) }}</div>
        <div class="stat-card-label">Failed Attempts</div>
        <div class="stat-card-delta" style="color:#dc2626;">potential security risk</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:rgba(99,102,241,.12);color:#6366f1;"><i class="fa-solid fa-list"></i></div>
        <div class="stat-card-num">{{ number_format($total) }}</div>
        <div class="stat-card-label">Total Filtered</div>
    </div>
</div>

<form method="get" style="margin-bottom:18px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:200px;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.85rem;"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Search email or IP…" style="padding:9px 12px 9px 32px;border:1.5px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:.85rem;width:100%;outline:none;" onfocus="this.style.borderColor='#0FA958'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <select name="status" style="padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit;color:#1e293b;background:#fff;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="success" {{ $filterStatus==='success'?'selected':'' }}>Success</option>
            <option value="failed"  {{ $filterStatus==='failed' ?'selected':'' }}>Failed</option>
        </select>
        <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-filter"></i> Filter</button>
        @if($filterStatus || $q)<a href="{{ route('admin.login-history') }}" class="btn-adm btn-adm-outline"><i class="fa-solid fa-xmark"></i> Clear</a>@endif
    </div>
</form>

<div class="adm-card" style="margin-bottom:0;">
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Admin Email</th><th>Status</th><th>IP Address</th><th>Browser / Device</th><th>Date &amp; Time</th></tr>
            </thead>
            <tbody>
                @forelse($history as $r)
                <tr>
                    <td style="font-family:monospace;font-size:.73rem;color:#94a3b8;">{{ $r->id }}</td>
                    <td style="font-weight:500;font-size:.85rem;">{{ $r->admin_email }}</td>
                    <td>
                        @if($r->status === 'success')
                            <span class="badge badge-green"><i class="fa-solid fa-circle-check"></i> Success</span>
                        @else
                            <span class="badge badge-red"><i class="fa-solid fa-circle-xmark"></i> Failed</span>
                        @endif
                    </td>
                    <td style="font-family:monospace;font-size:.82rem;">{{ $r->ip_address ?: '—' }}</td>
                    <td style="font-size:.73rem;color:#64748b;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->user_agent ?? '' }}">
                        @php
                            $ua = $r->user_agent ?? '';
                            $browser = 'Unknown';
                            if (str_contains($ua,'Chrome'))        $browser = 'Chrome';
                            elseif (str_contains($ua,'Firefox'))   $browser = 'Firefox';
                            elseif (str_contains($ua,'Safari'))    $browser = 'Safari';
                            elseif (str_contains($ua,'Edge'))      $browser = 'Edge';
                            $device = str_contains($ua,'Mobile') ? 'Mobile' : (str_contains($ua,'Tablet') ? 'Tablet' : 'Desktop');
                        @endphp
                        {{ $browser }} &middot; {{ $device }}
                    </td>
                    <td style="font-size:.75rem;color:#64748b;white-space:nowrap;">{{ $r->created_at->format('d M Y H:i:s') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:32px;">No login history yet. Login attempts will appear here after you log in/out.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($history->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="font-size:.78rem;color:#64748b;">Showing {{ $history->firstItem() }}–{{ $history->lastItem() }} of {{ $total }}</div>
        <div>{{ $history->appends(request()->query())->links('pagination::simple-default') }}</div>
    </div>
    @endif
</div>
@endsection
