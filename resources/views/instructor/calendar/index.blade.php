@extends('layouts.app')

@section('title', __('instructor.my_calendar'))
@section('page_title', __('instructor.my_calendar'))

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $viewerTz = $viewerTz ?? auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
    $fcLocale = $isRtl ? 'ar' : 'en';
    $upcoming = collect($events ?? [])->filter(fn ($e) => ($e->start_date ?? now()) >= now())->take(12);
@endphp

<div class="su-page su-cal-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-calendar-alt su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.my_calendar') }}
            </h1>
            <p class="su-page-head__sub">
                {{ __('instructor.calendar_subtitle') }}
                <strong>{{ \App\Support\AppTimezone::label($viewerTz) }}</strong>
            </p>
        </div>
        <div class="su-stat-pills">
            <div class="su-stat-pill su-soft-1">
                <i class="fas fa-layer-group" aria-hidden="true"></i>
                <span>{{ __('instructor.calendar_total') }}</span>
                <strong>{{ number_format($stats['total'] ?? 0) }}</strong>
            </div>
            <div class="su-stat-pill su-soft-3">
                <i class="fas fa-clock" aria-hidden="true"></i>
                <span>{{ __('instructor.upcoming') }}</span>
                <strong>{{ number_format($stats['upcoming'] ?? 0) }}</strong>
            </div>
        </div>
    </div>

    <div class="su-page-grid su-cal-grid">
        <section class="su-card su-card--flush su-fc" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
            <div id="calendar" class="su-fc__mount"></div>
            <div class="su-legend-row">
                <span><i class="su-legend-dot" style="background:#7c3aed"></i> {{ __('instructor.cal_private') }}</span>
                <span><i class="su-legend-dot" style="background:#1c1c1c"></i> {{ __('instructor.cal_group') }}</span>
                <span><i class="su-legend-dot" style="background:#a8c5da"></i> {{ __('instructor.cal_classroom') }}</span>
                <span><i class="su-legend-dot" style="background:#a1e3cb"></i> {{ __('instructor.cal_consultation') }}</span>
                <span><i class="su-legend-dot" style="background:#ef4444"></i> {{ __('instructor.cal_live') }}</span>
            </div>
        </section>

        <aside class="su-card su-cal-side">
            <h2 class="su-card__title">
                <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                {{ __('instructor.upcoming') }}
            </h2>
            <div class="su-upcoming-list ip-scroll">
                @forelse($upcoming as $event)
                    <a href="{{ $event->url ?? '#' }}" class="su-upcoming-item">
                        <span class="su-upcoming-item__ico" aria-hidden="true">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                        <span class="su-upcoming-item__body">
                            <span class="su-upcoming-item__title">{{ $event->title }}</span>
                            <span class="su-upcoming-item__meta">
                                <x-app-datetime :at="$event->start_date" :timezone="$viewerTz" pattern="D j M · g:i A" />
                            </span>
                        </span>
                        <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }} su-upcoming-item__chev" aria-hidden="true"></i>
                    </a>
                @empty
                    <div class="su-empty">
                        <i class="fas fa-calendar-times" aria-hidden="true"></i>
                        <p>{{ __('instructor.calendar_no_upcoming') }}</p>
                    </div>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var isRtl = @json($isRtl);
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') {
        if (calendarEl) {
            calendarEl.innerHTML = '<p class="su-empty" style="padding:40px;text-align:center">{{ $isRtl ? 'تعذر تحميل التقويم' : 'Calendar failed to load' }}</p>';
        }
        return;
    }

    var isMobile = window.matchMedia('(max-width: 640px)').matches;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: @json($fcLocale),
        direction: isRtl ? 'rtl' : 'ltr',
        timeZone: @json($viewerTz),
        initialView: isMobile ? 'listWeek' : 'timeGridWeek',
        /* FullCalendar v5: left/center/right only (start/end are v6). RTL flips sides. */
        headerToolbar: isRtl
            ? { right: 'prev,next today', center: 'title', left: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
        buttonText: isRtl
            ? { today: 'اليوم', month: 'شهر', week: 'أسبوع', day: 'يوم', list: 'قائمة' }
            : { today: 'Today', month: 'Month', week: 'Week', day: 'Day', list: 'List' },
        events: {
            url: @json(route('instructor.calendar.events')),
            failure: function () {
                console.error('Failed to load calendar events');
            }
        },
        eventClick: function (info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        height: 'auto',
        contentHeight: isMobile ? 480 : 620,
        firstDay: isRtl ? 6 : 0,
        navLinks: true,
        dayMaxEvents: 3,
        nowIndicator: true,
        stickyHeaderDates: true
    });
    calendar.render();

    window.addEventListener('resize', function () {
        var mobile = window.matchMedia('(max-width: 640px)').matches;
        calendar.setOption('contentHeight', mobile ? 480 : 620);
    });
});
</script>
@endpush
