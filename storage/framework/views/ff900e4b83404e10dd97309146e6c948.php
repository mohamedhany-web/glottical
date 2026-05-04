<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'info']));

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

foreach (array_filter((['type' => 'info']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $map = [
        'success' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-900'],
        'error' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-900'],
        'warning' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-900'],
        'info' => ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-900'],
    ];
    $c = $map[$type] ?? $map['info'];
?>

<div <?php echo e($attributes->merge(['class' => "rounded-xl {$c['bg']} border {$c['border']} {$c['text']} px-4 py-3 text-sm"])); ?>>
    <?php echo e($slot); ?>

</div>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\components\alert.blade.php ENDPATH**/ ?>