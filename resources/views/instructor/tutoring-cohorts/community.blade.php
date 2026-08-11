@extends('layouts.app')

@section('title', (app()->getLocale() === 'ar' ? 'مجتمع الفصل' : 'Class community').' · '.$cohort->title)
@section('page_title', app()->getLocale() === 'ar' ? 'مجتمع الفصل' : 'Class community')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $feedPosts = $feedPosts ?? collect();
    $canModerateFeed = $canModerateFeed ?? true;
@endphp

<div class="mx-auto max-w-3xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('instructor.tutoring-cohorts.show', $cohort) }}" class="text-sm font-bold text-accent hover:underline">
            ← {{ $isRtl ? 'مركز قيادة الدفعة' : 'Cohort command' }}
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl border border-line bg-gradient-to-br from-[#0B3D91] to-[#072A66] p-5 text-white shadow-soft">
        <p class="text-xs font-bold uppercase tracking-wide text-white/70">{{ $cohort->title }}</p>
        <h2 class="mt-1 text-2xl font-black">{{ $isRtl ? 'مجتمع الفصل' : 'Class community' }}</h2>
        <p class="mt-1 text-sm text-white/85">{{ $isRtl ? 'أسئلة الطلاب وإعلاناتك — نفس المجتمع الذي يراه الطلاب.' : 'Student questions and your announcements — the same feed students see.' }}</p>
    </section>

    <article class="rounded-2xl border border-line bg-white p-4 shadow-soft sm:p-5">
        <form method="POST" action="{{ route('instructor.tutoring-cohorts.feed.store', $cohort) }}" class="space-y-3">
            @csrf
            <textarea name="body" rows="3" maxlength="1000" required class="w-full rounded-xl border border-line px-3 py-2 text-sm"
                      placeholder="{{ $isRtl ? 'اكتب إعلاناً أو رداً للفصل…' : 'Write an announcement or reply…' }}"></textarea>
            <div class="flex flex-wrap items-center gap-3">
                <select name="post_type" class="h-9 rounded-xl border border-line px-3 text-sm">
                    <option value="announcement">{{ $isRtl ? 'إعلان' : 'Announcement' }}</option>
                    <option value="question">{{ $isRtl ? 'منشور' : 'Post' }}</option>
                </select>
                <label class="inline-flex items-center gap-2 text-xs font-bold text-muted">
                    <input type="checkbox" name="is_pinned" value="1" class="rounded border-line"> {{ $isRtl ? 'تثبيت' : 'Pin' }}
                </label>
                <button class="ms-auto inline-flex h-9 items-center rounded-xl bg-accent px-4 text-xs font-black text-white">
                    {{ $isRtl ? 'نشر' : 'Publish' }}
                </button>
            </div>
        </form>
    </article>

    <ul class="space-y-3">
        @forelse($feedPosts as $post)
            <li class="rounded-2xl border {{ $post->is_hidden ? 'border-red-200 bg-red-50' : 'border-line bg-white' }} p-4 shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="font-bold text-ink">{{ $post->author?->name }} · {{ $post->typeLabel() }}</p>
                        <p class="text-[11px] font-semibold text-muted">{{ $post->created_at?->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
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
                <p class="mt-3 whitespace-pre-wrap text-sm text-ink">{{ $post->body }}</p>

                @if($post->visibleComments && $post->visibleComments->isNotEmpty())
                    <ul class="mt-3 space-y-2 border-t border-line pt-3">
                        @foreach($post->visibleComments as $comment)
                            <li class="rounded-xl bg-slate-50 px-3 py-2 text-sm">
                                <strong>{{ $comment->author?->name }}</strong>
                                <span class="text-muted">· {{ $comment->created_at?->diffForHumans() }}</span>
                                <p class="mt-1 whitespace-pre-wrap">{{ $comment->body }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('instructor.class-feed.comment', $post) }}" class="mt-3 flex flex-wrap gap-2">
                    @csrf
                    <input type="text" name="body" required maxlength="1000" class="h-9 min-w-[200px] flex-1 rounded-xl border border-line px-3 text-sm"
                           placeholder="{{ $isRtl ? 'أضف تعليقاً…' : 'Add a comment…' }}">
                    <button class="h-9 rounded-xl border border-line px-3 text-xs font-bold">{{ $isRtl ? 'تعليق' : 'Comment' }}</button>
                </form>
            </li>
        @empty
            <li class="rounded-2xl border border-dashed border-line px-4 py-10 text-center text-sm text-muted">
                {{ $isRtl ? 'لا منشورات بعد في مجتمع هذا الفصل.' : 'No posts in this class community yet.' }}
            </li>
        @endforelse
    </ul>
</div>
@endsection
