<?php $__env->startSection('title', 'تفاصيل اتفاقية التقسيط'); ?>
<?php $__env->startSection('page_title', 'تفاصيل اتفاقية التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $agreement = $agreement ?? null;
    $plan = $agreement?->plan;
    $student = $agreement?->student;
    $course = $agreement?->course;
    $payments = $agreement?->payments ?? collect();
    $pendingPayments = $payments->where('status', \App\Models\InstallmentPayment::STATUS_PENDING)->sortBy('due_date');
    $nextPayment = $pendingPayments->first();
    $statusBadgeMap = [
        \App\Models\InstallmentAgreement::STATUS_ACTIVE => 'bg-accent-soft text-accent',
        \App\Models\InstallmentAgreement::STATUS_OVERDUE => 'bg-metal/15 text-metal',
        \App\Models\InstallmentAgreement::STATUS_COMPLETED => 'bg-accent-soft text-accent',
        \App\Models\InstallmentAgreement::STATUS_CANCELLED => 'bg-canvas-muted text-muted',
    ];
    $paymentBadgeMap = [
        \App\Models\InstallmentPayment::STATUS_PAID => 'bg-accent-soft text-accent',
        \App\Models\InstallmentPayment::STATUS_OVERDUE => 'bg-metal/15 text-metal',
        \App\Models\InstallmentPayment::STATUS_SKIPPED => 'bg-canvas-muted text-muted',
        \App\Models\InstallmentPayment::STATUS_PENDING => 'bg-canvas-muted text-muted',
    ];
    $statusBadge = $statusBadgeMap[$agreement->status] ?? 'bg-canvas-muted text-muted';
    $kpis = [
        ['label' => 'إجمالي الاتفاقية', 'value' => number_format($agreement->total_amount ?? 0, 2) . ' ج.م', 'icon' => 'fa-wallet', 'tone' => 'accent', 'note' => 'القيمة الكاملة التي سيتم سدادها عبر الخطة'],
        ['label' => 'الدفعة المقدمة', 'value' => number_format($agreement->deposit_amount ?? 0, 2) . ' ج.م', 'icon' => 'fa-hand-holding-usd', 'tone' => 'metal', 'note' => 'تم تحصيلها عند توقيع الاتفاقية'],
        ['label' => 'الأقساط المتبقية', 'value' => $pendingPayments->count(), 'icon' => 'fa-stream', 'tone' => 'accent', 'note' => 'القيمة التالية: ' . (optional($nextPayment)->amount ? number_format($nextPayment->amount, 2) . ' ج.م' : '—')],
        ['label' => 'القسط القادم', 'value' => optional($nextPayment)->due_date?->format('Y-m-d') ?? '—', 'icon' => 'fa-calendar', 'tone' => 'muted', 'note' => 'عدد الأقساط الكلي: ' . $agreement->installments_count],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $paidTotal = $payments->where('status', \App\Models\InstallmentPayment::STATUS_PAID)->sum('amount');
    $remainingTotal = ($agreement->total_amount ?? 0) - $paidTotal;
    $overdueCount = $payments->where('status', \App\Models\InstallmentPayment::STATUS_OVERDUE)->count();
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · التقسيط · اتفاقيات</p>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <h2 class="text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($student->name ?? 'معلم غير معروف'); ?></h2>
                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($statusBadge); ?>">
                    <?php echo e($statuses[$agreement->status] ?? $agreement->status); ?>

                </span>
            </div>
            <p class="mt-1 text-sm text-muted">
                الكورس: <?php echo e($course->title ?? 'خطة عامة'); ?> — بدأت في <?php echo e(optional($agreement->start_date)->format('Y-m-d')); ?>. تتبع أدناه جدول الأقساط والمبالغ المستحقة.
            </p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.installments.agreements.edit', $agreement)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-edit text-xs"></i>
                تعديل الاتفاقية
            </a>
            <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للقائمة
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
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e($kpi['value']); ?></p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">تفاصيل المعلم والكورس</h3>
                    <p class="mt-0.5 text-xs text-muted">بيانات الطالب والكورس المرتبط</p>
                </div>
                <dl class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div>
                        <dt class="text-xs font-medium text-muted">اسم المعلم</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($student->name ?? 'غير متوفر'); ?></dd>
                        <dd class="mt-0.5 text-xs text-muted"><?php echo e($student->phone ?? 'بدون هاتف'); ?> · <?php echo e($student->email ?? 'بدون بريد'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">الكورس</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($course->title ?? 'خطة عامة'); ?></dd>
                        <dd class="mt-0.5 text-xs text-muted">سعر الكورس: <?php echo e(number_format($course->price ?? 0, 2)); ?> ج.م</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">الخطة</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($plan->name ?? 'غير محددة'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">دورية الأقساط</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink">كل <?php echo e($plan->frequency_interval ?? '—'); ?> <?php echo e(($plan && ($frequencyUnits[$plan->frequency_unit] ?? $plan->frequency_unit)) ? ($frequencyUnits[$plan->frequency_unit] ?? $plan->frequency_unit) : '-'); ?></dd>
                        <dd class="mt-0.5 text-xs text-muted">فترة السماح: <?php echo e($plan->grace_period_days ?? 0); ?> يوم</dd>
                    </div>
                </dl>
                <?php if($agreement->notes): ?>
                    <div class="border-t border-line px-4 py-3 text-xs leading-relaxed text-muted sm:px-5">
                        <strong class="text-sm font-semibold text-ink">ملاحظات الاتفاقية:</strong>
                        <p class="mt-2"><?php echo e($agreement->notes); ?></p>
                    </div>
                <?php endif; ?>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">جدول الأقساط</h3>
                        <p class="mt-0.5 text-xs text-muted">جميع الدفعات المجدولة والمدفوعة</p>
                    </div>
                    <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
                        <i class="fas fa-stream me-1 text-[10px]"></i>
                        <?php echo e($payments->count()); ?> دفعات
                    </span>
                </div>
                <div class="admin-table-wrap overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                            <tr>
                                <th class="px-4 py-3 text-start font-medium">#</th>
                                <th class="px-4 py-3 text-start font-medium">تاريخ الاستحقاق</th>
                                <th class="px-4 py-3 text-start font-medium">المبلغ</th>
                                <th class="px-4 py-3 text-start font-medium">الحالة</th>
                                <th class="px-4 py-3 text-start font-medium">ملاحظات</th>
                                <th class="px-4 py-3 text-start font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $payBadge = $paymentBadgeMap[$payment->status] ?? 'bg-canvas-muted text-muted'; ?>
                                <tr class="hover:bg-canvas/40">
                                    <td class="px-4 py-3 font-semibold tabular-nums text-ink"><?php echo e($payment->sequence_number); ?></td>
                                    <td class="px-4 py-3 tabular-nums text-ink-soft"><?php echo e(optional($payment->due_date)->format('Y-m-d')); ?></td>
                                    <td class="px-4 py-3 font-semibold tabular-nums text-ink"><?php echo e(number_format($payment->amount ?? 0, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($payBadge); ?>">
                                            <?php echo e($paymentStatuses[$payment->status] ?? $payment->status); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-ink-soft"><?php echo e($payment->notes ?? '—'); ?></td>
                                    <td class="px-4 py-3">
                                        <form action="<?php echo e(route('admin.installments.agreements.mark-payment', $payment)); ?>" method="POST" class="inline-flex">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="status" value="<?php echo e($payment->status === \App\Models\InstallmentPayment::STATUS_PAID ? \App\Models\InstallmentPayment::STATUS_PENDING : \App\Models\InstallmentPayment::STATUS_PAID); ?>">
                                            <button type="submit" class="btn-press inline-flex h-7 items-center rounded-lg border border-line px-3 text-xs font-medium text-accent hover:border-accent/40">
                                                <?php echo e($payment->status === \App\Models\InstallmentPayment::STATUS_PAID ? 'تعيين كقيد الانتظار' : 'وضع علامة كمدفوع'); ?>

                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <p class="text-sm font-medium text-ink">لم يتم توليد جدول أقساط بعد</p>
                                        <p class="mt-1 text-xs text-muted">لا توجد دفعات مجدولة لهذه الاتفاقية.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </div>

        <div class="space-y-5">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">نظرة سريعة</h3>
                </div>
                <ul class="space-y-3 p-4 text-sm text-ink-soft sm:p-5">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-wallet mt-0.5 text-accent"></i>
                        مجموع ما تم دفعه حتى الآن: <?php echo e(number_format($paidTotal, 2)); ?> ج.م
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-balance-scale mt-0.5 text-accent"></i>
                        المبلغ المتبقي: <?php echo e(number_format($remainingTotal, 2)); ?> ج.م
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle mt-0.5 text-metal"></i>
                        الأقساط المتأخرة: <?php echo e($overdueCount); ?> قسط
                    </li>
                </ul>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">إجراءات إضافية</h3>
                </div>
                <div class="p-4 sm:p-5">
                    <form action="<?php echo e(route('admin.installments.agreements.destroy', $agreement)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء الاتفاقية؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-press flex w-full items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-600 transition hover:border-rose-300">
                            <span>إلغاء الاتفاقية</span>
                            <i class="fas fa-ban text-xs"></i>
                        </button>
                    </form>
                </div>
            </article>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\installments\agreements\show.blade.php ENDPATH**/ ?>