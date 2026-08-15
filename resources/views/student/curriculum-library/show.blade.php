@extends('layouts.student-timeline')

@section('title', $item->title.' — '.__('student_timeline.lib_manahij_title'))

@section('content')
@php
    $locale = app()->getLocale();
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $item->title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.family_library_title'), 'url' => route('student.library.home')],
        ['label' => __('student_timeline.lib_manahij_title'), 'url' => route('curriculum-library.index')],
        ['label' => $item->title, 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ $item->title }}</h2>
        <p>
            @if($item->category || $item->subject || $item->grade_level)
                @if($item->category)<span class="st-lib-badge st-lib-badge--academy">{{ $item->category->name }}</span>@endif
                @if($item->subject)<span class="st-lib-badge">{{ $item->subject }}</span>@endif
                @if($item->grade_level)<span class="st-lib-badge">{{ $item->grade_level }}</span>@endif
            @endif
            @if($item->description)
                <br>{{ $item->description }}
            @endif
        </p>
    </div>
    <div class="st-msg-intro__actions">
        <a href="{{ route('curriculum-library.index') }}" class="st-pill st-pill--outline">{{ __('student_timeline.lib_manahij_title') }}</a>
    </div>
</section>

@if(isset($sectionTree) && $sectionTree->isNotEmpty())
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
        <h3 class="text-lg font-bold text-slate-800 m-0"><i class="fas fa-sitemap text-indigo-500 ml-2"></i> محتوى المنهج</h3>
        @include('student.curriculum-library._section-node', ['sections' => $sectionTree, 'item' => $item, 'depth' => 0])
    </section>
@endif

@if($item->files && $item->files->isNotEmpty())
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3 mt-4">
        <h3 class="text-lg font-bold text-slate-800 m-0"><i class="fas fa-layer-group text-indigo-500 ml-2"></i> ملفات قديمة</h3>
        <ul class="space-y-2 m-0 p-0 list-none">
            @foreach($item->files as $file)
                <li class="flex flex-wrap items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="flex-1 font-medium text-slate-800">{{ $file->label ?: $file->file_type }}</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($file->file_type === 'html')
                            <a href="{{ route('curriculum-library.file.view', [$item, $file]) }}" target="_blank" class="st-pill st-pill--solid">عرض</a>
                        @elseif($file->file_type === 'presentation')
                            <a href="{{ route('curriculum-library.file.presentation', [$item, $file]) }}" target="_blank" class="st-pill st-pill--solid">عرض تفاعلي</a>
                        @elseif($file->file_type === 'pdf')
                            <a href="{{ route('curriculum-library.file.pdf', [$item, $file]) }}" target="_blank" class="st-pill st-pill--outline">عرض</a>
                            <a href="{{ route('curriculum-library.file.download', [$item, $file]) }}" class="st-pill st-pill--solid">تحميل</a>
                        @else
                            <a href="{{ route('curriculum-library.file.download', [$item, $file]) }}" class="st-pill st-pill--solid">تحميل</a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
@endif
@endsection
