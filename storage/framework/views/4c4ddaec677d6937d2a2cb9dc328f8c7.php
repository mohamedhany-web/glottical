

<?php $__env->startSection('title', 'تعديل المحفظة'); ?>
<?php $__env->startSection('page_title', 'تعديل المحفظة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · المحافظ · تعديل</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($wallet->name ?? 'محفظة بدون اسم'); ?></h2>
            <p class="mt-1 text-sm text-muted">
                <i class="fas fa-user-circle text-xs"></i>
                <?php echo e($wallet->user?->name ?? 'غير مرتبط بمستخدم'); ?>

            </p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.wallets.show', $wallet)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للتفاصيل
            </a>
        </div>
    </section>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-coins text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">الرصيد الحالي</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($wallet->balance, 2)); ?> <span class="text-sm font-normal text-muted"><?php echo e($wallet->currency ?? 'ج.م'); ?></span></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal">
                <i class="fas fa-tag text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">نوع المحفظة</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e($wallet->type_name ?? 'غير محدد'); ?></p>
        </article>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft" x-data="{ walletType: '<?php echo e(old('type', $wallet->type)); ?>' }">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">بيانات المحفظة</h3>
            <p class="mt-0.5 text-xs text-muted">قم بتحديث معلومات المحفظة واحفظ التغييرات.</p>
        </div>

        <form action="<?php echo e(route('admin.wallets.update', $wallet)); ?>" method="POST" class="space-y-6 p-4 sm:p-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="name">اسم المحفظة <span class="text-rose-500">*</span></label>
                    <input id="name" type="text" name="name" value="<?php echo e(old('name', $wallet->name)); ?>" required class="<?php echo e($fieldClass); ?>">
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
                    <label class="<?php echo e($labelClass); ?>" for="type">نوع المحفظة <span class="text-rose-500">*</span></label>
                    <select id="type" name="type" x-model="walletType" required class="<?php echo e($fieldClass); ?>">
                        <option value="vodafone_cash">فودافون كاش</option>
                        <option value="instapay">إنستا باي</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="cash">كاش</option>
                        <option value="other">أخرى</option>
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
                    <label class="<?php echo e($labelClass); ?>" for="account_number">رقم الحساب/المحفظة</label>
                    <input id="account_number" type="text" name="account_number" value="<?php echo e(old('account_number', $wallet->account_number)); ?>" class="<?php echo e($fieldClass); ?>">
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

                <div x-show="walletType === 'bank_transfer'" x-cloak>
                    <label class="<?php echo e($labelClass); ?>" for="bank_name">اسم البنك</label>
                    <input id="bank_name" type="text" name="bank_name" value="<?php echo e(old('bank_name', $wallet->bank_name)); ?>" class="<?php echo e($fieldClass); ?>">
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
                    <input id="account_holder" type="text" name="account_holder" value="<?php echo e(old('account_holder', $wallet->account_holder)); ?>" class="<?php echo e($fieldClass); ?>">
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
                    <label class="<?php echo e($labelClass); ?>" for="balance">الرصيد الحالي</label>
                    <div class="relative">
                        <input id="balance" type="number" name="balance" value="<?php echo e(old('balance', $wallet->balance)); ?>" step="0.01" min="0" readonly class="<?php echo e($fieldClass); ?> cursor-not-allowed bg-canvas-muted/50">
                        <span class="pointer-events-none absolute inset-y-0 start-4 flex items-center text-xs text-muted">غير قابل للتعديل</span>
                    </div>
                    <p class="mt-1 text-xs text-muted">لتعديل الرصيد استخدم صفحة المعاملات.</p>
                </div>
            </div>

            <div>
                <label class="<?php echo e($labelClass); ?>" for="notes">ملاحظات</label>
                <textarea id="notes" name="notes" rows="4" class="<?php echo e($areaClass); ?>"><?php echo e(old('notes', $wallet->notes)); ?></textarea>
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
                <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo e(old('is_active', $wallet->is_active) ? 'checked' : ''); ?> class="size-4 rounded border-line text-accent focus:ring-accent/20">
                <div>
                    <label for="is_active" class="text-sm font-semibold text-ink">تفعيل المحفظة</label>
                    <p class="text-xs text-muted">فعّل هذا الخيار للسماح باستخدام المحفظة.</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-line pt-4 sm:flex-row sm:items-center sm:justify-between">
                <a href="<?php echo e(route('admin.wallets.index')); ?>" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                    <i class="fas fa-arrow-right text-xs"></i>
                    إلغاء
                </a>
                <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\wallets\edit.blade.php ENDPATH**/ ?>