<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Resmi — {{ $booking->booking_code }} — SnapSpace Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: { DEFAULT: '#C8A27A', light: '#E8D5C4', dark: '#9B744A' },
                        cream: { DEFAULT: '#FAF7F2', dark: '#F0EBE1' },
                        ink: { DEFAULT: '#1A1A1A', soft: '#4A4A4A', muted: '#8A8A8A' },
                        teal: { DEFAULT: '#2F4F4F', light: '#3D6666' },
                        success: { DEFAULT: '#2E9E6B', light: '#E8F7F0' },
                        danger: { DEFAULT: '#E05252', light: '#FDF0F0' },
                        warning: { DEFAULT: '#E5A43B', light: '#FDF6E9' },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        };
    </script>
    <style>
        @media print {
            body {
                background: white !important;
                color: #1a1a1a !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 1.2cm;
            }
        }

        .stamp-box {
            display: inline-block;
            padding: 8px 18px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 2px;
            font-size: 14px;
            border-radius: 8px;
            position: relative;
            transform: rotate(-3deg);
        }

        .stamp-paid {
            color: #166534;
            border: 3px double #166534;
            background-color: rgba(34, 197, 94, 0.08);
            box-shadow: 0 0 0 2px #166534;
        }

        .stamp-dp {
            color: #9a3412;
            border: 3px double #9a3412;
            background-color: rgba(249, 115, 22, 0.08);
            box-shadow: 0 0 0 2px #9a3412;
        }
    </style>
