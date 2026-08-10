@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_learn'))

@section('content')
@php
    $locale = app()->getLocale();
    $tab = $tab ?? 'private';
    $packagesUrl = $packages_url ?? route('public.service-packages.index');
    $privateUnits = (int) ($private_units ?? 0);
    $collectiveUnits = (int) ($collective_units ?? 0);
    $individualUnits = (int) ($individual_units ?? 0);
    $globalUnits = (int) ($global_units ?? 0);
    $teachers = $teachers ?? collect();
    $groups = $groups ?? collect();
    $entitlements = $entitlements ?? collect();
    $upcomingPrivate = $upcoming_private ?? collect();
    $upcomingBookings = $upcoming_bookings ?? collect();
    $filters = $filters ?? ['q' => '', 'subject_id' => null, 'year_id' => null, 'type' => '', 'bookable' => false];
    $filterSubjects = $filter_subjects ?? collect();
    $filterYears = $filter_years ?? collect();
    $groupCredits = $collectiveUnits + $individualUnits + $globalUnits;
    $creditTotal = $privateUnits + $groupCredits;
    $needsPackage = ($tab === 'private' && $privateUnits < 1)
        || ($tab === 'groups' && $groupCredits < 1)
        || ($tab === 'mine' && $creditTotal < 1 && $entitlements->isEmpty());
    $hasActiveFilters = ($filters['q'] ?? '') !== ''
        || ! empty($filters['subject_id'])
        || ! empty($filters['year_id'])
        || ($filters['type'] ?? '') !== '';
    $tabUrl = function (string $nextTab) use ($filters) {
        $params = ['tab' => $nextTab];
        if (($filters['q'] ?? '') !== '') {
            $params['q'] = $filters['q'];
        }
        if (! empty($filters['subject_id'])) {
            $params['subject_id'] = $filters['subject_id'];
        }
        if (! empty($filters['year_id']) && $nextTab === 'groups') {
            $params['year_id'] = $filters['year_id'];
        }
        if (($filters['type'] ?? '') !== '' && $nextTab === 'groups') {
            $params['type'] = $filters['type'];
        }

        return route('student.learn.index', $params);
    };
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_learn'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_learn'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

{{-- Three clear learning paths --}}
<section class="st-learn-paths" aria-label="{{ __('student_timeline.learn_paths_title') }}">
    <a href="{{ route('dashboard') }}" class="st-learn-path">
        <span class="st-learn-path__icon" aria-hidden="true"><i class="fas fa-school"></i></span>
        <span class="st-learn-path__body">
            <strong>{{ __('student_timeline.learn_path_school') }}</strong>
            <small>{{ __('student_timeline.learn_path_school_hint') }}</small>
        </span>
    </a>
    <a href="{{ $tabUrl('private') }}" class="st-learn-path {{ $tab === 'private' ? 'is-on' : '' }}">
        <span class="st-learn-path__icon" aria-hidden="true"><i class="fas fa-user"></i></span>
        <span class="st-learn-path__body">
            <strong>{{ __('student_timeline.learn_path_private') }}</strong>
            <small>{{ __('student_timeline.learn_path_private_hint') }}</small>
        </span>
    </a>
    <a href="{{ $tabUrl('groups') }}" class="st-learn-path {{ $tab === 'groups' ? 'is-on' : '' }}">
        <span class="st-learn-path__icon" aria-hidden="true"><i class="fas fa-users"></i></span>
        <span class="st-learn-path__body">
            <strong>{{ __('student_timeline.learn_path_groups') }}</strong>
            <small>{{ __('student_timeline.learn_path_groups_hint') }}</small>
        </span>
    </a>
</section>

@if($needsPackage)
    <section class="st-join-hero" aria-label="{{ __('student_timeline.recharge_package') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.learn_kicker') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.learn_need_credit_title') }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.learn_need_credit_hint') }}</p>
        </div>
        <div class="st-join-hero__actions">
            <a href="{{ $packagesUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.recharge_package') }}</a>
        </div>
    </section>
