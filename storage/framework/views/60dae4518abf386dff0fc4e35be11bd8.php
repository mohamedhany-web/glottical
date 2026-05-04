<?php $__env->startSection('title', 'إضافة كوبون جديد'); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <style>
        .coupon-label{display:block;font-size:.875rem;font-weight:700;color:#334155;margin-bottom:.375rem}
        .coupon-input{width:100%;border-radius:.9rem;border:1px solid #cbd5e1;background:#fff;padding:.66rem .88rem;color:#0f172a;transition:all .2s}
        .coupon-input:focus{outline:none;border-color:#8b5cf6;box-shadow:0 0 0 3px rgba(139,92,246,.14)}
        .coupon-hint{font-size:.75rem;color:#64748b;margin-top:.375rem}
        .coupon-error{font-size:.75rem;color:#dc2626;margin-top:.375rem}
        .coupon-card-title{font-size:.95rem;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:.5rem}
    </style>

    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('admin.coupons.index')); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 font-heading flex items-center gap-2">
                        <i class="fas fa-ticket-alt text-violet-600"></i>
                        إضافة كوبون جديد
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">تصميم واضح لإدخال بيانات الكوبون وتحديد نطاقه بدقة.</p>
                </div>
            </div>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-rose-200 bg-rose-50/90 px-5 py-4">
            <p class="text-sm font-bold text-rose-800 mb-2 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                يوجد أخطاء في البيانات المدخلة
            </p>
            <ul class="list-disc pr-5 text-sm text-rose-700 space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.coupons.store')); ?>" method="POST" class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-5 sm:p-8 space-y-8">
        <?php echo csrf_field(); ?>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6 space-y-5">
            <h2 class="coupon-card-title">
                <i class="fas fa-sliders-h text-violet-600"></i>
                البيانات الأساسية للكوبون
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="coupon-label">الكود <span class="text-red-500">*</span></label>
                    <input type="text" name="code" required value="<?php echo e(old('code')); ?>" class="coupon-input uppercase font-mono" placeholder="WELCOME10">
                    <p class="coupon-hint">يُحفظ تلقائيًا بأحرف كبيرة ويُستخدم في صفحة الدفع.</p>
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">العنوان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="<?php echo e(old('title')); ?>" class="coupon-input" placeholder="مثال: خصم ترحيبي">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">نوع الخصم <span class="text-red-500">*</span></label>
                    <select name="discount_type" required class="coupon-input">
                        <option value="percentage" <?php echo e(old('discount_type', 'percentage') === 'percentage' ? 'selected' : ''); ?>>نسبة مئوية</option>
                        <option value="fixed" <?php echo e(old('discount_type') === 'fixed' ? 'selected' : ''); ?>>مبلغ ثابت</option>
                    </select>
                    <?php $__errorArgs = ['discount_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">قيمة الخصم <span class="text-red-500">*</span></label>
                    <input type="number" name="discount_value" step="0.01" min="0" required value="<?php echo e(old('discount_value')); ?>" class="coupon-input" placeholder="0.00">
                    <?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">الحد الأدنى للطلب (ج.م)</label>
                    <input type="number" name="minimum_amount" step="0.01" min="0" value="<?php echo e(old('minimum_amount')); ?>" class="coupon-input" placeholder="اختياري">
                    <?php $__errorArgs = ['minimum_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">الحد الأقصى للخصم (ج.م)</label>
                    <input type="number" name="maximum_discount" step="0.01" min="0" value="<?php echo e(old('maximum_discount')); ?>" class="coupon-input" placeholder="اختياري">
                    <p class="coupon-hint">مهم عند اختيار خصم نسبة مئوية.</p>
                    <?php $__errorArgs = ['maximum_discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">الحد الأقصى لعدد الاستخدامات</label>
                    <input type="number" name="max_uses" min="1" value="<?php echo e(old('max_uses')); ?>" class="coupon-input" placeholder="اتركه فارغًا = غير محدود">
                    <?php $__errorArgs = ['max_uses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">الحد لكل مستخدم</label>
                    <input type="number" name="usage_limit_per_user" min="1" value="<?php echo e(old('usage_limit_per_user', 1)); ?>" class="coupon-input">
                    <?php $__errorArgs = ['usage_limit_per_user'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">تاريخ البداية</label>
                    <input type="date" name="valid_from" value="<?php echo e(old('valid_from')); ?>" class="coupon-input">
                    <?php $__errorArgs = ['valid_from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">تاريخ الانتهاء</label>
                    <input type="date" name="valid_until" value="<?php echo e(old('valid_until')); ?>" class="coupon-input">
                    <?php $__errorArgs = ['valid_until'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
            <label class="coupon-label">الوصف</label>
            <textarea name="description" rows="3" class="coupon-input" placeholder="وصف داخلي يساعد فريق الإدارة على تمييز الكوبون"><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5 space-y-5 bg-slate-50/70">
            <h2 class="coupon-card-title">
                <i class="fas fa-bullseye text-violet-500"></i>
                نطاق الكوبون
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="applicable_to" value="subscriptions" class="sr-only coupon-scope" <?php echo e(old('applicable_to') === 'subscriptions' ? 'checked' : ''); ?>>
                    <div class="rounded-2xl border border-slate-300 bg-white p-4 coupon-scope-card">
                        <p class="font-bold text-slate-900 mb-1">كوبون للباقات</p>
                        <p class="text-xs text-slate-500">يُطبَّق في صفحة دفع الاشتراك (Starter / Pro).</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="applicable_to" value="courses" class="sr-only coupon-scope" <?php echo e(old('applicable_to', 'courses') === 'courses' ? 'checked' : ''); ?>>
                    <div class="rounded-2xl border border-slate-300 bg-white p-4 coupon-scope-card">
                        <p class="font-bold text-slate-900 mb-1">كوبون للكورسات</p>
                        <p class="text-xs text-slate-500">يُطبَّق في شراء كورسات محددة.</p>
                    </div>
                </label>
            </div>
            <?php $__errorArgs = ['applicable_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <div id="courseScopeWrap">
                <label class="coupon-label">الكورسات (عند اختيار «كوبون للكورسات»)</label>
                <div class="max-h-56 overflow-y-auto rounded-xl border border-slate-200 p-3 space-y-2 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $courses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="applicable_course_ids[]" value="<?php echo e($c->id); ?>" <?php echo e(in_array($c->id, old('applicable_course_ids', []), true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-slate-800"><?php echo e($c->title); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500">لا توجد كورسات في النظام.</p>
                    <?php endif; ?>
                </div>
                <?php $__errorArgs = ['applicable_course_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 p-5 space-y-4 bg-amber-50/50">
            <h2 class="coupon-card-title">
                <i class="fas fa-user-tag text-amber-600"></i>
                كوبون تسويقي شخصي + عمولة
            </h2>
            <div>
                <label class="coupon-label">معرّفات المستخدمين المسموح لهم (اختياري)</label>
                <textarea name="applicable_user_ids_text" rows="2" class="coupon-input font-mono text-sm" placeholder="مثال: 12, 45 أو سطر لكل رقم"><?php echo e(old('applicable_user_ids_text')); ?></textarea>
                <p class="coupon-hint">إن تركتها فارغة يمكن لأي مستخدم يملك الكود استخدامه (وفق الشروط). للتسويق المستهدف: أدخل معرّف الطالب وأزل خيار «ظاهر للجميع».</p>
                <?php $__errorArgs = ['applicable_user_ids_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="coupon-label">مستفيد العمولة (معرّف مستخدم)</label>
                    <input type="number" name="beneficiary_user_id" min="1" value="<?php echo e(old('beneficiary_user_id')); ?>" class="coupon-input font-mono" placeholder="فارغ = بدون عمولة">
                    <?php $__errorArgs = ['beneficiary_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">نسبة العمولة %</label>
                    <input type="number" name="commission_percent" step="0.01" min="0" max="100" value="<?php echo e(old('commission_percent')); ?>" class="coupon-input">
                    <?php $__errorArgs = ['commission_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="coupon-label">احتساب العمولة من</label>
                    <select name="commission_on" class="coupon-input">
                        <option value="final_paid" <?php echo e(old('commission_on', 'final_paid') === 'final_paid' ? 'selected' : ''); ?>>المبلغ النهائي بعد الخصم</option>
                        <option value="original_price" <?php echo e(old('commission_on') === 'original_price' ? 'selected' : ''); ?>>السعر الأصلي قبل الخصم</option>
                    </select>
                    <?php $__errorArgs = ['commission_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="coupon-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <p class="coupon-hint">تُسجَّل العمولة عند اعتماد الطلب من الإدارة، ثم من «عمولات كوبونات التسويق» يمكن إنشاء مصروف تسويقي؛ عند اعتماد المصروف تُحدَّث الحالة إلى مسوّى.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
            <h2 class="coupon-card-title mb-4">
                <i class="fas fa-toggle-on text-violet-500"></i>
                حالة الكوبون
            </h2>
            <div class="flex flex-wrap gap-6">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                    <span class="text-sm font-medium text-slate-700">كوبون نشط</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" <?php echo e(old('is_public', true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                    <span class="text-sm font-medium text-slate-700">ظاهر للجميع (يمكن إدخال كوده من صفحة الدفع)</span>
                </label>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-200 sticky bottom-0 bg-white/95 backdrop-blur py-3">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 transition-all">
                <i class="fas fa-save"></i> حفظ الكوبون
            </button>
            <a href="<?php echo e(route('admin.coupons.index')); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-300 transition-colors">إلغاء</a>
        </div>
    </form>
</div>

<script>
    (function () {
        const radios = Array.from(document.querySelectorAll('.coupon-scope'));
        const wrap = document.getElementById('courseScopeWrap');

        function refresh() {
            const selected = radios.find(r => r.checked)?.value || 'courses';
            radios.forEach((r) => {
                const card = r.closest('label')?.querySelector('.coupon-scope-card');
                if (!card) return;
                if (r.checked) {
                    card.classList.add('border-violet-500', 'ring-2', 'ring-violet-200', 'bg-violet-50/60');
                    card.classList.remove('border-slate-300');
                } else {
                    card.classList.remove('border-violet-500', 'ring-2', 'ring-violet-200', 'bg-violet-50/60');
                    card.classList.add('border-slate-300');
                }
            });
            if (wrap) {
                const isCourses = selected === 'courses';
                wrap.classList.toggle('hidden', !isCourses);
                wrap.querySelectorAll('input[type="checkbox"]').forEach((c) => {
                    c.disabled = !isCourses;
                    if (!isCourses) c.checked = false;
                });
            }
        }

        radios.forEach(r => r.addEventListener('change', refresh));
        refresh();
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\coupons\create.blade.php ENDPATH**/ ?>