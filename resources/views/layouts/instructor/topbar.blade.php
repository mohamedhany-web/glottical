@php
    $crumbSection = __('instructor.dashboards');
    $crumbPage = trim($__env->yieldContent('page_title') ?: $__env->yieldContent('title') ?: __('instructor.overview'));
    $switchLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $switchLabel = $switchLocale === 'ar' ? 'عربي' : 'EN';
    $notifUrl = Route::has('instructor.notifications.index')
        ? route('instructor.notifications.index')
        : (Route::has('notifications') ? route('notifications') : '#');
@endphp

<header class="ip-topbar">
    <div class="ip-topbar__start">
        <button type="button" class="su-icon-btn ip-menu-btn" @click="sidebarOpen = !sidebarOpen" aria-label="{{ __('instructor.menu') }}">
            <i class="fas fa-bars text-sm"></i>
        </button>
        <nav class="su-crumb" aria-label="breadcrumb">
            <span class="su-crumb__sec">{{ $crumbSection }}</span>
            <span class="su-crumb__sep" aria-hidden="true">/</span>
            <strong class="su-crumb__page truncate">{{ $crumbPage }}</strong>
        </nav>
    </div>

    <div class="ip-topbar__end">
        <label class="su-search">
            <i class="fas fa-search text-xs"></i>
            <input type="search" placeholder="{{ __('instructor.search_placeholder') }}" aria-label="{{ __('instructor.search_placeholder') }}">
            <kbd>/</kbd>
        </label>

        <a href="{{ url()->current() }}?lang={{ $switchLocale }}" class="su-lang" title="{{ __('instructor.switch_language') }}">
            <span>{{ $switchLabel }}</span>
        </a>

        <div x-data="themeManager()" x-init="init()">
            <button type="button" class="su-icon-btn" @click="toggle()" :title="dark ? '{{ __('instructor.light_mode') }}' : '{{ __('instructor.dark_mode') }}'">
                <i class="text-sm" :class="dark ? 'fas fa-sun' : 'fas fa-moon'"></i>
            </button>
        </div>

        <a href="{{ $notifUrl }}" class="su-icon-btn" title="{{ __('instructor.notifications') }}">
            <i class="fas fa-bell text-sm"></i>
        </a>

        <button type="button" class="su-icon-btn xl:hidden" @click="railOpen = !railOpen" title="{{ __('instructor.activity_rail') }}">
            <i class="fas fa-table-columns text-sm"></i>
        </button>
    </div>
</header>
