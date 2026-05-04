<?php $__env->startSection('title', 'مستخدمي الميزة: ' . $featureLabel); ?>
<?php $__env->startSection('header', 'مستخدمي الميزة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-xl <?php echo e($featureCfg['icon_bg'] ?? 'bg-slate-100'); ?> <?php echo e($featureCfg['icon_text'] ?? 'text-slate-600'); ?> flex items-center justify-center">
                    <i class="fas <?php echo e($featureCfg['icon'] ?? 'fa-star'); ?>"></i>
                </span>
                <div>
                    <h1 class="text-xl font-bold text-slate-900"><?php echo e($featureLabel); ?></h1>
                    <p class="text-xs text-slate-500"><?php echo e($featureKey); ?></p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.students-control.paid-features')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                رجوع للمزايا
            </a>
        </div>
    </div>

    <?php if($featureKey === 'teacher_profile' && Route::has('admin.portfolio-marketing-profiles.index')): ?>
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="font-black text-emerald-900">مراجعة ملفات التسويق الشخصي للطلاب</p>
            <p class="text-sm text-emerald-800/90 mt-1">اعتماد الصورة والعنوان والنبذة بعد أن يحفظ الطالب من «الملف التعريفي» في البورتفوليو.</p>
        </div>
        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index')); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black shrink-0">
            <i class="fas fa-clipboard-check"></i>
            مراجعة الملفات التعريفية
        </a>
    </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="text-sm font-bold text-slate-800">المستخدمون المشتركون بهذه الميزة</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs text-slate-600 uppercase">
                        <th class="px-6 py-3 text-right">المستخدم</th>
                        <th class="px-6 py-3 text-right">الهاتف</th>
                        <th class="px-6 py-3 text-right">الخطة</th>
                        <th class="px-6 py-3 text-right">ينتهي</th>
                        <th class="px-6 py-3 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $sub = $user->subscriptions->first(); ?>
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 text-sm font-semibold text-slate-900"><?php echo e($user->name); ?></td>
                            <td class="px-6 py-3 text-sm text-slate-600"><?php echo e($user->phone ?? '—'); ?></td>
                            <td class="px-6 py-3 text-sm text-slate-700"><?php echo e($sub->plan_name ?? '—'); ?></td>
                            <td class="px-6 py-3 text-sm text-slate-600"><?php echo e($sub?->end_date?->format('Y-m-d') ?? 'غير محدد'); ?></td>
                            <td class="px-6 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="text-sky-600 hover:underline">الحساب</a>
                                    <?php if($sub): ?>
                                        <a href="<?php echo e(route('admin.subscriptions.consumption', $sub)); ?>" class="text-emerald-600 hover:underline">الاستهلاك</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">لا يوجد مستخدمون لهذه الميزة حالياً.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            <?php echo e($users->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\student-control\feature-users.blade.php ENDPATH**/ ?>