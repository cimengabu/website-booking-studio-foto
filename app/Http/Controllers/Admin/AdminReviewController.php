<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

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

    public function reply(Request $request, int $id)
    {
        $review = Review::findOrFail($id);
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'admin_reply' => $request->admin_reply,
            'admin_replied_at' => now(),
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan.');
    }
}
