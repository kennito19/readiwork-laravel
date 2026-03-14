<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminsController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();
        return view('admin.admins', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:150|unique:admins,email',
            'name'     => 'required|string|max:100',
            'password' => 'required|string|min:8|max:255',
            'role'     => 'in:super_admin,admin',
        ]);

        Admin::create([
            'email'     => strtolower(trim($request->input('email'))),
            'name'      => $request->input('name'),
            'password'  => Hash::make($request->input('password')),
            'role'      => $request->input('role', 'admin'),
            'is_active' => true,
        ]);

        AdminActivityLog::record('Created admin', $request->input('email'));
        return back()->with('success', 'Admin added.');
    }

    public function destroy(int $id)
    {
        $current = session('readiwork_admin');
        if ((int)($current['id'] ?? 0) === $id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin = Admin::findOrFail($id);
        $email = $admin->email;
        $admin->delete();
        AdminActivityLog::record('Deleted admin', $email);
        return back()->with('success', 'Admin removed.');
    }
}
