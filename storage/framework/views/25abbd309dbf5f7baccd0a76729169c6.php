

<?php $__env->startSection('title', 'جدول عمل المجموعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="tutorWorkForm()">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">جدول عمل المجموعات</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">نوافذ الأسبوع التي تظهر لحجز المجموعات الفردية والجماعية (منفصل عن جدول كورسات 1:1)</p>
        </div>
        <?php if(Route::has('instructor.one-to-one-availability.index')): ?>
            <a href="<?php echo e(route('instructor.one-to-one-availability.index')); ?>" class="text-sm text-sky-600 hover:underline">جدول جلسات الكورس 1:1</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?php echo e(route('instructor.tutor-work-schedule.update')); ?>" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-6">
        <?php echo csrf_field(); ?>
        <p class="text-sm text-slate-600 dark:text-slate-300">أضف نوافذ العمل الأسبوعية. الحفظ يستبدل الجدول الحالي بالكامل.</p>

        <template x-for="(slot, index) in slots" :key="index">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">اليوم</label>
                    <select :name="'slots['+index+'][day_of_week]'" x-model="slot.day_of_week" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                        <?php $__currentLoopData = $dayLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($day); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">من</label>
                    <input type="time" :name="'slots['+index+'][start_time]'" x-model="slot.start_time" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">إلى</label>
                    <input type="time" :name="'slots['+index+'][end_time]'" x-model="slot.end_time" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">المدة</label>
                    <input type="number" :name="'slots['+index+'][slot_duration_minutes]'" x-model="slot.slot_duration_minutes" min="30" max="240" step="15" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">ينطبق على</label>
                    <select :name="'slots['+index+'][applies_to]'" x-model="slot.applies_to" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                        <option value="both">فردي وجماعي</option>
                        <option value="individual">فردي</option>
                        <option value="collective">جماعي</option>
                    </select>
                </div>
                <div class="sm:col-span-1 flex gap-2">
                    <button type="button" @click="removeSlot(index)" class="px-3 py-2 rounded-lg border border-rose-200 text-rose-600 text-sm font-semibold hover:bg-rose-50" x-show="slots.length > 1">حذف</button>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap gap-3">
            <button type="button" @click="addSlot()" class="px-4 py-2 rounded-xl border border-sky-200 text-sky-700 text-sm font-bold hover:bg-sky-50">
                <i class="fas fa-plus ml-1"></i>إضافة نافذة
            </button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold">حفظ الجدول</button>
        </div>
    </form>

    <?php if($rules->isNotEmpty()): ?>
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="font-bold text-slate-900 dark:text-white mb-4">الجدول الحالي</h2>
        <div class="space-y-2 text-sm">
            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($dayGroup['rules']->isNotEmpty()): ?>
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="font-semibold text-slate-700 dark:text-slate-200 w-20"><?php echo e($dayGroup['label']); ?></span>
                        <?php $__currentLoopData = $dayGroup['rules']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="px-2 py-1 rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-800 dark:text-sky-200 text-xs font-medium">
                                <?php echo e(substr((string) $rule->start_time, 0, 5)); ?> – <?php echo e(substr((string) $rule->end_time, 0, 5)); ?>

                                (<?php echo e((int) $rule->slot_duration_minutes); ?> د)
                                · <?php echo e($rule->applies_to === 'individual' ? 'فردي' : ($rule->applies_to === 'collective' ? 'جماعي' : 'الكل')); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function tutorWorkForm() {
    const existing = <?php echo json_encode($rules->map(fn ($r) => [
        'day_of_week' => (string) $r->day_of_week, 'start_time' => substr((string) $r->start_time, 0) ?>;
    return {
        slots: existing.length ? existing : [{ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '60', applies_to: 'both' }],
        addSlot() {
            this.slots.push({ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '60', applies_to: 'both' });
        },
        removeSlot(i) {
            this.slots.splice(i, 1);
        }
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\instructor\tutor-work-schedule\index.blade.php ENDPATH**/ ?>