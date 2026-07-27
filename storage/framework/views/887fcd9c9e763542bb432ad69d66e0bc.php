

<?php $__env->startSection('title', 'إضافة رأي - Glottical'); ?>
<?php $__env->startSection('page_title', 'إضافة رأي'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5" x-data="{ type: '<?php echo e(old('content_type', 'text')); ?>' }">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">محتوى الموقع · آراء الرئيسية</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">رأي جديد</h2>
            <p class="mt-1 text-sm text-muted">«نص» لاقتباس مكتوب، أو «صورة» لشهادة / لقطة</p>
        </div>
        <a href="<?php echo e(route('admin.site-testimonials.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
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

    <form action="<?php echo e(route('admin.site-testimonials.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
        <?php echo csrf_field(); ?>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">نوع العرض والمحتوى</h3>
                <p class="mt-0.5 text-xs text-muted">اختر نصاً أو صورة ثم أدخل المحتوى الظاهر للزوار</p>
            </div>
            <div class="grid gap-5 p-4 sm:p-5">
                <div>
                    <span class="<?php echo e($labelClass); ?>">نوع العرض <span class="text-danger">*</span></span>
                    <div class="flex flex-wrap gap-2">
                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-sm text-ink transition" :class="type === 'text' ? 'border-accent bg-accent-soft text-accent' : ''">
                            <input type="radio" name="content_type" value="text" x-model="type" class="text-accent focus:ring-accent/20">
                            <span>نص (اقتباس + اسم)</span>
                        </label>
                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-line bg-canvas px-4 py-2.5 text-sm text-ink transition" :class="type === 'image' ? 'border-accent bg-accent-soft text-accent' : ''">
                            <input type="radio" name="content_type" value="image" x-model="type" class="text-accent focus:ring-accent/20">
                            <span>صورة (شهادة / لقطة)</span>
                        </label>
                    </div>
                </div>

                <template x-if="type === 'text'">
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="body_text">نص الرأي <span class="text-danger">*</span></label>
                        <textarea id="body_text" name="body" rows="6" class="<?php echo e($areaClass); ?>"><?php echo e(old('body')); ?></textarea>
                    </div>
                </template>

                <template x-if="type === 'image'">
                    <div class="space-y-5">
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="image">صورة الشهادة <span class="text-danger">*</span></label>
                            <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                                   class="block w-full text-sm text-muted file:ml-4 file:rounded-xl file:border-0 file:bg-accent-soft file:px-4 file:py-2 file:text-sm file:font-medium file:text-accent hover:file:bg-accent hover:file:text-white">
                            <p class="mt-1.5 text-xs text-muted">jpg, png, webp, gif — حتى 10 ميغابايت تقريباً.</p>
                        </div>
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="body_image">وصف قصير تحت الصورة <span class="font-normal text-muted">(اختياري)</span></label>
                            <textarea id="body_image" name="body" rows="2" class="<?php echo e($areaClass); ?>"><?php echo e(old('body')); ?></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">صاحب الرأي</h3>
                <p class="mt-0.5 text-xs text-muted">الاسم والمسمى الظاهران بجانب الرأي</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="author_name">اسم صاحب الرأي</label>
                    <input id="author_name" type="text" name="author_name" value="<?php echo e(old('author_name')); ?>" maxlength="190" class="<?php echo e($fieldClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="role_label">المسمى <span class="font-normal text-muted">(اختياري)</span></label>
                    <input id="role_label" type="text" name="role_label" value="<?php echo e(old('role_label')); ?>" maxlength="190" placeholder="مثال: معلّم لغة عربية" class="<?php echo e($fieldClass); ?>">
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">العرض والنشر</h3>
                <p class="mt-0.5 text-xs text-muted">الترتيب والتفعيل والتمييز في الرئيسية</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="sort_order">ترتيب العرض</label>
                    <input id="sort_order" type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0" class="<?php echo e($fieldClass); ?>">
                </div>
                <div class="flex flex-col justify-end gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="hidden" name="is_featured" value="0">
                    <label class="inline-flex h-11 cursor-pointer items-center gap-2 rounded-xl border border-line bg-canvas px-4 text-sm font-medium text-ink">
                        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', '1') !== '0'): echo 'checked'; endif; ?> class="size-4 rounded border-line text-accent focus:ring-accent/20">
                        <span>نشط ويظهر في الموقع</span>
                    </label>
                    <label class="inline-flex h-11 cursor-pointer items-center gap-2 rounded-xl border border-line bg-canvas px-4 text-sm font-medium text-ink">
                        <input type="checkbox" name="is_featured" value="1" <?php if(old('is_featured')): echo 'checked'; endif; ?> class="size-4 rounded border-line text-metal focus:ring-metal/20">
                        <span>بطاقة مميزة</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-line px-4 py-4 sm:px-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i> حفظ الرأي
                </button>
                <a href="<?php echo e(route('admin.site-testimonials.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-6 text-sm font-medium text-ink hover:bg-canvas">إلغاء</a>
            </div>
        </article>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\site-testimonials\create.blade.php ENDPATH**/ ?>