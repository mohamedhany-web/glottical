

<?php $__env->startSection('title', __('student.one_to_one_admin_session', ['id' => $session->id])); ?>
<?php $__env->startSection('header', __('student.one_to_one_admin_session', ['id' => $session->id])); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl">
    <a href="<?php echo e(route('admin.one-to-one-sessions.index')); ?>" class="text-sm text-sky-600 hover:underline">← <?php echo e(__('student.one_to_one_admin_title')); ?></a>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap justify-between gap-2">
            <h2 class="text-xl font-bold text-slate-900"><?php echo e($session->course->title ?? '—'); ?></h2>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm font-semibold"><?php echo e($session->statusLabel()); ?></span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_col_student')); ?></dt>
                <dd class="font-semibold"><?php echo e($session->student->name ?? '—'); ?> <span class="text-slate-400 font-normal"><?php echo e($session->student->email ?? ''); ?></span></dd>
            </div>
            <div>
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_col_instructor')); ?></dt>
                <dd class="font-semibold"><?php echo e($session->instructor->name ?? '—'); ?></dd>
            </div>
            <div>
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_col_session')); ?></dt>
                <dd class="font-semibold"><?php echo e(__('student.one_to_one_session_number', ['n' => $session->session_number])); ?></dd>
            </div>
            <div>
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_appointment')); ?></dt>
                <dd class="font-semibold">
                    <?php if($session->scheduled_at): ?>
                        <?php echo e($session->scheduled_at->format('Y-m-d H:i')); ?>

                        (<?php echo e((int) $session->duration_minutes); ?> <?php echo e(__('student.minutes')); ?>)
                    <?php else: ?>
                        <?php echo e(__('student.one_to_one_pending_schedule')); ?>

                    <?php endif; ?>
                </dd>
            </div>
            <?php if($session->bookedBy): ?>
            <div>
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_booked_by')); ?></dt>
                <dd class="font-semibold"><?php echo e($session->bookedBy->name); ?></dd>
            </div>
            <?php endif; ?>
            <?php if($session->classroomMeeting): ?>
            <div class="sm:col-span-2">
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_join_session')); ?></dt>
                <dd>
                    <?php $joinUrl = $session->joinUrl(); ?>
                    <?php if($joinUrl): ?>
                        <a href="<?php echo e($joinUrl); ?>" target="_blank" rel="noopener" class="text-sky-600 font-semibold hover:underline"><?php echo e($joinUrl); ?></a>
                    <?php else: ?>
                        <span class="text-slate-500">—</span>
                    <?php endif; ?>
                </dd>
            </div>
            <?php endif; ?>
            <?php if($session->notes): ?>
            <div class="sm:col-span-2">
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_notes')); ?></dt>
                <dd class="text-slate-800 whitespace-pre-line"><?php echo e($session->notes); ?></dd>
            </div>
            <?php endif; ?>
        </dl>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-900"><?php echo e(__('student.one_to_one_instructor_schedule')); ?></h3>
        <?php if($availability->isEmpty()): ?>
            <p class="text-sm text-amber-700"><?php echo e(__('student.one_to_one_no_availability_rules')); ?></p>
        <?php else: ?>
            <div class="space-y-2 text-sm">
                <?php $__currentLoopData = $dayLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $dayRules = $availability->where('day_of_week', $day); ?>
                    <?php if($dayRules->isNotEmpty()): ?>
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="font-semibold w-24"><?php echo e($label); ?></span>
                            <?php $__currentLoopData = $dayRules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="px-2 py-1 rounded-lg bg-violet-100 text-violet-800 text-xs font-medium">
                                    <?php echo e(substr((string) $rule->start_time, 0, 5)); ?> – <?php echo e(substr((string) $rule->end_time, 0, 5)); ?>

                                    (<?php echo e((int) $rule->slot_duration_minutes); ?> <?php echo e(__('student.minutes')); ?>)
                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if($upcomingSlots->isNotEmpty()): ?>
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-3">
        <h3 class="font-bold text-slate-900"><?php echo e(__('student.one_to_one_upcoming_slots')); ?></h3>
        <div class="flex flex-wrap gap-2">
            <?php $__currentLoopData = $upcomingSlots->take(24); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="px-2 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                    <?php echo e($slot['starts_at']->format('Y-m-d H:i')); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($upcomingSlots->count() > 24): ?>
            <p class="text-xs text-slate-500"><?php echo e(__('student.one_to_one_more_slots', ['n' => $upcomingSlots->count() - 24])); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\one-to-one-sessions\show.blade.php ENDPATH**/ ?>