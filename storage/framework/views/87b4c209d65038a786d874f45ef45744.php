
<?php $__env->startSection('title', 'تسجيلات Classroom'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-chalkboard text-indigo-500 ml-2"></i>تسجيلات Classroom</h1>
            <p class="text-sm text-slate-500 mt-1">عرض كل اجتماعات Classroom مع روابط تحميل التسجيلات من Cloudflare R2.</p>
        </div>
    </div>

    <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="text-xs text-slate-500 mb-1 block">بحث</label>
            <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="عنوان، كود، اسم المعلم، أو البريد..." class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">حالة الاجتماع</label>
            <select name="status" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="all" <?php echo e($status === 'all' ? 'selected' : ''); ?>>الكل</option>
                <option value="live" <?php echo e($status === 'live' ? 'selected' : ''); ?>>مباشر</option>
                <option value="scheduled" <?php echo e($status === 'scheduled' ? 'selected' : ''); ?>>مجدول</option>
                <option value="ended" <?php echo e($status === 'ended' ? 'selected' : ''); ?>>منتهي</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">التسجيل</label>
            <select name="has_recording" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="all" <?php echo e($hasRecording === 'all' ? 'selected' : ''); ?>>الكل</option>
                <option value="yes" <?php echo e($hasRecording === 'yes' ? 'selected' : ''); ?>>يوجد تسجيل</option>
                <option value="no" <?php echo e($hasRecording === 'no' ? 'selected' : ''); ?>>بدون تسجيل</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-600 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
            <i class="fas fa-search ml-1"></i> بحث
        </button>
    </form>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">#</th>
                    <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">الاجتماع</th>
                    <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">المعلم</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">الحالة</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">وقت الرفع</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                <?php $__empty_1 = true; $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 text-slate-500"><?php echo e($meeting->id); ?></td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800 dark:text-white"><?php echo e($meeting->title ?: ('اجتماع #' . $meeting->id)); ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                كود: <span class="font-mono"><?php echo e($meeting->code); ?></span>
                                <?php if($meeting->ended_at): ?>
                                    · انتهى: <?php echo e($meeting->ended_at->format('Y-m-d H:i')); ?>

                                <?php elseif($meeting->scheduled_for): ?>
                                    · مجدول: <?php echo e($meeting->scheduled_for->format('Y-m-d H:i')); ?>

                                <?php endif; ?>
                            </p>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            <div><?php echo e($meeting->user->name ?? '—'); ?></div>
                            <div class="text-xs text-slate-500"><?php echo e($meeting->user->email ?? '—'); ?></div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($meeting->isLive()): ?>
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-700 text-xs">مباشر</span>
                            <?php elseif(!$meeting->started_at): ?>
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 text-xs">مجدول</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs">منتهي</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-500">
                            <?php echo e($meeting->recording_uploaded_at ? $meeting->recording_uploaded_at->format('Y-m-d H:i') : '—'); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-2">
                                <?php if($meeting->recording_download_url): ?>
                                    <a href="<?php echo e($meeting->recording_download_url); ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold">
                                        <i class="fas fa-download ml-1"></i> تحميل
                                    </a>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">لا يوجد تسجيل</span>
                                <?php endif; ?>
                                <button type="button" onclick="navigator.clipboard.writeText('<?php echo e(url('classroom/join/' . $meeting->code)); ?>'); this.textContent='تم النسخ'; setTimeout(()=>this.textContent='نسخ رابط الدخول', 1200)" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold">نسخ رابط الدخول</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                            <i class="fas fa-video-slash text-3xl text-slate-300 mb-3 block"></i>
                            لا توجد اجتماعات مطابقة للفلاتر الحالية.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($meetings->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($meetings->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\classroom-recordings\index.blade.php ENDPATH**/ ?>