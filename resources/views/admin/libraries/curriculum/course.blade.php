@extends('layouts.admin')

@section('title', 'منهج: '.$course->title)
@section('page_title', 'منهج الكورس')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.libraries.curriculum.index') }}" class="hover:text-accent">المناهج</a>
                · {{ $course->academicSubject?->academicYear?->name }}
                · {{ $course->academicSubject?->name }}
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $course->title }}</h2>
            <p class="mt-1 text-sm text-muted">أقسام المنهج وعناصر المحتوى المرتبطة بهذا الكورس.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.advanced-courses.edit', $course) }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-3 text-sm">تعديل الكورس</a>
            <a href="{{ route('admin.libraries.curriculum.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-ink px-3 text-sm text-white">رجوع</a>
        </div>
    </section>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">أقسام</div><div class="mt-1 text-2xl font-semibold">{{ $course->sections->count() }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">عناصر</div><div class="mt-1 text-2xl font-semibold">{{ $course->sections->sum(fn ($s) => $s->items->count()) }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">محاضرات</div><div class="mt-1 text-2xl font-semibold">{{ $course->lectures->count() }}</div></div>
    </div>

    @if(isset($itemTypeCounts) && $itemTypeCounts->isNotEmpty())
        <div class="flex flex-wrap gap-2">
            @foreach($itemTypeCounts as $type => $count)
                <span class="rounded-full bg-canvas-muted px-3 py-1 text-xs font-bold text-muted">{{ class_basename($type) }}: {{ $count }}</span>
            @endforeach
        </div>
    @endif

    @forelse($course->sections as $section)
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line bg-canvas-muted/40 px-4 py-3">
                <h3 class="font-semibold text-ink">{{ $section->title }}</h3>
                @if($section->description)
                    <p class="mt-0.5 text-xs text-muted">{{ $section->description }}</p>
                @endif
            </div>
            <ul class="divide-y divide-line">
                @forelse($section->items as $item)
                    @php
                        $related = $item->item;
                        $label = $related->title
                            ?? $related->name
                            ?? (class_basename((string) $item->item_type).' #'.$item->item_id);
                    @endphp
                    <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-sm">
                        <div>
                            <span class="font-medium text-ink">{{ $label }}</span>
                            <span class="ms-2 rounded-full bg-canvas-muted px-2 py-0.5 text-[10px] font-bold uppercase text-muted">{{ class_basename((string) $item->item_type) }}</span>
                            @unless($item->is_active)
                                <span class="ms-1 text-[10px] font-bold text-danger">معطّل</span>
                            @endunless
                        </div>
                        <span class="text-xs text-muted">ترتيب {{ $item->order }}</span>
                    </li>
                @empty
                    <li class="px-4 py-6 text-sm text-muted">لا عناصر في هذا القسم.</li>
                @endforelse
            </ul>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-line px-4 py-12 text-center text-muted">
            لا أقسام منهج لهذا الكورس بعد. يمكن بناؤها من لوحة المدرّس أو إدارة الكورس.
        </div>
    @endforelse
</div>
@endsection
