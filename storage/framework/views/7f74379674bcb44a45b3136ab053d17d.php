<?php $__env->startSection('title', 'استهلاك المستخدمين - رقابة شاملة'); ?>
<?php $__env->startSection('header', 'استهلاك المستخدمين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">رقابة الاستهلاك الشاملة</h1>
                <p class="text-sm text-slate-600 mt-1">مؤشرات قوية عن استهلاك المشتركين للخدمات المدفوعة.</p>
            </div>
            <form method="GET" action="<?php echo e(route('admin.students-control.consumption')); ?>" class="inline-flex items-center gap-2">
                <span class="text-xs text-slate-500">النطاق الزمني:</span>
                <select name="days" class="px-3 py-2 rounded-lg border border-slate-200 text-sm" onchange="this.form.submit()">
                    <option value="7" <?php echo e((int)$windowDays === 7 ? 'selected' : ''); ?>>آخر 7 أيام</option>
                    <option value="30" <?php echo e((int)$windowDays === 30 ? 'selected' : ''); ?>>آخر 30 يوم</option>
                    <option value="90" <?php echo e((int)$windowDays === 90 ? 'selected' : ''); ?>>آخر 90 يوم</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">الاشتراكات النشطة</p>
            <p class="text-3xl font-bold text-slate-900"><?php echo e(number_format($stats['active_subscriptions'])); ?></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">الطلاب النشطون المدفوعون</p>
            <p class="text-3xl font-bold text-slate-900"><?php echo e(number_format($stats['active_students'])); ?></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">متوسط المزايا لكل اشتراك</p>
            <p class="text-3xl font-bold text-slate-900"><?php echo e(number_format($stats['avg_features_per_subscription'], 2)); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h2 class="font-bold text-slate-800">انتشار المزايا المدفوعة</h2>
            </div>
            <div class="p-4 space-y-2 max-h-[520px] overflow-y-auto">
                <?php $__currentLoopData = $featureUsage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('admin.students-control.paid-features.show', $feature['key'])); ?>" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/40 transition-colors">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($feature['label']); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($feature['key']); ?></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700"><?php echo e(number_format($feature['count'])); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h2 class="font-bold text-slate-800">أعلى المستخدمين استهلاكاً</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-xs text-slate-600 uppercase">
                            <th class="px-4 py-3 text-right">المستخدم</th>
                            <th class="px-4 py-3 text-right">كورسات</th>
                            <th class="px-4 py-3 text-right">امتحانات</th>
                            <th class="px-4 py-3 text-right">طلبات</th>
                            <th class="px-4 py-3 text-right">نشاط</th>
                            <th class="px-4 py-3 text-right">مجموع</th>
                            <th class="px-4 py-3 text-right">رقابة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $topUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($row['user']->name); ?></td>
                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo e($row['enrollments']); ?></td>
                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo e($row['exam_attempts']); ?></td>
                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo e($row['orders']); ?></td>
                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo e($row['activities']); ?></td>
                                <td class="px-4 py-3 text-sm font-bold text-slate-900"><?php echo e($row['score']); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo e(route('admin.users.show', $row['user']->id)); ?>" class="text-sky-600 hover:underline">الحساب</a>
                                        <?php if($row['subscription']): ?>
                                            <a href="<?php echo e(route('admin.subscriptions.consumption', $row['subscription'])); ?>" class="text-emerald-600 hover:underline">تفصيلي</a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.students-control.consumption.user', ['user' => $row['user']->id, 'days' => $windowDays])); ?>" class="text-violet-600 hover:underline">Drill-down</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">لا توجد بيانات استهلاك كافية حالياً.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="font-bold text-slate-800">استهلاك المزايا المفعّلة (فعلي)</h2>
            <p class="text-xs text-slate-500 mt-1">يعرض من لديه الميزة مفعلة مقابل من استخدمها فعلياً وعدد أحداث الاستخدام.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs text-slate-600 uppercase">
                        <th class="px-4 py-3 text-right">الميزة</th>
                        <th class="px-4 py-3 text-right">مفعلة لدى</th>
                        <th class="px-4 py-3 text-right">استخدمها</th>
                        <th class="px-4 py-3 text-right">نسبة الاستخدام</th>
                        <th class="px-4 py-3 text-right">أحداث الاستخدام</th>
                        <th class="px-4 py-3 text-right">تفصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $featureConsumption; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($row['label']); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(number_format($row['enabled_users'])); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(number_format($row['used_users'])); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($row['usage_rate'] >= 60 ? 'bg-emerald-100 text-emerald-700' : ($row['usage_rate'] >= 30 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700')); ?>">
                                    <?php echo e(number_format($row['usage_rate'], 1)); ?>%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e(number_format($row['events_count'])); ?></td>
                            <td class="px-4 py-3 text-sm">
                                <a href="<?php echo e(route('admin.students-control.paid-features.show', $row['feature_key'])); ?>" class="text-sky-600 hover:underline">
                                    إدارة المستخدمين
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">لا تتوفر بيانات استهلاك للمزايا حالياً.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\student-control\consumption.blade.php ENDPATH**/ ?>