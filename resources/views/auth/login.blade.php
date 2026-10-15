<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk ke Akun — SnapSpace Studio</title>

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

<div class="min-h-screen flex flex-col lg:flex-row" x-data="loginForm()">

    <!-- LEFT SIDE: Editorial Photography Branding (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-ink overflow-hidden items-end p-12">
        <!-- Background Image with Overlay -->
        <img src="{{ asset('images/portrait.jpg') }}" alt="SnapSpace Studio" class="absolute inset-0 w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-ink/70 via-transparent to-transparent"></div>

        <!-- Decorative Pattern -->
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

        <!-- Bottom Quote & Badges -->
        <div class="relative z-10 space-y-6 max-w-lg">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-medium">
                <i class="fa-solid fa-star text-gold"></i>
                <span>4.9 / 5.0 dari 500+ sesi foto terselesaikan</span>
            </div>

            <blockquote class="font-serif text-3xl font-medium text-white leading-relaxed">
                "Setiap detik berharga, setiap senyum layak diabadikan dengan keindahan yang abadi."
            </blockquote>

            <div class="pt-4 border-t border-white/15 flex items-center justify-between text-xs text-white/70">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-gold"></i>
                    <span>Booking Instan & Real-time Slot</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-gold"></i>
                    <span>Lighting Profoto Premium</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Auth Card & Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 lg:p-16">
        <div class="w-full max-w-md space-y-8">

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
            <div class="space-y-2">
                <span class="inline-block text-xs font-bold text-gold uppercase tracking-widest">Portal Pelanggan & Mitra</span>
                <h1 class="font-serif font-bold text-3xl text-ink">Selamat Datang Kembali</h1>
                <p class="text-sm text-ink-muted">Masuk ke akun Anda untuk melihat jadwal, unduh hasil foto, atau pesan sesi baru.</p>
            </div>

            <!-- Quick Demo Login Switcher (Ideal for rapid testing & evaluation) -->
            <div class="bg-gold-50/70 border border-gold/30 rounded-2xl p-3.5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-gold-dark uppercase tracking-wider">
                        <i class="fa-solid fa-bolt mr-1"></i> Quick Demo Autofill:
                    </span>
                    <span class="text-[10px] text-ink-muted">Klik untuk isi otomatis</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="fill('customer@snapspace.id', 'password')"
                            class="px-2.5 py-1.5 rounded-xl bg-white text-ink text-xs font-medium border border-cream-dark hover:border-gold hover:text-gold shadow-xs transition-all text-center">
                        Pelanggan
                    </button>
                    <button type="button" @click="fill('staff@snapspace.id', 'password')"
                            class="px-2.5 py-1.5 rounded-xl bg-white text-ink text-xs font-medium border border-cream-dark hover:border-gold hover:text-gold shadow-xs transition-all text-center">
                        Staff QR
                    </button>
                    <button type="button" @click="fill('admin@snapspace.id', 'password')"
                            class="px-2.5 py-1.5 rounded-xl bg-white text-ink text-xs font-medium border border-cream-dark hover:border-gold hover:text-gold shadow-xs transition-all text-center">
                        Admin Studio
                    </button>
                </div>
            </div>

            <!-- Flash Error Message -->
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-danger/10 border border-danger/20 text-danger text-xs font-medium flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-base mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-bold">Gagal Masuk</p>
                        <p class="mt-0.5">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-success/10 border border-success/20 text-success text-xs font-medium flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-base mt-0.5 shrink-0"></i>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-ink-muted uppercase tracking-wider mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" x-model="email" value="{{ old('email') }}" required autofocus
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                               placeholder="email@kamu.com">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold text-ink-muted uppercase tracking-wider">Password</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" x-model="password" required
                               class="w-full pl-10 pr-10 py-3 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                               placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-ink-light hover:text-ink transition-colors">
                            <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-cream-dark text-gold focus:ring-gold/30">
                        <span class="text-xs text-ink-muted">Ingat saya</span>
                    </label>
                    <a href="https://wa.me/6281234567890?text=Halo%20SnapSpace,%20saya%20butuh%20bantuan%20reset%20password%20akun." target="_blank" class="text-xs text-gold hover:underline font-medium">
                        Lupa password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-2xl bg-gold hover:bg-gold-dark text-white font-bold text-sm shadow-warm hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                    <span>Masuk ke Akun</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center pt-2">
                <p class="text-xs text-ink-muted">
                    Belum memiliki akun SnapSpace?
                    <a href="{{ route('register') }}" class="text-gold font-bold hover:underline ml-1">
                        Daftar sekarang gratis
                    </a>
                </p>
            </div>

            <!-- Footer trust notes -->
            <div class="pt-6 border-t border-cream-dark text-center">
                <p class="text-[11px] text-ink-light flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Koneksi aman terenkripsi SSL 256-bit</span>
                </p>
            </div>

        </div>
    </div>

</div>

<script>
    function loginForm() {
        return {
            email: '{{ old('email') }}',
            password: '',
            showPassword: false,
            fill(e, p) {
                this.email = e;
                this.password = p;
            }
        }
    }
</script>

</body>
</html>
