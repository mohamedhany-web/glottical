@extends('layouts.admin')

@section('title', 'المناهج')
@section('page_title', 'المناهج')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.index') }}" class="hover:text-accent">المكتبات</a> · مناهج</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">المناهج الدراسية</h2>
            <p class="mt-1 text-sm text-muted">السنوات والمواد والكورسات المرتبطة — نفس هيكل واجهة الطالب.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.academic-years.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-3 text-sm">إدارة السنوات</a>
            <a href="{{ route('admin.academic-subjects.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-3 text-sm">المواد</a>
            <a href="{{ route('admin.advanced-courses.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-3 text-sm font-medium text-white">الكورسات</a>
        </div>
    </section>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">سنوات</div><div class="mt-1 text-2xl font-semibold">{{ $stats['years'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">مواد</div><div class="mt-1 text-2xl font-semibold">{{ $stats['subjects'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">كورسات</div><div class="mt-1 text-2xl font-semibold">{{ $stats['courses'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">أقسام</div><div class="mt-1 text-2xl font-semibold">{{ $stats['sections'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">عناصر منهج</div><div class="mt-1 text-2xl font-semibold">{{ $stats['items'] }}</div></div>
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-line bg-surface p-4 shadow-soft md:grid-cols-4">
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
        <button class="btn-press h-10 rounded-xl bg-ink px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    @forelse($years as $year)
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line bg-canvas-muted/40 px-4 py-3">
                <h3 class="text-base font-semibold text-ink">{{ $year->name }}</h3>
                <span class="text-xs text-muted">{{ $year->subjects_count ?? $year->subjects->count() }} مادة</span>
            </div>
            <div class="divide-y divide-line">
                @forelse($year->subjects as $subject)
                    <div class="px-4 py-3">
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <div class="font-medium text-ink">{{ $subject->name }}</div>
                            <span class="text-xs text-muted">{{ $subject->courses->count() }} كورس</span>
                        </div>
                        @if($subject->courses->isEmpty())
                            <p class="text-xs text-muted">لا كورسات مرتبطة.</p>
                        @else
                            <ul class="flex flex-wrap gap-2">
                                @foreach($subject->courses as $course)
                                    <li>
                                        <a href="{{ route('admin.libraries.curriculum.course', $course) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-line bg-canvas px-3 py-1.5 text-xs font-medium text-ink hover:border-accent hover:text-accent">
                                            {{ $course->title }}
                                            <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-muted">لا مواد في هذه السنة.</div>
                @endforelse
            </div>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-line px-4 py-12 text-center text-muted">
            لا سنوات دراسية بعد. ابدأ من <a href="{{ route('admin.academic-years.index') }}" class="text-accent hover:underline">إدارة السنوات</a>.
        </div>
    @endforelse

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-3 font-semibold text-ink">كورسات (جدول سريع)</div>
        <table class="min-w-full text-sm">
            <thead class="bg-canvas-muted text-xs text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">الكورس</th>
                    <th class="px-4 py-3 text-start">المادة / السنة</th>
                    <th class="px-4 py-3 text-start">أقسام</th>
                    <th class="px-4 py-3 text-start">محاضرات</th>
                    <th class="px-4 py-3 text-start"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr class="border-t border-line">
                        <td class="px-4 py-3 font-medium">{{ $course->title }}</td>
                        <td class="px-4 py-3 text-muted text-xs">
                            {{ $course->academicSubject?->name ?: '—' }}
                            @if($course->academicSubject?->academicYear)
                                · {{ $course->academicSubject->academicYear->name }}
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $course->sections_count }}</td>
                        <td class="px-4 py-3">{{ $course->lectures_count }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.libraries.curriculum.course', $course) }}" class="text-accent hover:underline">المنهج</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-muted">لا كورسات.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-line px-4 py-3">{{ $courses->links() }}</div>
    </article>
</div>
@endsection
