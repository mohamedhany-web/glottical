

<?php $__env->startSection('title', 'مراجعة الملف التعريفي — التسويق الشخصي'); ?>
<?php $__env->startSection('header', 'مراجعة ملفات التسويق الشخصي للطلاب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-2xl bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800 dark:text-green-300"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-2xl bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
            <span class="font-bold text-red-800 dark:text-red-300"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">ملف التعريفي للتسويق الشخصي (بورتفوليو)</h1>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">عند حفظ الطالب لملفه من «my-portfolio/profile» يُرسل للمراجعة هنا. المعتمد يُعرض للزوار في المعرض العام.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.students-control.paid-features')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-bold">
                    <i class="fas fa-layer-group"></i>
                    إدارة المزايا المدفوعة
                </a>
                <a href="<?php echo e(route('admin.portfolio.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-bold">
                    <i class="fas fa-images"></i>
                    مراجعة مشاريع المعرض
                </a>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index', ['status' => 'pending_review'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e($status === 'pending_review' ? 'bg-amber-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">
            قيد المراجعة <?php if($pendingCount > 0): ?><span class="mr-1 opacity-90">(<?php echo e($pendingCount); ?>)</span><?php endif; ?>
        </a>
        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index', ['status' => 'rejected'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e($status === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">مرفوض</a>
        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index', ['status' => 'approved'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e($status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">معتمد</a>
        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index', ['status' => 'all'])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e($status === 'all' ? 'bg-slate-700 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-200'); ?>">الكل</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-gray-200 dark:border-slate-700 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-slate-900/60 border-b-2 border-gray-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">الطالب</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">الهاتف</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">الحالة</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">آخر إرسال</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-slate-100">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-700/40">
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-slate-100"><?php echo e($u->name); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-slate-300"><?php echo e($u->phone ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm">
                                <?php if($u->portfolio_profile_status === \App\Models\User::PORTFOLIO_PROFILE_PENDING): ?>
                                    <span class="px-2 py-1 rounded-lg bg-amber-100 text-amber-800 text-xs font-bold">قيد المراجعة</span>
                                <?php elseif($u->portfolio_profile_status === \App\Models\User::PORTFOLIO_PROFILE_REJECTED): ?>
                                    <span class="px-2 py-1 rounded-lg bg-red-100 text-red-800 text-xs font-bold">مرفوض</span>
                                <?php elseif($u->portfolio_profile_status === \App\Models\User::PORTFOLIO_PROFILE_APPROVED): ?>
                                    <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">معتمد</span>
                                <?php else: ?>
                                    <span class="text-gray-500 dark:text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-slate-300"><?php echo e($u->portfolio_profile_submitted_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm">
                                <a href="<?php echo e(route('admin.portfolio-marketing-profiles.show', $u)); ?>" class="text-sky-600 dark:text-sky-400 font-bold hover:underline">عرض ومراجعة</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-slate-400">لا توجد طلبات في هذا التبويب.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/30">
            <?php echo e($users->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\portfolio-marketing\index.blade.php ENDPATH**/ ?>