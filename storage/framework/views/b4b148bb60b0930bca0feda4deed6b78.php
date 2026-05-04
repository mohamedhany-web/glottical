

<?php $__env->startSection('title', 'وظيفة جديدة'); ?>
<?php $__env->startSection('header', 'إضافة وظيفة شاغرة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6">
    <a href="<?php echo e(route('employee.hr.recruitment.openings.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('employee.hr.recruitment.openings.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المسمى الوظيفي *</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">القسم</label>
                <input type="text" name="department" value="<?php echo e(old('department')); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">وصف الوظيفة *</label>
                <textarea name="description" rows="6" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('description')); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المتطلبات</label>
                <textarea name="requirements" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('requirements')); ?></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">نوع التوظيف *</label>
                    <select name="employment_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <?php $__currentLoopData = \App\Models\HrJobOpening::employmentTypeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php echo e(old('employment_type', \App\Models\HrJobOpening::EMP_FULL_TIME) === $k ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة *</label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <?php $__currentLoopData = \App\Models\HrJobOpening::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php echo e(old('status', \App\Models\HrJobOpening::STATUS_DRAFT) === $k ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">إغلاق التقديم (اختياري)</label>
                <input type="date" name="closes_at" value="<?php echo e(old('closes_at')); ?>" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-violet-600 text-white font-bold text-sm">حفظ</button>
                <a href="<?php echo e(route('employee.hr.recruitment.openings.index')); ?>" class="px-5 py-2.5 rounded-lg bg-gray-200 text-gray-800 font-semibold text-sm">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\openings\create.blade.php ENDPATH**/ ?>