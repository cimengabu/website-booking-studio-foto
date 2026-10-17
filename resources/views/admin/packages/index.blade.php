@extends('layouts.admin')

@section('page-title', 'Kelola Paket Foto & Aturan Harga')

@section('content')
<div class="space-y-8" x-data="{
    addModal: false,
    editModal: false,
    editData: { id: '', name: '', price: '', duration_minutes: 30, max_people: 2, edited_photos: 5, description: '', is_active: true },
    openEdit(pkg) {
        this.editData = { ...pkg, is_active: Boolean(pkg.is_active) };
        this.editModal = true;
    }
}">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-box-open text-[#E8451E]"></i>
                Katalog Paket Foto & Aturan Weekend
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Kelola daftar penawaran paket foto, kuota durasi, foto editan, serta aturan harga khusus.</p>
        </div>
        <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Paket Baru
        </button>
    </div>

    <!-- Packages Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
            <h3 class="text-sm font-heading font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-[#F2C94C]"></i>
                Daftar Paket Foto Aktif ({{ $packages->count() }} Paket)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3.5">Nama Paket</th>
                        <th class="text-center px-4 py-3.5">Durasi</th>
                        <th class="text-center px-4 py-3.5">Kapasitas</th>
                        <th class="text-center px-4 py-3.5">Foto Diedit</th>
                        <th class="text-right px-4 py-3.5">Harga</th>
                        <th class="text-center px-4 py-3.5">Status</th>
                        <th class="text-right px-4 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($packages as $pkg)
                        <tr class="hover:bg-[#141311]/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-white font-sans text-sm block">{{ $pkg->name }}</span>
                                <span class="text-[10px] text-gray-500 font-mono">slug: {{ $pkg->slug }}</span>
                                @if($pkg->description)
                                    <p class="text-[11px] text-gray-400 font-sans mt-0.5 line-clamp-1">{{ $pkg->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center text-gray-300">
                                <span class="px-2.5 py-1 rounded-lg bg-[#141311] border border-[#2D2B26] inline-block font-bold">
                                    {{ $pkg->duration_minutes }} mnt
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-gray-300">
                                <span class="px-2.5 py-1 rounded-lg bg-[#141311] border border-[#2D2B26] inline-block">
                                    Maks {{ $pkg->max_people }} org
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-amber-300 font-bold">
                                {{ $pkg->edited_photos }} file
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-white text-sm">
                                Rp {{ number_format($pkg->price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($pkg->is_active)
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
                                    <button @click="openEdit({{ $pkg }})" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-300 hover:text-[#F2C94C] hover:border-[#F2C94C] transition-all" title="Edit Paket">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $pkg->id) }}" onsubmit="return confirm('Hapus paket {{ $pkg->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-rose-400 hover:border-rose-400 transition-all" title="Hapus Paket">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-500">Belum ada paket foto yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pricing Rules (Weekend / Peak Markup) -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
            <div>
                <h3 class="font-heading font-bold text-base text-white flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-amber-400"></i>
                    Aturan Penyesuaian Harga Weekend & Peak Hour
                </h3>
                <p class="text-xs text-gray-400 font-mono mt-0.5">Penyesuaian markup persentase harga otomatis saat pemesanan hari Sabtu-Minggu atau jam padat.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($pricingRules as $rule)
                <div class="p-4 rounded-2xl bg-[#141311] border border-[#2D2B26] flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-white block">{{ $rule->name }}</span>
                        <p class="text-[11px] text-gray-400 font-mono mt-0.5">
                            Hari: <span class="text-amber-300 font-semibold">{{ ucfirst($rule->day_type) }}</span> · 
                            Markup: <strong class="text-emerald-400">+{{ $rule->markup_percent }}%</strong>
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $rule->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-gray-500/10 text-gray-400' }}">
                        {{ $rule->is_active ? 'Aktif' : 'Off' }}
                    </span>
                </div>
            @empty
                <div class="col-span-2 text-center py-4 text-xs text-gray-500 font-mono">
                    Aturan harga default: Mengikuti tarif flat standar tanpa biaya tambahan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL: Tambah Paket -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="addModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="addModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Tambah Paket Foto Baru</h3>
                <button @click="addModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ route('admin.packages.store') }}" class="space-y-4 text-xs font-mono">
                @csrf
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Nama Paket</label>
                    <input type="text" name="name" required placeholder="Contoh: Graduation Deluxe" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Harga (Rp)</label>
                        <input type="number" name="price" required min="0" placeholder="150000" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" required value="30" step="5" min="15" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Kapasitas Orang</label>
                        <input type="number" name="max_people" required value="2" min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Foto Diedit</label>
                        <input type="number" name="edited_photos" required value="5" min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Deskripsi & Fasilitas</label>
                    <textarea name="description" rows="3" placeholder="Semua file mentah, 5 foto edit, background pilihan..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none font-sans"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="add_is_active" name="is_active" value="1" checked class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="add_is_active" class="text-xs text-gray-300">Langsung aktifkan paket ini untuk pemesanan</label>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Simpan Paket</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Paket -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="editModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Edit Paket Foto</h3>
                <button @click="editModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="`/admin/packages/${editData.id}`" method="POST" class="space-y-4 text-xs font-mono">
                @csrf @method('PUT')
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Nama Paket</label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Harga (Rp)</label>
                        <input type="number" name="price" x-model="editData.price" required min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" x-model="editData.duration_minutes" required step="5" min="15" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Kapasitas Orang</label>
                        <input type="number" name="max_people" x-model="editData.max_people" required min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Foto Diedit</label>
                        <input type="number" name="edited_photos" x-model="editData.edited_photos" required min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Deskripsi & Fasilitas</label>
                    <textarea name="description" x-model="editData.description" rows="3" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none font-sans"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" :checked="editData.is_active" class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="edit_is_active" class="text-xs text-gray-300">Status Aktif untuk pemesanan</label>
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
