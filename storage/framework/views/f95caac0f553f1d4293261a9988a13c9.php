<span <?php echo e($attributes->merge(['class' => 'app-datetime inline-block leading-snug'])); ?>>
    <span class="app-datetime__primary"><?php echo e($primary); ?></span>
    <?php if($secondary): ?>
        <span class="app-datetime__secondary block text-xs opacity-70 mt-0.5"><?php echo e($secondary); ?></span>
    <?php endif; ?>
</span>
<?php /**PATH /Users/cityphone/Documents/glottical/resources/views/components/app-datetime.blade.php ENDPATH**/ ?>