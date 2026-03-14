@extends('layouts.admin')
@section('title', 'Admin Users')
@section('page-title', 'Admin Users')

@section('content')
<div class="adm-grid-2" style="gap:20px;">
    <div class="adm-card" style="margin-bottom:0;">
        <div class="adm-card-head"><h2>All Admins</h2></div>
        <div style="overflow-x:auto;">
            <table class="adm-table">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Last Login</th><th></th></tr></thead>
                <tbody>
                    @foreach($admins as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td style="font-size:.8rem;">{{ $a->email }}</td>
                        <td><span class="badge {{ $a->role === 'super_admin' ? 'badge-green' : 'badge-blue' }}">{{ $a->role }}</span></td>
                        <td style="font-size:.75rem;">{{ $a->last_login ? $a->last_login->format('M d, H:i') : 'Never' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.admins.destroy', $a->id) }}" onsubmit="return confirm('Delete this admin?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-adm btn-adm-red" style="font-size:.72rem;padding:4px 8px;"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="adm-card" style="margin-bottom:0;">
        <div class="adm-card-head"><h2>Add Admin</h2></div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('admin.admins.store') }}">
                @csrf
                <div class="adm-form-group"><label>Name</label><input type="text" name="name" required></div>
                <div class="adm-form-group"><label>Email</label><input type="email" name="email" required></div>
                <div class="adm-form-group"><label>Password</label><input type="password" name="password" required minlength="8"></div>
                <div class="adm-form-group">
                    <label>Role</label>
                    <select name="role"><option value="admin">Admin</option><option value="super_admin">Super Admin</option></select>
                </div>
                <button type="submit" class="btn-adm btn-adm-primary"><i class="fa-solid fa-user-plus"></i> Add Admin</button>
            </form>
        </div>
    </div>
</div>
@endsection
