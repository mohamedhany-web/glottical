@extends('layouts.student-timeline')

@section('title', $notification->title)

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $senderName = $notification->sender->name ?? __('student_timeline.system');
    $typeLabel = \App\Models\Notification::getTypes()[$notification->type] ?? $notification->type;
    $priorityLabel = \App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority;
    $when = $notification->created_at
        ? $notification->created_at->timezone(config('app.timezone'))->translatedFormat($isRtl ? 'd M Y · g:i A' : 'M j, Y · g:i A')
        : null;
    $readWhen = $notification->read_at
        ? $notification->read_at->timezone(config('app.timezone'))->translatedFormat($isRtl ? 'd M Y · g:i A' : 'M j, Y · g:i A')
        : null;
    $expiresWhen = $notification->expires_at
        ? $notification->expires_at->timezone(config('app.timezone'))->translatedFormat($isRtl ? 'd M Y · g:i A' : 'M j, Y · g:i A')
        : null;
    $hasAction = $notification->action_url && $notification->action_text;
    $otherNotifications = $otherNotifications ?? collect();
@endphp

<div class="st-ndet-page">
@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.notif_detail_kicker'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_messages'), 'url' => route('notifications')],
        ['label' => __('student_timeline.notif_detail_kicker'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

<section class="st-ndet-hero" aria-label="{{ __('student_timeline.notif_detail_kicker') }}">
    <div class="st-ndet-hero__glow" aria-hidden="true"></div>
    <div class="st-ndet-hero__row">
        <div class="st-ndet-hero__copy">
            <h1>{{ $notification->title }}</h1>
            <p class="st-ndet-hero__meta">
                {{ __('student_timeline.notif_from', ['name' => $senderName]) }}
                @if($when)
                    <span aria-hidden="true">·</span> {{ $when }}
                @endif
            </p>
            <div class="st-ndet-hero__chips">
                <span class="st-ndet-chip">{{ $typeLabel }}</span>
                @if($notification->priority !== 'normal')
                    <span class="st-ndet-chip st-ndet-chip--gold">{{ $priorityLabel }}</span>
                @endif
                @if($notification->is_read)
                    <span class="st-ndet-chip st-ndet-chip--soft">{{ __('student_timeline.notif_read') }}</span>
                @else
                    <span class="st-ndet-chip st-ndet-chip--gold">{{ __('student_timeline.notif_new') }}</span>
                @endif
            </div>
        </div>
        <div class="st-ndet-hero__actions">
            @if($hasAction)
                <a href="{{ route('notifications.go', $notification) }}" class="st-pill st-pill--solid st-ndet-cta">
                    {{ $notification->action_text }}
                    <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                </a>
            @endif
            <a href="{{ route('notifications') }}" class="st-pill st-pill--outline st-ndet-back">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('student_timeline.notif_back') }}
            </a>
        </div>
    </div>
</section>

<section class="st-ndet-layout">
    <div class="st-ndet-main">
        <article class="st-panel st-ndet-panel">
            <h2 class="st-ndet-label">{{ __('student_timeline.notif_message') }}</h2>
            <div class="st-ndet-prose st-text-auto">{{ $notification->message }}</div>

            @if($hasAction)
                <a href="{{ route('notifications.go', $notification) }}" class="st-ndet-action-link">
                    <span>
                        <strong>{{ __('student_timeline.notif_action_needed') }}</strong>
                        <em>{{ $notification->action_text }}</em>
                    </span>
                    <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                </a>
            @endif

            @if(is_array($notification->data) && count($notification->data) > 0)
                <div class="st-ndet-data">
                    <p class="st-ndet-data__label">{{ __('student_timeline.notif_extra') }}</p>
                    <dl class="st-ndet-data__list">
                        @foreach($notification->data as $key => $value)
                            <div class="st-ndet-data__row">
                                <dt>{{ ucfirst((string) $key) }}</dt>
                                <dd class="st-text-auto">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </article>
    </div>

    <aside class="st-ndet-side">
        <article class="st-panel st-ndet-panel">
            <h2 class="st-ndet-label">{{ __('student_timeline.notif_info') }}</h2>
            <ul class="st-ndet-meta">
                <li>
                    <span>{{ __('student_timeline.notif_sender') }}</span>
                    <strong>{{ $senderName }}</strong>
                </li>
                <li>
                    <span>{{ __('student_timeline.notif_sent_at') }}</span>
                    <strong>{{ $when ?? '—' }}</strong>
                </li>
                <li>
                    <span>{{ __('student_timeline.notif_read_at') }}</span>
                    <strong>{{ $readWhen ?? __('student_timeline.notif_not_read_yet') }}</strong>
                </li>
                @if($expiresWhen)
                    <li>
                        <span>{{ __('student_timeline.notif_expires_at') }}</span>
                        <strong class="{{ $notification->isExpired() ? 'is-warn' : '' }}">{{ $expiresWhen }}</strong>
                    </li>
                @endif
                <li>
                    <span>{{ __('student_timeline.notif_status') }}</span>
                    <strong>{{ $notification->is_read ? __('student_timeline.notif_read') : __('student_timeline.notif_new') }}</strong>
                </li>
            </ul>

            <div class="st-ndet-actions">
                @if(! $notification->is_read)
                    <button type="button" class="st-pill st-pill--solid" id="stNdetMarkRead">
                        <i class="fas fa-check" aria-hidden="true"></i>
                        {{ __('student_timeline.mark_read') }}
                    </button>
                @endif
                <button type="button" class="st-pill st-pill--outline st-ndet-del" id="stNdetDelete">
                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                    {{ __('student_timeline.delete_notification') }}
                </button>
                <a href="{{ route('notifications') }}" class="st-pill st-pill--light">
                    <i class="fas fa-list" aria-hidden="true"></i>
                    {{ __('student_timeline.notif_all') }}
                </a>
            </div>
        </article>

        @if($otherNotifications->count() > 0)
            <article class="st-panel st-ndet-panel">
                <h2 class="st-ndet-label">{{ __('student_timeline.notif_others') }}</h2>
                <div class="st-ndet-others">
                    @foreach($otherNotifications as $other)
                        <a href="{{ route('notifications.show', $other) }}" class="st-ndet-other {{ $other->is_read ? '' : 'is-unread' }}">
                            <span class="st-ndet-other__icon" aria-hidden="true"><i class="{{ $other->type_icon }}"></i></span>
                            <span class="st-ndet-other__body">
                                <strong>{{ \Illuminate\Support\Str::limit($other->title, 42) }}</strong>
                                <em>{{ $other->created_at?->diffForHumans() }}</em>
                            </span>
                            @if(! $other->is_read)
                                <span class="st-ndet-other__dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </article>
        @endif
    </aside>
</section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var markBtn = document.getElementById('stNdetMarkRead');
    var delBtn = document.getElementById('stNdetDelete');
    var csrf = @json(csrf_token());
    var markUrl = @json(route('notifications.mark-read', $notification));
    var delUrl = @json(route('notifications.destroy', $notification));
    var listUrl = @json(route('notifications'));
    var confirmDel = @json(__('student_timeline.confirm_delete_notif'));

    if (markBtn) {
        markBtn.addEventListener('click', function () {
            fetch(markUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data.success) location.reload();
            }).catch(function () {});
        });
    }

    if (delBtn) {
        delBtn.addEventListener('click', function () {
            if (! window.confirm(confirmDel)) return;
            fetch(delUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data.success) window.location.href = listUrl;
            }).catch(function () {});
        });
    }
})();
</script>
@endpush
