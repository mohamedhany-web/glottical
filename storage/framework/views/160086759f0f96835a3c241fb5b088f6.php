

<?php $__env->startSection('title', 'تعديل المعاملة'); ?>
<?php $__env->startSection('page_title', 'تعديل المعاملة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · المعاملات · #<?php echo e($transaction->id); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تعديل المعاملة</h2>
            <p class="mt-1 text-sm text-muted"><?php echo e($transaction->transaction_number ?? 'معاملة #' . $transaction->id); ?></p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.transactions.show', $transaction)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-eye text-xs"></i>
                عرض التفاصيل
            </a>
            <a href="<?php echo e(route('admin.transactions.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع للقائمة
            </a>
        </div>
    </section>

    <form action="<?php echo e(route('admin.transactions.update', $transaction)); ?>" method="POST" class="space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات المعاملة</h3>
                <p class="mt-0.5 text-xs text-muted">العميل، النوع، المبلغ، والحالة</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="user_id">العميل <span class="text-danger">*</span></label>
                    <select id="user_id" name="user_id" required class="<?php echo e($fieldClass); ?>">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php echo e($transaction->user_id == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?> - <?php echo e($user->phone); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="type">النوع <span class="text-danger">*</span></label>
                    <select id="type" name="type" required class="<?php echo e($fieldClass); ?>">
                        <option value="deposit" <?php echo e($transaction->type == 'deposit' ? 'selected' : ''); ?>>إيداع</option>
                        <option value="withdrawal" <?php echo e($transaction->type == 'withdrawal' ? 'selected' : ''); ?>>سحب</option>
                        <option value="payment" <?php echo e($transaction->type == 'payment' ? 'selected' : ''); ?>>دفع</option>
                        <option value="refund" <?php echo e($transaction->type == 'refund' ? 'selected' : ''); ?>>استرداد</option>
                        <option value="commission" <?php echo e($transaction->type == 'commission' ? 'selected' : ''); ?>>عمولة</option>
                    </select>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="amount">المبلغ <span class="text-danger">*</span></label>
                    <input id="amount" type="number" name="amount" step="0.01" min="0" required value="<?php echo e(old('amount', $transaction->amount)); ?>" class="<?php echo e($fieldClass); ?>">
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="status">الحالة <span class="text-danger">*</span></label>
                    <select id="status" name="status" required class="<?php echo e($fieldClass); ?>">
                        <option value="pending" <?php echo e($transaction->status == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="completed" <?php echo e($transaction->status == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                        <option value="failed" <?php echo e($transaction->status == 'failed' ? 'selected' : ''); ?>>فاشلة</option>
                        <option value="cancelled" <?php echo e($transaction->status == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">الوصف</h3>
                <p class="mt-0.5 text-xs text-muted">تفاصيل إضافية اختيارية</p>
            </div>
            <div class="p-4 sm:p-5">
                <label class="<?php echo e($labelClass); ?>" for="description">الوصف</label>
                <textarea id="description" name="description" rows="3" class="<?php echo e($areaClass); ?>"><?php echo e(old('description', $transaction->description)); ?></textarea>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-line px-4 py-4 sm:px-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    تحديث المعاملة
                </button>
                <a href="<?php echo e(route('admin.transactions.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-6 text-sm font-medium text-ink hover:bg-canvas">
                    إلغاء
                </a>
            </div>
        </article>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\transactions\edit.blade.php ENDPATH**/ ?>