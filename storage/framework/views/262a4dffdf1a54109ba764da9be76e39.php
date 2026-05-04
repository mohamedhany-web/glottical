<?php $__env->startSection('title', 'التقارير الأكاديمية'); ?>
<?php $__env->startSection('header', 'التقارير الأكاديمية'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-book text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">التقارير الأكاديمية</h2>
                    <p class="text-sm text-slate-600 mt-1">تقارير عن الامتحانات، الواجبات، والمحاضرات</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('admin.reports.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
                <a href="<?php echo e(route('admin.reports.export.academic', array_merge(request()->all()))); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-file-excel"></i>
                    تصدير إلى Excel
                </a>
            </div>
        </div>
    </section>

    <!-- الفلاتر -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                الفلاتر
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الفترة</label>
                    <select name="period" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="today" <?php echo e($period == 'today' ? 'selected' : ''); ?>>اليوم</option>
                        <option value="week" <?php echo e($period == 'week' ? 'selected' : ''); ?>>هذا الأسبوع</option>
                        <option value="month" <?php echo e($period == 'month' ? 'selected' : ''); ?>>هذا الشهر</option>
                        <option value="year" <?php echo e($period == 'year' ? 'selected' : ''); ?>>هذا العام</option>
                        <option value="all" <?php echo e($period == 'all' ? 'selected' : ''); ?>>الكل</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">من تاريخ</label>
                    <input type="date" name="start_date" value="<?php echo e($startDate ? $startDate->format('Y-m-d') : ''); ?>" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="<?php echo e($endDate ? $endDate->format('Y-m-d') : ''); ?>" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div class="md:col-span-3 flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق الفلاتر
                    </button>
                    <a href="<?php echo e(route('admin.reports.academic')); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- الإحصائيات -->
    <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-clipboard-check text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الامتحانات</p>
                    <p class="text-xl font-black text-slate-900"><?php echo e(number_format($stats['total_exams'] ?? 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-tasks text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">المحاولات</p>
                    <p class="text-xl font-black text-slate-900"><?php echo e(number_format($stats['total_attempts'] ?? 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-file-alt text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الواجبات</p>
                    <p class="text-xl font-black text-slate-900"><?php echo e(number_format($stats['total_assignments'] ?? 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-video text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">المحاضرات</p>
                    <p class="text-xl font-black text-slate-900"><?php echo e(number_format($stats['total_lectures'] ?? 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-certificate text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الشهادات</p>
                    <p class="text-xl font-black text-slate-900"><?php echo e(number_format($stats['total_certificates'] ?? 0)); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- الامتحانات والمحاولات -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-blue-600"></i>
                    الامتحانات
                </h3>
                <span class="text-xs font-semibold text-slate-600"><?php echo e($exams->total()); ?> امتحان</span>
            </div>
            <div class="p-6">
                <?php if($exams->count() > 0): ?>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-bold text-slate-900"><?php echo e(htmlspecialchars($exam->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></h4>
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                    <i class="fas fa-users"></i>
                                    <?php echo e(number_format($exam->attempts_count)); ?>

                                </span>
                            </div>
                            <p class="text-xs text-slate-600"><?php echo e($exam->created_at->format('d/m/Y')); ?></p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($exams->hasPages()): ?>
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            <?php echo e($exams->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <p class="text-sm text-slate-600">لا توجد امتحانات</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600"></i>
                    الواجبات
                </h3>
                <span class="text-xs font-semibold text-slate-600"><?php echo e($assignments->total()); ?> واجب</span>
            </div>
            <div class="p-6">
                <?php if($assignments->count() > 0): ?>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-bold text-slate-900"><?php echo e(htmlspecialchars($assignment->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></h4>
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo e(number_format($assignment->submissions_count ?? 0)); ?>

                                </span>
                            </div>
                            <p class="text-xs text-slate-600"><?php echo e($assignment->created_at->format('d/m/Y')); ?></p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($assignments->hasPages()): ?>
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            <?php echo e($assignments->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <p class="text-sm text-slate-600">لا توجد واجبات</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i>
                    محاولات الامتحانات
                </h3>
                <span class="text-xs font-semibold text-slate-600"><?php echo e($examAttempts->total()); ?> محاولة</span>
            </div>
            <div class="p-6">
                <?php if($examAttempts->count() > 0): ?>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php $__currentLoopData = $examAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-lg border border-slate-200 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-900"><?php echo e(htmlspecialchars($attempt->exam->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></p>
                                    <span class="text-xs <?php echo e(($attempt->status ?? '') === 'passed' ? 'text-emerald-700' : 'text-rose-700'); ?>"><?php echo e(htmlspecialchars($attempt->status ?? '-', ENT_QUOTES, 'UTF-8')); ?></span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1"><?php echo e(htmlspecialchars($attempt->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?> • <?php echo e($attempt->created_at?->format('d/m/Y H:i')); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($examAttempts->hasPages()): ?>
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            <?php echo e($examAttempts->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-sm text-slate-600">لا توجد محاولات</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-blue-600"></i>
                    تسليمات الواجبات
                </h3>
                <span class="text-xs font-semibold text-slate-600"><?php echo e($submissions->total()); ?> تسليم</span>
            </div>
            <div class="p-6">
                <?php if($submissions->count() > 0): ?>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-lg border border-slate-200 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-900"><?php echo e(htmlspecialchars($submission->assignment->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></p>
                                    <span class="text-xs text-slate-700"><?php echo e(htmlspecialchars($submission->status ?? '-', ENT_QUOTES, 'UTF-8')); ?></span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1"><?php echo e(htmlspecialchars($submission->student->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?> • <?php echo e($submission->created_at?->format('d/m/Y H:i')); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($submissions->hasPages()): ?>
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            <?php echo e($submissions->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-sm text-slate-600">لا توجد تسليمات</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-video text-blue-600"></i>
                    المحاضرات
                </h3>
                <span class="text-xs font-semibold text-slate-600"><?php echo e($lectures->total()); ?> محاضرة</span>
            </div>
            <div class="p-6">
                <?php if($lectures->count() > 0): ?>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-lg border border-slate-200 p-3">
                                <p class="text-sm font-semibold text-slate-900"><?php echo e(htmlspecialchars($lecture->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></p>
                                <p class="text-xs text-slate-500 mt-1"><?php echo e(htmlspecialchars($lecture->instructor->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?> • <?php echo e($lecture->scheduled_at?->format('d/m/Y H:i') ?? '-'); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($lectures->hasPages()): ?>
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            <?php echo e($lectures->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-sm text-slate-600">لا توجد محاضرات</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-certificate text-blue-600"></i>
                    الشهادات
                </h3>
                <span class="text-xs font-semibold text-slate-600"><?php echo e($certificates->total()); ?> شهادة</span>
            </div>
            <div class="p-6">
                <?php if($certificates->count() > 0): ?>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-lg border border-slate-200 p-3">
                                <p class="text-sm font-semibold text-slate-900"><?php echo e(htmlspecialchars($certificate->course->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></p>
                                <p class="text-xs text-slate-500 mt-1"><?php echo e(htmlspecialchars($certificate->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?> • <?php echo e($certificate->issued_at?->format('d/m/Y') ?? '-'); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($certificates->hasPages()): ?>
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            <?php echo e($certificates->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-sm text-slate-600">لا توجد شهادات</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\reports\academic.blade.php ENDPATH**/ ?>