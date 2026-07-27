

<?php $__env->startSection('title', 'إضافة سؤال - Glottical'); ?>
<?php $__env->startSection('page_title', 'إضافة سؤال'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">محتوى الموقع · الأسئلة الشائعة</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إضافة سؤال</h2>
            <p class="mt-1 text-sm text-muted">سؤال جديد يظهر للزوار في صفحة الأسئلة الشائعة</p>
        </div>
        <a href="<?php echo e(route('admin.faq.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للقائمة
        </a>
    </section>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger shadow-soft">
            <p class="mb-2 font-semibold">يرجى تصحيح ما يلي:</p>
            <ul class="list-inside list-disc space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.faq.store')); ?>" method="POST" class="space-y-5">
        <?php echo csrf_field(); ?>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">السؤال والإجابة</h3>
                <p class="mt-0.5 text-xs text-muted">المحتوى الظاهر للزوار</p>
            </div>
            <div class="grid gap-5 p-4 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="question">السؤال <span class="text-danger">*</span></label>
                    <input id="question" type="text" name="question" value="<?php echo e(old('question')); ?>" required class="<?php echo e($fieldClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="answer">الإجابة <span class="text-danger">*</span></label>
                    <textarea id="answer" name="answer" rows="6" required class="<?php echo e($areaClass); ?>"><?php echo e(old('answer')); ?></textarea>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">التصنيف والنشر</h3>
                <p class="mt-0.5 text-xs text-muted">الفئة والترتيب وحالة الظهور</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="category">الفئة</label>
                    <input id="category" type="text" name="category" value="<?php echo e(old('category')); ?>" list="categories" placeholder="أدخل فئة أو اختر من القائمة" class="<?php echo e($fieldClass); ?>">
                    <datalist id="categories">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category); ?>">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </datalist>
                </div>
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="order">الترتيب</label>
                    <input id="order" type="number" name="order" value="<?php echo e(old('order', 0)); ?>" min="0" class="<?php echo e($fieldClass); ?>">
                </div>
                <div class="sm:col-span-2">
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex h-11 w-full cursor-pointer items-center gap-2 rounded-xl border border-line bg-canvas px-4 text-sm font-medium text-ink sm:w-auto">
                        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', '1') !== '0'): echo 'checked'; endif; ?> class="size-4 rounded border-line text-accent focus:ring-accent/20">
                        <span>نشط ويظهر في الموقع</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-line px-4 py-4 sm:px-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i> حفظ السؤال
                </button>
                <a href="<?php echo e(route('admin.faq.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-6 text-sm font-medium text-ink hover:bg-canvas">إلغاء</a>
            </div>
        </article>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\faq\create.blade.php ENDPATH**/ ?>