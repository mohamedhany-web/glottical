<?php $__env->startSection('title', 'خدمات الموقع - Glottical'); ?>
<?php $__env->startSection('page_title', 'خدمات الموقع'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $kpis = [
        ['label' => 'إجمالي الخدمات', 'value' => $stats['total'], 'icon' => 'fa-concierge-bell', 'tone' => 'accent', 'note' => 'كل الخدمات المسجّلة'],
        ['label' => 'نشطة', 'value' => $stats['active'], 'icon' => 'fa-eye', 'tone' => 'accent', 'note' => 'تظهر للزوار'],
        ['label' => 'معطّلة', 'value' => $stats['inactive'], 'icon' => 'fa-eye-slash', 'tone' => 'muted', 'note' => 'مخفية عن الموقع'],
        ['label' => 'بصور', 'value' => $stats['with_image'], 'icon' => 'fa-image', 'tone' => 'metal', 'note' => 'لها صورة مرفوعة'],
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
            <p class="text-xs font-medium text-muted">محتوى الموقع · تظهر في الصفحة العامة وشريط التنقل</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">خدمات الموقع</h2>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <?php if(Route::has('public.services.index')): ?>
                <a href="<?php echo e(route('public.services.index')); ?>" target="_blank" rel="noopener" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                    <i class="fas fa-external-link-alt text-xs"></i>
                    معاينة الموقع
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.site-services.create')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                خدمة جديدة
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
            <p class="mt-0.5 text-xs text-muted">ابحث بالاسم أو الرابط، أو صفِّ حسب حالة الظهور</p>
        </div>
        <form method="GET" action="<?php echo e(route('admin.site-services.index')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-3 md:items-end">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="search">البحث</label>
                <input id="search" type="search" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="الاسم، الرابط، أو المقدمة..." class="<?php echo e($fieldClass); ?>">
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
                    <a href="<?php echo e(route('admin.site-services.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                        <i class="fas fa-times text-xs"></i>
                        مسح
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div class="min-w-0">
                <h3 class="text-base font-semibold text-ink">قائمة الخدمات</h3>
                <p class="mt-0.5 text-xs text-muted"><?php echo e(number_format($services->total())); ?> خدمة</p>
            </div>
            <a href="<?php echo e(route('admin.site-services.create')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-plus text-xs"></i>
                إضافة
            </a>
        </div>

        <?php if($services->count() > 0): ?>
            <div class="admin-table-wrap">
                <table class="w-full min-w-[860px] text-right text-sm">
                    <thead class="bg-canvas text-[11px] uppercase tracking-wide text-muted">
                        <tr>
                            <th class="px-5 py-3 font-medium">الترتيب</th>
                            <th class="px-3 py-3 font-medium">الخدمة</th>
                            <th class="px-3 py-3 font-medium">الرابط</th>
                            <th class="px-3 py-3 font-medium">الحالة</th>
                            <th class="px-5 py-3 font-medium">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="transition hover:bg-canvas">
                                <td class="px-5 py-3 tabular-nums text-muted"><?php echo e($service->sort_order); ?></td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-3">
                                        <?php if($service->publicImageUrl()): ?>
                                            <img src="<?php echo e($service->publicImageUrl()); ?>" alt="" class="size-11 shrink-0 rounded-xl border border-line object-cover">
                                        <?php else: ?>
                                            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-sm font-semibold text-accent">
                                                <?php echo e(mb_substr($service->name, 0, 1, 'UTF-8')); ?>

                                            </span>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink"><?php echo e($service->name); ?></p>
                                            <?php if($service->summary): ?>
                                                <p class="mt-0.5 line-clamp-1 text-xs text-muted"><?php echo e(\Illuminate\Support\Str::limit($service->summary, 60)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <a href="<?php echo e(route('public.services.show', $service)); ?>" target="_blank" rel="noopener" class="font-mono text-xs text-accent hover:underline" dir="ltr">/services/<?php echo e($service->slug); ?></a>
                                </td>
                                <td class="px-3 py-3">
                                    <?php if($service->is_active): ?>
                                        <span class="rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">نشط</span>
                                    <?php else: ?>
                                        <span class="rounded-lg bg-canvas-muted px-2.5 py-1 text-xs font-medium text-muted">معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <a href="<?php echo e(route('public.services.show', $service)); ?>" target="_blank" rel="noopener"
                                           class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-canvas-muted text-muted transition hover:bg-ink hover:text-white"
                                           title="معاينة">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.site-services.edit', $service)); ?>"
                                           class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-accent-soft text-accent transition hover:bg-accent hover:text-white"
                                           title="تعديل">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.site-services.destroy', $service)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذه الخدمة؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                    class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-danger/10 text-danger transition hover:bg-danger hover:text-white"
                                                    title="حذف">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <?php if($services->hasPages()): ?>
                <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($services->withQueryString()->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="px-4 py-16 text-center sm:px-5">
                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <p class="text-sm font-medium text-ink">لا توجد خدمات</p>
                <p class="mt-1 text-xs text-muted">
                    <?php if(request()->anyFilled(['search', 'status'])): ?>
                        لا توجد نتائج مطابقة للفلتر الحالي.
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.site-services.create')); ?>" class="text-accent hover:underline">أضف أول خدمة</a> لتظهر على الموقع.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\site-services\index.blade.php ENDPATH**/ ?>