@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $homeStats = $homeStats ?? ['learners' => 0, 'courses' => 0, 'certificates' => 0, 'services' => 0];
    $fmt = fn (int $n) => number_format($n, 0, '.', ',');
    $a = 'landing.academy';

    $featuredList = ($featuredCourses ?? collect())->take(12);
    $courseCatalogForJs = [];
    foreach ($featuredList as $course) {
        $thumbUrl = $course->thumbnail_url ?? '';
        $instName = $course->instructor->name ?? '';
        $catName = optional($course->courseCategory)->name;
        $effective = $course->is_free ? 0.0 : (float) ($course->price_after_discount ?? $course->price ?? 0);
        $courseCatalogForJs[] = [
            'id' => (int) $course->id,
            'title' => $course->title,
            'instructor' => $instName,
            'rating' => $course->rating !== null ? round((float) $course->rating, 2) : 0,
            'level' => (string) ($course->level ?? 'beginner'),
            'price' => $effective,
            'isFree' => (bool) $course->is_free,
            'duration' => (int) ($course->duration_hours ?? 0),
            'lessons' => (int) ($course->lessons_count ?? 0),
            'category' => $catName ? \Illuminate\Support\Str::slug($catName) : '',
            'categoryName' => (string) ($catName ?? ''),
            'language' => strtolower((string) ($course->language ?? '')),
            'url' => route('public.course.show', $course->id),
            'thumb' => $thumbUrl,
        ];
    }
    $pathsList = ($landingPaths ?? collect())->take(10);
    $hasManagedPaths = $pathsList->isNotEmpty();
    if (! $hasManagedPaths) {
        $pathsList = collect([
            (object) ['name' => __($a.'.path_fallback_1'), 'description' => '', 'courses_count' => 0, 'image_url' => null, 'id' => 0, 'url' => route('public.learning-paths.index')],
            (object) ['name' => __($a.'.path_fallback_2'), 'description' => '', 'courses_count' => 0, 'image_url' => null, 'id' => 0, 'url' => route('public.learning-paths.index')],
            (object) ['name' => __($a.'.path_fallback_3'), 'description' => '', 'courses_count' => 0, 'image_url' => null, 'id' => 0, 'url' => route('public.learning-paths.index')],
        ]);
    }

    $planStarter = $teacherPlans['teacher_starter'] ?? [];
    $planPro = $teacherPlans['teacher_pro'] ?? [];
    $starterPrice = (float) ($planStarter['price'] ?? 0);
    $proPrice = (float) ($planPro['price'] ?? 0);

    $testimonialRows = ($homeTestimonials ?? collect());
    $countriesStat = 24;
    $satisfactionStat = 96;

    // Academy-oriented suggestions (avoid AI-focused examples)
    $searchSuggestions = [
        __($a.'.suggest_1'),
        __($a.'.suggest_2'),
        __($a.'.suggest_3'),
        __($a.'.suggest_4'),
        __($a.'.suggest_5'),
        __($a.'.suggest_6'),
    ];
    $trendingSearchLabels = [
        __($a.'.suggest_4'),
        __($a.'.suggest_3'),
        __($a.'.suggest_1'),
    ];

    $heroSpotlight = $heroSpotlight ?? [];
    $heroSlides = $heroSlides ?? [];
    if ($heroSlides === [] && $heroSpotlight !== []) {
        $heroSlides = collect($heroSpotlight)->pluck('bg')->filter()->values()->all();
    }
    if ($heroSlides === []) {
        $heroSlides = [
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1600&q=72',
        ];
    }

    $rowTrendingNow = $featuredList->sort(function ($a, $b) {
        $fa = (int) ($a->is_featured ?? false);
        $fb = (int) ($b->is_featured ?? false);
        if ($fa !== $fb) {
            return $fb <=> $fa;
        }

        return strtotime((string) ($b->created_at ?? '')) <=> strtotime((string) ($a->created_at ?? ''));
    })->values()->take(10);
    $rowRecommended = $featuredList->sortByDesc(fn ($c) => (int) ($c->lessons_count ?? 0))->values()->take(10);
    $rowNew = $featuredList->sortByDesc('created_at')->values()->take(10);

    // Streaming search chips for an academy (no AI chip)
    $searchChipsForJs = [
        ['id' => 'english', 'label' => __($a.'.chip_english'), 'keywords' => ['english', 'إنجليزي', 'grammar', 'speaking', 'ielts', 'toefl']],
        ['id' => 'arabic', 'label' => __($a.'.chip_arabic'), 'keywords' => ['arabic', 'عربي', 'نحو', 'بلاغة', 'إملاء', 'قراءة']],
        ['id' => 'fr', 'label' => __($a.'.chip_french'), 'keywords' => ['french', 'فرنسي', 'del f', 'delf', 'tcf']],
        ['id' => 'kids', 'label' => __($a.'.chip_kids'), 'keywords' => ['kids', 'أطفال', 'طفل', 'kids', 'مبتدئين']],
        ['id' => 'exams', 'label' => __($a.'.chip_exams'), 'keywords' => ['ielts', 'toefl', 'اختبار', 'امتحان', 'prep', 'تحضير']],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.seo-meta', [
        'title' => __('landing.meta.title'),
        'description' => __('landing.meta.description'),
        'keywords' => __('landing.meta.keywords'),
        'image' => \App\Services\SeoAssets::ogImageUrl(),
        'imageAlt' => __('landing.meta.og_title'),
        'url' => url('/'),
        'type' => 'website',
    ])
    <link rel="alternate" hreflang="ar" href="{{ url('/?lang=ar') }}">
    <link rel="alternate" hreflang="en" href="{{ url('/?lang=en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])

    @php
        $r2PublicBase = \App\Services\PlatformMediaSettings::r2PublicBaseUrl();
        $heroLcpImage = \App\Services\SeoAssets::optimizedRemoteImage($heroSlides[0] ?? null, 1400, 70);
    @endphp
    @if(!empty($heroLcpImage))
        <link rel="preload" as="image" href="{{ e($heroLcpImage) }}" fetchpriority="high">
    @endif
    @if(is_string($r2PublicBase) && $r2PublicBase !== '')
        @php $r2Host = parse_url($r2PublicBase, PHP_URL_HOST); @endphp
        @if($r2Host)
            <link rel="dns-prefetch" href="https://{{ $r2Host }}">
            <link rel="preconnect" href="https://{{ $r2Host }}" crossorigin>
        @endif
    @endif

    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    @include('partials.atheer-head')
    <meta name="theme-color" content="#0f5c57">
    <style>
        [x-cloak]{display:none!important}
        /* Free-trial modal — self-contained so it never falls back to old dark UI */
        #free-trial-modal{
            font-family:"IBM Plex Sans Arabic","Segoe UI",Tahoma,sans-serif;
        }
        #free-trial-modal.hidden{
            display:none !important;
        }
        #free-trial-modal .ft-backdrop{
            background:rgba(11,18,32,.48);
            backdrop-filter:blur(3px);
            -webkit-backdrop-filter:blur(3px);
            opacity:0;
            transition:opacity 220ms ease;
        }
        #free-trial-modal.flex .ft-backdrop,
        #free-trial-modal.ft-open .ft-backdrop{opacity:1}
        #free-trial-modal .ft-dialog{
            background:#ffffff !important;
            color:#0b1220 !important;
            border:1px solid #d7dde6 !important;
            box-shadow:0 18px 50px rgba(11,18,32,.12);
            opacity:0;
            transform:translateY(14px) scale(.985);
            transition:opacity 280ms cubic-bezier(.22,1,.36,1), transform 280ms cubic-bezier(.22,1,.36,1);
        }
        #free-trial-modal.flex .ft-dialog,
        #free-trial-modal.ft-open .ft-dialog{
            opacity:1;
            transform:translateY(0) scale(1);
        }
        #free-trial-modal .ft-head{
            border-bottom:1px solid #d7dde6;
            background:#fff;
        }
        #free-trial-modal .ft-kicker{color:#b08d57;font-size:.875rem;font-weight:500}
        #free-trial-modal .ft-title{color:#0b1220;font-size:1.25rem;font-weight:600;letter-spacing:-.01em;line-height:1.35}
        #free-trial-modal .ft-sub{color:#5b6577;font-size:.875rem;line-height:1.75}
        #free-trial-modal .ft-label{color:#0b1220;font-size:.875rem;font-weight:600}
        #free-trial-modal .ft-muted{color:#5b6577;font-size:.75rem}
        #free-trial-modal .ft-close{
            width:2.5rem;height:2.5rem;border-radius:.75rem;border:1px solid #d7dde6;
            background:#f3f5f7;color:#1c2738;display:inline-flex;align-items:center;justify-content:center;
        }
        #free-trial-modal .ft-close:hover{border-color:rgba(15,92,87,.35);background:#e6f2f1;color:#0f5c57}
        #free-trial-modal .ft-chip{
            border:1px solid #d7dde6;
            background:#f3f5f7;
            color:#1c2738;
            border-radius:.75rem;
            font-size:.8125rem;
            font-weight:600;
            padding:.55rem .85rem;
            transition:background-color 160ms ease,border-color 160ms ease,color 160ms ease;
            flex:0 0 auto;
            scroll-snap-align:start;
        }
        #free-trial-modal .ft-chip:hover{
            border-color:rgba(15,92,87,.35);
            background:#e6f2f1;
            color:#0f5c57;
        }
        #free-trial-modal .ft-chip.is-active{
            background:#0f5c57 !important;
            border-color:#0f5c57 !important;
            color:#fff !important;
        }
        #free-trial-modal .ft-chip .ft-chip-day{
            display:block;
            font-size:.6875rem;
            font-weight:500;
            opacity:.72;
            line-height:1.2;
        }
        #free-trial-modal .ft-chip .ft-chip-date{
            display:block;
            font-size:.875rem;
            font-weight:700;
            line-height:1.25;
            margin-top:.1rem;
        }
        #free-trial-modal .ft-scroll-wrap{
            position:relative;
        }
        #free-trial-modal .ft-scroll-wrap::before,
        #free-trial-modal .ft-scroll-wrap::after{
            content:"";
            position:absolute;
            top:0;bottom:0;
            width:1.75rem;
            pointer-events:none;
            z-index:2;
            opacity:0;
            transition:opacity 180ms ease;
        }
        #free-trial-modal .ft-scroll-wrap::before{
            inset-inline-start:0;
            background:linear-gradient(to left, transparent, #fff 70%);
        }
        #free-trial-modal .ft-scroll-wrap::after{
            inset-inline-end:0;
            background:linear-gradient(to right, transparent, #fff 70%);
        }
        [dir="ltr"] #free-trial-modal .ft-scroll-wrap::before{
            background:linear-gradient(to right, #fff 30%, transparent);
        }
        [dir="ltr"] #free-trial-modal .ft-scroll-wrap::after{
            background:linear-gradient(to left, #fff 30%, transparent);
        }
        #free-trial-modal .ft-scroll-wrap.has-start::before,
        #free-trial-modal .ft-scroll-wrap.has-end::after{opacity:1}
        #free-trial-modal .ft-hscroll{
            display:flex;
            gap:.5rem;
            overflow-x:auto;
            overflow-y:hidden;
            padding-bottom:.55rem;
            scroll-snap-type:x proximity;
            scroll-behavior:smooth;
            -webkit-overflow-scrolling:touch;
            overscroll-behavior-x:contain;
            scrollbar-width:thin;
            scrollbar-color:#b08d57 transparent;
        }
        #free-trial-modal .ft-hscroll::-webkit-scrollbar{height:5px}
        #free-trial-modal .ft-hscroll::-webkit-scrollbar-track{background:transparent}
        #free-trial-modal .ft-hscroll::-webkit-scrollbar-thumb{
            background:#c9b28a;
            border-radius:999px;
        }
        #free-trial-modal .ft-hscroll::-webkit-scrollbar-thumb:hover{background:#b08d57}
        #free-trial-modal .ft-scroll-btn{
            position:absolute;
            top:50%;
            transform:translateY(-60%);
            z-index:3;
            width:2rem;height:2rem;
            border-radius:.75rem;
            border:1px solid #d7dde6;
            background:#fff;
            color:#0f5c57;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 6px 16px rgba(11,18,32,.08);
            transition:opacity 160ms ease,background-color 160ms ease,border-color 160ms ease;
        }
        #free-trial-modal .ft-scroll-btn:hover{
            background:#e6f2f1;
            border-color:rgba(15,92,87,.35);
        }
        #free-trial-modal .ft-scroll-btn[disabled]{
            opacity:0;
            pointer-events:none;
        }
        #free-trial-modal .ft-scroll-btn.is-prev{inset-inline-start:.15rem}
        #free-trial-modal .ft-scroll-btn.is-next{inset-inline-end:.15rem}
        @media (max-width:639px){
            #free-trial-modal .ft-scroll-btn{display:none}
        }
        #free-trial-modal .ft-field{
            width:100%;
            height:2.75rem;
            border-radius:.75rem;
            border:1px solid #d7dde6;
            background:#fff;
            padding:0 1rem;
            font-size:.875rem;
            color:#0b1220;
        }
        #free-trial-modal .ft-field:focus{
            outline:none;
            border-color:#0f5c57;
            box-shadow:0 0 0 3px rgba(15,92,87,.15);
        }
        #free-trial-modal .ft-field::placeholder{color:#5b6577;opacity:.7}
        #free-trial-modal .ft-submit{
            width:100%;height:3rem;border-radius:.75rem;border:0;
            background:#0f5c57;color:#fff;font-size:.875rem;font-weight:600;
        }
        #free-trial-modal .ft-submit:hover{background:#0d4f4a}
        #free-trial-modal .ft-submit:disabled{opacity:.4;cursor:not-allowed}
        #free-trial-modal .ft-loading{
            align-items:center;gap:.75rem;
            border:1px solid #d7dde6;background:#f3f5f7;border-radius:1rem;
            padding:1.1rem 1rem;color:#5b6577;font-size:.875rem;
        }
        /* مهم: لا تفرض display:flex هنا — يتغلب على .hidden ويبقى التحميل ظاهراً للأبد */
        #free-trial-modal .ft-loading:not(.hidden){
            display:flex;
        }
        #free-trial-modal .ft-error{
            border:1px solid #f5c2c0;background:#fef3f2;color:#b42318;
            border-radius:1rem;padding:.75rem 1rem;font-size:.875rem;line-height:1.75;
        }
        #free-trial-modal .ft-error:not(.hidden){
            display:block;
        }
        #free-trial-modal .ft-success-icon{
            width:3.5rem;height:3.5rem;margin:0 auto 1rem;
            display:inline-flex;align-items:center;justify-content:center;
            border-radius:1rem;background:#e6f2f1;color:#0f5c57;
        }
        @media (prefers-reduced-motion: reduce){
            #free-trial-modal .ft-backdrop,
            #free-trial-modal .ft-dialog{transition:none}
        }
    </style>
