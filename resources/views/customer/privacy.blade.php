@extends('layouts.public')

@section('title', 'Kebijakan Privasi — SnapSpace Studio Klaten')
@section('meta_description', 'Kebijakan Privasi resmi SnapSpace Studio Klaten sesuai Undang-Undang Perlindungan Data Pribadi (UU PDP No. 27 Tahun 2022). Ketentuan retensi data, keamanan pembayaran Midtrans, dan hak subjek data.')

@section('content')
<div class="bg-cream-warm py-12 border-b border-cream-dark">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Kebijakan Privasi</span>
        </nav>
        <h1 class="font-serif font-bold text-3xl sm:text-4xl text-ink mb-2">Kebijakan Privasi</h1>
        <p class="text-xs sm:text-sm text-ink-muted">Terakhir diperbarui: {{ date('d F Y') }} · Kepatuhan UU PDP No. 27 Tahun 2022</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-cream-dark shadow-card space-y-10 text-ink leading-relaxed">

        {{-- Pendahuluan --}}
        <section class="space-y-3">
            <p class="text-sm sm:text-base text-ink-soft">
                SnapSpace Studio Klaten (selanjutnya disebut <strong>"SnapSpace"</strong>, <strong>"Kami"</strong>) berkomitmen penuh untuk melindungi, menghormati, dan menjamin hak privasi seluruh pelanggan dan pengguna layanan kami. Kebijakan Privasi ini disusun berlandaskan <strong>Undang-Undang Republik Indonesia Nomor 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP)</strong> serta standar keamanan informasi digital internasional.
            </p>
            <p class="text-sm sm:text-base text-ink-soft">
                Dengan mendaftar akun, melakukan reservasi (booking) sesi studio, atau mengakses situs web <code>snapspace.web.id</code>, Anda menyatakan telah membaca, memahami, dan menyetujui pemrosesan data pribadi Anda sesuai ketentuan di bawah ini.
            </p>
        </section>

        {{-- 1. Data yang Dikumpulkan --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">1</span>
                Data Pribadi yang Kami Kumpulkan
            </h2>
            <p class="text-sm text-ink-muted">Kami hanya mengumpulkan data pribadi yang relevan, proporsional, dan diperlukan untuk penyelenggaraan layanan studio foto:</p>
            <ul class="space-y-2 text-sm text-ink-soft list-disc pl-5">
                <li><strong>Identitas Diri & Kontak:</strong> Nama lengkap, alamat email, dan nomor telepon/WhatsApp aktif.</li>
                <li><strong>Data Reservasi & Booking:</strong> Tanggal dan jam sesi, studio yang dipilih, paket foto, pilihan add-on, jumlah orang dalam sesi, catatan permintaan khusus, dan kode tiket booking unik.</li>
                <li><strong>Data Transaksi Keuangan:</strong> Metode pembayaran yang dipilih, status settlement, tanggal transaksi, dan bukti faktur pembayaran resmi (kami <em>tidak</em> menyimpan data sensitif kartu perbankan).</li>
                <li><strong>Foto Digital Hasil Sesi:</strong> File softcopy foto (resolusi tinggi dan hasil edit) yang diunggah ke server galeri digital pribadi Anda.</li>
                <li><strong>Data Teknis & Akses:</strong> Alamat IP, user-agent peramban, cookie sesi, dan catatan kepatuhan persetujuan privasi (timestamp consent).</li>
            </ul>
        </section>

        {{-- 2. Pemrosesan Data Pembayaran --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">2</span>
                Pemrosesan & Keamanan Data Pembayaran
            </h2>
            <div class="p-5 rounded-2xl bg-cream border border-cream-dark space-y-3">
                <div class="flex items-center gap-2 text-success font-bold text-xs uppercase tracking-wide">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Tersertifikasi PCI-DSS Level 1 & Berlisensi Bank Indonesia</span>
                </div>
                <p class="text-sm text-ink-soft">
                    Seluruh transaksi online (QRIS, GoPay, OVO, ShopeePay, DANA, Virtual Account Bank BCA/BNI/BRI/Mandiri, dan Kartu Debit/Kredit) diproses secara langsung melalui payment gateway resmi rekanan kami, <strong>PT Midtrans (GoTo Financial)</strong>.
                </p>
                <p class="text-sm text-ink-soft">
                    <strong>Penting:</strong> Server SnapSpace <strong>tidak pernah mencatat, memproses, atau menyimpan</strong> nomor kartu kredit/debit lengkap, tanggal kedaluwarsa kartu, maupun kode verifikasi CVV/CVC Anda. Seluruh komunikasi data pembayaran dienkripsi penuh menggunakan protokol TLS 1.3 dengan standar enkripsi 256-bit langsung ke infrastruktur perbankan Midtrans.
                </p>
            </div>
        </section>

        {{-- 3. Masa Retensi Data --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">3</span>
                Masa Retensi & Penyimpanan Data
            </h2>
            <p class="text-sm text-ink-muted">Kami menerapkan jadwal retensi data yang tegas untuk menghindari penyimpanan informasi tanpa batas waktu:</p>
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm text-left border border-cream-dark rounded-xl overflow-hidden">
                    <thead class="bg-cream text-ink font-bold border-b border-cream-dark">
                        <tr>
                            <th class="p-3">Kategori Data</th>
                            <th class="p-3">Masa Retensi</th>
                            <th class="p-3">Tindakan Pasca Retensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-ink-soft">
                        <tr>
                            <td class="p-3 font-semibold text-ink">Foto Digital Sesi</td>
                            <td class="p-3"><strong>30 (tiga puluh) hari kalender</strong> sejak sesi foto selesai</td>
                            <td class="p-3 text-danger">Dihapus permanen otomatis dari server penyimpanan cloud.</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-ink">Data Akun & Profil</td>
                            <td class="p-3">Sepanjang akun berstatus aktif oleh pengguna</td>
                            <td class="p-3">Dihapus saat pengguna melakukan penutupan/penghapusan akun.</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-ink">Catatan Transaksi & Invoice</td>
                            <td class="p-3"><strong>5 (lima) tahun</strong> (Kewajiban regulasi perpajakan RI)</td>
                            <td class="p-3">Dianonimkan atau diarsipkan secara terenkripsi dingin (cold storage).</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-ink">Log Keamanan & Sesi</td>
                            <td class="p-3">Maksimal 12 (dua belas) bulan</td>
                            <td class="p-3">Dibersihkan secara berkala melalui cron job sistem.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 4. Pihak Ketiga Pemroses Data --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">4</span>
                Pihak Ketiga Pemroses Data (Sub-processors)
            </h2>
            <p class="text-sm text-ink-soft">
                Kami <strong>tidak pernah dan tidak akan pernah menjual atau menyewakan</strong> data pribadi pelanggan kepada pihak pengiklan mana pun. Untuk menjalankan operasional, kami hanya bermitra dengan pemroses data pihak ketiga tepercaya:
            </p>
            <ul class="space-y-2 text-sm text-ink-soft list-disc pl-5">
                <li><strong>Payment Gateway:</strong> PT Midtrans (GoTo Financial) — Pemrosesan verifikasi settlement transaksi keuangan.</li>
                <li><strong>Penyedia Infrastruktur Cloud:</strong> Server VPS dengan perlindungan firewall ketat berlokasi di node Asia Tenggara (Singapura/Indonesia) dengan sertifikasi ISO 27001.</li>
                <li><strong>Penyedia Pesan Notifikasi:</strong> Layanan WhatsApp Business API / Meta Platforms untuk pengiriman tiket booking QR, kode OTP, dan notifikasi pengingat sesi.</li>
            </ul>
        </section>

        {{-- 5. Hak Pengguna & Mekanisme Penghapusan --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">5</span>
                Hak Subjek Data & Mekanisme Penghapusan (UU PDP)
            </h2>
            <p class="text-sm text-ink-soft">Berdasarkan UU PDP No. 27 Tahun 2022, Anda memiliki hak-hak fundamental berikut yang dapat diakses secara mandiri:</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-cream border border-cream-dark space-y-2">
                    <p class="font-bold text-xs uppercase tracking-wider text-gold flex items-center gap-1.5">
                        <i class="fa-solid fa-file-export"></i>
                        <span>Hak Portabilitas Data</span>
                    </p>
                    <p class="text-xs text-ink-muted leading-relaxed">
                        Anda berhak memperoleh salinan seluruh data pribadi, riwayat booking, dan log transaksi Anda dalam format terstruktur (JSON). Fitur ini tersedia langsung di tombol <strong>Ekspor Data</strong> pada Dashboard Profil.
                    </p>
                </div>
                <div class="p-4 rounded-2xl bg-cream border border-cream-dark space-y-2">
                    <p class="font-bold text-xs uppercase tracking-wider text-danger flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Hak Dilupakan (Right to Erasure)</span>
                    </p>
                    <p class="text-xs text-ink-muted leading-relaxed">
                        Anda dapat menghapus akun dan data pribadi secara instan melalui fitur <strong>Hapus Akun</strong> di Dashboard Profil, atau melalui permohonan tertulis ke DPO kami.
                    </p>
                </div>
            </div>
            <p class="text-xs text-ink-muted">
                Untuk permohonan koreksi atau akses data manual, hubungi Petugas Perlindungan Data kami via email ke <a href="mailto:privacy@snapspace.web.id" class="text-gold underline">privacy@snapspace.web.id</a>. Permohonan Anda akan ditindaklanjuti dalam kurun waktu maksimal 3x24 jam kerja.
            </p>
        </section>

        {{-- 6. Hak Cipta & Privasi Foto --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">6</span>
                Hak Cipta & Kerahasiaan Foto Pelanggan
            </h2>
            <div class="p-5 rounded-2xl bg-teal-50 border border-teal/20 space-y-2">
                <p class="font-bold text-sm text-teal flex items-center gap-2">
                    <i class="fa-solid fa-lock"></i>
                    <span>Kebijakan Privasi Visual 100% Opt-In</span>
                </p>
                <p class="text-xs sm:text-sm text-ink-soft">
                    Seluruh file foto yang dihasilkan selama sesi foto adalah <strong>hak milik penuh pelanggan</strong>. SnapSpace memegang prinsip privasi ketat: kami <strong>TIDAK AKAN</strong> mempublikasikan, menampilkan, atau menggunakan foto sesi Anda sebagai portofolio publik, katalog situs web, maupun konten media sosial tanpa persetujuan tertulis eksplisit (opt-in) dari Anda terlebih dahulu.
                </p>
            </div>
        </section>

        {{-- 7. Penanganan Insiden Kebocoran Data --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">7</span>
                Protokol Penanganan Insiden Kebocoran Data (Data Breach)
            </h2>
            <p class="text-sm text-ink-soft">
                SnapSpace menerapkan arsitektur pertahanan berlapis (defense-in-depth). Namun, jika terjadi kegagalan perlindungan atau insiden kebocoran data pribadi yang terkonfirmasi, kami berkomitmen untuk:
            </p>
            <ol class="space-y-2 text-sm text-ink-soft list-decimal pl-5">
                <li>Melakukan mitigasi dan penutupan celah keamanan dalam kurun waktu kurang dari 24 jam sejak insiden terdeteksi.</li>
                <li>Menyampaikan pemberitahuan tertulis resmi kepada pelanggan yang terdampak paling lambat <strong>3 x 24 jam (72 jam)</strong> melalui email dan WhatsApp terdaftar.</li>
                <li>Melaporkan insiden secara resmi kepada Lembaga Perlindungan Data Pribadi Republik Indonesia sesuai amanat Pasal 46 UU PDP No. 27/2022.</li>
                <li>Menyediakan saluran bantuan khusus untuk membimbing pengguna dalam mengamankan kredensial mereka.</li>
            </ol>
        </section>

        {{-- 8. Kontak Petugas Privasi --}}
        <section class="space-y-4 pt-6 border-t border-cream-dark">
            <h2 class="font-serif font-bold text-xl sm:text-2xl text-ink flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-sm font-sans font-bold">8</span>
                Kontak & Petugas Perlindungan Data (DPO)
            </h2>
            <p class="text-sm text-ink-soft">
                Apabila Anda memiliki pertanyaan, keberatan, atau ingin menggunakan hak-hak hukum Anda terkait perlindungan data pribadi, silakan hubungi kami melalui saluran resmi:
            </p>
            <div class="p-5 rounded-2xl bg-cream border border-cream-dark text-xs sm:text-sm space-y-1.5 text-ink-soft">
                <p><strong>SnapSpace Studio Klaten — Data Protection Office</strong></p>
                <p>Alamat: Waru, RT 17 / RW 07, Pandanan, Wonosari, Klaten, Jawa Tengah 57463</p>
                <p>Email Privasi: <a href="mailto:privacy@snapspace.web.id" class="text-gold font-medium hover:underline">privacy@snapspace.web.id</a> / <a href="mailto:hello@snapspace.id" class="text-gold font-medium hover:underline">hello@snapspace.id</a></p>
                <p>WhatsApp Layanan: <a href="https://wa.me/6287825688608" target="_blank" rel="noopener" class="text-gold font-medium hover:underline">+62 878-2568-8608</a></p>
                <p>Jam Kerja CS: Setiap Hari 09:00 – 21:00 WIB</p>
            </div>
        </section>

    </div>
</div>
@endsection
