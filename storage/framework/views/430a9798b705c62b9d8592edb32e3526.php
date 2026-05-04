

<?php $__env->startSection('title', 'المرشحون'); ?>
<?php $__env->startSection('header', 'مرشحو التوظيف'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap justify-between gap-3">
        <a href="<?php echo e(route('employee.hr.recruitment.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> التوظيف</a>
        <a href="<?php echo e(route('employee.hr.recruitment.candidates.create')); ?>" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">مرشح جديد</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو البريد…" class="rounded-lg border border-gray-300 px-3 py-2 text-sm flex-1 min-w-[200px]">
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-700 text-white text-sm font-bold">بحث</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 font-semibold text-gray-600">
                <tr>
                    <th class="text-right px-4 py-3">الاسم</th>
                    <th class="text-right px-4 py-3">البريد</th>
                    <th class="text-right px-4 py-3">المصدر</th>
                    <th class="text-right px-4 py-3">طلبات</th>
                    <th class="text-right px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium"><?php echo e($c->full_name); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo e($c->email); ?></td>
                    <td class="px-4 py-3"><?php echo e($c->source_label); ?></td>
                    <td class="px-4 py-3 tabular-nums"><?php echo e($c->applications_count); ?></td>
                    <td class="px-4 py-3"><a href="<?php echo e(route('employee.hr.recruitment.candidates.show', $c)); ?>" class="text-violet-700 font-bold">عرض</a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">لا مرشحين.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($candidates->hasPages()): ?><div class="px-4 py-3 border-t"><?php echo e($candidates->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\candidates\index.blade.php ENDPATH**/ ?>