</head>
<body class="bg-stone-100 font-sans text-ink min-h-screen py-6 sm:py-10 antialiased">

    <!-- Top Action Bar (Screen Only) -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 mb-6 no-print">
        <div class="bg-white rounded-2xl shadow-sm border border-cream-dark p-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isStaff()))
                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold text-ink-soft hover:bg-cream hover:text-ink transition-colors">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Pesanan (Admin)
                    </a>
                @else
                    <a href="{{ route('my-bookings.show', $booking->booking_code) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold text-ink-soft hover:bg-cream hover:text-ink transition-colors">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Pesanan
                    </a>
                @endif
                <span class="text-stone-300">|</span>
                <span class="text-xs font-mono text-ink-muted">INV/{{ $booking->booking_date->format('Ymd') }}/SNAP/{{ substr($booking->booking_code, -6) }}</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-ink text-white text-sm font-bold hover:bg-ink-soft transition-all shadow-sm">
                    <i class="fa-solid fa-print text-gold"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Main Invoice Document -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="invoice-card bg-white rounded-3xl shadow-xl border border-stone-200 p-8 sm:p-12 relative overflow-hidden">

            <!-- Decorative Gold Top Accent -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-gold via-amber-400 to-teal"></div>

            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start pb-8 border-b-2 border-stone-100 gap-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-ink flex items-center justify-center text-gold shadow-sm">
                            <i class="fa-solid fa-camera-retro text-lg"></i>
                        </div>
                        <div>
                            <span class="font-serif font-bold text-2xl text-ink tracking-tight block">SnapSpace Studio</span>
                            <span class="text-[10px] uppercase font-bold tracking-widest text-gold block -mt-1">Creative Photography Space</span>
                        </div>
                    </div>
                    <div class="text-xs text-ink-muted space-y-0.5 leading-relaxed mt-2">
                        <p class="font-medium text-ink">PT SnapSpace Kreatif Indonesia</p>
                        <p>Waru, RT 17 / RW 07, Pandanan, Wonosari, Klaten, Jawa Tengah</p>
                        <p>WhatsApp: +62 878-2568-8608 · Email: billing@snapspace.id</p>
                        <p class="font-mono text-[11px] text-stone-500">NMID: ID1020240988771 · NPWP: 98.765.432.1-604.000</p>
                    </div>
                </div>

                <div class="sm:text-right">
                    <div class="inline-block px-3 py-1 bg-cream-dark/60 rounded-lg text-ink font-bold text-xs uppercase tracking-wider mb-2">
                        BUKTI PEMBAYARAN RESMI
                    </div>
                    <h1 class="font-mono font-bold text-lg sm:text-xl text-ink">
                        INV/{{ $booking->booking_date->format('Ymd') }}/{{ substr($booking->booking_code, -6) }}
                    </h1>
                    <div class="text-xs text-ink-muted space-y-1 mt-2">
                        <p>Tanggal Terbit: <span class="font-semibold text-ink">{{ now()->translatedFormat('d F Y') }}</span></p>
                        <p>Kode Booking: <span class="font-mono font-bold text-gold">{{ $booking->booking_code }}</span></p>
                        <p>Metode: <span class="font-medium text-ink uppercase">{{ $booking->payments->last()?->payment_type ?? 'QRIS / Online Gateway' }}</span></p>
                    </div>

                    <!-- Stamp -->
                    <div class="mt-4">
                        @if($booking->paid_amount >= $booking->total_price)
                            <div class="stamp-box stamp-paid">
                                <div class="text-center">
                                    <div class="text-[10px] tracking-wider font-semibold">SNAPSPACE KLATEN</div>
                                    <div class="text-base font-black">★ LUNAS ★</div>
                                    <div class="text-[9px] tracking-widest">{{ $booking->payments->last()?->paid_at?->format('d/m/Y H:i') ?? date('d/m/Y') }} WIB</div>
                                </div>
                            </div>
                        @elseif($booking->paid_amount > 0)
                            <div class="stamp-box stamp-dp">
                                <div class="text-center">
                                    <div class="text-[10px] tracking-wider font-semibold">SNAPSPACE KLATEN</div>
                                    <div class="text-sm font-black">★ DP 50% TERIMA ★</div>
                                    <div class="text-[9px] tracking-widest">SISA Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="stamp-box text-amber-700 border-2 border-amber-500 bg-amber-50/50">
                                <div class="text-xs font-bold">MENUNGGU PEMBAYARAN</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer & Session Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6 border-b border-stone-100 text-xs">
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-ink-muted mb-2">Tagihan Ditujukan Kepada:</h3>
                    <div class="bg-cream/40 rounded-2xl p-4 border border-cream-dark/60 space-y-1">
                        <p class="text-sm font-bold text-ink">{{ $booking->user->name }}</p>
                        <p class="text-ink-muted"><i class="fa-regular fa-envelope text-gold mr-1.5 w-3.5"></i> {{ $booking->user->email }}</p>
                        <p class="text-ink-muted"><i class="fa-solid fa-phone text-gold mr-1.5 w-3.5"></i> {{ $booking->user->phone ?? 'Nomor telepon belum diatur' }}</p>
                        <p class="text-ink-muted"><i class="fa-regular fa-id-card text-gold mr-1.5 w-3.5"></i> Member ID: #USR-{{ str_pad($booking->user->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-ink-muted mb-2">Detail Pemesanan Sesi:</h3>
                    <div class="bg-cream/40 rounded-2xl p-4 border border-cream-dark/60 space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-ink-muted">Ruangan Studio:</span>
                            <span class="font-bold text-ink">{{ $booking->room->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-muted">Paket Layanan:</span>
                            <span class="font-bold text-ink">{{ $booking->package->name }} ({{ $booking->package->duration_minutes }} Menit)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-muted">Waktu Sesi:</span>
                            <span class="font-semibold text-ink">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-muted">Jam Sesi:</span>
                            <span class="font-mono font-semibold text-gold">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-muted">Peserta:</span>
                            <span class="font-medium text-ink">{{ $booking->people_count }} Orang (Maks standar {{ $booking->package->max_people }} orang)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itemized Table -->
            <div class="py-6">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-ink-muted mb-3">Rincian Item Pembayaran:</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-y-2 border-stone-200 bg-stone-50 font-bold text-ink-soft">
                                <th class="py-3 px-3 w-10 text-center">No</th>
                                <th class="py-3 px-3">Deskripsi Layanan / Item</th>
                                <th class="py-3 px-3 text-center w-16">Qty</th>
                                <th class="py-3 px-3 text-right w-28">Harga Satuan</th>
                                <th class="py-3 px-3 text-right w-32">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <!-- Base Package -->
                            <tr>
                                <td class="py-3.5 px-3 text-center text-ink-muted font-mono">01</td>
                                <td class="py-3.5 px-3">
                                    <p class="font-bold text-ink">Paket Foto: {{ $booking->package->name }}</p>
                                    <p class="text-[11px] text-ink-muted mt-0.5">
                                        Durasi {{ $booking->package->duration_minutes }} menit di {{ $booking->room->name }} · Kuota dasar {{ $booking->package->max_people }} orang
                                    </p>
                                </td>
                                <td class="py-3.5 px-3 text-center font-mono">1</td>
                                <td class="py-3.5 px-3 text-right font-mono text-ink-muted">
                                    Rp {{ number_format($booking->package->price, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono font-semibold text-ink">
                                    Rp {{ number_format($booking->package->price, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Extra People if any -->
                            @php
                                $extraPeople = max(0, $booking->people_count - $booking->package->max_people);
                                $extraPersonRate = 25000;
                                $itemNo = 2;
                            @endphp
                            @if($extraPeople > 0)
                                <tr>
                                    <td class="py-3.5 px-3 text-center text-ink-muted font-mono">{{ str_pad($itemNo++, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-bold text-ink">Biaya Tambahan Peserta (Extra People)</p>
                                        <p class="text-[11px] text-ink-muted mt-0.5">
                                            Kelebihan kuota dasar {{ $extraPeople }} orang (Rp {{ number_format($extraPersonRate, 0, ',', '.') }}/orang)
                                        </p>
                                    </td>
                                    <td class="py-3.5 px-3 text-center font-mono">{{ $extraPeople }}</td>
                                    <td class="py-3.5 px-3 text-right font-mono text-ink-muted">
                                        Rp {{ number_format($extraPersonRate, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-mono font-semibold text-ink">
                                        Rp {{ number_format($extraPeople * $extraPersonRate, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif

                            <!-- Addons -->
                            @foreach($booking->bookingAddons as $ba)
                                <tr>
                                    <td class="py-3.5 px-3 text-center text-ink-muted font-mono">{{ str_pad($itemNo++, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-bold text-ink">Add-On: {{ $ba->addon->name }}</p>
                                        <p class="text-[11px] text-ink-muted mt-0.5">{{ $ba->addon->description ?? 'Layanan tambahan studio' }}</p>
                                    </td>
                                    <td class="py-3.5 px-3 text-center font-mono">{{ $ba->qty }}</td>
                                    <td class="py-3.5 px-3 text-right font-mono text-ink-muted">
                                        Rp {{ number_format($ba->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-mono font-semibold text-ink">
                                        Rp {{ number_format($ba->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Peak Hour / Weekend Surcharge if any -->
                            @if($booking->markup_amount > 0)
                                <tr>
                                    <td class="py-3.5 px-3 text-center text-ink-muted font-mono">{{ str_pad($itemNo++, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-bold text-ink">Penyesuaian Jadwal (Peak Hour / Weekend Surcharge)</p>
                                        <p class="text-[11px] text-ink-muted mt-0.5">Penyesuaian tarif sesi akhir pekan / slot ramai</p>
                                    </td>
                                    <td class="py-3.5 px-3 text-center font-mono">1</td>
                                    <td class="py-3.5 px-3 text-right font-mono text-ink-muted">
                                        Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-mono font-semibold text-amber-700">
                                        +Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Financial Calculations Breakdown -->
            <div class="border-t-2 border-stone-200 pt-5">
                <div class="flex flex-col sm:flex-row justify-between gap-6">

                    <!-- Left: Notes & Verification QR -->
                    <div class="sm:w-1/2 flex items-start gap-4">
                        <div class="w-24 h-24 p-1.5 border border-stone-200 rounded-xl bg-white shadow-xs flex-shrink-0">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('ticket.show', $booking->booking_code)) }}"
                                 alt="QR Validasi Nota" class="w-full h-full object-contain">
                        </div>
                        <div class="text-[11px] text-ink-muted space-y-1">
                            <p class="font-bold text-ink">Validasi Keaslian Dokumen</p>
                            <p class="leading-relaxed">
                                Pindai QR code ini untuk memverifikasi keabsahan nota langsung dari server resmi SnapSpace Studio. Dokumen ini sah dan diakui secara digital tanpa tanda tangan basah berdasarkan UU ITE.
                            </p>
                        </div>
                    </div>

                    <!-- Right: Detailed Totals & Discount -->
                    <div class="sm:w-5/12 space-y-2 text-xs">
                        <div class="flex justify-between text-ink-muted">
                            <span>Subtotal Layanan:</span>
                            <span class="font-mono text-ink">Rp {{ number_format($booking->subtotal + $booking->addons_total, 0, ',', '.') }}</span>
                        </div>

                        @if($booking->markup_amount > 0)
                            <div class="flex justify-between text-ink-muted">
                                <span>Biaya Tambahan Jadwal:</span>
                                <span class="font-mono text-amber-700">+Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- POTONGAN DISKON RESMI --}}
                        @if($booking->discount > 0)
                            <div class="flex justify-between items-center bg-emerald-50 text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 font-medium">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-tags text-[10px]"></i>
                                    <span>Diskon Voucher Promo {{ $booking->promo_code ? '('.$booking->promo_code.')' : '' }}</span>
                                </span>
                                <span class="font-mono font-bold">-Rp {{ number_format($booking->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between py-2 border-t-2 border-stone-200 text-sm font-bold text-ink">
                            <span>Total Tagihan:</span>
                            <span class="font-mono text-base text-ink">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between text-emerald-700 bg-emerald-50/50 px-3 py-1.5 rounded-lg border border-emerald-100">
                            <span class="font-semibold">Jumlah Dibayar:</span>
                            <span class="font-mono font-bold">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($booking->remaining_balance > 0)
                            <div class="flex justify-between text-rose-700 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-200 font-bold">
                                <span>Sisa Pelunasan:</span>
                                <span class="font-mono">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-[10px] text-ink-muted text-right italic">*Sisa tagihan dilunasi sebelum sesi foto berlangsung.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Log Table -->
            @if($booking->payments->isNotEmpty())
                <div class="mt-8 pt-6 border-t border-stone-100">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-ink-muted mb-3">Catatan Transaksi Pembayaran:</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[11px] border-collapse bg-stone-50 rounded-xl overflow-hidden">
                            <thead>
                                <tr class="border-b border-stone-200 text-ink-muted font-semibold">
                                    <th class="py-2.5 px-3">No. Transaksi (Order ID)</th>
                                    <th class="py-2.5 px-3">Kategori</th>
                                    <th class="py-2.5 px-3">Kanal Pembayaran</th>
                                    <th class="py-2.5 px-3">Waktu Bayar</th>
                                    <th class="py-2.5 px-3 text-right">Nominal</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                @foreach($booking->payments as $pay)
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono text-ink font-semibold">{{ $pay->order_id }}</td>
                                        <td class="py-2.5 px-3 uppercase text-ink-muted font-medium">{{ $pay->payment_kind === 'dp' ? 'Uang Muka (DP)' : ($pay->payment_kind === 'remaining' ? 'Pelunasan' : 'Bayar Penuh') }}</td>
                                        <td class="py-2.5 px-3 uppercase font-medium text-ink">{{ $pay->payment_type ?? 'QRIS' }}</td>
                                        <td class="py-2.5 px-3 text-ink-muted">{{ $pay->paid_at ? $pay->paid_at->translatedFormat('d M Y, H:i') : '-' }} WIB</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-ink">Rp {{ number_format($pay->gross_amount, 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                BERHASIL
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Footer & Terms -->
            <div class="mt-8 pt-6 border-t border-stone-100 grid grid-cols-1 md:grid-cols-2 gap-6 items-end text-xs text-ink-muted">
                <div class="space-y-1.5 leading-relaxed text-[11px]">
                    <p class="font-bold text-ink uppercase tracking-wider text-[10px]">Ketentuan Transaksi & Layanan:</p>
                    <ul class="list-disc list-inside space-y-1 text-stone-600">
                        <li>Harap hadir di lokasi studio minimal 15 menit sebelum jam sesi pemotretan.</li>
                        <li>Tunjukkan E-Tiket atau Bukti Pembayaran ini saat verifikasi kedatangan di meja resepsionis.</li>
                        <li>Sesuai regulasi studio, jika ada sisa pembayaran dapat diselesaikan melalui QRIS atau kartu di kasir.</li>
                    </ul>
                </div>

                <div class="text-center sm:text-right flex flex-col items-center sm:items-end">
                    <p class="text-[11px] text-ink-muted mb-1">Klaten, {{ now()->translatedFormat('d F Y') }}</p>
                    <div class="relative w-40 h-20 flex items-center justify-center my-1">
                        <!-- Digital Seal Graphic -->
                        <div class="absolute inset-0 border-2 border-dashed border-gold/40 rounded-xl bg-gold/5 flex items-center justify-center">
                            <span class="text-[9px] font-bold text-gold/80 tracking-widest uppercase text-center leading-tight">
                                PT SNAPSPACE KREATIF<br>DIGITALLY SIGNED<br>FINANCE DEPT
                            </span>
                        </div>
                    </div>
                    <p class="font-bold text-ink text-xs">SnapSpace Studio Administration</p>
                    <p class="text-[10px] text-ink-muted">Sistem Billing Terverifikasi Otomatis</p>
                </div>
            </div>

        </div>

        <!-- Print Footer Note -->
        <p class="text-center text-xs text-stone-400 mt-6 no-print">
            Dokumen ini di-generate otomatis oleh Sistem Billing SnapSpace Studio pada {{ now()->translatedFormat('d F Y H:i:s') }} WIB.
        </p>
    </div>

</body>
</html>

