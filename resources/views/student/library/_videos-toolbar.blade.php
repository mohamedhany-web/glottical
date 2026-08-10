@php
    $searchQuery = $searchQuery ?? '';
    $videoCount = $videoCount ?? 0;
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.videos_count', ['count' => $videoCount]) }}
    </p>
    <div class="st-top__chips">
        @if(Route::has('student.library.materials'))
            <a href="{{ route('student.library.materials') }}" class="st-chip">
                {{ __('student_timeline.nav_library_materials') }}
            </a>
        @endif
        @if(Route::has('student.lectures.index'))
            <a href="{{ route('student.lectures.index') }}" class="st-chip">
                {{ __('student_timeline.nav_lectures') }}
            </a>
        @endif
    </div>
</div>

<form class="st-search" method="get" action="{{ route('student.library.videos') }}" role="search">
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_videos') }}" aria-label="{{ __('student_timeline.search_videos') }}">
</form>
