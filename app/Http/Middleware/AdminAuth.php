<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    private const SESSION_TIMEOUT = 1800; // 30 minutes

    public function handle(Request $request, Closure $next)
    {
        $sessionAdmin = session('readiwork_admin');

        if (!$sessionAdmin || empty($sessionAdmin['id']) || empty($sessionAdmin['email'])) {
            return redirect()->route('admin.login');
        }

        // Session timeout
        $lastActivity = session('last_activity', time());
        if ((time() - $lastActivity) > self::SESSION_TIMEOUT) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login', ['timeout' => 1]);
        }

        // Verify admin still exists and is active in the database
        $admin = Admin::where('id', $sessionAdmin['id'])
                      ->where('email', $sessionAdmin['email'])
                      ->where('is_active', true)
                      ->first();

        if (!$admin) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login');
        }

        session(['last_activity' => time()]);
        return $next($request);
    }
}
