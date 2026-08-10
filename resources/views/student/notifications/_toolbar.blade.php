@php
    $status = request('status', '');
    $type = request('type', '');
    $searchQuery = trim((string) request('q', ''));
    $stats = $stats ?? ['total' => 0, 'unread' => 0, 'today' => 0, 'urgent' => 0];
    $chipUrl = function (array $overrides = []) use ($status, $type, $searchQuery) {
        $nextStatus = array_key_exists('status', $overrides) ? $overrides['status'] : ($status !== '' ? $status : null);
        $nextType = array_key_exists('type', $overrides) ? $overrides['type'] : ($type !== '' ? $type : null);
        $nextQ = array_key_exists('q', $overrides) ? $overrides['q'] : ($searchQuery !== '' ? $searchQuery : null);
        $params = array_filter([
            'status' => $nextStatus,
            'type' => $nextType,
            'q' => $nextQ,
            'lang' => request('lang'),
        ], fn ($v) => $v !== null && $v !== '');

        return route('notifications', $params);
    };
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.notif_unread_count', ['count' => $stats['unread'] ?? 0]) }}
    </p>
    <div class="st-top__chips">
        <a href="{{ $chipUrl(['status' => null]) }}" class="st-chip {{ $status === '' ? 'is-active' : '' }}">{{ __('student_timeline.filter_all') }}</a>
        <a href="{{ $chipUrl(['status' => 'unread']) }}" class="st-chip {{ $status === 'unread' ? 'is-active' : '' }}">{{ __('student_timeline.notif_unread') }}</a>
        <a href="{{ $chipUrl(['status' => 'read']) }}" class="st-chip {{ $status === 'read' ? 'is-active' : '' }}">{{ __('student_timeline.notif_read') }}</a>
        @if(($stats['unread'] ?? 0) > 0)
            <button type="button" class="st-chip is-active" id="stMarkAllRead">{{ __('student_timeline.mark_all_read') }}</button>
        @endif
    </div>
</div>

<form class="st-search" method="get" action="{{ route('notifications') }}" role="search">
    @if($status !== '')
        <input type="hidden" name="status" value="{{ $status }}">
    @endif
    @if($type !== '')
        <input type="hidden" name="type" value="{{ $type }}">
    @endif
    @if(request('lang'))
        <input type="hidden" name="lang" value="{{ request('lang') }}">
    @endif
    <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('student_timeline.search_notifications') }}" aria-label="{{ __('student_timeline.search_notifications') }}">
</form>
