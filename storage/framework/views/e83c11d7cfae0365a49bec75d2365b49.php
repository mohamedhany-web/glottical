<?php $__env->startSection('title', 'لوحة الإدارة - Glottical'); ?>
<?php $__env->startSection('page_title', 'لوحة الإدارة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $ds = $dashboardShow ?? [];
    $hasAnyDashboardWidget = collect($ds)->filter(fn ($v) => (bool) $v)->isNotEmpty();
    $trendPct = function (?array $trend): ?float {
        if (! is_array($trend) || ! array_key_exists('percent', $trend)) {
            return null;
        }
        return (float) $trend['percent'];
    };
    $spark = function (?float $pct): array {
        $base = [42, 48, 45, 52, 58, 55, 62];
        if ($pct === null) {
            return $base;
        }
        $factor = max(0.55, min(1.15, 0.85 + ($pct / 100)));
        return array_map(fn ($v) => (int) round($v * $factor), $base);
    };
    $ordersPending = (int) data_get($salesSection, 'orders_pending', 0);
    $recentOrders = collect(data_get($salesSection, 'recent_orders', []));
    $currency = 'ج.م';

    $studentsTotal = (int) data_get($metrics, 'students.total', data_get($stats, 'total_students', 0));
    $coursesTotal = (int) data_get($metrics, 'courses.total', data_get($stats, 'total_courses', 0));
    $enrollmentsTotal = (int) data_get($metrics, 'enrollments.total', data_get($stats, 'total_enrollments', 0));
    $usersTotal = (int) data_get($metrics, 'users.total', data_get($stats, 'total_users', 0));
    $funnelBase = max(1, $usersTotal ?: $studentsTotal);
    $funnelSteps = [
        ['label' => '1. المستخدمون', 'count' => $usersTotal ?: $studentsTotal, 'pct' => 100, 'opacity' => '0.35', 'accent' => false],
        ['label' => '2. الطلاب', 'count' => $studentsTotal, 'pct' => round(($studentsTotal / $funnelBase) * 100, 1), 'opacity' => '0.45', 'accent' => false],
        ['label' => '3. الكورسات النشطة', 'count' => $coursesTotal, 'pct' => round(($coursesTotal / $funnelBase) * 100, 1), 'opacity' => '0.55', 'accent' => false],
        ['label' => '4. طلبات معلّقة', 'count' => $ordersPending, 'pct' => round(($ordersPending / $funnelBase) * 100, 1), 'opacity' => '0.65', 'accent' => false],
        ['label' => '5. اشتراكات نشطة', 'count' => $enrollmentsTotal, 'pct' => round(($enrollmentsTotal / $funnelBase) * 100, 1), 'opacity' => '1', 'accent' => true],
    ];
    $conversionPct = $usersTotal > 0 ? round(($enrollmentsTotal / max(1, $usersTotal)) * 100, 1) : 0;

    $alertCount = count($quickActions ?? []);
    $pendingInvoicesCount = (int) data_get($metrics, 'pending_invoices.total', data_get($stats, 'pending_invoices', 0));
    $weekTotal = collect($weeklyActivity ?? [])->sum('count');
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">مرحباً، <?php echo e(auth()->user()->name); ?> · نظرة حسب صلاحيات دورك</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">نظرة تشغيلية لليوم</h2>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <?php if(! empty($ds['sales_section']) && Route::has('admin.orders.index')): ?>
                <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending'])); ?>" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">
                    الطلبات المعلّقة<?php if($ordersPending > 0): ?> (<?php echo e(number_format($ordersPending)); ?>)<?php endif; ?>
                </a>
            <?php endif; ?>
            <?php if(! empty($ds['courses_metric']) && Route::has('admin.courses.create')): ?>
                <a href="<?php echo e(route('admin.courses.create')); ?>" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-canvas">
                    كورس جديد
                </a>
            <?php endif; ?>
        </div>
    </section>

    <?php if(isset($dashboardShow) && ! $hasAnyDashboardWidget): ?>
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm text-ink shadow-soft">
            لا توجد بطاقات إحصائية لعرضها حالياً. اطلب من المسؤول إسناد الصلاحيات المناسبة لدورك.
        </div>
    <?php endif; ?>

    
    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <?php if(! empty($ds['monthly_revenue']) || ! empty($ds['revenue_total'])): ?>
            <?php
                $revTrend = $trendPct($metrics['monthly_revenue']['trend'] ?? null);
                $bars = $spark($revTrend);
                $revValue = ! empty($ds['monthly_revenue'])
                    ? ($metrics['monthly_revenue']['current'] ?? $stats['monthly_revenue'] ?? 0)
                    : ($stats['total_revenue'] ?? 0);
            ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                    <?php if($revTrend !== null): ?>
                        <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold tabular-nums <?php echo e($revTrend >= 0 ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($revTrend >= 0 ? '+' : ''); ?><?php echo e(number_format($revTrend, 1)); ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e(! empty($ds['monthly_revenue']) ? 'إيراد هذا الشهر' : 'إجمالي الإيراد'); ?></p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format((float) $revValue, 0)); ?> <?php echo e($currency); ?></p>
                <div class="mt-2 flex h-8 items-end gap-0.5">
                    <?php $__currentLoopData = $bars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="w-2 rounded-t <?php echo e($i === count($bars) - 1 ? 'bg-accent' : 'bg-accent/30'); ?>" style="height:<?php echo e($h); ?>%"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="mt-1 text-[11px] text-muted">مقارنة بالفترة السابقة</p>
            </article>
        <?php endif; ?>

        <?php if(! empty($ds['students_metric'])): ?>
            <?php $st = $metrics['students'] ?? []; $stTrend = $trendPct($st['trend'] ?? null); $bars = $spark($stTrend); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </div>
                    <?php if($stTrend !== null): ?>
                        <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold tabular-nums <?php echo e($stTrend >= 0 ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($stTrend >= 0 ? '+' : ''); ?><?php echo e(number_format($stTrend, 1)); ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-xs text-muted">الطلاب</p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($st['total'] ?? 0)); ?></p>
                <div class="mt-2 flex h-8 items-end gap-0.5">
                    <?php $__currentLoopData = $bars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="w-2 rounded-t <?php echo e($i === count($bars) - 1 ? 'bg-accent' : 'bg-accent/30'); ?>" style="height:<?php echo e($h); ?>%"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="mt-1 text-[11px] text-muted">هذا الشهر: <?php echo e(number_format($st['new_this_month'] ?? 0)); ?></p>
            </article>
        <?php elseif(! empty($ds['users_metric'])): ?>
            <?php $us = $metrics['users'] ?? []; $usTrend = $trendPct($us['trend'] ?? null); $bars = $spark($usTrend); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                    <?php if($usTrend !== null): ?>
                        <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold tabular-nums <?php echo e($usTrend >= 0 ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($usTrend >= 0 ? '+' : ''); ?><?php echo e(number_format($usTrend, 1)); ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-xs text-muted">المستخدمون</p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($us['total'] ?? 0)); ?></p>
                <div class="mt-2 flex h-8 items-end gap-0.5">
                    <?php $__currentLoopData = $bars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="w-2 rounded-t <?php echo e($i === count($bars) - 1 ? 'bg-accent' : 'bg-accent/30'); ?>" style="height:<?php echo e($h); ?>%"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="mt-1 text-[11px] text-muted">هذا الشهر: <?php echo e(number_format($us['new_this_month'] ?? 0)); ?></p>
            </article>
        <?php endif; ?>

        <?php if(! empty($ds['courses_metric'])): ?>
            <?php $co = $metrics['courses'] ?? []; $coTrend = $trendPct($co['trend'] ?? null); $bars = $spark($coTrend); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </div>
                    <?php if($coTrend !== null): ?>
                        <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold tabular-nums <?php echo e($coTrend >= 0 ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($coTrend >= 0 ? '+' : ''); ?><?php echo e(number_format($coTrend, 1)); ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-xs text-muted">الكورسات</p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($co['total'] ?? 0)); ?></p>
                <div class="mt-2 flex h-8 items-end gap-0.5">
                    <?php $__currentLoopData = $bars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="w-2 rounded-t <?php echo e($i === count($bars) - 1 ? 'bg-accent' : 'bg-accent/30'); ?>" style="height:<?php echo e($h); ?>%"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="mt-1 text-[11px] text-muted">جديد هذا الشهر: <?php echo e(number_format($co['new_this_month'] ?? 0)); ?></p>
            </article>
        <?php endif; ?>

        <?php if(! empty($ds['enrollments_metric'])): ?>
            <?php $en = $metrics['enrollments'] ?? []; $enTrend = $trendPct($en['trend'] ?? null); $bars = $spark($enTrend); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-user-plus text-sm"></i>
                    </div>
                    <?php if($enTrend !== null): ?>
                        <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold tabular-nums <?php echo e($enTrend >= 0 ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($enTrend >= 0 ? '+' : ''); ?><?php echo e(number_format($enTrend, 1)); ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-xs text-muted">الاشتراكات النشطة</p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($en['total'] ?? 0)); ?></p>
                <div class="mt-2 flex h-8 items-end gap-0.5">
                    <?php $__currentLoopData = $bars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="w-2 rounded-t <?php echo e($i === count($bars) - 1 ? 'bg-accent' : 'bg-accent/30'); ?>" style="height:<?php echo e($h); ?>%"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="mt-1 text-[11px] text-muted">هذا الشهر: <?php echo e(number_format($en['new_this_month'] ?? 0)); ?></p>
            </article>
        <?php elseif(! empty($ds['instructors_metric'])): ?>
            <?php $ins = $metrics['instructors'] ?? []; $insTrend = $trendPct($ins['trend'] ?? null); $bars = $spark($insTrend); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-chalkboard-teacher text-sm"></i>
                    </div>
                    <?php if($insTrend !== null): ?>
                        <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold tabular-nums <?php echo e($insTrend >= 0 ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($insTrend >= 0 ? '+' : ''); ?><?php echo e(number_format($insTrend, 1)); ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-xs text-muted">المدربون</p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($ins['total'] ?? 0)); ?></p>
                <div class="mt-2 flex h-8 items-end gap-0.5">
                    <?php $__currentLoopData = $bars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="w-2 rounded-t <?php echo e($i === count($bars) - 1 ? 'bg-accent' : 'bg-accent/30'); ?>" style="height:<?php echo e($h); ?>%"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="mt-1 text-[11px] text-muted">هذا الشهر: <?php echo e(number_format($ins['new_this_month'] ?? 0)); ?></p>
            </article>
        <?php endif; ?>
    </section>

    
    <section class="grid gap-5 xl:grid-cols-[1.55fr_0.95fr]">
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft md:p-6">
            <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-ink">نشاط المنصة · 7 أيام</h3>
                    <p class="mt-1 text-xs text-muted">سجل النشاط اليومي على الأكاديمية</p>
                </div>
                <div class="text-start">
                    <p class="text-xl font-semibold tabular-nums text-ink"><?php echo e(number_format($weekTotal)); ?></p>
                    <p class="mt-0.5 text-xs font-semibold text-muted">حدث هذا الأسبوع</p>
                </div>
            </div>
            <?php if(! empty($ds['activity_feed']) && collect($weeklyActivity ?? [])->isNotEmpty()): ?>
                <?php
                    $week = collect($weeklyActivity);
                    $max = max(1, (int) $week->max('count'));
                ?>
                <div class="flex h-36 items-end gap-1.5 sm:gap-2">
                    <?php $__currentLoopData = $week; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $h = max(8, (int) round(((int) $day->count / $max) * 100)); ?>
                        <div class="flex min-w-0 flex-1 flex-col items-center gap-1.5">
                            <span class="w-full max-w-[28px] rounded-t-lg bg-accent/80" style="height:<?php echo e($h); ?>%" title="<?php echo e($day->count); ?>"></span>
                            <span class="text-[10px] tabular-nums text-muted"><?php echo e(\Illuminate\Support\Carbon::parse($day->date)->format('d')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-3 flex items-center gap-4 text-[11px] text-muted">
                    <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-accent"></span>نشاط يومي</span>
                </div>
            <?php else: ?>
                <div class="flex h-36 items-center justify-center rounded-xl bg-canvas text-sm text-muted">
                    لا توجد بيانات نشاط للعرض
                </div>
            <?php endif; ?>
        </article>

        <div class="grid gap-5">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-ink">لمحة سريعة</h3>
                        <p class="mt-1 text-xs text-muted">مؤشرات الأكاديمية الآن</p>
                    </div>
                    <i class="fas fa-clock text-sm text-muted"></i>
                </div>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center justify-between gap-3">
                        <span class="text-muted">طلاب</span>
                        <span class="font-semibold tabular-nums text-ink"><?php echo e(number_format($studentsTotal)); ?></span>
                    </li>
                    <li class="flex items-center justify-between gap-3">
                        <span class="text-muted">كورسات</span>
                        <span class="font-semibold tabular-nums text-ink"><?php echo e(number_format($coursesTotal)); ?></span>
                    </li>
                    <li class="flex items-center justify-between gap-3">
                        <span class="text-muted">اشتراكات نشطة</span>
                        <span class="font-semibold tabular-nums text-ink"><?php echo e(number_format($enrollmentsTotal)); ?></span>
                    </li>
                    <li class="flex items-center justify-between gap-3">
                        <span class="text-muted">طلبات معلّقة</span>
                        <span class="font-semibold tabular-nums text-ink"><?php echo e(number_format($ordersPending)); ?></span>
                    </li>
                </ul>
            </article>
        </div>
    </section>

    
    <?php if($hasAnyDashboardWidget): ?>
    <section class="grid gap-5 lg:grid-cols-[1.2fr_1fr]">
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft md:p-6">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-ink">قمع الأكاديمية</h3>
                    <p class="mt-1 text-xs text-muted">من المستخدم حتى الاشتراك النشط · نسب نسبية</p>
                </div>
                <span class="rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($conversionPct, 1)); ?>% تحويل</span>
            </div>
            <div class="space-y-3">
                <?php $__currentLoopData = $funnelSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $barW = max(2, min(100, (float) $step['pct'])); ?>
                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                            <span class="font-medium text-ink"><?php echo e($step['label']); ?></span>
                            <span class="shrink-0 tabular-nums text-muted"><?php echo e(number_format($step['count'])); ?> · <?php echo e(number_format($step['pct'], 1)); ?>%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-[#eef1f5]">
                            <div class="h-full rounded-full <?php echo e($step['accent'] ? 'bg-accent' : 'bg-ink'); ?>" style="width:<?php echo e($barW); ?>%;opacity:<?php echo e($step['accent'] ? '1' : $step['opacity']); ?>"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft md:p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-ink">تنبيهات تحتاج إجراء</h3>
                <span class="text-xs text-muted"><?php echo e($alertCount); ?></span>
            </div>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = ($quickActions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e($action['route']); ?>" class="block rounded-xl border border-line bg-[#f7f8fa] px-4 py-3 transition hover:border-accent/30 hover:bg-accent-soft/40">
                        <div class="flex items-center justify-between gap-3">
                            <p class="min-w-0 truncate text-sm font-semibold text-ink"><?php echo e($action['title']); ?></p>
                            <?php
                                $prio = ((int) ($action['count'] ?? 0)) > 0 ? 'عاجل' : 'معلومة';
                                $prioClass = ((int) ($action['count'] ?? 0)) > 0 ? 'bg-metal/15 text-metal' : 'bg-canvas-muted text-muted';
                            ?>
                            <span class="shrink-0 rounded-lg px-2 py-0.5 text-[10px] font-medium <?php echo e($prioClass); ?>"><?php echo e($prio); ?></span>
                        </div>
                        <p class="mt-1.5 text-xs leading-6 text-muted"><?php echo e($action['meta'] ?? ($action['cta'] ?? '')); ?> · <?php echo e(number_format($action['count'] ?? 0)); ?></p>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-xl border border-line bg-[#f7f8fa] px-4 py-6 text-center text-sm text-muted">
                        لا توجد تنبيهات عاجلة حالياً
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </section>
    <?php endif; ?>

    
    <section class="grid gap-5 xl:grid-cols-[1.55fr_0.95fr]">
        <?php if(! empty($ds['sales_section'])): ?>
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-ink">تدفق الطلبات</h3>
                        <p class="mt-0.5 text-xs text-muted">آخر الطلبات مع الحالة والمبلغ</p>
                    </div>
                    <?php if(Route::has('admin.orders.index')): ?>
                        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn-press shrink-0 rounded-xl px-3 py-1.5 text-sm text-accent hover:bg-accent-soft">فتح الطابور</a>
                    <?php endif; ?>
                </div>
                <div class="admin-table-wrap">
                    <table class="w-full min-w-[740px] text-right text-sm">
                        <thead class="bg-[#f7f8fa] text-[11px] uppercase tracking-wide text-muted">
                            <tr>
                                <th class="px-5 py-3 font-medium">الطلب</th>
                                <th class="px-3 py-3 font-medium">الطالب</th>
                                <th class="px-3 py-3 font-medium">الكورس</th>
                                <th class="px-3 py-3 font-medium">الحالة</th>
                                <th class="px-3 py-3 font-medium">المبلغ</th>
                                <th class="px-5 py-3 font-medium">الوقت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $status = (string) ($order->status ?? '');
                                    $badgeClass = match ($status) {
                                        'approved', 'completed' => 'bg-accent-soft text-accent',
                                        'pending' => 'bg-metal/15 text-metal',
                                        'rejected', 'cancelled' => 'bg-danger/10 text-danger',
                                        default => 'bg-canvas-muted text-ink-soft',
                                    };
                                    $statusLabel = match ($status) {
                                        'approved' => 'موافق عليه',
                                        'pending' => 'معلّق',
                                        'rejected' => 'مرفوض',
                                        'cancelled' => 'ملغى',
                                        'completed' => 'مكتمل',
                                        default => $status ?: '—',
                                    };
                                ?>
                                <tr class="border-t border-line/70 transition hover:bg-[#f9fafb]">
                                    <td class="px-5 py-3.5">
                                        <?php if(Route::has('admin.orders.show')): ?>
                                            <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="font-semibold tabular-nums text-ink hover:text-accent">#<?php echo e($order->id); ?></a>
                                        <?php else: ?>
                                            <span class="font-semibold tabular-nums text-ink">#<?php echo e($order->id); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3.5 text-ink-soft"><?php echo e($order->user->name ?? '—'); ?></td>
                                    <td class="px-3 py-3.5 text-muted"><?php echo e(\Illuminate\Support\Str::limit($order->course->title ?? '—', 28)); ?></td>
                                    <td class="px-3 py-3.5"><span class="rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($badgeClass); ?>"><?php echo e($statusLabel); ?></span></td>
                                    <td class="px-3 py-3.5 font-semibold tabular-nums text-ink"><?php echo e(number_format((float) ($order->amount ?? 0), 0)); ?> <?php echo e($currency); ?></td>
                                    <td class="px-5 py-3.5 text-xs text-muted"><?php echo e(optional($order->created_at)->diffForHumans()); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-muted">لا توجد طلبات بعد</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        <?php else: ?>
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft md:p-6">
                <h3 class="text-base font-semibold text-ink">ملخص سريع</h3>
                <p class="mt-2 text-sm leading-7 text-muted">لا تملك صلاحية عرض قسم المبيعات. تُعرض الأقسام الأخرى حسب دورك.</p>
            </article>
        <?php endif; ?>

        <div class="space-y-5">
            <?php if(! empty($ds['recent_courses']) && $recent_courses): ?>
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h3 class="text-base font-semibold text-ink">أحدث الكورسات</h3>
                        <?php if(Route::has('admin.courses.index')): ?>
                            <a href="<?php echo e(route('admin.courses.index')); ?>" class="text-xs font-medium text-accent hover:underline">الكل</a>
                        <?php endif; ?>
                    </div>
                    <ul class="space-y-2.5">
                        <?php $__currentLoopData = $recent_courses->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3 rounded-xl border border-transparent px-2 py-2 hover:border-line hover:bg-[#f7f8fa]">
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-[#eef1f5] text-[11px] font-bold tabular-nums text-ink"><?php echo e($i + 1); ?></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-ink"><?php echo e($course->title); ?></p>
                                    <p class="text-[11px] text-muted"><?php echo e($course->academicSubject->name ?? '—'); ?></p>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </article>
            <?php endif; ?>

            <?php if(! empty($ds['recent_users']) && $recent_users): ?>
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h3 class="text-base font-semibold text-ink">أحدث المستخدمين</h3>
                        <?php if(Route::has('admin.users.index')): ?>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="text-xs font-medium text-accent hover:underline">الكل</a>
                        <?php endif; ?>
                    </div>
                    <ul class="space-y-2.5">
                        <?php $__currentLoopData = $recent_users->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3 rounded-xl border border-transparent px-2 py-2 hover:border-line hover:bg-[#f7f8fa]">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-accent-soft text-xs font-semibold text-accent"><?php echo e(mb_substr($user->name ?? '؟', 0, 1)); ?></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-ink"><?php echo e($user->name); ?></p>
                                    <p class="truncate text-[11px] text-muted"><?php echo e($user->email ?? $user->phone); ?></p>
                                </div>
                                <span class="shrink-0 rounded-lg bg-canvas px-2 py-0.5 text-[10px] text-muted"><?php echo e($user->role); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </article>
            <?php endif; ?>

            <?php if(! empty($ds['invoices_panel']) && isset($pending_invoices) && $pending_invoices->isNotEmpty()): ?>
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h3 class="text-base font-semibold text-ink">فواتير معلّقة</h3>
                        <?php if(Route::has('admin.invoices.index')): ?>
                            <a href="<?php echo e(route('admin.invoices.index', ['status' => 'pending'])); ?>" class="text-xs font-medium text-accent hover:underline">الكل</a>
                        <?php endif; ?>
                    </div>
                    <ul class="space-y-2.5">
                        <?php $__currentLoopData = $pending_invoices->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center justify-between gap-3 rounded-xl bg-[#f7f8fa] px-3 py-2.5">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-ink"><?php echo e($invoice->user->name ?? '—'); ?></p>
                                    <p class="text-[11px] text-muted">#<?php echo e($invoice->id); ?></p>
                                </div>
                                <span class="shrink-0 text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format((float) ($invoice->total_amount ?? 0), 0)); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </article>
            <?php endif; ?>
        </div>
    </section>

    <?php if(! empty($ds['payments_panel']) && isset($recent_payments) && $recent_payments->isNotEmpty()): ?>
        <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft md:p-6">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h3 class="text-base font-semibold text-ink">آخر المدفوعات</h3>
                <?php if(Route::has('admin.payments.index')): ?>
                    <a href="<?php echo e(route('admin.payments.index')); ?>" class="text-xs font-medium text-accent hover:underline">عرض الكل</a>
                <?php endif; ?>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                <?php $__currentLoopData = $recent_payments->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="min-w-0 rounded-xl border border-line bg-canvas px-4 py-3">
                        <p class="truncate text-sm font-semibold text-ink"><?php echo e($payment->user->name ?? '—'); ?></p>
                        <p class="mt-1 text-lg font-semibold tabular-nums text-accent"><?php echo e(number_format((float) ($payment->amount ?? 0), 0)); ?> <span class="text-xs font-normal text-muted"><?php echo e($currency); ?></span></p>
                        <p class="mt-1 text-[11px] text-muted"><?php echo e(optional($payment->created_at)->diffForHumans()); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>

    
    <section class="admin-status-strip rounded-2xl border border-line bg-ink p-4 text-white">
        <div class="px-2 py-1">
            <p class="text-[11px] text-white/45">طلبات معلّقة</p>
            <p class="mt-1 text-sm font-semibold tabular-nums"><?php echo e(number_format($ordersPending)); ?></p>
        </div>
        <div class="px-2 py-1">
            <p class="text-[11px] text-white/45">فواتير معلّقة</p>
            <p class="mt-1 text-sm font-semibold tabular-nums"><?php echo e(number_format($pendingInvoicesCount)); ?></p>
        </div>
        <div class="px-2 py-1">
            <p class="text-[11px] text-white/45">اشتراكات نشطة</p>
            <p class="mt-1 text-sm font-semibold tabular-nums"><?php echo e(number_format($enrollmentsTotal)); ?></p>
        </div>
        <div class="px-2 py-1">
            <p class="text-[11px] text-white/45">نشاط 7 أيام</p>
            <p class="mt-1 text-sm font-semibold tabular-nums"><?php echo e(number_format($weekTotal)); ?> حدث</p>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>