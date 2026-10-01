<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;

class AdminAddonController extends Controller
{
    public function index()
    {
        $addons = Addon::all();

        return view('admin.addons.index', compact('addons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:flat,per_person,per_unit',
        ]);

        Addon::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Add-on berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $addon = Addon::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:flat,per_person,per_unit',
        ]);

        $addon->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Add-on berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Addon::findOrFail($id)->delete();

        return back()->with('success', 'Add-on berhasil dihapus.');
    }
}
