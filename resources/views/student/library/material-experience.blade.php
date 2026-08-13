@extends('layouts.student-timeline')

@section('title', $material->title ?: $material->file_name)

@section('content')
@php
    $locale = app()->getLocale();
    $title = $material->title ?: $material->file_name;
    $isGame = $isGame ?? false;
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.family_library_title'), 'url' => route('student.library.home')],
        ['label' => __('student_timeline.nav_library_materials'), 'url' => route('student.library.materials')],
        ['label' => $title, 'url' => null],
    ],
])

<section class="st-xp-shell">
    <div class="st-xp-head">
        <div>
            <p class="st-xp-kicker">{{ $isGame ? __('student_timeline.family_play_mode') : __('student_timeline.family_view_mode') }}</p>
            <h2>{{ $title }}</h2>
            <p>{{ $material->description ?: __('student_timeline.family_experience_hint') }}</p>
        </div>
        <div class="st-xp-actions">
            <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--outline">{{ __('student_timeline.back_to_materials') }}</a>
            @if($material->downloadUrl())
                <a href="{{ $material->downloadUrl() }}" class="st-pill st-pill--solid">{{ __('student_timeline.download_file') }}</a>
            @endif
        </div>
    </div>

    <div class="st-xp-frame-wrap">
        <iframe
            class="st-xp-frame"
            title="{{ $title }}"
            src="{{ $frameUrl }}"
            sandbox="allow-scripts allow-forms allow-modals allow-pointer-lock allow-popups-to-escape-sandbox"
            referrerpolicy="no-referrer"
            loading="lazy"
        ></iframe>
    </div>
</section>
@endsection
