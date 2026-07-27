

<?php $__env->startSection('title', 'إضافة خطة تقسيط جديدة - ' . config('app.name')); ?>
<?php $__env->startSection('page_title', 'إضافة خطة تقسيط جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $frequencyUnits = $frequencyUnits ?? [];
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · التقسيط · خطط</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إنشاء خطة تقسيط</h2>
            <p class="mt-1 text-sm text-muted">اربط الخطة بكورس معين، حدّد قيمة الأقساط وفتراتها، واختر إن كان النظام سيولّد الخطط تلقائياً عند تسجيل الطالب</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.installments.plans.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة لقائمة الخطط
            </a>
        </div>
    </section>

    <div class="mx-auto max-w-4xl">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات الخطة</h3>
                <p class="mt-0.5 text-xs text-muted">أدخل التفاصيل الأساسية، ثم تابع بتحديد الأقساط والدورية</p>
            </div>

            <form action="<?php echo e(route('admin.installments.plans.store')); ?>" method="POST" class="space-y-6 p-4 sm:p-5">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="name">اسم الخطة *</label>
                        <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required class="<?php echo e($fieldClass); ?>">
                        <?php $__errorArgs = ['name'];
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
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="advanced_course_id">الكورس المرتبط (اختياري)</label>
                        <select id="advanced_course_id" name="advanced_course_id" class="<?php echo e($fieldClass); ?>">
                            <option value="">خطة عامة</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id') == $course->id ? 'selected' : ''); ?>>
                                    <?php echo e($course->title); ?> (<?php echo e(number_format($course->price ?? 0, 2)); ?> ج.م)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['advanced_course_id'];
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
                    <label class="<?php echo e($labelClass); ?>" for="description">تفاصيل الخطة</label>
                    <textarea id="description" name="description" rows="3" class="<?php echo e($areaClass); ?>" placeholder="أضف تفاصيل توضيحية إضافية عن خطة التقسيط"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
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

                <div class="rounded-xl border border-line bg-accent-soft/50 px-4 py-3 text-xs leading-relaxed text-ink">
                    <strong class="mb-1 block text-sm font-semibold">نصائح سريعة</strong>
                    - اترك المبلغ الإجمالي فارغاً ليتم استخدام سعر الكورس إن وجد.
                    <br>- يمكنك استخدام الخطة لكورس واحد أو كخطة عامة لكافة الطلاب.
                </div>

                <div class="rounded-xl border border-line bg-canvas/40 p-4 sm:p-5">
                    <h4 class="mb-4 text-sm font-semibold text-ink">الجانب المالي</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="total_amount">إجمالي المبلغ</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="total_amount" name="total_amount" value="<?php echo e(old('total_amount')); ?>"
                                       class="<?php echo e($fieldClass); ?> ps-12" placeholder="يتم استخدام سعر الكورس إن تركته فارغًا">
                                <span class="absolute inset-y-0 start-4 flex items-center text-xs font-medium text-muted">ج.م</span>
                            </div>
                            <?php $__errorArgs = ['total_amount'];
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
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="deposit_amount">الدفعة المقدمة</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="deposit_amount" name="deposit_amount" value="<?php echo e(old('deposit_amount', 0)); ?>"
                                       class="<?php echo e($fieldClass); ?> ps-12">
                                <span class="absolute inset-y-0 start-4 flex items-center text-xs font-medium text-muted">ج.م</span>
                            </div>
                            <?php $__errorArgs = ['deposit_amount'];
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
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="installments_count">عدد الأقساط *</label>
                            <input type="number" min="1" max="36" id="installments_count" name="installments_count" value="<?php echo e(old('installments_count', 6)); ?>" required class="<?php echo e($fieldClass); ?>">
                            <?php $__errorArgs = ['installments_count'];
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
                </div>

                <div class="rounded-xl border border-line bg-canvas/40 p-4 sm:p-5">
                    <h4 class="mb-4 text-sm font-semibold text-ink">الدورية والسماح</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="frequency_unit">وحدة الدورية *</label>
                            <select id="frequency_unit" name="frequency_unit" class="<?php echo e($fieldClass); ?>">
                                <?php $__currentLoopData = $frequencyUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(old('frequency_unit', 'month') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['frequency_unit'];
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
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="frequency_interval">الفاصل الزمني *</label>
                            <input type="number" min="1" max="12" id="frequency_interval" name="frequency_interval" value="<?php echo e(old('frequency_interval', 1)); ?>" required class="<?php echo e($fieldClass); ?>">
                            <?php $__errorArgs = ['frequency_interval'];
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
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="grace_period_days">فترة السماح (أيام)</label>
                            <input type="number" min="0" max="30" id="grace_period_days" name="grace_period_days" value="<?php echo e(old('grace_period_days', 0)); ?>" class="<?php echo e($fieldClass); ?>">
                            <?php $__errorArgs = ['grace_period_days'];
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
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                        <input type="checkbox" name="auto_generate_on_enrollment" value="1" <?php echo e(old('auto_generate_on_enrollment') ? 'checked' : ''); ?> class="rounded border-line text-accent focus:ring-accent/20">
                        إنشاء جدول الأقساط تلقائيًا عند تفعيل التسجيل
                    </label>
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="rounded border-line text-accent focus:ring-accent/20">
                        تفعيل الخطة فورًا
                    </label>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-5">
                    <a href="<?php echo e(route('admin.installments.plans.index')); ?>" class="btn-press inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm font-medium text-ink transition hover:bg-canvas">إلغاء</a>
                    <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                        <i class="fas fa-save text-xs"></i>
                        حفظ الخطة
                    </button>
                </div>
            </form>
        </article>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\installments\plans\create.blade.php ENDPATH**/ ?>