@extends('layouts.admin')

@section('page-title', 'Kelola Add-on Layanan')

@section('content')
<div class="space-y-8" x-data="{
    addModal: false,
    editModal: false,
    editData: { id: '', name: '', price: '', type: 'flat', description: '', is_active: true },
    openEdit(addon) {
        this.editData = { ...addon, is_active: Boolean(addon.is_active) };
        this.editModal = true;
    }
}">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-puzzle-piece text-[#E8451E]"></i>
                Katalog Add-on Layanan Ekstra
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Layanan tambahan seperti ekstra orang, cetak foto kanvas, kostum studio, atau makeup artist.</p>
        </div>
        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Add-on
        </button>
    </div>

    <!-- Addons Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
            <h3 class="text-sm font-heading font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-[#F2C94C]"></i>
                Daftar Add-on Aktif ({{ $addons->count() }} Item)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3.5">Nama Layanan</th>
                        <th class="text-left px-4 py-3.5">Tipe Perhitungan</th>
                        <th class="text-right px-4 py-3.5">Harga</th>
                        <th class="text-center px-4 py-3.5">Status</th>
                        <th class="text-right px-4 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($addons as $addon)
                        <tr class="hover:bg-[#141311]/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-white font-sans text-sm block">{{ $addon->name }}</span>
                                @if($addon->description)
                                    <p class="text-[11px] text-gray-400 font-sans mt-0.5 line-clamp-1">{{ $addon->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-1 rounded-lg bg-[#141311] border border-[#2D2B26] text-gray-300">
                                    @if($addon->type === 'per_person')
                                        <i class="fa-solid fa-user text-[10px] text-cyan-400 mr-1"></i> Per Orang
                                    @elseif($addon->type === 'per_unit')
                                        <i class="fa-solid fa-cubes text-[10px] text-amber-400 mr-1"></i> Per Unit / Lembar
                                    @else
                                        <i class="fa-solid fa-tag text-[10px] text-purple-400 mr-1"></i> Flat (Sekali Sesi)
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-white text-sm">
                                Rp {{ number_format($addon->price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($addon->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit({{ $addon }})" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-300 hover:text-[#F2C94C] hover:border-[#F2C94C] transition-all" title="Edit Add-on">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.addons.destroy', $addon->id) }}" onsubmit="return confirm('Hapus add-on {{ $addon->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-rose-400 hover:border-rose-400 transition-all" title="Hapus Add-on">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-500">Belum ada add-on layanan yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: Tambah Add-on -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="addModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="addModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Tambah Add-on Layanan</h3>
                <button @click="addModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ route('admin.addons.store') }}" class="space-y-4 text-xs font-mono">
                @csrf
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Nama Layanan</label>
                    <input type="text" name="name" required placeholder="Contoh: Tambah Cetak Foto 10R" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Harga (Rp)</label>
                        <input type="number" name="price" required min="0" placeholder="50000" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tipe Perhitungan</label>
                        <select name="type" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                            <option value="flat">Flat (Per Sesi)</option>
                            <option value="per_person">Per Orang</option>
                            <option value="per_unit">Per Unit</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Keterangan singkat add-on..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none font-sans"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="add_addon_active" name="is_active" value="1" checked class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="add_addon_active" class="text-xs text-gray-300">Langsung aktifkan add-on ini</label>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Simpan Add-on</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Add-on -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="editModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Edit Add-on</h3>
                <button @click="editModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="`/admin/addons/${editData.id}`" method="POST" class="space-y-4 text-xs font-mono">
                @csrf @method('PUT')
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Nama Layanan</label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Harga (Rp)</label>
                        <input type="number" name="price" x-model="editData.price" required min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tipe Perhitungan</label>
                        <select name="type" x-model="editData.type" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                            <option value="flat">Flat (Per Sesi)</option>
                            <option value="per_person">Per Orang</option>
                            <option value="per_unit">Per Unit</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Deskripsi</label>
                    <textarea name="description" x-model="editData.description" rows="3" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none font-sans"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="edit_addon_active" name="is_active" value="1" :checked="editData.is_active" class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="edit_addon_active" class="text-xs text-gray-300">Status Aktif untuk pemesanan</label>
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
