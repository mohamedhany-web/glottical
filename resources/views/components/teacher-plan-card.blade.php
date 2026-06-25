@props([
    'planKey',
    'plan',
    'highlighted' => false,
])

@php
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
@endphp

<article @class([
    'reveal rounded-2xl p-7 sm:p-8 flex flex-col relative border transition-all duration-300',
    'border-2 border-acad-yellow glass-panel pricing-pop md:-translate-y-2 shadow-[0_0_48px_-12px_rgba(245,184,0,.35)]' => $highlighted,
    'border-white/10 glass-panel hover:border-acad-cyan/35' => ! $highlighted,
])>
    @if($badge !== '')
        <span class="absolute top-4 {{ $isRtl ? 'left' : 'right' }}-4 text-[11px] font-black uppercase tracking-wide px-2.5 py-1 rounded-md bg-acad-yellow text-acad-blue z-10">
            {{ $badge }}
        </span>
    @endif

    <header class="mb-5 {{ $badge !== '' ? 'pt-2' : '' }}">
        <h3 @class(['text-2xl font-black mb-1', 'text-white' => $highlighted, 'text-acad-yellow' => ! $highlighted])>{{ $label }}</h3>
        @if($subtitle !== '')
            <p class="text-sm font-semibold text-acad-cyan/90 leading-relaxed">{{ $subtitle }}</p>
        @endif
    </header>

    <div class="mb-5">
        <p @class(['text-4xl font-black tabular-nums', 'text-acad-yellow' => $highlighted, 'text-white' => ! $highlighted])>
            {{ number_format($price, 0) }}
            <span class="text-base sm:text-lg font-bold text-white/50">{{ __('public.currency_egp') }}</span>
        </p>
        <p class="text-sm font-bold text-white/45 mt-1">{{ $cycleLabel }}</p>
        @if($priceHint !== '')
            <p class="text-sm text-white/55 mt-2">{{ $priceHint }}</p>
        @endif
    </div>

    @if($meetings > 0 || $featuredDays > 0 || $priority > 0)
        <div class="flex flex-wrap gap-2 mb-5">
            @if($meetings > 0)
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-acad-cyan/15 text-acad-cyan border border-acad-cyan/25">
                    <i class="fas fa-video text-[9px] {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                    @if($meetings >= 9999)
                        {{ __('public.pricing_limit_meetings_unlimited') }}
                    @else
                        {{ __('public.pricing_limit_meetings', ['count' => $meetings]) }}
                    @endif
                </span>
            @endif
            @if($featuredDays > 0)
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-violet-500/15 text-violet-300 border border-violet-400/25">
                    {{ __('public.pricing_limit_featured_days', ['days' => $featuredDays]) }}
                </span>
            @endif
            @if($priority > 0)
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-white/8 text-white/70 border border-white/10">
                    {{ __('public.pricing_limit_priority', ['score' => $priority]) }}
                </span>
            @endif
        </div>
    @endif

    <ul class="space-y-3 text-sm text-white/75 mb-6 flex-1">
        @foreach($features as $featureKey)
            <li class="flex items-start gap-2.5">
                <i class="fas fa-check-circle text-emerald-400 mt-0.5 shrink-0"></i>
                <div class="min-w-0">
                    <p class="font-semibold text-white/90">{{ __("student.subscription_feature.{$featureKey}") }}</p>
                    @php $desc = $featureDescriptions[$featureKey] ?? __("student.subscription_feature_desc.{$featureKey}"); @endphp
                    @if(filled($desc))
                        <p class="text-xs text-white/50 mt-0.5 leading-relaxed">{{ $desc }}</p>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>

    @if($footerNote !== '')
        <p class="mb-4 px-3 py-2 rounded-xl text-sm font-semibold text-acad-cyan bg-acad-cyan/10 border border-acad-cyan/20">{{ $footerNote }}</p>
    @endif

    <a href="{{ $ctaUrl }}"
       @class([
           'mt-auto w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-extrabold text-sm transition',
           'bg-acad-yellow text-acad-blue hover:brightness-110 shadow-lg' => $highlighted,
           'border-2 border-acad-yellow/60 text-acad-yellow hover:bg-acad-yellow hover:text-acad-blue' => ! $highlighted,
       ])>
        {{ $cta }}
        <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-xs"></i>
    </a>
</article>
