@extends('layouts.student-timeline')

@section('title', __('student_timeline.family_library_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $sections = $sections ?? [];
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.family_library_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.family_library_title'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif

<section class="st-family-hero">
    <div class="st-family-hero__copy">
        <p class="st-family-hero__eyebrow">{{ __('student_timeline.family_library_eyebrow') }}</p>
        <h2>{{ __('student_timeline.family_library_title') }}</h2>
        <p>{{ __('student_timeline.family_library_hint') }}</p>
        <div class="st-family-hero__actions">
            <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--solid">{{ __('student_timeline.nav_library_materials') }}</a>
            <a href="{{ route('student.library.videos') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_videos') }}</a>
        </div>
    </div>
    <div class="st-family-hero__trust" aria-hidden="true">
        <span><i class="fas fa-shield-alt"></i> {{ __('student_timeline.family_trust_safe') }}</span>
        <span><i class="fas fa-chalkboard-teacher"></i> {{ __('student_timeline.family_trust_supervised') }}</span>
        <span><i class="fas fa-home"></i> {{ __('student_timeline.family_trust_home') }}</span>
    </div>
</section>

<section class="st-family-grid" aria-label="{{ __('student_timeline.family_library_title') }}">
    @foreach($sections as $section)
        <a href="{{ $section['url'] }}" class="st-family-card st-family-card--{{ $section['tone'] }}">
            <span class="st-family-card__icon"><i class="{{ $section['icon'] }}" aria-hidden="true"></i></span>
            <strong>{{ $section['label'] }}</strong>
            <em>{{ $section['hint'] }}</em>
            <span class="st-family-card__cta">
                {{ $section['kind'] === 'videos' ? __('student_timeline.family_open_videos') : __('student_timeline.family_open_materials') }}
                <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
            </span>
        </a>
    @endforeach
</section>
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
