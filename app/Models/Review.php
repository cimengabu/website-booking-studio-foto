<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'guest_name',
        'room_id',
        'rating',
        'comment',
        'admin_reply',
        'admin_replied_at',
        'photo',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_visible' => 'boolean',
        'admin_replied_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Pelanggan SnapSpace';
    }

    public function getRoomNameAttribute(): string
    {
        return $this->booking?->room?->name ?? $this->room?->name ?? 'SnapSpace Studio';
    }
}
