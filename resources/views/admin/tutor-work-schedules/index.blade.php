@extends('layouts.admin')

@section('title', 'جداول عمل المدربين')
@section('page_title', 'جداول عمل المدربين')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $hourStart = (int) ($hourStart ?? 8);
    $hourEnd = (int) ($hourEnd ?? 22);
    $hours = $hours ?? range($hourStart, $hourEnd - 1);
    $rowHeight = 48; // px per hour
    $gridHeight = max(1, count($hours)) * $rowHeight;
@endphp

@push('styles')
<style>
    .tws-timetable {
        --tws-hour-h: {{ $rowHeight }}px;
        overflow: auto;
        border-radius: 16px;
        border: 1px solid #e6ebf4;
        background: #fff;
    }
    .tws-timetable__head,
    .tws-timetable__body {
        display: grid;
        grid-template-columns: 64px repeat(7, minmax(120px, 1fr));
        min-width: 980px;
    }
    .tws-timetable__corner,
    .tws-timetable__dayhead {
        position: sticky;
        top: 0;
        z-index: 3;
        background: #f8fafc;
        border-bottom: 1px solid #e6ebf4;
        border-inline-end: 1px solid #eef2f8;
        padding: 10px 8px;
        text-align: center;
        font-size: 12px;
        font-weight: 800;
        color: #0b1220;
    }
    .tws-timetable__corner {
        inset-inline-start: 0;
        z-index: 4;
    }
    .tws-timetable__dayhead.is-busy {
        background: #eef4ff;
        color: #0B3D91;
    }
    .tws-timetable__dayhead small {
        display: block;
        margin-top: 2px;
        font-size: 10px;
        font-weight: 700;
        color: #7b8499;
    }
    .tws-timetable__hours {
        position: sticky;
        inset-inline-start: 0;
        z-index: 2;
        background: #f8fafc;
        border-inline-end: 1px solid #e6ebf4;
    }
    .tws-timetable__hour {
        height: var(--tws-hour-h);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 4px;
        font-size: 11px;
        font-weight: 800;
        color: #7b8499;
        font-variant-numeric: tabular-nums;
        border-bottom: 1px solid #eef2f8;
    }
    .tws-timetable__daycol {
        position: relative;
        height: 100%;
        border-inline-end: 1px solid #eef2f8;
        background:
            repeating-linear-gradient(
                to bottom,
                #fff 0,
                #fff calc(var(--tws-hour-h) - 1px),
                #eef2f8 calc(var(--tws-hour-h) - 1px),
                #eef2f8 var(--tws-hour-h)
            );
    }
    .tws-block {
        position: absolute;
        inset-inline: 4px;
        border-radius: 10px;
        padding: 6px 8px;
        overflow: hidden;
        box-shadow: 0 6px 14px rgba(11, 61, 145, .12);
        border: 1px solid transparent;
        color: #fff;
        font-size: 11px;
        line-height: 1.35;
        z-index: 1;
    }
    .tws-block--both { background: linear-gradient(160deg, #0B3D91, #0a78c2); border-color: rgba(255,255,255,.2); }
    .tws-block--individual { background: linear-gradient(160deg, #047857, #10b981); }
    .tws-block--collective { background: linear-gradient(160deg, #b45309, #f59e0b); color: #1f1300; }
    .tws-block strong { display: block; font-size: 12px; font-weight: 900; }
    .tws-block span { display: block; opacity: .92; font-weight: 700; }
    .tws-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 11px;
        font-weight: 800;
        color: #5b6577;
    }
    .tws-legend i {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 4px;
        margin-inline-end: 6px;
        vertical-align: -1px;
    }
    .tws-legend .both { background: #0B3D91; }
    .tws-legend .individual { background: #10b981; }
    .tws-legend .collective { background: #f59e0b; }
</style>
@endpush

<div class="space-y-5" x-data="{
    slots: {{ Js::from($rules->map(fn ($r) => [
        'day_of_week' => (int) $r->day_of_week,
        'start_time' => $r->startTimeString(),
        'end_time' => $r->endTimeString(),
        'slot_duration_minutes' => (int) $r->slot_duration_minutes,
        'applies_to' => $r->applies_to,
        'note' => $r->note,
    ])->values()) }},
    addSlot(day) {
        this.slots.push({ day_of_week: day, start_time: '10:00', end_time: '14:00', slot_duration_minutes: 60, applies_to: 'both', note: '' });
        this.$nextTick(() => {
            const el = document.getElementById('tws-edit-' + day);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    },
    removeSlot(i) { this.slots.splice(i, 1); }
}">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الطلاب والخدمات · جداول التوافر</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">جدول عمل المدربين</h2>
            <p class="mt-1 text-sm text-muted">عرض أسبوعي كنوافذ زمنية — الأيام أفقياً والساعات رأسياً</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.placement.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-accent/30 bg-surface px-4 text-sm font-medium text-accent">
                <i class="fas fa-user-check text-xs"></i>
                التسكين
            </a>
            <a href="{{ route('admin.tutoring-group-bookings.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft hover:text-accent">
                <i class="fas fa-calendar-check text-xs"></i>
                الحجوزات
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">مدربون</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ number_format($stats['instructors']) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">نوافذ محفوظة</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ number_format($stats['windows']) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">نشطة</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ number_format($stats['active']) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">نوافذ المدرب الحالي</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ number_format($stats['selected']) }}</p>
        </article>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">اختيار المدرب</h3>
            <p class="mt-0.5 text-xs text-muted">ابحث ثم اختر لعرض جدوله الأسبوعي</p>
        </div>
        <form method="GET" action="{{ route('admin.tutor-work-schedules.index') }}" class="grid gap-3 p-4 sm:grid-cols-[1fr_auto] sm:items-end sm:p-5">
            <div>
                <label class="{{ $labelClass }}" for="instructorSearch">بحث</label>
                <input type="search" id="instructorSearch" autocomplete="off" placeholder="بحث بالاسم أو البريد…" class="{{ $fieldClass }} mb-2">
                <label class="{{ $labelClass }}" for="instructor_id">المدرب</label>
                <select id="instructor_id" name="instructor_id" class="{{ $fieldClass }}" onchange="this.form.submit()">
                    @forelse($instructors as $ins)
                        @php $hay = mb_strtolower(trim($ins->name.' '.($ins->email ?? '')), 'UTF-8'); @endphp
                        <option value="{{ $ins->id }}" data-search="{{ e($hay) }}" @selected($instructorId === (int) $ins->id)>
                            {{ $ins->name }}@if($ins->email) — {{ $ins->email }}@endif
                        </option>
                    @empty
                        <option value="">لا يوجد مدربون</option>
                    @endforelse
                </select>
            </div>
            <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink">عرض الجدول</button>
        </form>
    </article>

    @if($instructorId)
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                <div>
                    <h3 class="text-base font-semibold text-ink">الجدول الأسبوعي — {{ $selectedInstructor?->name ?? 'المدرب' }}</h3>
                    <p class="mt-0.5 text-xs text-muted">من {{ sprintf('%02d:00', $hourStart) }} إلى {{ sprintf('%02d:00', $hourEnd) }}</p>
                </div>
                <div class="tws-legend">
                    <span><i class="both"></i>فردي + جماعي</span>
                    <span><i class="individual"></i>فردي</span>
                    <span><i class="collective"></i>جماعي</span>
                </div>
            </div>

            <div class="p-3 sm:p-4">
                <div class="tws-timetable" dir="ltr">
                    <div class="tws-timetable__head">
                        <div class="tws-timetable__corner">الوقت</div>
                        @foreach($gridDays as $day)
                            <div class="tws-timetable__dayhead {{ $day['count'] > 0 ? 'is-busy' : '' }}">
                                {{ $day['label'] }}
                                <small>{{ $day['count'] }} نافذة</small>
                            </div>
                        @endforeach
                    </div>
                    <div class="tws-timetable__body" style="height: {{ $gridHeight }}px;">
                        <div class="tws-timetable__hours">
                            @foreach($hours as $h)
                                <div class="tws-timetable__hour">{{ sprintf('%02d:00', $h) }}</div>
                            @endforeach
                        </div>
                        @foreach($gridDays as $day)
                            <div class="tws-timetable__daycol">
                                @foreach($day['blocks'] as $block)
                                    <div class="tws-block tws-block--{{ $block['applies_to'] }}"
                                         style="top: {{ $block['top'] }}%; height: {{ max(8, $block['height']) }}%;"
                                         title="{{ $block['start'] }} - {{ $block['end'] }} · {{ $block['applies_label'] }}">
                                        <strong>{{ $block['start'] }} – {{ $block['end'] }}</strong>
                                        <span>{{ $block['applies_label'] }} · {{ $block['duration'] }}د</span>
                                        @if($block['note'] !== '')
                                            <span>{{ \Illuminate\Support\Str::limit($block['note'], 28) }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($stats['selected'] < 1)
                    <p class="mt-3 text-center text-sm text-muted">لا نوافذ لهذا المدرب بعد — أضف نوافذ من قسم التعديل بالأسفل.</p>
                @endif
            </div>
        </article>

        <form method="POST" action="{{ route('admin.tutor-work-schedules.sync') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="instructor_id" value="{{ $instructorId }}">

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">تعديل النوافذ</h3>
                        <p class="mt-0.5 text-xs text-muted">الحفظ يستبدل جدول هذا المدرب بالكامل ثم يحدّث العرض أعلاه</p>
                    </div>
                    <button type="submit" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                        <i class="fas fa-save text-xs"></i>
                        حفظ الجدول
                    </button>
                </div>

                <div class="space-y-4 p-4 sm:p-5">
                    @foreach($dayLabels as $day => $label)
                        <div class="rounded-2xl border border-line bg-canvas/30 p-4" id="tws-edit-{{ $day }}">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <h4 class="text-sm font-semibold text-ink">{{ $label }}</h4>
                                <button type="button" @click="addSlot({{ $day }})" class="btn-press inline-flex h-8 items-center gap-1.5 rounded-lg border border-line bg-surface px-3 text-xs font-medium text-accent">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    إضافة نافذة
                                </button>
                            </div>
                            <template x-for="(slot, index) in slots" :key="index">
                                <div x-show="Number(slot.day_of_week) === {{ $day }}" class="mb-2 grid grid-cols-1 gap-2 rounded-xl border border-line bg-surface p-3 sm:grid-cols-6 sm:items-end">
                                    <input type="hidden" :name="'slots['+index+'][day_of_week]'" :value="slot.day_of_week">
                                    <div>
                                        <label class="{{ $labelClass }}">من</label>
                                        <input type="time" class="{{ $fieldClass }}" x-model="slot.start_time" :name="'slots['+index+'][start_time]'">
                                    </div>
                                    <div>
                                        <label class="{{ $labelClass }}">إلى</label>
                                        <input type="time" class="{{ $fieldClass }}" x-model="slot.end_time" :name="'slots['+index+'][end_time]'">
                                    </div>
                                    <div>
                                        <label class="{{ $labelClass }}">مدة الشريحة</label>
                                        <select class="{{ $fieldClass }}" x-model="slot.slot_duration_minutes" :name="'slots['+index+'][slot_duration_minutes]'">
                                            <option value="30">30</option>
                                            <option value="45">45</option>
                                            <option value="60">60</option>
                                            <option value="90">90</option>
                                            <option value="120">120</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="{{ $labelClass }}">ينطبق على</label>
                                        <select class="{{ $fieldClass }}" x-model="slot.applies_to" :name="'slots['+index+'][applies_to]'">
                                            <option value="both">فردي وجماعي</option>
                                            <option value="individual">فردي فقط</option>
                                            <option value="collective">جماعي فقط</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" @click="removeSlot(index)" class="btn-press inline-flex h-11 w-full items-center justify-center rounded-xl border border-line text-xs font-medium text-danger hover:bg-danger/5">حذف</button>
                                    </div>
                                </div>
                            </template>
                            <p class="text-xs text-muted" x-show="!slots.some(s => Number(s.day_of_week) === {{ $day }})">لا نوافذ لهذا اليوم</p>
                        </div>
                    @endforeach
                </div>
            </article>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
  var search = document.getElementById('instructorSearch');
  var select = document.getElementById('instructor_id');
  if (!search || !select) return;
  var options = Array.prototype.slice.call(select.querySelectorAll('option'));
  function apply() {
    var q = (search.value || '').trim().toLowerCase();
    options.forEach(function (opt) {
      if (!opt.value) { opt.hidden = false; return; }
      if (opt.selected) { opt.hidden = false; return; }
      var hay = (opt.getAttribute('data-search') || opt.textContent || '').toLowerCase();
      opt.hidden = q.length > 0 && hay.indexOf(q) === -1;
    });
  }
  search.addEventListener('input', apply);
  search.addEventListener('search', apply);
})();
</script>
@endpush
