<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'SnapSpace — Studio foto mandiri premium di Surabaya. Booking slot real-time, lighting profesional, berbagai tema estetik. Abadikan momen terbaik Anda.')">
    <title>@yield('title', 'SnapSpace Studio — Foto Profesional, Booking Real-Time')</title>

    <!-- Canonical -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Google Fonts: Playfair Display (serif) + Plus Jakarta Sans (sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">

    <!-- Alpine.js -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>

    <!-- Tailwind CSS -->
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <script>
        if (typeof tailwind !== 'undefined') {
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            // Primary — Champagne Gold
                            gold: {
                                DEFAULT: '#C8A27A',
                                light: '#DEC4A0',
                                dark: '#A07C52',
                                50: '#FAF5EE',
                                100: '#F3E8D8',
                            },
                            // Backgrounds
                            cream: {
                                DEFAULT: '#F8F6F3',
                                dark: '#EDE9E3',
                                warm: '#F2EDE6',
                            },
                            // Neutral / Dark
                            ink: {
                                DEFAULT: '#1A1A1A',
                                soft: '#2D2D2D',
                                muted: '#6B6560',
                                light: '#9C9490',
                            },
                            // Secondary accent — Deep Teal
                            teal: {
                                DEFAULT: '#2F4F4F',
                                light: '#3D6464',
                                50: '#EEF4F4',
                            },
                            // Status colors
                            success: '#2E9E6B',
                            warning: '#E5A53B',
                            danger: '#D64545',
                            info: '#3B82F6',
                        },
                        fontFamily: {
                            serif: ['"Playfair Display"', 'Georgia', 'serif'],
                            sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        },
                        fontSize: {
                            'display': ['3rem', { lineHeight: '1.1', letterSpacing: '-0.02em' }],
                            'display-lg': ['3.75rem', { lineHeight: '1.05', letterSpacing: '-0.03em' }],
                        },
                        borderRadius: {
                            'xl': '12px',
                            '2xl': '16px',
                            '3xl': '24px',
                            '4xl': '32px',
                        },
                        boxShadow: {
                            'warm': '0 4px 24px -4px rgba(200, 162, 122, 0.18)',
                            'warm-lg': '0 12px 48px -8px rgba(200, 162, 122, 0.25)',
                            'card': '0 2px 16px -2px rgba(26, 26, 26, 0.08)',
                            'card-hover': '0 8px 40px -8px rgba(26, 26, 26, 0.16)',
                        },
                        animation: {
                            'fade-in': 'fadeIn 0.6s ease forwards',
                            'slide-up': 'slideUp 0.6s ease forwards',
                            'float': 'float 4s ease-in-out infinite',
                            'shimmer': 'shimmer 2.5s linear infinite',
                        },
                        keyframes: {
                            fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                            slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                            float: { '0%, 100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-8px)' } },
                            shimmer: { '0%': { backgroundPosition: '-200% 0' }, '100%': { backgroundPosition: '200% 0' } },
                        },
                    }
                }
            };
        }
    </script>

    <style>
        /* ── Global ──────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #F8F6F3;
            color: #1A1A1A;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Typography ──────────────────────────── */
        h1, h2, h3 { font-family: 'Playfair Display', Georgia, serif; }

        /* ── Custom Scrollbar ─────────────────────── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F8F6F3; }
        ::-webkit-scrollbar-thumb { background: #C8A27A; border-radius: 99px; }

        /* ── Selection ──────────────────────────── */
        ::selection { background: #C8A27A33; color: #1A1A1A; }

        /* ── Grain texture overlay ─────────────── */
        .grain {
            position: relative;
        }
        .grain::after {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.02;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
        }

        /* ── Gold gradient text ─────────────────── */
        .text-gold-gradient {
            background: linear-gradient(135deg, #C8A27A 0%, #DEC4A0 50%, #A07C52 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Underline accent ───────────────────── */
        .underline-gold {
            position: relative;
            display: inline-block;
        }
        .underline-gold::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #C8A27A, #A07C52);
            border-radius: 2px;
        }

        /* ── Card hover lift ────────────────────── */
        .card-lift {
            transition: transform 300ms ease, box-shadow 300ms ease;
        }
        .card-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px -12px rgba(200, 162, 122, 0.22);
        }

        /* ── Button micro-bounce ────────────────── */
        .btn-bounce:active {
            transform: scale(0.96);
        }

        /* ── Gold shimmer button ─────────────────── */
        .btn-gold {
            background: linear-gradient(135deg, #C8A27A 0%, #D4A97B 50%, #C8A27A 100%);
            background-size: 200% auto;
            transition: background-position 0.4s ease, transform 200ms ease, box-shadow 200ms ease;
        }
        .btn-gold:hover {
            background-position: right center;
            box-shadow: 0 8px 24px -6px rgba(200, 162, 122, 0.5);
        }
        .btn-gold:active { transform: scale(0.97); }

        /* ── Section divider ────────────────────── */
        .divider-warm {
            border: none;
            height: 1px;
            background: linear-gradient(90deg, transparent, #C8A27A44, transparent);
        }

        /* ── Skeleton loading ───────────────────── */
        .skeleton {
            background: linear-gradient(90deg, #EDE9E3 25%, #F8F6F3 50%, #EDE9E3 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* ── Sticky booking bar on mobile ───────── */
        @media (max-width: 767px) {
            .sticky-booking-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 40;
                background: white;
                border-top: 1px solid #EDE9E3;
                padding: 12px 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                box-shadow: 0 -4px 24px -4px rgba(26,26,26,0.12);
            }
        }

        /* ── Prefers reduced motion ─────────────── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ── Intersection Observer reveal ───────── */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 600ms ease, transform 600ms ease;
        }
        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Nav link active ────────────────────── */
        .nav-link-active {
            color: #C8A27A;
            position: relative;
        }
        .nav-link-active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 2px;
            background: #C8A27A;
            border-radius: 2px;
        }

        /* ── Floating label input ─────────────── */
        .input-float-group { position: relative; }
        .input-float-group input,
        .input-float-group select,
        .input-float-group textarea {
            padding: 20px 16px 8px;
            width: 100%;
            background: white;
            border: 1.5px solid #EDE9E3;
            border-radius: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            color: #1A1A1A;
            outline: none;
            transition: border-color 200ms ease;
        }
        .input-float-group input:focus,
        .input-float-group select:focus,
        .input-float-group textarea:focus {
            border-color: #C8A27A;
            box-shadow: 0 0 0 3px rgba(200, 162, 122, 0.12);
        }
        .input-float-group label {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            font-size: 14px;
            color: #9C9490;
            pointer-events: none;
            transition: all 200ms ease;
        }
        .input-float-group input:focus + label,
        .input-float-group input:not(:placeholder-shown) + label,
        .input-float-group textarea:focus + label,
        .input-float-group textarea:not(:placeholder-shown) + label {
            top: 10px;
            font-size: 11px;
            color: #C8A27A;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">

    {{-- ── NAVIGATION ──────────────────────────────────────────────────────── --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-cream-dark"
            x-data="{ mobileOpen: false, scrolled: false }"
            x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
            :class="scrolled ? 'shadow-card' : ''">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 py-4">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-gold flex items-center justify-center shadow-warm">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-serif font-bold text-xl text-ink leading-none tracking-tight group-hover:text-gold transition-colors duration-200">SnapSpace</span>
                        <span class="block text-[10px] font-sans font-medium text-ink-light tracking-widest uppercase leading-none mt-0.5">Photo Studio</span>
                    </div>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden lg:flex items-center gap-8">
                    @php
                        $navLinks = [
                            ['route' => 'home', 'label' => 'Beranda'],
                            ['route' => 'packages.index', 'label' => 'Paket & Harga'],
                            ['route' => 'rooms.index', 'label' => 'Ruangan'],
                            ['route' => 'portfolio.index', 'label' => 'Portfolio'],
                            ['route' => 'about', 'label' => 'Tentang Kami'],
                            ['route' => 'contact', 'label' => 'Kontak'],
                        ];
                    @endphp
                    @foreach($navLinks as $link)
                        @if(Route::has($link['route']))
                            <a href="{{ route($link['route']) }}"
                               class="relative text-sm font-medium transition-colors duration-200 pb-1
                                      {{ request()->routeIs($link['route'].'*') ? 'nav-link-active text-gold' : 'text-ink-soft hover:text-gold' }}">
                                {{ $link['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>

                {{-- Desktop Actions --}}
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl border border-cream-dark hover:border-gold transition-colors duration-200 bg-cream">
                                <div class="w-7 h-7 rounded-full bg-teal text-white flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-medium text-ink">{{ Str::limit(Auth::user()->name, 14) }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-ink-light transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-card-hover border border-cream-dark py-2 z-50">
                                <div class="px-4 py-3 border-b border-cream-dark">
                                    <p class="font-semibold text-sm text-ink truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-ink-muted truncate mt-0.5">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink-soft hover:bg-cream hover:text-ink transition-colors">
                                        <i class="fa-solid fa-gauge-high text-gold w-4"></i> Admin Dashboard
                                    </a>
                                @endif
                                <a href="{{ route('my-bookings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink-soft hover:bg-cream hover:text-ink transition-colors">
                                    <i class="fa-solid fa-ticket text-gold w-4"></i> Booking Saya
                                </a>
                                <div class="border-t border-cream-dark mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-danger hover:bg-red-50 transition-colors">
                                            <i class="fa-solid fa-right-from-bracket w-4"></i> Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-ink-soft hover:text-ink transition-colors px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-ink bg-cream-dark hover:bg-cream border border-cream-dark hover:border-gold-light px-4 py-2.5 rounded-xl transition-all duration-200">Daftar</a>
                    @endauth

                    <a href="{{ route('booking.wizard') }}"
                       class="btn-gold btn-bounce inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm shadow-warm">
                        <i class="fa-solid fa-calendar-plus text-xs"></i>
                        Booking Sekarang
                    </a>
                </div>

                {{-- Mobile Hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-xl hover:bg-cream transition-colors" aria-label="Toggle menu">
                    <div class="w-5 space-y-1.5">
                        <span class="block h-0.5 bg-ink rounded transition-transform duration-300" :class="mobileOpen ? 'rotate-45 translate-y-2' : ''"></span>
                        <span class="block h-0.5 bg-ink rounded transition-opacity duration-300" :class="mobileOpen ? 'opacity-0' : ''"></span>
                        <span class="block h-0.5 bg-ink rounded transition-transform duration-300" :class="mobileOpen ? '-rotate-45 -translate-y-2' : ''"></span>
                    </div>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" @click.outside="mobileOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="lg:hidden border-t border-cream-dark bg-white shadow-card-hover">
            <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
                @foreach($navLinks as $link)
                    @if(Route::has($link['route']))
                        <a href="{{ route($link['route']) }}" @click="mobileOpen = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                                  {{ request()->routeIs($link['route'].'*') ? 'bg-gold-100 text-gold font-semibold' : 'text-ink-soft hover:bg-cream hover:text-ink' }}">
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
                <div class="pt-3 border-t border-cream-dark space-y-2">
                    @auth
                        <a href="{{ route('my-bookings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-ink-soft hover:bg-cream">
                            <i class="fa-solid fa-ticket text-gold w-4"></i> Booking Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-danger hover:bg-red-50">
                                <i class="fa-solid fa-right-from-bracket w-4"></i> Keluar
                            </button>
                        </form>
                    @else
                        <div class="flex gap-2">
                            <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium border border-cream-dark text-ink-soft hover:border-gold transition-colors">Masuk</a>
                            <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium border border-cream-dark text-ink hover:border-gold transition-colors">Daftar</a>
                        </div>
                    @endauth
                    <a href="{{ route('booking.wizard') }}"
                       class="btn-gold btn-bounce flex items-center justify-center gap-2 w-full py-3.5 rounded-xl text-white font-semibold text-sm">
                        <i class="fa-solid fa-calendar-plus text-xs"></i> Booking Sekarang
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- ── FLASH MESSAGES ──────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 pt-4 w-full" x-data="{ show: true }" x-show="show" x-transition>
            <div class="bg-white border border-success/30 rounded-2xl px-5 py-3.5 flex items-center justify-between shadow-warm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-success/10 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-success text-sm"></i>
                    </div>
                    <p class="text-sm font-medium text-ink">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-ink-light hover:text-ink transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="max-w-7xl mx-auto px-4 pt-4 w-full">
            <div class="bg-white border border-danger/30 rounded-2xl px-5 py-3.5 shadow-warm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-danger/10 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-danger text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-ink">{{ session('error') ?? 'Terdapat beberapa kendala:' }}</p>
                        @if($errors->any())
                            <ul class="mt-1.5 space-y-0.5">
                                @foreach($errors->all() as $err)
                                    <li class="text-xs text-danger">• {{ $err }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── MAIN CONTENT ─────────────────────────────────────────────────────── --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ── FOOTER ───────────────────────────────────────────────────────────── --}}
    <footer class="bg-ink text-white mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">

                {{-- Brand --}}
                <div class="lg:col-span-1 space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gold flex items-center justify-center shadow-warm">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" fill="white"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-serif font-bold text-xl text-white leading-none">SnapSpace</span>
                            <span class="block text-[10px] text-gold tracking-widest uppercase leading-none mt-0.5">Photo Studio</span>
                        </div>
                    </div>
                    <p class="text-sm text-white/60 leading-relaxed">
                        Studio foto mandiri premium dengan berbagai tema estetik. Abadikan momen terbaik Anda bersama orang-orang tersayang.
                    </p>
                    <div class="flex gap-2">
                        <a href="#" class="w-9 h-9 rounded-xl bg-white/8 hover:bg-gold transition-colors duration-200 flex items-center justify-center" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-white/8 hover:bg-gold transition-colors duration-200 flex items-center justify-center" aria-label="TikTok">
                            <i class="fa-brands fa-tiktok text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-white/8 hover:bg-success transition-colors duration-200 flex items-center justify-center" aria-label="WhatsApp">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                        </a>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="space-y-5">
                    <h4 class="font-serif font-semibold text-base text-white">Navigasi</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['home', 'Beranda'],
                            ['packages.index', 'Paket & Harga'],
                            ['rooms.index', 'Pilih Ruangan'],
                            ['portfolio.index', 'Portfolio Galeri'],
                        ] as [$r, $l])
                            @if(Route::has($r))
                                <li><a href="{{ route($r) }}" class="text-sm text-white/60 hover:text-gold transition-colors duration-200">{{ $l }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                {{-- Info --}}
                <div class="space-y-5">
                    <h4 class="font-serif font-semibold text-base text-white">Informasi</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['about', 'Tentang Kami'],
                            ['contact', 'Kontak & Lokasi'],
                            ['faq', 'FAQ'],
                            ['terms', 'Syarat & Ketentuan'],
                            ['privacy', 'Kebijakan Privasi'],
                            ['booking.check', 'Cek Status Booking'],
                        ] as [$r, $l])
                            @if(Route::has($r))
                                <li><a href="{{ route($r) }}" class="text-sm text-white/60 hover:text-gold transition-colors duration-200">{{ $l }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                {{-- Contact Info --}}
                <div class="space-y-5">
                    <h4 class="font-serif font-semibold text-base text-white">Hubungi Kami</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gold/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-location-dot text-gold text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white/80 uppercase tracking-wide">Lokasi</p>
                                <p class="text-sm text-white/60 mt-0.5">Jl. Tunjungan No. 88<br>Surabaya, Jawa Timur</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gold/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-clock text-gold text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white/80 uppercase tracking-wide">Jam Operasional</p>
                                <p class="text-sm text-white/60 mt-0.5">Setiap Hari: 09.00 – 21.00 WIB</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-success/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-brands fa-whatsapp text-success text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white/80 uppercase tracking-wide">WhatsApp CS</p>
                                <a href="https://wa.me/6281234567890" class="text-sm text-success hover:text-success/80 transition-colors mt-0.5 block">+62 812-3456-7890</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="divider-warm opacity-20 mb-8">

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-white/40">&copy; {{ date('Y') }} SnapSpace Studio. Seluruh hak dilindungi undang-undang.</p>
                <div class="flex items-center gap-4 text-xs text-white/40">
                    <span>Dibuat dengan <i class="fa-solid fa-heart text-gold mx-1"></i> di Surabaya</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- ── MOBILE STICKY BOOKING BAR ─────────────────────────────────────────── --}}
    <div class="sticky-booking-bar lg:hidden">
        <div>
            <p class="text-xs text-ink-muted">Booking studio foto</p>
            <p class="text-sm font-bold text-ink">Mulai Rp 99.000</p>
        </div>
        <a href="{{ route('booking.wizard') }}"
           class="btn-gold btn-bounce flex items-center gap-2 px-5 py-3 rounded-xl text-white font-semibold text-sm whitespace-nowrap">
            <i class="fa-solid fa-calendar-plus text-xs"></i> Booking Sekarang
        </a>
    </div>

    {{-- ── SCRIPTS ──────────────────────────────────────────────────────────── --}}
    <script>
        // Intersection Observer for reveal animations
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        // stagger siblings
                        const delay = entry.target.dataset.delay || 0;
                        entry.target.style.transitionDelay = delay + 'ms';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
            reveals.forEach(el => observer.observe(el));
        });
    </script>
    @stack('scripts')
</body>
</html>
