

<?php $__env->startSection('title', 'Glottical CRM'); ?>
<?php $__env->startSection('header', 'Glottical CRM'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="rounded-2xl bg-gradient-to-l from-indigo-600 to-violet-700 text-white p-5">
    <p class="text-xs uppercase tracking-wider text-indigo-200">دورك: <?php echo e($roleLabel ?? $role); ?></p>
    <p class="font-black text-lg mt-1">ما لم يُسجَّل في CRM، لم يحدث</p>
    <p class="text-indigo-100 text-sm mt-1">أي تواصل أو بيع أو عمولة يجب أن يمر عبر النظام.</p>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">عملائي</p><p class="text-2xl font-black"><?php echo e($stats['my_leads']); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">مفتوحة</p><p class="text-2xl font-black text-sky-700"><?php echo e($stats['open']); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">ناجحة</p><p class="text-2xl font-black text-emerald-700"><?php echo e($stats['closed_won']); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">بانتظار الدفع</p><p class="text-2xl font-black text-amber-700"><?php echo e($stats['payment_pending']); ?></p></div>
  </div>

  <?php if(isset($stats['my_commissions'])): ?>
    <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">إجمالي عمولاتي:</span> <strong><?php echo e(number_format($stats['my_commissions'], 2)); ?> ج.م</strong></div>
  <?php endif; ?>
  <?php if(isset($stats['team_members'])): ?>
    <div class="rounded-xl border bg-white p-4 flex flex-wrap justify-between items-center gap-3">
      <div class="text-sm"><span class="text-gray-500">أعضاء الفريق:</span> <strong><?php echo e($stats['team_members']); ?></strong></div>
      <a href="<?php echo e(route('employee.crm.team.index')); ?>" class="text-sky-700 font-bold text-sm">تفاصيل أداء كل عضو ←</a>
    </div>
  <?php endif; ?>
  <?php if(isset($stats['team_revenue'])): ?>
    <div class="grid grid-cols-2 gap-3">
      <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">إيراد الفريق:</span> <strong><?php echo e(number_format($stats['team_revenue'], 2)); ?> ج.م</strong></div>
      <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">عمولات الفريق:</span> <strong><?php echo e(number_format($stats['team_commissions'], 2)); ?> ج.م</strong></div>
    </div>
  <?php endif; ?>
  <?php if(isset($stats['revenue_month'])): ?>
    <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">إيراد الشهر (كل الطلبات):</span> <strong><?php echo e(number_format($stats['revenue_month'], 2)); ?> ج.م</strong></div>
  <?php endif; ?>
  <?php if(isset($stats['total_leads'])): ?>
    <div class="grid grid-cols-2 gap-3">
      <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">كل العملاء:</span> <strong><?php echo e($stats['total_leads']); ?></strong></div>
      <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">طلبات معلقة (المنصة):</span> <strong><?php echo e($stats['total_orders_pending']); ?></strong></div>
    </div>
  <?php endif; ?>
  <?php if(isset($stats['pending_commissions'])): ?>
    <div class="grid grid-cols-2 gap-3">
      <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">عمولات معلقة:</span> <strong><?php echo e($stats['pending_commissions']); ?></strong></div>
      <div class="rounded-xl border bg-white p-4 text-sm"><span class="text-gray-500">عملاء بانتظار الدفع:</span> <strong><?php echo e($stats['pending_payments']); ?></strong></div>
    </div>
  <?php endif; ?>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">آخر العملاء المحتملين</div>
    <ul class="divide-y">
      <?php $__empty_1 = true; $__currentLoopData = $recentLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li><a href="<?php echo e(route('employee.crm.leads.show', $lead)); ?>" class="flex justify-between px-5 py-3 hover:bg-gray-50 text-sm">
          <span class="font-semibold"><?php echo e($lead->name); ?></span><span class="text-gray-500"><?php echo e($lead->status_label); ?></span>
        </a></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li class="px-5 py-8 text-center text-gray-500 text-sm">لا يوجد عملاء بعد</li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/dashboard.blade.php ENDPATH**/ ?>