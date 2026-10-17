<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'loyalty_points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isOwner(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'kasir']);
    }

    public function isPhotographer(): bool
    {
        return in_array($this->role, ['photographer', 'fotografer']);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this->role, ['owner', 'admin', 'staff', 'kasir', 'photographer', 'fotografer']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'owner', 'admin' => 'Owner / Admin',
            'staff', 'kasir' => 'Staff / Kasir',
            'photographer', 'fotografer' => 'Fotografer',
            default => 'Pelanggan',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'owner', 'admin' => 'bg-[#E8451E]/20 text-[#E8451E] border border-[#E8451E]/30',
            'staff', 'kasir' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
            'photographer', 'fotografer' => 'bg-[#F2C94C]/20 text-[#F2C94C] border border-[#F2C94C]/30',
            default => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
        };
    }

    public function photographerBookings()
    {
        return $this->hasMany(Booking::class, 'photographer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
