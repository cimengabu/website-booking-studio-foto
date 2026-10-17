<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PhotoResult;
use App\Models\Review;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MyBookingController extends Controller
{
    public function index()
    {
        $query = Booking::with(['room.images', 'package', 'payments'])
            ->where('user_id', Auth::id())
            ->latest();

        if (request('status') && request('status') !== 'all') {
            $query->where('status', request('status'));
        }

        $bookings = $query->paginate(10);

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'rating.required' => 'Bintang rating wajib dipilih.',
            'comment.required' => 'Komentar ulasan wajib diisi.',
            'photo.image' => 'File foto testimoni harus berupa gambar.',
            'photo.max' => 'Ukuran foto testimoni maksimal 5MB.',
        ]);

        $reviewData = [
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_visible' => true,
        ];

        if ($request->hasFile('photo')) {
            $reviewData['photo'] = $request->file('photo')->store('reviews', 'public');
        }

        $existingReview = Review::where('booking_id', $booking->id)->first();
        Review::updateOrCreate(
            ['booking_id' => $booking->id],
            $reviewData
        );

        // Reward 50 loyalty points on first-time review
        if (! $existingReview && Auth::user()) {
            Auth::user()->increment('loyalty_points', 50);
        }

        return back()->with('success', 'Terima kasih! Ulasan dan rating Anda telah berhasil dikirim. (+50 Poin Loyalti didapatkan!)');
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

    public function downloadSignedPhoto(Request $request, int $photoId)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Tautan unduhan tidak valid atau telah kadaluarsa.');
        }

        $photo = PhotoResult::with('booking.photoDelivery')->findOrFail($photoId);

        if ($photo->booking->photoDelivery) {
            $delivery = $photo->booking->photoDelivery;
            if ($delivery->expires_at && $delivery->expires_at->isPast()) {
                abort(410, 'Masa aktif unduhan foto telah berakhir.');
            }
            $delivery->increment('download_count');
        }

        if (Storage::disk('public')->exists($photo->path)) {
            return Storage::disk('public')->download($photo->path, $photo->original_name);
        }

        abort(404, 'File foto tidak ditemukan.');
    }

    public function downloadSignedZip(Request $request, string $code)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Tautan unduhan ZIP tidak valid atau telah kadaluarsa.');
        }

        $booking = Booking::with(['photoResults', 'photoDelivery'])->where('booking_code', $code)->firstOrFail();

        if ($booking->photoDelivery) {
            $delivery = $booking->photoDelivery;
            if ($delivery->expires_at && $delivery->expires_at->isPast()) {
                abort(410, 'Masa aktif unduhan foto telah berakhir.');
            }
            $delivery->increment('download_count');
        }

        if ($booking->photoResults->isEmpty()) {
            abort(404, 'Belum ada foto hasil yang diunggah.');
        }

        $zipFileName = "SnapSpace-{$booking->booking_code}-Signed-Photos.zip";
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

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function reschedule(Request $request, string $code, BookingService $bookingService)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'booking_date' => 'required|date|after:today',
            'start_time' => 'required|string',
        ], [
            'booking_date.required' => 'Tanggal jadwal baru wajib dipilih.',
            'booking_date.after' => 'Tanggal reschedule harus minimal H+1 dari hari ini.',
            'start_time.required' => 'Jam sesi foto baru wajib dipilih.',
        ]);

        try {
            $bookingService->rescheduleBooking($booking, $request->booking_date, $request->start_time);

            return back()->with('success', 'Jadwal sesi foto berhasil di-reschedule! Rincian terbaru telah diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['reschedule_error' => $e->getMessage()]);
        }
    }

    public function cancel(Request $request, string $code, BookingService $bookingService)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $bookingService->cancelBooking($booking, $request->reason);

            return back()->with('success', 'Booking berhasil dibatalkan sesuai dengan ketentuan kebijakan refund SnapSpace.');
        } catch (\Exception $e) {
            return back()->withErrors(['cancel_error' => $e->getMessage()]);
        }
    }
}
