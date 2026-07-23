@extends('layouts.app') @section('title', 'Agenda') @section('content')
<div class="row mb-4"> <div class="col-12 d-flex justify-content-between align-items-center"> <h2 class="fw-bold text-primary mb-0"><i class="fa-solid fa-calendar-alt me-2"></i> Agenda de Horários</h2> <a href="{{ route('appointments.create') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm"> <i class="fa-solid fa-plus me-1"></i> Novo Agendamento (Balcão) </a> </div>
</div> <div class="card shadow-sm border-0 rounded-4"> <div class="card-body p-4"> @if(session('success')) <div class="alert alert-success alert-dismissible fade show" role="alert"> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div> @endif <!-- FullCalendar Element --> <div id="calendar"></div> </div>
</div> <!-- Scripts FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var isMobile = window.innerWidth < 768;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobile ? 'timeGridDay' : 'timeGridWeek',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: isMobile ? 'timeGridDay,listWeek' : 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        events: '/appointments/api',
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        windowResize: function(view) {
            if (window.innerWidth < 768) {
                calendar.changeView('timeGridDay');
            } else {
                calendar.changeView('timeGridWeek');
            }
        }
    });
    calendar.render();
});
</script> 
<style> 
/* Pequeno ajuste para as cores do FullCalendar combinarem com o Bootstrap 5 */ 
.fc-theme-standard td, .fc-theme-standard th { border-color: #dee2e6; } 
.fc-col-header-cell-cushion, .fc-daygrid-day-number { text-decoration: none; color: #495057; } 
.fc-event { cursor: pointer; border: none; border-radius: 4px; padding: 2px; } 
.fc-timegrid-event .fc-event-main { padding: 4px; }

/* Melhorias para dispositivos móveis */
@media (max-width: 767px) {
    .fc-header-toolbar {
        flex-direction: column;
        gap: 12px;
    }
    .fc-toolbar-title {
        font-size: 1.25rem !important;
        text-align: center;
    }
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        width: 100%;
    }
}
</style>
@endsection

