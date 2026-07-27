<?php $__env->startSection('title', 'الفواتير - التقارير المحاسبية - Glottical'); ?>
<?php $__env->startSection('page_title', 'الفواتير'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $statusBadges = [
        'paid' => ['label' => 'مدفوعة', 'classes' => 'bg-accent-soft text-accent'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-metal/15 text-metal'],
        'overdue' => ['label' => 'متأخرة', 'classes' => 'bg-canvas-muted text-muted'],
    ];
    $kpis = [
        ['label' => 'إجمالي الفواتير', 'value' => $stats['total_invoices'] ?? 0, 'icon' => 'fa-file-invoice', 'tone' => 'accent', 'note' => 'في الفترة المحددة'],
        ['label' => 'مدفوعة', 'value' => $stats['paid_invoices'] ?? 0, 'icon' => 'fa-check-circle', 'tone' => 'accent', 'note' => 'تم دفعها'],
        ['label' => 'معلقة', 'value' => $stats['pending_invoices'] ?? 0, 'icon' => 'fa-hourglass-half', 'tone' => 'metal', 'note' => 'بانتظار الدفع'],
    ];
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="transition hover:text-accent">التقارير المحاسبية</a>
                <span class="mx-1">/</span>
                الفواتير
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">الفواتير في الفترة</h2>
            <p class="mt-1 text-sm text-muted">من <?php echo e($startDate->format('Y-m-d')); ?> إلى <?php echo e($endDate->format('Y-m-d')); ?></p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->query(), ['type' => 'invoices']))); ?>"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-file-excel text-xs"></i>
                تصدير Excel
            </a>
            <a href="<?php echo e(route('admin.accounting.reports')); ?>"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للملخص
            </a>
        </div>
    </section>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-3">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($toneClass[$kpi['tone']]); ?>">
                    <i class="fas <?php echo e($kpi['icon']); ?> text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e($kpi['label']); ?></p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($kpi['value'])); ?></p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">فلترة الفترة</h3>
            <p class="mt-0.5 text-xs text-muted">اختر فترة جاهزة أو حدّد نطاقاً مخصصاً</p>
        </div>
        <form method="GET" action="<?php echo e(route('admin.accounting.reports.invoices')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-4 xl:items-end">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="period">الفترة</label>
                <select id="period" name="period" class="<?php echo e($fieldClass); ?>" onchange="this.form.submit()">
                    <option value="day" <?php echo e(($period ?? '') == 'day' ? 'selected' : ''); ?>>اليوم</option>
                    <option value="week" <?php echo e(($period ?? '') == 'week' ? 'selected' : ''); ?>>هذا الأسبوع</option>
                    <option value="month" <?php echo e(($period ?? '') == 'month' ? 'selected' : ''); ?>>هذا الشهر</option>
                    <option value="year" <?php echo e(($period ?? '') == 'year' ? 'selected' : ''); ?>>هذه السنة</option>
                    <option value="all" <?php echo e(($period ?? '') == 'all' ? 'selected' : ''); ?>>الكل</option>
                    <option value="custom" <?php echo e(($period ?? '') == 'custom' ? 'selected' : ''); ?>>مخصص</option>
                </select>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="start_date">من تاريخ</label>
                <input id="start_date" type="date" name="start_date" value="<?php echo e($startDate ? $startDate->format('Y-m-d') : ''); ?>" class="<?php echo e($fieldClass); ?>" />
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="end_date">إلى تاريخ</label>
                <input id="end_date" type="date" name="end_date" value="<?php echo e($endDate ? $endDate->format('Y-m-d') : ''); ?>" class="<?php echo e($fieldClass); ?>" />
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تطبيق
                </button>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">قائمة الفواتير</h3>
                <p class="mt-0.5 text-xs text-muted">من الأحدث إلى الأقدم</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($items->total())); ?> فاتورة</span>
        </div>

        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">رقم الفاتورة</th>
                        <th class="px-4 py-3 text-start font-medium">العميل</th>
                        <th class="px-4 py-3 text-start font-medium">المبلغ</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $badge = $statusBadges[$invoice->status] ?? ['label' => $invoice->status, 'classes' => 'bg-canvas-muted text-muted']; ?>
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.invoices.show', $invoice)); ?>" class="font-semibold text-accent hover:text-accent"><?php echo e($invoice->invoice_number); ?></a>
                            </td>
                            <td class="px-4 py-3 text-ink-soft"><?php echo e($invoice->user->name ?? '—'); ?></td>
                            <td class="px-4 py-3 font-semibold tabular-nums text-ink">
                                <?php echo e(number_format($invoice->total_amount, 2)); ?>

                                <span class="text-xs font-normal text-muted">ج.م</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($badge['classes']); ?>"><?php echo e($badge['label']); ?></span>
                            </td>
                            <td class="px-4 py-3 text-xs tabular-nums text-muted"><?php echo e($invoice->created_at->format('Y-m-d H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد فواتير</p>
                                <p class="mt-1 text-xs text-muted">لا توجد فواتير في هذه الفترة.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($items->hasPages()): ?>
            <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($items->links()); ?></div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\accounting\reports-invoices.blade.php ENDPATH**/ ?>