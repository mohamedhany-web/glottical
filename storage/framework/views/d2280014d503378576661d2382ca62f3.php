<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $a = 'landing.academy';
    $name = $profile->user->name ?? __('public.instructor_fallback');
    $headline = $profile->headline_clean ?: __('public.instructor_fallback');
    $bioClean = $profile->bio_clean;
    $skills = $profile->skills_list;
    $experiences = $profile->experience_list;
    $instrPageTitle = $name.' — '.$headline.' | '.config('app.name', 'Glottical');
    $instrPageDesc = \Illuminate\Support\Str::limit($bioClean ?: $headline, 160);
    $instrPageImg = ($profile->photo_url ?? null) ?: asset('images/og-image.jpg');
    $instrPageUrl = route('public.instructors.show', $profile->user);
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e($instrPageTitle); ?></title>
    <meta name="title" content="<?php echo e($instrPageTitle); ?>">
    <meta name="description" content="<?php echo e($instrPageDesc); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#0d1528">
    <link rel="canonical" href="<?php echo e($instrPageUrl); ?>">
    <link rel="alternate" hreflang="ar" href="<?php echo e($instrPageUrl); ?>?lang=ar">
    <link rel="alternate" hreflang="en" href="<?php echo e($instrPageUrl); ?>?lang=en">
    <link rel="alternate" hreflang="x-default" href="<?php echo e($instrPageUrl); ?>">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="<?php echo e($instrPageUrl); ?>">
    <meta property="og:title" content="<?php echo e($instrPageTitle); ?>">
    <meta property="og:description" content="<?php echo e($instrPageDesc); ?>">
    <meta property="og:image" content="<?php echo e($instrPageImg); ?>">
    <meta property="og:locale" content="<?php echo e($locale === 'ar' ? 'ar_AR' : 'en_US'); ?>">
    <meta property="og:site_name" content="<?php echo e(config('app.name', 'Glottical')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'instructor', 'profile' => $profile], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        acad: {
                            blue: '#0B3D91',
                            cyan: '#00A3C4',
                            yellow: '#F5B800',
                            navy: '#0d1528',
                            navyMid: '#1a2d4d',
                        },
                    },
                    fontFamily: {
                        sans: ['Cairo', 'Tajawal', 'IBM Plex Sans Arabic', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <?php echo $__env->make('partials.public-academy-surface', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .line-clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
        .reveal.s1{transition-delay:.06s}.reveal.s2{transition-delay:.12s}.reveal.s3{transition-delay:.18s}
        .instr-avatar-ring{box-shadow:0 0 0 4px rgba(245,184,0,.35),0 24px 48px -20px rgba(0,163,196,.4)}
        .skill-chip{word-break:break-word}
    </style>
</head>
<body class="page-academy font-sans antialiased text-white">
    <div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>

    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1">
        
        <section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-14 overflow-hidden relative">
            <div class="absolute inset-0 bg-acad-navy"></div>
            <div class="absolute inset-0 opacity-[0.18] bg-cover bg-center"
                 style="background-image:url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=82')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/90 to-acad-navy/40"></div>
            <div class="absolute inset-0 pattern-dots opacity-[0.12] pointer-events-none"></div>

            <div class="container-acad relative z-10">
                <nav class="reveal text-sm text-white/50 mb-8 flex items-center gap-2 flex-wrap">
                    <a href="<?php echo e(route('home')); ?>" class="hover:text-acad-cyan transition-colors"><?php echo e(__('public.home')); ?></a>
                    <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] opacity-60"></i>
                    <a href="<?php echo e(route('public.instructors.index')); ?>" class="hover:text-acad-cyan transition-colors"><?php echo e(__('public.instructors_page_title')); ?></a>
                    <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] opacity-60"></i>
                    <span class="text-white font-semibold truncate max-w-[14rem] sm:max-w-none"><?php echo e($name); ?></span>
                </nav>

                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-center lg:items-start">
                    <div class="reveal shrink-0">
                        <?php if (isset($component)) { $__componentOriginalf5c368e96b80ecea05cc5aab59adcf79 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf5c368e96b80ecea05cc5aab59adcf79 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.instructor-avatar','data' => ['profile' => $profile,'size' => 'xl','rounded' => '2xl','class' => 'instr-avatar-ring border border-white/15']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('instructor-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['profile' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile),'size' => 'xl','rounded' => '2xl','class' => 'instr-avatar-ring border border-white/15']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf5c368e96b80ecea05cc5aab59adcf79)): ?>
