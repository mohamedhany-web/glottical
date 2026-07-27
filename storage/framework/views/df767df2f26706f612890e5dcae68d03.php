

<?php $__env->startSection('title', 'محادثة العميل'); ?>
<?php $__env->startSection('header', 'CRM — '.$lead->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 max-w-2xl">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <a href="<?php echo e(route('employee.crm.leads.show', $lead)); ?>" class="text-sm text-indigo-600 font-bold">← العودة لملف العميل</a>

  <div class="rounded-2xl border bg-white divide-y max-h-96 overflow-y-auto">
    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="p-4 text-sm">
        <div class="flex justify-between"><span class="font-bold"><?php echo e($msg->sender?->name); ?></span><span class="text-xs text-gray-500"><?php echo e($msg->created_at?->format('Y-m-d H:i')); ?></span></div>
        <p class="mt-2 whitespace-pre-wrap"><?php echo e($msg->body); ?></p>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="p-8 text-center text-gray-500">لا توجد رسائل على هذا العميل</p>
    <?php endif; ?>
  </div>

  <?php if($canSend): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.messages.store')); ?>" class="rounded-2xl border bg-white p-4 space-y-3">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="sales_lead_id" value="<?php echo e($lead->id); ?>">
    <textarea name="body" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm" required placeholder="اكتب رسالة للفريق حول هذا العميل"></textarea>
    <button class="px-4 py-2 rounded-xl bg-cyan-600 text-white font-bold text-sm">إرسال</button>
  </form>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\employee\crm\messages\lead-thread.blade.php ENDPATH**/ ?>