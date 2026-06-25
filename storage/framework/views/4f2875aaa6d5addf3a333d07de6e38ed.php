<?php
    $brand = config('app.name');
    $chevronNext = app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right';
?>

<?php $__env->startSection('title', __('public.help_page_title') . ' — ' . $brand); ?>
<?php $__env->startSection('meta_description', __('public.help_meta_description', ['brand' => $brand])); ?>
<?php $__env->startSection('meta_keywords', __('public.help_meta_keywords', ['brand' => $brand])); ?>
<?php $__env->startSection('canonical_url', url('/help')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .help-hub-card {
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(26, 45, 77, 0.55);
    }
    .help-hub-card:hover {
        transform: translateY(-4px);
        border-color: rgba(245, 184, 0, 0.35);
        box-shadow: 0 22px 48px -24px rgba(0, 0, 0, 0.45);
    }
    .help-topic-row {
        transition: background 0.2s ease, border-color 0.2s ease;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .help-topic-row:last-child { border-bottom: none; }
    .help-topic-row:hover {
        background: rgba(255, 255, 255, 0.06);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-12 sm:pb-14 overflow-hidden relative">
    <div class="absolute inset-0 bg-acad-navy"></div>
    <div class="absolute inset-0 opacity-[0.2] bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=2400&q=82')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/88 to-acad-navy/35"></div>
    <div class="absolute inset-0 pattern-dots opacity-[0.12] pointer-events-none"></div>
    <div class="container-acad relative z-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-6 glass-panel border border-white/10 text-white">
            <i class="fas fa-life-ring text-acad-cyan"></i> <?php echo e(__('public.help_hero_badge')); ?>

        </span>
        <h1 class="text-[1.85rem] sm:text-[2.6rem] lg:text-[3.15rem] leading-[1.12] font-black mb-5 text-white font-display">
            <?php echo e(__('public.help_page_title')); ?>

            <span class="block mt-2 text-acad-yellow text-[1.5rem] sm:text-[2rem] lg:text-[2.35rem]"><?php echo e($brand); ?></span>
        </h1>
        <p class="text-white/70 text-base sm:text-lg leading-8 max-w-2xl mx-auto mb-8">
            <?php echo e(__('public.help_hero_sub')); ?>

        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
            <a href="<?php echo e(route('public.faq')); ?>" class="btn-stream-primary px-7 py-3.5">
                <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_page_title')); ?>

            </a>
            <a href="<?php echo e(route('public.contact')); ?>" class="btn-stream-secondary px-7 py-3.5">
                <i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?>

            </a>
        </div>
    </div>
</section>

<section class="py-12 sm:py-16 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/25 to-acad-navy pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <div class="flex items-center gap-3 mb-8 justify-center sm:justify-start">
            <span class="w-12 h-1 rounded-full shrink-0 bg-gradient-to-r from-acad-yellow to-acad-cyan"></span>
            <h2 class="text-xl sm:text-2xl font-black text-white font-display"><?php echo e(__('public.help_start_title')); ?></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6">
            <a href="<?php echo e(route('public.faq')); ?>" class="help-hub-card rounded-[24px] p-6 sm:p-7 flex flex-col items-center text-center no-underline text-inherit">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-acad-blue text-2xl mb-4 shadow-md bg-acad-yellow">
                    <i class="fas fa-circle-question"></i>
                </div>
                <h3 class="text-lg font-black text-white mb-2"><?php echo e(__('public.help_card_faq_title')); ?></h3>
                <p class="text-white/65 text-sm leading-relaxed"><?php echo e(__('public.help_card_faq_desc')); ?></p>
            </a>
            <a href="<?php echo e(route('public.contact')); ?>" class="help-hub-card rounded-[24px] p-6 sm:p-7 flex flex-col items-center text-center no-underline text-inherit">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-acad-navy text-2xl mb-4 shadow-md bg-acad-cyan">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="text-lg font-black text-white mb-2"><?php echo e(__('public.help_card_contact_title')); ?></h3>
                <p class="text-white/65 text-sm leading-relaxed"><?php echo e(__('public.help_card_contact_desc')); ?></p>
            </a>
            <a href="<?php echo e(route('public.courses')); ?>" class="help-hub-card rounded-[24px] p-6 sm:p-7 flex flex-col items-center text-center no-underline text-inherit">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl mb-4 shadow-md bg-acad-blue border border-white/15">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <h3 class="text-lg font-black text-white mb-2"><?php echo e(__('public.help_card_courses_title')); ?></h3>
                <p class="text-white/65 text-sm leading-relaxed"><?php echo e(__('public.help_card_courses_desc')); ?></p>
            </a>
        </div>

        <div class="flex items-center gap-3 mt-14 mb-6 justify-center sm:justify-start">
            <span class="w-12 h-1 rounded-full shrink-0 bg-gradient-to-r from-acad-cyan to-acad-yellow"></span>
            <h2 class="text-xl sm:text-2xl font-black text-white font-display"><?php echo e(__('public.help_topics_title')); ?></h2>
        </div>
        <div class="rounded-[28px] border border-white/10 glass-panel overflow-hidden">
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
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-acad-navy text-base shadow-sm font-black <?php echo e($i % 2 === 0 ? 'bg-acad-cyan' : 'bg-acad-yellow'); ?>">
                        <i class="fas fa-<?php echo e($topicIcons[$i - 1]); ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0 text-start">
                        <h3 class="text-base sm:text-lg font-black text-white mb-1"><?php echo e(__('public.help_topic_'.$i.'_title')); ?></h3>
                        <p class="text-white/60 text-sm leading-relaxed"><?php echo e(__('public.help_topic_'.$i.'_desc')); ?></p>
                    </div>
                    <i class="fas <?php echo e($chevronNext); ?> text-white/35 flex-shrink-0 mt-2 text-sm"></i>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="flex items-center gap-3 mt-14 mb-8 justify-center sm:justify-start">
            <span class="w-12 h-1 rounded-full shrink-0 bg-gradient-to-r from-acad-yellow to-acad-cyan"></span>
            <h2 class="text-xl sm:text-2xl font-black text-white font-display"><?php echo e(__('public.help_steps_title')); ?></h2>
        </div>
        <div class="relative max-w-3xl mx-auto ps-8 sm:ps-10 border-s-2 border-white/20">
            <?php $__currentLoopData = range(1, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="relative pb-10 last:pb-0">
                <span class="absolute top-0 flex items-center justify-center w-9 h-9 rounded-full text-acad-navy text-sm font-black -start-[41px] sm:-start-[45px] <?php echo e($step % 2 === 1 ? 'bg-acad-yellow' : 'bg-acad-cyan'); ?>">
                    <?php echo e($step); ?>

                </span>
                <div class="rounded-2xl border border-white/10 glass-panel px-5 py-4">
                    <p class="font-black text-white mb-1"><?php echo e(__('public.help_step_'.$step.'_title')); ?></p>
                    <p class="text-sm text-white/65"><?php echo e(__('public.help_step_'.$step.'_desc')); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="pt-4 sm:pt-6 pb-14 sm:pb-16 relative">
    <div class="absolute inset-0 bg-acad-navy pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <div class="rounded-[28px] border border-white/10 glass-panel px-6 sm:px-10 py-10 sm:py-12 text-center overflow-hidden relative">
            <div class="absolute inset-0 pointer-events-none opacity-15 bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=2000&q=82')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-acad-navy/92 to-acad-blue/35 pointer-events-none"></div>
            <span class="relative z-10 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10">
                <i class="fas fa-headset text-acad-yellow"></i> <?php echo e(__('public.support')); ?>

            </span>
            <h3 class="relative z-10 text-2xl sm:text-3xl font-black mb-3 text-white font-display"><?php echo e(__('public.help_cta_title')); ?></h3>
            <p class="relative z-10 text-white/70 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-8">
                <?php echo e(__('public.help_cta_desc')); ?>

            </p>
            <div class="relative z-10 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="<?php echo e(route('public.contact')); ?>" class="btn-stream-primary px-8 py-3.5">
                    <i class="fas fa-paper-plane"></i> <?php echo e(__('public.help_cta_btn')); ?>

                </a>
                <a href="<?php echo e(route('public.faq')); ?>" class="btn-stream-secondary px-8 py-3.5">
                    <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_page_title')); ?>

                </a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\help.blade.php ENDPATH**/ ?>