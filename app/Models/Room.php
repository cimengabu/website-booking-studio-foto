<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'theme',
        'description',
        'capacity',
        'area_m2',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'area_m2' => 'integer',
    ];

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(RoomImage::class)->where('is_primary', true);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }
}
