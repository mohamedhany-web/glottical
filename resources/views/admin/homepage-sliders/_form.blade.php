@php
    $slide = $slide ?? null;
    $sourceType = old('source_type', $slide->source_type ?? \App\Models\HomepageSlider::SOURCE_COURSE);
@endphp

<div x-data="{ sourceType: @js($sourceType) }" class="space-y-6">
    <div>
        <span class="block text-sm font-semibold text-slate-700 mb-2">مصدر السلايدر <span class="text-rose-500">*</span></span>
        <div class="flex flex-wrap gap-3">
            @foreach($sourceTypes as $value => $label)
            <label class="inline-flex items-center gap-2 cursor-pointer rounded-xl border px-4 py-2.5 text-sm font-semibold transition"
                   :class="sourceType === @js($value) ? 'border-amber-400 bg-amber-50 text-amber-900' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                <input type="radio" name="source_type" value="{{ $value }}" x-model="sourceType" class="text-amber-600 focus:ring-amber-500">
                <span>{{ $label }}</span>
            </label>
            @endforeach
        </div>
        @error('source_type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        <p class="mt-2 text-xs text-slate-500">«من كورس» و«من مسار» يملآن العنوان والصورة تلقائياً من المحتوى — يمكنك تخصيصها أدناه.</p>
    </div>

    <template x-if="sourceType === 'course'">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">الكورس <span class="text-rose-500">*</span></label>
            <select name="advanced_course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white">
                <option value="">— اختر كورساً —</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" @selected((string) old('advanced_course_id', $slide->advanced_course_id ?? '') === (string) $c->id)>
                    {{ $c->title }}@if($c->is_featured) ★ @endif
                </option>
                @endforeach
            </select>
            @error('advanced_course_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
    </template>

    <template x-if="sourceType === 'path'">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">المسار التعليمي <span class="text-rose-500">*</span></label>
            <select name="academic_year_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white">
                <option value="">— اختر مساراً —</option>
                @foreach($paths as $p)
                <option value="{{ $p->id }}" @selected((string) old('academic_year_id', $slide->academic_year_id ?? '') === (string) $p->id)>
                    {{ $p->name }}
                </option>
                @endforeach
            </select>
            @error('academic_year_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
    </template>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">شارة صغيرة (Kicker)</label>
            <input type="text" name="kicker" value="{{ old('kicker', $slide->kicker ?? '') }}" maxlength="120" placeholder="مثال: محتوى مميّز" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان <span class="text-rose-500" x-show="sourceType === 'custom'">*</span></label>
            <input type="text" name="title" value="{{ old('title', $slide->title ?? '') }}" maxlength="255" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
            @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف الفرعي</label>
        <textarea name="subtitle" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl" placeholder="يُستخدم من الكورس/المسار إن تُرك فارغاً">{{ old('subtitle', $slide->subtitle ?? '') }}</textarea>
    </div>

    <div class="rounded-2xl border border-dashed border-slate-200 p-4 bg-slate-50/80">
        <label class="block text-sm font-semibold text-slate-700 mb-2">صورة الخلفية (تجاوز اختياري)</label>
        @if($slide && $slide->publicImageUrl())
        <div class="mb-3 flex items-center gap-4">
            <img src="{{ $slide->publicImageUrl() }}" alt="" class="h-20 w-36 object-cover rounded-xl border border-slate-200">
            <label class="inline-flex items-center gap-2 text-sm text-rose-700 cursor-pointer">
                <input type="hidden" name="remove_image" value="0">
                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-rose-600">
                حذف الصورة المخصصة
            </label>
        </div>
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-amber-50 file:text-amber-800">
        <p class="mt-1 text-xs text-slate-500">بدون رفع: تُستخدم صورة الكورس أو المسار. للمخصص يُفضّل رفع صورة 1920×1080 تقريباً.</p>
        @error('image')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">نص الزر الرئيسي</label>
            <input type="text" name="primary_label" value="{{ old('primary_label', $slide->primary_label ?? '') }}" maxlength="120" placeholder="ابدأ التعلّم الآن" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">رابط الزر الرئيسي</label>
            <input type="text" name="primary_url" value="{{ old('primary_url', $slide->primary_url ?? '') }}" maxlength="500" placeholder="/course/12 أو https://..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl" dir="ltr">
            @error('primary_url')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">نص الزر الثانوي</label>
            <input type="text" name="secondary_label" value="{{ old('secondary_label', $slide->secondary_label ?? '') }}" maxlength="120" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">رابط الزر الثانوي</label>
            <input type="text" name="secondary_url" value="{{ old('secondary_url', $slide->secondary_url ?? '') }}" maxlength="500" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl" dir="ltr">
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">ترتيب العرض</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $slide->sort_order ?? 0) }}" min="0" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">يبدأ في (اختياري)</label>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($slide?->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">ينتهي في (اختياري)</label>
            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($slide?->ends_at)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
            @error('ends_at')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slide->is_active ?? true)) class="rounded border-slate-300 text-emerald-600">
            <span class="text-sm font-semibold text-slate-700">نشط — يظهر في الصفحة الرئيسية</span>
        </label>
    </div>
</div>
