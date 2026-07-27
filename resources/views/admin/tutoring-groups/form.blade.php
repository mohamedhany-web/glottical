@extends('layouts.admin')

@section('title', ($mode === 'create' ? 'إضافة' : 'تعديل').' · '.$typeLabel.' - Glottical')
@section('page_title', $mode === 'create' ? 'مجموعة جديدة' : 'تعديل المجموعة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $action = $mode === 'create'
        ? route('admin.tutoring-groups.store', $type)
        : route('admin.tutoring-groups.update', [$type, $group]);
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحتوى · {{ $typeLabel }}</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">
                {{ $mode === 'create' ? 'مجموعة جديدة' : 'تعديل: '.$group->title }}
            </h2>
        </div>
        <a href="{{ route('admin.tutoring-groups.index', $type) }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للقائمة
        </a>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger shadow-soft">
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">البيانات الأساسية</h3>
            </div>
            <div class="grid gap-5 p-4 sm:grid-cols-2 sm:p-5">
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="title">العنوان <span class="text-danger">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title', $group->title) }}" required maxlength="255" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="slug">الرابط (اختياري)</label>
                    <input id="slug" type="text" name="slug" value="{{ old('slug', $group->slug) }}" dir="ltr" class="{{ $fieldClass }} font-mono" placeholder="auto-from-title">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="instructor_id">المدرب <span class="text-danger">*</span></label>
                    <select id="instructor_id" name="instructor_id" required class="{{ $fieldClass }}">
                        <option value="">اختر المدرب</option>
                        @foreach($instructors as $ins)
                            <option value="{{ $ins->id }}" @selected((string) old('instructor_id', $group->instructor_id) === (string) $ins->id)>{{ $ins->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="description">الوصف</label>
                    <textarea id="description" name="description" rows="5" class="{{ $areaClass }}">{{ old('description', $group->description) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="image">صورة الغلاف</label>
                    <input id="image" type="file" name="image" accept="image/*" class="block w-full text-sm text-muted file:ml-4 file:rounded-xl file:border-0 file:bg-accent-soft file:px-4 file:py-2 file:text-sm file:font-medium file:text-accent">
                    @if($group->imageUrl())
                        <img src="{{ $group->imageUrl() }}" alt="" class="mt-3 h-28 rounded-xl object-cover">
                    @endif
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">التسعير والجلسة</h3>
            </div>
            <div class="grid gap-5 p-4 sm:grid-cols-2 lg:grid-cols-4 sm:p-5">
                <div>
                    <label class="{{ $labelClass }}" for="price">السعر (اختياري)</label>
                    <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $group->price) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="currency">العملة</label>
                    <input id="currency" type="text" name="currency" value="{{ old('currency', $group->currency ?: 'EGP') }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="duration_minutes">مدة الجلسة (دقيقة)</label>
                    <input id="duration_minutes" type="number" min="30" max="240" name="duration_minutes" value="{{ old('duration_minutes', $group->duration_minutes ?: 60) }}" required class="{{ $fieldClass }}">
                </div>
                @if($type === 'collective')
                    <div>
                        <label class="{{ $labelClass }}" for="capacity">السعة</label>
                        <input id="capacity" type="number" min="2" max="500" name="capacity" value="{{ old('capacity', $group->capacity ?: 8) }}" required class="{{ $fieldClass }}">
                    </div>
                @else
                    <input type="hidden" name="capacity" value="1">
                @endif
                <div>
                    <label class="{{ $labelClass }}" for="sort_order">ترتيب العرض</label>
                    <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $group->sort_order ?: 0) }}" class="{{ $fieldClass }}">
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">الظهور</h3>
            </div>
            <div class="flex flex-wrap gap-6 p-4 sm:p-5">
                <label class="inline-flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-line text-accent focus:ring-accent/30" @checked(old('is_active', $group->is_active ?? true))>
                    نشطة وتظهر للزوار
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-line text-accent focus:ring-accent/30" @checked(old('is_featured', $group->is_featured ?? false))>
                    مميزة
                </label>
            </div>
        </article>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                <i class="fas fa-save text-xs"></i>
                حفظ
            </button>
            <a href="{{ route('admin.tutoring-groups.index', $type) }}" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-6 text-sm font-medium text-ink hover:bg-canvas">إلغاء</a>
        </div>
    </form>
</div>
@endsection
