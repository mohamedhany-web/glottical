<?php
    $brand = config('app.name');
?>

<?php $__env->startSection('title', __('public.about_page_title') . ' - ' . __('public.site_suffix')); ?>
<?php $__env->startSection('meta_description', __('public.about_page_title') . ' — ' . $brand . '، رسالتنا في تأهيل المعلمين العرب للعمل أونلاين، وقيمنا في التعليم الإلكتروني.'); ?>
<?php $__env->startSection('meta_keywords', 'من نحن, ' . $brand . ', تأهيل معلمين, تعليم إلكتروني عربي'); ?>
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

<section class="pt-24 sm:pt-28 lg:pt-32 pb-12 sm:pb-16 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
    <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5 about-fade-up" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
            <i class="fas fa-heart"></i> <?php echo e(__('public.about_page_title')); ?>

        </span>
        <h1 class="text-[1.75rem] sm:text-[2.35rem] lg:text-[3rem] leading-[1.2] font-black mb-5 about-fade-up" style="color:#1F2A7A;font-family:Tajawal,Cairo,sans-serif">
            <?php echo e(__('public.about_hero')); ?>

            <span class="block mt-2 sm:mt-3 text-lg sm:text-xl md:text-2xl font-bold" style="color:#FB5607"><?php echo e($brand); ?></span>
        </h1>
        <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-8 max-w-3xl mx-auto font-medium">
            <?php echo e(__('public.about_hero_sub')); ?>

        </p>
    </div>
</section>


