@php
    $isRtl = app()->getLocale() === 'ar';
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $authBg = $authBackgroundUrl ?? \App\Providers\AppServiceProvider::authBackgroundUrl();
    $authBgFallback = asset(\App\Providers\AppServiceProvider::AUTH_BACKGROUND_PUBLIC_RELATIVE);
    $authBgCss = str_replace(["\\", "'", '"'], ['/', '%27', '%22'], $authBg);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') — {{ config('app.name') }}</title>
  <meta name="theme-color" content="#0f5c57">
  <meta name="robots" content="noindex, nofollow">
  @include('partials.favicon-links')
  <link rel="preload" as="image" href="{{ $authBg }}" fetchpriority="high">
  @include('partials.atheer-head')
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    [x-cloak]{display:none!important}
    .auth-brand-photo{
      background-image:url('{{ $authBgCss }}');
      background-size:cover;
      background-position:center;
    }
  </style>
  @stack('head')
</head>
<body class="font-sans antialiased text-ink" @yield('body_attrs')>
  <div class="min-h-screen lg:grid lg:grid-cols-2">
    {{-- Brand / atmosphere panel --}}
    <aside class="auth-brand-panel relative hidden overflow-hidden text-white lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-14">
      <div class="auth-brand-photo pointer-events-none absolute inset-0 opacity-40" aria-hidden="true"></div>
      <img
        src="{{ $authBg }}"
        alt=""
        width="1200"
        height="1600"
        decoding="async"
        fetchpriority="high"
        class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-0"
        data-auth-bg
        onerror="this.onerror=null;this.src='{{ $authBgFallback }}';var p=this.previousElementSibling;if(p){p.style.backgroundImage='url(\'{{ $authBgFallback }}\')';}"
      >
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink via-ink/80 to-ink/45"></div>
      <div class="pointer-events-none absolute -{{ $isRtl ? 'left' : 'right' }}-24 top-1/3 size-72 rounded-full bg-accent/20 blur-3xl"></div>
      <div class="pointer-events-none absolute -{{ $isRtl ? 'right' : 'left' }}-16 bottom-0 size-64 rounded-full bg-metal/15 blur-3xl"></div>

      <div class="relative z-10 fade-up">
        @include('partials.auth-brand-link', ['size' => 'sm', 'variant' => 'dark', 'fallback' => 'accent', 'mb' => 'mb-0'])
      </div>

      <div class="relative z-10 max-w-md space-y-8 fade-up fade-up-delay-1">
        <div class="space-y-4">
          <p class="text-sm font-medium text-metal">@yield('brand_kicker', __('auth.visual_title'))</p>
          <h2 class="text-balance text-3xl font-semibold leading-tight xl:text-4xl">@yield('brand_title', __('auth.visual_desc'))</h2>
          <p class="text-sm leading-8 text-white/65">@yield('brand_lead')</p>
        </div>

        <ul class="space-y-4">
          @foreach ([
              ['title' => __('auth.effective_learning'), 'desc' => $isRtl ? 'مسارات وكورسات بمستوى احترافي' : 'Structured paths and professional courses'],
              ['title' => __('auth.collaboration'), 'desc' => $isRtl ? 'معلمون معتمدون ودعم مستمر' : 'Verified instructors and ongoing support'],
              ['title' => __('auth.continuous_growth'), 'desc' => $isRtl ? 'تتبّع تقدّمك وابدأ من حيث أنت' : 'Track progress and start where you are'],
          ] as $i => $point)
            <li class="flex gap-3 fade-up fade-up-delay-{{ min($i + 2, 3) }}">
              <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/10 text-metal ring-1 ring-white/10" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
              </span>
              <div>
                <p class="text-sm font-semibold text-white">{{ $point['title'] }}</p>
                <p class="mt-0.5 text-xs leading-6 text-white/55">{{ $point['desc'] }}</p>
              </div>
            </li>
          @endforeach
        </ul>
      </div>

      <p class="relative z-10 text-xs text-white/40 fade-up fade-up-delay-3">
        © {{ date('Y') }} {{ config('app.name') }}
      </p>
    </aside>

    {{-- Form column --}}
    <div class="relative flex min-h-screen flex-col">
      {{-- Mobile brand strip with photo --}}
      <div class="relative overflow-hidden bg-ink text-white lg:hidden">
        <div class="auth-brand-photo absolute inset-0 opacity-35" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-ink/70 to-ink"></div>
        <div class="relative z-10 flex items-center justify-between gap-3 px-5 py-4">
          @include('partials.auth-brand-link', ['size' => 'sm', 'variant' => 'dark', 'fallback' => 'accent', 'mb' => 'mb-0'])
          <div class="flex items-center gap-0.5 rounded-xl border border-white/15 bg-white/5 p-1 text-xs font-semibold backdrop-blur-sm">
            <a href="{{ $langSwitch('ar') }}" class="rounded-lg px-2.5 py-1 transition {{ $isRtl ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white' }}" hreflang="ar">عربي</a>
            <a href="{{ $langSwitch('en') }}" class="rounded-lg px-2.5 py-1 transition {{ ! $isRtl ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white' }}" hreflang="en">EN</a>
          </div>
        </div>
      </div>

      <header class="hidden items-center justify-between gap-3 px-5 py-4 sm:px-8 lg:flex lg:px-10">
        <div class="ms-auto flex items-center gap-2">
          <div class="flex items-center gap-0.5 rounded-xl border border-line bg-surface p-1 text-xs font-semibold shadow-soft">
            <a href="{{ $langSwitch('ar') }}" class="rounded-lg px-3 py-1.5 transition {{ $isRtl ? 'bg-accent-soft text-accent' : 'text-muted hover:text-ink' }}" hreflang="ar">عربي</a>
            <a href="{{ $langSwitch('en') }}" class="rounded-lg px-3 py-1.5 transition {{ ! $isRtl ? 'bg-accent-soft text-accent' : 'text-muted hover:text-ink' }}" hreflang="en">EN</a>
          </div>
          <a href="{{ route('home') }}" class="inline-flex h-9 items-center rounded-xl border border-line bg-surface px-3 text-xs font-medium text-muted shadow-soft transition hover:border-accent hover:text-accent">
            {{ __('auth.back_to_home') }}
          </a>
        </div>
      </header>

      <main class="page-enter flex flex-1 flex-col justify-center px-5 py-8 sm:px-8 lg:px-12 xl:px-16">
        <div class="mx-auto w-full @yield('form_max', 'max-w-[26rem]')">
          @yield('content')
        </div>
      </main>

      <footer class="px-5 pb-6 text-center text-xs text-muted sm:px-8 lg:hidden">
        <a href="{{ route('home') }}" class="font-medium text-accent transition hover:text-ink">{{ __('auth.back_to_home') }}</a>
      </footer>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
