@extends('layouts.app')

@section('title', 'مركز قيادة الدفعة')
@section('page_title', 'مركز قيادة الدفعة')

@push('styles')
<style>
    .cc-hero {
        border-radius: 24px;
        padding: 1.25rem 1.35rem;
        background: linear-gradient(135deg, #0B3D91 0%, #072A66 100%);
        color: #fff;
        box-shadow: 0 20px 36px -24px rgba(7,42,102,.7);
    }
    .cc-kpi {
        border-radius: 18px; border: 1.5px solid #E4EBF7; background: #fff;
        padding: .95rem 1rem;
    }
    .cc-kpi__label { font-size: .72rem; font-weight: 800; color: #5B6B88; }
    .cc-kpi__value { margin-top: .2rem; font-size: 1.4rem; font-weight: 900; color: #12233F; }
    .cc-risk {
        border-radius: 18px; border: 1.5px solid #FECACA; background: #FFF7F7; padding: 1rem;
    }
</style>
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $group = $cohort->tutoringGroup;
@endphp

<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('instructor.tutoring-cohorts.index') }}" class="text-sm font-bold text-accent hover:underline">
            ← {{ $isRtl ? 'كل الدفعات' : 'All cohorts' }}
        </a>
        @if($cohort->whatsapp_group_url)
            <a href="{{ $cohort->whatsapp_group_url }}" target="_blank" rel="noopener"
               class="inline-flex h-9 items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 text-sm font-bold text-emerald-700">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        @endif
    </div>

    <section class="cc-hero">
        <p class="text-xs font-bold uppercase tracking-wide text-white/70">
            {{ $isRtl ? 'School Command Center' : 'School Command Center' }}
        </p>
        <h2 class="mt-1 text-2xl font-black leading-tight">{{ $cohort->title }}</h2>
        <p class="mt-1 text-sm font-semibold text-white/85">
            {{ $group?->title }}
            @if($group?->schoolYear) · {{ $group->schoolYear->name }} @endif
            @if($group?->schoolSubject) · {{ $group->schoolSubject->name }} @endif
            · {{ $cohort->statusLabel() }}
        </p>
        <p class="mt-2 text-sm font-bold text-[#F5B800]">{{ $cohort->scheduleSummary() }}</p>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="cc-kpi">
            <p class="cc-kpi__label">{{ $isRtl ? 'الطلاب' : 'Students' }}</p>
            <p class="cc-kpi__value tabular-nums">{{ $center['students_count'] }}</p>
            <p class="mt-1 text-[11px] font-bold text-[#5B6B88]">{{ $cohort->enrolled_count }} / {{ $cohort->capacity }} {{ $isRtl ? 'سعة' : 'cap' }}</p>
        </article>
        <article class="cc-kpi">
            <p class="cc-kpi__label">{{ $isRtl ? 'نشط اليوم' : 'Active today' }}</p>
            <p class="cc-kpi__value tabular-nums">{{ $center['active_today'] }}</p>
        </article>
        <article class="cc-kpi">
            <p class="cc-kpi__label">{{ $isRtl ? 'متوسط التقدّم' : 'Avg progress' }}</p>
            <p class="cc-kpi__value tabular-nums">{{ $center['average_progress'] }}%</p>
        </article>
        <article class="cc-kpi">
            <p class="cc-kpi__label">{{ $isRtl ? 'الحصص' : 'Sessions' }}</p>
            <p class="cc-kpi__value tabular-nums">{{ $center['sessions_completed'] }}/{{ $center['sessions_total'] }}</p>
            <p class="mt-1 text-[11px] font-bold text-[#5B6B88]">{{ $center['sessions_upcoming'] }} {{ $isRtl ? 'قادمة' : 'upcoming' }}</p>
        </article>
    </section>

    @if($center['at_risk']->isNotEmpty())
        <section class="cc-risk">
            <h3 class="text-sm font-black text-[#B91C1C]">
                <i class="fas fa-triangle-exclamation me-1"></i>
                {{ $isRtl ? 'طلاب يحتاجون تدخّل' : 'Students at risk' }}
                ({{ $center['at_risk']->count() }})
            </h3>
            <ul class="mt-3 space-y-2">
                @foreach($center['at_risk']->take(8) as $risk)
                    <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-red-100 bg-white px-3 py-2.5 text-sm">
                        <div>
                            <p class="font-bold text-[#12233F]">{{ $risk->name }}</p>
                            <p class="text-xs font-semibold text-[#5B6B88]">
                                @if($risk->present_count === 0 && $risk->completed_sessions >= 2)
                                    {{ $isRtl ? 'لم يسجَّل حضور رغم حصص مكتملة' : 'No attendance despite completed sessions' }}
                                @else
                                    {{ $isRtl ? ('بدون نشاط منذ '.$risk->days_silent.' يوم') : ($risk->days_silent.' days without activity') }}
                                @endif
                            </p>
                        </div>
                        @if($risk->email)
                            <a href="mailto:{{ $risk->email }}" class="text-xs font-bold text-[#0B3D91] hover:underline">{{ $isRtl ? 'تذكير' : 'Remind' }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="grid gap-4 xl:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
            <div class="border-b border-line px-4 py-3 font-black text-ink">
                {{ $isRtl ? 'حصص اليوم / القادمة' : 'Today / upcoming sessions' }}
            </div>
            <ul class="divide-y divide-line text-sm">
                @forelse($center['upcoming_sessions'] as $session)
                    <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                        <div>
                            <p class="font-bold text-ink">{{ $session->displayTitle() }}</p>
                            <p class="text-xs font-semibold text-muted">
                                {{ $session->starts_at?->format('Y-m-d g:i A') }} · {{ $session->statusLabel() }}
                            </p>
                        </div>
                        @if($session->classroomMeeting?->code)
                            <a href="{{ url('classroom/join/'.$session->classroomMeeting->code) }}"
                               class="inline-flex h-8 items-center rounded-lg bg-accent px-3 text-xs font-bold text-white">
                                {{ $isRtl ? 'ادخل الغرفة' : 'Enter room' }}
                            </a>
                        @endif
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-muted">{{ $isRtl ? 'لا حصص قادمة قريباً.' : 'No upcoming sessions.' }}</li>
                @endforelse
            </ul>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
            <div class="border-b border-line px-4 py-3 font-black text-ink">
                {{ $isRtl ? 'قائمة الفصل' : 'Class roster' }}
                ({{ $center['roster']->count() }})
            </div>
            <ul class="divide-y divide-line text-sm max-h-[28rem] overflow-y-auto">
                @forelse($center['roster'] as $student)
                    <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                        <div class="min-w-0">
                            <p class="font-bold text-ink truncate">
                                {{ $student->name }}
                                @if($student->is_at_risk)
                                    <span class="ms-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-black text-red-700">Risk</span>
                                @endif
                            </p>
                            <p class="text-xs font-semibold text-muted truncate">
                                {{ $isRtl ? 'حضور' : 'Att' }} {{ $student->present_count }}
                                · {{ $student->progress_percent }}%
                                @if($student->last_activity_at)
                                    · {{ $isRtl ? 'آخر نشاط' : 'Last' }} {{ $student->last_activity_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                        <div class="w-24">
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <span class="block h-full rounded-full bg-[#0B3D91]" style="width: {{ $student->progress_percent }}%"></span>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-muted">{{ $isRtl ? 'لا طلاب مسجّلين بعد.' : 'No enrolled students yet.' }}</li>
                @endforelse
            </ul>
        </article>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <div class="border-b border-line px-4 py-3 font-black text-ink">{{ $isRtl ? 'جدول الحصص الكامل' : 'Full session schedule' }}</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-muted">
                    <tr>
                        <th class="px-4 py-2 text-start">#</th>
                        <th class="px-4 py-2 text-start">{{ $isRtl ? 'العنوان' : 'Title' }}</th>
                        <th class="px-4 py-2 text-start">{{ $isRtl ? 'الموعد' : 'When' }}</th>
                        <th class="px-4 py-2 text-start">{{ $isRtl ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($center['sessions'] as $session)
                        <tr>
                            <td class="px-4 py-2.5 tabular-nums">{{ $session->session_number }}</td>
                            <td class="px-4 py-2.5 font-semibold text-ink">{{ $session->displayTitle() }}</td>
                            <td class="px-4 py-2.5 text-muted">{{ $session->starts_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2.5">{{ $session->statusLabel() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-muted">{{ $isRtl ? 'لم يُولَّد جدول بعد.' : 'No schedule generated yet.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <article class="rounded-2xl border border-line bg-white p-4 shadow-soft sm:p-5">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-sm font-black text-ink">👥 {{ $isRtl ? 'مجتمع الفصل (Moderation)' : 'Class feed moderation' }}</h3>
        </div>
        <form method="POST" action="{{ route('instructor.tutoring-cohorts.feed.store', $cohort) }}" class="mb-4 space-y-2">
            @csrf
            <input type="hidden" name="post_type" value="announcement">
            <textarea name="body" rows="2" maxlength="1000" required class="w-full rounded-xl border border-line px-3 py-2 text-sm" placeholder="{{ $isRtl ? 'إعلان للفصل…' : 'Class announcement…' }}"></textarea>
            <label class="inline-flex items-center gap-2 text-xs font-bold text-muted">
                <input type="checkbox" name="is_pinned" value="1" class="rounded border-line"> {{ $isRtl ? 'تثبيت' : 'Pin' }}
            </label>
            <button class="ms-2 inline-flex h-9 items-center rounded-xl bg-accent px-4 text-xs font-black text-white">{{ $isRtl ? 'نشر إعلان' : 'Publish' }}</button>
        </form>
        <ul class="space-y-3">
            @forelse(($feedPosts ?? collect()) as $post)
                <li class="rounded-xl border {{ $post->is_hidden ? 'border-red-200 bg-red-50' : 'border-line bg-slate-50' }} p-3 text-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-bold text-ink">{{ $post->author?->name }} · {{ $post->typeLabel() }}</p>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('instructor.class-feed.pin', $post) }}">@csrf
                                <button class="text-xs font-bold text-accent">{{ $post->is_pinned ? 'Unpin' : 'Pin' }}</button>
                            </form>
                            @if($post->is_hidden)
                                <form method="POST" action="{{ route('instructor.class-feed.unhide', $post) }}">@csrf
                                    <button class="text-xs font-bold text-emerald-700">Unhide</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('instructor.class-feed.hide', $post) }}">@csrf
                                    <button class="text-xs font-bold text-red-600">Hide</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <p class="mt-2 whitespace-pre-wrap text-ink">{{ $post->body }}</p>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-muted">{{ $isRtl ? 'لا منشورات بعد.' : 'No posts yet.' }}</li>
            @endforelse
        </ul>
    </article>
</div>
@endsection
