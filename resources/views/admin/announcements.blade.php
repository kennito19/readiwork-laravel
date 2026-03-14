@extends('layouts.admin')
@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
<div class="adm-grid-2" style="gap:20px;">
    <div class="adm-card" style="margin-bottom:0;">
        <div class="adm-card-head"><h2>All Announcements</h2></div>
        <div style="overflow-x:auto;">
            <table class="adm-table">
                <thead><tr><th>Title</th><th>Type</th><th>Active</th><th>Created</th><th></th></tr></thead>
                <tbody>
                    @foreach($announcements as $a)
                    <tr>
                        <td><strong style="font-size:.85rem;">{{ $a->title }}</strong><br><span style="font-size:.75rem;color:#64748b;">{{ Str::limit($a->message, 60) }}</span></td>
                        <td><span class="badge badge-{{ $a->type === 'danger' ? 'red' : ($a->type === 'warning' ? 'yellow' : ($a->type === 'success' ? 'green' : 'blue')) }}">{{ $a->type }}</span></td>
                        <td><span class="badge {{ $a->is_active ? 'badge-green' : 'badge-gray' }}">{{ $a->is_active ? 'Active' : 'Off' }}</span></td>
                        <td style="font-size:.75rem;">{{ $a->created_at->format('M d') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $a->id) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-adm btn-adm-red" style="font-size:.72rem;padding:4px 8px;"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($announcements->isEmpty())
                    <tr><td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">No announcements.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="adm-card" style="margin-bottom:0;">
        <div class="adm-card-head"><h2>New Announcement</h2></div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('admin.announcements.store') }}">
                @csrf
                <div class="adm-form-group"><label>Title</label><input type="text" name="title" required maxlength="200"></div>
                <div class="adm-form-group"><label>Message</label><textarea name="message" required rows="4"></textarea></div>
                <div class="adm-form-group">
                    <label>Type</label>
                    <select name="type"><option value="info">Info</option><option value="success">Success</option><option value="warning">Warning</option><option value="danger">Danger</option></select>
                </div>
                <div class="adm-form-group"><label>Expires At (optional)</label><input type="datetime-local" name="expires_at"></div>
                <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-bullhorn"></i> Publish</button>
            </form>
        </div>
    </div>
</div>
@endsection
