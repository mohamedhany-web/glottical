

<?php $__env->startSection('title', 'تفاصيل خطة التقسيط'); ?>
<?php $__env->startSection('page_title', 'تفاصيل خطة التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $agreements = $plan->agreements ?? collect();
    $agreementsCount = $agreements->count();
    $activeAgreements = $agreements->where('status', 'active')->count();
    $totalFinanced = $agreements->sum('total_amount');
    $totalDeposits = $agreements->sum('deposit_amount');
    $averageInstallments = $agreementsCount > 0 ? round($agreements->avg('installments_count'), 1) : $plan->installments_count;
    $frequencyLabel = $frequencyUnits[$plan->frequency_unit] ?? $plan->frequency_unit;
    $statusBadge = $plan->is_active
        ? ['label' => 'خطة نشطة', 'classes' => 'bg-accent-soft text-accent']
        : ['label' => 'خطة معطلة', 'classes' => 'bg-canvas-muted text-muted'];
    $kpis = [
        ['label' => 'إجمالي المبلغ', 'value' => number_format($plan->total_amount ?? 0, 2) . ' ج.م', 'icon' => 'fa-coins', 'tone' => 'accent', 'note' => 'القيمة الكاملة للخطة قبل الدفعات المقدمة'],
        ['label' => 'الدفعة المقدمة', 'value' => number_format($plan->deposit_amount ?? 0, 2) . ' ج.م', 'icon' => 'fa-hand-holding-usd', 'tone' => 'metal', 'note' => 'المبلغ المطلوب دفعه مقدماً قبل بدء التقسيط'],
        ['label' => 'عدد الاتفاقيات', 'value' => number_format($agreementsCount), 'icon' => 'fa-users', 'tone' => 'accent', 'note' => number_format($activeAgreements) . ' اتفاقيات نشطة مرتبطة بالخطة'],
        ['label' => 'متوسط الأقساط', 'value' => number_format($averageInstallments, 1), 'icon' => 'fa-chart-line', 'tone' => 'muted', 'note' => 'متوسط عدد الدفعات للاتفاقيات المرتبطة'],
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
            <p class="text-xs font-medium text-muted">المالية · التقسيط · خطط</p>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <h2 class="text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($plan->name); ?></h2>
                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($statusBadge['classes']); ?>"><?php echo e($statusBadge['label']); ?></span>
                <?php if($plan->auto_generate_on_enrollment): ?>
                    <span class="inline-flex items-center gap-1 rounded-lg bg-metal/15 px-2.5 py-1 text-xs font-medium text-metal">
                        <i class="fas fa-robot text-[10px]"></i>
                        توليد تلقائي
                    </span>
                <?php endif; ?>
            </div>
            <p class="mt-1 text-sm text-muted"><?php echo e($plan->description ?: 'لا توجد ملاحظات إضافية لهذه الخطة.'); ?></p>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-muted">
                <span class="inline-flex items-center gap-1"><i class="fas fa-book text-[10px]"></i> <?php echo e($plan->course->title ?? 'خطة عامة'); ?></span>
                <span>·</span>
                <span class="inline-flex items-center gap-1"><i class="fas fa-calendar-alt text-[10px]"></i> كل <?php echo e($plan->frequency_interval); ?> <?php echo e($frequencyLabel); ?> · <?php echo e($plan->installments_count); ?> دفعة</span>
                <span>·</span>
                <span class="inline-flex items-center gap-1"><i class="fas fa-clock text-[10px]"></i> فترة سماح <?php echo e($plan->grace_period_days); ?> يوم</span>
            </div>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.installments.plans.edit', $plan)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-edit text-xs"></i>
                تعديل الخطة
            </a>
            <a href="<?php echo e(route('admin.installments.plans.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
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
                    <h3 class="text-base font-semibold text-ink">تفاصيل الخطة</h3>
                    <p class="mt-0.5 text-xs text-muted">معلومات الكورس والقيم الممولة</p>
                </div>
                <dl class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div>
                        <dt class="text-xs font-medium text-muted">الكورس المرتبط</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($plan->course->title ?? 'خطة عامة'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">القيمة الممولة</dt>
                        <dd class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($totalFinanced, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">إجمالي الدفعات المقدمة</dt>
                        <dd class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($totalDeposits, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">ملاحظات إضافية</dt>
                        <dd class="mt-1 text-sm font-medium text-ink"><?php echo e(data_get($plan->metadata, 'notes', '—')); ?></dd>
                    </div>
                </dl>
                <div class="border-t border-line px-4 py-3 text-xs text-muted sm:px-5">
                    عند تفعيل "التوليد التلقائي" سيتم إنشاء خطة أقساط مباشرة للطالب عند تسجيله في الكورس المرتبط.
                </div>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">الاتفاقيات المرتبطة</h3>
                        <p class="mt-0.5 text-xs text-muted">اتفاقيات الطلاب المرتبطة بهذه الخطة</p>
                    </div>
                    <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
                        <i class="fas fa-file-signature me-1 text-[10px]"></i>
                        <?php echo e($agreementsCount); ?> اتفاقية
                    </span>
                </div>
                <?php if($agreementsCount > 0): ?>
                    <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2">
                        <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="space-y-3 rounded-xl border border-line bg-canvas/40 p-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-ink"><?php echo e($agreement->user->name ?? 'مستخدم غير معروف'); ?></p>
                                    <span class="text-xs text-muted"><?php echo e(optional($agreement->created_at)->diffForHumans()); ?></span>
                                </div>
                                <p class="text-xs text-accent"><?php echo e($agreement->course->title ?? 'خطة عامة'); ?></p>
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <p class="text-[11px] font-medium uppercase text-muted">إجمالي الاتفاقية</p>
                                        <p class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($agreement->total_amount ?? 0, 2)); ?> <span class="font-normal text-muted">ج.م</span></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase text-muted">دفعة مقدمة</p>
                                        <p class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($agreement->deposit_amount ?? 0, 2)); ?> <span class="font-normal text-muted">ج.م</span></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase text-muted">عدد الأقساط</p>
                                        <p class="mt-1 text-sm font-medium text-ink"><?php echo e($agreement->installments_count); ?> دفعة</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase text-muted">الحالة</p>
                                        <p class="mt-1 text-sm font-medium text-ink"><?php echo e($agreement->status); ?></p>
                                    </div>
                                </div>
                                <?php if($agreement->notes): ?>
                                    <p class="text-xs leading-relaxed text-muted"><?php echo e($agreement->notes); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="px-4 py-12 text-center sm:px-5">
                        <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <p class="text-sm font-medium text-ink">لا توجد اتفاقيات مرتبطة</p>
                        <p class="mt-1 text-xs text-muted">لا توجد اتفاقيات مرتبطة بهذه الخطة حتى الآن.</p>
                    </div>
                <?php endif; ?>
            </article>
        </div>

        <div class="space-y-5">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">مخطط الدفعات</h3>
                </div>
                <ul class="space-y-3 p-4 text-sm text-ink-soft sm:p-5">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-hand-holding-usd mt-0.5 text-accent"></i>
                        الدفعة المقدمة: <?php echo e(number_format($plan->deposit_amount ?? 0, 2)); ?> ج.م تُسدد عند التعاقد.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-calendar-alt mt-0.5 text-accent"></i>
                        الأقساط: <?php echo e($plan->installments_count); ?> دفعة، كل <?php echo e($plan->frequency_interval); ?> <?php echo e($frequencyLabel); ?>.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-clock mt-0.5 text-accent"></i>
                        فترة سماح قبل إعتبار القسط متأخراً: <?php echo e($plan->grace_period_days); ?> يوم.
                    </li>
                </ul>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">إجراءات سريعة</h3>
                </div>
                <div class="space-y-2 p-4 sm:p-5">
                    <a href="<?php echo e(route('admin.installments.plans.edit', $plan)); ?>" class="btn-press flex items-center justify-between rounded-xl border border-line bg-canvas/40 px-4 py-3 text-accent transition hover:border-accent/30">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex size-10 items-center justify-center rounded-xl bg-accent-soft text-accent">
                                <i class="fas fa-pen text-sm"></i>
                            </span>
                            <span class="text-sm font-semibold">تعديل بيانات الخطة</span>
                        </div>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <a href="<?php echo e(route('admin.installments.plans.index')); ?>" class="btn-press flex items-center justify-between rounded-xl border border-line bg-canvas/40 px-4 py-3 text-ink transition hover:border-accent/30 hover:text-accent">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex size-10 items-center justify-center rounded-xl bg-canvas-muted text-muted">
                                <i class="fas fa-list text-sm"></i>
                            </span>
                            <span class="text-sm font-semibold">العودة إلى قائمة الخطط</span>
                        </div>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </article>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\installments\plans\show.blade.php ENDPATH**/ ?>