

<?php $__env->startSection('title', __('student.one_to_one_sessions_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php $__errorArgs = ['scheduled_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

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
                <dt class="text-slate-500"><?php echo e(__('student.one_to_one_appointment')); ?></dt>
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

        <?php if($session->status === \App\Models\OneToOneSession::STATUS_PENDING): ?>
            <div class="rounded-xl border border-violet-200 dark:border-violet-800 bg-violet-50 dark:bg-violet-900/20 p-4 space-y-4">
                <h2 class="font-bold text-violet-900 dark:text-violet-100"><?php echo e(__('student.one_to_one_pick_slot')); ?></h2>
                <?php if($availableSlots->isEmpty()): ?>
                    <p class="text-sm text-violet-800/80 dark:text-violet-200/80"><?php echo e(__('student.one_to_one_no_slots')); ?></p>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('student.one-to-one-sessions.book', $session)); ?>" class="space-y-3 max-h-80 overflow-y-auto">
                        <?php echo csrf_field(); ?>
                        <?php $grouped = $availableSlots->groupBy(fn ($s) => $s['starts_at']->format('Y-m-d')); ?>
                        <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $daySlots): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <p class="text-xs font-bold text-violet-700 dark:text-violet-300 mb-2"><?php echo e(\Carbon\Carbon::parse($date)->locale(app()->getLocale())->isoFormat('dddd D MMMM')); ?></p>
                                <div class="flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $daySlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="scheduled_at" value="<?php echo e($slot['starts_at']->format('Y-m-d H:i:s')); ?>" class="peer sr-only" required>
                                            <span class="inline-flex px-3 py-2 rounded-lg border border-violet-200 dark:border-violet-700 text-sm font-semibold text-violet-900 dark:text-violet-100 peer-checked:bg-violet-600 peer-checked:text-white peer-checked:border-violet-600 hover:bg-violet-100 dark:hover:bg-violet-900/40 transition">
                                                <?php echo e($slot['starts_at']->format('H:i')); ?>

                                            </span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <button type="submit" class="w-full sm:w-auto mt-2 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold">
                            <i class="fas fa-calendar-check <?php echo e(app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1'); ?>"></i><?php echo e(__('student.one_to_one_confirm_booking')); ?>

                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

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