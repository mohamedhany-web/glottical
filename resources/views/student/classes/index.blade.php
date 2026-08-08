@extends('layouts.app')

@section('title', 'فصولي')
@section('page_title', 'فصولي التعليمية')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-line bg-white px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    @if($upcoming->isNotEmpty())
        <section class="space-y-3">
            <h3 class="text-sm font-semibold text-ink">الحصص القادمة</h3>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach($upcoming as $session)
                    <article class="rounded-2xl border border-accent/20 bg-gradient-to-l from-accent/10 to-white p-4 shadow-soft">
                        <p class="text-xs font-bold uppercase tracking-wide text-accent">{{ $session->cohort?->title }}</p>
                        <h4 class="mt-1 font-semibold text-ink">{{ $session->displayTitle() }}</h4>
                        <p class="text-sm text-muted">{{ $session->starts_at?->format('Y-m-d H:i') }} · {{ $session->tutoringGroup?->title }}</p>
                        @if($session->isJoinable())
                            <form method="POST" action="{{ route('student.classes.sessions.join', $session) }}" class="mt-3">
                                @csrf
                                <button class="inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                                    <i class="fas fa-video"></i> دخول الحصة
                                </button>
                            </form>
                        @else
                            <p class="mt-3 text-xs text-muted">يفتح الدخول قبل الموعد بـ 30 دقيقة</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <div class="border-b border-line px-4 py-3">
            <h3 class="text-sm font-semibold text-ink">فصولي</h3>
        </div>
        <ul class="divide-y divide-line">
            @forelse($enrollments as $enrollment)
                @php $c = $enrollment->cohort; @endphp
                <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-4">
                    <div class="min-w-0">
                        <p class="font-semibold text-ink">{{ $c?->title }}</p>
                        <p class="text-sm text-muted">{{ $c?->tutoringGroup?->title }} · {{ $c?->scheduleSummary() }}</p>
                        <p class="text-xs text-muted">المعلم: {{ $c?->tutoringGroup?->instructor?->name ?: '—' }}</p>
                    </div>
                    <a href="{{ route('student.classes.show', $c) }}" class="inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm text-accent hover:bg-slate-50">عرض الجدول</a>
                </li>
            @empty
                <li class="px-4 py-10 text-center text-sm text-muted">
                    لست منضماً لأي فصل بعد.
                    <a href="{{ route('public.groups') }}" class="text-accent">تصفّح المجموعات</a>
                </li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
