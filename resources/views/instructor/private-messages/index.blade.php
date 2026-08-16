@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'رسائل الطلاب' : 'Student messages')
@section('page_title', app()->getLocale() === 'ar' ? 'رسائل الطلاب' : 'Student messages')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $avatarFallback = \App\Models\User::placeholderAvatarUrl();
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-ink">{{ $isRtl ? 'الرسائل الخاصة' : 'Private messages' }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $isRtl ? 'محادثات 1:1 مع طلاب الحصص الخاصة. عند إرسال رسالة يصل إشعار وبريد للطالب حتى لو لم يكن على المنصة.' : '1:1 chats with private-lesson students. Sending a message notifies the student in-app and by email if they are offline.' }}</p>
        </div>
    </section>

    <form method="get" class="flex flex-wrap gap-2">
        <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ $isRtl ? 'بحث باسم الطالب…' : 'Search by student…' }}"
               class="h-10 min-w-[220px] flex-1 rounded-xl border border-line bg-surface px-3 text-sm">
        <button class="h-10 rounded-xl bg-accent px-4 text-sm font-bold text-white">{{ $isRtl ? 'بحث' : 'Search' }}</button>
    </form>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <ul class="divide-y divide-line">
            @forelse($threads as $thread)
                @php
                    $student = $thread->student;
                    $name = $student?->name ?: ($isRtl ? 'طالب' : 'Student');
                    $preview = optional($thread->messages->first())->body;
                    $avatar = ($student && $student->profile_image) ? $student->profile_image_url : $avatarFallback;
                @endphp
                <li>
                    <a href="{{ route('instructor.private-messages.show', $thread) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                        <img src="{{ $avatar }}" alt="" class="h-11 w-11 rounded-full object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate font-bold text-ink">{{ $name }}</p>
                                <span class="shrink-0 text-[11px] text-muted">{{ $thread->last_message_at?->diffForHumans() }}</span>
                            </div>
                            <p class="truncate text-sm text-muted">{{ \Illuminate\Support\Str::limit($preview ?: ($isRtl ? 'ابدأ المحادثة' : 'Start chatting'), 80) }}</p>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-10 text-center text-sm text-muted">{{ $isRtl ? 'لا محادثات بعد.' : 'No conversations yet.' }}</li>
            @endforelse
        </ul>
    </div>

    @if(method_exists($threads, 'hasPages') && $threads->hasPages())
        <div>{{ $threads->links() }}</div>
    @endif
</div>
@endsection
