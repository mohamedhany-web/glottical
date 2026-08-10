@extends('layouts.student-timeline')

@section('title', $instructor->name.' — '.__('student_timeline.learn_teacher_profile'))

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $profile = $profile ?? null;
    $canBook = !empty($can_book);
    $unitsLeft = (int) ($units_left ?? 0);
    $bookableSlots = $bookable_slots ?? collect();
    $weeklyCalendar = $weekly_calendar ?? [];
    $groupCourses = $group_courses ?? collect();
    $oneToOneCourses = $one_to_one_courses ?? collect();
    $packagesUrl = $packages_url ?? route('public.service-packages.index');
    $photoUrl = $photo_url ?: asset('img/student-timeline/avatar.png');
    $headline = $profile?->headline_clean ?: '';
    $bio = $profile?->bio_clean ?: '';
    $skills = $profile?->skills_list ?? [];
    $experience = $profile?->experience_list ?? [];
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.learn_teacher_profile'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_learn'), 'url' => route('student.learn.index')],
        ['label' => $instructor->name, 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

<section class="st-teacher-hero">
    <div class="st-teacher-hero__glow" aria-hidden="true"></div>
    <div class="st-teacher-hero__inner">
        <img class="st-teacher-hero__avatar" src="{{ $photoUrl }}" alt="" width="112" height="112">
        <div class="st-teacher-hero__copy">
            <p class="st-teacher-hero__kicker">{{ __('student_timeline.learn_teacher_kicker') }}</p>
            <h1>{{ $instructor->name }}</h1>
            @if($headline)
                <p class="st-teacher-hero__headline">{{ $headline }}</p>
            @endif
            @if(!empty($skills))
                <div class="st-learn-chips st-learn-chips--light">
                    @foreach(array_slice($skills, 0, 6) as $skill)
                        <span>{{ $skill }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="st-teacher-hero__credit {{ $canBook ? 'is-ok' : 'is-empty' }}">
            @if($canBook)
                <span>{{ __('student_timeline.learn_your_credit') }}</span>
                <strong>{{ $unitsLeft }}</strong>
                <small>{{ __('student_timeline.learn_units_sessions') }}</small>
            @else
                <span>{{ __('student_timeline.learn_need_package') }}</span>
                <a href="{{ $packagesUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.recharge_package') }}</a>
            @endif
        </div>
    </div>
</section>

<div class="st-teacher-layout">
    <div class="st-teacher-main">
        <section class="st-settings-block" aria-labelledby="stTeacherBook">
            <div class="st-settings-block__head">
                <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-calendar-check"></i></span>
                <div>
                    <h3 id="stTeacherBook">{{ __('student_timeline.learn_book_private') }}</h3>
                    <p>{{ __('student_timeline.learn_book_private_hint') }}</p>
                </div>
            </div>

            @if($canBook)
                @if($bookableSlots->isNotEmpty())
                    <form method="POST" action="{{ route('student.one-to-one-sessions.book-instructor', $instructor) }}" class="st-teacher-slots">
                        @csrf
                        @foreach($bookableSlots as $slot)
                            @php
                                $starts = is_array($slot) ? ($slot['starts_at'] ?? null) : null;
                                $label = is_array($slot) ? ($slot['label'] ?? null) : null;
                                if ($starts instanceof \Carbon\Carbon) {
                                    $value = $starts->toDateTimeString();
                                    $label = $label ?: $starts->locale($locale)->translatedFormat('D j M — g:i A');
                                } else {
                                    continue;
                                }
                            @endphp
                            <button type="submit" name="scheduled_at" value="{{ $value }}" class="st-teacher-slot">
                                <span>{{ $label }}</span>
                                <i class="fas fa-bolt" aria-hidden="true"></i>
                            </button>
                        @endforeach
                    </form>
                @else
                    <p class="st-learn-note">{{ __('student_timeline.learn_no_slots') }}</p>
                @endif
            @else
                <div class="st-teacher-cta">
                    <p>{{ __('student_timeline.learn_book_needs_credit') }}</p>
                    <a href="{{ $packagesUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.recharge_package') }}</a>
                    <a href="{{ route('student.learn.index', ['tab' => 'private']) }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_learn') }}</a>
                </div>
            @endif
        </section>

        @if($bio !== '')
            <section class="st-settings-block" aria-labelledby="stTeacherBio">
                <div class="st-settings-block__head">
                    <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-quote-right"></i></span>
                    <div>
                        <h3 id="stTeacherBio">{{ __('student_timeline.learn_about_teacher') }}</h3>
                        <p>{{ __('student_timeline.learn_about_hint') }}</p>
                    </div>
                </div>
                <p class="st-teacher-bio">{{ $bio }}</p>
            </section>
        @endif

        @if(!empty($experience))
            <section class="st-settings-block" aria-labelledby="stTeacherExp">
                <div class="st-settings-block__head">
                    <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-medal"></i></span>
                    <div>
                        <h3 id="stTeacherExp">{{ __('student_timeline.learn_experience') }}</h3>
                        <p>{{ __('student_timeline.learn_experience_hint') }}</p>
                    </div>
                </div>
                <ul class="st-teacher-list">
                    @foreach(array_slice($experience, 0, 8) as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if($oneToOneCourses->isNotEmpty() || $groupCourses->isNotEmpty())
            <section class="st-settings-block" aria-labelledby="stTeacherCourses">
                <div class="st-settings-block__head">
                    <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-graduation-cap"></i></span>
                    <div>
                        <h3 id="stTeacherCourses">{{ __('student_timeline.learn_teacher_courses') }}</h3>
                        <p>{{ __('student_timeline.learn_teacher_courses_hint') }}</p>
                    </div>
                </div>
                <div class="st-teacher-courses">
                    @foreach($oneToOneCourses->take(4) as $course)
                        <article class="st-teacher-course">
                            <span class="st-learn-badge is-ok">1:1</span>
                            <strong>{{ $course->title }}</strong>
                            <small>{{ (int) ($course->lessons_count ?? 0) }} {{ __('student_timeline.learn_lessons') }}</small>
                        </article>
                    @endforeach
                    @foreach($groupCourses->take(4) as $course)
                        <article class="st-teacher-course">
                            <span class="st-learn-badge">{{ __('student_timeline.learn_tab_groups') }}</span>
                            <strong>{{ $course->title }}</strong>
                            <small>{{ (int) ($course->lessons_count ?? 0) }} {{ __('student_timeline.learn_lessons') }}</small>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <aside class="st-teacher-side">
        <section class="st-teacher-week" aria-label="{{ __('student_timeline.learn_week_rhythm') }}">
            <h3>{{ __('student_timeline.learn_week_rhythm') }}</h3>
            @forelse($weeklyCalendar as $day)
                <div class="st-teacher-week__day">
                    <strong>{{ $day['label'] }}</strong>
                    <div class="st-learn-chips">
                        @foreach($day['times'] as $time)
                            <span>{{ $time }}</span>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="st-learn-note">{{ __('student_timeline.learn_no_week_rules') }}</p>
            @endforelse
        </section>

        <a href="{{ route('student.learn.index', ['tab' => 'groups']) }}" class="st-event-card st-event-card--blue" style="display:block;text-decoration:none">
            <h3>{{ __('student_timeline.learn_tab_groups') }}</h3>
            <p class="st-event-card__sub">{{ __('student_timeline.learn_groups_hint') }}</p>
        </a>
    </aside>
</div>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ route('student.learn.index') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_learn') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_hint') }}</p>
</a>

@if($canBook)
    <div class="st-event-card st-event-card--green">
        <h3>{{ __('student_timeline.learn_credit_badge', ['count' => $unitsLeft]) }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.learn_book_private_hint') }}</p>
    </div>
@else
    <a href="{{ $packagesUrl }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.recharge_package') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.learn_book_needs_credit') }}</p>
    </a>
@endif

<a href="{{ route('student.private-lectures.index') }}" class="st-event-card st-event-card--pink">
    <h3>{{ __('student_timeline.nav_lessons') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.learn_upcoming_private') }}</p>
</a>
@endsection
