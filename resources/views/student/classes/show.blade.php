@extends('layouts.app')

@section('title', $cohort->title)
@section('page_title', $cohort->title)

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $feedPosts = $feedPosts ?? collect();
    $leaderboard = $leaderboard ?? collect();
    $canModerateFeed = $canModerateFeed ?? false;
    $game = $game ?? ['xp' => 0, 'level' => 1, 'streak' => ['current' => 0]];
@endphp
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-line bg-white px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <section class="rounded-2xl border border-line bg-gradient-to-l from-[#0B3D91] to-[#072A66] p-5 text-white shadow-soft">
        <p class="text-xs font-bold uppercase tracking-wide text-white/70">{{ $cohort->tutoringGroup?->title }}</p>
        <h2 class="mt-1 text-2xl font-black">{{ $cohort->title }}</h2>
        <p class="mt-2 text-sm font-semibold text-white/85">{{ $cohort->scheduleSummary() }}</p>
        <p class="mt-1 text-sm text-white/80">
            {{ $isRtl ? 'المعلم' : 'Teacher' }}: {{ $cohort->tutoringGroup?->instructor?->name ?: '—' }}
            · {{ $cohort->activeEnrollments->count() }}/{{ $cohort->capacity }}
            · ⭐ {{ number_format($game['xp'] ?? 0) }} XP · Lv {{ $game['level'] ?? 1 }}
            · 🔥 {{ $game['streak']['current'] ?? 0 }}
        </p>
        @if($cohort->whatsapp_group_url)
            <a href="{{ $cohort->whatsapp_group_url }}" target="_blank" class="mt-3 inline-flex h-9 items-center rounded-xl bg-white/15 px-4 text-sm font-bold text-white ring-1 ring-white/20">مجموعة واتساب</a>
        @endif
    </section>

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="space-y-4 xl:col-span-2">
            <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
                <div class="border-b border-line px-4 py-3">
                    <h3 class="text-sm font-black text-ink">{{ $isRtl ? 'مواعيد الحصص' : 'Class schedule' }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-line bg-slate-50 text-xs font-semibold text-muted">
                            <tr>
                                <th class="px-4 py-3 text-start">{{ $isRtl ? 'الحصة' : 'Session' }}</th>
                                <th class="px-4 py-3 text-start">{{ $isRtl ? 'الموعد' : 'When' }}</th>
                                <th class="px-4 py-3 text-start">{{ $isRtl ? 'الحالة' : 'Status' }}</th>
                                <th class="px-4 py-3 text-end">{{ $isRtl ? 'دخول' : 'Join' }}</th>
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
                                                <button class="text-accent hover:underline font-bold">{{ $isRtl ? 'دخول Live' : 'Join live' }}</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-muted">{{ $isRtl ? 'قريباً' : 'Soon' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-10 text-center text-muted">{{ $isRtl ? 'لم يُنشر جدول الحصص بعد.' : 'No schedule yet.' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-2xl border border-line bg-white p-4 shadow-soft sm:p-5">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-black text-ink">👥 {{ $isRtl ? 'مجتمع الفصل' : 'Class community' }}</h3>
                    <span class="text-[11px] font-bold text-muted">{{ $isRtl ? 'أسئلة وإعلانات' : 'Questions & announcements' }}</span>
                </div>

                <form method="POST" action="{{ route('student.classes.feed.store', $cohort) }}" class="mb-4 space-y-2">
                    @csrf
                    <textarea name="body" rows="3" maxlength="1000" required
                              class="w-full rounded-xl border border-line px-3 py-2 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"
                              placeholder="{{ $isRtl ? 'اسأل زملاءك أو شارك فكرة عن درس اليوم…' : 'Ask classmates or share a thought…' }}"></textarea>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <select name="post_type" class="h-9 rounded-lg border border-line px-2 text-xs font-bold">
                            <option value="question">{{ $isRtl ? 'سؤال' : 'Question' }}</option>
                            @if($canModerateFeed)
                                <option value="announcement">{{ $isRtl ? 'إعلان' : 'Announcement' }}</option>
                            @endif
                        </select>
                        <button class="inline-flex h-9 items-center rounded-xl bg-accent px-4 text-xs font-black text-white">
                            {{ $isRtl ? 'نشر' : 'Post' }}
                        </button>
                    </div>
                </form>

                <ul class="space-y-3">
                    @forelse($feedPosts as $post)
                        <li class="rounded-xl border {{ $post->is_hidden ? 'border-red-200 bg-red-50/60' : 'border-line bg-[#F8FAFD]' }} p-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-xs font-black text-ink">
                                    {{ $post->author?->name }}
                                    <span class="ms-1 rounded-full bg-white px-2 py-0.5 text-[10px] text-muted">{{ $post->typeLabel() }}</span>
                                    @if($post->is_pinned)
                                        <span class="ms-1 text-[10px] font-black text-amber-700">📌</span>
                                    @endif
                                    @if($post->is_hidden)
                                        <span class="ms-1 text-[10px] font-black text-red-600">{{ $isRtl ? 'مخفي' : 'Hidden' }}</span>
                                    @endif
                                </p>
                                <span class="text-[10px] font-bold text-muted">{{ $post->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-ink whitespace-pre-wrap">{{ $post->body }}</p>

                            @if($canModerateFeed)
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('student.classes.feed.pin', $post) }}">@csrf
                                        <button class="text-[11px] font-bold text-accent">{{ $post->is_pinned ? ($isRtl ? 'إلغاء التثبيت' : 'Unpin') : ($isRtl ? 'تثبيت' : 'Pin') }}</button>
                                    </form>
                                    @if($post->is_hidden)
                                        <form method="POST" action="{{ route('student.classes.feed.unhide', $post) }}">@csrf
                                            <button class="text-[11px] font-bold text-emerald-700">{{ $isRtl ? 'إظهار' : 'Unhide' }}</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('student.classes.feed.hide', $post) }}">@csrf
                                            <button class="text-[11px] font-bold text-red-600">{{ $isRtl ? 'إخفاء' : 'Hide' }}</button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            <ul class="mt-3 space-y-2 border-t border-line/70 pt-2">
                                @foreach($post->visibleComments as $comment)
                                    <li class="text-xs">
                                        <span class="font-black text-ink">{{ $comment->author?->name }}:</span>
                                        <span class="font-semibold text-muted">{{ $comment->body }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <form method="POST" action="{{ route('student.classes.feed.comment', $post) }}" class="mt-2 flex gap-2">
                                @csrf
                                <input type="text" name="body" maxlength="1000" required
                                       class="h-9 flex-1 rounded-lg border border-line px-3 text-xs"
                                       placeholder="{{ $isRtl ? 'اكتب تعليقاً…' : 'Write a comment…' }}">
                                <button class="h-9 rounded-lg bg-[#0B3D91] px-3 text-[11px] font-bold text-white">{{ $isRtl ? 'رد' : 'Reply' }}</button>
                            </form>
                        </li>
                    @empty
                        <li class="rounded-xl border border-dashed border-line px-4 py-8 text-center text-sm text-muted">
                            {{ $isRtl ? 'كن أول من يفتح نقاش الفصل.' : 'Be the first to start the class discussion.' }}
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-2xl border border-line bg-white p-4 shadow-soft">
                <h3 class="text-sm font-black text-ink">🏅 {{ $isRtl ? 'صدارة الفصل' : 'Class leaderboard' }}</h3>
                <p class="mt-1 text-[11px] font-semibold text-muted">{{ $isRtl ? 'حسب نشاط التعلّم (XP)' : 'By learning activity XP' }}</p>
                <ol class="mt-3 space-y-2">
                    @forelse($leaderboard as $row)
                        <li class="flex items-center justify-between gap-2 rounded-xl bg-[#F8FAFD] px-3 py-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full {{ $row->rank <= 3 ? 'bg-[#F5B800] text-[#072A66]' : 'bg-[#E8EEF8] text-[#0B3D91]' }} text-xs font-black">{{ $row->rank }}</span>
                                <span class="truncate text-sm font-bold text-ink">{{ $row->name }}</span>
                            </div>
                            <span class="text-xs font-black text-[#0B3D91] tabular-nums">{{ number_format($row->xp) }}</span>
                        </li>
                    @empty
                        <li class="py-6 text-center text-xs font-semibold text-muted">{{ $isRtl ? 'ابدأ الحضور لتظهر هنا.' : 'Attend to appear here.' }}</li>
                    @endforelse
                </ol>
            </section>
        </aside>
    </div>

    <p class="text-sm"><a href="{{ route('student.classes.index') }}" class="text-accent hover:underline">← {{ $isRtl ? 'رجوع لفصولي' : 'Back to classes' }}</a></p>
</div>
@endsection
