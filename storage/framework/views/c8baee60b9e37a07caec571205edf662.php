

<?php $__env->startSection('title', 'تقارير CRM'); ?>
<?php $__env->startSection('header', 'تقارير CRM'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <?php echo $__env->make('partials.crm-admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <form method="GET" class="flex flex-wrap gap-2">
    <select name="type" class="rounded-lg border px-3 py-2 text-sm">
      <option value="">كل الأنواع</option>
      <?php $__currentLoopData = $typeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('type')===$k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="status" class="rounded-lg border px-3 py-2 text-sm">
      <option value="">كل الحالات</option>
      <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('status')===$k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">تصفية</button>
  </form>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-right">الموظف</th>
          <th class="px-4 py-3 text-right">العنوان</th>
          <th class="px-4 py-3 text-right">النوع</th>
          <th class="px-4 py-3 text-right">الفترة</th>
          <th class="px-4 py-3 text-right">الحالة</th>
          <th class="px-4 py-3 text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="px-4 py-3"><?php echo e($report->user?->name); ?></td>
            <td class="px-4 py-3 font-semibold"><?php echo e($report->title); ?></td>
            <td class="px-4 py-3"><?php echo e($report->type_label); ?></td>
            <td class="px-4 py-3 text-gray-600"><?php if($report->period_start): ?><?php echo e($report->period_start->format('Y-m-d')); ?> — <?php echo e($report->period_end?->format('Y-m-d')); ?><?php else: ?>—<?php endif; ?></td>
            <td class="px-4 py-3"><?php echo e($report->status_label); ?></td>
            <td class="px-4 py-3"><a href="<?php echo e(route('admin.crm.reports.show', $report)); ?>" class="text-indigo-600 font-bold text-xs">عرض</a></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">لا توجد تقارير</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4"><?php echo e($reports->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/crm/reports/index.blade.php ENDPATH**/ ?>