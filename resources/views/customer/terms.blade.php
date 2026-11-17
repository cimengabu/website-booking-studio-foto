@extends('layouts.public')

@section('title', 'Syarat & Ketentuan — SnapSpace Studio Klaten')
@section('meta_description', 'Syarat dan Ketentuan resmi penggunaan layanan studio foto mandiri SnapSpace Studio Klaten. Aturan booking, pembayaran, kebijakan refund pembatalan, toleransi keterlambatan, dan penyelesaian sengketa.')

@section('content')
<div class="bg-cream-warm py-12 border-b border-cream-dark">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Syarat & Ketentuan</span>
        </nav>
        <h1 class="font-serif font-bold text-3xl sm:text-4xl text-ink mb-2">Syarat & Ketentuan Layanan</h1>
        <p class="text-xs sm:text-sm text-ink-muted">Terakhir diperbarui: {{ date('d F Y') }} · Berlaku untuk seluruh layanan SnapSpace Studio</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-cream-dark shadow-card space-y-10 text-ink leading-relaxed">

        {{-- Mukadimah --}}
        <section class="space-y-3">
            <p class="text-sm sm:text-base text-ink-soft">
                Selamat datang di <strong>SnapSpace Studio Klaten</strong>. Dokumen Syarat dan Ketentuan ini merupakan perjanjian hukum yang mengikat antara Anda sebagai Pengguna/Pelanggan dan Manajemen SnapSpace Studio Klaten. Dengan melakukan reservasi, membayar biaya sesi, atau menggunakan fasilitas studio foto kami, Anda menyetujui seluruh klausul di bawah ini secara sadar dan tanpa paksaan.
            </p>
        </section>

        {{-- 1. Ketentuan Pemesanan & Pembayaran --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">1</span>
                Pemesanan (Booking) & Pembayaran
            </h2>
            <ul class="space-y-2 text-sm text-ink-soft list-disc pl-5">
                <li><strong>Reservasi Slot Real-Time:</strong> Pemesanan jadwal dilakukan mandiri melalui sistem situs web <code>snapspace.web.id</code>. Slot yang telah Anda pilih dikunci secara sementara selama masa pembayaran.</li>
                <li><strong>Opsi Pembayaran:</strong> Pelanggan dapat memilih opsi pembayaran <strong>Lunas (100%)</strong> atau <strong>Uang Muka / Down Payment (50%)</strong> saat checkout.</li>
                <li><strong>Batas Waktu Pembayaran (Expiry Timer):</strong> Setiap kode pembayaran booking memiliki masa berlaku <strong>60 (enam puluh) menit</strong>. Apabila pembayaran tidak diselesaikan dalam kurun waktu tersebut, sistem secara otomatis membatalkan reservasi dan melepas slot kembali ke publik.</li>
                <li><strong>Pelunasan Uang Muka:</strong> Bagi pelanggan yang memilih opsi DP 50%, sisa pembayaran wajib diselesaikan selambat-lambatnya sebelum sesi foto dimulai di meja kasir studio (melalui QRIS, Debit, atau Tunai).</li>
            </ul>
        </section>

        {{-- 2. Kebijakan Pembatalan & Refund --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">2</span>
                Mekanisme Reschedule & Pengembalian Dana (Refund)
            </h2>
            <p class="text-sm text-ink-soft">Kami memahami rencana Anda dapat berubah. Oleh karena itu, kami memberlakukan aturan fleksibel berkeadilan:</p>
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm text-left border border-cream-dark rounded-xl overflow-hidden">
                    <thead class="bg-cream text-ink font-bold border-b border-cream-dark">
                        <tr>
                            <th class="p-3">Waktu Pengajuan</th>
                            <th class="p-3">Ketentuan Reschedule</th>
                            <th class="p-3">Ketentuan Pembatalan (Refund)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-ink-soft">
                        <tr>
                            <td class="p-3 font-semibold text-ink">H-3 (≥ 72 jam sebelum sesi)</td>
                            <td class="p-3 text-success">Gratis 1x Reschedule jadwal</td>
                            <td class="p-3 text-success">Pengembalian dana 100% penuh</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-ink">H-2 s/d H-1 (24–71 jam sebelum sesi)</td>
                            <td class="p-3 text-success">Gratis 1x Reschedule jadwal</td>
                            <td class="p-3 text-amber-700">Pengembalian dana 50% dari total nilai paket</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-ink">&lt; 24 jam atau Hari H (No-Show)</td>
                            <td class="p-3 text-danger">Tidak dapat reschedule</td>
                            <td class="p-3 text-danger">Dana hangus (Non-refundable)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-ink-muted">
                Catatan: Pengembalian dana (refund) yang disetujui akan diproses kembali ke rekening bank atau dompet digital pelanggan dalam waktu 2–5 hari kerja operasional perbankan.
            </p>
        </section>

        {{-- 3. Toleransi Keterlambatan --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">3</span>
                Kehadiran & Toleransi Keterlambatan
            </h2>
            <ul class="space-y-2 text-sm text-ink-soft list-disc pl-5">
                <li><strong>Waktu Kedatangan:</strong> Pelanggan wajib hadir di studio sekurang-kurangnya <strong>10–15 menit sebelum waktu sesi</strong> yang tercantum pada tiket untuk persiapan kostum, touch-up make-up, dan konfirmasi tiket QR.</li>
                <li><strong>Toleransi Keterlambatan:</strong> Kami memberikan toleransi keterlambatan maksimal <strong>10 (sepuluh) menit</strong>.</li>
                <li><strong>Konsekuensi Keterlambatan:</strong> Apabila pelanggan hadir terlambat, durasi sesi foto yang terbuang tidak dapat diganti atau diperpanjang, dan sesi tetap harus berakhir sesuai jadwal semula. Hal ini diterapkan demi melindungi hak slot pelanggan berikutnya.</li>
            </ul>
        </section>

        {{-- 4. Tata Tertib & Kerusakan Fasilitas --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">4</span>
                Tata Tertib Studio & Ganti Rugi Kerusakan
            </h2>
            <ul class="space-y-2 text-sm text-ink-soft list-disc pl-5">
                <li><strong>Larangan di Area Studio:</strong> Dilarang keras merokok/vaping, membawa zat terlarang/senjata, membawa makanan berkuah/berminyak, atau minuman berwarna ke area latar (backdrop) foto.</li>
                <li><strong>Perlakuan terhadap Alat:</strong> Pelanggan dilarang membongkar, menggeser tripod kamera utama, atau mengubah settingan teknis lighting tanpa bantuan dan izin dari staf SnapSpace.</li>
                <li><strong>Tanggung Jawab Kerusakan:</strong> Pelanggan bertanggung jawab penuh atas tindakan diri sendiri dan seluruh anggota rombongan selama di studio. Apabila terjadi kerusakan fasilitas studio (kamera, lampu Godox, monitor, backdrop sobek/kotor berat akibat tumpahan, properti pecah) yang disebabkan oleh kelalaian berat atau kesengajaan, pelanggan wajib memberikan ganti rugi perbaikan atau penggantian wajar sesuai harga pasar alat tersebut.</li>
            </ul>
        </section>

        {{-- 5. Gangguan Sistem & Keadaan Kahar --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">5</span>
                Gangguan Sistem & Keadaan Kahar (Force Majeure)
            </h2>
            <p class="text-sm text-ink-soft">
                Dalam hal terjadinya gangguan teknis internal pada studio kami (seperti kerusakan kamera mendadak, kerusakan lighting utama) atau keadaan kahar / Force Majeure (pemadaman listrik berkepanjangan tanpa genset, gempa bumi, banjir, kebakaran, atau regulasi darurat pemerintah):
            </p>
            <div class="p-4 rounded-2xl bg-cream border border-cream-dark text-xs sm:text-sm space-y-2 text-ink-soft">
                <p>Pelanggan berhak atas salah satu pilihan kompensasi penuh:</p>
                <ol class="list-decimal pl-5 space-y-1">
                    <li><strong>Penjadwalan Ulang (Reschedule) Prioritas</strong> ke hari dan jam pilihan pelanggan kapan saja tanpa biaya tambahan, ATAU</li>
                    <li><strong>Pengembalian Dana Penuh 100% (Full Refund)</strong> tanpa dikenakan potongan administrasi apa pun dalam kurun waktu 1x24 jam kerja.</li>
                </ol>
            </div>
        </section>

        {{-- 6. Kewajiban & Tanggung Jawab Pengelola --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">6</span>
                Kewajiban & Tanggung Jawab Pengelola SnapSpace
            </h2>
            <ul class="space-y-2 text-sm text-ink-soft list-disc pl-5">
                <li>Menyediakan ruangan studio yang bersih, berpendingin udara (AC), privat (tertutup 100% tanpa canggung), serta peralatan fotografi yang terkalibrasi dan siap pakai.</li>
                <li>Menjamin ketersediaan staf ramah yang siap memandu tata cara penggunaan remote shutter dan pemilihan tema sebelum sesi dimulai.</li>
                <li>Menyediakan tautan galeri digital beresolusi tinggi maksimal <strong>24 jam</strong> setelah sesi foto selesai. Tautan galeri dapat diakses dan diunduh selama <strong>30 (tiga puluh) hari kalender</strong>.</li>
                <li>Menjaga kerahasiaan seluruh file foto pelanggan dari akses pihak yang tidak berhak.</li>
            </ul>
        </section>

        {{-- 7. Hak Cipta & Penggunaan Foto --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">7</span>
                Hak Cipta & Penggunaan Foto untuk Materi Promosi
            </h2>
            <div class="p-5 rounded-2xl bg-teal-50 border border-teal/20 space-y-2">
                <p class="font-bold text-sm text-teal flex items-center gap-2">
                    <i class="fa-solid fa-camera"></i>
                    <span>Hak Milik Mutlak pada Pelanggan</span>
                </p>
                <p class="text-xs sm:text-sm text-ink-soft">
                    Semua foto hasil sesi studio adalah milik eksklusif pelanggan yang bersangkutan. SnapSpace <strong>TIDAK PERNAH</strong> mengklaim hak publikasi atas foto Anda. SnapSpace hanya diperkenankan menggunakan foto hasil sesi sebagai materi promosi (seperti postingan Instagram atau portofolio situs) <strong>hanya jika telah memperoleh izin tertulis atau persetujuan opt-in yang tegas dan sukarela</strong> dari pelanggan.
                </p>
            </div>
        </section>

        {{-- 8. Pembatasan Tanggung Jawab --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">8</span>
                Barang Pribadi & Batas Tanggung Jawab
            </h2>
            <p class="text-sm text-ink-soft">
                Pelanggan wajib menjaga barang-barang berharga pribadi (dompet, ponsel, perhiasan, pakaian ganti) selama berada di area studio. SnapSpace tidak bertanggung jawab atas segala bentuk kehilangan, kerusakan, atau tertukarnya barang bawaan pribadi pelanggan yang tertinggal di area studio.
            </p>
        </section>

        {{-- 9. Penyelesaian Sengketa & Yurisdiksi Hukum --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">9</span>
                Penyelesaian Sengketa & Hukum yang Berlaku
            </h2>
            <p class="text-sm text-ink-soft">
                Syarat dan Ketentuan ini diatur dan ditafsirkan berdasarkan hukum Negara Kesatuan Republik Indonesia. Apabila terjadi perbedaan penafsiran atau perselisihan yang timbul dari pelaksanaan layanan ini:
            </p>
            <ol class="space-y-2 text-sm text-ink-soft list-decimal pl-5">
                <li>Para pihak sepakat untuk mengutamakan penyelesaian secara damai melalui <strong>musyawarah untuk mufakat</strong>.</li>
                <li>Apabila musyawarah tidak mencapai mufakat dalam waktu 30 (tiga puluh) hari kalender, maka perselisihan tersebut akan diselesaikan melalui domisili hukum yang disepakati, yaitu <strong>Pengadilan Negeri Klaten</strong>, Jawa Tengah.</li>
            </ol>
        </section>

        {{-- Bantuan Layanan --}}
        <section class="pt-6 border-t border-cream-dark">
            <div class="p-5 rounded-2xl bg-cream border border-cream-dark flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="font-bold text-sm text-ink">Ada Pertanyaan Mengenai Ketentuan Ini?</p>
                    <p class="text-xs text-ink-muted">Tim layanan pelanggan kami siap membantu Anda setiap hari pukul 09:00 – 21:00 WIB.</p>
                </div>
                <a href="https://wa.me/6287825688608" target="_blank" rel="noopener"
                   class="px-5 py-2.5 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-xs shadow-warm transition-all flex items-center gap-2 shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Hubungi CS WhatsApp</span>
                </a>
            </div>
        </section>

    </div>
</div>
@endsection
