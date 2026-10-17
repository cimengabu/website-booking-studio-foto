<!DOCTYPE html>
<html lang="id" class="dark" x-data="{ darkMode: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SnapSpace — Self-Photo Studio Booking Platform')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;700&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&display=swap" rel="stylesheet">

    <!-- FontAwesome, Alpine.js, AOS & Helper Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            paper: '#F3EFE6',
                            ink: '#1B1A17',
                            safelight: '#E8451E',
                            forest: '#1F3A2E',
                            butter: '#F2C94C',
                            darkroom: '#141311',
                            cardDark: '#1E1D1A',
                            borderDark: '#2D2B26'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Bricolage Grotesque', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes scanbeam {
            0% { top: 0%; opacity: 0.8; }
            50% { top: 92%; opacity: 1; }
            100% { top: 0%; opacity: 0.8; }
        }
        .animate-scanbeam {
            animation: scanbeam 2.2s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(232, 69, 30, 0.4); }
            50% { box-shadow: 0 0 35px rgba(232, 69, 30, 0.85); }
        }
        .animate-pulse-glow {
            animation: pulseGlow 2.5s infinite;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float-slow {
            animation: floatSlow 4s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .shimmer-badge {
            background: linear-gradient(90deg, rgba(242, 201, 76, 0.1) 0%, rgba(242, 201, 76, 0.4) 50%, rgba(242, 201, 76, 0.1) 100%);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#141311] text-gray-100 font-sans antialiased min-h-screen flex flex-col selection:bg-[#E8451E] selection:text-white">


    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-[#141311]/90 backdrop-blur-md border-b border-[#2D2B26]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-[#E8451E] flex items-center justify-center text-white font-heading font-black text-xl shadow-lg shadow-[#E8451E]/20 group-hover:scale-105 transition-transform">
                        S
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading font-extrabold text-2xl tracking-tight text-white group-hover:text-[#E8451E] transition-colors">SnapSpace<span class="text-[#E8451E]">.</span></span>
                        <span class="text-[10px] font-mono text-gray-400 tracking-widest uppercase -mt-1">Studio Booking</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-300">
                    <a href="{{ route('home') }}" class="hover:text-[#E8451E] transition-colors {{ request()->routeIs('home') ? 'text-[#E8451E] font-semibold' : '' }}">Beranda</a>
                    <a href="{{ route('rooms.index') }}" class="hover:text-[#E8451E] transition-colors {{ request()->routeIs('rooms.*') ? 'text-[#E8451E] font-semibold' : '' }}">Ruangan & Tema</a>
                    <a href="{{ route('packages.index') }}" class="hover:text-[#E8451E] transition-colors {{ request()->routeIs('packages.*') ? 'text-[#E8451E] font-semibold' : '' }}">Paket & Harga</a>
                    <a href="{{ route('portfolio.index') }}" class="hover:text-[#E8451E] transition-colors {{ request()->routeIs('portfolio.*') ? 'text-[#E8451E] font-semibold' : '' }}">Portofolio</a>
                </nav>

                <!-- Actions / Auth -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('booking.wizard') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#E8451E] text-white font-semibold text-sm hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/30 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        <i class="fa-solid fa-calendar-plus text-xs"></i> Booking Sekarang
                    </a>

                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-full border border-[#2D2B26] hover:border-[#E8451E] transition-colors bg-[#1E1D1A]">
                                <div class="w-8 h-8 rounded-full bg-[#1F3A2E] text-[#F2C94C] flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span class="text-xs font-medium text-gray-200 hidden sm:inline pr-2">{{ Auth::user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 pr-2"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl shadow-2xl py-2 z-50 text-sm">
                                <div class="px-4 py-3 border-b border-[#2D2B26]">
                                    <p class="font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block px-2 py-0.5 mt-1 rounded text-[10px] font-mono font-bold bg-[#1F3A2E] text-[#F2C94C] uppercase">{{ Auth::user()->role }}</span>
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:bg-[#E8451E]/10 hover:text-[#E8451E]">
                                        <i class="fa-solid fa-chart-line text-xs w-4"></i> Admin Dashboard
                                    </a>
                                @endif

                                @if(Auth::user()->isStaff() || Auth::user()->isAdmin())
                                    <a href="{{ route('staff.scan') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:bg-[#E8451E]/10 hover:text-[#E8451E]">
                                        <i class="fa-solid fa-qrcode text-xs w-4"></i> Staff Scan QR
                                    </a>
                                @endif

                                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:bg-[#E8451E]/10 hover:text-[#E8451E]">
                                    <i class="fa-solid fa-house-chimney text-xs w-4"></i> Dashboard Saya
                                </a>
                                <a href="{{ route('my-bookings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:bg-[#E8451E]/10 hover:text-[#E8451E]">
                                    <i class="fa-solid fa-ticket text-xs w-4"></i> Booking Saya
                                </a>
                                <a href="{{ route('favorites.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:bg-[#E8451E]/10 hover:text-[#E8451E]">
                                    <i class="fa-solid fa-heart text-xs w-4"></i> Ruangan Favorit
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="border-t border-[#2D2B26] mt-1 pt-1">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-red-400 hover:bg-red-500/10 hover:text-red-300">
                                        <i class="fa-solid fa-right-from-bracket text-xs w-4"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-[#1E1D1A] border border-[#2D2B26] hover:border-[#E8451E] px-4 py-2 rounded-full transition-colors">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-2xl flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-100"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-rose-950/80 border border-rose-500/40 text-rose-200 px-4 py-3 rounded-2xl shadow-lg">
                <div class="flex items-center gap-3 mb-1">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg"></i>
                    <p class="text-sm font-semibold">{{ session('error') ?? 'Terdapat beberapa kendala:' }}</p>
                </div>
                @if($errors->any())
                    <ul class="list-disc list-inside text-xs text-rose-300 space-y-0.5 ml-7">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#0D0D0C] border-t border-[#2D2B26] mt-24 text-gray-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#E8451E] flex items-center justify-center text-white font-heading font-black text-lg">S</div>
                    <span class="font-heading font-extrabold text-xl text-white">SnapSpace<span class="text-[#E8451E]">.</span></span>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Platform booking self-photo studio online terdepan di Indonesia. Abadikan momen terbaik Anda bersama sahabat & pasangan tanpa rasa canggung.
                </p>
                <div class="flex gap-3 text-gray-400 pt-2">
                    <a href="#" class="w-8 h-8 rounded-full bg-[#1E1D1A] flex items-center justify-center hover:bg-[#E8451E] hover:text-white transition-colors"><i class="fa-brands fa-instagram text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-[#1E1D1A] flex items-center justify-center hover:bg-[#E8451E] hover:text-white transition-colors"><i class="fa-brands fa-tiktok text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-[#1E1D1A] flex items-center justify-center hover:bg-[#E8451E] hover:text-white transition-colors"><i class="fa-brands fa-whatsapp text-xs"></i></a>
                </div>
            </div>

            <div>
                <h4 class="font-heading font-bold text-white text-sm uppercase tracking-wider mb-4">Navigasi Studio</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('rooms.index') }}" class="hover:text-white transition-colors">Pilihan Ruangan & Tema</a></li>
                    <li><a href="{{ route('packages.index') }}" class="hover:text-white transition-colors">Daftar Paket Foto</a></li>
                    <li><a href="{{ route('portfolio.index') }}" class="hover:text-white transition-colors">Galeri Portofolio</a></li>
                    <li><a href="{{ route('booking.wizard') }}" class="hover:text-white transition-colors">Booking Sesi Foto</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-heading font-bold text-white text-sm uppercase tracking-wider mb-4">Lokasi & Jam Kerja</h4>
                <p class="text-xs text-gray-400 leading-relaxed mb-2">
                    <i class="fa-solid fa-location-dot text-[#E8451E] mr-1.5"></i> Jl. Tunjungan No. 88, Surabaya, Jawa Timur
                </p>
                <p class="text-xs text-gray-400 mb-1">
                    <i class="fa-solid fa-clock text-[#F2C94C] mr-1.5"></i> Buka Setiap Hari: 09:00 - 21:00 WIB
                </p>
                <p class="text-xs text-gray-400">
                    <i class="fa-solid fa-headset text-emerald-400 mr-1.5"></i> WA CS: +62 812-3456-7890
                </p>
            </div>

            <div>
                <h4 class="font-heading font-bold text-white text-sm uppercase tracking-wider mb-4">Jaminan Layanan</h4>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center gap-2 text-emerald-400 font-mono">
                        <i class="fa-solid fa-lock text-xs"></i> Realtime Slot Locking
                    </div>
                    <div class="flex items-center gap-2 text-emerald-400 font-mono">
                        <i class="fa-solid fa-[#F2C94C] fa-shield-halved text-xs"></i> Payment Midtrans Sandbox
                    </div>
                    <div class="flex items-center gap-2 text-emerald-400 font-mono">
                        <i class="fa-solid fa-bolt text-xs"></i> QR Code E-Ticket Instan
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-[#2D2B26]/60 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 font-mono">
            <p>&copy; {{ date('Y') }} SnapSpace Studio Platform. All rights reserved.</p>
            <p class="mt-2 sm:mt-0">Built with Laravel 11 & Tailwind CSS</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 700,
                    easing: 'ease-out-cubic',
                    once: true
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

