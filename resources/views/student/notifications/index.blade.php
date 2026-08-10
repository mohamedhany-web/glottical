@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_messages'))

@section('content')
@php
    $locale = app()->getLocale();
    $notifications = $notifications ?? collect();
    $stats = $stats ?? ['total' => 0, 'unread' => 0, 'today' => 0, 'urgent' => 0];
    $notificationTypes = $notificationTypes ?? [];
    $priorities = $priorities ?? [];
    $tones = ['blue', 'pink', 'orange', 'purple'];
    $status = request('status', '');
    $type = request('type', '');
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_messages'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_messages'), 'url' => null],
    ],
    'toolbarView' => 'student.notifications._toolbar',
    'toolbarData' => [
        'stats' => $stats,
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

@if(($stats['unread'] ?? 0) > 0)
    <section class="st-join-hero" aria-label="{{ __('student_timeline.notif_unread') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.notif_inbox') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.notif_unread_title', ['count' => $stats['unread']]) }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.notif_unread_hint') }}</p>
        </div>
        <div class="st-join-hero__actions">
            <button type="button" class="st-pill st-pill--solid st-pill--lg" id="stMarkAllReadHero">
                {{ __('student_timeline.mark_all_read') }}
            </button>
            <a href="{{ route('notifications', ['status' => 'unread']) }}" class="st-pill st-pill--outline">
                {{ __('student_timeline.view_unread') }}
            </a>
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.nav_messages') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.filter_all') }}</p>
        <p class="st-stat-card__value">{{ $stats['total'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.notif_unread') }}</p>
        <p class="st-stat-card__value">{{ $stats['unread'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.notif_today') }}</p>
        <p class="st-stat-card__value">{{ $stats['today'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.notif_urgent') }}</p>
        <p class="st-stat-card__value">{{ $stats['urgent'] }}</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.notif_title') }}</h2>
        <p>{{ __('student_timeline.notif_hint') }}</p>
    </div>
    <button type="button" class="st-pill st-pill--outline" id="stCleanupNotif">{{ __('student_timeline.cleanup_notifications') }}</button>
</section>

@if(! empty($notificationTypes))
    <div class="st-top__chips st-notif-types" aria-label="{{ __('student_timeline.notif_types') }}">
        <a href="{{ route('notifications', array_filter(['status' => $status ?: null, 'q' => request('q') ?: null])) }}" class="st-chip {{ $type === '' ? 'is-active' : '' }}">{{ __('student_timeline.filter_all') }}</a>
        @foreach($notificationTypes as $key => $label)
            <a href="{{ route('notifications', array_filter(['type' => $key, 'status' => $status ?: null, 'q' => request('q') ?: null])) }}" class="st-chip {{ $type === $key ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
@endif

<section class="st-notif-list" aria-label="{{ __('student_timeline.notif_title') }}">
    @forelse($notifications as $i => $notification)
        @php
            $tone = $tones[$i % count($tones)];
            $priorityLabel = $priorities[$notification->priority] ?? $notification->priority;
        @endphp
        <article class="st-notif-card st-notif-card--{{ $tone }} {{ $notification->is_read ? '' : 'is-unread' }}" data-notif-id="{{ $notification->id }}">
            <div class="st-notif-card__icon" aria-hidden="true">
                <i class="{{ $notification->type_icon }}"></i>
            </div>
            <div class="st-notif-card__body">
                <div class="st-notif-card__badges">
                    @if(! $notification->is_read)
                        <span class="st-notif-card__badge is-new">{{ __('student_timeline.notif_new') }}</span>
                    @endif
                    @if($notification->priority !== 'normal')
                        <span class="st-notif-card__badge is-priority">{{ $priorityLabel }}</span>
                    @endif
                    <span class="st-notif-card__when">{{ $notification->created_at?->diffForHumans() }}</span>
                </div>
                <h3>{{ $notification->title }}</h3>
                <p class="st-notif-card__msg">{{ \Illuminate\Support\Str::limit((string) $notification->message, 160) }}</p>
                <p class="st-notif-card__meta">
                    {{ __('student_timeline.notif_from', ['name' => $notification->sender->name ?? __('student_timeline.system')]) }}
                </p>
            </div>
            <div class="st-notif-card__foot">
                @if($notification->action_url && $notification->action_text)
                    <a href="{{ route('notifications.go', $notification) }}" class="st-pill st-pill--solid">
                        {{ $notification->action_text }}
                    </a>
                @endif
                <a href="{{ route('notifications.show', $notification) }}" class="st-pill st-pill--outline">
                    {{ __('student_timeline.open_notification') }}
                </a>
                @if(! $notification->is_read)
                    <button type="button" class="st-pill st-pill--outline st-notif-mark" data-id="{{ $notification->id }}">
                        {{ __('student_timeline.mark_read') }}
                    </button>
                @endif
                <button type="button" class="st-notif-del" data-id="{{ $notification->id }}" title="{{ __('student_timeline.delete_notification') }}" aria-label="{{ __('student_timeline.delete_notification') }}">
                    <i class="fas fa-trash" aria-hidden="true"></i>
                </button>
            </div>
        </article>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.no_notifications') }}</h3>
            <p>{{ __('student_timeline.no_notifications_hint') }}</p>
            <div class="st-biz-banner__actions">
                <a href="{{ route('dashboard') }}" class="st-pill st-pill--solid">{{ __('student_timeline.school_gate') }}</a>
            </div>
        </div>
    @endforelse
</section>

@if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
    <div class="st-pager">
        {{ $notifications->links() }}
    </div>
@endif
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ route('dashboard') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.school_gate') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.back_to_timeline') }}</p>
</a>

@if(Route::has('student.classes.index'))
    <a href="{{ route('student.classes.index') }}" class="st-event-card st-event-card--pink">
        <h3>{{ __('student_timeline.my_classes') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.classes_hint') }}</p>
    </a>
@endif

@if(Route::has('orders.index'))
    <a href="{{ route('orders.index') }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.nav_orders') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.orders_hint') }}</p>
    </a>
@endif

<div class="st-events__see">
    <a href="{{ route('notifications', ['status' => 'unread']) }}">{{ __('student_timeline.view_unread') }}</a>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var csrf = @json(csrf_token());
    var confirmMarkAll = @json(__('student_timeline.confirm_mark_all'));
    var confirmDelete = @json(__('student_timeline.confirm_delete_notif'));
    var confirmCleanup = @json(__('student_timeline.confirm_cleanup'));

    function postJson(url, method) {
        return fetch(url, {
            method: method || 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            }
        }).then(function (r) { return r.json(); });
    }

    function markAll() {
        if (!confirm(confirmMarkAll)) return;
        postJson(@json(route('notifications.mark-all-read'))).then(function (data) {
            if (data && data.success) location.reload();
        }).catch(function () {});
    }

    var markAllBtn = document.getElementById('stMarkAllRead');
    var markAllHero = document.getElementById('stMarkAllReadHero');
    if (markAllBtn) markAllBtn.addEventListener('click', markAll);
    if (markAllHero) markAllHero.addEventListener('click', markAll);

    document.querySelectorAll('.st-notif-mark').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            postJson('/notifications/' + id + '/mark-read').then(function (data) {
                if (data && data.success) location.reload();
            }).catch(function () {});
        });
    });

    document.querySelectorAll('.st-notif-del').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm(confirmDelete)) return;
            var id = btn.getAttribute('data-id');
            postJson('/notifications/' + id, 'DELETE').then(function (data) {
                if (data && data.success) location.reload();
            }).catch(function () {});
        });
    });

    var cleanupBtn = document.getElementById('stCleanupNotif');
    if (cleanupBtn) {
        cleanupBtn.addEventListener('click', function () {
            if (!confirm(confirmCleanup)) return;
            postJson(@json(route('notifications.cleanup'))).then(function (data) {
                if (data && data.success) {
                    if (data.message) alert(data.message);
                    location.reload();
                }
            }).catch(function () {});
        });
    }
})();
</script>
@endpush
