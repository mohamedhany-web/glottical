@extends('layouts.student-timeline')

@section('title', $cohort->title)

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $feedPosts = $feedPosts ?? collect();
    $leaderboard = $leaderboard ?? collect();
    $canModerateFeed = $canModerateFeed ?? false;
    $game = $game ?? ['xp' => 0, 'level' => 1, 'streak' => ['current' => 0]];
    $sessions = $cohort->classSessions ?? collect();
    $nextJoinable = $sessions->first(fn ($s) => method_exists($s, 'isJoinable') && $s->isJoinable());
    $upcomingCount = $sessions->filter(fn ($s) => ! in_array($s->status, ['cancelled', 'completed'], true) && $s->starts_at && $s->starts_at->gte(now()->subHour()))->count();
    $completedCount = $sessions->where('status', 'completed')->count();
    $totalSessions = $sessions->count();
    $progressPct = $totalSessions > 0 ? (int) round(($completedCount / $totalSessions) * 100) : 0;
    $instructorName = $cohort->tutoringGroup?->instructor?->name;
    $groupTitle = $cohort->tutoringGroup?->title;
    $tz = config('app.timezone');
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $cohort->title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.my_classes'), 'url' => route('student.classes.index')],
        ['label' => $cohort->title, 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

<section class="st-join-hero" aria-label="{{ __('student_timeline.class_room') }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $groupTitle ?: __('student_timeline.my_classes') }}</p>
        <h2 class="st-join-hero__title">{{ $cohort->title }}</h2>
        <p class="st-join-hero__meta">
            {{ $cohort->scheduleSummary() }}
            @if($instructorName)
                · {{ __('student_timeline.teacher') }}: {{ $instructorName }}
            @endif
            · {{ $cohort->activeEnrollments->count() }}/{{ $cohort->capacity }}
            · {{ number_format($game['xp'] ?? 0) }} XP · Lv {{ $game['level'] ?? 1 }}
            · 🔥 {{ $game['streak']['current'] ?? 0 }}
        </p>
    </div>
    <div class="st-join-hero__actions">
        @if($nextJoinable)
            <form method="POST" action="{{ route('student.classes.sessions.join', $nextJoinable) }}">
                @csrf
                <button type="submit" class="st-pill st-pill--solid st-pill--lg">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    {{ __('student_timeline.join_class_now') }}
                </button>
            </form>
        @endif
        @if($cohort->whatsapp_group_url)
            <a href="{{ $cohort->whatsapp_group_url }}" target="_blank" rel="noopener" class="st-pill st-pill--outline">
                <i class="fab fa-whatsapp" aria-hidden="true"></i>
                {{ __('student_timeline.class_whatsapp') }}
            </a>
        @endif
        <a href="{{ route('student.classes.index') }}" class="st-pill st-pill--outline">{{ __('student_timeline.back_to_classes') }}</a>
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.class_room') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.path_progress') }}</p>
        <p class="st-stat-card__value">{{ $progressPct }}%</p>
        <p class="st-stat-card__hint">{{ $completedCount }}/{{ $totalSessions }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.upcoming_short') }}</p>
        <p class="st-stat-card__value">{{ $upcomingCount }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.class_seats') }}</p>
        <p class="st-stat-card__value">{{ $cohort->activeEnrollments->count() }}</p>
        <p class="st-stat-card__hint">/ {{ $cohort->capacity }}</p>
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

            @if($sessions->isNotEmpty())
                <ul class="st-session-list">
                    @foreach($sessions as $session)
                        @php
                            $joinable = $session->status !== 'cancelled' && $session->isJoinable();
                            $statusTone = match ($session->status) {
                                'live' => 'live',
                                'completed' => 'done',
                                'cancelled' => 'off',
                                default => 'soon',
                            };
                        @endphp
                        <li class="st-session-row st-session-row--{{ $statusTone }}">
                            <div class="st-session-row__body">
                                <h3>{{ $session->displayTitle() }}</h3>
                                <p>
                                    @if($session->starts_at)
                                        {{ $session->starts_at->timezone($tz)->translatedFormat($isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A') }}
                                        @if($session->ends_at)
                                            — {{ $session->ends_at->timezone($tz)->format('g:i A') }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            <div class="st-session-row__actions">
                                <span class="st-session-badge st-session-badge--{{ $statusTone }}">{{ $session->statusLabel() }}</span>
                                @if($session->status === 'cancelled')
                                    <span class="st-session-row__muted">—</span>
                                @elseif($joinable)
                                    <form method="POST" action="{{ route('student.classes.sessions.join', $session) }}">
                                        @csrf
                                        <button type="submit" class="st-pill st-pill--solid">
                                            <i class="fas fa-video" aria-hidden="true"></i>
                                            {{ __('student_timeline.join_live') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="st-session-row__muted">{{ __('student_timeline.session_soon') }}</span>
                                @endif
                            </div>
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

        <section class="st-panel" id="st-class-feed">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student_timeline.class_community') }}</h2>
                    <p>{{ __('student_timeline.class_community_hint') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('student.classes.feed.store', $cohort) }}" class="st-feed-compose">
                @csrf
                <textarea name="body" rows="3" maxlength="1000" required
                          placeholder="{{ __('student_timeline.class_post_placeholder') }}"></textarea>
                <div class="st-feed-compose__bar">
                    <select name="post_type">
                        <option value="question">{{ __('student_timeline.class_post_question') }}</option>
                        @if($canModerateFeed)
                            <option value="announcement">{{ __('student_timeline.class_post_announcement') }}</option>
                        @endif
                    </select>
                    <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.class_post_submit') }}</button>
                </div>
            </form>

            <ul class="st-feed-list">
                @forelse($feedPosts as $post)
                    <li class="st-feed-card{{ $post->is_hidden ? ' is-hidden' : '' }}{{ $post->is_pinned ? ' is-pinned' : '' }}">
                        <div class="st-feed-card__head">
                            <div>
                                <strong>{{ $post->author?->name }}</strong>
                                <span class="st-feed-chip">{{ $post->typeLabel() }}</span>
                                @if($post->is_pinned)
                                    <span class="st-feed-chip st-feed-chip--gold">{{ __('student_timeline.class_pinned') }}</span>
                                @endif
                                @if($post->is_hidden)
                                    <span class="st-feed-chip st-feed-chip--danger">{{ __('student_timeline.class_hidden') }}</span>
                                @endif
                            </div>
                            <time>{{ $post->created_at?->diffForHumans() }}</time>
                        </div>
                        <p class="st-feed-card__body">{{ $post->body }}</p>

                        @if($canModerateFeed)
                            <div class="st-feed-card__mods">
                                <form method="POST" action="{{ route('student.classes.feed.pin', $post) }}">@csrf
                                    <button type="submit">{{ $post->is_pinned ? __('student_timeline.class_unpin') : __('student_timeline.class_pin') }}</button>
                                </form>
                                @if($post->is_hidden)
                                    <form method="POST" action="{{ route('student.classes.feed.unhide', $post) }}">@csrf
                                        <button type="submit">{{ __('student_timeline.class_unhide') }}</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('student.classes.feed.hide', $post) }}">@csrf
                                        <button type="submit" class="is-danger">{{ __('student_timeline.class_hide') }}</button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        <ul class="st-feed-comments">
                            @foreach($post->visibleComments as $comment)
                                <li>
                                    <strong>{{ $comment->author?->name }}:</strong>
                                    <span>{{ $comment->body }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <form method="POST" action="{{ route('student.classes.feed.comment', $post) }}" class="st-feed-reply">
                            @csrf
                            <input type="text" name="body" maxlength="1000" required placeholder="{{ __('student_timeline.class_comment_placeholder') }}">
                            <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.class_reply') }}</button>
                        </form>
                    </li>
                @empty
                    <li class="st-empty-panel">
                        <h3>{{ __('student_timeline.class_feed_empty') }}</h3>
                        <p>{{ __('student_timeline.class_feed_empty_hint') }}</p>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>

    <aside class="st-class-detail__side">
        <section class="st-panel st-panel--side">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student_timeline.class_leaderboard') }}</h2>
                    <p>{{ __('student_timeline.class_leaderboard_hint') }}</p>
                </div>
            </div>
            <ol class="st-board">
                @forelse($leaderboard as $row)
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

        @if($instructorName)
            <section class="st-panel st-panel--side">
                <div class="st-section-head">
                    <div>
                        <h2>{{ __('student_timeline.teacher') }}</h2>
                        <p>{{ $instructorName }}</p>
                    </div>
                </div>
                @if(Route::has('student.private-messages.index'))
                    <a href="{{ route('student.private-messages.index') }}" class="st-pill st-pill--outline st-pill--block">
                        {{ __('student_timeline.open_chats') }}
                    </a>
                @endif
            </section>
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
    $eventTones = ['pink', 'blue', 'orange'];
    $sideSessions = ($cohort->classSessions ?? collect())
        ->filter(fn ($s) => ! in_array($s->status, ['cancelled', 'completed'], true))
        ->take(6);
@endphp

<div class="st-events__top">
    <h2>{{ __('student_timeline.class_schedule') }}</h2>
</div>

<div data-tab-panel="activities">
    @forelse($sideSessions as $i => $session)
        @php $joinable = $session->isJoinable(); @endphp
        <div class="st-event-card st-event-card--{{ $eventTones[$i % 3] }}">
            <img class="st-event-card__mask" src="{{ $eventMasks[$i % 3] }}" alt="" width="160" height="160">
            <h3>{{ $session->displayTitle() }}</h3>
            <p class="st-event-card__sub">{{ $session->statusLabel() }}</p>
            <div class="st-event-card__meta">
                <img src="{{ asset('img/student-timeline/clock.png') }}" alt="" width="14" height="14">
                <span>{{ $session->starts_at?->timezone(config('app.timezone'))->translatedFormat('D · g:i A') }}</span>
            </div>
            <div class="st-event-card__actions">
                @if($joinable)
                    <form method="POST" action="{{ route('student.classes.sessions.join', $session) }}">
                        @csrf
                        <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.join_now') }}</button>
                    </form>
                @else
                    <span class="st-pill st-pill--outline">{{ __('student_timeline.session_soon') }}</span>
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
