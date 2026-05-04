<?php $opportunity = $opportunity ?? null; ?>
<?php if(isset($hiringAcademies) && $hiringAcademies->count()): ?>
<div class="rounded-xl border border-indigo-100 bg-indigo-50/50 dark:bg-indigo-950/20 dark:border-indigo-900 p-4 mb-2">
    <label class="block text-xs font-bold text-indigo-800 dark:text-indigo-200 mb-1"><?php echo e(__('admin.academy_opportunity_hiring_academy')); ?></label>
    <select name="hiring_academy_id" class="w-full px-3 py-2 rounded-lg border border-indigo-200 dark:border-indigo-800 dark:bg-slate-800 dark:text-white">
        <option value="">— بدون ربط بأكاديمية توظيف (إدخال يدوي لاسم الجهة) —</option>
        <?php $__currentLoopData = $hiringAcademies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ha): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($ha->id); ?>" <?php if(old('hiring_academy_id', $opportunity?->hiring_academy_id) == $ha->id): echo 'selected'; endif; ?>><?php echo e($ha->name); ?> <?php if($ha->city): ?> — <?php echo e($ha->city); ?> <?php endif; ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <p class="text-[11px] text-indigo-700 dark:text-indigo-300 mt-2 leading-relaxed">عند الربط، يُحدَّث حقل «اسم الجهة» تلقائياً عند الحفظ ليطابق اسم أكاديمية التوظيف (للعرض للمعلمين).</p>
</div>
<?php endif; ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">اسم الجهة / الأكاديمية</label>
        <input type="text" name="organization_name" value="<?php echo e(old('organization_name', $opportunity?->organization_name ?? '')); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
        <?php $__errorArgs = ['organization_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">عنوان الفرصة</label>
        <input type="text" name="title" value="<?php echo e(old('title', $opportunity?->title ?? '')); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">التخصص</label>
        <input type="text" name="specialization" value="<?php echo e(old('specialization', $opportunity?->specialization ?? '')); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">المدينة</label>
        <input type="text" name="city" value="<?php echo e(old('city', $opportunity?->city ?? '')); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">نمط العمل</label>
        <select name="work_mode" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            <?php $__currentLoopData = ['remote' => 'عن بُعد', 'onsite' => 'حضوري', 'hybrid' => 'هجين']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php echo e(old('work_mode', $opportunity?->work_mode ?? 'remote') === $k ? 'selected' : ''); ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
        <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            <?php $__currentLoopData = ['active', 'paused', 'closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php echo e(old('status', $opportunity?->status ?? 'active') === $k ? 'selected' : ''); ?>><?php echo e($k); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">آخر موعد للتقديم</label>
        <input type="date" name="apply_until" value="<?php echo e(old('apply_until', optional($opportunity?->apply_until)->format('Y-m-d'))); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
    </div>
    <div class="flex items-center gap-2 pt-6">
        <input type="hidden" name="is_featured" value="0">
        <input type="checkbox" name="is_featured" value="1" <?php echo e((int) old('is_featured', $opportunity?->is_featured ?? 0) === 1 ? 'checked' : ''); ?>>
        <span class="text-sm text-slate-700">فرصة مميزة</span>
    </div>
</div>
<div>
    <label class="block text-xs font-semibold text-slate-600 mb-1">المتطلبات</label>
    <textarea name="requirements" rows="6" class="w-full px-3 py-2 rounded-lg border border-slate-200"><?php echo e(old('requirements', $opportunity?->requirements ?? '')); ?></textarea>
</div>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\academy-opportunities\form.blade.php ENDPATH**/ ?>