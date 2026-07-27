<?php $__env->startSection('title', 'إدارة المدفوعات - ' . config('app.name')); ?>
<?php $__env->startSection('page_title', 'إدارة المدفوعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $kpis = [
        ['label' => 'إجمالي المدفوعات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fa-money-bill-wave', 'tone' => 'accent', 'note' => 'كل المدفوعات المسجلة'],
        ['label' => 'مكتملة', 'value' => number_format($stats['completed'] ?? 0), 'icon' => 'fa-check-circle', 'tone' => 'accent', 'note' => 'تمت بنجاح'],
        ['label' => 'معلقة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fa-hourglass-half', 'tone' => 'metal', 'note' => 'في انتظار المعالجة'],
        ['label' => 'إجمالي المبلغ', 'value' => number_format($stats['total_amount'] ?? 0, 2) . ' ج.م', 'icon' => 'fa-coins', 'tone' => 'muted', 'note' => 'قيمة المكتملة'],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-accent-soft text-accent'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-metal/15 text-metal'],
        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-accent-soft text-accent'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-canvas-muted text-muted'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-canvas-muted text-muted'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-canvas-muted text-muted'],
    ];
    $paymentMethodLabels = [
        'cash' => 'نقدي',
        'card' => 'بطاقة',
        'bank_transfer' => 'تحويل بنكي',
        'online' => 'دفع إلكتروني',
        'wallet' => 'محفظة',
        'other' => 'أخرى',
    ];
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · المدفوعات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إدارة المدفوعات</h2>
            <p class="mt-1 text-sm text-muted">متابعة المدفوعات وطرق الدفع وحالة المعالجة</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.payments.create')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                إضافة دفعة
            </a>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($toneClass[$kpi['tone']]); ?>">
                    <i class="fas <?php echo e($kpi['icon']); ?> text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e($kpi['label']); ?></p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e($kpi['value']); ?></p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">البحث والفلترة</h3>
            <p class="mt-0.5 text-xs text-muted">حسب الحالة أو رقم الدفعة أو بيانات العميل</p>
        </div>
        <form method="GET" id="filterForm" action="<?php echo e(route('admin.payments.index')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-4 xl:items-end">
            <div class="xl:col-span-2">
                <label class="<?php echo e($labelClass); ?>" for="search">البحث</label>
                <input id="search" type="search" name="search" value="<?php echo e(request('search')); ?>" maxlength="255" placeholder="رقم الدفعة، اسم العميل، هاتف، مرجع…" class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">جميع الحالات</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                    <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>قيد المعالجة</option>
                    <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>فاشلة</option>
                    <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    <option value="refunded" <?php echo e(request('status') == 'refunded' ? 'selected' : ''); ?>>مستردة</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-2 xl:col-span-4">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تطبيق
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                        <i class="fas fa-times text-xs"></i>
                        مسح
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">قائمة المدفوعات</h3>
                <p class="mt-0.5 text-xs text-muted">من الأحدث إلى الأقدم</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($payments->total())); ?> دفعة</span>
        </div>

        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">رقم الدفعة</th>
                        <th class="px-4 py-3 text-start font-medium">العميل</th>
                        <th class="px-4 py-3 text-start font-medium">المبلغ</th>
                        <th class="px-4 py-3 text-start font-medium">طريقة الدفع</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">التاريخ</th>
                        <th class="px-4 py-3 text-start font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $badge = $statusBadges[$payment->status] ?? ['label' => $payment->status, 'classes' => 'bg-canvas-muted text-muted']; ?>
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink"><?php echo e($payment->payment_number); ?></p>
                                <?php if($payment->reference_number): ?>
                                    <p class="mt-0.5 text-[11px] text-muted">مرجع: <?php echo e($payment->reference_number); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink"><?php echo e($payment->user->name ?? '—'); ?></p>
                                <p class="mt-0.5 text-[11px] text-muted"><?php echo e($payment->user->phone ?? $payment->user->email ?? '—'); ?></p>
                            </td>
                            <td class="px-4 py-3 font-semibold tabular-nums text-ink"><?php echo e(number_format($payment->amount, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></td>
                            <td class="px-4 py-3 text-ink-soft"><?php echo e($paymentMethodLabels[$payment->payment_method] ?? $payment->payment_method); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($badge['classes']); ?>"><?php echo e($badge['label']); ?></span>
                            </td>
                            <td class="px-4 py-3 text-xs tabular-nums text-muted"><?php echo e($payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : $payment->created_at->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.payments.show', $payment)); ?>" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-ink hover:border-accent/30 hover:text-accent" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد مدفوعات</p>
                                <p class="mt-1 text-xs text-muted">لم يتم تسجيل أي مدفوعات أو لا توجد نتائج للفلتر.</p>
                                <a href="<?php echo e(route('admin.payments.create')); ?>" class="btn-press mt-4 inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                                    <i class="fas fa-plus text-xs"></i>
                                    إضافة دفعة
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($payments->hasPages()): ?>
            <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($payments->appends(request()->query())->links()); ?></div>
        <?php endif; ?>
    </article>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    var filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            var q = this.querySelector('input[name="search"]');
            if (q) q.value = (q.value || '').replace(/[<>'"&]/g, '').trim();
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/payments/index.blade.php ENDPATH**/ ?>