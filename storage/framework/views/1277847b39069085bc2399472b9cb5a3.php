

<?php $__env->startSection('title', 'تواصل CRM'); ?>
<?php $__env->startSection('header', 'CRM — التواصل'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if($errors->any()): ?><div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>

  <?php if($canSend): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.messages.store')); ?>" enctype="multipart/form-data" class="rounded-2xl border bg-white p-5 space-y-3">
    <?php echo csrf_field(); ?>
    <h3 class="font-bold">رسالة جديدة</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <select name="recipient_id" class="rounded-lg border px-3 py-2 text-sm" id="msg-recipient">
        <option value="">رسالة مباشرة لـ...</option>
        <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <select name="crm_group_id" class="rounded-lg border px-3 py-2 text-sm" id="msg-group">
        <option value="">أو قناة فريق...</option>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>"><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <select name="sales_lead_id" class="rounded-lg border px-3 py-2 text-sm" id="msg-lead">
        <option value="">أو على عميل...</option>
        <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($l->id); ?>"><?php echo e($l->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <textarea name="body" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="اكتب رسالتك..." required></textarea>
    <div class="flex flex-wrap gap-3 items-center">
      <input type="file" name="attachment" class="text-sm">
      <button class="px-4 py-2 rounded-xl bg-cyan-600 text-white font-bold text-sm">إرسال</button>
    </div>
    <p class="text-xs text-gray-500">اختر قناة واحدة فقط: موظف مباشر، فريق، أو عميل محتمل.</p>
  </form>
  <?php endif; ?>

  <div class="rounded-2xl border bg-white divide-y max-h-[32rem] overflow-y-auto">
    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="p-4 text-sm <?php echo e($msg->recipient_id === auth()->id() && !$msg->read_at ? 'bg-cyan-50' : ''); ?>">
        <div class="flex justify-between gap-2 mb-1">
          <span class="font-bold"><?php echo e($msg->sender?->name); ?></span>
          <span class="text-xs text-gray-500"><?php echo e($msg->created_at?->format('Y-m-d H:i')); ?></span>
        </div>
        <?php if($msg->recipient): ?><p class="text-xs text-gray-500">إلى: <?php echo e($msg->recipient->name); ?></p><?php endif; ?>
        <?php if($msg->group): ?><p class="text-xs text-indigo-600">قناة الفريق: <?php echo e($msg->group->name); ?></p><?php endif; ?>
        <?php if($msg->lead): ?><p class="text-xs text-violet-600">عميل: <a href="<?php echo e(route('employee.crm.messages.lead', $msg->lead)); ?>" class="underline"><?php echo e($msg->lead->name); ?></a></p><?php endif; ?>
        <p class="mt-2 whitespace-pre-wrap"><?php echo e($msg->body); ?></p>
        <?php if($msg->attachment_path): ?>
          <a href="<?php echo e(route('employee.crm.messages.attachment', $msg->id)); ?>" class="text-xs text-indigo-600 font-bold mt-1 inline-block">مرفق: <?php echo e($msg->attachment_name); ?></a>
        <?php endif; ?>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="p-8 text-center text-gray-500">لا توجد رسائل بعد</p>
    <?php endif; ?>
  </div>
  <div><?php echo e($messages->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/messages/index.blade.php ENDPATH**/ ?>