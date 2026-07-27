

<?php $__env->startSection('title', 'إضافة عميل محتمل - CRM'); ?>
<?php $__env->startSection('page_title', 'إضافة عميل محتمل'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink';
?>
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · العملاء المحتملون</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إضافة عميل محتمل</h2>
            <p class="mt-1 text-sm text-muted">إنشاء Lead جديد مع تعيين اختياري للمبيعات أو التسويق</p>
        </div>
        <a href="<?php echo e(route('admin.crm.leads.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            العودة للقائمة
        </a>
    </section>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <form method="POST" action="<?php echo e(route('admin.crm.leads.store')); ?>" class="p-5 sm:p-6">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.crm.leads._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="mt-8 flex flex-wrap gap-2 border-t border-line pt-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ العميل
                </button>
                <a href="<?php echo e(route('admin.crm.leads.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                    إلغاء
                </a>
            </div>
        </form>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\leads\create.blade.php ENDPATH**/ ?>