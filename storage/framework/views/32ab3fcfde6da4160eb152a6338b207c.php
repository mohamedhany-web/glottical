

<?php $__env->startSection('title', 'تفاصيل تذكرة الدعم'); ?>
<?php $__env->startSection('header', 'تفاصيل تذكرة الدعم'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white"><?php echo e($ticket->subject); ?></h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    التصنيف: <?php echo e($ticket->inquiryCategory->name ?? '—'); ?>

                    <span class="mx-1">|</span>
                    الحالة: <?php echo e($ticket->status); ?>

                    <span class="mx-1">|</span>
                    الأولوية: <?php echo e($ticket->priority); ?>

                </p>
            </div>
            <a href="<?php echo e(route('student.support.index')); ?>" class="text-sm text-sky-600 hover:underline">العودة للتذاكر</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-3">
        <?php $__currentLoopData = $ticket->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-xl p-3 <?php echo e($reply->sender_type === 'admin' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200'); ?>">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-semibold text-slate-700"><?php echo e($reply->sender_type === 'admin' ? 'فريق الدعم' : 'أنت'); ?></p>
                    <p class="text-[11px] text-slate-500"><?php echo e($reply->created_at->format('Y-m-d H:i')); ?></p>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($reply->message); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if(!in_array($ticket->status, ['resolved', 'closed'], true)): ?>
        <form action="<?php echo e(route('student.support.reply', $ticket)); ?>" method="POST" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-3">
            <?php echo csrf_field(); ?>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">إضافة رد</label>
            <textarea name="message" rows="4" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white"><?php echo e(old('message')); ?></textarea>
            <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="text-left">
                <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">إرسال الرد</button>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\support\show.blade.php ENDPATH**/ ?>