@extends('layouts.app')

@section('title', 'دفعاتي الجماعية')
@section('page_title', 'الدفعات الجماعية')

@section('content')
<div class="space-y-5">
    <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <table class="min-w-full text-sm">
            <thead class="border-b border-line bg-slate-50 text-xs font-semibold text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">الدفعة</th>
                    <th class="px-4 py-3 text-start">المجموعة</th>
                    <th class="px-4 py-3 text-start">البداية</th>
                    <th class="px-4 py-3 text-start">المقاعد</th>
                    <th class="px-4 py-3 text-end"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($cohorts as $cohort)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $cohort->title }}</td>
                        <td class="px-4 py-3">
                            {{ $cohort->tutoringGroup?->title }}
                            @if($cohort->tutoringGroup?->schoolYear)
                                <div class="text-xs text-muted">{{ $cohort->tutoringGroup->schoolYear->name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 tabular-nums">{{ $cohort->starts_at?->format('Y-m-d') ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $cohort->enrolled_count }}/{{ $cohort->capacity }}</td>
                        <td class="px-4 py-3 text-end"><a href="{{ route('instructor.tutoring-cohorts.show', $cohort) }}" class="text-accent">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-muted">لا توجد دفعات مرتبطة بك.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $cohorts->links() }}
    </div>
</div>
@endsection
