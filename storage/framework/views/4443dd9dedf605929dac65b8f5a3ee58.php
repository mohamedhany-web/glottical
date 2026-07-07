

<?php $__env->startSection('title', 'رفع تقرير CRM'); ?>
<?php $__env->startSection('header', 'CRM — رفع تقرير'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 max-w-2xl">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <form method="POST" action="<?php echo e(route('employee.crm.reports.store')); ?>" enctype="multipart/form-data" class="rounded-2xl border bg-white p-6 space-y-4">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="type" value="<?php echo e($type); ?>">

    <div>
      <label class="block text-sm font-bold mb-1">نوع التقرير</label>
      <p class="text-sm text-gray-600"><?php echo e($typeLabels[$type] ?? $type); ?></p>
    </div>

    <div>
      <label class="block text-sm font-bold mb-1">عنوان التقرير *</label>
      <input name="title" value="<?php echo e(old('title')); ?>" class="w-full rounded-lg border px-3 py-2" required placeholder="مثال: تقرير أسبوع 24 — فريق التسويق">
      <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <?php if(in_array($type, ['weekly', 'monthly'], true)): ?>
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-bold mb-1">من تاريخ *</label>
        <input type="date" name="period_start" value="<?php echo e(old('period_start', $period['start'])); ?>" class="w-full rounded-lg border px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm font-bold mb-1">إلى تاريخ *</label>
        <input type="date" name="period_end" value="<?php echo e(old('period_end', $period['end'])); ?>" class="w-full rounded-lg border px-3 py-2" required>
      </div>
    </div>
    <?php endif; ?>

    <?php if($groups->isNotEmpty()): ?>
    <div>
      <label class="block text-sm font-bold mb-1">المجموعة (اختياري)</label>
      <select name="crm_group_id" class="w-full rounded-lg border px-3 py-2">
        <option value="">—</option>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>" <?php if(old('crm_group_id')==$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <?php endif; ?>

    <div>
      <label class="block text-sm font-bold mb-1">ملخص التقرير</label>
      <textarea name="summary" rows="4" class="w-full rounded-lg border px-3 py-2" placeholder="أبرز الإنجازات والتحديات"><?php echo e(old('summary')); ?></textarea>
    </div>

    <div>
      <label class="block text-sm font-bold mb-1">ملف التقرير (PDF / Word / Excel)</label>
      <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="w-full text-sm">
    </div>

    <button class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm">إرسال للإدارة</button>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/reports/create.blade.php ENDPATH**/ ?>