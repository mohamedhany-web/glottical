@extends('layouts.student-timeline')

@section('title', __('student_timeline.profile'))

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $avatarUrl = $user->profile_image_url
        ?: asset('img/student-timeline/avatar.png');

    $roleKey = match ($user->role ?? '') {
        'teacher' => 'role_teacher',
        'admin' => 'role_admin',
        'super_admin' => 'role_super_admin',
        default => 'role_student',
    };

    $memberSince = $user->created_at
        ? $user->created_at->copy()->locale($locale)->translatedFormat($isRtl ? 'd F Y' : 'M d, Y')
        : '—';

    $lastLogin = $user->last_login_at
        ? $user->last_login_at->copy()->locale($locale)->diffForHumans()
        : null;

    $memberId = '#'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT);
    $classUserId = (int) $user->id;
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.profile'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_settings'), 'url' => route('settings')],
        ['label' => __('student_timeline.profile'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

<section class="st-settings-hero" aria-label="{{ __('student_timeline.profile') }}">
    <img class="st-settings-hero__avatar" src="{{ $avatarUrl }}" alt="" width="56" height="56">
    <div class="st-settings-hero__copy">
        <h2>{{ $user->name }}</h2>
        <p>{{ $user->email ?: ($user->phone ?: __('student_timeline.'.$roleKey)) }}</p>
    </div>
    <div class="st-settings-hero__actions">
        <a href="{{ route('settings') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_settings') }}</a>
        <a href="{{ route('dashboard') }}" class="st-pill st-pill--solid">{{ __('student_timeline.school_gate') }}</a>
    </div>
</section>

<section class="st-class-id" aria-label="{{ __('student_timeline.class_user_id') }}">
    <div class="st-class-id__copy">
        <p class="st-class-id__kicker">{{ __('student_timeline.class_user_id') }}</p>
        <p class="st-class-id__value" dir="ltr" id="stClassUserId">{{ $classUserId }}</p>
        <p class="st-class-id__hint">{{ __('student_timeline.class_user_id_hint') }}</p>
    </div>
    <button type="button" class="st-pill st-pill--solid" id="stCopyClassUserId" data-copy="{{ $classUserId }}">
        <i class="fas fa-copy" aria-hidden="true"></i>
        {{ __('student_timeline.copy_user_id') }}
    </button>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.profile_title') }}</h2>
        <p>{{ __('student_timeline.profile_hint') }}</p>
    </div>
</section>

<section class="st-profile-meta" aria-label="{{ __('student_timeline.profile_summary') }}">
    <div class="st-profile-meta__item st-profile-meta__item--accent">
        <span>{{ __('student_timeline.class_user_id') }}</span>
        <strong dir="ltr">{{ $classUserId }}</strong>
    </div>
    <div class="st-profile-meta__item">
        <span>{{ __('student_timeline.member_id') }}</span>
        <strong dir="ltr">{{ $memberId }}</strong>
    </div>
    <div class="st-profile-meta__item">
        <span>{{ __('student_timeline.account_type') }}</span>
        <strong>{{ __('student_timeline.'.$roleKey) }}</strong>
    </div>
    <div class="st-profile-meta__item">
        <span>{{ __('student_timeline.join_date') }}</span>
        <strong>{{ $memberSince }}</strong>
    </div>
</section>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="st-profile-form">
    @csrf
    @method('PUT')

    <section class="st-settings-block" aria-labelledby="stProfBasics">
        <div class="st-settings-block__head">
            <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-user"></i></span>
            <div>
                <h3 id="stProfBasics">{{ __('student_timeline.profile_basics') }}</h3>
                <p>{{ __('student_timeline.profile_basics_hint') }}</p>
            </div>
        </div>

        <div class="st-profile-grid">
            <label class="st-field">
                <span>{{ __('student_timeline.full_name') }}</span>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name')<small class="st-field__err">{{ $message }}</small>@enderror
            </label>

            <label class="st-field">
                <span>{{ __('student_timeline.phone') }}</span>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required autocomplete="tel" dir="ltr">
                @error('phone')<small class="st-field__err">{{ $message }}</small>@enderror
            </label>

            <label class="st-field st-field--full">
                <span>{{ __('student_timeline.email_optional') }}</span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" dir="ltr">
                @error('email')<small class="st-field__err">{{ $message }}</small>@enderror
            </label>

            <label class="st-field st-field--full">
                <span>{{ app()->getLocale() === 'ar' ? 'المنطقة الزمنية' : 'Timezone' }}</span>
                @php
                    $tzOptions = \App\Support\AppTimezone::commonZones();
                    $tzCurrent = old('timezone', $user->timezone ?: \App\Support\AppTimezone::academy());
                    if ($tzCurrent && ! array_key_exists($tzCurrent, $tzOptions)) {
                        $tzOptions = [$tzCurrent => $tzCurrent] + $tzOptions;
                    }
                @endphp
                <select name="timezone" data-timezone-select required>
                    @foreach ($tzOptions as $tzId => $tzLabel)
                        <option value="{{ $tzId }}" @selected($tzCurrent === $tzId)>{{ $tzLabel }}</option>
                    @endforeach
                </select>
                <small class="st-field__hint" style="opacity:.75;display:block;margin-top:.35rem">
                    {{ app()->getLocale() === 'ar'
                        ? 'تُعرض مواعيد الحصص والفصول بتوقيتك. المعلمون يدخلون المواعيد بتوقيت مصر.'
                        : 'Class times are shown in your timezone. Teachers schedule in Egypt time.' }}
                </small>
                @error('timezone')<small class="st-field__err">{{ $message }}</small>@enderror
            </label>
        </div>
    </section>

    <section class="st-settings-block" aria-labelledby="stProfPhoto">
        <div class="st-settings-block__head">
            <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-camera"></i></span>
            <div>
                <h3 id="stProfPhoto">{{ __('student_timeline.profile_photo') }}</h3>
                <p>{{ __('student_timeline.profile_photo_hint') }}</p>
            </div>
        </div>

        <div class="st-profile-photo">
            <img src="{{ $avatarUrl }}" alt="" width="88" height="88" id="stProfPreview">
            <div class="st-profile-photo__actions">
                <label class="st-pill st-pill--solid st-profile-upload">
                    <i class="fas fa-upload" aria-hidden="true"></i>
                    {{ __('student_timeline.choose_photo') }}
                    <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif" id="stProfFile">
                </label>
                <p>{{ __('student_timeline.photo_limit') }}</p>
                @error('profile_image')<small class="st-field__err">{{ $message }}</small>@enderror
            </div>
        </div>
    </section>

    <section class="st-settings-block" aria-labelledby="stProfPass">
        <div class="st-settings-block__head">
            <span class="st-settings-block__icon" aria-hidden="true"><i class="fas fa-key"></i></span>
            <div>
                <h3 id="stProfPass">{{ __('student_timeline.change_password') }}</h3>
                <p>{{ __('student_timeline.change_password_hint') }}</p>
            </div>
        </div>

        <div class="st-profile-grid">
            <label class="st-field">
                <span>{{ __('student_timeline.current_password') }}</span>
                <input type="password" name="current_password" autocomplete="current-password">
                @error('current_password')<small class="st-field__err">{{ $message }}</small>@enderror
            </label>

            <label class="st-field">
                <span>{{ __('student_timeline.new_password') }}</span>
                <input type="password" name="password" autocomplete="new-password">
                @error('password')<small class="st-field__err">{{ $message }}</small>@enderror
            </label>

            <label class="st-field">
                <span>{{ __('student_timeline.confirm_password') }}</span>
                <input type="password" name="password_confirmation" autocomplete="new-password">
            </label>
        </div>
    </section>

    <div class="st-profile-foot">
        <a href="{{ route('settings') }}" class="st-pill st-pill--outline">{{ __('student_timeline.cancel') }}</a>
        <button type="submit" class="st-pill st-pill--solid">
            <i class="fas fa-save" aria-hidden="true"></i>
            {{ __('student_timeline.save_profile') }}
        </button>
    </div>
</form>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ route('settings') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_settings') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.settings_hint') }}</p>
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
    var input = document.getElementById('stProfFile');
    var preview = document.getElementById('stProfPreview');
    if (input && preview) {
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            var url = URL.createObjectURL(file);
            preview.src = url;
            preview.onload = function () { URL.revokeObjectURL(url); };
        });
    }

    var copyBtn = document.getElementById('stCopyClassUserId');
    if (copyBtn) {
        var original = copyBtn.innerHTML;
        copyBtn.addEventListener('click', function () {
            var value = copyBtn.getAttribute('data-copy') || '';
            function done() {
                copyBtn.innerHTML = '<i class="fas fa-check" aria-hidden="true"></i> ' + @json(__('student_timeline.copied'));
                setTimeout(function () { copyBtn.innerHTML = original; }, 1600);
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(value).then(done).catch(function () {
                    window.prompt(@json(__('student_timeline.copy_user_id')), value);
                });
            } else {
                window.prompt(@json(__('student_timeline.copy_user_id')), value);
            }
        });
    }
})();
</script>
@endpush
