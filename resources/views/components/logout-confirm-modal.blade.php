<div x-data="logoutModalHandler()"
     @open-logout-modal.window="openModal($event.detail)"
     x-show="isOpen"
     x-cloak
     style="display: none;"
     class="fixed inset-0 z-[100001] overflow-y-auto"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop -->
    <div x-show="isOpen"
         x-cloak
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-ink/60 backdrop-blur-sm"
         @click="close()"></div>

    <!-- Modal Box -->
    <div class="min-h-full flex items-center justify-center p-4">
        <div x-show="isOpen"
             x-cloak
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-cream-dark overflow-hidden">

            <!-- Step Progress Indicator -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-cream-dark">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200"
                          :class="step === 1 ? 'bg-gold text-white shadow-warm' : 'bg-success text-white'">
                        <span x-show="step === 1">1</span>
                        <i x-show="step > 1" class="fa-solid fa-check text-[10px]"></i>
                    </span>
                    <div class="w-8 h-0.5 rounded-full" :class="step > 1 ? 'bg-success' : 'bg-cream-dark'"></div>
                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200"
                          :class="step === 2 ? 'bg-danger text-white shadow-md' : 'bg-cream-dark text-ink-muted'">
                        2
                    </span>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted"
                      x-text="'Verifikasi ' + step + ' dari 2'"></span>
            </div>

            <!-- STEP 1: Konfirmasi Awal -->
            <div x-show="step === 1" class="space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-gold/15 text-gold flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>

                <div class="text-center space-y-2">
                    <h3 class="font-serif font-bold text-2xl text-ink">Konfirmasi Keluar Akun</h3>
                    <p class="text-sm text-ink-muted leading-relaxed">
                        Apakah Anda yakin ingin keluar dari sesi akun Anda saat ini?
                    </p>
                </div>

                <div class="bg-cream rounded-2xl p-4 border border-cream-dark text-xs text-ink-muted space-y-1">
                    <div class="flex items-center gap-2 text-ink font-semibold">
                        <i class="fa-solid fa-shield-halved text-gold text-xs"></i>
                        <span>Perlindungan Akun 2 Langkah Aktif</span>
                    </div>
                    <p>Sistem menerapkan konfirmasi ganda (2x) untuk memastikan akun Anda tidak keluar tanpa sengaja.</p>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button"
                            @click="close()"
                            class="w-full py-3 px-4 rounded-xl border border-cream-dark text-ink-soft text-sm font-semibold hover:bg-cream transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            @click="step = 2"
                            class="w-full py-3 px-4 rounded-xl bg-gold hover:bg-gold-dark text-white text-sm font-bold shadow-warm transition-all flex items-center justify-center gap-2">
                        <span>Lanjutkan (1/2)</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Verifikasi Keamanan Terakhir -->
            <div x-show="step === 2" class="space-y-4" style="display: none;">
                <div class="w-14 h-14 rounded-2xl bg-danger/15 text-danger flex items-center justify-center mx-auto text-2xl animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="text-center space-y-2">
                    <h3 class="font-serif font-bold text-2xl text-ink">Verifikasi Terakhir (2/2)</h3>
                    <p class="text-sm text-ink-muted leading-relaxed">
                        Anda harus login kembali dengan email & kata sandi untuk melihat e-ticket dan mengunduh foto studio.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-danger/10 border border-danger/20 text-xs text-danger space-y-1">
                    <p class="font-bold">Apakah Anda benar-benar yakin ingin keluar sekarang?</p>
                    <p class="text-[11px] text-danger/80">Klik tombol di bawah untuk menyelesaikan proses logout akun secara permanen dari sesi ini.</p>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button"
                            @click="step = 1"
                            class="w-full py-3 px-4 rounded-xl border border-cream-dark text-ink-soft text-sm font-semibold hover:bg-cream transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Kembali</span>
                    </button>
                    <button type="button"
                            @click="confirmLogout()"
                            class="w-full py-3 px-4 rounded-xl bg-danger hover:bg-red-700 text-white text-sm font-bold shadow-md transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        <span>Ya, Keluar Sekarang</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function logoutModalHandler() {
        return {
            isOpen: false,
            step: 1,
            targetForm: null,
            openModal(form) {
                this.targetForm = form;
                this.step = 1;
                this.isOpen = true;
            },
            close() {
                this.isOpen = false;
                this.step = 1;
                this.targetForm = null;
            },
            confirmLogout() {
                if (this.targetForm) {
                    this.targetForm.submit();
                } else {
                    const fallbackForm = document.querySelector('form[action="{{ route('logout') }}"]');
                    if (fallbackForm) {
                        fallbackForm.submit();
                    }
                }
            }
        }
    }

    function confirmLogout2X(event, button) {
        event.preventDefault();
        const form = button.closest('form');
        window.dispatchEvent(new CustomEvent('open-logout-modal', { detail: form }));
        return false;
    }
</script>

