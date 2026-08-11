@extends('layouts.app')

@section('title', $thread->student?->name ?: (app()->getLocale() === 'ar' ? 'محادثة' : 'Chat'))
@section('page_title', $thread->student?->name ?: (app()->getLocale() === 'ar' ? 'محادثة' : 'Chat'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $student = $thread->student;
    $name = $student?->name ?: ($isRtl ? 'طالب' : 'Student');
    $avatarFallback = asset('img/student-timeline/avatar.png');
    $avatar = ($student && $student->profile_image) ? $student->profile_image_url : $avatarFallback;
    $messages = $thread->messages->where('is_internal_note', false)->values();
    $meId = (int) auth()->id();
@endphp

<div class="mx-auto max-w-3xl space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('instructor.private-messages.index') }}" class="text-sm font-bold text-accent hover:underline">
            ← {{ $isRtl ? 'كل الرسائل' : 'All messages' }}
        </a>
        <a href="{{ route('instructor.notifications.index') }}" class="text-sm font-semibold text-muted hover:text-accent">
            {{ $isRtl ? 'الإشعارات' : 'Notifications' }}
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <article class="overflow-hidden rounded-2xl border border-line bg-white shadow-soft">
        <header class="flex items-center gap-3 border-b border-line px-4 py-3">
            <img src="{{ $avatar }}" alt="" class="h-11 w-11 rounded-full object-cover">
            <div>
                <h2 class="font-black text-ink">{{ $name }}</h2>
                <p class="text-xs font-semibold text-muted">{{ $thread->subject ?: ($isRtl ? 'محادثة خاصة' : 'Private chat') }}</p>
            </div>
        </header>

        <div class="max-h-[28rem] space-y-3 overflow-y-auto bg-slate-50 px-4 py-4">
            @forelse($messages as $msg)
                @php $mine = (int) $msg->sender_id === $meId; @endphp
                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-3.5 py-2.5 text-sm {{ $mine ? 'bg-[#0B3D91] text-white' : 'bg-white border border-line text-ink' }}">
                        <p class="text-[11px] font-bold opacity-70 mb-1">
                            {{ $msg->sender->name ?? '' }} · {{ $msg->created_at?->diffForHumans() }}
                        </p>
                        <p class="whitespace-pre-wrap">{{ $msg->body }}</p>
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-muted">{{ $isRtl ? 'لا رسائل بعد — اكتب أول رسالة.' : 'No messages yet — send the first one.' }}</p>
            @endforelse
        </div>

        <form method="post" action="{{ route('instructor.private-messages.send', $thread) }}" class="border-t border-line p-4 space-y-3">
            @csrf
            <textarea name="body" rows="3" required maxlength="5000" class="w-full rounded-xl border border-line px-3 py-2 text-sm"
                      placeholder="{{ $isRtl ? 'اكتب رسالة للطالب…' : 'Write a message…' }}">{{ old('body') }}</textarea>
            <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-bold text-white">
                <i class="fas fa-paper-plane text-xs"></i>
                {{ $isRtl ? 'إرسال' : 'Send' }}
            </button>
        </form>
    </article>
</div>
@endsection
