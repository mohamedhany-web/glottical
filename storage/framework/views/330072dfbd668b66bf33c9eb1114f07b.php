

<?php $__env->startSection('title', __('student.one_to_one_sessions_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium"><?php echo e(__('auth.dashboard')); ?></a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <span class="text-gray-900 dark:text-gray-200 font-semibold"><?php echo e(__('student.one_to_one_sessions_title')); ?></span>
    </div>

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(__('student.one_to_one_sessions_title')); ?></h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo e(__('student.one_to_one_sessions_subtitle')); ?></p>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-600 dark:text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right"><?php echo e(__('public.courses')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('landing.nav.instructors')); ?></th>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('public.checkout_step_3_title') ?? 'الحالة'); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('public.checkout_step_2_title') ?? 'الموعد'); ?></th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white"><?php echo e($session->course->title ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?php echo e($session->instructor->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-500"><?php echo e(__('student.one_to_one_session_number', ['n' => $session->session_number])); ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-md text-xs font-medium
                                    <?php if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED): ?> bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200
                                    <?php elseif($session->status === \App\Models\OneToOneSession::STATUS_COMPLETED): ?> bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200
                                    <?php elseif($session->status === \App\Models\OneToOneSession::STATUS_PENDING): ?> bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200
                                    <?php else: ?> bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 <?php endif; ?>">
                                    <?php echo e($session->statusLabel()); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                <?php if($session->scheduled_at): ?>
                                    <?php echo e($session->scheduled_at->format('Y-m-d H:i')); ?>

                                <?php else: ?>
                                    <?php echo e(__('student.one_to_one_pending_schedule')); ?>

                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('student.one-to-one-sessions.show', $session)); ?>" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline"><?php echo e(__('public.view_details')); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500"><?php echo e(__('student.one_to_one_sessions_empty')); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700"><?php echo e($sessions->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\one-to-one-sessions\index.blade.php ENDPATH**/ ?>