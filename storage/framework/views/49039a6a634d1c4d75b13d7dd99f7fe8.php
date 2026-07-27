

<?php $__env->startSection('title', 'جداول عمل المدربين - Glottical'); ?>
<?php $__env->startSection('page_title', 'جداول عمل المدربين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $fmt = fn ($t) => is_string($t) ? substr($t, 0, 5) : (is_object($t) ? $t->format('H:i') : '');
?>

<div class="space-y-5" x-data="{
    slots: <?php echo e(Js::from($rules->map(fn ($r) => [
        'day_of_week' => (int) $r->day_of_week,
        'start_time' => is_string($r->start_time) ? substr($r->start_time, 0, 5) : $r->start_time->format('H:i'),
        'end_time' => is_string($r->end_time) ? substr($r->end_time, 0, 5) : $r->end_time->format('H:i'),
        'slot_duration_minutes' => (int) $r->slot_duration_minutes,
        'applies_to' => $r->applies_to,
        'note' => $r->note,
    ])->values())); ?>,
    addSlot(day) {
        this.slots.push({ day_of_week: day, start_time: '10:00', end_time: '14:00', slot_duration_minutes: 60, applies_to: 'both', note: '' });
    },
    removeSlot(i) { this.slots.splice(i, 1); }
}">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المجموعات · جدول أسبوعي منفصل عن كورسات 1:1</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">جداول عمل المدربين</h2>
        </div>
        <a href="<?php echo e(route('admin.tutoring-group-bookings.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-calendar-check text-xs"></i>
            الحجوزات
        </a>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">مدربون</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['instructors'])); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">نوافذ محفوظة</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['windows'])); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">نشطة</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['active'])); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">نوافذ المدرب المحدد</p>
            <p class="mt-1 text-xl font-semibold text-ink"><?php echo e(number_format($stats['selected'])); ?></p>
        </article>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">اختيار المدرب</h3>
        </div>
        <form method="GET" action="<?php echo e(route('admin.tutor-work-schedules.index')); ?>" class="flex flex-wrap items-end gap-3 p-4 sm:p-5">
            <div class="min-w-[220px] flex-1">
                <label class="<?php echo e($labelClass); ?>" for="instructor_id">المدرب</label>
                <select id="instructor_id" name="instructor_id" class="<?php echo e($fieldClass); ?>" onchange="this.form.submit()">
                    <?php $__empty_1 = true; $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($ins->id); ?>" <?php if($instructorId === (int) $ins->id): echo 'selected'; endif; ?>><?php echo e($ins->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <option value="">لا يوجد مدربون</option>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </article>

    <?php if($instructorId): ?>
        <form method="POST" action="<?php echo e(route('admin.tutor-work-schedules.sync')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="instructor_id" value="<?php echo e($instructorId); ?>">

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">نوافذ الأسبوع</h3>
                        <p class="mt-0.5 text-xs text-muted">احفظ لاستبدال الجدول الحالي لهذا المدرب بالكامل</p>
                    </div>
                    <button type="submit" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                        <i class="fas fa-save text-xs"></i>
                        حفظ الجدول
                    </button>
                </div>

                <div class="space-y-4 p-4 sm:p-5">
                    <?php $__currentLoopData = $dayLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-2xl border border-line bg-canvas/30 p-4">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <h4 class="text-sm font-semibold text-ink"><?php echo e($label); ?></h4>
                                <button type="button" @click="addSlot(<?php echo e($day); ?>)" class="btn-press inline-flex h-8 items-center gap-1.5 rounded-lg border border-line bg-surface px-3 text-xs font-medium text-accent">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    إضافة نافذة
                                </button>
                            </div>
                            <template x-for="(slot, index) in slots" :key="index">
                                <div x-show="Number(slot.day_of_week) === <?php echo e($day); ?>" class="mb-2 grid grid-cols-1 gap-2 rounded-xl border border-line bg-surface p-3 sm:grid-cols-6 sm:items-end">
                                    <input type="hidden" :name="'slots['+index+'][day_of_week]'" :value="slot.day_of_week">
                                    <div>
                                        <label class="<?php echo e($labelClass); ?>">من</label>
                                        <input type="time" class="<?php echo e($fieldClass); ?>" x-model="slot.start_time" :name="'slots['+index+'][start_time]'">
                                    </div>
                                    <div>
                                        <label class="<?php echo e($labelClass); ?>">إلى</label>
                                        <input type="time" class="<?php echo e($fieldClass); ?>" x-model="slot.end_time" :name="'slots['+index+'][end_time]'">
                                    </div>
                                    <div>
                                        <label class="<?php echo e($labelClass); ?>">مدة الشريحة</label>
                                        <select class="<?php echo e($fieldClass); ?>" x-model="slot.slot_duration_minutes" :name="'slots['+index+'][slot_duration_minutes]'">
                                            <option value="30">30</option>
                                            <option value="45">45</option>
                                            <option value="60">60</option>
                                            <option value="90">90</option>
                                            <option value="120">120</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="<?php echo e($labelClass); ?>">ينطبق على</label>
                                        <select class="<?php echo e($fieldClass); ?>" x-model="slot.applies_to" :name="'slots['+index+'][applies_to]'">
                                            <option value="both">فردي وجماعي</option>
                                            <option value="individual">فردي فقط</option>
                                            <option value="collective">جماعي فقط</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" @click="removeSlot(index)" class="btn-press inline-flex h-11 w-full items-center justify-center rounded-xl border border-line text-xs font-medium text-danger hover:bg-danger/5">حذف</button>
                                    </div>
                                </div>
                            </template>
                            <p class="text-xs text-muted" x-show="!slots.some(s => Number(s.day_of_week) === <?php echo e($day); ?>)">لا نوافذ لهذا اليوم</p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </article>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\tutor-work-schedules\index.blade.php ENDPATH**/ ?>