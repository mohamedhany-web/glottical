

<?php $__env->startSection('title', __('student.one_to_one_schedule_session')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <a href="<?php echo e(route('instructor.one-to-one-sessions.index')); ?>" class="text-sm text-sky-600 hover:underline">← <?php echo e(__('student.one_to_one_sessions_instructor_nav')); ?></a>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-start gap-2">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white"><?php echo e($session->course->title ?? '—'); ?></h1>
                <p class="text-sm text-slate-500 mt-1"><?php echo e($session->student->name ?? 'طالب'); ?> — <?php echo e(__('student.one_to_one_session_number', ['n' => $session->session_number])); ?></p>
            </div>
            <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-semibold"><?php echo e($session->statusLabel()); ?></span>
        </div>

        <?php if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED && $session->classroomMeeting): ?>
            <?php $m = $session->classroomMeeting; ?>
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 space-y-3">
                <p class="font-bold text-emerald-900 dark:text-emerald-100">الموعد: <?php echo e($session->scheduled_at?->format('Y-m-d H:i')); ?></p>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('instructor.classroom.show', $m)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold">إعدادات الغرفة</a>
                    <?php if(!$m->ended_at): ?>
                    <a href="<?php echo e(route('instructor.classroom.room', $m)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">دخول الغرفة</a>
                    <?php endif; ?>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('instructor.one-to-one-sessions.complete', $session)); ?>" onsubmit="return confirm('تأكيد إتمام الحصة؟')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold"><?php echo e(__('student.one_to_one_mark_complete')); ?></button>
            </form>
        <?php elseif($session->status === \App\Models\OneToOneSession::STATUS_PENDING): ?>
            <form method="POST" action="<?php echo e(route('instructor.one-to-one-sessions.schedule', $session)); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">تاريخ ووقت الحصة</label>
                    <input type="datetime-local" name="scheduled_at" required
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5"
                           min="<?php echo e(now()->addHour()->format('Y-m-d\TH:i')); ?>">
                    <?php $__errorArgs = ['scheduled_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">المدة (دقيقة)</label>
                    <input type="number" name="duration_minutes" value="60" min="30" max="180" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5">
                </div>
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold"><?php echo e(__('student.one_to_one_schedule_session')); ?></button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\instructor\one-to-one-sessions\show.blade.php ENDPATH**/ ?>