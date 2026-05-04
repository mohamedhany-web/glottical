

<?php $__env->startSection('title', 'التوظيف والمقابلات'); ?>
<?php $__env->startSection('header', 'التوظيف والمقابلات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('employee.hr-desk.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> لوحة HR</a>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('employee.hr.recruitment.openings.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold">الوظائف الشاغرة</a>
        <a href="<?php echo e(route('employee.hr.recruitment.candidates.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 text-white text-sm font-bold">المرشحون</a>
        <a href="<?php echo e(route('employee.hr.recruitment.openings.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-violet-300 text-violet-800 text-sm font-bold">+ وظيفة</a>
        <a href="<?php echo e(route('employee.hr.recruitment.candidates.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-slate-300 text-slate-800 text-sm font-bold">+ مرشح</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-violet-200 bg-violet-50/80 p-5">
            <p class="text-sm text-violet-800 font-semibold">وظائف مفتوحة</p>
            <p class="text-3xl font-black text-violet-950 tabular-nums mt-1"><?php echo e($stats['openings_open']); ?></p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-5">
            <p class="text-sm text-amber-900 font-semibold">طلبات نشطة</p>
            <p class="text-3xl font-black text-amber-950 tabular-nums mt-1"><?php echo e($stats['applications_active']); ?></p>
        </div>
        <div class="rounded-2xl border border-teal-200 bg-teal-50/80 p-5">
            <p class="text-sm text-teal-900 font-semibold">مقابلات (14 يوماً)</p>
            <p class="text-3xl font-black text-teal-950 tabular-nums mt-1"><?php echo e($stats['interviews_upcoming']); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 font-bold text-gray-900">آخر الطلبات</div>
            <ul class="divide-y divide-gray-100 text-sm max-h-80 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentApplications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="px-5 py-3">
                        <a href="<?php echo e(route('employee.hr.recruitment.applications.show', $app)); ?>" class="font-semibold text-violet-700 hover:underline"><?php echo e($app->candidate?->full_name); ?></a>
                        <div class="text-xs text-gray-500 mt-0.5"><?php echo e($app->opening?->title); ?> · <?php echo e($app->status_label); ?></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="px-5 py-8 text-center text-gray-500">لا توجد طلبات بعد.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 font-bold text-gray-900">مقابلات قادمة</div>
            <ul class="divide-y divide-gray-100 text-sm max-h-80 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $upcomingInterviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $int): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="px-5 py-3">
                        <div class="font-semibold text-gray-900"><?php echo e($int->round_title); ?></div>
                        <div class="text-xs text-gray-600"><?php echo e($int->scheduled_at->format('Y-m-d H:i')); ?> — <?php echo e($int->application->candidate?->full_name); ?></div>
                        <div class="text-xs text-violet-600"><?php echo e($int->application->opening?->title); ?></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="px-5 py-8 text-center text-gray-500">لا توجد مقابلات مجدولة.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\index.blade.php ENDPATH**/ ?>