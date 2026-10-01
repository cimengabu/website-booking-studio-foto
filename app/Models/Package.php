<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_minutes',
        'max_people',
        'edited_photos',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'max_people' => 'integer',
        'edited_photos' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
