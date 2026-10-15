@extends('layouts.public')

@section('title', 'Kontak & Lokasi — SnapSpace Studio Surabaya')
@section('meta_description', 'Hubungi SnapSpace Studio Surabaya. Lokasi di Jl. Tunjungan No. 88. Buka setiap hari 09.00-21.00. Chat via WhatsApp atau isi form pesan di sini.')

@section('content')

{{-- ── PAGE HEADER ── --}}
<section class="relative bg-cream-warm py-20 border-b border-cream-dark overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-8">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Kontak</span>
        </nav>
        <div class="max-w-2xl">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Kami Siap Membantu</p>
            <h1 class="font-serif font-bold text-4xl sm:text-5xl text-ink mb-4">Hubungi Kami</h1>
            <p class="text-ink-muted text-lg leading-relaxed">Ada pertanyaan tentang paket, jadwal, atau ingin booking untuk acara khusus? Tim kami siap membantu Anda.</p>
        </div>
    </div>
</section>

{{-- ── CONTENT ── --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            {{-- Left: Contact Info --}}
            <div class="space-y-6">
                <div class="reveal">
                    <h2 class="font-serif font-semibold text-2xl text-ink mb-6">Informasi Kontak</h2>

                    <div class="space-y-4">
                        {{-- Location --}}
                        <div class="flex items-start gap-4 p-5 bg-cream rounded-2xl border border-cream-dark">
                            <div class="w-11 h-11 rounded-xl bg-gold/15 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-gold"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-ink mb-1">Lokasi Studio</p>
                                <p class="text-sm text-ink-muted leading-relaxed">
                                    Jl. Tunjungan No. 88<br>
                                    Genteng, Surabaya, Jawa Timur 60275<br>
                                    <a href="https://maps.google.com" target="_blank" rel="noopener" class="text-gold hover:underline text-xs mt-1 inline-block">
                                        <i class="fa-solid fa-map mr-1"></i> Buka di Google Maps
                                    </a>
                                </p>
                            </div>
                        </div>

                        {{-- Hours --}}
                        <div class="flex items-start gap-4 p-5 bg-cream rounded-2xl border border-cream-dark">
                            <div class="w-11 h-11 rounded-xl bg-teal/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-clock text-teal"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-ink mb-2">Jam Operasional</p>
                                <div class="space-y-1">
                                    @foreach([
                                        ['Senin – Jumat', '09:00 – 21:00 WIB'],
                                        ['Sabtu – Minggu', '08:00 – 22:00 WIB'],
                                        ['Hari Libur Nasional', '09:00 – 20:00 WIB'],
                                    ] as [$day, $time])
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-ink-muted">{{ $day }}</span>
                                            <span class="font-semibold text-ink">{{ $time }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- WhatsApp --}}
                        <a href="https://wa.me/6281234567890?text=Halo%20SnapSpace%2C%20saya%20ingin%20bertanya%20tentang%20studio%20foto." 
                           target="_blank" rel="noopener"
                           class="flex items-center gap-4 p-5 bg-success/5 rounded-2xl border border-success/20 hover:bg-success/10 transition-colors card-lift">
                            <div class="w-11 h-11 rounded-xl bg-success/15 flex items-center justify-center flex-shrink-0">
                                <i class="fa-brands fa-whatsapp text-success text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm text-ink">Chat via WhatsApp</p>
                                <p class="text-sm text-ink-muted">+62 812-3456-7890 (respon cepat)</p>
                            </div>
                            <i class="fa-solid fa-arrow-right text-success text-sm"></i>
                        </a>

                        {{-- Email --}}
                        <div class="flex items-start gap-4 p-5 bg-cream rounded-2xl border border-cream-dark">
                            <div class="w-11 h-11 rounded-xl bg-info/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-envelope text-info"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-ink mb-0.5">Email</p>
                                <a href="mailto:hello@snapspace.id" class="text-sm text-gold hover:underline">hello@snapspace.id</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Google Maps Embed --}}
                <div class="rounded-3xl overflow-hidden border border-cream-dark shadow-card reveal" data-delay="100">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.3!2d112.74!3d-7.26!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sJl.+Tunjungan+Surabaya!5e0!3m2!1sen!2sid!4v1000000000"
                        width="100%"
                        height="280"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi SnapSpace Studio di Google Maps">
                    </iframe>
                </div>
            </div>

            {{-- Right: Contact Form --}}
            <div class="reveal" data-delay="100">
                <div class="bg-cream rounded-3xl p-8 border border-cream-dark shadow-card">
                    <h2 class="font-serif font-semibold text-2xl text-ink mb-2">Kirim Pesan</h2>
                    <p class="text-sm text-ink-muted mb-7">Kami akan membalas dalam 1–2 jam kerja.</p>

                    @if(session('contact_success'))
                        <div class="bg-success/10 border border-success/30 rounded-2xl p-4 mb-6 flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <p class="text-sm font-medium text-success">Pesan Anda berhasil terkirim! Kami akan segera membalas.</p>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_name" class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Nama Lengkap *</label>
                                <input type="text" id="contact_name" name="name" required
                                       value="{{ old('name', Auth::user()->name ?? '') }}"
                                       placeholder="Contoh: Rina Wijaya"
                                       class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all"
                                       autocomplete="name">
                                @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="contact_phone" class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">No. WhatsApp *</label>
                                <input type="tel" id="contact_phone" name="phone" required
                                       value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                       placeholder="08xx-xxxx-xxxx"
                                       class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all"
                                       autocomplete="tel">
                                @error('phone') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="contact_email" class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Email</label>
                            <input type="email" id="contact_email" name="email"
                                   value="{{ old('email', Auth::user()->email ?? '') }}"
                                   placeholder="email@kamu.com"
                                   class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all"
                                   autocomplete="email">
                        </div>

                        <div>
                            <label for="contact_subject" class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Topik Pertanyaan *</label>
                            <select id="contact_subject" name="subject" required
                                    class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-white text-sm text-ink focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all appearance-none">
                                <option value="">-- Pilih topik --</option>
                                <option value="booking" {{ old('subject') === 'booking' ? 'selected' : '' }}>Pertanyaan tentang booking</option>
                                <option value="package" {{ old('subject') === 'package' ? 'selected' : '' }}>Informasi paket & harga</option>
                                <option value="reschedule" {{ old('subject') === 'reschedule' ? 'selected' : '' }}>Reschedule / pembatalan</option>
                                <option value="corporate" {{ old('subject') === 'corporate' ? 'selected' : '' }}>Booking korporat / grup besar</option>
                                <option value="other" {{ old('subject') === 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('subject') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="contact_message" class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Pesan *</label>
                            <textarea id="contact_message" name="message" rows="5" required
                                      placeholder="Tuliskan pertanyaan atau kebutuhan Anda di sini..."
                                      class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-white text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all resize-none leading-relaxed">{{ old('message') }}</textarea>
                            @error('message') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" :disabled="submitting"
                                class="btn-gold btn-bounce w-full flex items-center justify-center gap-2 py-4 rounded-2xl text-white font-bold text-base shadow-warm disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!submitting">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Pesan
                            </span>
                            <span x-show="submitting">
                                <i class="fa-solid fa-spinner animate-spin mr-2"></i> Mengirim...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── SOSIAL MEDIA ── --}}
<section class="py-12 bg-cream border-t border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
        <p class="text-xs font-bold text-ink-muted uppercase tracking-widest mb-4">Ikuti Kami</p>
        <div class="flex gap-4 justify-center">
            @foreach([
                ['fa-instagram', 'bg-gradient-to-br from-purple-500 to-pink-500', '@snapspace.id', 'https://instagram.com'],
                ['fa-tiktok', 'bg-ink', '@snapspacestudio', 'https://tiktok.com'],
                ['fa-youtube', 'bg-red-600', 'SnapSpace Studio', 'https://youtube.com'],
            ] as [$icon, $bg, $handle, $url])
                <a href="{{ $url }}" target="_blank" rel="noopener"
                   class="flex items-center gap-3 px-5 py-3 rounded-2xl {{ $bg }} text-white text-sm font-semibold hover:opacity-90 transition-all shadow-card btn-bounce">
                    <i class="fa-brands fa-{{ $icon }}"></i>
                    <span class="hidden sm:inline">{{ $handle }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
