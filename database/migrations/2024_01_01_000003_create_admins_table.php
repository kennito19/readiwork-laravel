<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150)->unique();
            $table->string('password_hash', 255);
            $table->string('name', 100)->default('Admin');
            $table->enum('role', ['super_admin', 'admin'])->default('admin');
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_login')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Default admin: email=admin@readi.work  password=admin123  (change immediately)
        DB::table('admins')->insertOrIgnore([
            [
                'email'         => 'admin@readi.work',
                'password_hash' => '$2y$12$T.hX5z9U1u0bF6z4HmfKd.IXy0VKtbJfFQRSq1wVh.JbUFhsBB6dy',
                'name'          => 'Admin',
                'role'          => 'super_admin',
                'is_active'     => 1,
                'created_at'    => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
