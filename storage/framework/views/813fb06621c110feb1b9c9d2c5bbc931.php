

<?php $__env->startSection('title', __('student.one_to_one_admin_title')); ?>
<?php $__env->startSection('header', __('student.one_to_one_admin_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="rounded-xl bg-white border border-amber-200 bg-amber-50/40 p-4">
            <p class="text-xs text-amber-700"><?php echo e(__('student.one_to_one_pending_schedule')); ?></p>
            <p class="text-2xl font-bold text-amber-800"><?php echo e($stats['pending']); ?></p>
        </div>
        <div class="rounded-xl bg-white border border-emerald-200 bg-emerald-50/40 p-4">
            <p class="text-xs text-emerald-700"><?php echo e(\App\Models\OneToOneSession::statusLabels()[\App\Models\OneToOneSession::STATUS_SCHEDULED]); ?></p>
            <p class="text-2xl font-bold text-emerald-800"><?php echo e($stats['scheduled']); ?></p>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500"><?php echo e(\App\Models\OneToOneSession::statusLabels()[\App\Models\OneToOneSession::STATUS_COMPLETED]); ?></p>
            <p class="text-2xl font-bold"><?php echo e($stats['completed']); ?></p>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <select name="status" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <option value="all" <?php echo e($status === 'all' ? 'selected' : ''); ?>><?php echo e(__('student.one_to_one_filter_all')); ?></option>
                    <?php $__currentLoopData = \App\Models\OneToOneSession::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e($status === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($instructors->isNotEmpty()): ?>
                <select name="instructor_id" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <option value="0"><?php echo e(__('student.one_to_one_filter_instructor')); ?></option>
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ins->id); ?>" <?php echo e((int) $instructorId === (int) $ins->id ? 'selected' : ''); ?>><?php echo e($ins->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php endif; ?>
                <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold"><?php echo e(__('student.one_to_one_filter_apply')); ?></button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('student.one_to_one_col_student')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('student.one_to_one_col_instructor')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('student.one_to_one_col_course')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('student.one_to_one_col_session')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('student.one_to_one_appointment')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('student.one_to_one_col_status')); ?></th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs"><?php echo e($session->id); ?></td>
                            <td class="px-4 py-3"><?php echo e($session->student->name ?? '—'); ?></td>
                            <td class="px-4 py-3"><?php echo e($session->instructor->name ?? '—'); ?></td>
                            <td class="px-4 py-3 max-w-[12rem] truncate" title="<?php echo e($session->course->title ?? ''); ?>"><?php echo e($session->course->title ?? '—'); ?></td>
                            <td class="px-4 py-3"><?php echo e(__('student.one_to_one_session_number', ['n' => $session->session_number])); ?></td>
                            <td class="px-4 py-3">
                                <?php if($session->scheduled_at): ?>
                                    <?php echo e($session->scheduled_at->format('Y-m-d H:i')); ?>

                                <?php else: ?>
                                    <span class="text-amber-600"><?php echo e(__('student.one_to_one_pending_schedule')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-xs font-semibold"><?php echo e($session->statusLabel()); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.one-to-one-sessions.show', $session)); ?>" class="text-sky-600 font-semibold hover:underline"><?php echo e(__('public.view_details')); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="px-4 py-12 text-center text-slate-500"><?php echo e(__('student.one_to_one_sessions_empty')); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($sessions->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($sessions->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\one-to-one-sessions\index.blade.php ENDPATH**/ ?>