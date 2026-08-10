@extends('layouts.app')

@section('title', 'مركز قيادة الفصول')
@section('page_title', 'مركز قيادة الفصول')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="space-y-5">
    <section class="rounded-2xl border border-line bg-gradient-to-l from-[#0B3D91] to-[#072A66] p-5 text-white shadow-soft">
        <p class="text-xs font-bold uppercase tracking-wide text-white/70">Command Center</p>
        <h2 class="mt-1 text-2xl font-black">
            {{ $isRtl ? 'صباح الخير' : 'Hello' }}, {{ $overview['instructor_name'] ?? auth()->user()->name }}
        </h2>
        <p class="mt-1 text-sm font-semibold text-white/85">
            {{ $isRtl ? 'نظرة سريعة على دفعاتك الجماعية داخل المدرسة.' : 'A quick look at your school cohorts.' }}
        </p>
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl bg-white/10 px-3 py-3 ring-1 ring-white/15">
                <p class="text-[11px] font-bold text-white/70">{{ $isRtl ? 'الدفعات' : 'Cohorts' }}</p>
                <p class="text-xl font-black tabular-nums">{{ $overview['cohorts_count'] ?? $cohorts->total() }}</p>
            </div>
            <div class="rounded-xl bg-white/10 px-3 py-3 ring-1 ring-white/15">
                <p class="text-[11px] font-bold text-white/70">{{ $isRtl ? 'الطلاب' : 'Students' }}</p>
                <p class="text-xl font-black tabular-nums">{{ $overview['students_count'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-white/10 px-3 py-3 ring-1 ring-white/15">
                <p class="text-[11px] font-bold text-white/70">{{ $isRtl ? 'حصص اليوم' : 'Sessions today' }}</p>
                <p class="text-xl font-black tabular-nums">{{ $overview['sessions_today'] ?? 0 }}</p>
            </div>
        </div>
    </section>

    <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <table class="min-w-full text-sm">
            <thead class="border-b border-line bg-slate-50 text-xs font-semibold text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">{{ $isRtl ? 'الدفعة / الفصل' : 'Cohort / class' }}</th>
                    <th class="px-4 py-3 text-start">{{ $isRtl ? 'المجموعة' : 'Group' }}</th>
                    <th class="px-4 py-3 text-start">{{ $isRtl ? 'الطلاب' : 'Students' }}</th>
                    <th class="px-4 py-3 text-start">{{ $isRtl ? 'البداية' : 'Starts' }}</th>
                    <th class="px-4 py-3 text-end"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($cohorts as $cohort)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-ink">{{ $cohort->title }}</td>
                        <td class="px-4 py-3">
                            {{ $cohort->tutoringGroup?->title }}
                            @if($cohort->tutoringGroup?->schoolYear)
                                <div class="text-xs text-muted">{{ $cohort->tutoringGroup->schoolYear->name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 tabular-nums">
                            {{ $cohort->students_count ?? $cohort->enrolled_count }}/{{ $cohort->capacity }}
                        </td>
                        <td class="px-4 py-3 tabular-nums">{{ $cohort->starts_at?->format('Y-m-d') ?: '—' }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('instructor.tutoring-cohorts.show', $cohort) }}"
                               class="inline-flex h-8 items-center rounded-lg bg-accent px-3 text-xs font-bold text-white">
                                {{ $isRtl ? 'Command Center' : 'Open' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-muted">{{ $isRtl ? 'لا توجد دفعات مرتبطة بك.' : 'No cohorts linked to you yet.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $cohorts->links() }}</div>
    </div>
</div>
@endsection
