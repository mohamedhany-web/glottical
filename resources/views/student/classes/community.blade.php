@extends('layouts.student-timeline')

@section('title', __('student_timeline.class_community').' · '.$cohort->title)

@section('content')
@php
    $locale = app()->getLocale();
    $feedPosts = $feedPosts ?? collect();
    $canModerateFeed = $canModerateFeed ?? false;
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.class_community'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.my_classes'), 'url' => route('student.classes.index')],
        ['label' => \Illuminate\Support\Str::limit($cohort->title, 24), 'url' => route('student.classes.show', $cohort)],
        ['label' => __('student_timeline.class_community'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

<section class="st-event-card st-event-card--blue st-biz-banner st-class-hero">
    <img class="st-event-card__mask" src="{{ $eventMasks[1] }}" alt="" width="160" height="160">
    <div class="st-biz-banner__row">
        <div>
            <p class="st-event-card__kicker">{{ $cohort->title }}</p>
            <h3>{{ __('student_timeline.class_community') }}</h3>
            <p class="st-event-card__sub">{{ __('student_timeline.class_community_hint') }}</p>
        </div>
        <div class="st-biz-banner__actions">
            <a href="{{ route('student.classes.show', $cohort) }}" class="st-pill st-pill--light">
                <i class="fas fa-arrow-{{ $locale === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('student_timeline.back_to_class') }}
            </a>
        </div>
    </div>
</section>

<section class="st-panel st-community-panel">
    <form method="POST" action="{{ route('student.classes.feed.store', $cohort) }}" class="st-feed-compose">
        @csrf
        <textarea name="body" rows="3" maxlength="1000" required
                  placeholder="{{ __('student_timeline.class_post_placeholder') }}"></textarea>
        <div class="st-feed-compose__bar">
            @if($canModerateFeed)
                <select name="post_type">
                    <option value="question">{{ __('student_timeline.class_post_question') }}</option>
                    <option value="announcement">{{ __('student_timeline.class_post_announcement') }}</option>
                </select>
            @else
                <input type="hidden" name="post_type" value="question">
                <span class="st-feed-compose__hint">{{ __('student_timeline.class_post_question') }}</span>
            @endif
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

                @if($post->visibleComments->isNotEmpty())
                    <ul class="st-feed-comments">
                        @foreach($post->visibleComments as $comment)
                            <li>
                                <strong>{{ $comment->author?->name }}:</strong>
                                <span>{{ $comment->body }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('student.classes.feed.comment', $post) }}" class="st-feed-reply">
                    @csrf
                    <input type="text" name="body" maxlength="1000" required placeholder="{{ __('student_timeline.class_comment_placeholder') }}">
                    <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.class_reply') }}</button>
                </form>
            </li>
        @empty
            <li class="st-empty-panel st-community-empty">
                <h3>{{ __('student_timeline.class_feed_empty') }}</h3>
                <p>{{ __('student_timeline.class_feed_empty_hint') }}</p>
            </li>
        @endforelse
    </ul>
</section>
@endsection

@section('events')
@php
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
@endphp
<div class="st-events__top">
    <h2>{{ __('student_timeline.my_classes') }}</h2>
</div>
<div data-tab-panel="activities">
    <a href="{{ route('student.classes.show', $cohort) }}" class="st-event-card st-event-card--blue">
        <img class="st-event-card__mask" src="{{ $eventMasks[1] }}" alt="" width="160" height="160">
        <h3>{{ $cohort->title }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.back_to_class') }}</p>
    </a>
</div>
<div class="st-events__see">
    <a href="{{ route('student.classes.index') }}">{{ __('student_timeline.back_to_classes') }}</a>
</div>
@endsection
