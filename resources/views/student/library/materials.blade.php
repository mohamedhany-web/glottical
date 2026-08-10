@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_library_materials'))

@section('content')
@php
    $locale = app()->getLocale();
    $materials = $materials ?? collect();
    $searchQuery = $searchQuery ?? '';
    $tones = ['blue', 'pink', 'orange', 'purple'];

    $fileMeta = function ($material) {
        $name = (string) ($material->file_name ?: $material->file_path ?: '');
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $map = [
            'pdf' => ['fa' => 'fas fa-file-pdf', 'label' => 'PDF'],
            'doc' => ['fa' => 'fas fa-file-word', 'label' => 'DOC'],
            'docx' => ['fa' => 'fas fa-file-word', 'label' => 'DOC'],
            'ppt' => ['fa' => 'fas fa-file-powerpoint', 'label' => 'PPT'],
            'pptx' => ['fa' => 'fas fa-file-powerpoint', 'label' => 'PPT'],
            'xls' => ['fa' => 'fas fa-file-excel', 'label' => 'XLS'],
            'xlsx' => ['fa' => 'fas fa-file-excel', 'label' => 'XLS'],
            'zip' => ['fa' => 'fas fa-file-archive', 'label' => 'ZIP'],
            'rar' => ['fa' => 'fas fa-file-archive', 'label' => 'RAR'],
            'png' => ['fa' => 'fas fa-file-image', 'label' => 'IMG'],
            'jpg' => ['fa' => 'fas fa-file-image', 'label' => 'IMG'],
            'jpeg' => ['fa' => 'fas fa-file-image', 'label' => 'IMG'],
            'mp3' => ['fa' => 'fas fa-file-audio', 'label' => 'AUD'],
            'mp4' => ['fa' => 'fas fa-file-video', 'label' => 'VID'],
        ];

        return $map[$ext] ?? ['fa' => 'fas fa-file-alt', 'label' => strtoupper($ext ?: 'FILE')];
    };
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_library_materials'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_library_materials'), 'url' => null],
    ],
    'toolbarView' => 'student.library._materials-toolbar',
    'toolbarData' => [
        'searchQuery' => $searchQuery,
        'materialCount' => $materials->count(),
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
        <h2>{{ __('student_timeline.materials_title') }}</h2>
        <p>{{ __('student_timeline.materials_hint') }}</p>
    </div>
    @if(Route::has('student.library.videos'))
        <a href="{{ route('student.library.videos') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_videos') }}</a>
    @endif
</section>

<section class="st-lib-list" aria-label="{{ __('student_timeline.materials_title') }}">
    @forelse($materials as $i => $material)
        @php
            $meta = $fileMeta($material);
            $tone = $tones[$i % count($tones)];
            $title = $material->title ?: $material->file_name ?: __('student_timeline.material_item');
            $url = $material->file_path
                ? \Illuminate\Support\Facades\Storage::url($material->file_path)
                : null;
        @endphp
        <article class="st-lib-card st-lib-card--{{ $tone }}">
            <div class="st-lib-card__icon" aria-hidden="true">
                <i class="{{ $meta['fa'] }}"></i>
                <span>{{ $meta['label'] }}</span>
            </div>
            <div class="st-lib-card__copy">
                <h3>{{ $title }}</h3>
                @if($material->lecture?->title)
                    <p class="st-lib-card__meta">{{ $material->lecture->title }}</p>
                @endif
                @if($material->file_name && $material->title && $material->file_name !== $material->title)
                    <p class="st-lib-card__file">{{ $material->file_name }}</p>
                @endif
            </div>
            <div class="st-lib-card__foot">
                @if($url)
                    <a href="{{ $url }}" class="st-pill st-pill--solid" target="_blank" rel="noopener">
                        <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                        {{ __('student_timeline.open_file') }}
                    </a>
                @else
                    <span class="st-lib-card__missing">{{ __('student_timeline.file_unavailable') }}</span>
                @endif
            </div>
        </article>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.no_materials') }}</h3>
            <p>{{ __('student_timeline.no_materials_hint') }}</p>
            <div class="st-biz-banner__actions">
                @if(Route::has('student.classes.index'))
                    <a href="{{ route('student.classes.index') }}" class="st-pill st-pill--solid">{{ __('student_timeline.my_classes') }}</a>
                @endif
                <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
            </div>
        </div>
    @endforelse
</section>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.library_links') }}</h2>
</div>

@if(Route::has('student.library.videos'))
    <a href="{{ route('student.library.videos') }}" class="st-event-card st-event-card--blue">
        <h3>{{ __('student_timeline.nav_library_videos') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.videos_side_hint') }}</p>
    </a>
@endif

@if(Route::has('student.lectures.index'))
    <a href="{{ route('student.lectures.index') }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.nav_lectures') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.lectures_side_hint') }}</p>
    </a>
@endif

@if(Route::has('student.assignments.index'))
    <a href="{{ route('student.assignments.index') }}" class="st-event-card st-event-card--pink">
        <h3>{{ __('student_timeline.nav_assignments') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.assignments_side_hint') }}</p>
    </a>
@endif

@if(Route::has('student.classes.index'))
    <a href="{{ route('student.classes.index') }}" class="st-event-card st-event-card--blue">
        <h3>{{ __('student_timeline.my_classes') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.classes_hint') }}</p>
    </a>
@endif

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
