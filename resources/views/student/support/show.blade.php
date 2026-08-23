@extends('layouts.student-timeline')

@section('title', 'تفاصيل تذكرة الدعم')
@section('header', 'تفاصيل تذكرة الدعم')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">{{ $ticket->subject }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    التصنيف: {{ $ticket->inquiryCategory->name ?? '—' }}
                    <span class="mx-1">|</span>
                    الحالة: {{ $ticket->status }}
                    <span class="mx-1">|</span>
                    الأولوية: {{ $ticket->priority }}
                </p>
            </div>
            <a href="{{ route('student.support.index') }}" class="text-sm text-sky-600 hover:underline">العودة للتذاكر</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-3">
        @foreach($ticket->replies as $reply)
            <div class="rounded-xl p-3 {{ $reply->sender_type === 'admin' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200' }}">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-semibold text-slate-700">{{ $reply->sender_type === 'admin' ? 'فريق الدعم' : 'أنت' }}</p>
                    <p class="text-[11px] text-slate-500">{{ $reply->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $reply->message }}</p>
            </div>
        @endforeach
    </div>

    @if(!in_array($ticket->status, ['resolved', 'closed'], true))
        <form action="{{ route('student.support.reply', $ticket) }}" method="POST" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-3">
            @csrf
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">إضافة رد</label>
            <textarea name="message" rows="4" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">{{ old('message') }}</textarea>
            @error('message')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            <div class="text-left">
                <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">إرسال الرد</button>
            </div>
        </form>
    @endif
</div>
@endsection

