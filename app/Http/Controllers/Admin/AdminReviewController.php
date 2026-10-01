<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'booking.room'])->latest()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleVisibility(int $id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_visible' => ! $review->is_visible]);

        return back()->with('success', 'Visibilitas ulasan berhasil diperbarui.');
    }
}
