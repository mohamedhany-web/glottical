<?php $__env->startSection('title', 'إنشاء فاتورة جديدة'); ?>
<?php $__env->startSection('page_title', 'إنشاء فاتورة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · الفواتير</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إنشاء فاتورة جديدة</h2>
            <p class="mt-1 text-sm text-muted">إصدار فاتورة جديدة لعميل مع تحديد المبالغ وتاريخ الاستحقاق</p>
        </div>
        <a href="<?php echo e(route('admin.invoices.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            العودة للقائمة
        </a>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="alert">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-canvas-muted text-muted"><i class="fas fa-exclamation-circle text-sm"></i></span>
            <p><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm text-ink shadow-soft" role="alert">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">بيانات الفاتورة</h3>
            <p class="mt-0.5 text-xs text-muted">اختر العميل وحدد تفاصيل المبالغ والوصف</p>
        </div>
        <form action="<?php echo e(route('admin.invoices.store')); ?>" method="POST" class="space-y-6 p-4 sm:p-5">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="invoice-user-id">العميل *</label>
                    <label for="invoice-client-search" class="sr-only">بحث عن عميل بالاسم أو البريد</label>
                    <input type="search" id="invoice-client-search" autocomplete="off" placeholder="بحث بالاسم أو البريد أو الجوال…"
                           class="<?php echo e($fieldClass); ?> mb-2">
                    <select id="invoice-user-id" name="user_id" required class="<?php echo e($fieldClass); ?>">
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
                    <label class="<?php echo e($labelClass); ?>" for="invoice-type">نوع الفاتورة *</label>
                    <select id="invoice-type" name="type" required class="<?php echo e($fieldClass); ?>">
                        <option value="course">كورس</option>
                        <option value="subscription">اشتراك</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="subtotal">المبلغ الفرعي *</label>
                    <input type="number" id="subtotal" name="subtotal" step="0.01" min="0" required value="<?php echo e(old('subtotal')); ?>" class="<?php echo e($fieldClass); ?>">
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="tax_amount">الضريبة</label>
                    <input type="number" id="tax_amount" name="tax_amount" step="0.01" min="0" value="<?php echo e(old('tax_amount', 0)); ?>" class="<?php echo e($fieldClass); ?>">
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="discount_amount">الخصم</label>
                    <input type="number" id="discount_amount" name="discount_amount" step="0.01" min="0" value="<?php echo e(old('discount_amount', 0)); ?>" class="<?php echo e($fieldClass); ?>">
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="due_date">تاريخ الاستحقاق</label>
                    <input type="date" id="due_date" name="due_date" value="<?php echo e(old('due_date')); ?>" class="<?php echo e($fieldClass); ?>">
                </div>
            </div>

            <div>
                <label class="<?php echo e($labelClass); ?>" for="description">الوصف</label>
                <textarea id="description" name="description" rows="3" class="<?php echo e($areaClass); ?>"><?php echo e(old('description')); ?></textarea>
            </div>

            <div>
                <label class="<?php echo e($labelClass); ?>" for="notes">ملاحظات</label>
                <textarea id="notes" name="notes" rows="3" class="<?php echo e($areaClass); ?>"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-line pt-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    إنشاء الفاتورة
                </button>
                <a href="<?php echo e(route('admin.invoices.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                    إلغاء
                </a>
            </div>
        </form>
    </article>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var searchInput = document.getElementById('invoice-client-search');
    var select = document.getElementById('invoice-user-id');
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\invoices\create.blade.php ENDPATH**/ ?>