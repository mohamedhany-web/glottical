@extends('layouts.admin')

@section('title', 'اشتراك #'.$subscription->id)
@section('page_title', 'تفاصيل الاشتراك')

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <a href="{{ route('admin.tutoring-subscriptions.index') }}" class="text-sm text-accent">← رجوع</a>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <article class="rounded-2xl border border-line bg-surface p-5">
        <h2 class="text-xl font-semibold text-ink">{{ $subscription->tutoringGroup?->title }}</h2>
        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
            <div><dt class="text-muted">الطالب</dt><dd class="font-medium">{{ $subscription->user?->name }} · {{ $subscription->user?->email }}</dd></div>
            <div><dt class="text-muted">الباقة</dt><dd class="font-medium">{{ $subscription->package?->name ?: '—' }}</dd></div>
            <div><dt class="text-muted">الحصص</dt><dd class="font-medium">{{ $subscription->sessions_used }} مستخدمة / {{ $subscription->sessions_total }}</dd></div>
            <div><dt class="text-muted">الحالة</dt><dd class="font-medium">{{ $subscription->statusLabel() }}</dd></div>
            <div><dt class="text-muted">يبدأ</dt><dd>{{ $subscription->starts_at?->format('Y-m-d') ?: '—' }}</dd></div>
            <div><dt class="text-muted">ينتهي</dt><dd>{{ $subscription->expires_at?->format('Y-m-d') ?: '—' }}</dd></div>
        </dl>

        @if($subscription->entitlement)
            <div class="mt-4 rounded-xl border border-line bg-canvas-muted px-4 py-3 text-sm">
                <div class="font-semibold">الرصيد المرتبط #{{ $subscription->entitlement->id }}</div>
                <div class="mt-1 text-muted">
                    {{ $subscription->entitlement->units_used }} / {{ $subscription->entitlement->units_total }}
                    · {{ $subscription->entitlement->status }}
                </div>
                <form method="POST" action="{{ route('admin.tutoring-subscriptions.sync', $subscription) }}" class="mt-3">
                    @csrf
                    <button class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-4 text-xs font-medium text-white">مزامنة من الرصيد</button>
                </form>
            </div>
        @endif
    </article>

    <section class="rounded-2xl border border-line bg-surface p-5">
        <h3 class="font-semibold text-ink">الحجوزات المرتبطة</h3>
        <ul class="mt-3 divide-y divide-line text-sm">
            @forelse($subscription->bookings as $booking)
                <li class="flex flex-wrap items-center justify-between gap-2 py-3">
                    <div>
                        <div class="font-medium">{{ $booking->starts_at?->format('Y-m-d H:i') }}</div>
                        <div class="text-xs text-muted">{{ $booking->statusLabel() }}</div>
                    </div>
                    <a href="{{ route('admin.tutoring-group-bookings.show', $booking) }}" class="text-accent text-xs font-semibold">عرض الحجز</a>
                </li>
            @empty
                <li class="py-4 text-muted">لا حجوزات بعد.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
