@php
    $totalLeft = $totalLeft ?? 0;
    $rechargeUrl = $rechargeUrl ?? route('dashboard');
@endphp
<div class="st-top__row st-top__row--secondary">
    <p class="st-top__count">
        {{ __('student_timeline.credits_total', ['count' => $totalLeft]) }}
    </p>
    <div class="st-top__chips">
        <a href="{{ $rechargeUrl }}" class="st-chip is-active">
            {{ __('student_timeline.recharge_package') }}
        </a>
    </div>
</div>
