<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PhotoResult;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MyBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['room', 'package', 'payments'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.my-bookings.index', compact('bookings'));
    }

    public function show(string $code)
    {
        $booking = Booking::with(['room.images', 'package', 'bookingAddons.addon', 'payments', 'photoResults', 'photoDelivery', 'review'])
            ->where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('customer.my-bookings.show', compact('booking'));
    }

    public function storeReview(Request $request, string $code)
    {
        $booking = Booking::where('booking_code', $code)->where('user_id', Auth::id())->firstOrFail();

        if (! in_array($booking->status, ['completed', 'checked_in', 'paid'])) {
            return back()->withErrors(['review' => 'Ulasan hanya dapat diberikan setelah sesi foto selesai.']);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ], [
            'rating.required' => 'Bintang rating wajib dipilih.',
            'comment.required' => 'Komentar ulasan wajib diisi.',
        ]);

        Review::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_visible' => true,
            ]
        );

        return back()->with('success', 'Terima kasih! Ulasan dan rating Anda telah berhasil dikirim.');
    }

    public function downloadPhoto(int $photoId)
    {
        $photo = PhotoResult::with('booking')->findOrFail($photoId);

        if ($photo->booking->user_id !== Auth::id() && ! Auth::user()?->isAdmin()) {
            abort(403);
        }

        if ($photo->booking->photoDelivery) {
            $photo->booking->photoDelivery->increment('download_count');
        }

        if (Storage::disk('public')->exists($photo->path)) {
            return Storage::disk('public')->download($photo->path, $photo->original_name);
        }

        return back()->withErrors(['download' => 'File foto tidak ditemukan di server.']);
    }

    public function downloadZip(string $code)
    {
        $booking = Booking::with('photoResults')->where('booking_code', $code)->firstOrFail();

        if ($booking->user_id !== Auth::id() && ! Auth::user()?->isAdmin()) {
            abort(403);
        }

        if ($booking->photoResults->isEmpty()) {
            return back()->withErrors(['download' => 'Belum ada foto hasil yang diunggah untuk booking ini.']);
        }

        $zipFileName = "SnapSpace-{$booking->booking_code}-Photos.zip";
        $zipPath = storage_path("app/public/zips/{$zipFileName}");

        if (! file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($booking->photoResults as $photo) {
                $filePath = storage_path("app/public/{$photo->path}");
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $photo->original_name);
                }
            }
            $zip->close();
        }

        if ($booking->photoDelivery) {
            $booking->photoDelivery->increment('download_count');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
