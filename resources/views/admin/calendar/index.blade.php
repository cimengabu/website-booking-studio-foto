@extends('layouts.admin')
@section('page-title', 'Kalender Studio')

@section('content')
<div class="space-y-6">
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-heading font-black text-xl text-white flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-[#E8451E]"></i>
                    Kalender Jadwal Studio Terpadu
                </h3>
                <p class="text-xs text-gray-400 font-mono mt-0.5">
                    Tampilan Bulan, Minggu, dan Hari. Geser (Drag-and-drop) event untuk reschedule otomatis atau klik slot untuk booking manual.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.rooms.index') }}" class="px-4 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-amber-500/50 text-amber-300 font-bold text-xs font-mono transition-all flex items-center gap-2">
                    <i class="fa-solid fa-ban text-xs"></i> Blok Libur / Maintenance
                </a>
                <a href="{{ route('admin.bookings.walkin') }}" class="px-4 py-2 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs font-mono transition-all flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-plus text-xs"></i> Booking Walk-In
                </a>
            </div>
        </div>

        <!-- Status & Room Color Legend -->
        <div class="flex flex-wrap items-center gap-4 p-3 bg-[#141311] border border-[#2D2B26] rounded-2xl text-[11px] font-mono">
            <span class="text-gray-500 uppercase text-[9px] font-bold">Keterangan:</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Terkonfirmasi</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> DP Terbayar</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Menunggu Bayar</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Checked-In</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-500"></span> Selesai</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-700"></span> Tutup / Maintenance</span>
        </div>

        <div id="calendar" class="rounded-2xl overflow-hidden"></div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<style>
    .fc { --fc-border-color: #2D2B26; --fc-page-bg-color: #141311; --fc-neutral-bg-color: #1E1D1A; --fc-list-event-hover-bg-color: #2D2B26; --fc-today-bg-color: rgba(232,69,30,0.08); }
    .fc .fc-toolbar-title { color: #F3EFE6; font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.25rem; font-weight: 800; }
    .fc .fc-button { background: #1E1D1A !important; border-color: #2D2B26 !important; color: #9CA3AF !important; border-radius: 0.5rem !important; font-size: 11px !important; }
    .fc .fc-button:hover { border-color: #E8451E !important; color: #E8451E !important; }
    .fc .fc-button-primary:not(.fc-button-active):focus { box-shadow: none !important; }
    .fc th { color: #6B7280; font-size: 11px; font-family: 'JetBrains Mono', monospace; font-weight: bold; text-transform: uppercase; border-color: #2D2B26 !important; }
    .fc td { border-color: #2D2B26 !important; }
    .fc .fc-daygrid-day-number { color: #9CA3AF; font-size: 12px; font-family: 'JetBrains Mono', monospace; }
    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number { color: #E8451E; font-weight: bold; }
    .fc-event { border-radius: 6px !important; font-size: 11px !important; font-family: 'JetBrains Mono', monospace; padding: 2px 6px; border: none !important; cursor: pointer; }
    .fc .fc-scrollgrid { border-color: #2D2B26 !important; border-radius: 12px; overflow: hidden; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        editable: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: '{{ route("admin.calendar.events") }}',
        eventClick: function(info) {
            const props = info.event.extendedProps;
            if (props.type === 'blocked') {
                alert("Jadwal Tutup Studio / Maintenance:\n" + props.reason);
                return;
            }
            if (props.booking_id) {
                window.location.href = `/admin/bookings/${props.booking_id}`;
            }
        },
        dateClick: function(info) {
            if (confirm("Buat Booking Walk-In baru untuk tanggal " + info.dateStr + "?")) {
                window.location.href = `{{ route('admin.bookings.walkin') }}?date=${info.dateStr}`;
            }
        },
        eventDrop: async function(info) {
            const bId = info.event.extendedProps.booking_id;
            if (!bId) {
                info.revert();
                return;
            }

            const newDate = info.event.start.toISOString().split('T')[0];
            const hours = String(info.event.start.getHours()).padStart(2, '0');
            const minutes = String(info.event.start.getMinutes()).padStart(2, '0');
            const newTime = `${hours}:${minutes}:00`;

            if (!confirm(`Pindahkan jadwal booking ke tanggal ${newDate} jam ${hours}:${minutes} WIB?`)) {
                info.revert();
                return;
            }

            try {
                const res = await fetch(`/admin/calendar/reschedule/${bId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        date: newDate,
                        start_time: newTime
                    })
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                } else {
                    alert("Gagal memindahkan jadwal.");
                    info.revert();
                }
            } catch (err) {
                alert("Terjadi kesalahan jaringan.");
                info.revert();
            }
        }
    });
    calendar.render();
</script>
@endpush
@endsection
