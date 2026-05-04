<?php
    $academy = $academy ?? null;
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1">اسم الأكاديمية <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="<?php echo e(old('name', $academy->name ?? '')); ?>" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1">الاسم القانوني / السجل التجاري (اختياري)</label>
        <input type="text" name="legal_name" value="<?php echo e(old('legal_name', $academy->legal_name ?? '')); ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">المدينة</label>
        <input type="text" name="city" value="<?php echo e(old('city', $academy->city ?? '')); ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">حالة العميل</label>
        <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            <?php $__currentLoopData = \App\Models\HiringAcademy::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(old('status', $academy->status ?? 'active') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1">العنوان</label>
        <input type="text" name="address" value="<?php echo e(old('address', $academy->address ?? '')); ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">اسم جهة الاتصال</label>
        <input type="text" name="contact_name" value="<?php echo e(old('contact_name', $academy->contact_name ?? '')); ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">البريد</label>
        <input type="email" name="contact_email" value="<?php echo e(old('contact_email', $academy->contact_email ?? '')); ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">الهاتف</label>
        <input type="text" name="contact_phone" value="<?php echo e(old('contact_phone', $academy->contact_phone ?? '')); ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">الموقع</label>
        <input type="text" name="website" value="<?php echo e(old('website', $academy->website ?? '')); ?>" placeholder="https://" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">الرقم الضريبي / تعريف</label>
        <input type="text" name="tax_id" value="<?php echo e(old('tax_id', $academy->tax_id ?? '')); ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات تجارية / تعاقد (داخلية)</label>
        <textarea name="commercial_notes" rows="3" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white"><?php echo e(old('commercial_notes', $academy->commercial_notes ?? '')); ?></textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات داخلية للفريق</label>
        <textarea name="internal_notes" rows="3" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white"><?php echo e(old('internal_notes', $academy->internal_notes ?? '')); ?></textarea>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\hiring-academies\_form.blade.php ENDPATH**/ ?>