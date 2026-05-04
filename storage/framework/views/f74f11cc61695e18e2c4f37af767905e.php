

<?php $__env->startSection('title', 'طلبات الاستشارة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium"><?php echo e(__('auth.dashboard')); ?></a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <span class="text-gray-900 dark:text-gray-200 font-semibold">طلبات الاستشارة</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">طلبات الاستشارة</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">الدفع على حسابات المنصة، مراجعة الإدارة، ثم الموعد</p>
        </div>
        <a href="<?php echo e(route('public.instructors.index')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold shadow-sm">تصفح المدربين</a>
    </div>

    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-600 dark:text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">المدرب</th>
                        <th class="px-4 py-3 text-right">المبلغ</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">الموعد</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white"><?php echo e($r->instructor->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?php echo e(number_format($r->price_amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-xs font-medium"><?php echo e($r->statusLabel()); ?></span></td>
                            <td class="px-4 py-3 text-xs text-gray-500"><?php echo e($r->scheduled_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3"><a href="<?php echo e(route('consultations.show', $r)); ?>" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline">تفاصيل</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات بعد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700"><?php echo e($requests->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\consultations\index.blade.php ENDPATH**/ ?>