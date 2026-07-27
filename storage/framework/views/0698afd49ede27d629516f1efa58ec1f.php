

<?php $__env->startSection('title', 'تفاصيل حجز مجموعة - Glottical'); ?>
<?php $__env->startSection('page_title', 'تفاصيل الحجز'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">حجوزات المجموعات · #<?php echo e($booking->id); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($booking->tutoringGroup?->title); ?></h2>
        </div>
        <a href="<?php echo e(route('admin.tutoring-group-bookings.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع
        </a>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <div class="grid gap-5 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات الحجز</h3>
            </div>
            <dl class="space-y-3 p-4 text-sm sm:p-5">
                <div class="flex justify-between gap-3"><dt class="text-muted">النوع</dt><dd class="font-medium text-ink"><?php echo e($booking->tutoringGroup?->typeLabel()); ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">المدرب</dt><dd class="font-medium text-ink"><?php echo e($booking->instructor?->name); ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">الطالب</dt><dd class="font-medium text-ink"><?php echo e($booking->contactName()); ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">الهاتف</dt><dd class="font-medium text-ink"><?php echo e($booking->contactPhone() ?: '—'); ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">البريد</dt><dd class="font-medium text-ink"><?php echo e($booking->contactEmail() ?: '—'); ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">من</dt><dd class="font-medium tabular-nums text-ink"><?php echo e($booking->starts_at?->format('Y-m-d H:i')); ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">إلى</dt><dd class="font-medium tabular-nums text-ink"><?php echo e($booking->ends_at?->format('Y-m-d H:i')); ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">الحالة</dt><dd class="font-medium text-accent"><?php echo e($booking->statusLabel()); ?></dd></div>
                <?php if($booking->student_notes): ?>
                    <div><dt class="mb-1 text-muted">ملاحظات الطالب</dt><dd class="text-ink"><?php echo e($booking->student_notes); ?></dd></div>
                <?php endif; ?>
            </dl>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">تحديث الحالة</h3>
            </div>
            <form method="POST" action="<?php echo e(route('admin.tutoring-group-bookings.update-status', $booking)); ?>" class="space-y-4 p-4 sm:p-5">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                    <select id="status" name="status" class="<?php echo e($fieldClass); ?>" required>
                        <?php $__currentLoopData = ['pending'=>'قيد المراجعة','confirmed'=>'مؤكد','cancelled'=>'ملغي','completed'=>'مكتمل']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if($booking->status === $val): echo 'selected'; endif; ?>><?php echo e($lab); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="admin_notes">ملاحظات الإدارة</label>
                    <textarea id="admin_notes" name="admin_notes" rows="4" class="<?php echo e($areaClass); ?>"><?php echo e(old('admin_notes', $booking->admin_notes)); ?></textarea>
                </div>
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">حفظ الحالة</button>
            </form>
            <form method="POST" action="<?php echo e(route('admin.tutoring-group-bookings.destroy', $booking)); ?>" class="border-t border-line p-4 sm:p-5" onsubmit="return confirm('حذف الحجز؟');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl border border-line px-4 text-sm font-medium text-danger hover:bg-danger/5">حذف الحجز</button>
            </form>
        </article>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\tutoring-group-bookings\show.blade.php ENDPATH**/ ?>