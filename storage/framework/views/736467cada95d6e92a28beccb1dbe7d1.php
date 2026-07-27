

<?php $__env->startSection('title', __('student.course_subscriptions_title')); ?>
<?php $__env->startSection('header', __('student.course_subscriptions_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e(__('student.course_subscriptions_title')); ?></h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1"><?php echo e(__('student.course_subscriptions_subtitle')); ?></p>
            </div>
            <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold transition shrink-0">
                <i class="fas fa-search"></i>
                <?php echo e(__('student.browse_courses')); ?>

            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 text-center">
            <p class="text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e($stats['total']); ?></p>
            <p class="text-xs font-semibold text-slate-500 mt-1"><?php echo e(__('student.course_subscriptions_stat_total')); ?></p>
        </div>
        <div class="rounded-xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/50 dark:bg-emerald-950/20 p-4 text-center">
            <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300"><?php echo e($stats['active']); ?></p>
            <p class="text-xs font-semibold text-emerald-600/80 mt-1"><?php echo e(__('student.course_subscriptions_stat_active')); ?></p>
        </div>
        <div class="rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/50 dark:bg-amber-950/20 p-4 text-center">
            <p class="text-2xl font-black text-amber-700 dark:text-amber-300"><?php echo e($stats['expiring_soon']); ?></p>
            <p class="text-xs font-semibold text-amber-600/80 mt-1"><?php echo e(__('student.course_subscriptions_stat_soon')); ?></p>
        </div>
        <div class="rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/20 p-4 text-center">
            <p class="text-2xl font-black text-rose-700 dark:text-rose-300"><?php echo e($stats['expired']); ?></p>
            <p class="text-xs font-semibold text-rose-600/80 mt-1"><?php echo e(__('student.course_subscriptions_stat_expired')); ?></p>
        </div>
    </div>

    <?php if($enrollments->isEmpty()): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/40 p-10 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
            <h2 class="text-lg font-black text-slate-800 dark:text-slate-100"><?php echo e(__('student.course_subscriptions_empty_title')); ?></h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 max-w-md mx-auto"><?php echo e(__('student.course_subscriptions_empty_desc')); ?></p>
            <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm transition">
                <i class="fas fa-graduation-cap"></i>
                <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $course = $enrollment->course;
                    if (!$course) { continue; }
                    $thumb = $course->thumbnail_url ?? null;
                    $isActive = $enrollment->subscriptionIsActive();
                    $isExpired = $enrollment->subscriptionIsExpired();
                    $expiringSoon = $enrollment->subscriptionExpiringSoon(7);
                    $daysLeft = $enrollment->daysUntilExpiry();
                    $monthlyPrice = $course->effectiveMonthlyPrice();
                ?>
                <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-48 lg:w-56 shrink-0 bg-slate-100 dark:bg-slate-900 aspect-video md:aspect-auto md:min-h-[140px]">
                            <?php if($thumb): ?>
                                <img src="<?php echo e($thumb); ?>" alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fas fa-book-open text-3xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 p-5 sm:p-6 flex flex-col gap-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 leading-snug"><?php echo e($course->title); ?></h2>
                                    <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-slate-500 dark:text-slate-400">
                                        <?php if($course->isOneToOne() && $course->instructor): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 font-semibold">
                                                <i class="fas fa-user"></i>
                                                <?php echo e(__('student.course_subscriptions_one_to_one')); ?>: <?php echo e($course->instructor->name); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 font-semibold">
                                                <i class="fas fa-users"></i>
                                                <?php echo e(__('student.course_subscriptions_group')); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php if($monthlyPrice > 0): ?>
                                            <span><?php echo e(number_format($monthlyPrice, 0)); ?> <?php echo e(__('public.currency_egp')); ?> / <?php echo e(__('public.per_month')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($isExpired): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        <i class="fas fa-times-circle"></i>
                                        <?php echo e(__('student.course_subscriptions_status_expired')); ?>

                                    </span>
                                <?php elseif($expiringSoon): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800">
                                        <i class="fas fa-hourglass-half"></i>
                                        <?php echo e(__('student.course_subscriptions_status_soon')); ?>

                                    </span>
                                <?php elseif($isActive): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo e(__('student.course_subscriptions_status_active')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700">
                                    <p class="text-xs font-semibold text-slate-500 uppercase"><?php echo e(__('student.course_subscriptions_activated')); ?></p>
                                    <p class="font-bold text-slate-800 dark:text-slate-100 mt-0.5"><?php echo e($enrollment->activated_at?->format('Y-m-d') ?? '—'); ?></p>
                                </div>
                                <div class="p-3 rounded-xl <?php echo e($isExpired ? 'bg-rose-50 dark:bg-rose-950/30 border-rose-100 dark:border-rose-900/50' : 'bg-slate-50 dark:bg-slate-900/50 border-slate-100 dark:border-slate-700'); ?> border">
                                    <p class="text-xs font-semibold <?php echo e($isExpired ? 'text-rose-600' : 'text-slate-500'); ?> uppercase"><?php echo e(__('student.course_subscriptions_expires')); ?></p>
                                    <p class="font-bold text-slate-800 dark:text-slate-100 mt-0.5"><?php echo e($enrollment->expires_at?->format('Y-m-d') ?? '—'); ?></p>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700">
                                    <p class="text-xs font-semibold text-slate-500 uppercase"><?php echo e(__('student.course_subscriptions_days_left')); ?></p>
                                    <p class="font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                        <?php if($daysLeft === null): ?>
                                            —
                                        <?php elseif($daysLeft < 0): ?>
                                            <?php echo e(__('student.course_subscriptions_expired_days', ['days' => abs($daysLeft)])); ?>

                                        <?php else: ?>
                                            <?php echo e(__('student.course_subscriptions_days_remaining', ['days' => $daysLeft])); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 pt-1">
                                <?php if($isActive): ?>
                                    <a href="<?php echo e(route('my-courses.show', $course->id)); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold transition">
                                        <i class="fas fa-play"></i>
                                        <?php echo e(__('student.continue_learning')); ?>

                                    </a>
                                <?php endif; ?>
                                <?php if($isExpired || $expiringSoon): ?>
                                    <a href="<?php echo e($enrollment->renewalCheckoutUrl()); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl <?php echo e($isExpired ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-500 hover:bg-amber-600'); ?> text-white text-sm font-bold transition">
                                        <i class="fas fa-sync-alt"></i>
                                        <?php echo e(__('student.course_subscriptions_renew')); ?>

                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                    <i class="fas fa-external-link-alt"></i>
                                    <?php echo e(__('student.course_subscriptions_view_course')); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <p class="text-sm text-slate-500 dark:text-slate-400">
        <?php echo e(__('student.course_subscriptions_footer')); ?>

    </p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\my-course-subscriptions\index.blade.php ENDPATH**/ ?>