@extends('layouts.admin')

@section('page-title', 'Kelola Promo & Voucher')

@section('content')
<div class="space-y-8" x-data="{
    addModal: false,
    editModal: false,
    editData: { id: '', code: '', type: 'percent', value: '', min_order: 0, max_discount: '', quota: 50, start_date: '', end_date: '', is_active: true },
    openEdit(p) {
        this.editData = { ...p, is_active: Boolean(p.is_active) };
        this.editModal = true;
    }
}">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-ticket-simple text-[#E8451E]"></i>
                Kode Promo & Voucher Diskon
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Kelola kupon diskon persentase atau nominal tunai, batas kuota, minimum transaksi, dan periode aktif.</p>
        </div>
        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Kode Promo
        </button>
    </div>

    <!-- Promo Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
            <h3 class="text-sm font-heading font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-tags text-[#F2C94C]"></i>
                Daftar Kode Promo ({{ $promos->total() }} Voucher)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3.5">Kode Promo</th>
                        <th class="text-left px-5 py-3.5">Diskon</th>
                        <th class="text-left px-5 py-3.5">Syarat Minimal</th>
                        <th class="text-center px-5 py-3.5">Penggunaan / Kuota</th>
                        <th class="text-left px-5 py-3.5">Periode Berlaku</th>
                        <th class="text-center px-5 py-3.5">Status</th>
                        <th class="text-right px-5 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($promos as $promo)
                        @php
                            $quotaPercent = $promo->quota > 0 ? min(100, round(($promo->used_count / $promo->quota) * 100)) : 0;
                            $isExpired = $promo->end_date && \Carbon\Carbon::parse($promo->end_date)->isPast();
                            $isExhausted = $promo->used_count >= $promo->quota;
                        @endphp
                        <tr class="hover:bg-[#141311]/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1.5 rounded-xl bg-[#141311] border border-dashed border-[#F2C94C]/40 text-[#F2C94C] font-bold tracking-wider text-xs">
                                        {{ $promo->code }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($promo->type === 'percent')
                                    <span class="text-emerald-400 font-bold text-sm">{{ $promo->value }}% OFF</span>
                                    @if($promo->max_discount)
                                        <p class="text-[10px] text-gray-500">Maks. Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</p>
                                    @endif
                                @else
                                    <span class="text-emerald-400 font-bold text-sm">Potongan Rp {{ number_format($promo->value, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-300">
                                @if($promo->min_order > 0)
                                    <span>Min. Rp {{ number_format($promo->min_order, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-500">Tanpa Minimum</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-white">{{ $promo->used_count }} / {{ $promo->quota }}</span>
                                <div class="w-24 h-1.5 bg-[#141311] rounded-full mx-auto mt-1.5 overflow-hidden border border-[#2D2B26]">
                                    <div class="h-full bg-gradient-to-r from-amber-500 to-[#E8451E] rounded-full" style="width: {{ $quotaPercent }}%"></div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-300">
                                @if($promo->start_date && $promo->end_date)
                                    <p>{{ \Carbon\Carbon::parse($promo->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($promo->end_date)->format('d M Y') }}</p>
                                @elseif($promo->end_date)
                                    <p>s/d {{ \Carbon\Carbon::parse($promo->end_date)->format('d M Y') }}</p>
                                @else
                                    <span class="text-gray-500">Selalu Aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if(!$promo->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-500/10 text-gray-400 border border-gray-700">Nonaktif</span>
                                @elseif($isExpired)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/10 text-rose-400 border border-rose-800">Kedaluwarsa</span>
                                @elseif($isExhausted)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500/10 text-amber-300 border border-amber-800">Habis</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit({{ $promo }})" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-300 hover:text-[#F2C94C] hover:border-[#F2C94C] transition-all" title="Edit Promo">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.promos.destroy', $promo->id) }}" onsubmit="return confirm('Hapus promo {{ $promo->code }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-rose-400 hover:border-rose-400 transition-all" title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-500">Belum ada kode promo atau voucher diskon.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($promos->hasPages())
            <div class="p-4 border-t border-[#2D2B26]">
                {{ $promos->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Tambah Promo -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="addModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="addModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Buat Kode Promo Baru</h3>
                <button @click="addModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ route('admin.promos.store') }}" class="space-y-4 text-xs font-mono" x-data="{ promoType: 'percent' }">
                @csrf
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Kode Promo (Huruf Kapital)</label>
                    <input type="text" name="code" required placeholder="Contoh: SNAPHEMAT20" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none uppercase font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tipe Diskon</label>
                        <select name="type" x-model="promoType" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                            <option value="percent">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold" x-text="promoType === 'percent' ? 'Besar Diskon (%)' : 'Besar Diskon (Rp)'"></label>
                        <input type="number" name="value" required min="1" placeholder="20" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Min. Belanja (Rp)</label>
                        <input type="number" name="min_order" value="0" min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Maks. Potongan Diskon (Rp)</label>
                        <input type="number" name="max_discount" placeholder="Opsional (tanpa batas)" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Batas Kuota Pemakaian</label>
                    <input type="number" name="quota" required value="50" min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Mulai Berlaku</label>
                        <input type="date" name="start_date" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Berakhir Pada</label>
                        <input type="date" name="end_date" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="add_promo_active" name="is_active" value="1" checked class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="add_promo_active" class="text-xs text-gray-300">Langsung aktifkan kode promo ini</label>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Simpan Kode Promo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Promo -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="editModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Edit Kode Promo</h3>
                <button @click="editModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="`/admin/promos/${editData.id}`" method="POST" class="space-y-4 text-xs font-mono">
                @csrf @method('PUT')
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Kode Promo</label>
                    <input type="text" name="code" x-model="editData.code" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none uppercase font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tipe Diskon</label>
                        <select name="type" x-model="editData.type" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                            <option value="percent">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Besar Diskon</label>
                        <input type="number" name="value" x-model="editData.value" required min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Min. Belanja (Rp)</label>
                        <input type="number" name="min_order" x-model="editData.min_order" min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Maks. Potongan Diskon (Rp)</label>
                        <input type="number" name="max_discount" x-model="editData.max_discount" placeholder="Opsional" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Batas Kuota Pemakaian</label>
                    <input type="number" name="quota" x-model="editData.quota" required min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Mulai Berlaku</label>
                        <input type="date" name="start_date" x-model="editData.start_date" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Berakhir Pada</label>
                        <input type="date" name="end_date" x-model="editData.end_date" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="edit_promo_active" name="is_active" value="1" :checked="editData.is_active" class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="edit_promo_active" class="text-xs text-gray-300">Status Aktif untuk pemesanan</label>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
