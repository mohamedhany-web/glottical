<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'profile',
    'name' => '',
    'size' => 'md',
    'rounded' => 'full',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'profile',
    'name' => '',
    'size' => 'md',
    'rounded' => 'full',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $photoUrl = $profile->photo_url ?? null;
    $sizeMap = [
        'sm' => 'w-16 h-16',
        'md' => 'w-24 h-24',
        'lg' => 'w-36 h-36 sm:w-44 sm:h-44',
        'xl' => 'w-36 h-36 sm:w-44 sm:h-44 lg:w-52 lg:h-52',
        'cover' => 'w-full h-full',
    ];
    $iconMap = [
        'sm' => 'text-xl',
        'md' => 'text-3xl',
        'lg' => 'text-5xl',
        'xl' => 'text-5xl',
        'cover' => 'text-4xl',
    ];
    $sizeClass = $sizeMap[$size] ?? $sizeMap['md'];
    $iconClass = $iconMap[$size] ?? $iconMap['md'];
    $radiusClass = $rounded === '2xl' ? 'rounded-2xl' : 'rounded-full';
?>

<div <?php echo e($attributes->merge(['class' => "{$sizeClass} {$radiusClass} overflow-hidden bg-[#1a2d4d] relative"])); ?>>
    <?php if($photoUrl): ?>
        <img src="<?php echo e($photoUrl); ?>"
             alt=""
             class="w-full h-full object-cover"
             loading="lazy"
             decoding="async"
             onerror="this.style.display='none';this.nextElementSibling?.classList.remove('hidden');">
        <div class="hidden absolute inset-0 flex items-center justify-center bg-[#1a2d4d]">
            <i class="fas fa-user text-white/35 <?php echo e($iconClass); ?>"></i>
        </div>
    <?php else: ?>
        <div class="absolute inset-0 flex items-center justify-center">
            <i class="fas fa-user text-white/35 <?php echo e($iconClass); ?>"></i>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views/components/instructor-avatar.blade.php ENDPATH**/ ?>