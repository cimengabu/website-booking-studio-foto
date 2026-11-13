<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\Request;

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

        $reviews = Review::with(['user', 'booking.room', 'room'])
            ->where('is_visible', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(9)
            ->get();

        $allRooms = Room::where('is_active', true)->orderBy('sort_order')->get();

        return view('customer.home.index', compact('featuredRooms', 'popularPackages', 'portfolioPreview', 'reviews', 'allRooms'));
    }

    /**
     * Store customer review & rating from the homepage.
     */
    public function storeReview(Request $request)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
            'name' => ['nullable', 'string', 'max:255'],
            'room_id' => ['nullable', 'exists:rooms,id'],
        ], [
            'rating.required' => 'Silakan pilih rating bintang 1 sampai 5.',
            'comment.required' => 'Ulasan / testimoni wajib diisi.',
            'comment.min' => 'Testimoni minimal 5 karakter.',
        ]);

        $authorName = auth()->check()
            ? auth()->user()->name
            : ($request->filled('name') ? trim($request->input('name')) : 'Pelanggan SnapSpace');

        Review::create([
            'user_id' => auth()->id(),
            'guest_name' => $authorName,
            'room_id' => $request->input('room_id'),
            'rating' => (int) $request->input('rating'),
            'comment' => trim($request->input('comment')),
            'is_visible' => true,
        ]);

        return back()->with('review_success', 'Terima kasih atas ulasan dan rating Anda! Testimoni berhasil diterbitkan.');
    }
}
