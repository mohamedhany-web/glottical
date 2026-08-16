@extends('layouts.app')
@section('title', 'جلسات البث المباشر')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                <i class="fas fa-broadcast-tower text-red-500 ml-2"></i>جلسات البث المباشر
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">إنشاء وإدارة جلسات البث المباشر لطلابك</p>
        </div>
        <a href="{{ route('instructor.live-sessions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 dark:bg-red-700 hover:bg-red-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/25 transition-all">
            <i class="fas fa-plus"></i> جلسة بث جديدة
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
            <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">إجمالي الجلسات</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-red-200 dark:border-red-900/50">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 dark:bg-red-700 rounded-full animate-pulse"></span>
                <p class="text-2xl font-bold text-red-600">{{ $stats['live'] }}</p>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">مباشر الآن</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-blue-200 dark:border-blue-900/50">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['scheduled'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">مجدولة</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-emerald-200 dark:border-emerald-900/50">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['ended'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">منتهية</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('instructor.live-sessions.index') }}" class="px-3 py-1.5 rounded-lg text-sm {{ !request('status') ? 'bg-slate-800 dark:bg-white dark:bg-slate-800/95 text-white dark:text-slate-800 dark:text-slate-100 font-semibold' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }} transition-colors">الكل</a>
        <a href="{{ route('instructor.live-sessions.index', ['status' => 'live']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'live' ? 'bg-red-600 dark:bg-red-700 text-white font-semibold' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }} transition-colors">مباشر</a>
        <a href="{{ route('instructor.live-sessions.index', ['status' => 'scheduled']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'scheduled' ? 'bg-blue-600 dark:bg-blue-700 text-white font-semibold' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }} transition-colors">مجدولة</a>
        <a href="{{ route('instructor.live-sessions.index', ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'ended' ? 'bg-slate-600 text-white font-semibold' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }} transition-colors">منتهية</a>
    </div>

    {{-- Sessions List --}}
    <div class="space-y-3">
        @forelse($sessions as $session)
        <div class="bg-white dark:bg-slate-800 rounded-xl border {{ $session->isLive() ? 'border-red-300 dark:border-red-800 ring-1 ring-red-200 dark:ring-red-900/50' : 'border-slate-200 dark:border-slate-700' }} p-5 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    @if($session->isLive())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 text-xs font-bold"><span class="w-1.5 h-1.5 bg-red-600 dark:bg-red-700 rounded-full animate-pulse"></span> مباشر</span>
                    @elseif($session->isScheduled())
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 text-xs font-medium">مجدولة</span>
                    @elseif($session->isEnded())
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-600 text-slate-500 dark:text-slate-400 text-xs font-medium">منتهية</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-600 text-xs font-medium">ملغاة</span>
                    @endif
                    @if($session->course)
                        <span class="text-xs text-slate-400">{{ Str::limit($session->course->title, 30) }}</span>
                    @endif
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white truncate">{{ $session->title }}</h3>
                <div class="flex items-center gap-4 mt-1 text-xs text-slate-500 dark:text-slate-400">
                    <span><i class="fas fa-calendar ml-1"></i>@if($session->scheduled_at)<x-app-datetime :at="$session->scheduled_at" pattern="Y/m/d H:i" />@else — @endif</span>
                    <span><i class="fas fa-users ml-1"></i>{{ $session->attendance_count }} حاضر</span>
                    @if($session->duration_minutes)
                        <span><i class="fas fa-clock ml-1"></i>{{ $session->duration_for_humans }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($session->isLive())
                    <a href="{{ route('instructor.live-sessions.room', $session) }}" class="px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-600 text-white rounded-lg text-sm font-semibold shadow-lg shadow-red-500/25 transition-all">
                        <i class="fas fa-video ml-1"></i> دخول البث
                    </a>
                    <form method="POST" action="{{ route('instructor.live-sessions.end', $session) }}" onsubmit="return confirm('إنهاء الجلسة؟')">
                        @csrf
                        <button class="px-3 py-2 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-sm hover:bg-slate-300 transition-colors"><i class="fas fa-stop"></i></button>
                    </form>
                @elseif($session->isScheduled())
                    <form method="POST" action="{{ route('instructor.live-sessions.start', $session) }}">
                        @csrf
                        <button class="px-4 py-2 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold shadow-lg shadow-emerald-500/25 transition-all">
                            <i class="fas fa-play ml-1"></i> بدء البث
                        </button>
                    </form>
                @endif
                <a href="{{ route('instructor.live-sessions.show', $session) }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-sm hover:bg-slate-200 transition-colors" title="تفاصيل"><i class="fas fa-eye"></i></a>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <i class="fas fa-broadcast-tower text-5xl text-slate-300 dark:text-slate-600 dark:text-slate-400 mb-4"></i>
            <p class="text-lg font-semibold text-slate-600 dark:text-slate-400 mb-2">لا توجد جلسات بث بعد</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">أنشئ أول جلسة بث مباشر لطلابك</p>
            <a href="{{ route('instructor.live-sessions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 dark:bg-red-700 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors">
                <i class="fas fa-plus"></i> إنشاء جلسة جديدة
            </a>
        </div>
        @endforelse
    </div>

    @if($sessions->hasPages())
    <div>{{ $sessions->links() }}</div>
    @endif
</div>
@endsection
