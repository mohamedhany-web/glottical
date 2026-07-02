@extends('layouts.app')

@section('title', 'إنشاء جلسة بث مباشر - ' . config('app.name'))
@section('header', 'إنشاء جلسة بث مباشر')

@push('styles')
<style>
    .form-card {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 1rem;
        transition: box-shadow 0.2s;
    }
    .form-card:focus-within { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06); }
    .dark .form-card {
        background: rgba(30, 41, 59, 0.95);
        border-color: rgb(51 65 85);
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- هيدر الصفحة --}}
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 dark:text-slate-400 mb-3">
            <a href="{{ route('instructor.live-sessions.index') }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors">جلسات البث المباشر</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">إنشاء جلسة</span>
        </nav>
        <div class="flex flex-wrap items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                <i class="fas fa-broadcast-tower text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">إنشاء جلسة بث مباشر</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">حدد عنوان الجلسة وموعد البث والكورس (اختياري) وكلمة المرور إن رغبت.</p>
            </div>
            <a href="{{ route('instructor.live-sessions.index') }}"
               class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors text-sm">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <div class="form-card shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('instructor.live-sessions.store') }}" class="space-y-6">
            @csrf

            <div>
                <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-8 h-px bg-slate-200 dark:bg-slate-600"></span>
                    معلومات الجلسة
                </h2>
                <div class="space-y-5">
                    <div>
                        <label for="live_title" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">عنوان الجلسة <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="live_title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/95"
                               placeholder="مثال: مراجعة الوحدة الثانية — جلسة تفاعلية">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="course_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الكورس <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <select name="course_id" id="course_id"
                                class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/95">
                            <option value="">جلسة عامة (بدون ربط بكورس)</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">عند الربط بكورس، يقتصر الدخول عادةً على الطلاب المسجّلين فيه.</p>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="scheduled_at" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">موعد البث <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" required
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/95">
                            @error('scheduled_at')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="max_participants" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الحد الأقصى للحضور</label>
                            <input type="number" name="max_participants" id="max_participants" value="{{ old('max_participants', 100) }}" min="2" max="500"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/95">
                            @error('max_participants')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">كلمة مرور الدخول <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="text" name="password" id="password" value="{{ old('password') }}" autocomplete="off"
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/95"
                               placeholder="اتركها فارغة للسماح بالدخول بدون كلمة مرور">
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">وصف الجلسة</label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-800/95 resize-y min-h-[100px]"
                                  placeholder="ما الذي ستغطيه في الجلسة؟ أي تعليمات للطلاب قبل البدء؟">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('instructor.live-sessions.index') }}"
                   class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors text-center">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 dark:hover:bg-sky-500 text-white rounded-xl font-semibold shadow-sm shadow-sky-500/25 transition-colors">
                    <i class="fas fa-broadcast-tower"></i>
                    إنشاء الجلسة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
