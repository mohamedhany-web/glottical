

<?php $__env->startSection('title', 'عمولات CRM'); ?>
<?php $__env->startSection('header', 'CRM — العمولات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <a href="<?php echo e(route('admin.crm.dashboard')); ?>" class="text-sm text-sky-600 font-semibold">← لوحة CRM</a>
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-slate-500">معلقة</p><p class="text-2xl font-bold"><?php echo e($stats['pending']); ?></p></div>
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-slate-500">معتمدة</p><p class="text-2xl font-bold text-emerald-700"><?php echo e($stats['approved']); ?></p></div>
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-slate-500">الإجمالي</p><p class="text-xl font-bold"><?php echo e(number_format($stats['total_amount'], 2)); ?></p></div>
    </div>
    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase"><tr>
                <th class="px-4 py-3 text-right">Lead</th><th class="px-4 py-3 text-right">المستفيد</th><th class="px-4 py-3 text-right">النوع</th><th class="px-4 py-3 text-right">المبلغ</th><th class="px-4 py-3 text-right">الحالة</th><th></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-3">#<?php echo e($c->sales_lead_id); ?> <?php echo e($c->lead?->name); ?></td>
                        <td class="px-4 py-3"><?php echo e($c->user?->name); ?></td>
                        <td class="px-4 py-3"><?php echo e($c->typeLabel()); ?></td>
                        <td class="px-4 py-3 font-bold"><?php echo e(number_format($c->commission_amount_egp, 2)); ?></td>
                        <td class="px-4 py-3"><?php echo e($c->status); ?></td>
                        <td class="px-4 py-3">
                            <?php if($c->status === \App\Models\CrmCommission::STATUS_PENDING): ?>
                                <form method="POST" action="<?php echo e(route('admin.crm.commissions.approve', $c)); ?>"><?php echo csrf_field(); ?>
                                    <button class="text-emerald-600 font-bold text-xs">اعتماد</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="p-4"><?php echo e($commissions->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\commissions\index.blade.php ENDPATH**/ ?>