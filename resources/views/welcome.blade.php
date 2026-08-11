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
        ['id' => 'english', 'label' => __($a.'.chip_english'), 'keywords' => ['english', 'Ø¥Ù†Ø¬Ù„ÙŠØ²ÙŠ', 'grammar', 'speaking', 'ielts', 'toefl']],
        ['id' => 'arabic', 'label' => __($a.'.chip_arabic'), 'keywords' => ['arabic', 'Ø¹Ø±Ø¨ÙŠ', 'Ù†Ø­Ùˆ', 'Ø¨Ù„Ø§ØºØ©', 'Ø¥Ù…Ù„Ø§Ø¡', 'Ù‚Ø±Ø§Ø¡Ø©']],
        ['id' => 'fr', 'label' => __($a.'.chip_french'), 'keywords' => ['french', 'ÙØ±Ù†Ø³ÙŠ', 'del f', 'delf', 'tcf']],
        ['id' => 'kids', 'label' => __($a.'.chip_kids'), 'keywords' => ['kids', 'Ø£Ø·ÙØ§Ù„', 'Ø·ÙÙ„', 'kids', 'Ù…Ø¨ØªØ¯Ø¦ÙŠÙ†']],
        ['id' => 'exams', 'label' => __($a.'.chip_exams'), 'keywords' => ['ielts', 'toefl', 'Ø§Ø®ØªØ¨Ø§Ø±', 'Ø§Ù…ØªØ­Ø§Ù†', 'prep', 'ØªØ­Ø¶ÙŠØ±']],
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

    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    @include('partials.landing.head', ['landingCss' => ['theme']])
    <style>
        [x-cloak]{display:none!important}
        .hidden{display:none !important}
        /* Old Tailwind overlays â€” must stay off (landing CSS has no utilities) */
        #market-search-overlay,
        #quick-view-modal{display:none !important;visibility:hidden !important;pointer-events:none !important}

        /* Free-trial modal â€” fully self-contained (no Tailwind dependency) */
        #free-trial-modal{
            font-family:"Tajawal","Cairo","Segoe UI",Tahoma,sans-serif;
            position:fixed;
            inset:0;
            z-index:100070;
            display:none;
            align-items:flex-end;
            justify-content:center;
            padding:0;
            box-sizing:border-box;
        }
        @media (min-width:640px){
            #free-trial-modal{align-items:center;padding:1rem}
        }
        #free-trial-modal.hidden{display:none !important}
        #free-trial-modal.is-open,
        #free-trial-modal.flex,
        #free-trial-modal.ft-open{display:flex !important}
        #free-trial-modal .ft-backdrop{
            position:absolute;
            inset:0;
            background:rgba(11,18,32,.48);
            backdrop-filter:blur(3px);
            -webkit-backdrop-filter:blur(3px);
            opacity:0;
            transition:opacity 220ms ease;
        }
        #free-trial-modal.is-open .ft-backdrop,
        #free-trial-modal.flex .ft-backdrop,
        #free-trial-modal.ft-open .ft-backdrop{opacity:1}
        #free-trial-modal .ft-dialog{
            position:relative;
            z-index:10;
            display:flex;
            flex-direction:column;
            width:100%;
            max-width:32rem;
            max-height:min(94vh,100dvh);
            overflow:hidden;
            border-radius:1.5rem 1.5rem 0 0;
            background:#ffffff !important;
            color:#0b1220 !important;
            border:1px solid rgba(11,61,145,.12) !important;
            box-shadow:
                0 28px 64px -16px rgba(11,61,145,.35),
                0 0 0 1px rgba(255,255,255,.4) inset;
            opacity:0;
            transform:translateY(14px) scale(.985);
            transition:opacity 280ms cubic-bezier(.22,1,.36,1), transform 280ms cubic-bezier(.22,1,.36,1);
        }
        @media (min-width:640px){
            #free-trial-modal .ft-dialog{border-radius:1.5rem}
        }
        #free-trial-modal.is-open .ft-dialog,
        #free-trial-modal.flex .ft-dialog,
        #free-trial-modal.ft-open .ft-dialog{
            opacity:1;
            transform:translateY(0) scale(1);
        }
        #free-trial-modal .ft-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:.75rem;
            padding:1.15rem 1.25rem 1.25rem;
            border-bottom:0;
            background:
                radial-gradient(circle at 90% 0%, rgba(245,184,0,.22), transparent 42%),
                linear-gradient(145deg, #051F4D 0%, #0B3D91 52%, #1A56B0 100%);
            color:#fff;
            position:relative;
        }
        #free-trial-modal .ft-head::after{
            content:"";
            position:absolute;
            left:0;right:0;bottom:0;
            height:3px;
            background:linear-gradient(90deg, #F5B800, #FFD24D, #F5B800);
        }
        @media (min-width:640px){
            #free-trial-modal .ft-head{padding:1.35rem 1.5rem 1.45rem}
        }
        #free-trial-modal .ft-body{flex:1;overflow-y:auto;padding:1.25rem;background:#F4F7FC}
        @media (min-width:640px){
            #free-trial-modal .ft-body{padding:1.5rem}
        }
        #free-trial-modal .ft-kicker{
            display:inline-flex;align-items:center;gap:.4rem;
            color:#F5B800;font-size:.8rem;font-weight:800;margin:0 0 .45rem;
            background:rgba(245,184,0,.14);border:1px solid rgba(245,184,0,.35);
            padding:.28rem .7rem;border-radius:999px;
        }
        #free-trial-modal .ft-title{color:#fff;font-family:"Cairo","Tajawal",sans-serif;font-size:1.3rem;font-weight:900;letter-spacing:-.01em;line-height:1.35;margin:0}
        #free-trial-modal .ft-sub{color:rgba(255,255,255,.82);font-size:.875rem;line-height:1.75;margin:.4rem 0 0}
        #free-trial-modal .ft-label{color:#0b1220;font-size:.875rem;font-weight:800;display:block;margin-bottom:.35rem}
        #free-trial-modal .ft-muted{color:#5b6577;font-size:.75rem}
        #free-trial-modal .ft-row{display:flex;align-items:flex-end;justify-content:space-between;gap:.75rem;margin-bottom:.75rem}
        #free-trial-modal .ft-times{display:flex;flex-wrap:wrap;gap:.5rem}
        #free-trial-modal .ft-form{
            display:flex;flex-direction:column;gap:1rem;padding:1.15rem 1.1rem 1.2rem;
            border-top:0;margin-top:.25rem;background:#fff;border:1px solid #d7dde6;border-radius:1.1rem;
        }
        #free-trial-modal .ft-form-grid{display:grid;grid-template-columns:1fr;gap:1rem}
        @media (min-width:640px){
            #free-trial-modal .ft-form-grid{grid-template-columns:1fr 1fr}
        }
        #free-trial-modal .ft-field-wrap{display:flex;flex-direction:column;gap:.35rem}
        #free-trial-modal .ft-phone-row{
            display:flex;align-items:stretch;gap:.5rem;direction:ltr;
        }
        #free-trial-modal .ft-phone-row .ft-cc{
            width:7.25rem;flex:0 0 auto;padding-inline:.65rem;
            appearance:none;-webkit-appearance:none;
        }
        #free-trial-modal .ft-phone-row .ft-field{flex:1 1 auto;min-width:0}
        #free-trial-modal .ft-calendar{display:flex;flex-direction:column;gap:1.25rem}
        #free-trial-modal .ft-success{padding:1.5rem 0;text-align:center}
        #free-trial-modal .ft-success .ft-title{color:#0B3D91}
        #free-trial-modal .ft-success .ft-sub{color:#5b6577}
        #free-trial-modal .ft-success .ft-close{margin:1.5rem auto 0}
        #free-trial-modal .ft-icon-spin{
            display:inline-flex;width:2rem;height:2rem;align-items:center;justify-content:center;
            border-radius:.75rem;background:#E8EEF8;color:#0B3D91;
        }
        #free-trial-modal .ft-icon-spin svg{width:1rem;height:1rem;animation:ft-spin 1s linear infinite}
        @keyframes ft-spin{to{transform:rotate(360deg)}}
        #free-trial-modal .ft-close{
            width:2.5rem;height:2.5rem;border-radius:999px;border:1px solid rgba(255,255,255,.28);
            background:rgba(255,255,255,.12);color:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;
        }
        #free-trial-modal .ft-close:hover{background:rgba(255,255,255,.22);border-color:rgba(245,184,0,.55);color:#FFD24D}
        #free-trial-modal .ft-success .ft-close,
        #free-trial-modal .ft-body .ft-close{
            border:1px solid #d7dde6;background:#fff;color:#0B3D91;
        }
        #free-trial-modal .ft-success .ft-close:hover,
        #free-trial-modal .ft-body .ft-close:hover{
            border-color:rgba(11,61,145,.35);background:#E8EEF8;color:#0B3D91;
        }
        #free-trial-modal .ft-chip{
            border:1.5px solid #d7dde6;
            background:#fff;
            color:#0B1220;
            border-radius:999px;
            font-size:.8125rem;
            font-weight:700;
            padding:.6rem .95rem;
            transition:background-color 160ms ease,border-color 160ms ease,color 160ms ease,box-shadow 160ms ease;
            flex:0 0 auto;
            scroll-snap-align:start;
            cursor:pointer;
        }
        #free-trial-modal .ft-chip:hover{
            border-color:rgba(11,61,145,.4);
            background:#E8EEF8;
            color:#0B3D91;
        }
        #free-trial-modal .ft-chip.is-active{
            background:#0B3D91 !important;
            border-color:#0B3D91 !important;
            color:#fff !important;
            box-shadow:0 8px 20px rgba(11,61,145,.28);
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
            border-radius:999px;
            border:1.5px solid #d7dde6;
            background:#fff;
            padding:0 1.1rem;
            font-size:.875rem;
            font-family:inherit;
            color:#0b1220;
            box-sizing:border-box;
        }
        #free-trial-modal .ft-field:focus{
            outline:none;
            border-color:#0B3D91;
            box-shadow:0 0 0 3px rgba(11,61,145,.14);
        }
        #free-trial-modal .ft-field::placeholder{color:#5b6577;opacity:.7}
        #free-trial-modal .ft-submit{
            width:100%;height:3.1rem;border-radius:999px;border:0;
            background:linear-gradient(180deg, #FFD24D 0%, #F5B800 55%, #E5AB00 100%);
            color:#0B1220;font-size:.9rem;font-weight:900;cursor:pointer;font-family:inherit;
            box-shadow:0 12px 28px rgba(245,184,0,.4);
        }
        #free-trial-modal .ft-submit:hover{filter:brightness(1.03)}
        #free-trial-modal .ft-submit:disabled{opacity:.4;cursor:not-allowed;box-shadow:none}
        #free-trial-modal .ft-loading{
            align-items:center;gap:.75rem;
            border:1px solid #d7dde6;background:#f3f5f7;border-radius:1rem;
            padding:1.1rem 1rem;color:#5b6577;font-size:.875rem;
        }
        /* Ù…Ù‡Ù…: Ù„Ø§ ØªÙØ±Ø¶ display:flex Ù‡Ù†Ø§ â€” ÙŠØªØºÙ„Ø¨ Ø¹Ù„Ù‰ .hidden ÙˆÙŠØ¨Ù‚Ù‰ Ø§Ù„ØªØ­Ù…ÙŠÙ„ Ø¸Ø§Ù‡Ø±Ø§Ù‹ Ù„Ù„Ø£Ø¨Ø¯ */
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
            border-radius:1rem;background:#E8EEF8;color:#0B3D91;
        }
        #free-trial-modal .ft-scroll-btn{color:#0B3D91}
        #free-trial-modal .ft-scroll-btn:hover{
            background:#E8EEF8;
            border-color:rgba(11,61,145,.35);
        }
        @media (prefers-reduced-motion: reduce){
            #free-trial-modal .ft-backdrop,
            #free-trial-modal .ft-dialog{transition:none}
        }
    </style>
