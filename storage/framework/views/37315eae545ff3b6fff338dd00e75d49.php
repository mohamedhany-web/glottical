

<?php
    $brand = config('app.name');
?>

<?php $__env->startSection('title', __('public.testimonials_page_title') . ' - ' . __('public.site_suffix')); ?>
<?php $__env->startSection('meta_description', __('public.home_testimonials_sub')); ?>
<?php $__env->startSection('meta_keywords', 'آراء, شهادات, ' . $brand . ', معلمين'); ?>
<?php $__env->startSection('canonical_url', url('/testimonials')); ?>

<?php $__env->startSection('content'); ?>
<section class="pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
            <i class="fas fa-quote-right"></i> <?php echo e(__('public.testimonials_page_title')); ?>

        </span>
        <h1 class="text-[1.85rem] sm:text-[2.5rem] lg:text-[3rem] leading-[1.15] font-black text-[#1F2A7A] dark:text-white mb-4" style="font-family:Tajawal,Cairo,sans-serif"><?php echo e(__('public.home_testimonials_heading')); ?></h1>
        <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto leading-8"><?php echo e(__('public.home_testimonials_sub')); ?></p>
    </div>
</section>

<section class="py-12 sm:py-16 bg-white dark:bg-slate-900">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <?php if($testimonials->isEmpty()): ?>
            <div class="text-center py-16 rounded-[24px] border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50">
                <p class="text-slate-600 dark:text-slate-400"><?php echo e(__('public.home_testimonials_empty')); ?></p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partials.home-testimonial-card', ['t' => $t, 'fluid' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\testimonials.blade.php ENDPATH**/ ?>