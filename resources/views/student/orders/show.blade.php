@extends('layouts.student-timeline')

@section('title', __('student_timeline.order_details_title', ['id' => $order->id]))

@section('content')
@php
    $locale = app()->getLocale();
    $status = $order->status;
    $rechargeUrl = Route::has('public.service-packages.index')
        ? route('public.service-packages.index')
        : route('dashboard');

    $title = match (true) {
        $order->order_type === \App\Models\Order::TYPE_CUSTOM_SERVICE_PACKAGE
            => $order->custom_package_data['name'] ?? __('student_timeline.custom_package'),
        (bool) $order->servicePackage => $order->servicePackage->name,
        $order->academic_year_id && ! $order->advanced_course_id
            => $order->learningPath->name ?? __('student_timeline.legacy_order'),
        default => $order->course->title ?? __('student_timeline.course'),
    };

    $paymentLabel = match ($order->payment_method) {
        'bank_transfer' => __('student_timeline.pay_bank'),
        'cash' => __('student_timeline.pay_cash'),
        'online', 'fawaterak' => __('student_timeline.pay_online'),
        default => __('student_timeline.pay_other'),
    };

    $statusHint = match ($status) {
        \App\Models\Order::STATUS_PENDING => __('student_timeline.order_status_pending_hint'),
        \App\Models\Order::STATUS_APPROVED => __('student_timeline.order_status_approved_hint'),
        default => __('student_timeline.order_status_rejected_hint'),
    };

    $heroKicker = match ($status) {
        \App\Models\Order::STATUS_PENDING => __('student_timeline.order_pending'),
        \App\Models\Order::STATUS_APPROVED => __('student_timeline.order_approved'),
        default => __('student_timeline.order_rejected'),
    };

    $proofUrl = null;
    if ($order->payment_proof) {
        $proofUrl = storage_asset($order->payment_proof);
        if (! $proofUrl && Route::has('storage.file')) {
            try {
                $proofUrl = route('storage.file', ['path' => $order->payment_proof]);
            } catch (\Throwable $e) {
                $proofUrl = null;
            }
        }
    }

    $custom = $order->order_type === \App\Models\Order::TYPE_CUSTOM_SERVICE_PACKAGE
        ? ($order->custom_package_data ?? [])
        : [];
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.order_details_title', ['id' => $order->id]),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_orders'), 'url' => route('orders.index')],
        ['label' => '#'.$order->id, 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

<section class="st-join-hero {{ $status === \App\Models\Order::STATUS_APPROVED ? '' : 'st-join-hero--muted' }}" aria-label="{{ $order->status_text }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $heroKicker }} · #{{ $order->id }}</p>
        <h2 class="st-join-hero__title">{{ $title }}</h2>
        <p class="st-join-hero__meta">{{ $statusHint }}</p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('orders.index') }}" class="st-pill st-pill--outline">{{ __('student_timeline.back_to_orders') }}</a>
        @if($status === \App\Models\Order::STATUS_APPROVED && $order->course && Route::has('courses.show'))
            <a href="{{ route('courses.show', $order->course) }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.enter_course') }}</a>
        @elseif($status === \App\Models\Order::STATUS_APPROVED && $order->servicePackage && Route::has('student.service-entitlements.index'))
            <a href="{{ route('student.service-entitlements.index') }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.nav_progress') }}</a>
        @elseif($status === \App\Models\Order::STATUS_REJECTED)
            <a href="{{ $rechargeUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.recharge_package') }}</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.order_summary') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.order_amount') }}</p>
        <p class="st-stat-card__value">{{ number_format((float) $order->amount, 2) }}</p>
        <p class="st-stat-card__hint">{{ $order->currencyCode() }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.pay_method') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $paymentLabel }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.order_date') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $order->created_at?->format('d/m/Y') }}</p>
        <p class="st-stat-card__hint">{{ $order->created_at?->format('H:i') }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.order_status') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">
            <span class="st-order-card__badge is-{{ $status }}">{{ $order->status_text }}</span>
        </p>
    </article>
</section>

<div class="st-order-show">
    <div class="st-order-show__main">
        <section class="st-order-panel">
            <header class="st-order-panel__head">
                <h2>{{ __('student_timeline.order_item') }}</h2>
            </header>
            <div class="st-order-panel__body">
                <div class="st-order-item">
                    <div class="st-order-item__media" aria-hidden="true">
                        @if($order->course?->thumbnail)
                            <img src="{{ storage_asset($order->course->thumbnail) }}" alt="" loading="lazy">
                        @else
                            <i class="fas fa-box-open"></i>
                        @endif
                    </div>
                    <div class="st-order-item__copy">
                        <h3>{{ $title }}</h3>

                        @if(! empty($custom))
                            <div class="st-order-facts">
                                <div><span>{{ __('student_timeline.learn_units_left') }}</span><strong>{{ $custom['sessions'] ?? '—' }}</strong></div>
                                <div><span>{{ __('student_timeline.order_session_minutes') }}</span><strong>{{ $custom['session_minutes'] ?? '—' }}</strong></div>
                                <div><span>{{ __('student_timeline.order_validity_days') }}</span><strong>{{ $custom['duration_days'] ?? '—' }}</strong></div>
                                <div><span>{{ __('student_timeline.order_discount') }}</span><strong>{{ ($custom['discount_percent'] ?? 0) }}%</strong></div>
                            </div>
                        @elseif($order->servicePackage)
                            <p>{{ $order->servicePackage->units_count }} {{ __('student_timeline.session_unit') }}
                                · {{ $order->servicePackage->sessionMinutes() }} {{ __('student_timeline.order_minutes_short') }}
                                · {{ $order->servicePackage->validityLabel() }}</p>
                        @elseif($order->course)
                            <p>
                                {{ collect([$order->course->academicYear?->name, $order->course->academicSubject?->name])->filter()->implode(' · ') }}
                            </p>
                            @if($order->course->description)
                                <p class="st-order-item__desc">{{ \Illuminate\Support\Str::limit(strip_tags($order->course->description), 140) }}</p>
                            @endif
                            @if(Route::has('courses.show'))
                                <a href="{{ route('courses.show', $order->course) }}" class="st-pill st-pill--outline">{{ __('student_timeline.view_course') }}</a>
                            @endif
                        @elseif($order->academic_year_id && ! $order->advanced_course_id)
                            <p>{{ __('student_timeline.legacy_order') }}</p>
                        @else
                            <p>{{ __('student_timeline.order_item_unknown') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="st-order-panel">
            <header class="st-order-panel__head">
                <h2>{{ __('student_timeline.order_payment_details') }}</h2>
            </header>
            <div class="st-order-panel__body">
                <div class="st-order-facts st-order-facts--wide">
                    <div>
                        <span>{{ __('student_timeline.order_amount') }}</span>
                        <strong>{{ number_format((float) $order->amount, 2) }} {{ $order->currencyCode() }}</strong>
                    </div>
                    @if((float) ($order->original_amount ?? 0) > (float) $order->amount)
                        <div>
                            <span>{{ __('student_timeline.order_original_amount') }}</span>
                            <strong>{{ number_format((float) $order->original_amount, 2) }} {{ $order->currencyCode() }}</strong>
                        </div>
                    @endif
                    @if((float) ($order->discount_amount ?? 0) > 0)
                        <div>
                            <span>{{ __('student_timeline.order_discount') }}</span>
                            <strong>−{{ number_format((float) $order->discount_amount, 2) }} {{ $order->currencyCode() }}</strong>
                        </div>
                    @endif
                    <div>
                        <span>{{ __('student_timeline.pay_method') }}</span>
                        <strong>{{ $paymentLabel }}</strong>
                    </div>
                    <div>
                        <span>{{ __('student_timeline.order_date') }}</span>
                        <strong>{{ $order->created_at?->format('d/m/Y H:i') }}</strong>
                    </div>
                    @if($order->approved_at)
                        <div>
                            <span>{{ __('student_timeline.approved_date') }}</span>
                            <strong>{{ $order->approved_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif
                </div>

                @if($order->notes)
                    <div class="st-order-notes">
                        <strong>{{ __('student_timeline.order_notes') }}</strong>
                        <p>{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </section>

        @if($proofUrl)
            <section class="st-order-panel">
                <header class="st-order-panel__head">
                    <h2>{{ __('student_timeline.order_receipt') }}</h2>
                </header>
                <div class="st-order-panel__body">
                    <button type="button" class="st-order-receipt" data-st-lightbox="{{ $proofUrl }}" aria-label="{{ __('student_timeline.order_receipt_open') }}">
                        <img src="{{ $proofUrl }}" alt="{{ __('student_timeline.order_receipt') }}" loading="lazy">
                    </button>
                    <p class="st-order-receipt__hint">{{ __('student_timeline.order_receipt_hint') }}</p>
                </div>
            </section>
        @endif
    </div>

    <aside class="st-order-show__side">
        <section class="st-order-panel st-order-panel--status is-{{ $status }}">
            <header class="st-order-panel__head">
                <h2>{{ __('student_timeline.order_status') }}</h2>
            </header>
            <div class="st-order-panel__body st-order-status">
                <span class="st-order-status__icon" aria-hidden="true">
                    @if($status === \App\Models\Order::STATUS_PENDING)
                        <i class="fas fa-clock"></i>
                    @elseif($status === \App\Models\Order::STATUS_APPROVED)
                        <i class="fas fa-check"></i>
                    @else
                        <i class="fas fa-times"></i>
                    @endif
                </span>
                <strong>{{ $order->status_text }}</strong>
                <p>{{ $statusHint }}</p>

                @if($status === \App\Models\Order::STATUS_APPROVED && $order->invoice && Route::has('student.invoices.show'))
                    <div class="st-order-invoice">
                        <span>{{ __('student_timeline.order_invoice') }}</span>
                        <strong dir="ltr">{{ $order->invoice->invoice_number }}</strong>
                        <a href="{{ route('student.invoices.show', $order->invoice) }}" class="st-pill st-pill--solid">{{ __('student_timeline.view_invoice') }}</a>
                    </div>
                @endif

                @if($order->approver)
                    <div class="st-order-reviewer">
                        <span>{{ __('student_timeline.order_reviewed_by') }}</span>
                        <strong>{{ $order->approver->name }}</strong>
                        @if($order->approved_at)
                            <small>{{ $order->approved_at->format('d/m/Y H:i') }}</small>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    </aside>
</div>

<div id="stOrderLightbox" class="st-lightbox" hidden>
    <button type="button" class="st-lightbox__close" aria-label="{{ __('student_timeline.close') }}">&times;</button>
    <img src="" alt="">
</div>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ route('orders.index') }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_orders') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.back_to_orders') }}</p>
</a>

<a href="{{ $rechargeUrl }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.recharge_package') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.recharge_hint') }}</p>
</a>

@if(Route::has('student.service-entitlements.index'))
    <a href="{{ route('student.service-entitlements.index') }}" class="st-event-card st-event-card--pink">
        <h3>{{ __('student_timeline.nav_progress') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.credits_use_hint') }}</p>
    </a>
@endif
@endsection

@push('scripts')
<script>
(function () {
  var box = document.getElementById('stOrderLightbox');
  if (!box) return;
  var img = box.querySelector('img');
  var closeBtn = box.querySelector('.st-lightbox__close');

  function open(src) {
    img.src = src;
    box.hidden = false;
    document.body.style.overflow = 'hidden';
  }
  function close() {
    box.hidden = true;
    img.src = '';
    document.body.style.overflow = '';
  }

  document.querySelectorAll('[data-st-lightbox]').forEach(function (el) {
    el.addEventListener('click', function () {
      open(el.getAttribute('data-st-lightbox'));
    });
  });
  box.addEventListener('click', function (e) {
    if (e.target === box) close();
  });
  if (closeBtn) closeBtn.addEventListener('click', close);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !box.hidden) close();
  });
})();
</script>
@endpush
