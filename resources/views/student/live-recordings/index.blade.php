@extends('layouts.student-timeline')

@section('title', 'تسجيلات جلسات البث')

@section('content')
<section class="st-join-hero" aria-label="تسجيلات جلسات البث">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">Recordings</p>
        <h1 class="st-join-hero__title">تسجيلات جلسات البث</h1>
        <p class="st-join-hero__meta">مشاهدة تسجيلات الجلسات المنتهية المنشورة لك</p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('student.live-sessions.index') }}" class="st-pill st-pill--outline st-pill--lg">
            <i class="fas fa-broadcast-tower"></i> جلسات البث
        </a>
    </div>
</section>

@if($recordings->isEmpty())
<div class="st-class-card items-center justify-center text-center p-10" style="min-height:180px;">
    <i class="fas fa-film text-4xl text-[var(--st-muted)] mb-4"></i>
    <p class="font-bold text-[var(--st-ink-strong)] m-0">لا توجد تسجيلات متاحة حالياً</p>
    <p class="text-sm text-[var(--st-muted)] mt-1">ستظهر هنا تسجيلات الجلسات بعد انتهائها ونشرها</p>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($recordings as $rec)
    <a href="{{ route('student.live-recordings.show', $rec) }}" class="st-class-card no-underline text-inherit" style="min-height:auto;padding:18px 20px;display:block;">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
            <i class="fas fa-play text-lg"></i>
        </div>
        <h3 class="font-black text-[var(--st-ink-strong)] m-0">{{ $rec->title ?? 'تسجيل #' . $rec->id }}</h3>
        <p class="text-sm text-[var(--st-muted)] mt-1 mb-2">{{ $rec->session?->title }}</p>
        <div class="flex items-center gap-3 text-xs text-[var(--st-muted)]">
            <span><i class="fas fa-clock ml-1"></i> {{ $rec->duration_for_humans }}</span>
            <span><i class="fas fa-hdd ml-1"></i> {{ $rec->file_size_for_humans }}</span>
        </div>
    </a>
    @endforeach
</div>
@if($recordings->hasPages())
<div class="mt-6">{{ $recordings->links() }}</div>
@endif
@endif
@endsection
