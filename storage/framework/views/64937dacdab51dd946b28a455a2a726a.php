<?php $__env->startSection('title', 'عرض السؤال - Glottical'); ?>
<?php $__env->startSection('page_title', 'عرض السؤال'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isActive = (bool) ($faq->is_active ?? false);
    $statusTone = $isActive ? 'bg-accent-soft text-accent' : 'bg-canvas-muted text-muted';
    $statusLabel = $isActive ? 'نشط' : 'غير نشط';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الأسئلة الشائعة · محتوى الموقع</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">عرض السؤال</h2>
            <p class="mt-1 text-sm text-muted">سؤال #<?php echo e($faq->id); ?> · <?php echo e($faq->created_at?->diffForHumans()); ?></p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.faq.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع للقائمة
            </a>
            <a href="<?php echo e(route('admin.faq.edit', $faq)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-pen text-xs"></i>
                تعديل
            </a>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-folder text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">الفئة</p>
            <p class="mt-1 text-sm font-semibold text-ink"><?php echo e($faq->category ?: 'غير محدد'); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal">
                <i class="fas fa-sort-numeric-down text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">الترتيب</p>
            <p class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e($faq->order ?? 0); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($statusTone); ?>">
                <i class="fas fa-toggle-<?php echo e($isActive ? 'on' : 'off'); ?> text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">الحالة</p>
            <p class="mt-1">
                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold <?php echo e($statusTone); ?>"><?php echo e($statusLabel); ?></span>
            </p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-calendar-day text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">تاريخ الإنشاء</p>
            <p class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e($faq->created_at?->format('Y-m-d H:i')); ?></p>
        </article>
    </section>

    <div class="grid gap-5 lg:grid-cols-5">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft lg:col-span-3">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">السؤال والإجابة</h3>
                <p class="mt-0.5 text-xs text-muted">المحتوى الظاهر للزوار في قسم الأسئلة الشائعة</p>
            </div>
            <div class="space-y-4 p-4 sm:p-5">
                <div class="rounded-xl border border-line bg-canvas/60 p-4">
                    <p class="text-xs font-medium text-muted">السؤال</p>
                    <p class="mt-1 text-base font-semibold leading-7 text-ink"><?php echo e($faq->question); ?></p>
                </div>
                <div class="rounded-xl border border-line bg-canvas/60 p-4">
                    <p class="text-xs font-medium text-muted">الإجابة</p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-7 text-ink"><?php echo e($faq->answer); ?></p>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft lg:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">إجراءات</h3>
                <p class="mt-0.5 text-xs text-muted">تعديل أو حذف هذا السؤال</p>
            </div>
            <div class="space-y-2 p-4 sm:p-5">
                <a href="<?php echo e(route('admin.faq.edit', $faq)); ?>" class="btn-press inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                    <i class="fas fa-pen text-xs"></i>
                    تعديل السؤال
                </a>
                <a href="<?php echo e(route('admin.faq.index')); ?>" class="btn-press inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                    <i class="fas fa-list text-xs"></i>
                    كل الأسئلة
                </a>
                <?php if(Route::has('admin.faq.destroy')): ?>
                    <form method="post" action="<?php echo e(route('admin.faq.destroy', $faq)); ?>" onsubmit="return confirm('حذف هذا السؤال نهائياً؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-press inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-danger/30 bg-surface px-4 text-sm font-medium text-danger transition hover:bg-danger/5">
                            <i class="fas fa-trash text-xs"></i>
                            حذف السؤال
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\faq\show.blade.php ENDPATH**/ ?>