@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_library_videos'))

@section('content')
@php
    $locale = app()->getLocale();
    $videos = $videos ?? collect();
    $searchQuery = $searchQuery ?? '';
    $tones = ['blue', 'pink', 'orange', 'purple'];
    $videosEmpty = $videos instanceof \Illuminate\Contracts\Pagination\Paginator
        ? $videos->isEmpty()
        : collect($videos)->isEmpty();
    $videoCount = method_exists($videos, 'total')
        ? $videos->total()
        : collect($videos)->count();
    $watchRoute = Route::has('student.live-recordings.show')
        ? 'student.live-recordings.show'
        : (Route::has('live-recordings.show') ? 'live-recordings.show' : null);

    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        if ($seconds <= 0) {
            return null;
        }
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;

        return sprintf('%d:%02d', $m, $s);
    };
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_library_videos'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_library_videos'), 'url' => null],
    ],
    'toolbarView' => 'student.library._videos-toolbar',
    'toolbarData' => [
        'searchQuery' => $searchQuery,
        'videoCount' => $videoCount,
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.videos_title') }}</h2>
        <p>{{ __('student_timeline.videos_hint') }}</p>
    </div>
    @if(Route::has('student.library.materials'))
        <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_materials') }}</a>
    @endif
</section>

@if($videosEmpty)
    <div class="st-empty-panel">
        <h3>{{ __('student_timeline.no_videos') }}</h3>
        <p>{{ __('student_timeline.no_videos_hint') }}</p>
        <div class="st-biz-banner__actions">
            @if(Route::has('student.library.materials'))
                <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--solid">{{ __('student_timeline.nav_library_materials') }}</a>
            @endif
            <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
        </div>
    </div>
@else
    <section class="st-video-grid" aria-label="{{ __('student_timeline.videos_title') }}">
        @foreach($videos as $i => $video)
            @php
                $tone = $tones[$i % count($tones)];
                $course = $video->session?->course?->title ?: __('student_timeline.recording');
                $title = $video->title
                    ?: ($video->session?->title ?: (__('student_timeline.recording').' #'.$video->id));
                $instructor = $video->session?->instructor?->name;
                $duration = $formatDuration($video->duration_seconds ?? 0);
                $watchUrl = $watchRoute ? route($watchRoute, $video) : null;
            @endphp
            <article class="st-video-card st-video-card--{{ $tone }}">
                <div class="st-video-card__thumb" aria-hidden="true">
                    <i class="fas fa-play"></i>
                    @if($duration)
                        <span class="st-video-card__dur">{{ $duration }}</span>
                    @endif
                </div>
                <div class="st-video-card__body">
                    <p class="st-video-card__course">{{ $course }}</p>
                    <h3>{{ $title }}</h3>
                    @if($instructor)
                        <p class="st-video-card__meta">{{ $instructor }}</p>
                    @endif
                </div>
                <div class="st-video-card__foot">
                    @if($watchUrl)
                        <a href="{{ $watchUrl }}" class="st-pill st-pill--solid">
                            <i class="fas fa-play" aria-hidden="true"></i>
                            {{ __('student_timeline.watch_video') }}
                        </a>
                    @else
                        <span class="st-lib-card__missing">{{ __('student_timeline.video_unavailable') }}</span>
                    @endif
                </div>
            </article>
        @endforeach
    </section>

    @if(method_exists($videos, 'hasPages') && $videos->hasPages())
        <div class="st-pager">
            {{ $videos->links() }}
        </div>
    @endif
@endif
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.library_links') }}</h2>
</div>

@if(Route::has('student.library.materials'))
    <a href="{{ route('student.library.materials') }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.nav_library_materials') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.materials_hint') }}</p>
    </a>
@endif

@if(Route::has('student.lectures.index'))
    <a href="{{ route('student.lectures.index') }}" class="st-event-card st-event-card--blue">
        <h3>{{ __('student_timeline.nav_lectures') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.lectures_side_hint') }}</p>
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
