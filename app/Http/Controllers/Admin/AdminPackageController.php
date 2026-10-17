<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Package;
use App\Models\PricingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->get();
        $pricingRules = PricingRule::all();

        return view('admin.packages.index', compact('packages', 'pricingRules'));
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

        $package = Package::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'max_people' => $request->max_people,
            'edited_photos' => $request->edited_photos,
            'price' => $request->price,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(auth()->id(), 'admin_create_package', "Menambahkan paket foto baru: {$package->name}");

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

        ActivityLog::record(auth()->id(), 'admin_update_package', "Memperbarui data paket: {$package->name}");

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $package = Package::findOrFail($id);
        $name = $package->name;
        $package->delete();

        ActivityLog::record(auth()->id(), 'admin_delete_package', "Menghapus paket foto: {$name}");

        return back()->with('success', 'Paket berhasil dihapus.');
    }
}
