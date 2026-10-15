@extends('layouts.public')

@section('title', 'Tentang Kami — SnapSpace Studio Foto Premium Surabaya')
@section('meta_description', 'Kenali SnapSpace lebih dekat. Studio foto mandiri premium di Surabaya dengan berbagai tema estetik, fotografer berpengalaman, dan fasilitas modern.')

@section('content')

{{-- ── PAGE HEADER ── --}}
<section class="relative bg-cream-warm py-20 border-b border-cream-dark overflow-hidden">
    <div class="absolute inset-0 opacity-40" style="background: radial-gradient(ellipse 60% 80% at 80% 50%, #2F4F4F0C, transparent)"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-8">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Tentang Kami</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <div>
                <p class="text-xs font-bold text-gold uppercase tracking-widest mb-4">Cerita di Balik Studio</p>
                <h1 class="font-serif font-bold text-4xl sm:text-5xl text-ink mb-5 leading-tight">
                    Kami percaya setiap orang berhak tampil percaya diri
                </h1>
                <p class="text-ink-muted text-lg leading-relaxed mb-6">
                    SnapSpace lahir dari satu mimpi sederhana: memudahkan siapa saja untuk mendapatkan foto berkualitas profesional tanpa rasa canggung di depan fotografer asing.
                </p>
                <p class="text-ink-muted leading-relaxed">
                    Berdiri sejak 2022, kami telah membantu lebih dari <strong class="text-ink">500 orang</strong> mengabadikan momen terbaik mereka — dari sesi santai bersama sahabat, foto wisuda penuh haru, hingga pre-wedding yang intim dan romantis.
                </p>
            </div>

            <div class="relative">
                <div class="relative rounded-4xl overflow-hidden shadow-warm-lg aspect-[4/3]">
                    <img src="/images/hero-studio.jpg" alt="Interior SnapSpace Studio" class="w-full h-full object-cover">
                </div>
                {{-- Floating stat card --}}
                <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl px-6 py-4 shadow-warm-lg border border-gold/20">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gold flex items-center justify-center">
                            <i class="fa-solid fa-trophy text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="font-serif font-bold text-2xl text-ink leading-none">4.9/5</p>
                            <p class="text-xs text-ink-muted mt-0.5">Rating rata-rata</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── NILAI KAMI ── --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Komitmen Kami</p>
            <h2 class="font-serif font-bold text-3xl sm:text-4xl text-ink">Mengapa Memilih SnapSpace?</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['fa-user-shield', '#C8A27A', 'Privasi Terjamin', 'Setiap sesi berlangsung tanpa gangguan. Ruangan dikunci untuk Anda saja selama sesi berlangsung — tidak ada orang lain yang bisa masuk.'],
                ['fa-wand-magic-sparkles', '#2F4F4F', 'Kualitas Profesional', 'Kamera DSLR full-frame terkalibrasi + lighting Godox studio-grade + preset editing eksklusif. Hasilnya? Foto siap cetak & unggah.'],
                ['fa-calendar-check', '#2E9E6B', 'Booking Bebas Ribet', 'Tidak perlu DM, menunggu konfirmasi, atau bayar DP manual. Semua otomatis — pilih slot, bayar, tiket QR langsung di tangan.'],
                ['fa-paint-roller', '#C8A27A', 'Tema yang Beragam', 'Dari Korean minimalist hingga vintage darkroom, dari aesthetic boho hingga clean white — ada 15+ tema yang bisa dipilih sesuai mood.'],
                ['fa-clock-rotate-left', '#2F4F4F', 'Hasil Cepat', 'Foto editing selesai dan masuk ke galeri online Anda dalam 24 jam setelah sesi. Tidak perlu menunggu berminggu-minggu.'],
                ['fa-headset', '#2E9E6B', 'CS Responsif', 'Tim customer service kami siap membantu via WhatsApp setiap hari 08.00–21.00 WIB — dari pertanyaan hingga bantuan teknis.'],
            ] as [$icon, $color, $title, $desc])
                <div class="bg-cream rounded-2xl p-7 border border-cream-dark card-lift reveal shadow-card">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background: {{ $color }}15;">
                        <i class="fa-solid fa-{{ $icon }} text-lg" style="color: {{ $color }}"></i>
                    </div>
                    <h3 class="font-serif font-semibold text-lg text-ink mb-2">{{ $title }}</h3>
                    <p class="text-sm text-ink-muted leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── FASILITAS STUDIO ── --}}