<?php $attributes = $__attributesOriginalf5c368e96b80ecea05cc5aab59adcf79; ?>
<?php unset($__attributesOriginalf5c368e96b80ecea05cc5aab59adcf79); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf5c368e96b80ecea05cc5aab59adcf79)): ?>
<?php $component = $__componentOriginalf5c368e96b80ecea05cc5aab59adcf79; ?>
<?php unset($__componentOriginalf5c368e96b80ecea05cc5aab59adcf79); ?>
<?php endif; ?>
                    </div>

                    <div class="reveal s1 flex-1 min-w-0 text-center lg:text-start">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold mb-4 glass-panel border border-white/10 text-acad-cyan">
                            <i class="fas fa-check-circle text-[10px]"></i>
                            <?php echo e(__('public.instructors_verified')); ?>

                        </span>

                        <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-black text-white leading-tight mb-2 font-display">
                            <?php echo e($name); ?>

                        </h1>

                        <p class="text-lg sm:text-xl font-bold text-acad-yellow mb-5 leading-relaxed">
                            <?php echo e($headline); ?>

                        </p>

                        <?php if($bioClean): ?>
                            <p class="text-white/65 text-base leading-8 mb-6 max-w-3xl mx-auto lg:mx-0 line-clamp-3">
                                <?php echo e($bioClean); ?>

                            </p>
                        <?php endif; ?>

                        <div class="flex flex-wrap gap-2.5 justify-center lg:justify-start mb-6">
                            <div class="flex items-center gap-2 px-4 py-2 rounded-xl glass-panel border border-white/10 text-sm font-bold">
                                <i class="fas fa-book-open text-acad-yellow"></i>
                                <span><?php echo e($courses->count()); ?> <?php echo e($courses->count() === 1 ? __('public.instructors_course_one') : __('public.instructors_course_many')); ?></span>
                            </div>
                            <?php if(count($skills) > 0): ?>
                            <div class="flex items-center gap-2 px-4 py-2 rounded-xl glass-panel border border-white/10 text-sm font-bold">
                                <i class="fas fa-cogs text-acad-cyan"></i>
                                <span><?php echo e(count($skills)); ?> <?php echo e(__('public.skills')); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if(count($experiences) > 0): ?>
                            <div class="flex items-center gap-2 px-4 py-2 rounded-xl glass-panel border border-white/10 text-sm font-bold">
                                <i class="fas fa-briefcase text-violet-300"></i>
                                <span><?php echo e(count($experiences)); ?> <?php echo e(__('public.experience')); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                            <?php if($courses->count() > 0): ?>
                                <a href="#instructor-courses" class="btn-stream-primary text-sm">
                                    <i class="fas fa-graduation-cap"></i>
                                    <?php echo e(__('public.instructor_courses')); ?>

                                </a>
                            <?php endif; ?>
                            <?php if(isset($consultationSetting) && $consultationSetting->is_active): ?>
                                <?php if(auth()->guard()->check()): ?>
                                    <?php if(auth()->user()->isStudent()): ?>
                                        <a href="<?php echo e(route('consultations.create', $profile->user)); ?>" class="btn-stream-secondary text-sm !border-acad-cyan/40 !text-acad-cyan hover:!bg-acad-cyan/10">
                                            <i class="fas fa-comments"></i>
                                            <?php echo e(__('public.instructors_consult_cta')); ?>

                                            <span class="text-white/50 font-normal">— <?php echo e(number_format($profile->effectiveConsultationPriceEgp(), 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="<?php echo e(route('login', ['redirect' => route('consultations.create', $profile->user)])); ?>" class="btn-stream-secondary text-sm !border-acad-cyan/40 !text-acad-cyan hover:!bg-acad-cyan/10">
                                        <i class="fas fa-comments"></i>
                                        <?php echo e(__('public.instructors_consult_cta')); ?>

                                        <span class="text-white/50 font-normal">— <?php echo e(number_format($profile->effectiveConsultationPriceEgp(), 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="py-12 sm:py-16 relative">
            <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/40 to-acad-navy pointer-events-none"></div>
            <div class="container-acad relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                    <div class="lg:col-span-2 space-y-8">
                        <?php if($bioClean): ?>
                        <article class="reveal glass-panel rounded-2xl p-6 sm:p-8 border border-white/10">
                            <header class="flex items-center gap-3 mb-5">
                                <span class="w-11 h-11 rounded-xl bg-acad-cyan/15 flex items-center justify-center text-acad-cyan"><i class="fas fa-user-circle text-xl"></i></span>
                                <h2 class="text-2xl font-black text-white"><?php echo e(__('public.instructor_bio_title')); ?></h2>
                            </header>
                            <div class="text-white/75 leading-8 text-base whitespace-pre-line"><?php echo e($bioClean); ?></div>
                        </article>
                        <?php endif; ?>

                        <?php if(count($experiences) > 0 || $profile->experience): ?>
                        <article class="reveal s1 glass-panel rounded-2xl p-6 sm:p-8 border border-white/10">
                            <header class="flex items-center gap-3 mb-5">
                                <span class="w-11 h-11 rounded-xl bg-amber-500/15 flex items-center justify-center text-amber-400"><i class="fas fa-briefcase text-xl"></i></span>
                                <h2 class="text-2xl font-black text-white"><?php echo e(__('public.experience')); ?></h2>
                            </header>
                            <?php if(count($experiences) > 0): ?>
                                <ul class="space-y-3">
                                    <?php $__currentLoopData = $experiences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="flex items-start gap-3 p-4 rounded-xl border border-white/8 bg-white/[0.03]">
                                        <span class="w-7 h-7 rounded-lg bg-acad-yellow/15 text-acad-yellow flex items-center justify-center shrink-0 mt-0.5"><i class="fas fa-check text-[10px]"></i></span>
                                        <span class="text-white/80 text-sm leading-relaxed flex-1"><?php echo e($item); ?></span>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-white/75 leading-8 whitespace-pre-line"><?php echo e($profile->sanitizedText($profile->experience)); ?></p>
                            <?php endif; ?>
                        </article>
                        <?php endif; ?>

                        <?php if($oneToOneCourses->isNotEmpty()): ?>
                        <article id="instructor-courses" class="reveal s2 glass-panel rounded-2xl p-6 sm:p-8 border border-violet-500/20">
                            <header class="flex flex-wrap items-center justify-between gap-3 mb-6">
                                <div class="flex items-center gap-3">
                                    <span class="w-11 h-11 rounded-xl bg-violet-500/20 flex items-center justify-center text-violet-300"><i class="fas fa-user-graduate text-xl"></i></span>
                                    <div>
                                        <h2 class="text-2xl font-black text-white"><?php echo e(__('public.instructor_one_to_one_courses')); ?></h2>
                                        <p class="text-sm text-white/50 mt-0.5"><?php echo e(__('public.instructor_one_to_one_sub')); ?></p>
                                    </div>
                                </div>
                            </header>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php $__currentLoopData = $oneToOneCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('partials.instructor-course-card', ['course' => $c, 'isRtl' => $isRtl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </article>
                        <?php endif; ?>

                        <?php if($groupCourses->isNotEmpty()): ?>
                        <article id="<?php echo e($oneToOneCourses->isEmpty() ? 'instructor-courses' : ''); ?>" class="reveal s2 glass-panel rounded-2xl p-6 sm:p-8 border border-white/10">
                            <header class="flex items-center gap-3 mb-6">
                                <span class="w-11 h-11 rounded-xl bg-acad-yellow/15 flex items-center justify-center text-acad-yellow"><i class="fas fa-graduation-cap text-xl"></i></span>
                                <h2 class="text-2xl font-black text-white"><?php echo e(__('public.instructor_courses')); ?></h2>
                            </header>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php $__currentLoopData = $groupCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('partials.instructor-course-card', ['course' => $c, 'isRtl' => $isRtl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </article>
                        <?php elseif($courses->isEmpty()): ?>
                        <article class="reveal s2 glass-panel rounded-2xl p-10 border border-white/10 text-center">
                            <i class="fas fa-book-open text-4xl text-white/25 mb-4"></i>
                            <p class="text-white/55"><?php echo e(__('public.instructor_no_courses_yet')); ?></p>
                            <a href="<?php echo e(route('public.courses')); ?>" class="btn-stream-primary mt-5 text-sm"><?php echo e(__('public.browse_courses')); ?></a>
                        </article>
                        <?php endif; ?>
                    </div>

                    <aside class="lg:col-span-1">
                        <div class="lg:sticky lg:top-28 space-y-6">
                            <?php if(count($skills) > 0): ?>
                            <div class="reveal glass-panel rounded-2xl overflow-hidden border border-white/10">
                                <div class="px-5 py-4 border-b border-white/10 bg-gradient-to-l from-acad-blue/40 to-acad-cyan/20">
                                    <h3 class="text-lg font-black text-white flex items-center gap-2">
                                        <i class="fas fa-cogs text-acad-cyan"></i>
                                        <?php echo e(__('public.skills')); ?>

                                    </h3>
                                </div>
                                <div class="p-5 flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="skill-chip px-3 py-2 rounded-xl text-sm font-medium text-white/85 bg-white/[0.06] border border-white/10 leading-snug"><?php echo e($skill); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="reveal s1 glass-panel rounded-2xl overflow-hidden border border-white/10">
                                <div class="px-5 py-4 border-b border-white/10">
                                    <h3 class="text-lg font-black text-white flex items-center gap-2">
                                        <i class="fas fa-info-circle text-acad-yellow"></i>
                                        <?php echo e(__('public.instructor_quick_info')); ?>

                                    </h3>
                                </div>
                                <div class="p-5 space-y-2.5">
                                    <div class="flex justify-between items-center p-3 rounded-xl bg-white/[0.04] border border-white/8 text-sm">
                                        <span class="text-white/55 flex items-center gap-2"><i class="fas fa-book-open text-acad-yellow"></i> <?php echo e(__('public.instructors_course_many')); ?></span>
                                        <span class="font-black text-white"><?php echo e($courses->count()); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 rounded-xl bg-white/[0.04] border border-white/8 text-sm">
                                        <span class="text-white/55 flex items-center gap-2"><i class="fas fa-cogs text-acad-cyan"></i> <?php echo e(__('public.skills')); ?></span>
                                        <span class="font-black text-white"><?php echo e(count($skills)); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 rounded-xl bg-white/[0.04] border border-white/8 text-sm">
                                        <span class="text-white/55 flex items-center gap-2"><i class="fas fa-check-circle text-emerald-400"></i> <?php echo e(__('public.instructor_status_label')); ?></span>
                                        <span class="font-black text-emerald-400"><?php echo e(__('public.instructors_verified')); ?></span>
                                    </div>
                                </div>
                                <div class="px-5 pb-5 space-y-2">
                                    <a href="<?php echo e(route('public.courses')); ?>" class="btn-stream-primary w-full text-sm !py-3">
                                        <i class="fas fa-graduation-cap"></i>
                                        <?php echo e(__('public.browse_courses')); ?>

                                    </a>
                                    <?php if($oneToOneCourses->isNotEmpty()): ?>
                                    <a href="<?php echo e(route('public.courses', ['delivery' => 'one_to_one'])); ?>" class="btn-stream-secondary w-full text-sm !py-3 !text-violet-300 !border-violet-400/30">
                                        <i class="fas fa-user-graduate"></i>
                                        <?php echo e(__('public.nav_one_to_one_courses')); ?>

                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <a href="<?php echo e(route('public.instructors.index')); ?>" class="btn-stream-secondary w-full text-sm !py-3.5">
                                <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-acad-yellow"></i>
                                <?php echo e(__('public.all_instructors_link')); ?>

                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        
        <section class="py-14 sm:py-16 border-t border-white/5">
            <div class="container-acad">
                <div class="reveal rounded-[28px] glass-panel border border-white/12 px-6 sm:px-10 py-10 sm:py-12 text-center">
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 border border-white/10 text-acad-cyan">
                        <i class="fas fa-rocket"></i> <?php echo e(__('public.instructors_cta_badge')); ?>

                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-4 font-display">
                        <?php echo e(__('public.instructors_cta_student_title')); ?>

                        <span class="text-acad-yellow"><?php echo e(__('public.instructors_cta_student_accent')); ?></span>
                    </h2>
                    <p class="text-white/60 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-7">
                        <?php echo e(__('public.instructors_cta_student_desc')); ?>

                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="<?php echo e(route('public.courses')); ?>" class="btn-stream-primary text-base px-8 py-4">
                            <?php echo e(__('public.instructors_cta_browse')); ?>

                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-sm"></i>
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="btn-stream-secondary text-base px-8 py-4">
                            <?php echo e(__('public.register_free')); ?>

                        </a>
                    </div>
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
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\instructors\show.blade.php ENDPATH**/ ?>