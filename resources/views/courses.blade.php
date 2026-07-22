@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $activeDelivery = $delivery ?? null;
    $activeCategoryId = (int) ($categoryId ?? 0);
    $searchQuery = $searchQuery ?? '';
    $courses = $courseModels ?? collect();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.courses_page_title') }} — Glottical</title>
  <meta name="description" content="{{ __('public.courses_subtitle') }}">
  <link rel="canonical" href="{{ route('public.courses') }}">
  <link rel="alternate" hreflang="ar" href="{{ url('/courses') }}?lang=ar">
  <link rel="alternate" hreflang="en" href="{{ url('/courses') }}?lang=en">
  @include('partials.favicon-links')
  @include('partials.seo-jsonld', ['jsonldType' => 'website'])
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
  <section class="container-wide py-10 md:py-14">
    <nav class="mb-6" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
      <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
        <li><a href="{{ url('/') }}" class="transition hover:text-ink">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a></li>
        <li aria-hidden="true" class="text-line">/</li>
        <li class="font-medium text-ink" aria-current="page">{{ __('landing.nav.courses') }}</li>
      </ol>
    </nav>
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
      <div class="max-w-2xl space-y-3">
        <p class="text-sm font-medium text-accent">{{ $isRtl ? 'كتالوج التعلّم' : 'Learning catalog' }}</p>
        <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-4xl">{{ __('public.courses_page_title') }}</h1>
        <p class="text-base leading-8 text-muted">{{ __('public.courses_subtitle') }}</p>
      </div>
      <p class="text-sm text-muted shrink-0">
        <span class="font-semibold text-ink">{{ number_format($courses->count()) }}</span>
        {{ $isRtl ? 'كورساً متاحاً' : 'courses available' }}
      </p>
    </div>
  </section>

  <section class="container-wide pb-8">
    <form action="{{ route('public.courses') }}" method="get" class="flex flex-col gap-3 sm:flex-row">
      @if($activeDelivery)
        <input type="hidden" name="delivery" value="{{ $activeDelivery }}">
      @endif
      @if($activeCategoryId > 0)
        <input type="hidden" name="category" value="{{ $activeCategoryId }}">
      @endif
      <div class="relative min-w-0 flex-1">
        <svg class="pointer-events-none absolute top-1/2 {{ $isRtl ? 'right-3' : 'left-3' }} size-4 -translate-y-1/2 text-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('public.search_course_placeholder') ?? ($isRtl ? 'ابحث عن كورس…' : 'Search courses…') }}" class="h-12 w-full rounded-xl border border-line bg-surface {{ $isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4' }} text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" aria-label="{{ $isRtl ? 'بحث' : 'Search' }}">
      </div>
      <button type="submit" class="btn-press inline-flex h-12 shrink-0 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ $isRtl ? 'بحث' : 'Search' }}</button>
    </form>

    <div class="mt-5 flex flex-wrap gap-2">
      @php
        $chip = 'inline-flex h-10 items-center rounded-xl border px-3.5 text-sm font-medium transition';
        $chipOn = 'border-accent bg-accent text-white';
        $chipOff = 'border-line bg-surface text-ink-soft hover:border-accent/30 hover:bg-accent-soft hover:text-accent';
      @endphp
      <a href="{{ route('public.courses', array_filter(['q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null])) }}" class="{{ $chip }} {{ ! $activeDelivery ? $chipOn : $chipOff }}">{{ __('public.courses_filter_all') ?? ($isRtl ? 'الكل' : 'All') }}</a>
      <a href="{{ route('public.courses', array_filter(['delivery' => 'group', 'q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null])) }}" class="{{ $chip }} {{ $activeDelivery === 'group' ? $chipOn : $chipOff }}">{{ __('public.courses_filter_group') ?? ($isRtl ? 'جماعي' : 'Group') }}</a>
      <a href="{{ route('public.courses', array_filter(['delivery' => 'one_to_one', 'q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null])) }}" class="{{ $chip }} {{ $activeDelivery === 'one_to_one' ? $chipOn : $chipOff }}">
        {{ __('public.courses_filter_one_to_one') ?? ($isRtl ? 'فردي' : '1:1') }}
        @if(($oneToOneCount ?? 0) > 0)
          <span class="ms-1.5 inline-flex min-w-[1.25rem] items-center justify-center rounded-lg {{ $activeDelivery === 'one_to_one' ? 'bg-white/20' : 'bg-accent-soft text-accent' }} px-1.5 text-[11px] font-semibold">{{ $oneToOneCount }}</span>
        @endif
      </a>
    </div>

    @if(($courseFilterCategories ?? collect())->isNotEmpty())
      <div class="mt-4 flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('public.courses', array_filter(['delivery' => $activeDelivery, 'q' => $searchQuery ?: null])) }}" class="{{ $chip }} shrink-0 {{ $activeCategoryId === 0 ? $chipOn : $chipOff }}">{{ __('public.all_course_categories') ?? ($isRtl ? 'كل التصنيفات' : 'All categories') }}</a>
        @foreach($courseFilterCategories as $cat)
          <a href="{{ route('public.courses', array_filter(['category' => $cat->id, 'delivery' => $activeDelivery, 'q' => $searchQuery ?: null])) }}" class="{{ $chip }} shrink-0 {{ $activeCategoryId === (int) $cat->id ? $chipOn : $chipOff }}">{{ $cat->name }}</a>
        @endforeach
      </div>
    @endif
  </section>

  <section class="container-wide pb-20 md:pb-24">
    @if($courses->isNotEmpty())
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @foreach($courses as $i => $course)
          @include('partials.landing-course-card-site', [
            'course' => $course,
            'badge' => ! empty($course->is_featured) ? (__('landing.featured_badge') ?? ($isRtl ? 'مميّز' : 'Featured')) : ($i < 2 && $searchQuery === '' && ! $activeCategoryId ? ($isRtl ? 'جديد' : 'New') : null),
          ])
        @endforeach
      </div>
    @else
      <div class="rounded-2xl border border-line bg-surface p-10 text-center shadow-soft">
        <p class="text-base text-muted">{{ $isRtl ? 'لا توجد كورسات مطابقة لبحثك حالياً.' : 'No courses match your filters right now.' }}</p>
        <div class="mt-5 flex flex-wrap justify-center gap-3">
          <a href="{{ route('public.courses') }}" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ $isRtl ? 'عرض كل الكورسات' : 'View all courses' }}</a>
          <a href="{{ route('public.categories') }}" class="inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.nav.categories') }}</a>
        </div>
      </div>
    @endif
  </section>

  <section class="container-wide pb-16 md:pb-20">
    <div class="relative overflow-hidden rounded-3xl bg-ink px-6 py-10 text-white shadow-soft sm:px-10 md:py-12">
      <div class="pointer-events-none absolute -top-20 {{ $isRtl ? '-left-16' : '-right-16' }} size-56 rounded-full bg-accent/25 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-24 {{ $isRtl ? '-right-10' : '-left-10' }} size-48 rounded-full bg-metal/20 blur-3xl"></div>
      <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl space-y-3">
          <p class="text-sm font-medium text-metal">{{ $isRtl ? 'لست متأكداً أي كورس يناسبك؟' : 'Not sure which course fits?' }}</p>
          <h2 class="text-balance text-2xl font-semibold md:text-3xl">{{ $isRtl ? 'احجز تقييم مستوى مجاني وسنرشّح لك الأنسب' : 'Book a free assessment — we’ll recommend the best fit' }}</h2>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row md:shrink-0">
          <a href="{{ url('/?open_trial=1') }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('landing.academy.free_trial_cta') }}</a>
          <a href="{{ route('public.learning-paths.index') }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-6 text-sm font-medium transition hover:bg-white/10">{{ __('landing.nav.learning_paths') }}</a>
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
