<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP — SnapSpace Studio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
                        gold: {
                            DEFAULT: '#C8A27A',
                            light: '#DEC4A0',
                            dark: '#A07C52',
                            50: '#FAF5EE',
                            100: '#F3E8D8',
                        },
                        cream: {
                            DEFAULT: '#F8F6F3',
                            dark: '#EDE9E3',
                            warm: '#F2EDE6',
                        },
                        ink: {
                            DEFAULT: '#1A1A1A',
                            soft: '#2D2D2D',
                            muted: '#6B6560',
                            light: '#9C9490',
                        },
                        teal: {
                            DEFAULT: '#2F4F4F',
                            light: '#3D6464',
                            50: '#EEF4F4',
                        },
                        success: '#2E9E6B',
                        warning: '#E5A53B',
                        danger: '#D9534F',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(26, 26, 26, 0.05), 0 2px 6px -1px rgba(26, 26, 26, 0.03)',
                        'warm': '0 8px 30px -4px rgba(200, 162, 122, 0.25)',
                    }
                }
            };
        }
    </script>
</head>
<body class="font-sans bg-cream text-ink antialiased min-h-screen">

<div class="min-h-screen flex flex-col lg:flex-row" x-data="otpForm()">

    <!-- LEFT SIDE: Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-ink overflow-hidden items-end p-12">
        <img src="{{ asset('images/portrait.jpg') }}" alt="SnapSpace Studio" class="absolute inset-0 w-full h-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/70 to-transparent"></div>

        <div class="absolute top-10 left-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gold flex items-center justify-center text-white shadow-warm">
                    <i class="fa-solid fa-camera-retro text-lg"></i>
                </div>
                <div>
                    <span class="block font-serif font-bold text-2xl text-white tracking-tight">SnapSpace</span>
                    <span class="block text-[10px] font-medium text-gold-light uppercase tracking-widest">Self-Photo & Studio Surabaya</span>
                </div>
            </a>
        </div>

        <div class="relative z-10 space-y-5 max-w-lg">
            <div class="w-16 h-16 rounded-2xl bg-gold/20 backdrop-blur-sm border border-gold/30 flex items-center justify-center">
                <i class="fa-solid fa-mobile-screen text-gold text-2xl"></i>
            </div>
            <h2 class="font-serif font-bold text-3xl text-white leading-tight">
                Perlindungan Akun<br>Dua Lapis
            </h2>
            <p class="text-white/70 leading-relaxed text-sm">
                Demi keamanan akun dan data booking Anda, SnapSpace mengirimkan kode verifikasi 6 digit ke nomor HP yang terdaftar setelah beberapa kali percobaan login yang gagal.
            </p>
            <div class="pt-4 border-t border-white/15 space-y-2 text-xs text-white/60">
                <div class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-gold"></i> Kode berlaku 10 menit</div>
                <div class="flex items-center gap-2"><i class="fa-solid fa-clock text-gold"></i> Hanya bisa digunakan 1x</div>
                <div class="flex items-center gap-2"><i class="fa-solid fa-lock text-gold"></i> Terenkripsi & tidak bisa ditebak</div>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: OTP Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 lg:p-16">
        <div class="w-full max-w-md space-y-7">

            <!-- Back Link -->
            <div class="flex items-center justify-between">
                <a href="{{ route('login') }}" class="flex lg:hidden items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-gold flex items-center justify-center text-white shadow-warm">
                        <i class="fa-solid fa-camera-retro text-sm"></i>
                    </div>
                    <span class="font-serif font-bold text-xl text-ink">SnapSpace</span>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-ink-muted hover:text-gold transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Login</span>
                </a>
            </div>

            <!-- Header -->
            <div class="space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-gold/15 flex items-center justify-center">
                    <i class="fa-solid fa-mobile-screen-button text-2xl text-gold"></i>
                </div>
                <span class="inline-block text-xs font-bold text-gold uppercase tracking-widest">Verifikasi Keamanan</span>
                <h1 class="font-serif font-bold text-3xl text-ink">Masukkan Kode OTP</h1>
                <p class="text-sm text-ink-muted leading-relaxed">
                    Karena ada beberapa percobaan login yang gagal, kode verifikasi 6 digit telah dikirim melalui SMS ke nomor HP:
                    <strong class="text-ink font-semibold">{{ session('masked_phone', '****') }}</strong>
                </p>
            </div>

            <!-- OTP Sent Banner -->
            @if(session('otp_sent'))
                <div class="p-4 rounded-2xl bg-success/10 border border-success/20 text-success text-xs font-medium flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-base mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-bold">Kode OTP Terkirim!</p>
                        <p class="mt-0.5 text-success/80">Periksa SMS di nomor HP <strong>{{ session('masked_phone') }}</strong>. Kode berlaku selama 10 menit.</p>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-danger/10 border border-danger/20 text-danger text-xs font-medium flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-base mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-bold">Verifikasi Gagal</p>
                        <p class="mt-0.5">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <!-- OTP Form -->
            <form method="POST" action="{{ route('login.otp.verify') }}" class="space-y-6" @submit="submitting = true">
                @csrf

                <!-- OTP Code Boxes -->
                <div>
                    <label class="block text-xs font-bold text-ink-muted uppercase tracking-wider mb-4">Kode OTP (6 Digit)</label>

                    <!-- Hidden input for form submission -->
                    <input type="hidden" name="otp" x-model="otpValue">

                    <!-- Visual 6-box OTP Input -->
                    <div class="flex gap-2 sm:gap-3 justify-center" id="otp-boxes">
                        @for($i = 0; $i < 6; $i++)
                            <input type="text"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="1"
                                   class="otp-box w-11 h-14 sm:w-12 sm:h-16 text-center text-xl font-bold rounded-xl border-2 border-cream-dark bg-white text-ink focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all shadow-xs"
                                   autocomplete="off"
                                   @input="handleOtpInput($event, {{ $i }})"
                                   @keydown="handleOtpKeydown($event, {{ $i }})"
                                   @paste.prevent="handlePaste($event)"
                                   data-index="{{ $i }}">
                        @endfor
                    </div>

                    <!-- Countdown Timer -->
                    <div class="mt-4 text-center">
                        <p class="text-xs text-ink-muted" x-show="secondsLeft > 0">
                            Kode berlaku selama <span class="font-bold text-gold" x-text="formatTime(secondsLeft)"></span>
                        </p>
                        <p class="text-xs text-danger font-semibold" x-show="secondsLeft <= 0">
                            <i class="fa-solid fa-clock mr-1"></i> Kode OTP telah kedaluwarsa
                        </p>
                    </div>
                </div>

                <!-- Dev Mode: Show OTP hint -->
                @if(config('app.debug'))
                    @php
                        $pendingEmail = session('otp_pending_email');
                        $pendingUser  = $pendingEmail ? \App\Models\User::where('email', $pendingEmail)->first() : null;
                    @endphp
                    @if($pendingUser && $pendingUser->login_otp)
                        <div class="p-3 rounded-xl bg-warning/10 border border-warning/20 text-xs text-warning">
                            <div class="flex items-center gap-2 font-bold mb-1">
                                <i class="fa-solid fa-flask"></i> Mode Debug — Kode OTP:
                            </div>
                            <p class="font-mono font-bold text-lg tracking-[0.5em] text-ink">{{ $pendingUser->login_otp }}</p>
                            <p class="text-warning/70 mt-1">Kode ini hanya tampil di mode development (APP_DEBUG=true)</p>
                        </div>
                    @endif
                @endif

                <!-- Submit Button -->
                <button type="submit"
                        :disabled="otpValue.length < 6 || submitting || secondsLeft <= 0"
                        class="w-full py-3.5 px-6 rounded-2xl bg-gold hover:bg-gold-dark text-white font-bold text-sm shadow-warm hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!submitting"><i class="fa-solid fa-shield-halved mr-1"></i> Verifikasi & Masuk</span>
                    <span x-show="submitting"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Memverifikasi...</span>
                </button>
            </form>

            <!-- Resend OTP -->
            <div class="text-center space-y-2">
                <p class="text-xs text-ink-muted">Tidak menerima SMS?</p>
                <form method="POST" action="{{ route('login.otp.resend') }}" class="inline" @submit="resending = true">
                    @csrf
                    <button type="submit"
                            :disabled="resending || secondsLeft > 540"
                            class="text-xs font-semibold text-gold hover:underline disabled:opacity-40 disabled:cursor-not-allowed disabled:no-underline transition-colors">
                        <span x-show="!resending"><i class="fa-solid fa-rotate-right mr-1"></i> Kirim ulang kode OTP</span>
                        <span x-show="resending"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengirim...</span>
                    </button>
                </form>
                <p class="text-[11px] text-ink-light" x-show="secondsLeft > 540">
                    Kirim ulang tersedia dalam <span class="font-semibold" x-text="formatTime(secondsLeft - 540)"></span>
                </p>
            </div>

            <!-- Footer -->
            <div class="pt-4 border-t border-cream-dark text-center">
                <p class="text-[11px] text-ink-light flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Perlindungan keamanan 2 lapisan — SnapSpace Studio</span>
                </p>
            </div>

        </div>
    </div>

