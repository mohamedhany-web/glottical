@extends('layouts.admin')

@section('title', __('student.one_to_one_admin_session', ['id' => $session->id]))
@section('header', __('student.one_to_one_admin_session', ['id' => $session->id]))

@section('content')
<div class="space-y-6 max-w-5xl">
    <a href="{{ route('admin.one-to-one-sessions.index') }}" class="text-sm text-sky-600 hover:underline">← {{ __('student.one_to_one_admin_title') }}</a>
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

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
                        <x-app-datetime :at="$session->scheduled_at" />
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
            @if($session->entitlement)
            <div>
                <dt class="text-slate-500">الرصيد المرتبط</dt>
                <dd class="font-semibold">
                    #{{ $session->entitlement->id }} · {{ $session->entitlement->unitsLeft() }} / {{ $session->entitlement->units_total }} متبقٍ
                    @if($session->entitlement->order)
                        · <a href="{{ route('admin.orders.show', $session->entitlement->order) }}" class="text-sky-600">الطلب #{{ $session->entitlement->order_id }}</a>
                    @endif
                </dd>
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
        @if($session->isOpenPlacement())
            <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                @if($session->series_id)
                    <form method="POST" action="{{ route('admin.one-to-one-sessions.destroy', $session) }}"
                          onsubmit="return confirm('حذف التسكين كاملاً؟ سيتم إلغاء كل الحصص غير المكتملة في هذه السلسلة وإرجاع الرصيد المحجوز.');">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="series" value="1">
                        <button type="submit" class="inline-flex h-10 items-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-medium text-rose-700 hover:bg-rose-100">حذف التسكين كاملاً</button>
                    </form>
                    <form method="POST" action="{{ route('admin.one-to-one-sessions.destroy', $session) }}"
                          onsubmit="return confirm('إلغاء هذه الحصة فقط؟ باقي حصص التسكين تبقى كما هي.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex h-10 items-center rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">إلغاء هذه الحصة فقط</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.one-to-one-sessions.destroy', $session) }}"
                          onsubmit="return confirm('حذف هذا التسكين؟ سيُلغى الموعد ويُعاد الرصيد المحجوز.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex h-10 items-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-medium text-rose-700 hover:bg-rose-100">حذف التسكين</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    @if($session->isOpenPlacement())
        @php
            $currentInSeries = \App\Services\OneToOneSessionUnlockService::currentSessionInSeries($session);
            $isCurrent = $currentInSeries && (int) $currentInSeries->id === (int) $session->id;
            $isManuallyUnlocked = \App\Services\OneToOneSessionUnlockService::isManuallyUnlocked($session);
        @endphp
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-900">فتح الحصة للطالب</h3>
            <p class="text-sm text-slate-500">
                الحصص تُفتح للطالب بالتسلسل — الحصة الحالية في التسكين هي رقم
                <strong>{{ $currentInSeries?->session_number ?? '—' }}</strong>.
                يمكنك فتح هذه الحصة يدوياً لتجاوز التسلسل.
            </p>
            @if($isManuallyUnlocked)
                <p class="text-sm text-emerald-700">
                    مفتوحة يدوياً
                    @if($session->student_unlocked_at)
                        · {{ $session->student_unlocked_at->format('Y-m-d H:i') }}
                    @endif
                    @if($session->studentUnlockedBy)
                        · بواسطة {{ $session->studentUnlockedBy->name }}
                    @endif
                </p>
                <form method="POST" action="{{ route('admin.one-to-one-sessions.revoke-unlock', $session) }}">
                    @csrf
                    <button type="submit" class="inline-flex h-10 items-center rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        إلغاء الفتح اليدوي
                    </button>
                </form>
            @elseif($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED)
                <form method="POST" action="{{ route('admin.one-to-one-sessions.unlock-for-student', $session) }}">
                    @csrf
                    <button type="submit" class="inline-flex h-10 items-center rounded-xl bg-emerald-600 px-4 text-sm font-medium text-white hover:bg-emerald-700">
                        فتح الحصة للطالب
                    </button>
                </form>
            @endif
            @if($isCurrent && ! $isManuallyUnlocked)
                <p class="text-xs text-slate-400">هذه هي الحصة الحالية — مفتوحة تلقائياً للطالب.</p>
            @endif
        </div>
    @endif

    @if($session->isOpenPlacement())
        @php
            $instructorTz = \App\Support\AppTimezone::forUser($session->instructor);
            $scheduledLocal = \App\Support\AppTimezone::datetimeLocalValue($session->scheduled_at, $instructorTz);
        @endphp
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-900">تعديل موعد الحصة</h3>
            <p class="text-sm text-slate-500">اكتب اليوم والساعة بحرية — يُحدَّث موعد Classroom تلقائياً ويُشعَر الطالب والمعلم.</p>
            <form method="POST" action="{{ route('admin.one-to-one-sessions.update-schedule', $session) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                @method('PATCH')
                <div class="min-w-[14rem] flex-1">
                    <label class="mb-1 block text-xs font-medium text-slate-500">الموعد (توقيت المعلم: {{ $instructorTz }})</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $scheduledLocal) }}" required dir="ltr"
                           class="h-10 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                </div>
                <div class="w-24">
                    <label class="mb-1 block text-xs font-medium text-slate-500">المدة (د)</label>
                    <input type="number" name="duration_minutes" min="15" max="180" step="5"
                           value="{{ old('duration_minutes', (int) ($session->duration_minutes ?: 50)) }}"
                           class="h-10 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                </div>
                <input type="hidden" name="timezone" value="{{ $instructorTz }}">
                <button type="submit" class="inline-flex h-10 items-center rounded-xl bg-sky-600 px-4 text-sm font-semibold text-white hover:bg-sky-500">
                    حفظ الموعد
                </button>
            </form>
        </div>
    @endif

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
