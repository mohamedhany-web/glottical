<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $planKeys = ['teacher_starter', 'teacher_pro'];
    $activePlans = collect($planKeys)
        ->mapWithKeys(fn ($key) => [$key => $teacherPlans[$key] ?? null])
        ->filter();
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e(__('public.pricing_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?></title>
    <meta name="description" content="<?php echo e(__('public.pricing_meta_description')); ?>">
    <meta name="theme-color" content="#0d1528">
    <link rel="canonical" href="<?php echo e(route('public.pricing')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <?php echo $__env->make('partials.public-academy-surface', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .line-clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
        .reveal.s1{transition-delay:.06s}.reveal.s2{transition-delay:.12s}
        .pricing-pop{box-shadow:0 24px 60px -20px rgba(0,212,255,.25)}
    </style>
</head>
<body class="page-academy font-sans antialiased text-white">
    <div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>

    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1">
        <section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-14 overflow-hidden relative">
            <div class="absolute inset-0 bg-acad-navy"></div>
            <div class="absolute inset-0 opacity-[0.18] bg-cover bg-center"
                 style="background-image:url('https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=2400&q=82')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/90 to-acad-navy/40"></div>
            <div class="absolute inset-0 pattern-dots opacity-[0.12] pointer-events-none"></div>

            <div class="container-acad relative z-10 text-center max-w-4xl mx-auto">
                <nav class="reveal text-sm text-white/50 mb-8 flex items-center justify-center gap-2 flex-wrap">
                    <a href="<?php echo e(route('home')); ?>" class="hover:text-acad-cyan transition-colors"><?php echo e(__('public.home')); ?></a>
                    <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] opacity-60"></i>
                    <span class="text-white font-semibold"><?php echo e(__('public.pricing_page_title')); ?></span>
                </nav>

                <span class="reveal inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-6 glass-panel border border-white/10 text-acad-cyan">
                    <i class="fas fa-tags text-[12px]"></i>
                    <?php echo e(__('public.pricing_hero_kicker')); ?>

                </span>

                <h1 class="reveal text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.18] font-black text-white mb-5 font-display">
                    <?php echo e(__('public.pricing_hero_title')); ?>

                    <span class="block text-acad-yellow"><?php echo e(__('public.pricing_hero_accent')); ?></span>
                </h1>

                <p class="reveal s1 text-white/70 text-base sm:text-lg leading-8 mb-4 max-w-3xl mx-auto">
                    <?php echo e(__('public.pricing_hero_sub')); ?>

                </p>
                <p class="reveal s1 text-sm text-white/45 max-w-2xl mx-auto">
                    <?php echo e(__('public.pricing_hero_note')); ?>

                </p>
            </div>
        </section>

        <section class="py-12 sm:py-16 relative">
            <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/40 to-acad-navy pointer-events-none"></div>
            <div class="container-acad relative z-10">
                <div class="reveal text-center max-w-2xl mx-auto mb-12">
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-extrabold mb-4 glass-panel border border-white/10 text-acad-cyan">
                        <?php echo e(__('public.pricing_teacher_plans_badge')); ?>

                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-3"><?php echo e(__('public.pricing_teacher_plans_title')); ?></h2>
                    <p class="text-white/60 leading-8"><?php echo e(__('public.pricing_teacher_plans_sub')); ?></p>
                </div>

                <?php if($activePlans->isNotEmpty()): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 max-w-5xl mx-auto items-stretch">
                        <?php $__currentLoopData = $planKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $plan = $teacherPlans[$planKey] ?? null; ?>
                            <?php if($plan): ?>
                                <?php if (isset($component)) { $__componentOriginal78c3e1244b8f60782f7f00b2926fc349 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal78c3e1244b8f60782f7f00b2926fc349 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.teacher-plan-card','data' => ['planKey' => $planKey,'plan' => $plan,'highlighted' => $planKey === 'teacher_pro']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('teacher-plan-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['plan-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($planKey),'plan' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($plan),'highlighted' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($planKey === 'teacher_pro')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal78c3e1244b8f60782f7f00b2926fc349)): ?>
<?php $attributes = $__attributesOriginal78c3e1244b8f60782f7f00b2926fc349; ?>
<?php unset($__attributesOriginal78c3e1244b8f60782f7f00b2926fc349); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal78c3e1244b8f60782f7f00b2926fc349)): ?>
<?php $component = $__componentOriginal78c3e1244b8f60782f7f00b2926fc349; ?>
<?php unset($__componentOriginal78c3e1244b8f60782f7f00b2926fc349); ?>
<?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <p class="reveal text-center text-xs text-white/40 mt-8 max-w-2xl mx-auto">
                        <?php echo e(__('public.pricing_sync_note')); ?>

                    </p>
                <?php else: ?>
                    <p class="text-center text-white/50 py-12"><?php echo e(__('public.pricing_no_plans')); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <?php if(isset($packages) && $packages->count() > 0): ?>
        <section class="py-12 sm:py-16 border-t border-white/5 bg-[#060d1a]/60">
            <div class="container-acad">
                <div class="reveal text-center max-w-2xl mx-auto mb-12">
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-extrabold mb-4 glass-panel border border-white/10 text-violet-300">
                        <?php echo e(__('public.pricing_packages_badge')); ?>

                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-3"><?php echo e(__('public.pricing_packages_title')); ?></h2>
                    <p class="text-white/60 leading-8"><?php echo e(__('public.pricing_packages_sub')); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $cardBody = trim((string) ($package->card_summary ?? '')) !== ''
                                ? $package->card_summary
                                : ($package->description ?? '');
                            $cardFeatures = collect($package->features ?? [])->map(fn ($f) => trim((string) $f))->filter()->values();
                            $isPopular = (bool) $package->is_popular;
                        ?>
                        <article class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'reveal card-stream flex flex-col overflow-hidden border',
                            'border-acad-yellow/50 ring-1 ring-acad-yellow/30' => $isPopular,
                            'border-white/10' => ! $isPopular,
                            's'.min($index + 1, 4) => true,
                        ]); ?>">
                            <div class="p-6 sm:p-7 flex flex-col flex-1">
                                <?php if($isPopular): ?>
                                    <span class="self-start text-[11px] font-black px-2.5 py-1 rounded-md bg-acad-yellow text-acad-blue mb-4"><?php echo e(__('public.pricing_package_popular')); ?></span>
                                <?php endif; ?>

                                <div class="flex items-start gap-4 mb-5">
                                    <?php if($package->thumbnail): ?>
                                        <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 ring-1 ring-white/15">
                                            <img src="<?php echo e(storage_asset($package->thumbnail)); ?>" alt="" class="w-full h-full object-cover" loading="lazy">
                                        </div>
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-xl bg-acad-cyan/15 flex items-center justify-center shrink-0 text-acad-cyan">
                                            <i class="fas fa-box text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0 text-start">
                                        <h3 class="text-xl font-black text-white leading-snug"><?php echo e($package->name); ?></h3>
                                        <?php if($package->courses_count > 0): ?>
                                            <p class="text-xs text-white/50 mt-1">
                                                <i class="fas fa-graduation-cap <?php echo e($isRtl ? 'ml-1' : 'mr-1'); ?>"></i>
                                                <?php echo e(__('public.path_courses_count', ['count' => $package->courses_count])); ?>

                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <?php if($package->original_price && $package->original_price > $package->price): ?>
                                        <p class="text-sm text-white/40 line-through tabular-nums"><?php echo e(number_format($package->original_price, 0)); ?> <?php echo e(__('public.currency_egp')); ?></p>
                                    <?php endif; ?>
                                    <p class="text-3xl font-black text-acad-yellow tabular-nums">
                                        <?php if($package->price > 0): ?>
                                            <?php echo e(number_format($package->price, 0)); ?> <span class="text-lg text-white/50"><?php echo e(__('public.currency_egp')); ?></span>
                                        <?php else: ?>
                                            <?php echo e(__('public.free_price')); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>

                                <?php if($cardBody !== ''): ?>
                                    <p class="text-sm text-white/55 leading-relaxed line-clamp-3 mb-4"><?php echo e($cardBody); ?></p>
                                <?php endif; ?>

                                <?php if($cardFeatures->isNotEmpty()): ?>
                                    <ul class="space-y-2 text-sm text-white/75 mb-6 flex-1">
                                        <?php $__currentLoopData = $cardFeatures->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="flex items-start gap-2">
                                                <i class="fas fa-check text-emerald-400 mt-1 text-xs shrink-0"></i>
                                                <span><?php echo e($feature); ?></span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>

                                <a href="<?php echo e(route('public.package.show', $package->slug)); ?>"
                                   class="mt-auto w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl font-extrabold text-sm transition
                                   <?php echo e($isPopular ? 'bg-acad-yellow text-acad-blue hover:brightness-110' : 'border border-white/20 text-white hover:bg-white/10'); ?>">
                                    <i class="fas fa-<?php echo e($package->price > 0 ? 'shopping-cart' : 'eye'); ?>"></i>
                                    <?php echo e($package->price > 0 ? __('public.pricing_package_buy') : __('public.view_details')); ?>

                                </a>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <section class="py-14 sm:py-16 border-t border-white/5">
            <div class="container-acad">
                <div class="reveal rounded-[28px] glass-panel border border-white/12 px-6 sm:px-10 py-10 sm:py-12 text-center">
                    <h2 class="text-2xl sm:text-3xl font-black text-white mb-3"><?php echo e(__('public.pricing_footer_cta_title')); ?></h2>
                    <p class="text-white/60 max-w-2xl mx-auto mb-6"><?php echo e(__('public.pricing_footer_cta_sub')); ?></p>
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="<?php echo e(route('register')); ?>" class="btn-stream-primary px-8 py-3.5"><?php echo e(__('public.register_free')); ?></a>
                        <a href="<?php echo e(route('public.contact')); ?>" class="btn-stream-secondary px-8 py-3.5"><?php echo e(__('public.pricing_footer_contact')); ?></a>
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
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\pricing.blade.php ENDPATH**/ ?>