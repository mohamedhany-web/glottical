

<?php $__env->startSection('title', $opening->title); ?>
<?php $__env->startSection('header', 'وظيفة شاغرة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap justify-between gap-3">
        <a href="<?php echo e(route('employee.hr.recruitment.openings.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.hr.recruitment.openings.edit', $opening)); ?>" class="px-3 py-2 rounded-lg bg-gray-800 text-white text-sm font-bold">تعديل</a>
            <form method="POST" action="<?php echo e(route('employee.hr.recruitment.openings.destroy', $opening)); ?>" onsubmit="return confirm('حذف الوظيفة وجميع الطلبات المرتبطة؟');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-3 py-2 rounded-lg bg-rose-100 text-rose-800 text-sm font-bold">حذف</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-3">
        <h1 class="text-2xl font-black text-gray-900"><?php echo e($opening->title); ?></h1>
        <div class="flex flex-wrap gap-2 text-sm">
            <span class="px-2 py-0.5 rounded-full bg-violet-100 text-violet-900 font-bold"><?php echo e($opening->status_label); ?></span>
            <span class="text-gray-600"><?php echo e($opening->employment_type_label); ?></span>
            <?php if($opening->department): ?><span class="text-gray-500">· <?php echo e($opening->department); ?></span><?php endif; ?>
            <?php if($opening->closes_at): ?><span class="text-gray-500">· إغلاق: <?php echo e($opening->closes_at->format('Y-m-d')); ?></span><?php endif; ?>
        </div>
        <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap border-t border-gray-100 pt-4"><?php echo e($opening->description); ?></div>
        <?php if($opening->requirements): ?>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-1">المتطلبات</p>
                <div class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-3"><?php echo e($opening->requirements); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">ربط مرشح بطلب جديد</h2>
        <?php if($opening->isAcceptingApplications() && $candidatesForSelect->isNotEmpty()): ?>
            <form method="POST" action="<?php echo e(route('employee.hr.recruitment.applications.store')); ?>" class="flex flex-col sm:flex-row gap-3 items-end">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="hr_job_opening_id" value="<?php echo e($opening->id); ?>">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">المرشح</label>
                    <select name="hr_candidate_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <?php $__currentLoopData = $candidatesForSelect; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->full_name); ?> — <?php echo e($c->email); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">رسالة / تغطية (اختياري)</label>
                    <textarea name="cover_letter" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="اختياري"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-violet-600 text-white font-bold text-sm">تسجيل الطلب</button>
            </form>
        <?php elseif(!$opening->isAcceptingApplications()): ?>
            <p class="text-sm text-amber-800">الوظيفة غير مفتوحة للتقديم (الحالة أو تاريخ الإغلاق).</p>
        <?php else: ?>
            <p class="text-sm text-gray-500">جميع المرشحين مرتبطون بالفعل أو لا يوجد مرشحون في النظام. أضف مرشحاً من قائمة المرشحين.</p>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-bold">طلبات التوظيف (<?php echo e($opening->applications->count()); ?>)</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-2">المرشح</th>
                        <th class="text-right px-4 py-2">الحالة</th>
                        <th class="text-right px-4 py-2">التقديم</th>
                        <th class="text-right px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $opening->applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo e($app->candidate?->full_name); ?></td>
                        <td class="px-4 py-2"><?php echo e($app->status_label); ?></td>
                        <td class="px-4 py-2 text-gray-500 whitespace-nowrap"><?php echo e($app->applied_at?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-2"><a href="<?php echo e(route('employee.hr.recruitment.applications.show', $app)); ?>" class="text-violet-700 font-bold">إدارة</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">لا طلبات بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\openings\show.blade.php ENDPATH**/ ?>