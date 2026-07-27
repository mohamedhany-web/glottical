

<?php $__env->startSection('title', 'تفاصيل حجز الحصة المجانية - Glottical'); ?>
<?php $__env->startSection('page_title', 'تفاصيل الحجز'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $statusLabel = match ($booking->status) {
        'confirmed' => 'مؤكد',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
        default => $booking->status,
    };
    $statusTone = match ($booking->status) {
        'confirmed' => 'bg-accent-soft text-accent',
        'completed' => 'bg-canvas-muted text-muted',
        'cancelled' => 'bg-danger/10 text-danger',
        default => 'bg-canvas-muted text-muted',
    };
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">حجوزات الحصة المجانية · تقييم المستوى</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($booking->name); ?></h2>
            <p class="mt-1 text-sm text-muted">حجز #<?php echo e($booking->id); ?> · <?php echo e($booking->created_at?->diffForHumans()); ?></p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.free-trial-bookings.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع للقائمة
            </a>
            <a href="<?php echo e(route('admin.free-trial-bookings.availability')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-clock text-xs"></i>
                أوقات الأسبوع
            </a>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-calendar-day text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">الموعد</p>
            <p class="mt-1 text-base font-semibold tabular-nums tracking-tight text-ink"><?php echo e($booking->starts_at?->format('Y-m-d')); ?></p>
            <p class="mt-0.5 text-sm text-muted"><?php echo e($booking->starts_at?->format('H:i')); ?> — <?php echo e($booking->ends_at?->format('H:i')); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal">
                <i class="fas fa-hourglass-half text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">المدة</p>
            <p class="mt-1 text-base font-semibold tracking-tight text-ink"><?php echo e((int) $booking->duration_minutes); ?> دقيقة</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($statusTone); ?>">
                <i class="fas fa-flag text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">الحالة</p>
            <p class="mt-1">
                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold <?php echo e($statusTone); ?>"><?php echo e($statusLabel); ?></span>
            </p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-envelope text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">التواصل</p>
            <p class="mt-1 truncate text-sm font-semibold text-ink" title="<?php echo e($booking->email); ?>"><?php echo e($booking->email ?: '—'); ?></p>
            <p class="mt-0.5 text-sm text-muted" dir="ltr"><?php echo e($booking->phone ?: '—'); ?></p>
        </article>
    </section>

    <div class="grid gap-5 lg:grid-cols-5">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft lg:col-span-3">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات الحجز</h3>
                <p class="mt-0.5 text-xs text-muted">تفاصيل الطالب وهدف التعلّم المرتبط بهذا الموعد</p>
            </div>
            <div class="space-y-4 p-4 sm:p-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-line bg-canvas/60 p-4">
                        <p class="text-xs font-medium text-muted">الاسم</p>
                        <p class="mt-1 text-sm font-semibold text-ink"><?php echo e($booking->name); ?></p>
                    </div>
                    <div class="rounded-xl border border-line bg-canvas/60 p-4">
                        <p class="text-xs font-medium text-muted">البريد</p>
                        <p class="mt-1 text-sm font-semibold text-ink break-all"><?php echo e($booking->email ?: '—'); ?></p>
                    </div>
                    <div class="rounded-xl border border-line bg-canvas/60 p-4">
                        <p class="text-xs font-medium text-muted">الهاتف</p>
                        <p class="mt-1 text-sm font-semibold text-ink" dir="ltr"><?php echo e($booking->phone ?: '—'); ?></p>
                    </div>
                    <div class="rounded-xl border border-line bg-canvas/60 p-4">
                        <p class="text-xs font-medium text-muted">تاريخ الإنشاء</p>
                        <p class="mt-1 text-sm font-semibold text-ink"><?php echo e($booking->created_at?->format('Y-m-d H:i') ?: '—'); ?></p>
                    </div>
                </div>

                <div class="rounded-xl border border-line bg-canvas/60 p-4">
                    <p class="text-xs font-medium text-muted">هدف التعلم</p>
                    <p class="mt-1 text-sm leading-7 text-ink"><?php echo e($booking->goal ?: '—'); ?></p>
                </div>

                <?php if($booking->user): ?>
                    <div class="rounded-xl border border-line bg-canvas/60 p-4">
                        <p class="text-xs font-medium text-muted">حساب مسجّل على المنصة</p>
                        <p class="mt-1 text-sm font-semibold text-ink"><?php echo e($booking->user->name); ?></p>
                        <p class="mt-0.5 text-sm text-muted"><?php echo e($booking->user->email); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft lg:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">تحديث الحالة</h3>
                <p class="mt-0.5 text-xs text-muted">غيّر الحالة وأضف ملاحظات داخلية للمتابعة</p>
            </div>
            <div class="p-4 sm:p-5">
                <form method="post" action="<?php echo e(route('admin.free-trial-bookings.update-status', $booking)); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                        <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                            <option value="confirmed" <?php if($booking->status === 'confirmed'): echo 'selected'; endif; ?>>مؤكد</option>
                            <option value="completed" <?php if($booking->status === 'completed'): echo 'selected'; endif; ?>>مكتمل</option>
                            <option value="cancelled" <?php if($booking->status === 'cancelled'): echo 'selected'; endif; ?>>ملغي</option>
                        </select>
                    </div>
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="notes">ملاحظات داخلية</label>
                        <textarea id="notes" name="notes" rows="5" class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="ملاحظات للمتابعة…"><?php echo e(old('notes', $booking->notes)); ?></textarea>
                    </div>
                    <button type="submit" class="btn-press inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                        <i class="fas fa-save text-xs"></i>
                        حفظ التحديث
                    </button>
                </form>

                <form method="post" action="<?php echo e(route('admin.free-trial-bookings.destroy', $booking)); ?>" class="mt-4 border-t border-line pt-4" onsubmit="return confirm('حذف الحجز نهائياً؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-press inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-danger/30 bg-surface px-4 text-sm font-medium text-danger transition hover:bg-danger/5">
                        <i class="fas fa-trash text-xs"></i>
                        حذف الحجز
                    </button>
                </form>
            </div>
        </article>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\free-trial-bookings\show.blade.php ENDPATH**/ ?>