<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
