@extends('layouts.public')

@section('title', 'FAQ — Pertanyaan yang Sering Diajukan | SnapSpace Studio')
@section('meta_description', 'Temukan jawaban atas pertanyaan umum seputar booking studio foto, paket, pembayaran, reschedule, dan layanan SnapSpace Studio Surabaya.')

@section('content')

{{-- ── PAGE HEADER ── --}}
<section class="bg-cream-warm py-20 border-b border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-8">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">FAQ</span>
        </nav>
        <div class="max-w-2xl">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Pusat Bantuan</p>
            <h1 class="font-serif font-bold text-4xl sm:text-5xl text-ink mb-4">Pertanyaan yang Sering Diajukan</h1>
            <p class="text-ink-muted text-lg">Tidak menemukan jawaban yang dicari? <a href="{{ Route::has('contact') ? route('contact') : '#' }}" class="text-gold hover:underline">Hubungi kami langsung</a>.</p>
        </div>
    </div>
</section>

{{-- ── FAQ CONTENT ── --}}
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $faqs = [
                'Sebelum Booking' => [
                    ['Apakah saya perlu membawa fotografer sendiri?', 'Tidak perlu sama sekali! Studio kami dilengkapi dengan kamera DSLR/Mirrorless full-frame yang terpasang di tripod, plus remote shutter wireless yang bisa Anda genggam. Anda cukup atur angle, pose, dan tekan tombol — hasilnya langsung terlihat di kamera.'],
                    ['Apakah harus mendaftar/buat akun untuk booking?', 'Untuk proses booking Anda perlu login atau mendaftar terlebih dahulu, namun proses registrasinya sangat cepat (kurang dari 1 menit). Akun dibutuhkan agar Anda bisa melihat status booking, mengunduh hasil foto, dan mengakses riwayat sesi.'],
                    ['Berapa orang yang bisa ikut dalam satu sesi?', 'Tergantung paket yang dipilih: Basic (1–2 orang), Standard (1–4 orang), Premium (hingga 6 orang). Untuk grup lebih besar atau acara korporat, silakan hubungi kami terlebih dahulu.'],
                    ['Apakah ada batas minimum usia?', 'Tidak ada batas minimum usia. Namun, untuk peserta di bawah 12 tahun wajib didampingi orang tua/wali yang bertanggung jawab sepanjang sesi berlangsung.'],
                ],
                'Booking & Pembayaran' => [
                    ['Metode pembayaran apa saja yang diterima?', 'Kami menerima: QRIS, GoPay, OVO, DANA, ShopeePay, Transfer Bank (BCA, BNI, BRI, Mandiri via Virtual Account), dan Kartu Kredit/Debit Visa/Mastercard. Semua pembayaran diproses melalui Midtrans — payment gateway yang aman dan berlisensi Bank Indonesia.'],
                    ['Apakah bisa bayar DP (uang muka) dulu?', 'Ya! Kami menyediakan opsi DP 50% dari total harga. Sisa pembayaran bisa dilunasi minimal H-1 sebelum sesi atau saat datang di studio (cash/transfer).'],
                    ['Berapa lama batas waktu pembayaran setelah booking?', 'Setelah membuat booking, Anda punya waktu 60 menit untuk menyelesaikan pembayaran. Jika melewati batas waktu, slot akan otomatis dilepas dan tersedia untuk pelanggan lain.'],
                    ['Apakah ada promo atau diskon khusus?', 'Ya! Kami sering mengadakan promo midweek (Selasa-Rabu), promo weekend pagi, dan promo seasonal (liburan, hari Valentine, dll.). Ikuti Instagram @snapspace.id untuk info promo terbaru. Anda juga bisa memasukkan kode promo saat checkout.'],
                ],
                'Saat & Setelah Sesi' => [
                    ['Apa yang perlu dibawa saat datang ke studio?', 'Cukup bawa: (1) Tiket QR dari email/akun SnapSpace, (2) KTP/identitas untuk konfirmasi, (3) Outfit yang ingin dipakai foto, (4) Properti tambahan jika ada yang tidak tersedia di studio.'],
                    ['Berapa lama foto hasil editing siap diterima?', 'Foto yang sudah melalui proses seleksi dan editing profesional akan dikirimkan ke galeri online akun Anda dalam maksimal 24 jam setelah sesi selesai. File asli (non-edited) tersedia segera setelah sesi untuk diunduh.'],
                    ['Format file apa yang dikirimkan? Apakah ada batas unduhan?', 'File dikirimkan dalam format JPEG resolusi tinggi (minimal 3000px sisi panjang). Link galeri aktif selama 30 hari. Setelah itu, file tidak bisa diakses kembali, jadi pastikan sudah mengunduh sebelum link expired.'],
                    ['Apakah saya boleh minta ulang edit foto?', 'Revisi minor (koreksi warna/exposure) bisa diminta gratis dalam 3 hari setelah foto diterima. Revisi mayor atau tambahan foto edit di luar paket dikenakan biaya tambahan sesuai tarif add-on.'],
                ],
                'Reschedule & Pembatalan' => [
                    ['Bagaimana prosedur reschedule?', 'Reschedule dapat dilakukan melalui halaman "Booking Saya" di akun Anda. Pilih booking yang ingin diubah, klik "Reschedule", lalu pilih slot baru. Reschedule gratis jika dilakukan minimal H-24 jam sebelum jadwal sesi.'],
                    ['Bagaimana kebijakan pembatalan dan refund?', 'Pembatalan H-24 jam atau lebih: Refund 100% (dikurangi biaya payment gateway 2%). Pembatalan kurang dari H-24 jam: Tidak ada refund, namun bisa dikonversi menjadi kredit studio berlaku 90 hari. Pembatalan tanpa pemberitahuan (no-show): Tidak ada refund.'],
                    ['Bagaimana jika studio bermasalah teknis di hari sesi?', 'Jika gangguan teknis (kamera rusak, listrik mati, dll.) terjadi karena kelalaian kami, Anda berhak mendapatkan: (1) Reschedule gratis ke jadwal lain, ATAU (2) Refund penuh 100%. Kami menjamin hal ini tanpa pertanyaan.'],
                ],
            ];
        @endphp

        <div class="space-y-12" x-data="{ openFaq: null }">
            @foreach($faqs as $category => $questions)
                <div class="reveal">
                    <h2 class="font-serif font-semibold text-xl text-ink border-b border-cream-dark pb-4 mb-5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gold/15 flex items-center justify-center">
                            <i class="fa-solid fa-{{ $loop->index === 0 ? 'circle-question' : ($loop->index === 1 ? 'credit-card' : ($loop->index === 2 ? 'camera' : 'rotate-left')) }} text-gold text-xs"></i>
                        </div>
                        {{ $category }}
                    </h2>

                    <div class="space-y-2.5">
                        @foreach($questions as $qidx => [$question, $answer])
                            @php $faqId = $loop->parent->index . '_' . $qidx; @endphp
                            <div class="bg-cream rounded-2xl border border-cream-dark overflow-hidden"
                                 x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="w-full flex items-center justify-between gap-4 px-6 py-4 text-left hover:bg-cream-warm transition-colors"
                                        :aria-expanded="open"
                                        id="faq-btn-{{ $faqId }}">
                                    <span class="font-semibold text-sm text-ink leading-snug">{{ $question }}</span>
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-white border border-cream-dark flex items-center justify-center transition-all duration-200"
                                         :class="open ? 'rotate-180 border-gold bg-gold/10' : ''">
                                        <i class="fa-solid fa-chevron-down text-xs text-ink-muted" :class="open ? 'text-gold' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="px-6 pb-5">
                                    <div class="border-t border-cream-dark pt-4">
                                        <p class="text-sm text-ink-muted leading-relaxed">{{ $answer }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Still have questions --}}
        <div class="mt-16 bg-cream-warm rounded-3xl border border-gold/20 p-8 text-center reveal" data-delay="100">
            <div class="w-14 h-14 rounded-2xl bg-gold/15 flex items-center justify-center mx-auto mb-4">
                <i class="fa-brands fa-whatsapp text-gold text-2xl"></i>
            </div>
            <h3 class="font-serif font-semibold text-xl text-ink mb-2">Masih Ada Pertanyaan?</h3>
            <p class="text-sm text-ink-muted mb-6 max-w-sm mx-auto">Tim kami siap membantu Anda melalui WhatsApp, setiap hari pukul 08.00–21.00 WIB.</p>
            <a href="https://wa.me/6281234567890?text=Halo%20SnapSpace%2C%20saya%20punya%20pertanyaan%20tentang..."
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-success text-white font-semibold text-sm hover:bg-success/90 transition-colors shadow-card btn-bounce">
                <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp Sekarang
            </a>
        </div>
    </div>
</section>

@endsection
