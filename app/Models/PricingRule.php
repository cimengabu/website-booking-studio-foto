<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'day_type',
        'start_time',
        'end_time',
        'markup_percent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'markup_percent' => 'float',
    ];
}
