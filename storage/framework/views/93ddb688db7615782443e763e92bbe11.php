

<?php $__env->startSection('title', $candidate->full_name); ?>
<?php $__env->startSection('header', 'ملف مرشح'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap justify-between gap-3">
        <a href="<?php echo e(route('employee.hr.recruitment.candidates.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>
        <div class="flex gap-2">
            <a href="<?php echo e(route('employee.hr.recruitment.candidates.edit', $candidate)); ?>" class="px-3 py-2 rounded-lg bg-gray-800 text-white text-sm font-bold">تعديل</a>
            <?php if($candidate->applications->count() === 0): ?>
                <form method="POST" action="<?php echo e(route('employee.hr.recruitment.candidates.destroy', $candidate)); ?>" onsubmit="return confirm('حذف المرشح؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-3 py-2 rounded-lg bg-rose-100 text-rose-800 text-sm font-bold">حذف</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-2">
        <h1 class="text-xl font-black text-gray-900"><?php echo e($candidate->full_name); ?></h1>
        <p class="text-sm text-gray-600"><?php echo e($candidate->email); ?> <?php if($candidate->phone): ?>· <?php echo e($candidate->phone); ?><?php endif; ?></p>
        <p class="text-xs text-gray-500">المصدر: <?php echo e($candidate->source_label); ?></p>
        <?php if($candidate->portfolio_url): ?>
            <p class="text-sm"><a href="<?php echo e($candidate->portfolio_url); ?>" target="_blank" class="text-violet-700 font-bold underline">رابط الأعمال</a></p>
        <?php endif; ?>
        <?php if($candidate->cv_path): ?>
            <p class="text-sm"><a href="<?php echo e($candidate->cvUrl()); ?>" target="_blank" class="text-violet-700 font-bold underline">السيرة الذاتية</a></p>
        <?php endif; ?>
        <?php if($candidate->notes): ?>
            <div class="mt-4 text-sm bg-gray-50 rounded-lg p-3 whitespace-pre-wrap"><?php echo e($candidate->notes); ?></div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold mb-4">التقديم على وظيفة</h2>
        <?php if($openingsForSelect->isNotEmpty()): ?>
            <form method="POST" action="<?php echo e(route('employee.hr.recruitment.applications.store')); ?>" class="flex flex-col sm:flex-row gap-3 items-end">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="hr_candidate_id" value="<?php echo e($candidate->id); ?>">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الوظيفة</label>
                    <select name="hr_job_opening_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <?php $__currentLoopData = $openingsForSelect; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($o->id); ?>"><?php echo e($o->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <input type="text" name="cover_letter" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="ملاحظة مع الطلب (اختياري)">
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-violet-600 text-white font-bold text-sm">تسجيل</button>
            </form>
        <?php else: ?>
            <p class="text-sm text-gray-500">لا توجد وظائف مفتوحة متاحة لهذا المرشح (أو سبق التقديم على كل المفتوحة).</p>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b font-bold">طلباته</div>
        <ul class="divide-y divide-gray-100 text-sm">
            <?php $__empty_1 = true; $__currentLoopData = $candidate->applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3 flex justify-between">
                    <span><?php echo e($app->opening?->title); ?></span>
                    <a href="<?php echo e(route('employee.hr.recruitment.applications.show', $app)); ?>" class="text-violet-700 font-bold"><?php echo e($app->status_label); ?></a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-gray-500">لا طلبات.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\candidates\show.blade.php ENDPATH**/ ?>