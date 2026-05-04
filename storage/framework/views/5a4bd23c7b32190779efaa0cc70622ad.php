

<?php $__env->startSection('title', 'إدارة المزايا المدفوعة'); ?>
<?php $__env->startSection('header', 'إدارة المزايا المدفوعة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">كل المزايا المدفوعة</h1>
                <p class="text-sm text-slate-600 mt-1">ادخل على أي ميزة لمراقبة المستخدمين والتحكم التفصيلي.</p>
            </div>
            <div class="flex gap-3 text-sm">
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700">اشتراكات نشطة: <strong><?php echo e(number_format($stats['active_subscriptions'])); ?></strong></span>
                <span class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700">طلاب نشطون: <strong><?php echo e(number_format($stats['active_students_with_subscription'])); ?></strong></span>
            </div>
        </div>
    </div>

    <?php if(Route::has('admin.portfolio-marketing-profiles.index')): ?>
    <div class="rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-white shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start gap-4">
                <span class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
                    <i class="fas fa-id-card text-xl"></i>
                </span>
                <div>
                    <h2 class="text-lg font-black text-slate-900">مراجعة الملف التعريفي (التسويق الشخصي)</h2>
                    <p class="text-sm text-slate-600 mt-1 max-w-2xl">عند حفظ الطالب لملفه من صفحة البورتفوليو التعريفية يُرسل للمراجعة هنا. اعتمادك يحدّث ما يظهر للزوار في المعرض العام. مرتبط بميزة «الملف التعريفي للمعلم» في الباقات.</p>
                    <?php if(($pendingPortfolioMarketingCount ?? 0) > 0): ?>
                        <p class="text-sm font-bold text-amber-800 mt-2">
                            <i class="fas fa-clock ml-1"></i>
                            <?php echo e(number_format($pendingPortfolioMarketingCount)); ?> طلب قيد المراجعة
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black shadow-sm shrink-0">
                <i class="fas fa-clipboard-check"></i>
                فتح صفحة المراجعة
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('admin.students-control.paid-features.show', $feature['key'])); ?>"
               class="rounded-2xl bg-white border border-slate-200 hover:border-sky-300 shadow-sm hover:shadow-md p-5 transition-all">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <span class="w-12 h-12 rounded-xl <?php echo e($feature['icon_bg']); ?> <?php echo e($feature['icon_text']); ?> flex items-center justify-center">
                        <i class="fas <?php echo e($feature['icon']); ?>"></i>
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                        <?php echo e(number_format($feature['users_count'])); ?> مستخدم
                    </span>
                </div>
                <h3 class="text-base font-bold text-slate-900"><?php echo e($feature['label']); ?></h3>
                <p class="text-xs text-slate-500 mt-1"><?php echo e($feature['key']); ?></p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full rounded-2xl bg-white border border-slate-200 p-10 text-center text-slate-500">
                لا توجد مزايا معرفة حالياً.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\student-control\paid-features.blade.php ENDPATH**/ ?>