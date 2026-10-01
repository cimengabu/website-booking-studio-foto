<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'path',
        'thumb_path',
        'original_name',
        'size',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
