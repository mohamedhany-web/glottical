

<?php $__env->startSection('title', 'سجل معاملات المحفظة'); ?>
<?php $__env->startSection('page_title', 'سجل معاملات المحفظة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $kpis = [
        ['label' => 'رصيد المحفظة', 'value' => number_format($wallet->balance, 2), 'icon' => 'fa-coins', 'tone' => 'accent', 'suffix' => ' ' . ($wallet->currency ?? 'ج.م')],
        ['label' => 'الرصيد المعلّق', 'value' => number_format($wallet->pending_balance ?? 0, 2), 'icon' => 'fa-hourglass-half', 'tone' => 'metal', 'suffix' => ' ' . ($wallet->currency ?? 'ج.م')],
        ['label' => 'عدد المعاملات', 'value' => $transactions->count(), 'icon' => 'fa-receipt', 'tone' => 'accent', 'suffix' => ''],
        ['label' => 'آخر عملية', 'value' => optional($transactions->first())->created_at?->format('Y-m-d H:i') ?? 'غير متوفر', 'icon' => 'fa-clock', 'tone' => 'muted', 'suffix' => '', 'small' => true],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · المحافظ · المعاملات</p>
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

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($toneClass[$kpi['tone']]); ?>">
                    <i class="fas <?php echo e($kpi['icon']); ?> text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e($kpi['label']); ?></p>
                <p class="mt-1 <?php echo e(!empty($kpi['small']) ? 'text-sm font-semibold' : 'text-xl font-semibold tabular-nums'); ?> tracking-tight text-ink"><?php echo e($kpi['value']); ?><?php echo e($kpi['suffix'] ?? ''); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">المعاملات الأخيرة</h3>
                <p class="mt-0.5 text-xs text-muted">تابع حركة المحفظة مع توضيح نوع العملية والملاحظات المرتبطة بها</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e($transactions->count()); ?> معاملة</span>
        </div>

        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">التاريخ</th>
                        <th class="px-4 py-3 text-start font-medium">النوع</th>
                        <th class="px-4 py-3 text-start font-medium">المبلغ</th>
                        <th class="px-4 py-3 text-start font-medium">الرصيد بعد العملية</th>
                        <th class="px-4 py-3 text-start font-medium">المرجع</th>
                        <th class="px-4 py-3 text-start font-medium">الملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3 text-xs tabular-nums text-muted">
                                <?php echo e($transaction->created_at?->format('Y-m-d H:i') ?? 'غير معروف'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($transaction->type === 'deposit' ? 'bg-accent-soft text-accent' : 'bg-canvas-muted text-muted'); ?>">
                                    <?php echo e($transaction->type === 'deposit' ? 'إيداع' : 'سحب'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold tabular-nums <?php echo e($transaction->type === 'deposit' ? 'text-accent' : 'text-ink'); ?>">
                                <?php echo e(number_format($transaction->amount, 2)); ?> <span class="text-xs font-normal text-muted"><?php echo e($wallet->currency ?? 'ج.م'); ?></span>
                            </td>
                            <td class="px-4 py-3 tabular-nums text-ink-soft">
                                <?php echo e(number_format($transaction->balance_after ?? 0, 2)); ?> <span class="text-xs text-muted"><?php echo e($wallet->currency ?? 'ج.م'); ?></span>
                            </td>
                            <td class="px-4 py-3 text-ink-soft">
                                <?php echo e($transaction->reference_number ?? '—'); ?>

                            </td>
                            <td class="px-4 py-3 text-ink-soft">
                                <?php echo e($transaction->notes ?? '—'); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد معاملات مسجلة</p>
                                <p class="mt-1 text-xs text-muted">ستظهر العمليات فور تسجيلها من خلال الإيداعات أو السحوبات.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\wallets\transactions.blade.php ENDPATH**/ ?>