

<?php $__env->startSection('title', 'CRM Leads'); ?>
<?php $__env->startSection('header', 'CRM — Leads'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <div class="flex justify-between items-center">
    <a href="<?php echo e(route('employee.crm.dashboard')); ?>" class="text-sm text-sky-600 font-bold">← CRM</a>
    <?php if($role === 'marketing'): ?>
      <a href="<?php echo e(route('employee.crm.leads.create')); ?>" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-bold">+ Lead</a>
    <?php endif; ?>
  </div>
  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase"><tr>
        <th class="px-4 py-3 text-right">الاسم</th><th class="px-4 py-3 text-right">الحالة</th><th class="px-4 py-3 text-right"></th>
      </tr></thead>
      <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="px-4 py-3 font-semibold"><?php echo e($lead->name); ?></td>
            <td class="px-4 py-3"><?php echo e($lead->status_label); ?></td>
            <td class="px-4 py-3"><a href="<?php echo e(route('employee.crm.leads.show', $lead)); ?>" class="text-sky-600 font-bold">عرض</a></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500">لا Leads</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4"><?php echo e($leads->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\employee\crm\leads\index.blade.php ENDPATH**/ ?>