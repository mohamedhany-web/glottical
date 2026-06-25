<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $homeStats = $homeStats ?? ['learners' => 0, 'courses' => 0, 'certificates' => 0, 'services' => 0];
    $fmt = fn (int $n) => number_format($n, 0, '.', ',');
    $a = 'landing.academy';

    $featuredList = ($featuredCourses ?? collect())->take(24);
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

    $rowTrendingNow = $featuredList->sort(function ($a, $b) {
        $fa = (int) ($a->is_featured ?? false);
        $fb = (int) ($b->is_featured ?? false);
        if ($fa !== $fb) {
            return $fb <=> $fa;
        }

        return strtotime((string) ($b->created_at ?? '')) <=> strtotime((string) ($a->created_at ?? ''));
    })->values();
    $rowPopularWeek = $featuredList->sortByDesc(fn ($c) => (int) ($c->students_count ?? 0))->values();
    $rowRecommended = $featuredList->sortByDesc(fn ($c) => (int) ($c->lessons_count ?? 0))->values();
    $rowNew = $featuredList->sortByDesc('created_at')->values();
    $rowTopRated = $featuredList->sortByDesc(fn ($c) => (float) ($c->rating ?? 0))->values();

    // Streaming search chips for an academy (no AI chip)
    $searchChipsForJs = [
        ['id' => 'english', 'label' => __($a.'.chip_english'), 'keywords' => ['english', 'إنجليزي', 'grammar', 'speaking', 'ielts', 'toefl']],
        ['id' => 'arabic', 'label' => __($a.'.chip_arabic'), 'keywords' => ['arabic', 'عربي', 'نحو', 'بلاغة', 'إملاء', 'قراءة']],
        ['id' => 'fr', 'label' => __($a.'.chip_french'), 'keywords' => ['french', 'فرنسي', 'del f', 'delf', 'tcf']],
        ['id' => 'kids', 'label' => __($a.'.chip_kids'), 'keywords' => ['kids', 'أطفال', 'طفل', 'kids', 'مبتدئين']],
        ['id' => 'exams', 'label' => __($a.'.chip_exams'), 'keywords' => ['ielts', 'toefl', 'اختبار', 'امتحان', 'prep', 'تحضير']],
    ];
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e(__('landing.meta.title')); ?></title>
    <meta name="title" content="<?php echo e(__('landing.meta.title')); ?>">
    <meta name="description" content="<?php echo e(__('landing.meta.description')); ?>">
    <meta name="theme-color" content="#0d1528">
    <link rel="canonical" href="<?php echo e(url('/')); ?>">
    <link rel="alternate" hreflang="ar" href="<?php echo e(url('/')); ?>?lang=ar">
    <link rel="alternate" hreflang="en" href="<?php echo e(url('/')); ?>?lang=en">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(url('/')); ?>">
    <meta property="og:title" content="<?php echo e(__('landing.meta.og_title')); ?>">
    <meta property="og:description" content="<?php echo e(__('landing.meta.og_description')); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $r2PublicBase = \App\Services\PlatformMediaSettings::r2PublicBaseUrl();
        $heroLcpImage = ($heroSpotlight[0]['bg'] ?? null) ?: null;
    ?>
    <?php if(!empty($heroLcpImage)): ?>
        <link rel="preload" as="image" href="<?php echo e(e($heroLcpImage)); ?>" fetchpriority="high">
    <?php endif; ?>
    <?php if(is_string($r2PublicBase) && $r2PublicBase !== ''): ?>
        <?php $r2Host = parse_url($r2PublicBase, PHP_URL_HOST); ?>
        <?php if($r2Host): ?>
            <link rel="preconnect" href="https://<?php echo e($r2Host); ?>" crossorigin>
        <?php endif; ?>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        acad: {
                            blue: '#0B3D91',
                            blueDark: '#072a66',
                            blueSoft: '#E8EEF8',
                            cyan: '#00A3C4',
                            yellow: '#F5B800',
                            yellowSoft: '#FFF8E1',
                            gray: '#F4F6FA',
                            ink: '#1a2d4d',
                            navy: '#0d1528',
                            navyMid: '#1a2d4d',
                            neon: '#00d4ff',
                        },
                    },
                    fontFamily: {
                        sans: ['Cairo', 'Tajawal', 'IBM Plex Sans Arabic', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>

    <style>
        [x-cloak]{display:none!important}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{overflow-x:hidden;background:linear-gradient(180deg,#0d1528 0%,#121f38 45%,#0d1528 100%);min-height:100vh;display:flex;flex-direction:column;color:#e8eef8;font-size:16px;line-height:1.65}
        .font-display{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',system-ui,sans-serif}
        .container-acad{max-width:1280px;margin-inline:auto;padding-inline:clamp(16px,4vw,28px)}
        .section-y{padding-block:clamp(3.5rem,7vw,5rem)}
        .reveal{opacity:0;transform:translateY(22px);transition:opacity .6s ease,transform .6s ease}
        .reveal.revealed{opacity:1;transform:translateY(0)}
        .stream-hero .hero-slide{opacity:0;pointer-events:none;transition:opacity 1s ease,transform 1s ease;transform:scale(1.02)}
        .stream-hero .hero-slide.is-active{opacity:1;pointer-events:auto;transform:scale(1)}
        .hero-dots button{width:10px;height:10px;border-radius:999px;background:rgba(255,255,255,.28);transition:transform .2s,background .2s,width .2s}
        .hero-dots button.is-active{background:#F5B800;transform:scale(1.15);width:28px;border-radius:999px}
        .glass-panel{background:rgba(15,31,58,.72);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}
        .floating-search-glow{box-shadow:0 0 0 1px rgba(255,255,255,.08),0 8px 40px -8px rgba(0,212,255,.15),0 20px 50px -20px rgba(0,0,0,.5)}
        .suggest-item{animation:suggestIn .35s ease backwards}
        @keyframes suggestIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
        .path-scroll{scrollbar-width:thin;scrollbar-color:rgba(0,212,255,.5) rgba(5,11,24,.75)}
        .path-scroll::-webkit-scrollbar{height:6px}
        .path-scroll::-webkit-scrollbar-track{background:rgba(5,11,24,.78);border-radius:999px}
        .path-scroll::-webkit-scrollbar-thumb{background:rgba(0,212,255,.35);border-radius:999px;border:1px solid rgba(5,11,24,.5)}
        .path-scroll::-webkit-scrollbar-thumb:hover{background:rgba(0,212,255,.5)}
        .path-scroll::-webkit-scrollbar-button{width:0;height:0;display:none}
        .path-scroll::-webkit-scrollbar-corner{background:transparent}
        .pricing-pop{box-shadow:0 24px 60px -20px rgba(0,212,255,.25)}
        .grid-12{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:clamp(1rem,2vw,1.5rem)}
        .stream-cta-band{background:linear-gradient(135deg,#0B3D91 0%,#123256 50%,#0d1528 100%);position:relative;overflow:hidden}
        .stream-cta-band::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 50% at 20% 80%,rgba(245,184,0,.15),transparent 55%),radial-gradient(ellipse 60% 40% at 85% 20%,rgba(0,212,255,.12),transparent 50%);pointer-events:none}
        .pattern-dots{background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.06) 1px,transparent 0);background-size:24px 24px}
        .netflix-row{display:flex;flex-direction:row;flex-wrap:nowrap;align-items:stretch;gap:1rem;overflow-x:auto;overflow-y:visible;scroll-snap-type:x mandatory;scrollbar-width:thin;scrollbar-color:rgba(0,212,255,.5) rgba(5,11,24,.82);scroll-behavior:smooth;-webkit-overflow-scrolling:touch;padding:8px 4px 16px;margin-inline:-4px;width:100%;max-width:100%}
        .netflix-row::-webkit-scrollbar{height:8px}
        .netflix-row::-webkit-scrollbar-track{background:rgba(5,11,24,.9);border-radius:999px;margin-inline:2px}
        .netflix-row::-webkit-scrollbar-thumb{background:linear-gradient(90deg,rgba(245,184,0,.55),rgba(0,212,255,.45));border-radius:999px;border:2px solid rgba(5,11,24,.85)}
        .netflix-row::-webkit-scrollbar-thumb:hover{background:linear-gradient(90deg,rgba(245,184,0,.7),rgba(0,212,255,.55))}
        .netflix-row::-webkit-scrollbar-button{width:0;height:0;display:none}
        .netflix-row::-webkit-scrollbar-corner{background:transparent}
        .stream-card-wrap.netflix-item{scroll-snap-align:start;flex:0 0 auto;width:min(17.5rem,82vw);min-width:min(17.5rem,82vw);max-width:20rem}
        @media(min-width:640px){.stream-card-wrap.netflix-item{width:18.5rem;min-width:18.5rem;max-width:19.5rem}}
        @media(min-width:1024px){.stream-card-wrap.netflix-item{width:20rem;min-width:20rem;max-width:20rem}}
        .stream-card-wrap .stream-card{width:100%}
        #market-live-results.netflix-row > a.netflix-item{flex:0 0 auto;width:min(9.25rem,38vw);min-width:8.5rem;max-width:10.5rem}
        .search-overlay-enter{animation:soIn .32s ease forwards}
        @keyframes soIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        #academy-search-anchor.search-bar-visible{animation:searchBarIn .28s ease forwards}
        @keyframes searchBarIn{from{opacity:0;transform:translate(-50%,-8px)}to{opacity:1;transform:translate(-50%,0)}}
        .media-thumb-skeleton{position:absolute;inset:0;background:linear-gradient(110deg,#152a4a 8%,#1e3a5f 18%,#152a4a 33%);background-size:200% 100%;animation:mediaShimmer 1.2s linear infinite}
        @keyframes mediaShimmer{to{background-position-x:-200%}}
        .media-thumb-img{opacity:0;transition:opacity .35s ease}
        .media-thumb-img.is-loaded{opacity:1}
        .chip-active{box-shadow:0 0 0 2px #F5B800,inset 0 0 20px rgba(245,184,0,.12)}
        @keyframes kenBurns{0%{transform:scale(1.08) translate(0,0)}100%{transform:scale(1.14) translate(-1%,-1%)}}
        @keyframes kenDrift{0%{transform:scale(1.12) translate(0,0)}100%{transform:scale(1.18) translate(1.2%,0.8%)}}
        .stream-hero .hero-slide.is-active .hero-ken{animation:kenBurns 16s ease-in-out infinite alternate}
        .stream-hero .hero-slide.is-active .hero-ken-accent{animation:kenDrift 18s ease-in-out infinite alternate}
        /* Make hero imagery feel crisp/cinematic (not washed out) */
        .stream-hero .hero-ken,
        .stream-hero .hero-ken-accent{
            filter: saturate(1.18) contrast(1.08) brightness(0.98);
            -webkit-filter: saturate(1.18) contrast(1.08) brightness(0.98);
        }
    </style>
</head>
<body class="font-sans text-white antialiased font-display bg-acad-navy">
<div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>

<?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="flex-1">
    
    <section class="stream-hero relative min-h-[100svh] flex flex-col justify-end overflow-hidden text-white -mt-14 sm:-mt-[60px] pt-14 sm:pt-[60px]">
        <?php $__currentLoopData = $heroSpotlight; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hi => $spot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="hero-slide <?php echo e($hi === 0 ? 'is-active' : ''); ?> absolute inset-0 flex flex-col justify-end pb-28 sm:pb-36 md:pb-44" data-hero-slide="<?php echo e($hi); ?>">
                <?php if(! empty($spot['bg'])): ?>
                    <img src="<?php echo e(e($spot['bg'])); ?>"
                         alt=""
                         class="hero-ken hero-slider-img absolute inset-0 w-full h-full object-cover object-center scale-110"
                         decoding="async"
                         fetchpriority="<?php echo e($hi === 0 ? 'high' : 'auto'); ?>"
                         loading="eager">
                <?php else: ?>
                    <div class="absolute inset-0 bg-gradient-to-br from-[#0B3D91] via-[#123256] to-[#0d1528]"></div>
                <?php endif; ?>
                <?php
                    $accent = $spot['accent_bg'] ?? '';
                    $showAccent = $accent !== '' && $accent !== ($spot['bg'] ?? '');
                ?>
                <?php if($showAccent): ?>
                    <img src="<?php echo e(e($accent)); ?>"
                         alt=""
                         class="hero-ken-accent absolute inset-0 w-full h-full object-cover object-center scale-110 opacity-[0.34] mix-blend-soft-light"
                         decoding="async"
                         loading="lazy">
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-br from-[#0B3D91]/18 via-transparent to-[#00d4ff]/10 pointer-events-none"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0d1528] via-[#0d1528]/70 to-[#0d1528]/12"></div>
                <div class="absolute inset-0 bg-gradient-to-<?php echo e($isRtl ? 'l' : 'r'); ?> from-[#0d1528] via-[#0d1528]/55 to-transparent w-full md:w-[75%]"></div>
                <div class="absolute inset-0 pattern-dots opacity-[0.14] pointer-events-none"></div>
                <div class="container-acad relative z-10 w-full">
                    <div class="max-w-3xl <?php echo e($isRtl ? 'ms-auto text-right' : 'me-auto text-left'); ?>">
                        <p class="text-acad-yellow font-black text-[11px] sm:text-xs tracking-[0.25em] uppercase mb-3 opacity-95"><?php echo e(__($a.'.stream_meta_kicker')); ?></p>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel text-xs font-bold text-white w-fit mb-4 border border-white/10"><?php echo e($spot['kicker']); ?></span>
                        <h1 class="text-3xl sm:text-5xl lg:text-[3.35rem] font-black leading-[1.12] tracking-tight drop-shadow-[0_4px_24px_rgba(0,0,0,.45)] text-white"><?php echo e($spot['title']); ?></h1>
                        <p class="mt-4 text-base sm:text-lg text-white/82 max-w-2xl leading-relaxed font-medium"><?php echo e($spot['sub']); ?></p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="<?php echo e($spot['primary_url']); ?>" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-acad-yellow text-acad-blue font-extrabold text-sm sm:text-base shadow-xl shadow-black/35 hover:brightness-110 transition ring-2 ring-acad-yellow/30"><?php echo e($spot['primary_label']); ?><i class="fas fa-play text-xs"></i></a>
                            <a href="<?php echo e($spot['secondary_url']); ?>" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl glass-panel text-white font-bold hover:bg-white/10 transition border border-white/15"><?php echo e($spot['secondary_label']); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="absolute bottom-6 sm:bottom-8 inset-x-0 z-20 flex justify-center pointer-events-none">
            <div class="hero-dots flex gap-2 pointer-events-auto" role="tablist" aria-label="Hero"></div>
        </div>
        <?php if($featuredList->isNotEmpty()): ?>
            <div class="hidden lg:flex flex-col gap-3 absolute bottom-28 <?php echo e($isRtl ? 'left-6' : 'right-6'); ?> z-20 w-[min(100%,280px)] pointer-events-auto">
                <?php $__currentLoopData = $featuredList->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $fcu = $fc->thumbnail_url;
                    ?>
                    <a href="<?php echo e(route('public.course.show', $fc->id)); ?>" class="flex items-center gap-3 rounded-xl glass-panel p-3 text-white hover:ring-1 hover:ring-acad-yellow/60 transition shadow-2xl border border-white/10">
                        <div class="w-14 h-14 rounded-lg overflow-hidden bg-white/10 shrink-0 ring-1 ring-white/15">
                            <?php if($fcu): ?>
                                <img src="<?php echo e($fcu); ?>" alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="flex w-full h-full items-center justify-center text-white/40"><i class="fas fa-play"></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0 text-start">
                            <p class="text-[10px] font-bold text-acad-yellow uppercase tracking-wide"><?php echo e(__($a.'.hero_preview_live')); ?></p>
                            <p class="text-sm font-bold leading-snug line-clamp-2"><?php echo e(\Illuminate\Support\Str::limit($fc->title, 52)); ?></p>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </section>

    
    <div class="hidden fixed top-[4.35rem] md:top-[4.6rem] left-1/2 -translate-x-1/2 z-[998] w-[calc(100%-2rem)] max-w-lg pointer-events-none" id="academy-search-anchor" aria-hidden="true">
        <form action="<?php echo e(route('public.courses')); ?>" method="get" id="academy-search-form" class="pointer-events-auto glass-panel rounded-full floating-search-glow px-4 py-2.5 flex items-center gap-3 border border-white/12">
            <i class="fas fa-magnifying-glass text-acad-cyan/80 text-sm"></i>
            <input type="search" name="q" autocomplete="off" placeholder="<?php echo e(__($a.'.search_placeholder')); ?>" class="flex-1 bg-transparent border-0 outline-none text-white placeholder:text-white/45 text-sm font-semibold" id="academy-search-input" data-open-search>
            <button type="button" class="text-acad-yellow font-extrabold text-xs shrink-0 lg:hidden" data-open-search-btn><?php echo e(__($a.'.search_open')); ?></button>
        </form>
        <div id="search-suggestions" class="hidden mt-2 glass-panel rounded-2xl border border-white/10 p-2 text-white max-h-56 overflow-y-auto shadow-xl"></div>
    </div>

    
    <section class="relative z-10 py-8 sm:py-10 border-y border-white/5 bg-[#080f1f]/95 backdrop-blur-md">
        <div class="container-acad">
            <p class="text-center text-[11px] font-extrabold text-acad-cyan uppercase tracking-[0.2em] mb-5"><?php echo e(__($a.'.strip_kicker_stream')); ?></p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 text-center">
                <div class="reveal">
                    <p class="text-2xl sm:text-4xl font-black text-white tabular-nums counter drop-shadow-[0_0_20px_rgba(245,184,0,.25)]" data-target="<?php echo e((int) $homeStats['learners']); ?>" data-suffix="<?php echo e(!empty($homeStats['learners_show_plus']) ? '+' : ''); ?>">0</p>
                    <p class="text-xs sm:text-sm font-bold text-white/55 mt-2"><?php echo e(__($a.'.stats_students')); ?></p>
                </div>
                <div class="reveal">
                    <p class="text-2xl sm:text-4xl font-black text-white tabular-nums counter drop-shadow-[0_0_20px_rgba(0,212,255,.2)]" data-target="<?php echo e((int) $homeStats['courses']); ?>">0</p>
                    <p class="text-xs sm:text-sm font-bold text-white/55 mt-2"><?php echo e(__($a.'.stats_courses')); ?></p>
                </div>
                <div class="reveal">
                    <p class="text-2xl sm:text-4xl font-black text-acad-yellow tabular-nums counter" data-target="<?php echo e($satisfactionStat); ?>">0</p>
                    <p class="text-xs sm:text-sm font-bold text-white/55 mt-2"><?php echo e(__($a.'.stats_completion')); ?></p>
                </div>
                <div class="reveal">
                    <p class="text-2xl sm:text-4xl font-black text-white tabular-nums counter" data-target="<?php echo e($countriesStat); ?>">0</p>
                    <p class="text-xs sm:text-sm font-bold text-white/55 mt-2"><?php echo e(__($a.'.stats_countries')); ?></p>
                </div>
            </div>
        </div>
    </section>

    
    <section id="stream-discover" class="section-y relative">
        <div class="container-acad space-y-10 md:space-y-14">
            <div class="reveal flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <span class="text-acad-cyan font-extrabold text-xs tracking-widest uppercase"><?php echo e(__($a.'.stream_meta_kicker')); ?></span>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-black text-white"><?php echo e(__($a.'.courses_title')); ?></h2>
                    <p class="mt-2 text-white/60 max-w-xl text-sm sm:text-base"><?php echo e(__($a.'.courses_sub')); ?></p>
                </div>
                <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-2 font-extrabold text-acad-yellow hover:text-white transition text-sm shrink-0"><?php echo e(__('landing.view_all_courses')); ?><i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i></a>
            </div>

            <?php $__currentLoopData = [
                ['title' => __($a.'.row_trending_now'), 'rows' => $rowTrendingNow],
                ['title' => __($a.'.row_recommended'), 'rows' => $rowRecommended],
                ['title' => __($a.'.row_new_releases'), 'rows' => $rowNew],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $band): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($band['rows']->isEmpty()): ?> <?php continue; ?> <?php endif; ?>
                <div class="reveal">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="h-1 w-8 rounded-full bg-gradient-to-l from-acad-yellow to-acad-cyan shrink-0"></span>
                        <h3 class="text-lg sm:text-xl font-black text-white tracking-tight"><?php echo e($band['title']); ?></h3>
                    </div>
                    <div class="netflix-row" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
                        <?php $__currentLoopData = $band['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('partials.landing-stream-card', ['course' => $course, 'a' => $a, 'isRtl' => $isRtl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($featuredList->isEmpty()): ?>
                <p class="text-center text-white/50 py-12"><?php echo e(__('public.no_courses_landing')); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <?php if(($oneToOneCourses ?? collect())->isNotEmpty()): ?>
    <section id="stream-one-to-one" class="section-y bg-violet-950/30 border-t border-violet-500/10">
        <div class="container-acad space-y-8">
            <div class="reveal flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <span class="text-violet-300 font-extrabold text-xs tracking-widest uppercase"><?php echo e(__('public.home_one_to_one_kicker')); ?></span>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-black text-white"><?php echo e(__('public.home_one_to_one_title')); ?></h2>
                    <p class="mt-2 text-white/60 max-w-xl text-sm sm:text-base"><?php echo e(__('public.home_one_to_one_sub')); ?></p>
                </div>
                <a href="<?php echo e(route('public.courses', ['delivery' => 'one_to_one'])); ?>" class="inline-flex items-center gap-2 font-extrabold text-violet-300 hover:text-white transition text-sm shrink-0">
                    <?php echo e(__('public.home_one_to_one_cta')); ?>

                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i>
                </a>
            </div>
            <div class="netflix-row reveal" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
                <?php $__currentLoopData = $oneToOneCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partials.landing-stream-card', ['course' => $course, 'a' => $a, 'isRtl' => $isRtl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    
    <section id="stream-paths" class="section-y bg-[#060d1a]/90 border-t border-white/5">
        <div class="container-acad">
            <div class="reveal max-w-3xl">
                <span class="text-acad-cyan font-extrabold text-xs tracking-widest uppercase"><?php echo e(__($a.'.paths_kicker')); ?></span>
                <h2 class="mt-2 text-3xl sm:text-4xl font-black text-white"><?php echo e(__($a.'.stream_paths_series')); ?></h2>
                <p class="mt-3 text-white/60 leading-relaxed"><?php echo e(__($a.'.stream_paths_sub')); ?></p>
            </div>
            <div class="path-scroll flex gap-5 mt-10 overflow-x-auto pb-4 snap-x snap-mandatory">
                <?php $__currentLoopData = $pathsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $pImg = $path->image_url ?? null;
                        $episodes = max(0, (int) ($path->courses_count ?? 0));
                        $pathUrl = $path->url ?? (isset($path->slug) && $path->slug !== '' ? route('public.learning-path.show', $path->slug) : route('public.learning-paths.index'));
                    ?>
                    <article class="reveal shrink-0 w-[min(100%,300px)] sm:w-[320px] snap-start rounded-2xl border border-white/10 glass-panel overflow-hidden shadow-xl hover:border-acad-yellow/40 transition-all duration-300">
                        <a href="<?php echo e($pathUrl); ?>" class="block h-40 relative overflow-hidden group">
                            <?php if($pImg): ?>
                                <img src="<?php echo e($pImg); ?>" alt="<?php echo e($path->name); ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                            <?php else: ?>
                                <div class="absolute inset-0 bg-gradient-to-br from-acad-blue to-[#0d1528]"></div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0d1528] via-[#0d1528]/50 to-transparent"></div>
                            <span class="absolute top-3 <?php echo e($isRtl ? 'right-3' : 'left-3'); ?> text-[10px] font-black uppercase tracking-wide px-2 py-1 rounded-md bg-acad-yellow/95 text-acad-blue"><?php echo e(__($a.'.stream_badge_series')); ?></span>
                            <div class="absolute bottom-3 start-4 end-4">
                                <h3 class="text-white font-black text-lg leading-tight drop-shadow-lg"><?php echo e($path->name); ?></h3>
                            </div>
                        </a>
                        <div class="p-5 text-start">
                            <p class="text-sm text-white/65 line-clamp-2"><?php echo e(\Illuminate\Support\Str::limit(strip_tags((string) ($path->description ?? '')), 110) ?: '—'); ?></p>
                            <p class="mt-3 text-xs font-bold text-acad-cyan">
                                <i class="fas fa-film me-1 opacity-80"></i>
                                <?php if($episodes > 0): ?>
                                    <?php echo e($episodes); ?> <?php echo e(__($a.'.path_episodes')); ?>

                                <?php else: ?>
                                    <?php echo e(__($a.'.path_episodes')); ?>

                                <?php endif; ?>
                            </p>
                            <a href="<?php echo e($pathUrl); ?>" class="mt-5 block text-center py-2.5 rounded-xl bg-acad-yellow text-acad-blue font-extrabold hover:brightness-110 transition"><?php echo e(__($a.'.path_continue')); ?></a>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="section-y bg-[#0d1528] border-t border-white/5">
        <div class="container-acad">
            <div class="reveal text-center max-w-2xl mx-auto mb-12">
                <span class="text-acad-cyan font-extrabold text-xs tracking-widest uppercase"><?php echo e(__($a.'.instructors_kicker')); ?></span>
                <h2 class="mt-2 text-3xl sm:text-4xl font-black text-white"><?php echo e(__($a.'.instructors_title')); ?></h2>
                <p class="mt-3 text-white/60"><?php echo e(__($a.'.instructors_sub')); ?></p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <?php $__empty_1 = true; $__currentLoopData = ($homeInstructors ?? collect())->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $name = $p->user->name ?? '';
                        $headline = $p->headline ?? '';
                        $skillTags = array_slice(array_filter(is_array($p->skills ?? null) ? $p->skills : []), 0, 3);
                        if (empty($skillTags)) {
                            $skillTags = [__('landing.nav.courses'), __('landing.nav.instructors'), __('public.services')];
                        }
                        $followers = max(120, abs(crc32((string) $name)) % 14000);
                        $courseCt = (int) ($p->courses_count ?? 0);
                        $avgRating = number_format(4.6 + (crc32((string) $name) % 35) / 100, 1);
                    ?>
                    <article class="reveal relative rounded-2xl border border-white/10 glass-panel p-6 hover:border-acad-cyan/35 hover:shadow-[0_0_40px_-10px_rgba(0,212,255,.35)] transition text-center overflow-hidden group">
                        <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="block relative z-10">
                            <div class="mx-auto w-24 h-24 rounded-full overflow-hidden ring-2 ring-acad-yellow/40 shadow-lg bg-[#1a2d4d]">
                                <?php if($p->photo_path): ?>
                                    <img src="<?php echo e($p->photo_url); ?>" alt="<?php echo e($name); ?>" class="w-full h-full object-cover" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-white/35"><i class="fas fa-user text-3xl"></i></div>
                                <?php endif; ?>
                            </div>
                            <h3 class="mt-5 font-black text-lg text-white"><?php echo e($name); ?></h3>
                            <?php if($headline !== ''): ?>
                                <p class="mt-1 text-sm font-bold text-acad-cyan/90 line-clamp-2"><?php echo e($headline); ?></p>
                            <?php endif; ?>
                            <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                                <?php $__currentLoopData = $skillTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-acad-cyan border border-white/10"><?php echo e($tag); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="mt-3 text-xs font-bold text-white/50"><i class="fas fa-user-friends text-acad-yellow me-1"></i><?php echo e(number_format($followers)); ?>+ <?php echo e(__($a.'.instructor_followers')); ?></p>
                            <div class="mt-2 text-amber-400 text-sm font-bold"><i class="fas fa-star"></i> <?php echo e($avgRating); ?></div>
                            <span class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-acad-yellow text-acad-blue font-extrabold text-sm group-hover:brightness-110 transition"><?php echo e(__($a.'.instructor_view_content')); ?></span>
                        </a>
                        <div class="pointer-events-none absolute inset-0 flex items-center justify-center bg-[#0d1528]/92 text-white p-4 opacity-0 group-hover:opacity-100 transition duration-300 backdrop-blur-sm">
                            <div class="text-center">
                                <p class="text-3xl font-black"><?php echo e($courseCt); ?></p>
                                <p class="text-xs font-bold text-white/80"><?php echo e(__($a.'.instructor_overlay_courses')); ?></p>
                                <p class="text-2xl font-black mt-3"><?php echo e($avgRating); ?></p>
                                <p class="text-xs font-bold text-white/80"><?php echo e(__($a.'.instructor_overlay_rating')); ?></p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="col-span-full text-center text-white/45"><?php echo e(__('public.no_instructors')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="section-y relative overflow-hidden text-white border-t border-white/5">
        <div class="absolute inset-0 bg-gradient-to-br from-[#121f38] via-[#0B3D91] to-[#0d1528]"></div>
        <div class="absolute inset-0 pattern-dots opacity-30"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-acad-yellow/5 to-transparent pointer-events-none"></div>
        <div class="container-acad relative z-10">
            <div class="reveal text-center max-w-2xl mx-auto mb-14">
                <span class="text-acad-yellow font-extrabold text-sm"><?php echo e(__($a.'.stats_kicker')); ?></span>
                <h2 class="mt-2 text-3xl sm:text-4xl font-black"><?php echo e(__($a.'.stats_title')); ?></h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <div class="reveal text-center">
                    <p class="text-4xl sm:text-5xl font-black tabular-nums text-acad-yellow counter" data-target="<?php echo e((int) $homeStats['learners']); ?>" data-suffix="<?php echo e(!empty($homeStats['learners_show_plus']) ? '+' : ''); ?>">0</p>
                    <p class="mt-2 font-bold text-white/85"><?php echo e(__($a.'.stats_students')); ?></p>
                </div>
                <div class="reveal text-center">
                    <p class="text-4xl sm:text-5xl font-black tabular-nums text-acad-yellow counter" data-target="<?php echo e((int) $homeStats['courses']); ?>">0</p>
                    <p class="mt-2 font-bold text-white/85"><?php echo e(__($a.'.stats_courses')); ?></p>
                </div>
                <div class="reveal text-center">
                    <p class="text-4xl sm:text-5xl font-black tabular-nums text-acad-yellow counter" data-target="<?php echo e($countriesStat); ?>">0</p>
                    <p class="mt-2 font-bold text-white/85"><?php echo e(__($a.'.stats_countries')); ?></p>
                </div>
                <div class="reveal text-center">
                    <p class="text-4xl sm:text-5xl font-black tabular-nums text-acad-yellow counter" data-target="<?php echo e($satisfactionStat); ?>">0</p>
                    <p class="mt-2 font-bold text-white/85"><?php echo e(__($a.'.stats_completion')); ?></p>
                </div>
            </div>
        </div>
    </section>

    
    <?php if($testimonialRows->isNotEmpty()): ?>
        <section class="section-y bg-[#080f1f] border-t border-white/5">
            <div class="container-acad">
                <div class="reveal text-center max-w-2xl mx-auto mb-12">
                    <span class="text-acad-cyan font-extrabold text-xs tracking-widest uppercase"><?php echo e(__($a.'.testimonials_kicker')); ?></span>
                    <h2 class="mt-2 text-3xl font-black text-white"><?php echo e(__($a.'.testimonials_title')); ?></h2>
                    <p class="mt-2 text-white/55"><?php echo e(__($a.'.testimonials_sub')); ?></p>
                </div>
                <div class="relative max-w-3xl mx-auto reveal">
                    <div class="overflow-hidden rounded-2xl border border-white/10 glass-panel" dir="ltr">
                        <div class="flex transition-transform duration-500 ease-out" id="testimonial-track" style="transform:translateX(0)">
                            <?php $__currentLoopData = $testimonialRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $img = method_exists($t, 'publicImageUrl') ? $t->publicImageUrl() : null; ?>
                                <div class="min-w-full px-2 py-2">
                                    <div class="rounded-xl p-8">
                                        <div class="flex items-start gap-4 flex-wrap">
                                            <div class="w-14 h-14 rounded-full overflow-hidden bg-white/10 flex-shrink-0 ring-2 ring-acad-yellow/30">
                                                <?php if($img): ?>
                                                    <img src="<?php echo e($img); ?>" alt="" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center font-black text-acad-yellow"><?php echo e(mb_substr((string) ($t->author_name ?? 'G'), 0, 1)); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 min-w-0 text-start">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="font-black text-white"><?php echo e($t->author_name ?? '—'); ?></p>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30"><i class="fas fa-check-circle me-1"></i><?php echo e(__($a.'.testimonial_verified')); ?></span>
                                                </div>
                                                <p class="text-sm text-acad-cyan font-bold"><?php echo e($t->role_label ?? ''); ?></p>
                                                <p class="text-amber-400 text-sm mt-1" aria-hidden="true"><?php echo str_repeat('<i class="fas fa-star"></i> ', 5); ?></p>
                                            </div>
                                        </div>
                                        <p class="mt-6 text-white/70 leading-relaxed"><?php echo e(\Illuminate\Support\Str::limit(strip_tags((string) ($t->body ?? '')), 280)); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="flex justify-center gap-3 mt-6">
                        <button type="button" class="test-prev w-11 h-11 rounded-full border border-white/20 bg-white/5 text-white hover:bg-acad-yellow hover:text-acad-blue hover:border-acad-yellow transition" aria-label="<?php echo e(__($a.'.carousel_prev')); ?>"><i class="fas fa-chevron-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i></button>
                        <button type="button" class="test-next w-11 h-11 rounded-full border border-white/20 bg-white/5 text-white hover:bg-acad-yellow hover:text-acad-blue hover:border-acad-yellow transition" aria-label="<?php echo e(__($a.'.carousel_next')); ?>"><i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></button>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    
    <section class="section-y bg-[#0d1528] border-t border-white/5">
        <div class="container-acad">
            <div class="reveal text-center max-w-2xl mx-auto mb-14">
                <span class="text-acad-cyan font-extrabold text-xs tracking-widest uppercase"><?php echo e(__($a.'.pricing_kicker')); ?></span>
                <h2 class="mt-2 text-3xl sm:text-4xl font-black text-white"><?php echo e(__($a.'.pricing_title')); ?></h2>
                <p class="mt-3 text-white/60"><?php echo e(__($a.'.pricing_sub')); ?></p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 items-stretch">
                <div class="reveal rounded-2xl border border-white/10 glass-panel p-8 flex flex-col">
                    <h3 class="text-xl font-black text-acad-yellow"><?php echo e(__($a.'.plan_basic')); ?></h3>
                    <p class="mt-2 text-white/60 text-sm"><?php echo e(__($a.'.plan_basic_desc')); ?></p>
                    <p class="mt-4 text-xs font-extrabold text-acad-cyan uppercase tracking-wide"><?php echo e(__($a.'.plan_features')); ?></p>
                    <ul class="mt-2 space-y-2 text-sm text-white/75 flex-1">
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_basic_f1')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_basic_f2')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_basic_f3')); ?></li>
                    </ul>
                    <p class="mt-6 text-3xl font-black text-white"><?php echo e($starterPrice > 0 ? $fmt((int) $starterPrice).' '.__('landing.currency') : __('landing.free')); ?><?php if($starterPrice > 0): ?><span class="text-sm font-bold text-white/45"> / <?php echo e(__($a.'.plan_period')); ?></span><?php endif; ?></p>
                    <a href="<?php echo e(route('register')); ?>" class="mt-6 block text-center py-3 rounded-xl border-2 border-acad-yellow/60 text-acad-yellow font-extrabold hover:bg-acad-yellow hover:text-acad-blue transition"><?php echo e(__($a.'.plan_cta')); ?></a>
                </div>
                <div class="reveal rounded-2xl pricing-pop border-2 border-acad-yellow glass-panel p-8 flex flex-col relative md:-translate-y-2 shadow-[0_0_48px_-12px_rgba(245,184,0,.35)]">
                    <span class="absolute top-4 end-4 text-[11px] font-black uppercase tracking-wide px-2 py-1 rounded-md bg-acad-yellow text-acad-blue"><?php echo e(__($a.'.plan_pro_badge')); ?></span>
                    <h3 class="text-xl font-black text-white"><?php echo e(__($a.'.plan_pro')); ?></h3>
                    <p class="mt-2 text-white/60 text-sm"><?php echo e(__($a.'.plan_pro_desc')); ?></p>
                    <p class="mt-4 text-xs font-extrabold text-acad-cyan uppercase tracking-wide"><?php echo e(__($a.'.plan_features')); ?></p>
                    <ul class="mt-2 space-y-2 text-sm text-white/75 flex-1">
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_pro_f1')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_pro_f2')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_pro_f3')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_pro_f4')); ?></li>
                    </ul>
                    <p class="mt-6 text-3xl font-black text-acad-yellow"><?php echo e($proPrice > 0 ? $fmt((int) $proPrice).' '.__('landing.currency') : '—'); ?><?php if($proPrice > 0): ?><span class="text-sm font-bold text-white/45"> / <?php echo e(__($a.'.plan_period')); ?></span><?php endif; ?></p>
                    <a href="<?php echo e(route('public.pricing')); ?>" class="mt-6 block text-center py-3 rounded-xl bg-acad-yellow text-acad-blue font-extrabold shadow-lg hover:brightness-110 transition"><?php echo e(__($a.'.plan_subscribe')); ?></a>
                </div>
                <div class="reveal rounded-2xl border border-white/10 glass-panel p-8 flex flex-col">
                    <h3 class="text-xl font-black text-acad-yellow"><?php echo e(__($a.'.plan_enterprise')); ?></h3>
                    <p class="mt-2 text-white/60 text-sm"><?php echo e(__($a.'.plan_enterprise_desc')); ?></p>
                    <p class="mt-4 text-xs font-extrabold text-acad-cyan uppercase tracking-wide"><?php echo e(__($a.'.plan_features')); ?></p>
                    <ul class="mt-2 space-y-2 text-sm text-white/75 flex-1">
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_ent_f1')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_ent_f2')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_ent_f3')); ?></li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-400 mt-0.5"></i><?php echo e(__($a.'.plan_ent_f4')); ?></li>
                    </ul>
                    <p class="mt-6 text-3xl font-black text-white"><?php echo e(__($a.'.plan_custom')); ?></p>
                    <a href="<?php echo e(route('public.contact')); ?>" class="mt-6 block text-center py-3 rounded-xl border-2 border-white/25 text-white font-extrabold hover:bg-white/10 transition"><?php echo e(__($a.'.plan_contact')); ?></a>
                </div>
            </div>
        </div>
    </section>

    
    <section class="section-y stream-cta-band text-white border-t border-white/5">
        <div class="container-acad relative z-10 text-center py-8">
            <h2 class="text-3xl sm:text-4xl font-black max-w-3xl mx-auto leading-tight reveal"><?php echo e(__($a.'.cta_title')); ?></h2>
            <p class="mt-4 text-lg text-white/85 max-w-xl mx-auto reveal"><?php echo e(__($a.'.cta_sub')); ?></p>
            <a href="<?php echo e(route('register')); ?>" class="mt-8 inline-flex items-center gap-2 px-10 py-4 rounded-xl bg-acad-yellow text-acad-blue font-extrabold text-lg shadow-xl hover:brightness-105 transition reveal ring-2 ring-acad-yellow/40"><?php echo e(__($a.'.cta_button')); ?><i class="fas fa-play text-sm"></i></a>
        </div>
    </section>
</main>


<div id="market-search-overlay" class="fixed inset-0 z-[100050] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-[#0d1528]/80 backdrop-blur-xl transition-opacity" data-close-search tabindex="-1"></div>
    <div class="relative z-10 min-h-0 flex flex-col items-stretch pt-16 sm:pt-20 px-3 sm:px-6 pb-8 pointer-events-none">
        <div class="max-w-5xl w-full mx-auto glass-panel rounded-2xl border border-white/15 shadow-2xl pointer-events-auto search-overlay-enter max-h-[min(90vh,840px)] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between gap-3 p-4 border-b border-white/10">
                <p class="text-sm font-black text-acad-yellow"><?php echo e(__($a.'.search_live')); ?></p>
                <button type="button" class="w-10 h-10 rounded-xl border border-white/15 text-white hover:bg-white/10 transition font-bold" data-close-search aria-label="<?php echo e(__($a.'.search_close')); ?>"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-4 border-b border-white/10 shrink-0">
                <div class="flex items-center gap-3 rounded-2xl border border-white/12 bg-white/5 px-4 py-3 floating-search-glow">
                    <i class="fas fa-magnifying-glass text-acad-cyan/90 text-sm"></i>
                    <input type="search" id="market-overlay-input" autocomplete="off" placeholder="<?php echo e(__($a.'.search_placeholder')); ?>" class="flex-1 bg-transparent border-0 outline-none font-semibold text-white placeholder:text-white/40">
                </div>
                <div class="mt-4 flex flex-wrap gap-2" id="search-chip-row">
                    <?php $__currentLoopData = $searchChipsForJs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="search-chip text-xs font-extrabold px-3 py-1.5 rounded-full border border-white/15 bg-white/5 text-white/90 hover:bg-acad-yellow hover:text-acad-blue hover:border-acad-yellow transition" data-chip="<?php echo e($ch['id']); ?>"><?php echo e($ch['label']); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <input type="hidden" id="ov-active-chip" value="">
            </div>
            <div class="flex-1 min-h-0 overflow-y-auto p-4 space-y-6">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-extrabold text-white/45 uppercase mb-2"><?php echo e(__($a.'.search_recent')); ?></p>
                            <div id="market-recent-list" class="space-y-1 text-sm font-semibold text-acad-cyan"></div>
                        </div>
                        <div>
                            <p class="text-xs font-extrabold text-white/45 uppercase mb-2"><?php echo e(__($a.'.search_trending')); ?></p>
                            <div id="market-trending-list" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-extrabold text-white/45 uppercase mb-2"><?php echo e(__($a.'.search_results_row')); ?></p>
                        <div id="market-live-results" class="netflix-row pb-2 -mx-1"></div>
                        <p id="market-no-results" class="hidden text-sm text-white/50 font-medium py-8 text-center"><?php echo e(__($a.'.search_no_results')); ?></p>
                    </div>
                </div>
            </div>
            <form action="<?php echo e(route('public.courses')); ?>" method="get" class="p-4 border-t border-white/10 bg-black/20 shrink-0 flex flex-wrap gap-2 justify-end">
                <input type="hidden" name="q" id="market-overlay-q-hidden" value="">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-acad-yellow text-acad-blue font-extrabold hover:brightness-110 transition"><?php echo e(__($a.'.search_btn')); ?></button>
            </form>
        </div>
    </div>
</div>


<div id="quick-view-modal" class="fixed inset-0 z-[100060] hidden items-center justify-center p-4" aria-hidden="true">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-md" data-close-qv></div>
    <div class="relative z-10 w-full max-w-lg rounded-2xl bg-slate-900/95 shadow-2xl border border-white/10 overflow-hidden max-h-[90vh] overflow-y-auto">
        <button type="button" class="absolute top-3 <?php echo e($isRtl ? 'left-3' : 'right-3'); ?> w-10 h-10 rounded-full bg-white/10 border border-white/15 text-white hover:bg-white/20 z-10" data-close-qv aria-label="<?php echo e(__($a.'.search_close')); ?>"><i class="fas fa-times"></i></button>
        <div id="quick-view-body" class="p-6 pt-14 text-white"></div>
    </div>
</div>

<?php echo $__env->make('components.unified-footer', ['footerVariant' => 'stream'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(isset($popupAd) && $popupAd): ?>
    <?php echo $__env->make('partials.popup-ad', ['ad' => $popupAd], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<script>
(function(){
    'use strict';
    var isRtl = document.documentElement.dir === 'rtl';
    var suggests = <?php echo json_encode($searchSuggestions, 15, 512) ?>;
    var trendingLabels = <?php echo json_encode($trendingSearchLabels, 15, 512) ?>;
    var courseCatalog = <?php echo json_encode($courseCatalogForJs, 15, 512) ?>;
    var searchChips = <?php echo json_encode($searchChipsForJs, 15, 512) ?>;
    var currencyLabel = <?php echo json_encode(__('landing.currency'), 15, 512) ?>;
    var freeLabel = <?php echo json_encode(__('landing.free'), 15, 512) ?>;
    var enrollLabel = <?php echo json_encode(__('landing.academy.modal_enroll'), 15, 512) ?>;
    var streamPlayLabel = <?php echo json_encode(__($a.'.stream_play'), 15, 512) ?>;

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
            slides.forEach(function(s, j){ s.classList.toggle('is-active', j === i); });
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
                '<span class="px-3 py-1.5 rounded-lg bg-acad-yellow text-acad-blue text-[10px] sm:text-[11px] font-extrabold shadow-lg">' + esc(streamPlayLabel) + '</span>' +
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
                b.classList.toggle('bg-acad-yellow', on);
                b.classList.toggle('text-acad-blue', on);
                b.classList.toggle('border-acad-yellow', on);
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
                '<a href="' + esc(c.url) + '" class="mt-4 block text-center py-3 rounded-xl bg-acad-yellow text-acad-blue font-extrabold hover:brightness-110 transition">' + esc(enrollLabel) + '</a>';
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
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views/welcome.blade.php ENDPATH**/ ?>