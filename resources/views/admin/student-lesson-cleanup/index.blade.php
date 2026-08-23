@extends('layouts.admin')

@section('title', 'تنظيف الحصص والبيانات التجريبية')
@section('page_title', 'تنظيف الحصص')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs text-muted">الطلاب والخدمات</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">تنظيف الحصص والبيانات التجريبية</h2>
            <p class="mt-1 max-w-3xl text-sm text-muted">
                احذف نهائياً أي حصة 1:1 أو حجز مجموعة أو غرفة Classroom أو بث تجريبي من عند الطلاب — مفيد لمسح تجارب التسكين والبث الإداري.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">حصص 1:1</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $stats['one_to_one'] }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">حجوزات مجموعات</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $stats['group'] }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">غرف Classroom</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $stats['meetings'] }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">جلسات بث</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $stats['live'] }}</p>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-soft">
            <p class="text-xs font-medium text-amber-800">بث إداري / تجريبي</p>
            <p class="mt-1 text-2xl font-semibold text-amber-900">{{ $stats['live_admin'] }}</p>
        </article>
    </div>

    <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-xs font-medium text-muted">النوع</label>
                <select name="type" class="h-10 rounded-xl border border-line bg-white px-3 text-sm">
                    <option value="one_to_one" @selected($type === 'one_to_one')>حصص 1:1</option>
                    <option value="group" @selected($type === 'group')>حجوزات مجموعات</option>
                    <option value="meetings" @selected($type === 'meetings')>غرف Classroom</option>
                    <option value="live" @selected($type === 'live')>بث مباشر</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-muted">طالب</label>
                <select name="student_id" class="h-10 min-w-[12rem] rounded-xl border border-line bg-white px-3 text-sm">
                    <option value="0">الكل</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected((int) $studentId === (int) $student->id)>{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[12rem] flex-1">
                <label class="mb-1 block text-xs font-medium text-muted">بحث</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="اسم / بريد / ملاحظات / رقم" class="h-10 w-full rounded-xl border border-line bg-white px-3 text-sm">
            </div>
            <label class="inline-flex h-10 items-center gap-2 rounded-xl border border-line bg-white px-3 text-sm">
                <input type="checkbox" name="experimental" value="1" @checked($experimentalOnly)>
                تجريبي فقط
            </label>
            <button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">تصفية</button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.student-lesson-cleanup.bulk') }}" id="cleanup-bulk-form"
          onsubmit="return confirm('حذف نهائي للعناصر المحددة؟ لا يمكن التراجع.');">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-muted">حدّد صفوفاً ثم احذف دفعة واحدة، أو احذف سطراً بسطر.</p>
            <button type="submit" class="inline-flex h-9 items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                <i class="fas fa-trash"></i> حذف المحدد
            </button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-line text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                        <tr>
                            <th class="px-3 py-3 text-right"><input type="checkbox" id="cleanup-check-all" class="rounded border-slate-300"></th>
                            <th class="px-4 py-3 text-right">#</th>
                            @if($type === 'one_to_one')
                                <th class="px-4 py-3 text-right">الطالب</th>
                                <th class="px-4 py-3 text-right">المعلم</th>
                                <th class="px-4 py-3 text-right">الموعد</th>
                                <th class="px-4 py-3 text-right">الحالة</th>
                                <th class="px-4 py-3 text-right">ملاحظات</th>
                            @elseif($type === 'group')
                                <th class="px-4 py-3 text-right">الطالب</th>
                                <th class="px-4 py-3 text-right">المجموعة</th>
                                <th class="px-4 py-3 text-right">الموعد</th>
                                <th class="px-4 py-3 text-right">الحالة</th>
                            @elseif($type === 'meetings')
                                <th class="px-4 py-3 text-right">العنوان</th>
                                <th class="px-4 py-3 text-right">الكود</th>
                                <th class="px-4 py-3 text-right">المضيف</th>
                                <th class="px-4 py-3 text-right">البداية / النهاية</th>
                            @else
                                <th class="px-4 py-3 text-right">العنوان</th>
                                <th class="px-4 py-3 text-right">المقدم</th>
                                <th class="px-4 py-3 text-right">الحالة</th>
                                <th class="px-4 py-3 text-right">ملاحظات</th>
                            @endif
                            <th class="px-4 py-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rows as $row)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-3 py-3"><input type="checkbox" name="ids[]" value="{{ $row->id }}" class="cleanup-row-check rounded border-slate-300"></td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $row->id }}</td>

                                @if($type === 'one_to_one')
                                    <td class="px-4 py-3">{{ $row->student?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $row->instructor?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ optional($row->scheduled_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="px-4 py-3"><span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ $row->statusLabel() }}</span></td>
                                    <td class="px-4 py-3 max-w-[14rem] truncate text-xs text-muted" title="{{ $row->notes }}">{{ $row->notes ?: '—' }}</td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.student-lesson-cleanup.destroy-one-to-one', $row) }}"
                                              onsubmit="return confirm('حذف نهائي لهذه الحصة؟');">
                                            @csrf
                                            @method('DELETE')
                                            @if($row->series_id)
                                                <input type="hidden" name="series" value="1">
                                            @endif
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">حذف نهائي</button>
                                        </form>
                                    </td>
                                @elseif($type === 'group')
                                    <td class="px-4 py-3">{{ $row->user?->name ?? ($row->guest_name ?: '—') }}</td>
                                    <td class="px-4 py-3">{{ $row->tutoringGroup?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ optional($row->starts_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="px-4 py-3"><span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ $row->status }}</span></td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.student-lesson-cleanup.destroy-group', $row) }}"
                                              onsubmit="return confirm('حذف نهائي لهذا الحجز؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">حذف نهائي</button>
                                        </form>
                                    </td>
                                @elseif($type === 'meetings')
                                    <td class="px-4 py-3">{{ $row->title ?: '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $row->code }}</td>
                                    <td class="px-4 py-3">{{ $row->user?->name ?? $row->oneToOneSession?->student?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs">
                                        {{ optional($row->started_at)->format('Y-m-d H:i') ?? '—' }}
                                        →
                                        {{ optional($row->ended_at)->format('Y-m-d H:i') ?? 'جارية/لم تنتهِ' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.student-lesson-cleanup.destroy-meeting', $row) }}"
                                              onsubmit="return confirm('حذف نهائي لغرفة Classroom؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">حذف نهائي</button>
                                        </form>
                                    </td>
                                @else
                                    <td class="px-4 py-3">
                                        {{ $row->title }}
                                        @if(\App\Services\StudentLessonCleanupService::looksExperimental($row->title.' '.$row->description) || (bool) data_get($row->settings, 'admin_only'))
                                            <span class="mr-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800">تجريبي/إداري</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $row->instructor?->name ?? '—' }}</td>
                                    <td class="px-4 py-3"><span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ $row->status }}</span></td>
                                    <td class="px-4 py-3 max-w-[14rem] truncate text-xs text-muted">{{ \Illuminate\Support\Str::limit($row->description, 60) }}</td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.student-lesson-cleanup.destroy-live', $row) }}"
                                              onsubmit="return confirm('حذف نهائي لجلسة البث؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">حذف نهائي</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">لا توجد عناصر مطابقة للتصفية الحالية.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($paginator && $paginator->hasPages())
                <div class="border-t border-line px-4 py-3">{{ $paginator->links() }}</div>
            @endif
        </div>
    </form>
</div>

<script>
    (function () {
        var all = document.getElementById('cleanup-check-all');
        if (!all) return;
        all.addEventListener('change', function () {
            document.querySelectorAll('.cleanup-row-check').forEach(function (el) {
                el.checked = all.checked;
            });
        });
    })();
</script>
@endsection
