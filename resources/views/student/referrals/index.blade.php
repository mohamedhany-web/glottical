@extends('layouts.student-timeline')

@section('title', __('student.referrals_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $activeProgram = $activeProgram ?? null;
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student.referrals_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student.referrals_title'), 'url' => null],
    ],
])

<section class="st-event-card st-event-card--blue st-biz-banner st-class-hero">
    <img class="st-event-card__mask" src="{{ $eventMasks[1] }}" alt="" width="160" height="160">
    <div class="st-biz-banner__row">
        <div>
            <p class="st-event-card__kicker">{{ __('student.referrals_kicker') }}</p>
            <h3>{{ __('student.referrals_title') }}</h3>
            <p class="st-event-card__sub">{{ __('student.referrals_subtitle') }}</p>
        </div>
        <div class="st-biz-banner__actions">
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="st-pill st-pill--light">
                <i class="fab fa-whatsapp" aria-hidden="true"></i>
                {{ __('student.referrals_share_wa') }}
            </a>
        </div>
    </div>
</section>

<section class="st-stats" aria-label="{{ __('student.referrals_stats') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <p class="st-stat-card__label">{{ __('student.total_referrals') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total_referrals']) }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <p class="st-stat-card__label">{{ __('student.completed_referrals') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['completed_referrals']) }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <p class="st-stat-card__label">{{ __('student.pending_referrals') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['pending_referrals']) }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <p class="st-stat-card__label">{{ __('student.referrals_credits_earned') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total_credits']) }}</p>
        <p class="st-stat-card__hint">{{ __('student.referrals_credits_hint') }}</p>
    </article>
</section>

@if($activeProgram)
    <section class="st-panel" style="margin-bottom:16px">
        <div class="st-section-head">
            <div>
                <h2>{{ __('student.referrals_rules_title') }}</h2>
                <p>{{ $activeProgram->name }}</p>
            </div>
        </div>
        <ul class="st-teacher-list">
            @if($activeProgram->usesCredits())
                <li>{{ __('student.referrals_rule_referred', ['units' => (int) $activeProgram->referred_credit_units, 'scope' => $activeProgram->creditScopeLabel()]) }}</li>
                <li>{{ __('student.referrals_rule_referrer', ['units' => (int) $activeProgram->referrer_credit_units]) }}</li>
                <li>{{ __('student.referrals_rule_when') }}</li>
            @else
                <li>{{ __('student.referrals_rule_discount_legacy') }}</li>
            @endif
        </ul>
    </section>
@else
    <div class="st-flash st-flash--err" style="margin-bottom:16px">{{ __('student.referrals_no_program') }}</div>
@endif

