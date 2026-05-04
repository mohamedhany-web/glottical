<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $thumbUrl = ($course->thumbnail ?? null) ? asset('storage/' . str_replace('\\','/', $course->thumbnail)) : null;
    $introVideoUrl = trim((string)($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    if (!$introEmbedUrl && $introVideoUrl !== '') {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        } elseif (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://player.vimeo.com/video/' . $m[1];
        }
    }
    $introDirectVideo = null;
    if (!$introEmbedUrl && $introVideoUrl !== '' && filter_var($introVideoUrl, FILTER_VALIDATE_URL)) {
        if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $introVideoUrl)) {
            $introDirectVideo = $introVideoUrl;
        }
    }
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <?php
        $courseOgImg  = $thumbUrl ?? asset('images/og-image.jpg');
        $courseDesc   = Str::limit(strip_tags($course->description ?? ''), 160);
        $courseTitle  = ($course->title ?? __('public.course_detail_title')) . ' | Muallimx';
        $courseUrl    = url('/course/' . ($course->id ?? ''));
    ?>
    <title><?php echo e($courseTitle); ?></title>
    <meta name="title"       content="<?php echo e($courseTitle); ?>">
    <meta name="description" content="<?php echo e($courseDesc); ?>">
    <meta name="keywords"    content="<?php echo e($course->title ?? 'كورس'); ?>, تعلم أونلاين, كورسات عربية, Muallimx, <?php echo e($categoryDisplay); ?>">
    <meta name="author"      content="<?php echo e(($course->instructor->name ?? null) ?? 'Muallimx'); ?>">
    <meta name="robots"      content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#283593">
    <link rel="canonical"    href="<?php echo e($courseUrl); ?>">
    <link rel="alternate" hreflang="ar"        href="<?php echo e($courseUrl); ?>?lang=ar">
    <link rel="alternate" hreflang="en"        href="<?php echo e($courseUrl); ?>?lang=en">
    <link rel="alternate" hreflang="x-default" href="<?php echo e($courseUrl); ?>">
    <!-- Open Graph -->
    <meta property="og:type"             content="article">
    <meta property="og:url"              content="<?php echo e($courseUrl); ?>">
    <meta property="og:title"            content="<?php echo e($courseTitle); ?>">
    <meta property="og:description"      content="<?php echo e($courseDesc); ?>">
    <meta property="og:image"            content="<?php echo e($courseOgImg); ?>">
    <meta property="og:image:alt"        content="<?php echo e($course->title ?? 'كورس'); ?>">
    <meta property="og:image:width"      content="1200">
    <meta property="og:image:height"     content="630">
    <meta property="og:locale"           content="<?php echo e($locale === 'ar' ? 'ar_AR' : 'en_US'); ?>">
    <meta property="og:site_name"        content="Muallimx">
    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:site"        content="@Muallimx">
    <meta name="twitter:url"         content="<?php echo e($courseUrl); ?>">
    <meta name="twitter:title"       content="<?php echo e($courseTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($courseDesc); ?>">
    <meta name="twitter:image"       content="<?php echo e($courseOgImg); ?>">
    <meta name="twitter:image:alt"   content="<?php echo e($course->title ?? 'كورس'); ?>">
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config={theme:{extend:{colors:{navy:{50:'#f0f4ff',100:'#dbe4ff',200:'#bac8ff',300:'#91a7ff',400:'#748ffc',500:'#5c7cfa',600:'#4c6ef5',700:'#4263eb',800:'#3b5bdb',900:'#364fc7',950:'#283593'},brand:{50:'#FFF3E0',100:'#FFE0B2',200:'#FFCC80',300:'#FFB74D',400:'#FFA726',500:'#FB5607',600:'#E04D00',700:'#BF360C',800:'#8D2600',900:'#5D1A00'},mx:{navy:'#283593',indigo:'#1F2A7A',orange:'#FB5607',rose:'#FFE5F7',gold:'#FFE569'}},fontFamily:{heading:['Cairo','Tajawal','IBM Plex Sans Arabic','sans-serif'],body:['Cairo','IBM Plex Sans Arabic','Tajawal','sans-serif']}}}}
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak]{display:none!important}
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden!important}
        body{overflow-x:hidden!important;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        body>*{flex-shrink:0}

        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width:768px){.container-1200{padding-inline:16px}}
        .reveal{opacity:0;transform:translateY(40px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
        .reveal.revealed{opacity:1;transform:translateY(0)}
        .stagger-1{transition-delay:.05s}.stagger-2{transition-delay:.1s}.stagger-3{transition-delay:.15s}.stagger-4{transition-delay:.2s}

        .glass{background:rgba(255,255,255,.75);backdrop-filter:blur(20px) saturate(180%);-webkit-backdrop-filter:blur(20px) saturate(180%);border:1px solid rgba(255,255,255,.5)}
        .glass-dark{background:rgba(15,23,42,.55);backdrop-filter:blur(20px) saturate(200%);-webkit-backdrop-filter:blur(20px) saturate(200%);border:1px solid rgba(255,255,255,.08)}

        .text-gradient{background:linear-gradient(135deg,#FB5607 0%,#283593 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .btn-primary{position:relative;overflow:hidden;transition:all .4s cubic-bezier(.16,1,.3,1)}
        .btn-primary::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.2),transparent);transition:left .6s}
        .btn-primary:hover::before{left:100%}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 20px 40px -12px rgba(251,86,7,.35)}
        .btn-outline{transition:all .3s cubic-bezier(.16,1,.3,1)}
        .btn-outline:hover{transform:translateY(-2px);box-shadow:0 10px 30px -10px rgba(15,23,42,.2)}
        .card-hover{transition:all .4s cubic-bezier(.16,1,.3,1)}
        .card-hover:hover{transform:translateY(-8px);box-shadow:0 25px 60px -15px rgba(0,0,0,.12)}
        .noise::after{content:'';position:absolute;inset:0;opacity:.02;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");pointer-events:none}
        #scroll-progress{position:fixed;top:0;left:0;width:0%;height:3px;background:linear-gradient(90deg,#FB5607,#FFE569);z-index:9999;transition:width .1s linear}
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .line-clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{background:rgba(31,42,122,.92)!important;backdrop-filter:blur(12px)!important;-webkit-backdrop-filter:blur(12px)!important;border-bottom:1px solid rgba(255,255,255,.08)!important}
        @media(max-width:768px){.reveal{transition-duration:.5s}.stagger-1,.stagger-2,.stagger-3,.stagger-4{transition-delay:0s}}
    </style>
</head>
<body class="bg-white text-slate-800 antialiased font-body">
    <div id="scroll-progress"></div>
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>.navbar-spacer{display:block}</style>

    <main class="flex-1">
        
        <?php $__currentLoopData = ['success' => 'emerald', 'info' => 'brand', 'error' => 'red']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session($type)): ?>
            <div class="max-w-5xl mx-auto px-5 sm:px-8 pt-24 pb-2" x-data="{s:true}" x-show="s" <?php if($type!=='error'): ?> x-init="setTimeout(()=>s=false,6000)" <?php endif; ?>>
                <div class="rounded-2xl border border-<?php echo e($color); ?>-200 bg-<?php echo e($color); ?>-50 px-5 py-4 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-<?php echo e($type==='success'?'check-circle':($type==='info'?'info-circle':'exclamation-circle')); ?> text-<?php echo e($color); ?>-600"></i>
                    <p class="text-<?php echo e($color); ?>-800 font-semibold flex-1"><?php echo e(session($type)); ?></p>
                    <button @click="s=false" class="text-<?php echo e($color); ?>-600 hover:text-<?php echo e($color); ?>-800"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
            <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>

            <div class="container-1200 relative z-10">
                
                <nav class="reveal text-sm text-slate-500 mb-8 flex items-center gap-2 flex-wrap">
                    <a href="<?php echo e(url('/')); ?>" class="hover:text-mx-indigo transition-colors"><?php echo e(__('public.home')); ?></a>
                    <i class="fas fa-chevron-<?php echo e($isRtl?'left':'right'); ?> text-[8px] text-slate-400"></i>
                    <a href="<?php echo e(route('public.courses')); ?>" class="hover:text-mx-indigo transition-colors"><?php echo e(__('public.courses')); ?></a>
                    <i class="fas fa-chevron-<?php echo e($isRtl?'left':'right'); ?> text-[8px] text-slate-400"></i>
                    <span class="text-mx-indigo font-semibold"><?php echo e(Str::limit($course->title ?? '', 40)); ?></span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12 items-start">
                    
                    <div class="lg:col-span-3 reveal">
                        <?php if($course->is_featured ?? false): ?>
                            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-amber-400/90 text-amber-900 text-xs font-bold mb-5 shadow-lg shadow-amber-500/20">
                                <i class="fas fa-star"></i>
                                <?php echo e(__('public.featured_course_badge')); ?>

                            </span>
                        <?php endif; ?>

                        <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.75rem] font-black text-mx-indigo leading-[1.15] mb-5">
                            <?php echo e($course->title ?? __('public.course_title_fallback')); ?>

                        </h1>

                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed line-clamp-3 mb-8 max-w-2xl">
                            <?php echo e($course->description ?? __('public.course_desc_fallback')); ?>

                        </p>

                        
                        <div class="flex flex-wrap gap-3 mb-8">
                            <?php
                            $heroBadges = [
                                ['icon'=>'fa-chalkboard-teacher','label'=>($course->lessons_count ?? 0).' '.__('public.lecture_single'),'color'=>'brand'],
                                ['icon'=>'fa-clock','label'=>($course->duration_hours ?? 0).' '.__('public.hours'),'color'=>'blue'],
                                ['icon'=>'fa-folder-open','label'=>$categoryDisplay,'color'=>'purple'],
                            ];
                            ?>
                            <?php $__currentLoopData = $heroBadges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-mx-indigo text-sm font-semibold shadow-sm">
                                <i class="fas <?php echo e($badge['icon']); ?> text-<?php echo e($badge['color']); ?>-400"></i>
                                <span><?php echo e($badge['label']); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php if($course->instructor): ?>
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 rounded-xl bg-[#FFE5F7] flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-[#FB5607]"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 font-medium"><?php echo e(__('public.instructor_label')); ?></p>
                                    <?php if(\App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists()): ?>
                                        <a href="<?php echo e(route('public.instructors.show', $course->instructor)); ?>" class="text-mx-indigo font-bold hover:text-[#FB5607] transition-colors"><?php echo e($course->instructor->name); ?></a>
                                    <?php else: ?>
                                        <span class="text-mx-indigo font-bold"><?php echo e($course->instructor->name); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                        <div class="flex flex-wrap gap-3">
                            <?php if(auth()->guard()->check()): ?>
                                <?php if($isEnrolled ?? false): ?>
                                    <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="btn-primary inline-flex items-center gap-2.5 bg-gradient-to-l from-brand-500 to-brand-600 text-white px-7 py-3.5 rounded-2xl font-bold shadow-xl shadow-brand-600/25 text-base">
                                        <i class="fas fa-play-circle"></i> <?php echo e(__('public.start_learning_now')); ?>

                                    </a>
                                <?php elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                                    <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="btn-primary inline-flex items-center gap-2.5 bg-gradient-to-l from-brand-500 to-brand-600 text-white px-7 py-3.5 rounded-2xl font-bold shadow-xl shadow-brand-600/25 text-base">
                                        <i class="fas fa-shopping-cart"></i> <?php echo e(__('public.buy_now')); ?>

                                    </a>
                                <?php else: ?>
                                    <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-primary inline-flex items-center gap-2.5 bg-gradient-to-l from-emerald-500 to-emerald-600 text-white px-7 py-3.5 rounded-2xl font-bold shadow-xl shadow-emerald-600/25 text-base cursor-pointer">
                                            <i class="fas fa-gift"></i> <?php echo e(__('public.register_free')); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if(auth()->guard()->guest()): ?>
                                <?php if($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                                    <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="btn-primary inline-flex items-center gap-2.5 bg-gradient-to-l from-brand-500 to-brand-600 text-white px-7 py-3.5 rounded-2xl font-bold shadow-xl shadow-brand-600/25 text-base">
                                        <i class="fas fa-shopping-cart"></i> <?php echo e(__('public.buy_now')); ?>

                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('register', ['redirect' => route('public.course.show', $course->id)])); ?>" class="btn-primary inline-flex items-center gap-2.5 bg-gradient-to-l from-emerald-500 to-emerald-600 text-white px-7 py-3.5 rounded-2xl font-bold shadow-xl shadow-emerald-600/25 text-base">
                                        <i class="fas fa-gift"></i> <?php echo e(__('public.register_free')); ?>

                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="<?php echo e(route('public.courses')); ?>" class="btn-outline inline-flex items-center gap-2 bg-white border-2 border-slate-200 text-mx-indigo hover:bg-slate-50 px-6 py-3.5 rounded-2xl font-bold text-base">
                                <i class="fas fa-arrow-<?php echo e($isRtl?'right':'left'); ?> text-sm"></i>
                                <?php echo e(__('public.all_courses')); ?>

                            </a>
                        </div>
                    </div>

                    
                    <div class="lg:col-span-2 reveal stagger-2">
                        <?php if($introEmbedUrl): ?>
                        <div class="card-hover rounded-3xl overflow-hidden border border-slate-200 shadow-xl bg-slate-900 ring-1 ring-slate-200/80">
                            <div class="aspect-video w-full">
                                <iframe src="<?php echo e($introEmbedUrl); ?>" title="<?php echo e(__('public.course_intro_video')); ?>"
                                    class="w-full h-full min-h-[220px]"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                    allowfullscreen loading="lazy"></iframe>
                            </div>
                        </div>
                        <?php elseif($introDirectVideo): ?>
                        <div class="card-hover rounded-3xl overflow-hidden border border-slate-200 shadow-xl bg-black ring-1 ring-slate-200/80">
                            <div class="aspect-video w-full">
                                <video src="<?php echo e($introDirectVideo); ?>" controls playsinline preload="metadata" class="w-full h-full object-contain">
                                    <?php echo e(__('public.course_intro_video_unsupported')); ?>

                                </video>
                            </div>
                        </div>
                        <?php elseif($thumbUrl): ?>
                        <div class="card-hover rounded-3xl overflow-hidden border border-slate-200 shadow-xl aspect-video bg-white ring-1 ring-slate-200/80">
                            <img src="<?php echo e($thumbUrl); ?>" alt="<?php echo e($course->title); ?>" class="w-full h-full object-cover">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="py-20 md:py-28 bg-white">
            <div class="container-1200">

                
                <div class="reveal grid grid-cols-2 sm:grid-cols-4 gap-4 mb-16">
                    <?php
                    $infoCards = [
                        ['icon'=>'fa-clock','label'=>__('public.duration'),'value'=>($course->duration_hours ?? 0).' '.__('public.hours'),'color'=>'brand'],
                        ['icon'=>'fa-chalkboard-teacher','label'=>__('public.lectures_count_label'),'value'=>($course->lessons_count ?? 0).' '.__('public.lecture_single'),'color'=>'blue'],
                        ['icon'=>'fa-folder-open','label'=>__('public.course_category_label'),'value'=>$categoryDisplay,'color'=>'purple'],
                        ['icon'=>'fa-book','label'=>__('public.subject_label'),'value'=>$course->academicSubject->name ?? __('public.course_category_not_set'),'color'=>'emerald'],
                    ];
                    ?>
                    <?php $__currentLoopData = $infoCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $ic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card-hover rounded-3xl bg-white border border-slate-100 p-5 sm:p-6 shadow-sm hover:shadow-xl hover:border-<?php echo e($ic['color']); ?>-200/50 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-<?php echo e($ic['color']); ?>-50 flex items-center justify-center mx-auto mb-3">
                            <i class="fas <?php echo e($ic['icon']); ?> text-<?php echo e($ic['color']); ?>-500 text-xl"></i>
                        </div>
                        <p class="font-heading text-xl sm:text-2xl font-black text-navy-950 mb-1"><?php echo e($ic['value']); ?></p>
                        <p class="text-xs text-slate-500 font-medium"><?php echo e($ic['label']); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                    
                    <div class="lg:col-span-2 space-y-8">
                        
                        <div class="reveal card-hover rounded-3xl bg-white border border-slate-100 p-6 sm:p-8 shadow-sm hover:shadow-xl hover:border-brand-200/50">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-11 h-11 rounded-xl bg-brand-50 flex items-center justify-center"><i class="fas fa-info-circle text-brand-500 text-xl"></i></div>
                                <h2 class="font-heading text-2xl font-black text-navy-950"><?php echo e(__('public.about_course')); ?></h2>
                            </div>
                            <div class="text-slate-600 leading-relaxed text-base">
                                <p><?php echo e($course->description ?? __('public.course_desc_fallback')); ?></p>
                                <?php if($course->objectives): ?>
                                <div class="mt-6">
                                    <h3 class="font-heading text-lg font-bold text-navy-950 mb-3"><?php echo e(__('public.course_objectives')); ?></h3>
                                    <div class="bg-gradient-to-br from-brand-50/60 to-blue-50/40 rounded-2xl p-6 border border-brand-100/50">
                                        <p class="whitespace-pre-line text-slate-700"><?php echo e($course->objectives); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <?php if($course->what_you_learn): ?>
                        <div class="reveal stagger-1 card-hover rounded-3xl bg-white border border-slate-100 p-6 sm:p-8 shadow-sm hover:shadow-xl hover:border-emerald-200/50">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center"><i class="fas fa-graduation-cap text-emerald-500 text-xl"></i></div>
                                <h2 class="font-heading text-2xl font-black text-navy-950"><?php echo e(__('public.what_you_learn')); ?></h2>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <?php $__currentLoopData = array_filter(explode("\n", $course->what_you_learn)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-emerald-50/60 to-brand-50/30 border border-emerald-100/60 hover:border-emerald-200 transition-colors">
                                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span class="text-slate-700 text-sm leading-relaxed"><?php echo e(trim($point)); ?></span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        
                        <?php if($course->requirements): ?>
                        <div class="reveal stagger-2 card-hover rounded-3xl bg-white border border-slate-100 p-6 sm:p-8 shadow-sm hover:shadow-xl hover:border-amber-200/50">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fas fa-list-check text-amber-500 text-xl"></i></div>
                                <h2 class="font-heading text-2xl font-black text-navy-950"><?php echo e(__('public.requirements')); ?></h2>
                            </div>
                            <div class="bg-gradient-to-br from-amber-50/50 to-slate-50/30 rounded-2xl p-6 border border-amber-100/50">
                                <p class="text-slate-700 whitespace-pre-line leading-relaxed"><?php echo e($course->requirements); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="lg:col-span-1">
                        
                        <div class="reveal sticky top-24 space-y-6">
                            <div class="card-hover rounded-3xl bg-white border border-slate-100 shadow-lg overflow-hidden">
                                
                                <div class="bg-gradient-to-l from-brand-500 to-brand-600 p-5 text-center">
                                    <?php if($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                                        <?php if($course->hasPromotionalPrice()): ?>
                                            <div class="text-sm text-white/80 line-through mb-1 tabular-nums"><?php echo e(number_format($course->listPriceAmount(), 0)); ?> <?php echo e(__('public.currency_egp')); ?></div>
                                            <div class="text-3xl font-black text-white tabular-nums"><?php echo e(number_format($course->effectivePurchasePrice(), 0)); ?> <span class="text-lg font-medium text-white/80"><?php echo e(__('public.currency_egp')); ?></span></div>
                                        <?php else: ?>
                                            <div class="text-3xl font-black text-white tabular-nums"><?php echo e(number_format($course->effectivePurchasePrice(), 0)); ?> <span class="text-lg font-medium text-white/80"><?php echo e(__('public.currency_egp')); ?></span></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-2xl font-black text-white flex items-center justify-center gap-2"><i class="fas fa-gift text-xl"></i><?php echo e(__('public.free_price')); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-6">
                                    <dl class="space-y-3 mb-6">
                                        <div class="flex justify-between items-center p-3 bg-slate-50/80 rounded-xl text-sm">
                                            <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-clock text-brand-500"></i> <?php echo e(__('public.duration')); ?></span>
                                            <span class="font-bold text-navy-950"><?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('public.hours')); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-slate-50/80 rounded-xl text-sm">
                                            <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-blue-500"></i> <?php echo e(__('public.lectures_count_label')); ?></span>
                                            <span class="font-bold text-navy-950"><?php echo e($course->lessons_count ?? 0); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-slate-50/80 rounded-xl text-sm">
                                            <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-folder-open text-purple-500"></i> <?php echo e(__('public.course_category_label')); ?></span>
                                            <span class="font-bold text-navy-950"><?php echo e($categoryDisplay); ?></span>
                                        </div>
                                    </dl>
                                    <?php if(auth()->guard()->check()): ?>
                                        <?php if($isEnrolled ?? false): ?>
                                            <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="btn-primary block w-full text-center py-3.5 rounded-2xl bg-gradient-to-l from-brand-500 to-brand-600 text-white font-bold shadow-lg">
                                                <i class="fas fa-play-circle <?php echo e($isRtl?'ml-2':'mr-2'); ?>"></i><?php echo e(__('public.start_learning_now')); ?>

                                            </a>
                                        <?php elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                                            <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="btn-primary block w-full text-center py-3.5 rounded-2xl bg-gradient-to-l from-brand-500 to-brand-600 text-white font-bold shadow-lg">
                                                <i class="fas fa-shopping-cart <?php echo e($isRtl?'ml-2':'mr-2'); ?>"></i><?php echo e(__('public.buy_now')); ?>

                                            </a>
                                        <?php else: ?>
                                            <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn-primary block w-full text-center py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg cursor-pointer">
                                                    <i class="fas fa-gift <?php echo e($isRtl?'ml-2':'mr-2'); ?>"></i><?php echo e(__('public.register_free')); ?>

                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(auth()->guard()->guest()): ?>
                                        <?php if($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                                            <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="btn-primary block w-full text-center py-3.5 rounded-2xl bg-gradient-to-l from-brand-500 to-brand-600 text-white font-bold shadow-lg">
                                                <i class="fas fa-shopping-cart <?php echo e($isRtl?'ml-2':'mr-2'); ?>"></i><?php echo e(__('public.buy_now')); ?>

                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('register', ['redirect' => route('public.course.show', $course->id)])); ?>" class="btn-primary block w-full text-center py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg">
                                                <i class="fas fa-gift <?php echo e($isRtl?'ml-2':'mr-2'); ?>"></i><?php echo e(__('public.register_free')); ?>

                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <?php if(isset($relatedCourses) && $relatedCourses->isNotEmpty()): ?>
                            <div class="rounded-3xl bg-white border border-slate-100 p-6 shadow-sm">
                                <h3 class="font-heading text-lg font-bold text-navy-950 mb-4 flex items-center gap-2">
                                    <i class="fas fa-bookmark text-brand-500"></i>
                                    كورسات ذات صلة
                                </h3>
                                <div class="space-y-3">
                                    <?php $__currentLoopData = $relatedCourses->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $relThumb = $related->thumbnail ? str_replace('\\','/', $related->thumbnail) : null; ?>
                                        <a href="<?php echo e(route('public.course.show', $related->id)); ?>" class="flex gap-3 p-3 rounded-2xl border border-slate-100 hover:border-brand-200 hover:shadow-md transition-all duration-300 group">
                                            <div class="w-16 h-16 flex-shrink-0 rounded-xl bg-gradient-to-br from-brand-500 to-navy-600 overflow-hidden flex items-center justify-center">
                                                <?php if($relThumb): ?>
                                                    <img src="<?php echo e(asset('storage/' . $relThumb)); ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                                <?php else: ?>
                                                    <i class="fas fa-book text-white/80 text-lg"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-navy-950 text-sm group-hover:text-brand-600 transition-colors line-clamp-2 leading-snug"><?php echo e($related->title); ?></h4>
                                                <div class="mt-1 block text-start">
                                                    <?php if (isset($component)) { $__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.advanced-course-card-price','data' => ['course' => $related,'size' => 'sm','class' => '!items-start']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('advanced-course-card-price'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($related),'size' => 'sm','class' => '!items-start']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542)): ?>
<?php $attributes = $__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542; ?>
<?php unset($__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542)): ?>
<?php $component = $__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542; ?>
<?php unset($__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542); ?>
<?php endif; ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="py-20 md:py-28 bg-slate-50/50">
            <div class="container-1200 text-center reveal">
                <h2 class="font-heading text-3xl sm:text-4xl md:text-5xl font-black text-navy-950 mb-5 leading-tight">
                    جاهز للانطلاق في هذا الكورس؟
                </h2>
                <p class="text-lg text-slate-500 leading-relaxed mb-10 font-medium">
                    سجّل الآن وابدأ التعلم بخطوات واضحة وتجربة احترافية متكاملة.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="btn-primary inline-flex items-center justify-center gap-3 bg-gradient-to-l from-brand-500 to-brand-600 text-white font-bold text-lg px-8 py-4 rounded-2xl shadow-xl shadow-brand-600/25">
                            <?php echo e(__('public.start_learning_now')); ?> <i class="fas fa-arrow-<?php echo e($isRtl?'left':'right'); ?> text-sm"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('register')); ?>" class="btn-primary inline-flex items-center justify-center gap-3 bg-gradient-to-l from-brand-500 to-brand-600 text-white font-bold text-lg px-8 py-4 rounded-2xl shadow-xl shadow-brand-600/25">
                            <?php echo e(__('public.register_free_now')); ?> <i class="fas fa-arrow-<?php echo e($isRtl?'left':'right'); ?> text-sm"></i>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('public.courses')); ?>" class="btn-outline inline-flex items-center justify-center gap-3 bg-white border-2 border-slate-200 hover:border-brand-300 text-navy-950 font-semibold text-lg px-8 py-4 rounded-2xl">
                        <?php echo e(__('public.all_courses')); ?> <i class="fas fa-arrow-<?php echo e($isRtl?'left':'right'); ?> text-sm"></i>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script>
    (function(){
        function p(){var s=window.pageYOffset||document.documentElement.scrollTop,h=document.documentElement.scrollHeight-window.innerHeight,b=document.getElementById('scroll-progress');if(b)b.style.width=(h>0?(s/h)*100:0)+'%';}
        window.addEventListener('scroll',p,{passive:true});
        function r(){var t=document.querySelectorAll('.reveal');if(!t.length)return;var o=new IntersectionObserver(function(e){e.forEach(function(n){if(n.isIntersecting){n.target.classList.add('revealed');o.unobserve(n.target);}});},{threshold:.08,rootMargin:'0px 0px -40px 0px'});t.forEach(function(el){o.observe(el);});}
        if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',r);else r();
    })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\course-show.blade.php ENDPATH**/ ?>