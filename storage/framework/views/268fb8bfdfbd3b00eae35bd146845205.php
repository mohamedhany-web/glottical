

<?php $__env->startSection('title', 'ماليات المدرب - ' . $instructor->name); ?>
<?php $__env->startSection('page_title', 'ماليات المدرب - ' . $instructor->name); ?>
<?php $__env->startSection('header', 'ماليات المدرب - ' . $instructor->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $agreementStatusBadges = [
        'active' => ['label' => 'نشط', 'classes' => 'bg-accent-soft text-accent'],
        'draft' => ['label' => 'مسودة', 'classes' => 'bg-canvas-muted text-muted'],
        'completed' => ['label' => 'مكتمل', 'classes' => 'bg-accent-soft text-accent'],
    ];
    $paymentStatusBadges = [
        'pending' => ['label' => 'قيد المراجعة', 'classes' => 'bg-canvas-muted text-muted'],
        'approved' => ['label' => 'مطلوب الدفع', 'classes' => 'bg-metal/15 text-metal'],
        'paid' => ['label' => 'تم الدفع', 'classes' => 'bg-accent-soft text-accent'],
    ];
    $kpis = [
        ['label' => 'مطلوب الدفع لهذا المدرب', 'value' => number_format($pendingTotal, 2), 'suffix' => 'ج.م', 'icon' => 'fa-hourglass-half', 'tone' => 'metal'],
        ['label' => 'تم الدفع لهذا المدرب', 'value' => number_format($paidTotal, 2), 'suffix' => 'ج.م', 'icon' => 'fa-check-circle', 'tone' => 'accent'],
    ];
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · الماليات · <?php echo e($instructor->name); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($instructor->name); ?></h2>
            <p class="mt-1 text-sm text-muted">جميع المطلوب دفعه والمدفوع — يمكنك الدفع مسبقاً أو في أي وقت</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.salaries.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة إلى قائمة المدربين
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
                    <?php echo e($kpi['value']); ?>

                    <span class="text-sm font-normal text-muted"><?php echo e($kpi['suffix']); ?></span>
                </p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">جدول الاتفاقيات</h3>
            <p class="mt-0.5 text-xs text-muted">الاتفاقيات المرتبطة بهذا المدرب — يمكنك دفع المبلغ الآن من زر «دفع الآن»</p>
        </div>

        <?php if($agreements->count() > 0): ?>
            <div class="admin-table-wrap overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                        <tr>
                            <th class="px-4 py-3 text-start font-medium">رقم الاتفاقية</th>
                            <th class="px-4 py-3 text-start font-medium">العنوان</th>
                            <th class="px-4 py-3 text-start font-medium">النوع</th>
                            <th class="px-4 py-3 text-start font-medium">المبلغ/المعدل (ج.م)</th>
                            <th class="px-4 py-3 text-start font-medium">من</th>
                            <th class="px-4 py-3 text-start font-medium">إلى</th>
                            <th class="px-4 py-3 text-start font-medium">الحالة</th>
                            <th class="px-4 py-3 text-start font-medium">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $agrBadge = $agreementStatusBadges[$agr->status] ?? ['label' => $agr->status ?? '—', 'classes' => 'bg-canvas-muted text-muted']; ?>
                            <tr class="hover:bg-canvas/40">
                                <td class="px-4 py-3 font-mono text-xs text-muted"><?php echo e($agr->agreement_number ?? '—'); ?></td>
                                <td class="px-4 py-3 font-medium text-ink"><?php echo e($agr->title ?? '—'); ?></td>
                                <td class="px-4 py-3 text-ink-soft">
                                    <?php if($agr->type === 'monthly_salary'): ?> راتب شهري
                                    <?php elseif($agr->type === 'hourly_rate'): ?> سعر بالساعة
                                    <?php elseif($agr->type === 'course_price'): ?> سعر للكورس
                                    <?php else: ?> <?php echo e($agr->type ?? '—'); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-semibold tabular-nums text-ink"><?php echo e(number_format((float) ($agr->rate ?? 0), 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></td>
                                <td class="px-4 py-3 text-xs tabular-nums text-muted"><?php echo e($agr->start_date ? $agr->start_date->format('Y-m-d') : '—'); ?></td>
                                <td class="px-4 py-3 text-xs tabular-nums text-muted"><?php echo e($agr->end_date ? $agr->end_date->format('Y-m-d') : '—'); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($agrBadge['classes']); ?>"><?php echo e($agrBadge['label']); ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if((float) ($agr->rate ?? 0) > 0): ?>
                                        <form action="<?php echo e(route('admin.salaries.pay-now-from-agreement', [$instructor, $agr])); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn-press inline-flex h-8 items-center gap-1 rounded-lg bg-accent px-3 text-xs font-medium text-white">
                                                <i class="fas fa-money-bill-wave"></i>
                                                دفع الآن
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-muted">لا يوجد مبلغ</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="px-4 py-12 text-center">
                <p class="text-sm font-medium text-ink">لا توجد اتفاقيات لهذا المدرب.</p>
            </div>
        <?php endif; ?>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">قائمة المدفوعات</h3>
                <p class="mt-0.5 text-xs text-muted">المطلوب دفعه والمدفوع — دفع وتحويل من الزر أدناه لأي مدفوعة مطلوب دفعها</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($payments->count())); ?> مدفوعة</span>
        </div>

        <?php if($payments->count() > 0): ?>
            <div class="admin-table-wrap overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                        <tr>
                            <th class="px-4 py-3 text-start font-medium">رقم المدفوعة</th>
                            <th class="px-4 py-3 text-start font-medium">الاتفاقية</th>
                            <th class="px-4 py-3 text-start font-medium">النوع</th>
                            <th class="px-4 py-3 text-start font-medium">المبلغ</th>
                            <th class="px-4 py-3 text-start font-medium">الحالة</th>
                            <th class="px-4 py-3 text-start font-medium">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $pBadge = $paymentStatusBadges[$p->status] ?? ['label' => $p->status, 'classes' => 'bg-canvas-muted text-muted']; ?>
                            <tr class="hover:bg-canvas/40">
                                <td class="px-4 py-3 font-mono text-xs text-muted"><?php echo e($p->payment_number); ?></td>
                                <td class="px-4 py-3 text-ink-soft"><?php echo e($p->agreement->title ?? '—'); ?></td>
                                <td class="px-4 py-3 text-ink-soft"><?php echo e($p->type_label); ?></td>
                                <td class="px-4 py-3 font-semibold tabular-nums text-ink"><?php echo e(number_format($p->amount, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($pBadge['classes']); ?>"><?php echo e($pBadge['label']); ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($p->status === 'pending'): ?>
                                        <span class="text-xs text-muted">في انتظار الاعتماد</span>
                                    <?php elseif($p->status === 'approved'): ?>
                                        <a href="<?php echo e(route('admin.salaries.pay', $p)); ?>" class="btn-press inline-flex h-8 items-center gap-1 rounded-lg bg-accent px-3 text-xs font-medium text-white">
                                            <i class="fas fa-money-bill-wave"></i>
                                            دفع وتحويل
                                        </a>
                                    <?php else: ?>
                                        <?php if($p->transfer_receipt_path): ?>
                                            <a href="<?php echo e(storage_asset($p->transfer_receipt_path)); ?>" target="_blank" class="btn-press inline-flex h-8 items-center gap-1 rounded-lg border border-line px-3 text-xs font-medium text-ink hover:border-accent/30 hover:text-accent">
                                                <i class="fas fa-receipt"></i>
                                                إيصال
                                            </a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="px-4 py-16 text-center">
                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <p class="text-sm font-medium text-ink">لا توجد مدفوعات مسجّلة لهذا المدرب.</p>
                <p class="mt-1 text-xs text-muted">يمكن إنشاء مدفوعات من الاتفاقيات من إدارة الاتفاقيات.</p>
            </div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\salaries\instructor.blade.php ENDPATH**/ ?>