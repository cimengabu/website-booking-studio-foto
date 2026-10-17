<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard — SnapSpace Studio')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;700&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&display=swap" rel="stylesheet">

    <!-- FontAwesome, Alpine.js, AOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
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
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #141311; }
        ::-webkit-scrollbar-thumb { background: #2D2B26; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #E8451E; }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 12px rgba(232, 69, 30, 0.3); }
            50% { box-shadow: 0 0 30px rgba(232, 69, 30, 0.7); }
        }
        .pulse-glow { animation: pulseGlow 2.5s infinite; }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, rgba(242,201,76,0.08) 0%, rgba(242,201,76,0.25) 50%, rgba(242,201,76,0.08) 100%);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-slide-up { animation: fadeSlideUp 0.4s ease-out forwards; }

        .nav-item-active {
            background: linear-gradient(135deg, rgba(232,69,30,0.15), rgba(232,69,30,0.05));
            border-left: 3px solid #E8451E;
            color: #E8451E;
        }
        .nav-item {
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.04);
            color: #E8451E;
        }

        /* Glassmorphism sidebar */
        .sidebar-glass {
            background: rgba(20, 19, 17, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#0F0E0C] text-gray-100 font-sans antialiased min-h-screen" x-data="{ mobileSidebar: false }">

{{-- Mobile sidebar overlay --}}
<div x-show="mobileSidebar" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
     @click="mobileSidebar = false"></div>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar-glass fixed inset-y-0 left-0 z-50 w-72 border-r border-[#2D2B26] flex flex-col transform transition-transform duration-300 ease-in-out
              -translate-x-full lg:translate-x-0"
       :class="{ 'translate-x-0': mobileSidebar }">

    {{-- Logo + Close --}}
    <div class="flex items-center justify-between h-20 px-6 border-b border-[#2D2B26] flex-shrink-0">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-xl bg-[#E8451E] flex items-center justify-center text-white font-heading font-black text-lg shadow-lg shadow-[#E8451E]/20">S</div>
            <div>
                <span class="font-heading font-extrabold text-xl text-white">SnapSpace<span class="text-[#E8451E]">.</span></span>
                <p class="text-[9px] font-mono text-gray-500 tracking-widest uppercase -mt-0.5">My Dashboard</p>
            </div>
        </a>
        <button @click="mobileSidebar = false" class="lg:hidden text-gray-500 hover:text-white p-1.5">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- User Card --}}
    <div class="px-4 py-5 border-b border-[#2D2B26] flex-shrink-0">
        <div class="bg-[#1E1D1A] rounded-2xl p-4 border border-[#2D2B26]">
            <div class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                    @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" class="w-12 h-12 rounded-full object-cover ring-2 ring-[#E8451E]/40">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#E8451E] to-[#d03a16] flex items-center justify-center font-heading font-black text-xl text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-[#1E1D1A]"></div>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-sm text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-gray-500 font-mono truncate">{{ Auth::user()->email }}</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-[#E8451E]/15 text-[#E8451E] uppercase tracking-wider">Member</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-[#F2C94C]/15 text-[#F2C94C]">
                            <i class="fa-solid fa-coins text-[8px]"></i> {{ number_format(Auth::user()->loyalty_points ?? 0, 0, ',', '.') }} pts
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
        <p class="px-3 text-[10px] font-mono text-gray-600 uppercase tracking-widest mb-3">Menu Utama</p>

        <a href="{{ route('dashboard.index') }}"
           class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard.index') ? 'nav-item-active' : 'text-gray-400' }}">
            <i class="fa-solid fa-house-chimney w-4 text-center text-base"></i>
            <span>Ringkasan</span>
        </a>

        <a href="{{ route('my-bookings.index') }}"
           class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('my-bookings.*') ? 'nav-item-active' : 'text-gray-400' }}">
            <i class="fa-solid fa-ticket w-4 text-center text-base"></i>
            <span>Pesanan Saya</span>
            @php $pendingCount = Auth::user()->bookings()->where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="ml-auto bg-[#E8451E] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingCount }}</span>
            @endif
        </a>

        <a href="{{ route('dashboard.gallery') }}"
           class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard.gallery') ? 'nav-item-active' : 'text-gray-400' }}">
            <i class="fa-solid fa-images w-4 text-center text-base"></i>
            <span>Galeri Foto Saya</span>
        </a>

        <a href="{{ route('dashboard.payments') }}"
           class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard.payments') ? 'nav-item-active' : 'text-gray-400' }}">
            <i class="fa-solid fa-receipt w-4 text-center text-base"></i>
            <span>Riwayat Pembayaran</span>
        </a>

        <a href="{{ route('favorites.index') }}"
           class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('favorites.*') ? 'nav-item-active' : 'text-gray-400' }}">
            <i class="fa-solid fa-heart w-4 text-center text-base"></i>
            <span>Ruangan Favorit</span>
        </a>

        <div class="pt-4 mt-2 border-t border-[#2D2B26]">
            <p class="px-3 text-[10px] font-mono text-gray-600 uppercase tracking-widest mb-3">Akun</p>

            <a href="{{ route('dashboard.profile') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard.profile') ? 'nav-item-active' : 'text-gray-400' }}">
                <i class="fa-solid fa-gear w-4 text-center text-base"></i>
                <span>Profil & Pengaturan</span>
            </a>

            <a href="{{ route('booking.wizard') }}"
               class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400">
                <i class="fa-solid fa-camera-retro w-4 text-center text-base"></i>
                <span>Booking Baru</span>
            </a>
        </div>
    </nav>

    {{-- Bottom actions --}}
    <div class="px-3 py-4 border-t border-[#2D2B26] flex-shrink-0 space-y-1">
        <a href="{{ route('home') }}"
           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-400">
            <i class="fa-solid fa-arrow-left w-4 text-center text-sm"></i>
            <span>Kembali ke Beranda</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-rose-500 hover:text-rose-400 hover:bg-rose-500/10">
                <i class="fa-solid fa-right-from-bracket w-4 text-center text-sm"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- ── MAIN CONTENT AREA ── --}}
