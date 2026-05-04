

<?php $__env->startSection('title', 'تعديل مرشح'); ?>
<?php $__env->startSection('header', 'تعديل بيانات مرشح'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-6">
    <a href="<?php echo e(route('employee.hr.recruitment.candidates.show', $candidate)); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> العرض</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('employee.hr.recruitment.candidates.update', $candidate)); ?>" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الاسم الكامل *</label>
                <input type="text" name="full_name" value="<?php echo e(old('full_name', $candidate->full_name)); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">البريد *</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $candidate->email)); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الهاتف</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $candidate->phone)); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">رابط معرض أعمال</label>
                <input type="url" name="portfolio_url" value="<?php echo e(old('portfolio_url', $candidate->portfolio_url)); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المصدر *</label>
                <select name="source" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <?php $__currentLoopData = \App\Models\HrCandidate::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php echo e(old('source', $candidate->source) === $k ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">استبدال السيرة (اختياري)</label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx" class="w-full text-sm">
                <?php if($candidate->cv_path): ?>
                    <p class="text-xs text-gray-500 mt-1">الملف الحالي: <a href="<?php echo e($candidate->cvUrl()); ?>" target="_blank" class="text-violet-700 underline">تحميل</a></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('notes', $candidate->notes)); ?></textarea>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-800 text-white font-bold text-sm">حفظ</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\candidates\edit.blade.php ENDPATH**/ ?>