<section class="py-14 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8">
        <h2 class="about-reveal-heading text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-10 md:mb-12">
            <?php echo e(__('public.about_heading')); ?>

            <span class="about-heading-underline block h-1 w-28 mt-2 rounded-full" style="background:linear-gradient(90deg,#283593,#FB5607)"></span>
        </h2>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            <div class="lg:col-span-7 about-reveal-from-right space-y-6">
                <p class="text-lg md:text-xl text-slate-700 dark:text-slate-300 leading-[1.85]">
                    <?php echo __('public.about_para1', ['brand' => '<strong class="font-black" style="color:#283593">' . e($brand) . '</strong>']); ?>

                </p>
                <p class="text-lg md:text-xl text-slate-700 dark:text-slate-300 leading-[1.85]">
                    <?php echo e(__('public.about_para2')); ?>

                </p>
            </div>
            <div class="lg:col-span-5 about-reveal-from-left flex justify-center lg:justify-start">
                <div class="w-full max-w-sm aspect-square rounded-[24px] flex items-center justify-center border shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)]" style="background:linear-gradient(135deg,#FFE5F7,#fff7ed,#e8eeff);border-color:rgba(40,53,147,.12)">
                    <i class="fas fa-graduation-cap text-7xl md:text-8xl" style="color:rgba(40,53,147,.35)"></i>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <div class="about-reveal-scale rounded-[24px] bg-white dark:bg-slate-800 p-8 md:p-10 border border-slate-200/80 dark:border-slate-700 shadow-[0_20px_44px_-26px_rgba(31,42,122,.2)] overflow-hidden relative group">
                <div class="absolute top-0 left-0 w-full h-1.5" style="background:linear-gradient(90deg,#283593,#3d4db8)"></div>
                <div class="flex gap-6 items-start">
                    <span class="flex-shrink-0 w-16 h-16 rounded-2xl text-white flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-105" style="background:linear-gradient(135deg,#283593,#1F2A7A)">
                        <i class="fas fa-eye text-2xl"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-4"><?php echo e(__('public.our_vision')); ?></h3>
                        <p class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed"><?php echo e(__('public.vision_text')); ?></p>
                    </div>
                </div>
            </div>
            <div class="about-reveal-scale rounded-[24px] bg-white dark:bg-slate-800 p-8 md:p-10 border border-slate-200/80 dark:border-slate-700 shadow-[0_20px_44px_-26px_rgba(251,86,7,.15)] overflow-hidden relative group">
                <div class="absolute top-0 left-0 w-full h-1.5" style="background:linear-gradient(90deg,#FB5607,#ff8c42)"></div>
                <div class="flex gap-6 items-start">
                    <span class="flex-shrink-0 w-16 h-16 rounded-2xl text-white flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-105" style="background:linear-gradient(135deg,#FB5607,#e84d00)">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-4"><?php echo e(__('public.our_mission')); ?></h3>
                        <p class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed mb-5"><?php echo e(__('public.mission_intro')); ?></p>
                        <ul class="space-y-3 text-slate-700 dark:text-slate-300">
                            <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full shrink-0" style="background:#FB5607"></span> <?php echo e(__('public.mission_1')); ?></li>
                            <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full shrink-0" style="background:#FB5607"></span> <?php echo e(__('public.mission_2')); ?></li>
                            <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full shrink-0" style="background:#FB5607"></span> <?php echo e(__('public.mission_3')); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8">
        <h2 class="about-reveal text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-12 md:mb-14 text-center">
            <?php echo e(__('public.why_platform')); ?>

        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 about-reveal-stagger">
            <div class="rounded-[20px] p-6 md:p-8 border h-full flex flex-col transition-all duration-300 hover:shadow-lg dark:bg-slate-800/50" style="background:linear-gradient(180deg,#f4f6ff,#fff);border-color:rgba(40,53,147,.15)">
                <div class="w-14 h-14 rounded-xl text-white flex items-center justify-center mb-5 shadow-md" style="background:#283593"><i class="fas fa-chalkboard-teacher text-xl"></i></div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white mb-2"><?php echo e(__('public.why_1_title')); ?></h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed flex-1"><?php echo e(__('public.why_1_desc')); ?></p>
            </div>
            <div class="rounded-[20px] p-6 md:p-8 border h-full flex flex-col transition-all duration-300 hover:shadow-lg dark:bg-slate-800/50" style="background:linear-gradient(180deg,#fff7ed,#fff);border-color:rgba(251,86,7,.2)">
                <div class="w-14 h-14 rounded-xl text-white flex items-center justify-center mb-5 shadow-md" style="background:#FB5607"><i class="fas fa-user-tie text-xl"></i></div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white mb-2"><?php echo e(__('public.why_2_title')); ?></h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed flex-1"><?php echo e(__('public.why_2_desc')); ?></p>
            </div>
            <div class="rounded-[20px] p-6 md:p-8 border h-full flex flex-col transition-all duration-300 hover:shadow-lg dark:bg-slate-800/50" style="background:linear-gradient(180deg,#f4f6ff,#fff);border-color:rgba(40,53,147,.15)">
                <div class="w-14 h-14 rounded-xl text-white flex items-center justify-center mb-5 shadow-md" style="background:#283593"><i class="fas fa-headset text-xl"></i></div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white mb-2"><?php echo e(__('public.why_3_title')); ?></h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed flex-1"><?php echo e(__('public.why_3_desc')); ?></p>
            </div>
            <div class="rounded-[20px] p-6 md:p-8 border h-full flex flex-col transition-all duration-300 hover:shadow-lg dark:bg-slate-800/50" style="background:linear-gradient(180deg,#fff7ed,#fff);border-color:rgba(251,86,7,.2)">
                <div class="w-14 h-14 rounded-xl text-white flex items-center justify-center mb-5 shadow-md" style="background:#FB5607"><i class="fas fa-certificate text-xl"></i></div>
                <h4 class="text-xl font-black text-slate-900 dark:text-white mb-2"><?php echo e(__('public.why_4_title')); ?></h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed flex-1"><?php echo e(__('public.why_4_desc')); ?></p>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8">
        <h2 class="about-reveal text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-12 md:mb-14 text-center">
            <?php echo e(__('public.our_values')); ?>

        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10 about-reveal-stagger">
            <div class="rounded-[24px] bg-white dark:bg-slate-800 p-8 md:p-10 border border-slate-200 dark:border-slate-700 text-center shadow-[0_16px_40px_-24px_rgba(31,42,122,.2)] hover:-translate-y-1 transition-all duration-300">
                <span class="inline-flex w-16 h-16 rounded-2xl font-black text-2xl items-center justify-center mb-6 text-white" style="background:#283593">1</span>
                <h4 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white mb-3"><?php echo e(__('public.value_1_title')); ?></h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_1_desc')); ?></p>
            </div>
            <div class="rounded-[24px] bg-white dark:bg-slate-800 p-8 md:p-10 border border-slate-200 dark:border-slate-700 text-center shadow-[0_16px_40px_-24px_rgba(251,86,7,.18)] hover:-translate-y-1 transition-all duration-300">
                <span class="inline-flex w-16 h-16 rounded-2xl font-black text-2xl items-center justify-center mb-6 text-white" style="background:#FB5607">2</span>
                <h4 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white mb-3"><?php echo e(__('public.value_2_title')); ?></h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_2_desc')); ?></p>
            </div>
            <div class="rounded-[24px] bg-white dark:bg-slate-800 p-8 md:p-10 border border-slate-200 dark:border-slate-700 text-center shadow-[0_16px_40px_-24px_rgba(31,42,122,.2)] hover:-translate-y-1 transition-all duration-300">
                <span class="inline-flex w-16 h-16 rounded-2xl font-black text-2xl items-center justify-center mb-6 text-white" style="background:#283593">3</span>
                <h4 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white mb-3"><?php echo e(__('public.value_3_title')); ?></h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_3_desc')); ?></p>
            </div>
        </div>
    </div>
