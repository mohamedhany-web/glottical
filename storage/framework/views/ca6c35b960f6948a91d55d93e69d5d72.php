

<?php $__env->startSection('title', 'الوظائف الشاغرة'); ?>
<?php $__env->startSection('header', 'الوظائف الشاغرة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap justify-between gap-3">
        <a href="<?php echo e(route('employee.hr.recruitment.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> التوظيف</a>
        <a href="<?php echo e(route('employee.hr.recruitment.openings.create')); ?>" class="px-4 py-2 rounded-lg bg-violet-600 text-white text-sm font-bold">إضافة وظيفة</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="rounded-lg border border-gray-300 px-3 py-2 text-sm w-56">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = \App\Models\HrJobOpening::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php echo e(request('status') === $k ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm font-bold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 font-semibold text-gray-600">
                    <tr>
                        <th class="text-right px-4 py-3">المسمى</th>
                        <th class="text-right px-4 py-3">القسم</th>
                        <th class="text-right px-4 py-3">النوع</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">طلبات</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $openings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($o->title); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($o->department ?: '—'); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($o->employment_type_label); ?></td>
                        <td class="px-4 py-3"><span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100"><?php echo e($o->status_label); ?></span></td>
                        <td class="px-4 py-3 tabular-nums"><?php echo e($o->applications_count); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('employee.hr.recruitment.openings.show', $o)); ?>" class="text-violet-700 font-bold hover:underline">عرض</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد وظائف.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($openings->hasPages()): ?><div class="px-4 py-3 border-t"><?php echo e($openings->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\openings\index.blade.php ENDPATH**/ ?>