@extends('layouts.student-timeline')

@section('title', 'باقاتي')
@section('page_title', 'باقات المجموعات')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-line bg-white px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    <div class="grid gap-4 sm:grid-cols-2">
        @forelse($subscriptions as $sub)
            <a href="{{ route('student.tutoring-subscriptions.show', $sub) }}" class="rounded-2xl border border-line bg-white p-5 shadow-soft transition hover:border-accent/40">
                <h3 class="font-semibold text-ink">{{ $sub->tutoringGroup?->title }}</h3>
                <p class="mt-1 text-sm text-muted">{{ $sub->package?->name ?: 'اشتراك' }}</p>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="font-bold text-accent">{{ $sub->sessionsLeft() }} / {{ $sub->sessions_total }} حصة متبقية</span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs">{{ $sub->statusLabel() }}</span>
                </div>
            </a>
        @empty
            <div class="sm:col-span-2 rounded-2xl border border-dashed border-line px-4 py-12 text-center text-muted">
                لا توجد باقات نشطة. <a href="{{ route('public.instructors.index') }}" class="text-accent">تصفّح المعلمين والحصص الخاصة</a>
            </div>
        @endforelse
    </div>
    {{ $subscriptions->links() }}
</div>
@endsection
