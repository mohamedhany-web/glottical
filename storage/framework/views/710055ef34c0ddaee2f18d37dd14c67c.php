

<?php $__env->startSection('title', 'دليل الموظفين'); ?>
<?php $__env->startSection('header', 'دليل الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.hr-desk.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> لوحة الموارد البشرية
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، بريد، هاتف، رمز…"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الوظيفة</label>
                <select name="job_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm min-w-[160px]">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($j->id); ?>" <?php echo e((string) request('job_id') === (string) $j->id ? 'selected' : ''); ?>><?php echo e($j->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 pb-2">
                <input type="checkbox" name="all" value="1" <?php echo e(request('all') ? 'checked' : ''); ?> class="rounded border-gray-300 text-indigo-600">
                عرض الموقوفين أيضاً
            </label>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold">تصفية</button>
            <a href="<?php echo e(route('employee.hr.employees.index')); ?>" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 text-sm font-semibold">إعادة ضبط</a>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('employee.hr.employees.show', $emp)); ?>" class="block rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-indigo-300 hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-gray-900"><?php echo e($emp->name); ?></h3>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e($emp->employeeJob?->name ?? '—'); ?></p>
                    </div>
                    <?php if($emp->is_active): ?>
                        <span class="shrink-0 text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">نشط</span>
                    <?php else: ?>
                        <span class="shrink-0 text-xs font-bold px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">موقوف</span>
                    <?php endif; ?>
                </div>
                <dl class="mt-3 space-y-1 text-xs text-gray-600">
                    <?php if($emp->employee_code): ?><div><span class="text-gray-400">رمز:</span> <?php echo e($emp->employee_code); ?></div><?php endif; ?>
                    <div class="truncate"><?php echo e($emp->email); ?></div>
                    <?php if($emp->phone): ?><div dir="ltr" class="text-right"><?php echo e($emp->phone); ?></div><?php endif; ?>
                </dl>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-16 text-gray-500 bg-white rounded-xl border border-gray-200">لا يوجد موظفون مطابقون للتصفية.</div>
        <?php endif; ?>
    </div>

    <?php if($employees->hasPages()): ?>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3"><?php echo e($employees->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\employees\index.blade.php ENDPATH**/ ?>