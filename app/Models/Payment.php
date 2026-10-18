<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',
        'payment_kind',
        'payment_type',
        'gross_amount',
        'transaction_status',
        'snap_token',
        'paid_at',
        'raw_response',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'raw_response' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getAmountAttribute(): float
    {
        return (float) $this->gross_amount;
    }

    public function getStatusAttribute(): string
    {
        return match ($this->transaction_status) {
            'settlement', 'capture' => 'success',
            'pending' => 'pending',
            'expire', 'cancel', 'deny' => 'failed',
            'refund' => 'refunded',
            default => $this->transaction_status ?? 'pending',
        };
    }

    public function getPaymentMethodAttribute(): ?string
    {
        return $this->payment_type;
    }

    public function getTransactionIdAttribute(): ?string
    {
        return $this->order_id;
    }
}
