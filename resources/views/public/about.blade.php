@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $heroImg = 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=80';
    $missionImg = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.about_page_title') }} — {{ $brand }}</title>
  <meta name="description" content="{{ __('public.about_intro') }}">
  <link rel="canonical" href="{{ route('public.about') }}">
  @include('partials.favicon-links')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  @include('partials.atheer-head')
  <meta name="theme-color" content="#0f5c57">
</head>
<body class="font-sans antialiased">
@include('partials.atheer-home-header')

<main class="page-enter">
  {{-- Full-bleed hero — brand first --}}
  <section class="relative overflow-hidden bg-ink py-20 text-white md:py-28">
    <img src="{{ $heroImg }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40" aria-hidden="true" loading="eager" width="2400" height="1200">
    <div class="absolute inset-0 {{ $isRtl ? 'bg-gradient-to-l' : 'bg-gradient-to-r' }} from-ink via-ink/80 to-ink/50"></div>
    <div class="container-wide relative max-w-3xl space-y-5">
      <p class="text-sm font-medium text-metal">{{ $isRtl ? 'قصة '.$brand : 'The '.$brand.' story' }}</p>
      <p class="text-4xl font-bold tracking-tight md:text-5xl">{{ $brand }}</p>
      <h1 class="text-balance text-2xl font-semibold text-white/95 md:text-4xl">{{ __('public.about_hero_sub') }}</h1>
      <p class="text-base leading-8 text-white/80 md:text-lg">{{ __('public.about_intro') }}</p>
    </div>
  </section>

  {{-- Mission / story --}}
  <section class="container-wide py-20 md:py-24">
    <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
      <div class="space-y-4">
        <p class="text-sm font-medium text-accent">{{ __('public.about_heading') }}</p>
        <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __('public.about_hero_sub') }}</h2>
        <p class="text-base leading-8 text-muted">{!! __('public.about_para1', ['brand' => '<strong class="font-semibold text-ink">'.e($brand).'</strong>']) !!}</p>
        <p class="text-base leading-8 text-muted">{{ __('public.about_para2') }}</p>
      </div>
      <div class="overflow-hidden rounded-3xl shadow-soft">
        <img src="{{ $missionImg }}" alt="{{ $brand }}" class="aspect-[4/3] w-full object-cover" loading="lazy" width="1200" height="900">
      </div>
    </div>
  </section>

  {{-- Vision & mission — one purpose, clean columns --}}
  <section class="bg-surface py-20 md:py-24">
    <div class="container-wide">
      <div class="grid gap-12 md:grid-cols-2 md:gap-16">
        <div class="space-y-4">
          <p class="text-sm font-medium text-accent">{{ __('public.our_vision') }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink">{{ $isRtl ? 'لغة تفتح باب الفرصة' : 'Language that opens opportunity' }}</h2>
          <p class="text-base leading-8 text-muted">{{ __('public.vision_text') }}</p>
        </div>
        <div class="space-y-4">
          <p class="text-sm font-medium text-accent">{{ __('public.our_mission') }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink">{{ $isRtl ? 'تعليم عملي حتى التوظيف' : 'Practical learning to hiring' }}</h2>
          <p class="text-base leading-8 text-muted">{{ __('public.mission_intro') }}</p>
          <ul class="space-y-3 text-sm leading-7 text-muted">
            <li class="flex gap-3"><span class="mt-2 size-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true"></span><span>{{ __('public.mission_1') }}</span></li>
            <li class="flex gap-3"><span class="mt-2 size-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true"></span><span>{{ __('public.mission_2') }}</span></li>
            <li class="flex gap-3"><span class="mt-2 size-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true"></span><span>{{ __('public.mission_3') }}</span></li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  {{-- Values — site/about pattern --}}
  <section class="container-wide py-20 md:py-24">
    <div class="mb-10 max-w-2xl space-y-3 md:mb-14">
      <p class="text-sm font-medium text-accent">{{ __('public.our_values') }}</p>
      <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __('public.why_platform') }}</h2>
      <p class="text-base leading-8 text-muted">{{ $isRtl ? 'ثلاث ركائز تحكم كل قرار نتخذه — من تصميم الكورس إلى متابعة الطالب.' : 'Three pillars guide every decision — from course design to student follow-up.' }}</p>
    </div>
    <div class="grid gap-6 md:grid-cols-3">
      <article class="rounded-2xl border border-line bg-surface p-8 shadow-soft">
        <div class="mb-4 flex size-12 items-center justify-center rounded-xl bg-accent-soft text-accent">
          <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-ink">{{ __('public.value_1_title') }}</h3>
        <p class="text-sm leading-7 text-muted">{{ __('public.value_1_desc') }}</p>
      </article>
      <article class="rounded-2xl border border-line bg-surface p-8 shadow-soft">
        <div class="mb-4 flex size-12 items-center justify-center rounded-xl bg-accent-soft text-accent">
          <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-ink">{{ __('public.value_2_title') }}</h3>
        <p class="text-sm leading-7 text-muted">{{ __('public.value_2_desc') }}</p>
      </article>
      <article class="rounded-2xl border border-line bg-surface p-8 shadow-soft">
        <div class="mb-4 flex size-12 items-center justify-center rounded-xl bg-accent-soft text-accent">
          <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.77.78-.77-.78a5.4 5.4 0 0 0-7.65 7.65l.77.78L12 21.23l7.65-7.65.77-.78a5.4 5.4 0 0 0 0-7.65z"/></svg>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-ink">{{ __('public.value_3_title') }}</h3>
        <p class="text-sm leading-7 text-muted">{{ __('public.value_3_desc') }}</p>
      </article>
    </div>
  </section>

  {{-- Quiet stats band (not first viewport) --}}
  <section class="bg-ink py-14 text-white md:py-16">
    <div class="container-wide">
      <div class="grid grid-cols-2 gap-8 text-center md:grid-cols-4 md:gap-6">
        <div class="space-y-1">
          <p class="text-3xl font-semibold tracking-tight text-metal md:text-4xl">{{ number_format((int) ($stats['courses'] ?? 0)) }}+</p>
          <p class="text-sm text-white/65">{{ __('public.stat_courses') }}</p>
        </div>
        <div class="space-y-1">
          <p class="text-3xl font-semibold tracking-tight text-metal md:text-4xl">{{ number_format((int) ($stats['students'] ?? 0)) }}+</p>
          <p class="text-sm text-white/65">{{ __('public.stat_students') }}</p>
        </div>
        <div class="space-y-1">
          <p class="text-3xl font-semibold tracking-tight text-metal md:text-4xl">{{ number_format((int) ($stats['instructors'] ?? 0)) }}+</p>
          <p class="text-sm text-white/65">{{ __('public.stat_instructors') }}</p>
        </div>
        <div class="space-y-1">
          <p class="text-3xl font-semibold tracking-tight text-metal md:text-4xl">100%</p>
          <p class="text-sm text-white/65">{{ __('public.stat_quality') }}</p>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="container-wide py-20 md:py-24">
    <div class="rounded-3xl border border-line bg-surface px-6 py-10 text-center shadow-soft md:px-14 md:py-14">
      <p class="mb-3 text-sm font-medium text-accent">{{ $brand }}</p>
      <h2 class="mb-4 text-balance text-2xl font-semibold text-ink md:text-3xl">{{ __('public.cta_about_title') }}</h2>
      <p class="mx-auto mb-6 max-w-lg text-base leading-8 text-muted">{{ __('public.cta_about_desc') }}</p>
      <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ route('register') }}" class="btn-press inline-flex h-12 items-center rounded-xl bg-accent px-7 font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('public.register_free_now') ?? ($isRtl ? 'سجّل مجاناً الآن' : 'Register free now') }}</a>
        <a href="{{ route('public.courses') }}" class="inline-flex h-12 items-center rounded-xl border border-line px-7 font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('public.browse_all_courses_btn') }}</a>
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
