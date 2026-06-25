@extends('layouts.app')

@section('title', __('student.one_to_one_sessions_title'))

@section('content')
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <a href="{{ route('student.one-to-one-sessions.index') }}" class="text-sm text-sky-600 hover:underline">← {{ __('student.one_to_one_sessions_nav') }}</a>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-start gap-2">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">{{ $session->course->title ?? __('student.one_to_one_sessions_title') }}</h1>
                <p class="text-sm text-slate-500 mt-1">{{ __('student.one_to_one_session_number', ['n' => $session->session_number]) }}</p>
            </div>
            <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-semibold">{{ $session->statusLabel() }}</span>
        </div>

        <dl class="text-sm space-y-2">
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2">
                <dt class="text-slate-500">{{ __('landing.nav.instructors') }}</dt>
                <dd class="font-bold text-slate-900 dark:text-white">{{ $session->instructor->name ?? '—' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2">
                <dt class="text-slate-500">{{ __('public.checkout_step_2_title') ?? 'الموعد' }}</dt>
                <dd class="font-bold">
                    @if($session->scheduled_at)
                        {{ $session->scheduled_at->format('Y-m-d H:i') }}
                        <span class="text-slate-500 font-normal">({{ (int) $session->duration_minutes }} {{ __('student.minutes') }})</span>
                    @else
                        {{ __('student.one_to_one_pending_schedule') }}
                    @endif
                </dd>
            </div>
        </dl>

        @if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED && $session->classroomMeeting)
            @php $joinUrl = $session->joinUrl(); @endphp
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 space-y-3">
                <p class="font-bold text-emerald-900 dark:text-emerald-100">{{ __('student.one_to_one_join_session') }}</p>
                @if($joinUrl)
                    <a href="{{ $joinUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">
                        <i class="fas fa-video"></i> {{ __('student.one_to_one_join_session') }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
