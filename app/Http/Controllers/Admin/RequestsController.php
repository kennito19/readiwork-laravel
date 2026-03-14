<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;

class RequestsController extends Controller
{
    public function index(Request $request)
    {
        $query = VerificationRequest::query();

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('national_id', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%")
                  ->orWhere('mpesa_receipt_number', 'like', "%$search%");
            });
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($service = $request->input('service')) {
            $query->where('service', $service);
        }

        $requests = $query->latest()->paginate(25)->withQueryString();

        return view('admin.requests', compact('requests'));
    }

    public function show(int $id)
    {
        $req = VerificationRequest::findOrFail($id);
        $crb = $req->result ? json_decode($req->result, true) : null;
        return view('admin.request-detail', compact('req', 'crb'));
    }

    public function transactions(Request $request)
    {
        $transactions = VerificationRequest::whereIn('status', ['completed', 'paid', 'refunded'])
            ->whereNotNull('mpesa_receipt_number')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.transactions', compact('transactions'));
    }
}
