@extends('layouts.admin')

@section('title', ($mode === 'create' ? 'باقة جديدة' : 'تعديل باقة').' - Glottical')
@section('page_title', $mode === 'create' ? 'باقة خدمات جديدة' : 'تعديل باقة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $action = $mode === 'create' ? route('admin.service-packages.store') : route('admin.service-packages.update', $package);
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">باقات الخدمات</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'إنشاء باقة' : 'تعديل: '.$package->name }}</h2>
        </div>
        <a href="{{ route('admin.service-packages.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <article class="rounded-2xl border border-line bg-surface p-4 sm:p-5 shadow-soft">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="name">اسم الباقة *</label>
                    <input id="name" name="name" value="{{ old('name', $package->name) }}" required class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="slug">الرابط</label>
                    <input id="slug" name="slug" value="{{ old('slug', $package->slug) }}" dir="ltr" class="{{ $fieldClass }} font-mono">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="badge">شارة</label>
                    <input id="badge" name="badge" value="{{ old('badge', $package->badge) }}" class="{{ $fieldClass }}" placeholder="الأكثر اختياراً">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="scope">النطاق *</label>
                    <select id="scope" name="scope" class="{{ $fieldClass }}">
                        @foreach(\App\Models\ServicePackage::scopes() as $value => $label)
                            <option value="{{ $value }}" @selected(old('scope', $package->scope) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="tutoring_group_id">مجموعة مخصصة (اختياري)</label>
                    <select id="tutoring_group_id" name="tutoring_group_id" class="{{ $fieldClass }}">
                        <option value="">— عامة —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" @selected((string) old('tutoring_group_id', $package->tutoring_group_id) === (string) $g->id)>{{ $g->title }} ({{ $g->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="units_count">عدد الحصص *</label>
                    <input id="units_count" type="number" min="1" name="units_count" value="{{ old('units_count', $package->units_count) }}" required class="{{ $fieldClass }}">
                    <p class="mt-1 text-[11px] text-muted">كل حصة = وحدة واحدة تُخصم عند اكتمال الحصة.</p>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="session_minutes">مدة الحصة (دقيقة)</label>
                    <input id="session_minutes" type="number" min="15" max="480" name="session_minutes" value="{{ old('session_minutes', $package->session_minutes ?: 60) }}" class="{{ $fieldClass }}">
                    <p class="mt-1 text-[11px] text-muted">تظهر للطالب كإجمالي ساعات الباقة.</p>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="duration_days">الصلاحية بالأيام</label>
                    <input id="duration_days" type="number" min="1" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" class="{{ $fieldClass }}">
                    <p class="mt-1 text-[11px] text-muted">اتركه فارغاً لباقة بلا تاريخ انتهاء.</p>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="price">السعر *</label>
                    <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $package->price) }}" required class="{{ $fieldClass }}" placeholder="USD">
                    <p class="mt-1 text-[11px] text-muted">بالدولار الأمريكي USD.</p>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="original_price">السعر قبل الخصم</label>
                    <input id="original_price" type="number" step="0.01" min="0" name="original_price" value="{{ old('original_price', $package->original_price) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="currency">العملة</label>
                    <input id="currency" value="USD — دولار أمريكي" class="{{ $fieldClass }}" readonly>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="sort_order">الترتيب</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" class="{{ $fieldClass }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="description">الوصف</label>
                    <textarea id="description" name="description" rows="3" class="{{ $areaClass }}">{{ old('description', $package->description) }}</textarea>
                </div>
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active ?? true))> نشطة</label>
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $package->is_featured))> مميزة</label>
            </div>
        </article>

        <button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white">حفظ</button>
    </form>
</div>
@endsection
