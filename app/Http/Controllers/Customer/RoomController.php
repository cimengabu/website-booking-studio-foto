<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['images', 'primaryImage'])->where('is_active', true)->orderBy('sort_order')->get();

        return view('customer.rooms.index', compact('rooms'));
    }

    public function show(string $slug)
    {
        $room = Room::with(['images', 'portfolios'])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('customer.rooms.show', compact('room'));
    }
}
