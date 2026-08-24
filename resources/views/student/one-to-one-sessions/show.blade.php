@extends('layouts.student-timeline')

@section('title', $session->course->title ?? __('student_timeline.private_lesson'))

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $viewerTz = auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
    $status = $session->status;
    $isScheduled = $status === \App\Models\OneToOneSession::STATUS_SCHEDULED;
    $isPending = $status === \App\Models\OneToOneSession::STATUS_PENDING;
    $isCompleted = $status === \App\Models\OneToOneSession::STATUS_COMPLETED;
    $awaiting = method_exists($session, 'isAwaitingTeacherStart') && $session->isAwaitingTeacherStart();
    $joinHref = ($isScheduled && $session->classroomMeeting)
        ? route('classroom.secure-enter', $session->classroomMeeting)
        : null;
    $recordingHref = ($session->classroomMeeting
        && $session->classroomMeeting->hasBrowserRecording()
        && (
            $isCompleted
            || $session->classroomMeeting->ended_at
        )
        && Route::has('student.classroom.recording'))
        ? route('student.classroom.recording', $session->classroomMeeting)
        : null;
    $recordingStatusUrl = ($session->classroomMeeting
        && (
            $isCompleted
            || $session->classroomMeeting->ended_at
        )
        && ! $recordingHref
        && Route::has('student.classroom.recording.status'))
        ? route('student.classroom.recording.status', $session->classroomMeeting)
        : null;
    $duration = (int) ($session->duration_minutes ?: 50);
    $instructor = $session->instructor;
    $title = $session->course->title ?? __('student_timeline.private_lesson');
    $lessonsUrl = Route::has('student.private-lectures.index')
        ? route('student.private-lectures.index')
        : route('student.one-to-one-sessions.index');
    $badgeTone = match ($status) {
        \App\Models\OneToOneSession::STATUS_SCHEDULED => 'live',
        \App\Models\OneToOneSession::STATUS_COMPLETED => 'done',
        \App\Models\OneToOneSession::STATUS_PENDING => 'soon',
        default => 'off',
    };
    $groupedSlots = collect();
    if ($isPending && $availableSlots->isNotEmpty()) {
        $groupedSlots = $availableSlots->groupBy(fn ($s) => $s['starts_at']->copy()->timezone($viewerTz)->format('Y-m-d'));
    }
    $avatar = $instructor?->avatarDisplayUrl() ?? asset('img/student-timeline/avatar.png');
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_lessons'), 'url' => $lessonsUrl],
        ['label' => __('student.one_to_one_session_number', ['n' => $session->session_number]), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

@if($isScheduled)
    <section class="st-join-hero" aria-label="{{ __('student.one_to_one_join_session') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">
                {{ $awaiting ? __('student_timeline.teacher_starting') : __('student_timeline.next_private_lesson') }}
            </p>
            <h2 class="st-join-hero__title">{{ $title }}</h2>
            <p class="st-join-hero__meta">
                @if($instructor)
                    {{ $instructor->name }} ·
                @endif
                @if($session->scheduled_at)
                    <x-app-datetime :at="$session->scheduled_at" :timezone="$viewerTz" :pattern="$isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'" />
                @endif
                · {{ $duration }} {{ __('student_timeline.minutes') }}
            </p>
        </div>
        <div class="st-join-hero__actions">
            @if($joinHref)
                <a href="{{ $joinHref }}" class="st-pill st-pill--solid st-pill--lg">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    {{ __('student_timeline.join_now') }}
                </a>
            @elseif($awaiting)
                <span class="st-pill st-pill--outline">{{ __('student_timeline.teacher_starting') }}</span>
            @endif
            <a href="{{ $lessonsUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_lessons') }}</a>
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.oto_session_details') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.oto_status') }}</p>
        <p class="st-stat-card__value" style="font-size:1.05rem">{{ $session->statusLabel() }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.oto_duration') }}</p>
        <p class="st-stat-card__value">{{ $duration }}</p>
        <p class="st-stat-card__hint">{{ __('student_timeline.minutes') }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.oto_your_clock') }}</p>
        <p class="st-stat-card__value" style="font-size:.95rem;line-height:1.35">{{ \App\Support\AppTimezone::label($viewerTz) }}</p>
    </article>
</section>

<div class="st-class-detail">
    <div class="st-class-detail__main">
        <section class="st-panel">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student_timeline.oto_session_details') }}</h2>
                    <p>{{ __('student.one_to_one_session_number', ['n' => $session->session_number]) }}</p>
                </div>
            </div>
            <div class="st-lesson-card__main" style="margin-bottom:14px">
                <img class="st-lesson-card__avatar" src="{{ $avatar }}" alt="" width="48" height="48">
                <div class="st-lesson-card__copy">
                    <div class="st-lesson-card__badges">
                        <span class="st-lesson-card__badge">{{ __('student_timeline.private_lesson') }}</span>
                        <span class="st-session-badge st-session-badge--{{ $badgeTone }}">{{ $session->statusLabel() }}</span>
                    </div>
                    <h3 style="margin:0;font-size:1.05rem;font-weight:900">{{ $title }}</h3>
                    <p class="st-lesson-card__meta">{{ $instructor->name ?? '—' }}</p>
                </div>
            </div>
            <dl class="st-oto-facts">
                <div>
                    <dt>{{ __('student.one_to_one_appointment') }}</dt>
                    <dd>
                        @if($session->scheduled_at)
                            <x-app-datetime :at="$session->scheduled_at" :timezone="$viewerTz" :pattern="$isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'" />
                        @else
                            {{ __('student_timeline.awaiting_schedule') }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>{{ __('landing.nav.instructors') }}</dt>
                    <dd>{{ $instructor->name ?? '—' }}</dd>
                </div>
            </dl>
        </section>

        @if($isPending)
            <section class="st-panel">
                <div class="st-section-head">
                    <div>
                        <h2>{{ __('student.one_to_one_pick_slot') }}</h2>
                        <p>{{ __('student_timeline.oto_pick_available') }}</p>
                    </div>
                </div>
                @if($groupedSlots->isEmpty())
                    <div class="st-empty-panel">
                        <h3>{{ __('student_timeline.awaiting_schedule') }}</h3>
                        <p>{{ __('student_timeline.oto_waiting_placement') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('student.one-to-one-sessions.book', $session) }}" class="st-oto-form">
                        @csrf
                        @foreach($groupedSlots as $date => $daySlots)
                            <div class="st-slot-day">
                                <p class="st-slot-day__label">{{ \Carbon\Carbon::parse($date, $viewerTz)->locale($locale)->isoFormat('dddd D MMMM') }}</p>
                                <div class="st-slot-chips">
                                    @foreach($daySlots as $slot)
                                        <label class="st-slot-chip">
                                            <input type="radio" name="scheduled_at" value="{{ $slot['starts_at']->copy()->utc()->toIso8601String() }}" required>
                                            <span>{{ $slot['starts_at']->copy()->timezone($viewerTz)->format('g:i A') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <button type="submit" class="st-pill st-pill--solid">
                            <i class="fas fa-calendar-check" aria-hidden="true"></i>
                            {{ __('student_timeline.oto_confirm') }}
                        </button>
                    </form>
                @endif
            </section>
        @endif

        @if($isCompleted || $recordingHref || $recordingStatusUrl)
            <div class="st-empty-panel" id="mx-oto-recording-panel">
                <h3>{{ $session->statusLabel() }}</h3>
                <p>
                    @if($session->scheduled_at)
                        <x-app-datetime :at="$session->scheduled_at" :timezone="$viewerTz" :pattern="$isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'" />
                    @endif
                </p>
                @if($recordingHref)
                    <div class="st-biz-banner__actions" style="margin-top:14px" id="mx-oto-recording-actions">
                        <a href="{{ $recordingHref }}" class="st-pill st-pill--solid" target="_blank" rel="noopener">
                            <i class="fas fa-play-circle" aria-hidden="true"></i>
                            {{ __('student_timeline.watch_recording') }}
                        </a>
                    </div>
                @elseif($recordingStatusUrl)
                    <div class="st-biz-banner__actions" style="margin-top:14px" id="mx-oto-recording-actions">
                        <span class="st-pill st-pill--outline" id="mx-oto-recording-wait">
                            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                            {{ $isRtl ? 'جاري تجهيز التسجيل… سيظهر هنا تلقائياً' : 'Preparing recording… it will appear here shortly' }}
                        </span>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <aside class="st-class-detail__side">
        <section class="st-panel st-panel--side">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student_timeline.nav_lessons') }}</h2>
                    <p>{{ __('student_timeline.private_lessons_hint') }}</p>
                </div>
            </div>
            <div class="st-event-card__actions" style="margin-top:0;flex-direction:column" id="mx-oto-recording-side">
                @if($recordingHref)
                    <a href="{{ $recordingHref }}" class="st-pill st-pill--solid st-pill--block" target="_blank" rel="noopener">
                        <i class="fas fa-play-circle" aria-hidden="true"></i>
                        {{ __('student_timeline.watch_recording') }}
                    </a>
                @elseif($recordingStatusUrl)
                    <span class="st-pill st-pill--outline st-pill--block" id="mx-oto-recording-side-wait">
                        <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                        {{ $isRtl ? 'جاري تجهيز التسجيل…' : 'Preparing recording…' }}
                    </span>
                @endif
                <a href="{{ $lessonsUrl }}" class="st-pill st-pill--{{ ($recordingHref || $recordingStatusUrl) ? 'outline' : 'solid' }} st-pill--block">{{ __('student_timeline.nav_lessons') }}</a>
                @if(Route::has('calendar'))
                    <a href="{{ route('calendar') }}" class="st-pill st-pill--outline st-pill--block">{{ __('student.calendar_title') }}</a>
                @endif
                @if($instructor && Route::has('student.private-messages.with'))
                    <a href="{{ route('student.private-messages.with', $instructor) }}" class="st-pill st-pill--outline st-pill--block">{{ __('student_timeline.open_chats') }}</a>
                @elseif(Route::has('student.private-messages.index'))
                    <a href="{{ route('student.private-messages.index') }}" class="st-pill st-pill--outline st-pill--block">{{ __('student_timeline.nav_feed') }}</a>
                @endif
            </div>
        </section>
    </aside>
</div>
@if($recordingStatusUrl)
<script>
(function () {
    var statusUrl = @json($recordingStatusUrl);
    var label = @json(__('student_timeline.watch_recording'));
    var tries = 0;
    var maxTries = 60;
    function paint(url) {
        if (!url) return;
        var main = document.getElementById('mx-oto-recording-actions');
        if (main) {
            main.innerHTML = '<a href="' + url + '" class="st-pill st-pill--solid" target="_blank" rel="noopener"><i class="fas fa-play-circle" aria-hidden="true"></i> ' + label + '</a>';
        }
        var sideWait = document.getElementById('mx-oto-recording-side-wait');
        if (sideWait && sideWait.parentNode) {
            var a = document.createElement('a');
            a.href = url;
            a.className = 'st-pill st-pill--solid st-pill--block';
            a.target = '_blank';
            a.rel = 'noopener';
            a.innerHTML = '<i class="fas fa-play-circle" aria-hidden="true"></i> ' + label;
            sideWait.parentNode.replaceChild(a, sideWait);
        }
    }
    function tick() {
        if (tries++ >= maxTries) return;
        fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (data && data.ready && data.watch_url) {
                    paint(data.watch_url);
                    return;
                }
                setTimeout(tick, 3000);
            })
            .catch(function () { setTimeout(tick, 4000); });
    }
    setTimeout(tick, 1500);
})();
</script>
@endif
@endsection
