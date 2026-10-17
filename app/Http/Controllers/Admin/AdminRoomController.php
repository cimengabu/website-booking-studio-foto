<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlockedSlot;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['images', 'primaryImage'])->orderBy('sort_order')->get();
        $blockedSlots = BlockedSlot::with('room')->latest()->take(20)->get();
        $photographers = User::whereIn('role', ['photographer', 'fotografer'])
            ->withCount(['photographerBookings as active_sessions_count' => function ($q) {
                $q->whereIn('status', ['confirmed', 'paid', 'dp_paid', 'in_progress', 'checked_in']);
            }])
            ->get();

        return view('admin.rooms.index', compact('rooms', 'blockedSlots', 'photographers'));
    }

    public function storeBlockedSlot(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'nullable|exists:rooms,id',
            'reason' => 'required|string|max:255',
        ]);

        $blockedSlot = BlockedSlot::create([
            'room_id' => $request->room_id ?: null,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
        ]);

        // Edge case: Hari libur mendadak / penutupan studio darurat
        // Deteksi booking aktif yang bertabrakan dengan jadwal blokir ini
        $affectedBookings = \App\Models\Booking::with('user')
            ->whereDate('booking_date', $request->date)
            ->when($request->room_id, fn ($q) => $q->where('room_id', $request->room_id))
            ->whereIn('status', ['confirmed', 'paid', 'dp_paid', 'pending', 'checked_in'])
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->get();

        if ($affectedBookings->isNotEmpty()) {
            $codes = $affectedBookings->pluck('booking_code')->implode(', ');
            foreach ($affectedBookings as $b) {
                $emergencyNote = "\n[STUDIO TUTUP/MAINTENANCE DARURAT: Alasan '{$request->reason}' pada {$request->date}]. Pelanggan berhak atas Reschedule Bebas Biaya atau Full Refund.";
                $b->update([
                    'internal_notes' => trim(($b->internal_notes ?? '').$emergencyNote),
                ]);
            }

            \App\Models\ActivityLog::record(
                auth()->id(),
                'admin_blocked_slot_with_conflicts',
                "Menambahkan blokir studio ({$request->reason}) yang berdampak pada {$affectedBookings->count()} booking: {$codes}"
            );

            return back()->with('warning', "Blokir jadwal berhasil dibuat. PERHATIAN: Terdapat {$affectedBookings->count()} booking pelanggan terdampak ({$codes}). Catatan darurat telah disematkan untuk penanganan reschedule/refund bebas biaya.");
        }

        \App\Models\ActivityLog::record(
            auth()->id(),
            'admin_blocked_slot',
            "Menambahkan jadwal blokir studio: {$request->reason} pada {$request->date} ({$request->start_time} - {$request->end_time})"
        );

        return back()->with('success', 'Jadwal blok libur / maintenance berhasil ditambahkan (Aman: tidak ada booking pelanggan yang bertabrakan).');
    }

    public function destroyBlockedSlot(int $id)
    {
        BlockedSlot::findOrFail($id)->delete();

        return back()->with('success', 'Blok jadwal berhasil dihapus.');
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'theme' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'area_m2' => 'required|integer|min:1',
            'image_url' => 'nullable|url',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'theme' => $request->theme,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'area_m2' => $request->area_m2,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $imagePath = $request->image_url ?: 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800&auto=format&fit=crop';
        RoomImage::create([
            'room_id' => $room->id,
            'path' => $imagePath,
            'is_primary' => true,
        ]);

        ActivityLog::record(auth()->id(), 'admin_create_room', "Menambahkan studio/ruangan baru: {$room->name}");

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan / Tema berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $room = Room::with('images')->findOrFail($id);

        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, int $id)
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'theme' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'area_m2' => 'required|integer|min:1',
        ]);

        $room->update([
            'name' => $request->name,
            'theme' => $request->theme,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'area_m2' => $request->area_m2,
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record(auth()->id(), 'admin_update_room', "Memperbarui data ruangan: {$room->name}");

        return redirect()->route('admin.rooms.index')->with('success', 'Data ruangan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $room = Room::findOrFail($id);
        $name = $room->name;
        $room->delete();

        ActivityLog::record(auth()->id(), 'admin_delete_room', "Menghapus data ruangan: {$name}");

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
