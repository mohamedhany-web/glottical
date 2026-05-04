<?php $__env->startSection('title', 'مرشح جديد'); ?>
<?php $__env->startSection('header', 'إضافة مرشح'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-6">
    <a href="<?php echo e(route('employee.hr.recruitment.candidates.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('employee.hr.recruitment.candidates.store')); ?>" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الاسم الكامل *</label>
                <input type="text" name="full_name" value="<?php echo e(old('full_name')); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">البريد *</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الهاتف</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">رابط معرض أعمال (اختياري)</label>
                <input type="url" name="portfolio_url" value="<?php echo e(old('portfolio_url')); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">مصدر التواصل *</label>
                <select name="source" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <?php $__currentLoopData = \App\Models\HrCandidate::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php echo e(old('source', \App\Models\HrCandidate::SOURCE_OTHER) === $k ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">السيرة (PDF / Word — حتى 40 ميجابايت)</label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx" class="w-full text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('notes')); ?></textarea>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-800 text-white font-bold text-sm">حفظ</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\recruitment\candidates\create.blade.php ENDPATH**/ ?>