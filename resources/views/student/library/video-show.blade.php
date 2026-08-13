@extends('layouts.student-timeline')

@section('title', $libraryVideo->title ?? __('student_timeline.watch_video'))

@section('content')
@php
    $locale = app()->getLocale();
    $title = $libraryVideo->title;
    $folder = $libraryVideo->folder;
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
                    oncontextmenu="return false"
                >
                    <source src="{{ $directUrl }}" type="{{ $libraryVideo->mime_type ?: 'video/mp4' }}">
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
                <span><i class="fas fa-cloud" aria-hidden="true"></i> {{ $libraryVideo->sourceLabel() }}</span>
                @if($libraryVideo->content_theme)
                    <span><i class="fas fa-tag" aria-hidden="true"></i> {{ $libraryVideo->themeLabel($locale) }}</span>
                @endif
                @if($libraryVideo->series_title)
                    <span><i class="fas fa-film" aria-hidden="true"></i> {{ $libraryVideo->series_title }}</span>
                @endif
                @if($libraryVideo->age_label)
                    <span><i class="fas fa-child" aria-hidden="true"></i> {{ $libraryVideo->age_label }}</span>
                @endif
                @if($libraryVideo->duration_for_humans)
                    <span><i class="fas fa-clock" aria-hidden="true"></i> {{ $libraryVideo->duration_for_humans }}</span>
                @endif
            </div>
            @if($libraryVideo->description)
                <p>{{ $libraryVideo->description }}</p>
            @endif
        </div>
    </article>
</section>
@endsection