</head>
<body class="font-sans antialiased">
@include('partials.atheer-home-header')

@include('partials.welcome-main-site')

{{-- Netflix-style full-screen search --}}
<div id="market-search-overlay" class="fixed inset-0 z-[100050] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-[#0d1528]/80 backdrop-blur-xl transition-opacity" data-close-search tabindex="-1"></div>
    <div class="relative z-10 min-h-0 flex flex-col items-stretch pt-16 sm:pt-20 px-3 sm:px-6 pb-8 pointer-events-none">
        <div class="max-w-5xl w-full mx-auto glass-panel-dark rounded-2xl border border-white/15 shadow-2xl pointer-events-auto search-overlay-enter max-h-[min(90vh,840px)] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between gap-3 p-4 border-b border-white/10">
                <p class="text-sm font-black text-metal">{{ __($a.'.search_live') }}</p>
                <button type="button" class="w-10 h-10 rounded-xl border border-white/15 text-white hover:bg-white/10 transition font-bold" data-close-search aria-label="{{ __($a.'.search_close') }}"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-4 border-b border-white/10 shrink-0">
                <div class="flex items-center gap-3 rounded-2xl border border-white/12 bg-white/5 px-4 py-3 floating-search-glow">
                    <i class="fas fa-magnifying-glass text-acad-cyan/90 text-sm"></i>
                    <input type="search" id="market-overlay-input" autocomplete="off" placeholder="{{ __($a.'.search_placeholder') }}" class="flex-1 bg-transparent border-0 outline-none font-semibold text-white placeholder:text-white/40">
                </div>
                <div class="mt-4 flex flex-wrap gap-2" id="search-chip-row">
                    @foreach($searchChipsForJs as $ch)
                        <button type="button" class="search-chip text-xs font-extrabold px-3 py-1.5 rounded-full border border-white/15 bg-white/5 text-white/90 hover:bg-acad-yellow hover:text-acad-blue hover:border-acad-yellow transition" data-chip="{{ $ch['id'] }}">{{ $ch['label'] }}</button>
                    @endforeach
                </div>
                <input type="hidden" id="ov-active-chip" value="">
            </div>
            <div class="flex-1 min-h-0 overflow-y-auto p-4 space-y-6">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-extrabold text-white/45 uppercase mb-2">{{ __($a.'.search_recent') }}</p>
                            <div id="market-recent-list" class="space-y-1 text-sm font-semibold text-acad-cyan"></div>
                        </div>
                        <div>
                            <p class="text-xs font-extrabold text-white/45 uppercase mb-2">{{ __($a.'.search_trending') }}</p>
                            <div id="market-trending-list" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-extrabold text-white/45 uppercase mb-2">{{ __($a.'.search_results_row') }}</p>
                        <div id="market-live-results" class="netflix-row pb-2 -mx-1"></div>
                        <p id="market-no-results" class="hidden text-sm text-white/50 font-medium py-8 text-center">{{ __($a.'.search_no_results') }}</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('public.courses') }}" method="get" class="p-4 border-t border-white/10 bg-black/20 shrink-0 flex flex-wrap gap-2 justify-end">
                <input type="hidden" name="q" id="market-overlay-q-hidden" value="">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-accent text-white font-extrabold hover:brightness-110 transition">{{ __($a.'.search_btn') }}</button>
            </form>
        </div>
    </div>
