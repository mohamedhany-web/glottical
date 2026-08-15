@extends('layouts.admin')

@section('title', $item ? 'تعديل عنصر المنهج' : 'إضافة عنصر منهج')
@section('header', $item ? 'تعديل عنصر المنهج' : 'إضافة عنصر منهج')

@section('content')
<div class="w-full max-w-none space-y-4">
    @if($item)
        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-indigo-900 font-semibold">الأقسام والمواد والتحكم بالعرض/التحميل تُدار من صفحة هيكل المنهج.</p>
            <a href="{{ route('admin.curriculum-library.items.structure', $item) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                <i class="fas fa-sitemap"></i> هيكل المنهج
            </a>
        </div>
    @endif
    @if($item && $item->files && $item->files->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
            <h3 class="text-sm font-bold text-slate-800 mb-3">ملفات قديمة (قبل الهيكل الهرمي)</h3>
            <ul class="space-y-2">
                @foreach($item->files as $f)
                    <li class="flex items-center justify-between py-2 px-3 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-sm text-slate-700">
                            {{ $f->label ?: ($f->file_type === 'presentation' ? 'عرض شرائح' : ($f->file_type === 'pdf' ? 'PDF' : ($f->file_type === 'assignment' ? 'وجبة' : 'HTML'))) }}
                        </span>
                        <form action="{{ route('admin.curriculum-library.items.files.destroy', [$item, $f]) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذا الملف؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">حذف</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
        <form action="{{ $item ? route('admin.curriculum-library.items.update', $item) : route('admin.curriculum-library.items.store') }}" method="POST" class="space-y-4">
            @csrf
            {{-- ملاحظة: بعض البيئات تفشل في method spoof (PUT)، لذلك التحديث يعتمد POST fallback route --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">العنوان</label>
                    <input type="text" name="title" value="{{ old('title', $item?->title) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                    @error('title') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">التصنيف</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="">— بدون تصنيف —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $item?->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الرابط (slug) — اختياري</label>
                    <input type="text" name="slug" value="{{ old('slug', $item?->slug) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                    @error('slug') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">المادة / التخصص</label>
                    <input type="text" name="subject" value="{{ old('subject', $item?->subject) }}" placeholder="مثال: رياضيات، لغة عربية"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">المستوى / المرحلة داخل القسم</label>
                    <input type="text" name="grade_level" value="{{ old('grade_level', $item?->grade_level) }}" placeholder="مثال: المستوى المبتدئ، المستوى الأول، الثاني، الثالث…"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-slate-500 mt-1">نص حر تضبطه أنت لكل قسم (عربي، إسلاميات، قرآن…).</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">اللغة (مناهج أكس)</label>
                    <select name="language" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="ar" {{ old('language', $item?->language ?? 'ar') === 'ar' ? 'selected' : '' }}>العربية</option>
                        <option value="en" {{ old('language', $item?->language ?? 'ar') === 'en' ? 'selected' : '' }}>English</option>
                        <option value="fr" {{ old('language', $item?->language ?? 'ar') === 'fr' ? 'selected' : '' }}>Français</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نوع المحتوى</label>
                    <select name="item_type" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="presentation" {{ old('item_type', $item?->item_type ?? 'presentation') === 'presentation' ? 'selected' : '' }}>بوربوينت تفاعلي</option>
                        <option value="assignment" {{ old('item_type', $item?->item_type ?? 'presentation') === 'assignment' ? 'selected' : '' }}>وجبة (تحميل/إرسال للطالب)</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-semibold text-slate-700">نشط (يظهر للمعلمين)</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_free_preview" id="is_free_preview" value="1" {{ old('is_free_preview', $item?->is_free_preview ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_free_preview" class="text-sm font-semibold text-slate-700">معاينة مجانية (يُعرض كعينة للتجربة)</label>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ترتيب العرض</label>
                    <input type="number" name="order" value="{{ old('order', $item?->order ?? 0) }}" min="0" class="w-24 px-3 py-2 rounded-lg border border-slate-200">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">وصف مختصر</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500" placeholder="ملخص يظهر في قائمة المكتبة">{{ old('description', $item?->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">المحتوى (تفصيلي — يدعم HTML)</label>
                <textarea name="content" rows="10" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 font-mono text-sm" placeholder="المحتوى التفاعلي أو التعليمي للدرس/الوحدة...">{{ old('content', $item?->content) }}</textarea>
                <p class="text-xs text-slate-500 mt-1">يمكنك استخدام HTML لعناوين، قوائم، روابط، أو تضمين أهداف الدرس وأنشطة مقترحة.</p>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">{{ $item ? 'حفظ التعديلات' : 'إضافة العنصر' }}</button>
                <a href="{{ route('admin.curriculum-library.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
