@extends('layouts.admin')

@section('title', 'إضافة كورس جديد')
@section('header', __('admin.courses_management'))

@section('content')
<div class="px-4 py-8" x-data="courseForm({ selectedSkills: @json(old('skills', [])) })">
    <div class="w-full max-w-full space-y-8">
        <div class="section-card">
            <div class="section-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-sky-600 dark:hover:text-sky-400">{{ __('admin.dashboard') }}</a>
                        <span class="mx-2">/</span>
                        <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-sky-600 dark:hover:text-sky-400">{{ __('admin.courses_management') }}</a>
                        <span class="mx-2">/</span>
                        <span class="text-slate-700 dark:text-slate-300">إضافة كورس</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">إنشاء كورس تدريبي جديد</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        أضف عنوان الكورس، المدرّس، المسار، والمحتوى التدريبي.
                    </p>
                </div>
                <a href="{{ route('admin.advanced-courses.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                    <i class="fas fa-arrow-right"></i>
                    العودة للكورسات
                </a>
            </div>
        </div>

        <form action="{{ route('admin.advanced-courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">المعلومات الأساسية</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">املأ تفاصيل الكورس التدريبي.</p>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2 space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">عنوان الكورس *</label>
                                    <input type="text" name="title" value="{{ old('title') }}" required
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition"
                                           placeholder="مثال: إدارة الصف الفعّال ـ التخطيط والتنفيذ">
                                    @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">المدرّس المسؤول</label>
                                    <select name="instructor_id"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                        <option value="">بدون مدرّس محدد</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">مسار الكورس (التصفية في صفحة الكورسات العامة)</label>
                                    <select name="course_category_id"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                        <option value="">— بدون مسار —</option>
                                        @foreach($courseCategories as $cc)
                                            <option value="{{ $cc->id }}" {{ (string) old('course_category_id') === (string) $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        أضف أو عدّل المسارات من
                                        <a href="{{ route('admin.course-categories.index') }}" class="text-sky-600 hover:underline font-semibold">إدارة المحتوى → مسارات الكورسات</a>.
                                    </p>
                                    @error('course_category_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">مدة الكورس (ساعات)</label>
                                    <input type="number" name="duration_hours" value="{{ old('duration_hours', 0) }}" min="0"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">مدة إضافية (دقائق)</label>
                                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 0) }}" min="0" max="59"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر الكورس الأساسي (قبل الخصم — جنيه)</label>
                                    <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                           placeholder="0 للمجاني">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر بعد الخصم (اختياري)</label>
                                    <input type="number" name="price_after_discount" value="{{ old('price_after_discount') }}" min="0" step="0.01"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                           placeholder="اتركه فارغاً إن لم يكن هناك عرض — يجب أن يكون أقل من السعر الأساسي">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">يُعرض على بطاقات الكورس كسعر قبل وبعد. الدفع والكوبونات تُحسب على هذا السعر.</p>
                                    @error('price_after_discount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">نوع الكورس</label>
                                    <select name="delivery_type" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200">
                                        <option value="group" @selected(old('delivery_type', 'group') === 'group')>جماعي</option>
                                        <option value="one_to_one" @selected(old('delivery_type') === 'one_to_one')>فردي 1:1 مع معلم</option>
                                    </select>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">نظام الدفع</label>
                                    <select name="billing_mode" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200">
                                        <option value="one_time" @selected(old('billing_mode', 'one_time') === 'one_time')>دفعة واحدة</option>
                                        <option value="monthly" @selected(old('billing_mode') === 'monthly')>اشتراك شهري</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر الاشتراك الشهري (جنيه)</label>
                                    <input type="number" name="monthly_price" value="{{ old('monthly_price') }}" min="0" step="0.01"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر شهري بعد الخصم</label>
                                    <input type="number" name="monthly_price_after_discount" value="{{ old('monthly_price_after_discount') }}" min="0" step="0.01"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">وصف الكورس</label>
                                <textarea name="description" rows="4"
                                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                          placeholder="اشرح محتوى الكورس وقيمته للمتدربين.">{{ old('description') }}</textarea>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    <i class="fas fa-video text-sky-600 ml-1"></i>
                                    رابط الفيديو التقديمي (يظهر في صفحة الكورس)
                                </label>
                                <input type="url" name="video_url" value="{{ old('video_url') }}"
                                       class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                       placeholder="رابط تضمين Bunny (iframe.mediadelivery.net)، YouTube، Vimeo، أو .mp4">
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">يُعرض في الصندوق الرئيسي بجانب وصف الكورس (بدل كارد السعر السابق).</p>
                                @error('video_url') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">تاريخ البداية</label>
                                    <input type="date" name="starts_at" value="{{ old('starts_at') }}"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">تاريخ النهاية</label>
                                    <input type="date" name="ends_at" value="{{ old('ends_at') }}"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">المهارات والمخرجات</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">المهارات المستهدفة (اختياري)</label>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">اختر أو أضف مهارات يكتسبها المتدرب من الكورس.</p>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach($skills as $skill)
                                        <button type="button" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-500 hover:border-sky-400 transition"
                                                @click="addSkill('{{ $skill }}')">
                                            {{ $skill }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-2">
                                    <input id="customSkill" type="text" class="flex-1 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition" placeholder="اكتب مهارة جديدة">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 text-sm font-semibold transition"
                                            @click="addSkill(document.getElementById('customSkill').value); document.getElementById('customSkill').value='';">
                                        <i class="fas fa-plus"></i>
                                        إضافة
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-3" x-show="selectedSkills.length">
                                    <template x-for="(skill, index) in selectedSkills" :key="skill">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 text-xs font-semibold">
                                            <span x-text="skill"></span>
                                            <button type="button" class="text-sky-600 dark:text-sky-400 hover:text-sky-800" @click="removeSkill(index)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <input type="hidden" name="skills[]" :value="skill">
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">المتطلبات المسبقة</label>
                                    <textarea name="prerequisites" rows="3"
                                              class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                              placeholder="ما الذي يجب أن يعرفه المتدرب قبل بدء الكورس؟">{{ old('prerequisites') }}</textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">ما الذي سيتعلمه المتدرب؟</label>
                                    <textarea name="what_you_learn" rows="3"
                                              class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                              placeholder="المخرجات التعليمية والمهارات المكتسبة">{{ old('what_you_learn') }}</textarea>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">متطلبات إضافية</label>
                                <textarea name="requirements" rows="3"
                                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                          placeholder="أدوات أو موارد يحتاجها المتدرب خلال الدراسة.">{{ old('requirements') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">إعدادات العرض</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-4 text-sm text-slate-700 dark:text-slate-300">
                            <label class="flex items-center justify-between">
                                <span class="font-medium">تفعيل الكورس فوراً</span>
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="font-medium">وضع الكورس ضمن المميزة</span>
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-5 h-5 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            </label>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">لغة المحتوى</label>
                                <select name="language"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                    <option value="ar" {{ old('language', 'ar') == 'ar' ? 'selected' : '' }}>العربية</option>
                                    <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>الإنجليزية</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">صورة غلاف الكورس</label>
                                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp"
                                       class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                <p class="text-xs text-slate-500 dark:text-slate-400">يُرفع الملف ويُولَّد رابط العرض تلقائياً على الموقع.</p>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">أو رابط صورة خارجي (اختياري)</label>
                                    <input type="url" name="thumbnail_link" value="{{ old('thumbnail_link') }}" dir="ltr" placeholder="https://..."
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                    @error('thumbnail_link') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                @error('thumbnail') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">ملخص</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-3 text-sm text-slate-600 dark:text-slate-400">
                            <div class="flex items-center justify-between">
                                <span>عدد المهارات المحددة</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedSkills.length"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>الحالة</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ old('is_active', true) ? 'نشط' : 'مسودة' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="p-6 sm:p-8 space-y-3">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 text-sm font-semibold shadow-lg transition">
                                <i class="fas fa-save"></i>
                                حفظ الكورس
                            </button>
                            <a href="{{ route('admin.advanced-courses.index') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-600 px-6 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function courseForm({ selectedSkills }) {
    return {
        selectedSkills: selectedSkills || [],
        addSkill(value) {
            const skill = (value || '').trim();
            if (!skill) return;
            if (!this.selectedSkills.includes(skill)) this.selectedSkills.push(skill);
        },
        removeSkill(index) {
            this.selectedSkills.splice(index, 1);
        }
    };
}
</script>
@endpush
@endsection