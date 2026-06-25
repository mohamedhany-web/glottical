<?php
    $brand = config('app.name');
    $aboutRtl = app()->getLocale() === 'ar';
?>

<?php $__env->startSection('title', __('public.about_page_title') . ' - ' . __('public.site_suffix')); ?>
<?php $__env->startSection('meta_description', __('public.about_page_title') . ' — ' . $brand . '، منصة لتعليم الألمانية والإنجليزية وربط اللغة بفرص العمل في الكول سنتر، السياحة، وألمانيا.'); ?>
<?php $__env->startSection('meta_keywords', 'من نحن, ' . $brand . ', تعليم ألماني, تعليم إنجليزي, كول سنتر, سوق العمل, ألمانيا'); ?>
<?php $__env->startSection('canonical_url', url('/about')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .about-reveal {
        opacity: 0;
        transform: translateY(36px);
        transition: opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1), transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .about-reveal.revealed { opacity: 1; transform: translateY(0); }
    .about-reveal-from-right {
        opacity: 0;
        transform: translate(40px, 20px);
        transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .about-reveal-from-right.revealed { opacity: 1; transform: translate(0, 0); }
    .about-reveal-from-left {
        opacity: 0;
        transform: translate(-40px, 20px);
        transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .about-reveal-from-left.revealed { opacity: 1; transform: translate(0, 0); }
    .about-reveal-scale {
        opacity: 0;
        transform: translateY(24px) scale(0.97);
        transition: opacity 0.8s cubic-bezier(0.22, 1, 0.36, 1), transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .about-reveal-scale.revealed { opacity: 1; transform: translateY(0) scale(1); }
    .about-reveal-heading {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease-out, transform 0.5s ease-out;
    }
    .about-reveal-heading .about-heading-underline {
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        transition-delay: 0.15s;
    }
    .about-reveal-heading.revealed { opacity: 1; transform: translateY(0); }
    .about-reveal-heading.revealed .about-heading-underline { transform: scaleX(1); transform-origin: left; }
    .about-reveal-stagger > * {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .about-reveal-stagger.revealed > *:nth-child(1) { transition-delay: 0s; }
    .about-reveal-stagger.revealed > *:nth-child(2) { transition-delay: 0.08s; }
    .about-reveal-stagger.revealed > *:nth-child(3) { transition-delay: 0.16s; }
    .about-reveal-stagger.revealed > *:nth-child(4) { transition-delay: 0.24s; }
    .about-reveal-stagger.revealed > * { opacity: 1; transform: translateY(0); }
    .about-fade-up {
        animation: aboutFadeUp 0.55s ease-out forwards;
        opacity: 0;
    }
    @keyframes aboutFadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-12 sm:pb-16 overflow-hidden relative">
    <div class="absolute inset-0 bg-acad-navy"></div>
    <div class="absolute inset-0 opacity-[0.22] bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=82')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/88 to-acad-navy/35"></div>
    <div class="absolute inset-0 pattern-dots opacity-[0.12] pointer-events-none"></div>
    <div class="container-acad relative z-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10 text-white about-fade-up">
            <i class="fas fa-heart text-acad-yellow"></i> <?php echo e(__('public.about_page_title')); ?>

        </span>
        <h1 class="text-[1.75rem] sm:text-[2.35rem] lg:text-[3rem] leading-[1.2] font-black mb-5 text-white font-display about-fade-up">
            <?php echo e(__('public.about_hero')); ?>

            <span class="block mt-2 sm:mt-3 text-lg sm:text-xl md:text-2xl font-bold text-acad-yellow"><?php echo e($brand); ?></span>
        </h1>
        <p class="text-white/75 text-base sm:text-lg leading-8 max-w-3xl mx-auto font-medium">
            <?php echo e(__('public.about_hero_sub')); ?>

        </p>
    </div>
</section>

<section class="py-14 md:py-20 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/30 to-acad-navy pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <h2 class="about-reveal-heading text-3xl md:text-4xl lg:text-5xl font-black text-white mb-10 md:mb-12 font-display">
            <?php echo e(__('public.about_heading')); ?>

            <span class="about-heading-underline block h-1 w-28 mt-2 rounded-full bg-gradient-to-r from-acad-yellow to-acad-cyan"></span>
        </h2>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            <div class="lg:col-span-7 about-reveal-from-right space-y-6">
                <p class="text-lg md:text-xl text-white/85 leading-[1.85]">
                    <?php echo __('public.about_para1', ['brand' => '<strong class="font-black text-acad-yellow">' . e($brand) . '</strong>']); ?>

                </p>
                <p class="text-lg md:text-xl text-white/80 leading-[1.85]">
                    <?php echo e(__('public.about_para2')); ?>

                </p>
            </div>
            <div class="lg:col-span-5 about-reveal-from-left flex justify-center lg:justify-start">
                <div class="w-full max-w-sm aspect-square rounded-[24px] flex items-center justify-center glass-panel border border-white/15 shadow-2xl bg-acad-navyMid/50">
                    <i class="fas fa-graduation-cap text-7xl md:text-8xl text-acad-cyan/50"></i>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 relative">
    <div class="absolute inset-0 bg-acad-navy/80 pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <div class="about-reveal-scale rounded-[24px] glass-panel p-8 md:p-10 border border-white/12 overflow-hidden relative group">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-acad-blue to-acad-cyan"></div>
                <div class="flex gap-6 items-start">
                    <span class="flex-shrink-0 w-16 h-16 rounded-2xl text-acad-blue flex items-center justify-center shadow-lg bg-acad-yellow transition-transform duration-300 group-hover:scale-105">
                        <i class="fas fa-eye text-2xl"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-2xl md:text-3xl font-black text-white mb-4 font-display"><?php echo e(__('public.our_vision')); ?></h3>
                        <p class="text-lg text-white/80 leading-relaxed"><?php echo e(__('public.vision_text')); ?></p>
                    </div>
                </div>
            </div>
            <div class="about-reveal-scale rounded-[24px] glass-panel p-8 md:p-10 border border-white/12 overflow-hidden relative group">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-acad-yellow to-acad-cyan"></div>
                <div class="flex gap-6 items-start">
                    <span class="flex-shrink-0 w-16 h-16 rounded-2xl text-acad-navy flex items-center justify-center shadow-lg bg-acad-cyan transition-transform duration-300 group-hover:scale-105">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-2xl md:text-3xl font-black text-white mb-4 font-display"><?php echo e(__('public.our_mission')); ?></h3>
                        <p class="text-lg text-white/80 leading-relaxed mb-5"><?php echo e(__('public.mission_intro')); ?></p>
                        <ul class="space-y-3 text-white/85">
                            <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full shrink-0 bg-acad-yellow"></span> <?php echo e(__('public.mission_1')); ?></li>
                            <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full shrink-0 bg-acad-yellow"></span> <?php echo e(__('public.mission_2')); ?></li>
                            <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full shrink-0 bg-acad-yellow"></span> <?php echo e(__('public.mission_3')); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/25 to-acad-navy pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <h2 class="about-reveal text-3xl md:text-4xl lg:text-5xl font-black text-white mb-12 md:mb-14 text-center font-display">
            <?php echo e(__('public.why_platform')); ?>

        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 about-reveal-stagger">
            <div class="rounded-[20px] p-6 md:p-8 border border-white/10 glass-panel h-full flex flex-col hover:border-acad-yellow/40 transition-all duration-300">
                <div class="w-14 h-14 rounded-xl bg-acad-blue/80 text-acad-yellow flex items-center justify-center mb-5 shadow-md border border-white/10"><i class="fas fa-chalkboard-teacher text-xl"></i></div>
                <h4 class="text-xl font-black text-white mb-2"><?php echo e(__('public.why_1_title')); ?></h4>
                <p class="text-white/70 leading-relaxed flex-1"><?php echo e(__('public.why_1_desc')); ?></p>
            </div>
            <div class="rounded-[20px] p-6 md:p-8 border border-white/10 glass-panel h-full flex flex-col hover:border-acad-cyan/40 transition-all duration-300">
                <div class="w-14 h-14 rounded-xl bg-acad-cyan/25 text-acad-cyan flex items-center justify-center mb-5 shadow-md border border-acad-cyan/30"><i class="fas fa-user-tie text-xl"></i></div>
                <h4 class="text-xl font-black text-white mb-2"><?php echo e(__('public.why_2_title')); ?></h4>
                <p class="text-white/70 leading-relaxed flex-1"><?php echo e(__('public.why_2_desc')); ?></p>
            </div>
            <div class="rounded-[20px] p-6 md:p-8 border border-white/10 glass-panel h-full flex flex-col hover:border-acad-yellow/40 transition-all duration-300">
                <div class="w-14 h-14 rounded-xl bg-acad-blue/80 text-acad-yellow flex items-center justify-center mb-5 shadow-md border border-white/10"><i class="fas fa-headset text-xl"></i></div>
                <h4 class="text-xl font-black text-white mb-2"><?php echo e(__('public.why_3_title')); ?></h4>
                <p class="text-white/70 leading-relaxed flex-1"><?php echo e(__('public.why_3_desc')); ?></p>
            </div>
            <div class="rounded-[20px] p-6 md:p-8 border border-white/10 glass-panel h-full flex flex-col hover:border-acad-cyan/40 transition-all duration-300">
                <div class="w-14 h-14 rounded-xl bg-acad-cyan/25 text-acad-cyan flex items-center justify-center mb-5 shadow-md border border-acad-cyan/30"><i class="fas fa-certificate text-xl"></i></div>
                <h4 class="text-xl font-black text-white mb-2"><?php echo e(__('public.why_4_title')); ?></h4>
                <p class="text-white/70 leading-relaxed flex-1"><?php echo e(__('public.why_4_desc')); ?></p>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 relative">
    <div class="absolute inset-0 bg-acad-navyMid/40 pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <h2 class="about-reveal text-3xl md:text-4xl lg:text-5xl font-black text-white mb-12 md:mb-14 text-center font-display">
            <?php echo e(__('public.our_values')); ?>

        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10 about-reveal-stagger">
            <div class="rounded-[24px] glass-panel p-8 md:p-10 border border-white/10 text-center hover:border-acad-yellow/35 transition-all duration-300">
                <span class="inline-flex w-16 h-16 rounded-2xl font-black text-2xl items-center justify-center mb-6 bg-acad-yellow text-acad-blue">1</span>
                <h4 class="text-xl md:text-2xl font-black text-white mb-3"><?php echo e(__('public.value_1_title')); ?></h4>
                <p class="text-white/70 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_1_desc')); ?></p>
            </div>
            <div class="rounded-[24px] glass-panel p-8 md:p-10 border border-white/10 text-center hover:border-acad-cyan/35 transition-all duration-300">
                <span class="inline-flex w-16 h-16 rounded-2xl font-black text-2xl items-center justify-center mb-6 bg-acad-cyan text-acad-navy">2</span>
                <h4 class="text-xl md:text-2xl font-black text-white mb-3"><?php echo e(__('public.value_2_title')); ?></h4>
                <p class="text-white/70 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_2_desc')); ?></p>
            </div>
            <div class="rounded-[24px] glass-panel p-8 md:p-10 border border-white/10 text-center hover:border-acad-yellow/35 transition-all duration-300">
                <span class="inline-flex w-16 h-16 rounded-2xl font-black text-2xl items-center justify-center mb-6 bg-acad-yellow text-acad-blue">3</span>
                <h4 class="text-xl md:text-2xl font-black text-white mb-3"><?php echo e(__('public.value_3_title')); ?></h4>
                <p class="text-white/70 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_3_desc')); ?></p>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 relative overflow-hidden text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-acad-blue via-acad-navy to-acad-navyMid"></div>
    <div class="absolute inset-0 opacity-[0.08] pattern-dots pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 text-center about-reveal-stagger">
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black text-acad-yellow drop-shadow-lg counter" data-target="<?php echo e($stats['courses'] ?? 50); ?>"><?php echo e($stats['courses'] ?? 50); ?>+</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_courses')); ?></div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black text-acad-yellow drop-shadow-lg counter" data-target="<?php echo e($stats['students'] ?? 1000); ?>"><?php echo e($stats['students'] ?? 1000); ?>+</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_students')); ?></div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black text-acad-yellow drop-shadow-lg counter" data-target="<?php echo e($stats['instructors'] ?? 20); ?>"><?php echo e($stats['instructors'] ?? 20); ?>+</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_instructors')); ?></div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black text-acad-cyan drop-shadow-lg">100%</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_quality')); ?></div>
            </div>
        </div>
    </div>
</section>


<section class="py-16 md:py-24 relative">
    <div class="container-acad relative z-10 about-reveal-scale">
        <div class="rounded-[28px] border border-white/10 glass-panel px-6 sm:px-10 py-10 sm:py-12 text-center overflow-hidden relative">
            <div class="absolute inset-0 pointer-events-none opacity-20 bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=2000&q=82')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-acad-navy/90 via-acad-navy/75 to-acad-blue/40 pointer-events-none"></div>
            <span class="relative z-10 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10">
                <i class="fas fa-rocket text-acad-yellow"></i> <?php echo e($brand); ?>

            </span>
            <h2 class="relative z-10 text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-white mb-4 leading-tight max-w-4xl mx-auto font-display">
                <?php echo e(__('public.cta_about_title')); ?>

            </h2>
            <p class="relative z-10 text-white/75 text-base md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                <?php echo e(__('public.cta_about_desc')); ?>

            </p>
            <div class="relative z-10 flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center items-center">
                <a href="<?php echo e(route('register')); ?>" class="btn-stream-primary px-8 py-3.5">
                    <i class="fas fa-user-plus"></i>
                    <?php echo e(__('public.register_free_now')); ?>

                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="btn-stream-secondary px-8 py-3.5">
                    <?php echo e(__('public.browse_all_courses_btn')); ?>

                    <i class="fas fa-arrow-<?php echo e($aboutRtl ? 'left' : 'right'); ?> ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-target'), 10);
        if (isNaN(target)) return;
        var duration = 2200;
        var start = performance.now();
        var loc = document.documentElement.lang === 'ar' ? 'ar-EG' : 'en-US';
        function tick(now) {
            var p = Math.min((now - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            var val = Math.floor(eased * target);
            el.textContent = val.toLocaleString(loc) + (target >= 85 && target < 4000 ? '+' : '');
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = target.toLocaleString(loc) + (target >= 85 && target < 4000 ? '+' : '');
        }
        requestAnimationFrame(tick);
    }

    var counters = document.querySelectorAll('.counter[data-target]');
    var co = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            animateCounter(entry.target);
            co.unobserve(entry.target);
        });
    }, { threshold: 0.25 });
    counters.forEach(function (c) { co.observe(c); });

    var sel = '.about-reveal, .about-reveal-from-right, .about-reveal-from-left, .about-reveal-scale, .about-reveal-heading, .about-reveal-stagger';
    var ro = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                ro.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll(sel).forEach(function (el) { ro.observe(el); });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\about.blade.php ENDPATH**/ ?>