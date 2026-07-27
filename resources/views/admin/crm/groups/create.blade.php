@extends('layouts.admin')

@section('title', 'مجموعة CRM جديدة')
@section('page_title', 'مجموعة جديدة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · المجموعات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">مجموعة جديدة</h2>
            <p class="mt-1 text-sm text-muted">أنشئ مجموعة ثم أضف الأعضاء من صفحة الإدارة</p>
        </div>
        <a href="{{ route('admin.crm.groups.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            العودة
        </a>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <article class="max-w-xl overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <form method="POST" action="{{ route('admin.crm.groups.store') }}" class="space-y-5 p-5 sm:p-6">
            @csrf
            <div>
                <label class="{{ $labelClass }}" for="name">اسم المجموعة <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="{{ $fieldClass }}" placeholder="مثال: فريق القاهرة">
                @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}" for="team_leader_id">قائد الفريق</label>
                <select name="team_leader_id" id="team_leader_id" class="{{ $fieldClass }}">
                    <option value="">— اختياري —</option>
                    @foreach($leaders as $l)
                        <option value="{{ $l->id }}" @selected((string) old('team_leader_id') === (string) $l->id)>{{ $l->name }}</option>
                    @endforeach
                </select>
                @error('team_leader_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-wrap gap-2 border-t border-line pt-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ
                </button>
                <a href="{{ route('admin.crm.groups.index') }}" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">إلغاء</a>
            </div>
        </form>
    </article>
</div>
@endsection
