@extends('layouts.student-timeline')

@section('title', $cohort->title)

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $feedCount = (int) ($feedCount ?? 0);
    $leaderboard = $leaderboard ?? collect();
    $game = $game ?? ['xp' => 0, 'level' => 1, 'streak' => ['current' => 0]];
    $sessions = ($cohort->classSessions ?? collect())->values();
    $tz = auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
    $nextJoinable = $sessions->first(fn ($s) => method_exists($s, 'isJoinable') && $s->isJoinable());
    $upcomingSessions = $sessions
        ->filter(fn ($s) => ! in_array($s->status, ['cancelled', 'completed'], true)
            && $s->starts_at
            && $s->starts_at->gte(now()->subHour()))
        ->values();
    $completedCount = $sessions->where('status', 'completed')->count();
    $totalSessions = $sessions->count();
    $progressPct = $totalSessions > 0 ? (int) round(($completedCount / $totalSessions) * 100) : 0;
    $instructorName = $cohort->tutoringGroup?->instructor?->name;
    $groupTitle = $cohort->tutoringGroup?->title;
    $subjectName = $cohort->tutoringGroup?->academicSubject?->name
        ?? $cohort->tutoringGroup?->schoolSubject?->name
        ?? null;
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $schedulePreview = $upcomingSessions->take(5);
    $pastPreview = $sessions->where('status', 'completed')->sortByDesc('starts_at')->take(3)->values();
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $cohort->title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.my_classes'), 'url' => route('student.classes.index')],
        ['label' => \Illuminate\Support\Str::limit($cohort->title, 28), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

{{-- One composition: class identity + single primary action --}}
<section class="st-event-card st-event-card--blue st-biz-banner st-class-hero">
    <img class="st-event-card__mask" src="{{ $eventMasks[1] }}" alt="" width="160" height="160">
    <div class="st-biz-banner__row">
        <div>
            <p class="st-event-card__kicker">{{ $subjectName ?: ($groupTitle ?: __('student_timeline.my_classes')) }}</p>
            <h3>{{ $cohort->title }}</h3>
            <p class="st-event-card__sub">
                {{ $cohort->scheduleSummary() }}
                @if($instructorName)
                    · {{ $instructorName }}
                @endif
            </p>
        </div>
        <div class="st-biz-banner__actions">
            @if($nextJoinable)
                <form method="POST" action="{{ route('student.classes.sessions.join', $nextJoinable) }}">
                    @csrf
                    <button type="submit" class="st-pill st-pill--light">
                        <i class="fas fa-video" aria-hidden="true"></i>
                        {{ __('student_timeline.join_class_now') }}
                    </button>
                </form>
            @elseif($upcomingSessions->first())
                <a href="#st-class-schedule" class="st-pill st-pill--light">{{ __('student_timeline.class_schedule') }}</a>
            @endif
            @if($cohort->whatsapp_group_url)
                <a href="{{ $cohort->whatsapp_group_url }}" target="_blank" rel="noopener" class="st-pill st-pill--ghost">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    WhatsApp
                </a>
            @endif
        </div>
    </div>
</section>

<section class="st-stats" aria-label="{{ __('student_timeline.class_room') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('student_timeline.path_progress') }}</p>
        <p class="st-stat-card__value">{{ $progressPct }}%</p>
        <div class="st-stat-card__bar"><span style="width: {{ max(4, $progressPct) }}%"></span></div>
        <p class="st-stat-card__hint">{{ $completedCount }}/{{ $totalSessions }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('student_timeline.upcoming_short') }}</p>
        <p class="st-stat-card__value">{{ $upcomingSessions->count() }}</p>
        <p class="st-stat-card__hint">
            @if($nextJoinable?->starts_at)
                <x-app-datetime :at="$nextJoinable->starts_at" pattern="D g:i A" />
            @else
                —
            @endif
        </p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('student_timeline.class_seats') }}</p>
        <p class="st-stat-card__value">{{ $cohort->activeEnrollments->count() }}</p>
        <p class="st-stat-card__hint">/ {{ $cohort->capacity }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">XP</p>
        <p class="st-stat-card__value">{{ number_format($game['xp'] ?? 0) }}</p>
        <p class="st-stat-card__hint">Lv {{ $game['level'] ?? 1 }} · 🔥 {{ $game['streak']['current'] ?? 0 }}</p>
    </article>
</section>

<div class="st-class-detail">
    <div class="st-class-detail__main">
        <section class="st-panel" id="st-class-schedule">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student_timeline.class_schedule') }}</h2>
                    <p>{{ __('student_timeline.class_schedule_hint') }}</p>
                </div>
            </div>

            @if($schedulePreview->isNotEmpty())
                <ul class="st-session-list">
                    @foreach($schedulePreview as $session)
                        @php
                            $joinable = $session->status !== 'cancelled' && $session->isJoinable();
                            $statusTone = match ($session->status) {
                                'live' => 'live',
                                'completed' => 'done',
                                'cancelled' => 'off',
                                default => 'soon',
                            };
                            $isPrimary = $nextJoinable && (int) $nextJoinable->id === (int) $session->id;
                        @endphp
                        <li class="st-session-row st-session-row--{{ $statusTone }}{{ $isPrimary ? ' is-primary' : '' }}">
                            <div class="st-session-row__body">
                                <h3>{{ $session->displayTitle() }}</h3>
                                <p>
                                    @if($session->starts_at)
                                        <x-app-datetime :at="$session->starts_at" :pattern="$isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'" />
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            <div class="st-session-row__actions">
                                <span class="st-session-badge st-session-badge--{{ $statusTone }}">{{ $session->statusLabel() }}</span>
                                @if($joinable && $isPrimary)
                                    <form method="POST" action="{{ route('student.classes.sessions.join', $session) }}">
                                        @csrf
                                        <button type="submit" class="st-pill st-pill--solid">
                                            <i class="fas fa-video" aria-hidden="true"></i>
                                            {{ __('student_timeline.join_live') }}
                                        </button>
                                    </form>
                                @elseif(! $joinable && $session->status !== 'cancelled')
                                    <span class="st-session-row__muted">{{ __('student_timeline.session_soon') }}</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if($upcomingSessions->count() > $schedulePreview->count())
                    <p class="st-class-more">+{{ $upcomingSessions->count() - $schedulePreview->count() }} {{ __('student_timeline.upcoming_short') }}</p>
                @endif
            @elseif($pastPreview->isNotEmpty())
                <ul class="st-session-list">
                    @foreach($pastPreview as $session)
                        <li class="st-session-row st-session-row--done">
                            <div class="st-session-row__body">
                                <h3>{{ $session->displayTitle() }}</h3>
                                <p><x-app-datetime :at="$session->starts_at" :pattern="$isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'" /></p>
                            </div>
                            <span class="st-session-badge st-session-badge--done">{{ $session->statusLabel() }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="st-empty-panel">
                    <h3>{{ __('student_timeline.no_schedule_yet') }}</h3>
                    <p>{{ __('student_timeline.no_schedule_hint') }}</p>
                </div>
            @endif
        </section>
    </div>

    <aside class="st-class-detail__side">
        <a href="{{ route('student.classes.community', $cohort) }}" class="st-community-tile" aria-label="{{ __('student_timeline.class_community') }}">
            <span class="st-community-tile__icon" aria-hidden="true">
                <i class="fas fa-comments"></i>
            </span>
            <span class="st-community-tile__text">
                <strong>{{ __('student_timeline.class_community') }}</strong>
                <small>{{ __('student_timeline.class_community_hint') }}</small>
            </span>
            @if($feedCount > 0)
                <span class="st-community-tile__badge">{{ $feedCount > 99 ? '99+' : $feedCount }}</span>
            @else
                <span class="st-community-tile__chevron" aria-hidden="true">
                    <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>
                </span>
            @endif
        </a>

        <section class="st-panel st-panel--side">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student_timeline.class_leaderboard') }}</h2>
                    <p>{{ __('student_timeline.class_leaderboard_hint') }}</p>
                </div>
            </div>
            <ol class="st-board">
                @forelse($leaderboard->take(5) as $row)
                    <li class="st-board__row{{ $row->rank <= 3 ? ' is-top' : '' }}">
                        <span class="st-board__rank">{{ $row->rank }}</span>
                        <span class="st-board__name">{{ $row->name }}</span>
                        <span class="st-board__xp">{{ number_format($row->xp) }}</span>
                    </li>
                @empty
                    <li class="st-board__empty">{{ __('student_timeline.class_leaderboard_empty') }}</li>
                @endforelse
            </ol>
        </section>

        @if($instructorName && Route::has('student.private-messages.index'))
            <a href="{{ route('student.private-messages.index') }}" class="st-event-card st-event-card--purple st-class-teacher-link">
                <img class="st-event-card__mask" src="{{ $eventMasks[0] }}" alt="" width="120" height="120">
                <p class="st-event-card__kicker">{{ __('student_timeline.teacher') }}</p>
                <h3>{{ $instructorName }}</h3>
                <p class="st-event-card__sub">{{ __('student_timeline.open_chats') }}</p>
            </a>
        @endif
    </aside>
</div>
@endsection

@section('events')
@php
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
    $eventTones = ['green', 'blue', 'orange', 'purple', 'pink'];
    $sideSessions = ($upcomingSessions ?? collect())->take(4);
@endphp

<div class="st-events__top">
    <h2>{{ __('student_timeline.upcoming_short') }}</h2>
</div>

<div data-tab-panel="activities">
    @forelse($sideSessions as $i => $session)
        @php
            $joinable = $session->isJoinable();
            $isPrimary = $nextJoinable && (int) $nextJoinable->id === (int) $session->id;
        @endphp
        <div class="st-event-card st-event-card--{{ $eventTones[$i % count($eventTones)] }}">
            <img class="st-event-card__mask" src="{{ $eventMasks[$i % 3] }}" alt="" width="160" height="160">
            <h3>{{ $session->displayTitle() }}</h3>
            <p class="st-event-card__sub">{{ $session->statusLabel() }}</p>
            <div class="st-event-card__meta">
                <img src="{{ asset('img/student-timeline/clock.png') }}" alt="" width="14" height="14">
                <span><x-app-datetime :at="$session->starts_at" pattern="D · g:i A" /></span>
            </div>
            <div class="st-event-card__actions">
                @if($joinable && $isPrimary)
                    <form method="POST" action="{{ route('student.classes.sessions.join', $session) }}">
                        @csrf
                        <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.join_now') }}</button>
                    </form>
                @else
                    <a href="#st-class-schedule" class="st-pill st-pill--outline">{{ __('student_timeline.class_schedule') }}</a>
                @endif
            </div>
        </div>
    @empty
        <p class="st-events__empty">{{ __('student_timeline.no_schedule_yet') }}</p>
    @endforelse
</div>

<div class="st-events__see">
    <a href="{{ route('student.classes.index') }}">{{ __('student_timeline.back_to_classes') }}</a>
</div>
@endsection
