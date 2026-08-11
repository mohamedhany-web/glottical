@php
    $searchQuery = $searchQuery ?? '';
    $materialCount = $materialCount ?? 0;
    $courseId = (int) ($courseId ?? 0);
    $lectureId = (int) ($lectureId ?? 0);
    $typeFilter = $typeFilter ?? 'all';
    $sort = $sort ?? 'newest';
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.materials_count', ['count' => $materialCount]) }}
    </p>
    <div class="st-top__chips">
        @if(Route::has('student.library.videos'))
            <a href="{{ route('student.library.videos') }}" class="st-chip">
                {{ __('student_timeline.nav_library_videos') }}
            </a>
        @endif
        @if(Route::has('student.lectures.index'))
            <a href="{{ route('student.lectures.index') }}" class="st-chip">
                {{ __('student_timeline.nav_lectures') }}
            </a>
        @endif
    </div>
</div>

<form class="st-search" method="get" action="{{ route('student.library.materials') }}" role="search">
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    @if($courseId)
        <input type="hidden" name="course" value="{{ $courseId }}">
    @endif
    @if($lectureId)
        <input type="hidden" name="lecture" value="{{ $lectureId }}">
    @endif
    @if($typeFilter && $typeFilter !== 'all')
        <input type="hidden" name="type" value="{{ $typeFilter }}">
    @endif
    @if($sort && $sort !== 'newest')
        <input type="hidden" name="sort" value="{{ $sort }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_materials') }}" aria-label="{{ __('student_timeline.search_materials') }}">
</form>
