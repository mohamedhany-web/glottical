

<?php $__env->startSection('title', 'عميل محتمل #'.$lead->id); ?>
<?php $__env->startSection('header', 'CRM — '.$lead->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if($errors->any()): ?><div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>

  <div class="rounded-2xl border bg-white p-6 space-y-3">
    <div class="flex justify-between gap-3"><h2 class="text-xl font-black"><?php echo e($lead->name); ?></h2><span class="text-sm font-bold bg-gray-100 px-2 py-1 rounded"><?php echo e($lead->status_label); ?></span></div>
    <p class="text-sm text-gray-600">مالك التسويق: <strong><?php echo e($lead->marketingOwner?->name ?? '—'); ?></strong> (لا يتغيّر)</p>
    <?php if($lead->assignedTo): ?><p class="text-sm">موظف المبيعات: <strong><?php echo e($lead->assignedTo->name); ?></strong></p><?php endif; ?>
    <?php if($lead->email || $lead->phone): ?><p class="text-sm text-gray-600"><?php echo e($lead->email); ?> <?php if($lead->phone): ?> — <?php echo e($lead->phone); ?><?php endif; ?></p><?php endif; ?>
    <?php if($lead->interestedCourse): ?><p class="text-sm">كورس الاهتمام: <strong><?php echo e($lead->interestedCourse->title); ?></strong></p><?php endif; ?>
    <?php if($lead->notes): ?><div class="text-sm whitespace-pre-wrap bg-gray-50 rounded-lg p-3"><?php echo e($lead->notes); ?></div><?php endif; ?>
  </div>

  <?php if($role === 'team_leader'): ?>
    <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-900 px-4 py-3 text-sm flex flex-wrap justify-between gap-2 items-center">
      <span>يمكنك متابعة الفريق وتعيين العملاء وإضافة ملاحظات.</span>
      <?php if(\App\Services\Crm\CrmAccessService::canViewTeamPerformance(auth()->user())): ?>
        <a href="<?php echo e(route('employee.crm.team.index')); ?>" class="font-bold text-sky-700 underline">عرض أداء الأعضاء</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if(!empty($canAssign) && $salesUsers->isNotEmpty() && !$lead->isClosed()): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.assign', $lead)); ?>" class="rounded-2xl border border-sky-200 bg-sky-50 p-6 space-y-3">
    <?php echo csrf_field(); ?>
    <h3 class="font-bold text-sky-900">تعيين لموظف مبيعات</h3>
    <select name="assigned_to" class="w-full rounded-lg border px-3 py-2" required>
      <option value="">اختر موظف المبيعات</option>
      <?php $__currentLoopData = $salesUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">تعيين العميل</button>
  </form>
  <?php endif; ?>

  <?php if(\App\Services\Crm\CrmAccessService::canUseMessages(auth()->user())): ?>
  <a href="<?php echo e(route('employee.crm.messages.lead', $lead)); ?>" class="inline-flex items-center gap-2 text-sm text-cyan-700 font-bold">محادثة الفريق حول هذا العميل</a>
  <?php endif; ?>

  <?php if($canEdit): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.update', $lead)); ?>" class="rounded-2xl border bg-white p-6 space-y-3">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <h3 class="font-bold">تعديل البيانات</h3>
    <input name="name" value="<?php echo e($lead->name); ?>" class="w-full rounded-lg border px-3 py-2" required>
    <input name="email" value="<?php echo e($lead->email); ?>" class="w-full rounded-lg border px-3 py-2" placeholder="البريد">
    <input name="phone" value="<?php echo e($lead->phone); ?>" class="w-full rounded-lg border px-3 py-2" placeholder="الهاتف">
    <select name="source" class="w-full rounded-lg border px-3 py-2">
      <?php $__currentLoopData = \App\Models\SalesLead::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if($lead->source===$k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">حفظ التعديلات</button>
  </form>
  <?php endif; ?>

  <?php if($role === 'sales' && !empty($nextStatuses) && !$lead->isClosed()): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.transition', $lead)); ?>" class="rounded-2xl border bg-white p-6 space-y-3">
    <?php echo csrf_field(); ?>
    <h3 class="font-bold">تحديث الحالة</h3>
    <p class="text-xs text-gray-500">حدّث الحالة بالترتيب بعد كل خطوة — لا يمكن تخطي المراحل.</p>
    <select name="status" class="w-full rounded-lg border px-3 py-2" required>
      <?php $__currentLoopData = $nextStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($st); ?>"><?php echo e(\App\Models\SalesLead::statusLabels()[$st]); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظة عن هذه الخطوة"></textarea>
    <button class="px-4 py-2 rounded-xl bg-violet-600 text-white font-bold text-sm">تحديث الحالة</button>
  </form>
  <?php endif; ?>

  <?php if($canSeePayment && $lead->status === \App\Models\SalesLead::STATUS_PAYMENT_PENDING): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.confirm-payment', $lead)); ?>" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6" onsubmit="return confirm('تأكيد استلام المبلغ لهذا العميل؟')">
    <?php echo csrf_field(); ?>
    <h3 class="font-bold text-emerald-900 mb-2">تأكيد الدفع</h3>
    <p class="text-sm text-emerald-800 mb-3">بعد التأكيد يُفعَّل الكورس وتُحسب العمولات تلقائياً.</p>
    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm">تأكيد الدفع</button>
  </form>
  <?php endif; ?>

  <?php if($canAddNotes): ?>
  <form method="POST" action="<?php echo e(route('employee.crm.leads.note', $lead)); ?>" class="rounded-2xl border bg-white p-6 space-y-3">
    <?php echo csrf_field(); ?>
    <h3 class="font-bold">إضافة ملاحظة</h3>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="دوّن ما دار في المكالمة أو الرسالة" required></textarea>
    <button class="px-4 py-2 rounded-xl bg-slate-700 text-white font-bold text-sm">إضافة ملاحظة</button>
  </form>
  <?php endif; ?>

  <?php if($lead->auditLogs->isNotEmpty()): ?>
  <div class="rounded-2xl border bg-white p-6">
    <h3 class="font-bold mb-3">سجل المتابعة</h3>
    <div class="space-y-2 max-h-64 overflow-y-auto text-xs">
      <?php $__currentLoopData = $lead->auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border-b pb-2">
          <span class="font-bold"><?php echo e($log->actionLabel()); ?></span>
          <span class="text-slate-500"> — <?php echo e($log->user?->name); ?> — <?php echo e($log->created_at?->format('Y-m-d H:i')); ?></span>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/leads/show.blade.php ENDPATH**/ ?>