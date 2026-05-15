
<?php
    $logoUrl = $adminPanelLogoUrl ?? null;
    $size = $size ?? 'lg';
    $fallback = $fallback ?? 'orange';
    $variant = $variant ?? 'light';
    $isDark = $variant === 'dark';
    $isSm = $size === 'sm';
    $box = $isSm ? 'w-10 h-10' : 'w-12 h-12';
    $brandText = $isSm ? 'text-xl' : 'text-2xl';
    $mText = $isSm ? 'text-lg' : 'text-xl';
    $mb = $mb ?? ($isSm ? 'mb-8' : 'mb-10');
    $nameClass = $isDark ? 'text-white font-extrabold' : 'text-mx-indigo font-extrabold';
    $logoWrap = $isDark
        ? 'bg-white/10 border border-white/20 shadow-lg shadow-black/20 ring-1 ring-white/10'
        : 'bg-white border border-slate-200/80 shadow-lg shadow-slate-200/40 ring-1 ring-slate-100';
?>
<a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-3 group <?php echo e($mb); ?>">
    <?php if($logoUrl): ?>
        <div class="<?php echo e($box); ?> rounded-xl flex items-center justify-center overflow-hidden <?php echo e($logoWrap); ?> group-hover:brightness-110 transition-all">
            <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" class="w-full h-full object-contain p-1" width="48" height="48" loading="eager" decoding="async">
        </div>
    <?php elseif($fallback === 'gradient' && $isSm): ?>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#00A3C4] to-[#0B3D91] flex items-center justify-center shadow-lg shadow-cyan-900/30">
            <span class="text-white font-black text-lg">M</span>
        </div>
    <?php else: ?>
        <div class="<?php echo e($box); ?> rounded-xl <?php echo e($isDark ? 'bg-gradient-to-br from-acad-yellow to-amber-400' : 'bg-[#FB5607]'); ?> flex items-center justify-center shadow-lg <?php echo e($isDark ? 'shadow-black/30' : 'shadow-orange-500/25'); ?> group-hover:opacity-95 transition-opacity">
            <span class="text-<?php echo e($isDark ? '[#0B3D91]' : 'white'); ?> font-black <?php echo e($mText); ?>">M</span>
        </div>
    <?php endif; ?>
    <span class="<?php echo e($nameClass); ?> <?php echo e($brandText); ?>" <?php if($isSm): ?> style="font-family:Tajawal,sans-serif" <?php endif; ?>><?php echo e(config('app.name', 'Muallimx')); ?></span>
</a>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views/partials/auth-brand-link.blade.php ENDPATH**/ ?>