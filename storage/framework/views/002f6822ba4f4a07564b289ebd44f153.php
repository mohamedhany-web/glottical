

<?php $__env->startSection('title', 'العملاء المحتملون'); ?>
<?php $__env->startSection('header', 'CRM — العملاء المحتملون'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <?php if($role === 'marketing'): ?>
    <div class="flex justify-end">
      <a href="<?php echo e(route('employee.crm.leads.create')); ?>" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-bold">+ إضافة عميل جديد</a>
    </div>
  <?php endif; ?>

  <form method="GET" class="flex flex-wrap gap-2">
    <select name="status" class="rounded-lg border px-3 py-2 text-sm">
      <option value="">كل الحالات</option>
      <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">تصفية</button>
  </form>

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
            <td class="px-4 py-3"><a href="<?php echo e(route('employee.crm.leads.show', $lead)); ?>" class="text-sky-600 font-bold">عرض التفاصيل</a></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500">لا يوجد عملاء محتملون في قائمتك</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4"><?php echo e($leads->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/leads/index.blade.php ENDPATH**/ ?>