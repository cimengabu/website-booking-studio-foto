<div x-data="adminSecretPortal()"
     x-cloak
     @open-admin-portal.window="openPortal()"
     @keydown.escape.window="closePortal()"
     class="relative z-[9999]"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-ink/75 backdrop-blur-sm transition-opacity"
         @click="closePortal()"></div>

    {{-- Modal Dialog --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="fixed inset-0 z-[10000] overflow-y-auto p-4 sm:p-6 md:p-20 flex items-center justify-center">

        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-cream-dark overflow-hidden transform transition-all text-ink"
             @click.stop>
            {{-- Header golden accent line --}}
            <div class="h-1.5 w-full bg-gradient-to-r from-gold via-gold-light to-gold"></div>

            <div class="p-6 sm:p-8">
                {{-- Header with icon and close button --}}
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gold/10 text-gold flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-shield-halved text-xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full bg-gold/10 text-gold border border-gold/20">Secret Portal</span>
                                <span class="text-[11px] text-ink-muted">Shortcut: ketik "admin"</span>
                            </div>
                            <h3 class="font-serif font-bold text-xl text-ink mt-0.5" id="modal-title">Control Panel Admin</h3>
                        </div>
                    </div>
                    <button type="button"
                            @click="closePortal()"
                            class="w-8 h-8 rounded-full bg-cream hover:bg-cream-dark text-ink-muted hover:text-ink flex items-center justify-center transition-colors"
                            aria-label="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @auth
                    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        {{-- Logged in as Admin / Staff --}}
                        <div class="bg-cream rounded-2xl p-5 border border-cream-dark mb-6">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-gold text-white font-bold flex items-center justify-center text-base shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-ink text-sm truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-ink-muted truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-teal/10 text-teal uppercase tracking-wider">
                                        Role: {{ Auth::user()->role }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2.5 mb-6">
                            <a href="{{ route('admin.dashboard') }}"
                               class="btn-gold flex items-center justify-center gap-2.5 w-full py-3.5 rounded-xl text-white font-semibold text-sm shadow-warm hover:brightness-105 transition-all">
                                <i class="fa-solid fa-gauge-high"></i>
                                Buka Dashboard Admin
                            </a>

                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <a href="{{ route('admin.bookings.index') }}"
                                   class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-cream-dark hover:border-gold hover:bg-cream text-xs font-medium text-ink transition-all">
                                    <i class="fa-solid fa-calendar-check text-gold"></i> Kelola Booking
                                </a>
                                <a href="{{ route('admin.calendar.index') }}"
                                   class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-cream-dark hover:border-gold hover:bg-cream text-xs font-medium text-ink transition-all">
                                    <i class="fa-solid fa-calendar-days text-teal"></i> Kalender Studio
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- Logged in but not Admin --}}
                        <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200 mb-6 text-xs text-amber-800">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm mt-0.5"></i>
                                <div>
                                    <p class="font-semibold text-amber-900">Akses Dibatasi</p>
                                    <p class="mt-0.5 leading-relaxed">
                                        Akun Anda saat ini (<strong>{{ Auth::user()->email }}</strong>) terdaftar sebagai pelanggan dan tidak memiliki hak akses admin.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full py-3 rounded-xl border border-danger/30 text-danger hover:bg-red-50 text-xs font-semibold transition-colors">
                                    Keluar dari Akun Ini
                                </button>
                            </form>
                            <a href="{{ route('login') }}" class="flex-1 py-3 text-center rounded-xl bg-ink text-white text-xs font-semibold hover:bg-ink-soft transition-colors">
                                Login Akun Lain
                            </a>
                        </div>
                    @endif
                @else
                    {{-- Guest: Fast Inline Login Form or Direct Redirect --}}
                    <p class="text-xs text-ink-muted mb-4 leading-relaxed">
                        Masukkan kredensial akun administrator Anda untuk langsung mengakses panel kontrol SnapSpace.
                    </p>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4 mb-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5" for="admin-portal-email">Email Admin</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-light">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </span>
                                <input type="email"
                                       name="email"
                                       id="admin-portal-email"
                                       required
                                       placeholder="admin@snapspace.id"
                                       class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-cream-dark focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold bg-cream/40">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5" for="admin-portal-password">Kata Sandi</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-light">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </span>
                                <input type="password"
                                       name="password"
                                       id="admin-portal-password"
                                       required
                                       placeholder="••••••••"
                                       class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-cream-dark focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold bg-cream/40">
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-ink-muted">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="remember" value="1" class="rounded border-cream-dark text-gold focus:ring-gold text-xs">
                                <span>Ingat saya</span>
                            </label>
                            <a href="{{ route('admin.dashboard') }}" class="text-gold hover:underline font-medium">Buka Halaman Login Penuh &rarr;</a>
                        </div>

                        <button type="submit"
                                class="btn-gold flex items-center justify-center gap-2 w-full py-3.5 rounded-xl text-white font-semibold text-xs shadow-warm hover:brightness-105 transition-all">
                            <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                            Masuk ke Dashboard Admin
                        </button>
                    </form>
                @endauth

                {{-- Hint footer --}}
                <div class="pt-4 border-t border-cream-dark text-center">
                    <p class="text-[11px] text-ink-light flex items-center justify-center gap-1">
                        <span>💡 Di halaman mana pun, ketik</span>
                        <kbd class="px-1.5 py-0.5 rounded bg-cream border border-cream-dark font-mono text-[10px] text-ink font-semibold">a</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-cream border border-cream-dark font-mono text-[10px] text-ink font-semibold">d</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-cream border border-cream-dark font-mono text-[10px] text-ink font-semibold">m</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-cream border border-cream-dark font-mono text-[10px] text-ink font-semibold">i</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-cream border border-cream-dark font-mono text-[10px] text-ink font-semibold">n</kbd>
                        <span>untuk membuka popup ini.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminSecretPortal', () => ({
            isOpen: false,
            buffer: '',
            secret: 'admin',

            init() {
                window.addEventListener('keydown', (e) => {
                    const active = document.activeElement;
                    const isEditing = active && (
                        active.tagName === 'INPUT' ||
                        active.tagName === 'TEXTAREA' ||
                        active.tagName === 'SELECT' ||
                        active.isContentEditable
                    );

                    // Shortcut: Ctrl + Shift + A or Alt + A
                    if (((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'A' || e.key === 'a')) ||
                        (e.altKey && (e.key === 'A' || e.key === 'a'))) {
                        e.preventDefault();
                        this.openPortal();
                        return;
                    }

                    if (isEditing) {
                        return;
                    }

                    // Process single character keystroke
                    if (e.key && e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                        this.buffer += e.key.toLowerCase();
                        if (this.buffer.length > 20) {
                            this.buffer = this.buffer.slice(-10);
                        }
                        if (this.buffer.endsWith(this.secret)) {
                            this.buffer = '';
                            this.openPortal();
                        }
                    }
                });
            },

            openPortal() {
                this.isOpen = true;
                this.$nextTick(() => {
                    const emailInput = document.getElementById('admin-portal-email');
                    if (emailInput) {
                        emailInput.focus();
                    }
                });
            },

            closePortal() {
                this.isOpen = false;
            }
        }));
    });
</script>
