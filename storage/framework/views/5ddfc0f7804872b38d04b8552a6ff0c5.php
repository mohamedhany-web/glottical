

<?php $__env->startSection('title', 'خطط التقسيط - ' . config('app.name')); ?>
<?php $__env->startSection('page_title', 'خطط التقسيط والاشتراكات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $statusBadges = [
        true => ['label' => 'نشطة', 'classes' => 'bg-accent-soft text-accent'],
        false => ['label' => 'معطلة', 'classes' => 'bg-canvas-muted text-muted'],
    ];
    $kpis = [
        ['label' => 'إجمالي القيم الممولة', 'value' => number_format($stats['total_amount'] ?? 0, 2) . ' ج.م', 'icon' => 'fa-coins', 'tone' => 'accent', 'note' => 'إجمالي المبالغ التي تغطيها الخطط الحالية'],
        ['label' => 'إجمالي الدفعات المقدمة', 'value' => number_format($stats['total_deposit'] ?? 0, 2) . ' ج.م', 'icon' => 'fa-piggy-bank', 'tone' => 'metal', 'note' => 'قيمة الدفعات المقدمة المطلوبة عند الاشتراك'],
        ['label' => 'متوسط عدد الأقساط', 'value' => number_format($stats['average_installments'] ?? 0, 1), 'icon' => 'fa-chart-area', 'tone' => 'accent', 'note' => 'متوسط الأقساط لكل خطة تمويلية'],
        ['label' => 'خطط جديدة هذا الشهر', 'value' => number_format($monthlyNew ?? 0), 'icon' => 'fa-calendar-plus', 'tone' => 'muted', 'note' => 'بقيمة ' . number_format($monthlyAmount ?? 0, 2) . ' ج.م منذ بداية الشهر'],
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
            <p class="text-xs font-medium text-muted">المالية · التقسيط</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">لوحة خطط التقسيط</h2>
            <p class="mt-1 text-sm text-muted">تعرّف على أداء خطط الدفع بالتقسيط، قيمها الإجمالية، وعدد الاتفاقيات المرتبطة بها</p>
            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-accent-soft px-2.5 py-1 text-accent">
                    <span class="size-1.5 rounded-full bg-accent"></span>
                    نشطة: <?php echo e(number_format($stats['active'] ?? 0)); ?>

                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-canvas-muted px-2.5 py-1 text-muted">
                    <span class="size-1.5 rounded-full bg-muted"></span>
                    غير نشطة: <?php echo e(number_format($stats['inactive'] ?? 0)); ?>

                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-metal/15 px-2.5 py-1 text-metal">
                    <i class="fas fa-robot text-[10px]"></i>
                    توليد تلقائي: <?php echo e(number_format($stats['auto_generate'] ?? 0)); ?>

                </span>
            </div>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-file-signature text-xs"></i>
                الاتفاقيات
            </a>
            <a href="<?php echo e(route('admin.installments.plans.create')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                إضافة خطة جديدة
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

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="xl:col-span-2 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">توزيع حسب دورية السداد</h3>
                        <p class="mt-0.5 text-xs text-muted">حسب وحدة التكرار</p>
                    </div>
                    <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
                        <i class="fas fa-clock me-1 text-[10px]"></i>
                        <?php echo e($frequencyBreakdown->sum('plans_count')); ?> خطة
                    </span>
                </div>
                <div class="space-y-4 p-4 sm:p-5">
                    <?php $__empty_1 = true; $__currentLoopData = $frequencyBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex size-10 items-center justify-center rounded-xl bg-accent-soft text-accent">
                                    <i class="fas fa-sync-alt text-sm"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-ink"><?php echo e($unitLabels[$item->frequency_unit] ?? $item->frequency_unit); ?></p>
                                    <p class="text-xs text-muted"><?php echo e(number_format($item->plans_count)); ?> خطة</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($item->total_amount, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-muted">لا توجد بيانات للتوزيع حالياً.</p>
                    <?php endif; ?>
                </div>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">أعلى الخطط قيمة</h3>
                    <p class="mt-0.5 text-xs text-muted">الخطط ذات أعلى مبالغ تمويل</p>
                </div>
                <div class="space-y-3 p-4 sm:p-5">
                    <?php $__empty_1 = true; $__currentLoopData = $highValuePlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="rounded-xl border border-line bg-canvas/40 p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-ink"><?php echo e($plan->name); ?></p>
                                <span class="text-xs text-muted"><?php echo e(optional($plan->created_at)->diffForHumans()); ?></span>
                            </div>
                            <p class="mt-1 text-xs text-accent"><?php echo e($plan->course->title ?? 'خطة عامة'); ?></p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($plan->total_amount ?? 0, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></span>
                                <a href="<?php echo e(route('admin.installments.plans.show', $plan)); ?>" class="text-xs font-medium text-accent hover:underline">
                                    تفاصيل <i class="fas fa-arrow-left text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-muted">لا توجد خطط مميزة بعد.</p>
                    <?php endif; ?>
                </div>
            </article>
        </div>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">أحدث الخطط</h3>
                <p class="mt-0.5 text-xs text-muted">آخر الخطط المضافة</p>
            </div>
            <div class="space-y-3 p-4 sm:p-5">
                <?php $__empty_1 = true; $__currentLoopData = $recentPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-line bg-canvas/40 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-ink"><?php echo e($recent->name); ?></p>
                            <span class="text-xs text-muted"><?php echo e(optional($recent->created_at)->diffForHumans()); ?></span>
                        </div>
                        <p class="mt-1 text-xs text-accent"><?php echo e($recent->course->title ?? 'خطة عامة'); ?></p>
                        <p class="mt-1 text-xs text-muted"><?php echo e(number_format($recent->installments_count)); ?> دفعة · كل <?php echo e($recent->frequency_interval); ?> <?php echo e($unitLabels[$recent->frequency_unit] ?? $recent->frequency_unit); ?></p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($recent->total_amount ?? 0, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></span>
                            <a href="<?php echo e(route('admin.installments.plans.show', $recent)); ?>" class="text-xs font-medium text-accent hover:underline">
                                عرض سريع <i class="fas fa-arrow-left text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-muted">لا توجد خطط حديثة.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">قائمة خطط التقسيط</h3>
                <p class="mt-0.5 text-xs text-muted">كل الخطط المتاحة مع تفاصيل المبالغ، الدورية، وعدد الاتفاقيات المرتبطة</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($stats['total'] ?? 0)); ?> خطة</span>
        </div>

        <?php if($plans->count()): ?>
            <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-3">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $badge = $statusBadges[$plan->is_active] ?? null; ?>
                    <div class="flex flex-col gap-4 rounded-2xl border border-line bg-canvas/30 p-5 transition hover:shadow-soft">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-base font-semibold text-ink"><?php echo e($plan->name); ?></h4>
                                    <?php if($badge): ?>
                                        <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($badge['classes']); ?>"><?php echo e($badge['label']); ?></span>
                                    <?php endif; ?>
                                    <?php if($plan->auto_generate_on_enrollment): ?>
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-metal/15 px-2.5 py-1 text-xs font-medium text-metal">
                                            <i class="fas fa-robot text-[10px]"></i>
                                            توليد تلقائي
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-1 text-xs text-accent"><?php echo e($plan->course->title ?? 'خطة عامة'); ?></p>
                            </div>
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent">
                                <i class="fas fa-wallet text-sm"></i>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-[11px] font-medium uppercase text-muted">إجمالي المبلغ</p>
                                <p class="mt-1 font-semibold tabular-nums text-ink"><?php echo e(number_format($plan->total_amount ?? 0, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium uppercase text-muted">دفعة مقدمة</p>
                                <p class="mt-1 font-semibold tabular-nums text-ink"><?php echo e(number_format($plan->deposit_amount ?? 0, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium uppercase text-muted">عدد الأقساط</p>
                                <p class="mt-1 font-medium text-ink"><?php echo e($plan->installments_count); ?> دفعة</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium uppercase text-muted">الدورية</p>
                                <p class="mt-1 font-medium text-ink">كل <?php echo e($plan->frequency_interval); ?> <?php echo e($unitLabels[$plan->frequency_unit] ?? $plan->frequency_unit); ?></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-muted">
                            <span>أضيفت <?php echo e(optional($plan->created_at)->diffForHumans()); ?></span>
                            <span>اتفاقيات: <?php echo e(number_format($plan->agreements_count ?? 0)); ?></span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="<?php echo e(route('admin.installments.plans.show', $plan)); ?>" class="btn-press inline-flex h-8 flex-1 items-center justify-center gap-2 rounded-lg bg-accent px-3 text-xs font-medium text-white">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            <a href="<?php echo e(route('admin.installments.plans.edit', $plan)); ?>" class="btn-press inline-flex size-8 items-center justify-center rounded-lg border border-line text-ink transition hover:border-accent/30 hover:text-accent" title="تعديل">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="<?php echo e(route('admin.installments.plans.destroy', $plan)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-press inline-flex size-8 items-center justify-center rounded-lg border border-line text-rose-600 transition hover:border-rose-300 hover:bg-rose-50" title="حذف">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($plans->hasPages()): ?>
                <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($plans->withQueryString()->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="px-4 py-16 text-center sm:px-5">
                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                    <i class="fas fa-folder-open"></i>
                </div>
                <p class="text-sm font-medium text-ink">لا توجد خطط تقسيط بعد</p>
                <p class="mt-1 text-xs text-muted">ابدأ بإنشاء أول خطة لتفعيل نظام الأقساط.</p>
                <a href="<?php echo e(route('admin.installments.plans.create')); ?>" class="btn-press mt-4 inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                    <i class="fas fa-plus text-xs"></i>
                    إضافة خطة جديدة
                </a>
            </div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\installments\plans\index.blade.php ENDPATH**/ ?>