@extends('layouts.app')

@section('title', 'جدول عمل المجموعات')

@section('content')
<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="tutorWorkForm()">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">جدول عمل المجموعات</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">نوافذ الأسبوع التي تظهر لحجز المجموعات الفردية والجماعية (منفصل عن جدول كورسات 1:1)</p>
        </div>
        @if(Route::has('instructor.one-to-one-availability.index'))
            <a href="{{ route('instructor.one-to-one-availability.index') }}" class="text-sm text-sky-600 hover:underline">جدول جلسات الكورس 1:1</a>
        @endif
    </div>

    <form method="POST" action="{{ route('instructor.tutor-work-schedule.update') }}" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-6">
        @csrf
        <p class="text-sm text-slate-600 dark:text-slate-300">أضف نوافذ العمل الأسبوعية. الحفظ يستبدل الجدول الحالي بالكامل.</p>

        <template x-for="(slot, index) in slots" :key="index">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">اليوم</label>
                    <select :name="'slots['+index+'][day_of_week]'" x-model="slot.day_of_week" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                        @foreach($dayLabels as $day => $label)
                            <option value="{{ $day }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">من</label>
                    <input type="time" :name="'slots['+index+'][start_time]'" x-model="slot.start_time" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">إلى</label>
                    <input type="time" :name="'slots['+index+'][end_time]'" x-model="slot.end_time" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">المدة</label>
                    <input type="number" :name="'slots['+index+'][slot_duration_minutes]'" x-model="slot.slot_duration_minutes" min="30" max="240" step="15" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">ينطبق على</label>
                    <select :name="'slots['+index+'][applies_to]'" x-model="slot.applies_to" class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                        <option value="both">فردي وجماعي</option>
                        <option value="individual">فردي</option>
                        <option value="collective">جماعي</option>
                    </select>
                </div>
                <div class="sm:col-span-1 flex gap-2">
                    <button type="button" @click="removeSlot(index)" class="px-3 py-2 rounded-lg border border-rose-200 text-rose-600 text-sm font-semibold hover:bg-rose-50" x-show="slots.length > 1">حذف</button>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap gap-3">
            <button type="button" @click="addSlot()" class="px-4 py-2 rounded-xl border border-sky-200 text-sky-700 text-sm font-bold hover:bg-sky-50">
                <i class="fas fa-plus ml-1"></i>إضافة نافذة
            </button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold">حفظ الجدول</button>
        </div>
    </form>

    @if($rules->isNotEmpty())
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="font-bold text-slate-900 dark:text-white mb-4">الجدول الحالي</h2>
        <div class="space-y-2 text-sm">
            @foreach($grouped as $dayGroup)
                @if($dayGroup['rules']->isNotEmpty())
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="font-semibold text-slate-700 dark:text-slate-200 w-20">{{ $dayGroup['label'] }}</span>
                        @foreach($dayGroup['rules'] as $rule)
                            <span class="px-2 py-1 rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-800 dark:text-sky-200 text-xs font-medium">
                                {{ substr((string) $rule->start_time, 0, 5) }} – {{ substr((string) $rule->end_time, 0, 5) }}
                                ({{ (int) $rule->slot_duration_minutes }} د)
                                · {{ $rule->applies_to === 'individual' ? 'فردي' : ($rule->applies_to === 'collective' ? 'جماعي' : 'الكل') }}
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
function tutorWorkForm() {
    const existing = @json($rules->map(fn ($r) => [
        'day_of_week' => (string) $r->day_of_week,
        'start_time' => substr((string) $r->start_time, 0, 5),
        'end_time' => substr((string) $r->end_time, 0, 5),
        'slot_duration_minutes' => (string) ($r->slot_duration_minutes ?: 60),
        'applies_to' => $r->applies_to ?: 'both',
    ])->values());
    return {
        slots: existing.length ? existing : [{ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '60', applies_to: 'both' }],
        addSlot() {
            this.slots.push({ day_of_week: '1', start_time: '09:00', end_time: '12:00', slot_duration_minutes: '60', applies_to: 'both' });
        },
        removeSlot(i) {
            this.slots.splice(i, 1);
        }
    };
}
</script>
@endsection
