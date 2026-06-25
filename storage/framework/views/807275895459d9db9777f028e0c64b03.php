<?php
    $slide = $slide ?? null;
    $sourceType = old('source_type', $slide->source_type ?? \App\Models\HomepageSlider::SOURCE_COURSE);
?>

<div x-data="{ sourceType: <?php echo \Illuminate\Support\Js::from($sourceType)->toHtml() ?> }" class="space-y-6">
    <div>
        <span class="block text-sm font-semibold text-slate-700 mb-2">مصدر السلايدر <span class="text-rose-500">*</span></span>
        <div class="flex flex-wrap gap-3">
            <?php $__currentLoopData = $sourceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="inline-flex items-center gap-2 cursor-pointer rounded-xl border px-4 py-2.5 text-sm font-semibold transition"
                   :class="sourceType === <?php echo \Illuminate\Support\Js::from($value)->toHtml() ?> ? 'border-amber-400 bg-amber-50 text-amber-900' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                <input type="radio" name="source_type" value="<?php echo e($value); ?>" x-model="sourceType" class="text-amber-600 focus:ring-amber-500">
                <span><?php echo e($label); ?></span>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['source_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <p class="mt-2 text-xs text-slate-500">«من كورس» و«من مسار» يملآن العنوان والصورة تلقائياً من المحتوى — يمكنك تخصيصها أدناه.</p>
    </div>

    <template x-if="sourceType === 'course'">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">الكورس <span class="text-rose-500">*</span></label>
            <select name="advanced_course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white">
                <option value="">— اختر كورساً —</option>
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php if((string) old('advanced_course_id', $slide->advanced_course_id ?? '') === (string) $c->id): echo 'selected'; endif; ?>>
                    <?php echo e($c->title); ?><?php if($c->is_featured): ?> ★ <?php endif; ?>
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </template>

    <template x-if="sourceType === 'path'">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">المسار التعليمي <span class="text-rose-500">*</span></label>
            <select name="academic_year_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white">
                <option value="">— اختر مساراً —</option>
                <?php $__currentLoopData = $paths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id); ?>" <?php if((string) old('academic_year_id', $slide->academic_year_id ?? '') === (string) $p->id): echo 'selected'; endif; ?>>
                    <?php echo e($p->name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['academic_year_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </template>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">شارة صغيرة (Kicker)</label>
            <input type="text" name="kicker" value="<?php echo e(old('kicker', $slide->kicker ?? '')); ?>" maxlength="120" placeholder="مثال: محتوى مميّز" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان <span class="text-rose-500" x-show="sourceType === 'custom'">*</span></label>
            <input type="text" name="title" value="<?php echo e(old('title', $slide->title ?? '')); ?>" maxlength="255" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف الفرعي</label>
        <textarea name="subtitle" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl" placeholder="يُستخدم من الكورس/المسار إن تُرك فارغاً"><?php echo e(old('subtitle', $slide->subtitle ?? '')); ?></textarea>
    </div>

    <div class="rounded-2xl border border-dashed border-slate-200 p-4 bg-slate-50/80">
        <label class="block text-sm font-semibold text-slate-700 mb-2">صورة الخلفية (تجاوز اختياري)</label>
        <?php if($slide && $slide->publicImageUrl()): ?>
        <div class="mb-3 flex items-center gap-4">
            <img src="<?php echo e($slide->publicImageUrl()); ?>" alt="" class="h-20 w-36 object-cover rounded-xl border border-slate-200">
            <label class="inline-flex items-center gap-2 text-sm text-rose-700 cursor-pointer">
                <input type="hidden" name="remove_image" value="0">
                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-rose-600">
                حذف الصورة المخصصة
            </label>
        </div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-amber-50 file:text-amber-800">
        <p class="mt-1 text-xs text-slate-500">بدون رفع: تُستخدم صورة الكورس أو المسار. للمخصص يُفضّل رفع صورة 1920×1080 تقريباً.</p>
        <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">نص الزر الرئيسي</label>
            <input type="text" name="primary_label" value="<?php echo e(old('primary_label', $slide->primary_label ?? '')); ?>" maxlength="120" placeholder="ابدأ التعلّم الآن" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">رابط الزر الرئيسي</label>
            <input type="text" name="primary_url" value="<?php echo e(old('primary_url', $slide->primary_url ?? '')); ?>" maxlength="500" placeholder="/course/12 أو https://..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl" dir="ltr">
            <?php $__errorArgs = ['primary_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">نص الزر الثانوي</label>
            <input type="text" name="secondary_label" value="<?php echo e(old('secondary_label', $slide->secondary_label ?? '')); ?>" maxlength="120" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">رابط الزر الثانوي</label>
            <input type="text" name="secondary_url" value="<?php echo e(old('secondary_url', $slide->secondary_url ?? '')); ?>" maxlength="500" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl" dir="ltr">
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">ترتيب العرض</label>
            <input type="number" name="sort_order" value="<?php echo e(old('sort_order', $slide->sort_order ?? 0)); ?>" min="0" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">يبدأ في (اختياري)</label>
            <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', optional($slide?->starts_at)->format('Y-m-d\TH:i'))); ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">ينتهي في (اختياري)</label>
            <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', optional($slide?->ends_at)->format('Y-m-d\TH:i'))); ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
            <?php $__errorArgs = ['ends_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $slide->is_active ?? true)): echo 'checked'; endif; ?> class="rounded border-slate-300 text-emerald-600">
            <span class="text-sm font-semibold text-slate-700">نشط — يظهر في الصفحة الرئيسية</span>
        </label>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\homepage-sliders\_form.blade.php ENDPATH**/ ?>