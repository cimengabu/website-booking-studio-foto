<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Promo;
use Illuminate\Http\Request;

class AdminPromoController extends Controller
{
    public function index()
    {
        $promos = Promo::latest()->paginate(15);

        return view('admin.promos.index', compact('promos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:promos,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
        ]);

        $promo = Promo::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order' => $request->min_order,
            'max_discount' => $request->max_discount ?: null,
            'quota' => $request->quota,
            'start_date' => $request->start_date ?: null,
            'end_date' => $request->end_date ?: null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(auth()->id(), 'admin_create_promo', "Membuat kode promo baru: {$promo->code}");

        return back()->with('success', 'Kode Promo berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $promo = Promo::findOrFail($id);
        $request->validate([
            'code' => 'required|string|unique:promos,code,'.$promo->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
        ]);

        $promo->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order' => $request->min_order,
            'max_discount' => $request->max_discount ?: null,
            'quota' => $request->quota,
            'start_date' => $request->start_date ?: null,
            'end_date' => $request->end_date ?: null,
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record(auth()->id(), 'admin_update_promo', "Memperbarui kode promo: {$promo->code}");

        return back()->with('success', 'Kode Promo berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $promo = Promo::findOrFail($id);
        $code = $promo->code;
        $promo->delete();

        ActivityLog::record(auth()->id(), 'admin_delete_promo', "Menghapus kode promo: {$code}");

        return back()->with('success', 'Kode Promo berhasil dihapus.');
    }
}
