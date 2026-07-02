@extends('layouts.app')

@section('title', __('student.one_to_one_sessions_title'))

@section('content')
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @error('scheduled_at')
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ $message }}</div>
    @enderror

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
                <dt class="text-slate-500">{{ __('student.one_to_one_appointment') }}</dt>
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

        @if($session->status === \App\Models\OneToOneSession::STATUS_PENDING)
            <div class="rounded-xl border border-violet-200 dark:border-violet-800 bg-violet-50 dark:bg-violet-900/20 p-4 space-y-4">
                <h2 class="font-bold text-violet-900 dark:text-violet-100">{{ __('student.one_to_one_pick_slot') }}</h2>
                @if($availableSlots->isEmpty())
                    <p class="text-sm text-violet-800/80 dark:text-violet-200/80">{{ __('student.one_to_one_no_slots') }}</p>
                @else
                    <form method="POST" action="{{ route('student.one-to-one-sessions.book', $session) }}" class="space-y-3 max-h-80 overflow-y-auto">
                        @csrf
                        @php $grouped = $availableSlots->groupBy(fn ($s) => $s['starts_at']->format('Y-m-d')); @endphp
                        @foreach($grouped as $date => $daySlots)
                            <div>
                                <p class="text-xs font-bold text-violet-700 dark:text-violet-300 mb-2">{{ \Carbon\Carbon::parse($date)->locale(app()->getLocale())->isoFormat('dddd D MMMM') }}</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($daySlots as $slot)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="scheduled_at" value="{{ $slot['starts_at']->format('Y-m-d H:i:s') }}" class="peer sr-only" required>
                                            <span class="inline-flex px-3 py-2 rounded-lg border border-violet-200 dark:border-violet-700 text-sm font-semibold text-violet-900 dark:text-violet-100 peer-checked:bg-violet-600 peer-checked:text-white peer-checked:border-violet-600 hover:bg-violet-100 dark:hover:bg-violet-900/40 transition">
                                                {{ $slot['starts_at']->format('H:i') }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <button type="submit" class="w-full sm:w-auto mt-2 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold">
                            <i class="fas fa-calendar-check {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('student.one_to_one_confirm_booking') }}
                        </button>
                    </form>
                @endif
            </div>
        @endif

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
