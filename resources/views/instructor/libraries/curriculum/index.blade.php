@extends('layouts.app')

@section('title', 'مكتبة المناهج')
@section('header', 'مكتبة المناهج')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-800 focus:border-[#0B3D91] focus:outline-none focus:ring-2 focus:ring-[#0B3D91]/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">لوحة المعلم · محتوى تعليمي للعرض فقط</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">مكتبة المناهج</h2>
            <p class="mt-1 max-w-2xl text-sm text-slate-500">
                السنوات والمواد والكورسات — مرجع للمعلمين المعتمدين. التسجيل وحده لا يفتح هذه الصفحة.
            </p>
        </div>
    </section>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        <i class="fas fa-info-circle ml-1"></i>
        هذه المكتبة للتصفح والاطلاع فقط. بناء منهج كورسك الخاص يتم من صفحة الكورس إن وُجدت.
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">سنوات</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $stats['years'] }}</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">مواد</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $stats['subjects'] }}</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">كورسات</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $stats['courses'] }}</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">أقسام</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $stats['sections'] }}</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">عناصر منهج</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $stats['items'] }}</div></div>
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث مادة أو كورس" class="{{ $field }}">
        <select name="year_id" class="{{ $field }}">
            <option value="">كل السنوات</option>
            @foreach($years as $y)
                <option value="{{ $y->id }}" @selected((string) request('year_id') === (string) $y->id)>{{ $y->name }}</option>
            @endforeach
        </select>
        <select name="subject_id" class="{{ $field }}">
            <option value="">كل المواد</option>
            @foreach($subjects as $subj)
                <option value="{{ $subj->id }}" @selected((string) request('subject_id') === (string) $subj->id)>{{ $subj->name }}</option>
            @endforeach
        </select>
        <button class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0B3D91] px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    @forelse($years as $year)
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-4 py-3">
                <h3 class="text-base font-semibold text-slate-900">{{ $year->name }}</h3>
                <span class="text-xs text-slate-500">{{ $year->subjects_count ?? $year->subjects->count() }} مادة</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($year->subjects as $subject)
                    <div class="px-4 py-3">
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <div class="font-medium text-slate-900">{{ $subject->name }}</div>
                            <span class="text-xs text-slate-500">{{ $subject->courses->count() }} كورس</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @forelse($subject->courses as $course)
                                <a href="{{ route('instructor.libraries.curriculum.course', $course) }}"
                                   class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-[#0B3D91] hover:border-[#0B3D91]/40 hover:bg-[#0B3D91]/5">
                                    {{ $course->title }}
                                </a>
                            @empty
                                <span class="text-xs text-slate-400">لا كورسات بعد</span>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-slate-500">لا مواد في هذه السنة.</div>
                @endforelse
            </div>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-12 text-center text-slate-500">
            لا مناهج منشورة بعد.
        </div>
    @endforelse

    @if($courses->total() > 0)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-3">
                <h3 class="font-semibold text-slate-900">كل الكورسات</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-start">الكورس</th>
                            <th class="px-4 py-3 text-start">السنة / المادة</th>
                            <th class="px-4 py-3 text-start">أقسام</th>
                            <th class="px-4 py-3 text-end">عرض</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($courses as $course)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $course->title }}</td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $course->academicSubject?->academicYear?->name ?? '—' }}
                                    · {{ $course->academicSubject?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 tabular-nums text-slate-600">{{ $course->sections_count }}</td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('instructor.libraries.curriculum.course', $course) }}" class="font-semibold text-[#0B3D91] hover:underline">المنهج</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">{{ $courses->links() }}</div>
        </section>
    @endif
</div>
@endsection
