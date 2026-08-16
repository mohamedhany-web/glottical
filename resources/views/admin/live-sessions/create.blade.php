@extends('layouts.admin')
@section('title', 'إنشاء جلسة بث مباشر')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.live-sessions.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-plus-circle text-red-500 ml-2"></i>إنشاء جلسة بث مباشر</h1>
    </div>

    <form method="POST" action="{{ route('admin.live-sessions.store') }}" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
        @csrf
        <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">عنوان الجلسة <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="مثال: حصة تفاعلية — الذكاء الاصطناعي">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">المعلم (المشترك) <span class="text-red-500">*</span></label>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">المعلم = المشترك عندنا (طالب يشترون منا الخدمة).</p>
                <select name="instructor_id" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">اختر المعلم</option>
                    @foreach($instructors as $inst)
                        <option value="{{ $inst->id }}" {{ old('instructor_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}{{ $inst->role === 'student' ? ' (مشترك)' : '' }}</option>
                    @endforeach
                </select>
                @error('instructor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الكورس (اختياري)</label>
                <select name="course_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">جلسة عامة</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ Str::limit($course->title, 50) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                @include('partials.timezone-select', [
                    'value' => old('timezone', auth()->user()?->timezoneCode()),
                    'class' => 'w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white',
                    'labelClass' => 'block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1',
                ])
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">موعد البث <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('scheduled_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">سيرفر البث</label>
                <select name="server_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">الافتراضي</option>
                    @foreach($servers as $server)
                        <option value="{{ $server->id }}">{{ $server->name }} ({{ $server->domain }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى للمشاركين</label>
                <input type="number" name="max_participants" value="{{ old('max_participants', 100) }}" min="2" max="1000" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">كلمة مرور (اختياري)</label>
                <input type="text" name="password" value="{{ old('password') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="اتركها فارغة إذا لا تريد">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">وصف الجلسة</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="وصف مختصر عن محتوى الجلسة...">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="border-t border-slate-200 dark:border-slate-700 pt-5">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">إعدادات الغرفة</h3>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="is_recorded" value="1" {{ old('is_recorded') ? 'checked' : '' }} class="rounded text-red-500 focus:ring-red-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">تسجيل الجلسة</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="allow_chat" value="1" {{ old('allow_chat', true) ? 'checked' : '' }} class="rounded text-blue-500 focus:ring-blue-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">السماح بالشات</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="allow_screen_share" value="1" {{ old('allow_screen_share', true) ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">مشاركة الشاشة</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="require_enrollment" value="1" {{ old('require_enrollment', true) ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">يتطلب تسجيل في الكورس</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="mute_on_join" value="1" {{ old('mute_on_join', true) ? 'checked' : '' }} class="rounded text-violet-500 focus:ring-violet-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">كتم الصوت عند الدخول</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="video_off_on_join" value="1" {{ old('video_off_on_join', true) ? 'checked' : '' }} class="rounded text-pink-500 focus:ring-pink-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">إيقاف الفيديو عند الدخول</span>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/25 transition-all">
                <i class="fas fa-broadcast-tower ml-1"></i> إنشاء الجلسة
            </button>
            <a href="{{ route('admin.live-sessions.index') }}" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
@endsection
