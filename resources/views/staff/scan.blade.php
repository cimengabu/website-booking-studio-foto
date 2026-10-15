<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Check-In Scanner — SnapSpace Studio</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

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
    <style>
        #reader video {
            border-radius: 1.25rem !important;
            object-fit: cover !important;
            width: 100% !important;
        }
        @keyframes scanLaser {
            0% { top: 10%; opacity: 0.8; }
            50% { top: 85%; opacity: 1; }
            100% { top: 10%; opacity: 0.8; }
        }
        .laser-line {
            animation: scanLaser 2.2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-ink text-white font-sans min-h-screen flex flex-col justify-between antialiased">

    <!-- Header Navigation -->
    <header class="bg-ink-soft border-b border-white/10 px-4 sm:px-8 py-3.5 flex items-center justify-between sticky top-0 z-30 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gold flex items-center justify-center text-white shadow-md">
                <i class="fa-solid fa-qrcode text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-serif font-bold text-lg text-white">SnapSpace</span>
                    <span class="px-2 py-0.5 rounded-full bg-gold/20 text-gold text-[10px] font-bold uppercase tracking-wider">Staff Mode</span>
                </div>
                <span class="text-[11px] text-white/50">Scanner Check-in Studio</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('staff.today') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-xs font-bold text-white transition-colors flex items-center gap-2">
                <i class="fa-solid fa-calendar-day text-gold"></i>
                <span class="hidden sm:inline">Jadwal Sesi Hari Ini</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" title="Keluar" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-danger/20 hover:text-danger text-white/60 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-xl mx-auto px-4 py-8 w-full space-y-6">

        <!-- Title & Status -->
        <div class="text-center space-y-1">
            <h1 class="font-serif font-bold text-2xl sm:text-3xl text-white">Pindai QR E-Ticket</h1>
            <p class="text-xs text-white/60">Arahkan kamera ke QR Code pelanggan saat kedatangan di resepsionis studio.</p>
        </div>

        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-success/20 border border-success/40 text-emerald-200 text-xs font-semibold flex items-center gap-3 animate-bounce">
                <i class="fa-solid fa-circle-check text-xl text-success shrink-0"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-warning/20 border border-warning/40 text-amber-200 text-xs font-semibold flex items-center gap-3">
                <i class="fa-solid fa-circle-info text-xl text-warning shrink-0"></i>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-danger/20 border border-danger/40 text-rose-200 text-xs font-semibold flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-xl text-danger shrink-0"></i>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <!-- Camera Scanner Viewport Card -->
        <div class="bg-ink-soft rounded-3xl p-5 border border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Camera Container -->
            <div class="relative rounded-2xl overflow-hidden bg-black aspect-square flex items-center justify-center border border-white/10">
                <div id="reader" class="w-full h-full"></div>

                <!-- Laser scan animation overlay -->
                <div id="scanOverlay" class="absolute inset-0 pointer-events-none flex flex-col justify-between p-8">
                    <!-- Targeting Brackets -->
                    <div class="flex justify-between">
                        <div class="w-8 h-8 border-t-2 border-l-2 border-gold rounded-tl-lg"></div>
                        <div class="w-8 h-8 border-t-2 border-r-2 border-gold rounded-tr-lg"></div>
                    </div>

                    <!-- Glowing Laser line -->
                    <div class="laser-line w-full h-0.5 bg-gradient-to-r from-transparent via-gold to-transparent shadow-[0_0_12px_#C8A27A]"></div>

                    <div class="flex justify-between">
                        <div class="w-8 h-8 border-b-2 border-l-2 border-gold rounded-bl-lg"></div>
                        <div class="w-8 h-8 border-b-2 border-r-2 border-gold rounded-br-lg"></div>
                    </div>
                </div>
            </div>

            <!-- Scanner Controls -->
            <div class="mt-4 flex items-center justify-between text-xs text-white/70 px-1">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="cameraStatus">Kamera Aktif</span>
                </div>
                <button type="button" id="btnSwitchCamera" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-camera-rotate"></i>
                    <span>Ganti Kamera</span>
                </button>
            </div>
        </div>

        <!-- Manual Input Alternative -->
        <div class="bg-ink-soft rounded-3xl p-6 border border-white/10 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-serif font-bold text-base text-white">Input Manual Kode Booking</h3>
                <span class="text-[10px] text-gold font-bold uppercase tracking-wider">Alternatif</span>
            </div>

            <form id="checkinForm" method="POST" action="{{ route('staff.checkin') }}" class="space-y-3">
                @csrf
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                        <i class="fa-solid fa-ticket text-sm"></i>
                    </div>
                    <input type="text" id="tokenInput" name="token" required
                           class="w-full pl-10 pr-24 py-3 rounded-xl bg-black/40 border border-white/15 focus:border-gold focus:ring-1 focus:ring-gold text-white font-mono text-sm uppercase placeholder-white/30 tracking-wider focus:outline-none transition-all"
                           placeholder="Contoh: BK-20241015-ABC">
                    <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-4 rounded-lg bg-gold hover:bg-gold-dark text-white font-bold text-xs transition-colors flex items-center gap-1.5">
                        <span>Check-In</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </form>
            <p class="text-[11px] text-white/50 leading-relaxed">
                Jika kamera tidak dapat mendeteksi kode QR, masukkan kode booking atau token yang tertera pada e-ticket pelanggan.
            </p>
        </div>

        <!-- Bottom Quick Today Card -->
        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-teal/40 flex items-center justify-center text-teal-light">
                    <i class="fa-solid fa-clipboard-list text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-white">Lihat Antrean & Status Sesi</p>
                    <p class="text-[11px] text-white/50">Cek daftar pelanggan yang sudah hadir hari ini</p>
                </div>
            </div>
            <a href="{{ route('staff.today') }}" class="px-3.5 py-1.5 rounded-xl bg-gold/20 hover:bg-gold/30 text-gold text-xs font-bold transition-colors">
                Buka Jadwal →
            </a>
        </div>

    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-[11px] text-white/40 border-t border-white/5">
        SnapSpace Studio Staff Portal &copy; {{ date('Y') }} — Surabaya
    </footer>

    <!-- Audio Beep for successful scan -->
    <script>
        function playSuccessBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime); // A5 note
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.2);
            } catch (e) {
                console.log('Audio not supported', e);
            }
        }

        // Initialize HTML5 QR Code Scanner
        let html5QrCode = null;
        let currentCameraIndex = 0;
        let cameras = [];

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);
            playSuccessBeep();

            // Stop scanner temporarily
            if (html5QrCode) {
                html5QrCode.pause();
            }

            // Fill input and submit form
            const tokenInput = document.getElementById('tokenInput');
            tokenInput.value = decodedText;
            document.getElementById('checkinForm').submit();
        }

        function onScanFailure(error) {
            // Silence frequent scan failures during active searching
        }

        document.addEventListener('DOMContentLoaded', () => {
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameras = devices;
                    // Prefer back camera if available
                    let cameraId = cameras[0].id;
                    const backCam = cameras.find(c => c.label.toLowerCase().includes('back') || c.label.toLowerCase().includes('rear') || c.label.toLowerCase().includes('environment'));
                    if (backCam) {
                        cameraId = backCam.id;
                        currentCameraIndex = cameras.indexOf(backCam);
                    }

                    html5QrCode = new Html5Qrcode("reader");
                    startScanner(cameraId);
                } else {
                    document.getElementById('cameraStatus').innerText = 'Kamera tidak ditemukan';
                }
            }).catch(err => {
                console.error(err);
                document.getElementById('cameraStatus').innerText = 'Izin kamera dibutuhkan';
            });

            // Switch camera button
            document.getElementById('btnSwitchCamera').addEventListener('click', () => {
                if (cameras.length > 1 && html5QrCode) {
                    html5QrCode.stop().then(() => {
                        currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
                        startScanner(cameras[currentCameraIndex].id);
                    }).catch(err => console.error(err));
                }
            });
        });

        function startScanner(cameraId) {
            html5QrCode.start(
                cameraId,
                {
                    fps: 15,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                document.getElementById('cameraStatus').innerText = 'Kamera Aktif & Memindai';
            }).catch(err => {
                document.getElementById('cameraStatus').innerText = 'Gagal membuka kamera';
            });
        }
    </script>
</body>
</html>
