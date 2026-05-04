

<?php $__env->startSection('title', 'تعديل الملف التعريفي - ' . ($personal_branding->user->name ?? '')); ?>
<?php $__env->startSection('header', 'تعديل الملف التعريفي'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-4xl space-y-6">
    <nav class="text-sm text-slate-500">
        <a href="<?php echo e(route('admin.personal-branding.index')); ?>" class="text-sky-600 hover:text-sky-700">التسويق الشخصي</a>
        <span class="mx-1">/</span>
        <a href="<?php echo e(route('admin.personal-branding.show', $personal_branding)); ?>" class="text-sky-600 hover:text-sky-700"><?php echo e($personal_branding->user->name ?? 'مدرب'); ?></a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">تعديل</span>
    </nav>

    <?php if($errors->any()): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">تعديل ملف <?php echo e($personal_branding->user->name ?? 'المدرب'); ?></h1>
            <p class="text-slate-500 text-sm mt-1"><?php echo e($personal_branding->user->email ?? ''); ?></p>
            <span class="inline-block mt-3 rounded-full px-3 py-1 text-xs font-semibold
                <?php if($personal_branding->status == 'approved'): ?> bg-emerald-100 text-emerald-700
                <?php elseif($personal_branding->status == 'pending_review'): ?> bg-amber-100 text-amber-700
                <?php elseif($personal_branding->status == 'rejected'): ?> bg-rose-100 text-rose-700
                <?php else: ?> bg-slate-100 text-slate-600
                <?php endif; ?>">
                <?php echo e(\App\Models\InstructorProfile::statusLabel($personal_branding->status)); ?>

            </span>
            <p class="text-xs text-slate-500 mt-3 leading-relaxed">تعديل المحتوى لا يغيّر الحالة تلقائياً. للموافقة أو الرفض استخدم صفحة المراجعة.</p>
        </div>

        <form method="POST" action="<?php echo e(route('admin.personal-branding.update', $personal_branding)); ?>" enctype="multipart/form-data" class="p-5 sm:p-8 space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">صورة الملف</label>
                <?php if($personal_branding->photo_path): ?>
                    <div class="w-24 h-24 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 mb-2">
                        <img src="<?php echo e($personal_branding->photo_url); ?>" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'">
                    </div>
                <?php endif; ?>
                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100">
                <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان التعريفي</label>
                <input type="text" name="headline" value="<?php echo e(old('headline', $personal_branding->headline)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                <?php $__errorArgs = ['headline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">النبذة</label>
                <textarea name="bio" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"><?php echo e(old('bio', $personal_branding->bio)); ?></textarea>
                <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الخبرات في المجال</label>
                <textarea name="experience" rows="10" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"><?php echo e(old('experience', $personal_branding->experience)); ?></textarea>
                <?php $__errorArgs = ['experience'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">المهارات</label>
                <p class="text-xs text-slate-500 mb-2">سطر لكل مهارة أو مفصولة بفاصلة.</p>
                <textarea name="skills" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"><?php echo e(old('skills', $personal_branding->skills)); ?></textarea>
                <?php $__errorArgs = ['skills'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 sm:p-5 bg-slate-50/80">
                <h3 class="text-sm font-bold text-slate-800 mb-1">الاستشارة (اختياري)</h3>
                <p class="text-xs text-slate-500 mb-4">فارغ = الافتراضي من إعدادات المنصة.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">السعر (ج.م)</label>
                        <input type="number" step="0.01" min="0" max="999999.99" name="consultation_price_egp" value="<?php echo e(old('consultation_price_egp', $personal_branding->consultation_price_egp)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" dir="ltr">
                        <?php $__errorArgs = ['consultation_price_egp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">المدة (دقيقة)</label>
                        <input type="number" min="15" max="480" name="consultation_duration_minutes" value="<?php echo e(old('consultation_duration_minutes', $personal_branding->consultation_duration_minutes)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" dir="ltr">
                        <?php $__errorArgs = ['consultation_duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-sky-600 text-white px-6 py-2.5 text-sm font-bold hover:bg-sky-700">حفظ التعديلات</button>
                <a href="<?php echo e(route('admin.personal-branding.show', $personal_branding)); ?>" class="rounded-xl border border-slate-200 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\marketing\personal-branding\edit.blade.php ENDPATH**/ ?>