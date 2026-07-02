@extends('layouts.app')

@section('title', __('student.one_to_one_schedule_session'))

@section('content')
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <a href="{{ route('instructor.one-to-one-sessions.index') }}" class="text-sm text-sky-600 hover:underline">← {{ __('student.one_to_one_sessions_instructor_nav') }}</a>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-start gap-2">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">{{ $session->course->title ?? '—' }}</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $session->student->name ?? 'طالب' }} — {{ __('student.one_to_one_session_number', ['n' => $session->session_number]) }}</p>
            </div>
            <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-semibold">{{ $session->statusLabel() }}</span>
        </div>

        @if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED && $session->classroomMeeting)
            @php $m = $session->classroomMeeting; @endphp
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 space-y-3">
                <p class="font-bold text-emerald-900 dark:text-emerald-100">الموعد: {{ $session->scheduled_at?->format('Y-m-d H:i') }}</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('instructor.classroom.show', $m) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold">إعدادات الغرفة</a>
                    @if(!$m->ended_at)
                    <a href="{{ route('instructor.classroom.room', $m) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">دخول الغرفة</a>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('instructor.one-to-one-sessions.complete', $session) }}" onsubmit="return confirm('تأكيد إتمام الحصة؟')">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold">{{ __('student.one_to_one_mark_complete') }}</button>
            </form>
        @elseif($session->status === \App\Models\OneToOneSession::STATUS_PENDING)
            <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4 text-sm text-amber-900 dark:text-amber-100">
                {{ __('student.one_to_one_instructor_pending_hint') }}
                <a href="{{ route('instructor.one-to-one-availability.index') }}" class="font-bold text-sky-600 hover:underline block mt-2">{{ __('student.one_to_one_availability_title') }}</a>
            </div>
            <form method="POST" action="{{ route('instructor.one-to-one-sessions.schedule', $session) }}" class="space-y-4 border-t border-slate-100 dark:border-slate-700 pt-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">تاريخ ووقت الحصة</label>
                    <input type="datetime-local" name="scheduled_at" required
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5"
                           min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                    @error('scheduled_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">المدة (دقيقة)</label>
                    <input type="number" name="duration_minutes" value="60" min="30" max="180" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5">
                </div>
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold">{{ __('student.one_to_one_schedule_session') }}</button>
            </form>
        @endif
    </div>
</div>
@endsection
