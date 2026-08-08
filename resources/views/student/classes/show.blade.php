@extends('layouts.app')

@section('title', $cohort->title)
@section('page_title', $cohort->title)

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-line bg-white px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <section class="rounded-2xl border border-line bg-white p-5 shadow-soft">
        <p class="text-xs font-medium text-muted">{{ $cohort->tutoringGroup?->title }}</p>
        <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $cohort->title }}</h2>
        <p class="mt-2 text-sm text-muted">{{ $cohort->scheduleSummary() }}</p>
        <p class="mt-1 text-sm text-muted">المعلم: {{ $cohort->tutoringGroup?->instructor?->name ?: '—' }} · {{ $cohort->activeEnrollments->count() }}/{{ $cohort->capacity }} طالب</p>
        @if($cohort->whatsapp_group_url)
            <a href="{{ $cohort->whatsapp_group_url }}" target="_blank" class="mt-3 inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm text-ink-soft">مجموعة واتساب</a>
        @endif
    </section>

    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <div class="border-b border-line px-4 py-3">
            <h3 class="text-sm font-semibold text-ink">مواعيد الحصص</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-slate-50 text-xs font-semibold text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start">الحصة</th>
                        <th class="px-4 py-3 text-start">الموعد</th>
                        <th class="px-4 py-3 text-start">الحالة</th>
                        <th class="px-4 py-3 text-end">دخول</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($cohort->classSessions as $session)
                        <tr>
                            <td class="px-4 py-3 font-medium text-ink">{{ $session->displayTitle() }}</td>
                            <td class="px-4 py-3 tabular-nums text-muted">
                                {{ $session->starts_at?->format('Y-m-d H:i') }}
                                @if($session->ends_at)
                                    <span>— {{ $session->ends_at->format('H:i') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ $session->statusLabel() }}</span></td>
                            <td class="px-4 py-3 text-end">
                                @if($session->status === 'cancelled')
                                    <span class="text-muted">—</span>
                                @elseif($session->isJoinable())
                                    <form method="POST" action="{{ route('student.classes.sessions.join', $session) }}" class="inline">
                                        @csrf
                                        <button class="text-accent hover:underline">دخول Live</button>
                                    </form>
                                @else
                                    <span class="text-xs text-muted">قريباً</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-muted">لم يُنشر جدول الحصص بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <p class="text-sm"><a href="{{ route('student.classes.index') }}" class="text-accent hover:underline">← رجوع لفصولي</a></p>
</div>
@endsection
