@extends('layouts.admin')

@section('title', $category ? 'تعديل التصنيف' : 'إضافة تصنيف')
@section('header', $category ? 'تعديل التصنيف' : 'إضافة تصنيف')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
        <form action="{{ $category ? route('admin.curriculum-library.categories.update', $category) : route('admin.curriculum-library.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            @if($category) @method('PUT') @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">اسم التصنيف</label>
                <input type="text" name="name" value="{{ old('name', $category?->name) }}" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">الرابط (slug) — اختياري</label>
                <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                @error('slug') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">{{ old('description', $category?->description) }}</textarea>
            </div>
            <div class="flex gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ترتيب العرض</label>
                    <input type="number" name="order" value="{{ old('order', $category?->order ?? 0) }}" min="0" class="w-24 px-3 py-2 rounded-lg border border-slate-200">
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-semibold text-slate-700">نشط</label>
                </div>
            </div>

            <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-4 space-y-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_restricted" id="is_restricted" value="1" {{ old('is_restricted', $category?->is_restricted ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <label for="is_restricted" class="text-sm font-bold text-slate-800">قسم خاص (يظهر فقط للمستخدمين المحددين)</label>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">أنسب لقسم «العميل يرفع ملف وتتحوله تفاعلي» أو أي محتوى لا يظهر للجميع. أنشئ التصنيف ثم اختر الحسابات المسموح لها.</p>
                @php
                    $selectedRestrict = old('restricted_user_ids', isset($category) ? $category->restrictedUsers->pluck('id')->all() : []);
                @endphp
                <div>
                    <label for="restricted_user_ids" class="block text-sm font-semibold text-slate-700 mb-1">الطلاب المسموح لهم (Ctrl/Cmd + نقر لاختيار أكثر من واحد)</label>
                    <select name="restricted_user_ids[]" id="restricted_user_ids" multiple size="8" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-amber-500 text-sm">
                        @foreach($users ?? [] as $u)
                            <option value="{{ $u->id }}" {{ in_array($u->id, $selectedRestrict, true) ? 'selected' : '' }}>
                                {{ $u->name }} — {{ $u->email }} ({{ $u->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('restricted_user_ids') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">{{ $category ? 'حفظ التعديلات' : 'إضافة' }}</button>
                <a href="{{ route('admin.curriculum-library.categories') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
