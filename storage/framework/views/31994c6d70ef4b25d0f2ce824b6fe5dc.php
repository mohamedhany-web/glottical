

<?php $__env->startSection('title', 'عمولاتي'); ?>
<?php $__env->startSection('header', 'CRM — عمولاتي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase"><tr>
        <th class="px-4 py-3 text-right">العميل</th><th class="px-4 py-3 text-right">النوع</th><th class="px-4 py-3 text-right">المبلغ</th><th class="px-4 py-3 text-right">الحالة</th><th></th>
      </tr></thead>
      <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="px-4 py-3">#<?php echo e($c->sales_lead_id); ?> <?php echo e($c->lead?->name); ?></td>
            <td class="px-4 py-3"><?php echo e($c->typeLabel()); ?></td>
            <td class="px-4 py-3 font-bold"><?php echo e(number_format($c->commission_amount_egp, 2)); ?> ج.م</td>
            <td class="px-4 py-3"><?php echo e($c->statusLabel()); ?></td>
            <td class="px-4 py-3">
              <?php if($role === 'finance' && $c->status === \App\Models\CrmCommission::STATUS_PENDING): ?>
                <form method="POST" action="<?php echo e(route('employee.crm.commissions.approve', $c)); ?>"><?php echo csrf_field(); ?><button class="text-emerald-600 font-bold text-xs">اعتماد للصرف</button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">لا توجد عمولات بعد</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4"><?php echo e($commissions->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/commissions.blade.php ENDPATH**/ ?>