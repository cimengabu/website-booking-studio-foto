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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&family=Bricolage+Grotesque:wght@700;800&display=swap" rel="stylesheet">

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
    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #141311; }
        ::-webkit-scrollbar-thumb { background: #2D2B26; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #E8451E; }

        .nav-link-active {
            background: linear-gradient(135deg, rgba(232,69,30,0.18), rgba(232,69,30,0.06));
            border-left: 3px solid #E8451E;
            color: #E8451E;
            font-weight: 600;
        }
        .nav-link {
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.04);
            color: #E8451E;
        }

        /* Pulse glow animation */
        @keyframes notifPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.8; }
        }
        .notif-pulse { animation: notifPulse 2s infinite ease-in-out; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#141311] text-gray-100 font-sans antialiased min-h-screen flex"
      x-data="{
          sidebarCollapsed: localStorage.getItem('admin_sidebar_collapsed') === 'true',
          mobileSidebar: false,
          globalSearch: false,
          notificationsOpen: false,
          profileOpen: false,
          searchQuery: '',
          searchResults: [],
          isSearching: false,
          notifications: [],
          unreadCount: 0,
          toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('admin_sidebar_collapsed', this.sidebarCollapsed);
          },
          async performSearch() {
              if (this.searchQuery.length < 2) {
                  this.searchResults = [];
                  return;
              }
              this.isSearching = true;
              try {
                  const res = await fetch(`{{ route('admin.api.search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                  const data = await res.json();
                  this.searchResults = data.results || [];
              } catch (e) {
                  console.error(e);
                  this.searchResults = [];
              } finally {
                  this.isSearching = false;
              }
          },
          async fetchNotifications() {
              try {
                  const res = await fetch('{{ route('admin.api.notifications') }}');
                  const data = await res.json();
                  this.notifications = data.notifications || [];
                  this.unreadCount = data.unread_count || 0;
              } catch (e) {
                  console.error(e);
              }
          }
      }"
      x-init="fetchNotifications();"
      @keydown.window.ctrl.k.prevent="globalSearch = true; $nextTick(() => $refs.searchInput?.focus())"
      @keydown.window.escape="globalSearch = false; notificationsOpen = false; profileOpen = false">

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="mobileSidebar" x-transition.opacity
         @click="mobileSidebar = false"
         class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── COLLAPSIBLE SIDEBAR ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <aside class="bg-[#1E1D1A] border-r border-[#2D2B26] flex flex-col justify-between shrink-0 min-h-screen sticky top-0 h-screen z-50 transition-all duration-300 ease-in-out
                  fixed lg:static inset-y-0 left-0"
           :class="{
               'w-64': !sidebarCollapsed,
               'w-20': sidebarCollapsed,
               'translate-x-0': mobileSidebar,
               '-translate-x-full lg:translate-x-0': !mobileSidebar
           }">

        <div class="flex flex-col h-full overflow-hidden">
            {{-- Header Logo --}}
            <div class="h-16 flex items-center px-4 border-b border-[#2D2B26] shrink-0"
                 :class="sidebarCollapsed ? 'justify-center' : 'justify-between px-5'">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-[#E8451E] flex items-center justify-center text-white font-heading font-black text-xl shadow-lg shadow-[#E8451E]/20 shrink-0">
                        S
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition.opacity class="truncate">
                        <span class="font-heading font-extrabold text-xl text-white">SnapSpace<span class="text-[#E8451E]">.</span></span>
                        <span class="block text-[9px] font-mono text-[#F2C94C] uppercase font-bold tracking-widest -mt-0.5">Admin Studio</span>
                    </div>
                </a>

                {{-- Close on Mobile --}}
                <button @click="mobileSidebar = false" class="lg:hidden text-gray-500 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            {{-- Role Mini Badge (when expanded) --}}
            <div x-show="!sidebarCollapsed" x-transition.opacity class="px-5 py-3 border-b border-[#2D2B26] bg-[#171614]">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono text-gray-400 uppercase tracking-wider">Peran Anda:</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ Auth::user()->role_badge_class }}">
                        {{ Auth::user()->role_label }}
                    </span>
                </div>
            </div>

            {{-- Navigation Items (Scrollable) --}}
            <nav class="flex-1 overflow-y-auto p-2.5 space-y-1 text-sm font-medium">

                {{-- 1. UTAMA --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-base"></i>
                    <span x-show="!sidebarCollapsed" class="truncate text-xs">Dashboard</span>
                    {{-- Tooltip when collapsed --}}
                    <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                        Dashboard
                    </div>
                </a>

                {{-- ── SECTION: OPERASIONAL & BOOKING (Owner, Staff, Photographer) ── --}}
                <div x-show="!sidebarCollapsed" class="pt-3 pb-1 px-3 text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest">
                    Operasional
                </div>

                {{-- Kelola Booking --}}
                <a href="{{ route('admin.bookings.index') }}"
                   class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bookings.index') || request()->routeIs('admin.bookings.show') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <i class="fa-solid fa-calendar-check w-5 text-center text-base"></i>
                    <span x-show="!sidebarCollapsed" class="truncate text-xs">
                        {{ Auth::user()->isPhotographer() ? 'Jadwal Sesi Saya' : 'Kelola Booking' }}
                    </span>
                    <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                        {{ Auth::user()->isPhotographer() ? 'Jadwal Sesi Saya' : 'Kelola Booking' }}
                    </div>
                </a>

                {{-- Booking Walk-In (Owner & Staff) --}}
                @if(Auth::user()->isOwner() || Auth::user()->isStaff())
                    <a href="{{ route('admin.bookings.walkin') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bookings.walkin') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-person-walking-dashed-line-arrow-right w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Booking Walk-In</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Booking Walk-In
                        </div>
                    </a>

                    {{-- Staff QR Scan --}}
                    <a href="{{ route('staff.scan') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('staff.scan') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-qrcode w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Scan QR Check-In</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Scan QR Check-In
                        </div>
                    </a>
                @endif

                {{-- Kalender Studio --}}
                <a href="{{ route('admin.calendar.index') }}"
                   class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.calendar.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <i class="fa-solid fa-calendar-days w-5 text-center text-base"></i>
                    <span x-show="!sidebarCollapsed" class="truncate text-xs">Kalender Studio</span>
                    <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                        Kalender Studio
                    </div>
                </a>

                {{-- Upload Hasil Foto (Owner, Staff, Photographer) --}}
                <a href="{{ route('admin.photos.index') }}"
                   class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.photos.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <i class="fa-solid fa-cloud-arrow-up w-5 text-center text-base"></i>
                    <span x-show="!sidebarCollapsed" class="truncate text-xs">Upload Hasil Foto</span>
                    <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                        Upload Hasil Foto
                    </div>
                </a>

                {{-- Customers (Owner & Staff) --}}
                @if(Auth::user()->isOwner() || Auth::user()->isStaff())
                    <a href="{{ route('admin.customers.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.customers.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-users w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Data Pelanggan</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Data Pelanggan
                        </div>
                    </a>

                    {{-- Moderasi Ulasan --}}
                    <a href="{{ route('admin.reviews.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.reviews.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-star w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Moderasi Ulasan</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Moderasi Ulasan
                        </div>
                    </a>
                @endif

                {{-- ── SECTION: OWNER EXCLUSIVE (Master Data & Finansial & Tim) ── --}}
                @if(Auth::user()->isOwner())
                    <div x-show="!sidebarCollapsed" class="pt-3 pb-1 px-3 text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest">
                        Master Data
                    </div>

                    <a href="{{ route('admin.rooms.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.rooms.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-door-open w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Ruangan & Tema</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Ruangan & Tema
                        </div>
                    </a>

                    <a href="{{ route('admin.packages.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.packages.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-box-open w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Paket Foto</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Paket Foto
                        </div>
                    </a>

                    <a href="{{ route('admin.addons.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.addons.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-puzzle-piece w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Add-on Layanan</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Add-on Layanan
                        </div>
                    </a>

                    <a href="{{ route('admin.promos.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.promos.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-ticket-simple w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Kode Promo</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Kode Promo
                        </div>
                    </a>

                    <a href="{{ route('admin.portfolios.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.portfolios.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-images w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Portofolio</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Portofolio
                        </div>
                    </a>

                    {{-- ── SECTION: PENGATURAN & RBAC ── --}}
                    <div x-show="!sidebarCollapsed" class="pt-3 pb-1 px-3 text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest">
                        Keuangan & RBAC
                    </div>

                    <a href="{{ route('admin.reports.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.reports.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Laporan Keuangan</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Laporan Keuangan
                        </div>
                    </a>

                    {{-- Manajemen Tim & RBAC --}}
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-user-shield w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Manajemen Tim (RBAC)</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Manajemen Tim (RBAC)
                        </div>
                    </a>

                    <a href="{{ route('admin.settings.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.settings.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-gears w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Pengaturan Studio</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Pengaturan Studio
                        </div>
                    </a>

                    <a href="{{ route('admin.logs.index') }}"
                       class="nav-link relative group flex items-center gap-3.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.logs.*') ? 'nav-link-active' : 'text-gray-400 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <i class="fa-solid fa-list-check w-5 text-center text-base"></i>
                        <span x-show="!sidebarCollapsed" class="truncate text-xs">Activity Log</span>
                        <div x-show="sidebarCollapsed" class="hidden group-hover:block absolute left-full ml-3 px-3 py-1.5 bg-[#1E1D1A] text-white text-xs font-semibold rounded-xl border border-[#2D2B26] shadow-2xl z-50 whitespace-nowrap pointer-events-none">
                            Activity Log
                        </div>
                    </a>
                @endif
            </nav>

            {{-- Sidebar Footer with Collapse Toggle --}}
            <div class="p-3 border-t border-[#2D2B26] bg-[#171614] shrink-0">
                <button @click="toggleSidebar()"
                        class="hidden lg:flex items-center gap-3 w-full p-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/[0.04] transition-colors"
                        :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <i class="fa-solid text-sm" :class="sidebarCollapsed ? 'fa-angles-right text-[#E8451E]' : 'fa-angles-left'"></i>
                        <span x-show="!sidebarCollapsed" class="text-xs font-mono font-medium truncate">Ciutkan Sidebar</span>
                    </div>
                    <kbd x-show="!sidebarCollapsed" class="text-[9px] font-mono bg-[#1E1D1A] border border-[#2D2B26] px-1.5 py-0.5 rounded text-gray-500">Ctrl+[</kbd>
                </button>
            </div>
        </div>
    </aside>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── MAIN CONTENT WRAPPER ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="flex-grow min-w-0 flex flex-col min-h-screen">

        {{-- ── TOPBAR ── --}}
        <header class="bg-[#1E1D1A]/95 backdrop-blur-md border-b border-[#2D2B26] px-4 sm:px-6 h-16 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-3">
                {{-- Hamburger for Mobile --}}
                <button @click="mobileSidebar = true" class="lg:hidden p-2 text-gray-400 hover:text-white">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                {{-- Desktop Collapse Toggle --}}
                <button @click="toggleSidebar()"
                        class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg border border-[#2D2B26] text-gray-400 hover:text-white hover:border-[#E8451E]/40 transition-colors"
                        title="Toggle Sidebar">
                    <i class="fa-solid text-xs" :class="sidebarCollapsed ? 'fa-indent' : 'fa-outdent'"></i>
                </button>

                {{-- Page Title --}}
                <div class="hidden sm:block">
                    <h1 class="text-sm font-heading font-bold text-white">@yield('page-title', 'Dashboard Overview')</h1>
                    <p class="text-[10px] text-gray-400 font-mono">SnapSpace Management Platform</p>
                </div>
            </div>

            {{-- Center: Global Search Bar Trigger (Ctrl + K) --}}
            <div class="flex-1 max-w-md mx-4">
                <button type="button"
                        @click="globalSearch = true; $nextTick(() => $refs.searchInput?.focus())"
                        class="w-full flex items-center justify-between gap-3 bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E]/40 text-gray-400 hover:text-gray-300 rounded-xl px-3.5 py-2 text-xs transition-all shadow-inner group">
                    <div class="flex items-center gap-2.5 truncate">
                        <i class="fa-solid fa-magnifying-glass text-gray-500 group-hover:text-[#E8451E] transition-colors"></i>
                        <span class="truncate">Cari booking, customer, ruangan...</span>
                    </div>
                    <kbd class="hidden sm:inline-flex items-center gap-0.5 font-mono text-[10px] bg-[#1E1D1A] border border-[#2D2B26] text-gray-400 px-1.5 py-0.5 rounded shadow-sm">
                        Ctrl+K
                    </kbd>
                </button>
            </div>

            {{-- Right: Actions & User Menu --}}
            <div class="flex items-center gap-2.5">
                {{-- Role Badge --}}
                <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold uppercase {{ Auth::user()->role_badge_class }}">
                    @if(Auth::user()->isOwner())
                        <i class="fa-solid fa-crown text-[8px]"></i>
                    @elseif(Auth::user()->isStaff())
                        <i class="fa-solid fa-cash-register text-[8px]"></i>
                    @elseif(Auth::user()->isPhotographer())
                        <i class="fa-solid fa-camera text-[8px]"></i>
                    @endif
                    {{ Auth::user()->role_label }}
                </span>

                {{-- Quick Actions --}}
                @if(Auth::user()->isOwner() || Auth::user()->isStaff())
                    <a href="{{ route('admin.bookings.walkin') }}"
                       class="hidden xl:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs shadow-md shadow-[#E8451E]/20 transition-all">
                        <i class="fa-solid fa-plus text-[10px]"></i> Walk-In
                    </a>
                @endif

                {{-- ── NOTIFICATION BELL DROPDOWN ── --}}
                <div class="relative" x-data="{ notifOpen: false }">
                    <button @click="notifOpen = !notifOpen"
                            class="relative w-9 h-9 rounded-xl border border-[#2D2B26] bg-[#141311] hover:border-[#E8451E]/40 text-gray-400 hover:text-white flex items-center justify-center transition-colors">
                        <i class="fa-regular fa-bell text-sm"></i>
                        <template x-if="unreadCount > 0">
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#E8451E] text-white text-[9px] font-bold font-mono rounded-full flex items-center justify-center shadow-lg notif-pulse"
                                  x-text="unreadCount"></span>
                        </template>
                    </button>

                    <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition.opacity
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl shadow-2xl z-50 overflow-hidden">
                        <div class="p-4 border-b border-[#2D2B26] flex items-center justify-between">
                            <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                                <i class="fa-solid fa-bell text-[#E8451E]"></i> Notifikasi Sistem
                            </h3>
                            <button @click="fetchNotifications()" class="text-xs font-mono text-gray-500 hover:text-[#E8451E]" title="Refresh">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-[#2D2B26]">
                            <template x-for="item in notifications" :key="item.id">
                                <a :href="item.url" class="p-3.5 flex items-start gap-3 hover:bg-[#141311] transition-colors block">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                                         :class="item.bg">
                                        <i class="fa-solid" :class="[item.icon, item.color]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-xs text-white" x-text="item.title"></p>
                                        <p class="text-[11px] text-gray-400 truncate mt-0.5" x-text="item.message"></p>
                                        <span class="text-[9px] font-mono text-gray-500" x-text="item.time"></span>
                                    </div>
                                </a>
                            </template>
                            <template x-if="notifications.length === 0">
                                <div class="p-6 text-center text-gray-500 font-mono text-xs">
                                    Tidak ada notifikasi baru saat ini.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ── PROFILE MENU DROPDOWN ── --}}
                <div class="relative" x-data="{ profileMenu: false }">
                    <button @click="profileMenu = !profileMenu"
                            class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-white/[0.04] transition-colors">
                        <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 border border-[#2D2B26]">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#E8451E] to-[#b33112] text-white font-bold text-xs flex items-center justify-center">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-500"></i>
                    </button>

                    <div x-show="profileMenu" @click.outside="profileMenu = false" x-transition.opacity
                         class="absolute right-0 mt-2 w-64 bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl shadow-2xl z-50 overflow-hidden py-1">
                        {{-- User Header --}}
                        <div class="px-4 py-3 border-b border-[#2D2B26] bg-[#171614]">
                            <p class="font-bold text-xs text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-400 font-mono truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-mono font-bold uppercase {{ Auth::user()->role_badge_class }}">
                                {{ Auth::user()->role_label }}
                            </span>
                        </div>

                        {{-- Links --}}
                        <div class="py-1 text-xs">
                            <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-gray-300 hover:bg-[#E8451E]/10 hover:text-[#E8451E] transition-colors">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs w-4"></i> Lihat Situs Pelanggan
                            </a>
                            <a href="{{ route('dashboard.profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-gray-300 hover:bg-[#E8451E]/10 hover:text-[#E8451E] transition-colors">
                                <i class="fa-solid fa-gear text-xs w-4"></i> Profil & Pengaturan Akun
                            </a>
                        </div>

                        {{-- Logout --}}
                        <div class="border-t border-[#2D2B26] pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full text-left px-4 py-2.5 text-xs text-rose-400 hover:bg-rose-500/10 transition-colors font-medium">
                                    <i class="fa-solid fa-right-from-bracket text-xs w-4"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mx-4 sm:mx-8 mt-6 bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-2xl flex items-center justify-between text-xs">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 sm:mx-8 mt-6 bg-rose-950/80 border border-rose-500/40 text-rose-200 px-4 py-3 rounded-2xl text-xs">
                <div class="flex items-center gap-3 mb-1">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                    <p class="font-semibold">Terdapat kendala input:</p>
                </div>
                <ul class="list-disc list-inside text-rose-300 ml-6 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Page Body --}}
        <main class="p-4 sm:p-8 flex-grow">
            @yield('content')
        </main>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── GLOBAL SEARCH MODAL (Ctrl + K) ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="globalSearch" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md flex items-start justify-center pt-20 p-4">
        <div @click.outside="globalSearch = false"
             class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-xl w-full shadow-2xl overflow-hidden">
            {{-- Search Input --}}
            <div class="p-4 border-b border-[#2D2B26] flex items-center gap-3">
                <i class="fa-solid fa-magnifying-glass text-[#E8451E] text-sm"></i>
                <input type="text" x-ref="searchInput" x-model="searchQuery"
                       @input.debounce.250ms="performSearch()"
                       placeholder="Cari kode booking, nama customer, nomor HP, atau ruangan..."
                       class="w-full bg-transparent text-white text-sm font-sans focus:outline-none placeholder-gray-500">
                <kbd class="font-mono text-[10px] bg-[#141311] border border-[#2D2B26] text-gray-400 px-1.5 py-0.5 rounded">ESC</kbd>
            </div>

            {{-- Results List --}}
            <div class="max-h-96 overflow-y-auto divide-y divide-[#2D2B26] p-2">
                <template x-if="isSearching">
                    <div class="p-6 text-center text-xs font-mono text-gray-500 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-spinner animate-spin text-[#E8451E]"></i> Mencari di basis data...
                    </div>
                </template>

                <template x-for="item in searchResults" :key="item.url + item.title">
                    <a :href="item.url" class="p-3 rounded-xl hover:bg-[#141311] flex items-center gap-3 transition-colors block group">
                        <div class="w-8 h-8 rounded-xl bg-[#2D2B26] flex items-center justify-center text-gray-400 group-hover:text-[#E8451E] shrink-0">
                            <i class="fa-solid text-xs" :class="item.icon"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold uppercase" :class="item.badge_class" x-text="item.badge"></span>
                                <p class="font-bold text-xs text-white truncate" x-text="item.title"></p>
                            </div>
                            <p class="text-[11px] text-gray-400 truncate mt-0.5" x-text="item.subtitle"></p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-600 group-hover:text-white transition-colors"></i>
                    </a>
                </template>

                <template x-if="!isSearching && searchResults.length === 0 && searchQuery.length >= 2">
                    <div class="p-8 text-center text-gray-500 font-mono text-xs">
                        Tidak ada data yang cocok dengan "<span x-text="searchQuery"></span>".
                    </div>
                </template>

                <template x-if="searchQuery.length < 2">
                    <div class="p-6 text-center text-gray-500 font-mono text-[11px]">
                        Ketik minimal 2 huruf untuk mulai mencari di seluruh sistem.
                    </div>
                </template>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
