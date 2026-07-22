@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $a = 'landing.academy';
    $currency = __('landing.currency');
    $fallbackImgs = [
        'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1546410531-bb4caa6b139d?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1400&q=80',
    ];
    $paths = $learningPaths ?? collect();
    $featured = $featuredCourses ?? collect();
    if ($featured->isEmpty()) {
        $featured = $paths->flatMap(fn ($p) => $p->courses ?? collect())->unique('id')->take(8)->values();
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.learning_paths_page_title') }} — Glottical</title>
  <meta name="description" content="{{ $isRtl ? 'مسارات تعليمية منسّقة في Glottical — خطط واضحة من البداية حتى الطلاقة.' : 'Curated learning paths at Glottical — clear plans from beginner to fluency.' }}">
  <link rel="canonical" href="{{ route('public.learning-paths.index') }}">
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
  <section class="container-wide py-10 md:py-14">
    <nav class="mb-6" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
      <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
        <li><a href="{{ url('/') }}" class="transition hover:text-ink">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a></li>
        <li aria-hidden="true" class="text-line">/</li>
        <li class="font-medium text-ink" aria-current="page">{{ __('landing.nav.learning_paths') }}</li>
      </ol>
    </nav>
    <div class="max-w-2xl space-y-3">
      <p class="text-sm font-medium text-accent">{{ __($a.'.paths_kicker') }}</p>
      <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-4xl">{{ __('landing.nav.learning_paths') }}</h1>
      <p class="text-base leading-8 text-muted">{{ $isRtl ? 'مسارات ديناميكية تُبنى حول هدفك اللغوي — لتقليل التردد وبدء التعلّم بخطوات واضحة.' : 'Dynamic paths built around your language goal — less hesitation, clearer steps to start learning.' }}</p>
    </div>
  </section>

  <section class="container-wide pb-16 md:pb-20">
    <div class="grid gap-5 lg:grid-cols-3">
      @forelse($paths as $i => $path)
        @php
          $thumb = $path->thumbnail ? storage_public_url(str_replace('\\', '/', $path->thumbnail)) : null;
          $img = $thumb ?: $fallbackImgs[$i % count($fallbackImgs)];
          $url = (!empty($path->slug))
            ? route('public.learning-path.show', $path->slug)
            : route('public.learning-paths.index');
          $count = (int) ($path->courses_count ?? 0);
          $desc = \Illuminate\Support\Str::limit(strip_tags((string) ($path->description ?? '')), 110);
          if ($desc === '') {
            $desc = $isRtl
              ? ($count > 0 ? "{$count} كورساً منسّقاً في مسار واضح" : 'مسار تعلّم بمعايير Glottical')
              : ($count > 0 ? "{$count} curated courses in a clear path" : 'A learning path to Glottical standards');
          }
          $price = (float) ($path->price ?? 0);
        @endphp
        <article class="group relative min-h-80 sm:min-h-96 overflow-hidden rounded-2xl shadow-soft card-lift">
          <img src="{{ $img }}" alt="{{ $path->name }}" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy" width="700" height="900">
          <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/40 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 space-y-3 p-5 sm:p-6 text-white">
            @if($count > 0)
              <p class="text-xs font-medium text-white/70">{{ $isRtl ? "{$count} كورساً" : "{$count} courses" }}</p>
            @endif
            <h2 class="text-xl sm:text-2xl font-semibold">{{ $path->name }}</h2>
            <p class="text-sm leading-7 text-white/75">{{ $desc }}</p>
            <div class="flex flex-wrap items-center gap-3 pt-1">
              <a href="{{ $url }}" class="btn-press inline-flex h-10 items-center rounded-xl bg-white px-5 text-sm font-medium text-ink transition hover:bg-canvas">
                {{ $isRtl ? 'استكشف المسار' : 'Explore path' }}
              </a>
              @if($price > 0)
                <span class="text-sm font-semibold text-white/90">{{ number_format($price, 0) }} {{ $currency }}</span>
              @elseif($price === 0.0 && $count > 0)
                <span class="text-sm font-medium text-metal">{{ __('landing.free') }}</span>
              @endif
            </div>
          </div>
        </article>
      @empty
        <div class="col-span-full rounded-2xl border border-line bg-surface p-10 text-center shadow-soft">
          <p class="text-base text-muted">{{ $isRtl ? 'لا توجد مسارات تعليمية متاحة حالياً.' : 'No learning paths available yet.' }}</p>
          <a href="{{ route('public.courses') }}" class="btn-press mt-4 inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('landing.view_all_courses') }}</a>
        </div>
      @endforelse
    </div>
  </section>

  @if($featured->isNotEmpty())
  <section class="bg-surface py-20 md:py-24">
    <div class="container-wide">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'من المسارات' : 'From the paths' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'كورسات مختارة من المسارات' : 'Courses picked from the paths' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'بطاقات واضحة مع تقييم وسعر وتسجيل سريع — لتقليل الخطوات حتى بدء التعلّم.' : 'Clear cards with rating, price, and quick enroll — fewer steps to start learning.' }}</p>
        </div>
        <a href="{{ route('public.courses') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.view_all_courses') }}</a>
      </div>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @foreach($featured->take(8) as $i => $course)
          @include('partials.landing-course-card-site', [
            'course' => $course,
            'badge' => $i === 0 ? ($isRtl ? 'مميّز' : 'Featured') : null,
          ])
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <section class="container-wide py-16 md:py-20">
    <div class="relative overflow-hidden rounded-3xl bg-ink px-6 py-10 text-white shadow-soft sm:px-10 md:px-12 md:py-12">
      <div class="pointer-events-none absolute -top-20 {{ $isRtl ? '-left-16' : '-right-16' }} size-56 rounded-full bg-accent/25 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-24 {{ $isRtl ? '-right-10' : '-left-10' }} size-48 rounded-full bg-metal/20 blur-3xl"></div>
      <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl space-y-3">
          <p class="text-sm font-medium text-metal">{{ $isRtl ? 'لست متأكداً من أين تبدأ؟' : 'Not sure where to start?' }}</p>
          <h2 class="text-balance text-2xl font-semibold md:text-3xl">{{ $isRtl ? 'احجز تقييم مستوى مجاني وسنرشّح لك المسار الأنسب' : 'Book a free level assessment — we’ll recommend the right path' }}</h2>
          <p class="text-sm leading-7 text-white/70 md:text-base">{{ $isRtl ? 'جلسة 30 دقيقة توضّح مستواك، وخطواتك التالية، دون تخمين.' : 'A 30-minute session clarifies your level and next steps — no guesswork.' }}</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row md:shrink-0">
          <a href="{{ url('/?open_trial=1') }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __($a.'.free_trial_cta') }}</a>
          <a href="{{ route('public.categories') }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-6 text-sm font-medium transition hover:bg-white/10">{{ __('landing.nav.categories') }}</a>
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
