@extends('layouts.app')

@section('title', 'تقويمي')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
<style>
    .fc { direction: rtl; }
    .fc-toolbar { flex-direction: row-reverse; }
    .fc-button-group { flex-direction: row-reverse; }
    .fc-event { cursor: pointer; border-radius: 4px; padding: 2px 4px; }
    .fc-daygrid-event { white-space: normal; font-size: 0.85rem; }
</style>
@endpush

@section('content')
@php
    $viewerTz = $viewerTz ?? auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
@endphp
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm mb-6 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">تقويمي</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                        حصصك الخاصة والمجموعات والبث — الأوقات بتوقيتك:
                        <strong>{{ \App\Support\AppTimezone::label($viewerTz) }}</strong>
                    </p>
                </div>
                <div class="text-sm text-gray-600 dark:text-slate-300 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-[#0B3D91]"></i>
                    <span>الكل: <strong>{{ $stats['total'] ?? 0 }}</strong> — قادمة: <strong>{{ $stats['upcoming'] ?? 0 }}</strong></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 md:p-6">
                    <div id="calendar" class="calendar-container"></div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-700 flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-slate-300">
                        <span class="inline-flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#7c3aed] inline-block"></span> حصة خاصة</span>
                        <span class="inline-flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#0B3D91] inline-block"></span> مجموعة</span>
                        <span class="inline-flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#F5B800] inline-block"></span> فصل</span>
                        <span class="inline-flex items-center gap-2"><span class="w-4 h-4 rounded bg-emerald-600 inline-block"></span> استشارة</span>
                        <span class="inline-flex items-center gap-2"><span class="w-4 h-4 rounded bg-red-600 inline-block"></span> بث</span>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 md:p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">القادمة</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($events->filter(fn ($e) => ($e->start_date ?? now()) >= now())->take(12) as $event)
                            <a href="{{ $event->url ?? '#' }}" class="block p-3 rounded-lg border border-gray-200 dark:border-slate-600 hover:border-[#0B3D91] transition-colors">
                                <div class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $event->title }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <x-app-datetime :at="$event->start_date" :timezone="$viewerTz" pattern="D j M · g:i A" />
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-6">لا توجد حصص قادمة</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        direction: 'rtl',
        timeZone: @json($viewerTz),
        initialView: 'timeGridWeek',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: { today: 'اليوم', month: 'شهر', week: 'أسبوع', day: 'يوم' },
        events: {
            url: '{{ route("instructor.calendar.events") }}',
            failure: function() { alert('حدث خطأ في تحميل الأحداث'); }
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        },
        height: 'auto',
        contentHeight: 620,
        firstDay: 6,
        navLinks: true,
        dayMaxEvents: 4,
        nowIndicator: true
    });
    calendar.render();
});
</script>
@endpush