</section>


<section class="py-14 md:py-20 relative overflow-hidden text-white" style="background:linear-gradient(115deg,#283593 0%,#1F2A7A 45%,#283593 70%,#c2410c 100%)">
    <div class="absolute inset-0 opacity-[0.07]" style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 text-center about-reveal-stagger">
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black drop-shadow-lg counter" data-target="<?php echo e($stats['courses'] ?? 50); ?>"><?php echo e($stats['courses'] ?? 50); ?>+</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_courses')); ?></div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black drop-shadow-lg counter" data-target="<?php echo e($stats['students'] ?? 1000); ?>"><?php echo e($stats['students'] ?? 1000); ?>+</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_students')); ?></div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black drop-shadow-lg counter" data-target="<?php echo e($stats['instructors'] ?? 20); ?>"><?php echo e($stats['instructors'] ?? 20); ?>+</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_instructors')); ?></div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl lg:text-6xl font-black drop-shadow-lg">100%</div>
                <div class="text-white/85 font-semibold mt-2 text-sm md:text-lg"><?php echo e(__('public.stat_quality')); ?></div>
            </div>
        </div>
    </div>
</section>


<section class="py-16 md:py-24 relative overflow-hidden" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8 text-center about-reveal-scale">
        <div class="rounded-[28px] border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593">
                <i class="fas fa-rocket"></i> <?php echo e($brand); ?>

            </span>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-4 leading-tight max-w-4xl mx-auto" style="font-family:Tajawal,Cairo,sans-serif">
                <?php echo e(__('public.cta_about_title')); ?>

            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-base md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                <?php echo e(__('public.cta_about_desc')); ?>

            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center items-center">
                <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-8 py-3.5 shadow-lg transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                    <i class="fas fa-user-plus"></i>
                    <?php echo e(__('public.register_free_now')); ?>

                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold px-8 py-3.5 border-2 transition-all hover:bg-slate-50 dark:hover:bg-slate-700" style="border-color:#283593;color:#283593">
                    <?php echo e(__('public.browse_all_courses_btn')); ?>

                    <i class="fas fa-arrow-left mr-1"></i>
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
        function tick(now) {
            var p = Math.min((now - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            var val = Math.floor(eased * target);
            el.textContent = val.toLocaleString('ar-EG') + (target >= 85 && target < 4000 ? '+' : '');
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = target.toLocaleString('ar-EG') + (target >= 85 && target < 4000 ? '+' : '');
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

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\about.blade.php ENDPATH**/ ?>