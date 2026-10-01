@extends('layouts.admin')
@section('page-title', 'Kalender Studio')

@section('content')
<div class="space-y-6">
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-heading font-bold text-lg text-white">Kalender Semua Ruangan</h3>
                <p class="text-xs text-gray-400 font-mono">Klik tanggal untuk detail booking</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach($rooms as $room)
                    <span class="flex items-center gap-2 text-xs font-mono">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $room->color ?? '#E8451E' }}"></span>
                        {{ $room->name }}
                    </span>
                @endforeach
            </div>
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
    .fc-event { border-radius: 6px !important; font-size: 11px !important; font-family: 'JetBrains Mono', monospace; padding: 2px 6px; border: none !important; }
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
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: '{{ route("admin.calendar.events") }}',
        eventClick: function(info) {
            const bId = info.event.extendedProps.booking_id;
            if (bId) window.location.href = `/admin/bookings/${bId}`;
        },
        eventMouseEnter: function(info) {
            info.el.style.cursor = 'pointer';
            info.el.style.opacity = '0.85';
        },
        eventMouseLeave: function(info) {
            info.el.style.opacity = '1';
        },
    });
    calendar.render();
</script>
@endpush
@endsection
