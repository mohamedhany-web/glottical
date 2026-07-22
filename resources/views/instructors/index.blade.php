@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $profiles = $profiles ?? collect();
    $featuredCourses = $featuredCourses ?? collect();
    $consultationOn = isset($consultationSetting) && $consultationSetting->is_active;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.instructors_page_title') }} — Glottical</title>
  <meta name="description" content="{{ __('public.instructors_subtitle') }}">
  <link rel="canonical" href="{{ route('public.instructors.index') }}">
  <link rel="alternate" hreflang="ar" href="{{ url('/instructors') }}?lang=ar">
  <link rel="alternate" hreflang="en" href="{{ url('/instructors') }}?lang=en">
  @include('partials.favicon-links')
  @include('partials.seo-jsonld', ['jsonldType' => 'website'])
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
        <li class="font-medium text-ink" aria-current="page">{{ __('landing.nav.instructors') }}</li>
      </ol>
    </nav>
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
      <div class="max-w-2xl space-y-3">
        <p class="text-sm font-medium text-accent">{{ $isRtl ? 'مدربون موثوقون' : 'Trusted instructors' }}</p>
        <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-4xl">{{ __('public.instructors_page_title') }}</h1>
        <p class="text-base leading-8 text-muted">{{ __('public.instructors_subtitle') }}</p>
      </div>
      <div class="flex flex-wrap gap-6 text-sm text-muted">
        <p><span class="font-semibold text-ink">{{ number_format($profiles->count()) }}</span> {{ $isRtl ? 'مدرباً' : 'instructors' }}</p>
        <p><span class="font-semibold text-ink">{{ number_format((int) $profiles->sum('courses_count')) }}</span> {{ $isRtl ? 'كورساً' : 'courses' }}</p>
      </div>
    </div>
  </section>

  {{-- شريط سريع بأسماء المدربين (مثل علامات brands) --}}
  @if($profiles->isNotEmpty())
  <section class="container-wide pb-10 md:pb-12">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
      @foreach($profiles->take(8) as $p)
        <a href="{{ route('public.instructors.show', $p->user) }}" class="group flex h-28 flex-col items-center justify-center gap-2 rounded-2xl border border-line bg-surface px-2 text-center shadow-soft transition hover:border-accent/30 hover:bg-accent-soft hover:shadow-lift card-lift">
          <span class="text-sm font-bold text-ink-soft transition group-hover:text-accent line-clamp-2">{{ \Illuminate\Support\Str::limit($p->user->name ?? '—', 22) }}</span>
          <span class="text-xs text-muted line-clamp-1">{{ \Illuminate\Support\Str::limit($p->headline ?? __('public.instructor_fallback'), 24) }}</span>
        </a>
      @endforeach
    </div>
  </section>
  @endif

  <section class="bg-surface py-16 md:py-20">
    <div class="container-wide">
      <div class="mb-8 max-w-2xl space-y-3 md:mb-10">
        <p class="text-sm font-medium text-accent">{{ $isRtl ? 'ملفات المدربين' : 'Instructor profiles' }}</p>
        <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'تعرّف على من يعلّمك' : 'Meet who will teach you' }}</h2>
        <p class="text-base leading-8 text-muted">{{ $isRtl ? 'كل مدرب بهوية واضحة — خبرة، تخصصات، وكورسات يمكنك البدء بها فوراً.' : 'Each instructor has a clear identity — expertise, skills, and courses you can start now.' }}</p>
      </div>

      @if($profiles->isNotEmpty())
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          @foreach($profiles as $p)
            @php
              $url = route('public.instructors.show', $p->user);
              $skills = is_array($p->skills_list ?? null) ? array_slice($p->skills_list, 0, 3) : [];
            @endphp
            <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-canvas shadow-soft card-lift">
              <a href="{{ $url }}" class="relative block aspect-[4/3] overflow-hidden bg-canvas-muted">
                @if($p->photo_url)
                  <img src="{{ $p->photo_url }}" alt="{{ $p->user->name }}" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy" width="640" height="480">
                @else
                  <span class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-accent to-ink text-4xl text-white/35">✦</span>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-transparent to-transparent"></div>
                <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
                  @if(($p->courses_count ?? 0) > 0)
                    <span class="inline-flex items-center rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">{{ $p->courses_count }} {{ $isRtl ? 'كورس' : 'courses' }}</span>
                  @else
                    <span></span>
                  @endif
                  @if(! empty($p->marketing_featured_today))
                    <span class="inline-flex items-center rounded-lg bg-[#f4eadc] px-2.5 py-1 text-xs font-medium text-[#7a5c2e]">{{ __('public.instructors_featured_badge') }}</span>
                  @endif
                </div>
              </a>
              <div class="flex flex-1 flex-col gap-3 p-5">
                <div>
                  <h3 class="text-lg font-semibold text-ink transition group-hover:text-accent">
                    <a href="{{ $url }}">{{ $p->user->name }}</a>
                  </h3>
                  <p class="mt-1 text-sm font-medium text-accent">{{ $p->headline ?? __('public.instructor_fallback') }}</p>
                </div>
                @if($skills !== [])
                  <div class="flex flex-wrap gap-1.5">
                    @foreach($skills as $skill)
                      <span class="rounded-lg border border-line bg-surface px-2.5 py-1 text-[11px] font-medium text-ink-soft">{{ $skill }}</span>
                    @endforeach
                  </div>
                @endif
                @if($p->bio)
                  <p class="line-clamp-2 text-sm leading-6 text-muted">{{ $p->bio }}</p>
                @endif
                <div class="mt-auto flex flex-wrap items-center gap-2 border-t border-line pt-4">
                  <a href="{{ $url }}" class="btn-press inline-flex h-10 flex-1 items-center justify-center rounded-xl bg-accent px-4 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('public.view_instructor_profile') }}</a>
                  @if($consultationOn)
                    @auth
                      @if(auth()->user()->isStudent())
                        <a href="{{ route('consultations.create', $p->user) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface px-3 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('public.instructors_consult_cta') }}</a>
                      @endif
                    @else
                      <a href="{{ route('login', ['redirect' => route('consultations.create', $p->user)]) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface px-3 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('public.instructors_consult_cta') }}</a>
                    @endauth
                  @endif
                </div>
                @if($consultationOn)
                  <p class="text-xs text-muted">{{ __('public.instructors_consult_label') }} — <span class="font-semibold text-ink">{{ number_format($p->effectiveConsultationPriceEgp(), 0) }}</span> {{ $isRtl ? 'ج.م' : 'EGP' }}</p>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      @else
        <div class="rounded-2xl border border-line bg-canvas p-10 text-center shadow-soft">
          <p class="text-base text-muted">{{ $isRtl ? 'لا يوجد مدربون معتمدون للعرض حالياً.' : 'No approved instructors to show yet.' }}</p>
          <a href="{{ route('public.courses') }}" class="btn-press mt-4 inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('landing.view_all_courses') }}</a>
        </div>
      @endif
    </div>
  </section>

  @if($featuredCourses->isNotEmpty())
  <section class="container-wide py-16 md:py-20">
    <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
      <div class="max-w-2xl space-y-3">
        <p class="text-sm font-medium text-accent">{{ $isRtl ? 'من المدربين' : 'From instructors' }}</p>
        <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'كورسات مختارة من مدرّبينا' : 'Courses from our instructors' }}</h2>
        <p class="text-base leading-8 text-muted">{{ $isRtl ? 'ابدأ من كورس واضح بمعايير Glottical — تقييم وسعر وتسجيل سريع.' : 'Start with a clear course — rating, price, and quick enroll.' }}</p>
      </div>
      <a href="{{ route('public.courses') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.view_all_courses') }}</a>
    </div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
      @foreach($featuredCourses as $i => $course)
        @include('partials.landing-course-card-site', [
          'course' => $course,
          'badge' => $i === 0 ? (__('landing.featured_badge') ?? ($isRtl ? 'مميّز' : 'Featured')) : null,
        ])
      @endforeach
    </div>
  </section>
  @endif

  <section class="container-wide pb-16 md:pb-20">
    <div class="relative overflow-hidden rounded-3xl bg-ink px-6 py-10 text-white shadow-soft sm:px-10 md:py-12">
      <div class="pointer-events-none absolute -top-20 {{ $isRtl ? '-left-16' : '-right-16' }} size-56 rounded-full bg-accent/25 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-24 {{ $isRtl ? '-right-10' : '-left-10' }} size-48 rounded-full bg-metal/20 blur-3xl"></div>
      <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl space-y-3">
          <p class="text-sm font-medium text-metal">{{ $isRtl ? 'هل تحتاج توجيهاً شخصياً؟' : 'Need personal guidance?' }}</p>
          <h2 class="text-balance text-2xl font-semibold md:text-3xl">{{ $isRtl ? 'احجز تقييم مستوى مجاني وسنربطك بالمدرب الأنسب' : 'Book a free assessment — we’ll match you with the right instructor' }}</h2>
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
