@extends('layouts.student-timeline')

@section('title', $lecture->title ?? __('student_timeline.watch_video'))

@section('content')
@php
    $locale = app()->getLocale();
    $title = $lecture->title;
    $backUrl = route('student.library.videos', array_filter([
        'source' => 'lectures',
        'lang' => request('lang'),
    ]));
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_library_videos'), 'url' => route('student.library.videos')],
        ['label' => $title, 'url' => null],
    ],
])

<section class="st-player-page">
    <div class="st-player-bar">
        <a href="{{ $backUrl }}" class="st-pill st-pill--outline">
            <i class="fas fa-arrow-{{ $locale === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
            {{ __('student_timeline.back_to_library') }}
        </a>
        @if($lecture->course?->title)
            <span class="st-chip">
                <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                {{ $lecture->course->title }}
            </span>
        @endif
    </div>

    <article class="st-player-shell">
        <div class="st-player-frame" data-source="{{ $source }}">
            @if($embedUrl)
                <iframe
                    src="{{ $embedUrl }}"
                    title="{{ $title }}"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                    loading="eager"
                ></iframe>
            @elseif($directUrl)
                <video
                    controls
                    playsinline
                    preload="metadata"
                    @if($thumbnail) poster="{{ $thumbnail }}" @endif
                    controlslist="nodownload"
                    oncontextmenu="return false"
                >
                    <source src="{{ $directUrl }}" type="video/mp4">
                    {{ __('student_timeline.video_unsupported') }}
                </video>
            @else
                <div class="st-player-fallback">
                    <p>{{ __('student_timeline.video_unavailable') }}</p>
                </div>
            @endif
        </div>

        <div class="st-player-meta">
            <h1>{{ $title }}</h1>
            <div class="st-player-meta__row">
                @if($lecture->course?->title)
                    <span><i class="fas fa-graduation-cap" aria-hidden="true"></i> {{ $lecture->course->title }}</span>
                @endif
                @if($lecture->instructor?->name)
                    <span><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> {{ $lecture->instructor->name }}</span>
                @endif
            </div>
            @if($lecture->description)
                <p>{{ $lecture->description }}</p>
            @endif
            @if(Route::has('my-courses.learn'))
                <div class="st-biz-banner__actions" style="margin-top:1rem">
                    <a href="{{ route('my-courses.learn', $lecture->course_id) }}" class="st-pill st-pill--outline">
                        {{ __('student_timeline.open_in_course') }}
                    </a>
                </div>
            @endif
        </div>
    </article>
</section>
@endsection
