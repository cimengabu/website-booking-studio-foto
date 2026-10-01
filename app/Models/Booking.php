<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'room_id',
        'package_id',
        'booking_date',
        'start_time',
        'end_time',
        'people_count',
        'subtotal',
        'addons_total',
        'markup_amount',
        'discount',
        'total_price',
        'paid_amount',
        'payment_option',
        'status',
        'expires_at',
        'qr_token',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function bookingAddons()
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function photoResults()
    {
        return $this->hasMany(PhotoResult::class);
    }

    public function photoDelivery()
    {
        return $this->hasOne(PhotoDelivery::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->total_price - (float) $this->paid_amount);
    }
}
