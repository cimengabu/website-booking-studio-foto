<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — SnapSpace Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Bricolage+Grotesque:wght@700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'], heading: ['Bricolage Grotesque','sans-serif'], mono: ['JetBrains Mono','monospace'] } } }
        }
    </script>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-[#141311] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-[#E8451E] flex items-center justify-center text-white font-heading font-black text-2xl shadow-xl shadow-[#E8451E]/40">S</div>
                <div class="text-left">
                    <span class="block font-heading font-extrabold text-2xl text-white">SnapSpace</span>
                    <span class="block text-[10px] font-mono text-[#F2C94C] uppercase font-bold tracking-wider">Studio Foto Booking</span>
                </div>
            </a>
        </div>

        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-8 shadow-2xl space-y-6">
            <div class="text-center">
                <h1 class="font-heading font-extrabold text-2xl text-white">Buat Akun Baru</h1>
                <p class="text-xs text-gray-400 font-mono mt-1">Gratis selamanya</p>
            </div>

            @if($errors->any())
                <div class="bg-rose-950/60 border border-rose-700/40 text-rose-200 rounded-2xl px-4 py-3 text-xs space-y-1">
                    @foreach($errors->all() as $err)
                        <p><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none transition-colors"
                           placeholder="Budi Santoso">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none transition-colors"
                           placeholder="email@kamu.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-2">Nomor HP (WhatsApp)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none transition-colors"
                           placeholder="0812xxxxxxxx">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none transition-colors"
                           placeholder="Min 8 karakter">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none transition-colors"
                           placeholder="Ulangi password">
                </div>
                <button type="submit" class="w-full py-4 rounded-full bg-[#E8451E] text-white font-extrabold text-sm hover:bg-[#d03a16] shadow-xl shadow-[#E8451E]/30 transition-all mt-2">
                    Daftar Sekarang
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 font-mono">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#E8451E] font-bold hover:underline">Masuk</a>
            </p>
        </div>

        <p class="text-center text-[10px] font-mono text-gray-600 mt-6">
            <a href="{{ route('home') }}" class="hover:text-gray-400">← Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>
