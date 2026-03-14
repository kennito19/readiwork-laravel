<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLoginHistory extends Model
{
    protected $table = 'admin_login_history';
    public $timestamps = false;
    protected $fillable = ['admin_email', 'ip_address', 'user_agent', 'status', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];
}
