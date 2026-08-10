@php
    $searchQuery = $searchQuery ?? '';
    $sortMode = $sortMode ?? 'classes';
    $sortUrl = $sortUrl ?? route('student.classes.index');
    $classCount = $classCount ?? 0;
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.classes_count', ['count' => $classCount]) }}
    </p>
    <div class="st-top__chips">
        <a href="{{ $sortUrl }}" class="st-chip {{ $sortMode === 'progress' ? 'is-active' : '' }}">
            <img src="{{ asset('img/student-timeline/filter.svg') }}" alt="" width="14" height="14">
            {{ $sortMode === 'progress' ? __('student_timeline.sort_by_progress') : __('student_timeline.sort_by_classes') }}
        </a>
    </div>
</div>

<form class="st-search" method="get" action="{{ route('student.classes.index') }}" role="search">
    @if($sortMode !== 'classes')
        <input type="hidden" name="sort" value="{{ $sortMode }}">
    @endif
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_classes') }}" aria-label="{{ __('student_timeline.search_classes') }}">
</form>
