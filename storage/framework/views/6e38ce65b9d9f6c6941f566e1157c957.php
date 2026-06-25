<?php
    $acadTheme = config('academy-theme');
?>
<style>
    :root {
        --acad-navy: <?php echo e($acadTheme['navy']); ?>;
        --acad-navy-mid: <?php echo e($acadTheme['navy_mid']); ?>;
        --acad-navy-gradient: <?php echo e($acadTheme['navy_gradient']); ?>;
        --acad-navy-deep: <?php echo e($acadTheme['navy_deep']); ?>;
        --acad-navy-rgb: <?php echo e($acadTheme['navy_rgb']); ?>;
        --acad-navy-mid-rgb: <?php echo e($acadTheme['navy_mid_rgb']); ?>;
    }
</style>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\academy-theme-vars.blade.php ENDPATH**/ ?>