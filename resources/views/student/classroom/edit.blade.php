@extends(!empty($useInstructorRoutes) ? 'layouts.app' : 'layouts.student-timeline')

@section('title', 'تعديل الاجتماع')
@section('header', 'تعديل الاجتماع')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <form action="{{ route('student.classroom.update', $meeting) }}" method="POST" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">تعديل إعدادات الاجتماع</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">الكود: <span class="font-mono">{{ $meeting->code }}</span></p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">عنوان الاجتماع</label>
            <input type="text" name="title" value="{{ old('title', $meeting->title) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            @error('title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الحد الأقصى للمشاركين</label>
            <input type="number" min="2" max="{{ (int) $limits['classroom_max_participants'] }}" name="max_participants" value="{{ old('max_participants', (int) ($meeting->max_participants ?? 25)) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            @error('max_participants')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">موعد الاجتماع (اختياري)</label>
            <input type="datetime-local" name="scheduled_for" value="{{ old('scheduled_for', optional($meeting->scheduled_for)->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            @error('scheduled_for')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">مدة الاجتماع (بالدقائق)</label>
            <input type="number" min="15" max="{{ (int) $limits['classroom_max_duration_minutes'] }}" name="planned_duration_minutes" value="{{ old('planned_duration_minutes', (int) ($meeting->planned_duration_minutes ?? $limits['classroom_default_duration_minutes'])) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">الافتراضي حسب الباقة: {{ (int) $limits['classroom_default_duration_minutes'] }} دقيقة — الحد الأقصى: {{ (int) $limits['classroom_max_duration_minutes'] }} دقيقة.</p>
            @error('planned_duration_minutes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('student.classroom.show', $meeting) }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300">رجوع</a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold">حفظ التعديلات</button>
        </div>
    </form>
</div>
@endsection

