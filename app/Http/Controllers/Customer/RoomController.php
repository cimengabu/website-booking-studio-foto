<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['images', 'primaryImage'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('customer.rooms.index', compact('rooms'));
    }

    public function show(string $slug)
    {
        $room = Room::with(['images', 'portfolios', 'primaryImage'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Grab reviews for bookings made in this room
        $reviews = Review::with('user')
            ->whereHas('booking', fn ($q) => $q->where('room_id', $room->id))
            ->where('is_visible', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(5)
            ->get();

        // Other rooms for the "Related Rooms" strip
        $otherRooms = Room::with('primaryImage')
            ->where('is_active', true)
            ->where('id', '!=', $room->id)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('customer.rooms.show', compact('room', 'reviews', 'otherRooms'));
    }
}
