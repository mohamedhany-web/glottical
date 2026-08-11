@extends('layouts.student-timeline')

@section('title', $liveRecording->title ?? __('student_timeline.watch_video'))

@section('content')
@php
    $locale = app()->getLocale();
    $title = $liveRecording->title
        ?: ($liveRecording->session?->title ?: (__('student_timeline.recording').' #'.$liveRecording->id));
    $folder = $liveRecording->folder;
    $backUrl = route('student.library.videos', array_filter([
        'folder' => $folder?->slug ?: $folder?->id,
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
        @if($folder)
            <a href="{{ route('student.library.videos', ['folder' => $folder->slug ?: $folder->id]) }}" class="st-chip">
                <i class="{{ $folder->icon ?: 'fas fa-folder' }}" aria-hidden="true"></i>
                {{ $folder->displayName($locale) }}
            </a>
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
                @if($liveRecording->session?->course?->title)
                    <span><i class="fas fa-graduation-cap" aria-hidden="true"></i> {{ $liveRecording->session->course->title }}</span>
                @endif
                @if($liveRecording->session?->instructor?->name)
                    <span><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> {{ $liveRecording->session->instructor->name }}</span>
                @endif
                @if($liveRecording->duration_seconds)
                    <span><i class="fas fa-clock" aria-hidden="true"></i> {{ $liveRecording->duration_for_humans }}</span>
                @endif
            </div>
            <p class="st-player-note">{{ __('student_timeline.player_stay_note') }}</p>
        </div>
    </article>
</section>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.library_links') }}</h2>
</div>

<a href="{{ route('student.library.videos') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_library_videos') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.videos_hint') }}</p>
</a>

@if(Route::has('student.library.materials'))
    <a href="{{ route('student.library.materials') }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.nav_library_materials') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.materials_hint') }}</p>
    </a>
@endif

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
