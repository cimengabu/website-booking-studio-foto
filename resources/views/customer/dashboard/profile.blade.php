@extends('layouts.dashboard')

@section('title', 'Profil & Pengaturan — SnapSpace Studio')
@section('breadcrumb', 'Profil & Pengaturan')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── PROFILE HEADER ── --}}
    <div class="bg-gradient-to-br from-[#1E1D1A] to-[#141311] border border-[#2D2B26] rounded-3xl p-6 sm:p-8 relative overflow-hidden" data-aos="fade-up">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full bg-[#E8451E]/5 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row items-center sm:items-start gap-6">
            {{-- Avatar section --}}
            <div class="relative group">
                @if(Auth::user()->avatar ?? false)
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar"
                         class="w-24 h-24 rounded-2xl object-cover ring-2 ring-[#E8451E]/30">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-[#E8451E] to-[#d03a16] flex items-center justify-center">
                        <span class="font-heading font-black text-4xl text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    </div>
                @endif
                {{-- Upload overlay --}}
                <label for="avatar-upload"
                       class="absolute inset-0 rounded-2xl bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                    <i class="fa-solid fa-camera text-white text-lg mb-1"></i>
                    <span class="text-[10px] text-white font-mono">Ganti</span>
                </label>
                <form id="avatar-form" method="POST" action="{{ route('dashboard.profile.avatar') }}" enctype="multipart/form-data">
                    @csrf @method('PATCH')
                    <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*"
                           onchange="document.getElementById('avatar-form').submit()">
                </form>
            </div>

            {{-- User info --}}
            <div class="flex-1 text-center sm:text-left">
                <h1 class="font-heading font-bold text-2xl text-white">{{ Auth::user()->name }}</h1>
                <p class="text-gray-400 text-sm font-mono mt-1">{{ Auth::user()->email }}</p>
                @if(Auth::user()->phone)
                    <p class="text-gray-500 text-sm mt-0.5"><i class="fa-solid fa-phone text-xs text-[#E8451E] mr-1.5"></i>{{ Auth::user()->phone }}</p>
                @endif
                <div class="flex flex-wrap justify-center sm:justify-start gap-3 mt-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#E8451E]/10 border border-[#E8451E]/25 rounded-full text-xs font-mono text-[#E8451E]">
                        <i class="fa-solid fa-user-check text-[10px]"></i> Member SnapSpace
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/25 rounded-full text-xs font-mono text-emerald-400">
                        <i class="fa-solid fa-shield-halved text-[10px]"></i> Akun Aktif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── EDIT PROFILE FORM ── --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-6" data-aos="fade-up" data-aos-delay="50">
        <h2 class="font-heading font-bold text-lg text-white mb-5 flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-[#E8451E] text-sm"></i>
            Informasi Profil
        </h2>

        <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-5">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] rounded-xl px-4 py-3 text-sm text-white placeholder-gray-700 outline-none transition-colors"
                           placeholder="Nama lengkap Anda">
                    @error('name') <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Alamat Email *</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] rounded-xl px-4 py-3 text-sm text-white placeholder-gray-700 outline-none transition-colors"
                           placeholder="email@contoh.com">
                    @error('email') <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                <div class="flex">
                    <span class="flex items-center px-3 bg-[#141311] border border-r-0 border-[#2D2B26] rounded-l-xl text-gray-500 text-sm font-mono">+62</span>
                    <input type="text" name="phone" value="{{ old('phone', ltrim(Auth::user()->phone ?? '', '+62')) }}"
                           class="flex-1 bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] rounded-r-xl px-4 py-3 text-sm text-white placeholder-gray-700 outline-none transition-colors"
                           placeholder="8123456789">
                </div>
                @error('phone') <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#E8451E] text-white rounded-xl font-semibold text-sm hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- ── CHANGE PASSWORD FORM ── --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-6" data-aos="fade-up" data-aos-delay="100">
        <h2 class="font-heading font-bold text-lg text-white mb-5 flex items-center gap-2">
            <i class="fa-solid fa-lock text-[#F2C94C] text-sm"></i>
            Ubah Password
        </h2>

        <form method="POST" action="{{ route('dashboard.profile.password') }}" class="space-y-5">
            @csrf @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password Saat Ini</label>
                <div x-data="{ show: false }" class="relative">
                    <input :type="show ? 'text' : 'password'" name="current_password" required
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#F2C94C] rounded-xl px-4 py-3 pr-11 text-sm text-white placeholder-gray-700 outline-none transition-colors"
                           placeholder="Password lama Anda">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                        <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                    </button>
                </div>
                @error('current_password') <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password Baru</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" required
                               class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#F2C94C] rounded-xl px-4 py-3 pr-11 text-sm text-white placeholder-gray-700 outline-none transition-colors"
                               placeholder="Min. 8 karakter">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                               class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#F2C94C] rounded-xl px-4 py-3 pr-11 text-sm text-white placeholder-gray-700 outline-none transition-colors"
                               placeholder="Ulangi password baru">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-[#141311] border border-[#2D2B26] rounded-xl px-4 py-3">
                <p class="text-xs text-gray-500">
                    <i class="fa-solid fa-shield-halved text-[#F2C94C] mr-1.5"></i>
                    Password minimal 8 karakter, kombinasikan huruf besar, kecil, angka, dan simbol untuk keamanan lebih baik.
                </p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#F2C94C] text-[#141311] rounded-xl font-semibold text-sm hover:bg-[#e6ba2e] shadow-lg shadow-[#F2C94C]/15 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fa-solid fa-lock text-xs"></i>
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>

    {{-- ── NOTIFICATION PREFERENCES (UI only) ── --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-6" data-aos="fade-up" data-aos-delay="150">
        <h2 class="font-heading font-bold text-lg text-white mb-5 flex items-center gap-2">
            <i class="fa-solid fa-bell text-emerald-400 text-sm"></i>
            Preferensi Notifikasi
        </h2>

        <div class="space-y-4" x-data="{
            emailBooking: true,
            emailPayment: true,
            emailPromo: false,
            whatsapp: true
        }">
            @foreach([
                ['emailBooking', 'Konfirmasi & Update Booking', 'Email notifikasi saat booking dikonfirmasi, reschedule, atau dibatalkan.', 'fa-ticket'],
                ['emailPayment', 'Pengingat Pembayaran', 'Email pengingat jika ada pembayaran yang belum selesai atau pelunasan jatuh tempo.', 'fa-credit-card'],
                ['emailPromo', 'Promo & Penawaran Spesial', 'Email mengenai promo terbaru, diskon, dan penawaran eksklusif member.', 'fa-tag'],
                ['whatsapp', 'Notifikasi WhatsApp', 'Pengingat sesi foto H-1 dan H-2 jam via WhatsApp.', 'fa-whatsapp'],
            ] as [$key, $label, $desc, $icon])
            <div class="flex items-start justify-between gap-4 py-3 border-b border-[#2D2B26] last:border-0">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#141311] border border-[#2D2B26] flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-{{ $icon === 'fa-whatsapp' ? 'brands' : 'solid' }} {{ $icon }} text-sm text-gray-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $label }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
                <button @click="{{ $key }} = !{{ $key }}"
                        class="relative flex-shrink-0 w-11 h-6 rounded-full transition-colors mt-1"
                        :class="{{ $key }} ? 'bg-[#E8451E]' : 'bg-[#2D2B26]'">
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                          :class="{{ $key }} ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
            </div>
            @endforeach

            <div class="flex justify-end pt-2">
                <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#141311] border border-[#2D2B26] text-gray-300 hover:text-white rounded-xl font-semibold text-sm transition-colors">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Preferensi
                </button>
            </div>
        </div>
    </div>

    {{-- ── DANGER ZONE ── --}}
    <div class="bg-rose-950/20 border border-rose-500/20 rounded-2xl p-6" data-aos="fade-up" data-aos-delay="200">
        <h2 class="font-heading font-bold text-lg text-rose-400 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-sm"></i>
            Zona Berbahaya
        </h2>
        <p class="text-sm text-rose-300/70 mb-5">
            Menghapus akun bersifat permanen dan tidak dapat dipulihkan. Semua data booking, galeri foto, dan riwayat transaksi akan ikut terhapus.
        </p>

        <div x-data="{ open: false, confirm: '' }">
            <button @click="open = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-500/15 border border-rose-500/30 text-rose-400 hover:bg-rose-500/25 rounded-xl font-semibold text-sm transition-colors">
                <i class="fa-solid fa-trash text-xs"></i>
                Hapus Akun Saya
            </button>

            <div x-show="open" x-transition
                 class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
                 @click.self="open = false">
                <div class="bg-[#1E1D1A] border border-rose-500/30 rounded-2xl p-6 max-w-md w-full" @click.stop>
                    <div class="text-center mb-5">
                        <div class="w-14 h-14 rounded-2xl bg-rose-500/15 border border-rose-500/25 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-triangle-exclamation text-rose-400 text-2xl"></i>
                        </div>
                        <h3 class="font-heading font-bold text-xl text-white mb-1">Hapus Akun?</h3>
                        <p class="text-sm text-gray-400">Tindakan ini tidak dapat dibatalkan. Ketik <strong class="text-rose-400 font-mono">HAPUS</strong> untuk mengonfirmasi.</p>
                    </div>

                    <form method="POST" action="{{ route('dashboard.profile.delete') }}">
                        @csrf @method('DELETE')
                        <input type="text" name="confirm_delete" x-model="confirm"
                               class="w-full bg-[#141311] border border-[#2D2B26] focus:border-rose-500 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-700 outline-none transition-colors mb-4 text-center font-mono tracking-widest"
                               placeholder="Ketik HAPUS di sini">

                        <div class="flex gap-3">
                            <button type="button" @click="open = false; confirm = ''"
                                    class="flex-1 py-3 border border-[#2D2B26] text-gray-300 hover:text-white rounded-xl font-semibold text-sm transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="confirm !== 'HAPUS'"
                                    :class="confirm === 'HAPUS' ? 'bg-rose-500 hover:bg-rose-600 text-white shadow-lg shadow-rose-500/25' : 'bg-rose-500/20 text-rose-500/40 cursor-not-allowed'"
                                    class="flex-1 py-3 rounded-xl font-semibold text-sm transition-all">
                                <i class="fa-solid fa-trash text-xs mr-1.5"></i>Hapus Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
