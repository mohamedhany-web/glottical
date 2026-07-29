@extends('layouts.app')

@section('title', 'تفاصيل حجز')
@section('page_title', 'تفاصيل الحجز')

@section('content')
<div class="mx-auto max-w-2xl space-y-4">
    <a href="{{ route('instructor.tutoring-bookings.index') }}" class="text-sm text-accent">← رجوع</a>
    <article class="rounded-2xl border border-line bg-white p-5 shadow-soft">
        <h2 class="text-xl font-semibold">{{ $booking->tutoringGroup?->title }}</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-muted">الطالب</dt><dd>{{ $booking->contactName() }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">الموعد</dt><dd>{{ $booking->starts_at?->format('Y-m-d H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">الحالة</dt><dd>{{ $booking->statusLabel() }}</dd></div>
        </dl>
        @if($booking->classroomMeeting)
            <a href="{{ url('classroom/join/'.$booking->classroomMeeting->code) }}" class="mt-5 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent text-white text-sm font-medium">
                <i class="fas fa-video"></i> بدء / دخول Live
            </a>
        @endif
    </article>
</div>
@endsection
