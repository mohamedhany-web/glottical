@extends('layouts.app')

@section('title', 'منهج: '.$course->title)
@section('header', 'منهج الكورس')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">
                <a href="{{ route('instructor.libraries.curriculum.index') }}" class="hover:text-[#0B3D91]">مكتبة المناهج</a>
                · {{ $course->academicSubject?->academicYear?->name }}
                · {{ $course->academicSubject?->name }}
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $course->title }}</h2>
            <p class="mt-1 text-sm text-slate-500">أقسام المنهج وعناصر المحتوى — للعرض فقط.</p>
        </div>
        <a href="{{ route('instructor.libraries.curriculum.index') }}" class="inline-flex h-9 items-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700">رجوع</a>
    </section>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">أقسام</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $course->sections->count() }}</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">عناصر</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $course->sections->sum(fn ($s) => $s->items->count()) }}</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs text-slate-500">محاضرات</div><div class="mt-1 text-2xl font-semibold text-[#0B3D91]">{{ $course->lectures->count() }}</div></div>
    </div>

    @forelse($course->sections as $section)
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
                <h3 class="font-semibold text-slate-900">{{ $section->title }}</h3>
                @if($section->description)
                    <p class="mt-0.5 text-xs text-slate-500">{{ $section->description }}</p>
                @endif
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($section->items as $item)
                    @php
                        $related = $item->item;
                        $label = $related->title
                            ?? $related->name
                            ?? (class_basename((string) $item->item_type).' #'.$item->item_id);
                    @endphp
                    <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-sm">
                        <div>
                            <span class="font-medium text-slate-900">{{ $label }}</span>
                            <span class="ms-2 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-500">{{ class_basename((string) $item->item_type) }}</span>
                        </div>
                        <span class="text-xs text-slate-500">ترتيب {{ $item->order }}</span>
                    </li>
                @empty
                    <li class="px-4 py-6 text-sm text-slate-500">لا عناصر في هذا القسم.</li>
                @endforelse
            </ul>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-12 text-center text-slate-500">
            لا أقسام منهج لهذا الكورس بعد.
        </div>
    @endforelse
</div>
@endsection
