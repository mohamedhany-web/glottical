@extends('layouts.app')

@section('title', $item->title.' — مكتبة المناهج')
@section('header', $item->title)

@section('content')
@php
    $hasSections = isset($sectionTree) && $sectionTree->isNotEmpty();
    $hasLegacyFiles = $item->files && $item->files->isNotEmpty();
    $hasHtmlContent = filled($item->content ?? null);
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">
                <a href="{{ route('instructor.libraries.curriculum.index') }}" class="hover:text-[#0B3D91]">مكتبة المناهج</a>
                @if($item->category) · {{ $item->category->name }}@endif
                @if($item->subject) · {{ $item->subject }}@endif
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $item->title }}</h2>
            @if($item->description)
                <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ $item->description }}</p>
            @endif
        </div>
        <a href="{{ route('instructor.libraries.curriculum.index') }}" class="inline-flex h-9 items-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700">رجوع</a>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    @if($hasSections)
        <div class="space-y-3">
            @include('instructor.libraries.curriculum._section-node', ['sections' => $sectionTree, 'item' => $item, 'depth' => 0])
        </div>
    @endif

    @if($hasLegacyFiles)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
                <h3 class="font-semibold text-slate-900">ملفات قديمة</h3>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach($item->files as $file)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $file->label ?: $file->file_type }}</div>
                        <div class="flex flex-wrap gap-2">
                            @if($file->file_type === 'html')
                                <a href="{{ route('curriculum-library.file.view', [$item, $file]) }}" target="_blank" rel="noopener" class="rounded-lg bg-[#0B3D91] px-3 py-1.5 text-xs font-semibold text-white">عرض</a>
                            @elseif($file->file_type === 'presentation')
                                <a href="{{ route('curriculum-library.file.presentation', [$item, $file]) }}" target="_blank" rel="noopener" class="rounded-lg bg-[#0B3D91] px-3 py-1.5 text-xs font-semibold text-white">عرض تفاعلي</a>
                            @elseif($file->file_type === 'pdf')
                                <a href="{{ route('curriculum-library.file.pdf', [$item, $file]) }}" target="_blank" rel="noopener" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">عرض</a>
                                <a href="{{ route('curriculum-library.file.download', [$item, $file]) }}" class="rounded-lg bg-[#0B3D91] px-3 py-1.5 text-xs font-semibold text-white">تحميل</a>
                            @else
                                <a href="{{ route('curriculum-library.file.download', [$item, $file]) }}" class="rounded-lg bg-[#0B3D91] px-3 py-1.5 text-xs font-semibold text-white">تحميل</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($hasHtmlContent)
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm prose max-w-none">{!! $item->content !!}</article>
    @endif

    @if(! $hasSections && ! $hasLegacyFiles && ! $hasHtmlContent)
        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-12 text-center text-slate-500">
            لا يوجد محتوى لهذا المنهج بعد.
        </div>
    @endif
</div>
@endsection
