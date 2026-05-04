<?php $__env->startSection('title', 'الاستهلاك التفصيلي للطالب'); ?>
<?php $__env->startSection('header', 'الاستهلاك التفصيلي للطالب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900"><?php echo e($user->name); ?></h1>
                <p class="text-sm text-slate-600 mt-1">مراقبة دقيقة لاستهلاك باقة الطالب والاجتماعات.</p>
            </div>
            <form method="GET" action="<?php echo e(route('admin.students-control.consumption.user', $user->id)); ?>" class="inline-flex items-center gap-2">
                <span class="text-xs text-slate-500">النطاق الزمني:</span>
                <select name="days" class="px-3 py-2 rounded-lg border border-slate-200 text-sm" onchange="this.form.submit()">
                    <option value="7" <?php echo e((int)$windowDays === 7 ? 'selected' : ''); ?>>آخر 7 أيام</option>
                    <option value="30" <?php echo e((int)$windowDays === 30 ? 'selected' : ''); ?>>آخر 30 يوم</option>
                    <option value="90" <?php echo e((int)$windowDays === 90 ? 'selected' : ''); ?>>آخر 90 يوم</option>
                </select>
                <a href="<?php echo e(route('admin.students-control.consumption', ['days' => $windowDays])); ?>" class="px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50">رجوع</a>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">حد الاجتماعات الشهري</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($limits['classroom_meetings_per_month'])); ?></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">المستخدم هذا الشهر</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($monthlyMeetingsUsed)); ?></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">الحد الأقصى لطلاب الميتينج</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($limits['classroom_max_participants'])); ?></p>
        </div>
        <div class="rounded-2xl border p-5 shadow-sm <?php echo e($alertLevel === 'danger' ? 'bg-rose-50 border-rose-200' : ($alertLevel === 'warning' ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200')); ?>">
            <p class="text-xs mb-1 <?php echo e($alertLevel === 'danger' ? 'text-rose-600' : ($alertLevel === 'warning' ? 'text-amber-600' : 'text-emerald-600')); ?>">تنبيه الاستهلاك الشهري</p>
            <p class="text-2xl font-bold <?php echo e($alertLevel === 'danger' ? 'text-rose-700' : ($alertLevel === 'warning' ? 'text-amber-700' : 'text-emerald-700')); ?>"><?php echo e(number_format($usagePercent, 1)); ?>%</p>
            <p class="text-xs mt-1 <?php echo e($alertLevel === 'danger' ? 'text-rose-600' : ($alertLevel === 'warning' ? 'text-amber-600' : 'text-emerald-600')); ?>">
                <?php echo e($alertLevel === 'danger' ? 'تجاوز الحد 100%' : ($alertLevel === 'warning' ? 'اقترب من الحد (80%+)' : 'ضمن المعدل الطبيعي')); ?>

            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">عدد الاجتماعات (النطاق الحالي)</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($totals['meetings'])); ?></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">أعلى ذروة مشاركين</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($totals['participants_peak'])); ?></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">إجمالي المشاركات</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($totals['participants_total'])); ?></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">إجمالي مدة الاجتماعات (دقائق)</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($totals['duration_minutes'])); ?></p>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="font-bold text-slate-800">الاجتماعات في النطاق الزمني</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs text-slate-600 uppercase">
                        <th class="px-4 py-3 text-right">الكود</th>
                        <th class="px-4 py-3 text-right">العنوان</th>
                        <th class="px-4 py-3 text-right">بدأ</th>
                        <th class="px-4 py-3 text-right">انتهى</th>
                        <th class="px-4 py-3 text-right">المدة</th>
                        <th class="px-4 py-3 text-right">الذروة</th>
                        <th class="px-4 py-3 text-right">إجمالي المشاركين</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-sm font-mono text-slate-800"><?php echo e($m['code']); ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($m['title']); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(optional($m['started_at'])->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(optional($m['ended_at'])->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e($m['duration_minutes'] !== null ? number_format($m['duration_minutes']) . ' دقيقة' : '—'); ?></td>
                            <td class="px-4 py-3 text-sm font-bold text-slate-900"><?php echo e(number_format($m['participants_peak'])); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(number_format($m['participants_total'])); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">لا توجد اجتماعات في هذا النطاق.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="font-bold text-slate-800">Timeline المشاركين (Join / Leave / Duration)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs text-slate-600 uppercase">
                        <th class="px-4 py-3 text-right">الاجتماع</th>
                        <th class="px-4 py-3 text-right">المشارك</th>
                        <th class="px-4 py-3 text-right">Join</th>
                        <th class="px-4 py-3 text-right">Leave</th>
                        <th class="px-4 py-3 text-right">آخر نشاط</th>
                        <th class="px-4 py-3 text-right">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold text-slate-900"><?php echo e($t['meeting_title']); ?></div>
                                <div class="text-xs font-mono text-slate-500"><?php echo e($t['meeting_code']); ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-800"><?php echo e($t['display_name']); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(optional($t['joined_at'])->format('Y-m-d H:i:s') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(optional($t['left_at'])->format('Y-m-d H:i:s') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e(optional($t['last_seen_at'])->format('Y-m-d H:i:s') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($t['duration_minutes'] !== null ? number_format($t['duration_minutes']) . ' دقيقة' : '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">لا توجد أحداث مشاركة في هذا النطاق.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\student-control\user-consumption.blade.php ENDPATH**/ ?>