<?php $__env->startSection('title', 'تحليلات المبيعات'); ?>
<?php $__env->startSection('header', 'تحليلات المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800/60 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-emerald-50 to-white dark:from-emerald-950/40 dark:to-slate-900 border-b border-emerald-100 dark:border-emerald-800/50 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-slate-100 flex items-center gap-3">
                    <span class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md"><i class="fas fa-chart-pie"></i></span>
                    لوحة المبيعات
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">إيرادات معتمدة، أداء المناديب، وأكثر الكورسات طلباً خلال الفترة المختارة.</p>
            </div>
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">من</label>
                    <input type="date" name="from" value="<?php echo e($from->format('Y-m-d')); ?>" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">إلى</label>
                    <input type="date" name="to" value="<?php echo e($to->format('Y-m-d')); ?>" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white px-4 py-2 text-sm font-bold">تحديث</button>
            </form>
        </div>
        <div class="p-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-4 bg-slate-50/80 dark:bg-slate-700/50">
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">طلبات (خلال الفترة)</p>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100 tabular-nums mt-1"><?php echo e(number_format($stats['orders_total'])); ?></p>
            </div>
            <div class="rounded-xl border border-amber-200 dark:border-amber-700/50 p-4 bg-amber-50/50 dark:bg-amber-950/35">
                <p class="text-xs font-semibold text-amber-800 dark:text-amber-200">معلّقة الآن</p>
                <p class="text-2xl font-black text-amber-900 dark:text-amber-100 tabular-nums mt-1"><?php echo e(number_format($stats['pending'])); ?></p>
            </div>
            <div class="rounded-xl border border-emerald-200 dark:border-emerald-700/50 p-4 bg-emerald-50/50 dark:bg-emerald-950/35">
                <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-200">إيراد معتمد (الفترة)</p>
                <p class="text-xl font-black text-emerald-900 dark:text-emerald-100 tabular-nums mt-1"><?php echo e(number_format($stats['revenue_period'], 2)); ?></p>
            </div>
            <div class="rounded-xl border border-blue-200 dark:border-blue-700/50 p-4 bg-blue-50/50 dark:bg-blue-950/35">
                <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">إيراد معتمد (الشهر)</p>
                <p class="text-xl font-black text-blue-900 dark:text-blue-100 tabular-nums mt-1"><?php echo e(number_format($stats['revenue_month'], 2)); ?></p>
            </div>
        </div>
        <?php if($stats['conversion'] !== null): ?>
            <div class="px-6 pb-6">
                <p class="text-sm text-slate-700 dark:text-slate-300">نسبة التحويل التقريبية (معتمد ÷ طلبات جديدة في الفترة): <strong class="text-emerald-700 dark:text-emerald-400"><?php echo e($stats['conversion']); ?>%</strong></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($unassignedPending > 0): ?>
        <div class="rounded-xl border border-amber-300 dark:border-amber-600/60 bg-amber-50 dark:bg-amber-950/40 px-4 py-3 text-sm text-amber-900 dark:text-amber-100">
            <i class="fas fa-exclamation-triangle ml-2"></i>
            يوجد <strong><?php echo e($unassignedPending); ?></strong> طلب معلّق بدون مندوب مبيعات.
            <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending', 'sales_owner_id' => 'unassigned'])); ?>" class="font-bold underline mr-2 text-amber-950 dark:text-amber-200 hover:text-amber-700 dark:hover:text-amber-50">عرضها</a>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg p-6">
            <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-4">طلبات جديدة يومياً</h2>
            <div class="flex items-end gap-1 h-40">
                <?php
                    $maxD = max($daily->max() ?: 0, 1);
                ?>
                <?php $__currentLoopData = $daily; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex-1 min-w-0 flex flex-col items-center justify-end group">
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mb-1 tabular-nums"><?php echo e($count); ?></span>
                        <div class="w-full max-w-[24px] mx-auto rounded-t bg-emerald-500/80 dark:bg-emerald-500 group-hover:bg-emerald-600 dark:group-hover:bg-emerald-400 transition-colors" style="height: <?php echo e(max(4, ($count / $maxD) * 100)); ?>%"></div>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-1 truncate w-full text-center" title="<?php echo e($day); ?>"><?php echo e(\Illuminate\Support\Str::afterLast($day, '-')); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($daily->isEmpty()): ?>
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-8">لا بيانات في هذه الفترة.</p>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/60">
                <h2 class="text-lg font-black text-slate-900 dark:text-slate-100">أكثر الكورسات إيراداً (معتمد)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 font-semibold">
                        <tr>
                            <th class="text-right px-4 py-3">الكورس</th>
                            <th class="text-right px-4 py-3">طلبات</th>
                            <th class="text-right px-4 py-3">إيراد معتمد</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                        <?php $__empty_1 = true; $__currentLoopData = $topCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100"><?php echo e($row->course?->title ?? 'كورس #'.$row->advanced_course_id); ?></td>
                                <td class="px-4 py-3 tabular-nums text-slate-700 dark:text-slate-300"><?php echo e(number_format($row->order_count)); ?></td>
                                <td class="px-4 py-3 font-bold tabular-nums text-emerald-800 dark:text-emerald-400"><?php echo e(number_format((float) $row->revenue, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">لا بيانات.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/60 flex flex-wrap justify-between gap-2">
            <h2 class="text-lg font-black text-slate-900 dark:text-slate-100">أداء مناديب المبيعات</h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">وظيفة «سيلز» فقط</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">المندوب</th>
                        <th class="text-right px-4 py-3">طلبات معلّقة مسندة</th>
                        <th class="text-right px-4 py-3">صفقات معتمدة (الفترة)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    <?php $__empty_1 = true; $__currentLoopData = $repStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100"><?php echo e($rep->name); ?></td>
                            <td class="px-4 py-3 tabular-nums text-slate-700 dark:text-slate-300"><?php echo e(number_format($rep->owned_pending)); ?></td>
                            <td class="px-4 py-3 font-bold tabular-nums text-emerald-800 dark:text-emerald-400"><?php echo e(number_format($rep->owned_won_period)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">لا يوجد موظفون بوظيفة مبيعات أو لا بيانات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-600 dark:hover:bg-slate-500 text-white px-5 py-2.5 text-sm font-bold">
            <i class="fas fa-shopping-bag"></i> إدارة الطلبات
        </a>
        <a href="<?php echo e(route('admin.coupons.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60">
            <i class="fas fa-ticket-alt"></i> الكوبونات
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\sales\index.blade.php ENDPATH**/ ?>