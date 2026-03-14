<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $grouped = Setting::grouped();
        return view('admin.settings', compact('grouped'));
    }

    public function update(Request $request)
    {
        $allowed = Setting::pluck('key')->flip()->toArray();
        $data    = array_intersect_key($request->except(['_token', '_method']), $allowed);

        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => substr((string) $value, 0, 500)]);
            Cache::forget('setting_' . $key);
        }
        Cache::forget('all_settings');
        AdminActivityLog::record('Updated settings', implode(', ', array_keys($data)));
        return back()->with('success', 'Settings saved.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $file     = $request->file('logo');
        $filename = 'logo_custom.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $filename);

        $path = 'uploads/' . $filename;
        Setting::where('key', 'logo_path')->update(['value' => $path]);
        Cache::forget('setting_logo_path');
        Cache::forget('all_settings');

        AdminActivityLog::record('Uploaded logo', $path);
        return back()->with('success', 'Logo updated.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|max:255|confirmed',
        ]);

        $adminId = session('readiwork_admin.id');
        $admin   = Admin::findOrFail($adminId);

        if (!$admin->verifyPassword($request->input('current_password'))) {
            return back()->with('pw_error', 'Current password is incorrect.')->withFragment('tab-security');
        }

        $admin->update(['password' => Hash::make($request->input('new_password'))]);
        AdminActivityLog::record('Changed own password', $admin->email);
        return back()->with('pw_success', 'Password changed successfully.')->withFragment('tab-security');
    }
}
