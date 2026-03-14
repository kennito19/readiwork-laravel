<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->string('service', 80)->default('loan-eligibility');
            $table->string('national_id', 20);
            $table->string('phone', 20)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->enum('status', ['pending_payment', 'paid', 'completed', 'payment_failed', 'refunded'])->default('pending_payment');
            $table->longText('result')->nullable()->comment('JSON from Metropol APIs');
            $table->string('checkout_request_id', 100)->nullable();
            $table->string('merchant_request_id', 100)->nullable();
            $table->string('mpesa_receipt_number', 30)->nullable();
            $table->string('payment_phone', 20)->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_error', 255)->nullable();
            $table->string('full_name', 150)->nullable();
            $table->string('dob', 20)->nullable();
            $table->string('gender', 10)->nullable();
            $table->timestamps();

            $table->index('national_id');
            $table->index('checkout_request_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};
