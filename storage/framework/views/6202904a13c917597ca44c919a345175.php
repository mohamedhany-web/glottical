

<?php $__env->startSection('title', 'إنشاء اجتماع جديد'); ?>
<?php $__env->startSection('header', 'إنشاء اجتماع جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">إنشاء اجتماع</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">تحكم كامل في عنوان الاجتماع وحدود المشاركين قبل البدء.</p>
    </div>

    <form action="<?php echo e(route('student.classroom.store')); ?>" method="POST" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">عنوان الاجتماع</label>
            <input type="text" name="title" value="<?php echo e(old('title', 'غرفة Muallimx - ' . now()->format('H:i'))); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
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
            <input type="number" min="2" max="<?php echo e((int) $limits['classroom_max_participants']); ?>" name="max_participants" value="<?php echo e(old('max_participants', (int) $limits['classroom_max_participants'])); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">الحد الأقصى حسب باقتك: <?php echo e((int) $limits['classroom_max_participants']); ?></p>
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
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">وضع البداية</label>
            <select name="start_now" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="1" <?php echo e(old('start_now', '1') === '1' ? 'selected' : ''); ?>>ابدأ الآن مباشرة</option>
                <option value="0" <?php echo e(old('start_now') === '0' ? 'selected' : ''); ?>>إنشاء كمجدول فقط</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">موعد الاجتماع (اختياري عند الجدولة)</label>
            <input type="datetime-local" name="scheduled_for" value="<?php echo e(old('scheduled_for')); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">إذا اخترت "إنشاء كمجدول فقط" يفضل تحديد موعد هنا.</p>
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
            <input type="number" min="15" max="<?php echo e((int) $limits['classroom_max_duration_minutes']); ?>" name="planned_duration_minutes" value="<?php echo e(old('planned_duration_minutes', (int) $limits['classroom_default_duration_minutes'])); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
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

        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-600 p-3 text-xs text-slate-600 dark:text-slate-300">
            استهلاك الشهر الحالي: <?php echo e(number_format($usedMeetingsThisMonth)); ?> من <?php echo e(number_format($limits['classroom_meetings_per_month'])); ?>.
            المتبقي: <span class="font-bold"><?php echo e(number_format($remainingMeetingsThisMonth)); ?></span>
        </div>

        <div class="flex items-center gap-2 justify-end">
            <a href="<?php echo e(route('student.classroom.index')); ?>" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300">إلغاء</a>
            <button type="submit" <?php echo e($remainingMeetingsThisMonth <= 0 ? 'disabled' : ''); ?> class="px-5 py-2 rounded-xl <?php echo e($remainingMeetingsThisMonth <= 0 ? 'bg-slate-400 cursor-not-allowed' : 'bg-red-500 hover:bg-red-600'); ?> text-white font-semibold">إنشاء الاجتماع</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\classroom\create.blade.php ENDPATH**/ ?>