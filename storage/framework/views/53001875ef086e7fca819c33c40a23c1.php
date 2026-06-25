<?php $__env->startSection('title', 'البورتفوليو - الرقابة'); ?>
<?php $__env->startSection('header', 'البورتفوليو'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-2xl bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800 dark:text-green-300"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <p class="text-gray-600 dark:text-slate-300">مراجعة مشاريع البورتفوليو من الأدمن فقط — اعتماد أو رفض أو نشر، ثم إظهار/إخفاء من المعرض.</p>
    <?php if(Route::has('admin.portfolio-marketing-profiles.index')): ?>
    <p class="text-sm text-gray-700 dark:text-slate-300 mt-2">
        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index')); ?>" class="font-bold text-emerald-700 dark:text-emerald-400 hover:underline"><i class="fas fa-id-card ml-1"></i>مراجعة الملف التعريفي التسويقي للطلاب (صورة ونبذة)</a>
        — من «التحكم في المزايا» أو مباشرة من هنا.
    </p>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2 mb-4">
        <a href="<?php echo e(route('admin.portfolio.index')); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e(!request('status') && !request()->has('visible') ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">الكل</a>
        <a href="<?php echo e(route('admin.portfolio.index', ['status' => 'pending_review'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e(request('status') === 'pending_review' ? 'bg-amber-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">قيد المراجعة</a>
        <a href="<?php echo e(route('admin.portfolio.index', ['status' => 'approved'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e(request('status') === 'approved' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">معتمد</a>
        <a href="<?php echo e(route('admin.portfolio.index', ['status' => 'published'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e(request('status') === 'published' ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">منشور</a>
        <a href="<?php echo e(route('admin.portfolio.index', ['visible' => '1'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e(request('visible') === '1' ? 'bg-emerald-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">ظاهر</a>
        <a href="<?php echo e(route('admin.portfolio.index', ['visible' => '0'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e(request('visible') === '0' ? 'bg-amber-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">مخفي</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-gray-200 dark:border-slate-700 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-slate-900/60 border-b-2 border-gray-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">المشروع</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">المعلم</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">المسار</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">الحالة</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">ظاهر</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40">
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.portfolio.show', $project)); ?>" class="font-bold text-blue-600 dark:text-sky-400 hover:underline"><?php echo e($project->title); ?></a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-slate-200"><?php echo e($project->user->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-slate-200"><?php echo e($project->academicYear->name ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <?php
                                    $statusLabels = ['pending_review' => 'قيد المراجعة', 'approved' => 'معتمد', 'rejected' => 'مرفوض', 'published' => 'منشور'];
                                ?>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200"><?php echo e($statusLabels[$project->status] ?? $project->status); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($project->is_visible): ?>
                                    <span class="text-green-600 dark:text-green-400 font-bold">نعم</span>
                                <?php else: ?>
                                    <span class="text-amber-600 dark:text-amber-400 font-bold">مخفي</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.portfolio.show', $project)); ?>" class="text-blue-600 dark:text-sky-400 hover:underline text-sm font-bold">عرض</a>
                                <form action="<?php echo e(route('admin.portfolio.toggle-visibility', $project)); ?>" method="POST" class="inline mr-2">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-amber-600 dark:text-amber-400 hover:underline text-sm font-bold"><?php echo e($project->is_visible ? 'إخفاء' : 'إظهار'); ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-slate-400">لا توجد مشاريع.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-slate-700 dark:bg-slate-900/30"><?php echo e($projects->withQueryString()->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\portfolio\index.blade.php ENDPATH**/ ?>