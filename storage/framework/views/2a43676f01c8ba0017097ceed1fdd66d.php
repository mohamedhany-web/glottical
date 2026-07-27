

<?php $__env->startSection('title', 'تقارير البث الذكي (n8n)'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-robot text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                        تقارير البث الذكي (n8n)
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        مراجعة التقارير التي تم إنشاؤها تلقائياً من جلسات البث المباشر عبر n8n وربطها بالمعلمين والجلسات.
                    </p>
                </div>
            </div>
        </div>

        
        <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs text-slate-500 mb-1 block">الحالة</label>
                <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    <option value="">كل الحالات</option>
                    <option value="pending" <?php echo e($status === 'pending' ? 'selected' : ''); ?>>قيد الانتظار</option>
                    <option value="processing" <?php echo e($status === 'processing' ? 'selected' : ''); ?>>قيد المعالجة</option>
                    <option value="completed" <?php echo e($status === 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                    <option value="failed" <?php echo e($status === 'failed' ? 'selected' : ''); ?>>فشل</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="text-xs text-slate-500 mb-1 block">معرّف المعلم</label>
                <input type="number"
                       name="instructor_id"
                       value="<?php echo e($instructorId); ?>"
                       class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm"
                       placeholder="ID المعلم">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm">
                    <i class="fas fa-filter ml-1"></i>
                    <span>تطبيق الفلاتر</span>
                </button>
                <?php if(request()->hasAny(['status', 'instructor_id'])): ?>
                    <a href="<?php echo e(route('admin.n8n.live-session-reports.index')); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm hover:bg-slate-300 dark:hover:bg-slate-600">
                        مسح
                    </a>
                <?php endif; ?>
            </div>
        </form>

        
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">#</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">الجلسة</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">المعلم</th>
                            <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">الحالة</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">العنوان / الملخص</th>
                            <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">التسجيل</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">آخر تحديث</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3 text-slate-500"><?php echo e($report->id); ?></td>
                            <td class="px-4 py-3">
                                <?php if($report->session): ?>
                                    <div>
                                        <div class="font-semibold text-slate-800 dark:text-white">
                                            <?php echo e(\Illuminate\Support\Str::limit($report->session->title, 40)); ?>

                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            ID: <?php echo e($report->session->id); ?>

                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($report->instructor): ?>
                                    <div>
                                        <div class="font-medium text-slate-800 dark:text-white">
                                            <?php echo e($report->instructor->name); ?>

                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            ID: <?php echo e($report->instructor->id); ?>

                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php
                                    $badgeClasses = match($report->status) {
                                        'pending' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200',
                                        'processing' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'failed' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200',
                                    };
                                ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($badgeClasses); ?>">
                                    <?php if($report->status === 'processing'): ?>
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <?php elseif($report->status === 'completed'): ?>
                                        <i class="fas fa-check-circle text-[11px]"></i>
                                    <?php elseif($report->status === 'failed'): ?>
                                        <i class="fas fa-times-circle text-[11px]"></i>
                                    <?php endif; ?>
                                    <span><?php echo e($report->status); ?></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <div class="space-y-1">
                                    <div class="font-semibold text-slate-800 dark:text-slate-100 truncate">
                                        <?php echo e($report->title ?? '—'); ?>

                                    </div>
                                    <?php if($report->summary): ?>
                                        <div class="text-xs text-slate-500 dark:text-slate-300 line-clamp-2" title="<?php echo e($report->summary); ?>">
                                            <?php echo e(\Illuminate\Support\Str::limit($report->summary, 120)); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($report->recording): ?>
                                    <a href="<?php echo e($report->recording->getUrl()); ?>"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-emerald-500/60 text-emerald-600 dark:text-emerald-300 text-xs font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                                        <i class="fas fa-play text-[11px]"></i>
                                        تشغيل
                                    </a>
                                <?php elseif($report->audio_path): ?>
                                    <span class="text-[11px] font-mono text-slate-500 break-all">
                                        <?php echo e($report->audio_path); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">
                                <?php echo e($report->updated_at?->format('Y-m-d H:i') ?? '—'); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <i class="fas fa-robot text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                                <p class="text-slate-500 dark:text-slate-300">لا توجد تقارير حالياً.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($reports->hasPages()): ?>
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                    <?php echo e($reports->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\n8n\live-session-reports\index.blade.php ENDPATH**/ ?>