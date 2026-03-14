@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
<a href="{{ route('admin.export') }}" class="btn-adm btn-adm-outline"><i class="fa-solid fa-file-arrow-down"></i> Export</a>
@endsection

@section('content')
<!-- Stats -->
<div class="adm-stats">
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#eff6ff; color:#2563eb;"><i class="fa-solid fa-list-check"></i></div>
        <div class="stat-card-num">{{ number_format($stats['total']) }}</div>
        <div class="stat-card-label">Total Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-card-num">{{ number_format($stats['completed']) }}</div>
        <div class="stat-card-label">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#fffbeb; color:#d97706;"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-card-num">{{ number_format($stats['pending']) }}</div>
        <div class="stat-card-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fa-solid fa-coins"></i></div>
        <div class="stat-card-num">KES {{ number_format($stats['revenue'], 0) }}</div>
        <div class="stat-card-label">Total Revenue</div>
    </div>
</div>

<!-- Recent Requests -->
<div class="adm-card">
    <div class="adm-card-head">
        <h2>Recent Requests</h2>
        <a href="{{ route('admin.requests') }}" class="btn-adm btn-adm-outline" style="font-size:.75rem;">View All</a>
    </div>
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead><tr><th>ID</th><th>Service</th><th>National ID</th><th>Name</th><th>Status</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($recent as $r)
                <tr>
                    <td>#{{ $r->id }}</td>
                    <td>{{ str_replace('-', ' ', ucfirst($r->service)) }}</td>
                    <td style="font-family:monospace;">{{ $r->national_id }}</td>
                    <td>{{ $r->full_name ?: '—' }}</td>
                    <td>
                        @php $badgeClass = match($r->status) { 'completed' => 'badge-green', 'paid' => 'badge-blue', 'payment_failed' => 'badge-red', default => 'badge-yellow' }; @endphp
                        <span class="badge {{ $badgeClass }}">{{ $r->status }}</span>
                    </td>
                    <td>KES {{ number_format($r->price, 0) }}</td>
                    <td>{{ $r->created_at->format('M d, H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Service Breakdown -->
@if($serviceBreakdown->count())
<div class="adm-card">
    <div class="adm-card-head"><h2>Service Breakdown (Completed)</h2></div>
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead><tr><th>Service</th><th>Completed Checks</th></tr></thead>
            <tbody>
                @foreach($serviceBreakdown as $s)
                <tr>
                    <td>{{ str_replace('-', ' ', ucfirst($s->service)) }}</td>
                    <td>{{ number_format($s->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
