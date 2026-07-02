

<?php $__env->startSection('title', 'سجل تدقيق CRM'); ?>
<?php $__env->startSection('header', 'CRM — سجل التدقيق'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <a href="<?php echo e(route('admin.crm.dashboard')); ?>" class="text-sm text-sky-600 font-semibold">← لوحة CRM</a>
    <p class="text-sm text-slate-600">السجلات غير قابلة للحذف — كل عملية موثقة.</p>
    <form method="GET" class="flex gap-2">
        <select name="action" class="rounded-lg border px-3 py-2 text-sm">
            <option value="">كل العمليات</option>
            <?php $__currentLoopData = $actionLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('action')===$k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">تصفية</button>
    </form>
    <div class="rounded-2xl bg-white border divide-y text-sm">
        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-5 py-3 flex flex-wrap justify-between gap-2">
                <div>
                    <span class="font-bold"><?php echo e($log->actionLabel()); ?></span>
                    <?php if($log->lead): ?><a href="<?php echo e(route('admin.crm.leads.show', $log->lead)); ?>" class="text-sky-600 mr-2">Lead #<?php echo e($log->sales_lead_id); ?></a><?php endif; ?>
                </div>
                <span class="text-slate-500 text-xs"><?php echo e($log->user?->name); ?> — <?php echo e($log->created_at?->format('Y-m-d H:i:s')); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div><?php echo e($logs->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\audit\index.blade.php ENDPATH**/ ?>