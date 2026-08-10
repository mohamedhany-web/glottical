@extends('layouts.student-timeline')

@section('title', __('student_timeline.booking_details_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $group = $booking->tutoringGroup;
    $status = (string) $booking->status;
    $hasLive = (bool) $booking->classroomMeeting;
    $joinUrl = $hasLive ? url('classroom/join/'.$booking->classroomMeeting->code) : null;
    $teacherUrl = ($booking->instructor_id && Route::has('student.learn.teacher'))
        ? route('student.learn.teacher', $booking->instructor_id)
        : null;
    $learnGroupsUrl = Route::has('student.learn.index')
        ? route('student.learn.index', ['tab' => 'groups'])
        : (Route::has('public.groups') ? route('public.groups') : route('dashboard'));
    $photo = $booking->instructor?->profile_image_url
        ?: asset('img/student-timeline/avatar.png');

    $statusHint = match ($status) {
        \App\Models\TutoringGroupBooking::STATUS_PENDING => __('student_timeline.booking_hint_pending'),
        \App\Models\TutoringGroupBooking::STATUS_CONFIRMED => $hasLive
            ? __('student_timeline.booking_hint_confirmed_live')
            : __('student_timeline.booking_hint_confirmed'),
        \App\Models\TutoringGroupBooking::STATUS_COMPLETED => __('student_timeline.booking_hint_completed'),
        \App\Models\TutoringGroupBooking::STATUS_CANCELLED => __('student_timeline.booking_hint_cancelled'),
        default => __('student_timeline.booking_hint_default'),
    };

    $when = $booking->starts_at
        ? $booking->starts_at->locale($locale)->translatedFormat('D j M Y · H:i')
        : '—';
    $ends = $booking->ends_at
        ? $booking->ends_at->locale($locale)->translatedFormat('H:i')
        : null;
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.booking_details_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.bookings_title'), 'url' => route('student.tutoring-bookings.index')],
        ['label' => '#'.$booking->id, 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

<section class="st-join-hero {{ $status === \App\Models\TutoringGroupBooking::STATUS_CONFIRMED ? '' : 'st-join-hero--muted' }}" aria-label="{{ $booking->statusLabel() }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $booking->statusLabel() }} · #{{ $booking->id }}</p>
        <h2 class="st-join-hero__title">{{ $group?->title ?? __('student_timeline.group_session') }}</h2>
        <p class="st-join-hero__meta">{{ $statusHint }}</p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('student.tutoring-bookings.index') }}" class="st-pill st-pill--outline">{{ __('student_timeline.back_to_bookings') }}</a>
        @if($joinUrl)
            <a href="{{ $joinUrl }}" class="st-pill st-pill--solid st-pill--lg">
                <i class="fas fa-video" aria-hidden="true"></i>
                {{ __('student_timeline.join_live') }}
            </a>
        @elseif($status === \App\Models\TutoringGroupBooking::STATUS_PENDING)
            <a href="{{ $learnGroupsUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.learn_tab_groups') }}</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.booking_summary') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.booking_when') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $when }}</p>
        @if($ends)
            <p class="st-stat-card__hint">{{ __('student_timeline.booking_ends_at', ['time' => $ends]) }}</p>
        @endif
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.booking_instructor') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $booking->instructor?->name ?? '—' }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.booking_status') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">
            <span class="st-booking-badge is-{{ $status }}">{{ $booking->statusLabel() }}</span>
        </p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.booking_payment') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $booking->paymentStatusLabel() }}</p>
    </article>
</section>