<section class="py-16 bg-cream border-y border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <div class="order-2 lg:order-1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="aspect-square rounded-3xl overflow-hidden bg-cream-dark">
                        <img src="/images/portrait.jpg" alt="Studio fotografer" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-square rounded-3xl overflow-hidden bg-cream-dark mt-6">
                        <img src="/images/couple.jpg" alt="Sesi foto couple" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-square rounded-3xl overflow-hidden bg-cream-dark col-span-2">
                        <img src="/images/hero-studio.jpg" alt="Interior studio" class="w-full h-full object-cover object-bottom" style="max-height: 200px;">
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 reveal">
                <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Fasilitas Premium</p>
                <h2 class="font-serif font-bold text-3xl sm:text-4xl text-ink mb-5 leading-tight">
                    Semua yang Anda Butuhkan, Sudah Tersedia
                </h2>
                <p class="text-ink-muted leading-relaxed mb-7">
                    Kami menyiapkan setiap detail agar sesi foto Anda berjalan sempurna. Mulai dari peralatan hingga ruang ganti, semua ada.
                </p>

                <ul class="space-y-4">
                    @foreach([
                        ['Kamera DSLR/Mirrorless Full-Frame', 'Sony A7R IV + Canon EOS R5 terkalibrasi & terawat'],
                        ['Lighting Studio Godox Grade', 'Softbox, strip light, hair light — semua sudah ter-set optimal'],
                        ['Remote Shutter Wireless', 'Bebas jepret tanpa perlu minta tolong siapa pun'],
                        ['Ruang Ganti Privat', 'Ganti outfit nyaman tanpa rasa tidak enak'],
                        ['AC + WiFi Kencang', 'Nyaman sepanjang sesi, bisa langsung unggah stories'],
                        ['Parking Area', 'Tersedia area parkir motor dan mobil yang luas'],
                    ] as [$title, $desc])
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-xl bg-gold/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-check text-gold text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-ink">{{ $title }}</p>
                                <p class="text-xs text-ink-muted mt-0.5">{{ $desc }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ── TIM FOTOGRAFER ── --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Tim Profesional</p>
            <h2 class="font-serif font-bold text-3xl sm:text-4xl text-ink mb-4">Dikelola oleh Para Seniman Visual</h2>
            <p class="text-ink-muted max-w-xl mx-auto">Meski Anda foto mandiri, proses editing dan kurasi dilakukan oleh fotografer & photo editor berpengalaman kami.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['Arini Ramadhani', 'Founder & Head Photographer', '8+ tahun pengalaman di fashion & portrait photography', '#C8A27A'],
                ['Bagas Kurniawan', 'Photo Editor & Colorist', 'Spesialis retouching & color grading gaya editorial', '#2F4F4F'],
                ['Citra Wulandari', 'Studio Manager & CS Lead', 'Memastikan setiap sesi berjalan mulus dan pelanggan puas', '#2E9E6B'],
            ] as [$name, $role, $bio, $accent])
                <div class="bg-cream rounded-3xl p-7 border border-cream-dark card-lift reveal text-center">
                    <div class="w-20 h-20 rounded-full mx-auto mb-5 flex items-center justify-center text-white font-serif font-bold text-2xl" style="background: {{ $accent }}">
                        {{ strtoupper(substr($name, 0, 1)) }}
                    </div>
                    <h3 class="font-serif font-semibold text-lg text-ink">{{ $name }}</h3>
                    <p class="text-xs font-bold uppercase tracking-wide mt-1 mb-3" style="color: {{ $accent }}">{{ $role }}</p>
                    <p class="text-sm text-ink-muted leading-relaxed">{{ $bio }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CTA ── --}}
<section class="py-16 bg-cream border-t border-cream-dark">
    <div class="max-w-3xl mx-auto px-4 text-center reveal">
        <h2 class="font-serif font-bold text-3xl sm:text-4xl text-ink mb-4">Siap Mencoba Sendiri?</h2>
        <p class="text-ink-muted mb-8 leading-relaxed">Kunjungi studio kami atau booking langsung online sekarang. Kami jamin pengalaman yang menyenangkan.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('booking.wizard') }}" class="btn-gold btn-bounce inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-white font-bold shadow-warm">
                <i class="fa-solid fa-calendar-plus"></i> Booking Sekarang
            </a>
            @if(Route::has('contact'))
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-ink font-semibold bg-white border border-cream-dark hover:border-gold transition-all duration-200">
                    <i class="fa-solid fa-location-dot text-gold"></i> Kunjungi Studio
                </a>
            @endif
        </div>
    </div>
</section>

@endsection
