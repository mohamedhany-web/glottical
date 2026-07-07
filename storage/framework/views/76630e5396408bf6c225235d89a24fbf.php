

<?php $__env->startSection('title', 'تقارير CRM'); ?>
<?php $__env->startSection('header', 'CRM — التقارير'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div class="flex flex-wrap gap-2 justify-between items-center">
    <p class="text-sm text-gray-600">ارفع تقارير أسبوعية أو شهرية للإدارة (PDF أو ملف Office).</p>
    <div class="flex gap-2">
      <a href="<?php echo e(route('employee.crm.reports.create', ['type' => 'weekly'])); ?>" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">تقرير أسبوعي</a>
      <a href="<?php echo e(route('employee.crm.reports.create', ['type' => 'monthly'])); ?>" class="px-3 py-2 rounded-lg bg-violet-600 text-white text-sm font-bold">تقرير شهري</a>
      <a href="<?php echo e(route('employee.crm.reports.create', ['type' => 'ad_hoc'])); ?>" class="px-3 py-2 rounded-lg bg-slate-600 text-white text-sm font-bold">تقرير خاص</a>
    </div>
  </div>

  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-right">العنوان</th>
          <th class="px-4 py-3 text-right">النوع</th>
          <th class="px-4 py-3 text-right">الفترة</th>
          <th class="px-4 py-3 text-right">الحالة</th>
          <th class="px-4 py-3 text-right">الملف</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="px-4 py-3 font-semibold"><?php echo e($report->title); ?></td>
            <td class="px-4 py-3"><?php echo e($report->type_label); ?></td>
            <td class="px-4 py-3 text-gray-600">
              <?php if($report->period_start): ?><?php echo e($report->period_start->format('Y-m-d')); ?> — <?php echo e($report->period_end?->format('Y-m-d')); ?><?php else: ?>—<?php endif; ?>
            </td>
            <td class="px-4 py-3"><?php echo e($report->status_label); ?></td>
            <td class="px-4 py-3">
              <?php if($report->file_path): ?>
                <a href="<?php echo e(route('employee.crm.reports.download', $report)); ?>" class="text-indigo-600 font-bold text-xs">تحميل</a>
              <?php else: ?> — <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">لم تُرفع تقارير بعد</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4"><?php echo e($reports->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/reports/index.blade.php ENDPATH**/ ?>