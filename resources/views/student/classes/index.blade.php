@extends('layouts.student-timeline')

@section('title', __('student_timeline.my_classes'))

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $classes = $classes ?? collect();
    $upcoming = $upcoming ?? collect();
    $nextJoinable = $nextJoinable ?? null;
    $searchQuery = $searchQuery ?? '';
    $sortMode = $sortMode ?? 'classes';
    $progress = $progress ?? ['percent' => 0, 'completed_sessions' => 0, 'total_sessions' => 0];
    $subjectTones = ['pink', 'blue', 'purple', 'orange'];
    $subjectIcons = [
        asset('img/student-timeline/sqrt.svg'),
        asset('img/student-timeline/earth.svg'),
        asset('img/student-timeline/clock.png'),
        asset('img/student-timeline/teacher.png'),
    ];
    $groupsUrl = Route::has('public.groups') ? route('public.groups') : route('dashboard');
    $sortUrl = request()->fullUrlWithQuery([
        'sort' => $sortMode === 'classes' ? 'progress' : 'classes',
        'q' => $searchQuery !== '' ? $searchQuery : null,
    ]);
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.my_classes'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.my_classes'), 'url' => null],
    ],
    'toolbarView' => 'student.classes._toolbar',
    'toolbarData' => [
        'searchQuery' => $searchQuery,
        'sortMode' => $sortMode,
        'sortUrl' => $sortUrl,
        'classCount' => $classes->count(),
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

{{-- One primary action: join next live/open class --}}
@if($nextJoinable)
    <section class="st-join-hero" aria-label="{{ __('student_timeline.join_class_now') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">
                {{ $nextJoinable->isJoinable() && $nextJoinable->status === \App\Models\TutoringClassSession::STATUS_LIVE
                    ? __('student_timeline.live_now')
                    : __('student_timeline.next_class') }}
            </p>
            <h2 class="st-join-hero__title">{{ $nextJoinable->displayTitle() }}</h2>
            <p class="st-join-hero__meta">
                {{ $nextJoinable->cohort?->title }}
                · {{ $nextJoinable->starts_at?->timezone(config('app.timezone'))->translatedFormat($isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A') }}
                @if($nextJoinable->cohort?->tutoringGroup?->instructor?->name)
                    · {{ $nextJoinable->cohort->tutoringGroup->instructor->name }}
                @endif
            </p>
        </div>
        <div class="st-join-hero__actions">
            <form method="POST" action="{{ route('student.classes.sessions.join', $nextJoinable) }}">
                @csrf
                <button type="submit" class="st-pill st-pill--solid st-pill--lg">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    {{ __('student_timeline.join_class_now') }}
                </button>
            </form>
            @if($nextJoinable->cohort)
                <a href="{{ route('student.classes.show', $nextJoinable->cohort) }}" class="st-pill st-pill--outline">
                    {{ __('student_timeline.open_class') }}
                </a>
            @endif
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.my_classes') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.active_classes') }}</p>
        <p class="st-stat-card__value">{{ $classes->count() }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.path_progress') }}</p>
        <p class="st-stat-card__value">{{ (int) ($progress['percent'] ?? 0) }}%</p>
        <p class="st-stat-card__hint">{{ __('student_timeline.of_completed', ['count' => (int) ($progress['completed_sessions'] ?? 0)]) }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.upcoming_short') }}</p>
        <p class="st-stat-card__value">{{ $upcoming->count() }}</p>
    </article>
</section>

<section id="st-class-list">
    <div class="st-section-head">
        <div>
            <h2>{{ __('student_timeline.my_classes') }}</h2>
            <p>{{ __('student_timeline.classes_hint') }}</p>
        </div>
        <a class="st-see" href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
    </div>

    @if($classes->isNotEmpty())
        <div class="st-class-grid">
            @foreach($classes as $i => $class)
                @php
                    $tone = $subjectTones[$i % count($subjectTones)];
                    $icon = $subjectIcons[$i % count($subjectIcons)];
                    $mask = $i % 2 === 0
                        ? asset('img/student-timeline/subj-mask-1.svg')
                        : asset('img/student-timeline/subj-mask-2.svg');
                    $label = $class->subject_name ?: $class->title;
                    $next = $class->next_session ?? null;
                    $canJoin = $next && method_exists($next, 'isJoinable') && $next->isJoinable();
                @endphp
                <article class="st-class-card st-class-card--{{ $tone }}">
                    <a href="{{ $class->url }}" class="st-class-card__main" title="{{ $class->title }}">
                        <img class="st-class-card__blob" src="{{ $mask }}" alt="" width="120" height="120">
                        <span class="st-class-card__icon">
                            <img src="{{ $icon }}" alt="" width="22" height="22">
                        </span>
                        <h3 class="st-class-card__name">{{ $label }}</h3>
                        <p class="st-class-card__sub">{{ $class->title }}</p>
                        <div class="st-class-card__progress" aria-hidden="true">
                            <span style="width: {{ max(4, (int) $class->progress_percent) }}%"></span>
                        </div>
                        <p class="st-class-card__meta">
                            {{ $class->progress_percent }}%
                            · {{ $class->completed_sessions }}/{{ $class->total_sessions }}
                            @if($class->instructor_name)
                                · {{ $class->instructor_name }}
                            @endif
                        </p>
                        @if($class->schedule)
                            <p class="st-class-card__sched">{{ $class->schedule }}</p>
                        @endif
                    </a>

                    <div class="st-class-card__foot">
                        @if($canJoin)
                            <form method="POST" action="{{ route('student.classes.sessions.join', $next) }}">
                                @csrf
                                <button type="submit" class="st-pill st-pill--solid">
                                    <i class="fas fa-video" aria-hidden="true"></i>
                                    {{ __('student_timeline.join_now') }}
                                </button>
                            </form>
                        @elseif($next && $next->starts_at)
                            <span class="st-class-card__next">
                                {{ __('student_timeline.next_at', ['time' => $next->starts_at->timezone(config('app.timezone'))->translatedFormat('D g:i A')]) }}
                            </span>
                        @else
                            <span class="st-class-card__next">{{ __('student_timeline.no_upcoming_in_class') }}</span>
                        @endif
                        <a href="{{ $class->url }}" class="st-pill st-pill--outline">{{ __('student_timeline.open_class') }}</a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.no_classes') }}</h3>
            <p>{{ __('student_timeline.no_classes_hint') }}</p>
            <div class="st-biz-banner__actions">
                <a href="{{ $groupsUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.browse_school') }}</a>
                <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
            </div>
        </div>
    @endif
</section>
@endsection

@section('events')
@php
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
    $eventTones = ['pink', 'blue', 'orange'];
@endphp

<div class="st-events__top">
    <h2>{{ __('student_timeline.upcoming_classes') }}</h2>
</div>

<div data-tab-panel="activities">
    @forelse($upcoming->take(6) as $i => $session)
        @php
            $joinable = $session->isJoinable();
            $cohortUrl = $session->cohort && Route::has('student.classes.show')
                ? route('student.classes.show', $session->cohort)
                : '#';
        @endphp
        <div class="st-event-card st-event-card--{{ $eventTones[$i % 3] }}">
            <img class="st-event-card__mask" src="{{ $eventMasks[$i % 3] }}" alt="" width="160" height="160">
            <h3>{{ $session->displayTitle() }}</h3>
            <p class="st-event-card__sub">{{ $session->cohort?->title }}</p>
            <div class="st-event-card__meta">
                <img src="{{ asset('img/student-timeline/clock.png') }}" alt="" width="14" height="14">
                <span>{{ $session->starts_at?->timezone(config('app.timezone'))->translatedFormat('D · g:i A') }}</span>
            </div>
            <div class="st-event-card__actions">
                @if($joinable)
                    <form method="POST" action="{{ route('student.classes.sessions.join', $session) }}">
                        @csrf
                        <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.join_now') }}</button>
                    </form>
                @else
                    <a href="{{ $cohortUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.open_class') }}</a>
                @endif
            </div>
        </div>
    @empty
        <p class="st-events__empty">{{ __('student_timeline.no_events') }}</p>
    @endforelse
</div>

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
