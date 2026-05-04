

<?php $__env->startSection('title', 'الإشراف الأكاديمي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الإشراف الأكاديمي</h1>
            <p class="text-sm text-gray-600 mt-1">مشرفون أكاديميون والطلاب المعيّنون لكل مشرف.</p>
        </div>
    </div>

    <?php if(!$job): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-900 text-sm px-4 py-3">
            وظيفة «مشرف أكاديمي» غير موجودة في قاعدة البيانات. شغّل <code class="bg-white/80 px-1 rounded">php artisan db:seed --class=EmployeeJobSeeder</code>
        </div>
    <?php endif; ?>

    <form method="get" class="flex flex-wrap gap-2 items-center">
        <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم، البريد، الجوال، رقم الموظف…"
               class="rounded-xl border border-gray-300 px-4 py-2 text-sm min-w-[240px] focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
        <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-500">تصفية</button>
    </form>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">المشرف</th>
                        <th class="text-right px-4 py-3">الوظيفة</th>
                        <th class="text-right px-4 py-3">عدد الطلاب</th>
                        <th class="text-right px-4 py-3 w-32"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900"><?php echo e($sup->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($sup->email); ?></p>
                            </td>
                            <td class="px-4 py-3"><?php echo e($sup->employeeJob?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 font-bold tabular-nums"><?php echo e($sup->supervised_students_as_academic_count); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.academic-supervision.supervisors.show', $sup)); ?>" class="text-teal-700 font-semibold hover:underline">إدارة</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-500">لا يوجد موظفون بوظيفة مشرف أكاديمي. عيّن الوظيفة من «الموظفين» أو «الوظائف».</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($supervisors->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\academic-supervision\index.blade.php ENDPATH**/ ?>