</div>

<script>
    function otpForm() {
        return {
            otpValue: '',
            submitting: false,
            resending: false,
            secondsLeft: 600, // 10 minutes
            timer: null,

            init() {
                this.startTimer();
                this.$nextTick(() => {
                    const firstBox = document.querySelector('.otp-box');
                    if (firstBox) firstBox.focus();
                });
            },

            startTimer() {
                this.timer = setInterval(() => {
                    if (this.secondsLeft > 0) {
                        this.secondsLeft--;
                    } else {
                        clearInterval(this.timer);
                    }
                }, 1000);
            },

            formatTime(secs) {
                const m = Math.floor(secs / 60);
                const s = secs % 60;
                return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            },

            handleOtpInput(event, index) {
                const input = event.target;
                const value = input.value.replace(/\D/g, '');
                input.value = value.slice(-1); // keep only last digit

                this.updateOtpValue();

                // Move to next box
                if (value && index < 5) {
                    const boxes = document.querySelectorAll('.otp-box');
                    boxes[index + 1]?.focus();
                }
            },

            handleOtpKeydown(event, index) {
                if (event.key === 'Backspace') {
                    const boxes = document.querySelectorAll('.otp-box');
                    if (!boxes[index].value && index > 0) {
                        boxes[index - 1].focus();
                        boxes[index - 1].value = '';
                        this.updateOtpValue();
                    }
                }
                if (event.key === 'ArrowLeft' && index > 0) {
                    document.querySelectorAll('.otp-box')[index - 1]?.focus();
                }
                if (event.key === 'ArrowRight' && index < 5) {
                    document.querySelectorAll('.otp-box')[index + 1]?.focus();
                }
            },

            handlePaste(event) {
                const text = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                const boxes = document.querySelectorAll('.otp-box');
                [...text].forEach((char, i) => {
                    if (boxes[i]) boxes[i].value = char;
                });
                if (boxes[text.length - 1]) boxes[text.length - 1].focus();
                this.updateOtpValue();
            },

            updateOtpValue() {
                const boxes = document.querySelectorAll('.otp-box');
                this.otpValue = [...boxes].map(b => b.value).join('');
            }
        }
    }
</script>

</body>
</html>

