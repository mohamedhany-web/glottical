@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_settings'))

@section('content')
@php
    $locale = app()->getLocale();
    $user = auth()->user();
    $avatarUrl = $user?->avatarDisplayUrl() ?? \App\Models\User::placeholderAvatarUrl();
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_settings'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_settings'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

<section class="st-settings-hero" aria-label="{{ __('student_timeline.nav_settings') }}">
    <img class="st-settings-hero__avatar" src="{{ $avatarUrl }}" alt="" width="56" height="56">
    <div class="st-settings-hero__copy">
        <h2>{{ $user?->name }}</h2>
        <p>{{ $user?->email }}</p>
    </div>
    <div class="st-settings-hero__actions">
        <a href="{{ route('profile') }}" class="st-pill st-pill--solid">{{ __('student_timeline.profile') }}</a>
        <a href="{{ route('notifications') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_messages') }}</a>
    </div>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.settings_title') }}</h2>
        <p>{{ __('student_timeline.settings_hint') }}</p>
    </div>
</section>

<section class="st-settings-block" aria-labelledby="stSetLang">
    <div class="st-settings-block__head">
        <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-language"></i></span>
        <div>
            <h3 id="stSetLang">{{ __('student_timeline.settings_language') }}</h3>
            <p>{{ __('student_timeline.settings_language_hint') }}</p>
        </div>
    </div>
    <div class="st-settings-lang">
        <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}" class="st-pill {{ $locale === 'ar' ? 'st-pill--solid' : 'st-pill--outline' }}">{{ __('student_timeline.lang_ar') }}</a>
        <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="st-pill {{ $locale === 'en' ? 'st-pill--solid' : 'st-pill--outline' }}">{{ __('student_timeline.lang_en') }}</a>
    </div>
</section>

<section class="st-settings-block" aria-labelledby="stSetNotif">
    <div class="st-settings-block__head">
        <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-bell"></i></span>
        <div>
            <h3 id="stSetNotif">{{ __('student_timeline.settings_notifications') }}</h3>
            <p>{{ __('student_timeline.settings_notifications_hint') }}</p>
        </div>
    </div>
    <div class="st-settings-rows" id="stPrefNotif">
        <label class="st-settings-row">
            <span>
                <strong>{{ __('student_timeline.pref_new_courses') }}</strong>
                <small>{{ __('student_timeline.pref_new_courses_hint') }}</small>
            </span>
            <input type="checkbox" data-pref="notif_courses" checked>
        </label>
        <label class="st-settings-row">
            <span>
                <strong>{{ __('student_timeline.pref_orders') }}</strong>
                <small>{{ __('student_timeline.pref_orders_hint') }}</small>
            </span>
            <input type="checkbox" data-pref="notif_orders" checked>
        </label>
        <label class="st-settings-row">
            <span>
                <strong>{{ __('student_timeline.pref_classes') }}</strong>
                <small>{{ __('student_timeline.pref_classes_hint') }}</small>
            </span>
            <input type="checkbox" data-pref="notif_classes" checked>
        </label>
    </div>
</section>

<section class="st-settings-block" aria-labelledby="stSetPrivacy">
    <div class="st-settings-block__head">
        <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-shield-halved"></i></span>
        <div>
            <h3 id="stSetPrivacy">{{ __('student_timeline.settings_privacy') }}</h3>
            <p>{{ __('student_timeline.settings_privacy_hint') }}</p>
        </div>
    </div>
    <div class="st-settings-rows" id="stPrefPrivacy">
        <label class="st-settings-row">
            <span>
                <strong>{{ __('student_timeline.pref_show_progress') }}</strong>
                <small>{{ __('student_timeline.pref_show_progress_hint') }}</small>
            </span>
            <input type="checkbox" data-pref="privacy_progress" checked>
        </label>
        <label class="st-settings-row">
            <span>
                <strong>{{ __('student_timeline.pref_show_activity') }}</strong>
                <small>{{ __('student_timeline.pref_show_activity_hint') }}</small>
            </span>
            <input type="checkbox" data-pref="privacy_activity">
        </label>
    </div>
</section>

<section class="st-settings-block" aria-labelledby="stSetAccount">
    <div class="st-settings-block__head">
        <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-user-cog"></i></span>
        <div>
            <h3 id="stSetAccount">{{ __('student_timeline.settings_account') }}</h3>
            <p>{{ __('student_timeline.settings_account_hint') }}</p>
        </div>
    </div>
    <div class="st-settings-actions">
        <a href="{{ route('profile') }}" class="st-settings-action">
            <i class="fas fa-user" aria-hidden="true"></i>
            <span>
                <strong>{{ __('student_timeline.edit_profile') }}</strong>
                <small>{{ __('student_timeline.edit_profile_hint') }}</small>
            </span>
        </a>
        <a href="{{ route('notifications') }}" class="st-settings-action">
            <i class="fas fa-bell" aria-hidden="true"></i>
            <span>
                <strong>{{ __('student_timeline.nav_messages') }}</strong>
                <small>{{ __('student_timeline.notif_hint') }}</small>
            </span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="st-settings-action st-settings-action--form">
            @csrf
            <button type="submit">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                <span>
                    <strong>{{ __('student_timeline.nav_logout') }}</strong>
                    <small>{{ __('student_timeline.logout_hint') }}</small>
                </span>
            </button>
        </form>
    </div>
</section>

<p class="st-settings-note" id="stPrefSaved" hidden>{{ __('student_timeline.prefs_saved') }}</p>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ route('profile') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.profile') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.edit_profile_hint') }}</p>
</a>

<a href="{{ route('notifications') }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.nav_messages') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.notif_hint') }}</p>
</a>

<a href="{{ route('dashboard') }}" class="st-event-card st-event-card--pink">
    <h3>{{ __('student_timeline.school_gate') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.back_to_timeline') }}</p>
</a>
@endsection

@push('scripts')
<script>
(function () {
    var KEY = 'st-student-prefs';
    var note = document.getElementById('stPrefSaved');
    var defaults = {
        notif_courses: true,
        notif_orders: true,
        notif_classes: true,
        privacy_progress: true,
        privacy_activity: false
    };

    function load() {
        try {
            return Object.assign({}, defaults, JSON.parse(localStorage.getItem(KEY) || '{}'));
        } catch (e) {
            return Object.assign({}, defaults);
        }
    }

    function save(prefs) {
        try { localStorage.setItem(KEY, JSON.stringify(prefs)); } catch (e) {}
        if (note) {
            note.hidden = false;
            clearTimeout(window.__stPrefTimer);
            window.__stPrefTimer = setTimeout(function () { note.hidden = true; }, 1800);
        }
    }

    var prefs = load();
    document.querySelectorAll('[data-pref]').forEach(function (input) {
        var key = input.getAttribute('data-pref');
        if (Object.prototype.hasOwnProperty.call(prefs, key)) {
            input.checked = !!prefs[key];
        }
        input.addEventListener('change', function () {
            prefs[key] = !!input.checked;
            save(prefs);
        });
    });
})();
</script>
@endpush
