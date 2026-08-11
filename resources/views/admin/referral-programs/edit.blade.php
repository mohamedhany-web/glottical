@extends('layouts.admin')

@section('title', 'تعديل برنامج الإحالات - ' . config('app.name'))
@section('page_title', 'تعديل برنامج الإحالات')

@section('content')
@php
    $checkClass = 'size-4 rounded border-line text-accent focus:ring-accent/20';
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">التسويق · الإحالات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تعديل: {{ $referralProgram->name }}</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">تحديث قواعد رصيد الحصص أو الخصم</p>
        </div>
        <a href="{{ route('admin.referral-programs.index') }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للقائمة
        </a>
    </section>

    <form action="{{ route('admin.referral-programs.update', $referralProgram) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        @include('admin.referral-programs._form', ['program' => $referralProgram, 'scopes' => $scopes ?? []])

        <article class="rounded-2xl border border-line bg-accent-soft p-4 shadow-soft sm:p-5">
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_default" id="is_default" value="1" @checked(old('is_default', $referralProgram->is_default)) class="{{ $checkClass }}">
                <label for="is_default" class="text-sm font-medium text-ink">جعل هذا البرنامج <strong>الافتراضي</strong> لإحالات التسجيل الجديدة</label>
            </div>
        </article>

        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-4 shadow-soft">
            <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $referralProgram->is_active)) class="{{ $checkClass }}">
            <label for="is_active" class="text-sm font-medium text-ink">تفعيل البرنامج</label>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('admin.referral-programs.index') }}"
               class="btn-press inline-flex h-11 items-center rounded-xl border border-line bg-surface px-6 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">إلغاء</a>
            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                <i class="fas fa-save text-xs"></i> حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection
