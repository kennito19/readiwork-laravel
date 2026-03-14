<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\AdminLoginHistory;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $query = AdminActivityLog::latest('created_at');
        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('action', 'like', "%$q%")
                   ->orWhere('admin_email', 'like', "%$q%")
                   ->orWhere('details', 'like', "%$q%");
            });
        }
        $total = $query->count();
        $logs  = $query->paginate(40);
        return view('admin.logs', compact('logs', 'q', 'total'));
    }

    public function loginHistory(Request $request)
    {
        $q            = trim($request->get('q', ''));
        $filterStatus = $request->get('status', '');
        $query        = AdminLoginHistory::latest('created_at');
        if ($filterStatus) $query->where('status', $filterStatus);
        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('admin_email', 'like', "%$q%")
                   ->orWhere('ip_address', 'like', "%$q%");
            });
        }
        $total        = $query->count();
        $history      = $query->paginate(40);
        $totalSuccess = AdminLoginHistory::where('status', 'success')->count();
        $totalFailed  = AdminLoginHistory::where('status', 'failed')->count();
        $todayLogins  = AdminLoginHistory::where('status', 'success')
                          ->whereDate('created_at', today())->count();
        return view('admin.login-history', compact(
            'history', 'q', 'filterStatus', 'total', 'totalSuccess', 'totalFailed', 'todayLogins'
        ));
    }
}
