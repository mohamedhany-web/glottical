
<?php
    $logoUrl = $adminPanelLogoUrl ?? null;
    $size = $size ?? 'lg';
    $fallback = $fallback ?? 'orange';
    $isSm = $size === 'sm';
    $box = $isSm ? 'w-10 h-10' : 'w-12 h-12';
    $brandText = $isSm ? 'text-xl' : 'text-2xl';
    $mText = $isSm ? 'text-lg' : 'text-xl';
    $mb = $mb ?? ($isSm ? 'mb-8' : 'mb-10');
?>
<a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-3 group <?php echo e($mb); ?>">
    <?php if($logoUrl): ?>
        <div class="<?php echo e($box); ?> rounded-xl flex items-center justify-center overflow-hidden bg-white border border-slate-200/80 shadow-lg shadow-slate-200/40 group-hover:shadow-md transition-shadow ring-1 ring-slate-100">
            <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" class="w-full h-full object-contain p-1" width="48" height="48" loading="eager" decoding="async">
        </div>
    <?php elseif($fallback === 'gradient' && $isSm): ?>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center shadow-lg">
            <span class="text-white font-black text-lg">M</span>
        </div>
    <?php else: ?>
        <div class="<?php echo e($box); ?> rounded-xl bg-[#FB5607] flex items-center justify-center shadow-lg shadow-orange-500/25 group-hover:shadow-orange-500/40 transition-shadow">
            <span class="text-white font-black <?php echo e($mText); ?>">M</span>
        </div>
    <?php endif; ?>
    <span class="text-mx-indigo font-extrabold <?php echo e($brandText); ?>" <?php if($isSm): ?> style="font-family:Tajawal,sans-serif" <?php endif; ?>><?php echo e(config('app.name', 'Muallimx')); ?></span>
</a>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\partials\auth-brand-link.blade.php ENDPATH**/ ?>