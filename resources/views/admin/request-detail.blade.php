@extends('layouts.admin')
@section('title', 'Request #' . $req->id)
@section('page-title', 'Request #' . $req->id)

@section('topbar-actions')
<a href="{{ route('admin.requests') }}" class="btn-adm btn-adm-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
@endsection

@section('content')
<div class="adm-grid-2" style="margin-bottom:20px;">
    <div class="adm-card" style="margin-bottom:0;">
        <div class="adm-card-head"><h2>Request Details</h2></div>
        <div style="padding:16px 20px;">
            @foreach([
                'ID' => '#' . $req->id,
                'Service' => str_replace('-', ' ', ucfirst($req->service)),
                'National ID' => $req->national_id,
                'Full Name' => $req->full_name ?: '—',
                'Status' => $req->status,
                'Price' => 'KES ' . number_format($req->price, 0),
                'Created' => $req->created_at->format('M d Y, H:i:s'),
            ] as $label => $value)
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:.85rem;">
                <span style="color:#64748b;font-weight:500;">{{ $label }}</span>
                <span style="font-weight:600;color:#1e293b;">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="adm-card" style="margin-bottom:0;">
        <div class="adm-card-head"><h2>Payment Details</h2></div>
        <div style="padding:16px 20px;">
            @foreach([
                'Receipt' => $req->mpesa_receipt_number ?: '—',
                'Payment Phone' => $req->payment_phone ?: '—',
                'Payment Amount' => $req->payment_amount ? 'KES ' . number_format($req->payment_amount, 0) : '—',
                'Payment Date' => $req->payment_date ? $req->payment_date->format('M d Y, H:i:s') : '—',
                'Checkout ID' => $req->checkout_request_id ?: '—',
                'Error' => $req->payment_error ?: '—',
            ] as $label => $value)
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:.85rem;">
                <span style="color:#64748b;font-weight:500;">{{ $label }}</span>
                <span style="font-weight:600;color:#1e293b;word-break:break-all;text-align:right;max-width:60%;">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@if($crb)
<div class="adm-card">
    <div class="adm-card-head"><h2>CRB Result Data (Raw JSON)</h2></div>
    <div style="padding:16px 20px;overflow:auto;max-height:500px;">
        <pre style="font-size:.75rem;color:#1e293b;background:#f8fafc;padding:16px;border-radius:8px;white-space:pre-wrap;word-break:break-all;">{{ json_encode($crb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</div>
@endif
@endsection
