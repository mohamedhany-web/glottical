@extends('layouts.app')

@section('title', __('instructor.submissions_of') . ': ' . $assignment->title . ' - ' . config('app.name'))
@section('header', __('instructor.submissions_title'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
            <a href="{{ route('instructor.assignments.index') }}" class="hover:text-sky-600">{{ __('instructor.assignments') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.assignments.show', $assignment) }}" class="hover:text-sky-600">{{ $assignment->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ __('instructor.submissions_title') }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ __('instructor.submissions_of') }}: {{ $assignment->title }}</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">{{ __('instructor.max_score_points') }}: {{ $assignment->max_score }}</p>
            </div>
            <a href="{{ route('instructor.assignments.show', $assignment) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">
                <i class="fas fa-arrow-right"></i> {{ __('instructor.back_to_assignment') }}
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 font-bold text-slate-800 dark:text-slate-100">{{ __('instructor.submissions_list') }}</div>
        <div class="overflow-x-auto">
            @if($submissions->count() > 0)
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/40">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('instructor.student') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('instructor.submission_date') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('instructor.score_label') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('common.status') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('instructor.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-slate-50 dark:bg-slate-800/50">
                                <td class="px-4 py-3 text-sm text-slate-800 dark:text-slate-100">{{ $sub->student->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $sub->submitted_at?->format('Y/m/d H:i') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($sub->score !== null)
                                        <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $sub->score }}/{{ $assignment->max_score }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($sub->status === 'graded')
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">{{ __('instructor.graded_status') }}</span>
                                    @elseif($sub->status === 'returned')
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-sky-100 text-sky-700">{{ __('instructor.returned_status') }}</span>
                                    @else
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-amber-100 text-amber-700">{{ __('instructor.pending_review') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" onclick="toggleDetail({{ $sub->id }})" class="text-sky-600 hover:text-sky-700 text-sm font-semibold">
                                        <i class="fas fa-eye"></i> {{ __('instructor.view_grade') }}
                                    </button>
                                </td>
                            </tr>
                            <tr id="detail-{{ $sub->id }}" class="hidden bg-slate-50 dark:bg-slate-800/50">
                                <td colspan="5" class="px-4 py-4">
                                    <div class="space-y-4 max-w-3xl">
                                        @if($sub->content)
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.content_label') }}</h4>
                                                <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap rounded-lg bg-white dark:bg-slate-800/95 p-3 border border-slate-200 dark:border-slate-700">{{ $sub->content }}</div>
                                            </div>
                                        @endif
                                        @if($sub->attachments && count($sub->attachments) > 0)
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.attachments_label') }}</h4>
                                                <ul class="text-sm space-y-1">
                                                    @foreach($sub->attachments as $att)
                                                        @php
                                                            $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                                            $url = $path ? (\App\Services\AssignmentFileStorage::publicUrl($path) ?? (str_starts_with((string) $path, 'http') ? $path : url('storage/'.$path))) : '#';
                                                            $label = is_array($att) ? ($att['original_name'] ?? $att['name'] ?? basename($path ?? __('instructor.attachment_fallback'))) : basename($att);
                                                        @endphp
                                                        <li>
                                                            <a href="{{ $url }}" target="_blank" rel="noopener" class="text-sky-600 hover:underline">
                                                                {{ $label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        @if($sub->feedback)
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.feedback_label') }}</h4>
                                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $sub->feedback }}</p>
                                            </div>
                                        @endif
                                        <form action="{{ route('instructor.assignments.grade', [$assignment, $sub]) }}" method="POST" class="flex flex-wrap items-end gap-4 pt-2 border-t border-slate-200 dark:border-slate-700">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">{{ __('instructor.score_label') }} (0–{{ $assignment->max_score }})</label>
                                                <input type="number" name="score" min="0" max="{{ $assignment->max_score }}" value="{{ old('score', $sub->score) }}" class="w-24 px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100">
                                            </div>
                                            <div class="flex-1 min-w-[200px]">
                                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">{{ __('instructor.feedback_label') }}</label>
                                                <input type="text" name="feedback" value="{{ old('feedback', $sub->feedback) }}" placeholder="{{ __('instructor.optional_comment') }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">{{ __('common.status') }}</label>
                                                <select name="status" class="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100">
                                                    <option value="submitted" {{ $sub->status === 'submitted' ? 'selected' : '' }}>{{ __('instructor.pending_review') }}</option>
                                                    <option value="graded" {{ $sub->status === 'graded' ? 'selected' : '' }}>{{ __('instructor.graded_status') }}</option>
                                                    <option value="returned" {{ $sub->status === 'returned' ? 'selected' : '' }}>{{ __('instructor.returned_status') }}</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="px-4 py-2 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white rounded-lg font-semibold text-sm">
                                                <i class="fas fa-check"></i> {{ __('instructor.save_grade') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3 border-t border-slate-200 dark:border-slate-700">{{ $submissions->links() }}</div>
            @else
                <p class="p-8 text-center text-slate-500 dark:text-slate-400">{{ __('instructor.no_submissions_yet') }}</p>
            @endif
        </div>
    </div>
</div>

@if($submissions->count() > 0)
<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (!row) return;
    row.classList.toggle('hidden');
}
</script>
@endif
@endsection
