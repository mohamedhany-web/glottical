@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $fallbackImgs = [
        'https://images.unsplash.com/photo-1546410531-bb4caa6b139d?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1456513080080-7e9e0e9c0b0b?auto=format&fit=crop&w=1200&q=80',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('landing.nav.categories') }} — Glottical</title>
  <meta name="description" content="{{ $isRtl ? 'تصفّح تصنيفات Glottical — مسارات بصرية واضحة تقودك للكورس المناسب.' : 'Browse Glottical categories — clear visual paths to the right course.' }}">
  <link rel="canonical" href="{{ route('public.categories') }}">
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
  <section class="container-wide py-10 md:py-14">
    <nav class="mb-6" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
      <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
        <li><a href="{{ url('/') }}" class="transition hover:text-ink">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a></li>
        <li aria-hidden="true" class="text-line">/</li>
        <li class="font-medium text-ink" aria-current="page">{{ __('landing.nav.categories') }}</li>
      </ol>
    </nav>
    <div class="max-w-2xl space-y-3">
      <p class="text-sm font-medium text-accent">{{ $isRtl ? 'اكتشف بسرعة' : 'Discover fast' }}</p>
      <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-4xl">{{ __('landing.nav.categories') }}</h1>
      <p class="text-base leading-8 text-muted">{{ $isRtl ? 'مسارات بصرية واضحة تقلل التردد وتقرّبك من الكورس المناسب بعدد نقرات أقل — بمعايير Glottical للجودة والثقة.' : 'Clear visual paths reduce hesitation and get you to the right course in fewer clicks.' }}</p>
    </div>
  </section>

  <section class="container-wide pb-16 md:pb-20">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      @forelse($categories as $i => $cat)
        @php
          $img = $cat['thumb_url'] ?: $fallbackImgs[$i % count($fallbackImgs)];
        @endphp
        <a href="{{ $cat['url'] }}" class="group relative min-h-56 sm:min-h-72 overflow-hidden rounded-2xl shadow-soft card-lift">
          <img src="{{ $img }}" alt="{{ $cat['name'] }}" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy" width="600" height="720">
          <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/25 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 space-y-1 p-5 text-white">
            <h2 class="text-xl font-semibold">{{ $cat['name'] }}</h2>
            <p class="text-sm text-white/75">{{ $cat['desc'] }}</p>
          </div>
        </a>
      @empty
        <div class="col-span-full rounded-2xl border border-line bg-surface p-10 text-center shadow-soft">
          <p class="text-base text-muted">{{ $isRtl ? 'لا توجد تصنيفات متاحة حالياً.' : 'No categories available yet.' }}</p>
          <a href="{{ route('public.courses') }}" class="mt-4 inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('landing.view_all_courses') }}</a>
        </div>
      @endforelse
    </div>
  </section>

  @if($featuredCourses->isNotEmpty())
  <section class="bg-surface py-20 md:py-24">
    <div class="container-wide">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'كورسات مختارة' : 'Featured courses' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'أبرز ما في التصنيفات' : 'Highlights across categories' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'بطاقات واضحة مع تقييم وسعر وتسجيل سريع — لتقليل الخطوات حتى بدء التعلّم.' : 'Clear cards with rating, price, and quick enroll — fewer steps to start learning.' }}</p>
        </div>
        <a href="{{ route('public.courses') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.view_all_courses') }}</a>
      </div>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @foreach($featuredCourses as $i => $course)
          @include('partials.landing-course-card-site', [
            'course' => $course,
            'badge' => $i === 0 ? ($isRtl ? 'مميّز' : 'Featured') : null,
          ])
        @endforeach
      </div>
    </div>
  </section>
  @endif
</main>

@include('partials.atheer-home-footer')

<script>
  document.querySelectorAll('[data-open-free-trial]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      window.location.href = {{ Js::from(url('/?open_trial=1')) }};
    });
  });
</script>
</body>
</html>
