
<?php
    $checkedKeys = array_values(array_unique(array_map('strval', $checkedKeys ?? [])));
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
    <?php $__currentLoopData = $featureKeysOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="inline-flex items-start gap-2">
            <input type="checkbox" name="features[<?php echo e($fk); ?>]" value="1" data-sub-feature="<?php echo e($fk); ?>"
                   class="mt-0.5 ml-2 rounded border-gray-300 text-sky-600 focus:ring-sky-500 shrink-0"
                   <?php echo e(in_array($fk, $checkedKeys, true) ? 'checked' : ''); ?>>
            <span class="min-w-0">
                <span class="block font-medium text-gray-900 leading-snug"><?php echo e($featureDisplayLines[$fk] ?? $fk); ?></span>
            </span>
        </label>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\subscriptions\_subscription-feature-checkboxes.blade.php ENDPATH**/ ?>