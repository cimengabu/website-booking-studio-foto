<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminPortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::with('room')->latest()->paginate(15);
        $rooms = Room::all();
        $categories = ['wisuda', 'prewedding', 'produk', 'keluarga', 'self-photo'];

        return view('admin.portfolios.index', compact('portfolios', 'rooms', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'image_url' => 'required|string',
        ]);

        Portfolio::create([
            'title' => $request->title,
            'category' => $request->category,
            'image_path' => $request->image_url,
            'room_id' => $request->room_id ?: null,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return back()->with('success', 'Portofolio baru berhasil ditambahkan.');
    }

    public function destroy(int $id)
    {
        Portfolio::findOrFail($id)->delete();

        return back()->with('success', 'Portofolio berhasil dihapus.');
    }
}
