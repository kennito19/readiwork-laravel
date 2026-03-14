<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'     => VerificationRequest::count(),
            'completed' => VerificationRequest::where('status', 'completed')->count(),
            'pending'   => VerificationRequest::whereIn('status', ['pending_payment', 'paid'])->count(),
            'failed'    => VerificationRequest::where('status', 'payment_failed')->count(),
            'revenue'   => VerificationRequest::where('status', 'completed')->sum('price'),
        ];

        $recent = VerificationRequest::latest()->limit(10)->get();

        $serviceBreakdown = VerificationRequest::select('service', DB::raw('count(*) as total'))
            ->where('status', 'completed')
            ->groupBy('service')
            ->get();

        $announcements = Announcement::where('is_active', true)
            ->where(function($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->latest('created_at')->get();

        return view('admin.dashboard', compact('stats', 'recent', 'serviceBreakdown', 'announcements'));
    }

    public function analytics()
    {
        $dailyRevenue = VerificationRequest::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(price) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $serviceStats = VerificationRequest::select('service',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status="completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(price) as revenue')
            )
            ->groupBy('service')
            ->get();

        return view('admin.analytics', compact('dailyRevenue', 'serviceStats'));
    }
}
