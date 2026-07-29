@extends('layouts.app')

@section('title', 'تفاصيل الحجز')
@section('page_title', 'تفاصيل الحجز')

@section('content')
<div class="mx-auto max-w-2xl space-y-5">
    <a href="{{ route('student.tutoring-bookings.index') }}" class="text-sm text-accent hover:underline">← رجوع</a>
    <article class="rounded-2xl border border-line bg-white p-5 shadow-soft">
        <h2 class="text-xl font-semibold text-ink">{{ $booking->tutoringGroup?->title }}</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-muted">الموعد</dt><dd class="font-medium">{{ $booking->starts_at?->format('Y-m-d H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">المدرب</dt><dd class="font-medium">{{ $booking->instructor?->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">الحالة</dt><dd class="font-medium text-accent">{{ $booking->statusLabel() }}</dd></div>
            @if($booking->cohort)
                <div class="flex justify-between"><dt class="text-muted">الدفعة</dt><dd class="font-medium">{{ $booking->cohort->title }}</dd></div>
            @endif
        </dl>
        @if($booking->classroomMeeting)
            <a href="{{ url('classroom/join/'.$booking->classroomMeeting->code) }}" class="mt-5 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-medium text-white">
                <i class="fas fa-video"></i> دخول غرفة Live
            </a>
            <p class="mt-2 text-center text-xs text-muted font-mono" dir="ltr">Code: {{ $booking->classroomMeeting->code }}</p>
        @elseif($booking->status === 'pending')
            <p class="mt-4 rounded-xl bg-amber-50 px-3 py-2 text-sm text-amber-800">الحجز قيد المراجعة — سيُفعَّل رابط Live بعد التأكيد.</p>
        @endif
    </article>
</div>
@endsection