</div>

{{-- Free trial booking modal — light storefront brand (self-styled) --}}
<div id="free-trial-modal" class="fixed inset-0 z-[100070] hidden items-end sm:items-center justify-center p-0 sm:p-4" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ft-title">
    <div class="ft-backdrop absolute inset-0" data-close-free-trial></div>
    <div class="ft-dialog relative z-10 flex w-full sm:max-w-lg flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl max-h-[min(94vh,100dvh)]">
        <div class="ft-head flex items-start justify-between gap-3 px-5 py-4 sm:px-6 sm:py-5">
            <div class="min-w-0 space-y-1.5">
                <p class="ft-kicker inline-flex items-center gap-1.5">
                    <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    Glottical · 30 {{ $isRtl ? 'دقيقة' : 'min' }}
                </p>
                <h3 id="ft-title" class="ft-title sm:text-2xl">{{ __($a.'.free_trial_modal_title') }}</h3>
                <p class="ft-sub">{{ __($a.'.free_trial_modal_sub') }}</p>
            </div>
            <button type="button" class="ft-close shrink-0" data-close-free-trial aria-label="{{ __($a.'.free_trial_close') }}">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-5 sm:px-6 sm:py-6" style="background:#fff">
            <div id="ft-loading" class="ft-loading">
                <span class="inline-flex size-8 items-center justify-center rounded-xl" style="background:#e6f2f1;color:#0f5c57">
                    <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                </span>
                {{ __($a.'.free_trial_loading') }}
            </div>

            <div id="ft-error" class="ft-error mb-4 hidden" role="alert"></div>

            <div id="ft-calendar" class="hidden space-y-6">
                <div>
                    <div class="mb-3 flex items-end justify-between gap-3">
                        <p class="ft-label">{{ __($a.'.free_trial_pick_date') }}</p>
                        <p class="ft-muted">{{ $isRtl ? 'خلال أسبوعين' : 'Next 2 weeks' }}</p>
                    </div>
                    <div class="ft-scroll-wrap" data-ft-scroll-wrap>
                        <button type="button" class="ft-scroll-btn is-prev" data-ft-scroll="-1" aria-label="{{ $isRtl ? 'الأيام السابقة' : 'Previous days' }}" disabled>
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="{{ $isRtl ? 'm9 18 6-6-6-6' : 'm15 18-6-6 6-6' }}"/></svg>
                        </button>
                        <div id="ft-dates" class="ft-hscroll" role="listbox" aria-label="{{ __($a.'.free_trial_pick_date') }}"></div>
                        <button type="button" class="ft-scroll-btn is-next" data-ft-scroll="1" aria-label="{{ $isRtl ? 'الأيام التالية' : 'Next days' }}" disabled>
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="{{ $isRtl ? 'm15 18-6-6 6-6' : 'm9 18 6-6-6-6' }}"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <p class="ft-label mb-3">{{ __($a.'.free_trial_pick_time') }}</p>
                    <div id="ft-times" class="flex flex-wrap gap-2"></div>
                    <p id="ft-no-times" class="ft-sub mt-2 hidden">{{ __($a.'.free_trial_no_slots') }}</p>
                </div>

                <form id="ft-form" class="space-y-4 pt-5" style="border-top:1px solid #d7dde6">
                    <input type="hidden" name="starts_at" id="ft-starts-at" required>
                    <div class="space-y-2">
                        <label for="ft-name" class="ft-label block">{{ __($a.'.free_trial_name') }}</label>
                        <input type="text" name="name" id="ft-name" required autocomplete="name" class="ft-field" value="{{ auth()->user()->name ?? '' }}">
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="ft-email" class="ft-label block">{{ __($a.'.free_trial_email') }}</label>
                            <input type="email" name="email" id="ft-email" autocomplete="email" class="ft-field" value="{{ auth()->user()->email ?? '' }}">
                        </div>
                        <div class="space-y-2">
                            <label for="ft-phone" class="ft-label block">{{ __($a.'.free_trial_phone') }}</label>
                            <input type="tel" name="phone" id="ft-phone" autocomplete="tel" class="ft-field" value="{{ auth()->user()->phone ?? '' }}">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="ft-goal" class="ft-label block">{{ __($a.'.free_trial_goal') }}</label>
                        <input type="text" name="goal" id="ft-goal" class="ft-field" placeholder="{{ $isRtl ? 'سفر، عمل، دراسة…' : 'Travel, work, study…' }}">
                    </div>
                    <button type="submit" id="ft-submit" disabled class="ft-submit">
                        {{ __($a.'.free_trial_submit') }}
                    </button>
                    <p class="text-center ft-muted" style="line-height:1.6">{{ $isRtl ? 'بدون التزام · سنؤكد الموعد برسالة قصيرة' : 'No commitment · We’ll confirm by a short message' }}</p>
                </form>
            </div>

            <div id="ft-success" class="hidden py-6 text-center">
                <div class="ft-success-icon">
                    <svg class="size-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h4 class="ft-title">{{ __($a.'.free_trial_success') }}</h4>
                <p id="ft-success-msg" class="ft-sub mx-auto mt-2 max-w-sm"></p>
                <button type="button" data-close-free-trial class="ft-close mt-6" aria-label="{{ __($a.'.free_trial_close') }}">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Quick view modal --}}
