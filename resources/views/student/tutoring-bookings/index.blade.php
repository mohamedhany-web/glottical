@extends('layouts.student-timeline')

@section('title', __('student_timeline.bookings_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $bookings = $bookings ?? collect();
    $upcoming = $upcoming ?? null;
    $learnGroupsUrl = Route::has('student.learn.index')
        ? route('student.learn.index', ['tab' => 'groups'])
        : (Route::has('public.groups') ? route('public.groups') : route('dashboard'));
    $tones = ['blue', 'pink', 'orange', 'purple'];
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.bookings_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.bookings_title'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif

@if($upcoming)
    @php
        $upJoin = $upcoming->classroomMeeting
            ? url('classroom/join/'.$upcoming->classroomMeeting->code)
            : null;
    @endphp
    <section class="st-join-hero" aria-label="{{ __('student_timeline.booking_next') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.booking_next') }}</p>
            <h2 class="st-join-hero__title">{{ $upcoming->tutoringGroup?->title ?? __('student_timeline.group_session') }}</h2>
            <p class="st-join-hero__meta">
                {{ $upcoming->starts_at?->locale($locale)->translatedFormat('D j M · H:i') }}
                · {{ $upcoming->instructor?->name ?? '—' }}
            </p>
        </div>
        <div class="st-join-hero__actions">
            @if($upJoin)
                <a href="{{ $upJoin }}" class="st-pill st-pill--solid st-pill--lg">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    {{ __('student_timeline.join_live') }}
                </a>
            @endif
            <a href="{{ route('student.tutoring-bookings.show', $upcoming) }}" class="st-pill st-pill--outline">{{ __('student_timeline.view_booking') }}</a>
        </div>
    </section>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.bookings_title') }}</h2>
        <p>{{ __('student_timeline.bookings_hint') }}</p>
    </div>
    <a href="{{ $learnGroupsUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.learn_tab_groups') }}</a>
</section>

<section class="st-order-list" aria-label="{{ __('student_timeline.bookings_title') }}">
    @forelse($bookings as $i => $booking)
        @php
            $tone = $tones[$i % count($tones)];
            $status = $booking->status;
            $join = $booking->classroomMeeting
                ? url('classroom/join/'.$booking->classroomMeeting->code)
                : null;
        @endphp
        <article class="st-order-card st-order-card--{{ $tone }}">
            <div class="st-order-card__main">
                <div class="st-order-card__copy">
                    <div class="st-order-card__badges">
                        <span class="st-booking-badge is-{{ $status }}">{{ $booking->statusLabel() }}</span>
                        <span class="st-order-card__when">{{ $booking->starts_at?->diffForHumans() }}</span>
                    </div>
                    <h3>{{ $booking->tutoringGroup?->title ?? __('student_timeline.group_session') }}</h3>
                    <p class="st-order-card__meta">
                        {{ $booking->instructor?->name ?? '—' }}
                        @if($booking->cohort) · {{ $booking->cohort->title }}@endif
                    </p>
                </div>
                <div class="st-order-card__amount">
                    <strong>{{ $booking->starts_at?->format('H:i') ?: '—' }}</strong>
                    <span>{{ $booking->starts_at?->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="st-order-card__facts">
                <span>{{ __('student_timeline.booking_status') }}: {{ $booking->statusLabel() }}</span>
                <span>{{ __('student_timeline.booking_payment') }}: {{ $booking->paymentStatusLabel() }}</span>
            </div>

            <div class="st-order-card__foot">
                <a href="{{ route('student.tutoring-bookings.show', $booking) }}" class="st-pill st-pill--solid">
                    {{ __('student_timeline.view_booking') }}
                </a>
                @if($join)
                    <a href="{{ $join }}" class="st-pill st-pill--outline">
                        <i class="fas fa-video" aria-hidden="true"></i>
                        {{ __('student_timeline.join_live') }}
                    </a>
                @endif
            </div>
        </article>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.no_bookings') }}</h3>
            <p>{{ __('student_timeline.no_bookings_hint') }}</p>
            <div class="st-biz-banner__actions">
                <a href="{{ $learnGroupsUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.learn_tab_groups') }}</a>
            </div>
        </div>
    @endforelse
</section>

@if(method_exists($bookings, 'hasPages') && $bookings->hasPages())
    <div class="st-pager">{{ $bookings->links() }}</div>
@endif
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ $learnGroupsUrl }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.learn_tab_groups') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_groups_simple') }}</p>
</a>

<a href="{{ route('dashboard') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.school_gate') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_path_school_hint') }}</p>
</a>
@endsection
