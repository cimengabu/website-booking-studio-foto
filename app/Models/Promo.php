<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order',
        'max_discount',
        'quota',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'quota' => 'integer',
        'used_count' => 'integer',
    ];

    public function isValidForAmount(float $amount): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->quota <= $this->used_count) {
            return false;
        }
        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }
        if ($amount < $this->min_order) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (! $this->isValidForAmount($amount)) {
            return 0.0;
        }

        if ($this->type === 'percent') {
            $discount = ($amount * $this->value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }

            return $discount;
        }

        return (float) min($this->value, $amount);
    }
}
