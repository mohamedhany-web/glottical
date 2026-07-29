@extends('layouts.app')

@section('title', 'حجوزات المجموعات')
@section('page_title', 'حجوزات المجموعات')

@section('content')
<div class="space-y-5">
    @if($upcoming && $upcoming->classroomMeeting)
        <article class="rounded-2xl border border-accent/25 bg-gradient-to-l from-accent/10 to-white p-5 shadow-soft">
            <p class="text-xs font-bold uppercase tracking-wide text-accent">حصتك القادمة</p>
            <h3 class="mt-1 text-lg font-semibold text-ink">{{ $upcoming->tutoringGroup?->title }}</h3>
            <p class="text-sm text-muted">{{ $upcoming->starts_at?->format('Y-m-d H:i') }} · {{ $upcoming->instructor?->name }}</p>
            <a href="{{ url('classroom/join/'.$upcoming->classroomMeeting->code) }}" class="mt-3 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-video"></i> دخول Live
            </a>
        </article>
    @endif

    @if(session('success'))
        <div class="rounded-xl border border-line bg-white px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <table class="min-w-full text-sm">
            <thead class="border-b border-line bg-slate-50 text-xs font-semibold text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">المجموعة</th>
                    <th class="px-4 py-3 text-start">الموعد</th>
                    <th class="px-4 py-3 text-start">الحالة</th>
                    <th class="px-4 py-3 text-end">عرض</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium text-ink">{{ $booking->tutoringGroup?->title }}</td>
                        <td class="px-4 py-3 tabular-nums text-muted">{{ $booking->starts_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ $booking->statusLabel() }}</span></td>
                        <td class="px-4 py-3 text-end"><a href="{{ route('student.tutoring-bookings.show', $booking) }}" class="text-accent hover:underline">تفاصيل</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-muted">لا توجد حجوزات بعد. <a href="{{ route('public.groups') }}" class="text-accent">تصفّح المجموعات</a></td></tr>
                @endforelse
            </tbody>
        </table>
        @if($bookings->hasPages())
            <div class="border-t border-line px-4 py-3">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
