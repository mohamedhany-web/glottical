@php
    $a = $a ?? 'landing.academy';
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $heroImg = $heroSlides[0] ?? 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=2400&q=80';
    $catTiles = [
        ['label' => __($a.'.chip_english'), 'q' => 'english', 'count' => max(8, (int)($homeStats['courses'] ?? 20) - 2), 'img' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b139d?auto=format&fit=crop&w=1200&q=80'],
        ['label' => __($a.'.chip_arabic'), 'q' => 'arabic', 'count' => max(6, (int)($homeStats['courses'] ?? 18) - 4), 'img' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1200&q=80'],
        ['label' => __($a.'.chip_french'), 'q' => 'french', 'count' => max(5, (int)($homeStats['courses'] ?? 12) - 6), 'img' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80'],
        ['label' => __($a.'.chip_kids'), 'q' => 'kids', 'count' => max(4, (int)($homeStats['courses'] ?? 10) - 5), 'img' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1200&q=80'],
    ];
    $flashCourses = $rowTrendingNow->take(2)->values();
    $inspo = [
        ['title' => $isRtl ? 'رحلة تحدث بطلاقة' : 'Speak with confidence', 'sub' => $isRtl ? 'من المفردات إلى المحادثة اليومية بأسلوب أهل اللغة.' : 'From vocabulary to daily conversation like a native.', 'img' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80', 'url' => route('public.courses')],
        ['title' => $isRtl ? 'تحضير اختبارات عالمية' : 'Exam prep paths', 'sub' => $isRtl ? 'IELTS وTOEFL وغيرها بخطط واضحة وجلسات مركّزة.' : 'IELTS, TOEFL and more with clear focused plans.', 'img' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80', 'url' => route('public.courses', ['q' => 'ielts'])],
        ['title' => $isRtl ? 'تعلّم للأطفال' : 'Kids learning', 'sub' => $isRtl ? 'مناهج مرحة وآمنة تناسب الصغار وتبني أساس قوي.' : 'Fun safe curricula that build a strong foundation.', 'img' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1200&q=80', 'url' => route('public.courses', ['q' => 'kids'])],
    ];
    $fallbackReviews = [
        (object)['author_name' => $isRtl ? 'ليلى أحمد' : 'Layla Ahmed', 'role_label' => $isRtl ? 'الرياض · إنجليزي محادثة' : 'Riyadh · Spoken English', 'body' => $isRtl ? 'التقييم المجاني وضّح مستواي فوراً، والمدرب اختار لي المسار المناسب بدون تخمين.' : 'The free assessment clarified my level instantly, and my tutor picked the right path.'],
        (object)['author_name' => $isRtl ? 'عمر حسن' : 'Omar Hassan', 'role_label' => $isRtl ? 'دبي · IELTS' : 'Dubai · IELTS', 'body' => $isRtl ? 'تجربة عربية أصيلة فعلاً — مو ترجمة. الحصص المباشرة حسّنت طلاقتي بسرعة.' : 'A truly Arabic-first experience. Live sessions improved my fluency fast.'],
        (object)['author_name' => $isRtl ? 'نورة السالم' : 'Noura Alsalem', 'role_label' => $isRtl ? 'الكويت · فرنسي للمبتدئين' : 'Kuwait · French beginners', 'body' => $isRtl ? 'المنصة مرتبة والواجهة راقية بدون ضوضاء. أحس إني أتعلم بمتعة كل أسبوع.' : 'Clean platform, elegant UI — learning feels enjoyable every week.'],
    ];
    $reviewRows = ($testimonialRows ?? collect())->take(3);
    if ($reviewRows->count() < 3) {
        $reviewRows = collect($fallbackReviews);
    }
@endphp

<main class="page-enter">
    {{-- 1. Hero --}}
    <section class="relative min-h-[92vh] overflow-hidden bg-ink text-white">
      <img src="{{ e(\App\Services\SeoAssets::optimizedRemoteImage($heroImg, 1600, 70)) }}" alt="" class="absolute inset-0 h-full w-full object-cover object-center opacity-45" width="1600" height="900" fetchpriority="high" decoding="async">
      <div class="hero-scrim" aria-hidden="true"></div>
      <div class="container-wide relative flex min-h-[92vh] flex-col justify-end pb-14 pt-32 sm:pb-16 md:justify-center md:pb-24 md:pt-36">
        <div class="max-w-2xl space-y-5 sm:space-y-6">
          <p class="fade-up text-3xl font-bold tracking-tight sm:text-4xl md:text-6xl">Glottical</p>
          <h1 class="fade-up fade-up-delay-1 text-balance text-2xl font-semibold leading-tight sm:text-3xl md:text-5xl md:leading-[1.15]">{{ __($a.'.identity_title') }}</h1>
          <p class="fade-up fade-up-delay-2 max-w-xl text-sm leading-7 text-white/85 sm:text-base sm:leading-8 md:text-lg">{{ __($a.'.identity_sub') }}</p>
          <div class="hero-cta-row fade-up fade-up-delay-3 flex flex-col gap-3 pt-1 sm:flex-row sm:flex-wrap sm:pt-2">
            <button type="button" data-open-free-trial class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white shadow-[0_10px_24px_rgba(15,92,87,0.25)] transition hover:bg-[#0d4f4a] sm:h-14 sm:px-7 sm:text-base">{{ __($a.'.free_trial_cta') }}</button>
            <a href="{{ route('register') }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl border border-white/25 bg-white/10 px-6 text-sm font-medium text-white transition hover:bg-white/15 sm:h-14 sm:px-7 sm:text-base">{{ __($a.'.identity_secondary_cta') }}</a>
          </div>
        </div>
      </div>
    </section>

    {{-- 2. Free assessment banner --}}
    <section class="container-wide py-8 md:py-12">
      <div class="relative overflow-hidden rounded-3xl bg-ink px-6 py-10 text-white md:px-12 md:py-14">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(15,92,87,0.45),transparent_45%),radial-gradient(circle_at_90%_80%,rgba(176,141,87,0.28),transparent_40%)]"></div>
        <div class="relative grid gap-8 lg:grid-cols-[1.4fr_1fr] lg:items-center">
          <div class="space-y-4">
            <p class="inline-flex items-center gap-2 text-sm text-metal">
              <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
              {{ __($a.'.identity_badge') }}
            </p>
            <h2 class="text-balance text-2xl font-semibold md:text-4xl">{{ $isRtl ? 'قل ما تحتاجه… وسنرتّب لك مساراً واضحاً للتعلّم' : 'Tell us your goal — we’ll map a clear learning path' }}</h2>
            <p class="max-w-xl text-sm leading-8 text-white/70 md:text-base">{{ $isRtl ? 'تقييم مجاني لمدة 30 دقيقة يقلّل التخمين: نحدد مستواك، نرشّح الكورس المناسب، ونوضّح خطواتك التالية — بدون واجهة مزدحمة.' : 'A free 30-minute assessment cuts the guesswork: we place your level, recommend the right course, and outline next steps.' }}</p>
          </div>
          <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
            <button type="button" data-open-free-trial class="btn-press inline-flex h-12 sm:h-14 items-center justify-center rounded-xl bg-accent px-7 font-medium transition hover:bg-[#0d4f4a]">{{ __($a.'.free_trial_cta') }}</button>
            <a href="{{ route('public.courses') }}" class="btn-press inline-flex h-12 sm:h-14 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-7 font-medium transition hover:bg-white/10">{{ __('landing.view_all_courses') }}</a>
          </div>
        </div>
      </div>
    </section>

    {{-- 3. Categories --}}
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'اكتشف بسرعة' : 'Discover fast' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'تصنيفات تأخذك مباشرة لما تبحث عنه' : 'Categories that take you straight to what you need' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'مسارات بصرية واضحة تقلل التردد وتقرّبك من الكورس المناسب بعدد نقرات أقل.' : 'Clear visual paths reduce hesitation and get you to the right course faster.' }}</p>
        </div>
        <a href="{{ route('public.categories') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ $isRtl ? 'كل التصنيفات' : 'All categories' }}</a>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($catTiles as $tile)
        <a href="{{ route('public.courses', ['q' => $tile['q']]) }}" class="group relative min-h-56 sm:min-h-72 overflow-hidden rounded-2xl card-lift">
          <img src="{{ $tile['img'] }}" alt="{{ $tile['label'] }}" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy">
          <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/25 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 space-y-1 p-5 text-white">
            <h3 class="text-xl font-semibold">{{ $tile['label'] }}</h3>
            <p class="text-sm text-white/75">{{ $tile['count'] }} {{ $isRtl ? 'كورساً' : 'courses' }}</p>
          </div>
        </a>
        @endforeach
      </div>
    </section>

    {{-- 4. Featured Collections = learning paths --}}
    <section class="bg-surface py-20 md:py-24">
      <div class="container-wide">
        <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
          <div class="max-w-2xl space-y-3">
            <p class="text-sm font-medium text-accent">{{ __($a.'.paths_kicker') }}</p>
            <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __($a.'.stream_paths_series') }}</h2>
            <p class="text-base leading-8 text-muted">{{ __($a.'.stream_paths_sub') }}</p>
          </div>
          <a href="{{ route('public.learning-paths.index') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.view_all_paths') }}</a>
        </div>
        <div class="grid gap-5 lg:grid-cols-3">
          @foreach($pathsList->take(3) as $path)
            @php
              $pImg = $path->image_url ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=80';
              $pathUrl = $path->url ?? (isset($path->slug) && $path->slug !== '' ? route('public.learning-path.show', $path->slug) : route('public.learning-paths.index'));
            @endphp
            <article class="group relative min-h-80 sm:min-h-96 overflow-hidden rounded-2xl card-lift">
              <img src="{{ $pImg }}" alt="{{ $path->name }}" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy">
              <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/40 to-transparent"></div>
              <div class="absolute inset-x-0 bottom-0 space-y-3 p-5 sm:p-6 text-white">
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $path->name }}</h3>
                <p class="text-sm leading-7 text-white/75">{{ \Illuminate\Support\Str::limit(strip_tags((string)($path->description ?? '')), 90) ?: __($a.'.stream_badge_series') }}</p>
                <a href="{{ $pathUrl }}" class="btn-press inline-flex h-10 items-center rounded-xl bg-white px-5 text-sm font-medium text-ink transition hover:bg-canvas">{{ __($a.'.path_continue') }}</a>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- 5. Best sellers / trending --}}
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ __($a.'.row_trending_now') }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'كورسات أثبتت جدارتها عند المتعلمين' : 'Courses learners trust most' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'بطاقات واضحة مع تقييم وسعر وتسجيل سريع — لتقليل الخطوات حتى بدء التعلّم.' : 'Clear cards with rating, price, and quick enroll — fewer steps to start learning.' }}</p>
        </div>
        <a href="{{ route('public.courses') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.view_all_courses') }}</a>
      </div>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @forelse($rowTrendingNow->take(4) as $i => $course)
          @include('partials.landing-course-card-site', ['course' => $course, 'badge' => $i === 0 ? ($isRtl ? 'الأكثر طلباً' : 'Best seller') : null])
        @empty
          <p class="col-span-full text-center text-muted py-10">{{ __('public.no_courses_landing') }}</p>
        @endforelse
      </div>
    </section>

    {{-- 6. New arrivals --}}
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ __($a.'.row_new_releases') }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'إضافات جديدة تستحق نظرة أولى' : 'Fresh releases worth a first look' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'اكتشاف مستمر لكورسات حديثة مع الحفاظ على نفس معايير الجودة والوضوح.' : 'Ongoing discovery of new courses with the same quality bar.' }}</p>
        </div>
        <a href="{{ route('public.courses', ['sort' => 'newest']) }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.view_all_courses') }}</a>
      </div>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @foreach($rowNew->take(4) as $course)
          @include('partials.landing-course-card-site', ['course' => $course, 'badge' => $isRtl ? 'جديد' : 'New'])
        @endforeach
      </div>
    </section>

    {{-- 7. Flash deals --}}
    @if($flashCourses->isNotEmpty())
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'لفترة محدودة' : 'Limited time' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'عروض سريعة بمقاعد وحدود واضحة' : 'Flash offers with clear seat limits' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'عداد ومقاعد ظاهرة تقلّل التردد و تزيد وضوح القرار — دون ضوضاء بصرية.' : 'Visible countdown and seats reduce hesitation — without visual noise.' }}</p>
        </div>
        <a href="{{ route('public.courses') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ $isRtl ? 'كل العروض' : 'All offers' }}</a>
      </div>
      <div class="grid gap-5 lg:grid-cols-2">
        @foreach($flashCourses as $fi => $course)
          @php
            $fUrl = route('public.course.show', $course->id);
            $fThumb = $course->thumbnail_url ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80';
            $fPrice = $course->is_free ? 0 : (float)($course->price_after_discount ?? $course->price ?? 0);
            $fOld = (float)($course->price ?? 0);
            $seats = 8 + ($fi * 6);
            $dealId = 'flash-deal-'.($fi + 1);
          @endphp
          <article id="{{ $dealId }}" data-ends-at="" class="card-lift grid overflow-hidden rounded-2xl bg-surface shadow-soft md:grid-cols-[1.1fr_1fr]">
            <a href="{{ $fUrl }}" class="group relative min-h-52 sm:min-h-64 md:min-h-full overflow-hidden">
              <img src="{{ $fThumb }}" alt="{{ $course->title }}" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy">
            </a>
            <div class="flex flex-col justify-center gap-3 sm:gap-4 p-5 sm:p-6 md:p-8">
              <span class="inline-flex w-fit items-center rounded-lg bg-[#fef3f2] px-2.5 py-1 text-xs font-medium text-danger">{{ $isRtl ? 'عرض محدود' : 'Limited offer' }}</span>
              <h3 class="text-lg sm:text-xl font-semibold text-ink md:text-2xl"><a href="{{ $fUrl }}" class="hover:text-accent">{{ $course->title }}</a></h3>
              <p class="text-base font-semibold text-ink">
                @if($course->is_free)
                  {{ __('landing.free') }}
                @else
                  {{ number_format($fPrice, 0) }} {{ __('landing.currency') }}
                  @if($fOld > $fPrice)
                    <span class="text-sm font-normal text-muted line-through">{{ number_format($fOld, 0) }} {{ __('landing.currency') }}</span>
                  @endif
                @endif
              </p>
              <div class="flash-countdown flex gap-2" aria-label="{{ $isRtl ? 'الوقت المتبقي' : 'Time left' }}">
                <div class="min-w-14 sm:min-w-16 rounded-xl bg-canvas px-2 sm:px-3 py-2 text-center"><p id="{{ $dealId }}-hours" class="text-base sm:text-lg font-semibold tabular-nums text-ink">00</p><p class="text-[11px] text-muted">{{ $isRtl ? 'ساعة' : 'hrs' }}</p></div>
                <div class="min-w-14 sm:min-w-16 rounded-xl bg-canvas px-2 sm:px-3 py-2 text-center"><p id="{{ $dealId }}-mins" class="text-base sm:text-lg font-semibold tabular-nums text-ink">00</p><p class="text-[11px] text-muted">{{ $isRtl ? 'دقيقة' : 'min' }}</p></div>
                <div class="min-w-14 sm:min-w-16 rounded-xl bg-canvas px-2 sm:px-3 py-2 text-center"><p id="{{ $dealId }}-secs" class="text-base sm:text-lg font-semibold tabular-nums text-ink">00</p><p class="text-[11px] text-muted">{{ $isRtl ? 'ثانية' : 'sec' }}</p></div>
              </div>
              <p class="text-sm text-muted">{{ $isRtl ? 'تبقّى' : 'Only' }} <span class="font-semibold text-ink">{{ $seats }}</span> {{ $isRtl ? 'مقعداً فقط' : 'seats left' }}</p>
              <a href="{{ $fUrl }}" class="btn-press inline-flex h-11 sm:h-12 w-full sm:w-fit items-center justify-center rounded-xl bg-accent px-6 font-medium text-white transition hover:bg-[#0d4f4a]">{{ $isRtl ? 'اغتنم العرض' : 'Claim offer' }}</a>
            </div>
          </article>
        @endforeach
      </div>
    </section>
    @endif

    {{-- 8. Instructors as trusted brands --}}
    <section class="bg-surface py-20 md:py-24">
      <div class="container-wide">
        <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
          <div class="max-w-2xl space-y-3">
            <p class="text-sm font-medium text-accent">{{ __($a.'.instructors_kicker') }}</p>
            <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __($a.'.instructors_title') }}</h2>
            <p class="text-base leading-8 text-muted">{{ __($a.'.instructors_sub') }}</p>
          </div>
          <a href="{{ route('public.instructors.index') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ $isRtl ? 'عرض الكل' : 'View all' }}</a>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
          @forelse(($homeInstructors ?? collect())->take(8) as $p)
            <a href="{{ route('public.instructors.show', $p->user) }}" class="flex h-24 items-center justify-center rounded-2xl border border-line bg-canvas px-2 text-center text-sm font-semibold text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ \Illuminate\Support\Str::limit($p->user->name ?? '—', 18) }}</a>
          @empty
            @foreach([$isRtl?'مدرب معتمد':'Certified tutor',$isRtl?'ناطق أصلي':'Native speaker',$isRtl?'خبير اختبارات':'Exam expert',$isRtl?'لغة للأطفال':'Kids specialist'] as $label)
              <span class="flex h-24 items-center justify-center rounded-2xl border border-line bg-canvas text-sm font-semibold text-ink-soft">{{ $label }}</span>
            @endforeach
          @endforelse
        </div>
      </div>
    </section>

    {{-- 9. Recommended --}}
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ __($a.'.row_recommended') }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'اقتراحات مخصّصة لمسار تعلّمك' : 'Suggestions tailored to your learning path' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'اختيارات جاهزة تبدأ من هدفك وتتطور مع مستواك.' : 'Ready picks that start from your goal and grow with your level.' }}</p>
        </div>
        <a href="{{ route('public.courses') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ __('landing.view_all_courses') }}</a>
      </div>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @foreach($rowRecommended->take(4) as $course)
          @include('partials.landing-course-card-site', ['course' => $course])
        @endforeach
      </div>
    </section>

    {{-- 10. Inspiration gallery --}}
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'معرض إلهام' : 'Inspiration' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'قصص تعلّم تساعدك تتخيّل النتيجة' : 'Learning stories that help you picture the outcome' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'سرد بصري يربط الكورس بأسلوب حياتك — لاكتشاف أعمق دون إعلانات مزعجة.' : 'Visual storytelling that connects courses to real life — deeper discovery without noise.' }}</p>
        </div>
        <a href="{{ route('public.learning-paths.index') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ $isRtl ? 'المزيد من الإلهام' : 'More inspiration' }}</a>
      </div>
      <div class="grid gap-4 md:grid-cols-3">
        @foreach($inspo as $item)
        <a href="{{ $item['url'] }}" class="group relative min-h-64 sm:min-h-80 overflow-hidden rounded-2xl card-lift">
          <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy">
          <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/30 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 space-y-2 p-5 text-white">
            <h3 class="text-xl font-semibold">{{ $item['title'] }}</h3>
            <p class="text-sm leading-7 text-white/75">{{ $item['sub'] }}</p>
          </div>
        </a>
        @endforeach
      </div>
    </section>

    {{-- 11. Customer reviews --}}
    <section class="bg-surface py-20 md:py-24">
      <div class="container-wide">
        <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
          <div class="max-w-2xl space-y-3">
            <p class="text-sm font-medium text-accent">{{ __($a.'.testimonials_kicker') }}</p>
            <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ __($a.'.testimonials_title') }}</h2>
            <p class="text-base leading-8 text-muted">{{ __($a.'.testimonials_sub') }}</p>
          </div>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
          @foreach($reviewRows->take(3) as $t)
          <blockquote class="flex h-full flex-col gap-4 rounded-2xl border border-line bg-canvas p-6">
            <p class="flex items-center gap-1 text-sm"><svg class="size-3.5 fill-metal text-metal" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg><span class="font-medium text-ink">5.0</span></p>
            <p class="flex-1 text-sm leading-8 text-ink-soft">"{{ \Illuminate\Support\Str::limit(strip_tags((string)($t->body ?? '')), 160) }}"</p>
            <footer class="space-y-1 text-sm"><p class="font-semibold text-ink">{{ $t->author_name ?? '—' }}</p><p class="text-muted">{{ $t->role_label ?? '' }}</p></footer>
          </blockquote>
          @endforeach
        </div>
      </div>
    </section>

    {{-- 12. Benefits --}}
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'لماذا Glottical؟' : 'Why Glottical?' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'ضمانات واضحة تزيد ثقتك قبل التسجيل' : 'Clear promises that build trust before you enroll' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'كل وعد ظاهر ومفهوم — تقييم، حصص، شهادة، ودعم — لتقليل القلق وزيادة الالتزام.' : 'Every promise is clear — assessment, sessions, certificate, support.' }}</p>
        </div>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-line bg-surface p-6 shadow-soft">
          <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-accent-soft text-accent"><svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div>
          <h3 class="mb-2 text-base font-semibold text-ink">{{ $isRtl ? 'تعلّم مع أهل اللغة' : 'Learn with natives' }}</h3>
          <p class="text-sm leading-7 text-muted">{{ $isRtl ? 'مدربون معتمدون وناطقون أصليون بأسلوب حياة حقيقي.' : 'Certified tutors and native speakers with real-life style.' }}</p>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-6 shadow-soft">
          <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-accent-soft text-accent"><svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg></div>
          <h3 class="mb-2 text-base font-semibold text-ink">{{ $isRtl ? 'حجز مرن' : 'Flexible booking' }}</h3>
          <p class="text-sm leading-7 text-muted">{{ $isRtl ? 'اختر موعد الحصة بما يناسب جدولك بسهولة.' : 'Pick session times that fit your schedule.' }}</p>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-6 shadow-soft">
          <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-accent-soft text-accent"><svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg></div>
          <h3 class="mb-2 text-base font-semibold text-ink">{{ $isRtl ? 'دفع آمن' : 'Secure payment' }}</h3>
          <p class="text-sm leading-7 text-muted">{{ $isRtl ? 'بوابات موثوقة وشفافية كاملة في الأسعار.' : 'Trusted gateways and transparent pricing.' }}</p>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-6 shadow-soft">
          <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-accent-soft text-accent"><svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"/></svg></div>
          <h3 class="mb-2 text-base font-semibold text-ink">{{ $isRtl ? 'دعم بشري سريع' : 'Human support' }}</h3>
          <p class="text-sm leading-7 text-muted">{{ $isRtl ? 'فريق عربي يفهم هدفك ويرافقك خطوة بخطوة.' : 'An Arabic-speaking team that guides you step by step.' }}</p>
        </div>
      </div>
    </section>

    {{-- 13. Promo band --}}
    <section class="container-wide py-12 md:py-16">
      <div class="grid overflow-hidden rounded-3xl bg-accent text-white md:grid-cols-[1.3fr_1fr]">
        <div class="space-y-4 px-8 py-10 md:px-12 md:py-14">
          <p class="text-sm text-white/75">{{ $isRtl ? 'ابدأ رحلتك الآن' : 'Start your journey' }}</p>
          <h2 class="text-2xl font-semibold md:text-3xl">{{ __($a.'.cta_title') }}</h2>
          <p class="max-w-lg text-sm leading-8 text-white/80">{{ __($a.'.cta_sub') }}</p>
          <a href="{{ route('register') }}" class="btn-press inline-flex h-12 items-center rounded-xl bg-white px-6 font-medium text-ink transition hover:bg-canvas">{{ __($a.'.cta_button') }}</a>
        </div>
        <div class="relative hidden items-center justify-center bg-[#0c4d49] md:flex">
          <div class="h-64 w-36 rounded-[2rem] border border-white/20 bg-white/10 p-3 shadow-lift">
            <div class="flex h-full flex-col justify-between rounded-[1.4rem] bg-ink/40 p-4">
              <p class="text-lg font-bold">Glottical</p>
              <p class="text-xs leading-6 text-white/70">{{ $isRtl ? 'أكاديمية لغات عربية أصيلة على أي شاشة.' : 'An authentic language academy on any screen.' }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- 14. Newsletter / interest --}}
    <section class="container-wide py-20 md:py-24">
      <div class="rounded-3xl border border-line bg-surface px-6 py-10 shadow-soft md:px-12 md:py-14">
        <div class="mb-6 max-w-2xl space-y-3 md:mb-8">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'نشرة Glottical' : 'Glottical digest' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'اختيارات أسبوعية… بلا ضوضاء' : 'Weekly picks — without the noise' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'وصول مبكر للكورسات الجديدة، ومسارات موسمية، ونصائح تعلّم توفّر وقتك.' : 'Early access to new courses, seasonal paths, and learning tips that save time.' }}</p>
        </div>
        <form id="newsletter-form" action="{{ route('register') }}" method="get" class="flex max-w-xl flex-col gap-3 sm:flex-row sm:items-end">
          <div class="flex-1 space-y-2">
            <label for="newsletter-email" class="text-sm font-medium text-ink">{{ $isRtl ? 'بريدك الإلكتروني' : 'Your email' }}</label>
            <input id="newsletter-email" type="email" name="email" required autocomplete="email" placeholder="name@example.com" class="h-12 w-full rounded-xl border border-line bg-surface px-4 text-sm sm:min-w-72" />
          </div>
          <button type="submit" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-7 font-medium text-white transition hover:bg-[#0d4f4a]">{{ $isRtl ? 'اشترك' : 'Subscribe' }}</button>
        </form>
        <p id="newsletter-success" class="mt-4 hidden text-sm text-success" role="status">{{ $isRtl ? 'تم تسجيل اهتمامك بنجاح. شكراً لانضمامك إلى Glottical.' : 'You’re on the list. Welcome to Glottical.' }}</p>
      </div>
    </section>

    {{-- 15. FAQ --}}
    <section class="container-wide py-20 md:py-24">
      <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl space-y-3">
          <p class="text-sm font-medium text-accent">{{ $isRtl ? 'أسئلة شائعة' : 'FAQ' }}</p>
          <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">{{ $isRtl ? 'إجابات سريعة قبل أن تحتاج للدعم' : 'Quick answers before you need support' }}</h2>
          <p class="text-base leading-8 text-muted">{{ $isRtl ? 'وضوح في التسجيل، الحصص، والتقييم، والشهادات — لتقليل الاحتكاك وزيادة الثقة.' : 'Clarity on signup, sessions, assessment, and certificates.' }}</p>
        </div>
        <a href="{{ route('public.contact') }}" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">{{ $isRtl ? 'مركز المساعدة' : 'Help center' }}</a>
      </div>
      <div id="faq-accordion" class="mx-auto max-w-3xl divide-y divide-line rounded-2xl border border-line bg-surface">
        <div class="faq-item">
          <button type="button" class="faq-trigger flex w-full items-center justify-between gap-4 px-5 py-5 {{ $isRtl ? 'text-right' : 'text-left' }}" aria-expanded="true">
            <span class="text-sm font-semibold text-ink md:text-base">{{ $isRtl ? 'هل التقييم المجاني يحتاج حساباً؟' : 'Do I need an account for the free assessment?' }}</span>
            <svg class="faq-icon size-5 shrink-0 rotate-180 text-muted transition" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="faq-panel px-5 pb-5 text-sm leading-8 text-muted">{{ $isRtl ? 'لا. يمكنك حجز التقييم مباشرة، ثم إنشاء حساب لاحقاً لمتابعة كورساتك.' : 'No. Book the assessment directly, then create an account later to continue learning.' }}</div>
        </div>
        <div class="faq-item">
          <button type="button" class="faq-trigger flex w-full items-center justify-between gap-4 px-5 py-5 {{ $isRtl ? 'text-right' : 'text-left' }}" aria-expanded="false">
            <span class="text-sm font-semibold text-ink md:text-base">{{ $isRtl ? 'كم مدة الحصة؟' : 'How long is a session?' }}</span>
            <svg class="faq-icon size-5 shrink-0 text-muted transition" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="faq-panel hidden px-5 pb-5 text-sm leading-8 text-muted">{{ $isRtl ? 'التقييم المجاني 30 دقيقة. مدة كورساتك تختلف حسب الباقة والمدرب.' : 'The free assessment is 30 minutes. Course sessions vary by plan and tutor.' }}</div>
        </div>
        <div class="faq-item">
          <button type="button" class="faq-trigger flex w-full items-center justify-between gap-4 px-5 py-5 {{ $isRtl ? 'text-right' : 'text-left' }}" aria-expanded="false">
            <span class="text-sm font-semibold text-ink md:text-base">{{ $isRtl ? 'هل الحصص مباشرة أم مسجّلة؟' : 'Are sessions live or recorded?' }}</span>
            <svg class="faq-icon size-5 shrink-0 text-muted transition" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="faq-panel hidden px-5 pb-5 text-sm leading-8 text-muted">{{ $isRtl ? 'نوفّر مسارات مباشرة ومرنة حسب الكورس — التفاصيل تظهر في صفحة كل كورس.' : 'We offer live and flexible formats depending on the course — details are on each course page.' }}</div>
        </div>
        <div class="faq-item">
          <button type="button" class="faq-trigger flex w-full items-center justify-between gap-4 px-5 py-5 {{ $isRtl ? 'text-right' : 'text-left' }}" aria-expanded="false">
            <span class="text-sm font-semibold text-ink md:text-base">{{ $isRtl ? 'هل أحصل على شهادة؟' : 'Do I get a certificate?' }}</span>
            <svg class="faq-icon size-5 shrink-0 text-muted transition" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="faq-panel hidden px-5 pb-5 text-sm leading-8 text-muted">{{ $isRtl ? 'نعم لمعظم المسارات المكتملة وفق شروط الكورس المحددة.' : 'Yes for most completed paths, per each course’s requirements.' }}</div>
        </div>
      </div>
    </section>
</main>
