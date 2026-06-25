@extends('layouts.app')

@section('title', __('student.one_to_one_sessions_title'))

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('dashboard') }}" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium">{{ __('auth.dashboard') }}</a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <span class="text-gray-900 dark:text-gray-200 font-semibold">{{ __('student.one_to_one_sessions_title') }}</span>
    </div>

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('student.one_to_one_sessions_title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('student.one_to_one_sessions_subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-600 dark:text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ __('public.courses') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('landing.nav.instructors') }}</th>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right">{{ __('public.checkout_step_3_title') ?? 'الحالة' }}</th>
                        <th class="px-4 py-3 text-right">{{ __('public.checkout_step_2_title') ?? 'الموعد' }}</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $session->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $session->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ __('student.one_to_one_session_number', ['n' => $session->session_number]) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-md text-xs font-medium
                                    @if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED) bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200
                                    @elseif($session->status === \App\Models\OneToOneSession::STATUS_COMPLETED) bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200
                                    @elseif($session->status === \App\Models\OneToOneSession::STATUS_PENDING) bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200
                                    @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ $session->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @if($session->scheduled_at)
                                    {{ $session->scheduled_at->format('Y-m-d H:i') }}
                                @else
                                    {{ __('student.one_to_one_pending_schedule') }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('student.one-to-one-sessions.show', $session) }}" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline">{{ __('public.view_details') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">{{ __('student.one_to_one_sessions_empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $sessions->links() }}</div>
    </div>
</div>
@endsection
