<?php $__env->startSection('title', 'إنشاء مصروف جديد'); ?>
<?php $__env->startSection('page_title', 'إنشاء مصروف جديد'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $fileClass = 'w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink file:me-3 file:rounded-lg file:border-0 file:bg-accent-soft file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-accent';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الحسابات · المصروفات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e(__('إنشاء مصروف جديد')); ?></h2>
            <p class="mt-1 text-sm text-muted"><?php echo e(__('إضافة مصروف جديد للموافقة عليه')); ?></p>
        </div>
        <a href="<?php echo e(route('admin.expenses.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            <?php echo e(__('العودة')); ?>

        </a>
    </section>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger shadow-soft">
            <p class="mb-2 font-semibold">يرجى تصحيح ما يلي:</p>
            <ul class="list-inside list-disc space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.expenses.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
        <?php echo csrf_field(); ?>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات المصروف</h3>
                <p class="mt-0.5 text-xs text-muted">العنوان، الفئة، المبلغ، والتاريخ</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="title"><?php echo e(__('العنوان')); ?> <span class="text-danger">*</span></label>
                    <input id="title" type="text" name="title" value="<?php echo e(old('title')); ?>" required class="<?php echo e($fieldClass); ?>" placeholder="<?php echo e(__('مثال: شراء معدات للقاعة')); ?>">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="category"><?php echo e(__('الفئة')); ?> <span class="text-danger">*</span></label>
                    <select id="category" name="category" required class="<?php echo e($fieldClass); ?>">
                        <option value=""><?php echo e(__('اختر الفئة')); ?></option>
                        <option value="operational" <?php if(old('category') == 'operational'): echo 'selected'; endif; ?>><?php echo e(__('تشغيلي')); ?></option>
                        <option value="marketing" <?php if(old('category') == 'marketing'): echo 'selected'; endif; ?>><?php echo e(__('تسويق')); ?></option>
                        <option value="salaries" <?php if(old('category') == 'salaries'): echo 'selected'; endif; ?>><?php echo e(__('رواتب')); ?></option>
                        <option value="utilities" <?php if(old('category') == 'utilities'): echo 'selected'; endif; ?>><?php echo e(__('مرافق')); ?></option>
                        <option value="equipment" <?php if(old('category') == 'equipment'): echo 'selected'; endif; ?>><?php echo e(__('معدات')); ?></option>
                        <option value="maintenance" <?php if(old('category') == 'maintenance'): echo 'selected'; endif; ?>><?php echo e(__('صيانة')); ?></option>
                        <option value="other" <?php if(old('category') == 'other'): echo 'selected'; endif; ?>><?php echo e(__('أخرى')); ?></option>
                    </select>
                    <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="amount"><?php echo e(__('المبلغ')); ?> <span class="text-danger">*</span> (ج.م)</label>
                    <input id="amount" type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount')); ?>" required class="<?php echo e($fieldClass); ?>" placeholder="0.00">
                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="expense_date"><?php echo e(__('تاريخ المصروف')); ?> <span class="text-danger">*</span></label>
                    <input id="expense_date" type="date" name="expense_date" value="<?php echo e(old('expense_date', date('Y-m-d'))); ?>" required class="<?php echo e($fieldClass); ?>">
                    <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">الدفع والمرفقات</h3>
                <p class="mt-0.5 text-xs text-muted">طريقة الدفع، المحفظة، المرجع، والإيصال</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="payment_method"><?php echo e(__('طريقة الدفع')); ?> <span class="text-danger">*</span></label>
                    <select id="payment_method" name="payment_method" required class="<?php echo e($fieldClass); ?>">
                        <option value=""><?php echo e(__('اختر طريقة الدفع')); ?></option>
                        <option value="cash" <?php if(old('payment_method') == 'cash'): echo 'selected'; endif; ?>><?php echo e(__('نقدي')); ?></option>
                        <option value="bank_transfer" <?php if(old('payment_method') == 'bank_transfer'): echo 'selected'; endif; ?>><?php echo e(__('تحويل بنكي')); ?></option>
                        <option value="card" <?php if(old('payment_method') == 'card'): echo 'selected'; endif; ?>><?php echo e(__('بطاقة')); ?></option>
                        <option value="wallet" <?php if(old('payment_method') == 'wallet'): echo 'selected'; endif; ?>><?php echo e(__('محفظة إلكترونية')); ?></option>
                        <option value="other" <?php if(old('payment_method') == 'other'): echo 'selected'; endif; ?>><?php echo e(__('أخرى')); ?></option>
                    </select>
                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="wallet_field" style="display: none;">
                    <label class="<?php echo e($labelClass); ?>" for="wallet_id"><?php echo e(__('المحفظة الإلكترونية')); ?></label>
                    <select id="wallet_id" name="wallet_id" class="<?php echo e($fieldClass); ?>">
                        <option value=""><?php echo e(__('اختر محفظة')); ?></option>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wallet->id); ?>" <?php if(old('wallet_id') == $wallet->id): echo 'selected'; endif; ?>>
                                <?php echo e($wallet->name); ?> (<?php echo e($wallet->type_name); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="reference_number"><?php echo e(__('رقم المرجع')); ?> (اختياري)</label>
                    <input id="reference_number" type="text" name="reference_number" value="<?php echo e(old('reference_number')); ?>" class="<?php echo e($fieldClass); ?>" placeholder="<?php echo e(__('رقم الفاتورة، رقم الشيك، إلخ')); ?>">
                    <?php $__errorArgs = ['reference_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="attachment"><?php echo e(__('صورة الفاتورة/الإيصال')); ?> (اختياري)</label>
                    <input id="attachment" type="file" name="attachment" accept="image/*" class="<?php echo e($fileClass); ?>">
                    <p class="mt-1 text-[11px] text-muted"><?php echo e(__('يُسمح بالصور فقط (JPEG, PNG, JPG) - الحد الأقصى 40 ميجابايت')); ?></p>
                    <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">تفاصيل إضافية</h3>
                <p class="mt-0.5 text-xs text-muted">الوصف والملاحظات (اختياري)</p>
            </div>
            <div class="space-y-4 p-4 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="description"><?php echo e(__('الوصف')); ?> (اختياري)</label>
                    <textarea id="description" name="description" rows="3" class="<?php echo e($areaClass); ?>" placeholder="<?php echo e(__('وصف تفصيلي للمصروف...')); ?>"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="notes"><?php echo e(__('ملاحظات')); ?> (اختياري)</label>
                    <textarea id="notes" name="notes" rows="2" class="<?php echo e($areaClass); ?>" placeholder="<?php echo e(__('ملاحظات إضافية...')); ?>"><?php echo e(old('notes')); ?></textarea>
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-line px-4 py-4 sm:px-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    <?php echo e(__('إنشاء المصروف')); ?>

                </button>
                <a href="<?php echo e(route('admin.expenses.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-6 text-sm font-medium text-ink hover:bg-canvas">
                    <i class="fas fa-times text-xs"></i>
                    <?php echo e(__('إلغاء')); ?>

                </a>
            </div>
        </article>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const walletField = document.getElementById('wallet_field');
    const walletId = document.getElementById('wallet_id');

    paymentMethod.addEventListener('change', function() {
        if (this.value === 'wallet' || this.value === 'bank_transfer') {
            walletField.style.display = 'block';
            if (this.value === 'wallet') {
                walletId.setAttribute('required', 'required');
            } else {
                walletId.removeAttribute('required');
            }
        } else {
            walletField.style.display = 'none';
            walletId.removeAttribute('required');
            walletId.value = '';
        }
    });

    // Check on page load if payment method is already selected
    if (paymentMethod.value === 'wallet' || paymentMethod.value === 'bank_transfer') {
        walletField.style.display = 'block';
        if (paymentMethod.value === 'wallet') {
            walletId.setAttribute('required', 'required');
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/expenses/create.blade.php ENDPATH**/ ?>