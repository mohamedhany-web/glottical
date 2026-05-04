

<?php $__env->startSection('title', 'تعديل الاجتماع'); ?>
<?php $__env->startSection('header', 'تعديل الاجتماع'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <form action="<?php echo e(route('student.classroom.update', $meeting)); ?>" method="POST" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">تعديل إعدادات الاجتماع</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">الكود: <span class="font-mono"><?php echo e($meeting->code); ?></span></p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">عنوان الاجتماع</label>
            <input type="text" name="title" value="<?php echo e(old('title', $meeting->title)); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الحد الأقصى للمشاركين</label>
            <input type="number" min="2" max="<?php echo e((int) $limits['classroom_max_participants']); ?>" name="max_participants" value="<?php echo e(old('max_participants', (int) ($meeting->max_participants ?? 25))); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <?php $__errorArgs = ['max_participants'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">موعد الاجتماع (اختياري)</label>
            <input type="datetime-local" name="scheduled_for" value="<?php echo e(old('scheduled_for', optional($meeting->scheduled_for)->format('Y-m-d\TH:i'))); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <?php $__errorArgs = ['scheduled_for'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">مدة الاجتماع (بالدقائق)</label>
            <input type="number" min="15" max="<?php echo e((int) $limits['classroom_max_duration_minutes']); ?>" name="planned_duration_minutes" value="<?php echo e(old('planned_duration_minutes', (int) ($meeting->planned_duration_minutes ?? $limits['classroom_default_duration_minutes']))); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">الافتراضي حسب الباقة: <?php echo e((int) $limits['classroom_default_duration_minutes']); ?> دقيقة — الحد الأقصى: <?php echo e((int) $limits['classroom_max_duration_minutes']); ?> دقيقة.</p>
            <?php $__errorArgs = ['planned_duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="<?php echo e(route('student.classroom.show', $meeting)); ?>" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300">رجوع</a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold">حفظ التعديلات</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\classroom\edit.blade.php ENDPATH**/ ?>