<div id="quick-view-modal" class="fixed inset-0 z-[100060] hidden items-center justify-center p-4" aria-hidden="true">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-md" data-close-qv></div>
    <div class="relative z-10 w-full max-w-lg rounded-2xl bg-slate-900/95 shadow-2xl border border-white/10 overflow-hidden max-h-[90vh] overflow-y-auto">
        <button type="button" class="absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} w-10 h-10 rounded-full bg-white/10 border border-white/15 text-white hover:bg-white/20 z-10" data-close-qv aria-label="{{ __($a.'.search_close') }}"><i class="fas fa-times"></i></button>
        <div id="quick-view-body" class="p-6 pt-14 text-white"></div>
    </div>
</div>

@include('partials.atheer-home-footer')

@if(isset($popupAd) && $popupAd)
    @include('partials.popup-ad', ['ad' => $popupAd])
@endif

<script>
(function(){
    'use strict';
    var isRtl = document.documentElement.dir === 'rtl';
    var suggests = @json($searchSuggestions);
    var trendingLabels = @json($trendingSearchLabels);
    var courseCatalog = @json($courseCatalogForJs);
    var searchChips = @json($searchChipsForJs);
    var currencyLabel = @json(__('landing.currency'));
    var freeLabel = @json(__('landing.free'));
    var enrollLabel = @json(__('landing.academy.modal_enroll'));
    var streamPlayLabel = @json(__($a.'.stream_play'));

    function esc(s){
        var d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
    }

    function reveal(){
        var els = document.querySelectorAll('.reveal');
        if (!els.length) return;
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(e){
                if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
        els.forEach(function(el){ io.observe(el); });
    }

    function heroParallax(){
        var els = document.querySelectorAll('.hero-parallax');
        if (!els.length) return;
        window.addEventListener('scroll', function(){
            var y = window.pageYOffset || document.documentElement.scrollTop;
            els.forEach(function(el, i){
                var f = i % 2 === 0 ? 0.07 : 0.05;
                el.style.transform = 'translateY(' + (y * f) + 'px)';
            });
        }, { passive: true });
    }

    function heroSlider(){
        var slides = document.querySelectorAll('.hero-slide');
        var dotsWrap = document.querySelector('.hero-dots');
        if (!slides.length || !dotsWrap) return;
        var i = 0, t = null;
        slides.forEach(function(_, idx){
            var b = document.createElement('button');
            b.type = 'button';
            b.setAttribute('aria-label', 'slide ' + (idx+1));
            if (idx === 0) b.classList.add('is-active');
            b.addEventListener('click', function(){ go(idx); reset(); });
            dotsWrap.appendChild(b);
        });
        var dots = dotsWrap.querySelectorAll('button');
        function go(n){
            i = n;
            slides.forEach(function(s, j){
                var on = j === i;
                s.classList.toggle('is-active', on);
                s.setAttribute('aria-hidden', on ? 'false' : 'true');
            });
            dots.forEach(function(d, j){ d.classList.toggle('is-active', j === i); });
        }
        function next(){ go((i + 1) % slides.length); }
        function reset(){ clearInterval(t); t = setInterval(next, 6500); }
        reset();
    }

    function searchSuggest(){
        var input = document.getElementById('academy-search-input');
        var box = document.getElementById('search-suggestions');
        if (!input || !box) return;
        function render(q){
            if (!q || q.length < 1) { box.classList.add('hidden'); box.innerHTML = ''; return; }
            var m = suggests.filter(function(s){ return s.toLowerCase().indexOf(q.toLowerCase()) !== -1; });
            if (!m.length) m = suggests.slice(0, 4);
            box.innerHTML = '<p class="text-xs font-bold text-white/45 px-2 py-1">' + (isRtl ? 'اقتراحات' : 'Suggestions') + '</p>' + m.map(function(s, idx){
                return '<button type="button" class="suggest-item w-full text-start px-3 py-2 rounded-lg hover:bg-white/10 text-sm font-semibold text-white/95" style="animation-delay:' + (idx*50) + 'ms">' + esc(s) + '</button>';
            }).join('');
            box.classList.remove('hidden');
            box.querySelectorAll('button').forEach(function(btn){
                btn.addEventListener('click', function(){ input.value = btn.textContent; box.classList.add('hidden'); });
            });
        }
        input.addEventListener('input', function(){ render(input.value.trim()); });
        input.addEventListener('focus', function(){ if (input.value.trim()) render(input.value.trim()); });
        document.addEventListener('click', function(e){
            var form = document.getElementById('academy-search-form');
            if (form && !form.contains(e.target)) box.classList.add('hidden');
        });
    }

    function recentStorage(){
        var key = 'academy_search_recent';
        return {
            get: function(){
                try { var raw = localStorage.getItem(key); return raw ? JSON.parse(raw) : []; } catch (e) { return []; }
            },
            add: function(q){
                if (!q || q.length < 2) return;
                var a = this.get().filter(function(x){ return x !== q; });
                a.unshift(q);
                localStorage.setItem(key, JSON.stringify(a.slice(0, 5)));
            }
        };
    }

    function chipKeywords(chipId){
        if (!chipId) return null;
        var ch = searchChips.filter(function(x){ return x.id === chipId; })[0];
        return ch && ch.keywords ? ch.keywords : null;
    }

    function matchesChipKeywords(c, keywords){
        if (!keywords || !keywords.length) return true;
        var blob = ((c.title || '') + ' ' + (c.instructor || '') + ' ' + (c.categoryName || '') + ' ' + (c.category || '')).toLowerCase();
        return keywords.some(function(k){ return blob.indexOf(String(k).toLowerCase()) !== -1; });
    }

    function filterCatalog(q, chipId){
        var ql = (q || '').toLowerCase();
        var kws = chipKeywords(chipId);
        return courseCatalog.filter(function(c){
            if (!matchesChipKeywords(c, kws)) return false;
            if (!ql) return true;
            return (c.title && c.title.toLowerCase().indexOf(ql) !== -1) ||
                (c.instructor && c.instructor.toLowerCase().indexOf(ql) !== -1) ||
                (c.categoryName && c.categoryName.toLowerCase().indexOf(ql) !== -1);
        });
    }

    function renderLiveResults(container, list){
        if (!container) return;
        var no = document.getElementById('market-no-results');
        container.innerHTML = '';
        var top = list.slice(0, 14);
        if (!top.length) {
            if (no) { no.classList.remove('hidden'); }
            return;
        }
        if (no) no.classList.add('hidden');
        top.forEach(function(c){
            var dur = c.duration ? (String(c.duration) + (isRtl ? ' س' : 'h')) : '';
            var rt = c.rating ? Number(c.rating).toFixed(1) : '';
            var el = document.createElement('a');
            el.href = c.url;
            el.className = 'netflix-item group flex-shrink-0 w-36 sm:w-40 block rounded-xl overflow-hidden border border-white/10 bg-white/5 hover:border-acad-yellow/55 hover:shadow-[0_0_28px_-6px_rgba(245,184,0,0.4)] hover:scale-[1.04] transition duration-300';
            el.innerHTML = '<div class="relative aspect-[2/3] bg-slate-900/85">' +
                (c.thumb ? '<img src="' + esc(c.thumb) + '" alt="" class="w-full h-full object-cover">' : '<div class="w-full h-full flex items-center justify-center text-white/20"><i class="fas fa-play-circle text-3xl"></i></div>') +
                '<div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>' +
                (dur ? '<span class="absolute top-2 ' + (isRtl ? 'right-2' : 'left-2') + ' text-[10px] font-extrabold px-1.5 py-0.5 rounded-md bg-black/60 text-white/95 border border-white/10">' + esc(dur) + '</span>' : '') +
                '<div class="absolute bottom-0 inset-x-0 p-2">' +
                '<p class="text-[11px] sm:text-xs font-black text-white leading-snug line-clamp-2">' + esc(c.title) + '</p>' +
                (rt ? '<p class="text-[10px] text-amber-300/95 mt-0.5 font-bold">★ ' + esc(rt) + '</p>' : '') +
                '</div>' +
                '<div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-black/45">' +
                '<span class="px-3 py-1.5 rounded-lg bg-accent text-white text-[10px] sm:text-[11px] font-extrabold shadow-lg">' + esc(streamPlayLabel) + '</span>' +
                '</div></div>';
            container.appendChild(el);
        });
    }

    function marketSearchOverlay(){
        var overlay = document.getElementById('market-search-overlay');
        var anchor = document.getElementById('academy-search-anchor');
        var ovInput = document.getElementById('market-overlay-input');
        var mainInput = document.getElementById('academy-search-input');
        var hiddenQ = document.getElementById('market-overlay-q-hidden');
        var recentEl = document.getElementById('market-recent-list');
        var trendEl = document.getElementById('market-trending-list');
        var liveEl = document.getElementById('market-live-results');
        var ovChipHidden = document.getElementById('ov-active-chip');
        var rec = recentStorage();
        if (!overlay || !ovInput) return;

        function isSearchBarVisible(){
            return !!(anchor && !anchor.classList.contains('hidden'));
        }

        function showSearchBar(){
            if (!anchor) return;
            anchor.classList.remove('hidden');
            anchor.classList.add('search-bar-visible');
            anchor.setAttribute('aria-hidden', 'false');
            setTimeout(function(){
                anchor.classList.remove('search-bar-visible');
                if (mainInput) mainInput.focus();
            }, 300);
        }

        function hideSearchBar(){
            if (!anchor) return;
            anchor.classList.add('hidden');
            anchor.classList.remove('search-bar-visible');
            anchor.setAttribute('aria-hidden', 'true');
            var suggestBox = document.getElementById('search-suggestions');
            if (suggestBox) suggestBox.classList.add('hidden');
        }

        function paintRecent(){
            if (!recentEl) return;
            recentEl.innerHTML = '';
            rec.get().forEach(function(q){
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'block w-full text-start px-2 py-1.5 rounded-lg hover:bg-white/10 text-sm text-white/90';
                b.textContent = q;
                b.addEventListener('click', function(){ ovInput.value = q; syncLive(); });
                recentEl.appendChild(b);
            });
        }

        function paintTrending(){
            if (!trendEl) return;
            trendEl.innerHTML = '';
            trendingLabels.forEach(function(t){
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'text-xs font-bold px-3 py-1.5 rounded-full bg-acad-blue/10 text-acad-blue hover:bg-acad-yellow hover:text-acad-blue transition';
                b.textContent = t;
                b.addEventListener('click', function(){ ovInput.value = t; syncLive(); });
                trendEl.appendChild(b);
            });
        }

        function syncChipsVisual(){
            var cur = ovChipHidden ? ovChipHidden.value : '';
            document.querySelectorAll('#market-search-overlay .search-chip').forEach(function(b){
                var on = !!(cur && b.getAttribute('data-chip') === cur);
                b.classList.toggle('bg-accent', on);
                b.classList.toggle('text-white', on);
                b.classList.toggle('border-accent', on);
                b.classList.toggle('bg-white/5', !on);
                b.classList.toggle('text-white/90', !on);
                b.classList.toggle('border-white/15', !on);
            });
        }

        function bindChips(){
            document.querySelectorAll('#market-search-overlay .search-chip').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var id = btn.getAttribute('data-chip');
                    if (!ovChipHidden) return;
                    ovChipHidden.value = (ovChipHidden.value === id) ? '' : id;
                    syncChipsVisual();
                    syncLive();
                });
            });
        }

        function syncLive(){
            var q = ovInput.value.trim();
            if (hiddenQ) hiddenQ.value = q;
            if (mainInput) mainInput.value = q;
            var chipId = ovChipHidden ? ovChipHidden.value : '';
            renderLiveResults(liveEl, filterCatalog(q, chipId));
        }

        function openOverlay(){
            overlay.classList.remove('hidden');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            paintRecent();
            paintTrending();
            syncChipsVisual();
            syncLive();
            setTimeout(function(){ ovInput.focus(); }, 50);
        }

        function closeOverlay(){
            overlay.classList.add('hidden');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        bindChips();
        document.querySelectorAll('[data-open-search-btn]').forEach(function(osb) {
            osb.addEventListener('click', function(e){
                e.preventDefault();
                if (osb.closest('#academy-search-form')) {
                    openOverlay();
                    return;
                }
                if (window.matchMedia('(min-width: 640px)').matches) {
                    isSearchBarVisible() ? hideSearchBar() : showSearchBar();
                } else {
                    openOverlay();
                }
            });
        });
        document.addEventListener('glottical:open-search', function(){
            if (window.matchMedia('(min-width: 640px)').matches) {
                showSearchBar();
            } else {
                openOverlay();
            }
        });
        document.addEventListener('click', function(e){
            if (!isSearchBarVisible()) return;
            if (anchor.contains(e.target)) return;
            if (e.target.closest('#navbar [data-open-search-btn]')) return;
            hideSearchBar();
        });

        overlay.querySelectorAll('[data-close-search]').forEach(function(el){
            el.addEventListener('click', closeOverlay);
        });
        document.addEventListener('keydown', function(e){
            if (e.key !== 'Escape') return;
            if (!overlay.classList.contains('hidden')) closeOverlay();
            else if (isSearchBarVisible()) hideSearchBar();
        });

        ovInput.addEventListener('input', syncLive);

        var mainForm = document.getElementById('academy-search-form');
        if (mainForm) {
            mainForm.addEventListener('submit', function(){
                var v = mainInput ? mainInput.value.trim() : '';
                if (v) rec.add(v);
            });
        }
        var ovForm = overlay.querySelector('form');
        if (ovForm) {
            ovForm.addEventListener('submit', function(){
                var v = ovInput.value.trim();
                if (v) rec.add(v);
            });
        }
    }

    function quickViewModal(){
        var modal = document.getElementById('quick-view-modal');
        var body = document.getElementById('quick-view-body');
        if (!modal || !body) return;
        var byId = {};
        courseCatalog.forEach(function(c){ byId[String(c.id)] = c; });

        function close(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
        }

        function open(id){
            var c = byId[String(id)];
            if (!c) return;
            var price = c.isFree ? freeLabel : (Math.round(c.price).toLocaleString() + ' ' + currencyLabel);
            var stars = c.rating >= 4 ? '★★★★★' : (c.rating >= 3 ? '★★★★☆' : '★★★☆☆');
            body.innerHTML = '<div class="aspect-video rounded-xl overflow-hidden bg-white/5 border border-white/10 mb-4">' +
                (c.thumb ? '<img src="' + esc(c.thumb) + '" alt="" class="w-full h-full object-cover">' : '') +
                '</div><h3 class="text-xl font-black text-white">' + esc(c.title) + '</h3>' +
                '<p class="text-sm text-white/55 mt-1">' + esc(c.instructor) + '</p>' +
                '<p class="text-amber-300 text-sm mt-2">' + esc(stars) + ' ' + (c.rating || '—') + '</p>' +
                '<p class="text-lg font-black text-acad-cyan mt-3">' + esc(price) + '</p>' +
                '<a href="' + esc(c.url) + '" class="mt-4 block text-center py-3 rounded-xl bg-accent text-white font-extrabold hover:brightness-110 transition">' + esc(enrollLabel) + '</a>';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
        }

        document.addEventListener('click', function(e){
            var b = e.target.closest('[data-quick-view]');
            if (b) {
                e.preventDefault();
                open(b.getAttribute('data-quick-view'));
            }
        });
        modal.querySelectorAll('[data-close-qv]').forEach(function(el){
            el.addEventListener('click', close);
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && modal.classList.contains('flex')) close();
        });
    }

    function counters(){
        var els = document.querySelectorAll('.counter');
        if (!els.length) return;
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
                if (!entry.isIntersecting) return;
                var el = entry.target;
                var target = parseInt(el.getAttribute('data-target'), 10) || 0;
                var suffix = el.getAttribute('data-suffix') || '';
                var dur = 1200, start = null;
                function step(ts){
                    if (!start) start = ts;
                    var p = Math.min((ts - start) / dur, 1);
                    var ease = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * ease).toLocaleString() + (p >= 1 ? suffix : '');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
                io.unobserve(el);
            });
        }, { threshold: 0.35 });
        els.forEach(function(el){ io.observe(el); });
    }

    function testimonialCarousel(){
        var track = document.getElementById('testimonial-track');
        if (!track) return;
        var slides = track.children.length;
        if (slides < 2) return;
        var idx = 0;
        function apply(){
            track.style.transform = 'translateX(' + (-idx * 100) + '%)';
        }
        document.querySelector('.test-next') && document.querySelector('.test-next').addEventListener('click', function(){ idx = (idx + 1) % slides; apply(); });
        document.querySelector('.test-prev') && document.querySelector('.test-prev').addEventListener('click', function(){ idx = (idx - 1 + slides) % slides; apply(); });
    }

    function scrollProgress(){
        var bar = document.getElementById('scroll-progress');
        if (!bar) return;
        function p(){
            var s = window.pageYOffset || document.documentElement.scrollTop;
            var h = document.documentElement.scrollHeight - window.innerHeight;
            bar.style.width = h > 0 ? (s / h * 100) + '%' : '0%';
        }
        window.addEventListener('scroll', p, { passive: true });
        p();
    }

    function freeTrialBooking(){
        var modal = document.getElementById('free-trial-modal');
        if (!modal) return;
        var slotsByDate = {};
        var selectedDate = null;
        var selectedStart = null;
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var token = csrf ? csrf.getAttribute('content') : '';

        function openModal(){
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(function(){ modal.classList.add('ft-open'); });
            loadSlots();
        }
        function closeModal(){
            modal.classList.remove('ft-open');
            setTimeout(function(){
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }, 200);
        }
        document.querySelectorAll('[data-open-free-trial]').forEach(function(btn){
            btn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
        });
        document.querySelectorAll('[data-close-free-trial]').forEach(function(btn){
            btn.addEventListener('click', closeModal);
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
        try {
            if (new URLSearchParams(window.location.search).get('open_trial') === '1') {
                openModal();
                if (window.history && window.history.replaceState) {
                    var clean = window.location.pathname + (window.location.hash || '');
                    window.history.replaceState({}, '', clean);
                }
            }
        } catch (e) {}

        function loadSlots(){
            var loadingEl = document.getElementById('ft-loading');
            var calendarEl = document.getElementById('ft-calendar');
            var errorEl = document.getElementById('ft-error');
            var successEl = document.getElementById('ft-success');
            if (loadingEl) loadingEl.classList.remove('hidden');
            if (calendarEl) calendarEl.classList.add('hidden');
            if (successEl) successEl.classList.add('hidden');
            if (errorEl) {
                errorEl.classList.add('hidden');
                errorEl.textContent = '';
            }
            var ctrl = (typeof AbortController !== 'undefined') ? new AbortController() : null;
            var timeoutId = setTimeout(function(){ if (ctrl) ctrl.abort(); }, 15000);
            fetch(@json(route('public.free-trial.slots')) + '?days=14', {
                headers: { 'Accept': 'application/json' },
                signal: ctrl ? ctrl.signal : undefined,
                credentials: 'same-origin'
            })
                .then(function(r){
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function(data){
                    slotsByDate = data.slots_by_date || {};
                    var datesWrap = document.getElementById('ft-dates');
                    if (!datesWrap) return;
                    datesWrap.innerHTML = '';
                    var dates = data.dates || [];
                    dates.forEach(function(d, i){
                        var b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'ft-chip';
                        b.dataset.date = d;
                        b.setAttribute('role', 'option');
                        var parts = formatDateChip(d);
                        b.innerHTML = '<span class="ft-chip-day">' + parts.day + '</span><span class="ft-chip-date">' + parts.date + '</span>';
                        b.addEventListener('click', function(){ selectDate(d); });
                        datesWrap.appendChild(b);
                        if (i === 0) selectDate(d);
                    });
                    if (calendarEl) calendarEl.classList.remove('hidden');
                    setupDateScroller(datesWrap);
                    if (!dates.length && errorEl) {
                        errorEl.textContent = @json(__($a.'.free_trial_no_slots'));
                        errorEl.classList.remove('hidden');
                    }
                })
                .catch(function(){
                    if (errorEl) {
                        errorEl.textContent = @json($isRtl ? 'تعذّر تحميل المواعيد. حاول مرة أخرى.' : 'Could not load slots. Please try again.');
                        errorEl.classList.remove('hidden');
                    }
                })
                .finally(function(){
                    clearTimeout(timeoutId);
                    if (loadingEl) loadingEl.classList.add('hidden');
                });
        }

        function formatDateChip(isoDate){
            try {
                var dt = new Date(isoDate + 'T12:00:00');
                var day = dt.toLocaleDateString(@json($locale), { weekday: 'short' });
                var date = dt.toLocaleDateString(@json($locale), { day: 'numeric', month: 'short' });
                return { day: day, date: date };
            } catch (e) {
                return { day: '', date: isoDate };
            }
        }

        function setupDateScroller(scroller){
            var wrap = scroller.closest('[data-ft-scroll-wrap]');
            if (!wrap || wrap.dataset.ftScrollReady === '1') {
                updateScrollEdges(wrap, scroller);
                return;
            }
            wrap.dataset.ftScrollReady = '1';
            var prev = wrap.querySelector('[data-ft-scroll="-1"]');
            var next = wrap.querySelector('[data-ft-scroll="1"]');
            function sync(){ updateScrollEdges(wrap, scroller); }
            scroller.addEventListener('scroll', sync, { passive: true });
            window.addEventListener('resize', sync);
            if (prev) prev.addEventListener('click', function(){ scroller.scrollBy({ left: (@json($isRtl) ? 1 : -1) * Math.max(180, scroller.clientWidth * 0.7), behavior: 'smooth' }); });
            if (next) next.addEventListener('click', function(){ scroller.scrollBy({ left: (@json($isRtl) ? -1 : 1) * Math.max(180, scroller.clientWidth * 0.7), behavior: 'smooth' }); });
            // سحب بالماوس على سطح المكتب
            var dragging = false, startX = 0, startLeft = 0;
            scroller.addEventListener('pointerdown', function(e){
                if (e.pointerType !== 'mouse' || e.button !== 0) return;
                dragging = true;
                startX = e.clientX;
                startLeft = scroller.scrollLeft;
                scroller.setPointerCapture(e.pointerId);
            });
            scroller.addEventListener('pointermove', function(e){
                if (!dragging) return;
                scroller.scrollLeft = startLeft - (e.clientX - startX);
            });
            scroller.addEventListener('pointerup', function(){ dragging = false; });
            scroller.addEventListener('pointercancel', function(){ dragging = false; });
            requestAnimationFrame(sync);
        }

        function updateScrollEdges(wrap, scroller){
            if (!wrap || !scroller) return;
            var canScroll = scroller.scrollWidth - scroller.clientWidth > 4;
            var atStart = true;
            var atEnd = true;
            var first = scroller.firstElementChild;
            var last = scroller.lastElementChild;
            if (first && last && canScroll) {
                var box = scroller.getBoundingClientRect();
                var firstRect = first.getBoundingClientRect();
                var lastRect = last.getBoundingClientRect();
                var rtl = @json($isRtl);
                atStart = rtl
                    ? firstRect.right >= box.right - 10
                    : firstRect.left <= box.left + 10;
                atEnd = rtl
                    ? lastRect.left <= box.left + 10
                    : lastRect.right >= box.right - 10;
            }
            wrap.classList.toggle('has-start', canScroll && !atStart);
            wrap.classList.toggle('has-end', canScroll && !atEnd);
            var prev = wrap.querySelector('[data-ft-scroll="-1"]');
            var next = wrap.querySelector('[data-ft-scroll="1"]');
            if (prev) prev.disabled = !canScroll || atStart;
            if (next) next.disabled = !canScroll || atEnd;
        }

        function selectDate(d){
            selectedDate = d;
            selectedStart = null;
            document.getElementById('ft-starts-at').value = '';
            document.getElementById('ft-submit').disabled = true;
            var activeBtn = null;
            document.querySelectorAll('#ft-dates button').forEach(function(b){
                var on = b.dataset.date === d;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-selected', on ? 'true' : 'false');
                if (on) activeBtn = b;
            });
            if (activeBtn && typeof activeBtn.scrollIntoView === 'function') {
                activeBtn.scrollIntoView({ inline: 'nearest', block: 'nearest', behavior: 'smooth' });
            }
            var scroller = document.getElementById('ft-dates');
            var wrap = scroller ? scroller.closest('[data-ft-scroll-wrap]') : null;
            if (wrap) updateScrollEdges(wrap, scroller);
            var times = slotsByDate[d] || [];
            var wrapTimes = document.getElementById('ft-times');
            wrapTimes.innerHTML = '';
            document.getElementById('ft-no-times').classList.toggle('hidden', times.length > 0);
            times.forEach(function(slot){
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'ft-chip';
                b.textContent = slot.time;
                b.addEventListener('click', function(){
                    selectedStart = slot.starts_at;
                    document.getElementById('ft-starts-at').value = slot.starts_at;
                    document.getElementById('ft-submit').disabled = false;
                    wrapTimes.querySelectorAll('button').forEach(function(x){ x.classList.remove('is-active'); });
                    b.classList.add('is-active');
                });
                wrapTimes.appendChild(b);
            });
        }

        var form = document.getElementById('ft-form');
        form.addEventListener('submit', function(e){
            e.preventDefault();
            var err = document.getElementById('ft-error');
            err.classList.add('hidden');
            var payload = {
                name: document.getElementById('ft-name').value,
                email: document.getElementById('ft-email').value || null,
                phone: document.getElementById('ft-phone').value || null,
                goal: document.getElementById('ft-goal').value || null,
                starts_at: document.getElementById('ft-starts-at').value
            };
            document.getElementById('ft-submit').disabled = true;
            fetch(@json(route('public.free-trial.book')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(payload)
            }).then(function(r){ return r.json().then(function(j){ return { ok: r.ok, status: r.status, body: j }; }); })
            .then(function(res){
                if (!res.ok) {
                    err.textContent = (res.body && res.body.message) ? res.body.message : ((res.body && res.body.errors) ? Object.values(res.body.errors)[0][0] : 'فشل الحجز');
                    err.classList.remove('hidden');
                    document.getElementById('ft-submit').disabled = false;
                    return;
                }
                document.getElementById('ft-calendar').classList.add('hidden');
                document.getElementById('ft-success').classList.remove('hidden');
                document.getElementById('ft-success-msg').textContent = (res.body.message || '') + (res.body.booking && res.body.booking.label ? (' — ' + res.body.booking.label) : '');
            }).catch(function(){
                err.textContent = 'فشل الاتصال — حاول مجدداً';
                err.classList.remove('hidden');
                document.getElementById('ft-submit').disabled = false;
            });
        });
    }

    function boot(){
        reveal();
        heroParallax();
        heroSlider();
        searchSuggest();
        marketSearchOverlay();
        quickViewModal();
        counters();
        testimonialCarousel();
        scrollProgress();
        freeTrialBooking();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
})();
</script>

<script>
(function () {
  var deals = [
    { id: "flash-deal-1", hoursOffset: 14 },
    { id: "flash-deal-2", hoursOffset: 9 },
  ];
  deals.forEach(function (deal) {
    var card = document.getElementById(deal.id);
    if (!card) return;
    var endsAt = Date.now() + deal.hoursOffset * 60 * 60 * 1000;
    card.setAttribute("data-ends-at", new Date(endsAt).toISOString());
    var hoursEl = document.getElementById(deal.id + "-hours");
    var minsEl = document.getElementById(deal.id + "-mins");
    var secsEl = document.getElementById(deal.id + "-secs");
    function tick() {
      var diff = Math.max(0, endsAt - Date.now());
      var hours = Math.floor(diff / (1000 * 60 * 60));
      var mins = Math.floor((diff / (1000 * 60)) % 60);
      var secs = Math.floor((diff / 1000) % 60);
      if (hoursEl) hoursEl.textContent = String(hours).padStart(2, "0");
      if (minsEl) minsEl.textContent = String(mins).padStart(2, "0");
      if (secsEl) secsEl.textContent = String(secs).padStart(2, "0");
    }
    tick();
    setInterval(tick, 1000);
  });

  document.querySelectorAll(".faq-trigger").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var item = btn.closest(".faq-item");
      var panel = item ? item.querySelector(".faq-panel") : null;
      var icon = btn.querySelector(".faq-icon");
      var isOpen = btn.getAttribute("aria-expanded") === "true";
      document.querySelectorAll(".faq-trigger").forEach(function (other) {
        if (other === btn) return;
        other.setAttribute("aria-expanded", "false");
        var otherPanel = other.closest(".faq-item").querySelector(".faq-panel");
        var otherIcon = other.querySelector(".faq-icon");
        if (otherPanel) otherPanel.classList.add("hidden");
        if (otherIcon) otherIcon.classList.remove("rotate-180");
      });
      btn.setAttribute("aria-expanded", isOpen ? "false" : "true");
      if (panel) panel.classList.toggle("hidden", isOpen);
      if (icon) icon.classList.toggle("rotate-180", !isOpen);
    });
  });

  var form = document.getElementById("newsletter-form");
  var success = document.getElementById("newsletter-success");
  if (form && success) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      var email = document.getElementById("newsletter-email");
      if (!email || !email.value.trim()) return;
      success.classList.remove("hidden");
      var btn = form.querySelector("button[type='submit']");
      if (btn) btn.disabled = true;
      setTimeout(function () {
        window.location.href = form.getAttribute("action") || "/register";
      }, 700);
    });
  }
})();
</script>
</body>
</html>
