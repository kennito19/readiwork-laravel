@extends('layouts.admin')
@section('title', 'Transactions')
@section('page-title', 'Transactions')

@section('topbar-actions')
<a href="{{ route('admin.export', ['status' => 'completed']) }}" class="btn-adm btn-adm-outline"><i class="fa-solid fa-file-arrow-down"></i> Export</a>
@endsection

@section('content')
<div class="adm-card">
    <div class="adm-card-head"><h2>Completed Transactions</h2></div>
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead><tr><th>ID</th><th>Receipt</th><th>Service</th><th>National ID</th><th>Name</th><th>Amount</th><th>Phone</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td>#{{ $t->id }}</td>
                    <td style="font-family:monospace;font-size:.8rem;">{{ $t->mpesa_receipt_number }}</td>
                    <td style="font-size:.75rem;">{{ str_replace('-', ' ', ucfirst($t->service)) }}</td>
                    <td style="font-family:monospace;font-size:.82rem;">{{ $t->national_id }}</td>
                    <td>{{ $t->full_name ?: '—' }}</td>
                    <td>KES {{ number_format($t->payment_amount ?: $t->price, 0) }}</td>
                    <td>{{ $t->payment_phone ?: '—' }}</td>
                    <td style="font-size:.75rem;white-space:nowrap;">{{ $t->created_at->format('M d, H:i') }}</td>
                </tr>
                @endforeach
                @if($transactions->isEmpty())
                <tr><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">No transactions yet.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div style="padding:14px 16px;">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
