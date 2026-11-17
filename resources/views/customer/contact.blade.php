@extends('layouts.public')

@section('title', 'Kontak & Lokasi — SnapSpace Studio Klaten')
@section('meta_description', 'Hubungi SnapSpace Studio Klaten. Lokasi di Waru, RT 17 / RW 07, Pandanan, Wonosari, Klaten. Buka setiap hari 09.00-21.00. Chat via WhatsApp atau isi form pesan di sini.')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" integrity="sha512-Zcn6bjR/8RBhbEZq8nHvUcE/VCITgkWFQdaGY/vv9ZGQgrCA/PenPRUMNKzIEaeFoNbMGr221PghhTr7+wtkitg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
        padding: 4px;
    }
    .leaflet-popup-content {
        margin: 10px 12px;
        line-height: 1.4;
    }
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endpush

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
                                    Waru, RT 17 / RW 07<br>
                                    Pandanan, Wonosari, Klaten, Jawa Tengah<br>
                                    <a href="https://www.google.com/maps/search/?api=1&query=Waru+RT+17+RW+07+Pandanan+Wonosari+Klaten" target="_blank" rel="noopener" class="text-gold hover:underline text-xs mt-1 inline-block">
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
                                        ['Setiap Hari (Senin – Minggu)', '09:00 – 21:00 WIB'],
                                        ['Hari Libur Nasional', '09:00 – 21:00 WIB (Tetap Buka)'],
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
                        <a href="https://wa.me/6287825688608?text=Halo%20SnapSpace%2C%20saya%20ingin%20bertanya%20tentang%20studio%20foto." 
                           target="_blank" rel="noopener"
                           class="flex items-center gap-4 p-5 bg-success/5 rounded-2xl border border-success/20 hover:bg-success/10 transition-colors card-lift">
                            <div class="w-11 h-11 rounded-xl bg-success/15 flex items-center justify-center flex-shrink-0">
                                <i class="fa-brands fa-whatsapp text-success text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm text-ink">Chat via WhatsApp</p>
                                <p class="text-sm text-ink-muted">+62 878-2568-8608 (respon cepat)</p>
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
                                <a href="mailto:ind4hwijaya06@gmail.com" class="text-sm text-gold hover:underline">ind4hwijaya06@gmail.com</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map Embed & Interactive Card --}}
                <div class="rounded-3xl overflow-hidden border border-cream-dark shadow-card reveal bg-cream" data-delay="100">
                    <div class="relative w-full h-[300px] bg-cream-dark">
                        {{-- Interactive Leaflet Map Container --}}
                        <div id="studio-map" class="w-full h-full z-0">
                            {{-- Fallback OpenStreetMap iframe while JS initializes or if JS disabled --}}
                            <iframe
                                id="osm-fallback-iframe"
                                src="https://www.openstreetmap.org/export/embed.html?bbox=110.7328%2C-7.6250%2C110.7528%2C-7.6100&amp;layer=mapnik&amp;marker=-7.61747%2C110.74286"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                loading="lazy"
                                title="Peta Lokasi SnapSpace Studio Pandanan Wonosari Klaten">
                            </iframe>
                        </div>
                    </div>
                    <div class="p-4 bg-white border-t border-cream-dark flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gold/15 flex items-center justify-center text-gold shrink-0">
                                <i class="fa-solid fa-location-dot text-base text-gold"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-ink">SnapSpace Studio Klaten</p>
                                <p class="text-[11px] text-ink-muted">Waru, RT 17 / RW 07, Pandanan, Wonosari, Klaten</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="https://www.google.com/maps/search/?api=1&query=Waru+RT+17+RW+07+Pandanan+Wonosari+Klaten"
                               target="_blank" rel="noopener"
                               class="px-3.5 py-1.5 rounded-xl bg-gold hover:bg-gold-dark text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 shrink-0">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <span>Buka Google Maps</span>
                            </a>
                        </div>
                    </div>
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
                            <span x-show="submitting" x-cloak style="display: none;">
                                <i class="fa-solid fa-spinner animate-spin mr-2"></i> Mengirim...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js" integrity="sha512-BwHfrr4c9kmRkLw6AvXEtZP40N42h68hZQD631IEWHUENW/P20afmpz4FTBoH8n61P7vaJS51MfTx5wna3c/Zg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fallback = document.getElementById('osm-fallback-iframe');
        const mapContainer = document.getElementById('studio-map');

        if (typeof L !== 'undefined' && mapContainer) {
            try {
                if (fallback) {
                    fallback.remove();
                }

                const lat = -7.6174726;
                const lon = 110.7428622;

                const map = L.map('studio-map', {
                    center: [lat, lon],
                    zoom: 16,
                    scrollWheelZoom: false
                });

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
                }).addTo(map);

                const customPin = L.divIcon({
                    className: 'custom-studio-pin',
                    html: '<div style="background-color:#C8A27A;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(200,162,122,0.6);border:3px solid #ffffff;"><i class="fa-solid fa-camera" style="color:white;font-size:15px;"></i></div>',
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -22]
                });

                const marker = L.marker([lat, lon], { icon: customPin }).addTo(map);

                marker.bindPopup(`
                    <div style="font-family:'Plus Jakarta Sans',sans-serif;padding:2px;min-width:180px;">
                        <p style="font-weight:700;font-size:13px;margin:0 0 3px;color:#1A1A1A;">SnapSpace Studio</p>
                        <p style="font-size:11px;color:#6B6560;margin:0 0 8px;line-height:1.4;">Waru, RT 17 / RW 07, Pandanan, Wonosari, Klaten</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=Waru+RT+17+RW+07+Pandanan+Wonosari+Klaten" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:4px;background:#C8A27A;color:#fff;font-size:11px;font-weight:700;padding:5px 10px;border-radius:8px;text-decoration:none;">
                            <span>Buka di Google Maps &rarr;</span>
                        </a>
                    </div>
                `).openPopup();

                setTimeout(() => {
                    map.invalidateSize();
                }, 200);
            } catch (e) {
                console.warn('Leaflet initialization failed, fallback remains:', e);
            }
        }
    });
</script>
@endpush
