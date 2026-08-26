<?php
    $tzName = $name ?? 'timezone';
    $tzId = $id ?? 'timezoneSelect';
    $tzClass = $class ?? 'w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm';
    $tzOptions = \App\Support\AppTimezone::commonZones();
    $tzCurrent = old($tzName, $value ?? auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy());
    if ($tzCurrent && ! array_key_exists($tzCurrent, $tzOptions)) {
        $tzOptions = [$tzCurrent => $tzCurrent] + $tzOptions;
    }
    $tzRequired = $required ?? true;
    $tzLabel = $label ?? (app()->getLocale() === 'ar' ? 'المنطقة الزمنية للميعاد' : 'Appointment timezone');
    $tzHint = $hint ?? (app()->getLocale() === 'ar'
        ? 'الساعة اللي هتكتبها هتتحسب حسب المنطقة دي. مثال: أمريكا — نيويورك + 6:00 مساءً = المقابلة 6 مساءً بتوقيت نيويورك.'
        : 'The time you type is in this zone. Example: America/New York + 6:00 PM means 6 PM Eastern.');
?>
<div>
    <label for="<?php echo e($tzId); ?>" class="<?php echo e($labelClass ?? 'block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1'); ?>"><?php echo e($tzLabel); ?></label>
    <select name="<?php echo e($tzName); ?>" id="<?php echo e($tzId); ?>" class="<?php echo e($tzClass); ?>" data-timezone-select <?php if($tzRequired): ?> required <?php endif; ?>>
        <?php $__currentLoopData = $tzOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tzIdValue => $tzOptionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($tzIdValue); ?>" <?php if($tzCurrent === $tzIdValue): echo 'selected'; endif; ?>><?php echo e($tzOptionLabel); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php if($tzHint): ?>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?php echo e($tzHint); ?></p>
    <?php endif; ?>
    <?php $__errorArgs = [$tzName];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH /Users/cityphone/Documents/glottical/resources/views/partials/timezone-select.blade.php ENDPATH**/ ?>