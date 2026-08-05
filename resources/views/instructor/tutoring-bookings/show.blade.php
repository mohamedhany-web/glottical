@extends('layouts.app')

@section('title', 'تفاصيل حجز')
@section('page_title', 'تفاصيل الحجز')

@section('content')
<div class="mx-auto max-w-2xl space-y-4">
    <a href="{{ route('instructor.tutoring-bookings.index') }}" class="text-sm text-accent">← رجوع</a>
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ session('error') }}</div>
    @endif
    <article class="rounded-2xl border border-line bg-white p-5 shadow-soft">
        <h2 class="text-xl font-semibold">{{ $booking->tutoringGroup?->title }}</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-muted">الطالب</dt><dd>{{ $booking->contactName() }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">الموعد</dt><dd>{{ $booking->starts_at?->format('Y-m-d H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">نهاية الحصة</dt><dd>{{ $booking->ends_at?->format('Y-m-d H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">الحالة</dt><dd>{{ $booking->statusLabel() }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">مصدر الحجز</dt><dd>{{ $booking->student_service_entitlement_id ? 'رصيد حصص' : 'اشتراك مباشر' }}</dd></div>
            @if($booking->student_notes)
                <div class="border-t border-line pt-3"><dt class="text-muted">ملاحظات الطالب</dt><dd class="mt-1">{{ $booking->student_notes }}</dd></div>
            @endif
        </dl>
        @if($booking->classroomMeeting)
            <a href="{{ url('classroom/join/'.$booking->classroomMeeting->code) }}" class="mt-5 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent text-white text-sm font-medium">
                <i class="fas fa-video"></i> بدء / دخول Live
            </a>
        @endif
        @if($booking->status === \App\Models\TutoringGroupBooking::STATUS_CONFIRMED)
            <form method="POST" action="{{ route('instructor.tutoring-bookings.complete', $booking) }}" class="mt-3" onsubmit="return confirm('تأكيد إكمال الحصة؟ سيتم خصم حصة واحدة من رصيد الطالب وإغلاق غرفة Live.');">
                @csrf
                <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 text-sm font-semibold text-emerald-800 hover:bg-emerald-100">
                    <i class="fas fa-circle-check"></i> إنهاء الحصة وخصم الرصيد
                </button>
            </form>
            <p class="mt-2 text-center text-xs text-muted">لا تستخدم هذا الزر إلا بعد انتهاء الحصة فعلياً. العملية محمية من الخصم المتكرر.</p>
        @elseif($booking->status === \App\Models\TutoringGroupBooking::STATUS_COMPLETED)
            <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-800">
                <i class="fas fa-check-circle"></i> أُكملت الحصة وتمت تسوية الرصيد
            </div>
        @endif
    </article>
</div>
@endsection
