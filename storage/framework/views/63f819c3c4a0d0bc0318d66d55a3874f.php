<?php $__env->startSection('title', 'تنفيذ الدفع للمدرب'); ?>
<?php $__env->startSection('page_title', 'تنفيذ الدفع للمدرب'); ?>
<?php $__env->startSection('header', 'تنفيذ الدفع للمدرب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $fileClass = 'w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-ink transition file:me-3 file:rounded-lg file:border-0 file:bg-accent-soft file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-accent focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · الماليات · <?php echo e($payment->instructor->name ?? 'مدرب'); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تنفيذ الدفع وتحويل المبلغ</h2>
            <p class="mt-1 text-sm text-muted">رفع إيصال التحويل وتسجيل الدفع للمدرب</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.salaries.instructor', $payment->instructor)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة لماليات المدرب
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">تفاصيل المدفوعة</h3>
                <p class="mt-0.5 text-xs text-muted">بيانات المدفوعة المراد تنفيذها</p>
            </div>
            <dl class="grid grid-cols-1 gap-4 p-4 sm:p-5">
                <div>
                    <dt class="text-xs font-medium text-muted">رقم المدفوعة</dt>
                    <dd class="mt-1 font-mono text-sm font-semibold text-ink"><?php echo e($payment->payment_number); ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">المدرب</dt>
                    <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($payment->instructor->name ?? '—'); ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">الاتفاقية</dt>
                    <dd class="mt-1 text-sm font-medium text-ink-soft"><?php echo e($payment->agreement->title ?? '—'); ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">المبلغ</dt>
                    <dd class="mt-1 text-xl font-semibold tabular-nums text-accent"><?php echo e(number_format($payment->amount, 2)); ?> <span class="text-sm font-normal text-muted">ج.م</span></dd>
                </div>
            </dl>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">حساب التحويل (بيانات المدرب)</h3>
                <p class="mt-0.5 text-xs text-muted">بيانات الحساب البنكي للتحويل</p>
            </div>
            <div class="p-4 sm:p-5">
                <?php if($payment->instructor && $payment->instructor->payoutDetail && $payment->instructor->payoutDetail->hasAnyDetails()): ?>
                    <?php $d = $payment->instructor->payoutDetail; ?>
                    <dl class="grid grid-cols-1 gap-3 text-sm">
                        <?php if($d->bank_name): ?>
                            <div><dt class="text-xs font-medium text-muted">البنك</dt><dd class="mt-0.5 font-medium text-ink"><?php echo e($d->bank_name); ?></dd></div>
                        <?php endif; ?>
                        <?php if($d->account_holder_name): ?>
                            <div><dt class="text-xs font-medium text-muted">اسم صاحب الحساب</dt><dd class="mt-0.5 text-ink-soft"><?php echo e($d->account_holder_name); ?></dd></div>
                        <?php endif; ?>
                        <?php if($d->account_number): ?>
                            <div><dt class="text-xs font-medium text-muted">رقم الحساب</dt><dd class="mt-0.5 font-mono text-ink"><?php echo e($d->account_number); ?></dd></div>
                        <?php endif; ?>
                        <?php if($d->iban): ?>
                            <div><dt class="text-xs font-medium text-muted">الآيبان</dt><dd class="mt-0.5 font-mono text-ink"><?php echo e($d->iban); ?></dd></div>
                        <?php endif; ?>
                        <?php if($d->branch_name): ?>
                            <div><dt class="text-xs font-medium text-muted">الفرع</dt><dd class="mt-0.5 text-ink-soft"><?php echo e($d->branch_name); ?></dd></div>
                        <?php endif; ?>
                    </dl>
                <?php else: ?>
                    <div class="rounded-xl border border-line bg-canvas-muted/50 px-4 py-3 text-sm text-ink-soft">
                        <span class="inline-flex size-8 items-center justify-center rounded-lg bg-metal/15 text-metal"><i class="fas fa-exclamation-triangle text-xs"></i></span>
                        <p class="mt-2">المدرب لم يضف بعد بيانات حساب التحويل. يمكنك تنفيذ الدفع ورفع الإيصال، وننصح بإخبار المدرب بإضافة بيانات التحويل من صفحة «حساب التحويل» في لوحته.</p>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">تسجيل الدفع ورفع إيصال التحويل</h3>
            <p class="mt-0.5 text-xs text-muted">PDF أو صورة، حجم أقصى 40 ميجابايت</p>
        </div>
        <form action="<?php echo e(route('admin.salaries.mark-paid', $payment)); ?>" method="POST" enctype="multipart/form-data" class="max-w-xl space-y-4 p-4 sm:p-5">
            <?php echo csrf_field(); ?>
            <div>
                <label for="transfer_receipt" class="<?php echo e($labelClass); ?>">إيصال التحويل (مطلوب) *</label>
                <input type="file" name="transfer_receipt" id="transfer_receipt" accept=".pdf,.jpg,.jpeg,.png" required class="<?php echo e($fileClass); ?>">
                <?php $__errorArgs = ['transfer_receipt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-metal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="notes" class="<?php echo e($labelClass); ?>">ملاحظات (اختياري)</label>
                <textarea name="notes" id="notes" rows="2" class="<?php echo e($areaClass); ?>"><?php echo e(old('notes')); ?></textarea>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-check text-xs"></i>
                    تسجيل الدفع ورفع الإيصال
                </button>
                <a href="<?php echo e(route('admin.salaries.instructor', $payment->instructor)); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">إلغاء</a>
            </div>
        </form>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\salaries\pay.blade.php ENDPATH**/ ?>