<div class="lg:pl-72 flex flex-col min-h-screen">

    {{-- Top header bar --}}
    <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 sm:px-6 bg-[#0F0E0C]/90 backdrop-blur-md border-b border-[#2D2B26]">

        {{-- Mobile menu toggle --}}
        <button @click="mobileSidebar = true" class="lg:hidden p-2 text-gray-400 hover:text-white">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        {{-- Page title --}}
        <div class="flex items-center gap-2 text-sm font-medium text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">SnapSpace</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-white">@yield('breadcrumb', 'Dashboard')</span>
        </div>

        {{-- Header right actions --}}
        <div class="flex items-center gap-3">
            {{-- Notification bell --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-white transition-colors">
                    <i class="fa-regular fa-bell text-lg"></i>
                    @php $pendingCt = Auth::user()->bookings()->where('status','pending')->count(); @endphp
                    @if($pendingCt)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-[#E8451E] rounded-full pulse-glow"></span>
                    @endif
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-80 bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl shadow-2xl z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-[#2D2B26] flex items-center justify-between">
                        <p class="font-semibold text-sm text-white">Notifikasi</p>
                        <span class="text-[10px] font-mono text-gray-500">Hari ini</span>
                    </div>
                    @if($pendingCt > 0)
                        <div class="px-4 py-3.5 flex items-start gap-3 hover:bg-white/5 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-[#E8451E]/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-clock text-[#E8451E] text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm text-white font-medium">{{ $pendingCt }} booking menunggu pembayaran</p>
                                <p class="text-xs text-gray-500 mt-0.5">Segera selesaikan pembayaran sebelum expired.</p>
                                <a href="{{ route('my-bookings.index', ['status' => 'pending']) }}" class="text-xs text-[#E8451E] hover:underline mt-1 inline-block">Lihat detail →</a>
                            </div>
                        </div>
                    @else
                        <div class="px-4 py-8 text-center">
                            <i class="fa-regular fa-bell-slash text-2xl text-gray-600 mb-2"></i>
                            <p class="text-sm text-gray-500">Tidak ada notifikasi baru</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick book button --}}
            <a href="{{ route('booking.wizard') }}"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#E8451E] text-white font-semibold text-xs hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i class="fa-solid fa-plus text-[10px]"></i> Booking Baru
            </a>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mx-4 sm:mx-6 mt-4 bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-2xl flex items-center justify-between shadow-lg fade-slide-up">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-100 p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="mx-4 sm:mx-6 mt-4 bg-rose-950/80 border border-rose-500/40 text-rose-200 px-4 py-3 rounded-2xl shadow-lg fade-slide-up">
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
    @endif

    {{-- Page content --}}
    <main class="flex-1 px-4 sm:px-6 py-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-[#2D2B26] px-4 sm:px-6 py-4 text-xs text-gray-600 font-mono flex items-center justify-between">
        <p>© {{ date('Y') }} SnapSpace Studio Platform</p>
        <a href="{{ route('home') }}" class="hover:text-gray-400 transition-colors">← Kembali ke Beranda</a>
    </footer>
</div>

{{-- ── MOBILE BOTTOM NAV ── --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-[#141311]/95 backdrop-blur-md border-t border-[#2D2B26] flex items-center justify-around h-16 px-2 pb-safe">
    <a href="{{ route('dashboard.index') }}"
       class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('dashboard.index') ? 'text-[#E8451E]' : 'text-gray-500' }}">
        <i class="fa-solid fa-house text-lg"></i>
        <span class="text-[9px] font-mono uppercase tracking-wider">Beranda</span>
    </a>
    <a href="{{ route('my-bookings.index') }}"
       class="relative flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('my-bookings.*') ? 'text-[#E8451E]' : 'text-gray-500' }}">
        <i class="fa-solid fa-ticket text-lg"></i>
        <span class="text-[9px] font-mono uppercase tracking-wider">Pesanan</span>
        @if(Auth::user()->bookings()->where('status','pending')->count() > 0)
            <span class="absolute top-0.5 right-1.5 w-2 h-2 bg-[#E8451E] rounded-full"></span>
        @endif
    </a>
    <a href="{{ route('booking.wizard') }}"
       class="flex flex-col items-center gap-0.5">
        <div class="w-12 h-12 -mt-4 rounded-full bg-[#E8451E] flex items-center justify-center shadow-lg shadow-[#E8451E]/40 pulse-glow">
            <i class="fa-solid fa-camera text-white text-lg"></i>
        </div>
    </a>
    <a href="{{ route('dashboard.gallery') }}"
       class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('dashboard.gallery') ? 'text-[#E8451E]' : 'text-gray-500' }}">
        <i class="fa-solid fa-images text-lg"></i>
        <span class="text-[9px] font-mono uppercase tracking-wider">Galeri</span>
    </a>
    <a href="{{ route('dashboard.profile') }}"
       class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('dashboard.profile') ? 'text-[#E8451E]' : 'text-gray-500' }}">
        <i class="fa-solid fa-user text-lg"></i>
        <span class="text-[9px] font-mono uppercase tracking-wider">Profil</span>
    </a>
</nav>

<div class="h-16 lg:hidden"></div> {{-- Bottom nav spacer --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 600, easing: 'ease-out-cubic', once: true });
        }
    });
</script>
@stack('scripts')
</body>
</html>
