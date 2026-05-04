

<?php $__env->startSection('title', 'لوحة المحاسب'); ?>
<?php $__env->startSection('header', 'لوحة المحاسب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <p class="text-sm text-gray-600">نظرة عامة على الطلبات المالية والاتفاقيات ودفعات الرواتب. لاعتماد الطلبات أو الصرف استخدم لوحة الإدارة إن كانت لديك صلاحية.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border-2 border-amber-200/60 bg-gradient-to-br from-white to-amber-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">طلبات دفع معلّقة</p>
            <p class="text-3xl font-black text-amber-800 tabular-nums"><?php echo e(number_format($stats['orders_pending'])); ?></p>
        </div>
        <div class="rounded-2xl border-2 border-emerald-200/60 bg-gradient-to-br from-white to-emerald-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">اتفاقيات نشطة</p>
            <p class="text-3xl font-black text-emerald-800 tabular-nums"><?php echo e(number_format($stats['agreements_active'])); ?></p>
        </div>
        <div class="rounded-2xl border-2 border-sky-200/60 bg-gradient-to-br from-white to-sky-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">رواتب بانتظار الصرف</p>
            <p class="text-3xl font-black text-sky-800 tabular-nums"><?php echo e(number_format($stats['salary_payments_pending'])); ?></p>
        </div>
        <div class="rounded-2xl border-2 border-indigo-200/60 bg-gradient-to-br from-white to-indigo-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">دفعات مصروفة هذا الشهر</p>
            <p class="text-3xl font-black text-indigo-800 tabular-nums"><?php echo e(number_format($stats['salary_payments_paid_month'])); ?></p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">آخر طلبات الدفع المعلّقة</h2>
            <span class="text-xs text-gray-500">حتى 10 طلبات</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">المستخدم</th>
                        <th class="text-right px-4 py-3">الكورس</th>
                        <th class="text-right px-4 py-3">المبلغ</th>
                        <th class="text-right px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentPendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900"><?php echo e($order->user?->name ?? '—'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($order->user?->email); ?></div>
                        </td>
                        <td class="px-4 py-3 text-gray-800"><?php echo e($order->course?->title ?? '—'); ?></td>
                        <td class="px-4 py-3 font-semibold tabular-nums"><?php echo e(number_format((float) $order->amount, 2)); ?></td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap"><?php echo e($order->created_at?->format('Y-m-d H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">لا توجد طلبات معلّقة حالياً.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\accountant-desk\index.blade.php ENDPATH**/ ?>