

<?php $__env->startSection('title', __('student.one_to_one_sessions_instructor_nav')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(__('student.one_to_one_sessions_instructor_nav')); ?></h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo e(__('student.one_to_one_sessions_subtitle')); ?></p>
    </div>

    <?php if($students->isNotEmpty()): ?>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <h2 class="text-sm font-black text-gray-900 dark:text-white mb-3"><?php echo e(__('student.one_to_one_instructor_students')); ?></h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-lg border border-gray-100 dark:border-gray-700 p-3">
                    <p class="font-bold text-gray-900 dark:text-white"><?php echo e($row['student']->name ?? '—'); ?></p>
                    <p class="text-xs text-gray-500 mt-0.5"><?php echo e($row['course']->title ?? ''); ?></p>
                    <p class="text-xs mt-2 text-amber-600"><?php echo e($row['pending']); ?> بانتظار الجدولة · <?php echo e($row['scheduled']); ?> مجدولة</p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-600 dark:text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">الكورس</th>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">الموعد</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white"><?php echo e($session->student->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?php echo e($session->course->title ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-500"><?php echo e($session->session_number); ?></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-xs font-medium"><?php echo e($session->statusLabel()); ?></span></td>
                            <td class="px-4 py-3 text-xs text-gray-500"><?php echo e($session->scheduled_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('instructor.one-to-one-sessions.show', $session)); ?>" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline">إدارة</a>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\instructor\one-to-one-sessions\index.blade.php ENDPATH**/ ?>