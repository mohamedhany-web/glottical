

<?php $__env->startSection('title', $typeLabel.' - Glottical'); ?>
<?php $__env->startSection('page_title', $typeLabel); ?>

<?php $__env->startSection('content'); ?>
<?php
    $kpis = [
        ['label' => 'الإجمالي', 'value' => $stats['total'], 'icon' => 'fa-layer-group', 'tone' => 'accent', 'note' => 'كل المجموعات من هذا النوع'],
        ['label' => 'نشطة', 'value' => $stats['active'], 'icon' => 'fa-eye', 'tone' => 'accent', 'note' => 'تظهر للزوار'],
        ['label' => 'معطّلة', 'value' => $stats['inactive'], 'icon' => 'fa-eye-slash', 'tone' => 'muted', 'note' => 'مخفية عن الموقع'],
        ['label' => 'مميزة', 'value' => $stats['featured'], 'icon' => 'fa-star', 'tone' => 'metal', 'note' => 'أولوية في العرض'],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحتوى · المجموعات · منفصل عن الكورسات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($typeLabel); ?></h2>
            <p class="mt-1 text-sm text-muted">إنشاء وإدارة العروض الظاهرة للطلاب مع الحجز حسب جدول المدرب</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.tutor-work-schedules.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-calendar-week text-xs"></i>
                جداول المدربين
            </a>
            <a href="<?php echo e(route('admin.tutoring-groups.create', $type)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                مجموعة جديدة
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
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($kpi['value'])); ?></p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">البحث والفلترة</h3>
        </div>
        <form method="GET" action="<?php echo e(route('admin.tutoring-groups.index', $type)); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-3 md:items-end">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="search">البحث</label>
                <input id="search" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="العنوان أو الرابط..." class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">كل الحالات</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>معطل</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تصفية
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('admin.tutoring-groups.index', $type)); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">مسح</a>
                <?php endif; ?>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">قائمة المجموعات</h3>
            <p class="mt-0.5 text-xs text-muted"><?php echo e(number_format($groups->total())); ?> نتيجة</p>
        </div>
        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">المجموعة</th>
                        <th class="px-4 py-3 text-start font-medium">المدرب</th>
                        <th class="px-4 py-3 text-start font-medium">السعر</th>
                        <th class="px-4 py-3 text-start font-medium">السعة</th>
                        <th class="px-4 py-3 text-start font-medium">الحجوزات</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink"><?php echo e($group->title); ?></p>
                                <p class="mt-0.5 font-mono text-[11px] text-muted" dir="ltr"><?php echo e($group->slug); ?></p>
                            </td>
                            <td class="px-4 py-3 text-ink-soft"><?php echo e($group->instructor?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 tabular-nums text-ink"><?php echo e($group->formattedPrice()); ?></td>
                            <td class="px-4 py-3 tabular-nums text-ink"><?php echo e($group->capacity); ?></td>
                            <td class="px-4 py-3">
                                <span class="text-ink"><?php echo e(number_format($group->bookings_count)); ?></span>
                                <?php if(($group->pending_bookings_count ?? 0) > 0): ?>
                                    <span class="ms-1 text-xs text-metal">(<?php echo e($group->pending_bookings_count); ?> قيد المراجعة)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($group->is_active ? 'bg-accent-soft text-accent' : 'bg-canvas-muted text-muted'); ?>">
                                    <?php echo e($group->is_active ? 'نشط' : 'معطل'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('admin.tutoring-groups.edit', [$type, $group])); ?>" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-ink hover:border-accent/30 hover:text-accent">تعديل</a>
                                    <form method="POST" action="<?php echo e(route('admin.tutoring-groups.toggle-status', [$type, $group])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-ink-soft hover:border-accent/30 hover:text-accent">
                                            <?php echo e($group->is_active ? 'إيقاف' : 'تفعيل'); ?>

                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.tutoring-groups.destroy', [$type, $group])); ?>" onsubmit="return confirm('حذف هذه المجموعة؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-danger hover:bg-danger/5">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-users"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد مجموعات بعد</p>
                                <a href="<?php echo e(route('admin.tutoring-groups.create', $type)); ?>" class="mt-3 inline-flex text-sm font-semibold text-accent">إنشاء أول مجموعة</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($groups->hasPages()): ?>
            <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($groups->links()); ?></div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\tutoring-groups\index.blade.php ENDPATH**/ ?>