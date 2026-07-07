

<?php $__env->startSection('title', 'التقارير المالية للمبيعات'); ?>
<?php $__env->startSection('header', 'CRM — التقارير المالية للمبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div class="rounded-2xl bg-gradient-to-l from-emerald-600 to-teal-700 text-white p-5">
    <p class="font-black text-lg">تقارير مبيعات CRM فقط</p>
    <p class="text-emerald-100 text-sm mt-1">طلبات، إيرادات، عمولات، وتقارير الفريق — بدون تقارير المنصة العامة (مهام/إجازات/محاسبة كاملة).</p>
  </div>

  <form method="GET" class="flex flex-wrap gap-2 items-center">
    <label class="text-sm font-bold text-gray-700">الفترة:</label>
    <?php $__currentLoopData = ['week'=>'أسبوع','month'=>'شهر','quarter'=>'ربع سنة','year'=>'سنة']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route('employee.crm.sales-financial', ['period'=>$k])); ?>"
         class="px-3 py-1.5 rounded-lg text-sm font-bold <?php echo e($period===$k ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'); ?>"><?php echo e($l); ?></a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <span class="text-xs text-gray-500 mr-2"><?php echo e($report['start']->format('Y-m-d')); ?> — <?php echo e($report['end']->format('Y-m-d')); ?></span>
  </form>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">إيراد معتمد</p><p class="text-2xl font-black text-emerald-700"><?php echo e(number_format($report['orders']['revenue_approved'], 2)); ?> ج.م</p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">طلبات معلقة</p><p class="text-2xl font-black text-amber-700"><?php echo e($report['orders']['pending']); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">عمولات معلقة</p><p class="text-2xl font-black text-violet-700"><?php echo e(number_format($report['commissions']['pending'], 2)); ?> ج.م</p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">عملاء بانتظار الدفع</p><p class="text-2xl font-black text-rose-700"><?php echo e($report['leads']['payment_pending']); ?></p></div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded-2xl border bg-white p-5 space-y-3">
      <h3 class="font-bold">ملخص الطلبات (مبيعات)</h3>
      <dl class="grid grid-cols-2 gap-2 text-sm">
        <div><dt class="text-gray-500">إجمالي الطلبات</dt><dd class="font-bold"><?php echo e($report['orders']['total']); ?></dd></div>
        <div><dt class="text-gray-500">معتمدة</dt><dd class="font-bold text-emerald-700"><?php echo e($report['orders']['approved']); ?></dd></div>
        <div><dt class="text-gray-500">مرفوضة</dt><dd class="font-bold"><?php echo e($report['orders']['rejected']); ?></dd></div>
        <div><dt class="text-gray-500">مرتبطة بـ CRM</dt><dd class="font-bold"><?php echo e($report['orders']['crm_linked']); ?></dd></div>
        <div class="col-span-2"><dt class="text-gray-500">إيراد CRM المعتمد</dt><dd class="font-bold"><?php echo e(number_format($report['orders']['crm_revenue'], 2)); ?> ج.م</dd></div>
      </dl>
    </div>
    <div class="rounded-2xl border bg-white p-5 space-y-3">
      <h3 class="font-bold">ملخص العمولات</h3>
      <dl class="grid grid-cols-2 gap-2 text-sm">
        <div><dt class="text-gray-500">معلقة</dt><dd class="font-bold text-amber-700"><?php echo e(number_format($report['commissions']['pending'], 2)); ?> ج.م</dd></div>
        <div><dt class="text-gray-500">معتمدة</dt><dd class="font-bold text-emerald-700"><?php echo e(number_format($report['commissions']['approved'], 2)); ?> ج.م</dd></div>
        <div><dt class="text-gray-500">مصروفة</dt><dd class="font-bold"><?php echo e(number_format($report['commissions']['paid'], 2)); ?> ج.م</dd></div>
        <?php $__currentLoopData = $report['commissions']['by_type']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div><dt class="text-gray-500">عمولة <?php echo e($type); ?></dt><dd class="font-bold"><?php echo e(number_format((float)$total, 2)); ?> ج.م</dd></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </dl>
    </div>
  </div>

  <?php if($report['revenue_by_sales']->isNotEmpty()): ?>
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">إيراد المبيعات حسب المندوب</div>
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase"><tr>
        <th class="px-4 py-3 text-right">المندوب</th>
        <th class="px-4 py-3 text-right">طلبات</th>
        <th class="px-4 py-3 text-right">إيراد</th>
      </tr></thead>
      <tbody class="divide-y">
        <?php $__currentLoopData = $report['revenue_by_sales']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="px-4 py-3 font-semibold"><?php echo e($row->sales_name); ?></td>
            <td class="px-4 py-3"><?php echo e($row->orders_count); ?></td>
            <td class="px-4 py-3 font-bold"><?php echo e(number_format((float)$row->revenue, 2)); ?> ج.م</td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php if($teamPerformance->isNotEmpty()): ?>
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">أداء فرق المبيعات والتسويق</div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase"><tr>
          <th class="px-4 py-3 text-right">المجموعة</th>
          <th class="px-4 py-3 text-right">العضو</th>
          <th class="px-4 py-3 text-right">الدور</th>
          <th class="px-4 py-3 text-right">عملاء</th>
          <th class="px-4 py-3 text-right">ناجحة</th>
          <th class="px-4 py-3 text-right">إيراد</th>
          <th class="px-4 py-3 text-right">عمولات</th>
        </tr></thead>
        <tbody class="divide-y">
          <?php $__currentLoopData = $teamPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td class="px-4 py-3"><?php echo e($row['group_name']); ?></td>
              <td class="px-4 py-3 font-semibold"><?php echo e($row['user_name']); ?></td>
              <td class="px-4 py-3"><?php echo e($row['role_label']); ?></td>
              <td class="px-4 py-3"><?php echo e($row['total_leads']); ?></td>
              <td class="px-4 py-3 text-emerald-700 font-bold"><?php echo e($row['closed_won']); ?></td>
              <td class="px-4 py-3"><?php echo e(number_format($row['revenue'], 2)); ?> ج.م</td>
              <td class="px-4 py-3"><?php echo e(number_format($row['commissions'], 2)); ?> ج.م</td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">تقارير الفريق المرفوعة (تسويق/مبيعات/قادة)</div>
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase"><tr>
        <th class="px-4 py-3 text-right">الموظف</th>
        <th class="px-4 py-3 text-right">العنوان</th>
        <th class="px-4 py-3 text-right">النوع</th>
        <th class="px-4 py-3 text-right">التاريخ</th>
        <th class="px-4 py-3 text-right">الحالة</th>
      </tr></thead>
      <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $report['submitted_reports']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="px-4 py-3"><?php echo e($r->user?->name); ?></td>
            <td class="px-4 py-3 font-semibold"><?php echo e($r->title); ?></td>
            <td class="px-4 py-3"><?php echo e($r->type_label); ?></td>
            <td class="px-4 py-3 text-gray-600"><?php echo e($r->created_at?->format('Y-m-d')); ?></td>
            <td class="px-4 py-3"><?php echo e($r->status_label); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">لا توجد تقارير مرفوعة في هذه الفترة</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/sales-financial.blade.php ENDPATH**/ ?>