

<?php $__env->startSection('title', 'عملاء المبيعات المحتملون'); ?>
<?php $__env->startSection('page_title', 'عملاء المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · العملاء المحتملون</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">عملاء المبيعات</h2>
            <p class="mt-1 text-sm text-muted">عرض الـ Leads — للإدارة الكاملة استخدم CRM</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.crm.leads.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-external-link-alt text-xs"></i>
                فتح في CRM
            </a>
            <a href="<?php echo e(route('admin.sales.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-chart-pie text-xs"></i>
                تحليلات المبيعات
            </a>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">البحث والفلترة</h3>
        </div>
        <form method="GET" action="<?php echo e(route('admin.sales.leads.index')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-3 md:items-end">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="search">بحث</label>
                <input id="search" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، بريد، هاتف، رقم…" class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = \App\Models\SalesLead::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تصفية
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                        مسح
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">القائمة</h3>
                <p class="mt-0.5 text-xs text-muted"><?php echo e(number_format($leads->total())); ?> نتيجة</p>
            </div>
        </div>
        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">#</th>
                        <th class="px-4 py-3 text-start font-medium">الاسم</th>
                        <th class="px-4 py-3 text-start font-medium">تواصل</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">المسؤول</th>
                        <th class="px-4 py-3 text-start font-medium">التاريخ</th>
                        <th class="px-4 py-3 text-start font-medium">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3 tabular-nums text-muted"><?php echo e($lead->id); ?></td>
                            <td class="px-4 py-3 font-semibold text-ink"><?php echo e($lead->name); ?></td>
                            <td class="px-4 py-3 text-xs text-muted">
                                <div><?php echo e($lead->email ?: '—'); ?></div>
                                <div><?php echo e($lead->phone ?: '—'); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e($lead->status_label); ?></span>
                            </td>
                            <td class="px-4 py-3 text-ink-soft"><?php echo e($lead->assignedTo?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-xs tabular-nums text-muted"><?php echo e($lead->created_at?->format('Y-m-d')); ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    <a href="<?php echo e(route('admin.sales.leads.show', $lead)); ?>" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-ink hover:border-accent/30 hover:text-accent" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.crm.leads.show', $lead)); ?>" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-ink hover:border-accent/30 hover:text-accent" title="CRM">
                                        <i class="fas fa-project-diagram"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد سجلات</p>
                                <p class="mt-1 text-xs text-muted">جرّب مسح الفلاتر أو أضف عميلاً من CRM.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($leads->hasPages()): ?>
            <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($leads->links()); ?></div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\sales\leads\index.blade.php ENDPATH**/ ?>