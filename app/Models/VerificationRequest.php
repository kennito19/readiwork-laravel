<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationRequest extends Model
{
    protected $fillable = [
        'service', 'national_id', 'phone', 'price', 'quoted_amount', 'status',
        'result', 'checkout_request_id', 'merchant_request_id',
        'mpesa_receipt_number', 'payment_phone', 'payment_amount',
        'payment_date', 'payment_error', 'full_name', 'dob', 'gender',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'quoted_amount'  => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'payment_date'   => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public function getResultDataAttribute(): ?array
    {
        return $this->result ? json_decode($this->result, true) : null;
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending_payment');
    }
}
