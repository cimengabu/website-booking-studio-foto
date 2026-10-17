@extends('layouts.admin')

@section('page-title', 'Detail Booking — ' . $booking->booking_code)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.bookings.index') }}" class="p-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="font-heading font-bold text-xl text-white">{{ $booking->booking_code }}</h2>
            <p class="text-xs text-gray-400 font-mono">{{ $booking->user->name }} · {{ $booking->booking_date->format('d M Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-6">
            <!-- Booking Info -->
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                <h3 class="font-heading font-bold text-lg text-white border-b border-[#2D2B26] pb-4">Informasi Booking</h3>
                <div class="grid grid-cols-2 gap-4 text-xs font-mono">
                    <div><p class="text-gray-400">Pelanggan</p><p class="font-bold text-white mt-0.5">{{ $booking->user->name }}</p><p class="text-gray-500 text-[10px]">{{ $booking->user->email }} · {{ $booking->user->phone }}</p></div>
                    <div><p class="text-gray-400">Ruangan</p><p class="font-bold text-white mt-0.5">{{ $booking->room->name }}</p><p class="text-gray-500 text-[10px]">{{ $booking->room->theme }}</p></div>
                    <div><p class="text-gray-400">Paket</p><p class="font-bold text-white mt-0.5">{{ $booking->package->name }}</p></div>
                    <div><p class="text-gray-400">Tanggal & Jam</p><p class="font-bold text-[#F2C94C] mt-0.5">{{ $booking->booking_date->format('d M Y') }} · {{ substr($booking->start_time, 0, 5) }}–{{ substr($booking->end_time, 0, 5) }}</p></div>
                    <div><p class="text-gray-400">Jumlah Orang</p><p class="font-bold text-white mt-0.5">{{ $booking->people_count }} orang</p></div>
                    <div><p class="text-gray-400">QR Token</p><p class="font-bold text-white mt-0.5 text-[10px] break-all">{{ $booking->qr_token }}</p></div>
                </div>
                @if($booking->notes)
                    <div class="border-t border-[#2D2B26] pt-4">
                        <p class="text-[10px] text-gray-400 mb-1">Catatan:</p>
                        <p class="text-xs text-gray-200 italic">"{{ $booking->notes }}"</p>
                    </div>
                @endif

                <!-- Reschedule Details -->
                @if($booking->original_booking_date || $booking->reschedule_count > 0)
                    <div class="border-t border-[#2D2B26] pt-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                        <p class="text-xs font-bold text-amber-300 flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-calendar-days"></i> Booking Pernah Di-Reschedule ({{ $booking->reschedule_count }}x)
                        </p>
                        <p class="text-[11px] text-gray-300">
                            Jadwal Asal: <span class="font-bold text-white">{{ $booking->original_booking_date }}</span> jam <span class="font-bold text-white">{{ substr($booking->original_start_time ?? '', 0, 5) }} WIB</span>
                        </p>
                    </div>
                @endif

                <!-- Cancellation / No Show Details -->
                @if($booking->status === 'cancelled' || $booking->status === 'no_show')
                    <div class="border-t border-[#2D2B26] pt-4 p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl space-y-1">
                        <p class="text-xs font-bold text-rose-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-ban"></i> {{ $booking->status === 'no_show' ? 'Pelanggan Tidak Hadir (No-Show)' : 'Booking Dibatalkan' }}
                        </p>
                        @if($booking->cancellation_reason)
                            <p class="text-[11px] text-gray-300">Alasan: <span class="italic text-white">"{{ $booking->cancellation_reason }}"</span></p>
                        @endif
                        <div class="flex gap-4 pt-1 text-[11px]">
                            <span>Refund: <strong class="text-emerald-400">Rp {{ number_format($booking->refund_amount ?? 0, 0, ',', '.') }}</strong></span>
                            <span>Penalti: <strong class="text-rose-400">Rp {{ number_format($booking->penalty_amount ?? 0, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Photographer Assignment Card (RBAC) -->
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                            <i class="fa-solid fa-camera text-amber-400"></i>
                            Penugasan Fotografer (Crew Bertugas)
                        </h3>
                        <p class="text-[11px] text-gray-400 font-mono mt-0.5">Fotografer yang bertugas memandu sesi dan mengunggah hasil foto</p>
                    </div>
                    @if($booking->photographer)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 font-mono text-xs font-bold">
                            <i class="fa-solid fa-user-check text-[10px]"></i> {{ $booking->photographer->name }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#141311] border border-dashed border-[#2D2B26] text-gray-400 font-mono text-xs">
                            <i class="fa-solid fa-user-slash text-[10px]"></i> Belum Ditugaskan
                        </span>
                    @endif
                </div>

                @if(!auth()->user()->isPhotographer())
                    <form method="POST" action="{{ route('admin.bookings.assign-photographer', $booking->id) }}" class="flex flex-col sm:flex-row gap-3 items-end pt-2 border-t border-[#2D2B26]">
                        @csrf @method('PATCH')
                        <div class="w-full sm:flex-grow">
                            <label class="block text-[10px] font-mono font-bold text-gray-400 uppercase mb-1.5">Tugaskan / Ganti Fotografer</label>
                            <select name="photographer_id" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                                <option value="">-- Tanpa Fotografer (Kosongkan) --</option>
                                @foreach($photographers as $ph)
                                    <option value="{{ $ph->id }}" {{ $booking->photographer_id == $ph->id ? 'selected' : '' }}>
                                        {{ $ph->name }} ({{ $ph->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition-all whitespace-nowrap">
                            <i class="fa-solid fa-check mr-1.5"></i> Simpan Penugasan
                        </button>
                    </form>
                @else
                    @if($booking->photographer_id === auth()->id())
                        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-between">
                            <p class="text-xs text-emerald-300 font-mono">
                                <i class="fa-solid fa-circle-check mr-1"></i> Sesi ini telah ditugaskan kepada Anda.
                            </p>
                            <a href="{{ route('admin.photos.upload', $booking->id) }}" class="text-xs font-bold text-emerald-400 hover:underline">
                                Langsung Unggah Foto &rarr;
                            </a>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Update Status Form (Staff & Owner) -->
            @if(!auth()->user()->isPhotographer())
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-sm text-white">Update Status Lifecycle</h3>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-[#141311] border border-[#2D2B26] text-gray-300">
                            Status Saat Ini: <strong class="text-[#F2C94C]">{{ $booking->status_label }}</strong>
                        </span>
                    </div>
                    <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="flex gap-3 items-end">
                        @csrf @method('PATCH')
                        <select name="status" class="flex-grow bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                            @foreach([
                                'pending'     => 'Menunggu Pembayaran',
                                'confirmed'   => 'Dikonfirmasi (Lunas / DP)',
                                'in_progress' => 'Sedang Berlangsung (Sesi)',
                                'completed'   => 'Selesai Sesi Pemotretan',
                                'delivered'   => 'Foto Siap (Delivery)',
                                'closed'      => 'Ditutup (Closed)',
                                'rescheduled' => 'Rescheduled',
                                'cancelled'   => 'Dibatalkan',
                                'no_show'     => 'No-Show (DP Hangus)',
                                'expired'     => 'Kedaluwarsa',
                            ] as $s => $lbl)
                                <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>{{ strtoupper($s) }} — {{ $lbl }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16]">Update Status</button>
                    </form>
                </div>
            @endif

            <!-- Record Cash Payment -->
            @if($booking->remaining_balance > 0)
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <h3 class="font-heading font-bold text-sm text-white">Catat Pembayaran Tunai / EDC (Pelunasan)</h3>
                    <form method="POST" action="{{ route('admin.bookings.pay-cash', $booking->id) }}" class="flex gap-3 items-end">
                        @csrf
                        <div class="flex-grow">
                            <label class="text-[10px] font-mono text-gray-400 mb-1 block">Jumlah (Rp)</label>
                            <input type="number" name="amount" value="{{ $booking->remaining_balance }}" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                        </div>
                        <div>
                            <label class="text-[10px] font-mono text-gray-400 mb-1 block">Metode</label>
                            <input type="text" name="payment_method" value="Cash" class="w-32 bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none">
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-600">Catat</button>
                    </form>
                </div>
            @endif

            <!-- Photo Upload Button -->
            @if(in_array($booking->status, ['confirmed', 'paid', 'dp_paid', 'in_progress', 'checked_in', 'completed', 'delivered', 'closed']))
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-sm text-white">Unggah Hasil Foto Sesi</h3>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $booking->photoResults->count() }} foto sudah diunggah</p>
                    </div>
                    <a href="{{ route('admin.photos.upload', $booking->id) }}" class="px-5 py-2.5 rounded-xl bg-[#1F3A2E] text-[#F2C94C] font-bold text-xs hover:bg-[#152820] flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Foto
                    </a>
                </div>
            @endif
            <!-- Internal Notes (Staff & Owner) -->
            @if(!auth()->user()->isPhotographer())
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-3">
                    <div class="flex items-center justify-between border-b border-[#2D2B26] pb-3">
                        <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                            <i class="fa-solid fa-note-sticky text-[#F2C94C]"></i>
                            Catatan Internal Tim
                        </h3>
                        <span class="text-[9px] text-gray-500 font-mono">Privat (Hanya Staf)</span>
                    </div>
                    <form method="POST" action="{{ route('admin.bookings.notes', $booking->id) }}" class="space-y-3">
                        @csrf
                        <textarea name="internal_notes" rows="3" placeholder="Catatan khusus staf, permintaan lighting, properti tambahan..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E] font-sans">{{ $booking->internal_notes }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-xs font-mono font-bold text-gray-300 hover:text-white transition-all">
                                Simpan Catatan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notification & WhatsApp Dispatcher -->
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <h3 class="font-heading font-bold text-sm text-white border-b border-[#2D2B26] pb-3 flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                        Kirim Ulang Notifikasi
                    </h3>
                    <div class="space-y-2">
                        <form method="POST" action="{{ route('admin.bookings.resend-notification', $booking->id) }}">
                            @csrf
                            <input type="hidden" name="type" value="confirmation">
                            <button type="submit" class="w-full text-left px-3.5 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-emerald-500/40 text-xs font-mono text-gray-300 hover:text-emerald-400 flex items-center justify-between transition-all">
                                <span><i class="fa-solid fa-paper-plane mr-2 text-[10px]"></i> Konfirmasi Booking</span>
                                <i class="fa-solid fa-chevron-right text-[9px] text-gray-600"></i>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.bookings.resend-notification', $booking->id) }}">
                            @csrf
                            <input type="hidden" name="type" value="h1">
                            <button type="submit" class="w-full text-left px-3.5 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-blue-500/40 text-xs font-mono text-gray-300 hover:text-blue-400 flex items-center justify-between transition-all">
                                <span><i class="fa-solid fa-bell mr-2 text-[10px]"></i> Pengingat H-1</span>
                                <i class="fa-solid fa-chevron-right text-[9px] text-gray-600"></i>
                            </button>
                        </form>

                        @if($booking->photoResults->isNotEmpty())
                            <form method="POST" action="{{ route('admin.bookings.resend-notification', $booking->id) }}">
                                @csrf
                                <input type="hidden" name="type" value="photos">
                                <button type="submit" class="w-full text-left px-3.5 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-purple-500/40 text-xs font-mono text-gray-300 hover:text-purple-400 flex items-center justify-between transition-all">
                                    <span><i class="fa-solid fa-images mr-2 text-[10px]"></i> Notifikasi Foto Siap</span>
                                    <i class="fa-solid fa-chevron-right text-[9px] text-gray-600"></i>
                                </button>
                            </form>
                        @endif

                        @php
                            $waPhone = preg_replace('/[^0-9]/', '', $booking->user->phone ?? '');
                            if (str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);
                            $waText = urlencode("Halo {$booking->user->name}, mengenai booking Anda di SnapSpace ({$booking->booking_code}) pada " . $booking->booking_date->format('d M Y') . "...");
                        @endphp
                        @if($waPhone)
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" class="w-full block text-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs font-mono transition-all mt-3">
                                <i class="fa-brands fa-whatsapp text-sm mr-1.5"></i> Chat Klien via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Refund Action (if not cancelled yet and has payment) -->
                @if($booking->paid_amount > 0 && !in_array($booking->status, ['cancelled', 'no_show', 'closed']))
                    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4" x-data="{ openRefund: false }">
                        <div class="flex items-center justify-between border-b border-[#2D2B26] pb-3">
                            <h3 class="font-heading font-bold text-sm text-rose-400 flex items-center gap-2">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                                Pembatalan & Refund
                            </h3>
                            <button @click="openRefund = !openRefund" class="text-xs text-gray-400 hover:text-white font-mono">
                                <span x-text="openRefund ? 'Tutup' : 'Buka Form'"></span>
                            </button>
                        </div>

                        <div x-show="openRefund" x-cloak class="space-y-3">
                            <form method="POST" action="{{ route('admin.bookings.refund', $booking->id) }}" class="space-y-3 text-xs font-mono" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini dan memproses refund?')">
                                @csrf
                                <div>
                                    <label class="text-[10px] text-gray-400 uppercase font-bold block mb-1">Nominal Refund (Rp)</label>
                                    <input type="number" name="refund_amount" value="{{ $booking->paid_amount }}" required min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2 rounded-xl focus:border-[#E8451E] focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-400 uppercase font-bold block mb-1">Nominal Penalti (Rp)</label>
                                    <input type="number" name="penalty_amount" value="0" min="0" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2 rounded-xl focus:border-[#E8451E] focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-400 uppercase font-bold block mb-1">Alasan Pembatalan</label>
                                    <input type="text" name="cancellation_reason" required placeholder="Permintaan pelanggan / keadaan darurat..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2 rounded-xl focus:border-[#E8451E] focus:outline-none">
                                </div>
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-700 hover:bg-rose-600 text-white font-bold text-xs transition-all">
                                    Proses Pembatalan & Refund
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
