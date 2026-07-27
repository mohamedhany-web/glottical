<?php
    $acadTheme = config('academy-theme');
?>
<style>
    :root {
        --acad-navy: <?php echo e($acadTheme['navy']); ?>;
        --acad-navy-mid: <?php echo e($acadTheme['navy_mid']); ?>;
        --acad-navy-gradient: <?php echo e($acadTheme['navy_gradient']); ?>;
        --acad-navy-deep: <?php echo e($acadTheme['navy_deep']); ?>;
        --acad-navy-hero-mid: <?php echo e($acadTheme['navy_hero_mid']); ?>;
        --acad-navy-rgb: <?php echo e($acadTheme['navy_rgb']); ?>;
        --acad-navy-mid-rgb: <?php echo e($acadTheme['navy_mid_rgb']); ?>;
        --acad-blue: <?php echo e($acadTheme['blue']); ?>;
        --acad-blue-dark: <?php echo e($acadTheme['blue_dark']); ?>;
        --acad-cyan: <?php echo e($acadTheme['cyan']); ?>;
        --acad-cyan-rgb: <?php echo e($acadTheme['cyan_rgb']); ?>;
        --acad-yellow: <?php echo e($acadTheme['yellow']); ?>;
        --acad-yellow-rgb: <?php echo e($acadTheme['yellow_rgb']); ?>;
        --acad-ink: <?php echo e($acadTheme['ink']); ?>;
        --acad-canvas: <?php echo e($acadTheme['canvas'] ?? '#F3F5F7'); ?>;
        --acad-surface: <?php echo e($acadTheme['surface'] ?? '#FFFFFF'); ?>;
        --acad-muted: <?php echo e($acadTheme['muted'] ?? '#5B6577'); ?>;
        --acad-line: <?php echo e($acadTheme['line'] ?? '#D7DDE6'); ?>;
    }
</style>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\academy-theme-vars.blade.php ENDPATH**/ ?>