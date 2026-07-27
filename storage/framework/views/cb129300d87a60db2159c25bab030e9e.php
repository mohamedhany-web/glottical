

<?php $__env->startSection('title', 'تعديل اتفاقية التقسيط'); ?>
<?php $__env->startSection('page_title', 'تعديل اتفاقية التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $agreement = $agreement ?? null;
    $plans = $plans ?? collect();
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · التقسيط · اتفاقيات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($agreement->student->name ?? 'طالب غير معروف'); ?></h2>
            <p class="mt-1 text-sm text-muted">يمكنك تغيير حالة الاتفاقية أو نقلها إلى خطة أخرى، بالإضافة إلى إضافة ملاحظات إدارية</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.installments.agreements.show', $agreement)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للتفاصيل
            </a>
        </div>
    </section>

    <div class="mx-auto max-w-3xl">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">تحديث الاتفاقية</h3>
                <p class="mt-0.5 text-xs text-muted">تعديل الحالة والملاحظات الإدارية</p>
            </div>

            <form action="<?php echo e(route('admin.installments.agreements.update', $agreement)); ?>" method="POST" class="space-y-6 p-4 sm:p-5">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="<?php echo e($labelClass); ?>">الخطة المرتبطة</label>
                        <select name="installment_plan_id" disabled class="h-11 w-full rounded-xl border border-line bg-canvas-muted px-4 text-sm text-muted">
                            <option><?php echo e($agreement->plan->name ?? 'خطة عامة'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="status">حالة الاتفاقية *</label>
                        <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(old('status', $agreement->status) === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-rose-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="notes">ملاحظات إدارية</label>
                    <textarea id="notes" name="notes" rows="4" class="<?php echo e($areaClass); ?>"><?php echo e(old('notes', $agreement->notes)); ?></textarea>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-5">
                    <a href="<?php echo e(route('admin.installments.agreements.show', $agreement)); ?>" class="btn-press inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm font-medium text-ink transition hover:bg-canvas">إلغاء</a>
                    <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                        <i class="fas fa-save text-xs"></i>
                        تحديث الاتفاقية
                    </button>
                </div>
            </form>
        </article>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\installments\agreements\edit.blade.php ENDPATH**/ ?>