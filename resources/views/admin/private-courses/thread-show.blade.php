@extends('layouts.admin')

@section('title', 'محادثة بريفيت')
@section('page_title', 'محادثة بريفيت')

@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ route('admin.private-courses.threads') }}" class="text-sm font-bold text-accent">← كل المحادثات</a>
    <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
        <p class="text-sm text-muted">{{ $thread->student->name ?? '' }} ↔ {{ $thread->instructor->name ?? '' }}</p>
    </div>
    <div class="rounded-2xl border border-line bg-surface p-4 space-y-3 shadow-soft">
        @foreach($thread->messages as $msg)
            <div class="rounded-xl px-3 py-2 {{ $msg->is_internal_note ? 'bg-amber-50 border border-amber-200' : 'bg-[#F4F7FC]' }}">
                <p class="text-[11px] font-bold text-muted">
                    {{ $msg->sender->name ?? '' }} · {{ $msg->sender_role }}
                    @if($msg->is_internal_note) · ملاحظة داخلية @endif
                    · {{ $msg->created_at?->format('Y-m-d H:i') }}
                </p>
                <p class="text-sm whitespace-pre-wrap">{{ $msg->body }}</p>
            </div>
        @endforeach
    </div>
    <form method="post" action="{{ route('admin.private-courses.threads.reply', $thread) }}" class="rounded-2xl border border-line bg-surface p-4 space-y-3 shadow-soft">
        @csrf
        <textarea name="body" rows="3" required class="w-full rounded-xl border border-line px-3 py-2 text-sm"></textarea>
        <label class="flex items-center gap-2 text-xs text-muted">
            <input type="checkbox" name="is_internal_note" value="1"> ملاحظة داخلية (للإدارة فقط)
        </label>
        <button class="rounded-xl bg-accent px-4 py-2 text-sm font-bold text-white">إرسال</button>
    </form>
</div>
@endsection
