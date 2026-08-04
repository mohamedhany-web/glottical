@extends('layouts.admin')

@section('title', 'سنوات المدرسة - Glottical')
@section('page_title', 'سنوات المدرسة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">المدرسة · Islamic Foundations</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">السنوات الدراسية</h2>
        </div>
        <a href="{{ route('admin.school-years.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
            <i class="fas fa-plus text-xs"></i> سنة جديدة
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><p class="text-xs text-muted">الإجمالي</p><p class="mt-1 text-xl font-semibold">{{ $stats['total'] }}</p></article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><p class="text-xs text-muted">نشطة</p><p class="mt-1 text-xl font-semibold">{{ $stats['active'] }}</p></article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><p class="text-xs text-muted">متوقفة</p><p class="mt-1 text-xl font-semibold">{{ $stats['inactive'] }}</p></article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><p class="text-xs text-muted">فصول مربوطة</p><p class="mt-1 text-xl font-semibold">{{ $stats['linked'] }}</p></article>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas-muted/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">المستوى</th>
                        <th class="px-4 py-3 text-start font-medium">الاسم</th>
                        <th class="px-4 py-3 text-start font-medium">الوصف المختصر</th>
                        <th class="px-4 py-3 text-start font-medium">فصول</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($years as $year)
                        <tr>
                            <td class="px-4 py-3 font-semibold tabular-nums">{{ $year->level_number }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-ink">{{ $year->name }}</div>
                                <div class="text-xs text-muted" dir="ltr">{{ $year->slug }}</div>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $year->tagline }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $year->groups_count }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $year->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-canvas-muted text-muted' }}">
                                    {{ $year->is_active ? 'نشطة' : 'متوقفة' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.school-years.edit', $year) }}" class="text-accent hover:underline">تعديل</a>
                                    <form method="POST" action="{{ route('admin.school-years.toggle-status', $year) }}">@csrf<button class="text-ink-soft hover:underline">تبديل</button></form>
                                    <form method="POST" action="{{ route('admin.school-years.destroy', $year) }}" onsubmit="return confirm('حذف السنة؟')">@csrf @method('DELETE')<button class="text-danger hover:underline">حذف</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-muted">لا توجد سنوات بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</div>
@endsection
