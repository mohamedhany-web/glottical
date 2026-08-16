@extends('layouts.app')
@section('title', $liveSession->title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('instructor.live-sessions.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 transition-colors"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $liveSession->title }}</h1>
                <p class="text-sm text-slate-400 font-mono">{{ $liveSession->room_name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($liveSession->isLive())
                <a href="{{ route('instructor.live-sessions.room', $liveSession) }}" class="px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-600 text-white rounded-lg text-sm font-semibold shadow-lg shadow-red-500/25 transition-all">
                    <i class="fas fa-video ml-1"></i> دخول البث
                </a>
            @elseif($liveSession->isScheduled())
                <form method="POST" action="{{ route('instructor.live-sessions.start', $liveSession) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold shadow-lg shadow-emerald-500/25 transition-all">
                        <i class="fas fa-play ml-1"></i> بدء البث الآن
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($liveSession->isLive())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
        <span class="w-3 h-3 bg-red-600 dark:bg-red-700 rounded-full animate-pulse"></span>
        <span class="font-semibold text-red-700 dark:text-red-400">البث مباشر الآن — بدأ {{ $liveSession->started_at?->diffForHumans() }}</span>
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="font-bold text-slate-800 dark:text-white mb-4"><i class="fas fa-info-circle text-blue-500 ml-2"></i>تفاصيل الجلسة</h2>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-slate-500 dark:text-slate-400">الكورس:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">{{ $liveSession->course?->title ?? 'جلسة عامة' }}</span></div>
                    <div><span class="text-slate-500 dark:text-slate-400">الموعد:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2"><x-app-datetime :at="$liveSession->scheduled_at" pattern="Y/m/d H:i" /></span></div>
                    <div><span class="text-slate-500 dark:text-slate-400">المدة:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">{{ $liveSession->duration_for_humans }}</span></div>
                    <div><span class="text-slate-500 dark:text-slate-400">الحد الأقصى:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">{{ $liveSession->max_participants }}</span></div>
                </div>
                @if($liveSession->description)
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $liveSession->description }}</p>
                </div>
                @endif
            </div>

            {{-- Attendance --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="font-bold text-slate-800 dark:text-white mb-4">
                    <i class="fas fa-users text-emerald-500 ml-2"></i>الحضور ({{ $attendees->count() }})
                </h2>
                @if($attendees->count() > 0)
                <div class="space-y-2">
                    @foreach($attendees as $att)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-{{ $att->role_in_session === 'instructor' ? 'blue' : 'slate' }}-100 dark:bg-slate-600 flex items-center justify-center">
                                <i class="fas fa-{{ $att->role_in_session === 'instructor' ? 'chalkboard-teacher' : 'user-graduate' }} text-xs text-{{ $att->role_in_session === 'instructor' ? 'blue' : 'slate' }}-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $att->user?->name }}</p>
                                <p class="text-[11px] text-slate-400">دخل {{ $att->joined_at?->format('H:i') }} {{ $att->left_at ? '— خرج ' . $att->left_at->format('H:i') : '' }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $att->duration_for_humans }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-slate-500 dark:text-slate-400 py-4">لا يوجد حضور بعد</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 text-center">
                @if($liveSession->isScheduled())
                    <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-xl text-blue-500"></i>
                    </div>
                    <p class="font-semibold text-slate-800 dark:text-white mb-1">الجلسة مجدولة</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $liveSession->scheduled_at?->diffForHumans() }}</p>
                @elseif($liveSession->isLive())
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                        <span class="w-4 h-4 bg-red-600 dark:bg-red-700 rounded-full animate-pulse"></span>
                    </div>
                    <p class="font-bold text-red-600 mb-1">مباشر الآن</p>
                @elseif($liveSession->isEnded())
                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check text-xl text-emerald-500"></i>
                    </div>
                    <p class="font-semibold text-slate-800 dark:text-white mb-1">انتهت الجلسة</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">المدة: {{ $liveSession->duration_for_humans }}</p>
                @endif
            </div>

            @if($liveSession->recordings->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="font-bold text-slate-800 dark:text-white mb-3 text-sm"><i class="fas fa-play-circle text-emerald-500 ml-1"></i> التسجيلات</h3>
                @foreach($liveSession->recordings as $rec)
                <div class="p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $rec->title ?? 'تسجيل' }}</span>
                    @if($rec->getUrl())
                    <a href="{{ $rec->getUrl() }}" target="_blank" class="text-blue-500 text-xs"><i class="fas fa-external-link-alt"></i></a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
