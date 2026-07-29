@extends('layouts.app')

@section('title', 'حجوزات المجموعات')
@section('page_title', 'حجوزات المجموعات')

@section('content')
<div class="space-y-5">
    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-2xl border border-line bg-white p-4 shadow-soft">
            <p class="text-xs text-muted">قادمة مؤكدة</p>
            <p class="text-2xl font-bold text-ink">{{ $stats['upcoming'] }}</p>
        </div>
        <div class="rounded-2xl border border-line bg-white p-4 shadow-soft">
            <p class="text-xs text-muted">قيد المراجعة</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <table class="min-w-full text-sm">
            <thead class="border-b border-line bg-slate-50 text-xs font-semibold text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">المجموعة</th>
                    <th class="px-4 py-3 text-start">الطالب</th>
                    <th class="px-4 py-3 text-start">الموعد</th>
                    <th class="px-4 py-3 text-start">الحالة</th>
                    <th class="px-4 py-3 text-end"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $booking->tutoringGroup?->title }}</td>
                        <td class="px-4 py-3">{{ $booking->contactName() }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $booking->starts_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3">{{ $booking->statusLabel() }}</td>
                        <td class="px-4 py-3 text-end"><a href="{{ route('instructor.tutoring-bookings.show', $booking) }}" class="text-accent">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-muted">لا حجوزات بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $bookings->links() }}
    </div>
</div>
@endsection
