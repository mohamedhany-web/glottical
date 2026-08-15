@extends('layouts.student-timeline')

@section('title', __('student_timeline.lib_files_title'))

@section('content')
@php
    $locale = $locale ?? app()->getLocale();
    $tab = $tab ?? 'all';
    $searchQuery = $searchQuery ?? '';
    $cards = $cards ?? collect();
    $materialCount = (int) ($materialCount ?? 0);
    $manahijCount = (int) ($manahijCount ?? 0);
    $hasLibraryEntitlement = (bool) ($hasLibraryEntitlement ?? false);
    $packagesUrl = $packagesUrl ?? route('dashboard');
    $empty = method_exists($cards, 'isEmpty') ? $cards->isEmpty() : collect($cards)->isEmpty();
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.lib_files_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.family_library_title'), 'url' => route('student.library.home')],
        ['label' => __('student_timeline.lib_files_title'), 'url' => null],
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
        <h2>{{ __('student_timeline.lib_files_title') }}</h2>
        <p>{{ __('student_timeline.lib_files_hint') }}</p>
    </div>
    <div class="st-msg-intro__actions">
        <a href="{{ route('student.library.home') }}" class="st-pill st-pill--outline">{{ __('student_timeline.family_library_title') }}</a>
        <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_materials') }}</a>
        <a href="{{ route('curriculum-library.index') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_manahij') }}</a>
    </div>
</section>

@if(! $hasLibraryEntitlement)
    <div class="st-lib-package-banner" role="status">
        <div>
            <strong>{{ __('student_timeline.lib_need_package_title') }}</strong>
            <p>{{ __('student_timeline.lib_files_package_hint') }}</p>
        </div>
        <a href="{{ $packagesUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.lib_browse_packages') }}</a>
    </div>
@endif

<div class="st-family-hero__actions" style="margin-bottom:1rem">
    <a href="{{ route('student.library.files', array_filter(['tab' => 'all', 'q' => $searchQuery ?: null])) }}" class="st-pill {{ $tab === 'all' ? 'st-pill--solid' : 'st-pill--outline' }}">{{ __('student_timeline.lib_files_tab_all') }}</a>
    <a href="{{ route('student.library.files', array_filter(['tab' => 'materials', 'q' => $searchQuery ?: null])) }}" class="st-pill {{ $tab === 'materials' ? 'st-pill--solid' : 'st-pill--outline' }}">{{ __('student_timeline.lib_files_tab_materials') }} ({{ $materialCount }})</a>
    <a href="{{ route('student.library.files', array_filter(['tab' => 'manahij', 'q' => $searchQuery ?: null])) }}" class="st-pill {{ $tab === 'manahij' ? 'st-pill--solid' : 'st-pill--outline' }}">{{ __('student_timeline.lib_files_tab_manahij') }} ({{ $manahijCount }})</a>
</div>

<form class="st-lib-search" method="get" action="{{ route('student.library.files') }}" role="search">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <label class="sr-only" for="lib-files-q">{{ __('student_timeline.lib_files_search') }}</label>
    <input id="lib-files-q" type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.lib_files_search') }}">
    <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.search') }}</button>
</form>

@if($empty)
    <div class="st-empty-panel">
        <h3>{{ __('student_timeline.lib_files_empty_title') }}</h3>
        <p>{{ __('student_timeline.lib_files_empty_hint') }}</p>
    </div>
@else
    <section class="st-curriculum-grid" aria-label="{{ __('student_timeline.lib_files_title') }}">
        @foreach($cards as $i => $card)
            @php
                $tones = ['blue', 'pink', 'orange', 'purple', 'green'];
                $tone = $tones[$i % count($tones)];
                $badge = $card['badge'] ?? 'academy';
            @endphp
            <a href="{{ $card['url'] }}" class="st-curriculum-card st-curriculum-card--{{ $tone }} {{ !empty($card['locked']) ? 'opacity-90' : '' }}">
                <div class="st-curriculum-card__top">
                    @if($badge === 'manahij')
                        <span class="st-lib-badge st-lib-badge--academy">{{ __('student_timeline.lib_badge_manahij') }}</span>
                    @elseif($badge === 'teacher')
                        <span class="st-lib-badge st-lib-badge--teacher">{{ __('student_timeline.lib_badge_teacher') }}</span>
                    @else
                        <span class="st-lib-badge st-lib-badge--academy">{{ __('student_timeline.lib_badge_academy') }}</span>
                    @endif
                    @if(!empty($card['locked']))
                        <span class="st-lib-badge"><i class="fas fa-lock"></i></span>
                    @endif
                </div>
                <h3><i class="{{ $card['icon'] ?? 'fas fa-file' }}" aria-hidden="true"></i> {{ $card['title'] }}</h3>
                @if(!empty($card['meta']))
                    <p class="st-curriculum-card__meta">{{ $card['meta'] }}</p>
                @endif
                <div class="st-curriculum-card__foot">
                    <span class="st-pill st-pill--solid">
                        {{ !empty($card['locked']) ? __('student_timeline.lib_browse_packages') : __('student_timeline.open_in_platform') }}
                        <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </section>
@endif
@endsection
