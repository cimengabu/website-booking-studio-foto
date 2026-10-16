@extends('layouts.admin')

@section('title', 'Pengaturan Studio & Aturan Bisnis — SnapSpace')
@section('page-title', 'Pengaturan Studio & Aturan Bisnis')

@section('content')
<div class="max-w-5xl space-y-8">

    {{-- Breadcrumb / Header info --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-heading font-black text-2xl text-white">Konfigurasi Aturan Bisnis & Studio</h2>
            <p class="text-sm text-gray-400 mt-1">Atur kebijakan reschedule, penalti refund pembatalan, no-show, jam operasional, dan parameter sistem.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
        @csrf

        {{-- ── CARD 1: ATURAN RESCHEDULE (JADWAL ULANG) ── --}}
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 sm:p-8 shadow-xl">
            <div class="flex items-center gap-3 pb-5 border-b border-[#2D2B26] mb-6">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/15 text-cyan-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-lg text-white">Aturan Reschedule (Jadwal Ulang)</h3>
                    <p class="text-xs text-gray-400">Batasan frekuensi dan batas waktu maksimal pengajuan reschedule oleh pelanggan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="reschedule_max_times">
                        Maksimal Reschedule per Booking
                    </label>
                    <div class="relative">
                        <input type="number" id="reschedule_max_times" name="reschedule_max_times" min="0" max="5" required
                               value="{{ $settings['reschedule_max_times'] ?? 1 }}"
                               class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E] focus:ring-1 focus:ring-[#E8451E]">
                        <span class="absolute right-4 top-3 text-xs text-gray-500">kali</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1.5">Standar: 1x kesempatan reschedule per tiket booking.</p>
                </div>

                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="reschedule_min_days">
                        Batas Minimal Reschedule (Hari Sebelum Sesi)
                    </label>
                    <div class="relative">
                        <input type="number" id="reschedule_min_days" name="reschedule_min_days" min="1" max="14" required
                               value="{{ $settings['reschedule_min_days'] ?? 2 }}"
                               class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E] focus:ring-1 focus:ring-[#E8451E]">
                        <span class="absolute right-4 top-3 text-xs text-gray-500">hari (H-{{ $settings['reschedule_min_days'] ?? 2 }})</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1.5">Contoh: Diatur 2 hari berarti minimal H-2 sebelum tanggal sesi foto.</p>
                </div>
            </div>
        </div>

        {{-- ── CARD 2: ATURAN PEMBATALAN & REFUND BERTINGKAT ── --}}
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 sm:p-8 shadow-xl">
            <div class="flex items-center gap-3 pb-5 border-b border-[#2D2B26] mb-6">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-lg text-white">Aturan Pembatalan & Kebijakan Refund</h3>
                    <p class="text-xs text-gray-400">Besaran persentase refund atau potongan penalti berdasarkan selisih hari pembatalan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Tier 1: H-3+ --}}
                <div class="p-5 rounded-2xl bg-[#141311] border border-[#2D2B26] space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-400 bg-emerald-500/15 px-2.5 py-1 rounded-lg">H-3 atau Lebih</span>
                        <span class="text-xs text-gray-500">Tier Awal</span>
                    </div>
                    <p class="text-xs text-gray-400">Pembatalan jauh hari sebelum tanggal sesi.</p>
                    <div>
                        <label class="block text-[11px] text-gray-400 mb-1" for="cancellation_h3_refund_percent">Persentase Refund</label>
                        <div class="relative">
                            <input type="number" id="cancellation_h3_refund_percent" name="cancellation_h3_refund_percent" min="0" max="100" required
                                   value="{{ $settings['cancellation_h3_refund_percent'] ?? 100 }}"
                                   class="w-full bg-[#1E1D1A] border border-[#2D2B26] rounded-xl px-3 py-2 text-white text-sm focus:border-[#E8451E]">
                            <span class="absolute right-3 top-2 text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                {{-- Tier 2: H-2 s/d H-1 --}}
                <div class="p-5 rounded-2xl bg-[#141311] border border-[#2D2B26] space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-amber-400 bg-amber-500/15 px-2.5 py-1 rounded-lg">H-2 s/d H-1</span>
                        <span class="text-xs text-gray-500">Tier Menengah</span>
                    </div>
                    <p class="text-xs text-gray-400">Pembatalan mendekati jadwal sesi foto.</p>
                    <div>
                        <label class="block text-[11px] text-gray-400 mb-1" for="cancellation_h1_refund_percent">Persentase Refund</label>
                        <div class="relative">
                            <input type="number" id="cancellation_h1_refund_percent" name="cancellation_h1_refund_percent" min="0" max="100" required
                                   value="{{ $settings['cancellation_h1_refund_percent'] ?? 50 }}"
                                   class="w-full bg-[#1E1D1A] border border-[#2D2B26] rounded-xl px-3 py-2 text-white text-sm focus:border-[#E8451E]">
                            <span class="absolute right-3 top-2 text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>

                {{-- Tier 3: Hari H --}}
                <div class="p-5 rounded-2xl bg-[#141311] border border-[#2D2B26] space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-rose-400 bg-rose-500/15 px-2.5 py-1 rounded-lg">Hari H (Hari Sesi)</span>
                        <span class="text-xs text-gray-500">Tier Terakhir</span>
                    </div>
                    <p class="text-xs text-gray-400">Pembatalan mendadak pada hari pelaksanaan.</p>
                    <div>
                        <label class="block text-[11px] text-gray-400 mb-1" for="cancellation_h0_refund_percent">Persentase Refund</label>
                        <div class="relative">
                            <input type="number" id="cancellation_h0_refund_percent" name="cancellation_h0_refund_percent" min="0" max="100" required
                                   value="{{ $settings['cancellation_h0_refund_percent'] ?? 0 }}"
                                   class="w-full bg-[#1E1D1A] border border-[#2D2B26] rounded-xl px-3 py-2 text-white text-sm focus:border-[#E8451E]">
                            <span class="absolute right-3 top-2 text-xs text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CARD 3: KEBIJAKAN NO-SHOW & PEMBAYARAN ── --}}
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 sm:p-8 shadow-xl">
            <div class="flex items-center gap-3 pb-5 border-b border-[#2D2B26] mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 text-rose-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-lg text-white">Kebijakan No-Show & Pembayaran DP</h3>
                    <p class="text-xs text-gray-400">Ketentuan apabila pelanggan tidak hadir tanpa kabar dan aturan uang muka.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="no_show_refund_percent">
                        Pengembalian Dana Kasus No-Show
                    </label>
                    <div class="relative">
                        <input type="number" id="no_show_refund_percent" name="no_show_refund_percent" min="0" max="100" required
                               value="{{ $settings['no_show_refund_percent'] ?? 0 }}"
                               class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E]">
                        <span class="absolute right-4 top-3 text-xs text-gray-500">% (0% = Hangus)</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1.5">Kebijakan standar: Uang muka (DP) 100% hangus jika pelanggan tidak hadir (no-show).</p>
                </div>

                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="dp_percent">
                        Persentase Minimal Uang Muka (DP)
                    </label>
                    <div class="relative">
                        <input type="number" id="dp_percent" name="dp_percent" min="10" max="90" required
                               value="{{ $settings['dp_percent'] ?? 50 }}"
                               class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E]">
                        <span class="absolute right-4 top-3 text-xs text-gray-500">%</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1.5">Pelunasan sisa tagihan wajib diselesaikan maksimal sebelum sesi foto dimulai.</p>
                </div>
            </div>
        </div>

        {{-- ── CARD 4: OPERASIONAL STUDIO & KONTAK ── --}}
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 sm:p-8 shadow-xl">
            <div class="flex items-center gap-3 pb-5 border-b border-[#2D2B26] mb-6">
                <div class="w-10 h-10 rounded-xl bg-purple-500/15 text-purple-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-lg text-white">Jam Operasional & Buffer Waktu</h3>
                    <p class="text-xs text-gray-400">Jadwal buka-tutup studio dan jeda steril antar sesi untuk mencegah tabrakan slot.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="open_time">Jam Buka Studio</label>
                    <input type="time" id="open_time" name="open_time" required
                           value="{{ $settings['open_time'] ?? '09:00' }}"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E]">
                </div>

                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="close_time">Jam Tutup Studio</label>
                    <input type="time" id="close_time" name="close_time" required
                           value="{{ $settings['close_time'] ?? '21:00' }}"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E]">
                </div>

                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="buffer_minutes">Jeda Buffer Antar Sesi</label>
                    <div class="relative">
                        <input type="number" id="buffer_minutes" name="buffer_minutes" min="5" max="60" required
                               value="{{ $settings['buffer_minutes'] ?? 15 }}"
                               class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E]">
                        <span class="absolute right-4 top-3 text-xs text-gray-500">menit</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="studio_address">Alamat Studio</label>
                    <input type="text" id="studio_address" name="studio_address" required
                           value="{{ $settings['studio_address'] ?? 'Jl. Tunjungan No. 88, Surabaya' }}"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E]">
                </div>

                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-300 mb-2" for="studio_phone">Nomor WhatsApp CS</label>
                    <input type="text" id="studio_phone" name="studio_phone" required
                           value="{{ $settings['studio_phone'] ?? '+62 812-3456-7890' }}"
                           class="w-full bg-[#141311] border border-[#2D2B26] rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E8451E]">
                </div>
            </div>
        </div>

        {{-- Submit button --}}
        <div class="flex items-center justify-end gap-4 pt-4">
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-[#E8451E] hover:bg-[#d03b16] text-white font-bold text-sm shadow-lg shadow-[#E8451E]/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Pengaturan Aturan Bisnis</span>
            </button>
        </div>
    </form>
</div>
@endsection
