@extends('layouts.app')

@section('title', __('instructor.profile') . ' - ' . config('app.name'))
@section('page_title', __('instructor.profile'))

@section('content')
@php
    $user = auth()->user();
    $memberSince = $user->created_at ? $user->created_at->copy()->locale(app()->getLocale())->translatedFormat('d F Y') : '—';
    $myCoursesCount = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->count();
    $totalStudents = \App\Models\StudentCourseEnrollment::whereHas('course', function($q) use ($user) {
        $q->where('instructor_id', $user->id);
    })->where('status', 'active')->distinct('user_id')->count();
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale(app()->getLocale())->diffForHumans() : '—';
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-user-circle su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.profile') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.manage_profile_data') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
        </div>
    @endif

    <section class="su-card" style="margin-bottom:20px">
        <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:center">
            <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;flex:1;min-width:0">
                <div style="width:96px;height:96px;border-radius:16px;overflow:hidden;border:1px solid var(--su-line,rgba(0,0,0,.08));background:var(--su-soft-1,rgba(59,130,246,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image_url }}" alt="{{ __('instructor.profile_image') }}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                        <span class="hidden" style="font-size:32px;font-weight:700;color:var(--su-accent,#3b82f6)">{{ mb_substr($user->name, 0, 1) }}</span>
                    @else
                        <span style="font-size:32px;font-weight:700;color:var(--su-accent,#3b82f6)">{{ mb_substr($user->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="su-chip su-soft-1"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> {{ __('instructor.instructor_role') }}</span>
                    <h2 style="margin:8px 0 4px;font-size:20px;font-weight:700">{{ $user->name }}</h2>
                    @if($user->phone)
                        <p style="margin:0;font-size:13px;color:var(--su-ink-40)"><i class="fas fa-phone" aria-hidden="true"></i> {{ $user->phone }}</p>
                    @endif
                    @if($user->email)
                        <p style="margin:2px 0 0;font-size:13px;color:var(--su-ink-40)"><i class="fas fa-envelope" aria-hidden="true"></i> {{ $user->email }}</p>
                    @endif
                </div>
            </div>
            <div class="su-kpi-row su-kpi-row--4" style="flex:1;min-width:220px">
                <div class="su-kpi su-kpi--1">
                    <div class="su-kpi__l">{{ __('instructor.join_date') }}</div>
                    <div class="su-kpi__v" style="font-size:14px">{{ $memberSince }}</div>
                </div>
                <div class="su-kpi su-kpi--2">
                    <div class="su-kpi__l">{{ __('instructor.my_courses') }}</div>
                    <div class="su-kpi__v">{{ $myCoursesCount }}</div>
                </div>
                <div class="su-kpi su-kpi--3">
                    <div class="su-kpi__l">{{ __('instructor.students') }}</div>
                    <div class="su-kpi__v">{{ $totalStudents }}</div>
                </div>
                <div class="su-kpi su-kpi--4">
                    <div class="su-kpi__l">{{ __('instructor.last_login') }}</div>
                    <div class="su-kpi__v" style="font-size:14px">{{ $lastLogin }}</div>
                </div>
            </div>
        </div>
    </section>

    <div class="su-detail-grid">
        <div style="display:flex;flex-direction:column;gap:16px">
            <section class="su-card">
                <h2 class="su-card__title"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('instructor.account_info') }}</h2>
                <div class="su-meta-list">
                    <div class="su-meta-row">
                        <span>{{ __('instructor.membership_number') }}</span>
                        <strong>#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                    <div class="su-meta-row">
                        <span>{{ __('instructor.account_type') }}</span>
                        <span class="su-chip su-soft-1">{{ __('instructor.instructor_role') }}</span>
                    </div>
                    <div class="su-meta-row">
                        <span>{{ __('common.status') }}</span>
                        <span class="su-chip {{ $user->is_active ? 'su-chip--ok' : 'su-chip--off' }}">
                            {{ $user->is_active ? __('instructor.active_status') : __('instructor.not_active') }}
                        </span>
                    </div>
                </div>
            </section>

            @if($user->isAcademyWorkingInstructor())
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-folder-open" aria-hidden="true"></i> {{ __('instructor.your_libraries') }}</h2>
                    <p style="margin:0 0 12px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.your_libraries_desc') }}</p>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <a href="{{ route('instructor.libraries.materials.index') }}" class="su-btn" style="justify-content:space-between">
                            <span><i class="fas fa-file-upload" aria-hidden="true"></i> {{ __('instructor.upload_materials') }}</span>
                            <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('instructor.libraries.curriculum.index') }}" class="su-btn" style="justify-content:space-between">
                            <span><i class="fas fa-book-open" aria-hidden="true"></i> {{ __('instructor.view_academy_curriculum') }}</span>
                            <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('instructor.courses.index') }}" class="su-btn" style="justify-content:space-between">
                            <span><i class="fas fa-layer-group" aria-hidden="true"></i> {{ __('instructor.build_course_curriculum') }}</span>
                            <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('instructor.libraries.videos.index') }}" class="su-btn" style="justify-content:space-between">
                            <span><i class="fas fa-video" aria-hidden="true"></i> {{ __('instructor.video_library') }}</span>
                            <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                    </div>
                </section>
            @endif

            <section class="su-card">
                <h2 class="su-card__title"><i class="fas fa-lightbulb" aria-hidden="true"></i> {{ __('instructor.tips_for_instructor') }}</h2>
                <div class="su-meta-list">
                    <div class="su-meta-row" style="align-items:flex-start">
                        <span class="su-meta-ico su-soft-1"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('instructor.update_bio') }}</strong>
                            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.add_bio_for_students') }}</div>
                        </div>
                    </div>
                    <div class="su-meta-row" style="align-items:flex-start">
                        <span class="su-meta-ico su-soft-2"><i class="fas fa-lock" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('instructor.strong_password') }}</strong>
                            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.change_password_regularly') }}</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div>
            <section class="su-card">
                <h2 class="su-card__title">{{ __('instructor.update_data') }}</h2>
                <p style="margin:0 0 16px;font-size:13px;color:var(--su-ink-40)">{{ __('instructor.update_data_subtitle') }}</p>

                <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                        <div class="su-field">
                            <label>{{ __('instructor.full_name') }}</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="su-input">
                            @error('name')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label>{{ __('instructor.phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="su-input">
                            @error('phone')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.email_optional') }}</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="su-input">
                            @error('email')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.timezone') }}</label>
                            @php
                                $tzOptions = \App\Support\AppTimezone::commonZones();
                                $tzCurrent = old('timezone', $user->timezone ?: \App\Support\AppTimezone::academy());
                                if ($tzCurrent && ! array_key_exists($tzCurrent, $tzOptions)) {
                                    $tzOptions = [$tzCurrent => $tzCurrent] + $tzOptions;
                                }
                            @endphp
                            <select name="timezone" data-timezone-select required class="su-select">
                                @foreach ($tzOptions as $tzId => $tzLabel)
                                    <option value="{{ $tzId }}" @selected($tzCurrent === $tzId)>{{ $tzLabel }}</option>
                                @endforeach
                            </select>
                            <span style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.timezone_hint') }}</span>
                            @error('timezone')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.bio_optional') }}</label>
                            <textarea name="bio" rows="4" class="su-input" style="min-height:100px;resize:vertical"
                                      placeholder="{{ __('instructor.bio_placeholder_short') }}">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.profile_image') }}</label>
                            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
                                <div style="width:88px;height:88px;border-radius:12px;overflow:hidden;border:1px solid var(--su-line,rgba(0,0,0,.08));background:var(--su-soft-1,rgba(0,0,0,.04));display:flex;align-items:center;justify-content:center">
                                    @if($user->profile_image)
                                        <img src="{{ $user->profile_image_url }}" alt="{{ __('instructor.profile_image') }}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                        <i class="fas fa-user hidden" style="color:var(--su-ink-40)" aria-hidden="true"></i>
                                    @else
                                        <i class="fas fa-user" style="color:var(--su-ink-40)" aria-hidden="true"></i>
                                    @endif
                                </div>
                                <label class="su-btn" style="cursor:pointer">
                                    <i class="fas fa-upload" aria-hidden="true"></i>
                                    {{ __('instructor.choose_image_label') }}
                                    <input type="file" name="profile_image" accept="image/*" class="hidden" style="display:none">
                                </label>
                            </div>
                            @error('profile_image')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="su-card" style="margin:20px 0;background:rgba(0,0,0,.02)">
                        <h3 class="su-card__title">{{ __('instructor.change_password') }}</h3>
                        <p style="margin:0 0 12px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.leave_empty_if_no_change') }}</p>
                        <div class="su-form-grid" style="grid-template-columns:1fr 1fr 1fr">
                            <div class="su-field">
                                <label>{{ __('instructor.current_password') }}</label>
                                <input type="password" name="current_password" class="su-input">
                                @error('current_password')<p class="su-field-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="su-field">
                                <label>{{ __('instructor.new_password') }}</label>
                                <input type="password" name="password" class="su-input">
                                @error('password')<p class="su-field-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="su-field">
                                <label>{{ __('instructor.confirm_password') }}</label>
                                <input type="password" name="password_confirmation" class="su-input">
                            </div>
                        </div>
                    </div>

                    <div class="su-form-actions" style="justify-content:space-between;gap:8px;padding-top:16px;border-top:1px solid var(--su-line,rgba(0,0,0,.06))">
                        <a href="{{ route('dashboard') }}" class="su-btn">
                            <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                            {{ __('instructor.back_to_dashboard') }}
                        </a>
                        <button type="submit" class="su-btn su-btn--primary">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            {{ __('instructor.save_changes') }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
