
<?php
    $logoUrl = $adminPanelLogoUrl ?? null;
    $size = $size ?? 'lg';
    $fallback = $fallback ?? 'accent';
    $variant = $variant ?? 'light';
    $isDark = $variant === 'dark';
    $isSm = $size === 'sm';
    $box = $isSm ? 'size-10' : 'size-12';
    $brandText = $isSm ? 'text-xl' : 'text-2xl';
    $mText = $isSm ? 'text-lg' : 'text-xl';
    $mb = $mb ?? ($isSm ? 'mb-8' : 'mb-10');
    $nameClass = $isDark ? 'font-semibold text-white' : 'font-semibold text-ink';
    $logoWrap = $isDark
        ? 'bg-white/10 border border-white/15 shadow-soft ring-1 ring-white/10'
        : 'bg-surface border border-line shadow-soft';
?>
<a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-3 group <?php echo e($mb); ?>">
    <?php if($logoUrl): ?>
        <div class="<?php echo e($box); ?> rounded-xl flex items-center justify-center overflow-hidden <?php echo e($logoWrap); ?> transition group-hover:opacity-95">
            <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" class="h-full w-full object-contain p-1" width="48" height="48" loading="eager" decoding="async" fetchpriority="high" onerror="this.onerror=null;this.src=<?php echo e(\Illuminate\Support\Js::from(\App\Services\AdminPanelBranding::inlineFallbackDataUri())); ?>;">
        </div>
    <?php else: ?>
        <div class="<?php echo e($box); ?> flex items-center justify-center rounded-xl bg-accent text-white shadow-[0_10px_24px_rgba(11,61,145,0.28)] transition group-hover:bg-[#072A66]">
            <span class="font-bold <?php echo e($mText); ?>">G</span>
        </div>
    <?php endif; ?>
    <span class="<?php echo e($nameClass); ?> <?php echo e($brandText); ?> tracking-tight"><?php echo e(config('app.name')); ?></span>
</a>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\auth-brand-link.blade.php ENDPATH**/ ?>