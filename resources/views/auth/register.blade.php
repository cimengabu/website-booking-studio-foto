<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun Baru — SnapSpace Studio</title>

    <!-- Google Fonts: Playfair Display + Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            DEFAULT: '#C8A27A',
                            light: '#DEC4A0',
                            dark: '#A07C52',
                            50: '#FAF5EE',
                            100: '#F3E8D8',
                        },
                        cream: {
                            DEFAULT: '#F8F6F3',
                            dark: '#EDE9E3',
                            warm: '#F2EDE6',
                        },
                        ink: {
                            DEFAULT: '#1A1A1A',
                            soft: '#2D2D2D',
                            muted: '#6B6560',
                            light: '#9C9490',
                        },
                        teal: {
                            DEFAULT: '#2F4F4F',
                            light: '#3D6464',
                            50: '#EEF4F4',
                        },
                        success: '#2E9E6B',
                        warning: '#E5A53B',
                        danger: '#D9534F',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(26, 26, 26, 0.05), 0 2px 6px -1px rgba(26, 26, 26, 0.03)',
                        'warm': '0 8px 30px -4px rgba(200, 162, 122, 0.25)',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-cream text-ink antialiased min-h-screen">

<div class="min-h-screen flex flex-col lg:flex-row" x-data="{ showPass: false, showConfirm: false }">

    <!-- LEFT SIDE: Editorial Photography Branding (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-ink overflow-hidden items-end p-12">
        <!-- Background Image with Overlay -->
        <img src="{{ asset('images/couple.jpg') }}" alt="SnapSpace Studio Sesi Foto" class="absolute inset-0 w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-ink/70 via-transparent to-transparent"></div>

        <!-- Top Logo -->
        <div class="absolute top-10 left-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gold flex items-center justify-center text-white shadow-warm group-hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid fa-camera-retro text-lg"></i>
                </div>
                <div>
                    <span class="block font-serif font-bold text-2xl text-white tracking-tight">SnapSpace</span>
                    <span class="block text-[10px] font-medium text-gold-light uppercase tracking-widest">Self-Photo & Studio Surabaya</span>
                </div>
            </a>
        </div>

        <!-- Bottom Quote & Highlights -->
        <div class="relative z-10 space-y-6 max-w-lg">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-medium">
                <i class="fa-solid fa-gift text-gold"></i>
                <span>Gratis konsultasi konsep & wardrobe touch-up</span>
            </div>

            <blockquote class="font-serif text-3xl font-medium text-white leading-relaxed">
                "Wujudkan potret impian Anda bersama pasangan, keluarga, dan sahabat dalam kenyamanan ruang privat."
            </blockquote>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/15 text-xs text-white/70">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-gold"></i>
                    <span>Akses galeri digital instan</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-gold"></i>
                    <span>Diskon member berkala</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Auth Card & Registration Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 lg:p-16">
        <div class="w-full max-w-md space-y-6">

            <!-- Mobile Top Logo & Navigation -->
            <div class="flex items-center justify-between lg:justify-end">
                <a href="{{ route('home') }}" class="flex lg:hidden items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-gold flex items-center justify-center text-white shadow-warm">
                        <i class="fa-solid fa-camera-retro text-sm"></i>
                    </div>
                    <span class="font-serif font-bold text-xl text-ink">SnapSpace</span>
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-ink-muted hover:text-gold transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            <!-- Header -->
            <div class="space-y-1.5">
                <span class="inline-block text-xs font-bold text-gold uppercase tracking-widest">Pendaftaran Pelanggan</span>
                <h1 class="font-serif font-bold text-3xl text-ink">Buat Akun SnapSpace</h1>
                <p class="text-sm text-ink-muted">Daftar sekarang untuk booking jadwal foto tercepat dan simpan riwayat galeri Anda.</p>
            </div>

            <!-- Flash Error Message -->
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-danger/10 border border-danger/20 text-danger text-xs font-medium space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                        <span>Mohon periksa data formulir:</span>
                    </div>
                    <ul class="list-disc list-inside pl-1 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-bold text-ink-muted uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                            <i class="fa-regular fa-user text-sm"></i>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                               placeholder="Contoh: Clarissa Wijaya">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-ink-muted uppercase tracking-wider mb-1.5">Alamat Email *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                               placeholder="email@kamu.com">
                    </div>
                </div>

                <!-- Phone (WhatsApp) -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-ink-muted uppercase tracking-wider mb-1.5">Nomor WhatsApp *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                        </div>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                               placeholder="081234567890">
                    </div>
                    <p class="text-[11px] text-ink-light mt-1">Digunakan untuk mengirimkan e-ticket QR dan notifikasi status sesi.</p>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-ink-muted uppercase tracking-wider mb-1.5">Password *</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" id="password" name="password" required
                                   class="w-full px-3.5 pr-9 py-2.5 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                                   placeholder="Min. 8 karakter">
                            <button type="button" @click="showPass = !showPass"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-ink-light hover:text-ink">
                                <i class="fa-regular text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-ink-muted uppercase tracking-wider mb-1.5">Ulangi Password *</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                   class="w-full px-3.5 pr-9 py-2.5 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                                   placeholder="Konfirmasi">
                            <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-ink-light hover:text-ink">
                                <i class="fa-regular text-xs" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Terms agreement checkbox -->
                <div class="pt-1">
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" required class="w-4 h-4 mt-0.5 rounded border-cream-dark text-gold focus:ring-gold/30">
                        <span class="text-xs text-ink-muted leading-relaxed">
                            Saya menyetujui <a href="{{ route('terms') }}" target="_blank" class="text-gold font-medium hover:underline">Syarat & Ketentuan</a> serta <a href="{{ route('privacy') }}" target="_blank" class="text-gold font-medium hover:underline">Kebijakan Privasi</a> SnapSpace Studio.
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-2xl bg-gold hover:bg-gold-dark text-white font-bold text-sm shadow-warm hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 pt-3">
                    <span>Daftar Akun Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Login Link -->
            <div class="text-center pt-2">
                <p class="text-xs text-ink-muted">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="text-gold font-bold hover:underline ml-1">
                        Masuk di sini
                    </a>
                </p>
            </div>

            <!-- Trust Badge -->
            <div class="pt-4 border-t border-cream-dark text-center">
                <p class="text-[11px] text-ink-light flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-lock text-gold"></i>
                    <span>Data pribadi Anda aman & dilindungi kerahasiaannya.</span>
                </p>
            </div>

        </div>
    </div>

</div>

</body>
</html>
