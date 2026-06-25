

<?php $__env->startSection('title', 'الحضور والغياب - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', 'الحضور والغياب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-5 py-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-5 py-4">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
            <h2 class="text-xl font-black text-slate-900 dark:text-slate-100">إدارة الحضور والغياب</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">فلترة ومراجعة سجلات الحضور لجميع المحاضرات.</p>
        </div>

        <div class="p-5">
            <form method="GET" action="<?php echo e(route('admin.attendance.index')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">المحاضرة</label>
                    <select name="lecture_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100">
                        <option value="">كل المحاضرات</option>
                        <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lecture->id); ?>" <?php echo e(request('lecture_id') == $lecture->id ? 'selected' : ''); ?>>
                                <?php echo e($lecture->title); ?><?php echo e($lecture->course ? ' - ' . $lecture->course->title : ''); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100">
                        <option value="">كل الحالات</option>
                        <option value="present" <?php echo e(request('status') == 'present' ? 'selected' : ''); ?>>حاضر</option>
                        <option value="late" <?php echo e(request('status') == 'late' ? 'selected' : ''); ?>>متأخر</option>
                        <option value="partial" <?php echo e(request('status') == 'partial' ? 'selected' : ''); ?>>جزئي</option>
                        <option value="absent" <?php echo e(request('status') == 'absent' ? 'selected' : ''); ?>>غائب</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    <?php if(request()->anyFilled(['lecture_id', 'status'])): ?>
                        <a href="<?php echo e(route('admin.attendance.index')); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">سجلات الحضور</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/40">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">المحاضرة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">الدقائق</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">النسبة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                            <td class="px-6 py-4 text-sm">
                                <a href="<?php echo e(route('admin.attendance.lecture', $record->lecture_id)); ?>" class="font-semibold text-blue-600 hover:text-blue-700">
                                    <?php echo e($record->lecture->title ?? '—'); ?>

                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100"><?php echo e($record->student->name ?? 'غير محدد'); ?></td>
                            <td class="px-6 py-4 text-sm">
                                <?php
                                    $statusClasses = [
                                        'present' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                        'late' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                        'partial' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300',
                                        'absent' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300',
                                    ];
                                ?>
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($statusClasses[$record->status] ?? 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'); ?>">
                                    <?php echo e($record->status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300"><?php echo e((int) ($record->attendance_minutes ?? 0)); ?></td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300"><?php echo e(number_format((float) ($record->attendance_percentage ?? 0), 1)); ?>%</td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400"><?php echo e(optional($record->created_at)->format('Y-m-d H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-600 dark:text-slate-300">لا توجد سجلات حضور مطابقة.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($records->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                <?php echo e($records->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\attendance\index.blade.php ENDPATH**/ ?>