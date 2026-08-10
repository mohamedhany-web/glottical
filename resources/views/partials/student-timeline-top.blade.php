@php
    $locale = $locale ?? app()->getLocale();
    $brandLogoUrl = $brandLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $brandLogoFallback = $brandLogoFallback ?? \App\Services\AdminPanelBranding::inlineFallbackDataUri();
    $pageTitle = $pageTitle ?? __('student_timeline.timeline');
    $crumbs = $crumbs ?? [];
    $toolbarView = $toolbarView ?? null;
    $toolbarData = $toolbarData ?? [];
@endphp
<header class="st-top">
    <div class="st-top__row st-top__row--primary">
        <a href="{{ route('dashboard') }}" class="st-top__brand" title="{{ config('app.name') }}">
            <span class="st-top__brand-mark">
                <img src="{{ $brandLogoUrl }}" alt="{{ config('app.name') }}" width="36" height="36" loading="eager" decoding="async" onerror="this.onerror=null;this.src='{{ $brandLogoFallback }}';">
            </span>
        </a>

        <div class="st-top__heading">
            @if(! empty($crumbs))
                <nav class="st-crumb" aria-label="breadcrumb">
                    @foreach($crumbs as $i => $crumb)
                        @if($i > 0)<span class="st-crumb__sep" aria-hidden="true">/</span>@endif
                        @if(! empty($crumb['url']) && ! $loop->last)
                            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                        @else
                            <span class="{{ $loop->last ? 'is-current' : '' }}">{{ $crumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>
            @endif
            <h1 class="st-top__title">{{ $pageTitle }}</h1>
        </div>

        <div class="st-top__actions">
            <div class="st-lang" role="group" aria-label="Language">
                <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}" class="{{ $locale === 'ar' ? 'is-active' : '' }}">{{ __('student_timeline.lang_ar') }}</a>
                <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="{{ $locale === 'en' ? 'is-active' : '' }}">{{ __('student_timeline.lang_en') }}</a>
            </div>

            <a href="{{ route('notifications') }}" class="st-bell" aria-label="{{ __('student_timeline.nav_messages') }}">
                <img src="{{ asset('img/student-timeline/bell.svg') }}" alt="" width="20" height="20">
                <span class="st-bell__dot" aria-hidden="true"></span>
            </a>

            <button type="button" class="st-top__menu" id="stTopMenu" aria-expanded="false" aria-controls="stRail" aria-label="{{ __('student_timeline.toggle_sidebar') }}">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    @if($toolbarView)
        @include($toolbarView, $toolbarData)
    @endif
</header>
