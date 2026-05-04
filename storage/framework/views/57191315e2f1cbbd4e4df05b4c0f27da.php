
<?php
    $brandIcon = \App\Services\AdminPanelBranding::logoPublicUrl();
?>
<?php if($brandIcon): ?>
    <link rel="icon" href="<?php echo e($brandIcon); ?>" sizes="any">
    <link rel="shortcut icon" href="<?php echo e($brandIcon); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($brandIcon); ?>">
    <link rel="icon" href="<?php echo e($brandIcon); ?>" sizes="32x32">
    <link rel="icon" href="<?php echo e($brandIcon); ?>" sizes="16x16">
<?php else: ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\partials\favicon-links.blade.php ENDPATH**/ ?>