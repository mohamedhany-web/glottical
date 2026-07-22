@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $g = 'landing.groups_page';
    $groupCountLabel = $groupCount === 1
        ? __($g.'.courses_count_one')
        : __($g.'.courses_count', ['count' => $groupCount]);
    $soloCountLabel = $oneToOneCount === 1
        ? __($g.'.courses_count_one')
        : __($g.'.courses_count', ['count' => $oneToOneCount]);
    $groupImg = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1400&q=80';
    $soloImg = 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1400&q=80';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __($g.'.meta_title') }} — Glottical</title>
  <meta name="description" content="{{ __($g.'.meta_desc') }}">
  <link rel="canonical" href="{{ route('public.groups') }}">
  @include('partials.favicon-links')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="{{ versioned_asset('js/atheer-tailwind-config.js') }}"></script>
  <link rel="stylesheet" href="{{ versioned_asset('css/atheer.css') }}">
  <meta name="theme-color" content="#0f5c57">
</head>
<body class="font-sans antialiased">
@include('partials.atheer-home-header')

<main class="page-enter">
  {{-- Intro — collections pattern --}}
  <section class="container-wide py-10 md:py-14">
    <nav class="mb-6" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
      <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
        <li><a href="{{ url('/') }}" class="transition hover:text-ink">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a></li>
        <li aria-hidden="true" class="text-line">/</li>
        <li class="font-medium text-ink" aria-current="page">{{ __($g.'.title') }}</li>
      </ol>
    </nav>
    <div class="max-w-2xl space-y-3">
      <p class="text-sm font-medium text-accent">{{ __($g.'.kicker') }}</p>
      <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-4xl">{{ __($g.'.title') }}</h1>
      <p class="text-base leading-8 text-muted">{{ __($g.'.intro') }}</p>
    </div>
  </section>

  {{-- Two delivery modes as hero tiles --}}
  <section class="container-wide pb-16 md:pb-20">
    <div class="grid gap-5 lg:grid-cols-2">
      <article class="group relative min-h-96 overflow-hidden rounded-2xl shadow-soft">
        <img src="{{ $groupImg }}" alt="{{ __($g.'.group_tile_title') }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="eager" width="1400" height="900">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 space-y-3 p-6 text-white sm:p-8">
          @if($groupCount > 0)
            <p class="text-xs font-medium tracking-wide text-metal">{{ $groupCountLabel }}</p>
          @endif
          <h2 class="text-2xl font-semibold md:text-3xl">{{ __($g.'.group_tile_title') }}</h2>
          <p class="max-w-md text-sm leading-7 text-white/75">{{ __($g.'.group_tile_sub') }}</p>
          <a href="{{ route('public.courses', ['delivery' => 'group']) }}" class="btn-press inline-flex h-10 items-center rounded-xl bg-white px-5 text-sm font-medium text-ink transition hover:bg-canvas">
            {{ __($g.'.group_tile_cta') }}
          </a>
        </div>
      </article>

      <article class="group relative min-h-96 overflow-hidden rounded-2xl shadow-soft">
        <img src="{{ $soloImg }}" alt="{{ __($g.'.solo_tile_title') }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="eager" width="1400" height="900">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/45 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 space-y-3 p-6 text-white sm:p-8">
          @if($oneToOneCount > 0)
            <p class="text-xs font-medium tracking-wide text-metal">{{ $soloCountLabel }}</p>
          @endif
          <h2 class="text-2xl font-semibold md:text-3xl">{{ __($g.'.solo_tile_title') }}</h2>
          <p class="max-w-md text-sm leading-7 text-white/75">{{ __($g.'.solo_tile_sub') }}</p>
          <a href="{{ route('public.courses', ['delivery' => 'one_to_one']) }}" class="btn-press inline-flex h-10 items-center rounded-xl bg-white px-5 text-sm font-medium text-ink transition hover:bg-canvas">
            {{ __($g.'.solo_tile_cta') }}
          </a>
        </div>
      </article>
    </div>
  </section>

  {{-- Compare --}}
  <section class="bg-surface py-16 md:py-20">
    <div class="container-wide">
      <div class="mb-10 max-w-2xl space-y-3 md:mb-12">
        <p class="text-sm font-medium text-accent">{{ __($g.'.compare_kicker') }}</p>
        <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __($g.'.compare_title') }}</h2>
        <p class="text-base leading-8 text-muted">{{ __($g.'.compare_sub') }}</p>
      </div>
      <div class="grid gap-10 md:grid-cols-2 md:gap-16">
        <div class="space-y-5">
          <div class="flex items-center gap-3">
            <span class="inline-flex size-10 items-center justify-center rounded-xl bg-accent-soft text-accent" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <h3 class="text-lg font-semibold text-ink">{{ __($g.'.group_label') }}</h3>
          </div>
          <ul class="space-y-3 text-sm leading-7 text-muted">
            @foreach(__($g.'.group_points') as $point)
              <li class="flex gap-3">
                <span class="mt-2 size-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true"></span>
                <span>{{ $point }}</span>
              </li>
            @endforeach
          </ul>
        </div>
        <div class="space-y-5">
          <div class="flex items-center gap-3">
            <span class="inline-flex size-10 items-center justify-center rounded-xl bg-accent-soft text-accent" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <h3 class="text-lg font-semibold text-ink">{{ __($g.'.solo_label') }}</h3>
          </div>
          <ul class="space-y-3 text-sm leading-7 text-muted">
            @foreach(__($g.'.solo_points') as $point)
              <li class="flex gap-3">
                <span class="mt-2 size-1.5 shrink-0 rounded-full bg-metal" aria-hidden="true"></span>
                <span>{{ $point }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </section>

  {{-- How to join --}}
  <section class="container-wide py-16 md:py-20">
    <div class="mb-10 max-w-2xl space-y-3 md:mb-12">
      <p class="text-sm font-medium text-accent">{{ __($g.'.join_kicker') }}</p>
      <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __($g.'.join_title') }}</h2>
      <p class="text-base leading-8 text-muted">{{ __($g.'.join_sub') }}</p>
    </div>
    <ol class="grid gap-8 md:grid-cols-3 md:gap-6">
      <li class="relative space-y-3 {{ $isRtl ? 'md:border-l md:border-line md:pl-6' : 'md:border-r md:border-line md:pr-6' }}">
        <p class="text-sm font-semibold text-metal">01</p>
        <h3 class="text-lg font-semibold text-ink">{{ __($g.'.step1_title') }}</h3>
        <p class="text-sm leading-7 text-muted">{{ __($g.'.step1_desc') }}</p>
      </li>
      <li class="relative space-y-3 {{ $isRtl ? 'md:border-l md:border-line md:pl-6' : 'md:border-r md:border-line md:pr-6' }}">
        <p class="text-sm font-semibold text-metal">02</p>
        <h3 class="text-lg font-semibold text-ink">{{ __($g.'.step2_title') }}</h3>
        <p class="text-sm leading-7 text-muted">{{ __($g.'.step2_desc') }}</p>
      </li>
      <li class="space-y-3">
        <p class="text-sm font-semibold text-metal">03</p>
        <h3 class="text-lg font-semibold text-ink">{{ __($g.'.step3_title') }}</h3>
        <p class="text-sm leading-7 text-muted">{{ __($g.'.step3_desc') }}</p>
      </li>
    </ol>
  </section>

  {{-- Group courses grid --}}
  <section class="bg-surface py-16 md:py-24">
    <div class="container-wide">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ __($g.'.from_groups_kicker') }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __($g.'.from_groups_title') }}</h2>
          <p class="text-base leading-8 text-muted">{{ __($g.'.from_groups_sub') }}</p>
        </div>
        <a href="{{ route('public.courses', ['delivery' => 'group']) }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">
          {{ __($g.'.view_all_group') }}
        </a>
      </div>
      @if($groupCourses->isNotEmpty())
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
          @foreach($groupCourses as $i => $course)
            @include('partials.landing-course-card-site', [
              'course' => $course,
              'badge' => $i === 0 ? ($isRtl ? 'جماعي' : 'Group') : null,
            ])
          @endforeach
        </div>
      @else
        <p class="rounded-2xl border border-line bg-canvas px-6 py-10 text-center text-muted">{{ __($g.'.empty_group') }}</p>
      @endif
    </div>
  </section>

  {{-- One-to-one courses --}}
  <section class="container-wide py-16 md:py-24">
    <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
      <div class="max-w-2xl space-y-3">
        <p class="text-sm font-medium text-accent">{{ __($g.'.from_solo_kicker') }}</p>
        <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __($g.'.from_solo_title') }}</h2>
        <p class="text-base leading-8 text-muted">{{ __($g.'.from_solo_sub') }}</p>
      </div>
      <a href="{{ route('public.courses', ['delivery' => 'one_to_one']) }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">
        {{ __($g.'.view_all_solo') }}
      </a>
    </div>
    @if($oneToOneCourses->isNotEmpty())
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @foreach($oneToOneCourses as $i => $course)
          @include('partials.landing-course-card-site', [
            'course' => $course,
            'badge' => $i === 0 ? ($isRtl ? 'فردي' : '1:1') : null,
          ])
        @endforeach
      </div>
    @else
      <p class="rounded-2xl border border-line bg-surface px-6 py-10 text-center text-muted shadow-soft">{{ __($g.'.empty_solo') }}</p>
    @endif
  </section>

  {{-- Closing CTA --}}
  <section class="container-wide pb-20 md:pb-28">
    <div class="relative overflow-hidden rounded-2xl bg-ink px-6 py-12 text-white shadow-soft sm:px-10 md:px-14 md:py-16">
      <div class="pointer-events-none absolute inset-0 opacity-40" style="background: radial-gradient(ellipse at 20% 0%, rgba(15,92,87,0.55), transparent 55%), radial-gradient(ellipse at 90% 100%, rgba(176,141,87,0.25), transparent 45%);"></div>
      <div class="relative max-w-xl space-y-4">
        <h2 class="text-balance text-2xl font-semibold tracking-tight md:text-3xl">{{ __($g.'.cta_title') }}</h2>
        <p class="text-base leading-8 text-white/70">{{ __($g.'.cta_sub') }}</p>
        <div class="flex flex-wrap gap-3 pt-2">
          <button type="button" data-open-free-trial class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">
            {{ __($g.'.cta_trial') }}
          </button>
          <a href="{{ route('public.courses') }}" class="inline-flex h-11 items-center rounded-xl border border-white/20 bg-white/5 px-5 text-sm font-medium text-white transition hover:bg-white/10">
            {{ __($g.'.cta_courses') }}
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

@include('partials.atheer-home-footer')
<script>
  document.querySelectorAll('[data-open-free-trial]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      window.location.href = {{ \Illuminate\Support\Js::from(url('/?open_trial=1')) }};
    });
  });
</script>
</body>
</html>
