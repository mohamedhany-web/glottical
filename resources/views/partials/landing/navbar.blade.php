@php
    $isRtl = app()->getLocale() === 'ar';
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? 'https://wa.me/201044610507';
    $navHero = $navHero ?? true;
    $navSolid = $navSolid ?? false;
    $active = $navActive ?? '';
@endphp
<header id="sana-nav" class="sana-nav {{ $navSolid ? 'is-solid' : ($navHero ? 'sana-nav--hero' : 'is-solid') }}">
  <div class="sana-container">
    <div class="sana-nav__inner">
      <a href="{{ route('home') }}" class="sana-nav__brand">
        <span class="sana-nav__logo-text">{{ config('app.name', 'Glottical') }}</span>
      </a>
      <nav class="sana-nav__links" aria-label="{{ $isRtl ? 'القائمة' : 'Main' }}">
        <a href="{{ route('home') }}" class="{{ $active === 'home' ? 'is-active' : '' }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
        <a href="{{ route('public.courses') }}" class="sana-nav__path sana-nav__path--family {{ $active === 'courses' ? 'is-active' : '' }}">{{ __('landing.nav.courses') }}</a>
        <a href="{{ route('public.groups') }}" class="{{ $active === 'groups' ? 'is-active' : '' }}">{{ __('landing.nav.groups') }}</a>
        <a href="{{ route('public.contact') }}" class="{{ $active === 'contact' ? 'is-active' : '' }}">{{ $isRtl ? 'تواصل معنا' : 'Contact' }}</a>
      </nav>
      <div class="sana-nav__actions">
        <a href="{{ route('login') }}" class="sana-nav__login">{{ __('landing.nav.login') }}</a>
        @if(request()->routeIs('home'))
          <button type="button" data-open-free-trial class="sana-nav__signup">{{ __('landing.academy.free_trial_cta') }}</button>
        @else
          <a href="{{ route('home') }}?open_trial=1" class="sana-nav__signup">{{ __('landing.academy.free_trial_cta') }}</a>
        @endif
      </div>
      <button type="button" id="sana-mobile-toggle" class="sana-nav__burger" aria-expanded="false" aria-controls="sana-mobile-menu" aria-label="{{ $isRtl ? 'فتح القائمة' : 'Open menu' }}">
        <i class="fas fa-bars" aria-hidden="true"></i>
      </button>
    </div>
    <div id="sana-mobile-menu" class="sana-nav__mobile" aria-hidden="true">
      <a href="{{ route('home') }}" class="{{ $active === 'home' ? 'is-active' : '' }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
      <a href="{{ route('public.courses') }}" class="sana-nav__path sana-nav__path--family {{ $active === 'courses' ? 'is-active' : '' }}">{{ __('landing.nav.courses') }}</a>
      <a href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a>
      <a href="{{ route('public.groups') }}" class="{{ $active === 'groups' ? 'is-active' : '' }}">{{ __('landing.nav.groups') }}</a>
      <a href="{{ route('public.contact') }}" class="{{ $active === 'contact' ? 'is-active' : '' }}">{{ $isRtl ? 'تواصل معنا' : 'Contact' }}</a>
      <a href="{{ route('login') }}">{{ __('landing.nav.login') }}</a>
      @if(request()->routeIs('home'))
        <button type="button" data-open-free-trial class="sana-nav__signup sana-nav__signup--block">{{ __('landing.academy.free_trial_cta') }}</button>
      @else
        <a href="{{ route('home') }}?open_trial=1" class="sana-nav__signup sana-nav__signup--block">{{ __('landing.academy.free_trial_cta') }}</a>
      @endif
      <a href="{{ $waUrl }}" class="sana-nav__signup sana-nav__signup--block sana-nav__signup--wa" target="_blank" rel="noopener">{{ $isRtl ? 'تواصل عبر واتساب' : 'WhatsApp' }}</a>
    </div>
  </div>
</header>
<div id="sana-mobile-backdrop" class="sana-nav__backdrop" aria-hidden="true"></div>
