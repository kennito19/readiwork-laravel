@extends('layouts.admin')
@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('content')
<div class="adm-card">
    <div class="adm-card-head"><h2>Revenue (Last 30 Days)</h2></div>
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead><tr><th>Date</th><th>Revenue (KES)</th><th>Checks</th></tr></thead>
            <tbody>
                @foreach($dailyRevenue as $d)
                <tr>
                    <td>{{ $d->date }}</td>
                    <td>KES {{ number_format($d->revenue, 0) }}</td>
                    <td>{{ number_format($d->count) }}</td>
                </tr>
                @endforeach
                @if($dailyRevenue->isEmpty())
                <tr><td colspan="3" style="text-align:center;padding:24px;color:#94a3b8;">No data for the last 30 days.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-head"><h2>Service Performance</h2></div>
    <div style="overflow-x:auto;">
        <table class="adm-table">
            <thead><tr><th>Service</th><th>Total</th><th>Completed</th><th>Conversion</th><th>Revenue</th></tr></thead>
            <tbody>
                @foreach($serviceStats as $s)
                @php $conv = $s->total > 0 ? round($s->completed / $s->total * 100) : 0; @endphp
                <tr>
                    <td>{{ str_replace('-', ' ', ucfirst($s->service)) }}</td>
                    <td>{{ number_format($s->total) }}</td>
                    <td>{{ number_format($s->completed) }}</td>
                    <td>{{ $conv }}%</td>
                    <td>KES {{ number_format($s->revenue, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
