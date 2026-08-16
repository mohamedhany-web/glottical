@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_library_curriculum'))

@section('content')
@php
    $locale = $locale ?? app()->getLocale();
    $items = $items ?? collect();
    $grouped = $grouped ?? collect();
    $categories = $categories ?? collect();
    $grades = $grades ?? collect();
    $subjects = $subjects ?? collect();
    $categoryId = (int) ($categoryId ?? 0);
    $grade = (string) ($grade ?? '');
    $subject = (string) ($subject ?? '');
    $searchQuery = $searchQuery ?? '';
    $hasFilters = (bool) ($hasFilters ?? false);
    $packagesUrl = $packagesUrl ?? route('dashboard');
    $hasFullAccess = (bool) ($hasFullAccess ?? false);
    $usedFreePreview = (bool) ($usedFreePreview ?? false);
    $itemsEmpty = method_exists($items, 'isEmpty') ? $items->isEmpty() : collect($items)->isEmpty();
    $itemCount = collect($items)->count();
    $tones = ['blue', 'pink', 'orange', 'purple', 'green'];
    $cardIndex = 0;
    $currQuery = function (array $override = []) use ($searchQuery, $categoryId, $grade, $subject) {
        $cat = array_key_exists('category_id', $override) ? (int) $override['category_id'] : $categoryId;
        $g = array_key_exists('grade', $override) ? (string) $override['grade'] : $grade;
        $s = array_key_exists('subject', $override) ? (string) $override['subject'] : $subject;
        $q = array_key_exists('q', $override) ? (string) $override['q'] : $searchQuery;

        return array_filter([
            'lang' => request('lang') ?: null,
            'q' => $q !== '' ? $q : null,
            'category_id' => $cat > 0 ? $cat : null,
            'grade' => $g !== '' ? $g : null,
            'subject' => $s !== '' ? $s : null,
        ], fn ($value) => $value !== null && $value !== '');
    };
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
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.lib_curriculum_title') }}</h2>
        <p>
            {{ __('student_timeline.lib_curriculum_hint') }}
            @if(! $hasFullAccess)
                <br><span class="text-amber-700 font-semibold text-xs">{{ __('student_timeline.lib_manahij_preview_note') }}</span>
            @else
                <br><span class="text-emerald-700 font-semibold text-xs">{{ __('student_timeline.lib_manahij_full_note') }}</span>
            @endif
        </p>
    </div>
    <div class="st-msg-intro__actions">
        <a href="{{ route('student.library.home') }}" class="st-pill st-pill--outline">{{ __('student_timeline.family_library_title') }}</a>
        <a href="{{ route('student.library.files') }}" class="st-pill st-pill--outline">{{ __('student_timeline.lib_files_title') }}</a>
    </div>
</section>

@if($categories->isNotEmpty())
    <section class="st-theme-strip" aria-label="{{ __('student_timeline.lib_manahij_all_categories') }}">
        <a href="{{ route('student.library.curriculum', $currQuery(['category_id' => 0, 'grade' => ''])) }}"
           class="st-theme-chip {{ $categoryId === 0 ? 'is-active' : '' }}">{{ __('student_timeline.lib_manahij_all_categories') }}</a>
        @foreach($categories as $cat)
            <a href="{{ route('student.library.curriculum', $currQuery(['category_id' => $cat->id])) }}"
               class="st-theme-chip {{ $categoryId === (int) $cat->id ? 'is-active' : '' }}">{{ $cat->name }}</a>
        @endforeach
    </section>
@endif

