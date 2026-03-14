@extends('layouts.admin')
@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')
<div style="margin-bottom:18px;">
    <form method="get">
        <div style="display:flex;gap:10px;">
            <div style="position:relative;flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.85rem;"></i>
                <input type="text" name="q" value="{{ $q }}" placeholder="Search action, email, details…" style="padding:9px 12px 9px 32px;border:1.5px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:.85rem;width:100%;outline:none;" onfocus="this.style.borderColor='#0FA958'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-filter"></i> Search</button>
            @if($q)<a href="{{ route('admin.logs') }}" class="btn-adm btn-adm-outline"><i class="fa-solid fa-xmark"></i> Clear</a>@endif
        </div>
    </form>
</div>

<div class="adm-card" style="margin-bottom:0;">
    <div class="adm-card-head">
        <h2><i class="fa-solid fa-scroll" style="color:#0FA958;margin-right:6px;"></i> Activity Logs
            <span style="font-size:.78rem;font-weight:500;color:#64748b;margin-left:8px;">{{ number_format($total) }} entries</span>
        </h2>
    </div>
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Admin</th><th>Action</th><th>Details</th><th>IP Address</th><th>Timestamp</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $r)
                <tr>
                    <td style="font-family:monospace;font-size:.73rem;color:#94a3b8;">{{ $r->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div style="width:26px;height:26px;background:#6366f1;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr($r->admin_email ?? 'A', 0, 1)) }}
                            </div>
                            <span style="font-size:.8rem;font-weight:500;">{{ $r->admin_email ?: '—' }}</span>
                        </div>
                    </td>
                    <td>
                        @php
                            $actionColor = str_contains($r->action,'delete') ? '#ef4444' :
                                          (str_contains($r->action,'create') ? '#0FA958' :
                                          (str_contains($r->action,'toggle') ? '#d97706' :
                                          (str_contains($r->action,'password') ? '#6366f1' : '#64748b')));
                            $actionIcon = str_starts_with($r->action,'admin.') ? 'fa-user-shield' :
                                         (str_starts_with($r->action,'announcement.') ? 'fa-bullhorn' :
                                         (str_starts_with($r->action,'settings.') ? 'fa-sliders' : 'fa-scroll'));
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:700;color:{{ $actionColor }};">
                            <i class="fa-solid {{ $actionIcon }}"></i>
                            {{ $r->action }}
                        </span>
                    </td>
                    <td style="font-size:.78rem;color:#475569;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->details ?? '' }}">{{ $r->details ?: '—' }}</td>
                    <td style="font-family:monospace;font-size:.75rem;color:#64748b;">{{ $r->ip_address ?: '—' }}</td>
                    <td style="font-size:.75rem;color:#64748b;white-space:nowrap;">{{ $r->created_at->format('d M Y H:i:s') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:32px;">No activity logs yet. Logs are recorded as admins perform actions.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="font-size:.78rem;color:#64748b;">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $total }}</div>
        <div class="pagination">{{ $logs->appends(request()->query())->links('pagination::simple-default') }}</div>
    </div>
    @endif
</div>
@endsection
