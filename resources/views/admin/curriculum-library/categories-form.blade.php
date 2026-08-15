@extends('layouts.admin')

@section('title', $category ? 'تعديل التصنيف' : 'إضافة تصنيف')
@section('page_title', $category ? 'تعديل التصنيف' : 'إضافة تصنيف')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
    $selectedRestrict = old('restricted_user_ids', isset($category) ? $category->restrictedUsers->pluck('id')->all() : []);
@endphp

<div class="mx-auto max-w-3xl space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.curriculum-library.index') }}" class="hover:text-accent">المناهج التفاعلية</a>
                ·
                <a href="{{ route('admin.curriculum-library.categories') }}" class="hover:text-accent">التصنيفات</a>
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $category ? 'تعديل التصنيف' : 'إضافة تصنيف' }}</h2>
            <p class="mt-1 text-sm text-muted">تنظيم عناصر المناهج التفاعلية تحت تصنيف واضح للطلاب والمعلمين.</p>
        </div>
        <a href="{{ route('admin.curriculum-library.categories') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ $category ? route('admin.curriculum-library.categories.update', $category) : route('admin.curriculum-library.categories.store') }}"
          method="POST"
          class="space-y-5">
        @csrf
        @if($category) @method('PUT') @endif

        <article class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <div>
                <label class="{{ $label }}" for="name">اسم التصنيف *</label>
                <input id="name" type="text" name="name" value="{{ old('name', $category?->name) }}" required class="{{ $field }}">
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}" for="slug">الرابط (slug) — اختياري</label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $category?->slug) }}" class="{{ $field }}" placeholder="math-arabic">
                @error('slug') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}" for="description">الوصف</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">{{ old('description', $category?->description) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $label }}" for="order">ترتيب العرض</label>
                    <input id="order" type="number" name="order" value="{{ old('order', $category?->order ?? 0) }}" min="0" class="{{ $field }}">
                </div>
                <div class="flex items-end pb-1">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-ink">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }} class="rounded border-line text-accent focus:ring-accent/30">
                        نشط ويظهر للطلاب
                    </label>
                </div>
            </div>
        </article>

        <article class="space-y-4 rounded-2xl border border-amber-200/80 bg-amber-50/40 p-5 shadow-soft">
            <div class="flex items-start gap-3">
                <input type="checkbox" name="is_restricted" id="is_restricted" value="1" {{ old('is_restricted', $category?->is_restricted ?? false) ? 'checked' : '' }} class="mt-1 rounded border-line text-amber-600 focus:ring-amber-500/30">
                <div>
                    <label for="is_restricted" class="text-sm font-semibold text-ink">قسم خاص (يظهر فقط للمستخدمين المحددين)</label>
                    <p class="mt-1 text-xs leading-relaxed text-muted">مناسب لمحتوى لا يظهر للجميع. أنشئ التصنيف ثم اختر الحسابات المسموح لها.</p>
                </div>
            </div>

            <div>
                <label class="{{ $label }}" for="restricted_user_ids">الطلاب المسموح لهم</label>
                <select name="restricted_user_ids[]" id="restricted_user_ids" multiple size="8"
                        class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    @forelse($users ?? [] as $u)
                        <option value="{{ $u->id }}" {{ in_array($u->id, $selectedRestrict, true) ? 'selected' : '' }}>
                            {{ $u->name }} — {{ $u->email }}
                        </option>
                    @empty
                        <option disabled value="">لا طلاب نشطون حالياً</option>
                    @endforelse
                </select>
                <p class="mt-1.5 text-[11px] text-muted">Ctrl / Cmd + نقر لاختيار أكثر من حساب.</p>
                @error('restricted_user_ids') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
        </article>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-semibold text-white">
                {{ $category ? 'حفظ التعديلات' : 'إضافة التصنيف' }}
            </button>
            <a href="{{ route('admin.curriculum-library.categories') }}" class="btn-press inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm font-medium text-ink">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
