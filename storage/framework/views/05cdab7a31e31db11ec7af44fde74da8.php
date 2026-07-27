

<?php $__env->startSection('title', 'صلاحيات '.$employee->name); ?>
<?php $__env->startSection('header', 'صلاحيات موظف المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 max-w-4xl">

  <div class="rounded-xl border bg-white p-4 text-sm">
    <p><strong><?php echo e($employee->name); ?></strong> — <?php echo e($employee->employeeJob?->name); ?></p>
    <p class="text-gray-500"><?php echo e($employee->email); ?></p>
  </div>

  <form method="POST" action="<?php echo e(route('admin.crm.sales-permissions.update', $employee)); ?>" class="rounded-2xl border bg-white p-6 space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-violet-200 bg-violet-50 cursor-pointer">
      <input type="checkbox" name="employee_permissions_custom" value="1"
             <?php if(old('employee_permissions_custom', $employee->usesCustomEmployeePermissions())): echo 'checked'; endif; ?>
             class="mt-1 w-4 h-4 text-violet-600 rounded">
      <span>
        <span class="font-bold text-violet-900 block">صلاحيات مخصصة لهذا الموظف</span>
        <span class="text-sm text-violet-800">عند التفعيل تُستخدم القائمة أدناه فقط (وليس صلاحيات الوظيفة العامة). أزل أي خانة ليختفي القسم من نظام الموظف بالكامل.</span>
      </span>
    </label>

    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupTitle => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div>
        <h3 class="font-bold text-gray-900 mb-2"><?php echo e($groupTitle); ?></h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 border rounded-xl p-4">
          <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="employee_permissions[]" value="<?php echo e($key); ?>"
                     <?php if(in_array($key, $selected, true)): echo 'checked'; endif; ?>
                     class="w-4 h-4 text-indigo-600 rounded border-gray-300">
              <span><?php echo e($label); ?></span>
            </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="flex gap-3 pt-4 border-t">
      <a href="<?php echo e(route('admin.crm.sales-permissions.index')); ?>" class="px-5 py-2.5 rounded-lg bg-gray-500 text-white font-bold text-sm">إلغاء</a>
      <button class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-bold text-sm">حفظ الصلاحيات</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\sales-permissions\edit.blade.php ENDPATH**/ ?>