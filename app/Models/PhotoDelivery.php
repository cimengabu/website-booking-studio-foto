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
        'watermark_enabled',
        'watermark_text',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'expires_at' => 'datetime',
        'download_count' => 'integer',
        'watermark_enabled' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
