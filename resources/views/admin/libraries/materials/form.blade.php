@extends('layouts.admin')

@section('title', $mode === 'create' ? 'رفع ماتريال' : 'تعديل ماتريال')
@section('page_title', $mode === 'create' ? 'رفع ماتريال' : 'تعديل ماتريال')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.materials.index') }}" class="hover:text-accent">مكتبة الماتريال</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'رفع ملف جديد' : 'تعديل ملف' }}</h2>
        </div>
        <a href="{{ route('admin.libraries.materials.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $mode === 'create' ? route('admin.libraries.materials.store') : route('admin.libraries.materials.update', $material) }}"
          class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">المحاضرة (اختياري إن اخترت مجلداً)</label>
                    <select name="lecture_id" class="{{ $field }}">
                        <option value="">بدون محاضرة — فولدر فقط</option>
                        @foreach($lectures as $lecture)
                            <option value="{{ $lecture->id }}" @selected((string) old('lecture_id', $material->lecture_id) === (string) $lecture->id)>
                                #{{ $lecture->id }} — {{ $lecture->title }} @if($lecture->course) ({{ $lecture->course->title }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">مجلد الماتريال (معلم×سنة)</label>
                    <select name="library_folder_id" class="{{ $field }}">
                        <option value="">بدون مجلد</option>
                        @foreach(($folders ?? []) as $folder)
                            <option value="{{ $folder->id }}" @selected((string) old('library_folder_id', $material->library_folder_id) === (string) $folder->id)>
                                {{ $folder->name_ar }}@if($folder->name_en) / {{ $folder->name_en }}@endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-muted">يجب اختيار محاضرة أو مجلد على الأقل.</p>
                </div>
            </div>
            <div>
                <label class="{{ $label }}">عنوان العرض</label>
                <input type="text" name="title" value="{{ old('title', $material->title) }}" class="{{ $field }}" placeholder="اختياري — افتراضياً اسم الملف">
            </div>
            <div>
                <label class="{{ $label }}">وصف مختصر للطالب</label>
                <textarea name="description" rows="2" class="{{ $field }} py-3" placeholder="مثال: قصة تفاعلية آمنة داخل المنصة">{{ old('description', $material->description) }}</textarea>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">تصنيف المحتوى</label>
                    <select name="content_theme" class="{{ $field }}">
                        @foreach(($themes ?? \App\Support\FamilyLibraryThemes::labels('ar')) as $key => $labelTheme)
                            <option value="{{ $key }}" @selected(old('content_theme', $material->content_theme ?: 'general') === $key)>{{ $labelTheme }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-muted">كتب PDF · بوربوينت · HTML · ألعاب تعليمية — ليبقى الطفل داخل المنصة.</p>
                </div>
                <div>
                    <label class="{{ $label }}">تجربة الطالب</label>
                    <select name="experience_mode" class="{{ $field }}">
                        <option value="download" @selected(old('experience_mode', $material->experience_mode ?: 'download') === 'download')>تحميل / فتح ملف</option>
                        <option value="view" @selected(old('experience_mode', $material->experience_mode) === 'view')>عرض داخل المنصة (HTML)</option>
                        <option value="play" @selected(old('experience_mode', $material->experience_mode) === 'play')>لعب داخل المنصة (لعبة)</option>
                    </select>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">ترتيب</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $material->sort_order ?? 0) }}" class="{{ $field }}">
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_visible_to_student" value="1" @checked(old('is_visible_to_student', $material->is_visible_to_student ?? true))>
                        ظاهر للطلاب في المكتبة
                    </label>
                </div>
            </div>
            <div>
                <label class="{{ $label }}">الملف {{ $mode === 'create' ? '*' : '(اتركه فارغاً للإبقاء على الحالي)' }}</label>
                <input type="file" name="file" @if($mode === 'create') required @endif class="block w-full text-sm"
                       accept="{{ \App\Support\FamilyLibraryThemes::materialAcceptAttr() }}">
                <p class="mt-1 text-xs text-muted">
                    يشمل PDF وPPT وHTML والألعاب (ZIP) والصور والصوت.
                    التخزين:
                    <strong>{{ ($storageDisk ?? 'r2') === 'r2' ? 'Cloudflare R2' : ($storageDisk ?? 'local') }}</strong>
                </p>
                @if($mode === 'edit' && $material->file_name)
                    <p class="mt-1 text-xs text-muted">الحالي: {{ $material->file_name }}@if($material->storage_disk) ({{ $material->storage_disk }})@endif</p>
                @endif
            </div>
        </article>

        <button class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-semibold text-white">
            <i class="fas fa-save text-xs"></i> حفظ
        </button>
    </form>
</div>
@endsection
