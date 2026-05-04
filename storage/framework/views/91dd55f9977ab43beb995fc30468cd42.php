
<?php $__env->startSection('title', 'تسجيلات الجلسات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-play-circle text-emerald-500 ml-2"></i>تسجيلات الجلسات</h1>
            <p class="text-sm text-slate-500 mt-1">Jibri → R2 ثم تسجيل هنا (ويب هوك أو إضافة يدوية)</p>
        </div>
        <a href="<?php echo e(route('admin.live-recordings.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold shadow-lg shadow-emerald-500/25 transition-all">
            <i class="fas fa-plus"></i> إضافة تسجيل (R2 / يدوي)
        </a>
    </div>

    <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs text-slate-500 mb-1 block">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم التسجيل أو الجلسة..." class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <?php if(isset($sessions) && $sessions->isNotEmpty()): ?>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">الجلسة</label>
            <select name="session_id" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e(request('session_id') == $s->id ? 'selected' : ''); ?>><?php echo e(Str::limit($s->title, 35)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php endif; ?>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">الحالة</label>
            <select name="status" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <option value="ready" <?php echo e(request('status') == 'ready' ? 'selected' : ''); ?>>جاهز</option>
                <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>قيد المعالجة</option>
                <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>فشل</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-600 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors"><i class="fas fa-search ml-1"></i> بحث</button>
    </form>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">#</th>
                    <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">التسجيل</th>
                    <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">الجلسة</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">المدة</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">الحجم</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">الحالة</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">منشور</th>
                    <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                <?php $__empty_1 = true; $__currentLoopData = $recordings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-3 text-slate-500"><?php echo e($rec->id); ?></td>
                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white"><?php echo e($rec->title ?? 'تسجيل #' . $rec->id); ?></td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?php echo e(Str::limit($rec->session?->title ?? '—', 30)); ?></td>
                    <td class="px-4 py-3 text-center text-slate-500"><?php echo e($rec->duration_for_humans); ?></td>
                    <td class="px-4 py-3 text-center text-slate-500"><?php echo e($rec->file_size_for_humans); ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php if($rec->status === 'ready'): ?>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 text-xs">جاهز</span>
                        <?php elseif($rec->status === 'processing'): ?>
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 text-xs">معالجة</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-xs">فشل</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="<?php echo e(route('admin.live-recordings.toggle-publish', $rec)); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button class="text-lg <?php echo e($rec->is_published ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'); ?>" title="<?php echo e($rec->is_published ? 'إلغاء النشر' : 'نشر'); ?>">
                                <i class="fas fa-toggle-<?php echo e($rec->is_published ? 'on' : 'off'); ?>"></i>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if($rec->getUrl()): ?>
                        <a href="<?php echo e($rec->getUrl()); ?>" target="_blank" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-blue-500" title="مشاهدة"><i class="fas fa-external-link-alt text-xs"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="px-4 py-12 text-center text-slate-500"><i class="fas fa-film text-4xl text-slate-300 mb-3 block"></i>لا توجد تسجيلات بعد</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($recordings->hasPages()): ?>
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($recordings->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-recordings\index.blade.php ENDPATH**/ ?>