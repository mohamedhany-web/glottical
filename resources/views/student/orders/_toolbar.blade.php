@php
    $searchQuery = $searchQuery ?? '';
    $filter = $filter ?? 'all';
    $counts = $counts ?? ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
    $filterUrl = function (string $key) use ($searchQuery) {
        return route('orders.index', array_filter([
            'filter' => $key === 'all' ? null : $key,
            'q' => $searchQuery !== '' ? $searchQuery : null,
            'lang' => request('lang'),
        ], fn ($v) => $v !== null && $v !== ''));
    };
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.orders_count', ['count' => $counts['all'] ?? 0]) }}
    </p>
    <div class="st-top__chips">
        <a href="{{ $filterUrl('all') }}" class="st-chip {{ $filter === 'all' ? 'is-active' : '' }}">{{ __('student_timeline.filter_all') }} ({{ $counts['all'] }})</a>
        <a href="{{ $filterUrl('pending') }}" class="st-chip {{ $filter === 'pending' ? 'is-active' : '' }}">{{ __('student_timeline.order_pending') }} ({{ $counts['pending'] }})</a>
        <a href="{{ $filterUrl('approved') }}" class="st-chip {{ $filter === 'approved' ? 'is-active' : '' }}">{{ __('student_timeline.order_approved') }} ({{ $counts['approved'] }})</a>
        <a href="{{ $filterUrl('rejected') }}" class="st-chip {{ $filter === 'rejected' ? 'is-active' : '' }}">{{ __('student_timeline.order_rejected') }} ({{ $counts['rejected'] }})</a>
    </div>
</div>

<form class="st-search" method="get" action="{{ route('orders.index') }}" role="search">
    @if($filter !== 'all')
        <input type="hidden" name="filter" value="{{ $filter }}">
    @endif
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_orders') }}" aria-label="{{ __('student_timeline.search_orders') }}">
</form>
