<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Hari Ini — SnapSpace Staff</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Bricolage+Grotesque:wght@700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif'],heading:['Bricolage Grotesque','sans-serif'],mono:['JetBrains Mono','monospace']}}}}</script>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-[#141311] min-h-screen text-white">
<header class="bg-[#1E1D1A] border-b border-[#2D2B26] px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-[#E8451E] flex items-center justify-center font-heading font-black text-white text-xl">S</div>
        <div>
            <span class="font-heading font-bold text-white">SnapSpace</span>
            <span class="block text-[10px] font-mono text-[#F2C94C] uppercase">Staff Mode</span>
        </div>
    </div>
    <a href="{{ route('staff.scan') }}" class="px-4 py-2 rounded-xl bg-[#E8451E] text-white text-xs font-bold hover:bg-[#d03a16]">
        <i class="fa-solid fa-qrcode mr-1.5"></i> Scan QR
    </a>
</header>

<div class="max-w-3xl mx-auto px-6 py-10 space-y-8">
    <div>
        <h1 class="font-heading font-extrabold text-2xl text-white">Jadwal Sesi Hari Ini</h1>
        <p class="text-xs text-gray-400 font-mono mt-1">{{ now()->format('l, d F Y') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 rounded-2xl px-5 py-3 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400"></i> {{ session('success') }}
        </div>
    @endif

    @if($bookings->isEmpty())
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-12 text-center space-y-3">
            <i class="fa-solid fa-sun text-4xl text-[#F2C94C]"></i>
            <h3 class="font-heading font-bold text-xl text-white">Tidak Ada Sesi Hari Ini</h3>
            <p class="text-xs text-gray-400">Studio bebas! Tidak ada booking terjadwal untuk hari ini.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $b)
                @php
                    $statusColors = ['paid'=>'bg-emerald-500/10 border-emerald-700/40 text-emerald-300','dp_paid'=>'bg-blue-500/10 border-blue-700/40 text-blue-300','checked_in'=>'bg-violet-500/10 border-violet-700/40 text-violet-300','completed'=>'bg-gray-500/10 border-gray-700/40 text-gray-300'];
                @endphp
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <p class="font-heading font-bold text-lg text-white">{{ substr($b->start_time, 0, 5) }} – {{ substr($b->end_time, 0, 5) }}</p>
                                <span class="px-2.5 py-1 rounded-full border text-[10px] font-mono font-bold uppercase {{ $statusColors[$b->status] ?? '' }}">{{ $b->status }}</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-300">{{ $b->room->name }}</p>
                            <p class="text-xs font-mono text-gray-400">{{ $b->package->name }} · {{ $b->people_count }} orang</p>
                        </div>
                        <div class="text-right text-xs font-mono">
                            <p class="font-bold text-white">{{ $b->user->name }}</p>
                            <p class="text-gray-400">{{ $b->user->phone }}</p>
                            <p class="text-[#F2C94C] font-bold mt-0.5">{{ $b->booking_code }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        @if(in_array($b->status, ['paid', 'dp_paid']))
                            <form method="POST" action="{{ route('staff.checkin') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="qr_token" value="{{ $b->qr_token }}">
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 text-white font-bold text-xs hover:bg-violet-500 transition-all">
                                    <i class="fa-solid fa-qrcode mr-1.5"></i> Check-In Manual
                                </button>
                            </form>
                        @endif
                        @if($b->status === 'checked_in')
                            <form method="POST" action="{{ route('staff.completed', $b->id) }}" class="flex-1" onsubmit="return confirm('Tandai sesi ini selesai?')">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-600 transition-all">
                                    <i class="fa-solid fa-circle-check mr-1.5"></i> Tandai Selesai
                                </button>
                            </form>
                        @endif
                        @if($b->status === 'completed')
                            <div class="flex-1 py-2.5 rounded-xl bg-gray-800 text-gray-400 font-bold text-xs text-center">
                                <i class="fa-solid fa-check mr-1.5"></i> Sesi Selesai
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
