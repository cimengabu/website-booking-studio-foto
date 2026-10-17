@extends('layouts.admin')

@section('page-title', 'Studio, Ruangan & Fotografer')

@section('content')
<div class="space-y-8" x-data="{
    activeTab: 'rooms',
    addRoomModal: false,
    editRoomModal: false,
    addBlockModal: false,
    editRoomData: { id: '', name: '', theme: '', capacity: 6, area_m2: 35, description: '', is_active: true },
    openEditRoom(r) {
        this.editRoomData = { ...r, is_active: Boolean(r.is_active) };
        this.editRoomModal = true;
    }
}">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-door-open text-[#E8451E]"></i>
                Studio, Ruangan & Tim Fotografer
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Kelola data tema ruangan studio, kapasitas, jadwal kerja crew fotografer, serta blok libur/maintenance.</p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="addRoomModal = true" class="px-4 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Ruangan
            </button>
            <button @click="addBlockModal = true" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-amber-500/50 text-amber-300 font-bold text-xs transition-all flex items-center gap-2">
                <i class="fa-solid fa-ban text-xs"></i> Blok Libur / Maintenance
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex gap-2 border-b border-[#2D2B26] pb-1">
        <button @click="activeTab = 'rooms'" :class="activeTab === 'rooms' ? 'text-[#E8451E] border-b-2 border-[#E8451E] font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-xs font-mono transition-all flex items-center gap-2">
            <i class="fa-solid fa-camera-retro"></i> Ruangan & Tema Studio ({{ $rooms->count() }})
        </button>
        <button @click="activeTab = 'photographers'" :class="activeTab === 'photographers' ? 'text-[#E8451E] border-b-2 border-[#E8451E] font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-xs font-mono transition-all flex items-center gap-2">
            <i class="fa-solid fa-id-badge"></i> Jadwal Kerja Fotografer ({{ $photographers->count() }})
        </button>
        <button @click="activeTab = 'blocked'" :class="activeTab === 'blocked' ? 'text-[#E8451E] border-b-2 border-[#E8451E] font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-xs font-mono transition-all flex items-center gap-2">
            <i class="fa-solid fa-calendar-xmark"></i> Blok Libur & Maintenance ({{ $blockedSlots->count() }})
        </button>
    </div>

    <!-- TAB 1: Ruangan & Tema Studio -->
    <div x-show="activeTab === 'rooms'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rooms as $room)
                @php
                    $primaryImg = $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800&auto=format&fit=crop';
                @endphp
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl overflow-hidden shadow-xl flex flex-col justify-between group hover:border-[#E8451E]/40 transition-all">
                    <!-- Image Banner -->
                    <div class="h-44 relative overflow-hidden bg-gray-900">
                        <img src="{{ $primaryImg }}" alt="{{ $room->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1E1D1A] via-transparent to-black/30"></div>
                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold uppercase {{ $room->is_active ? 'bg-emerald-500/90 text-white' : 'bg-rose-500/90 text-white' }}">
                            {{ $room->is_active ? 'Aktif' : 'Tutup' }}
                        </span>
                        <div class="absolute bottom-3 left-4 right-4">
                            <span class="text-[10px] font-mono text-[#F2C94C] uppercase tracking-wider block font-bold">{{ $room->theme }}</span>
                            <h3 class="font-heading font-black text-xl text-white">{{ $room->name }}</h3>
                        </div>
                    </div>

                    <!-- Room Meta -->
                    <div class="p-5 space-y-4 flex-grow flex flex-col justify-between">
                        <p class="text-xs text-gray-300 font-sans line-clamp-2">
                            {{ $room->description ?: 'Studio foto profesional dengan peralatan lighting lengkap dan background modular.' }}
                        </p>

                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-[#2D2B26] text-xs font-mono">
                            <div class="p-2.5 rounded-xl bg-[#141311] border border-[#2D2B26]">
                                <span class="text-[10px] text-gray-500 uppercase block">Kapasitas</span>
                                <strong class="text-white">{{ $room->capacity }} Orang</strong>
                            </div>
                            <div class="p-2.5 rounded-xl bg-[#141311] border border-[#2D2B26]">
                                <span class="text-[10px] text-gray-500 uppercase block">Luas Area</span>
                                <strong class="text-white">{{ $room->area_m2 }} m²</strong>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-3 border-t border-[#2D2B26] flex items-center justify-between">
                            <button @click="openEditRoom({{ $room }})" class="px-4 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-xs font-mono font-bold text-gray-300 hover:text-[#F2C94C] hover:border-[#F2C94C] transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-pen text-[10px]"></i> Edit Studio
                            </button>
                            <form method="POST" action="{{ route('admin.rooms.destroy', $room->id) }}" onsubmit="return confirm('Hapus ruangan {{ $room->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-rose-400 hover:border-rose-400 transition-all" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 text-gray-500 bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl">
                    Belum ada studio atau ruangan yang ditambahkan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 2: Jadwal Kerja Tim Fotografer -->
    <div x-show="activeTab === 'photographers'" class="space-y-6">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
            <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                        <i class="fa-solid fa-camera text-amber-400"></i>
                        Daftar Crew & Fotografer Aktif
                    </h3>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">Pantau sesi aktif yang sedang ditangani tiap fotografer studio</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-xs font-mono font-bold text-gray-300 hover:text-white transition-all">
                    Kelola Anggota Tim &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs font-mono">
                    <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase">
                        <tr>
                            <th class="text-left px-5 py-3.5">Nama Fotografer</th>
                            <th class="text-left px-5 py-3.5">Kontak</th>
                            <th class="text-center px-5 py-3.5">Sesi Aktif Ditangani</th>
                            <th class="text-center px-5 py-3.5">Status Ketersediaan</th>
                            <th class="text-right px-5 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2D2B26]">
                        @forelse($photographers as $ph)
                            <tr class="hover:bg-[#141311]/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 font-bold flex items-center justify-center font-heading text-sm">
                                            {{ strtoupper(substr($ph->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-white font-sans text-sm">{{ $ph->name }}</p>
                                            <span class="text-[10px] text-gray-500 font-mono">ID Crew: #{{ $ph->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-300">
                                    <p>{{ $ph->email }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $ph->phone ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold font-mono
                                        {{ $ph->active_sessions_count > 0 ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30' : 'bg-gray-500/10 text-gray-400' }}">
                                        <i class="fa-solid fa-camera"></i> {{ $ph->active_sessions_count }} Sesi
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        Tersedia (Ready)
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.bookings.index', ['photographer_id' => $ph->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-gray-300 hover:text-white text-xs font-mono font-bold transition-all">
                                        Lihat Jadwal
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-gray-500">Belum ada user dengan role Fotografer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: Blok Libur & Maintenance -->
    <div x-show="activeTab === 'blocked'" class="space-y-6">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
            <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                        <i class="fa-solid fa-calendar-xmark text-rose-400"></i>
                        Daftar Tanggal / Jam Studio Diblokir
                    </h3>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">Slot pada waktu ini otomatis tidak dapat dipilih atau dipesan oleh pelanggan di website</p>
                </div>
                <button @click="addBlockModal = true" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs font-mono transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Blok Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs font-mono">
                    <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase">
                        <tr>
                            <th class="text-left px-5 py-3.5">Tanggal</th>
                            <th class="text-left px-5 py-3.5">Rentang Jam</th>
                            <th class="text-left px-5 py-3.5">Ruangan / Studio</th>
                            <th class="text-left px-5 py-3.5">Alasan Penutupan</th>
                            <th class="text-right px-5 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2D2B26]">
                        @forelse($blockedSlots as $block)
                            <tr class="hover:bg-[#141311]/50 transition-colors">
                                <td class="px-5 py-4 font-bold text-white font-sans text-sm">
                                    {{ \Carbon\Carbon::parse($block->date)->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-amber-300 font-bold">
                                    {{ substr($block->start_time, 0, 5) }} - {{ substr($block->end_time, 0, 5) }} WIB
                                </td>
                                <td class="px-5 py-4 text-gray-300">
                                    {{ $block->room ? $block->room->name : 'Semua Ruangan (Studio Tutup Total)' }}
                                </td>
                                <td class="px-5 py-4 text-gray-300 font-sans italic">
                                    "{{ $block->reason }}"
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.blocked-slots.destroy', $block->id) }}" onsubmit="return confirm('Buka kembali slot ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-rose-400 hover:border-rose-400 transition-all" title="Hapus Blokir">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-gray-500">Tidak ada jadwal blokir atau hari libur yang sedang aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL: Tambah Ruangan -->
    <div x-show="addRoomModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="addRoomModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="addRoomModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Tambah Ruangan Studio Baru</h3>
                <button @click="addRoomModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ route('admin.rooms.store') }}" class="space-y-4 text-xs font-mono">
                @csrf
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Nama Ruangan</label>
                    <input type="text" name="name" required placeholder="Contoh: Studio A (Vintage Minimalist)" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tema / Gaya Ruangan</label>
                    <input type="text" name="theme" required placeholder="Contoh: Warm Earth Tone & Retro" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Kapasitas Maksimal (Orang)</label>
                        <input type="number" name="capacity" required value="6" min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Luas Area (m²)</label>
                        <input type="number" name="area_m2" required value="35" min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">URL Foto Utama</label>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Deskripsi Studio</label>
                    <textarea name="description" rows="3" placeholder="Peralatan lighting, background, AC, dll..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none font-sans"></textarea>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="addRoomModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Simpan Ruangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Ruangan -->
    <div x-show="editRoomModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="editRoomModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="editRoomModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Edit Ruangan Studio</h3>
                <button @click="editRoomModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="`/admin/rooms/${editRoomData.id}`" method="POST" class="space-y-4 text-xs font-mono">
                @csrf @method('PUT')
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Nama Ruangan</label>
                    <input type="text" name="name" x-model="editRoomData.name" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tema / Gaya Ruangan</label>
                    <input type="text" name="theme" x-model="editRoomData.theme" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Kapasitas (Orang)</label>
                        <input type="number" name="capacity" x-model="editRoomData.capacity" required min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Luas Area (m²)</label>
                        <input type="number" name="area_m2" x-model="editRoomData.area_m2" required min="1" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Deskripsi Studio</label>
                    <textarea name="description" x-model="editRoomData.description" rows="3" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none font-sans"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="edit_room_active" name="is_active" value="1" :checked="editRoomData.is_active" class="rounded border-[#2D2B26] text-[#E8451E] focus:ring-0">
                    <label for="edit_room_active" class="text-xs text-gray-300">Status Aktif untuk pemesanan</label>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="editRoomModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Tambah Blok Tanggal/Jam -->
    <div x-show="addBlockModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="addBlockModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="addBlockModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Blokir Jadwal Libur / Maintenance</h3>
                <button @click="addBlockModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ route('admin.blocked-slots.store') }}" class="space-y-4 text-xs font-mono">
                @csrf
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Ruangan yang Diblokir</label>
                    <select name="room_id" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                        <option value="">Semua Ruangan (Studio Tutup Penuh)</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->theme }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tanggal</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Mulai Jam</label>
                        <input type="time" name="start_time" required value="09:00" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Sampai Jam</label>
                        <input type="time" name="end_time" required value="21:00" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Alasan Penutupan</label>
                    <input type="text" name="reason" required placeholder="Contoh: Maintenance Lighting / Libur Nasional" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none">
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="addBlockModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-500">Simpan Blokir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
