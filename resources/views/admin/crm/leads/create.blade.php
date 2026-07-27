@extends('layouts.admin')

@section('title', 'إضافة عميل محتمل - CRM')
@section('page_title', 'إضافة عميل محتمل')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · العملاء المحتملون</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إضافة عميل محتمل</h2>
            <p class="mt-1 text-sm text-muted">إنشاء Lead جديد مع تعيين اختياري للمبيعات أو التسويق</p>
        </div>
        <a href="{{ route('admin.crm.leads.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            العودة للقائمة
        </a>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <form method="POST" action="{{ route('admin.crm.leads.store') }}" class="p-5 sm:p-6">
            @csrf
            @include('admin.crm.leads._form')
            <div class="mt-8 flex flex-wrap gap-2 border-t border-line pt-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ العميل
                </button>
                <a href="{{ route('admin.crm.leads.index') }}" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                    إلغاء
                </a>
            </div>
        </form>
    </article>
</div>
@endsection
