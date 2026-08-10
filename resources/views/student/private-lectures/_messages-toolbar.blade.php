@php
    $searchQuery = $searchQuery ?? '';
    $threadCount = $threadCount ?? 0;
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.threads_count', ['count' => $threadCount]) }}
    </p>
</div>

<form class="st-search" method="get" action="{{ route('student.private-messages.index') }}" role="search">
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_messages') }}" aria-label="{{ __('student_timeline.search_messages') }}">
</form>
