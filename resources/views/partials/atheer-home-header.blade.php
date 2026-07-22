{{-- هيدر مطابق لـ site/storefront مع روابط الأكاديمية --}}
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="bg-ink text-white announce-bar">
  <div class="container-wide flex h-10 items-center justify-center gap-2 sm:gap-3 text-center text-xs sm:text-sm">
    <span class="text-metal shrink-0">✦</span>
    <p class="min-w-0">{{ $isRtl ? 'تقييم مستوى مجاني · حصص مباشرة · شهادة معتمدة' : 'Free level assessment · Live sessions · Certified learning' }}</p>
    <button type="button" data-open-free-trial class="hidden underline underline-offset-4 opacity-90 transition hover:opacity-100 sm:inline shrink-0">{{ $isRtl ? 'احجز الآن' : 'Book now' }}</button>
  </div>
</div>

<header class="sticky top-0 z-50 border-b border-line/80 bg-surface/90 backdrop-blur-xl">
  <div class="container-wide flex h-16 items-center gap-3 md:h-20 md:gap-4">
    <button type="button" id="nav-toggle" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas lg:hidden" aria-label="{{ $isRtl ? 'القائمة' : 'Menu' }}" aria-expanded="false" aria-controls="mobile-nav">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16"/></svg>
    </button>

    <a href="{{ url('/') }}" class="shrink-0 text-2xl font-bold tracking-tight text-ink md:text-3xl">Glottical</a>

    <nav class="{{ $isRtl ? 'mr-2' : 'ml-2' }} hidden items-center gap-0.5 lg:flex" aria-label="{{ $isRtl ? 'التنقل الرئيسي' : 'Main' }}">
      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="{{ route('public.learning-paths.index') }}">{{ __('landing.nav.learning_paths') }}</a>
      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="{{ route('public.courses') }}">{{ __('landing.nav.courses') }}</a>
      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="{{ route('public.groups') }}">{{ __('landing.nav.groups') }}</a>
      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="{{ route('public.categories') }}">{{ __('landing.nav.categories') }}</a>
      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a>
    </nav>

    <div class="mx-auto hidden min-w-0 max-w-xl flex-1 md:block">
      <form action="{{ route('public.courses') }}" method="get" class="relative">
        <svg class="pointer-events-none absolute top-1/2 {{ $isRtl ? 'right-3' : 'left-3' }} size-4 -translate-y-1/2 text-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input name="q" class="h-12 w-full rounded-xl border border-line bg-surface {{ $isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4' }} text-sm transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="{{ __('landing.academy.search_placeholder') }}" aria-label="{{ $isRtl ? 'بحث' : 'Search' }}" />
      </form>
    </div>

    <div class="ms-auto flex items-center gap-0.5 sm:gap-1">
      <button type="button" data-open-free-trial class="btn-press hidden h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white transition hover:bg-[#0d4f4a] lg:inline-flex">
        {{ __('landing.academy.free_trial_cta') }}
      </button>

      @auth
        <a href="{{ url('/dashboard') }}" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas" aria-label="{{ $isRtl ? 'حسابي' : 'Account' }}">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </a>
      @else
        <a href="{{ route('login') }}" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas" aria-label="{{ $isRtl ? 'تسجيل الدخول' : 'Login' }}">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </a>
        <a href="{{ route('register') }}" class="hidden sm:inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ $isRtl ? 'إنشاء حساب' : 'Sign up' }}</a>
      @endauth

      <div class="hidden sm:flex items-center rounded-xl border border-line overflow-hidden text-xs font-semibold ms-1">
        <a href="{{ url('/?lang=ar') }}" class="px-2.5 py-2 {{ app()->getLocale()==='ar' ? 'bg-accent text-white' : 'hover:bg-canvas' }}">ع</a>
        <a href="{{ url('/?lang=en') }}" class="px-2.5 py-2 {{ app()->getLocale()==='en' ? 'bg-accent text-white' : 'hover:bg-canvas' }}">EN</a>
      </div>
    </div>
  </div>

  <div id="mobile-nav" class="hidden border-t border-line bg-surface px-4 py-4 lg:hidden">
    <div class="mb-4">
      <form action="{{ route('public.courses') }}" method="get" class="relative">
        <svg class="pointer-events-none absolute top-1/2 {{ $isRtl ? 'right-3' : 'left-3' }} size-4 -translate-y-1/2 text-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input name="q" class="h-11 w-full rounded-xl border border-line bg-canvas {{ $isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4' }} text-sm" placeholder="{{ $isRtl ? 'ابحث…' : 'Search…' }}" aria-label="{{ $isRtl ? 'بحث' : 'Search' }}" />
      </form>
    </div>
    <div class="flex flex-col gap-0.5 text-sm font-medium">
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ route('public.learning-paths.index') }}">{{ __('landing.nav.learning_paths') }}</a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ route('public.courses') }}">{{ __('landing.nav.courses') }}</a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ route('public.groups') }}">{{ __('landing.nav.groups') }}</a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ route('public.categories') }}">{{ __('landing.nav.categories') }}</a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a>
      <button type="button" data-open-free-trial class="rounded-xl px-3 py-3 text-start text-accent font-semibold transition hover:bg-canvas">{{ __('landing.academy.free_trial_cta') }}</button>
      @guest
        <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ route('login') }}">{{ __('landing.nav.login') ?? ($isRtl ? 'تسجيل الدخول' : 'Login') }}</a>
        <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ route('register') }}">{{ __('landing.nav.register') ?? ($isRtl ? 'إنشاء حساب' : 'Register') }}</a>
      @else
        <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="{{ url('/dashboard') }}">{{ $isRtl ? 'لوحتي' : 'Dashboard' }}</a>
      @endguest
      <a class="rounded-xl px-3 py-3 text-muted transition hover:bg-canvas hover:text-ink" href="{{ route('public.contact') }}">{{ $isRtl ? 'تواصل معنا' : 'Contact' }}</a>
    </div>
  </div>
</header>
