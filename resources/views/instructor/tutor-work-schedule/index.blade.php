@extends('layouts.app')

@section('title', 'جدول عمل المجموعات')

@push('styles')
<style>
    .tws { --tws-blue:#0B3D91; --tws-dark:#072A66; --tws-gold:#F5B800; --tws-canvas:#F4F7FC; --tws-line:#E8EEF8; --tws-muted:#5B6577; }
    .tws-panel {
        background: #fff;
        border: 1px solid var(--tws-line);
        border-radius: 18px;
    }
    .dark .tws-panel { background: #111827; border-color: #1f2937; }
    .tws-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 999px;
        font-size: 11px; font-weight: 800;
        background: #EEF3FB; color: var(--tws-blue);
    }
    .tws-field {
        width: 100%;
        height: 42px;
        border-radius: 12px;
        border: 1px solid var(--tws-line);
        background: #fff;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 600;
        color: #0B1220;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .tws-field:focus {
        border-color: var(--tws-blue);
        box-shadow: 0 0 0 3px rgba(11,61,145,.12);
    }
    .dark .tws-field {
        background: #0f172a;
        border-color: #334155;
        color: #f1f5f9;
    }
    .tws-slot-card {
        border: 1px solid var(--tws-line);
        background: var(--tws-canvas);
        border-radius: 16px;
        padding: 14px;
        transition: border-color .15s;
    }
    .dark .tws-slot-card { background: #0f172a; border-color: #1f2937; }
    .tws-day {
        border: 1px solid var(--tws-line);
        border-radius: 16px;
        background: #fff;
        min-height: 140px;
        display: flex;
        flex-direction: column;
    }
    .dark .tws-day { background: #111827; border-color: #1f2937; }
    .tws-day.has-slots { border-color: rgba(11,61,145,.28); }
    .tws-day__head {
        padding: 10px 12px;
        border-bottom: 1px solid var(--tws-line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .dark .tws-day__head { border-bottom-color: #1f2937; }
    .tws-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 6px 8px; border-radius: 10px;
        font-size: 11px; font-weight: 700;
        background: #EEF3FB; color: var(--tws-blue);
        line-height: 1.3;
    }
    .tws-pill--gold { background: #FFF6D6; color: #8A6A00; }
    .tws-pill--soft { background: #F4F7FC; color: var(--tws-muted); }
    .dark .tws-pill { background: #132445; color: #bfdbfe; }
    .dark .tws-pill--gold { background: #3b2f0a; color: #fde68a; }
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
            'slot_duration_minutes' => (string) ($r->slot_duration_minutes ?: 60),
            'applies_to' => $r->applies_to ?: 'both',
        ];
    })->values();
@endphp

<div class="tws w-full space-y-5" x-data="tutorWorkForm()">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
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

    {{-- Header --}}
    <section class="tws-panel overflow-hidden">
        <div class="relative px-5 py-5 sm:px-6 sm:py-6">
            <div class="absolute inset-y-0 {{ $isRtl ? 'left-0' : 'right-0' }} w-40 sm:w-56 pointer-events-none opacity-90"
                 style="background: radial-gradient(ellipse at center, rgba(245,184,0,0.22), transparent 70%);"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div class="min-w-0">
                    <span class="tws-chip mb-3">
                        <i class="fas fa-users text-[10px]"></i>
                        تدريس مباشر · مجموعات
                    </span>
                    <h1 class="font-heading text-2xl sm:text-[28px] font-black tracking-tight text-[#0B1220] dark:text-white leading-tight">
                        جدول عمل المجموعات
                    </h1>
                    <p class="mt-1.5 text-sm text-[color:var(--tws-muted)] dark:text-gray-400 max-w-2xl">
                        نوافذ الأسبوع المتاحة لحجز المجموعات الفردية والجماعية — منفصل عن جدول كورسات 1:1.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if(Route::has('instructor.tutoring-bookings.index'))
                        <a href="{{ route('instructor.tutoring-bookings.index') }}"
                           class="inline-flex h-10 items-center gap-2 rounded-xl border border-[color:var(--tws-line)] dark:border-gray-600 bg-white dark:bg-gray-800 px-4 text-sm font-bold text-[#0B1220] dark:text-white hover:border-[#0B3D91]/40">
                            <i class="fas fa-calendar-check text-xs text-[#0B3D91]"></i>
                            الحجوزات
                        </a>
                    @endif
                    @if(Route::has('instructor.one-to-one-availability.index'))
                        <a href="{{ route('instructor.one-to-one-availability.index') }}"
                           class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#0B3D91] px-4 text-sm font-bold text-white hover:brightness-110">
                            <i class="fas fa-user-graduate text-xs"></i>
                            جدول 1:1
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="tws-panel px-4 py-4">
            <p class="text-[11px] font-bold text-[color:var(--tws-muted)]">نوافذ العمل</p>
            <p class="mt-1 text-2xl font-black text-[#0B3D91] dark:text-blue-300 tabular-nums" x-text="slots.length">{{ $windowsCount }}</p>
        </div>
        <div class="tws-panel px-4 py-4">
            <p class="text-[11px] font-bold text-[color:var(--tws-muted)]">أيام مفعّلة</p>
            <p class="mt-1 text-2xl font-black text-[#0B1220] dark:text-white tabular-nums">{{ $daysWithSlots }}</p>
        </div>
        <div class="tws-panel px-4 py-4">
            <p class="text-[11px] font-bold text-[color:var(--tws-muted)]">محفوظ حالياً</p>
            <p class="mt-1 text-2xl font-black text-[#8A6A00] tabular-nums">{{ $windowsCount }}</p>
        </div>
        <div class="tws-panel px-4 py-4 flex items-center">
            <p class="text-xs text-[color:var(--tws-muted)] leading-relaxed">
                الحفظ يستبدل الجدول بالكامل بالنوافذ المعروضة في النموذج.
            </p>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        {{-- Editor --}}
        <form method="POST" action="{{ route('instructor.tutor-work-schedule.update') }}"
              class="xl:col-span-7 tws-panel p-5 sm:p-6 space-y-5">
            @csrf
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-extrabold text-[#0B1220] dark:text-white">تحرير النوافذ</h2>
                    <p class="mt-1 text-xs text-[color:var(--tws-muted)]">أضف يوم ووقت ومدة الحصة. لا يوجد حد عند 3 نوافذ — مدّد «إلى» لو عايز مواعيد أكتر في نفس اليوم.</p>
                </div>
                <button type="button" @click="addSlot()"
                        class="inline-flex h-10 items-center gap-2 rounded-xl border border-[#F5B800] bg-[#FFF6D6] px-4 text-sm font-extrabold text-[#072A66] hover:brightness-105">
                    <i class="fas fa-plus text-xs"></i>
                    إضافة نافذة
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(slot, index) in slots" :key="slot._uid">
                    <div class="tws-slot-card">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[11px] font-extrabold text-[#0B3D91] dark:text-blue-300">
                                نافذة <span x-text="index + 1"></span>
                            </span>
                            <button type="button" @click="removeSlot(index)" x-show="slots.length > 1"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-lg px-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20">
                                <i class="fas fa-trash text-[10px]"></i>
                                حذف
                            </button>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3">
                            <div class="col-span-2 lg:col-span-2">
                                <label class="block text-[11px] font-bold text-[color:var(--tws-muted)] mb-1.5">اليوم</label>
                                <select :name="'slots['+index+'][day_of_week]'" x-model="slot.day_of_week" class="tws-field" required>
                                    @foreach($dayLabels as $day => $label)
                                        <option value="{{ $day }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[color:var(--tws-muted)] mb-1.5">من</label>
                                <input type="time" step="60" :name="'slots['+index+'][start_time]'" x-model="slot.start_time" class="tws-field" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[color:var(--tws-muted)] mb-1.5">إلى</label>
                                <input type="time" step="60" :name="'slots['+index+'][end_time]'" x-model="slot.end_time" class="tws-field" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[color:var(--tws-muted)] mb-1.5">المدة (د)</label>
                                <input type="number" :name="'slots['+index+'][slot_duration_minutes]'" x-model="slot.slot_duration_minutes" min="30" max="240" step="15" class="tws-field">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[color:var(--tws-muted)] mb-1.5">ينطبق على</label>
                                <select :name="'slots['+index+'][applies_to]'" x-model="slot.applies_to" class="tws-field">
                                    <option value="both">فردي وجماعي</option>
                                    <option value="individual">فردي</option>
                                    <option value="collective">جماعي</option>
                                </select>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] font-bold" :class="slotYield(slot) > 0 ? 'text-[#0B3D91] dark:text-blue-300' : 'text-rose-600'">
                            <span x-show="slotYield(slot) > 0" x-text="'تنتج ' + slotYield(slot) + ' مواعيد في هذا اليوم'"></span>
                            <span x-show="slotYield(slot) < 1">لن يُقبل موعد: مدّد «إلى» ليغطي مدة الحصة (حتى منتصف الليل استخدم 00:00).</span>
                        </p>
                    </div>
                </template>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-[color:var(--tws-line)] dark:border-gray-700">
                <p class="text-[11px] text-[color:var(--tws-muted)]">
                    <i class="fas fa-info-circle ml-1 opacity-70"></i>
                    احفظ بعد الانتهاء لتحديث مواعيد الحجز الظاهرة للطلاب.
                </p>
                <button type="submit"
                        class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#0B3D91] px-5 text-sm font-extrabold text-white hover:brightness-110 shadow-[0_12px_28px_-16px_rgba(11,61,145,.55)]">
                    <i class="fas fa-save text-xs"></i>
                    حفظ الجدول
                </button>
            </div>
        </form>

        {{-- Week overview --}}
        <aside class="xl:col-span-5 space-y-4">
            <div class="tws-panel p-5 sm:p-6">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <h2 class="text-base font-extrabold text-[#0B1220] dark:text-white">عرض الأسبوع</h2>
                    <span class="tws-chip">محفوظ</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">
                    @foreach($grouped as $dayGroup)
                        @php $has = $dayGroup['rules']->isNotEmpty(); @endphp
                        <div class="tws-day {{ $has ? 'has-slots' : '' }}">
                            <div class="tws-day__head">
                                <span class="text-sm font-extrabold text-[#0B1220] dark:text-white">{{ $dayGroup['label'] }}</span>
                                @if($has)
                                    <span class="text-[10px] font-bold text-[#0B3D91] tabular-nums">{{ $dayGroup['rules']->count() }}</span>
                                @else
                                    <span class="text-[10px] font-bold text-[color:var(--tws-muted)]">فارغ</span>
                                @endif
                            </div>
                            <div class="p-2.5 flex flex-wrap gap-1.5 flex-1 content-start">
                                @forelse($dayGroup['rules'] as $rule)
                                    @php
                                        $applies = $rule->applies_to === 'individual' ? 'فردي' : ($rule->applies_to === 'collective' ? 'جماعي' : 'الكل');
                                        $pillClass = $rule->applies_to === 'collective' ? 'tws-pill--gold' : ($rule->applies_to === 'individual' ? 'tws-pill' : 'tws-pill');
                                    @endphp
                                    <span class="tws-pill {{ $pillClass }}">
                                        <i class="far fa-clock text-[9px] opacity-70"></i>
                                        {{ substr((string) $rule->start_time, 0, 5) }}–{{ substr((string) $rule->end_time, 0, 5) }}
                                        · {{ (int) $rule->slot_duration_minutes }}د
                                        · {{ $applies }}
                                    </span>
                                @empty
                                    <span class="tws-pill tws-pill--soft w-full justify-center py-4">لا نوافذ في هذا اليوم</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="tws-panel px-5 py-4 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2 text-[11px] font-bold">
                    <span class="tws-pill">فردي / الكل</span>
                    <span class="tws-pill tws-pill--gold">جماعي</span>
                </div>
                @if(Route::has('instructor.tutoring-cohorts.index'))
                    <a href="{{ route('instructor.tutoring-cohorts.index') }}" class="text-xs font-bold text-[#0B3D91] dark:text-blue-300 hover:underline">
                        الدفعات الجماعية ←
                    </a>
                @endif
            </div>
        </aside>
    </div>
</div>

<script>
function tutorWorkForm() {
    const existing = @json($existingSlots);
    let uid = 1;
    const withIds = (existing.length ? existing : [{ day_of_week: '1', start_time: '16:00', end_time: '22:00', slot_duration_minutes: '60', applies_to: 'both' }])
        .map(function (slot) {
            slot._uid = uid++;
            return slot;
        });
    return {
        slots: withIds,
        nextUid: uid,
        addSlot() {
            this.slots.push({
                _uid: this.nextUid++,
                day_of_week: '1',
                start_time: '16:00',
                end_time: '22:00',
                slot_duration_minutes: '60',
                applies_to: 'both'
            });
        },
        removeSlot(i) {
            this.slots.splice(i, 1);
        },
        minutes(t) {
            if (!t) return 0;
            const p = String(t).split(':');
            return (parseInt(p[0], 10) || 0) * 60 + (parseInt(p[1], 10) || 0);
        },
        slotYield(slot) {
            let start = this.minutes(slot.start_time);
            let end = this.minutes(slot.end_time);
            if (end === 0 && start > 0) end = 24 * 60;
            const duration = parseInt(slot.slot_duration_minutes, 10) || 60;
            if (end <= start || duration < 1) return 0;
            return Math.floor((end - start) / duration);
        }
    };
}
</script>
@endsection
