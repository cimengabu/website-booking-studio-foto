<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel — SnapSpace Studio')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Bricolage+Grotesque:wght@700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome & Alpine & Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#141311] text-gray-100 font-sans antialiased min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#1E1D1A] border-r border-[#2D2B26] flex flex-col justify-between shrink-0 min-h-screen sticky top-0 h-screen overflow-y-auto">
        <div>
            <!-- Header Logo -->
            <div class="p-6 border-b border-[#2D2B26]">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#E8451E] flex items-center justify-center text-white font-heading font-black text-xl shadow-lg">S</div>
                    <div>
                        <span class="font-heading font-extrabold text-xl text-white">SnapSpace</span>
                        <span class="block text-[10px] font-mono text-[#F2C94C] uppercase font-bold tracking-wider">ADMIN CONTROL</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
                </a>
                
                <div class="pt-4 pb-1 px-4 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider">Manajemen Booking</div>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.bookings.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-calendar-check w-5"></i> Kelola Booking
                </a>
                <a href="{{ route('admin.calendar.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.calendar.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-calendar-days w-5"></i> Kalender Studio
                </a>
                <a href="{{ route('admin.photos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.photos.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-cloud-arrow-up w-5"></i> Upload Hasil Foto
                </a>

                <div class="pt-4 pb-1 px-4 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider">Master Data</div>
                <a href="{{ route('admin.rooms.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.rooms.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-[#F2C94C] fa-door-open w-5"></i> Ruangan & Tema
                </a>
                <a href="{{ route('admin.packages.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.packages.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-box-open w-5"></i> Paket Foto
                </a>
                <a href="{{ route('admin.addons.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.addons.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-puzzle-piece w-5"></i> Add-on Layanan
                </a>
                <a href="{{ route('admin.portfolios.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.portfolios.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-images w-5"></i> Portofolio
                </a>

                <div class="pt-4 pb-1 px-4 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider">Pemasaran & Laporan</div>
                <a href="{{ route('admin.promos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.promos.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-ticket-simple w-5"></i> Kode Promo
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.reviews.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-star w-5"></i> Moderasi Ulasan
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5"></i> Laporan Keuangan
                </a>

                <div class="pt-4 pb-1 px-4 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider">Sistem</div>
                <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-users w-5"></i> Data Pelanggan
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-gears w-5"></i> Pengaturan Studio
                </a>
                <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-[#E8451E]/15 hover:text-[#E8451E] transition-colors {{ request()->routeIs('admin.logs.*') ? 'bg-[#E8451E] text-white font-semibold' : 'text-gray-300' }}">
                    <i class="fa-solid fa-[#F2C94C] fa-list-check w-5"></i> Activity Log
                </a>
            </nav>
        </div>

        <!-- Admin Profile -->
        <div class="p-4 border-t border-[#2D2B26] bg-[#141311]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#E8451E] text-white font-bold flex items-center justify-center text-xs">
                        AD
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">Administrator</p>
                    </div>
                </div>
                <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-xs p-2" title="Kembali ke Situs Utama">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-grow min-w-0 flex flex-col">
        <!-- Top Navbar -->
        <header class="bg-[#1E1D1A] border-b border-[#2D2B26] px-8 py-4 flex items-center justify-between sticky top-0 z-40">
            <div>
                <h1 class="text-lg font-heading font-bold text-white">@yield('page-title', 'Dashboard Overview')</h1>
                <p class="text-xs text-gray-400 font-mono">SnapSpace Management Platform</p>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.bookings.walkin') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#E8451E] text-white font-semibold text-xs hover:bg-[#d03a16] shadow-md transition-all">
                    <i class="fa-solid fa-plus text-xs"></i> Booking Walk-In
                </a>
                <a href="{{ route('staff.scan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#1F3A2E] text-[#F2C94C] border border-[#1F3A2E] font-semibold text-xs hover:bg-[#152820] transition-colors">
                    <i class="fa-solid fa-qrcode text-xs"></i> Mode Staff Scanner
                </a>
            </div>
        </header>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="mx-8 mt-6 bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-2xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-8 mt-6 bg-rose-950/80 border border-rose-500/40 text-rose-200 px-4 py-3 rounded-2xl">
                <div class="flex items-center gap-3 mb-1">
                    <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                    <p class="text-sm font-semibold">Terdapat kendala input:</p>
                </div>
                <ul class="list-disc list-inside text-xs text-rose-300 ml-7 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Page Body -->
        <main class="p-8 flex-grow">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
