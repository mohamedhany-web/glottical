@extends('layouts.student-timeline')

@section('title', __('student_timeline.lib_manahij_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $packagesUrl = $packagesUrl ?? route('dashboard');
    $hasFullAccess = (bool) ($hasFullAccess ?? false);
    $usedFreePreview = (bool) ($usedFreePreview ?? false);
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.lib_manahij_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.family_library_title'), 'url' => route('student.library.home')],
        ['label' => __('student_timeline.lib_manahij_title'), 'url' => null],
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
        <h2>{{ __('student_timeline.lib_manahij_title') }}</h2>
        <p>
            {{ __('student_timeline.lib_manahij_hint') }}
            @if(! $hasFullAccess)
                <br><span class="text-amber-700 font-semibold text-xs">{{ __('student_timeline.lib_manahij_preview_note') }}</span>
            @else
                <br><span class="text-emerald-700 font-semibold text-xs">{{ __('student_timeline.lib_manahij_full_note') }}</span>
            @endif
        </p>
    </div>
    <div class="st-msg-intro__actions">
        <a href="{{ route('student.library.home') }}" class="st-pill st-pill--outline">{{ __('student_timeline.family_library_title') }}</a>
        <a href="{{ route('student.library.curriculum') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_curriculum') }}</a>
    </div>
</section>

<form class="st-lib-search" method="get" action="{{ route('curriculum-library.index') }}" role="search">
    <label class="sr-only" for="manahij-q">{{ __('student_timeline.lib_manahij_search') }}</label>
    <input id="manahij-q" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('student_timeline.lib_manahij_search') }}">
    <select name="category_id" class="st-lib-search__select">
        <option value="">{{ __('student_timeline.lib_manahij_all_categories') }}</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="language" class="st-lib-search__select">
        <option value="">{{ __('student_timeline.lib_manahij_all_languages') }}</option>
        <option value="ar" @selected(request('language') === 'ar')>العربية</option>
        <option value="en" @selected(request('language') === 'en')>English</option>
        <option value="fr" @selected(request('language') === 'fr')>Français</option>
    </select>
    <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.search') }}</button>
</form>

@if($items->isEmpty())
    <div class="st-empty-panel">
        <h3>{{ __('student_timeline.lib_manahij_empty_title') }}</h3>
        <p>{{ __('student_timeline.lib_manahij_empty_hint') }}</p>
        <a href="{{ route('student.library.home') }}" class="st-pill st-pill--solid">{{ __('student_timeline.family_library_title') }}</a>
    </div>
@else
    <section class="st-curriculum-grid" aria-label="{{ __('student_timeline.lib_manahij_title') }}">
        @foreach($items as $i => $item)
            @php
                $tones = ['blue', 'pink', 'orange', 'purple', 'green'];
                $tone = $tones[$i % count($tones)];
                $isLocked = (! $hasFullAccess) && $usedFreePreview;
                $href = $isLocked ? $packagesUrl : route('curriculum-library.show', $item);
            @endphp
            <a href="{{ $href }}" class="st-curriculum-card st-curriculum-card--{{ $tone }} {{ $isLocked ? 'opacity-90' : '' }}">
                <div class="st-curriculum-card__top">
                    <span class="st-lib-badge st-lib-badge--academy">{{ __('student_timeline.lib_badge_manahij') }}</span>
                    @if($item->is_free_preview)
                        <span class="st-lib-badge st-lib-badge--teacher">{{ __('student_timeline.lib_manahij_free') }}</span>
                    @elseif($isLocked)
                        <span class="st-lib-badge"><i class="fas fa-lock"></i> {{ __('student_timeline.lib_manahij_locked') }}</span>
                    @endif
                </div>
                <h3>{{ $item->title }}</h3>
                <p class="st-curriculum-card__meta">
                    @if($item->category){{ $item->category->name }}@endif
                    @if($item->category && $item->subject) · @endif
                    @if($item->subject){{ $item->subject }}@endif
                    @if($item->grade_level) · {{ $item->grade_level }}@endif
                </p>
                @if($item->description)
                    <p class="text-sm text-slate-600 mt-2 line-clamp-2">{{ \Illuminate\Support\Str::limit($item->description, 100) }}</p>
                @endif
                <div class="st-curriculum-card__foot">
                    <span class="st-pill st-pill--solid">
                        {{ $isLocked ? __('student_timeline.lib_browse_packages') : __('student_timeline.lib_manahij_open') }}
                        <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </section>
    @if($items->hasPages())
        <div class="mt-6">{{ $items->withQueryString()->links() }}</div>
    @endif
@endif
@endsection