@else
    <section class="st-join-hero" aria-label="{{ __('student_timeline.nav_learn') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.learn_kicker') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.learn_ready_title', ['count' => $creditTotal]) }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.learn_ready_hint') }}</p>
        </div>
        <div class="st-join-hero__actions">
            @if($tab === 'private' && method_exists($teachers, 'isNotEmpty') && $teachers->isNotEmpty())
                <a href="{{ $teachers->first()['url'] }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.learn_pick_teacher') }}</a>
            @elseif($tab !== 'private')
                <a href="{{ $tabUrl('private') }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.learn_tab_private') }}</a>
            @endif
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.learn_total_credits') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.credits_private') }}</p>
        <p class="st-stat-card__value">{{ $privateUnits }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.credits_collective') }}</p>
        <p class="st-stat-card__value">{{ $groupCredits }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.learn_total_credits') }}</p>
        <p class="st-stat-card__value">{{ $creditTotal }}</p>
    </article>
</section>

<nav class="st-learn-tabs" aria-label="{{ __('student_timeline.learn_tabs') }}">
    <a href="{{ $tabUrl('private') }}" class="st-learn-tab {{ $tab === 'private' ? 'is-on' : '' }}">{{ __('student_timeline.learn_tab_private') }}</a>
    <a href="{{ $tabUrl('groups') }}" class="st-learn-tab {{ $tab === 'groups' ? 'is-on' : '' }}">{{ __('student_timeline.learn_tab_groups') }}</a>
    <a href="{{ route('student.learn.index', ['tab' => 'mine']) }}" class="st-learn-tab {{ $tab === 'mine' ? 'is-on' : '' }}">{{ __('student_timeline.learn_tab_mine') }}</a>
</nav>

@if(in_array($tab, ['private', 'groups'], true))
    <form class="st-learn-toolbar" method="get" action="{{ route('student.learn.index') }}" role="search">
        <input type="hidden" name="tab" value="{{ $tab }}">
        @if(request('lang'))
            <input type="hidden" name="lang" value="{{ request('lang') }}">
        @endif

        <label class="st-learn-toolbar__search">
            <span class="visually-hidden">{{ __('student_timeline.learn_search') }}</span>
            <i class="fas fa-search" aria-hidden="true"></i>
            <input
                type="search"
                name="q"
                value="{{ $filters['q'] }}"
                placeholder="{{ $tab === 'private' ? __('student_timeline.learn_search_teachers') : __('student_timeline.learn_search_groups') }}"
                autocomplete="off"
            >
        </label>

        <label class="st-learn-toolbar__select">
            <span class="visually-hidden">{{ __('student_timeline.learn_filter_subject') }}</span>
            <select name="subject_id" aria-label="{{ __('student_timeline.learn_filter_subject') }}">
                <option value="">{{ __('student_timeline.learn_filter_subject_all') }}</option>
                @foreach($filterSubjects as $subject)
                    <option value="{{ $subject->id }}" @selected((int) ($filters['subject_id'] ?? 0) === (int) $subject->id)>{{ $subject->name }}</option>
                @endforeach
            </select>
        </label>

        @if($tab === 'groups')
            <label class="st-learn-toolbar__select">
                <span class="visually-hidden">{{ __('student_timeline.learn_filter_year') }}</span>
                <select name="year_id" aria-label="{{ __('student_timeline.learn_filter_year') }}">
                    <option value="">{{ __('student_timeline.learn_filter_year_all') }}</option>
                    @foreach($filterYears as $year)
                        <option value="{{ $year->id }}" @selected((int) ($filters['year_id'] ?? 0) === (int) $year->id)>{{ $year->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="st-learn-toolbar__select">
                <span class="visually-hidden">{{ __('student_timeline.learn_filter_type') }}</span>
                <select name="type" aria-label="{{ __('student_timeline.learn_filter_type') }}">
                    <option value="">{{ __('student_timeline.learn_filter_type_all') }}</option>
                    <option value="individual" @selected(($filters['type'] ?? '') === 'individual')>{{ __('student_timeline.learn_type_individual') }}</option>
                    <option value="collective" @selected(($filters['type'] ?? '') === 'collective')>{{ __('student_timeline.learn_type_collective') }}</option>
                </select>
            </label>
        @endif

        <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.learn_filter_apply') }}</button>

        @if($hasActiveFilters)
            <a href="{{ route('student.learn.index', ['tab' => $tab]) }}" class="st-pill st-pill--ghost">{{ __('student_timeline.learn_filter_clear') }}</a>
        @endif
    </form>
@endif

@if($tab === 'private')
    <section class="st-msg-intro">
        <div>
            <h2>{{ __('student_timeline.learn_teachers_title') }}</h2>
            <p>
                {{ __('student_timeline.learn_teachers_simple') }}
                @if(method_exists($teachers, 'total'))
                    · {{ __('student_timeline.learn_results_count', ['count' => $teachers->total()]) }}
                @endif
            </p>
        </div>
    </section>

    <section class="st-learn-list" aria-label="{{ __('student_timeline.learn_teachers_title') }}">
        @forelse($teachers as $teacher)
            <a href="{{ $teacher['url'] }}" class="st-learn-row-card">
                <img src="{{ $teacher['photo'] }}" alt="" width="56" height="56" loading="lazy">
                <div class="st-learn-row-card__copy">
                    <h3>{{ $teacher['name'] }}</h3>
                    <p>{{ $teacher['headline'] ? \Illuminate\Support\Str::limit($teacher['headline'], 70) : __('student_timeline.learn_teacher_fallback') }}</p>
                </div>
                <span class="st-learn-row-card__cta">
                    {{ $teacher['can_book'] ? __('student_timeline.learn_book_now') : __('student_timeline.learn_view_teacher') }}
                    <i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                </span>
            </a>
        @empty
            <div class="st-learn-empty">
                {{ $hasActiveFilters ? __('student_timeline.learn_no_filter_results') : __('student_timeline.learn_no_teachers') }}
            </div>
        @endforelse
    </section>

    @if(method_exists($teachers, 'hasPages') && $teachers->hasPages())
        <div class="st-pager">{{ $teachers->links() }}</div>
    @endif
@elseif($tab === 'groups')
    <section class="st-msg-intro">
        <div>
            <h2>{{ __('student_timeline.learn_groups_title') }}</h2>
            <p>
                {{ __('student_timeline.learn_groups_simple') }}
                @if(method_exists($groups, 'total'))
                    · {{ __('student_timeline.learn_results_count', ['count' => $groups->total()]) }}
                @endif
            </p>
        </div>
    </section>

    <section class="st-learn-list" aria-label="{{ __('student_timeline.learn_groups_title') }}">
        @forelse($groups as $group)
            <article class="st-learn-row-card st-learn-row-card--block">
                <img src="{{ $group['instructor_photo'] }}" alt="" width="56" height="56" loading="lazy">
                <div class="st-learn-row-card__copy">
                    <h3>{{ $group['title'] }}</h3>
                    <p>
                        {{ $group['instructor_name'] }}
                        · {{ $group['type_label'] }}
                        @if($group['subject']) · {{ $group['subject'] }}@endif
                        @if($group['year']) · {{ $group['year'] }}@endif
                    </p>
                </div>

                @if($group['can_book'] && $group['slots']->isNotEmpty())
                    <form method="POST" action="{{ route('student.tutoring-bookings.from-entitlement') }}" class="st-learn-row-card__book" onclick="event.stopPropagation()">
                        @csrf
                        <input type="hidden" name="tutoring_group_id" value="{{ $group['id'] }}">
                        <select name="starts_at" required aria-label="{{ __('student_timeline.learn_pick_slot') }}">
                            <option value="">{{ __('student_timeline.learn_choose_slot') }}</option>
                            @foreach($group['slots'] as $slot)
                                <option value="{{ $slot['starts_at'] }}">{{ $slot['label'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.learn_book_now') }}</button>
                    </form>
                @elseif($group['can_book'])
                    <span class="st-learn-row-card__muted">{{ __('student_timeline.learn_no_slots') }}</span>
                @elseif($group['teacher_url'])
                    <a href="{{ $group['teacher_url'] }}" class="st-learn-row-card__cta">
                        {{ __('student_timeline.learn_view_teacher') }}
                        <i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                    </a>
                @endif
            </article>
        @empty
            <div class="st-learn-empty">
                {{ $hasActiveFilters ? __('student_timeline.learn_no_filter_results') : __('student_timeline.learn_no_groups') }}
            </div>
        @endforelse
    </section>

    @if(method_exists($groups, 'hasPages') && $groups->hasPages())
        <div class="st-pager">{{ $groups->links() }}</div>
    @endif
@else
    <section class="st-msg-intro">
        <div>
            <h2>{{ __('student_timeline.learn_mine_title') }}</h2>
            <p>{{ __('student_timeline.learn_mine_hint') }}</p>
        </div>
    </section>

    <section class="st-learn-mine-grid">
        <div class="st-learn-panel">
            <h3>{{ __('student_timeline.learn_active_credits') }}</h3>
            @forelse($entitlements as $ent)
                <div class="st-learn-row">
                    <div>
                        <strong>{{ $ent->servicePackage?->name ?? ($ent->tutoringGroup?->title ?: __('student_timeline.nav_progress')) }}</strong>
                        <small>{{ max(0, (int) $ent->units_total - (int) $ent->units_used) }} {{ __('student_timeline.learn_units_left') }}</small>
                    </div>
                </div>
            @empty
                <p class="st-learn-note">{{ __('student_timeline.learn_no_credits') }}</p>
            @endforelse
        </div>

        <div class="st-learn-panel">
            <h3>{{ __('student_timeline.learn_upcoming_private') }}</h3>
            @forelse($upcomingPrivate as $session)
                <a href="{{ route('student.one-to-one-sessions.show', $session) }}" class="st-learn-row st-learn-row--link">
                    <div>
                        <strong>{{ $session->instructor?->name ?? '—' }}</strong>
                        <small>
                            @if($session->scheduled_at)
                                {{ $session->scheduled_at->locale($locale)->translatedFormat('D j M · H:i') }}
                            @else
                                {{ __('student_timeline.learn_pending_schedule') }}
                            @endif
                        </small>
                    </div>
                </a>
            @empty
                <p class="st-learn-note">{{ __('student_timeline.learn_no_private_upcoming') }}</p>
            @endforelse
        </div>

        <div class="st-learn-panel">
            <h3>{{ __('student_timeline.learn_upcoming_groups') }}</h3>
            @forelse($upcomingBookings as $booking)
                <a href="{{ route('student.tutoring-bookings.show', $booking) }}" class="st-learn-row st-learn-row--link">
                    <div>
                        <strong>{{ $booking->tutoringGroup?->title ?? '—' }}</strong>
                        <small>{{ optional($booking->starts_at)->locale($locale)->translatedFormat('D j M · H:i') }}</small>
                    </div>
                </a>
            @empty
                <p class="st-learn-note">{{ __('student_timeline.learn_no_group_upcoming') }}</p>
            @endforelse
        </div>
    </section>
@endif
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ route('dashboard') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.learn_path_school') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_path_school_hint') }}</p>
</a>

<a href="{{ $tabUrl('private') }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.learn_path_private') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_path_private_hint') }}</p>
</a>

<a href="{{ $tabUrl('groups') }}" class="st-event-card st-event-card--pink">
    <h3>{{ __('student_timeline.learn_path_groups') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_path_groups_hint') }}</p>
</a>
@endsection
