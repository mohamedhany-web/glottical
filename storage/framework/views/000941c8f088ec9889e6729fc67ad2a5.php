<?php $__env->startSection('title', 'التسكين'); ?>
<?php $__env->startSection('page_title', 'نظام التسكين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs text-muted">الطلاب والخدمات</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">التسكين حسب المعلم والطالب والباقة</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">اختر طالباً لديه باقة نشطة، ثم معلماً له وقت متاح، ثم ثبّت الموعد — للنظام 1:1 أو المجموعات.</p>
        </div>
        <a href="<?php echo e(route('admin.placement.create')); ?>" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
            <i class="fas fa-user-check"></i> تسكين جديد
        </a>
    </div>

    <?php echo $__env->make('admin.partials.workflow-guide', [
        'title' => 'ما هو التسكين؟',
        'body' => 'للحصص الفردية ثبّت شهرياً (موعدان أسبوعياً) بدل حجز حصة بحصة — أخف على الطالب والمعلم. الرصيد يُحجز عند التثبيت ويُخصم عند إكمال كل حصة.',
        'steps' => [
            'تأكد أن للطالب باقة نشطة أو رصيد قابل للحجز يكفي عدد الحصص.',
            'حدّث جداول توافر المعلم 1:1.',
            'من «تسكين جديد» اختر تثبيتاً شهرياً أو عدة مواعيد مع نفس المعلم.',
            'راجع الحصص المجدولة من قائمة 1:1، أو احذف التسكين المسجّل من القائمة أدناه.',
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">بانتظار موعد 1:1</p>
            <p class="mt-1 text-2xl font-semibold text-ink"><?php echo e($pendingOneToOne); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">حصص 1:1 قادمة</p>
            <p class="mt-1 text-2xl font-semibold text-ink"><?php echo e($upcomingOneToOne); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">حجوزات مجموعات قادمة</p>
            <p class="mt-1 text-2xl font-semibold text-ink"><?php echo e($upcomingGroups); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">وحدات قابلة للحجز</p>
            <p class="mt-1 text-2xl font-semibold text-ink"><?php echo e($bookableCredits); ?></p>
        </article>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h3 class="text-base font-semibold text-ink">أحدث تسكين 1:1</h3>
                <a href="<?php echo e(route('admin.one-to-one-sessions.index')); ?>" class="text-xs font-medium text-accent">الكل</a>
            </div>
            <div class="divide-y divide-line">
                <?php $__empty_1 = true; $__currentLoopData = $recentPrivate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $instructorTz = \App\Support\AppTimezone::forUser($session->instructor);
                        $scheduledLocal = \App\Support\AppTimezone::datetimeLocalValue($session->scheduled_at, $instructorTz);
                    ?>
                    <div class="py-3 text-sm space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <a href="<?php echo e(route('admin.one-to-one-sessions.show', $session)); ?>" class="min-w-0 flex-1 hover:bg-slate-50/60 rounded-lg px-1 py-0.5">
                                <p class="truncate font-medium text-ink"><?php echo e($session->student?->name); ?> ← <?php echo e($session->instructor?->name); ?></p>
                                <p class="text-xs text-muted"><?php echo e($session->statusLabel()); ?> · <?php echo e(optional($session->scheduled_at ?? $session->created_at)->format('Y-m-d H:i')); ?></p>
                            </a>
                            <?php if($session->isOpenPlacement()): ?>
                                <form method="POST" action="<?php echo e(route('admin.placement.destroy-private', $session)); ?>" class="shrink-0"
                                      onsubmit="return confirm(<?php echo json_encode($session->series_id ? 'حذف التسكين؟ سيتم إلغاء كل الحصص غير المكتملة في هذا التسكين وإرجاع الرصيد المحجوز.' : 'حذف هذا التسكين؟ سيُلغى الموعد ويُعاد الرصيد المحجوز.', 15, 512) ?>);">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-xs font-medium text-danger hover:underline">حذف التسكين</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <?php if($session->isOpenPlacement()): ?>
                            <form method="POST" action="<?php echo e(route('admin.placement.update-private-schedule', $session)); ?>"
                                  class="flex flex-wrap items-end gap-2 rounded-xl border border-line/80 bg-canvas/40 p-2.5">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <div class="min-w-[11rem] flex-1">
                                    <label class="mb-1 block text-[10px] font-medium text-muted">موعد الحصة (توقيت المعلم)</label>
                                    <input type="datetime-local" name="scheduled_at" value="<?php echo e(old('scheduled_at', $scheduledLocal)); ?>"
                                           required dir="ltr"
                                           class="h-9 w-full rounded-lg border border-line bg-surface px-2.5 text-xs text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                                </div>
                                <div class="w-20">
                                    <label class="mb-1 block text-[10px] font-medium text-muted">المدة (د)</label>
                                    <input type="number" name="duration_minutes" min="15" max="180" step="5"
                                           value="<?php echo e(old('duration_minutes', (int) ($session->duration_minutes ?: 50))); ?>"
                                           class="h-9 w-full rounded-lg border border-line bg-surface px-2.5 text-xs text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                                </div>
                                <input type="hidden" name="timezone" value="<?php echo e($instructorTz); ?>">
                                <button type="submit" class="btn-press inline-flex h-9 items-center rounded-lg bg-accent px-3 text-xs font-semibold text-white">
                                    حفظ الموعد
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="py-6 text-center text-sm text-muted">لا تسكين 1:1 بعد</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h3 class="text-base font-semibold text-ink">أحدث تسكين مجموعات</h3>
                <a href="<?php echo e(route('admin.tutoring-group-bookings.index')); ?>" class="text-xs font-medium text-accent">الكل</a>
            </div>
            <div class="divide-y divide-line">
                <?php $__empty_1 = true; $__currentLoopData = $recentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $groupInstructorTz = \App\Support\AppTimezone::forUser($booking->instructor);
                        $startsLocal = \App\Support\AppTimezone::datetimeLocalValue($booking->starts_at, $groupInstructorTz);
                    ?>
                    <div class="py-3 text-sm space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <a href="<?php echo e(route('admin.tutoring-group-bookings.show', $booking)); ?>" class="min-w-0 flex-1 hover:bg-slate-50/60 rounded-lg px-1 py-0.5">
                                <p class="truncate font-medium text-ink"><?php echo e($booking->user?->name); ?> · <?php echo e($booking->tutoringGroup?->title); ?></p>
                                <p class="text-xs text-muted"><?php echo e($booking->statusLabel()); ?> · <?php echo e(optional($booking->starts_at)->format('Y-m-d H:i')); ?></p>
                            </a>
                            <?php if($booking->isOpenPlacement()): ?>
                                <form method="POST" action="<?php echo e(route('admin.placement.destroy-group', $booking)); ?>" class="shrink-0"
                                      onsubmit="return confirm('حذف تسكين المجموعة؟ سيُلغى الحجز ويُعاد الرصيد المحجوز.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-xs font-medium text-danger hover:underline">حذف التسكين</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <?php if($booking->isOpenPlacement()): ?>
                            <form method="POST" action="<?php echo e(route('admin.placement.update-group-schedule', $booking)); ?>"
                                  class="flex flex-wrap items-end gap-2 rounded-xl border border-line/80 bg-canvas/40 p-2.5">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <div class="min-w-[11rem] flex-1">
                                    <label class="mb-1 block text-[10px] font-medium text-muted">موعد الحصة (توقيت المعلم)</label>
                                    <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', $startsLocal)); ?>"
                                           required dir="ltr"
                                           class="h-9 w-full rounded-lg border border-line bg-surface px-2.5 text-xs text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                                </div>
                                <input type="hidden" name="timezone" value="<?php echo e($groupInstructorTz); ?>">
                                <button type="submit" class="btn-press inline-flex h-9 items-center rounded-lg bg-accent px-3 text-xs font-semibold text-white">
                                    حفظ الموعد
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="py-6 text-center text-sm text-muted">لا حجوزات مجموعات بعد</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <a href="<?php echo e(route('admin.placement.create', ['mode' => 'private'])); ?>" class="rounded-2xl border border-accent/25 bg-accent/5 p-4 transition hover:border-accent/50">
            <p class="text-sm font-semibold text-ink"><i class="fas fa-user me-2 text-accent"></i>تسكين 1:1</p>
            <p class="mt-1 text-xs text-muted">طالب + معلم + موعد من جدول التوافر + باقة خاصة</p>
        </a>
        <a href="<?php echo e(route('admin.placement.create', ['mode' => 'group'])); ?>" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
            <p class="text-sm font-semibold text-ink"><i class="fas fa-users me-2 text-accent"></i>تسكين مجموعة / فصل</p>
            <p class="mt-1 text-xs text-muted">طالب + مجموعة + موعد من جدول عمل المدرب + باقة جماعية</p>
        </a>
        <?php if(Route::has('admin.student-entitlements.create')): ?>
            <a href="<?php echo e(route('admin.student-entitlements.create')); ?>" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-coins me-2 text-accent"></i>منح باقة / رصيد</p>
                <p class="mt-1 text-xs text-muted">إذا الطالب غير مشترك — امنحه رصيداً ثم اسكنه</p>
            </a>
        <?php endif; ?>
        <?php if(Route::has('admin.tutoring-group-bookings.index')): ?>
            <a href="<?php echo e(route('admin.tutoring-group-bookings.index')); ?>" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-calendar-check me-2 text-accent"></i>تسكين الفصول والحجوزات</p>
                <p class="mt-1 text-xs text-muted">متابعة حجوزات المجموعات وإعادة التسكين</p>
            </a>
        <?php endif; ?>
        <?php if(Route::has('admin.tutoring-groups.index')): ?>
            <a href="<?php echo e(route('admin.tutoring-groups.index', 'collective')); ?>" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-school me-2 text-accent"></i>فصول المدرسة</p>
                <p class="mt-1 text-xs text-muted">إدارة الفصول الجماعية المرتبطة بالطلاب</p>
            </a>
        <?php endif; ?>
        <?php if(Route::has('admin.tutor-work-schedules.index')): ?>
            <a href="<?php echo e(route('admin.tutor-work-schedules.index')); ?>" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-calendar-week me-2 text-accent"></i>جداول عمل المدربين</p>
                <p class="mt-1 text-xs text-muted">أوقات التوافر اللازمة للتسكين</p>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/glottical/resources/views/admin/placement/index.blade.php ENDPATH**/ ?>