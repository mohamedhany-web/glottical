<?php $__env->startSection('title', 'فرص الأكاديميات'); ?>
<?php $__env->startSection('header', 'فرص الأكاديميات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo e(__('admin.academy_opportunities_title')); ?></h1>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">تظهر للمعلمين للتقديم الذاتي؛ ومن «مكتب التوظيف» تتحكم المنصة في الملفات المعتمدة للأكاديميات.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if(Route::has('admin.hiring-academies.index')): ?>
            <a href="<?php echo e(route('admin.hiring-academies.index')); ?>" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">أكاديميات التوظيف</a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.academy-opportunities.create')); ?>" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">إضافة فرصة</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs uppercase text-slate-600 dark:text-slate-400">
                        <th class="px-4 py-3 text-right">الجهة</th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.academy_opportunity_hiring_academy')); ?></th>
                        <th class="px-4 py-3 text-right">العنوان</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">مميزة</th>
                        <th class="px-4 py-3 text-right">تقديمات</th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.academy_opportunity_presentations_count')); ?></th>
                        <th class="px-4 py-3 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $opportunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="dark:border-slate-700">
                            <td class="px-4 py-3 text-sm text-slate-800 dark:text-slate-200"><?php echo e($op->organization_name); ?></td>
                            <td class="px-4 py-3 text-xs">
                                <?php if($op->hiringAcademy): ?>
                                    <a href="<?php echo e(route('admin.hiring-academies.show', $op->hiringAcademy)); ?>" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline"><?php echo e($op->hiringAcademy->name); ?></a>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-white"><?php echo e($op->title); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-700 dark:text-slate-300"><?php echo e($op->status); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e($op->is_featured ? 'نعم' : 'لا'); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e(number_format($op->applications_count)); ?></td>
                            <td class="px-4 py-3 text-xs font-semibold text-violet-600 dark:text-violet-400"><?php echo e(number_format($op->teacher_presentations_count)); ?></td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <a href="<?php echo e(route('admin.academy-opportunities.recruitment', $op)); ?>" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">مكتب التوظيف</a>
                                    <span class="text-slate-300">|</span>
                                    <a href="<?php echo e(route('admin.academy-opportunities.applications', $op)); ?>" class="text-violet-600 hover:underline">الطلبات</a>
                                    <a href="<?php echo e(route('admin.academy-opportunities.edit', $op)); ?>" class="text-sky-600 hover:underline">تعديل</a>
                                    <form action="<?php echo e(route('admin.academy-opportunities.destroy', $op)); ?>" method="POST" onsubmit="return confirm('حذف الفرصة؟');" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="text-rose-600 hover:underline">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500">لا توجد فرص مضافة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-200"><?php echo e($opportunities->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\academy-opportunities\index.blade.php ENDPATH**/ ?>