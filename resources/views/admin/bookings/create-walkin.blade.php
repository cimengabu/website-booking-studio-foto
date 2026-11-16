@extends('layouts.admin')

@section('title', 'Booking Walk-In — SnapSpace Admin')
@section('page-title', 'Booking Walk-In Kasir')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header & Back Button -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.bookings.index') }}" class="p-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="font-heading font-bold text-xl text-white flex items-center gap-2.5">
                <i class="fa-solid fa-person-walking text-[#E8451E]"></i>
                Input Booking Walk-In Studio
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-0.5">Catat sesi tamu yang datang langsung ke meja kasir studio secara instan.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
            <div class="flex items-center gap-2 mb-2 font-bold text-rose-400">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Terdapat kesalahan pengisian form:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bookings.walkin.store') }}" class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf

        <!-- 1. Pelanggan -->
        <div class="space-y-2">
            <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                1. Pilih Pelanggan Terdaftar <span class="text-[#E8451E]">*</span>
            </label>
            <select name="user_id" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                <option value="">-- Pilih Akun Tamu / Pelanggan --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('user_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->email }} · {{ $c->phone ?: 'No HP -' }})
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-gray-500 font-mono">Jika tamu baru belum punya akun, buat terlebih dahulu di menu <a href="{{ route('admin.users.index') }}" class="text-[#F2C94C] hover:underline">Manajemen Tim & Pengguna</a>.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-[#2D2B26]">
            <!-- 2. Ruangan -->
            <div class="space-y-2">
                <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                    2. Ruangan Studio <span class="text-[#E8451E]">*</span>
                </label>
                <select name="room_id" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ old('room_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }} (Tema: {{ $r->theme }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Paket Foto -->
            <div class="space-y-2">
                <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                    3. Paket Foto <span class="text-[#E8451E]">*</span>
                </label>
                <select name="package_id" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                    <option value="">-- Pilih Paket Foto --</option>
                    @foreach($packages as $p)
                        <option value="{{ $p->id }}" {{ old('package_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} — Rp {{ number_format($p->price, 0, ',', '.') }} ({{ $p->duration_minutes }} menit, max {{ $p->max_people }} org)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2 border-t border-[#2D2B26]">
            <!-- 4. Tanggal Sesi -->
            <div class="space-y-2">
                <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                    4. Tanggal Sesi <span class="text-[#E8451E]">*</span>
                </label>
                <input type="date" name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
            </div>

            <!-- 5. Jam Mulai -->
            <div class="space-y-2">
                <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                    5. Jam Mulai <span class="text-[#E8451E]">*</span>
                </label>
                <select name="start_time" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                    @foreach(['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'] as $time)
                        <option value="{{ $time }}" {{ old('start_time', '10:00') === $time ? 'selected' : '' }}>{{ $time }} WIB</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Jumlah Orang -->
            <div class="space-y-2">
                <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                    6. Jumlah Orang <span class="text-[#E8451E]">*</span>
                </label>
                <input type="number" name="people_count" min="1" max="20" value="{{ old('people_count', 2) }}" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-[#2D2B26]">
            <!-- 7. Opsi Pembayaran -->
            <div class="space-y-2">
                <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                    7. Skema Pembayaran <span class="text-[#E8451E]">*</span>
                </label>
                <select name="payment_kind" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                    <option value="full" {{ old('payment_kind') === 'full' ? 'selected' : '' }}>Langsung Lunas 100% (Direkomendasikan)</option>
                    <option value="dp" {{ old('payment_kind') === 'dp' ? 'selected' : '' }}>Down Payment (DP 50%)</option>
                </select>
            </div>

            <!-- 8. Metode Pembayaran Kasir -->
            <div class="space-y-2">
                <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                    8. Metode Pembayaran Kasir <span class="text-[#E8451E]">*</span>
                </label>
                <select name="payment_method" required class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                    <option value="Tunai / Cash Kasir">Tunai / Uang Cash</option>
                    <option value="Mesin EDC Debit BCA">Mesin EDC / Debit Card</option>
                    <option value="QRIS Statis Meja Kasir">QRIS Statis Toko</option>
                    <option value="Transfer Manual Studio">Transfer Bank Studio</option>
                </select>
            </div>
        </div>

        <!-- Catatan Tambahan -->
        <div class="space-y-2 pt-2 border-t border-[#2D2B26]">
            <label class="block text-xs font-mono font-bold text-gray-300 uppercase">
                Catatan Sesi / Request Tamu (Opsional)
            </label>
            <textarea name="notes" rows="2" placeholder="Catatan tambahan, permintaan tema lighting, aksesoris khusus..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">{{ old('notes') }}</textarea>
        </div>

        <!-- Tombol Submit -->
        <div class="pt-4 border-t border-[#2D2B26] flex items-center justify-end gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 rounded-xl border border-[#2D2B26] text-gray-400 hover:text-white text-xs font-mono transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs shadow-lg shadow-[#E8451E]/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Konfirmasi & Terbitkan Booking Walk-In</span>
            </button>
        </div>
    </form>
</div>
@endsection
