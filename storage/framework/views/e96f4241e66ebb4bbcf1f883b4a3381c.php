<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'planKey',
    'plan',
    'highlighted' => false,
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
    'planKey',
    'plan',
    'highlighted' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isRtl = app()->getLocale() === 'ar';
    $label = $plan['label'] ?? $planKey;
    $price = (float) ($plan['price'] ?? 0);
    $cycle = $plan['billing_cycle'] ?? 'monthly';
    $cycleLabel = match ($cycle) {
        'quarterly' => __('public.pricing_billing_quarterly'),
        'yearly' => __('public.pricing_billing_yearly'),
        default => __('public.pricing_billing_monthly'),
    };
    $features = is_array($plan['features'] ?? null) ? $plan['features'] : [];
    $featureDescriptions = is_array($plan['feature_descriptions'] ?? null) ? $plan['feature_descriptions'] : [];
    $limits = is_array($plan['limits'] ?? null) ? $plan['limits'] : [];
    $meetings = (int) ($limits['classroom_meetings_per_month'] ?? 0);
    $featuredDays = (int) ($limits['personal_marketing_monthly_featured_days'] ?? 0);
    $priority = (int) ($limits['personal_marketing_priority_score'] ?? 0);
    $checkoutUrl = route('public.subscription.checkout', $planKey);
    $ctaUrl = auth()->check() ? $checkoutUrl : route('login', ['intended' => $checkoutUrl]);
    $badge = trim((string) ($plan['card_badge'] ?? ''));
    $subtitle = trim((string) ($plan['card_subtitle'] ?? ''));
    $priceHint = trim((string) ($plan['card_price_hint'] ?? ''));
    $cta = trim((string) ($plan['card_cta'] ?? '')) ?: __('public.pricing_cta_default');
    $footerNote = trim((string) ($plan['card_footer_note'] ?? ''));
?>

<article class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'reveal rounded-2xl p-7 sm:p-8 flex flex-col relative border transition-all duration-300',
    'border-2 border-acad-yellow glass-panel pricing-pop md:-translate-y-2 shadow-[0_0_48px_-12px_rgba(245,184,0,.35)]' => $highlighted,
    'border-white/10 glass-panel hover:border-acad-cyan/35' => ! $highlighted,
]); ?>">
    <?php if($badge !== ''): ?>
        <span class="absolute top-4 <?php echo e($isRtl ? 'left' : 'right'); ?>-4 text-[11px] font-black uppercase tracking-wide px-2.5 py-1 rounded-md bg-acad-yellow text-acad-blue z-10">
            <?php echo e($badge); ?>

        </span>
    <?php endif; ?>

    <header class="mb-5 <?php echo e($badge !== '' ? 'pt-2' : ''); ?>">
        <h3 class="<?php echo \Illuminate\Support\Arr::toCssClasses(['text-2xl font-black mb-1', 'text-white' => $highlighted, 'text-acad-yellow' => ! $highlighted]); ?>"><?php echo e($label); ?></h3>
        <?php if($subtitle !== ''): ?>
            <p class="text-sm font-semibold text-acad-cyan/90 leading-relaxed"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </header>

    <div class="mb-5">
        <p class="<?php echo \Illuminate\Support\Arr::toCssClasses(['text-4xl font-black tabular-nums', 'text-acad-yellow' => $highlighted, 'text-white' => ! $highlighted]); ?>">
            <?php echo e(number_format($price, 0)); ?>

            <span class="text-base sm:text-lg font-bold text-white/50"><?php echo e(__('public.currency_egp')); ?></span>
        </p>
        <p class="text-sm font-bold text-white/45 mt-1"><?php echo e($cycleLabel); ?></p>
        <?php if($priceHint !== ''): ?>
            <p class="text-sm text-white/55 mt-2"><?php echo e($priceHint); ?></p>
        <?php endif; ?>
    </div>

    <?php if($meetings > 0 || $featuredDays > 0 || $priority > 0): ?>
        <div class="flex flex-wrap gap-2 mb-5">
            <?php if($meetings > 0): ?>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-acad-cyan/15 text-acad-cyan border border-acad-cyan/25">
                    <i class="fas fa-video text-[9px] <?php echo e($isRtl ? 'ml-1' : 'mr-1'); ?>"></i>
                    <?php if($meetings >= 9999): ?>
                        <?php echo e(__('public.pricing_limit_meetings_unlimited')); ?>

                    <?php else: ?>
                        <?php echo e(__('public.pricing_limit_meetings', ['count' => $meetings])); ?>

                    <?php endif; ?>
                </span>
            <?php endif; ?>
            <?php if($featuredDays > 0): ?>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-violet-500/15 text-violet-300 border border-violet-400/25">
                    <?php echo e(__('public.pricing_limit_featured_days', ['days' => $featuredDays])); ?>

                </span>
            <?php endif; ?>
            <?php if($priority > 0): ?>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-white/8 text-white/70 border border-white/10">
                    <?php echo e(__('public.pricing_limit_priority', ['score' => $priority])); ?>

                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <ul class="space-y-3 text-sm text-white/75 mb-6 flex-1">
        <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featureKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="flex items-start gap-2.5">
                <i class="fas fa-check-circle text-emerald-400 mt-0.5 shrink-0"></i>
                <div class="min-w-0">
                    <p class="font-semibold text-white/90"><?php echo e(__("student.subscription_feature.{$featureKey}")); ?></p>
                    <?php $desc = $featureDescriptions[$featureKey] ?? __("student.subscription_feature_desc.{$featureKey}"); ?>
                    <?php if(filled($desc)): ?>
                        <p class="text-xs text-white/50 mt-0.5 leading-relaxed"><?php echo e($desc); ?></p>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <?php if($footerNote !== ''): ?>
        <p class="mb-4 px-3 py-2 rounded-xl text-sm font-semibold text-acad-cyan bg-acad-cyan/10 border border-acad-cyan/20"><?php echo e($footerNote); ?></p>
    <?php endif; ?>

    <a href="<?php echo e($ctaUrl); ?>"
       class="<?php echo \Illuminate\Support\Arr::toCssClasses([
           'mt-auto w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-extrabold text-sm transition',
           'bg-acad-yellow text-acad-blue hover:brightness-110 shadow-lg' => $highlighted,
           'border-2 border-acad-yellow/60 text-acad-yellow hover:bg-acad-yellow hover:text-acad-blue' => ! $highlighted,
       ]); ?>">
        <?php echo e($cta); ?>

        <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i>
    </a>
</article>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\components\teacher-plan-card.blade.php ENDPATH**/ ?>