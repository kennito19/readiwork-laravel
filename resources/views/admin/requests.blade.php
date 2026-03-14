@extends('layouts.admin')
@section('title', 'All Requests')
@section('page-title', 'All Requests')

@section('topbar-actions')
<a href="{{ route('admin.export') }}" class="btn-adm btn-adm-outline"><i class="fa-solid fa-file-arrow-down"></i> Export</a>
@endsection

@section('content')
<div class="adm-card">
    <div class="adm-card-head">
        <h2>Verification Requests</h2>
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
            <input name="search" placeholder="ID, Name, Receipt…" value="{{ request('search') }}" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:7px;font-size:.82rem;font-family:inherit;width:200px;">
            <select name="status" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:7px;font-size:.82rem;font-family:inherit;">
                <option value="">All Statuses</option>
                @foreach(['completed','paid','pending_payment','payment_failed','refunded'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
            <select name="service" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:7px;font-size:.82rem;font-family:inherit;">
                <option value="">All Services</option>
                @foreach(['loan-eligibility','identity-verification','credit-score-check','crb-blacklist-check','full-credit-report'] as $svc)
                <option value="{{ $svc }}" @selected(request('service') === $svc)>{{ ucfirst(str_replace('-', ' ', $svc)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead><tr><th>ID</th><th>Service</th><th>National ID</th><th>Name</th><th>Status</th><th>Price</th><th>Receipt</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @foreach($requests as $r)
                <tr>
                    <td>#{{ $r->id }}</td>
                    <td><span style="font-size:.75rem;">{{ str_replace('-', ' ', ucfirst($r->service)) }}</span></td>
                    <td style="font-family:monospace;font-size:.82rem;">{{ $r->national_id }}</td>
                    <td>{{ $r->full_name ?: '—' }}</td>
                    <td>
                        @php $bc = match($r->status) { 'completed' => 'badge-green', 'paid' => 'badge-blue', 'payment_failed' => 'badge-red', default => 'badge-yellow' }; @endphp
                        <span class="badge {{ $bc }}">{{ str_replace('_',' ',$r->status) }}</span>
                    </td>
                    <td>KES {{ number_format($r->price, 0) }}</td>
                    <td style="font-size:.75rem; font-family:monospace;">{{ $r->mpesa_receipt_number ?: '—' }}</td>
                    <td style="font-size:.75rem; white-space:nowrap;">{{ $r->created_at->format('M d, H:i') }}</td>
                    <td><a href="{{ route('admin.requests.show', $r->id) }}" class="btn-adm btn-adm-outline" style="font-size:.72rem;padding:5px 10px;">View</a></td>
                </tr>
                @endforeach
                @if($requests->isEmpty())
                <tr><td colspan="9" style="text-align:center;padding:24px;color:#94a3b8;">No requests found.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div style="padding:14px 16px;display:flex;gap:6px;flex-wrap:wrap;">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
