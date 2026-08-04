@extends('layouts.app')

@section('title', __('student.one_to_one_availability_title'))

@push('styles')
<style>
    .o1a { --o1a-blue:#0B3D91; --o1a-dark:#072A66; --o1a-gold:#F5B800; --o1a-canvas:#F4F7FC; --o1a-line:#E8EEF8; --o1a-muted:#5B6577; }
    .o1a-panel {
        background: #fff;
        border: 1px solid var(--o1a-line);
        border-radius: 18px;
    }
    .dark .o1a-panel { background: #111827; border-color: #1f2937; }
    .o1a-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 999px;
        font-size: 11px; font-weight: 800;
        background: #EEF3FB; color: var(--o1a-blue);
    }
    .o1a-field {
        width: 100%;
        height: 42px;
        border-radius: 12px;
        border: 1px solid var(--o1a-line);
        background: #fff;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 600;
        color: #0B1220;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .o1a-field:focus {
        border-color: var(--o1a-blue);
        box-shadow: 0 0 0 3px rgba(11,61,145,.12);
    }
    .dark .o1a-field {
        background: #0f172a;
        border-color: #334155;
        color: #f1f5f9;
    }
    .o1a-slot-card {
        border: 1px solid var(--o1a-line);
        background: var(--o1a-canvas);
        border-radius: 16px;
        padding: 14px;
    }
    .dark .o1a-slot-card { background: #0f172a; border-color: #1f2937; }
    .o1a-day {
        border: 1px solid var(--o1a-line);
        border-radius: 16px;
        background: #fff;
        min-height: 140px;
        display: flex;
        flex-direction: column;
    }
    .dark .o1a-day { background: #111827; border-color: #1f2937; }
    .o1a-day.has-slots { border-color: rgba(11,61,145,.28); }
    .o1a-day__head {
        padding: 10px 12px;
        border-bottom: 1px solid var(--o1a-line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .dark .o1a-day__head { border-bottom-color: #1f2937; }
    .o1a-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 6px 8px; border-radius: 10px;
        font-size: 11px; font-weight: 700;
        background: #EEF3FB; color: var(--o1a-blue);
        line-height: 1.3;
    }
    .o1a-pill--soft { background: #F4F7FC; color: var(--o1a-muted); }
    .dark .o1a-pill { background: #132445; color: #bfdbfe; }
</style>
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $windowsCount = $rules->count();
    $daysWithSlots = $grouped->filter(fn ($g) => $g['rules']->isNotEmpty())->count();
    $existingSlots = $rules->map(function ($r) {
        return [
            'day_of_week' => (string) $r->day_of_week,
            'start_time' => substr((string) $r->start_time, 0, 5),
            'end_time' => substr((string) $r->end_time, 0, 5),
            'slot_duration_minutes' => (string) ($r->slot_duration_minutes ?: 50),
        ];
    })->values();
@endphp

<div class="o1a w-full space-y-5" x-data="availabilityForm()">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300">
            <i class="fas fa-check-circle {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:bg-rose-900/20 dark:border-rose-800 dark:text-rose-300">
            <ul class="list-disc pe-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="o1a-panel overflow-hidden">
        <div class="relative px-5 py-5 sm:px-6 sm:py-6">
            <div class="absolute inset-y-0 {{ $isRtl ? 'left-0' : 'right-0' }} w-40 sm:w-56 pointer-events-none opacity-90"
                 style="background: radial-gradient(ellipse at center, rgba(245,184,0,0.22), transparent 70%);"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div class="min-w-0">
                    <span class="o1a-chip mb-3">
                        <i class="fas fa-user-graduate text-[10px]"></i>
                        {{ $isRtl ? 'تدريس مباشر · حصص فردية' : 'Live teaching · 1:1' }}
                    </span>
                    <h1 class="font-heading text-2xl sm:text-[28px] font-black tracking-tight text-[#0B1220] dark:text-white leading-tight">
                        {{ __('student.one_to_one_availability_title') }}
                    </h1>
                    <p class="mt-1.5 text-sm text-[color:var(--o1a-muted)] dark:text-gray-400 max-w-2xl">
                        {{ __('student.one_to_one_availability_sub') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if(Route::has('instructor.tutor-work-schedule.index'))
                        <a href="{{ route('instructor.tutor-work-schedule.index') }}"
                           class="inline-flex h-10 items-center gap-2 rounded-xl border border-[color:var(--o1a-line)] dark:border-gray-600 bg-white dark:bg-gray-800 px-4 text-sm font-bold text-[#0B1220] dark:text-white hover:border-[#0B3D91]/40">
                            <i class="fas fa-users text-xs text-[#0B3D91]"></i>
                            {{ $isRtl ? 'جدول المجموعات' : 'Group schedule' }}
                        </a>
                    @endif
                    @if(Route::has('instructor.one-to-one-sessions.index'))
                        <a href="{{ route('instructor.one-to-one-sessions.index') }}"
                           class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#0B3D91] px-4 text-sm font-bold text-white hover:brightness-110">
                            <i class="fas fa-chalkboard-teacher text-xs"></i>
                            {{ __('student.one_to_one_sessions_instructor_nav') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="o1a-panel px-4 py-4">
            <p class="text-[11px] font-bold text-[color:var(--o1a-muted)]">{{ $isRtl ? 'نوافذ التوفر' : 'Windows' }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B3D91] dark:text-blue-300 tabular-nums" x-text="slots.length">{{ $windowsCount }}</p>
        </div>
        <div class="o1a-panel px-4 py-4">
            <p class="text-[11px] font-bold text-[color:var(--o1a-muted)]">{{ $isRtl ? 'أيام مفعّلة' : 'Active days' }}</p>
            <p class="mt-1 text-2xl font-black text-[#0B1220] dark:text-white tabular-nums">{{ $daysWithSlots }}</p>
        </div>
        <div class="o1a-panel px-4 py-4">
            <p class="text-[11px] font-bold text-[color:var(--o1a-muted)]">{{ $isRtl ? 'محفوظ حالياً' : 'Saved' }}</p>
            <p class="mt-1 text-2xl font-black text-[#8A6A00] tabular-nums">{{ $windowsCount }}</p>
        </div>
        <div class="o1a-panel px-4 py-4 flex items-center">
            <p class="text-xs text-[color:var(--o1a-muted)] leading-relaxed">
                {{ __('student.one_to_one_availability_hint') }}
            </p>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        <form method="POST" action="{{ route('instructor.one-to-one-availability.update') }}"
              class="xl:col-span-7 o1a-panel p-5 sm:p-6 space-y-5">
            @csrf
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'تحرير النوافذ' : 'Edit windows' }}</h2>
                    <p class="mt-1 text-xs text-[color:var(--o1a-muted)]">{{ $isRtl ? 'أضف يوم ووقت ومدة الحصة الفردية.' : 'Add day, time, and session length.' }}</p>
                </div>
                <button type="button" @click="addSlot()"
                        class="inline-flex h-10 items-center gap-2 rounded-xl border border-[#F5B800] bg-[#FFF6D6] px-4 text-sm font-extrabold text-[#072A66] hover:brightness-105">
                    <i class="fas fa-plus text-xs"></i>
                    {{ __('student.one_to_one_add_slot') }}
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(slot, index) in slots" :key="index">
                    <div class="o1a-slot-card">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[11px] font-extrabold text-[#0B3D91] dark:text-blue-300">
                                {{ $isRtl ? 'نافذة' : 'Slot' }} <span x-text="index + 1"></span>
                            </span>
                            <button type="button" @click="removeSlot(index)" x-show="slots.length > 1"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-lg px-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20">
                                <i class="fas fa-trash text-[10px]"></i>
                                {{ __('student.one_to_one_remove_slot') }}
                            </button>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-[color:var(--o1a-muted)] mb-1.5">{{ __('student.one_to_one_day') }}</label>
                                <select :name="'slots['+index+'][day_of_week]'" x-model="slot.day_of_week" class="o1a-field" required>
                                    @foreach($dayLabels as $day => $label)
                                        <option value="{{ $day }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[color:var(--o1a-muted)] mb-1.5">{{ __('student.one_to_one_from') }}</label>
                                <input type="time" :name="'slots['+index+'][start_time]'" x-model="slot.start_time" class="o1a-field" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[color:var(--o1a-muted)] mb-1.5">{{ __('student.one_to_one_to') }}</label>
                                <input type="time" :name="'slots['+index+'][end_time]'" x-model="slot.end_time" class="o1a-field" required>
                            </div>
                            <div class="col-span-2 lg:col-span-4 lg:max-w-xs">
                                <label class="block text-[11px] font-bold text-[color:var(--o1a-muted)] mb-1.5">{{ __('student.minutes') }}</label>
                                <input type="number" :name="'slots['+index+'][slot_duration_minutes]'" x-model="slot.slot_duration_minutes" min="30" max="180" step="15" class="o1a-field">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-[color:var(--o1a-line)] dark:border-gray-700">
                <p class="text-[11px] text-[color:var(--o1a-muted)]">
                    <i class="fas fa-info-circle {{ $isRtl ? 'ml-1' : 'mr-1' }} opacity-70"></i>
                    {{ $isRtl ? 'الحفظ يستبدل الجدول الحالي بالكامل.' : 'Saving replaces the current schedule.' }}
                </p>
                <button type="submit"
                        class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#0B3D91] px-5 text-sm font-extrabold text-white hover:brightness-110 shadow-[0_12px_28px_-16px_rgba(11,61,145,.55)]">
                    <i class="fas fa-save text-xs"></i>
                    {{ __('student.one_to_one_save_schedule') }}
                </button>
            </div>
        </form>

        <aside class="xl:col-span-5 space-y-4">
            <div class="o1a-panel p-5 sm:p-6">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <h2 class="text-base font-extrabold text-[#0B1220] dark:text-white">{{ __('student.one_to_one_current_schedule') }}</h2>
                    <span class="o1a-chip">{{ $isRtl ? 'محفوظ' : 'Saved' }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">
                    @foreach($grouped as $dayGroup)
                        @php $has = $dayGroup['rules']->isNotEmpty(); @endphp
                        <div class="o1a-day {{ $has ? 'has-slots' : '' }}">
                            <div class="o1a-day__head">
                                <span class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $dayGroup['label'] }}</span>
                                @if($has)
                                    <span class="text-[10px] font-bold text-[#0B3D91] tabular-nums">{{ $dayGroup['rules']->count() }}</span>
                                @else
                                    <span class="text-[10px] font-bold text-[color:var(--o1a-muted)]">{{ $isRtl ? 'فارغ' : 'Empty' }}</span>
                                @endif
                            </div>
                            <div class="p-2.5 flex flex-wrap gap-1.5 flex-1 content-start">
                                @forelse($dayGroup['rules'] as $rule)
                                    <span class="o1a-pill">
                                        <i class="far fa-clock text-[9px] opacity-70"></i>
                                        {{ substr((string) $rule->start_time, 0, 5) }}–{{ substr((string) $rule->end_time, 0, 5) }}
                                        · {{ (int) $rule->slot_duration_minutes }} {{ __('student.minutes') }}
                                    </span>
                                @empty
                                    <span class="o1a-pill o1a-pill--soft w-full justify-center py-4">{{ $isRtl ? 'لا نوافذ في هذا اليوم' : 'No windows this day' }}</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
function availabilityForm() {
    const existing = @json($existingSlots);
    return {
        slots: existing.length ? existing : [{ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '50' }],
        addSlot() {
            this.slots.push({ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '50' });
        },
        removeSlot(i) {
            this.slots.splice(i, 1);
        }
    };
}
</script>
@endsection
