<?php $__env->startSection('title', 'إضافة دفعة جديدة'); ?>
<?php $__env->startSection('page_title', 'إضافة دفعة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · المدفوعات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إضافة دفعة جديدة</h2>
            <p class="mt-1 text-sm text-muted">تسجيل دفعة جديدة وربطها بالعميل والفاتورة</p>
        </div>
        <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للقائمة
        </a>
    </section>

    <form action="<?php echo e(route('admin.payments.store')); ?>" method="POST" class="space-y-5">
        <?php echo csrf_field(); ?>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات الدفعة</h3>
                <p class="mt-0.5 text-xs text-muted">اختر العميل والفاتورة وأدخل تفاصيل المبلغ وطريقة الدفع</p>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:p-5 md:grid-cols-2">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="payment-client-search">العميل *</label>
                    <label for="payment-client-search" class="sr-only">بحث عن عميل بالاسم أو البريد</label>
                    <input type="search" id="payment-client-search" autocomplete="off" placeholder="بحث بالاسم أو البريد أو الجوال…"
                           class="<?php echo e($fieldClass); ?> mb-2">
                    <select id="payment-user-id" name="user_id" required class="<?php echo e($fieldClass); ?>">
                        <option value="">اختر العميل</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $searchHaystack = mb_strtolower(
                                trim($user->name.' '.($user->email ?? '').' '.($user->phone ?? '')),
                                'UTF-8'
                            );
                        ?>
                        <option value="<?php echo e($user->id); ?>" data-search="<?php echo e(e($searchHaystack)); ?>"><?php echo e($user->name); ?> — <?php echo e($user->email); ?> <?php if($user->phone): ?> · <?php echo e($user->phone); ?> <?php endif; ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>">الفاتورة *</label>
                    <select name="invoice_id" required class="<?php echo e($fieldClass); ?>">
                        <?php if($invoices->isEmpty()): ?>
                            <option value="" disabled selected>لا توجد فواتير مستحقة حاليًا</option>
                        <?php else: ?>
                            <option value="">اختر الفاتورة</option>
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($invoice->id); ?>">
                                <?php echo e($invoice->invoice_number); ?> · <?php echo e($invoice->user->name); ?> · متبقي <?php echo e(number_format($invoice->remaining_amount, 2)); ?> ج.م
                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                    <?php if($invoices->isEmpty()): ?>
                        <p class="mt-2 text-xs text-metal">لا توجد فواتير بحاجة إلى دفع في الوقت الحالي.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="<?php echo e(old('amount')); ?>" class="<?php echo e($fieldClass); ?>">
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>">طريقة الدفع *</label>
                    <select name="payment_method" required class="<?php echo e($fieldClass); ?>">
                        <option value="cash">نقدي</option>
                        <option value="card">بطاقة</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="online">دفع إلكتروني</option>
                        <option value="wallet">محفظة</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="<?php echo e($labelClass); ?>">ملاحظات</label>
                    <textarea name="notes" rows="3" class="<?php echo e($areaClass); ?>"><?php echo e(old('notes')); ?></textarea>
                </div>
            </div>
        </article>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                إضافة الدفعة
            </button>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                إلغاء
            </a>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var searchInput = document.getElementById('payment-client-search');
    var select = document.getElementById('payment-user-id');
    if (!searchInput || !select) return;
    var options = Array.prototype.slice.call(select.querySelectorAll('option'));
    function applyFilter() {
        var q = (searchInput.value || '').trim().toLowerCase();
        options.forEach(function (opt) {
            if (!opt.value) {
                opt.hidden = false;
                return;
            }
            if (opt.selected) {
                opt.hidden = false;
                return;
            }
            var hay = (opt.getAttribute('data-search') || '').toLowerCase();
            opt.hidden = q.length > 0 && hay.indexOf(q) === -1;
        });
    }
    searchInput.addEventListener('input', applyFilter);
    searchInput.addEventListener('search', applyFilter);
    select.addEventListener('change', applyFilter);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\payments\create.blade.php ENDPATH**/ ?>