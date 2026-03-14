<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class AnnouncementsController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest('created_at')->get();
        return view('admin.announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:200',
            'message' => 'required|string',
            'type'    => 'in:info,warning,success,danger',
        ]);

        $admin = session('readiwork_admin', []);
        Announcement::create([
            'title'      => $request->input('title'),
            'message'    => $request->input('message'),
            'type'       => $request->input('type', 'info'),
            'is_active'  => true,
            'created_by' => $admin['id'] ?? null,
            'created_at' => now(),
            'expires_at' => $request->input('expires_at') ?: null,
        ]);

        AdminActivityLog::record('Created announcement', $request->input('title'));
        return back()->with('success', 'Announcement created.');
    }

    public function destroy(int $id)
    {
        Announcement::findOrFail($id)->delete();
        AdminActivityLog::record('Deleted announcement', 'ID: ' . $id);
        return back()->with('success', 'Announcement deleted.');
    }
}
