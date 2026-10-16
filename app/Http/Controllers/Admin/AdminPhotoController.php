<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\PhotoDelivery;
use App\Models\PhotoResult;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminPhotoController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'room', 'package', 'photoResults', 'photoDelivery'])
            ->whereIn('status', ['checked_in', 'completed', 'paid'])
            ->latest()
            ->paginate(15);

        return view('admin.photos.index', compact('bookings'));
    }

    public function showUpload(int $bookingId)
    {
        $booking = Booking::with(['user', 'room', 'package', 'photoResults', 'photoDelivery'])->findOrFail($bookingId);

        return view('admin.photos.upload', compact('booking'));
    }

    public function storeUpload(Request $request, int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:15360', // max 15MB each
            'image_urls' => 'nullable|array',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs("photo_results/{$booking->booking_code}", $filename, 'public');

                PhotoResult::create([
                    'booking_id' => $booking->id,
                    'path' => $path,
                    'thumb_path' => $path,
                    'original_name' => $originalName,
                    'size' => $file->getSize(),
                ]);
            }
        }

        // Demo URLs support (if provided via form)
        if ($request->filled('image_urls')) {
            foreach ($request->image_urls as $url) {
                if ($url) {
                    PhotoResult::create([
                        'booking_id' => $booking->id,
                        'path' => $url,
                        'thumb_path' => $url,
                        'original_name' => 'SnapSpace-Edited-'.rand(1000, 9999).'.jpg',
                        'size' => 2450000,
                    ]);
                }
            }
        }

        // Update / Create Photo Delivery Status
        PhotoDelivery::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'delivered_at' => now(),
                'expires_at' => now()->addDays(30), // 30 days active download link
            ]
        );

        $booking->update(['status' => 'delivered']);

        ActivityLog::record(request()->user()?->id, 'admin_upload_photos', "Mengunggah hasil foto untuk booking {$booking->booking_code}");

        app(NotificationService::class)->sendPhotosReadyNotification($booking);

        return redirect()->route('admin.photos.upload', $booking->id)
            ->with('success', 'Hasil foto berhasil diunggah! Notifikasi WhatsApp foto siap unduh telah dikirimkan ke pelanggan.');
    }

    public function destroyPhoto(int $photoId)
    {
        $photo = PhotoResult::findOrFail($photoId);
        $bookingId = $photo->booking_id;
        $photo->delete();

        return redirect()->route('admin.photos.upload', $bookingId)
            ->with('success', 'Foto berhasil dihapus.');
    }
}
