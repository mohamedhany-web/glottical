

<?php $__env->startSection('title', __('student.one_to_one_sessions_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <a href="<?php echo e(route('student.one-to-one-sessions.index')); ?>" class="text-sm text-sky-600 hover:underline">← <?php echo e(__('student.one_to_one_sessions_nav')); ?></a>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-start gap-2">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white"><?php echo e($session->course->title ?? __('student.one_to_one_sessions_title')); ?></h1>
                <p class="text-sm text-slate-500 mt-1"><?php echo e(__('student.one_to_one_session_number', ['n' => $session->session_number])); ?></p>
            </div>
            <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-semibold"><?php echo e($session->statusLabel()); ?></span>
        </div>

        <dl class="text-sm space-y-2">
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2">
                <dt class="text-slate-500"><?php echo e(__('landing.nav.instructors')); ?></dt>
                <dd class="font-bold text-slate-900 dark:text-white"><?php echo e($session->instructor->name ?? '—'); ?></dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2">
                <dt class="text-slate-500"><?php echo e(__('public.checkout_step_2_title') ?? 'الموعد'); ?></dt>
                <dd class="font-bold">
                    <?php if($session->scheduled_at): ?>
                        <?php echo e($session->scheduled_at->format('Y-m-d H:i')); ?>

                        <span class="text-slate-500 font-normal">(<?php echo e((int) $session->duration_minutes); ?> <?php echo e(__('student.minutes')); ?>)</span>
                    <?php else: ?>
                        <?php echo e(__('student.one_to_one_pending_schedule')); ?>

                    <?php endif; ?>
                </dd>
            </div>
        </dl>

        <?php if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED && $session->classroomMeeting): ?>
            <?php $joinUrl = $session->joinUrl(); ?>
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 space-y-3">
                <p class="font-bold text-emerald-900 dark:text-emerald-100"><?php echo e(__('student.one_to_one_join_session')); ?></p>
                <?php if($joinUrl): ?>
                    <a href="<?php echo e($joinUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">
                        <i class="fas fa-video"></i> <?php echo e(__('student.one_to_one_join_session')); ?>

                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\one-to-one-sessions\show.blade.php ENDPATH**/ ?>