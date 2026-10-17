@extends('layouts.admin')

@section('title', 'Manajemen Tim & RBAC — SnapSpace Admin')
@section('page-title', 'Manajemen Tim & Hak Akses (RBAC)')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, selectedUser: null, newRole: '' }">

    {{-- Top Action & Role Tabs --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        {{-- Role Tabs --}}
        <div class="inline-flex p-1 bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl gap-1 text-xs font-mono">
            @foreach([
                ['all', 'Semua (' . $counts['all'] . ')'],
                ['owner', 'Owner (' . $counts['owner'] . ')'],
                ['staff', 'Staff / Kasir (' . $counts['staff'] . ')'],
                ['photographer', 'Fotografer (' . $counts['photographer'] . ')'],
            ] as [$key, $label])
                <a href="{{ route('admin.users.index', ['role' => $key, 'search' => request('search')]) }}"
                   class="px-3.5 py-1.5 rounded-xl font-bold transition-all {{ (request('role', 'all') === $key) ? 'bg-[#E8451E] text-white shadow-md' : 'text-gray-400 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Add Team Button --}}
        <button @click="addModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white text-xs font-bold shadow-lg shadow-[#E8451E]/20 transition-all shrink-0">
            <i class="fa-solid fa-user-plus text-xs"></i> Tambah Anggota Tim
        </button>
    </div>

    {{-- Search Filter Bar --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-4 shadow-xl">
        <form method="GET" class="flex gap-3">
            <input type="hidden" name="role" value="{{ request('role', 'all') }}">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, email, atau nomor HP anggota tim..."
                       class="w-full bg-[#141311] border border-[#2D2B26] text-white text-xs rounded-xl pl-9 pr-4 py-2.5 focus:outline-none focus:border-[#E8451E] font-mono">
            </div>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#2D2B26] hover:bg-[#383630] text-gray-200 text-xs font-bold transition-colors">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users.index', ['role' => request('role')]) }}" class="px-3 py-2.5 rounded-xl border border-[#2D2B26] text-gray-400 hover:text-white text-xs flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Dense & Clean Data Table --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs font-sans">
                <thead class="bg-[#141311] text-gray-400 font-mono text-[10px] uppercase border-b border-[#2D2B26]">
                    <tr>
                        <th class="text-left px-4 py-3">Anggota Tim</th>
                        <th class="text-left px-4 py-3">Kontak</th>
                        <th class="text-left px-4 py-3">Hak Akses (Role)</th>
                        <th class="text-left px-4 py-3">Izin Akses</th>
                        <th class="text-left px-4 py-3">Bergabung</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($users as $u)
                        <tr class="hover:bg-[#171614] transition-colors">
                            {{-- Name & Avatar --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full overflow-hidden shrink-0">
                                        @if($u->avatar)
                                            <img src="{{ Storage::url($u->avatar) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-[#E8451E] to-[#b33112] flex items-center justify-center font-bold text-white text-xs">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-white text-xs truncate">{{ $u->name }}</p>
                                        <p class="text-[10px] text-gray-500 font-mono">ID: #USR-{{ str_pad($u->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-4 py-3 font-mono">
                                <p class="text-gray-300 text-xs truncate">{{ $u->email }}</p>
                                <p class="text-[11px] text-gray-500">{{ $u->phone ?: '—' }}</p>
                            </td>

                            {{-- Role Badge --}}
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider {{ $u->role_badge_class }}">
                                    @if($u->isOwner())
                                        <i class="fa-solid fa-crown text-[9px]"></i>
                                    @elseif($u->isStaff())
                                        <i class="fa-solid fa-cash-register text-[9px]"></i>
                                    @elseif($u->isPhotographer())
                                        <i class="fa-solid fa-camera text-[9px]"></i>
                                    @endif
                                    {{ $u->role_label }}
                                </span>
                            </td>

                            {{-- Permissions snippet --}}
                            <td class="px-4 py-3 text-[11px] text-gray-400">
                                @if($u->isOwner())
                                    <span class="text-amber-400 font-medium">Akses Penuh (Full Control, Finansial & Master)</span>
                                @elseif($u->isStaff())
                                    <span class="text-emerald-400 font-medium">Booking, Pembayaran, Check-in, Walk-In</span>
                                @elseif($u->isPhotographer())
                                    <span class="text-[#F2C94C] font-medium">Jadwal Sesi Sendiri, Upload Foto Galeri</span>
                                @endif
                            </td>

                            {{-- Joined --}}
                            <td class="px-4 py-3 text-[11px] font-mono text-gray-400">
                                {{ $u->created_at->format('d M Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Edit Role Button --}}
                                    <button @click="selectedUser = {{ $u->id }}; newRole = '{{ $u->role }}'; editModal = true"
                                            class="px-2.5 py-1.5 rounded-lg bg-[#2D2B26] hover:bg-[#383630] text-gray-300 hover:text-white text-xs font-mono font-medium transition-colors"
                                            title="Ubah Hak Akses">
                                        <i class="fa-solid fa-user-gear mr-1"></i> Ubah Role
                                    </button>

                                    {{-- Delete Button (forbidden for current user) --}}
                                    @if($u->id !== Auth::id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota tim {{ addslashes($u->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors" title="Hapus User">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-500 font-mono text-xs">
                                Tidak ada anggota tim yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#2D2B26]">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- ── MODAL TAMBAH ANGGOTA TIM ── --}}
    <div x-show="addModal" x-transition.opacity class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="addModal = false" class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#2D2B26]">
                <h3 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-[#E8451E]"></i> Tambah Anggota Tim
                </h3>
                <button @click="addModal = false" class="text-gray-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-mono font-bold text-gray-400 uppercase mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Contoh: Rian Pratama"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-[#E8451E]">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-gray-400 uppercase mb-1">Email Login *</label>
                    <input type="email" name="email" required placeholder="rian@snapspace.id"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-[#E8451E] font-mono">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-gray-400 uppercase mb-1">Nomor WhatsApp</label>
                    <input type="text" name="phone" placeholder="08xxxxxxxxxx"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-[#E8451E] font-mono">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-gray-400 uppercase mb-1">Peran / Hak Akses (RBAC) *</label>
                    <select name="role" required class="w-full bg-[#141311] border border-[#2D2B26] rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-[#E8451E] font-mono">
                        <option value="staff">Staff / Kasir (Booking & Pembayaran)</option>
                        <option value="photographer">Fotografer (Jadwal Sendiri & Upload Foto)</option>
                        <option value="owner">Owner / Admin (Akses Penuh)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-gray-400 uppercase mb-1">Password Sementara *</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-[#E8451E]">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2D2B26]">
                    <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl border border-[#2D2B26] text-gray-400 hover:text-white text-xs font-mono font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white text-xs font-bold transition-all shadow-md">
                        Simpan Anggota Tim
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL UBAH ROLE ── --}}
    <div x-show="editModal" x-transition.opacity class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="editModal = false" class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#2D2B26]">
                <h3 class="font-heading font-bold text-base text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-[#F2C94C]"></i> Ubah Hak Akses (Role)
                </h3>
                <button @click="editModal = false" class="text-gray-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'{{ url('/admin/users') }}/' + selectedUser + '/role'" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-mono font-bold text-gray-400 uppercase mb-2">Pilih Role Baru:</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[#2D2B26] hover:border-[#E8451E]/40 bg-[#141311] cursor-pointer">
                            <input type="radio" name="role" value="owner" x-model="newRole" class="text-[#E8451E] focus:ring-0">
                            <div>
                                <p class="text-xs font-bold text-white">Owner / Admin</p>
                                <p class="text-[10px] text-gray-500 font-mono">Akses penuh ke semua modul sistem & keuangan</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[#2D2B26] hover:border-emerald-500/40 bg-[#141311] cursor-pointer">
                            <input type="radio" name="role" value="staff" x-model="newRole" class="text-emerald-500 focus:ring-0">
                            <div>
                                <p class="text-xs font-bold text-white">Staff / Kasir</p>
                                <p class="text-[10px] text-gray-500 font-mono">Kelola booking, kasir, walk-in, dan check-in QR</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[#2D2B26] hover:border-[#F2C94C]/40 bg-[#141311] cursor-pointer">
                            <input type="radio" name="role" value="photographer" x-model="newRole" class="text-[#F2C94C] focus:ring-0">
                            <div>
                                <p class="text-xs font-bold text-white">Fotografer</p>
                                <p class="text-[10px] text-gray-500 font-mono">Jadwal sesi foto sendiri & upload hasil foto</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2D2B26]">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-[#2D2B26] text-gray-400 hover:text-white text-xs font-mono font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#F2C94C] hover:bg-[#dfb73e] text-black font-bold text-xs transition-all shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
