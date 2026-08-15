@extends('layouts.student-timeline')

@section('title', __('student_timeline.family_library_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $sections = $sections ?? [];
    $hasLibraryEntitlement = (bool) ($hasLibraryEntitlement ?? false);
    $linkedTeacherCount = (int) ($linkedTeacherCount ?? 0);
    $academyFolderCount = (int) ($academyFolderCount ?? 0);
    $teacherFolderCount = (int) ($teacherFolderCount ?? 0);
    $academyMaterialCount = (int) ($academyMaterialCount ?? 0);
    $teacherMaterialCount = (int) ($teacherMaterialCount ?? 0);
    $academyVideoCount = (int) ($academyVideoCount ?? 0);
    $teacherVideoCount = (int) ($teacherVideoCount ?? 0);
    $packagesUrl = $packagesUrl ?? route('dashboard');
    $manahijItemCount = (int) ($manahijItemCount ?? 0);
    $filesTotal = $academyMaterialCount + $teacherMaterialCount + $manahijItemCount;
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
            <a href="{{ route('student.library.files') }}" class="st-pill st-pill--solid">{{ __('student_timeline.lib_files_open') }}</a>
            <a href="{{ route('student.library.videos') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_videos') }}</a>
            <a href="{{ route('student.library.curriculum') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_curriculum') }}</a>
        </div>
    </div>
    <div class="st-family-hero__trust" aria-hidden="true">
        <span><i class="fas fa-shield-alt"></i> {{ __('student_timeline.family_trust_safe') }}</span>
        <span><i class="fas fa-chalkboard-teacher"></i> {{ __('student_timeline.family_trust_supervised') }}</span>
        <span><i class="fas fa-home"></i> {{ __('student_timeline.family_trust_home') }}</span>
    </div>
</section>

@if(! $hasLibraryEntitlement)
    <div class="st-lib-package-banner" role="status">
        <div>
            <strong>{{ __('student_timeline.lib_need_package_title') }}</strong>
            <p>{{ __('student_timeline.lib_need_package_hint') }}</p>
        </div>
        <a href="{{ $packagesUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.lib_browse_packages') }}</a>
    </div>
@endif

<section class="st-lib-hub-grid st-lib-hub-grid--2" aria-label="{{ __('student_timeline.lib_sources_title') }}">
    <a href="{{ route('student.library.files') }}" class="st-lib-hub-card st-lib-hub-card--academy">
        <span class="st-lib-hub-card__icon"><i class="fas fa-folder-open" aria-hidden="true"></i></span>
        <strong>{{ __('student_timeline.lib_files_title') }}</strong>
        <em>{{ __('student_timeline.lib_files_hub_hint') }}</em>
        <span class="st-lib-hub-card__meta">
            {{ trans_choice('student_timeline.lib_items_count', $filesTotal, ['count' => $filesTotal]) }}
            · {{ __('student_timeline.lib_files_sources_meta') }}
        </span>
        <span class="st-lib-hub-card__cta">
            {{ __('student_timeline.lib_files_open') }}
            <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
        </span>
    </a>

    <a href="{{ route('student.library.videos') }}" class="st-lib-hub-card st-lib-hub-card--teacher">
        <span class="st-lib-hub-card__icon"><i class="fas fa-film" aria-hidden="true"></i></span>
        <strong>{{ __('student_timeline.nav_library_videos') }}</strong>
        <em>{{ __('student_timeline.family_videos_hint') }}</em>
        <span class="st-lib-hub-card__meta">
            {{ trans_choice('student_timeline.lib_videos_count', $academyVideoCount + $teacherVideoCount, ['count' => $academyVideoCount + $teacherVideoCount]) }}
            @if($linkedTeacherCount > 0)
                · {{ trans_choice('student_timeline.lib_teachers_count', $linkedTeacherCount, ['count' => $linkedTeacherCount]) }}
            @endif
        </span>
        <span class="st-lib-hub-card__cta">
            {{ __('student_timeline.family_open_videos') }}
            <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
        </span>
    </a>
</section>

<section class="st-family-grid" aria-label="{{ __('student_timeline.family_themes') }}">
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
<a href="{{ route('student.library.files') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.lib_files_title') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.lib_files_hub_hint') }}</p>
</a>
<a href="{{ route('student.library.videos') }}" class="st-event-card st-event-card--pink">
    <h3>{{ __('student_timeline.nav_library_videos') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.family_videos_side') }}</p>
</a>
<a href="{{ route('student.library.curriculum') }}" class="st-event-card st-event-card--green">
    <h3>{{ __('student_timeline.nav_library_curriculum') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.lib_curriculum_hub_hint') }}</p>
</a>
@if(! $hasLibraryEntitlement)
    <a href="{{ $packagesUrl }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.lib_browse_packages') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.lib_need_package_hint') }}</p>
    </a>
@endif
@endsection
