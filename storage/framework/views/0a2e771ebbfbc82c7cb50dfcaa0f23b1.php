<?php $__env->startSection('title', 'تعديل الكوبون'); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="<?php echo e(route('admin.coupons.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                <i class="fas fa-edit text-amber-500 ml-2"></i>تعديل الكوبون
            </h1>
            <p class="text-sm text-slate-500 mt-1 font-mono"><?php echo e($coupon->code); ?></p>
        </div>
    </div>

    <form action="<?php echo e(route('admin.coupons.update', $coupon)); ?>" method="POST" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-6 shadow-sm">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الكود <span class="text-red-500">*</span></label>
                <input type="text" name="code" required value="<?php echo e(old('code', $coupon->code)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-mono">
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">العنوان <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="<?php echo e(old('title', $coupon->title ?? $coupon->name)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">نوع الخصم <span class="text-red-500">*</span></label>
                <select name="discount_type" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="percentage" <?php echo e(old('discount_type', $coupon->discount_type) === 'percentage' ? 'selected' : ''); ?>>نسبة مئوية</option>
                    <option value="fixed" <?php echo e(old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : ''); ?>>مبلغ ثابت</option>
                </select>
                <?php $__errorArgs = ['discount_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">قيمة الخصم <span class="text-red-500">*</span></label>
                <input type="number" name="discount_value" step="0.01" min="0" required value="<?php echo e(old('discount_value', $coupon->discount_value)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأدنى للطلب (ج.م)</label>
                <input type="number" name="minimum_amount" step="0.01" min="0" value="<?php echo e(old('minimum_amount', $coupon->minimum_amount)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['minimum_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى للخصم (ج.م)</label>
                <input type="number" name="maximum_discount" step="0.01" min="0" value="<?php echo e(old('maximum_discount', $coupon->maximum_discount)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['maximum_discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى لعدد الاستخدامات</label>
                <input type="number" name="max_uses" min="1" value="<?php echo e(old('max_uses', $coupon->usage_limit)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="فارغ = غير محدود">
                <?php $__errorArgs = ['max_uses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد لكل مستخدم</label>
                <input type="number" name="usage_limit_per_user" min="1" value="<?php echo e(old('usage_limit_per_user', $coupon->usage_limit_per_user ?? 1)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['usage_limit_per_user'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">تاريخ البداية</label>
                <input type="date" name="valid_from" value="<?php echo e(old('valid_from', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['valid_from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">تاريخ الانتهاء</label>
                <input type="date" name="valid_until" value="<?php echo e(old('valid_until', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['valid_until'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"><?php echo e(old('description', $coupon->description)); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php
            $oldCourseIds = old('applicable_course_ids', $coupon->applicable_course_ids ?? []);
            $oldUserIdsText = old('applicable_user_ids_text', is_array($coupon->applicable_user_ids) && count($coupon->applicable_user_ids) ? implode(', ', $coupon->applicable_user_ids) : '');
        ?>

        <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-5 space-y-4 bg-slate-50/80 dark:bg-slate-900/30">
            <h2 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2"><i class="fas fa-bullseye text-violet-500"></i> نطاق التطبيق (الكورسات)</h2>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">ينطبق على <span class="text-red-500">*</span></label>
                <select name="applicable_to" required class="w-full max-w-xl rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="all" <?php echo e(old('applicable_to', $coupon->applicable_to ?? 'all') === 'all' ? 'selected' : ''); ?>>جميع الكورسات (صفحة دفع الكورس)</option>
                    <option value="courses" <?php echo e(old('applicable_to', $coupon->applicable_to ?? 'all') === 'courses' ? 'selected' : ''); ?>>كورسات محددة فقط</option>
                    <option value="specific" <?php echo e(old('applicable_to', $coupon->applicable_to ?? 'all') === 'specific' ? 'selected' : ''); ?>>كورسات محددة + تقييد مستخدمين (اختياري بالأسفل)</option>
                    <option value="subscriptions" <?php echo e(old('applicable_to', $coupon->applicable_to ?? 'all') === 'subscriptions' ? 'selected' : ''); ?>>الاشتراكات فقط (لا يُطبَّق على دفع الكورس)</option>
                </select>
                <?php $__errorArgs = ['applicable_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">الكورسات (عند اختيار «محددة»)</label>
                <div class="max-h-56 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-600 p-3 space-y-2 bg-white dark:bg-slate-800">
                    <?php $__empty_1 = true; $__currentLoopData = $courses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="applicable_course_ids[]" value="<?php echo e($c->id); ?>" <?php echo e(in_array($c->id, array_map('intval', (array) $oldCourseIds), true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-slate-800 dark:text-slate-200"><?php echo e($c->title); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500">لا توجد كورسات في النظام.</p>
                    <?php endif; ?>
                </div>
                <?php $__errorArgs = ['applicable_course_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="rounded-xl border border-amber-200 dark:border-amber-900/50 p-5 space-y-4 bg-amber-50/50 dark:bg-amber-950/20">
            <h2 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2"><i class="fas fa-user-tag text-amber-600"></i> كوبون تسويقي شخصي + عمولة</h2>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">معرّفات المستخدمين المسموح لهم (اختياري)</label>
                <textarea name="applicable_user_ids_text" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-mono text-sm" placeholder="مثال: 12, 45"><?php echo e($oldUserIdsText); ?></textarea>
                <?php $__errorArgs = ['applicable_user_ids_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">مستفيد العمولة (معرّف مستخدم)</label>
                    <input type="number" name="beneficiary_user_id" min="1" value="<?php echo e(old('beneficiary_user_id', $coupon->beneficiary_user_id)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-mono" placeholder="فارغ = بدون عمولة">
                    <?php $__errorArgs = ['beneficiary_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">نسبة العمولة %</label>
                    <input type="number" name="commission_percent" step="0.01" min="0" max="100" value="<?php echo e(old('commission_percent', $coupon->commission_percent)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <?php $__errorArgs = ['commission_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">احتساب العمولة من</label>
                    <select name="commission_on" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="final_paid" <?php echo e(old('commission_on', $coupon->commission_on ?? 'final_paid') === 'final_paid' ? 'selected' : ''); ?>>المبلغ النهائي بعد الخصم</option>
                        <option value="original_price" <?php echo e(old('commission_on', $coupon->commission_on ?? 'final_paid') === 'original_price' ? 'selected' : ''); ?>>السعر الأصلي قبل الخصم</option>
                    </select>
                    <?php $__errorArgs = ['commission_on'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-6">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $coupon->is_active) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">كوبون نشط</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_public" value="1" <?php echo e(old('is_public', $coupon->is_public ?? true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">ظاهر للجميع</span>
            </label>
        </div>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-200 dark:border-slate-700">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 transition-all">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="<?php echo e(route('admin.coupons.show', $coupon)); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 dark:hover:bg-slate-600">عرض التفاصيل</a>
            <a href="<?php echo e(route('admin.coupons.index')); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\coupons\edit.blade.php ENDPATH**/ ?>