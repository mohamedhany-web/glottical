

<?php $__env->startSection('title', 'حجوزات المجموعات - Glottical'); ?>
<?php $__env->startSection('page_title', 'حجوزات المجموعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المجموعات · حجوزات فردية وجماعية</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">حجوزات المجموعات</h2>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.tutoring-groups.index', 'individual')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft hover:border-accent/30 hover:text-accent">فردية</a>
            <a href="<?php echo e(route('admin.tutoring-groups.index', 'collective')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft hover:border-accent/30 hover:text-accent">جماعية</a>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">الإجمالي</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['total'])); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">قيد المراجعة</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['pending'])); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">مؤكد</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['confirmed'])); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">قادم مؤكد</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['upcoming'])); ?></p>
        </article>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">فلترة</h3>
        </div>
        <form method="GET" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-5 lg:items-end sm:p-5">
            <div>
                <label class="<?php echo e($labelClass); ?>">بحث</label>
                <input type="search" name="search" value="<?php echo e(request('search')); ?>" class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>">النوع</label>
                <select name="type" class="<?php echo e($fieldClass); ?>">
                    <option value="">الكل</option>
                    <option value="individual" <?php if(request('type')==='individual'): echo 'selected'; endif; ?>>فردي</option>
                    <option value="collective" <?php if(request('type')==='collective'): echo 'selected'; endif; ?>>جماعي</option>
                </select>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>">الحالة</label>
                <select name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">الكل</option>
                    <option value="pending" <?php if(request('status')==='pending'): echo 'selected'; endif; ?>>قيد المراجعة</option>
                    <option value="confirmed" <?php if(request('status')==='confirmed'): echo 'selected'; endif; ?>>مؤكد</option>
                    <option value="cancelled" <?php if(request('status')==='cancelled'): echo 'selected'; endif; ?>>ملغي</option>
                    <option value="completed" <?php if(request('status')==='completed'): echo 'selected'; endif; ?>>مكتمل</option>
                </select>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>">من</label>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="<?php echo e($fieldClass); ?>">
            </div>
            <div class="flex gap-2">
                <button class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white">تصفية</button>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start">المجموعة</th>
                        <th class="px-4 py-3 text-start">الطالب</th>
                        <th class="px-4 py-3 text-start">المدرب</th>
                        <th class="px-4 py-3 text-start">الموعد</th>
                        <th class="px-4 py-3 text-start">الحالة</th>
                        <th class="px-4 py-3 text-start">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-ink"><?php echo e($booking->tutoringGroup?->title); ?></p>
                                <p class="text-[11px] text-muted"><?php echo e($booking->tutoringGroup?->typeLabel()); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-ink"><?php echo e($booking->contactName()); ?></p>
                                <p class="text-[11px] text-muted"><?php echo e($booking->contactPhone() ?: $booking->contactEmail()); ?></p>
                            </td>
                            <td class="px-4 py-3 text-ink-soft"><?php echo e($booking->instructor?->name); ?></td>
                            <td class="px-4 py-3 tabular-nums text-ink"><?php echo e($booking->starts_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e($booking->statusLabel()); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.tutoring-group-bookings.show', $booking)); ?>" class="text-xs font-semibold text-accent">عرض</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-sm text-muted">لا توجد حجوزات</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($bookings->hasPages()): ?>
            <div class="border-t border-line px-4 py-4"><?php echo e($bookings->links()); ?></div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\tutoring-group-bookings\index.blade.php ENDPATH**/ ?>