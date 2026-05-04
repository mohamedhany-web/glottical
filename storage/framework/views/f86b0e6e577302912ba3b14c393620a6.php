

<?php $__env->startSection('title', 'تعديل Lead'); ?>
<?php $__env->startSection('header', 'تعديل عميل محتمل'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('employee.sales.leads.show', $salesLead)); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> العرض
        </a>
        <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">القائمة</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="<?php echo e(route('employee.sales.leads.update', $salesLead)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الاسم <span class="text-rose-600">*</span></label>
                <input type="text" name="name" value="<?php echo e(old('name', $salesLead->name)); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">البريد</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $salesLead->email)); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الهاتف</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $salesLead->phone)); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الشركة</label>
                <input type="text" name="company" value="<?php echo e(old('company', $salesLead->company)); ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">المصدر <span class="text-rose-600">*</span></label>
                    <select name="source" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        <?php $__currentLoopData = \App\Models\SalesLead::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php echo e(old('source', $salesLead->source) === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة <span class="text-rose-600">*</span></label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="<?php echo e(\App\Models\SalesLead::STATUS_NEW); ?>" <?php echo e(old('status', $salesLead->status) === \App\Models\SalesLead::STATUS_NEW ? 'selected' : ''); ?>>جديد</option>
                        <option value="<?php echo e(\App\Models\SalesLead::STATUS_CONTACTED); ?>" <?php echo e(old('status', $salesLead->status) === \App\Models\SalesLead::STATUS_CONTACTED ? 'selected' : ''); ?>>تم التواصل</option>
                        <option value="<?php echo e(\App\Models\SalesLead::STATUS_QUALIFIED); ?>" <?php echo e(old('status', $salesLead->status) === \App\Models\SalesLead::STATUS_QUALIFIED ? 'selected' : ''); ?>>مؤهل</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">كورس مهتم به</label>
                <select name="interested_advanced_course_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">—</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e((string) old('interested_advanced_course_id', $salesLead->interested_advanced_course_id) === (string) $c->id ? 'selected' : ''); ?>><?php echo e($c->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500"><?php echo e(old('notes', $salesLead->notes)); ?></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">حفظ</button>
                <a href="<?php echo e(route('employee.sales.leads.show', $salesLead)); ?>" class="px-5 py-2.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\sales\leads\edit.blade.php ENDPATH**/ ?>