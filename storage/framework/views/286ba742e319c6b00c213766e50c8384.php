

<?php $__env->startSection('title', 'رسائل التواصل - Glottical'); ?>
<?php $__env->startSection('page_title', 'رسائل التواصل'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $kpis = [
        ['label' => 'إجمالي الرسائل', 'value' => $stats['total'], 'icon' => 'fa-inbox', 'tone' => 'accent', 'note' => 'جميع الرسائل'],
        ['label' => 'غير مقروءة', 'value' => $stats['unread'], 'icon' => 'fa-envelope', 'tone' => 'metal', 'note' => 'تحتاج للمراجعة'],
        ['label' => 'مقروءة', 'value' => $stats['read'], 'icon' => 'fa-check-double', 'tone' => 'muted', 'note' => 'تمت المراجعة'],
        ['label' => 'رسائل اليوم', 'value' => $stats['today'], 'icon' => 'fa-calendar-day', 'tone' => 'accent', 'note' => 'تم الاستلام اليوم'],
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
            <p class="text-xs font-medium text-muted">رسائل الزوار من صفحة «تواصل معنا»</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">رسائل التواصل</h2>
        </div>
        <?php if($stats['unread'] > 0): ?>
            <span class="inline-flex h-9 items-center gap-2 rounded-xl bg-metal/15 px-4 text-sm font-medium text-metal">
                <i class="fas fa-exclamation-circle text-xs"></i>
                <?php echo e(number_format($stats['unread'])); ?> رسالة غير مقروءة
            </span>
        <?php endif; ?>
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
            <p class="mt-0.5 text-xs text-muted">ابحث بالاسم أو البريد أو الموضوع، أو صفِّ حسب الحالة</p>
        </div>
        <form method="GET" action="<?php echo e(route('admin.contact-messages.index')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-3 md:items-end">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="search">البحث</label>
                <input id="search" type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="الاسم، البريد الإلكتروني، أو الموضوع..." class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">جميع الرسائل</option>
                    <option value="unread" <?php if(request('status') === 'unread'): echo 'selected'; endif; ?>>غير مقروءة</option>
                    <option value="read" <?php if(request('status') === 'read'): echo 'selected'; endif; ?>>مقروءة</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-search text-xs"></i>
                    بحث
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('admin.contact-messages.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
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
                <h3 class="text-base font-semibold text-ink">سجل الرسائل</h3>
                <p class="mt-0.5 text-xs text-muted"><?php echo e(number_format($messages->total())); ?> رسالة</p>
            </div>
            <span class="text-xs text-muted">آخر تحديث: <?php echo e(now()->format('H:i')); ?></span>
        </div>

        <?php if($messages->count() > 0): ?>
            <div class="admin-table-wrap">
                <table class="w-full min-w-[900px] text-right text-sm">
                    <thead class="bg-[#f7f8fa] text-[11px] uppercase tracking-wide text-muted">
                        <tr>
                            <th class="px-5 py-3 font-medium">المرسل</th>
                            <th class="px-3 py-3 font-medium">الموضوع</th>
                            <th class="px-3 py-3 font-medium">الحالة</th>
                            <th class="px-3 py-3 font-medium">تاريخ الإرسال</th>
                            <th class="px-5 py-3 font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="transition hover:bg-[#f7f8fa] <?php echo e(! $message->read_at ? 'bg-accent-soft/30' : ''); ?>">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-sm font-semibold text-accent">
                                            <?php echo e(mb_substr($message->name, 0, 1, 'UTF-8')); ?>

                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink"><?php echo e($message->name); ?></p>
                                            <p class="mt-0.5 truncate text-xs text-muted"><i class="fas fa-envelope ml-1 text-[10px]"></i><?php echo e($message->email); ?></p>
                                            <?php if($message->phone): ?>
                                                <p class="mt-0.5 truncate text-xs text-muted"><i class="fas fa-phone ml-1 text-[10px]"></i><?php echo e($message->phone); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="font-medium text-ink"><?php echo e($message->subject); ?></p>
                                    <p class="mt-1 line-clamp-2 max-w-md text-xs leading-5 text-muted"><?php echo e(Str::limit($message->message, 100)); ?></p>
                                </td>
                                <td class="px-3 py-3">
                                    <?php if($message->read_at): ?>
                                        <span class="rounded-lg bg-canvas-muted px-2.5 py-1 text-xs font-medium text-muted">مقروءة</span>
                                    <?php else: ?>
                                        <span class="rounded-lg bg-metal/15 px-2.5 py-1 text-xs font-medium text-metal">غير مقروءة</span>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3">
                                    <p class="font-medium tabular-nums text-ink"><?php echo e($message->created_at->format('d/m/Y')); ?></p>
                                    <p class="mt-0.5 text-xs tabular-nums text-muted"><?php echo e($message->created_at->format('H:i')); ?></p>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <a href="<?php echo e(route('admin.contact-messages.show', $message)); ?>"
                                           class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-accent-soft text-accent transition hover:bg-accent hover:text-white"
                                           title="عرض التفاصيل">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <?php if($message->read_at): ?>
                                            <form action="<?php echo e(route('admin.contact-messages.mark-as-unread', $message)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                        class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-metal/15 text-metal transition hover:bg-metal hover:text-white"
                                                        title="تحديد كغير مقروءة">
                                                    <i class="fas fa-envelope text-xs"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?php echo e(route('admin.contact-messages.mark-as-read', $message)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                        class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-canvas-muted text-muted transition hover:bg-ink hover:text-white"
                                                        title="تحديد كمقروءة">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?php echo e(route('admin.contact-messages.destroy', $message)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                    class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-danger/10 text-danger transition hover:bg-danger hover:text-white"
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
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

            <?php if($messages->hasPages()): ?>
                <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($messages->withQueryString()->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="px-4 py-16 text-center sm:px-5">
                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="text-sm font-medium text-ink">لا توجد رسائل</p>
                <p class="mt-1 text-xs text-muted">
                    <?php if(request()->anyFilled(['search', 'status'])): ?>
                        لا توجد نتائج مطابقة للفلتر الحالي.
                    <?php else: ?>
                        لم يتم استلام أي رسائل تواصل بعد.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\contact-messages\index.blade.php ENDPATH**/ ?>