@extends('layouts.app')

@section('title', $material->title ?: $material->file_name)
@section('header', $material->title ?: $material->file_name)

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">
                <a href="{{ route('instructor.libraries.materials.index') }}" class="hover:text-[#0B3D91]">مكتبة الماتريال</a>
                ·
                <a href="{{ route('instructor.libraries.materials.show', $folder) }}" class="hover:text-[#0B3D91]">{{ $folder->displayName() }}</a>
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $material->title ?: $material->file_name }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $isGame ? 'تشغيل داخل المنصة' : 'عرض داخل المنصة' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('instructor.libraries.materials.show', $folder) }}" class="inline-flex h-9 items-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700">رجوع</a>
            <a href="{{ route('instructor.libraries.materials.download', [$folder, $material]) }}" class="inline-flex h-9 items-center rounded-xl bg-[#0B3D91] px-4 text-sm font-semibold text-white">تحميل</a>
        </div>
    </section>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <iframe
            src="{{ $frameUrl }}"
            title="{{ $material->title ?: $material->file_name }}"
            class="h-[75vh] w-full"
            sandbox="allow-scripts allow-same-origin allow-forms"
        ></iframe>
    </div>
</div>
@endsection
