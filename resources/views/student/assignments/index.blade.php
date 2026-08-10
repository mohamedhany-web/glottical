@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_assignments'))

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $assignments = $assignments ?? collect();
    $searchQuery = $searchQuery ?? '';
    $filter = $filter ?? 'all';
    $counts = $counts ?? ['all' => 0, 'pending' => 0, 'submitted' => 0, 'graded' => 0];
    $tones = ['blue', 'pink', 'orange', 'purple'];
    $coursesUrl = Route::has('my-courses.index') ? route('my-courses.index') : route('dashboard');
    $nextPending = $assignments->first(fn ($a) => ! ($a->my_submission ?? null));
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_assignments'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_assignments'), 'url' => null],
    ],
    'toolbarView' => 'student.assignments._toolbar',
    'toolbarData' => [
        'searchQuery' => $searchQuery,
        'filter' => $filter,
        'counts' => $counts,
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

@if($nextPending)
    <section class="st-join-hero" aria-label="{{ __('student_timeline.next_assignment') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.next_assignment') }}</p>
            <h2 class="st-join-hero__title">{{ $nextPending->title }}</h2>
            <p class="st-join-hero__meta">
                {{ $nextPending->course->title ?? __('student_timeline.course') }}
                @if($nextPending->due_date)
                    · {{ __('student_timeline.due_at', ['date' => $nextPending->due_date->timezone(config('app.timezone'))->translatedFormat($isRtl ? 'd M · g:i A' : 'M j · g:i A')]) }}
                @endif
            </p>
        </div>
        <div class="st-join-hero__actions">
            <a href="{{ route('student.assignments.show', $nextPending) }}" class="st-pill st-pill--solid st-pill--lg">
                {{ __('student_timeline.open_assignment') }}
            </a>
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.nav_assignments') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.filter_all') }}</p>
        <p class="st-stat-card__value">{{ $counts['all'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.filter_pending') }}</p>
        <p class="st-stat-card__value">{{ $counts['pending'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.filter_submitted') }}</p>
        <p class="st-stat-card__value">{{ $counts['submitted'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.filter_graded') }}</p>
        <p class="st-stat-card__value">{{ $counts['graded'] }}</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.assignments_title') }}</h2>
        <p>{{ __('student_timeline.assignments_hint') }}</p>
    </div>
    <a href="{{ $coursesUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.my_courses_short') }}</a>
</section>

<section class="st-assign-list" aria-label="{{ __('student_timeline.assignments_title') }}">
    @forelse($assignments as $i => $assignment)
        @php
            $sub = $assignment->my_submission ?? null;
            $tone = $tones[$i % count($tones)];
            $courseTitle = $assignment->course->title ?? __('student_timeline.course');
            if (! $sub) {
                $statusKey = 'pending';
                $statusLabel = __('student_timeline.asg_not_submitted');
            } elseif ($sub->status === 'submitted') {
                $statusKey = 'submitted';
                $statusLabel = __('student_timeline.asg_under_review');
            } elseif ($sub->status === 'graded') {
                $statusKey = 'graded';
                $statusLabel = __('student_timeline.asg_graded');
                if ($sub->score !== null) {
                    $statusLabel .= ' — '.$sub->score.'/'.$assignment->max_score;
                }
            } elseif ($sub->status === 'returned') {
                $statusKey = 'returned';
                $statusLabel = __('student_timeline.asg_returned');
            } else {
                $statusKey = 'pending';
                $statusLabel = $sub->status;
            }
        @endphp
        <a href="{{ route('student.assignments.show', $assignment) }}" class="st-assign-card st-assign-card--{{ $tone }}">
            <div class="st-assign-card__main">
                <div class="st-assign-card__icon" aria-hidden="true">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="st-assign-card__copy">
                    <div class="st-assign-card__badges">
                        <span class="st-assign-card__badge is-{{ $statusKey }}">{{ $statusLabel }}</span>
                        @if($assignment->due_date)
                            <span class="st-assign-card__due">
                                {{ __('student_timeline.due_at', ['date' => $assignment->due_date->timezone(config('app.timezone'))->format('Y-m-d H:i')]) }}
                            </span>
                        @endif
                    </div>
                    <h3>{{ $assignment->title }}</h3>
                    <p class="st-assign-card__meta">{{ $courseTitle }}</p>
                </div>
            </div>
            <span class="st-assign-card__cta">{{ __('student_timeline.open_assignment') }}</span>
        </a>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.no_assignments') }}</h3>
            <p>{{ __('student_timeline.no_assignments_hint') }}</p>
            <div class="st-biz-banner__actions">
                <a href="{{ $coursesUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.my_courses_short') }}</a>
                <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
            </div>
        </div>
    @endforelse
</section>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

@if(Route::has('student.library.materials'))
    <a href="{{ route('student.library.materials') }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.nav_library_materials') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.materials_hint') }}</p>
    </a>
@endif

@if(Route::has('student.library.videos'))
    <a href="{{ route('student.library.videos') }}" class="st-event-card st-event-card--blue">
        <h3>{{ __('student_timeline.nav_library_videos') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.videos_side_hint') }}</p>
    </a>
@endif

@if(Route::has('student.classes.index'))
    <a href="{{ route('student.classes.index') }}" class="st-event-card st-event-card--pink">
        <h3>{{ __('student_timeline.my_classes') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.classes_hint') }}</p>
    </a>
@endif

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
