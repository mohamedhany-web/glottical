@extends('layouts.app')

@section('title', __('instructor.dashboard_title'))
@section('page_title', __('instructor.overview'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $showCourses = instructor_ui('show_courses', false);
    $pending = (int) ($stats['pending_submissions'] ?? 0);
    $students = (int) ($stats['total_students'] ?? 0);
    $courses = (int) ($stats['my_courses'] ?? 0);
    $lectures = (int) ($stats['total_lectures'] ?? 0);
    $upcomingTutoring = (int) ($stats['upcoming_tutoring'] ?? 0);
    $upcomingLectures = (int) ($stats['upcoming_lectures'] ?? 0);
    $exams = (int) ($stats['total_exams'] ?? 0);
    $assignments = (int) ($stats['total_assignments'] ?? 0);
    $bookingsHref = Route::has('instructor.tutoring-bookings.index')
        ? route('instructor.tutoring-bookings.index')
        : route('dashboard');
    $cohortsHref = Route::has('instructor.tutoring-cohorts.index')
        ? route('instructor.tutoring-cohorts.index')
        : $bookingsHref;
    $liveHref = Route::has('instructor.live-sessions.index')
        ? route('instructor.live-sessions.index')
        : route('dashboard');
    $calendarHref = Route::has('instructor.calendar')
        ? route('instructor.calendar')
        : route('dashboard');

    if ($showCourses) {
        $workload = [
            ['label' => __('instructor.my_courses'), 'value' => $courses, 'href' => route('instructor.courses.index')],
            ['label' => __('instructor.total_students'), 'value' => $students, 'href' => route('instructor.courses.index')],
            ['label' => __('instructor.upcoming_lectures'), 'value' => $upcomingLectures, 'href' => route('instructor.lectures.index')],
            ['label' => __('instructor.group_bookings'), 'value' => $upcomingTutoring, 'href' => $bookingsHref],
            ['label' => __('instructor.need_grading'), 'value' => $pending, 'href' => route('instructor.assignments.index')],
            ['label' => __('instructor.exams'), 'value' => $exams, 'href' => route('instructor.exams.index')],
        ];
    } else {
        $workload = [
            ['label' => __('instructor.group_bookings'), 'value' => $upcomingTutoring, 'href' => $bookingsHref],
            ['label' => __('instructor.class_command'), 'value' => (int) ($stats['cohorts_count'] ?? 0), 'href' => $cohortsHref],
            ['label' => __('instructor.live_broadcast'), 'value' => (int) ($stats['live_now'] ?? 0), 'href' => $liveHref],
            ['label' => __('instructor.upcoming'), 'value' => $upcomingTutoring, 'href' => $calendarHref],
        ];
    }
    $maxWorkload = max(1, collect($workload)->max('value'));
@endphp

<div class="su-overview">
    <div class="su-ov-head">
        <div>
            <h1 class="su-ov-title">{{ __('instructor.overview') }}</h1>
            <p class="su-rail-m" style="margin-top:4px">{{ __('instructor.welcome') }}، {{ auth()->user()->name }}</p>
        </div>
        <div class="su-today" aria-hidden="true">
            {{ now()->translatedFormat($isRtl ? 'l، j F' : 'D, M j') }}
        </div>
    </div>

    <section class="su-kpi-row">
        @if($showCourses)
            <a href="{{ route('instructor.courses.index') }}" class="su-kpi su-kpi--1">
                <div class="su-kpi__l">{{ __('instructor.my_courses') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format($courses) }}</div>
                    <div class="su-kpi__d">{{ $lectures }} {{ __('instructor.lectures') }}</div>
                </div>
            </a>
            <a href="{{ route('instructor.courses.index') }}" class="su-kpi su-kpi--2">
                <div class="su-kpi__l">{{ __('instructor.total_students') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format($students) }}</div>
                    <div class="su-kpi__d">{{ __('instructor.active') }}</div>
                </div>
            </a>
            <a href="{{ route('instructor.lectures.index') }}" class="su-kpi su-kpi--3">
                <div class="su-kpi__l">{{ __('instructor.upcoming_lectures') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format($upcomingLectures) }}</div>
                    <div class="su-kpi__d">{{ $assignments }} {{ __('instructor.assignments') }}</div>
                </div>
            </a>
            <a href="{{ $bookingsHref }}" class="su-kpi su-kpi--4">
                <div class="su-kpi__l">{{ __('instructor.need_grading') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format($pending) }}</div>
                    <div class="su-kpi__d">{{ $upcomingTutoring }} {{ __('instructor.group_bookings') }}</div>
                </div>
            </a>
        @else
            <a href="{{ $bookingsHref }}" class="su-kpi su-kpi--1">
                <div class="su-kpi__l">{{ __('instructor.group_bookings') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format($upcomingTutoring) }}</div>
                    <div class="su-kpi__d">{{ __('instructor.upcoming') }}</div>
                </div>
            </a>
            <a href="{{ $cohortsHref }}" class="su-kpi su-kpi--2">
                <div class="su-kpi__l">{{ __('instructor.class_command') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format((int) ($stats['cohorts_count'] ?? 0)) }}</div>
                    <div class="su-kpi__d">{{ __('instructor.tc_cohorts') }}</div>
                </div>
            </a>
            <a href="{{ $liveHref }}" class="su-kpi su-kpi--3">
                <div class="su-kpi__l">{{ __('instructor.live_broadcast') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format((int) ($stats['live_now'] ?? 0)) }}</div>
                    <div class="su-kpi__d">{{ __('instructor.ls_live_now') }}</div>
                </div>
            </a>
            <a href="{{ $calendarHref }}" class="su-kpi su-kpi--4">
                <div class="su-kpi__l">{{ __('instructor.my_calendar') }}</div>
                <div class="su-kpi__row">
                    <div class="su-kpi__v">{{ number_format($upcomingTutoring) }}</div>
                    <div class="su-kpi__d">{{ __('instructor.upcoming') }}</div>
                </div>
            </a>
        @endif
    </section>

    @if(!empty($upcomingTutoringBooking))
        <section class="su-block su-next-session">
            <div class="min-w-0">
                <p class="su-rail-m">{{ __('instructor.next_live_session') }}</p>
                <h2 class="su-ov-title" style="margin-top:4px">{{ $upcomingTutoringBooking->tutoringGroup?->title ?? __('instructor.group_session') }}</h2>
                <p class="su-rail-m" style="margin-top:4px">
                    <x-app-datetime :at="$upcomingTutoringBooking->starts_at" pattern="D j M · g:i A" />
                    @if($upcomingTutoringBooking->user) · {{ $upcomingTutoringBooking->user->name }} @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($upcomingTutoringBooking->classroomMeeting)
                    <form method="POST" action="{{ route('instructor.classroom.start-meeting', $upcomingTutoringBooking->classroomMeeting) }}">
                        @csrf
                        <button type="submit" class="su-btn su-btn--primary">
                            <i class="fas fa-video"></i> {{ __('instructor.start_live') }}
                        </button>
                    </form>
                @endif
                @if(Route::has('instructor.tutoring-bookings.show'))
                    <a href="{{ route('instructor.tutoring-bookings.show', $upcomingTutoringBooking) }}" class="su-btn">
                        {{ __('instructor.view_details') }}
                    </a>
                @endif
            </div>
        </section>
    @endif

    <section class="su-quick-grid">
        @if(Route::has('instructor.calendar'))
        <a href="{{ route('instructor.calendar') }}" class="su-quick">
            <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-calendar-alt"></i></span>
            <span>
                <strong>{{ __('instructor.my_calendar') }}</strong>
                <em>{{ ($showCourses ? $upcomingLectures : 0) + $upcomingTutoring }} {{ __('instructor.upcoming') }}</em>
            </span>
        </a>
        @endif
        @if(Route::has('instructor.tutoring-bookings.index'))
        <a href="{{ route('instructor.tutoring-bookings.index') }}" class="su-quick">
            <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-users"></i></span>
            <span>
                <strong>{{ __('instructor.group_bookings') }}</strong>
                <em>{{ $upcomingTutoring }} {{ __('instructor.upcoming') }}</em>
            </span>
        </a>
        @endif
        @if($showCourses)
        <a href="{{ route('instructor.assignments.index') }}" class="su-quick">
            <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-tasks"></i></span>
            <span>
                <strong>{{ __('instructor.assignments') }}</strong>
                <em>@if($pending > 0){{ $pending }} {{ __('instructor.need_grading') }}@else{{ __('instructor.all_assignments_graded') }}@endif</em>
            </span>
        </a>
        @elseif(Route::has('instructor.tutoring-cohorts.index'))
        <a href="{{ route('instructor.tutoring-cohorts.index') }}" class="su-quick">
            <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-layer-group"></i></span>
            <span>
                <strong>{{ __('instructor.class_command') }}</strong>
                <em>{{ __('instructor.tc_subtitle') }}</em>
            </span>
        </a>
        @endif
        @if(Route::has('instructor.live-sessions.index'))
        <a href="{{ route('instructor.live-sessions.index') }}" class="su-quick">
            <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-broadcast-tower"></i></span>
            <span>
                <strong>{{ __('instructor.live_broadcast') }}</strong>
                <em>{{ __('instructor.manage_streams') }}</em>
            </span>
        </a>
        @endif
    </section>

    <section class="su-bottom">
        @if($showCourses)
        <div class="su-block">
            <div class="flex items-center justify-between mb-3">
                <div class="su-block__title" style="margin:0">{{ __('instructor.upcoming_lectures') }}</div>
                <a href="{{ route('instructor.lectures.index') }}" class="su-rail-m">{{ __('instructor.view_all') }}</a>
            </div>
            <div class="space-y-1">
                @forelse($upcoming_lectures->take(5) as $lecture)
                    <a href="{{ route('instructor.lectures.show', $lecture) }}" class="su-rail-item">
                        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-chalkboard"></i></span>
                        <div class="min-w-0">
                            <div class="su-rail-t truncate">{{ $lecture->title }}</div>
                            <div class="su-rail-m truncate">
                                {{ $lecture->course->title ?? __('instructor.not_specified') }}
                                · {{ $lecture->scheduled_at?->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="su-rail-m" style="padding:16px;text-align:center">{{ __('instructor.no_lectures') }}</p>
                @endforelse
            </div>
        </div>
        @else
        <div class="su-block">
            <div class="flex items-center justify-between mb-3">
                <div class="su-block__title" style="margin:0">{{ __('instructor.group_bookings') }}</div>
                <a href="{{ $bookingsHref }}" class="su-rail-m">{{ __('instructor.view_all') }}</a>
            </div>
            <div class="space-y-1">
                @forelse(($upcoming_tutoring_bookings ?? collect())->take(5) as $booking)
                    <a href="{{ route('instructor.tutoring-bookings.show', $booking) }}" class="su-rail-item">
                        <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-users"></i></span>
                        <div class="min-w-0">
                            <div class="su-rail-t truncate">{{ $booking->tutoringGroup?->title ?? __('instructor.group_session') }}</div>
                            <div class="su-rail-m truncate">
                                {{ $booking->contactName() }}
                                · {{ $booking->starts_at?->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="su-rail-m" style="padding:16px;text-align:center">{{ __('instructor.tb_empty') }}</p>
                @endforelse
            </div>
        </div>
        @endif

        <div class="su-block">
            <div class="su-block__title">{{ __('instructor.workload_summary') }}</div>
            <div class="su-workload">
                @foreach($workload as $row)
                    <a href="{{ $row['href'] }}" class="su-workload-row">
                        <span class="su-workload-label truncate">{{ $row['label'] }}</span>
                        <div class="su-workload-bar" aria-hidden="true">
                            <i style="width: {{ max(8, (int) round(($row['value'] / $maxWorkload) * 100)) }}%"></i>
                        </div>
                        <strong>{{ number_format($row['value']) }}</strong>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($showCourses)
    <section class="su-bottom" style="margin-top:20px">
        <div class="su-block">
            <div class="flex items-center justify-between mb-3">
                <div class="su-block__title" style="margin:0">{{ __('instructor.my_recent_courses') }}</div>
                <a href="{{ route('instructor.courses.index') }}" class="su-rail-m">{{ __('instructor.view_all') }}</a>
            </div>
            <div class="space-y-1">
                @forelse($my_courses as $course)
                    <a href="{{ route('instructor.courses.show', $course) }}" class="su-rail-item">
                        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-book"></i></span>
                        <div class="min-w-0">
                            <div class="su-rail-t truncate">{{ $course->title }}</div>
                            <div class="su-rail-m">{{ $course->active_students_count ?? 0 }} {{ __('instructor.student_single') }}</div>
                        </div>
                    </a>
                @empty
                    <p class="su-rail-m" style="padding:16px;text-align:center">{{ __('instructor.no_courses_assigned') }}</p>
                @endforelse
            </div>
        </div>

        <div class="su-block">
            <div class="flex items-center justify-between mb-3">
                <div class="su-block__title" style="margin:0">{{ __('instructor.assignments_need_grading') }}</div>
                @if($pending > 0)<span class="su-link__badge">{{ $pending }}</span>@endif
            </div>
            <div class="space-y-1">
                @forelse($pending_assignments->take(5) as $submission)
                    <a href="{{ route('instructor.assignments.submissions', $submission->assignment) }}" class="su-rail-item">
                        <span class="su-rail-avatar">{{ mb_substr($submission->student->name ?? 'S', 0, 1) }}</span>
                        <div class="min-w-0">
                            <div class="su-rail-t truncate">{{ $submission->assignment->title ?? __('instructor.assignment_default') }}</div>
                            <div class="su-rail-m">{{ $submission->student->name ?? '' }} · {{ $submission->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <p class="su-rail-m" style="padding:16px;text-align:center">{{ __('instructor.all_assignments_graded') }}</p>
                @endforelse
            </div>
        </div>
    </section>
    @endif
</div>
@endsection
