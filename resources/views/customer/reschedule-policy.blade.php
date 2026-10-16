@extends('layouts.public')

@section('title', 'Kebijakan Reschedule, Pembatalan & Aturan Bisnis — SnapSpace Studio')
@section('meta_description', 'Kebijakan resmi reschedule maksimal 1x minimal H-2, refund pembatalan berjenjang, penalti no-show, dan tata cara pelunasan di SnapSpace Studio Surabaya.')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 space-y-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-ink-muted">
        <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <span class="text-ink font-medium">Kebijakan Reschedule & Pembatalan</span>
    </nav>

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-cream-dark shadow-card space-y-10">

        {{-- Header --}}
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-gold bg-gold/10 px-3 py-1 rounded-full">Transparansi Layanan</span>
            <h1 class="font-serif font-bold text-3xl sm:text-4xl text-ink mt-3 mb-2">Kebijakan Reschedule, Pembatalan & Aturan Studio</h1>
            <p class="text-xs text-ink-muted">Terakhir diperbarui: {{ date('d F Y') }} — Berlaku untuk seluruh pemesanan di SnapSpace Studio.</p>
        </div>

        {{-- 1. Reschedule Policy --}}
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-600 flex items-center justify-center font-bold text-sm">1</div>
                <h2 class="font-serif font-bold text-xl text-ink">Aturan Reschedule (Jadwal Ulang)</h2>
            </div>
            <div class="p-6 rounded-2xl bg-cyan-500/5 border border-cyan-500/20 text-ink space-y-3 text-sm leading-relaxed">
                <p>Pelanggan berhak mengajukan jadwal ulang (reschedule) sesi foto mandiri melalui dashboard <strong>Booking Saya</strong> dengan ketentuan:</p>
                <ul class="list-disc list-inside space-y-1.5 text-ink-muted text-xs sm:text-sm">
                    <li><strong class="text-ink">Maksimal 1 (satu) Kali:</strong> Setiap nomor booking hanya memiliki kuota 1x reschedule.</li>
                    <li><strong class="text-ink">Batas Waktu Minimal H-2:</strong> Pengajuan wajib dilakukan minimal 2 hari (H-2) sebelum tanggal sesi yang terdaftar. Pengajuan kurang dari H-2 akan otomatis terkunci oleh sistem.</li>
                    <li><strong class="text-ink">Bebas Biaya:</strong> Tidak ada biaya administrasi tambahan selama memenuhi syarat H-2.</li>
                    <li><strong class="text-ink">Ketersediaan Slot:</strong> Pemindahan jadwal bergantung pada slot ruangan yang masih kosong dan tidak bertabrakan dengan jadwal pelanggan lain.</li>
                </ul>
            </div>
        </div>

        {{-- 2. Cancellation & Refund Policy --}}
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-sm">2</div>
                <h2 class="font-serif font-bold text-xl text-ink">Aturan Pembatalan & Refund Berjenjang</h2>
            </div>
            <p class="text-xs text-ink-muted leading-relaxed">
                Pengembalian dana (refund) untuk pembatalan dihitung otomatis oleh sistem berdasarkan selisih hari pengajuan pembatalan dengan tanggal sesi foto:
            </p>

            <div class="overflow-hidden rounded-2xl border border-cream-dark">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-cream border-b border-cream-dark text-ink font-bold">
                        <tr>
                            <th class="p-4">Waktu Pembatalan</th>
                            <th class="p-4">Persentase Refund</th>
                            <th class="p-4">Penalti / Potongan</th>
                            <th class="p-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark">
                        <tr class="bg-white">
                            <td class="p-4 font-semibold text-emerald-600">H-3 atau Lebih</td>
                            <td class="p-4 font-bold text-emerald-600">100%</td>
                            <td class="p-4 text-ink-muted">0%</td>
                            <td class="p-4 text-xs text-ink-muted">Dana dikembalikan utuh (potongan admin gateway bank Rp 0).</td>
                        </tr>
                        <tr class="bg-cream/40">
                            <td class="p-4 font-semibold text-amber-600">H-2 hingga H-1</td>
                            <td class="p-4 font-bold text-amber-600">50%</td>
                            <td class="p-4 text-ink-muted">50%</td>
                            <td class="p-4 text-xs text-ink-muted">50% dana dikembalikan, 50% kompensasi slot yang tertahan.</td>
                        </tr>
                        <tr class="bg-white">
                            <td class="p-4 font-semibold text-rose-600">Hari H (Hari Sesi)</td>
                            <td class="p-4 font-bold text-rose-600">0% (Hangus)</td>
                            <td class="p-4 text-ink-muted">100%</td>
                            <td class="p-4 text-xs text-ink-muted">Pembatalan mendadak tidak mendapatkan pengembalian dana.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-ink-muted italic">* Proses transfer dana refund membutuhkan waktu 1–3 hari kerja ke rekening bank pemesan.</p>
        </div>

        {{-- 3. No-Show Policy --}}
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-sm">3</div>
                <h2 class="font-serif font-bold text-xl text-ink">Kebijakan No-Show (Tidak Hadir)</h2>
            </div>
            <div class="p-6 rounded-2xl bg-rose-500/5 border border-rose-500/20 text-ink space-y-2 text-sm leading-relaxed">
                <p class="font-semibold text-rose-700">Uang Muka (DP) & Pembayaran 100% Hangus</p>
                <p class="text-xs sm:text-sm text-ink-muted">
                    Jika pelanggan tidak hadir pada jam sesi yang telah dijadwalkan tanpa konfirmasi resmi sebelum sesi dimulai, status booking akan ditandai sebagai <strong>No-Show</strong>. Seluruh pembayaran (baik DP 50% maupun Lunas) tidak dapat di-refund dan tidak dapat dijadwalkan ulang.
                </p>
            </div>
        </div>

        {{-- 4. Settlement & Pelunasan --}}
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-teal/10 text-teal flex items-center justify-center font-bold text-sm">4</div>
                <h2 class="font-serif font-bold text-xl text-ink">Pelunasan Pembayaran (Bagi Opsi DP 50%)</h2>
            </div>
            <div class="p-6 rounded-2xl bg-teal/5 border border-teal/20 text-ink space-y-2 text-sm leading-relaxed">
                <p class="font-semibold text-teal">Pelunasan Maksimal Sebelum Sesi Foto Dimulai</p>
                <p class="text-xs sm:text-sm text-ink-muted">
                    Bagi pelanggan yang memilih opsi DP 50%, sisa pembayaran dapat dilunasi secara online melalui tautan pelunasan di e-ticket booking, atau dibayar langsung secara tunai/QRIS/EDC kepada staff resepsionis sebelum memasuki ruangan foto.
                </p>
            </div>
        </div>

        {{-- 5. Anti-Double Booking Guarantee --}}
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gold/10 text-gold flex items-center justify-center font-bold text-sm">5</div>
                <h2 class="font-serif font-bold text-xl text-ink">Jaminan Bebas Double Booking</h2>
            </div>
            <div class="p-6 rounded-2xl bg-gold/5 border border-gold/20 text-ink space-y-2 text-sm leading-relaxed">
                <p class="font-semibold text-gold">Sistem Penguncian Real-Time & Jeda Sterilisasi</p>
                <p class="text-xs sm:text-sm text-ink-muted">
                    Sistem booking SnapSpace menerapkan penguncian database unik real-time sehingga satu slot jam hanya bisa ditempati oleh satu pemesan. Setiap sesi juga dilengkapi <strong>jeda buffer 15 menit</strong> untuk pembersihan ruangan, sterilisasi properti, dan persiapan tim.
                </p>
            </div>
        </div>

        {{-- Callout Contact --}}
        <div class="pt-6 border-t border-cream-dark flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-ink">Ada pertanyaan seputar jadwal atau kendala darurat?</p>
                <p class="text-xs text-ink-muted">Customer Service kami siap membantu Anda setiap hari pukul 09.00 – 21.00 WIB.</p>
            </div>
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
               class="px-5 py-3 rounded-xl bg-success text-white font-semibold text-xs flex items-center gap-2 hover:bg-success/90 transition-colors shrink-0">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Hubungi WhatsApp CS</span>
            </a>
        </div>

    </div>
</div>
@endsection
