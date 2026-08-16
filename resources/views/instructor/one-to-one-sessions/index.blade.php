@extends('layouts.app')

@section('title', __('student.one_to_one_sessions_instructor_nav'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $lessonDuration = (int) ($lessonDuration ?? 50);
@endphp
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $isRtl ? 'حصصي الخاصة' : 'Private Lessons' }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $isRtl ? 'جدول اليوم والطلاب المُسنَدين — مدة الحصة 50 دقيقة.' : 'Today’s schedule and assigned students — 50 min lessons.' }}</p>
    </div>

    @if(($newAssignments ?? collect())->isNotEmpty())
    <div class="space-y-3">
        @foreach($newAssignments as $assignment)
            @php
                $student = $assignment->student;
                $age = null;
                if ($student?->birth_date) {
                    $age = $student->birth_date->age;
                }
                $related = ($students ?? collect())->first(fn ($row) => (int) ($row['student']->id ?? 0) === (int) ($student->id ?? 0));
            @endphp
            <section class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 dark:bg-amber-950/30 dark:border-amber-800">
                <p class="text-sm font-black text-amber-900 dark:text-amber-200">🎉 New Student Assigned</p>
                <p class="mt-1 text-sm text-amber-900/90 dark:text-amber-100/90">
                    {{ $student->name ?? 'Student' }} has been assigned to you for private lessons.
                </p>
                <dl class="mt-3 grid sm:grid-cols-2 lg:grid-cols-4 gap-2 text-xs">
                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 px-3 py-2">
                        <dt class="font-bold text-gray-500">{{ $isRtl ? 'الطالب' : 'Student' }}</dt>
                        <dd class="font-extrabold text-gray-900 dark:text-white">{{ $student->name ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 px-3 py-2">
                        <dt class="font-bold text-gray-500">{{ $isRtl ? 'العمر' : 'Age' }}</dt>
                        <dd class="font-extrabold text-gray-900 dark:text-white">{{ $age !== null ? $age : '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 px-3 py-2">
                        <dt class="font-bold text-gray-500">{{ $isRtl ? 'المادة / النطاق' : 'Subject / scope' }}</dt>
                        <dd class="font-extrabold text-gray-900 dark:text-white">{{ $related['course']->title ?? $assignment->scopeLabel() }}</dd>
                    </div>
                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 px-3 py-2">
                        <dt class="font-bold text-gray-500">{{ $isRtl ? 'عدد الحصص' : 'Lessons' }}</dt>
                        <dd class="font-extrabold text-gray-900 dark:text-white">{{ $related['total'] ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 px-3 py-2">
                        <dt class="font-bold text-gray-500">{{ $isRtl ? 'بداية الاشتراك' : 'Plan starts' }}</dt>
                        <dd class="font-extrabold text-gray-900 dark:text-white">{{ optional($related['starts_at'] ?? $assignment->starts_at)->format('Y-m-d') ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 px-3 py-2">
                        <dt class="font-bold text-gray-500">{{ $isRtl ? 'نهاية الاشتراك' : 'Plan ends' }}</dt>
                        <dd class="font-extrabold text-gray-900 dark:text-white">{{ optional($related['ends_at'] ?? $assignment->ends_at)->format('Y-m-d') ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2 rounded-xl bg-white/80 dark:bg-gray-900/50 px-3 py-2">
                        <dt class="font-bold text-gray-500">{{ $isRtl ? 'ملاحظات ولي الأمر' : 'Parent / student notes' }}</dt>
                        <dd class="font-semibold text-gray-800 dark:text-gray-200">{{ $related['notes'] ?? $assignment->notes ?? '—' }}</dd>
                    </div>
                </dl>
            </section>
        @endforeach
    </div>
    @endif

    <section class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base font-black text-gray-900 dark:text-white">{{ $isRtl ? 'جدول اليوم' : "Today's Schedule" }}</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($todaysSchedule ?? [] as $slot)
                @php
                    $dur = (int) ($slot->duration_minutes ?: $lessonDuration);
                    $end = $slot->scheduled_at?->copy()->addMinutes($dur);
                @endphp
                <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-lg font-black text-[#0B3D91] tabular-nums"><x-app-datetime :at="$slot->scheduled_at" pattern="g:i A" /></p>
                        <p class="font-bold text-gray-900 dark:text-white">
                            {{ $slot->course->title ?? 'Private lesson' }}
                            <span class="text-gray-500 font-semibold">— {{ $slot->student->name ?? '—' }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $dur }} {{ $isRtl ? 'دقيقة' : 'min' }}@if($end) · <x-app-datetime :at="$slot->scheduled_at" pattern="g:i A" />–<x-app-datetime :at="$end" pattern="g:i A" />@endif</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-extrabold">🟢 Upcoming</span>
                        <a href="{{ route('instructor.one-to-one-sessions.show', $slot) }}" class="text-sm font-bold text-sky-600 hover:underline">{{ $isRtl ? 'إدارة' : 'Manage' }}</a>
                    </div>
                </div>
            @empty
                <p class="px-5 py-8 text-sm text-gray-500">{{ $isRtl ? 'لا حصص مجدولة اليوم.' : 'No lessons scheduled for today.' }}</p>
            @endforelse
        </div>
    </section>

    @if(($students ?? collect())->isNotEmpty())
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <h2 class="text-sm font-black text-gray-900 dark:text-white mb-3">{{ __('student.one_to_one_instructor_students') }}</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($students as $row)
                <div class="rounded-lg border border-gray-100 dark:border-gray-700 p-3">
                    <p class="font-bold text-gray-900 dark:text-white">{{ $row['student']->name ?? '—' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $row['course']->title ?? '' }}</p>
                    <p class="text-xs mt-2 text-amber-600">{{ $row['pending'] }} {{ $isRtl ? 'بانتظار الجدولة' : 'pending' }} · {{ $row['scheduled'] }} {{ $isRtl ? 'مجدولة' : 'scheduled' }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-600 dark:text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $isRtl ? 'الطالب' : 'Student' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isRtl ? 'المادة' : 'Subject' }}</th>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right">{{ $isRtl ? 'الحالة' : 'Status' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isRtl ? 'الموعد' : 'Time' }}</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $session->student->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $session->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $session->session_number }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-xs font-medium">{{ $session->statusLabel() }}</span></td>
                            <td class="px-4 py-3 text-xs text-gray-500">@if($session->scheduled_at)<x-app-datetime :at="$session->scheduled_at" pattern="Y-m-d H:i" />@else — @endif</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('instructor.one-to-one-sessions.show', $session) }}" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline">{{ $isRtl ? 'إدارة' : 'Manage' }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">{{ __('student.one_to_one_sessions_empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $sessions->links() }}</div>
    </div>
</div>
@endsection
