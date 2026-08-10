@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_orders'))

@section('content')
@php
    $locale = app()->getLocale();
    $orders = $orders ?? collect();
    $searchQuery = $searchQuery ?? '';
    $filter = $filter ?? 'all';
    $counts = $counts ?? ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
    $tones = ['blue', 'pink', 'orange', 'purple'];
    $browseUrl = Route::has('academic-years')
        ? route('academic-years')
        : (Route::has('public.service-packages.index') ? route('public.service-packages.index') : route('dashboard'));
    $rechargeUrl = Route::has('public.service-packages.index')
        ? route('public.service-packages.index')
        : $browseUrl;

    $orderTitle = function ($order) {
        if ($order->order_type === \App\Models\Order::TYPE_CUSTOM_SERVICE_PACKAGE) {
            return $order->custom_package_data['name'] ?? __('student_timeline.custom_package');
        }
        if ($order->servicePackage) {
            return $order->servicePackage->name;
        }
        if ($order->academic_year_id && ! $order->advanced_course_id) {
            return $order->learningPath->name ?? __('student_timeline.legacy_order');
        }

        return $order->course->title ?? __('student_timeline.course');
    };

    $orderMeta = function ($order) {
        if ($order->order_type === \App\Models\Order::TYPE_CUSTOM_SERVICE_PACKAGE) {
            $sessions = $order->custom_package_data['sessions'] ?? '—';

            return __('student_timeline.custom_sessions', ['count' => $sessions]);
        }
        if ($order->servicePackage) {
            return $order->servicePackage->units_count.' '.__('student_timeline.session_unit');
        }
        if ($order->academic_year_id && ! $order->advanced_course_id) {
            return __('student_timeline.legacy_order');
        }

        return collect([
            $order->course?->academicYear?->name,
            $order->course?->academicSubject?->name,
        ])->filter()->implode(' · ');
    };

    $paymentLabel = function ($order) {
        return match ($order->payment_method) {
            'bank_transfer' => __('student_timeline.pay_bank'),
            'cash' => __('student_timeline.pay_cash'),
            'online', 'fawaterak' => __('student_timeline.pay_online'),
            default => __('student_timeline.pay_other'),
        };
    };
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_orders'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_orders'), 'url' => null],
    ],
    'toolbarView' => 'student.orders._toolbar',
    'toolbarData' => [
        'searchQuery' => $searchQuery,
        'filter' => $filter,
        'counts' => $counts,
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

@if(($counts['pending'] ?? 0) > 0)
    <section class="st-join-hero st-join-hero--muted" aria-label="{{ __('student_timeline.order_pending') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.orders_pending_kicker') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.orders_pending_title', ['count' => $counts['pending']]) }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.orders_pending_hint') }}</p>
        </div>
        <div class="st-join-hero__actions">
            <a href="{{ route('orders.index', ['filter' => 'pending']) }}" class="st-pill st-pill--solid st-pill--lg">
                {{ __('student_timeline.view_pending_orders') }}
            </a>
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.nav_orders') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.filter_all') }}</p>
        <p class="st-stat-card__value">{{ $counts['all'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.order_pending') }}</p>
        <p class="st-stat-card__value">{{ $counts['pending'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.order_approved') }}</p>
        <p class="st-stat-card__value">{{ $counts['approved'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.order_rejected') }}</p>
        <p class="st-stat-card__value">{{ $counts['rejected'] }}</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.orders_title') }}</h2>
        <p>{{ __('student_timeline.orders_hint') }}</p>
    </div>
    <a href="{{ $browseUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.browse_courses_short') }}</a>
</section>

<section class="st-order-list" aria-label="{{ __('student_timeline.orders_title') }}">
    @forelse($orders as $i => $order)
        @php
            $tone = $tones[$i % count($tones)];
            $status = $order->status;
            $title = $orderTitle($order);
            $meta = $orderMeta($order);
        @endphp
        <article class="st-order-card st-order-card--{{ $tone }}">
            <div class="st-order-card__main">
                <div class="st-order-card__copy">
                    <div class="st-order-card__badges">
                        <span class="st-order-card__badge is-{{ $status }}">{{ $order->status_text }}</span>
                        <span class="st-order-card__when">{{ $order->created_at?->diffForHumans() }}</span>
                    </div>
                    <h3>{{ $title }}</h3>
                    @if($meta !== '')
                        <p class="st-order-card__meta">{{ $meta }}</p>
                    @endif
                </div>
                <div class="st-order-card__amount">
                    <strong>{{ number_format((float) $order->amount, 2) }}</strong>
                    <span>{{ $order->currencyCode() }}</span>
                </div>
            </div>

            <div class="st-order-card__facts">
                <span>{{ __('student_timeline.pay_method') }}: {{ $paymentLabel($order) }}</span>
                <span>{{ __('student_timeline.order_date') }}: {{ $order->created_at?->format('d/m/Y') }}</span>
                @if($order->approved_at)
                    <span>{{ __('student_timeline.approved_date') }}: {{ $order->approved_at->format('d/m/Y') }}</span>
                @endif
            </div>

            @if($order->notes)
                <p class="st-order-card__notes">{{ $order->notes }}</p>
            @endif

            <div class="st-order-card__foot">
                <a href="{{ route('orders.show', $order) }}" class="st-pill st-pill--solid">
                    {{ __('student_timeline.view_order') }}
                </a>
                @if($order->status === \App\Models\Order::STATUS_APPROVED && $order->course && Route::has('courses.show'))
                    <a href="{{ route('courses.show', $order->course) }}" class="st-pill st-pill--outline">
                        {{ __('student_timeline.enter_course') }}
                    </a>
                @elseif($order->status === \App\Models\Order::STATUS_APPROVED && $order->servicePackage && Route::has('student.service-entitlements.index'))
                    <a href="{{ route('student.service-entitlements.index') }}" class="st-pill st-pill--outline">
                        {{ __('student_timeline.nav_progress') }}
                    </a>
                @endif
            </div>
        </article>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.no_orders') }}</h3>
            <p>{{ __('student_timeline.no_orders_hint') }}</p>
            <div class="st-biz-banner__actions">
                <a href="{{ $browseUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.browse_courses_short') }}</a>
                <a href="{{ $rechargeUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.recharge_package') }}</a>
            </div>
        </div>
    @endforelse
</section>

@if(method_exists($orders, 'hasPages') && $orders->hasPages())
    <div class="st-pager">
        {{ $orders->links() }}
    </div>
@endif
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ $rechargeUrl }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.recharge_package') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.recharge_hint') }}</p>
</a>

<a href="{{ $browseUrl }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.browse_courses_short') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.browse_courses_hint') }}</p>
</a>

@if(Route::has('student.service-entitlements.index'))
    <a href="{{ route('student.service-entitlements.index') }}" class="st-event-card st-event-card--pink">
        <h3>{{ __('student_timeline.nav_progress') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.credits_use_hint') }}</p>
    </a>
@endif

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
