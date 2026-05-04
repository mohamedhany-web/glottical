<?php $__env->startSection('title', 'طلبات الاستشارة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-comments text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-black leading-tight truncate">طلبات الاستشارة</h1>
                        <p class="text-sm text-white/90 mt-0.5">تُدار الجدولة والدفع من الإدارة؛ ستصلك إشعارات عند التفعيل</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="<?php echo e(route('instructor.courses.index')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 border border-white/20 text-white font-semibold transition-colors">
                    <i class="fas fa-book"></i>
                    <span><?php echo e(__('instructor.courses')); ?></span>
                </a>
                <?php if(\Illuminate\Support\Facades\Route::has('instructor.calendar')): ?>
                    <a href="<?php echo e(route('instructor.calendar')); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-indigo-700 hover:bg-white/90 border border-white/20 font-extrabold transition-colors">
                        <i class="fas fa-calendar-alt"></i>
                        <span>تقويم الاستشارات</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-xs text-slate-600 dark:text-slate-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">المبلغ</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">الموعد</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white"><?php echo e($r->student->name ?? '—'); ?></td>
                            <td class="px-4 py-3"><?php echo e(number_format($r->price_amount, 2)); ?> ج.م</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs"><?php echo e($r->statusLabel()); ?></span></td>
                            <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($r->scheduled_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3"><a href="<?php echo e(route('instructor.consultations.show', $r)); ?>" class="text-sky-600 font-semibold hover:underline">تفاصيل</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">لا توجد طلبات</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700"><?php echo e($requests->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\consultations\index.blade.php ENDPATH**/ ?>