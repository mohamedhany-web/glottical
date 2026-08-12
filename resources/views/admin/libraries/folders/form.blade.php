@extends('layouts.admin')

@section('title', $mode === 'create' ? 'مجلد جديد' : 'تعديل مجلد')
@section('page_title', $mode === 'create' ? 'مجلد جديد' : 'تعديل مجلد')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.folders.index') }}" class="hover:text-accent">مجلدات الفيديو</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'مجلد جديد' : 'تعديل المجلد' }}</h2>
        </div>
        <a href="{{ route('admin.libraries.folders.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST"
          action="{{ $mode === 'create' ? route('admin.libraries.folders.store') : route('admin.libraries.folders.update', $folder) }}"
          class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">الاسم (عربي) *</label>
                    <input type="text" name="name_ar" required value="{{ old('name_ar', $folder->name_ar) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">الاسم (إنجليزي)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $folder->name_en) }}" class="{{ $field }}">
                </div>
            </div>
            <div>
                <label class="{{ $label }}">Slug (اختياري)</label>
                <input type="text" name="slug" value="{{ old('slug', $folder->slug) }}" class="{{ $field }}" placeholder="conversation">
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">وصف عربي</label>
                    <input type="text" name="description_ar" value="{{ old('description_ar', $folder->description_ar) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">وصف إنجليزي</label>
                    <input type="text" name="description_en" value="{{ old('description_en', $folder->description_en) }}" class="{{ $field }}">
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="{{ $label }}">أيقونة Font Awesome</label>
                    <input type="text" name="icon" value="{{ old('icon', $folder->icon ?: 'fas fa-folder') }}" class="{{ $field }}" placeholder="fas fa-folder">
                </div>
                <div>
                    <label class="{{ $label }}">اللون</label>
                    <select name="color" class="{{ $field }}">
                        @foreach(\App\Models\LibraryFolder::COLORS as $c)
                            <option value="{{ $c }}" @selected(old('color', $folder->color ?: 'blue') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">الترتيب</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $folder->sort_order ?? 0) }}" class="{{ $field }}">
                </div>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $folder->is_active ?? true))>
                ظاهر للطالبات
            </label>
            <div class="grid gap-4 md:grid-cols-2 pt-2 border-t border-line">
                <div>
                    <label class="{{ $label }}">النوع</label>
                    <select name="kind" class="{{ $field }}">
                        <option value="videos" @selected(old('kind', $folder->kind ?? 'videos') === 'videos')>فيديو</option>
                        <option value="materials" @selected(old('kind', $folder->kind ?? '') === 'materials')>ماتريال</option>
                        <option value="both" @selected(old('kind', $folder->kind ?? '') === 'both')>فيديو + ماتريال</option>
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">السنة الدراسية</label>
                    <select name="academic_year_id" class="{{ $field }}">
                        <option value="">— عامة —</option>
                        @foreach(($years ?? []) as $y)
                            <option value="{{ $y->id }}" @selected((string) old('academic_year_id', $folder->academic_year_id) === (string) $y->id)>{{ $y->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">المعلم (اختياري)</label>
                    <select name="instructor_id" class="{{ $field }}">
                        <option value="">— الكل / الإدارة —</option>
                        @foreach(($instructors ?? []) as $ins)
                            <option value="{{ $ins->id }}" @selected((string) old('instructor_id', $folder->instructor_id) === (string) $ins->id)>{{ $ins->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="requires_library_entitlement" value="1" @checked(old('requires_library_entitlement', $folder->requires_library_entitlement ?? true))>
                        يتطلب اشتراك باقة مكتبات
                    </label>
                </div>
            </div>
        </article>

        <button class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-semibold text-white">
            <i class="fas fa-save text-xs"></i> حفظ
        </button>
    </form>
</div>
@endsection
