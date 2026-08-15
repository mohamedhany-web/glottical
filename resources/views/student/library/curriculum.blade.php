@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_library_curriculum'))

@section('content')
@php
    $locale = $locale ?? app()->getLocale();
    $courses = $courses ?? collect();
    $searchQuery = $searchQuery ?? '';
    $packagesUrl = $packagesUrl ?? route('dashboard');
    $coursesEmpty = method_exists($courses, 'isEmpty') ? $courses->isEmpty() : collect($courses)->isEmpty();
    $courseCount = method_exists($courses, 'total') ? $courses->total() : collect($courses)->count();
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_library_curriculum'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.family_library_title'), 'url' => route('student.library.home')],
        ['label' => __('student_timeline.nav_library_curriculum'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.lib_curriculum_title') }}</h2>
        <p>{{ __('student_timeline.lib_curriculum_hint') }}</p>
    </div>
    <div class="st-msg-intro__actions">
        <a href="{{ route('student.library.home') }}" class="st-pill st-pill--outline">{{ __('student_timeline.family_library_title') }}</a>
        <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_materials') }}</a>
    </div>
</section>

<form class="st-lib-search" method="get" action="{{ route('student.library.curriculum') }}" role="search">
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <label class="sr-only" for="lib-curriculum-q">{{ __('student_timeline.lib_curriculum_search') }}</label>
    <input id="lib-curriculum-q" type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.lib_curriculum_search') }}">
    <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.search') }}</button>
</form>

<p class="st-lib-count">{{ trans_choice('student_timeline.lib_curriculum_courses_count', $courseCount, ['count' => $courseCount]) }}</p>

@if($coursesEmpty)
    <div class="st-empty-panel">
        <h3>{{ __('student_timeline.lib_curriculum_empty_title') }}</h3>
        <p>{{ __('student_timeline.lib_curriculum_empty_hint') }}</p>
        <div class="st-biz-banner__actions">
            @if(Route::has('student.learn.index'))
                <a href="{{ route('student.learn.index') }}" class="st-pill st-pill--solid">{{ __('student_timeline.nav_learn') }}</a>
            @endif
            @if(Route::has('my-courses.index'))
                <a href="{{ route('my-courses.index') }}" class="st-pill st-pill--outline">{{ __('student_timeline.my_classes') }}</a>
            @endif
            <a href="{{ $packagesUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.lib_browse_packages') }}</a>
        </div>
    </div>
@else
    <section class="st-curriculum-grid" aria-label="{{ __('student_timeline.lib_curriculum_title') }}">
        @foreach($courses as $i => $course)
            @php
                $tones = ['blue', 'pink', 'orange', 'purple', 'green'];
                $tone = $tones[$i % count($tones)];
                $teacherName = $course->instructor->name ?? $course->teacher->name ?? null;
                $yearName = $course->academicYear->name ?? $course->academicSubject?->academicYear?->name ?? null;
                $subjectName = $course->academicSubject->name ?? null;
                $sectionsCount = (int) ($course->sections_count ?? 0);
                $itemsCount = (int) ($course->curriculum_items_count ?? 0);
                $lecturesCount = (int) ($course->lectures_count ?? 0);
                $learnUrl = Route::has('my-courses.learn') ? route('my-courses.learn', $course) : (Route::has('my-courses.show') ? route('my-courses.show', $course) : null);
            @endphp
            <article class="st-curriculum-card st-curriculum-card--{{ $tone }}">
                <div class="st-curriculum-card__top">
                    <span class="st-lib-badge st-lib-badge--academy">{{ __('student_timeline.lib_badge_curriculum') }}</span>
                    @if($teacherName)
                        <span class="st-lib-badge st-lib-badge--teacher">{{ $teacherName }}</span>
                    @endif
                </div>
                <h3>{{ $course->title }}</h3>
                <p class="st-curriculum-card__meta">
                    @if($yearName){{ $yearName }}@endif
                    @if($yearName && $subjectName) · @endif
                    @if($subjectName){{ $subjectName }}@endif
                </p>
                <ul class="st-curriculum-card__stats">
                    <li><i class="fas fa-layer-group" aria-hidden="true"></i> {{ trans_choice('student_timeline.lib_curriculum_sections_count', $sectionsCount, ['count' => $sectionsCount]) }}</li>
                    <li><i class="fas fa-list" aria-hidden="true"></i> {{ trans_choice('student_timeline.lib_curriculum_items_count', $itemsCount, ['count' => $itemsCount]) }}</li>
                    <li><i class="fas fa-chalkboard" aria-hidden="true"></i> {{ trans_choice('student_timeline.lib_curriculum_lectures_count', $lecturesCount, ['count' => $lecturesCount]) }}</li>
                </ul>
                <div class="st-curriculum-card__foot">
                    @if($learnUrl)
                        <a href="{{ $learnUrl }}" class="st-pill st-pill--solid">
                            <i class="fas fa-play" aria-hidden="true"></i>
                            {{ __('student_timeline.lib_curriculum_open') }}
                        </a>
                    @endif
                    <a href="{{ route('student.library.materials', ['course' => $course->id]) }}" class="st-pill st-pill--outline">
                        {{ __('student_timeline.nav_library_materials') }}
                    </a>
                </div>
            </article>
        @endforeach
    </section>

    @if(method_exists($courses, 'hasPages') && $courses->hasPages())
        <div class="st-pager">
            {{ $courses->links() }}
        </div>
    @endif
@endif
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.library_links') }}</h2>
</div>
<a href="{{ route('student.library.materials') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_library_materials') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.family_materials_side') }}</p>
</a>
<a href="{{ route('student.library.videos') }}" class="st-event-card st-event-card--pink">
    <h3>{{ __('student_timeline.nav_library_videos') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.family_videos_side') }}</p>
</a>
@endsection
