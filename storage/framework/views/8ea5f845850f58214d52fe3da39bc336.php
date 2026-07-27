

<?php $__env->startSection('title', 'مراجعة الملف التعريفي - ' . ($personal_branding->user->name ?? '')); ?>
<?php $__env->startSection('header', 'مراجعة الملف التعريفي'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <nav class="text-sm text-slate-500 mb-2">
        <a href="<?php echo e(route('admin.personal-branding.index')); ?>" class="text-sky-600 hover:text-sky-700">التسويق الشخصي</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700"><?php echo e($personal_branding->user->name ?? 'مدرب'); ?></span>
    </nav>

    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-900">الملف التعريفي — <?php echo e($personal_branding->user->name); ?></h1>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.personal-branding.edit', $personal_branding)); ?>" class="inline-flex items-center gap-1.5 rounded-xl bg-sky-600 text-white px-4 py-2 text-sm font-bold hover:bg-sky-700 shadow-sm">
                    <i class="fas fa-pen text-xs"></i>
                    تعديل الملف
                </a>
                <form method="POST" action="<?php echo e(route('admin.personal-branding.destroy', $personal_branding)); ?>" class="inline" onsubmit="return confirm('حذف الملف التعريفي بالكامل؟ سيُزال من الموقع ويمكن للمدرب إنشاء ملف جديد لاحقاً.');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 px-4 py-2 text-sm font-bold hover:bg-rose-100">
                        <i class="fas fa-trash text-xs"></i>
                        حذف الملف
                    </button>
                </form>
                <span class="rounded-full px-3 py-1 text-sm font-semibold
                    <?php if($personal_branding->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?> bg-emerald-100 text-emerald-700
                    <?php elseif($personal_branding->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): ?> bg-amber-100 text-amber-700
                    <?php elseif($personal_branding->status === \App\Models\InstructorProfile::STATUS_REJECTED): ?> bg-rose-100 text-rose-700
                    <?php else: ?> bg-slate-100 text-slate-600
                    <?php endif; ?>">
                    <?php echo e(\App\Models\InstructorProfile::statusLabel($personal_branding->status)); ?>

                </span>
            </div>
        </div>
        <div class="p-5 sm:p-8 space-y-6">
            <div class="flex flex-wrap gap-4 items-start">
                <?php if($personal_branding->photo_path): ?>
                    <?php $photoPath = str_replace('\\', '/', trim($personal_branding->photo_path)); ?>
                    <div class="w-28 h-28 rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 relative">
                        <img src="<?php echo e(storage_asset($photoPath)); ?>" alt="صورة المدرب" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-4xl"></i></div>
                    </div>
                <?php else: ?>
                    <div class="w-28 h-28 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-4xl"></i></div>
                <?php endif; ?>
                <div>
                    <p class="text-slate-500 text-sm">البريد: <?php echo e($personal_branding->user->email ?? '—'); ?></p>
                    <p class="text-slate-500 text-sm mt-1">تاريخ التقديم: <?php echo e($personal_branding->submitted_at ? $personal_branding->submitted_at->format('Y-m-d H:i') : '—'); ?></p>
                    <?php if($personal_branding->reviewed_at): ?>
                        <p class="text-slate-500 text-sm">تمت المراجعة: <?php echo e($personal_branding->reviewed_at->format('Y-m-d H:i')); ?> — <?php echo e($personal_branding->reviewedByUser->name ?? ''); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-1">العنوان التعريفي</h3>
                <p class="text-slate-900"><?php echo e($personal_branding->headline ?? '—'); ?></p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-1">النبذة</h3>
                <p class="text-slate-900 whitespace-pre-line"><?php echo e($personal_branding->bio ?? '—'); ?></p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">الخبرات في المجال</h3>
                <?php if(count($personal_branding->experience_list) > 0): ?>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $personal_branding->experience_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex gap-2 text-slate-900">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-bold">•</span>
                        <span><?php echo e($item); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php else: ?>
                <p class="text-slate-900"><?php echo e($personal_branding->experience ?: '—'); ?></p>
                <?php endif; ?>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">المهارات</h3>
                <?php if(count($personal_branding->skills_list) > 0): ?>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $personal_branding->skills_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-flex items-center rounded-xl bg-sky-50 text-sky-800 px-3 py-1.5 text-sm font-medium border border-sky-200"><?php echo e($skill); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <p class="text-slate-900"><?php echo e($personal_branding->skills ?: '—'); ?></p>
                <?php endif; ?>
            </div>
            <?php if($personal_branding->rejection_reason): ?>
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200">
                <h3 class="text-sm font-semibold text-rose-700 mb-1">سبب الرفض</h3>
                <p class="text-rose-900"><?php echo e($personal_branding->rejection_reason); ?></p>
            </div>
            <?php endif; ?>

            <?php $consultDefaults = \App\Models\ConsultationSetting::current(); ?>
            <div class="p-5 rounded-2xl bg-emerald-50/90 border border-emerald-200 space-y-4">
                <div>
                    <h3 class="text-base font-bold text-emerald-900">استشارة مدفوعة (جنيه مصري)</h3>
                    <p class="text-xs text-emerald-800/90 mt-1">حدّد سعراً ومدة خاصة بهذا المدرب. إن تركت حقل السعر فارغاً يُستخدم السعر الافتراضي للمنصة حالياً: <strong><?php echo e(number_format($consultDefaults->default_price, 2)); ?> ج.م</strong> — مدة افتراضية: <strong><?php echo e((int) $consultDefaults->default_duration_minutes); ?> دقيقة</strong>.</p>
                    <p class="text-xs text-emerald-800/90 mt-1">السعر الظاهر للزوار الآن لهذا المدرب: <strong class="text-base"><?php echo e(number_format($personal_branding->effectiveConsultationPriceEgp(), 2)); ?> ج.م</strong> — المدة: <strong><?php echo e($personal_branding->effectiveConsultationDurationMinutes()); ?> دقيقة</strong></p>
                </div>
                <form method="POST" action="<?php echo e(route('admin.personal-branding.consultation-pricing', $personal_branding)); ?>" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-semibold text-emerald-900 mb-1">سعر الاستشارة (ج.م) — اختياري</label>
                        <input type="number" step="0.01" name="consultation_price_egp" value="<?php echo e(old('consultation_price_egp', $personal_branding->consultation_price_egp)); ?>" class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-sm bg-white" placeholder="فارغ = الافتراضي">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-emerald-900 mb-1">مدة الجلسة (دقيقة) — اختياري</label>
                        <input type="number" name="consultation_duration_minutes" value="<?php echo e(old('consultation_duration_minutes', $personal_branding->consultation_duration_minutes)); ?>" min="15" max="480" class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-sm bg-white" placeholder="فارغ = الافتراضي">
                    </div>
                    <div class="sm:col-span-2 flex flex-wrap gap-2">
                        <button type="submit" class="rounded-xl bg-emerald-700 text-white px-5 py-2.5 text-sm font-bold hover:bg-emerald-800">حفظ سعر الاستشارة</button>
                        <p class="text-[11px] text-emerald-800 self-center">تفعيل خدمة الاستشارات العامة من: إدارة المنصة ← استشارات المدربين.</p>
                    </div>
                </form>
            </div>
        </div>
        <div class="px-5 py-6 sm:px-8 border-t border-slate-200 bg-slate-50/80">
            <h3 class="text-sm font-bold text-slate-700 mb-3">إجراءات المراجعة</h3>
            <?php if($personal_branding->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): ?>
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <form method="POST" action="<?php echo e(route('admin.personal-branding.approve', $personal_branding)); ?>" class="inline" onsubmit="return confirm('تأكيد الموافقة على هذا الملف ونشره للطلاب في الموقع؟');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="rounded-2xl bg-emerald-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-emerald-700 shadow-sm">موافقة ونشر على الموقع</button>
                        </form>
                    </div>
                    
                    <form method="POST" action="<?php echo e(route('admin.personal-branding.reject', $personal_branding)); ?>" class="max-w-xl space-y-2 rounded-2xl border border-rose-200 bg-rose-50/50 p-4" onsubmit="return confirm('تأكيد رفض هذا الملف التعريفي؟ يمكن للمدرب تعديله وإعادة الإرسال.');">
                        <?php echo csrf_field(); ?>
                        <p class="text-xs font-semibold text-rose-800">رفض الملف</p>
                        <label class="block text-xs font-semibold text-slate-600">سبب الرفض (اختياري)</label>
                        <textarea name="rejection_reason" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white" placeholder="اكتب سبب الرفض للمدرب..."><?php echo e(old('rejection_reason')); ?></textarea>
                        <?php $__errorArgs = ['rejection_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-rose-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <button type="submit" class="rounded-2xl bg-rose-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-rose-700">تأكيد الرفض</button>
                    </form>
                </div>
            <?php elseif(in_array($personal_branding->status, [\App\Models\InstructorProfile::STATUS_APPROVED, \App\Models\InstructorProfile::STATUS_REJECTED], true)): ?>
                <form method="POST" action="<?php echo e(route('admin.personal-branding.send-back', $personal_branding)); ?>" class="inline" onsubmit="return confirm('إعادة هذا الملف إلى قيد المراجعة؟');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="rounded-2xl bg-amber-100 text-amber-800 px-5 py-2.5 text-sm font-semibold hover:bg-amber-200 border border-amber-200">إعادة للمراجعة</button>
                </form>
            <?php else: ?>
                <p class="text-slate-600 text-sm">هذا الملف ما زال <strong>مسودة</strong> ولم يُرسل من المدرب للمراجعة بعد. أزرار الموافقة والرفض تظهر عندما تكون الحالة <strong>قيد المراجعة</strong> (بعد ضغط المدرب على «إرسال للمراجعة»).</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\marketing\personal-branding\show.blade.php ENDPATH**/ ?>