

<?php $__env->startSection('title', 'Lead #'.$lead->id); ?>
<?php $__env->startSection('header', 'CRM — '.$lead->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if($errors->any()): ?><div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>

  <a href="<?php echo e(route('employee.crm.leads.index')); ?>" class="text-sm text-sky-600 font-bold">← القائمة</a>

  <div class="rounded-2xl border bg-white p-6 space-y-3">
    <div class="flex justify-between"><h2 class="text-xl font-black"><?php echo e($lead->name); ?></h2><span class="text-sm font-bold bg-gray-100 px-2 py-1 rounded"><?php echo e($lead->status_label); ?></span></div>
    <p class="text-sm text-gray-600">مالك التسويق: <strong><?php echo e($lead->marketingOwner?->name); ?></strong> (لا يتغير)</p>
    <?php if($lead->assignedTo): ?><p class="text-sm">سيلز: <strong><?php echo e($lead->assignedTo->name); ?></strong></p><?php endif; ?>
    <?php if($lead->notes): ?><div class="text-sm whitespace-pre-wrap bg-gray-50 rounded-lg p-3"><?php echo e($lead->notes); ?></div><?php endif; ?>
  </div>

  <?php if($canEdit): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.update', $lead)); ?>" class="rounded-2xl border bg-white p-6 space-y-3">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <input name="name" value="<?php echo e($lead->name); ?>" class="w-full rounded-lg border px-3 py-2" required>
    <input name="email" value="<?php echo e($lead->email); ?>" class="w-full rounded-lg border px-3 py-2">
    <input name="phone" value="<?php echo e($lead->phone); ?>" class="w-full rounded-lg border px-3 py-2">
    <select name="source" class="w-full rounded-lg border px-3 py-2">
      <?php $__currentLoopData = \App\Models\SalesLead::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if($lead->source===$k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">حفظ</button>
  </form>
  <?php endif; ?>

  <?php if(!empty($nextStatuses) && !$lead->isClosed()): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.transition', $lead)); ?>" class="rounded-2xl border bg-white p-6 space-y-3">
    <?php echo csrf_field(); ?>
    <h3 class="font-bold">تحديث الحالة</h3>
    <select name="status" class="w-full rounded-lg border px-3 py-2" required>
      <?php $__currentLoopData = $nextStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($st); ?>"><?php echo e(\App\Models\SalesLead::statusLabels()[$st]); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظة"></textarea>
    <button class="px-4 py-2 rounded-xl bg-violet-600 text-white font-bold text-sm">تحديث</button>
  </form>
  <?php endif; ?>

  <?php if($canSeePayment && $lead->status === \App\Models\SalesLead::STATUS_PAYMENT_PENDING): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.confirm-payment', $lead)); ?>">
    <?php echo csrf_field(); ?>
    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm">تأكيد الدفع (Finance)</button>
  </form>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('employee.crm.leads.note', $lead)); ?>" class="rounded-2xl border bg-white p-6 space-y-3">
    <?php echo csrf_field(); ?>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="إضافة ملاحظة" required></textarea>
    <button class="px-4 py-2 rounded-xl bg-slate-700 text-white font-bold text-sm">إضافة ملاحظة</button>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\employee\crm\leads\show.blade.php ENDPATH**/ ?>