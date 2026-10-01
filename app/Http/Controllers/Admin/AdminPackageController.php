<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();

        return view('admin.packages.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'max_people' => 'required|integer|min:1',
            'edited_photos' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Package::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'max_people' => $request->max_people,
            'edited_photos' => $request->edited_photos,
            'price' => $request->price,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Paket foto berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $package = Package::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
        ]);

        $package->update([
            'name' => $request->name,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'max_people' => $request->max_people,
            'edited_photos' => $request->edited_photos,
            'price' => $request->price,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Package::findOrFail($id)->delete();

        return back()->with('success', 'Paket berhasil dihapus.');
    }
}
