

<?php $__env->startSection('title', 'تذكرة دعم'); ?>
<?php $__env->startSection('header', 'تذكرة دعم'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900"><?php echo e($ticket->subject); ?></h1>
                <p class="text-xs text-slate-500 mt-1">
                    العميل: <?php echo e($ticket->user->name ?? '—'); ?>

                    <span class="mx-1">|</span>
                    التصنيف: <?php echo e($ticket->inquiryCategory->name ?? '—'); ?>

                    <span class="mx-1">|</span>
                    الحالة: <?php echo e($ticket->status); ?>

                    <span class="mx-1">|</span>
                    الأولوية: <?php echo e($ticket->priority); ?>

                </p>
            </div>
            <form method="POST" action="<?php echo e(route('admin.support-tickets.status', $ticket)); ?>" class="flex items-center gap-2">
                <?php echo csrf_field(); ?>
                <select name="status" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <?php $__currentLoopData = ['open','in_progress','resolved','closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php echo e($ticket->status === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">حفظ الحالة</button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 space-y-3">
        <?php $__currentLoopData = $ticket->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-xl p-3 <?php echo e($reply->sender_type === 'admin' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200'); ?>">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-semibold text-slate-700"><?php echo e($reply->sender_type === 'admin' ? 'الإدارة' : ($reply->user->name ?? 'العميل')); ?></p>
                    <p class="text-[11px] text-slate-500"><?php echo e($reply->created_at->format('Y-m-d H:i')); ?></p>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($reply->message); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="POST" action="<?php echo e(route('admin.support-tickets.reply', $ticket)); ?>" class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 space-y-3">
        <?php echo csrf_field(); ?>
        <label class="block text-sm font-semibold text-slate-700">رد فريق الدعم</label>
        <textarea name="message" rows="4" class="w-full px-3 py-2 rounded-lg border border-slate-200"><?php echo e(old('message')); ?></textarea>
        <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <div class="text-left">
            <button class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold">إرسال الرد</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\support-tickets\show.blade.php ENDPATH**/ ?>