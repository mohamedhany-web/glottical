@extends('layouts.app')

@section('title', __('instructor.lecture_details') . ' - ' . $lecture->title)
@section('header', __('instructor.lecture_details'))

@push('styles')
<style>
    .dashboard-card-lecture {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 2px solid rgba(226, 232, 240, 0.9);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dashboard-card-lecture:hover {
        border-color: rgba(14, 165, 233, 0.3);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    .stats-mini-lecture {
        background: #fff;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .stats-mini-lecture:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .student-row-lecture:hover {
        background: linear-gradient(to left, rgba(14, 165, 233, 0.04), transparent);
    }
</style>
@endpush

@push('scripts')
<script>
function updateAttendance(studentId, status) {
    const formData = {
        student_id: studentId,
        status: status,
        _token: '{{ csrf_token() }}'
    };
    
    fetch('{{ route("instructor.lectures.update-attendance", $lecture) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || @json(__('instructor.attendance_update_error')));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(@json(__('instructor.attendance_update_error')));
    });
}

function updateStatus(status) {
    fetch('{{ route("instructor.lectures.update-status", $lecture) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || @json(__('instructor.status_update_error')));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(@json(__('instructor.status_update_error')));
    });
}
</script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('instructor.lectures.index') }}" class="hover:text-white transition-colors">{{ __('instructor.lectures') }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white font-semibold">{{ Str::limit($lecture->title, 60) }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-chalkboard-teacher text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-black leading-tight truncate">{{ $lecture->title }}</h1>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white/15 border border-white/20">
                                @if($lecture->status == 'scheduled')
                                    <i class="fas fa-calendar-alt"></i> مجدولة
                                @elseif($lecture->status == 'in_progress')
                                    <i class="fas fa-clock"></i> قيد التنفيذ
                                @elseif($lecture->status == 'completed')
                                    <i class="fas fa-check-circle"></i> مكتملة
                                @else
                                    <i class="fas fa-times-circle"></i> ملغاة
                                @endif
                            </span>
                            @if($lecture->course)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white/10 border border-white/15">
                                    <i class="fas fa-book"></i>
                                    {{ Str::limit($lecture->course->title, 40) }}
                                </span>
                            @endif
                            @if($lecture->scheduled_at)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white/10 border border-white/15">
                                    <i class="fas fa-calendar"></i>
                                    <x-app-datetime :at="$lecture->scheduled_at" pattern="Y/m/d H:i" />
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('instructor.lectures.edit', $lecture) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-indigo-700 hover:bg-white/90 border border-white/20 font-extrabold transition-colors">
                    <i class="fas fa-edit"></i>
                    <span>{{ __('common.edit') ?? 'تعديل' }}</span>
                </a>
                <a href="{{ route('instructor.lectures.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 border border-white/20 text-white font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>{{ __('common.back') ?? 'العودة' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات السريعة -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="stats-mini-lecture rounded-2xl p-4 text-center shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-check-circle text-sm"></i>
            </div>
            <div class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $attendanceStats['present'] ?? 0 }}</div>
            <div class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1">حاضر</div>
        </div>
        <div class="stats-mini-lecture rounded-2xl p-4 text-center shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <div class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $attendanceStats['late'] ?? 0 }}</div>
            <div class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1">متأخر</div>
        </div>
        <div class="stats-mini-lecture rounded-2xl p-4 text-center shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-user-clock text-sm"></i>
            </div>
            <div class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $attendanceStats['partial'] ?? 0 }}</div>
            <div class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1">جزئي</div>
        </div>
        <div class="stats-mini-lecture rounded-2xl p-4 text-center shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-times-circle text-sm"></i>
            </div>
            <div class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $attendanceStats['absent'] ?? 0 }}</div>
            <div class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1">غائب</div>
        </div>
        <div class="stats-mini-lecture rounded-2xl p-4 text-center shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-users text-sm"></i>
            </div>
            <div class="text-xl font-black text-slate-800 dark:text-slate-100">{{ $attendanceStats['total_students'] ?? 0 }}</div>
            <div class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1">إجمالي</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- معلومات المحاضرة -->
        <div class="xl:col-span-2 space-y-6">
            <!-- معلومات أساسية -->
            <div class="dashboard-card-lecture rounded-2xl p-5 sm:p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-info-circle text-sky-600"></i>
                        معلومات المحاضرة
                    </h3>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">الحالة</label>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                    @if($lecture->status == 'scheduled') bg-gradient-to-r from-sky-500 to-blue-600 text-white
                                    @elseif($lecture->status == 'in_progress') bg-gradient-to-r from-amber-400 to-amber-500 text-white
                                    @elseif($lecture->status == 'completed') bg-gradient-to-r from-emerald-500 to-green-600 text-white
                                    @else bg-gradient-to-r from-red-500 to-rose-600 text-white
                                    @endif">
                                    @if($lecture->status == 'scheduled') مجدولة
                                    @elseif($lecture->status == 'in_progress') قيد التنفيذ
                                    @elseif($lecture->status == 'completed') مكتملة
                                    @else ملغاة
                                    @endif
                                </span>
                                @if($lecture->status != 'completed' && $lecture->status != 'cancelled')
                                <select onchange="updateStatus(this.value)"
                                        class="text-xs border-2 border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 bg-white dark:bg-slate-800/95 text-slate-800 dark:text-slate-100 font-bold focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all">
                                    <option value="scheduled" {{ $lecture->status == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                    <option value="in_progress" {{ $lecture->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="completed" {{ $lecture->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                    <option value="cancelled" {{ $lecture->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">التاريخ والوقت</label>
                            <div class="flex items-center gap-2 text-slate-800 dark:text-slate-100 font-black">
                                <i class="fas fa-calendar-alt text-sky-600"></i>
                                <x-app-datetime :at="$lecture->scheduled_at" pattern="Y/m/d H:i" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">المدة</label>
                            <div class="flex items-center gap-2 text-slate-800 dark:text-slate-100 font-black">
                                <i class="fas fa-clock text-amber-500"></i>
                                {{ $lecture->duration_minutes }} دقيقة
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">الكورس</label>
                            <div class="flex items-center gap-2 text-slate-800 dark:text-slate-100 font-bold">
                                <i class="fas fa-book text-sky-600"></i>
                                {{ $lecture->course->title ?? 'غير محدد' }}
                            </div>
                        </div>
                        @if($lecture->lesson)
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">الدرس</label>
                            <div class="flex items-center gap-2 text-slate-800 dark:text-slate-100 font-bold">
                                <i class="fas fa-play-circle text-violet-600"></i>
                                {{ $lecture->lesson->title }}
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($lecture->description)
                    <div class="pt-4 border-t-2 border-slate-200 dark:border-slate-700">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">الوصف</label>
                        <div class="text-slate-700 dark:text-slate-300 font-medium bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                            {{ $lecture->description }}
                        </div>
                    </div>
                    @endif

                    @if($lecture->notes)
                    <div class="pt-4 border-t-2 border-slate-200 dark:border-slate-700">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">الملاحظات</label>
                        <div class="text-slate-700 dark:text-slate-300 font-medium bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                            {{ $lecture->notes }}
                        </div>
                    </div>
                    @endif

                    @if($lecture->recording_url)
                    <div class="pt-4 border-t-2 border-slate-200 dark:border-slate-700">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wide">الروابط</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <a href="{{ $lecture->recording_url }}" target="_blank"
                               class="flex items-center gap-3 p-4 bg-violet-50 dark:bg-violet-900/30 rounded-xl border-2 border-violet-200 hover:bg-violet-100 hover:border-violet-300 transition-all shadow-sm">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                                    <i class="fas fa-play-circle text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 dark:text-slate-100 text-sm">تسجيل المحاضرة</div>
                                    <div class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-0.5">رابط التسجيل</div>
                                </div>
                                <i class="fas fa-external-link-alt text-violet-600 text-sm"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- إدارة الحضور -->
            @if($lecture->has_attendance_tracking)
            <div class="dashboard-card-lecture rounded-2xl p-5 sm:p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-sky-600"></i>
                        إدارة الحضور والغياب
                    </h3>
                    <div class="text-sm text-slate-600 dark:text-slate-400 font-bold">
                        إجمالي: <span class="text-sky-600">{{ $attendanceStats['total_students'] }}</span>
                    </div>
                </div>
                <div>
                    @if($enrollments->count() > 0)
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800/40">
                                    <tr>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">الطالب</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">الحالة</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">دقائق الحضور</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800/95 divide-y divide-slate-200 dark:divide-slate-700">
                                    @foreach($enrollments as $enrollment)
                                    @php
                                        $record = $attendanceRecords->get($enrollment->user_id);
                                    @endphp
                                    <tr class="student-row-lecture">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white font-black shadow-md">
                                                    {{ mb_substr($enrollment->user->name ?? 'ط', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-black text-slate-800 dark:text-slate-100">
                                                        {{ $enrollment->user->name ?? 'غير محدد' }}
                                                    </div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                        {{ $enrollment->user->email ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($record)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                                    @if($record->status == 'present') bg-gradient-to-r from-emerald-500 to-green-600 text-white
                                                    @elseif($record->status == 'late') bg-gradient-to-r from-amber-400 to-amber-500 text-white
                                                    @elseif($record->status == 'partial') bg-gradient-to-r from-sky-500 to-blue-600 text-white
                                                    @else bg-gradient-to-r from-red-500 to-rose-600 text-white
                                                    @endif">
                                                    @if($record->status == 'present')
                                                        <i class="fas fa-check-circle"></i>
                                                        حاضر
                                                    @elseif($record->status == 'late')
                                                        <i class="fas fa-clock"></i>
                                                        متأخر
                                                    @elseif($record->status == 'partial')
                                                        <i class="fas fa-user-clock"></i>
                                                        جزئي
                                                    @else
                                                        <i class="fas fa-times-circle"></i>
                                                        غائب
                                                    @endif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-400 text-white shadow-md">
                                                    <i class="fas fa-question-circle"></i>
                                                    غير محدد
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-slate-800 dark:text-slate-100">
                                                {{ $record && isset($record->attendance_minutes) ? $record->attendance_minutes : 0 }} / {{ $lecture->duration_minutes }}
                                            </div>
                                            @if($record && isset($record->attendance_percentage) && $record->attendance_percentage)
                                                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                    {{ number_format($record->attendance_percentage, 1) }}%
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <select onchange="updateAttendance({{ $enrollment->user_id }}, this.value)"
                                                    class="text-xs border-2 border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 bg-white dark:bg-slate-800/95 text-slate-800 dark:text-slate-100 font-bold focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all">
                                                <option value="present" {{ $record && $record->status == 'present' ? 'selected' : '' }}>حاضر</option>
                                                <option value="late" {{ $record && $record->status == 'late' ? 'selected' : '' }}>متأخر</option>
                                                <option value="partial" {{ $record && $record->status == 'partial' ? 'selected' : '' }}>جزئي</option>
                                                <option value="absent" {{ !$record || $record->status == 'absent' ? 'selected' : '' }}>غائب</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-700/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-4xl text-slate-400"></i>
                            </div>
                            <p class="text-lg font-black text-slate-800 dark:text-slate-100 mb-2">لا يوجد طلاب مسجلين</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">لا يوجد طلاب مسجلين في هذا الكورس</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- الخيارات -->
            <div class="dashboard-card-lecture rounded-2xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-cog text-sky-600"></i>
                        الخيارات
                    </h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-sm text-slate-700 dark:text-slate-300 font-bold">تتبع الحضور</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $lecture->has_attendance_tracking ? 'bg-emerald-600 dark:bg-emerald-700 text-white' : 'bg-slate-400 text-white' }}">
                            <i class="fas {{ $lecture->has_attendance_tracking ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $lecture->has_attendance_tracking ? 'مفعل' : 'معطل' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/30 rounded-xl border border-amber-200">
                        <span class="text-sm text-slate-700 dark:text-slate-300 font-bold">يوجد واجب</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $lecture->has_assignment ? 'bg-emerald-600 dark:bg-emerald-700 text-white' : 'bg-slate-400 text-white' }}">
                            <i class="fas {{ $lecture->has_assignment ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $lecture->has_assignment ? 'نعم' : 'لا' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-violet-50 dark:bg-violet-900/30 rounded-xl border border-violet-200">
                        <span class="text-sm text-slate-700 dark:text-slate-300 font-bold">يوجد تقييم</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $lecture->has_evaluation ? 'bg-emerald-600 dark:bg-emerald-700 text-white' : 'bg-slate-400 text-white' }}">
                            <i class="fas {{ $lecture->has_evaluation ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $lecture->has_evaluation ? 'نعم' : 'لا' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- إجراءات سريعة -->
            <div class="dashboard-card-lecture rounded-2xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-bolt text-sky-600"></i>
                        إجراءات سريعة
                    </h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('instructor.lectures.edit', $lecture) }}"
                       class="flex items-center gap-3 p-3 bg-sky-50 dark:bg-sky-900/30 hover:bg-sky-100 rounded-xl border border-sky-200 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 dark:text-slate-100 text-sm">تعديل المحاضرة</span>
                    </a>
                    @if($lecture->course)
                    <a href="{{ route('instructor.courses.show', $lecture->course) }}"
                       class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:bg-emerald-900/40 rounded-xl border border-emerald-200 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-book text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 dark:text-slate-100 text-sm">عرض الكورس</span>
                    </a>
                    @endif
                    <a href="{{ route('instructor.attendance.lecture', $lecture) }}"
                       class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 rounded-xl border border-blue-200 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-clipboard-list text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 dark:text-slate-100 text-sm">تفاصيل الحضور</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