</head>
<body class="sana-home">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'home', 'navHero' => true])

@include('partials.welcome-landing-main')

{{-- Free trial booking modal â€” self-styled (no Tailwind) --}}
<div id="free-trial-modal" class="hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ft-title">
    <div class="ft-backdrop" data-close-free-trial></div>
    <div class="ft-dialog">
        <div class="ft-head">
            <div class="ft-head-copy">
                <p class="ft-kicker">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    Glottical · 30 {{ $isRtl ? 'دقيقة' : 'min' }}
                </p>
                <h3 id="ft-title" class="ft-title">{{ __($a.'.free_trial_modal_title') }}</h3>
                <p class="ft-sub">{{ __($a.'.free_trial_modal_sub') }}</p>
                        </div>
            <button type="button" class="ft-close" data-close-free-trial aria-label="{{ __($a.'.free_trial_close') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
                    </div>

        <div class="ft-body">
            <div id="ft-loading" class="ft-loading">
                <span class="ft-icon-spin">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                </span>
                {{ __($a.'.free_trial_loading') }}
    </div>

            <div id="ft-error" class="ft-error hidden" role="alert"></div>

            <div id="ft-calendar" class="ft-calendar hidden">
                <div>
                    <div class="ft-row">
                        <p class="ft-label" style="margin:0">{{ __($a.'.free_trial_pick_date') }}</p>
                        <p class="ft-muted">{{ $isRtl ? 'خلال أسبوعين' : 'Next 2 weeks' }}</p>
                </div>
                    <div class="ft-scroll-wrap" data-ft-scroll-wrap>
                        <button type="button" class="ft-scroll-btn is-prev" data-ft-scroll="-1" aria-label="{{ $isRtl ? 'الأيام السابقة' : 'Previous days' }}" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="{{ $isRtl ? 'm9 18 6-6-6-6' : 'm15 18-6-6 6-6' }}"/></svg>
                        </button>
                        <div id="ft-dates" class="ft-hscroll" role="listbox" aria-label="{{ __($a.'.free_trial_pick_date') }}"></div>
                        <button type="button" class="ft-scroll-btn is-next" data-ft-scroll="1" aria-label="{{ $isRtl ? 'الأيام التالية' : 'Next days' }}" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="{{ $isRtl ? 'm15 18-6-6 6-6' : 'm9 18 6-6-6-6' }}"/></svg>
                        </button>
            </div>
                    </div>

                <div>
                    <p class="ft-label">{{ __($a.'.free_trial_pick_time') }}</p>
                    <div id="ft-times" class="ft-times"></div>
                    <p id="ft-no-times" class="ft-sub hidden">{{ __($a.'.free_trial_no_slots') }}</p>
        </div>

                <form id="ft-form" class="ft-form">
                    @php
                        $phoneCountries = config('phone_countries.countries', []);
                        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'))
                            ?: ['dial_code' => '+966', 'name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'placeholder' => '5xxxxxxxx'];
                    @endphp
                    <input type="hidden" name="starts_at" id="ft-starts-at" required>
                    <div class="ft-field-wrap">
                        <label for="ft-name" class="ft-label">{{ __($a.'.free_trial_name') }}</label>
                        <input type="text" name="name" id="ft-name" required autocomplete="name" class="ft-field" value="{{ auth()->user()->name ?? '' }}">
                    </div>
                    <div class="ft-form-grid">
                        <div class="ft-field-wrap">
                            <label for="ft-email" class="ft-label">{{ __($a.'.free_trial_email') }}</label>
                            <input type="email" name="email" id="ft-email" autocomplete="email" class="ft-field" value="{{ auth()->user()->email ?? '' }}">
                        </div>
                        <div class="ft-field-wrap">
                            <label for="ft-phone" class="ft-label">{{ __($a.'.free_trial_phone') }}</label>
                            <div class="ft-phone-row">
                                <select name="country_code" id="ft-country-code" class="ft-field ft-cc" aria-label="{{ __($a.'.free_trial_country_code') }}">
                                    @foreach($phoneCountries as $c)
                                        @php $dial = $c['dial_code'] ?? ''; @endphp
                                        @if($dial !== '' && $dial !== 'OTHER')
                                            <option
                                                value="{{ $dial }}"
                                                data-placeholder="{{ $c['placeholder'] ?? '' }}"
                                                @selected($dial === ($defaultCountry['dial_code'] ?? '+966'))
                                            >{{ $dial }} {{ $isRtl ? ($c['name_ar'] ?? $c['name_en'] ?? '') : ($c['name_en'] ?? $c['name_ar'] ?? '') }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <input
                                    type="tel"
                                    name="phone"
                                    id="ft-phone"
                                    autocomplete="tel-national"
                                    class="ft-field"
                                    dir="ltr"
                                    inputmode="numeric"
                                    placeholder="{{ $defaultCountry['placeholder'] ?? '' }}"
                                    value="{{ auth()->user()->phone ? preg_replace('/^\+\d+/', '', (string) auth()->user()->phone) : '' }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="ft-field-wrap">
                        <label for="ft-goal" class="ft-label">{{ __($a.'.free_trial_goal') }}</label>
                        <select name="goal" id="ft-goal" class="ft-field" required>
                            <option value="" disabled selected>{{ __($a.'.free_trial_goal_placeholder') }}</option>
                            <option value="consultation">{{ __($a.'.free_trial_goal_consultation') }}</option>
                            <option value="trial">{{ __($a.'.free_trial_goal_trial') }}</option>
                            <option value="placement">{{ __($a.'.free_trial_goal_placement') }}</option>
                        </select>
                    </div>
                    <button type="submit" id="ft-submit" disabled class="ft-submit">
                        {{ __($a.'.free_trial_submit') }}
                    </button>
                    <p class="ft-muted" style="text-align:center;line-height:1.6">{{ $isRtl ? 'بدون التزام · سنؤكد الموعد برسالة قصيرة' : 'No commitment · We’ll confirm by a short message' }}</p>
                </form>
            </div>

            <div id="ft-success" class="ft-success hidden">
                <div class="ft-success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
                <h4 class="ft-title">{{ __($a.'.free_trial_success') }}</h4>
                <p id="ft-success-msg" class="ft-sub"></p>
                <button type="button" data-close-free-trial class="ft-close" aria-label="{{ __($a.'.free_trial_close') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                            </div>
                            </div>
                            </div>
                        </div>

@include('partials.landing.footer')

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
            box.innerHTML = '<p class="text-xs font-bold text-white/45 px-2 py-1">' + (isRtl ? 'Ø§Ù‚ØªØ±Ø§Ø­Ø§Øª' : 'Suggestions') + '</p>' + m.map(function(s, idx){
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
            var dur = c.duration ? (String(c.duration) + (isRtl ? ' Ø³' : 'h')) : '';
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
                (rt ? '<p class="text-[10px] text-amber-300/95 mt-0.5 font-bold">â˜… ' + esc(rt) + '</p>' : '') +
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
            var stars = c.rating >= 4 ? 'â˜…â˜…â˜…â˜…â˜…' : (c.rating >= 3 ? 'â˜…â˜…â˜…â˜…â˜†' : 'â˜…â˜…â˜…â˜†â˜†');
            body.innerHTML = '<div class="aspect-video rounded-xl overflow-hidden bg-white/5 border border-white/10 mb-4">' +
                (c.thumb ? '<img src="' + esc(c.thumb) + '" alt="" class="w-full h-full object-cover">' : '') +
                '</div><h3 class="text-xl font-black text-white">' + esc(c.title) + '</h3>' +
                '<p class="text-sm text-white/55 mt-1">' + esc(c.instructor) + '</p>' +
                '<p class="text-amber-300 text-sm mt-2">' + esc(stars) + ' ' + (c.rating || 'â€”') + '</p>' +
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
            modal.classList.add('is-open', 'flex', 'ft-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            loadSlots();
        }
        function closeModal(){
            modal.classList.remove('ft-open', 'is-open');
            setTimeout(function(){
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'is-open', 'ft-open');
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
            // Ø³Ø­Ø¨ Ø¨Ø§Ù„Ù…Ø§ÙˆØ³ Ø¹Ù„Ù‰ Ø³Ø·Ø­ Ø§Ù„Ù…ÙƒØªØ¨
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
        var countrySelect = document.getElementById('ft-country-code');
        var phoneInput = document.getElementById('ft-phone');
        if (countrySelect && phoneInput) {
            countrySelect.addEventListener('change', function(){
                var opt = countrySelect.options[countrySelect.selectedIndex];
                var ph = opt ? (opt.getAttribute('data-placeholder') || '') : '';
                if (ph) phoneInput.setAttribute('placeholder', ph);
            });
        }
        form.addEventListener('submit', function(e){
            e.preventDefault();
            var err = document.getElementById('ft-error');
            err.classList.add('hidden');
            var payload = {
                name: document.getElementById('ft-name').value,
                email: document.getElementById('ft-email').value || null,
                phone: document.getElementById('ft-phone').value || null,
                country_code: document.getElementById('ft-country-code').value || null,
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
        var steps = [
            reveal,
            heroParallax,
            heroSlider,
            searchSuggest,
            counters,
            testimonialCarousel,
            scrollProgress,
            freeTrialBooking
        ];
        steps.forEach(function(fn){
            try { fn(); } catch (err) { if (window.console && console.warn) console.warn('[home]', err); }
        });
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
