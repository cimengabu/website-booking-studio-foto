@extends('layouts.admin')

@section('page-title', 'Moderasi Ulasan Pelanggan')

@section('content')
<div class="space-y-8" x-data="{
    replyModal: false,
    replyData: { id: '', name: '', comment: '', existingReply: '' },
    openReply(r) {
        this.replyData = {
            id: r.id,
            name: r.user ? r.user.name : 'Pelanggan',
            comment: r.comment || '',
            existingReply: r.admin_reply || ''
        };
        this.replyModal = true;
    }
}">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-star text-[#F2C94C]"></i>
                Moderasi Ulasan & Balasan Testimoni
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Setujui, sembunyikan, atau berikan tanggapan resmi studio atas ulasan dan rating pelanggan.</p>
        </div>
    </div>

    <!-- Review Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
            <h3 class="text-sm font-heading font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-comments text-[#E8451E]"></i>
                Semua Ulasan Masuk ({{ $reviews->total() }} Ulasan)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3.5">Pelanggan & Booking</th>
                        <th class="text-left px-5 py-3.5">Rating & Komentar</th>
                        <th class="text-left px-5 py-3.5">Balasan Studio</th>
                        <th class="text-center px-5 py-3.5">Visibilitas</th>
                        <th class="text-right px-5 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-[#141311]/50 transition-colors">
                            <td class="px-5 py-4 align-top w-64">
                                <p class="font-bold text-white font-sans text-sm">{{ $review->user->name ?? 'Pelanggan' }}</p>
                                <a href="{{ route('admin.bookings.show', $review->booking_id) }}" class="text-[#F2C94C] hover:underline text-[11px] block mt-0.5">
                                    {{ $review->booking->booking_code ?? 'Booking #' . $review->booking_id }}
                                </a>
                                @if($review->booking && $review->booking->room)
                                    <span class="text-[10px] text-gray-500 font-sans block mt-0.5">{{ $review->booking->room->name }}</span>
                                @endif
                                <span class="text-[9px] text-gray-600 block mt-1">{{ $review->created_at->format('d M Y H:i') }}</span>
                            </td>

                            <td class="px-5 py-4 align-top">
                                <!-- Star Rating -->
                                <div class="flex items-center gap-1 text-amber-400 text-xs mb-1.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-[#F2C94C]' : 'text-gray-700' }}"></i>
                                    @endfor
                                    <span class="text-white font-bold ml-1.5 font-mono text-xs">{{ $review->rating }}.0</span>
                                </div>
                                <p class="text-gray-200 font-sans text-xs leading-relaxed italic">
                                    "{{ $review->comment ?: 'Tidak ada komentar teks.' }}"
                                </p>
                            </td>

                            <td class="px-5 py-4 align-top w-72">
                                @if($review->admin_reply)
                                    <div class="p-3 rounded-xl bg-[#141311] border border-[#2D2B26] space-y-1">
                                        <div class="flex items-center gap-1.5 text-[10px] text-[#E8451E] font-bold">
                                            <i class="fa-solid fa-reply"></i> Tim SnapSpace
                                        </div>
                                        <p class="text-[11px] text-gray-300 font-sans">{{ $review->admin_reply }}</p>
                                        <span class="text-[9px] text-gray-500 block">{{ $review->admin_replied_at?->format('d M Y') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-500 text-[11px] italic">Belum dibalas</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center align-top">
                                @if($review->is_visible)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-eye text-[9px]"></i> Tampil
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        <i class="fa-solid fa-eye-slash text-[9px]"></i> Disembunyikan
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right align-top">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openReply({{ $review }})" class="px-3 py-1.5 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-[#F2C94C] text-[#F2C94C] text-xs font-bold transition-all flex items-center gap-1">
                                        <i class="fa-solid fa-reply text-[10px]"></i> Balas
                                    </button>
                                    <form method="POST" action="{{ route('admin.reviews.toggle', $review->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-gray-400 hover:text-white transition-all text-xs" title="{{ $review->is_visible ? 'Sembunyikan dari Web' : 'Setujui & Tampilkan di Web' }}">
                                            <i class="fa-solid {{ $review->is_visible ? 'fa-eye-slash text-rose-400' : 'fa-check text-emerald-400' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-500">Belum ada ulasan dari pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-[#2D2B26]">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Balas Ulasan -->
    <div x-show="replyModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="replyModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="replyModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Balas Ulasan Pelanggan</h3>
                <button @click="replyModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="p-3.5 rounded-2xl bg-[#141311] border border-[#2D2B26] space-y-1">
                <p class="text-[10px] font-mono font-bold text-gray-400 uppercase" x-text="replyData.name"></p>
                <p class="text-xs text-gray-200 font-sans italic" x-text="'&ldquo;' + replyData.comment + '&rdquo;'"></p>
            </div>

            <form :action="`/admin/reviews/${replyData.id}/reply`" method="POST" class="space-y-4 text-xs font-mono">
                @csrf
                <div>
                    <label class="block text-gray-400 uppercase text-[10px] mb-1 font-bold">Tanggapan Resmi Studio</label>
                    <textarea name="admin_reply" rows="4" x-model="replyData.existingReply" required placeholder="Terima kasih atas kunjungannya ke SnapSpace Studio! Kami sangat senang..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3.5 py-2.5 rounded-xl focus:border-[#E8451E] focus:outline-none font-sans text-xs"></textarea>
                </div>

                <div class="pt-4 border-t border-[#2D2B26] flex justify-end gap-3">
                    <button type="button" @click="replyModal = false" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold hover:bg-[#d03a16]">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
