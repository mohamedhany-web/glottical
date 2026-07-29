@extends('layouts.app')

@section('title', 'تفاصيل الدفعة')
@section('page_title', 'تفاصيل الدفعة')

@section('content')
<div class="space-y-5">
    <a href="{{ route('instructor.tutoring-cohorts.index') }}" class="text-sm text-accent">← رجوع</a>
    <article class="rounded-2xl border border-line bg-white p-5 shadow-soft">
        <h2 class="text-xl font-semibold">{{ $cohort->title }}</h2>
        <p class="text-sm text-muted">{{ $cohort->tutoringGroup?->title }} · {{ $cohort->statusLabel() }}</p>
        <p class="mt-2 text-sm">{{ $cohort->enrolled_count }} / {{ $cohort->capacity }} مسجّل</p>
        @if($cohort->whatsapp_group_url)
            <a href="{{ $cohort->whatsapp_group_url }}" target="_blank" class="mt-3 inline-flex text-sm text-emerald-600">مجموعة واتساب</a>
        @endif
    </article>
    <article class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <div class="border-b border-line px-4 py-3 font-semibold text-ink">الطلاب والحجوزات</div>
        <ul class="divide-y divide-line text-sm">
            @forelse($cohort->bookings as $b)
                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                    <span>{{ $b->contactName() }} · {{ $b->starts_at?->format('Y-m-d H:i') }}</span>
                    <span class="text-muted">{{ $b->statusLabel() }}</span>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-muted">لا حجوزات في هذه الدفعة.</li>
            @endforelse
        </ul>
    </article>
</div>
@endsection
