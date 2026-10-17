<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // Linear Lifecycle
    public const STATUS_PENDING = 'pending';          // Menunggu Pembayaran

    public const STATUS_CONFIRMED = 'confirmed';      // Dikonfirmasi (DP / Lunas)

    public const STATUS_IN_PROGRESS = 'in_progress';  // Sedang Berlangsung (Checked-in)

    public const STATUS_COMPLETED = 'completed';      // Selesai Sesi

    public const STATUS_DELIVERED = 'delivered';      // Foto Siap Diunduh (Delivery)

    public const STATUS_CLOSED = 'closed';            // Ditutup

    // Branches / Cabang
    public const STATUS_CANCELLED = 'cancelled';      // Dibatalkan

    public const STATUS_EXPIRED = 'expired';          // Kedaluwarsa

    public const STATUS_RESCHEDULED = 'rescheduled';  // Dijadwalkan Ulang

    public const STATUS_NO_SHOW = 'no_show';          // No-Show (DP Hangus)

    // Legacy statuses support
    public const STATUS_DP_PAID = 'dp_paid';

    public const STATUS_PAID = 'paid';

    public const STATUS_CHECKED_IN = 'checked_in';

    protected $fillable = [
        'booking_code',
        'user_id',
        'photographer_id',
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
        'reschedule_count',
        'original_booking_date',
        'original_start_time',
        'cancellation_reason',
        'refund_amount',
        'penalty_amount',
        'expires_at',
        'qr_token',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'original_booking_date' => 'date',
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'reschedule_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photographer()
    {
        return $this->belongsTo(User::class, 'photographer_id');
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_CONFIRMED, self::STATUS_PAID => 'Dikonfirmasi (Lunas)',
            self::STATUS_DP_PAID => 'Dikonfirmasi (DP 50%)',
            self::STATUS_IN_PROGRESS, self::STATUS_CHECKED_IN => 'Sedang Berlangsung',
            self::STATUS_COMPLETED => 'Selesai Sesi',
            self::STATUS_DELIVERED => 'Foto Siap Diunduh',
            self::STATUS_CLOSED => 'Ditutup',
            self::STATUS_RESCHEDULED => 'Reschedule',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_EXPIRED => 'Kedaluwarsa',
            self::STATUS_NO_SHOW => 'No-Show (Hangus)',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED, self::STATUS_PAID => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_DP_PAID => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_PENDING => 'bg-orange-50 text-orange-700 border-orange-200',
            self::STATUS_IN_PROGRESS, self::STATUS_CHECKED_IN => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STATUS_COMPLETED => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::STATUS_DELIVERED => 'bg-purple-50 text-purple-700 border-purple-200',
            self::STATUS_CLOSED => 'bg-gray-100 text-gray-700 border-gray-300',
            self::STATUS_RESCHEDULED => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            self::STATUS_CANCELLED, self::STATUS_EXPIRED, self::STATUS_NO_SHOW => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    /**
     * Check if booking can be rescheduled according to business rules:
     * - Reschedule max 1x (or setting 'reschedule_max_times')
     * - Minimum H-2 days before session (or setting 'reschedule_min_days')
     */
    public function isReschedulable(): bool
    {
        if (! in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_DP_PAID, self::STATUS_PAID])) {
            return false;
        }

        $maxTimes = (int) Setting::getByKey('reschedule_max_times', 1);
        if ($this->reschedule_count >= $maxTimes) {
            return false;
        }

        $minDays = (int) Setting::getByKey('reschedule_min_days', 2);
        $daysDiff = now()->startOfDay()->diffInDays(Carbon::parse($this->booking_date)->startOfDay(), false);

        return $daysDiff >= $minDays;
    }

    /**
     * Calculate cancellation refund and penalty based on flexible settings:
     * - H-3 or more: refund 100% (cancellation_h3_refund_percent)
     * - H-2 s/d H-1: refund 50% (cancellation_h1_refund_percent)
     * - Hari H: refund 0% (cancellation_h0_refund_percent)
     */
    public function calculateCancellationRefund(): array
    {
        $paid = (float) $this->paid_amount;
        $daysDiff = now()->startOfDay()->diffInDays(Carbon::parse($this->booking_date)->startOfDay(), false);

        $h3Percent = (float) Setting::getByKey('cancellation_h3_refund_percent', 100);
        $h1Percent = (float) Setting::getByKey('cancellation_h1_refund_percent', 50);
        $h0Percent = (float) Setting::getByKey('cancellation_h0_refund_percent', 0);

        if ($daysDiff >= 3) {
            $percent = $h3Percent;
            $tier = 'H-3 atau lebih (Refund '.$percent.'%)';
        } elseif ($daysDiff >= 1) {
            $percent = $h1Percent;
            $tier = 'H-2 s/d H-1 (Refund '.$percent.'%)';
        } else {
            $percent = $h0Percent;
            $tier = 'Hari H (Refund '.$percent.'%)';
        }

        $refundAmount = ($paid * $percent) / 100;
        $penaltyAmount = max(0, $paid - $refundAmount);

        return [
            'days_diff' => $daysDiff,
            'percent' => $percent,
            'tier' => $tier,
            'paid_amount' => $paid,
            'refund_amount' => $refundAmount,
            'penalty_amount' => $penaltyAmount,
        ];
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_DP_PAID,
            self::STATUS_PAID,
        ]);
    }
}
