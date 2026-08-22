@extends('layouts.app')

@section('title', __('instructor.my_courses') . ' - ' . config('app.name'))
@section('page_title', __('instructor.my_courses'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-book-open su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.my_courses') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.courses_assigned_to_you') }}</p>
        </div>
        <div class="su-page-head__actions">
            @if(Route::has('instructor.lectures.index'))
                <a href="{{ route('instructor.lectures.index') }}" class="su-btn">
                    <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                    {{ __('instructor.lectures') }}
                </a>
            @endif
            @if(Route::has('instructor.calendar'))
                <a href="{{ route('instructor.calendar') }}" class="su-btn su-btn--primary">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                    {{ __('instructor.my_calendar') }}
                </a>
            @endif
        </div>
    </div>

    {{-- Same pastel KPI colors as dashboard --}}
    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_courses') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-book" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.active') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['active'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.inactive') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['inactive'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-ban" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.total_students') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_students'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-user-graduate" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid">
            <div class="su-field">
                <label for="search">{{ __('common.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="{{ __('instructor.search_in_course_titles') }}"
                       class="su-input">
            </div>
            <div class="su-field">
                <label for="status">{{ __('common.status') }}</label>
                <select name="status" id="status" class="su-select">
                    <option value="">{{ __('instructor.all_statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('instructor.active_status') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('instructor.inactive_status') }}</option>
                </select>
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary" style="flex:1;justify-content:center;height:40px">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    {{ __('common.search') }}
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('instructor.courses.index') }}" class="su-btn" style="height:40px;width:40px;padding:0;justify-content:center" title="{{ __('common.reset') ?? 'Reset' }}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </section>

    @if($courses->count() > 0)
        <div class="su-course-grid">
            @foreach($courses as $course)
                <article class="su-course-card">
                    <div class="su-course-card__head">
                        <h3 class="su-course-card__title">{{ $course->title }}</h3>
                        <span class="su-chip {{ $course->is_active ? 'su-chip--ok' : 'su-chip--off' }}">
                            <i class="fas {{ $course->is_active ? 'fa-check-circle' : 'fa-ban' }}" aria-hidden="true"></i>
                            {{ $course->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
                        </span>
                    </div>

                    <div class="su-course-card__body">
                        @if($course->description)
                            <p class="su-course-card__desc">{{ Str::limit($course->description, 100) }}</p>
                        @endif

                        <div class="su-meta-list">
                            @if($course->academicYear)
                                <div class="su-meta-row">
                                    <span class="su-meta-ico su-soft-1"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span>
                                    <span>{{ __('instructor.year') }}:</span>
                                    <strong>{{ $course->academicYear->name }}</strong>
                                </div>
                            @endif
                            @if($course->academicSubject)
                                <div class="su-meta-row">
                                    <span class="su-meta-ico su-soft-2"><i class="fas fa-book" aria-hidden="true"></i></span>
                                    <span>{{ __('instructor.subject') }}:</span>
                                    <strong>{{ $course->academicSubject->name }}</strong>
                                </div>
                            @endif
                            @if($course->programming_language)
                                <div class="su-meta-row">
                                    <span class="su-meta-ico su-soft-3"><i class="fas fa-code" aria-hidden="true"></i></span>
                                    <span>{{ __('instructor.language_label') }}:</span>
                                    <strong>{{ $course->programming_language }}</strong>
                                </div>
                            @endif
                            @if($course->level)
                                <div class="su-meta-row">
                                    <span class="su-meta-ico su-soft-4"><i class="fas fa-signal" aria-hidden="true"></i></span>
                                    <span>{{ __('instructor.level_label') }}:</span>
                                    <strong>
                                        @if($course->level == 'beginner') {{ __('instructor.beginner') }}
                                        @elseif($course->level == 'intermediate') {{ __('instructor.intermediate') }}
                                        @else {{ __('instructor.advanced') }}
                                        @endif
                                    </strong>
                                </div>
                            @endif
                            @if(!$course->is_free && $course->effectivePurchasePrice() > 0)
                                <div class="su-meta-row">
                                    <span class="su-meta-ico su-soft-1"><i class="fas fa-money-bill-wave" aria-hidden="true"></i></span>
                                    <span>{{ __('instructor.price') }}:</span>
                                    <strong class="tabular-nums">
                                        @if($course->hasPromotionalPrice())
                                            <span style="text-decoration:line-through;color:var(--su-ink-40);font-size:11px;margin-inline-end:4px">{{ number_format($course->listPriceAmount(), 2) }}</span>
                                        @endif
                                        {{ number_format($course->effectivePurchasePrice(), 2) }} $
                                    </strong>
                                </div>
                            @else
                                <div class="su-meta-row">
                                    <span class="su-meta-ico su-soft-3"><i class="fas fa-gift" aria-hidden="true"></i></span>
                                    <strong style="color:#15803d">{{ __('instructor.free') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="su-course-card__stats">
                        <div class="su-course-card__stat">
                            <b>{{ $course->lectures_count ?? 0 }}</b>
                            <span>{{ __('instructor.lecture_single') }}</span>
                        </div>
                        <div class="su-course-card__stat">
                            <b>{{ $course->enrollments_count ?? 0 }}</b>
                            <span>{{ __('instructor.student_single') }}</span>
                        </div>
                    </div>

                    <div class="su-course-card__foot">
                        <a href="{{ route('instructor.courses.show', $course) }}" class="su-btn su-btn--primary">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            {{ __('instructor.view_details') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="su-pager">
            {{ $courses->links() }}
        </div>
    @else
        <div class="su-card">
            <div class="su-empty" style="padding:48px 16px">
                <i class="fas fa-book-open" aria-hidden="true"></i>
                <h3 style="margin:0;font-size:16px;font-weight:600;color:var(--su-ink)">{{ __('instructor.no_courses') }}</h3>
                <p>{{ __('instructor.courses_description_empty') }}</p>
            </div>
        </div>
    @endif
</div>
@endsection
