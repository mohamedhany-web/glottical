

<?php $__env->startSection('title', 'Lead جديد'); ?>
<?php $__env->startSection('header', 'CRM — Lead جديد'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('employee.crm.leads.store')); ?>" class="max-w-xl space-y-4 rounded-2xl border bg-white p-6">
  <?php echo csrf_field(); ?>
  <input name="name" placeholder="الاسم *" class="w-full rounded-lg border px-3 py-2" required value="<?php echo e(old('name')); ?>">
  <input name="email" type="email" placeholder="البريد" class="w-full rounded-lg border px-3 py-2" value="<?php echo e(old('email')); ?>">
  <input name="phone" placeholder="الهاتف" class="w-full rounded-lg border px-3 py-2" value="<?php echo e(old('phone')); ?>">
  <select name="source" class="w-full rounded-lg border px-3 py-2" required>
    <?php $__currentLoopData = \App\Models\SalesLead::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
  <select name="interested_advanced_course_id" class="w-full rounded-lg border px-3 py-2">
    <option value="">كورس الاهتمام</option>
    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
  <textarea name="notes" rows="3" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظات"></textarea>
  <button class="px-5 py-2.5 rounded-xl bg-teal-600 text-white font-bold">حفظ Lead</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\employee\crm\leads\create.blade.php ENDPATH**/ ?>