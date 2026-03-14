<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Admin extends Model
{
    public $timestamps = false;

    protected $fillable = ['email', 'password', 'name', 'role', 'is_active', 'last_login'];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active'  => 'boolean',
        'last_login' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function verifyPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }
}
