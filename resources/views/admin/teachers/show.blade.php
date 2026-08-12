@extends('layouts.admin')

@section('title', 'تحكم: '.$teacher->name)
@section('page_title', 'تحكم المعلم')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
    $tabs = [
        'profile' => 'البيانات',
        'schedule' => 'الجدول',
        'sessions' => 'الحصص 1:1',
        'bookings' => 'الحجوزات',
        'courses' => 'الكورسات',
    ];
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.teachers.index') }}" class="hover:text-accent">تحكم المعلمين</a>
                · {{ $teacher->name }}
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $teacher->name }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $teacher->email ?: 'بدون بريد' }} · {{ $teacher->phone ?: 'بدون هاتف' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold {{ $teacher->is_active ? 'bg-success/10 text-success' : 'bg-canvas-muted text-muted' }}">
                {{ $teacher->is_active ? 'مفعّل' : 'معطّل' }}
            </span>
            @if(Route::has('admin.users.edit'))
                <a href="{{ route('admin.users.edit', $teacher->id) }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">ملف المستخدم</a>
            @endif
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">كورسات</div><div class="mt-1 text-xl font-semibold tabular-nums">{{ $stats['courses'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">مجموعات</div><div class="mt-1 text-xl font-semibold tabular-nums">{{ $stats['groups'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">1:1 مفتوحة</div><div class="mt-1 text-xl font-semibold tabular-nums">{{ $stats['open_sessions'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">حجوزات قادمة</div><div class="mt-1 text-xl font-semibold tabular-nums">{{ $stats['upcoming_bookings'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">نوافذ مجموعات</div><div class="mt-1 text-xl font-semibold tabular-nums">{{ $stats['work_windows'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">نوافذ 1:1</div><div class="mt-1 text-xl font-semibold tabular-nums">{{ $stats['oto_windows'] }}</div></div>
    </div>

    <nav class="flex flex-wrap gap-2 border-b border-line pb-3">
        @foreach($tabs as $key => $labelTab)
            <a href="{{ route('admin.teachers.show', ['teacher' => $teacher, 'tab' => $key, 'range' => $range]) }}"
               class="inline-flex h-9 items-center rounded-xl px-4 text-sm font-semibold {{ $tab === $key ? 'bg-accent text-white' : 'border border-line text-ink hover:bg-canvas' }}">
                {{ $labelTab }}
            </a>
        @endforeach
    </nav>

    @if($tab === 'profile')
        <div class="grid gap-5 lg:grid-cols-3">
            <form method="POST" action="{{ route('admin.teachers.update-profile', $teacher) }}" class="lg:col-span-2 space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                @csrf
                @method('PUT')
                <h3 class="text-base font-semibold text-ink">بيانات الحساب</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="{{ $label }}">الاسم *</label>
                        <input type="text" name="name" required value="{{ old('name', $teacher->name) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="{{ $label }}">البريد</label>
                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="{{ $label }}">الهاتف</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="{{ $label }}">الدور</label>
                        <select name="role" class="{{ $field }}">
                            <option value="instructor" @selected(old('role', $teacher->role) === 'instructor')>instructor</option>
                            <option value="teacher" @selected(old('role', $teacher->role) === 'teacher')>teacher</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="{{ $label }}">نبذة</label>
                    <textarea name="bio" rows="3" class="{{ $field }} py-3">{{ old('bio', $teacher->bio) }}</textarea>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="{{ $label }}">كلمة مرور جديدة (اختياري)</label>
                        <input type="password" name="password" class="{{ $field }}" autocomplete="new-password">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $teacher->is_active))>
                            حساب مفعّل
                        </label>
                    </div>
                </div>
                <button class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-semibold text-white">حفظ البيانات</button>
            </form>

            <aside class="space-y-3 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-base font-semibold text-ink">روابط سريعة</h3>
                <ul class="space-y-2 text-sm">
                    @if(Route::has('admin.academy-instructors.show'))
                        <li><a class="text-accent hover:underline" href="{{ route('admin.academy-instructors.show', $teacher) }}">توصيف الطلاب</a></li>
                    @endif
                    @if(Route::has('admin.tutor-work-schedules.index'))
                        <li><a class="text-accent hover:underline" href="{{ route('admin.tutor-work-schedules.index', ['instructor_id' => $teacher->id]) }}">جدول المجموعات (الشاشة الكاملة)</a></li>
                    @endif
                    @if(Route::has('admin.one-to-one-sessions.index'))
                        <li><a class="text-accent hover:underline" href="{{ route('admin.one-to-one-sessions.index', ['instructor_id' => $teacher->id]) }}">كل حصص 1:1</a></li>
                    @endif
                    @if(Route::has('admin.tutoring-group-bookings.index'))
                        <li><a class="text-accent hover:underline" href="{{ route('admin.tutoring-group-bookings.index', ['instructor_id' => $teacher->id]) }}">كل الحجوزات</a></li>
                    @endif
                    @if(Route::has('admin.libraries.videos.index'))
                        <li><a class="text-accent hover:underline" href="{{ route('admin.libraries.videos.index', ['audience' => 'teacher_students']) }}">فيديوهات المكتبة (معلمين)</a></li>
                    @endif
                    @if($application && Route::has('admin.tutor-applications.show'))
                        <li><a class="text-accent hover:underline" href="{{ route('admin.tutor-applications.show', $application) }}">طلب التوظيف #{{ $application->id }}</a></li>
                    @endif
                    @if(Route::has('admin.quality-control.instructors'))
                        <li><a class="text-accent hover:underline" href="{{ route('admin.quality-control.instructors') }}">مراقبة الجودة</a></li>
                    @endif
                </ul>
            </aside>
        </div>
    @endif

    @if($tab === 'schedule')
        <div class="grid gap-5 xl:grid-cols-2">
            <form method="POST" action="{{ route('admin.teachers.sync-work-schedule', $teacher) }}" class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft" x-data="teacherSlots(@js($workSlotsFlat))">
                @csrf
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-ink">جدول مجموعات التدريس</h3>
                        <p class="text-xs text-muted mt-1">نوافذ العمل للحجوزات الفردية/الجماعية.</p>
                    </div>
                    <button type="button" @click="addSlot(1)" class="text-xs font-semibold text-accent">+ نافذة</button>
                </div>
                <template x-for="(slot, idx) in slots" :key="idx">
                    <div class="grid gap-2 md:grid-cols-6 items-end border border-line rounded-xl p-3">
                        <div>
                            <label class="{{ $label }}">اليوم</label>
                            <select class="{{ $field }}" x-model="slot.day_of_week" :name="`slots[${idx}][day_of_week]`">
                                @foreach($workDayLabels as $day => $dayLabel)
                                    <option value="{{ $day }}">{{ $dayLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">من</label>
                            <input type="time" class="{{ $field }}" x-model="slot.start_time" :name="`slots[${idx}][start_time]`">
                        </div>
                        <div>
                            <label class="{{ $label }}">إلى</label>
                            <input type="time" class="{{ $field }}" x-model="slot.end_time" :name="`slots[${idx}][end_time]`">
                        </div>
                        <div>
                            <label class="{{ $label }}">المدة</label>
                            <input type="number" min="30" max="240" class="{{ $field }}" x-model="slot.slot_duration_minutes" :name="`slots[${idx}][slot_duration_minutes]`">
                        </div>
                        <div>
                            <label class="{{ $label }}">ينطبق على</label>
                            <select class="{{ $field }}" x-model="slot.applies_to" :name="`slots[${idx}][applies_to]`">
                                <option value="both">فردي+جماعي</option>
                                <option value="individual">فردي</option>
                                <option value="collective">جماعي</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" class="{{ $field }}" placeholder="ملاحظة" x-model="slot.note" :name="`slots[${idx}][note]`">
                            <button type="button" class="h-11 px-3 text-danger" @click="removeSlot(idx)">×</button>
                        </div>
                    </div>
                </template>
                <p class="text-xs text-muted" x-show="slots.length === 0">لا توجد نوافذ — الحفظ سيمسح الجدول الحالي.</p>
                <button class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-semibold text-white">حفظ جدول المجموعات</button>
            </form>

            <form method="POST" action="{{ route('admin.teachers.sync-oto-availability', $teacher) }}" class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft" x-data="teacherSlots(@js($otoSlotsFlat))">
                @csrf
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-ink">توفر الحصص الخاصة (1:1)</h3>
                        <p class="text-xs text-muted mt-1">نفس تحكم المعلم — من الإدارة مباشرة.</p>
                    </div>
                    <button type="button" @click="addSlot(1)" class="text-xs font-semibold text-accent">+ نافذة</button>
                </div>
                <template x-for="(slot, idx) in slots" :key="idx">
                    <div class="grid gap-2 md:grid-cols-5 items-end border border-line rounded-xl p-3">
                        <div>
                            <label class="{{ $label }}">اليوم</label>
                            <select class="{{ $field }}" x-model="slot.day_of_week" :name="`slots[${idx}][day_of_week]`">
                                @foreach($otoDayLabels as $day => $dayLabel)
                                    <option value="{{ $day }}">{{ $dayLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">من</label>
                            <input type="time" class="{{ $field }}" x-model="slot.start_time" :name="`slots[${idx}][start_time]`">
                        </div>
                        <div>
                            <label class="{{ $label }}">إلى</label>
                            <input type="time" class="{{ $field }}" x-model="slot.end_time" :name="`slots[${idx}][end_time]`">
                        </div>
                        <div>
                            <label class="{{ $label }}">المدة</label>
                            <input type="number" min="30" max="180" class="{{ $field }}" x-model="slot.slot_duration_minutes" :name="`slots[${idx}][slot_duration_minutes]`">
                        </div>
                        <button type="button" class="h-11 px-3 text-danger" @click="removeSlot(idx)">حذف</button>
                    </div>
                </template>
                <p class="text-xs text-muted" x-show="slots.length === 0">لا توجد نوافذ — الحفظ سيمسح التوفر الحالي.</p>
                <button class="btn-press inline-flex h-11 items-center rounded-xl bg-ink px-5 text-sm font-semibold text-white">حفظ توفر 1:1</button>
            </form>
        </div>
    @endif

    @if($tab === 'sessions')
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach(['upcoming' => 'قادم', 'past' => 'سابق', 'all' => 'الكل'] as $key => $labelRange)
                <a href="{{ route('admin.teachers.show', ['teacher' => $teacher, 'tab' => 'sessions', 'range' => $key]) }}"
                   class="inline-flex h-8 items-center rounded-full px-3 text-xs font-bold {{ $range === $key ? 'bg-accent text-white' : 'border border-line' }}">{{ $labelRange }}</a>
            @endforeach
            @if(Route::has('admin.one-to-one-sessions.create'))
                <a href="{{ route('admin.one-to-one-sessions.create') }}" class="inline-flex h-8 items-center rounded-full border border-line px-3 text-xs font-bold text-accent">إنشاء تسكين جديد</a>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas-muted text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start">الطالب</th>
                        <th class="px-4 py-3 text-start">الحالة</th>
                        <th class="px-4 py-3 text-start">الموعد</th>
                        <th class="px-4 py-3 text-start">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr class="border-t border-line align-top">
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $session->student->name ?? '—' }}</div>
                                <div class="text-xs text-muted">#{{ $session->id }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $session->statusLabel() }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $session->scheduled_at?->format('Y-m-d H:i') ?: '—' }}</td>
                            <td class="px-4 py-3">
                                @if(in_array($session->status, [\App\Models\OneToOneSession::STATUS_PENDING, \App\Models\OneToOneSession::STATUS_SCHEDULED], true))
                                    <form method="POST" action="{{ route('admin.teachers.sessions.schedule', [$teacher, $session]) }}" class="flex flex-wrap gap-2 mb-2">
                                        @csrf
                                        <input type="datetime-local" name="scheduled_at" required class="h-9 rounded-lg border border-line px-2 text-xs" value="{{ optional($session->scheduled_at)->format('Y-m-d\TH:i') }}">
                                        <label class="inline-flex items-center gap-1 text-[11px] text-muted">
                                            <input type="checkbox" name="force" value="1"> تجاوز التوفر
                                        </label>
                                        <button class="h-9 rounded-lg bg-accent px-3 text-xs font-bold text-white">جدولة</button>
                                    </form>
                                    <div class="flex flex-wrap gap-2">
                                        @if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED)
                                            <form method="POST" action="{{ route('admin.teachers.sessions.complete', [$teacher, $session]) }}">@csrf<button class="text-xs font-bold text-success">إكمال</button></form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.teachers.sessions.cancel', [$teacher, $session]) }}" onsubmit="return confirm('إلغاء الحصة؟')">@csrf<button class="text-xs font-bold text-danger">إلغاء</button></form>
                                        <form method="POST" action="{{ route('admin.teachers.sessions.reassign', [$teacher, $session]) }}" class="flex gap-1 items-center">
                                            @csrf
                                            <select name="instructor_id" class="h-8 rounded-lg border border-line text-xs">
                                                @foreach($instructors as $ins)
                                                    <option value="{{ $ins->id }}" @selected((int)$ins->id === (int)$teacher->id)>{{ $ins->name }}</option>
                                                @endforeach
                                            </select>
                                            <button class="text-xs font-bold text-accent">نقل</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-muted">لا إجراءات</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-muted">لا توجد حصص في هذا النطاق.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $sessions->links() }}</div>
    @endif

    @if($tab === 'bookings')
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach(['upcoming' => 'قادم', 'past' => 'سابق', 'all' => 'الكل'] as $key => $labelRange)
                <a href="{{ route('admin.teachers.show', ['teacher' => $teacher, 'tab' => 'bookings', 'range' => $key]) }}"
                   class="inline-flex h-8 items-center rounded-full px-3 text-xs font-bold {{ $range === $key ? 'bg-accent text-white' : 'border border-line' }}">{{ $labelRange }}</a>
            @endforeach
            @if(Route::has('admin.tutoring-group-bookings.create'))
                <a href="{{ route('admin.tutoring-group-bookings.create') }}" class="inline-flex h-8 items-center rounded-full border border-line px-3 text-xs font-bold text-accent">حجز جديد</a>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas-muted text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start">الطالب</th>
                        <th class="px-4 py-3 text-start">المجموعة</th>
                        <th class="px-4 py-3 text-start">الموعد</th>
                        <th class="px-4 py-3 text-start">الحالة</th>
                        <th class="px-4 py-3 text-start">تحديث</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr class="border-t border-line">
                            <td class="px-4 py-3 font-semibold">{{ $booking->user->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $booking->tutoringGroup->title ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $booking->starts_at?->format('Y-m-d H:i') ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $booking->statusLabel() }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.teachers.bookings.status', [$teacher, $booking]) }}" class="flex flex-wrap gap-2 items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="h-8 rounded-lg border border-line text-xs">
                                        @foreach([
                                            'pending' => 'قيد المراجعة',
                                            'confirmed' => 'مؤكد',
                                            'cancelled' => 'ملغي',
                                            'completed' => 'مكتمل',
                                        ] as $st => $stLabel)
                                            <option value="{{ $st }}" @selected($booking->status === $st)>{{ $stLabel }}</option>
                                        @endforeach
                                    </select>
                                    <button class="text-xs font-bold text-accent">حفظ</button>
                                    @if(Route::has('admin.tutoring-group-bookings.show'))
                                        <a href="{{ route('admin.tutoring-group-bookings.show', $booking) }}" class="text-xs text-muted hover:underline">تفاصيل</a>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-muted">لا توجد حجوزات في هذا النطاق.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $bookings->links() }}</div>
    @endif

    @if($tab === 'courses')
        <div class="grid gap-5 lg:grid-cols-2">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-base font-semibold text-ink mb-3">الكورسات المسندة</h3>
                <ul class="space-y-2 text-sm">
                    @forelse($courses as $course)
                        <li class="flex items-center justify-between gap-3 border-b border-line py-2">
                            <div>
                                <div class="font-semibold">{{ $course->title }}</div>
                                <div class="text-xs text-muted">{{ $course->academicYear->name ?? '' }} · {{ $course->academicSubject->name ?? '' }}</div>
                            </div>
                            @if(Route::has('admin.advanced-courses.edit'))
                                <a href="{{ route('admin.advanced-courses.edit', $course) }}" class="text-xs font-bold text-accent">تعديل</a>
                            @endif
                        </li>
                    @empty
                        <li class="text-muted">لا توجد كورسات مسندة.</li>
                    @endforelse
                </ul>
            </article>
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-base font-semibold text-ink mb-3">مجموعات التدريس</h3>
                <ul class="space-y-2 text-sm">
                    @forelse($groups as $group)
                        <li class="flex items-center justify-between gap-3 border-b border-line py-2">
                            <div>
                                <div class="font-semibold">{{ $group->title }}</div>
                                <div class="text-xs text-muted">{{ $group->type }} · {{ $group->is_active ? 'نشط' : 'متوقف' }}</div>
                            </div>
                            @if(Route::has('admin.tutoring-groups.edit'))
                                <a href="{{ route('admin.tutoring-groups.edit', [$group->type, $group]) }}" class="text-xs font-bold text-accent">تعديل</a>
                            @endif
                        </li>
                    @empty
                        <li class="text-muted">لا توجد مجموعات.</li>
                    @endforelse
                </ul>
            </article>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function teacherSlots(initial) {
    return {
        slots: Array.isArray(initial) ? initial.map(function (s) { return Object.assign({}, s); }) : [],
        addSlot: function (day) {
            this.slots.push({
                day_of_week: day || 1,
                start_time: '09:00',
                end_time: '12:00',
                slot_duration_minutes: 60,
                applies_to: 'both',
                note: ''
            });
        },
        removeSlot: function (idx) {
            this.slots.splice(idx, 1);
        }
    };
}
</script>
@endpush
