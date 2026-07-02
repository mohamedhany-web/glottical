@extends('layouts.admin')

@section('title', __('student.one_to_one_admin_session', ['id' => $session->id]))
@section('header', __('student.one_to_one_admin_session', ['id' => $session->id]))

@section('content')
<div class="space-y-6 max-w-5xl">
    <a href="{{ route('admin.one-to-one-sessions.index') }}" class="text-sm text-sky-600 hover:underline">← {{ __('student.one_to_one_admin_title') }}</a>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap justify-between gap-2">
            <h2 class="text-xl font-bold text-slate-900">{{ $session->course->title ?? '—' }}</h2>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm font-semibold">{{ $session->statusLabel() }}</span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-slate-500">{{ __('student.one_to_one_col_student') }}</dt>
                <dd class="font-semibold">{{ $session->student->name ?? '—' }} <span class="text-slate-400 font-normal">{{ $session->student->email ?? '' }}</span></dd>
            </div>
            <div>
                <dt class="text-slate-500">{{ __('student.one_to_one_col_instructor') }}</dt>
                <dd class="font-semibold">{{ $session->instructor->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">{{ __('student.one_to_one_col_session') }}</dt>
                <dd class="font-semibold">{{ __('student.one_to_one_session_number', ['n' => $session->session_number]) }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">{{ __('student.one_to_one_appointment') }}</dt>
                <dd class="font-semibold">
                    @if($session->scheduled_at)
                        {{ $session->scheduled_at->format('Y-m-d H:i') }}
                        ({{ (int) $session->duration_minutes }} {{ __('student.minutes') }})
                    @else
                        {{ __('student.one_to_one_pending_schedule') }}
                    @endif
                </dd>
            </div>
            @if($session->bookedBy)
            <div>
                <dt class="text-slate-500">{{ __('student.one_to_one_booked_by') }}</dt>
                <dd class="font-semibold">{{ $session->bookedBy->name }}</dd>
            </div>
            @endif
            @if($session->classroomMeeting)
            <div class="sm:col-span-2">
                <dt class="text-slate-500">{{ __('student.one_to_one_join_session') }}</dt>
                <dd>
                    @php $joinUrl = $session->joinUrl(); @endphp
                    @if($joinUrl)
                        <a href="{{ $joinUrl }}" target="_blank" rel="noopener" class="text-sky-600 font-semibold hover:underline">{{ $joinUrl }}</a>
                    @else
                        <span class="text-slate-500">—</span>
                    @endif
                </dd>
            </div>
            @endif
            @if($session->notes)
            <div class="sm:col-span-2">
                <dt class="text-slate-500">{{ __('student.one_to_one_notes') }}</dt>
                <dd class="text-slate-800 whitespace-pre-line">{{ $session->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-900">{{ __('student.one_to_one_instructor_schedule') }}</h3>
        @if($availability->isEmpty())
            <p class="text-sm text-amber-700">{{ __('student.one_to_one_no_availability_rules') }}</p>
        @else
            <div class="space-y-2 text-sm">
                @foreach($dayLabels as $day => $label)
                    @php $dayRules = $availability->where('day_of_week', $day); @endphp
                    @if($dayRules->isNotEmpty())
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="font-semibold w-24">{{ $label }}</span>
                            @foreach($dayRules as $rule)
                                <span class="px-2 py-1 rounded-lg bg-violet-100 text-violet-800 text-xs font-medium">
                                    {{ substr((string) $rule->start_time, 0, 5) }} – {{ substr((string) $rule->end_time, 0, 5) }}
                                    ({{ (int) $rule->slot_duration_minutes }} {{ __('student.minutes') }})
                                </span>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @if($upcomingSlots->isNotEmpty())
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-3">
        <h3 class="font-bold text-slate-900">{{ __('student.one_to_one_upcoming_slots') }}</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($upcomingSlots->take(24) as $slot)
                <span class="px-2 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                    {{ $slot['starts_at']->format('Y-m-d H:i') }}
                </span>
            @endforeach
        </div>
        @if($upcomingSlots->count() > 24)
            <p class="text-xs text-slate-500">{{ __('student.one_to_one_more_slots', ['n' => $upcomingSlots->count() - 24]) }}</p>
        @endif
    </div>
    @endif
</div>
@endsection
