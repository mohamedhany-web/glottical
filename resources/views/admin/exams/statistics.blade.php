@extends('layouts.admin')

@section('title', 'إحصائيات الامتحان')
@section('page_title', 'إحصائيات الامتحان')

@php
    $overview = $stats['overview'] ?? [];
    $totalAttempts = (int) ($overview['total_attempts'] ?? 0);
    $gradeBarClasses = [
        'ممتاز' => 'bg-emerald-500',
        'جيد جداً' => 'bg-accent',
        'جيد' => 'bg-amber-500',
        'مقبول' => 'bg-orange-500',
        'ضعيف' => 'bg-rose-500',
    ];
@endphp

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-accent">الامتحانات</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="hover:text-accent">{{ Str::limit($exam->course?->title ?? '', 25) }}</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.show', $exam) }}" class="hover:text-accent">{{ Str::limit($exam->title, 25) }}</a>
                <span class="mx-1">·</span>
                <span class="text-ink">الإحصائيات</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إحصائيات الامتحان</h2>
            <p class="mt-1 text-sm text-muted">{{ $exam->title }} — {{ $totalAttempts }} محاولة</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.exams.show', $exam) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للامتحان
            </a>
            <a href="{{ route('admin.exams.preview', $exam) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-eye text-xs"></i>
                معاينة الامتحان
            </a>
            <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-list text-xs"></i>
                امتحانات البرنامج
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex items-center gap-4 p-5">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ $totalAttempts }}</p>
                    <p class="text-sm text-muted">إجمالي المحاولات</p>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex items-center gap-4 p-5">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ number_format($overview['average_score'] ?? 0, 1) }}</p>
                    <p class="text-sm text-muted">متوسط الدرجات</p>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex items-center gap-4 p-5">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ number_format($overview['highest_score'] ?? 0, 1) }}</p>
                    <p class="text-sm text-muted">أعلى درجة</p>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex items-center gap-4 p-5">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ number_format($overview['pass_rate'] ?? 0, 1) }}%</p>
                    <p class="text-sm text-muted">معدل النجاح</p>
                </div>
            </div>
        </article>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">توزيع الدرجات</h3>
            </div>
            <div class="p-5">
                @if($stats['score_distribution']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['score_distribution'] as $grade)
                            @php
                                $pct = $totalAttempts > 0 ? round(($grade->count / $totalAttempts) * 100, 1) : 0;
                                $barClass = $gradeBarClasses[$grade->grade] ?? 'bg-muted';
                            @endphp
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="size-3 shrink-0 rounded-full {{ $barClass }}"></span>
                                    <span class="font-medium text-ink">{{ $grade->grade }}</span>
                                </div>
                                <div class="flex shrink-0 items-center gap-3">
                                    <div class="h-2 w-28 overflow-hidden rounded-full bg-[#f2f5f4] sm:w-32">
                                        <div class="h-full rounded-full {{ $barClass }}" style="width: {{ min($pct, 100) }}%"></div>
                                    </div>
                                    <span class="min-w-[70px] text-sm tabular-nums text-muted">{{ $grade->count }} ({{ $pct }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-line bg-[#f2f5f4]/50 py-10 text-center">
                        <i class="fas fa-chart-bar mb-3 text-4xl text-muted/40"></i>
                        <p class="text-sm text-muted">لا توجد بيانات لعرض توزيع الدرجات</p>
                    </div>
                @endif
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">المحاولات حسب التاريخ</h3>
            </div>
            <div class="p-5">
                @if($stats['attempts_by_date']->count() > 0)
                    <div class="space-y-2">
                        @foreach($stats['attempts_by_date']->take(10) as $attempt)
                            <div class="flex items-center justify-between rounded-xl border border-line bg-[#f2f5f4]/50 p-3 transition hover:border-accent/30">
                                <div class="flex items-center gap-3">
                                    <div class="inline-flex size-9 items-center justify-center rounded-lg bg-[#f2f5f4] text-accent">
                                        <i class="fas fa-calendar text-sm"></i>
                                    </div>
                                    <span class="font-medium tabular-nums text-ink">{{ \Carbon\Carbon::parse($attempt->date)->format('d/m/Y') }}</span>
                                </div>
                                <span class="inline-flex items-center rounded-lg bg-accent-soft px-3 py-1 text-sm font-semibold text-accent">
                                    {{ $attempt->count }} محاولة
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-line bg-[#f2f5f4]/50 py-10 text-center">
                        <i class="fas fa-calendar-alt mb-3 text-4xl text-muted/40"></i>
                        <p class="text-sm text-muted">لا توجد محاولات مسجلة</p>
                    </div>
                @endif
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">تفاصيل النتائج</h3>
            </div>
            <div class="space-y-3 p-5">
                <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="inline-flex size-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="font-medium text-emerald-800">الطلاب الناجحون</span>
                    </div>
                    <span class="text-lg font-bold tabular-nums text-emerald-800">{{ $overview['passed_attempts'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="inline-flex size-9 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                            <i class="fas fa-times"></i>
                        </div>
                        <span class="font-medium text-rose-800">الطلاب الراسبون</span>
                    </div>
                    <span class="text-lg font-bold tabular-nums text-rose-800">{{ $overview['failed_attempts'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="inline-flex size-9 items-center justify-center rounded-lg bg-[#f2f5f4] text-muted">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <span class="font-medium text-ink">أقل درجة</span>
                    </div>
                    <span class="text-lg font-bold tabular-nums text-ink">{{ number_format($overview['lowest_score'] ?? 0, 1) }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="inline-flex size-9 items-center justify-center rounded-lg bg-[#f2f5f4] text-muted">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                        <span class="font-medium text-ink">درجة النجاح المطلوبة</span>
                    </div>
                    <span class="text-lg font-bold tabular-nums text-ink">{{ $exam->passing_marks }}%</span>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">معلومات الامتحان</h3>
            </div>
            <div class="space-y-4 p-5">
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="text-muted">عدد الأسئلة</span>
                    <span class="font-semibold tabular-nums text-ink">{{ $exam->examQuestions->count() }} سؤال</span>
                </div>
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="text-muted">إجمالي الدرجات</span>
                    <span class="font-semibold tabular-nums text-ink">{{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="text-muted">مدة الامتحان</span>
                    <span class="font-semibold tabular-nums text-ink">{{ $exam->duration_minutes }} دقيقة</span>
                </div>
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="text-muted">عدد المحاولات المسموحة</span>
                    <span class="font-semibold text-ink">{{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="text-muted">حالة الامتحان</span>
                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-medium {{ $exam->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ $exam->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="text-muted">حالة النشر</span>
                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-medium {{ $exam->is_published ? 'bg-accent-soft text-accent' : 'bg-[#f2f5f4] text-muted' }}">
                        {{ $exam->is_published ? 'منشور' : 'مسودة' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-muted">تاريخ الإنشاء</span>
                    <span class="font-semibold tabular-nums text-ink">{{ $exam->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