<form class="st-lib-search" method="get" action="{{ route('student.library.curriculum') }}" role="search">
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    @if($categoryId > 0)
        <input type="hidden" name="category_id" value="{{ $categoryId }}">
    @endif
    <label class="sr-only" for="lib-curriculum-q">{{ __('student_timeline.lib_curriculum_search') }}</label>
    <input id="lib-curriculum-q" type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.lib_curriculum_search') }}">
    @if($grades->isNotEmpty())
        <label class="sr-only" for="lib-curriculum-grade">{{ __('student_timeline.lib_curriculum_grade') }}</label>
        <select id="lib-curriculum-grade" name="grade" class="st-lib-search__select" onchange="this.form.submit()">
            <option value="">{{ __('student_timeline.lib_curriculum_all_grades') }}</option>
            @foreach($grades as $gradeOption)
                <option value="{{ $gradeOption }}" @selected($grade === (string) $gradeOption)>{{ $gradeOption }}</option>
            @endforeach
        </select>
    @endif
    @if($subjects->isNotEmpty())
        <label class="sr-only" for="lib-curriculum-subject">{{ __('student_timeline.lib_curriculum_subject') }}</label>
        <select id="lib-curriculum-subject" name="subject" class="st-lib-search__select" onchange="this.form.submit()">
            <option value="">{{ __('student_timeline.lib_curriculum_all_subjects') }}</option>
            @foreach($subjects as $subjectOption)
                <option value="{{ $subjectOption }}" @selected($subject === (string) $subjectOption)>{{ $subjectOption }}</option>
            @endforeach
        </select>
    @endif
    <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.search') }}</button>
    @if($hasFilters)
        <a href="{{ route('student.library.curriculum', array_filter(['lang' => request('lang') ?: null])) }}" class="st-pill st-pill--outline">{{ __('student_timeline.lib_curriculum_clear_filters') }}</a>
    @endif
</form>

<p class="st-lib-count">{{ trans_choice('student_timeline.lib_curriculum_courses_count', $itemCount, ['count' => $itemCount]) }}</p>

@if($itemsEmpty)
    <div class="st-empty-panel">
        <h3>{{ __('student_timeline.lib_curriculum_empty_title') }}</h3>
        <p>{{ $hasFilters ? __('student_timeline.lib_curriculum_empty_filter_hint') : __('student_timeline.lib_curriculum_empty_hint') }}</p>
        <div class="st-biz-banner__actions">
            @if($hasFilters)
                <a href="{{ route('student.library.curriculum', array_filter(['lang' => request('lang') ?: null])) }}" class="st-pill st-pill--solid">{{ __('student_timeline.lib_curriculum_clear_filters') }}</a>
            @endif
            <a href="{{ route('student.library.home') }}" class="st-pill st-pill--outline">{{ __('student_timeline.family_library_title') }}</a>
        </div>
    </div>
@else
    @foreach($grouped as $group)
        <section class="st-curr-group" aria-label="{{ $group['name'] ?: __('student_timeline.lib_curriculum_uncategorized') }}">
            <header class="st-curr-group__head">
                <h3>{{ $group['name'] ?: __('student_timeline.lib_curriculum_uncategorized') }}</h3>
                <span class="st-curr-group__count">{{ trans_choice('student_timeline.lib_curriculum_courses_count', $group['items_count'], ['count' => $group['items_count']]) }}</span>
            </header>
            @foreach($group['grades'] as $gradeGroup)
                @if($gradeGroup['name'])
                    <h4 class="st-curr-group__subject">{{ $gradeGroup['name'] }}</h4>
                @endif
                <div class="st-curriculum-grid">
                    @foreach($gradeGroup['items'] as $item)
                        @php
                            $tone = $tones[$cardIndex % count($tones)];
                            $cardIndex++;
                            $isLocked = (! $hasFullAccess) && $usedFreePreview;
                            $href = $isLocked ? $packagesUrl : route('curriculum-library.show', $item);
                            $sectionsCount = (int) ($item->sections_count ?? 0);
                            $filesCount = (int) ($item->files_count ?? 0);
                        @endphp
                        <article class="st-curriculum-card st-curriculum-card--{{ $tone }}">
                            <div class="st-curriculum-card__top">
                                <span class="st-lib-badge st-lib-badge--academy">{{ __('student_timeline.lib_badge_curriculum') }}</span>
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
                            <ul class="st-curriculum-card__stats">
                                <li><i class="fas fa-layer-group" aria-hidden="true"></i> {{ trans_choice('student_timeline.lib_curriculum_sections_count', $sectionsCount, ['count' => $sectionsCount]) }}</li>
                                <li><i class="fas fa-file" aria-hidden="true"></i> {{ trans_choice('student_timeline.lib_curriculum_files_count', $filesCount, ['count' => $filesCount]) }}</li>
                            </ul>
                            <div class="st-curriculum-card__foot">
                                <a href="{{ $href }}" class="st-pill st-pill--solid">
                                    <i class="fas fa-book-open" aria-hidden="true"></i>
                                    {{ $isLocked ? __('student_timeline.lib_browse_packages') : __('student_timeline.lib_curriculum_open') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        </section>
    @endforeach
@endif
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
@endsection
