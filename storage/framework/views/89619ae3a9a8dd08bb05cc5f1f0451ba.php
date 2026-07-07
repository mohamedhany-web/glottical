

<?php $__env->startSection('title', 'العملاء المحتملون'); ?>
<?php $__env->startSection('header', 'CRM — العملاء المحتملون'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <?php echo $__env->make('partials.crm-admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <form method="GET" class="flex flex-wrap gap-2">
        <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو البريد أو الهاتف..." class="rounded-lg border px-3 py-2 text-sm">
        <select name="status" class="rounded-lg border px-3 py-2 text-sm">
            <option value="">كل الحالات</option>
            <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">تصفية</button>
    </form>

    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">#</th>
                    <th class="px-4 py-3 text-right">الاسم</th>
                    <th class="px-4 py-3 text-right">مالك التسويق</th>
                    <th class="px-4 py-3 text-right">المبيعات</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($lead->id); ?></td>
                        <td class="px-4 py-3 font-semibold"><?php echo e($lead->name); ?></td>
                        <td class="px-4 py-3"><?php echo e($lead->marketingOwner?->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($lead->assignedTo?->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-xs font-bold"><?php echo e($lead->status_label); ?></span></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('admin.crm.leads.show', $lead)); ?>" class="text-sky-600 font-bold">تفاصيل</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا يوجد عملاء محتملون</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($leads->hasPages()): ?><div class="p-4"><?php echo e($leads->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/crm/leads/index.blade.php ENDPATH**/ ?>