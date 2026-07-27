

<?php $__env->startSection('title', 'إضافة محفظة جديدة - ' . config('app.name')); ?>
<?php $__env->startSection('page_title', 'إضافة محفظة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · المحافظ · إنشاء</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إضافة محفظة جديدة</h2>
            <p class="mt-1 text-sm text-muted">إعداد محفظة للدفع أو التحويل (فودافون كاش، إنستا باي، تحويل بنكي، إلخ).</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.wallets.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للمحافظ
            </a>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">بيانات المحفظة</h3>
            <p class="mt-0.5 text-xs text-muted">أدخل معلومات المحفظة والرصيد الابتدائي</p>
        </div>

        <form action="<?php echo e(route('admin.wallets.store')); ?>" method="POST" class="space-y-6 p-4 sm:p-5">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="name">اسم المحفظة <span class="text-rose-500">*</span></label>
                    <input id="name" type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="255" class="<?php echo e($fieldClass); ?>" placeholder="مثال: فودافون كاش - 01000000000">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="wallet-type">نوع المحفظة <span class="text-rose-500">*</span></label>
                    <select name="type" id="wallet-type" required class="<?php echo e($fieldClass); ?>">
                        <option value="">اختر النوع</option>
                        <option value="vodafone_cash" <?php echo e(old('type') == 'vodafone_cash' ? 'selected' : ''); ?>>فودافون كاش</option>
                        <option value="instapay" <?php echo e(old('type') == 'instapay' ? 'selected' : ''); ?>>إنستا باي</option>
                        <option value="bank_transfer" <?php echo e(old('type') == 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                        <option value="cash" <?php echo e(old('type') == 'cash' ? 'selected' : ''); ?>>كاش</option>
                        <option value="other" <?php echo e(old('type') == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="account_number">رقم الحساب / المحفظة</label>
                    <input id="account_number" type="text" name="account_number" value="<?php echo e(old('account_number')); ?>" maxlength="100" class="<?php echo e($fieldClass); ?>" placeholder="مثال: 01000000000">
                    <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="bank-name-field" class="hidden">
                    <label class="<?php echo e($labelClass); ?>" for="bank_name">اسم البنك</label>
                    <input id="bank_name" type="text" name="bank_name" value="<?php echo e(old('bank_name')); ?>" maxlength="100" class="<?php echo e($fieldClass); ?>" placeholder="مثال: البنك الأهلي">
                    <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="account_holder">اسم صاحب الحساب</label>
                    <input id="account_holder" type="text" name="account_holder" value="<?php echo e(old('account_holder')); ?>" maxlength="255" class="<?php echo e($fieldClass); ?>" placeholder="الاسم كما يظهر في الحساب">
                    <?php $__errorArgs = ['account_holder'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="balance">الرصيد الابتدائي (ج.م)</label>
                    <input id="balance" type="number" name="balance" value="<?php echo e(old('balance', 0)); ?>" step="0.01" min="0" class="<?php echo e($fieldClass); ?>" placeholder="0">
                    <?php $__errorArgs = ['balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="<?php echo e($labelClass); ?>" for="notes">ملاحظات</label>
                <textarea id="notes" name="notes" rows="3" maxlength="1000" class="<?php echo e($areaClass); ?> resize-none" placeholder="أي تفاصيل إضافية عن المحفظة"><?php echo e(old('notes')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex items-center gap-3 rounded-xl border border-line bg-canvas/40 px-4 py-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="size-4 rounded border-line text-accent focus:ring-accent/20">
                <label for="is_active" class="text-sm font-medium text-ink">المحفظة نشطة</label>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                <a href="<?php echo e(route('admin.wallets.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                    إلغاء
                </a>
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ المحفظة
                </button>
            </div>
        </form>
    </article>
</div>

<script>
(function() {
    var typeSelect = document.getElementById('wallet-type');
    var bankField = document.getElementById('bank-name-field');
    if (!typeSelect || !bankField) return;
    function toggle() {
        if (typeSelect.value === 'bank_transfer') {
            bankField.classList.remove('hidden');
        } else {
            bankField.classList.add('hidden');
        }
    }
    typeSelect.addEventListener('change', toggle);
    toggle();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\wallets\create.blade.php ENDPATH**/ ?>