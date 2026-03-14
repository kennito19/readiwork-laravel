<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'message', 'type', 'is_active', 'created_by', 'created_at', 'expires_at'];
    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
