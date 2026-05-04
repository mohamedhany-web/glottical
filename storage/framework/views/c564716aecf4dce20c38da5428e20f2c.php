

<?php $__env->startSection('title', 'الدعم الفني'); ?>
<?php $__env->startSection('header', 'إدارة الدعم الفني'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-slate-900">قسم الدعم الفني</h1>
        <p class="text-sm text-slate-600 mt-1">إدارة تذاكر العملاء ومتابعة الحلول على مدار اليوم.</p>
        <?php if(Route::has('admin.support-inquiry-categories.index')): ?>
            <a href="<?php echo e(route('admin.support-inquiry-categories.index')); ?>" class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-tags"></i> إدارة تصنيفات الاستفسار
            </a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 p-4"><p class="text-xs text-slate-500">مفتوحة</p><p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($stats['open'])); ?></p></div>
        <div class="rounded-xl bg-white border border-slate-200 p-4"><p class="text-xs text-slate-500">قيد التنفيذ</p><p class="text-2xl font-bold text-amber-700"><?php echo e(number_format($stats['in_progress'])); ?></p></div>
        <div class="rounded-xl bg-white border border-slate-200 p-4"><p class="text-xs text-slate-500">تم الحل</p><p class="text-2xl font-bold text-emerald-700"><?php echo e(number_format($stats['resolved'])); ?></p></div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="category_id" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <option value="all" <?php echo e(($categoryId ?? 'all') === 'all' || ($categoryId ?? '') === '' ? 'selected' : ''); ?>>كل التصنيفات</option>
                    <?php $__currentLoopData = $inquiryCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ic->id); ?>" <?php echo e((string) ($categoryId ?? '') === (string) $ic->id ? 'selected' : ''); ?>><?php echo e($ic->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="status" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <option value="all" <?php echo e($status === 'all' ? 'selected' : ''); ?>>كل الحالات</option>
                    <option value="open" <?php echo e($status === 'open' ? 'selected' : ''); ?>>open</option>
                    <option value="in_progress" <?php echo e($status === 'in_progress' ? 'selected' : ''); ?>>in_progress</option>
                    <option value="resolved" <?php echo e($status === 'resolved' ? 'selected' : ''); ?>>resolved</option>
                    <option value="closed" <?php echo e($status === 'closed' ? 'selected' : ''); ?>>closed</option>
                </select>
                <select name="priority" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <option value="all" <?php echo e($priority === 'all' ? 'selected' : ''); ?>>كل الأولويات</option>
                    <option value="low" <?php echo e($priority === 'low' ? 'selected' : ''); ?>>low</option>
                    <option value="normal" <?php echo e($priority === 'normal' ? 'selected' : ''); ?>>normal</option>
                    <option value="high" <?php echo e($priority === 'high' ? 'selected' : ''); ?>>high</option>
                    <option value="urgent" <?php echo e($priority === 'urgent' ? 'selected' : ''); ?>>urgent</option>
                </select>
                <button class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold">تصفية</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs text-slate-600 uppercase">
                        <th class="px-4 py-3 text-right">العميل</th>
                        <th class="px-4 py-3 text-right">التصنيف</th>
                        <th class="px-4 py-3 text-right">الموضوع</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">الأولوية</th>
                        <th class="px-4 py-3 text-right">آخر رد</th>
                        <th class="px-4 py-3 text-right">عرض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-800"><?php echo e($ticket->user->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($ticket->inquiryCategory->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($ticket->subject); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e($ticket->status); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e($ticket->priority); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-500"><?php echo e(optional($ticket->last_reply_at ?? $ticket->updated_at)->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3 text-sm"><a class="text-sky-600 hover:underline" href="<?php echo e(route('admin.support-tickets.show', $ticket)); ?>">فتح</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">لا توجد تذاكر دعم حالياً.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-200"><?php echo e($tickets->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\support-tickets\index.blade.php ENDPATH**/ ?>