

<?php $__env->startSection('title', 'محافظ المنصة - التقارير المحاسبية'); ?>
<?php $__env->startSection('page_title', 'محافظ المنصة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $kpis = [
        ['label' => 'إجمالي المحافظ', 'value' => $stats['wallet_stats']['total_wallets'] ?? 0, 'icon' => 'fa-wallet', 'tone' => 'accent', 'note' => ($stats['wallet_stats']['active_wallets'] ?? 0) . ' نشطة'],
        ['label' => 'إجمالي الأرصدة', 'value' => number_format($stats['wallet_stats']['total_balance'] ?? 0, 2) . ' ج.م', 'icon' => 'fa-coins', 'tone' => 'metal', 'note' => 'مجموع أرصدة المحافظ', 'raw' => true],
    ];
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="transition hover:text-accent">التقارير المحاسبية</a>
                <span class="mx-1">/</span>
                محافظ المنصة
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">محافظ المنصة</h2>
            <p class="mt-1 text-sm text-muted">عرض أرصدة المحافظ وحركتها</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->query(), ['type' => 'wallets']))); ?>"
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

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($toneClass[$kpi['tone']]); ?>">
                    <i class="fas <?php echo e($kpi['icon']); ?> text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e($kpi['label']); ?></p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">
                    <?php if(!empty($kpi['raw'])): ?>
                        <?php echo e($kpi['value']); ?>

                    <?php else: ?>
                        <?php echo e(number_format($kpi['value'])); ?>

                    <?php endif; ?>
                </p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">قائمة المحافظ</h3>
                <p class="mt-0.5 text-xs text-muted">جميع محافظ المنصة المسجلة</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($items->total())); ?> محفظة</span>
        </div>

        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">اسم المحفظة</th>
                        <th class="px-4 py-3 text-start font-medium">النوع</th>
                        <th class="px-4 py-3 text-start font-medium">رقم الحساب</th>
                        <th class="px-4 py-3 text-start font-medium">الرصيد</th>
                        <th class="px-4 py-3 text-start font-medium">المعلق</th>
                        <th class="px-4 py-3 text-start font-medium">معاملات</th>
                        <th class="px-4 py-3 text-start font-medium">نشطة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.wallets.show', $w)); ?>" class="font-semibold text-accent hover:text-accent"><?php echo e($w->name ?? '—'); ?></a>
                            </td>
                            <td class="px-4 py-3 text-ink-soft"><?php echo e(\App\Models\Wallet::typeLabel($w->type ?? '')); ?></td>
                            <td class="px-4 py-3 font-mono text-xs text-muted"><?php echo e($w->account_number ?? '—'); ?></td>
                            <td class="px-4 py-3 font-semibold tabular-nums text-ink">
                                <?php echo e(number_format($w->balance, 2)); ?>

                                <span class="text-xs font-normal text-muted">ج.م</span>
                            </td>
                            <td class="px-4 py-3 tabular-nums text-ink-soft"><?php echo e(number_format($w->pending_balance ?? 0, 2)); ?> ج.م</td>
                            <td class="px-4 py-3 tabular-nums text-muted"><?php echo e($w->transactions_count ?? 0); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($w->is_active ? 'bg-accent-soft text-accent' : 'bg-canvas-muted text-muted'); ?>">
                                    <?php echo e($w->is_active ? 'نعم' : 'لا'); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد محافظ</p>
                                <p class="mt-1 text-xs text-muted">لا توجد محافظ مسجلة.</p>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/accounting/reports-wallets.blade.php ENDPATH**/ ?>