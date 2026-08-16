@extends('layouts.app')

@section('title', __('instructor.dashboard_title'))

@push('styles')
<style>
    .ins-dash { --ins-blue:#0B3D91; --ins-dark:#072A66; --ins-gold:#F5B800; --ins-canvas:#F4F7FC; --ins-line:#E8EEF8; --ins-muted:#5B6577; }
    .ins-panel {
        background: #fff;
        border: 1px solid var(--ins-line);
        border-radius: 18px;
    }
    .dark .ins-panel { background: #111827; border-color: #1f2937; }
    .ins-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 999px;
        font-size: 11px; font-weight: 800;
        background: #EEF3FB; color: var(--ins-blue);
    }
    .ins-action {
        display: flex; flex-direction: column; gap: 10px;
        padding: 16px; border-radius: 16px;
        border: 1px solid var(--ins-line); background: #fff;
        text-decoration: none !important; color: inherit;
        transition: border-color .15s, transform .15s, box-shadow .15s;
        min-height: 108px;
    }
    .ins-action:hover {
        border-color: #C5D4EF; transform: translateY(-2px);
        box-shadow: 0 12px 28px -16px rgba(11,61,145,.25);
    }
    .dark .ins-action { background: #111827; border-color: #1f2937; }
    .ins-action__icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #EEF3FB; color: var(--ins-blue); font-size: 15px;
    }
    .ins-action--gold .ins-action__icon { background: #FFF6D6; color: #8A6A00; }
    .ins-action--warn .ins-action__icon { background: #FFF1E8; color: #B45309; }
    .ins-row {
        display: flex; align-items: center; gap: 12px;
        padding: 12px; border-radius: 14px;
        border: 1px solid transparent;
        transition: background .15s, border-color .15s;
        text-decoration: none !important; color: inherit;
    }
    .ins-row:hover { background: var(--ins-canvas); border-color: var(--ins-line); }
    .dark .ins-row:hover { background: #1f2937; border-color: #334155; }
</style>
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $pending = (int) ($stats['pending_submissions'] ?? 0);
@endphp

<div class="ins-dash space-y-5">
    {{-- Greeting --}}
    <section class="ins-panel overflow-hidden">
        <div class="relative px-5 py-5 sm:px-6 sm:py-6">
            <div class="absolute inset-y-0 {{ $isRtl ? 'left-0' : 'right-0' }} w-40 sm:w-56 pointer-events-none opacity-90"
                 style="background: radial-gradient(ellipse at center, rgba(245,184,0,0.22), transparent 70%);"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div class="min-w-0">
                    <span class="ins-chip mb-3">
                        <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                        {{ config('app.name') }} · {{ __('instructor.instructor_panel') }}
                    </span>
                    <h1 class="font-heading text-2xl sm:text-[28px] font-black tracking-tight text-[#0B1220] dark:text-white leading-tight">
                        {{ __('instructor.welcome') }}، {{ auth()->user()->name }}
                    </h1>
                    <p class="mt-1.5 text-sm text-[color:var(--ins-muted)] dark:text-gray-400 max-w-xl">
                        {{ $isRtl ? 'ابدأ من التقويم — كل حصصك بتوقيتك، ثم المجموعات والبث والكورسات.' : 'Start from your calendar — every class in your timezone, then groups, live, and courses.' }}
                    </p>
                    <p class="mt-2 text-xs text-[color:var(--ins-muted)] flex items-center gap-2">
                        <i class="fas fa-calendar-day opacity-60"></i>
                        <time datetime="{{ now()->toIso8601String() }}">{{ now()->translatedFormat('l، d F Y') }}</time>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(Route::has('instructor.calendar'))
                    <a href="{{ route('instructor.calendar') }}"
                       class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#F5B800] px-4 text-sm font-bold text-[#072A66] hover:brightness-105">
                        <i class="fas fa-calendar-alt text-xs"></i> {{ $isRtl ? 'تقويمي' : 'My calendar' }}
                    </a>
                    @endif
                    <a href="{{ route('instructor.lectures.create') }}"
                       class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#0B3D91] px-4 text-sm font-bold text-white hover:brightness-110">
                        <i class="fas fa-video text-xs"></i> {{ __('instructor.add_lecture') }}
                    </a>
                    <a href="{{ route('instructor.assignments.create') }}"
                       class="inline-flex h-10 items-center gap-2 rounded-xl border border-[#E8EEF8] dark:border-gray-600 bg-white dark:bg-gray-800 px-4 text-sm font-bold text-[#0B1220] dark:text-white hover:border-[#0B3D91]/40">
                        <i class="fas fa-tasks text-xs"></i> {{ __('instructor.add_assignment') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Next live tutoring --}}
    @if(!empty($upcomingTutoringBooking))
        <section class="rounded-2xl overflow-hidden border border-[#0B3D91]/20 bg-[#0B3D91] text-white shadow-[0_18px_40px_-20px_rgba(11,61,145,0.55)]">
            <div class="px-5 py-5 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-[#F5B800]">{{ $isRtl ? 'حصتك المباشرة القادمة' : 'Next live session' }}</p>
                    <h2 class="mt-1 text-xl font-black truncate">{{ $upcomingTutoringBooking->tutoringGroup?->title ?? ($isRtl ? 'حصة مجموعة' : 'Group session') }}</h2>
                    <p class="mt-1 text-sm text-white/75">
                        <i class="far fa-clock ml-1"></i>
                        <x-app-datetime :at="$upcomingTutoringBooking->starts_at" pattern="D j M · g:i A" />
                        @if($upcomingTutoringBooking->user)
                            · {{ $upcomingTutoringBooking->user->name }}
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($upcomingTutoringBooking->classroomMeeting)
                        <a href="{{ url('classroom/join/'.$upcomingTutoringBooking->classroomMeeting->code) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-[#F5B800] px-4 py-2.5 text-sm font-extrabold text-[#072A66] hover:brightness-105">
                            <i class="fas fa-video"></i> {{ $isRtl ? 'دخول Live' : 'Join Live' }}
                        </a>
                    @endif
                    @if(Route::has('instructor.tutoring-bookings.show'))
                        <a href="{{ route('instructor.tutoring-bookings.show', $upcomingTutoringBooking) }}"
                           class="inline-flex items-center rounded-xl border border-white/30 px-4 py-2.5 text-sm font-bold text-white hover:bg-white/10">
                            {{ $isRtl ? 'التفاصيل' : 'Details' }}
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @else
        <section class="ins-panel px-5 py-4 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm font-bold text-[#0B1220] dark:text-white">{{ $isRtl ? 'لا توجد حصة مجموعة قادمة' : 'No upcoming group session' }}</p>
                <p class="text-xs text-[color:var(--ins-muted)] mt-0.5">{{ $isRtl ? 'راجع الحجوزات والدفعات الجماعية عند توفرها.' : 'Check bookings and cohorts when available.' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(Route::has('instructor.tutoring-bookings.index'))
                    <a href="{{ route('instructor.tutoring-bookings.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#0B3D91] px-4 text-sm font-bold text-white">
                        <i class="fas fa-calendar-check text-xs"></i> {{ $isRtl ? 'الحجوزات' : 'Bookings' }}
                    </a>
                @endif
            </div>
        </section>
    @endif

    {{-- Quick actions --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @if(Route::has('instructor.calendar'))
        <a href="{{ route('instructor.calendar') }}" class="ins-action ins-action--gold">
            <span class="ins-action__icon"><i class="fas fa-calendar-alt"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'تقويمي' : 'My calendar' }}</p>
                <p class="text-[11px] text-[color:var(--ins-muted)] mt-0.5">{{ $isRtl ? 'جدول الحصص بتوقيتك' : 'Classes in your timezone' }}</p>
            </div>
        </a>
        @endif
        @if(Route::has('instructor.tutoring-bookings.index'))
        <a href="{{ route('instructor.tutoring-bookings.index') }}" class="ins-action ins-action--gold">
            <span class="ins-action__icon"><i class="fas fa-users"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'حجوزات المجموعات' : 'Group bookings' }}</p>
                <p class="text-[11px] text-[color:var(--ins-muted)] mt-0.5">{{ ($stats['upcoming_tutoring'] ?? 0) }} {{ $isRtl ? 'قادمة' : 'upcoming' }}</p>
            </div>
        </a>
        @endif
        @if(Route::has('instructor.live-sessions.index'))
        <a href="{{ route('instructor.live-sessions.index') }}" class="ins-action">
            <span class="ins-action__icon"><i class="fas fa-broadcast-tower"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'البث المباشر' : 'Live broadcast' }}</p>
                <p class="text-[11px] text-[color:var(--ins-muted)] mt-0.5">{{ $isRtl ? 'إدارة البث' : 'Manage streams' }}</p>
            </div>
        </a>
        @endif
        <a href="{{ route('instructor.courses.index') }}" class="ins-action">
            <span class="ins-action__icon"><i class="fas fa-book-open"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ __('instructor.my_courses') }}</p>
                <p class="text-[11px] text-[color:var(--ins-muted)] mt-0.5">{{ $stats['my_courses'] }} {{ $isRtl ? 'كورس' : 'courses' }}</p>
            </div>
        </a>
        <a href="{{ route('instructor.assignments.index') }}" class="ins-action {{ $pending > 0 ? 'ins-action--warn' : '' }}">
            <span class="ins-action__icon"><i class="fas fa-tasks"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ __('instructor.assignments') }}</p>
                <p class="text-[11px] text-[color:var(--ins-muted)] mt-0.5">
                    @if($pending > 0)
                        {{ $pending }} {{ __('instructor.need_grading') }}
                    @else
                        {{ $isRtl ? 'لا تقييمات معلّقة' : 'Nothing pending' }}
                    @endif
                </p>
            </div>
        </a>
    </section>

    {{-- Quiet stats --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="ins-panel px-4 py-4 hover:border-[#0B3D91]/25 transition-colors block">
            <p class="text-[11px] font-bold text-[color:var(--ins-muted)]">{{ __('instructor.my_courses') }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B3D91] dark:text-blue-300 tabular-nums">{{ number_format($stats['my_courses']) }}</p>
        </a>
        <a href="{{ route('instructor.courses.index') }}" class="ins-panel px-4 py-4 hover:border-[#0B3D91]/25 transition-colors block">
            <p class="text-[11px] font-bold text-[color:var(--ins-muted)]">{{ __('instructor.total_students') }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B1220] dark:text-white tabular-nums">{{ number_format($stats['total_students']) }}</p>
        </a>
        <a href="{{ route('instructor.lectures.index') }}" class="ins-panel px-4 py-4 hover:border-[#0B3D91]/25 transition-colors block">
            <p class="text-[11px] font-bold text-[color:var(--ins-muted)]">{{ __('instructor.lectures') }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B1220] dark:text-white tabular-nums">{{ number_format($stats['total_lectures']) }}</p>
            @if(($stats['upcoming_lectures'] ?? 0) > 0)
                <p class="text-[10px] font-bold text-[#0B3D91] mt-1">{{ $stats['upcoming_lectures'] }} {{ __('instructor.upcoming') }}</p>
            @endif
        </a>
        <a href="{{ route('instructor.assignments.index') }}" class="ins-panel px-4 py-4 hover:border-[#0B3D91]/25 transition-colors block">
            <p class="text-[11px] font-bold text-[color:var(--ins-muted)]">{{ __('instructor.need_grading') }}</p>
            <p class="mt-1 text-2xl font-black text-[#8A6A00] tabular-nums">{{ number_format($pending) }}</p>
        </a>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Courses --}}
        <section class="lg:col-span-2 ins-panel min-w-0">
            <div class="flex items-center justify-between gap-3 px-5 pt-5">
                <h2 class="font-heading text-base font-extrabold text-[#0B1220] dark:text-white">{{ __('instructor.my_recent_courses') }}</h2>
                <a href="{{ route('instructor.courses.index') }}" class="text-xs font-bold text-[#0B3D91] dark:text-blue-300">{{ __('instructor.view_all') }}</a>
            </div>
            <div class="p-3 sm:p-4 space-y-1">
                @forelse($my_courses as $course)
                    <a href="{{ route('instructor.courses.show', $course) }}" class="ins-row">
                        <div class="w-10 h-10 rounded-xl bg-[#EEF3FB] text-[#0B3D91] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-sm text-[#0B1220] dark:text-white truncate">{{ $course->title }}</h3>
                            <p class="text-[11px] text-[color:var(--ins-muted)] mt-0.5 truncate">
                                {{ $course->active_students_count ?? 0 }} {{ __('instructor.student_single') }}
                                @if($course->academicSubject) · {{ $course->academicSubject->name }} @endif
                            </p>
                        </div>
                        <i class="fas fa-chevron-left text-gray-300 dark:text-gray-600 text-xs flex-shrink-0"></i>
                    </a>
                @empty
                    <div class="text-center py-10 px-4">
                        <p class="font-bold text-[#0B1220] dark:text-white">{{ __('instructor.no_courses_assigned') }}</p>
                    </div>
                @endforelse
            </div>
        </section>

        <div class="space-y-5 min-w-0">
            {{-- Upcoming lectures --}}
            <section class="ins-panel">
                <div class="flex items-center justify-between px-4 pt-4">
                    <h3 class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ __('instructor.upcoming_lectures') }}</h3>
                    <a href="{{ route('instructor.lectures.index') }}" class="text-[11px] font-bold text-[#0B3D91]">{{ __('instructor.view_all') }}</a>
                </div>
                <div class="p-3 space-y-2">
                    @forelse($upcoming_lectures->take(3) as $lecture)
                        <a href="{{ route('instructor.lectures.show', $lecture) }}" class="block rounded-xl border border-[color:var(--ins-line)] dark:border-gray-700 px-3 py-2.5 hover:border-[#0B3D91]/30 transition-colors">
                            <p class="text-xs font-bold text-[#0B1220] dark:text-white truncate">{{ $lecture->title }}</p>
                            <p class="text-[11px] text-[color:var(--ins-muted)] truncate mt-0.5">{{ $lecture->course->title ?? __('instructor.not_specified') }}</p>
                            <p class="text-[10px] font-bold text-[#0B3D91] mt-1.5">{{ $lecture->scheduled_at->diffForHumans() }}</p>
                        </a>
                    @empty
                        <p class="text-center text-xs text-[color:var(--ins-muted)] py-6">{{ __('instructor.no_lectures') }}</p>
                    @endforelse
                </div>
            </section>

            {{-- Pending grading --}}
            <section class="ins-panel">
                <div class="flex items-center justify-between px-4 pt-4 gap-2">
                    <h3 class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ __('instructor.assignments_need_grading') }}</h3>
                    @if($pending > 0)
                        <span class="ins-chip">{{ $pending }}</span>
                    @endif
                </div>
                <div class="p-3 space-y-2">
                    @forelse($pending_assignments->take(3) as $submission)
                        <a href="{{ route('instructor.assignments.submissions', $submission->assignment) }}" class="block rounded-xl border border-[color:var(--ins-line)] dark:border-gray-700 px-3 py-2.5 hover:border-[#F5B800]/50 transition-colors">
                            <p class="text-xs font-bold text-[#0B1220] dark:text-white truncate">{{ $submission->assignment->title ?? __('instructor.assignment_default') }}</p>
                            <p class="text-[11px] text-[color:var(--ins-muted)] truncate mt-0.5">{{ $submission->student->name ?? __('instructor.student_single') }} · {{ $submission->created_at->diffForHumans() }}</p>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <div class="w-10 h-10 rounded-xl bg-[#EEF8F0] text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <p class="text-xs text-[color:var(--ins-muted)]">{{ __('instructor.all_assignments_graded') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
