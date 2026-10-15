<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Sesi Hari Ini — SnapSpace Staff</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: { DEFAULT: '#C8A27A', light: '#DEC4A0', dark: '#A07C52', 50: '#FAF5EE' },
                        cream: { DEFAULT: '#F8F6F3', dark: '#EDE9E3', warm: '#F2EDE6' },
                        ink: { DEFAULT: '#1A1A1A', soft: '#2D2D2D', muted: '#6B6560', light: '#9C9490' },
                        teal: { DEFAULT: '#2F4F4F', light: '#3D6464' },
                        success: '#2E9E6B', warning: '#E5A53B', danger: '#D9534F'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-ink text-white font-sans min-h-screen flex flex-col justify-between antialiased">

    <!-- Header Navigation -->
    <header class="bg-ink-soft border-b border-white/10 px-4 sm:px-8 py-3.5 flex items-center justify-between sticky top-0 z-30 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gold flex items-center justify-center text-white shadow-md">
                <i class="fa-solid fa-calendar-day text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-serif font-bold text-lg text-white">SnapSpace</span>
                    <span class="px-2 py-0.5 rounded-full bg-gold/20 text-gold text-[10px] font-bold uppercase tracking-wider">Staff Mode</span>
                </div>
                <span class="text-[11px] text-white/50">Jadwal Sesi Studio Hari Ini</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('staff.scan') }}" class="px-4 py-2 rounded-xl bg-gold hover:bg-gold-dark text-white text-xs font-bold transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-qrcode"></i>
                <span>Scan QR Masuk</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" title="Keluar" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-danger/20 hover:text-danger text-white/60 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8 w-full space-y-6">

        <!-- Header Info & Date Badge -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-ink-soft rounded-3xl p-6 border border-white/10">
            <div>
                <span class="text-[11px] font-bold text-gold uppercase tracking-widest block mb-1">Operasional Studio</span>
                <h1 class="font-serif font-bold text-2xl sm:text-3xl text-white">Antrean Sesi Hari Ini</h1>
                <p class="text-xs text-white/60 mt-1">
                    <i class="fa-regular fa-calendar mr-1.5 text-gold"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 text-center">
                    <span class="block text-2xl font-serif font-bold text-gold">{{ $bookings->count() }}</span>
                    <span class="text-[10px] text-white/50 uppercase tracking-wider font-semibold">Total Sesi</span>
                </div>
                <div class="px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 text-center">
                    <span class="block text-2xl font-serif font-bold text-success">{{ $bookings->where('status', 'checked_in')->count() }}</span>
                    <span class="text-[10px] text-white/50 uppercase tracking-wider font-semibold">Sedang Foto</span>
                </div>
            </div>
        </div>

        <!-- Flash Alert -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-success/20 border border-success/40 text-emerald-200 text-xs font-semibold flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl text-success shrink-0"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if($bookings->isEmpty())
            <div class="bg-ink-soft rounded-3xl p-12 border border-white/10 text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center mx-auto text-gold">
                    <i class="fa-regular fa-calendar-check text-3xl"></i>
                </div>
                <h3 class="font-serif font-bold text-xl text-white">Tidak Ada Jadwal Hari Ini</h3>
                <p class="text-xs text-white/50 max-w-sm mx-auto">
                    Seluruh slot studio kosong untuk hari ini, atau sesi belum dipesan oleh pelanggan.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($bookings as $b)
                    @php
                        $statusBadge = match($b->status) {
                            'paid' => ['bg-success/20 text-success border-success/30', 'Sudah Bayar / Menunggu Kedatangan', 'fa-clock'],
                            'dp_paid' => ['bg-warning/20 text-warning border-warning/30', 'DP Lunas (Pelunasan di Studio)', 'fa-hand-holding-dollar'],
                            'checked_in' => ['bg-teal/30 text-teal-light border-teal/40', 'Sedang di Studio (Check-in)', 'fa-camera'],
                            'completed' => ['bg-white/10 text-white/60 border-white/15', 'Selesai', 'fa-check-double'],
                            default => ['bg-white/10 text-white/60 border-white/15', ucfirst($b->status), 'fa-circle'],
                        };
                    @endphp

                    <div class="bg-ink-soft rounded-3xl p-6 border border-white/10 hover:border-gold/30 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                        
                        <!-- Left: Time & Booking Info -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono text-xl font-bold text-gold tracking-tight">
                                    {{ substr($b->start_time, 0, 5) }} – {{ substr($b->end_time, 0, 5) }} WIB
                                </span>
                                <span class="px-3 py-1 rounded-full text-[11px] font-semibold border flex items-center gap-1.5 {{ $statusBadge[0] }}">
                                    <i class="fa-solid {{ $statusBadge[2] }} text-[10px]"></i>
                                    <span>{{ $statusBadge[1] }}</span>
                                </span>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-serif font-bold text-lg text-white">{{ $b->user->name }}</h3>
                                    <span class="text-xs text-white/40 font-mono">({{ $b->booking_code }})</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-white/60">
                                    <span><i class="fa-solid fa-door-open text-gold mr-1"></i> {{ $b->room->name }}</span>
                                    <span><i class="fa-solid fa-box text-gold mr-1"></i> {{ $b->package->name }}</span>
                                    <span><i class="fa-solid fa-users text-gold mr-1"></i> {{ $b->people_count }} Orang</span>
                                    @if($b->user->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $b->user->phone) }}" target="_blank" class="text-success hover:underline">
                                            <i class="fa-brands fa-whatsapp mr-0.5"></i> {{ $b->user->phone }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if($b->notes)
                                <div class="p-3 rounded-xl bg-white/5 border border-white/5 text-xs text-white/70 italic">
                                    <i class="fa-solid fa-message text-[10px] text-gold mr-1"></i>
                                    "{{ $b->notes }}"
                                </div>
                            @endif
                        </div>

                        <!-- Right: Actions for Staff -->
                        <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                            @if($b->status === 'paid' || $b->status === 'dp_paid')
                                <form method="POST" action="{{ route('staff.checkin') }}">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $b->booking_code }}">
                                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-teal hover:bg-teal-light text-white text-xs font-bold transition-colors flex items-center gap-2 shadow-sm">
                                        <i class="fa-solid fa-user-check"></i>
                                        <span>Check-In Manual</span>
                                    </button>
                                </form>
                            @elseif($b->status === 'checked_in')
                                <form method="POST" action="{{ route('staff.completed', $b->id) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-success hover:bg-emerald-600 text-white text-xs font-bold transition-colors flex items-center gap-2 shadow-sm">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Tandai Selesai Foto</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-white/40 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-check text-success"></i> Selesai
                                </span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-[11px] text-white/40 border-t border-white/5">
        SnapSpace Studio Staff Portal &copy; {{ date('Y') }} — Surabaya
    </footer>

</body>
</html>
