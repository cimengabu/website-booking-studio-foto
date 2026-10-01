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
}
