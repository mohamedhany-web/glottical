@extends('layouts.admin')

@section('title', ($mode === 'create' ? 'سنة جديدة' : 'تعديل سنة').' - Glottical')
@section('page_title', $mode === 'create' ? 'سنة دراسية جديدة' : 'تعديل السنة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $action = $mode === 'create' ? route('admin.school-years.store') : route('admin.school-years.update', $year);
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">المدرسة</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'سنة جديدة' : 'تعديل: '.$year->name }}</h2>
        </div>
        <a href="{{ route('admin.school-years.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft sm:p-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="name">الاسم *</label>
                    <input id="name" name="name" required maxlength="255" value="{{ old('name', $year->name) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="slug">Slug</label>
                    <input id="slug" name="slug" dir="ltr" value="{{ old('slug', $year->slug) }}" class="{{ $fieldClass }} font-mono">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="level_number">رقم المستوى *</label>
                    <input id="level_number" type="number" min="1" max="20" name="level_number" required value="{{ old('level_number', $year->level_number) }}" class="{{ $fieldClass }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="tagline">عبارة قصيرة</label>
                    <input id="tagline" name="tagline" value="{{ old('tagline', $year->tagline) }}" class="{{ $fieldClass }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="description">الوصف</label>
                    <textarea id="description" name="description" rows="4" class="{{ $areaClass }}">{{ old('description', $year->description) }}</textarea>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="sort_order">الترتيب</label>
                    <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $year->sort_order ?: 0) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="image">صورة</label>
                    <input id="image" type="file" name="image" accept="image/*" class="block w-full text-sm text-muted">
                    @if($year->imageUrl())
                        <img src="{{ $year->imageUrl() }}" alt="" class="mt-2 h-20 rounded-xl object-cover">
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-line text-accent" @checked(old('is_active', $year->is_active ?? true))>
                        نشطة وتظهر في صفحة المدرسة
                    </label>
                </div>
            </div>
        </article>
        <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">حفظ</button>
    </form>
</div>
@endsection
