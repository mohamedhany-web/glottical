@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إشعارات المعلم' : 'Instructor notifications')
@section('page_title', app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-ink">{{ $isRtl ? 'الإشعارات' : 'Notifications' }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $isRtl ? 'رسائل الطلاب والتحديثات — يصل أيضاً بريد إلكتروني إن لم تكن على المنصة.' : 'Student messages and updates — also emailed when you are offline.' }}</p>
        </div>
        <form method="post" action="{{ route('instructor.notifications.mark-all-read') }}">
            @csrf
            <button class="h-9 rounded-xl border border-line px-3 text-sm font-bold">{{ $isRtl ? 'تعليمليم الكل كمقروء' : 'Mark all read' }}</button>
        </form>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <ul class="space-y-2">
        @forelse($notifications as $n)
            <li class="rounded-2xl border border-line bg-white px-4 py-3 shadow-soft {{ $n->is_read ? '' : 'ring-2 ring-[#F5B800]/40' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-ink">{{ $n->title }}</p>
                        <p class="mt-1 text-sm text-muted">{{ $n->message }}</p>
                        <p class="mt-2 text-[11px] font-semibold text-muted">{{ $n->created_at?->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($n->action_url)
                            <a href="{{ route('instructor.notifications.go', $n) }}" class="inline-flex h-9 items-center rounded-xl bg-accent px-3 text-xs font-bold text-white">
                                {{ $n->action_text ?: ($isRtl ? 'فتح' : 'Open') }}
                            </a>
                        @endif
                        @unless($n->is_read)
                            <form method="post" action="{{ route('instructor.notifications.mark-read', $n) }}">@csrf
                                <button class="h-9 rounded-xl border border-line px-3 text-xs font-bold">{{ $isRtl ? 'مقروء' : 'Read' }}</button>
                            </form>
                        @endunless
                    </div>
                </div>
            </li>
        @empty
            <li class="rounded-2xl border border-dashed border-line px-4 py-10 text-center text-sm text-muted">
                {{ $isRtl ? 'لا إشعارات حالياً.' : 'No notifications yet.' }}
            </li>
        @endforelse
    </ul>

    @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
