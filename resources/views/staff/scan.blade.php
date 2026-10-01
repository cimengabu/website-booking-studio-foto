<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Check-In Scanner — SnapSpace</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Bricolage+Grotesque:wght@700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode:'class', theme:{ extend:{ fontFamily:{ sans:['Plus Jakarta Sans','sans-serif'], heading:['Bricolage Grotesque','sans-serif'], mono:['JetBrains Mono','monospace'] } } } }
    </script>
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
    <a href="{{ route('staff.today') }}" class="px-4 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-xs font-bold text-gray-300 hover:border-[#E8451E]">
        <i class="fa-solid fa-calendar-day mr-1.5"></i> Jadwal Hari Ini
    </a>
</header>

<div class="max-w-lg mx-auto px-6 py-12 space-y-8">
    <div class="text-center space-y-2">
        <h1 class="font-heading font-extrabold text-3xl text-white">Scan QR Check-In</h1>
        <p class="text-xs text-gray-400 font-mono">Arahkan kamera ke QR tiket pelanggan</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 rounded-3xl p-6 text-center space-y-2">
            <i class="fa-solid fa-circle-check text-4xl text-emerald-400"></i>
            <p class="font-bold text-lg">{{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-950/80 border border-rose-500/40 text-rose-200 rounded-3xl p-5 text-center">
            <i class="fa-solid fa-circle-exclamation text-2xl text-rose-400 mb-2"></i>
            <p class="text-sm">{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- Manual Input --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
        <h3 class="font-heading font-bold text-base text-white">Input Manual Kode / Token QR</h3>
        <form method="POST" action="{{ route('staff.checkin') }}" class="flex gap-3">
            @csrf
            <input type="text" name="qr_token" required autofocus
                   class="flex-grow bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm font-mono focus:outline-none uppercase"
                   placeholder="SNAP-XXXXXXXX atau token QR...">
            <button type="submit" class="px-5 py-3 rounded-xl bg-[#E8451E] text-white font-bold text-sm hover:bg-[#d03a16] shadow-lg">
                <i class="fa-solid fa-qrcode"></i>
            </button>
        </form>
        <p class="text-[10px] text-gray-500 font-mono">Ketik kode booking (SNAP-XXXX) atau token QR dari tiket pelanggan.</p>
    </div>

    {{-- Quick Link to Today --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-bold text-white">Jadwal Sesi Hari Ini</p>
            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ now()->format('l, d M Y') }}</p>
        </div>
        <a href="{{ route('staff.today') }}" class="px-5 py-2.5 rounded-xl bg-[#1F3A2E] text-[#F2C94C] font-bold text-xs hover:bg-[#152820] flex items-center gap-2">
            <i class="fa-solid fa-calendar-day"></i> Lihat Jadwal
        </a>
    </div>
</div>
</body>
</html>
