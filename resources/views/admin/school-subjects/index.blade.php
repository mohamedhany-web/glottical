@extends('layouts.admin')

@section('title', 'مواد المدرسة - Glottical')
@section('page_title', 'مواد المدرسة')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">المدرسة · المنهج</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">المواد الدراسية</h2>
        </div>
        <a href="{{ route('admin.school-subjects.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
            <i class="fas fa-plus text-xs"></i> مادة جديدة
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
                        <th class="px-4 py-3 text-start font-medium">المادة</th>
                        <th class="px-4 py-3 text-start font-medium">الوصف</th>
                        <th class="px-4 py-3 text-start font-medium">فصول</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($subjects as $subject)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 font-semibold text-ink">
                                    <i class="fas {{ $subject->faIcon() }} text-accent"></i>
                                    {{ $subject->name }}
                                </div>
                                <div class="text-xs text-muted" dir="ltr">{{ $subject->slug }}</div>
                            </td>
                            <td class="px-4 py-3 text-muted max-w-sm">{{ $subject->description }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $subject->groups_count }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $subject->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-canvas-muted text-muted' }}">
                                    {{ $subject->is_active ? 'نشطة' : 'متوقفة' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.school-subjects.edit', $subject) }}" class="text-accent hover:underline">تعديل</a>
                                    <form method="POST" action="{{ route('admin.school-subjects.toggle-status', $subject) }}">@csrf<button class="text-ink-soft hover:underline">تبديل</button></form>
                                    <form method="POST" action="{{ route('admin.school-subjects.destroy', $subject) }}" onsubmit="return confirm('حذف المادة؟')">@csrf @method('DELETE')<button class="text-danger hover:underline">حذف</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-muted">لا توجد مواد بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</div>
@endsection
