<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\Room;

class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::with('primaryImage')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $popularPackages = Package::where('is_active', true)
            ->orderBy('price')
            ->get();

        $portfolioPreview = Portfolio::where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $reviews = Review::with(['user', 'booking.room'])
            ->where('is_visible', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(6)
            ->get();

        return view('customer.home.index', compact('featuredRooms', 'popularPackages', 'portfolioPreview', 'reviews'));
    }
}
