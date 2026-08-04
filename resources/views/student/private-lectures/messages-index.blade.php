@extends('layouts.app')

@section('title', 'رسائل المعلمين')
@section('page_title', 'رسائل المعلمين')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-[#E8EEF8] bg-white p-5">
        <h1 class="text-lg font-extrabold">رسائل كورسات بريفيت</h1>
        <p class="text-sm text-[#5B6577]">تواصل مباشر مع معلمك — الإدارة تستطيع متابعة المحادثات.</p>
    </div>
    <div class="rounded-2xl border border-[#E8EEF8] bg-white divide-y divide-[#E8EEF8]">
        @forelse($threads as $thread)
            <a href="{{ route('student.private-messages.show', $thread) }}" class="flex items-center justify-between gap-3 px-5 py-4 hover:bg-[#F4F7FC]">
                <div>
                    <p class="font-bold text-[#0B1220]">{{ $thread->instructor->name ?? 'معلم' }}</p>
                    <p class="text-xs text-[#8A94A6]">{{ $thread->subject }} · {{ $thread->last_message_at?->diffForHumans() ?? 'بدون رسائل' }}</p>
                </div>
                <i class="fas fa-chevron-left text-[#8A94A6] text-xs"></i>
            </a>
        @empty
            <p class="px-5 py-8 text-sm text-[#8A94A6]">لا محادثات بعد. تُفتح المحادثة تلقائياً عند تسكينك مع معلم.</p>
        @endforelse
    </div>
</div>
@endsection
