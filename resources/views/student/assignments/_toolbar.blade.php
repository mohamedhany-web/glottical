@php
    $searchQuery = $searchQuery ?? '';
    $filter = $filter ?? 'all';
    $counts = $counts ?? ['all' => 0, 'pending' => 0, 'submitted' => 0, 'graded' => 0];
    $filterUrl = function (string $key) use ($searchQuery) {
        return route('student.assignments.index', array_filter([
            'filter' => $key === 'all' ? null : $key,
            'q' => $searchQuery !== '' ? $searchQuery : null,
            'lang' => request('lang'),
        ], fn ($v) => $v !== null && $v !== ''));
    };
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.assignments_count', ['count' => $counts['all'] ?? 0]) }}
    </p>
    <div class="st-top__chips">
        <a href="{{ $filterUrl('all') }}" class="st-chip {{ $filter === 'all' ? 'is-active' : '' }}">{{ __('student_timeline.filter_all') }} ({{ $counts['all'] }})</a>
        <a href="{{ $filterUrl('pending') }}" class="st-chip {{ $filter === 'pending' ? 'is-active' : '' }}">{{ __('student_timeline.filter_pending') }} ({{ $counts['pending'] }})</a>
        <a href="{{ $filterUrl('submitted') }}" class="st-chip {{ $filter === 'submitted' ? 'is-active' : '' }}">{{ __('student_timeline.filter_submitted') }} ({{ $counts['submitted'] }})</a>
        <a href="{{ $filterUrl('graded') }}" class="st-chip {{ $filter === 'graded' ? 'is-active' : '' }}">{{ __('student_timeline.filter_graded') }} ({{ $counts['graded'] }})</a>
    </div>
</div>

<form class="st-search" method="get" action="{{ route('student.assignments.index') }}" role="search">
    @if($filter !== 'all')
        <input type="hidden" name="filter" value="{{ $filter }}">
    @endif
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_assignments') }}" aria-label="{{ __('student_timeline.search_assignments') }}">
</form>
