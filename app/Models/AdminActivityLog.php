<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['admin_id', 'admin_email', 'action', 'details', 'ip_address', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public static function record(string $action, string $details = ''): void
    {
        try {
            $session = session('readiwork_admin', []);
            static::create([
                'admin_id'    => $session['id']    ?? null,
                'admin_email' => $session['email'] ?? null,
                'action'      => $action,
                'details'     => $details ?: null,
                'ip_address'  => request()->ip(),
                'created_at'  => now(),
            ]);
        } catch (\Exception $e) { /* non-fatal */ }
    }
}
