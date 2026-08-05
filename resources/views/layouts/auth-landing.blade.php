@php
    $isRtl = app()->getLocale() === 'ar';
    $brand = config('app.name', 'Glottical');
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') — {{ $brand }}</title>
  <meta name="robots" content="noindex, nofollow">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme']])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    body.gl-auth {
      margin: 0;
      min-height: 100dvh;
      background: #F4F7FC;
      color: #0B1220;
      font-family: Tajawal, Cairo, sans-serif;
      overflow-x: clip;
    }
    body.gl-auth.sana-home { direction: inherit; text-align: initial; }
    .gl-auth-shell { position: relative; min-height: 100dvh; }
    .gl-auth-bg {
      position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden;
      background:
        radial-gradient(ellipse 70% 50% at 92% -8%, rgba(11,61,145,.16), transparent 55%),
        radial-gradient(ellipse 50% 40% at -8% 100%, rgba(245,184,0,.14), transparent 50%),
        #F4F7FC;
    }
    .gl-auth-bg__dot {
      position: absolute; inset: 0; opacity: .4;
      background-image: radial-gradient(rgba(11,61,145,.14) 1px, transparent 1px);
      background-size: 22px 22px;
    }
    .gl-auth-layer { position: relative; z-index: 1; min-height: 100dvh; display: flex; flex-direction: column; }
    .gl-auth-nav {
      display: flex; align-items: center; justify-content: space-between; gap: .75rem;
      padding: max(.9rem, env(safe-area-inset-top)) clamp(1rem, 4vw, 2rem) .75rem;
    }
    .gl-auth-logo {
      font-family: Cairo, Tajawal, sans-serif; font-weight: 900; font-size: 1.15rem;
      color: #0B3D91; text-decoration: none !important;
    }
    .gl-auth-nav__actions { display: flex; align-items: center; gap: .55rem; flex-wrap: wrap; justify-content: flex-end; }
    .gl-auth-lang {
      display: inline-flex; gap: 2px; padding: 3px;
      border: 1.5px solid #D7DDE6; border-radius: 12px; background: #fff;
    }
    .gl-auth-lang a {
      min-height: 32px; padding: 0 .7rem; border-radius: 9px;
      display: inline-flex; align-items: center; text-decoration: none !important;
      font: 800 .72rem Tajawal, sans-serif; color: #5B6577;
    }
    .gl-auth-lang a.is-on { background: #0B3D91; color: #fff; }
    .gl-auth-nav-link {
      font: 800 .8rem Tajawal, sans-serif; color: #0B3D91; text-decoration: none !important;
      padding: .45rem .7rem; border-radius: 10px;
    }
    .gl-auth-nav-link:hover { background: #E8EEF8; }
    .gl-auth-main {
      flex: 1; display: flex; align-items: center; justify-content: center;
      padding: 1rem clamp(1rem, 4vw, 1.5rem) 2rem;
    }
    .gl-auth-main--top { align-items: flex-start; padding-top: .5rem; }
    .gl-auth-card {
      width: 100%; max-width: 420px; background: #fff;
      border: 1.5px solid #D7DDE6; border-radius: 20px;
      padding: 1.35rem 1.25rem 1.4rem;
      box-shadow: 0 18px 48px -24px rgba(11,61,145,.35);
    }
    .gl-auth-card--wide { max-width: 520px; }
    .gl-auth-brand {
      text-align: center; margin-bottom: .85rem;
      font-family: Cairo, Tajawal, sans-serif; font-weight: 900; font-size: 1.25rem; color: #0B3D91;
    }
    .gl-auth-title {
      margin: 0 0 .35rem; text-align: center;
      font-family: Cairo, Tajawal, sans-serif; font-weight: 900;
      font-size: clamp(1.35rem, 3.5vw, 1.7rem); line-height: 1.35; color: #0B1220;
    }
    .gl-auth-title em { color: #0B3D91; font-style: normal; }
    .gl-auth-lead {
      margin: 0 0 1.15rem; text-align: center;
      font: 600 .86rem/1.65 Tajawal, sans-serif; color: #5B6577;
    }
    .gl-auth-alert {
      margin: 0 0 .85rem; padding: .7rem .85rem; border-radius: 12px;
      font: 700 .8rem/1.5 Tajawal, sans-serif;
    }
    .gl-auth-alert--ok { background: #ECFDF5; color: #047857; }
    .gl-auth-alert--warn { background: #FFF8E6; color: #9A7200; }
    .gl-auth-alert--err { background: #FEF2F2; color: #B91C1C; }
    .gl-auth-alert--info { background: #E8EEF8; color: #0B3D91; }
    .gl-auth-field { margin-bottom: .9rem; }
    .gl-auth-field label {
      display: block; margin-bottom: .35rem;
      font: 800 .78rem Tajawal, sans-serif; color: #0B1220;
    }
    .gl-auth-input-wrap { position: relative; }
    .gl-auth-input {
      width: 100%; height: 3rem; box-sizing: border-box;
      border: 1.5px solid #D7DDE6; border-radius: 12px;
      padding: 0 2.75rem 0 1rem; background: #F4F7FC;
      font: 600 .92rem Tajawal, sans-serif; color: #0B1220;
      outline: none; transition: border-color .15s, box-shadow .15s, background .15s;
    }
    html[dir="rtl"] .gl-auth-input { padding: 0 1rem 0 2.75rem; }
    .gl-auth-input:focus {
      border-color: #0B3D91; background: #fff;
      box-shadow: 0 0 0 3px rgba(11,61,145,.12);
    }
    .gl-auth-input.has-error { border-color: #DC2626; }
    .gl-auth-input::placeholder { color: #94A3B8; font-weight: 500; }
    .gl-auth-icon {
      position: absolute; top: 50%; transform: translateY(-50%);
      inset-inline-start: .85rem; color: #94A3B8; pointer-events: none;
      display: inline-flex; width: 1.1rem; justify-content: center;
    }
    .gl-auth-input.has-icon { padding-inline-start: 2.55rem; }
    .gl-auth-pw-btn {
      position: absolute; top: 50%; transform: translateY(-50%);
      inset-inline-end: .35rem; border: 0; background: transparent;
      min-width: 2.4rem; height: 2.4rem; border-radius: 8px; cursor: pointer;
      font: 800 .68rem Tajawal, sans-serif; color: #5B6577;
    }
    .gl-auth-pw-btn:hover { background: #E8EEF8; color: #0B3D91; }
    .gl-auth-error { margin: .3rem 0 0; font: 700 .72rem Tajawal, sans-serif; color: #DC2626; }
    .gl-auth-phone {
      display: grid; grid-template-columns: minmax(8.5rem, 11.5rem) minmax(0, 1fr); gap: .5rem;
      align-items: start;
    }
    .gl-auth-phone > input[type="tel"] {
      width: 100%; height: 3rem; box-sizing: border-box;
      border: 1.5px solid #D7DDE6; border-radius: 12px; background: #F4F7FC;
      padding: 0 .85rem; font: 600 .88rem Tajawal, sans-serif; color: #0B1220; outline: none;
    }
    .gl-auth-phone > input[type="tel"]:focus {
      border-color: #0B3D91; background: #fff;
      box-shadow: 0 0 0 3px rgba(11,61,145,.12);
    }
    .gl-auth-phone.has-error > input[type="tel"],
    .gl-auth-phone.has-error .gl-auth-cc-btn { border-color: #DC2626; }
    .gl-auth-cc { position: relative; min-width: 0; }
    .gl-auth-cc-btn {
      width: 100%; height: 3rem; box-sizing: border-box;
      display: inline-flex; align-items: center; gap: .35rem;
      border: 1.5px solid #D7DDE6; border-radius: 12px; background: #F4F7FC;
      padding: 0 .55rem 0 .7rem; cursor: pointer;
      font: 600 .82rem Tajawal, sans-serif; color: #0B1220; text-align: start;
    }
    .gl-auth-cc-btn:hover,
    .gl-auth-cc-btn:focus-visible {
      border-color: #0B3D91; background: #fff;
      box-shadow: 0 0 0 3px rgba(11,61,145,.12); outline: none;
    }
    .gl-auth-cc-dial { flex: 0 0 auto; font-weight: 800; color: #0B3D91; white-space: nowrap; }
    .gl-auth-cc-name {
      flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
      color: #5B6577; font-size: .74rem;
    }
    .gl-auth-cc-btn > i { margin-inline-start: auto; color: #94A3B8; font-size: .65rem; }
    .gl-auth-cc-panel {
      position: absolute; z-index: 40; top: calc(100% + .35rem); inset-inline: 0;
      width: max(100%, 16.5rem); max-width: min(22rem, 92vw);
      border: 1.5px solid #D7DDE6; border-radius: 14px; background: #fff;
      box-shadow: 0 14px 36px rgba(11, 34, 64, .14);
      overflow: hidden;
    }
    .gl-auth-cc-search {
      display: flex; align-items: center; gap: .45rem;
      padding: .55rem .7rem; border-bottom: 1px solid #E8EEF5; background: #F8FAFD;
    }
    .gl-auth-cc-search i { color: #94A3B8; font-size: .8rem; }
    .gl-auth-cc-search input {
      flex: 1; min-width: 0; border: 0; background: transparent; outline: none;
      font: 600 .82rem Tajawal, sans-serif; color: #0B1220; height: 1.8rem;
    }
    .gl-auth-cc-list {
      list-style: none; margin: 0; padding: .25rem; max-height: 14rem; overflow: auto;
    }
    .gl-auth-cc-option {
      width: 100%; display: flex; align-items: center; gap: .55rem;
      border: 0; background: transparent; border-radius: 10px;
      padding: .55rem .6rem; cursor: pointer; text-align: start;
      font: 600 .82rem Tajawal, sans-serif; color: #0B1220;
    }
    .gl-auth-cc-option:hover,
    .gl-auth-cc-option.is-active { background: #EAF0FA; }
    .gl-auth-cc-option-dial { flex: 0 0 3.4rem; font-weight: 800; color: #0B3D91; }
    .gl-auth-cc-option-name { flex: 1; min-width: 0; color: #334155; }
    .gl-auth-cc-empty {
      padding: .85rem .7rem; text-align: center;
      font: 600 .8rem Tajawal, sans-serif; color: #94A3B8;
    }
    @media (max-width: 520px) {
      .gl-auth-phone { grid-template-columns: 1fr; }
      .gl-auth-cc-panel { width: 100%; max-width: none; }
    }
    .gl-auth-row {
      display: flex; align-items: center; justify-content: space-between; gap: .75rem;
      flex-wrap: wrap; margin: .15rem 0 1rem;
    }
    .gl-auth-check {
      display: inline-flex; align-items: center; gap: .45rem; cursor: pointer;
      font: 600 .8rem Tajawal, sans-serif; color: #5B6577;
    }
    .gl-auth-check input { accent-color: #0B3D91; width: 1rem; height: 1rem; }
    .gl-auth-link {
      font: 800 .78rem Tajawal, sans-serif; color: #0B3D91; text-decoration: none !important;
    }
    .gl-auth-link:hover { text-decoration: underline !important; }
    .gl-auth-terms {
      display: flex; align-items: flex-start; gap: .55rem; margin: .35rem 0 1rem;
      font: 600 .8rem/1.55 Tajawal, sans-serif; color: #5B6577; cursor: pointer;
    }
    .gl-auth-terms input { margin-top: .2rem; accent-color: #0B3D91; flex-shrink: 0; }
    .gl-auth-submit {
      width: 100%; min-height: 3.1rem; border: 0; border-radius: 999px; cursor: pointer;
      display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
      background: linear-gradient(135deg, #F5B800, #E5A800); color: #072A66;
      font: 900 .95rem Cairo, Tajawal, sans-serif;
      box-shadow: 0 10px 28px rgba(245,184,0,.4);
    }
    .gl-auth-submit:hover { filter: brightness(1.03); }
    .gl-auth-foot {
      margin-top: 1.15rem; padding-top: 1rem; border-top: 1px solid #E8EEF8;
      text-align: center; font: 600 .84rem Tajawal, sans-serif; color: #5B6577;
    }
    .gl-auth-trust {
      display: flex; flex-wrap: wrap; justify-content: center; gap: .45rem .85rem;
      margin-top: 1rem; font: 700 .7rem Tajawal, sans-serif; color: #5B6577;
    }
    .gl-auth-trust span { display: inline-flex; align-items: center; gap: 5px; }
    .gl-auth-trust i { color: #0B3D91; }
    .gl-auth-sr-only {
      position: absolute !important; width: 1px; height: 1px; padding: 0; margin: -1px;
      overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;
    }
    [x-cloak] { display: none !important; }
  </style>
  @stack('head')
</head>
<body class="gl-auth sana-home" @yield('body_attrs')>
  <div class="gl-auth-shell">
    <div class="gl-auth-bg" aria-hidden="true"><span class="gl-auth-bg__dot"></span></div>
    <div class="gl-auth-layer">
      <nav class="gl-auth-nav">
        <a href="{{ route('home') }}" class="gl-auth-logo">{{ $brand }}</a>
        <div class="gl-auth-nav__actions">
          <div class="gl-auth-lang" role="group" aria-label="{{ $isRtl ? 'اللغة' : 'Language' }}">
            <a href="{{ $langSwitch('ar') }}" class="{{ $isRtl ? 'is-on' : '' }}" hreflang="ar">عربي</a>
            <a href="{{ $langSwitch('en') }}" class="{{ ! $isRtl ? 'is-on' : '' }}" hreflang="en">EN</a>
          </div>
          @hasSection('nav_action')
            @yield('nav_action')
          @else
            <a href="{{ route('home') }}" class="gl-auth-nav-link">{{ __('auth.back_to_home') }}</a>
          @endif
        </div>
      </nav>
      <main class="gl-auth-main @yield('main_class')">
        @yield('content')
      </main>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
