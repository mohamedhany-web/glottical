@props([
    'course',
    'size' => 'default',
])
@php
    /** @var \App\Models\AdvancedCourse $course */
    $isMonthly = $course->isMonthlyBilling();
    $list = $isMonthly ? $course->monthlyListPrice() : (float) ($course->price ?? 0);
    $pay = $course->effectiveCheckoutPrice();
    $promo = $isMonthly
        ? ($course->monthly_price_after_discount !== null && $pay < $list && $list > 0)
        : $course->hasPromotionalPrice();
    $isFree = ($course->is_free ?? false) || ($pay <= 0 && $list <= 0);
    $small = $size === 'sm';
    $perMonth = __('public.per_month');
@endphp
@if($isFree)
    <span {{ $attributes->class(['inline-flex items-center gap-1 font-bold text-emerald-600', $small ? 'text-xs' : 'text-sm']) }}>
        <i class="fas fa-gift {{ $small ? 'text-[9px]' : 'text-[10px]' }}"></i>
        {{ __('public.free_price') }}
    </span>
@elseif($promo)
    <span {{ $attributes->class(['inline-flex flex-col items-end gap-0.5']) }}>
        <span class="{{ $small ? 'text-[10px]' : 'text-xs' }} text-slate-400 line-through tabular-nums">{{ number_format($list, 0) }} {{ __('public.currency_egp') }}@if($isMonthly)/{{ $perMonth }}@endif</span>
        <span class="{{ $small ? 'text-xs font-bold' : 'text-sm font-black' }} text-mx-orange tabular-nums">{{ number_format($pay, 0) }} {{ __('public.currency_egp') }}@if($isMonthly)/{{ $perMonth }}@endif</span>
    </span>
@else
    <span {{ $attributes->class(['font-bold text-mx-orange tabular-nums', $small ? 'text-xs' : 'text-sm']) }}>
        {{ number_format($pay, 0) }} {{ __('public.currency_egp') }}@if($isMonthly)/{{ $perMonth }}@endif
    </span>
@endif
