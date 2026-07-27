

<?php $__env->startSection('title', 'حجوزات الحصة المجانية - Glottical'); ?>
<?php $__env->startSection('page_title', 'حجوزات الحصة المجانية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $kpis = [
        ['label' => 'الإجمالي', 'value' => $stats['total'], 'icon' => 'fa-inbox', 'tone' => 'accent'],
        ['label' => 'مؤكد', 'value' => $stats['confirmed'], 'icon' => 'fa-check-circle', 'tone' => 'accent'],
        ['label' => 'قادم', 'value' => $stats['upcoming'], 'icon' => 'fa-hourglass-half', 'tone' => 'metal'],
        ['label' => 'اليوم', 'value' => $stats['today'], 'icon' => 'fa-calendar-day', 'tone' => 'metal'],
        ['label' => 'مكتمل', 'value' => $stats['completed'], 'icon' => 'fa-flag-checkered', 'tone' => 'muted'],
        ['label' => 'ملغي', 'value' => $stats['cancelled'], 'icon' => 'fa-ban', 'tone' => 'danger'],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
        'danger' => 'bg-danger/10 text-danger',
    ];
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">تقييم المستوى · 30 دقيقة من الصفحة الرئيسية</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">حجوزات الحصة المجانية</h2>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.free-trial-bookings.availability')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-clock text-xs"></i>
                ضبط أوقات الأسبوع
            </a>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($toneClass[$kpi['tone']]); ?>">
                    <i class="fas <?php echo e($kpi['icon']); ?> text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e($kpi['label']); ?></p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($kpi['value'])); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">البحث والفلترة</h3>
            <p class="mt-0.5 text-xs text-muted">حدد الحالة أو نطاق التاريخ للوصول السريع للحجز</p>
        </div>
        <form method="get" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-5 md:items-end">
            <div class="md:col-span-2">
                <label class="<?php echo e($labelClass); ?>" for="search">بحث</label>
                <input id="search" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم / بريد / هاتف / هدف" class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">الكل</option>
                    <option value="confirmed" <?php if(request('status')==='confirmed'): echo 'selected'; endif; ?>>مؤكد</option>
                    <option value="completed" <?php if(request('status')==='completed'): echo 'selected'; endif; ?>>مكتمل</option>
                    <option value="cancelled" <?php if(request('status')==='cancelled'): echo 'selected'; endif; ?>>ملغي</option>
                </select>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="from">من تاريخ</label>
                <input id="from" type="date" name="from" value="<?php echo e(request('from')); ?>" class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="to">إلى تاريخ</label>
                <input id="to" type="date" name="to" value="<?php echo e(request('to')); ?>" class="<?php echo e($fieldClass); ?>">
            </div>
            <div class="flex flex-wrap gap-2 md:col-span-5">
                <button type="submit" class="btn-press inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تصفية
                </button>
                <?php if(request()->anyFilled(['search', 'status', 'from', 'to'])): ?>
                    <a href="<?php echo e(route('admin.free-trial-bookings.index')); ?>" class="btn-press inline-flex h-10 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                        <i class="fas fa-times text-xs"></i>
                        مسح الفلاتر
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div class="min-w-0">
                <h3 class="text-base font-semibold text-ink">سجل الحجوزات</h3>
                <p class="mt-0.5 text-xs text-muted"><?php echo e(number_format($bookings->total())); ?> حجز</p>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="w-full min-w-[860px] text-right text-sm">
                <thead class="bg-[#f7f8fa] text-[11px] uppercase tracking-wide text-muted">
                    <tr>
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-3 py-3 font-medium">الطالب</th>
                        <th class="px-3 py-3 font-medium">التواصل</th>
                        <th class="px-3 py-3 font-medium">الموعد</th>
                        <th class="px-3 py-3 font-medium">المدة</th>
                        <th class="px-3 py-3 font-medium">الحالة</th>
                        <th class="px-5 py-3 font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $badgeClass = match($b->status) {
                                'completed' => 'bg-accent-soft text-accent',
                                'cancelled' => 'bg-danger/10 text-danger',
                                default => 'bg-metal/15 text-metal',
                            };
                            $statusLabel = match($b->status) {
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                                default => 'مؤكد',
                            };
                        ?>
                        <tr class="transition hover:bg-[#f7f8fa]">
                            <td class="px-5 py-3 tabular-nums text-muted"><?php echo e($b->id); ?></td>
                            <td class="px-3 py-3">
                                <p class="font-semibold text-ink"><?php echo e($b->name); ?></p>
                                <?php if($b->goal): ?>
                                    <p class="mt-0.5 text-xs text-muted"><?php echo e(\Illuminate\Support\Str::limit($b->goal, 40)); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-xs text-muted">
                                <?php if($b->email): ?><p><i class="fas fa-envelope ml-1 text-[10px]"></i><?php echo e($b->email); ?></p><?php endif; ?>
                                <?php if($b->phone): ?><p class="mt-0.5"><i class="fas fa-phone ml-1 text-[10px]"></i><?php echo e($b->phone); ?></p><?php endif; ?>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 font-medium tabular-nums text-ink">
                                <?php echo e($b->starts_at?->timezone(config('app.timezone'))->format('Y-m-d H:i')); ?>

                            </td>
                            <td class="px-3 py-3 tabular-nums text-muted"><?php echo e($b->duration_minutes); ?> د</td>
                            <td class="px-3 py-3">
                                <span class="rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($badgeClass); ?>"><?php echo e($statusLabel); ?></span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5">
                                    <a href="<?php echo e(route('admin.free-trial-bookings.show', $b)); ?>"
                                       class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-accent-soft text-accent transition hover:bg-accent hover:text-white"
                                       title="عرض">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form method="post" action="<?php echo e(route('admin.free-trial-bookings.destroy', $b)); ?>" onsubmit="return confirm('حذف هذا الحجز؟');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-danger/10 text-danger transition hover:bg-danger hover:text-white"
                                                title="حذف">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد حجوزات</p>
                                <p class="mt-1 text-xs text-muted">ستظهر هنا حجوزات الحصة المجانية القادمة من الموقع.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($bookings->hasPages()): ?>
            <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($bookings->withQueryString()->links()); ?></div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\free-trial-bookings\index.blade.php ENDPATH**/ ?>