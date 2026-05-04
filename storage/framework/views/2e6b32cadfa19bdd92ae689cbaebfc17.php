

<?php
    $brand = 'Muallimx';
    $chevronNext = app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right';
?>

<?php $__env->startSection('title', __('public.help_page_title') . ' — Muallimx'); ?>
<?php $__env->startSection('meta_description', __('public.help_meta_description', ['brand' => $brand])); ?>
<?php $__env->startSection('meta_keywords', __('public.help_meta_keywords', ['brand' => $brand])); ?>
<?php $__env->startSection('canonical_url', url('/help')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .help-hub-card {
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        border: 1px solid rgb(226 232 240);
    }
    .help-hub-card:hover {
        transform: translateY(-4px);
        border-color: rgba(40, 53, 147, 0.28);
        box-shadow: 0 22px 48px -24px rgba(31, 42, 122, 0.38);
    }
    html.dark .help-hub-card {
        border-color: rgb(51 65 85);
        background: rgb(30 41 59 / 0.88);
    }
    .help-topic-row {
        transition: background 0.2s ease, border-color 0.2s ease;
        border-bottom: 1px solid rgb(241 245 249);
    }
    .help-topic-row:last-child { border-bottom: none; }
    .help-topic-row:hover {
        background: rgba(255, 229, 247, 0.35);
    }
    html.dark .help-topic-row {
        border-color: rgb(51 65 85);
    }
    html.dark .help-topic-row:hover {
        background: rgba(40, 53, 147, 0.2);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<section class="pt-24 sm:pt-28 lg:pt-32 pb-12 sm:pb-14 overflow-hidden relative" style="background:radial-gradient(circle at 50% 120%,rgba(255,229,247,.55),transparent 42%),radial-gradient(circle at 10% 20%,rgba(40,53,147,.09),transparent 35%),linear-gradient(180deg,#f4f6ff 0%,#ffffff 100%)">
    <div class="absolute inset-0 pointer-events-none opacity-30" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.06) 1px,transparent 0);background-size:32px 32px"></div>
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
            <i class="fas fa-life-ring"></i> <?php echo e(__('public.help_hero_badge')); ?>

        </span>
        <h1 class="text-[1.85rem] sm:text-[2.6rem] lg:text-[3.15rem] leading-[1.12] font-black mb-5 text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif">
            <?php echo e(__('public.help_page_title')); ?>

            <span class="block mt-2 text-[#FB5607] dark:text-orange-400 text-[1.5rem] sm:text-[2rem] lg:text-[2.35rem]"><?php echo e($brand); ?></span>
        </h1>
        <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-8 max-w-2xl mx-auto mb-8">
            <?php echo e(__('public.help_hero_sub')); ?>

        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
            <a href="<?php echo e(route('public.faq')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-7 py-3.5 shadow-lg transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_page_title')); ?>

            </a>
            <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold px-7 py-3.5 border-2 transition-all text-white" style="background:#283593;border-color:#283593">
                <i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?>

            </a>
        </div>
    </div>
</section>

