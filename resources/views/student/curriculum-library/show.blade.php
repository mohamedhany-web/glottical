@extends('layouts.student-timeline')

@section('title', $item->title.' — '.__('student_timeline.lib_manahij_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $hasSections = isset($sectionTree) && $sectionTree->isNotEmpty();
    $hasLegacyFiles = $item->files && $item->files->isNotEmpty();
    $hasHtmlContent = filled($item->content ?? null);
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
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
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
        <a href="{{ route('student.library.files') }}" class="st-pill st-pill--outline">{{ __('student_timeline.lib_files_title') }}</a>
    </div>
</section>

@if($hasSections)
    <section class="st-cl-panel" aria-label="محتوى المنهج">
        <header class="st-cl-panel__head">
            <h3><i class="fas fa-sitemap" aria-hidden="true"></i> محتوى المنهج</h3>
            <p>الأقسام والمواد المرفوعة لهذا المنهج — اضغط عرض أو تحميل حسب نوع الملف.</p>
        </header>
        <div class="st-cl-tree">
            @include('student.curriculum-library._section-node', ['sections' => $sectionTree, 'item' => $item, 'depth' => 0])
        </div>
    </section>
@endif

@if($hasLegacyFiles)
    <section class="st-cl-panel" aria-label="ملفات قديمة">
        <header class="st-cl-panel__head">
            <h3><i class="fas fa-layer-group" aria-hidden="true"></i> ملفات قديمة</h3>
            <p>ملفات مرفقة قبل الهيكل الهرمي للأقسام.</p>
        </header>
        <ul class="st-cl-mat-list">
            @foreach($item->files as $file)
                <li class="st-cl-mat">
                    <span class="st-cl-mat__icon st-cl-mat__icon--{{ $file->file_type === 'presentation' ? 'pptx' : ($file->file_type === 'pdf' ? 'pdf' : ($file->file_type === 'html' ? 'html' : 'other')) }}">
                        <i class="fas {{ $file->file_type === 'presentation' ? 'fa-file-powerpoint' : ($file->file_type === 'pdf' ? 'fa-file-pdf' : ($file->file_type === 'html' ? 'fa-code' : 'fa-file')) }}" aria-hidden="true"></i>
                    </span>
                    <div class="st-cl-mat__body">
                        <strong>{{ $file->label ?: $file->file_type }}</strong>
                    </div>
                    <div class="st-cl-mat__actions">
                        @if($file->file_type === 'html')
                            <a href="{{ route('curriculum-library.file.view', [$item, $file]) }}" target="_blank" rel="noopener" class="st-pill st-pill--solid">عرض</a>
                        @elseif($file->file_type === 'presentation')
                            <a href="{{ route('curriculum-library.file.presentation', [$item, $file]) }}" target="_blank" rel="noopener" class="st-pill st-pill--solid">عرض تفاعلي</a>
                        @elseif($file->file_type === 'pdf')
                            <a href="{{ route('curriculum-library.file.pdf', [$item, $file]) }}" target="_blank" rel="noopener" class="st-pill st-pill--outline">عرض</a>
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

@if($hasHtmlContent)
    <section class="st-cl-panel st-cl-panel--prose">
        <div class="st-cl-prose">{!! $item->content !!}</div>
    </section>
@endif

@if(! $hasSections && ! $hasLegacyFiles && ! $hasHtmlContent)
    <div class="st-empty-panel">
        <h3>لا يوجد محتوى لهذا المنهج بعد</h3>
        <p>لم تُرفع أقسام أو مواد لهذا المنهج. راجع هيكل المنهج من لوحة الإدارة بعد رفع الملفات إلى التخزين.</p>
        <a href="{{ route('curriculum-library.index') }}" class="st-pill st-pill--solid">العودة للمناهج</a>
    </div>
@endif
@endsection
