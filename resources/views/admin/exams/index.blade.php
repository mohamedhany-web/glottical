@extends('layouts.admin')

@section('title', 'الامتحانات - ' . config('app.name'))
@section('page_title', 'الامتحانات')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحتوى · التقييمات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إدارة الامتحانات</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">اختر برنامجاً لعرض امتحاناته وأسئلة التقييم.</p>
        </div>
        <a href="{{ route('admin.question-bank.index') }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
            <i class="fas fa-database text-xs"></i>
            بنك الأسئلة
        </a>
    </section>

    @if($courses->count() > 0)
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($courses as $course)
                <a href="{{ route('admin.exams.by-course', $course) }}"
                   class="group rounded-2xl border border-line bg-surface p-4 shadow-soft transition hover:border-accent/30 hover:bg-accent-soft/30">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="line-clamp-2 text-sm font-semibold text-ink group-hover:text-accent">{{ $course->title }}</h3>
                            @if($course->academicSubject)
                                <p class="mt-1 truncate text-xs text-muted">{{ $course->academicSubject->name }}</p>
                            @endif
                        </div>
                        <div class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent group-hover:bg-accent group-hover:text-white">
                            <i class="fas fa-clipboard-list text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#f2f5f4] px-2.5 py-1 text-xs font-medium text-ink-soft">
                            <i class="fas fa-file-alt text-[10px] text-accent"></i>
                            {{ $course->exams_count ?? 0 }} امتحان
                        </span>
                        <span class="text-xs font-medium text-accent">فتح <i class="fas fa-arrow-left mr-1"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <article class="rounded-2xl border border-dashed border-line bg-surface px-6 py-14 text-center shadow-soft">
            <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-graduation-cap text-xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد برامج</h3>
            <p class="mt-1 text-sm text-muted">أضف برنامجاً أولاً ثم أنشئ امتحاناته.</p>
        </article>
    @endif
</div>
@endsection
