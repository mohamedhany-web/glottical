@php
    $searchQuery = $searchQuery ?? '';
    $sessionCount = $sessionCount ?? 0;
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.lessons_count', ['count' => $sessionCount]) }}
    </p>
    @if(Route::has('student.private-messages.index'))
        <div class="st-top__chips">
            <a href="{{ route('student.private-messages.index') }}" class="st-chip">
                {{ __('student_timeline.nav_feed') }}
            </a>
        </div>
    @endif
</div>

<form class="st-search" method="get" action="{{ route('student.private-lectures.index') }}" role="search">
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_lessons') }}" aria-label="{{ __('student_timeline.search_lessons') }}">
</form>
