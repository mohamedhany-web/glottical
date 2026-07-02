@extends('layouts.app')

@section('title', __('student.one_to_one_availability_title'))

@section('content')
<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="availabilityForm()">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('student.one_to_one_availability_title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('student.one_to_one_availability_sub') }}</p>
        </div>
        <a href="{{ route('instructor.one-to-one-sessions.index') }}" class="text-sm text-sky-600 hover:underline">{{ __('student.one_to_one_sessions_instructor_nav') }}</a>
    </div>

    <form method="POST" action="{{ route('instructor.one-to-one-availability.update') }}" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-6">
        @csrf
        <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('student.one_to_one_availability_hint') }}</p>

        <template x-for="(slot, index) in slots" :key="index">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('student.one_to_one_day') }}</label>
                    <select :name="'slots['+index+'][day_of_week]'" x-model="slot.day_of_week" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                        @foreach($dayLabels as $day => $label)
                            <option value="{{ $day }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('student.one_to_one_from') }}</label>
                    <input type="time" :name="'slots['+index+'][start_time]'" x-model="slot.start_time" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('student.one_to_one_to') }}</label>
                    <input type="time" :name="'slots['+index+'][end_time]'" x-model="slot.end_time" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('student.minutes') }}</label>
                    <input type="number" :name="'slots['+index+'][slot_duration_minutes]'" x-model="slot.slot_duration_minutes" min="30" max="180" step="15" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-3 flex gap-2">
                    <button type="button" @click="removeSlot(index)" class="px-3 py-2 rounded-lg border border-rose-200 text-rose-600 text-sm font-semibold hover:bg-rose-50" x-show="slots.length > 1">{{ __('student.one_to_one_remove_slot') }}</button>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap gap-3">
            <button type="button" @click="addSlot()" class="px-4 py-2 rounded-xl border border-sky-200 text-sky-700 text-sm font-bold hover:bg-sky-50">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('student.one_to_one_add_slot') }}
            </button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold">{{ __('student.one_to_one_save_schedule') }}</button>
        </div>
    </form>

    @if($rules->isNotEmpty())
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="font-bold text-slate-900 dark:text-white mb-4">{{ __('student.one_to_one_current_schedule') }}</h2>
        <div class="space-y-2 text-sm">
            @foreach($grouped as $group)
                @if($group['rules']->isNotEmpty())
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="font-semibold text-slate-700 dark:text-slate-200 w-20">{{ $group['label'] }}</span>
                        @foreach($group['rules'] as $rule)
                            <span class="px-2 py-1 rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-200 text-xs font-medium">
                                {{ substr((string) $rule->start_time, 0, 5) }} – {{ substr((string) $rule->end_time, 0, 5) }}
                                ({{ (int) $rule->slot_duration_minutes }} {{ __('student.minutes') }})
                            </span>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
function availabilityForm() {
    const existing = @json($rules->map(fn ($r) => [
        'day_of_week' => (string) $r->day_of_week,
        'start_time' => substr((string) $r->start_time, 0, 5),
        'end_time' => substr((string) $r->end_time, 0, 5),
        'slot_duration_minutes' => (string) ($r->slot_duration_minutes ?: 60),
    ])->values());
    return {
        slots: existing.length ? existing : [{ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '60' }],
        addSlot() {
            this.slots.push({ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '60' });
        },
        removeSlot(i) {
            this.slots.splice(i, 1);
        }
    };
}
</script>
@endsection
