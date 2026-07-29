@extends('layouts.app')

@section('title', __('student.dashboard_title'))

@push('styles')
<style>
    .stu-dash { --stu-blue:#0B3D91; --stu-dark:#072A66; --stu-gold:#F5B800; --stu-canvas:#F4F7FC; --stu-line:#E8EEF8; --stu-muted:#5B6577; }
    .stu-panel {
        background: #fff;
        border: 1px solid var(--stu-line);
        border-radius: 18px;
    }
    .dark .stu-panel { background: #111827; border-color: #1f2937; }
    .stu-progress {
        height: 8px; border-radius: 999px; background: var(--stu-line); overflow: hidden;
    }
    .dark .stu-progress { background: #334155; }
    .stu-progress > span {
        display: block; height: 100%; border-radius: 999px;
        background: linear-gradient(90deg, var(--stu-gold), #FFD24D);
    }
    .stu-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 999px;
        font-size: 11px; font-weight: 800;
        background: #EEF3FB; color: var(--stu-blue);
    }
    .stu-action {
        display: flex; flex-direction: column; gap: 10px;
        padding: 16px; border-radius: 16px;
        border: 1px solid var(--stu-line); background: #fff;
        text-decoration: none !important; color: inherit;
        transition: border-color .15s, transform .15s, box-shadow .15s;
        min-height: 108px;
    }
    .stu-action:hover {
        border-color: #C5D4EF; transform: translateY(-2px);
        box-shadow: 0 12px 28px -16px rgba(11,61,145,.25);
    }
    .dark .stu-action { background: #111827; border-color: #1f2937; }
    .stu-action__icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #EEF3FB; color: var(--stu-blue); font-size: 15px;
    }
    .stu-action--gold .stu-action__icon { background: #FFF6D6; color: #8A6A00; }
    .stu-row {
        display: flex; align-items: center; gap: 12px;
        padding: 12px; border-radius: 14px;
        border: 1px solid transparent;
        transition: background .15s, border-color .15s;
        text-decoration: none !important; color: inherit;
    }
    .stu-row:hover { background: var(--stu-canvas); border-color: var(--stu-line); }
    .dark .stu-row:hover { background: #1f2937; border-color: #334155; }
</style>
@endpush

@section('content')
@php
    $progress = min((int) ($stats['total_progress'] ?? 0), 100);
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="stu-dash space-y-5">
    {{-- Greeting strip --}}
    <section class="stu-panel overflow-hidden">
        <div class="relative px-5 py-5 sm:px-6 sm:py-6">
            <div class="absolute inset-y-0 {{ $isRtl ? 'left-0' : 'right-0' }} w-40 sm:w-56 pointer-events-none opacity-90"
                 style="background: radial-gradient(ellipse at center, rgba(245,184,0,0.22), transparent 70%);"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div class="min-w-0">
                    <span class="stu-chip mb-3">
                        <i class="fas fa-language text-[10px]"></i>
                        {{ config('app.name') }} · {{ __('student.your_dashboard') }}
                    </span>
                    <h1 class="font-heading text-2xl sm:text-[28px] font-black tracking-tight text-[#0B1220] dark:text-white leading-tight">
                        {{ __('student.welcome_name', ['name' => auth()->user()->name]) }}
                    </h1>
                    <p class="mt-1.5 text-sm text-[color:var(--stu-muted)] dark:text-gray-400 max-w-xl">
                        {{ $isRtl ? 'منصتك للتعلّم المباشر أولاً — مجموعات وحصص فردية وبث، والكورسات المسجّلة داعمة لمسارك.' : 'Live learning first — groups, 1:1, and broadcasts; recorded courses support your path.' }}
                    </p>
                </div>
                <div class="w-full lg:w-56 flex-shrink-0">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span class="text-[color:var(--stu-muted)]">{{ __('student.total_progress') }}</span>
                        <span class="text-[#0B3D91] dark:text-blue-300 tabular-nums">{{ $progress }}%</span>
                    </div>
                    <div class="stu-progress"><span style="width: {{ $progress }}%"></span></div>
                    <p class="mt-1.5 text-[11px] text-[color:var(--stu-muted)]">{{ __('student.from_course_completion') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Next live / tutoring --}}
    @if(!empty($upcomingTutoringBooking))
        <section class="rounded-2xl overflow-hidden border border-[#0B3D91]/20 bg-[#0B3D91] text-white shadow-[0_18px_40px_-20px_rgba(11,61,145,0.55)]">
            <div class="px-5 py-5 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-[#F5B800]">{{ $isRtl ? 'حصتك القادمة' : 'Next session' }}</p>
                    <h2 class="mt-1 text-xl font-black truncate">{{ $upcomingTutoringBooking->tutoringGroup?->title ?? ($isRtl ? 'حصة مجموعة' : 'Group session') }}</h2>
                    <p class="mt-1 text-sm text-white/75">
                        <i class="far fa-clock ml-1"></i>
                        {{ $upcomingTutoringBooking->starts_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($upcomingTutoringBooking->classroomMeeting)
                        <a href="{{ url('classroom/join/'.$upcomingTutoringBooking->classroomMeeting->code) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-[#F5B800] px-4 py-2.5 text-sm font-extrabold text-[#072A66] hover:brightness-105">
                            <i class="fas fa-video"></i> {{ $isRtl ? 'دخول Live' : 'Join Live' }}
                        </a>
                    @endif
                    <a href="{{ route('student.tutoring-bookings.show', $upcomingTutoringBooking) }}"
                       class="inline-flex items-center rounded-xl border border-white/30 px-4 py-2.5 text-sm font-bold text-white hover:bg-white/10">
                        {{ $isRtl ? 'التفاصيل' : 'Details' }}
                    </a>
                </div>
            </div>
        </section>
    @else
        <section class="stu-panel px-5 py-4 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm font-bold text-[#0B1220] dark:text-white">{{ $isRtl ? 'لا توجد حصة مباشرة قادمة' : 'No upcoming live session' }}</p>
                <p class="text-xs text-[color:var(--stu-muted)] mt-0.5">{{ $isRtl ? 'احجز مجموعة أو باقة فردية للبدء.' : 'Book a group or individual package to start.' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(Route::has('public.groups'))
                    <a href="{{ route('public.groups') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#0B3D91] px-4 text-sm font-bold text-white">
                        <i class="fas fa-users text-xs"></i> {{ $isRtl ? 'تصفح المجموعات' : 'Browse groups' }}
                    </a>
                @endif
            </div>
        </section>
    @endif

    {{-- Quick actions — business pillars --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @if(Route::has('public.groups'))
        <a href="{{ route('public.groups') }}" class="stu-action stu-action--gold">
            <span class="stu-action__icon"><i class="fas fa-users"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'مجموعات حية' : 'Live groups' }}</p>
                <p class="text-[11px] text-[color:var(--stu-muted)] mt-0.5">{{ $isRtl ? 'دفعات جماعية محدودة' : 'Capped cohorts' }}</p>
            </div>
        </a>
        @endif
        @if(Route::has('student.tutoring-subscriptions.index'))
        <a href="{{ route('student.tutoring-subscriptions.index') }}" class="stu-action">
            <span class="stu-action__icon"><i class="fas fa-user-graduate"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'باقات فردية' : '1:1 packages' }}</p>
                <p class="text-[11px] text-[color:var(--stu-muted)] mt-0.5">{{ $isRtl ? 'حصصك المتبقية' : 'Sessions left' }}</p>
            </div>
        </a>
        @endif
        @if(Route::has('student.live-sessions.index'))
        <a href="{{ route('student.live-sessions.index') }}" class="stu-action">
            <span class="stu-action__icon"><i class="fas fa-broadcast-tower"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'البث المباشر' : 'Live broadcast' }}</p>
                <p class="text-[11px] text-[color:var(--stu-muted)] mt-0.5">{{ $isRtl ? 'انضم الآن أو لاحقاً' : 'Join now or later' }}</p>
            </div>
        </a>
        @endif
        <a href="{{ route('my-courses.index') }}" class="stu-action">
            <span class="stu-action__icon"><i class="fas fa-book-open"></i></span>
            <div>
                <p class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ __('student.my_active_courses') }}</p>
                <p class="text-[11px] text-[color:var(--stu-muted)] mt-0.5">{{ $stats['active_courses'] }} {{ $isRtl ? 'نشط' : 'active' }}</p>
            </div>
        </a>
    </section>

    {{-- Stats row (quiet) --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('my-courses.index') }}" class="stu-panel px-4 py-4 hover:border-[#0B3D91]/25 transition-colors block">
            <p class="text-[11px] font-bold text-[color:var(--stu-muted)]">{{ __('student.my_active_courses') }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B3D91] dark:text-blue-300 tabular-nums">{{ $stats['active_courses'] }}</p>
        </a>
        <a href="{{ route('student.certificates.index') }}" class="stu-panel px-4 py-4 hover:border-[#0B3D91]/25 transition-colors block">
            <p class="text-[11px] font-bold text-[color:var(--stu-muted)]">{{ __('student.completed') }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B1220] dark:text-white tabular-nums">{{ $stats['completed_courses'] }}</p>
        </a>
        <div class="stu-panel px-4 py-4">
            <p class="text-[11px] font-bold text-[color:var(--stu-muted)]">{{ __('student.total_progress') }}</p>
            <p class="mt-1 text-2xl font-black text-[#8A6A00] tabular-nums">{{ $stats['total_progress'] }}%</p>
        </div>
        <a href="{{ route('orders.index') }}" class="stu-panel px-4 py-4 hover:border-[#0B3D91]/25 transition-colors block">
            <p class="text-[11px] font-bold text-[color:var(--stu-muted)]">{{ __('student.pending_orders') }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B1220] dark:text-white tabular-nums">{{ $stats['pending_orders'] }}</p>
        </a>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <section class="lg:col-span-2 stu-panel min-w-0">
            <div class="flex items-center justify-between gap-3 px-5 pt-5">
                <h2 class="font-heading text-base font-extrabold text-[#0B1220] dark:text-white">{{ __('student.my_active_courses') }}</h2>
                <a href="{{ route('my-courses.index') }}" class="text-xs font-bold text-[#0B3D91] dark:text-blue-300">{{ __('student.view_all') }}</a>
            </div>
            <div class="p-3 sm:p-4 space-y-1">
                @forelse($activeCourses->take(5) as $course)
                    @php $prog = (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0); @endphp
                    <a href="{{ route('my-courses.show', $course->id) }}" class="stu-row">
                        <div class="w-10 h-10 rounded-xl bg-[#EEF3FB] text-[#0B3D91] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-sm text-[#0B1220] dark:text-white truncate">{{ $course->title }}</h3>
                            <div class="mt-1.5 flex items-center gap-2">
                                <div class="flex-1 stu-progress"><span style="width: {{ $prog }}%"></span></div>
                                <span class="text-[11px] font-bold text-[color:var(--stu-muted)] tabular-nums w-8">{{ (int) $prog }}%</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-10 px-4">
                        <p class="font-bold text-[#0B1220] dark:text-white">{{ __('student.no_active_courses') }}</p>
                        <p class="text-sm text-[color:var(--stu-muted)] mt-1 mb-4">{{ __('student.start_journey_now') }}</p>
                        <a href="{{ route('public.courses') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0B3D91] px-4 py-2.5 text-sm font-bold text-white">
                            {{ __('student.explore_courses') }}
                        </a>
                    </div>
                @endforelse
            </div>
        </section>

        <div class="space-y-5 min-w-0">
            <section class="stu-panel">
                <div class="flex items-center justify-between px-4 pt-4">
                    <h3 class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ __('student.assignments') }}</h3>
                    @if($upcomingAssignments->count() > 0)
                        <span class="stu-chip">{{ $upcomingAssignments->count() }}</span>
                    @endif
                </div>
                <div class="p-3 space-y-2">
                    @forelse($upcomingAssignments->take(3) as $assignment)
                        @php
                            $lecture = $assignment->lecture ?? null;
                            $course = $assignment->course ?? optional($lecture)->course;
                            $dueDate = optional($assignment->due_date);
                        @endphp
                        <div class="rounded-xl border border-[color:var(--stu-line)] dark:border-gray-700 px-3 py-2.5">
                            <p class="text-xs font-bold text-[#0B1220] dark:text-white truncate">{{ $assignment->title }}</p>
                            @if($course)<p class="text-[11px] text-[color:var(--stu-muted)] truncate mt-0.5">{{ $course->title }}</p>@endif
                            @if($dueDate)
                                <p class="text-[10px] font-bold text-[#0B3D91] mt-1.5">{{ $dueDate->translatedFormat('d M') }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-xs text-[color:var(--stu-muted)] py-6">{{ __('student.no_assignments') }}</p>
                    @endforelse
                </div>
            </section>

            <section class="stu-panel">
                <div class="flex items-center justify-between px-4 pt-4">
                    <h3 class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ __('student.exams') }}</h3>
                    @if($upcomingExams->count() > 0)
                        <span class="stu-chip">{{ $upcomingExams->count() }}</span>
                    @endif
                </div>
                <div class="p-3 space-y-2">
                    @forelse($upcomingExams->take(3) as $exam)
                        @php
                            $course = $exam->course;
                            $startAt = $exam->start_time ?? ($exam->start_date ? $exam->start_date->copy()->startOfDay() : null);
                            $isAvailableNow = $startAt ? $startAt->isPast() : true;
                        @endphp
                        <a href="{{ route('student.exams.show', $exam) }}" class="block rounded-xl border border-[color:var(--stu-line)] dark:border-gray-700 px-3 py-2.5 hover:border-[#0B3D91]/30">
                            <p class="text-xs font-bold text-[#0B1220] dark:text-white truncate">{{ $exam->title }}</p>
                            @if($course)<p class="text-[11px] text-[color:var(--stu-muted)] truncate mt-0.5">{{ $course->title }}</p>@endif
                            <p class="text-[10px] font-bold mt-1.5 {{ $isAvailableNow ? 'text-emerald-600' : 'text-[color:var(--stu-muted)]' }}">
                                {{ $isAvailableNow ? __('student.available') : __('student.coming_soon') }}
                            </p>
                        </a>
                    @empty
                        <p class="text-center text-xs text-[color:var(--stu-muted)] py-6">{{ __('student.no_exams') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <section class="stu-panel min-w-0">
            <h2 class="font-heading text-sm font-extrabold text-[#0B1220] dark:text-white px-5 pt-5">{{ __('student.exam_results') }}</h2>
            <div class="p-4 space-y-2">
                @forelse($recentExamAttempts->take(4) as $attempt)
                    @php $exam = $attempt->exam; @endphp
                    <div class="flex items-center gap-3 rounded-xl border border-[color:var(--stu-line)] dark:border-gray-700 px-3 py-2.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold truncate text-[#0B1220] dark:text-white">{{ $exam->title ?? __('student.exam_deleted') }}</p>
                            <p class="text-[11px] text-[color:var(--stu-muted)] mt-0.5">
                                {{ $attempt->result_status }}
                                @if(!is_null($attempt->percentage)) · {{ number_format($attempt->percentage, 1) }}%@endif
                            </p>
                        </div>
                        @if($exam)
                            <a href="{{ route('student.exams.result', [$exam, $attempt]) }}" class="text-[11px] font-bold text-[#0B3D91]">{{ __('common.view') }}</a>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-xs text-[color:var(--stu-muted)] py-8">{{ __('student.no_results_yet') }}</p>
                @endforelse
            </div>
        </section>

        <section class="stu-panel min-w-0">
            <h2 class="font-heading text-sm font-extrabold text-[#0B1220] dark:text-white px-5 pt-5">{{ __('student.issued_certificates') }}</h2>
            <div class="p-4 space-y-2">
                @forelse($recentCertificates->take(4) as $certificate)
                    <div class="rounded-xl border border-[color:var(--stu-line)] dark:border-gray-700 px-3 py-2.5">
                        <p class="text-xs font-bold truncate text-[#0B1220] dark:text-white">
                            {{ $certificate->title ?? $certificate->course_name ?? __('student.certificate_untitled') }}
                        </p>
                        @if($certificate->certificate_number)
                            <p class="text-[10px] font-bold text-[#0B3D91] mt-1">{{ $certificate->certificate_number }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-xs text-[color:var(--stu-muted)] py-8">{{ __('student.no_certificates_yet') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
