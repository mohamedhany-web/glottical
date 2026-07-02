

<?php $__env->startSection('title', 'عمولات CRM'); ?>
<?php $__env->startSection('header', 'CRM — العمولات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <a href="<?php echo e(route('employee.crm.dashboard')); ?>" class="text-sm text-sky-600 font-bold">← CRM</a>
  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase"><tr>
        <th class="px-4 py-3 text-right">Lead</th><th class="px-4 py-3 text-right">النوع</th><th class="px-4 py-3 text-right">المبلغ</th><th class="px-4 py-3 text-right">الحالة</th><th></th>
      </tr></thead>
      <tbody class="divide-y">
        <?php $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="px-4 py-3">#<?php echo e($c->sales_lead_id); ?></td>
            <td class="px-4 py-3"><?php echo e($c->typeLabel()); ?></td>
            <td class="px-4 py-3 font-bold"><?php echo e(number_format($c->commission_amount_egp, 2)); ?></td>
            <td class="px-4 py-3"><?php echo e($c->status); ?></td>
            <td class="px-4 py-3">
              <?php if($role === 'finance' && $c->status === \App\Models\CrmCommission::STATUS_PENDING): ?>
                <form method="POST" action="<?php echo e(route('employee.crm.commissions.approve', $c)); ?>"><?php echo csrf_field(); ?><button class="text-emerald-600 font-bold text-xs">اعتماد</button></form>
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

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\employee\crm\commissions.blade.php ENDPATH**/ ?>