<div class="st-order-show">
    <div class="st-order-show__main">
        <section class="st-order-panel">
            <header class="st-order-panel__head">
                <h2>{{ __('student_timeline.booking_session') }}</h2>
            </header>
            <div class="st-order-panel__body">
                <div class="st-order-item">
                    <div class="st-order-item__media" aria-hidden="true">
                        <img src="{{ $photo }}" alt="" loading="lazy">
                    </div>
                    <div class="st-order-item__copy">
                        <h3>{{ $group?->title ?? __('student_timeline.group_session') }}</h3>
                        <p>
                            {{ $booking->instructor?->name ?? '—' }}
                            @if($group?->typeLabel()) · {{ $group->typeLabel() }}@endif
                            @if($group?->academicSubject?->name) · {{ $group->academicSubject->name }}@endif
                        </p>

                        <div class="st-order-facts st-order-facts--wide">
                            <div>
                                <span>{{ __('student_timeline.booking_when') }}</span>
                                <strong>{{ $when }}</strong>
                            </div>
                            @if($booking->cohort)
                                <div>
                                    <span>{{ __('student_timeline.booking_cohort') }}</span>
                                    <strong>{{ $booking->cohort->title }}</strong>
                                </div>
                            @endif
                            @if($booking->package)
                                <div>
                                    <span>{{ __('student_timeline.booking_package') }}</span>
                                    <strong>{{ $booking->package->name ?? ('#'.$booking->package->id) }}</strong>
                                </div>
                            @endif
                            @if($hasLive)
                                <div>
                                    <span>{{ __('student_timeline.booking_live_code') }}</span>
                                    <strong dir="ltr">{{ $booking->classroomMeeting->code }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="st-order-card__foot" style="margin-top:10px">
                            @if($joinUrl)
                                <a href="{{ $joinUrl }}" class="st-pill st-pill--solid">
                                    <i class="fas fa-video" aria-hidden="true"></i>
                                    {{ __('student_timeline.join_live') }}
                                </a>
                            @endif
                            @if($teacherUrl)
                                <a href="{{ $teacherUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.learn_view_teacher') }}</a>
                            @endif
                            <a href="{{ $learnGroupsUrl }}" class="st-pill st-pill--ghost">{{ __('student_timeline.learn_tab_groups') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if($booking->student_notes || $booking->admin_notes)
            <section class="st-order-panel">
                <header class="st-order-panel__head">
                    <h2>{{ __('student_timeline.booking_notes') }}</h2>
                </header>
                <div class="st-order-panel__body">
                    @if($booking->student_notes)
                        <div class="st-order-notes">
                            <strong>{{ __('student_timeline.booking_student_notes') }}</strong>
                            <p>{{ $booking->student_notes }}</p>
                        </div>
                    @endif
                    @if($booking->admin_notes)
                        <div class="st-order-notes" @if($booking->student_notes) style="margin-top:10px" @endif>
                            <strong>{{ __('student_timeline.booking_admin_notes') }}</strong>
                            <p>{{ $booking->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </div>

    <aside class="st-order-show__side">
        <section class="st-order-panel st-order-panel--status is-{{ $status === 'confirmed' ? 'approved' : ($status === 'cancelled' ? 'rejected' : ($status === 'pending' ? 'pending' : 'approved')) }}">
            <header class="st-order-panel__head">
                <h2>{{ __('student_timeline.booking_status') }}</h2>
            </header>
            <div class="st-order-panel__body st-order-status">
                <span class="st-order-status__icon" aria-hidden="true">
                    @if($status === 'pending')
                        <i class="fas fa-clock"></i>
                    @elseif($status === 'confirmed')
                        <i class="fas fa-check"></i>
                    @elseif($status === 'completed')
                        <i class="fas fa-flag-checkered"></i>
                    @else
                        <i class="fas fa-times"></i>
                    @endif
                </span>
                <strong>{{ $booking->statusLabel() }}</strong>
                <p>{{ $statusHint }}</p>

                @if($joinUrl)
                    <a href="{{ $joinUrl }}" class="st-pill st-pill--solid" style="width:100%;justify-content:center">
                        <i class="fas fa-video" aria-hidden="true"></i>
                        {{ __('student_timeline.join_live') }}
                    </a>
                    <div class="st-order-invoice">
                        <span>{{ __('student_timeline.booking_live_code') }}</span>
                        <strong dir="ltr">{{ $booking->classroomMeeting->code }}</strong>
                    </div>
                @endif
            </div>
        </section>
    </aside>
</div>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ route('student.tutoring-bookings.index') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.bookings_title') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.back_to_bookings') }}</p>
</a>

<a href="{{ $learnGroupsUrl }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.learn_tab_groups') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_groups_simple') }}</p>
</a>

@if($teacherUrl)
    <a href="{{ $teacherUrl }}" class="st-event-card st-event-card--pink">
        <h3>{{ __('student_timeline.learn_view_teacher') }}</h3>
        <p class="st-event-card__sub">{{ $booking->instructor?->name }}</p>
    </a>
@endif
@endsection
