

<?php $__env->startSection('title', $academy->name); ?>
<?php $__env->startSection('header', $academy->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold mb-2
                <?php if($academy->status === 'active'): ?> bg-emerald-100 text-emerald-800
                <?php elseif($academy->status === 'lead'): ?> bg-amber-100 text-amber-900
                <?php else: ?> bg-slate-200 text-slate-700 <?php endif; ?>"><?php echo e($academy->statusLabel()); ?></span>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white font-heading"><?php echo e($academy->name); ?></h1>
            <?php if($academy->legal_name): ?><p class="text-sm text-slate-500 mt-1"><?php echo e($academy->legal_name); ?></p><?php endif; ?>
            <p class="text-xs text-slate-400 mt-2 font-mono">slug: <?php echo e($academy->slug); ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.hiring-academies.edit', $academy)); ?>" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold">تعديل</a>
            <a href="<?php echo e(route('admin.academy-opportunities.create')); ?>" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">فرصة جديدة</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-address-card text-indigo-500"></i> التواصل</h2>
            <dl class="space-y-2 text-sm">
                <?php if($academy->contact_name): ?><div><dt class="text-slate-500">الاسم</dt><dd class="font-semibold text-slate-800 dark:text-slate-100"><?php echo e($academy->contact_name); ?></dd></div><?php endif; ?>
                <?php if($academy->contact_email): ?><div><dt class="text-slate-500">البريد</dt><dd class="font-semibold"><?php echo e($academy->contact_email); ?></dd></div><?php endif; ?>
                <?php if($academy->contact_phone): ?><div><dt class="text-slate-500">الهاتف</dt><dd class="font-semibold font-mono"><?php echo e($academy->contact_phone); ?></dd></div><?php endif; ?>
                <?php if($academy->website): ?><div><dt class="text-slate-500">الموقع</dt><dd><a href="<?php echo e($academy->website); ?>" class="text-sky-600 hover:underline" target="_blank" rel="noopener"><?php echo e($academy->website); ?></a></dd></div><?php endif; ?>
                <?php if($academy->city): ?><div><dt class="text-slate-500">المدينة</dt><dd><?php echo e($academy->city); ?></dd></div><?php endif; ?>
                <?php if($academy->address): ?><div><dt class="text-slate-500">العنوان</dt><dd><?php echo e($academy->address); ?></dd></div><?php endif; ?>
            </dl>
        </div>
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-file-contract text-amber-500"></i> داخلي</h2>
            <?php if($academy->commercial_notes): ?>
                <p class="text-xs font-bold text-slate-500 mb-1">تجاري / تعاقد</p>
                <div class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line mb-4"><?php echo e($academy->commercial_notes); ?></div>
            <?php endif; ?>
            <?php if($academy->internal_notes): ?>
                <p class="text-xs font-bold text-slate-500 mb-1">ملاحظات الفريق</p>
                <div class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line"><?php echo e($academy->internal_notes); ?></div>
            <?php endif; ?>
            <?php if(!$academy->commercial_notes && !$academy->internal_notes): ?>
                <p class="text-sm text-slate-400">لا توجد ملاحظات داخلية.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <h2 class="font-bold text-slate-900 dark:text-white">فرص التوظيف المرتبطة</h2>
            <span class="text-sm font-bold text-indigo-600"><?php echo e(number_format($academy->opportunities_count)); ?></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-xs text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">العنوان</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">تقديمات</th>
                        <th class="px-4 py-3 text-right">عروض</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $academy->opportunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white"><?php echo e($op->title); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e($op->status); ?></td>
                            <td class="px-4 py-3"><?php echo e(number_format($op->applications_count)); ?></td>
                            <td class="px-4 py-3"><?php echo e(number_format($op->teacher_presentations_count)); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.academy-opportunities.recruitment', $op)); ?>" class="text-violet-600 font-semibold hover:underline">مكتب التوظيف</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد فرص بعد. أنشئ فرصة من قائمة «فرص الأكاديميات» واربطها بهذه الأكاديمية.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <form action="<?php echo e(route('admin.hiring-academies.destroy', $academy)); ?>" method="POST" onsubmit="return confirm('حذف الأكاديمية؟');" class="inline">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="text-rose-600 text-sm font-semibold hover:underline">حذف الأكاديمية (إن لم تكن مرتبطة بفرص)</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\hiring-academies\show.blade.php ENDPATH**/ ?>