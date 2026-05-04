

<?php $__env->startSection('title', 'لوحة الإشراف'); ?>
<?php $__env->startSection('header', 'لوحة الإشراف'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <p class="text-sm text-gray-600">متابعة مهام الفريق والمهام المتأخرة عن الموعد النهائي.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border-2 border-indigo-200/60 bg-gradient-to-br from-white to-indigo-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">موظفون نشطون</p>
            <p class="text-3xl font-black text-indigo-900 tabular-nums"><?php echo e(number_format($stats['employees_active'])); ?></p>
        </div>
        <div class="rounded-2xl border-2 border-blue-200/60 bg-gradient-to-br from-white to-blue-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">مهام مفتوحة</p>
            <p class="text-3xl font-black text-blue-900 tabular-nums"><?php echo e(number_format($stats['tasks_open'])); ?></p>
        </div>
        <div class="rounded-2xl border-2 border-red-200/60 bg-gradient-to-br from-white to-red-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">مهام متأخرة</p>
            <p class="text-3xl font-black text-red-800 tabular-nums"><?php echo e(number_format($stats['tasks_overdue'])); ?></p>
        </div>
        <div class="rounded-2xl border-2 border-emerald-200/60 bg-gradient-to-br from-white to-emerald-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">مكتملة آخر أسبوع</p>
            <p class="text-3xl font-black text-emerald-800 tabular-nums"><?php echo e(number_format($stats['tasks_done_week'])); ?></p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">مهام متأخرة (تحتاج متابعة)</h2>
            <span class="text-xs text-gray-500">حتى 25 مهمة</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">المهمة</th>
                        <th class="text-right px-4 py-3">الموظف</th>
                        <th class="text-right px-4 py-3">المكلف</th>
                        <th class="text-right px-4 py-3">الموعد</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $atRiskTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-red-50/40">
                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($task->title); ?></td>
                        <td class="px-4 py-3 text-gray-900"><?php echo e($task->employee?->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($task->assigner?->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-red-700 font-semibold whitespace-nowrap"><?php echo e($task->deadline?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3">
                            <?php if($task->status === 'pending'): ?>
                                <span class="text-xs font-bold text-amber-700">معلّقة</span>
                            <?php elseif($task->status === 'in_progress'): ?>
                                <span class="text-xs font-bold text-blue-700">قيد التنفيذ</span>
                            <?php else: ?>
                                <span class="text-xs text-gray-600"><?php echo e($task->status); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">لا توجد مهام متأخرة حالياً.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\supervision-desk\index.blade.php ENDPATH**/ ?>