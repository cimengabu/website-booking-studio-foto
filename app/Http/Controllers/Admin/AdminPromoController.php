<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        Promo::create([
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

        return back()->with('success', 'Kode Promo berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Promo::findOrFail($id)->delete();

        return back()->with('success', 'Kode Promo berhasil dihapus.');
    }
}
