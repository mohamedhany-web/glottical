

<?php $__env->startSection('title', 'Glottical CRM'); ?>
<?php $__env->startSection('header', 'Glottical CRM'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="rounded-2xl bg-gradient-to-l from-indigo-600 to-violet-700 text-white p-5">
    <p class="text-xs uppercase tracking-wider text-indigo-200">دورك: <?php echo e($role); ?></p>
    <p class="font-black text-lg mt-1">IF IT IS NOT IN CRM, IT DID NOT HAPPEN</p>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">Leads</p><p class="text-2xl font-black"><?php echo e($stats['my_leads']); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">مفتوحة</p><p class="text-2xl font-black text-sky-700"><?php echo e($stats['open']); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">ناجحة</p><p class="text-2xl font-black text-emerald-700"><?php echo e($stats['closed_won']); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">بانتظار الدفع</p><p class="text-2xl font-black text-amber-700"><?php echo e($stats['payment_pending']); ?></p></div>
  </div>

  <div class="flex flex-wrap gap-2">
    <a href="<?php echo e(route('employee.crm.leads.index')); ?>" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">Leads</a>
    <?php if($role === 'marketing'): ?>
      <a href="<?php echo e(route('employee.crm.leads.create')); ?>" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-bold">Lead جديد</a>
    <?php endif; ?>
    <a href="<?php echo e(route('employee.crm.commissions')); ?>" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold">عمولاتي</a>
  </div>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">آخر Leads</div>
    <ul class="divide-y">
      <?php $__currentLoopData = $recentLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><a href="<?php echo e(route('employee.crm.leads.show', $lead)); ?>" class="flex justify-between px-5 py-3 hover:bg-gray-50 text-sm">
          <span class="font-semibold"><?php echo e($lead->name); ?></span><span class="text-gray-500"><?php echo e($lead->status_label); ?></span>
        </a></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\employee\crm\dashboard.blade.php ENDPATH**/ ?>