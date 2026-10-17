<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\PhotoDelivery;
use App\Models\PhotoResult;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPhotoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'room', 'package', 'photoResults', 'photoDelivery', 'photographer'])
            ->whereIn('status', ['confirmed', 'paid', 'dp_paid', 'checked_in', 'in_progress', 'completed', 'delivered', 'closed']);

        if ($user && $user->isPhotographer()) {
            $query->where(function ($q) use ($user) {
                $q->where('photographer_id', $user->id)
                    ->orWhereNull('photographer_id');
            });
        }

        $bookings = $query->latest()->paginate(15);

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
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:15360', // max 15MB each
            'image_urls' => 'nullable|array',
        ]);

        $hasUrls = ! empty(array_filter($request->input('image_urls', [])));
        if (! $request->hasFile('photos') && ! $hasUrls) {
            return back()->withErrors(['photos' => 'Harap upload minimal 1 file foto atau masukkan 1 URL foto demo.']);
        }

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