<section class="py-12 sm:py-16 bg-gradient-to-b from-white to-slate-50 dark:from-slate-900 dark:to-slate-950">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="flex items-center gap-3 mb-8 justify-center sm:justify-start">
            <span class="w-12 h-1 rounded-full shrink-0" style="background:linear-gradient(90deg,#283593,#FB5607)"></span>
            <h2 class="text-xl sm:text-2xl font-black text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif"><?php echo e(__('public.help_start_title')); ?></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6">
            <a href="<?php echo e(route('public.faq')); ?>" class="help-hub-card rounded-[24px] bg-white dark:bg-slate-800 p-6 sm:p-7 flex flex-col items-center text-center no-underline text-inherit shadow-sm">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl mb-4 shadow-md" style="background:linear-gradient(135deg,#283593,#1F2A7A)">
                    <i class="fas fa-circle-question"></i>
                </div>
                <h3 class="text-lg font-black text-[#1F2A7A] dark:text-white mb-2"><?php echo e(__('public.help_card_faq_title')); ?></h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed"><?php echo e(__('public.help_card_faq_desc')); ?></p>
            </a>
            <a href="<?php echo e(route('public.contact')); ?>" class="help-hub-card rounded-[24px] bg-white dark:bg-slate-800 p-6 sm:p-7 flex flex-col items-center text-center no-underline text-inherit shadow-sm">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl mb-4 shadow-md" style="background:#FB5607;box-shadow:0 10px 24px -8px rgba(251,86,7,.45)">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="text-lg font-black text-[#1F2A7A] dark:text-white mb-2"><?php echo e(__('public.help_card_contact_title')); ?></h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed"><?php echo e(__('public.help_card_contact_desc')); ?></p>
            </a>
            <a href="<?php echo e(route('public.courses')); ?>" class="help-hub-card rounded-[24px] bg-white dark:bg-slate-800 p-6 sm:p-7 flex flex-col items-center text-center no-underline text-inherit shadow-sm">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl mb-4 shadow-md" style="background:linear-gradient(135deg,#1F2A7A,#3d4db8)">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <h3 class="text-lg font-black text-[#1F2A7A] dark:text-white mb-2"><?php echo e(__('public.help_card_courses_title')); ?></h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed"><?php echo e(__('public.help_card_courses_desc')); ?></p>
            </a>
        </div>

        <div class="flex items-center gap-3 mt-14 mb-6 justify-center sm:justify-start">
            <span class="w-12 h-1 rounded-full shrink-0" style="background:linear-gradient(90deg,#FB5607,#283593)"></span>
            <h2 class="text-xl sm:text-2xl font-black text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif"><?php echo e(__('public.help_topics_title')); ?></h2>
        </div>
        <div class="rounded-[28px] border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-28px_rgba(31,42,122,.25)] overflow-hidden">
            <?php
                $topicLinks = [
                    route('public.faq') . '#faq-main',
                    route('public.faq') . '#faq-main',
                    route('public.faq') . '#faq-main',
                    route('public.certificates'),
                    route('public.contact'),
                ];
                $topicIcons = ['user-plus', 'credit-card', 'route', 'certificate', 'headset'];
            ?>
            <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($topicLinks[$i - 1]); ?>" class="help-topic-row block px-5 sm:px-8 py-5 sm:py-6 no-underline text-inherit">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-base shadow-sm" style="background:<?php echo e($i % 2 === 0 ? '#FB5607' : '#283593'); ?>">
                        <i class="fas fa-<?php echo e($topicIcons[$i - 1]); ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0 text-start">
                        <h3 class="text-base sm:text-lg font-black text-[#1F2A7A] dark:text-white mb-1"><?php echo e(__('public.help_topic_'.$i.'_title')); ?></h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed"><?php echo e(__('public.help_topic_'.$i.'_desc')); ?></p>
                    </div>
                    <i class="fas <?php echo e($chevronNext); ?> text-slate-300 dark:text-slate-600 flex-shrink-0 mt-2 text-sm"></i>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="flex items-center gap-3 mt-14 mb-8 justify-center sm:justify-start">
            <span class="w-12 h-1 rounded-full shrink-0" style="background:linear-gradient(90deg,#283593,#FB5607)"></span>
            <h2 class="text-xl sm:text-2xl font-black text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif"><?php echo e(__('public.help_steps_title')); ?></h2>
        </div>
        <div class="relative max-w-3xl mx-auto ps-8 sm:ps-10 border-s-2 border-[#283593]/25 dark:border-indigo-500/30">
            <?php $__currentLoopData = range(1, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="relative pb-10 last:pb-0">
                <span class="absolute top-0 flex items-center justify-center w-9 h-9 rounded-full text-white text-sm font-black -start-[41px] sm:-start-[45px]" style="background:<?php echo e($step % 2 === 1 ? '#283593' : '#FB5607'); ?>;box-shadow:0 8px 20px -6px <?php echo e($step % 2 === 1 ? 'rgba(40,53,147,.35)' : 'rgba(251,86,7,.35)'); ?>">
                    <?php echo e($step); ?>

                </span>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-5 py-4 shadow-sm">
                    <p class="font-black text-[#1F2A7A] dark:text-white mb-1"><?php echo e(__('public.help_step_'.$step.'_title')); ?></p>
                    <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo e(__('public.help_step_'.$step.'_desc')); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="pt-4 sm:pt-6 pb-14 sm:pb-16" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="rounded-[28px] border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12 text-center">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593">
                <i class="fas fa-headset"></i> <?php echo e(__('public.support')); ?>

            </span>
            <h3 class="text-2xl sm:text-3xl font-black mb-3 text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif"><?php echo e(__('public.help_cta_title')); ?></h3>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-8">
                <?php echo e(__('public.help_cta_desc')); ?>

            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-8 py-3.5 transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                    <i class="fas fa-paper-plane"></i> <?php echo e(__('public.help_cta_btn')); ?>

                </a>
                <a href="<?php echo e(route('public.faq')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold px-8 py-3.5 border-2 border-slate-200 dark:border-slate-600 text-[#1F2A7A] dark:text-slate-100 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_page_title')); ?>

                </a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\help.blade.php ENDPATH**/ ?>