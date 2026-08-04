@extends('layouts.app')

@section('title', 'رسائل المعلم')
@section('page_title', 'رسائل المعلم')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="rounded-2xl border border-[#E8EEF8] bg-white p-4">
        <a href="{{ route('student.private-lectures.index') }}" class="text-sm font-bold text-[#0B3D91]">← المحاضرات الخاصة</a>
        <h1 class="mt-2 text-lg font-extrabold">محادثة مع {{ $thread->instructor->name ?? 'المعلم' }}</h1>
        <p class="text-xs text-[#8A94A6]">التواصل مرئي للإدارة ضمن نظام كورسات بريفيت.</p>
    </div>

    <div class="rounded-2xl border border-[#E8EEF8] bg-white p-4 space-y-3 min-h-[280px]">
        @forelse($thread->messages->where('is_internal_note', false) as $msg)
            <div class="rounded-xl px-3 py-2 {{ (int)$msg->sender_id === (int)auth()->id() ? 'bg-[#E8EEF8] ms-8' : 'bg-[#F4F7FC] me-8' }}">
                <p class="text-[11px] font-bold text-[#8A94A6]">{{ $msg->sender->name ?? '' }} · {{ $msg->created_at?->format('Y-m-d H:i') }}</p>
                <p class="text-sm text-[#0B1220] whitespace-pre-wrap">{{ $msg->body }}</p>
            </div>
        @empty
            <p class="text-sm text-[#8A94A6]">لا رسائل بعد — ابدأ المحادثة.</p>
        @endforelse
    </div>

    <form method="post" action="{{ route('student.private-messages.send', $thread) }}" class="rounded-2xl border border-[#E8EEF8] bg-white p-4 space-y-3">
        @csrf
        <textarea name="body" rows="3" required class="w-full rounded-xl border border-[#D7DDE6] px-3 py-2 text-sm" placeholder="اكتب رسالتك للمعلم…"></textarea>
        <button type="submit" class="rounded-xl bg-[#0B3D91] px-4 py-2 text-sm font-bold text-white">إرسال</button>
    </form>
</div>
@endsection
