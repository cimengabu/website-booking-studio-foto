<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'delivered_at',
        'expires_at',
        'download_count',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'expires_at' => 'datetime',
        'download_count' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
