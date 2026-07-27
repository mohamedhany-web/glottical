

<?php $__env->startSection('title', 'صلاحيات قسم المبيعات'); ?>
<?php $__env->startSection('header', 'صلاحيات موظفي المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">

  <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <div class="rounded-2xl bg-white border p-5">
    <h1 class="text-xl font-black text-gray-900">تحكم فردي بصلاحيات قسم المبيعات</h1>
    <p class="text-sm text-gray-600 mt-1">أضف أو أزل أي صلاحية لكل موظف — ما تُلغيه يختفي من السايدبار وصفحات CRM فوراً.</p>
  </div>

  <form method="GET" class="flex flex-wrap gap-2 items-end bg-white border rounded-xl p-4">
    <div>
      <label class="block text-xs font-bold text-gray-600 mb-1">بحث</label>
      <input name="search" value="<?php echo e(request('search')); ?>" class="rounded-lg border px-3 py-2 text-sm" placeholder="اسم / بريد / رمز">
    </div>
    <div>
      <label class="block text-xs font-bold text-gray-600 mb-1">الوظيفة</label>
      <select name="job_code" class="rounded-lg border px-3 py-2 text-sm">
        <option value="">الكل</option>
        <?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($job->code); ?>" <?php if(request('job_code')===$job->code): echo 'selected'; endif; ?>><?php echo e($job->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="block text-xs font-bold text-gray-600 mb-1">نوع الصلاحيات</label>
      <select name="custom" class="rounded-lg border px-3 py-2 text-sm">
        <option value="">الكل</option>
        <option value="1" <?php if(request('custom')==='1'): echo 'selected'; endif; ?>>مخصصة للموظف</option>
        <option value="0" <?php if(request('custom')==='0'): echo 'selected'; endif; ?>>من الوظيفة فقط</option>
      </select>
    </div>
    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">تصفية</button>
  </form>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-right">الموظف</th>
          <th class="px-4 py-3 text-right">الوظيفة</th>
          <th class="px-4 py-3 text-right">الصلاحيات</th>
          <th class="px-4 py-3 text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="px-4 py-3">
              <div class="font-semibold"><?php echo e($employee->name); ?></div>
              <div class="text-xs text-gray-500"><?php echo e($employee->email); ?></div>
            </td>
            <td class="px-4 py-3"><?php echo e($employee->employeeJob?->name ?? '—'); ?></td>
            <td class="px-4 py-3">
              <?php if($employee->usesCustomEmployeePermissions()): ?>
                <span class="text-xs font-bold bg-violet-100 text-violet-800 px-2 py-1 rounded">مخصصة</span>
                <span class="text-gray-600 text-xs mr-1"><?php echo e(count($employee->effectiveEmployeePermissions())); ?> صلاحية</span>
              <?php else: ?>
                <span class="text-xs font-bold bg-gray-100 text-gray-700 px-2 py-1 rounded">من الوظيفة</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3">
              <a href="<?php echo e(route('admin.crm.sales-permissions.edit', $employee)); ?>" class="text-indigo-600 font-bold text-xs">تعديل الصلاحيات</a>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">لا يوجد موظفون في قسم المبيعات</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4"><?php echo e($employees->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\sales-permissions\index.blade.php ENDPATH**/ ?>