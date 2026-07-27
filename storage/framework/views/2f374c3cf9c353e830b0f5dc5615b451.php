

<?php $__env->startSection('title', 'إنشاء اتفاقية تقسيط'); ?>
<?php $__env->startSection('page_title', 'إنشاء اتفاقية تقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $plans = $plans ?? collect();
    $enrollments = $enrollments ?? collect();
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · التقسيط · اتفاقيات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">اتفاقية تقسيط جديدة</h2>
            <p class="mt-1 text-sm text-muted">اختر خطة التقسيط المناسبة، وحدد التسجيل الخاص بالطالب، ثم راجع بيانات المبالغ لتوليد جدول السداد تلقائياً</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للاتفاقيات
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft xl:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات الاتفاقية</h3>
                <p class="mt-0.5 text-xs text-muted">اربط الطالب بخطة دفع مرنة</p>
            </div>

            <form action="<?php echo e(route('admin.installments.agreements.store')); ?>" method="POST" class="space-y-6 p-4 sm:p-5">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="installment_plan_id">خطة التقسيط *</label>
                        <select id="installment_plan_id" name="installment_plan_id" class="<?php echo e($fieldClass); ?>" required>
                            <option value="">اختر خطة</option>
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($plan->id); ?>" <?php echo e(old('installment_plan_id', $selectedPlanId) == $plan->id ? 'selected' : ''); ?>>
                                    <?php echo e($plan->name); ?> — <?php echo e($plan->course->title ?? 'خطة عامة'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['installment_plan_id'];
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
                        <label class="<?php echo e($labelClass); ?>" for="student_course_enrollment_id">التسجيل المرتبط *</label>
                        <select id="student_course_enrollment_id" name="student_course_enrollment_id" class="<?php echo e($fieldClass); ?>" required>
                            <option value="">اختر طالباً وكورساً</option>
                            <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($enrollment->id); ?>" <?php echo e(old('student_course_enrollment_id') == $enrollment->id ? 'selected' : ''); ?>>
                                    <?php echo e($enrollment->student->name ?? 'طالب غير معروف'); ?> — <?php echo e($enrollment->course->title ?? 'بدون كورس'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['student_course_enrollment_id'];
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

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="start_date">تاريخ البدء *</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo e(old('start_date', now()->format('Y-m-d'))); ?>" required class="<?php echo e($fieldClass); ?>">
                        <?php $__errorArgs = ['start_date'];
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
                        <label class="<?php echo e($labelClass); ?>" for="status">حالة الاتفاقية</label>
                        <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                            <option value="">الحالة الافتراضية (نشط)</option>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(old('status') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="rounded-xl border border-line bg-canvas/40 p-4 sm:p-5">
                    <h4 class="mb-4 text-sm font-semibold text-ink">تفاصيل المبالغ</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="total_amount">إجمالي المبلغ</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="total_amount" name="total_amount" value="<?php echo e(old('total_amount')); ?>"
                                       class="<?php echo e($fieldClass); ?> ps-12" placeholder="يتم استخدام قيمة الخطة أو الكورس تلقائياً">
                                <span class="absolute inset-y-0 start-4 flex items-center text-xs font-medium text-muted">ج.م</span>
                            </div>
                        </div>
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="deposit_amount">الدفعة المقدمة</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="deposit_amount" name="deposit_amount" value="<?php echo e(old('deposit_amount')); ?>"
                                       class="<?php echo e($fieldClass); ?> ps-12">
                                <span class="absolute inset-y-0 start-4 flex items-center text-xs font-medium text-muted">ج.م</span>
                            </div>
                        </div>
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="installments_count">عدد الأقساط</label>
                            <input type="number" min="1" max="60" id="installments_count" name="installments_count" value="<?php echo e(old('installments_count')); ?>" class="<?php echo e($fieldClass); ?>">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="notes">ملاحظات إضافية</label>
                    <textarea id="notes" name="notes" rows="3" class="<?php echo e($areaClass); ?>" placeholder="أضف أي تعليمات أو ملاحظات تخص هذه الاتفاقية"><?php echo e(old('notes')); ?></textarea>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-5">
                    <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="btn-press inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm font-medium text-ink transition hover:bg-canvas">إلغاء</a>
                    <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                        <i class="fas fa-save text-xs"></i>
                        حفظ الاتفاقية
                    </button>
                </div>
            </form>
        </article>

        <div class="space-y-5">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">دليل سريع</h3>
                </div>
                <ul class="space-y-3 p-4 text-sm text-ink-soft sm:p-5">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-info-circle mt-0.5 text-accent"></i>
                        تأكد من اختيار خطة نشطة أو مرتبطة بكورس يسمح بالتقسيط.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-user mt-0.5 text-accent"></i>
                        لا يمكن تفعيل أكثر من اتفاقية نشطة لنفس تسجيل الطالب في نفس الوقت.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-calculator mt-0.5 text-accent"></i>
                        اترك حقول المبالغ فارغة لاستخدام قيم الخطة بشكل افتراضي.
                    </li>
                </ul>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">بحث عن تسجيل</h3>
                    <p class="mt-0.5 text-xs text-muted">صفِّ التسجيلات المتاحة قبل إنشاء الاتفاقية</p>
                </div>
                <form method="GET" class="space-y-4 p-4 sm:p-5">
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="student">اسم الطالب أو رقم الهاتف</label>
                        <input type="text" id="student" name="student" value="<?php echo e(request('student')); ?>" class="<?php echo e($fieldClass); ?>">
                    </div>
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="course">اسم الكورس</label>
                        <input type="text" id="course" name="course" value="<?php echo e(request('course')); ?>" class="<?php echo e($fieldClass); ?>">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                            <i class="fas fa-search text-xs"></i>
                            تطبيق البحث
                        </button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/installments/agreements/create.blade.php ENDPATH**/ ?>