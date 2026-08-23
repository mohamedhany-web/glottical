@extends('layouts.student-timeline')

@section('title', 'تفاصيل الباقة')
@section('page_title', 'تفاصيل الباقة')

@section('content')
<div class="mx-auto max-w-2xl space-y-5">
    <a href="{{ route('student.tutoring-subscriptions.index') }}" class="text-sm text-accent hover:underline">← رجوع</a>
    <article class="rounded-2xl border border-line bg-white p-5 shadow-soft">
        <h2 class="text-xl font-semibold">{{ $subscription->tutoringGroup?->title }}</h2>
        <p class="text-sm text-muted">{{ $subscription->package?->name }} · {{ $subscription->statusLabel() }}</p>
        <p class="mt-3 text-lg font-bold text-accent">{{ $subscription->sessionsLeft() }} حصة متبقية من {{ $subscription->sessions_total }}</p>
        @if($subscription->expires_at)
            <p class="text-xs text-muted">ينتهي: {{ $subscription->expires_at->format('Y-m-d') }}</p>
        @endif
    </article>

    @if($subscription->hasSessionsLeft() && $slots->isNotEmpty())
        <article class="rounded-2xl border border-line bg-white p-5 shadow-soft">
            <h3 class="font-semibold text-ink mb-3">احجز حصة من الباقة</h3>
            @if($errors->any())
                <div class="mb-3 rounded-xl bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('student.tutoring-bookings.from-subscription') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                <div class="max-h-56 space-y-2 overflow-y-auto">
                    @foreach($slots as $slot)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line px-3 py-2 text-sm hover:border-accent/40">
                            <input type="radio" name="starts_at" value="{{ $slot['starts_at'] }}" required>
                            {{ $slot['label'] }}
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-accent text-sm font-medium text-white">تأكيد الحجز + Live</button>
            </form>
        </article>
    @endif

    @if($subscription->bookings->isNotEmpty())
        <article class="rounded-2xl border border-line bg-white p-5 shadow-soft">
            <h3 class="mb-3 font-semibold">الحجوزات السابقة</h3>
            <ul class="space-y-2 text-sm">
                @foreach($subscription->bookings as $b)
                    <li class="flex justify-between border-b border-line py-2">
                        <span>{{ $b->starts_at?->format('Y-m-d H:i') }}</span>
                        <a href="{{ route('student.tutoring-bookings.show', $b) }}" class="text-accent">{{ $b->statusLabel() }}</a>
                    </li>
                @endforeach
            </ul>
        </article>
    @endif
</div>
@endsection
