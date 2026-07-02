@extends('layouts.admin')

@section('title', __('student.one_to_one_admin_title'))
@section('header', __('student.one_to_one_admin_title'))

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="rounded-xl bg-white border border-amber-200 bg-amber-50/40 p-4">
            <p class="text-xs text-amber-700">{{ __('student.one_to_one_pending_schedule') }}</p>
            <p class="text-2xl font-bold text-amber-800">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-xl bg-white border border-emerald-200 bg-emerald-50/40 p-4">
            <p class="text-xs text-emerald-700">{{ \App\Models\OneToOneSession::statusLabels()[\App\Models\OneToOneSession::STATUS_SCHEDULED] }}</p>
            <p class="text-2xl font-bold text-emerald-800">{{ $stats['scheduled'] }}</p>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs text-slate-500">{{ \App\Models\OneToOneSession::statusLabels()[\App\Models\OneToOneSession::STATUS_COMPLETED] }}</p>
            <p class="text-2xl font-bold">{{ $stats['completed'] }}</p>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <select name="status" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>{{ __('student.one_to_one_filter_all') }}</option>
                    @foreach(\App\Models\OneToOneSession::statusLabels() as $key => $label)
                        <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($instructors->isNotEmpty())
                <select name="instructor_id" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <option value="0">{{ __('student.one_to_one_filter_instructor') }}</option>
                    @foreach($instructors as $ins)
                        <option value="{{ $ins->id }}" {{ (int) $instructorId === (int) $ins->id ? 'selected' : '' }}>{{ $ins->name }}</option>
                    @endforeach
                </select>
                @endif
                <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">{{ __('student.one_to_one_filter_apply') }}</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right">{{ __('student.one_to_one_col_student') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('student.one_to_one_col_instructor') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('student.one_to_one_col_course') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('student.one_to_one_col_session') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('student.one_to_one_appointment') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('student.one_to_one_col_status') }}</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $session)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $session->id }}</td>
                            <td class="px-4 py-3">{{ $session->student->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $session->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 max-w-[12rem] truncate" title="{{ $session->course->title ?? '' }}">{{ $session->course->title ?? '—' }}</td>
                            <td class="px-4 py-3">{{ __('student.one_to_one_session_number', ['n' => $session->session_number]) }}</td>
                            <td class="px-4 py-3">
                                @if($session->scheduled_at)
                                    {{ $session->scheduled_at->format('Y-m-d H:i') }}
                                @else
                                    <span class="text-amber-600">{{ __('student.one_to_one_pending_schedule') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-xs font-semibold">{{ $session->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.one-to-one-sessions.show', $session) }}" class="text-sky-600 font-semibold hover:underline">{{ __('public.view_details') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-12 text-center text-slate-500">{{ __('student.one_to_one_sessions_empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sessions->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $sessions->links() }}</div>
        @endif
    </div>
</div>
@endsection
