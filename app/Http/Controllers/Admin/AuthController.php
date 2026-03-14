<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminLoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 300; // 5 minutes lockout

    public function showLogin(Request $request)
    {
        if (session('readiwork_admin')) {
            return redirect()->route('admin.dashboard');
        }
        $timeout = $request->query('timeout') == 1;
        return view('admin.login', compact('timeout'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:150',
            'password' => 'required|string|max:255',
        ]);

        $email    = strtolower(trim($request->input('email')));
        $password = $request->input('password');
        $key      = 'admin_login:' . $request->ip();

        // Rate limiting
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Too many attempts. Try again in {$seconds} seconds."])->withInput(['email' => $email]);
        }

        $admin     = Admin::where('email', $email)->where('is_active', true)->first();
        $logStatus = 'failed';

        if ($admin && $admin->verifyPassword($password)) {
            RateLimiter::clear($key);
            $logStatus = 'success';

            $request->session()->regenerate();
            session([
                'readiwork_admin' => [
                    'id'    => $admin->id,
                    'email' => $admin->email,
                    'name'  => $admin->name,
                    'role'  => $admin->role,
                ],
                'last_activity' => time(),
            ]);
            $admin->update(['last_login' => now()]);
        } else {
            RateLimiter::hit($key, self::DECAY_SECONDS);
        }

        AdminLoginHistory::create([
            'admin_email' => $email,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'status'      => $logStatus,
            'created_at'  => now(),
        ]);

        if ($logStatus === 'success') {
            return redirect()->intended(route('admin.dashboard'));
        }

        $remaining = RateLimiter::remaining($key, self::MAX_ATTEMPTS);
        $msg = $remaining > 0
            ? "Invalid credentials. {$remaining} attempt(s) remaining."
            : 'Invalid credentials.';

        return back()->withErrors(['email' => $msg])->withInput(['email' => $email]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
