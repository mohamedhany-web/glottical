

<?php $__env->startSection('title', 'Glottical CRM - Glottical'); ?>
<?php $__env->startSection('page_title', 'لوحة CRM'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $kpis = [
        ['label' => 'كل العملاء', 'value' => $stats['total_leads'] ?? 0, 'icon' => 'fa-users', 'tone' => 'accent', 'note' => 'إجمالي العملاء المحتملين'],
        ['label' => 'مفتوحة', 'value' => $stats['open_leads'] ?? 0, 'icon' => 'fa-folder-open', 'tone' => 'accent', 'note' => 'ما زالت قيد المتابعة'],
        ['label' => 'بانتظار الدفع', 'value' => $stats['payment_pending'] ?? 0, 'icon' => 'fa-clock', 'tone' => 'metal', 'note' => 'مرحلة الدفع'],
        ['label' => 'مغلقة ناجحة', 'value' => $stats['closed_won'] ?? 0, 'icon' => 'fa-check-circle', 'tone' => 'accent', 'note' => 'صفقات مكتملة'],
        ['label' => 'عمولات معلقة', 'value' => $stats['commissions_pending'] ?? 0, 'icon' => 'fa-hourglass-half', 'tone' => 'muted', 'note' => 'بانتظار الصرف'],
        ['label' => 'إجمالي عمولات', 'value' => number_format((float) ($stats['commissions_total'] ?? 0), 0), 'icon' => 'fa-coins', 'tone' => 'metal', 'note' => 'EGP · كل الحالات', 'raw' => true],
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
            <p class="text-xs font-medium text-muted">المبيعات · Glottical CRM</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">لوحة CRM</h2>
            <p class="mt-1 text-sm text-muted">متابعة العملاء والعمولات ومسار البيع من مكان واحد</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.crm.pipeline')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-columns text-xs"></i>
                Pipeline
            </a>
            <a href="<?php echo e(route('admin.crm.leads.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-user-plus text-xs"></i>
                العملاء المحتملون
            </a>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">القاعدة الذهبية</h3>
            <p class="mt-0.5 text-xs text-muted">ما لم يُسجَّل في CRM، لم يحدث</p>
        </div>
        <div class="flex items-start gap-3 p-4 sm:p-5">
            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-shield-alt"></i>
            </span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-ink">أي تواصل أو بيع أو عمولة يجب أن يمر عبر النظام</p>
                <p class="mt-1 text-xs text-muted">وإلا لن يُحتسب ضمن التقارير أو العمولات.</p>
            </div>
        </div>
    </article>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
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
                        <?php echo e(number_format((int) $kpi['value'])); ?>

                    <?php endif; ?>
                </p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <div class="grid gap-5 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                <div>
                    <h3 class="text-base font-semibold text-ink">آخر العملاء المحتملين</h3>
                    <p class="mt-0.5 text-xs text-muted">أحدث 10 سجلات</p>
                </div>
                <a href="<?php echo e(route('admin.crm.leads.index')); ?>" class="text-xs font-semibold text-accent hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-line">
                <?php $__empty_1 = true; $__currentLoopData = $recentLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('admin.crm.leads.show', $lead)); ?>" class="flex items-center justify-between gap-3 px-4 py-3 transition hover:bg-canvas/50 sm:px-5">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink"><?php echo e($lead->name); ?></p>
                            <p class="mt-0.5 text-[11px] text-muted">
                                سيلز: <?php echo e($lead->assignedTo?->name ?? '—'); ?>

                                · تسويق: <?php echo e($lead->marketingOwner?->name ?? '—'); ?>

                            </p>
                        </div>
                        <span class="shrink-0 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e($lead->status_label); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-16 text-center sm:px-5">
                        <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <p class="text-sm font-medium text-ink">لا يوجد عملاء بعد</p>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">توزيع الحالات</h3>
                <p class="mt-0.5 text-xs text-muted">عدد العملاء في كل مرحلة</p>
            </div>
            <div class="divide-y divide-line">
                <?php $__currentLoopData = \App\Models\SalesLead::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5">
                        <span class="text-sm text-ink-soft"><?php echo e($label); ?></span>
                        <span class="text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format((int) ($statusBreakdown[$key] ?? 0))); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </article>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\dashboard.blade.php ENDPATH**/ ?>