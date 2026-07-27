

<?php $__env->startSection('title', 'تعديل الدفعة'); ?>
<?php $__env->startSection('page_title', 'تعديل الدفعة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · المدفوعات · <?php echo e($payment->payment_number); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تعديل الدفعة #<?php echo e($payment->payment_number); ?></h2>
            <p class="mt-1 text-sm text-muted">تحديث بيانات الدفعة والحالة وطريقة الدفع</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.payments.show', $payment)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-eye text-xs"></i>
                عرض التفاصيل
            </a>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع للقائمة
            </a>
        </div>
    </section>

    <form action="<?php echo e(route('admin.payments.update', $payment)); ?>" method="POST" class="space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات الدفعة</h3>
                <p class="mt-0.5 text-xs text-muted">تعديل العميل والفاتورة والمبلغ وطريقة الدفع والحالة</p>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:p-5 md:grid-cols-2">
                <div>
                    <label class="<?php echo e($labelClass); ?>">العميل *</label>
                    <select name="user_id" required class="<?php echo e($fieldClass); ?>">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php echo e($payment->user_id == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?> - <?php echo e($user->phone); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>">الفاتورة</label>
                    <select name="invoice_id" required class="<?php echo e($fieldClass); ?>">
                        <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($invoice->id); ?>" <?php echo e($payment->invoice_id == $invoice->id ? 'selected' : ''); ?>>
                                <?php echo e($invoice->invoice_number); ?> · <?php echo e($invoice->user->name); ?> · متبقي <?php echo e(number_format($invoice->remaining_amount + ($payment->invoice_id === $invoice->id ? $payment->amount : 0), 2)); ?> ج.م
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value="" disabled selected>لا توجد فواتير متاحة</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="<?php echo e(old('amount', $payment->amount)); ?>" class="<?php echo e($fieldClass); ?>">
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>">طريقة الدفع</label>
                    <select name="payment_method" required class="<?php echo e($fieldClass); ?>">
                        <option value="cash" <?php echo e($payment->payment_method == 'cash' ? 'selected' : ''); ?>>نقدي</option>
                        <option value="card" <?php echo e($payment->payment_method == 'card' ? 'selected' : ''); ?>>بطاقة</option>
                        <option value="bank_transfer" <?php echo e($payment->payment_method == 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                        <option value="online" <?php echo e($payment->payment_method == 'online' ? 'selected' : ''); ?>>دفع إلكتروني</option>
                        <option value="wallet" <?php echo e($payment->payment_method == 'wallet' ? 'selected' : ''); ?>>محفظة</option>
                        <option value="other" <?php echo e($payment->payment_method == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>">الحالة *</label>
                    <select name="status" required class="<?php echo e($fieldClass); ?>">
                        <option value="pending" <?php echo e($payment->status == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="completed" <?php echo e($payment->status == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                        <option value="failed" <?php echo e($payment->status == 'failed' ? 'selected' : ''); ?>>فاشلة</option>
                        <option value="cancelled" <?php echo e($payment->status == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="<?php echo e($labelClass); ?>">ملاحظات</label>
                    <textarea name="notes" rows="3" class="<?php echo e($areaClass); ?>"><?php echo e(old('notes', $payment->notes)); ?></textarea>
                </div>
            </div>
        </article>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                <i class="fas fa-save text-xs"></i>
                تحديث الدفعة
            </button>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                إلغاء
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/payments/edit.blade.php ENDPATH**/ ?>