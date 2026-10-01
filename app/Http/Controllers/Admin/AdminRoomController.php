<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['images', 'primaryImage'])->orderBy('sort_order')->get();

        return view('admin.rooms.index', compact('rooms'));
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

        return redirect()->route('admin.rooms.index')->with('success', 'Data ruangan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
