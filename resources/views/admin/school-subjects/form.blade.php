@extends('layouts.admin')

@section('title', ($mode === 'create' ? 'مادة جديدة' : 'تعديل مادة').' - Glottical')
@section('page_title', $mode === 'create' ? 'مادة جديدة' : 'تعديل المادة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $action = $mode === 'create' ? route('admin.school-subjects.store') : route('admin.school-subjects.update', $subject);
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">المدرسة</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'مادة جديدة' : 'تعديل: '.$subject->name }}</h2>
        </div>
        <a href="{{ route('admin.school-subjects.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft sm:p-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="name">الاسم *</label>
                    <input id="name" name="name" required maxlength="255" value="{{ old('name', $subject->name) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="slug">Slug</label>
                    <input id="slug" name="slug" dir="ltr" value="{{ old('slug', $subject->slug) }}" class="{{ $fieldClass }} font-mono">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="icon">أيقونة Font Awesome</label>
                    <input id="icon" name="icon" dir="ltr" placeholder="book-open" value="{{ old('icon', $subject->icon) }}" class="{{ $fieldClass }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="description">الوصف</label>
                    <textarea id="description" name="description" rows="4" class="{{ $areaClass }}">{{ old('description', $subject->description) }}</textarea>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="sort_order">الترتيب</label>
                    <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $subject->sort_order ?: 0) }}" class="{{ $fieldClass }}">
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-line text-accent" @checked(old('is_active', $subject->is_active ?? true))>
                        نشطة وتظهر في صفحة المدرسة
                    </label>
                </div>
            </div>
        </article>
        <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">حفظ</button>
    </form>
</div>
@endsection
