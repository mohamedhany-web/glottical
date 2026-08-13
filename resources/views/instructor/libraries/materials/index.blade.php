@extends('layouts.app')

@section('title', 'مكتبة الماتريال')
@section('header', 'مكتبة الماتريال')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">لوحة المعلم</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-900">ماتريال حسب السنة</h2>
            <p class="mt-1 text-sm text-slate-500">مجلداتك تظهر لطلابك فقط. مجلدات الإدارة للعرض إن وُجدت.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 mb-3">إنشاء مجلد جديد</h3>
        <form method="POST" action="{{ route('instructor.libraries.materials.folders.store') }}" class="grid gap-3 md:grid-cols-5">
            @csrf
            <input type="text" name="name_ar" required placeholder="اسم المجلد (عربي)" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
            <input type="text" name="name_en" placeholder="Name (EN)" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
            <select name="academic_year_id" required class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
                <option value="">السنة الدراسية *</option>
                @foreach($years as $y)
                    <option value="{{ $y->id }}">{{ $y->name }}</option>
                @endforeach
            </select>
            <select name="content_theme" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
                @foreach(\App\Support\FamilyLibraryThemes::labels('ar') as $key => $themeLabel)
                    <option value="{{ $key }}">{{ $themeLabel }}</option>
                @endforeach
            </select>
            <button class="h-10 rounded-xl bg-[#0B3D91] px-4 text-sm font-semibold text-white">إنشاء</button>
        </form>
    </article>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($folders as $folder)
            <a href="{{ route('instructor.libraries.materials.show', $folder) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-[#0B3D91]/40">
                <div class="text-xs text-slate-500">{{ $folder->academicYear->name ?? 'عامة' }}</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $folder->displayName() }}</div>
                <div class="mt-2 text-xs text-slate-500">{{ (int) $folder->materials_count }} ملف</div>
                @if(! $folder->instructor_id)
                    <div class="mt-2 text-[11px] text-amber-700">مجلد إداري (عرض فقط)</div>
                @endif
            </a>
        @empty
            <p class="text-sm text-slate-500 col-span-full">لا توجد فولدرات بعد.</p>
        @endforelse
    </div>
</div>
@endsection
