<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $query = VerificationRequest::query();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($service = $request->input('service')) {
            $query->where('service', $service);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $records = $query->latest()->get();

        $filename = 'readiwork-export-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($records) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Service', 'National ID', 'Full Name', 'Status', 'Price',
                'Receipt', 'Payment Phone', 'Payment Date', 'Created At']);
            foreach ($records as $r) {
                fputcsv($out, [
                    $r->id, $r->service, $r->national_id, $r->full_name, $r->status,
                    $r->price, $r->mpesa_receipt_number, $r->payment_phone,
                    $r->payment_date, $r->created_at,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