<div class="st-teacher-layout">
    <div class="st-teacher-main">
        <section class="st-panel">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student.referrals_your_code') }}</h2>
                    <p>{{ __('student.referrals_code_hint') }}</p>
                </div>
            </div>
            <div class="st-feed-compose" style="gap:12px">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between">
                    <strong style="font-size:1.35rem;letter-spacing:.04em;color:#0B3D91" id="stRefCode">{{ $referralCode }}</strong>
                    <button type="button" class="st-pill st-pill--solid" onclick="stCopyRef('{{ $referralCode }}', 'code')">
                        <i class="fas fa-copy" aria-hidden="true"></i> {{ __('student.referrals_copy_code') }}
                    </button>
                </div>
                <label class="st-feed-compose__hint" for="stRefLink">{{ __('student.referrals_your_link') }}</label>
                <input id="stRefLink" type="text" readonly value="{{ $referralLink }}" class="st-feed-reply" style="width:100%;padding:12px 14px;border:1.5px solid #d7dde6;border-radius:14px;font:700 13px Cairo,Tajawal,sans-serif;direction:ltr">
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    <button type="button" class="st-pill st-pill--solid" onclick="stCopyRef(document.getElementById('stRefLink').value, 'link')">
                        <i class="fas fa-link" aria-hidden="true"></i> {{ __('student.referrals_copy_link') }}
                    </button>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="st-pill st-pill--outline">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i> {{ __('student.referrals_share_wa') }}
                    </a>
                </div>
            </div>
        </section>

        <section class="st-panel">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student.referrals_how_title') }}</h2>
                    <p>{{ __('student.referrals_how_hint') }}</p>
                </div>
            </div>
            <div class="st-teacher-courses">
                <article class="st-teacher-course">
                    <span class="st-learn-badge is-ok">1</span>
                    <strong>{{ __('student.referrals_step1_title') }}</strong>
                    <small>{{ __('student.referrals_step1_body') }}</small>
                </article>
                <article class="st-teacher-course">
                    <span class="st-learn-badge is-ok">2</span>
                    <strong>{{ __('student.referrals_step2_title') }}</strong>
                    <small>{{ __('student.referrals_step2_body') }}</small>
                </article>
                <article class="st-teacher-course">
                    <span class="st-learn-badge is-ok">3</span>
                    <strong>{{ __('student.referrals_step3_title') }}</strong>
                    <small>{{ __('student.referrals_step3_body') }}</small>
                </article>
            </div>
        </section>
    </div>

    <aside class="st-teacher-side">
        <section class="st-panel st-panel--side">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student.referrals_list_title') }}</h2>
                    <p>{{ __('student.referrals_list_hint') }}</p>
                </div>
            </div>
            @forelse($referrals as $referral)
                <div class="st-board__row" style="display:grid;grid-template-columns:1fr auto;gap:8px;padding:10px 0;border-bottom:1px solid var(--st-line)">
                    <div>
                        <strong style="font-size:13px">{{ $referral->referred->name ?? '—' }}</strong>
                        <p style="margin:2px 0 0;font-size:11px;color:var(--st-muted)">{{ $referral->created_at?->format('Y-m-d') }}</p>
                    </div>
                    <div style="text-align:end">
                        <span class="st-learn-badge {{ $referral->status === 'completed' ? 'is-ok' : '' }}">
                            @if($referral->status === 'completed') {{ __('student.referrals_status_done') }}
                            @elseif($referral->status === 'pending') {{ __('student.referrals_status_pending') }}
                            @else {{ __('student.referrals_status_cancelled') }}
                            @endif
                        </span>
                        <p style="margin:4px 0 0;font-size:11px;font-weight:800;color:#0B3D91">
                            +{{ (int) $referral->referrer_units_granted }} {{ __('student.referrals_units_short') }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="st-learn-note">{{ __('student.referrals_empty') }}</p>
            @endforelse
            <div style="margin-top:12px">{{ $referrals->links() }}</div>
        </section>
    </aside>
</div>

<div id="stRefToast" class="st-flash st-flash--ok" style="display:none;position:fixed;inset-inline-start:16px;bottom:16px;z-index:80;max-width:280px" role="status"></div>
<script>
function stCopyRef(text, kind) {
    var msg = kind === 'code'
        ? @json(__('student.referrals_copied_code'))
        : @json(__('student.referrals_copied_link'));
    var toast = document.getElementById('stRefToast');
    function show() {
        if (!toast) return;
        toast.textContent = msg;
        toast.style.display = 'block';
        clearTimeout(window._stRefT);
        window._stRefT = setTimeout(function () { toast.style.display = 'none'; }, 2800);
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(show).catch(show);
    } else { show(); }
}
</script>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>
<a href="{{ route('student.learn.index') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_learn') }}</h3>
    <p class="st-event-card__sub">{{ __('student.referrals_cta_learn') }}</p>
</a>
<a href="{{ route('student.service-entitlements.index') }}" class="st-event-card st-event-card--green">
    <h3>{{ __('student_timeline.nav_progress') }}</h3>
    <p class="st-event-card__sub">{{ __('student.referrals_cta_credits') }}</p>
</a>
<a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="st-event-card st-event-card--orange">
    <h3>{{ __('student.referrals_share_wa') }}</h3>
    <p class="st-event-card__sub">{{ __('student.referrals_share_hint') }}</p>
</a>
@endsection
