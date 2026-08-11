@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_library_materials'))

@section('content')
@php
    $locale = app()->getLocale();
    $materials = $materials ?? collect();
    $searchQuery = $searchQuery ?? '';
    $courseId = (int) ($courseId ?? 0);
    $lectureId = (int) ($lectureId ?? 0);
    $typeFilter = $typeFilter ?? 'all';
    $sort = $sort ?? 'newest';
    $courses = $courses ?? collect();
    $lectures = $lectures ?? collect();
    $typeCounts = $typeCounts ?? [];
    $tones = ['blue', 'pink', 'orange', 'purple', 'green'];

    $materialCount = method_exists($materials, 'total') ? $materials->total() : collect($materials)->count();
    $materialsEmpty = method_exists($materials, 'isEmpty') ? $materials->isEmpty() : collect($materials)->isEmpty();

    $fileMeta = function ($material) {
        $name = (string) ($material->file_name ?: $material->file_path ?: '');
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $map = [
            'pdf' => ['fa' => 'fas fa-file-pdf', 'label' => 'PDF', 'tone' => 'pink'],
            'doc' => ['fa' => 'fas fa-file-word', 'label' => 'DOC', 'tone' => 'blue'],
            'docx' => ['fa' => 'fas fa-file-word', 'label' => 'DOC', 'tone' => 'blue'],
            'ppt' => ['fa' => 'fas fa-file-powerpoint', 'label' => 'PPT', 'tone' => 'orange'],
            'pptx' => ['fa' => 'fas fa-file-powerpoint', 'label' => 'PPT', 'tone' => 'orange'],
            'xls' => ['fa' => 'fas fa-file-excel', 'label' => 'XLS', 'tone' => 'green'],
            'xlsx' => ['fa' => 'fas fa-file-excel', 'label' => 'XLS', 'tone' => 'green'],
            'zip' => ['fa' => 'fas fa-file-archive', 'label' => 'ZIP', 'tone' => 'purple'],
            'rar' => ['fa' => 'fas fa-file-archive', 'label' => 'RAR', 'tone' => 'purple'],
            'png' => ['fa' => 'fas fa-file-image', 'label' => 'IMG', 'tone' => 'blue'],
            'jpg' => ['fa' => 'fas fa-file-image', 'label' => 'IMG', 'tone' => 'blue'],
            'jpeg' => ['fa' => 'fas fa-file-image', 'label' => 'IMG', 'tone' => 'blue'],
            'mp3' => ['fa' => 'fas fa-file-audio', 'label' => 'AUD', 'tone' => 'orange'],
            'mp4' => ['fa' => 'fas fa-file-video', 'label' => 'VID', 'tone' => 'purple'],
        ];

        return $map[$ext] ?? ['fa' => 'fas fa-file-alt', 'label' => strtoupper($ext ?: 'FILE'), 'tone' => 'blue'];
    };

    $typeChips = [
        'all' => __('student_timeline.materials_filter_all'),
        'pdf' => 'PDF',
        'doc' => __('student_timeline.materials_filter_doc'),
        'ppt' => __('student_timeline.materials_filter_ppt'),
        'sheet' => __('student_timeline.materials_filter_sheet'),
        'zip' => __('student_timeline.materials_filter_zip'),
        'image' => __('student_timeline.materials_filter_image'),
        'audio' => __('student_timeline.materials_filter_audio'),
        'video' => __('student_timeline.materials_filter_video'),
        'other' => __('student_timeline.materials_filter_other'),
    ];

    $filterBase = array_filter([
        'q' => $searchQuery ?: null,
        'course' => $courseId ?: null,
        'lecture' => $lectureId ?: null,
        'sort' => $sort !== 'newest' ? $sort : null,
        'lang' => request('lang') ?: null,
    ], fn ($v) => $v !== null && $v !== '');
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
        'materialCount' => $materialCount,
        'courseId' => $courseId,
        'lectureId' => $lectureId,
        'typeFilter' => $typeFilter,
        'sort' => $sort,
        'courses' => $courses,
        'lectures' => $lectures,
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

<section class="st-mat-shell" aria-label="{{ __('student_timeline.materials_title') }}">
    <div class="st-mat-filters">
        <div class="st-mat-filters__row" role="tablist" aria-label="{{ __('student_timeline.materials_filter_type') }}">
            @foreach($typeChips as $key => $label)
                @php
                    $count = (int) ($typeCounts[$key] ?? 0);
                    $chipUrl = route('student.library.materials', array_filter($filterBase + ['type' => $key !== 'all' ? $key : null]));
                @endphp
                @if($key === 'all' || $count > 0 || $typeFilter === $key)
                    <a href="{{ $chipUrl }}"
                       class="st-mat-chip {{ $typeFilter === $key ? 'is-active' : '' }}"
                       role="tab"
                       aria-selected="{{ $typeFilter === $key ? 'true' : 'false' }}">
                        <span>{{ $label }}</span>
                        @if($key === 'all' || $count > 0)
                            <em>{{ $count }}</em>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>

        <form class="st-mat-filters__form" method="get" action="{{ route('student.library.materials') }}">
            @if(request('lang'))
                <input type="hidden" name="lang" value="{{ request('lang') }}">
            @endif
            @if($searchQuery !== '')
                <input type="hidden" name="q" value="{{ $searchQuery }}">
            @endif
            @if($typeFilter !== 'all')
                <input type="hidden" name="type" value="{{ $typeFilter }}">
            @endif

            <label class="st-mat-select">
                <span>{{ __('student_timeline.materials_filter_course') }}</span>
                <select name="course" onchange="this.form.submit()">
                    <option value="">{{ __('student_timeline.materials_filter_course_all') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected($courseId === (int) $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
            </label>

            <label class="st-mat-select">
                <span>{{ __('student_timeline.materials_filter_lecture') }}</span>
                <select name="lecture" onchange="this.form.submit()">
                    <option value="">{{ __('student_timeline.materials_filter_lecture_all') }}</option>
                    @foreach($lectures as $lecture)
                        <option value="{{ $lecture->id }}" @selected($lectureId === (int) $lecture->id)>{{ $lecture->title }}</option>
                    @endforeach
                </select>
            </label>

            <label class="st-mat-select">
                <span>{{ __('student_timeline.materials_filter_sort') }}</span>
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" @selected($sort === 'newest')>{{ __('student_timeline.materials_sort_newest') }}</option>
                    <option value="oldest" @selected($sort === 'oldest')>{{ __('student_timeline.materials_sort_oldest') }}</option>
                    <option value="title" @selected($sort === 'title')>{{ __('student_timeline.materials_sort_title') }}</option>
                    <option value="lecture" @selected($sort === 'lecture')>{{ __('student_timeline.materials_sort_lecture') }}</option>
                </select>
            </label>

            @if($courseId || $lectureId || $typeFilter !== 'all' || $searchQuery !== '')
                <a href="{{ route('student.library.materials') }}" class="st-mat-reset">{{ __('student_timeline.materials_clear_filters') }}</a>
            @endif
        </form>
    </div>

    @if($materialsEmpty)
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.no_materials') }}</h3>
            <p>{{ __('student_timeline.no_materials_hint') }}</p>
            <div class="st-biz-banner__actions">
                @if($courseId || $lectureId || $typeFilter !== 'all' || $searchQuery !== '')
                    <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--solid">{{ __('student_timeline.materials_clear_filters') }}</a>
                @endif
                @if(Route::has('student.classes.index'))
                    <a href="{{ route('student.classes.index') }}" class="st-pill st-pill--outline">{{ __('student_timeline.my_classes') }}</a>
                @endif
            </div>
        </div>
    @else
        <div class="st-mat-grid" role="list">
            @foreach($materials as $i => $material)
                @php
                    $meta = $fileMeta($material);
                    $tone = $meta['tone'] ?? $tones[$i % count($tones)];
                    $title = $material->title ?: $material->file_name ?: __('student_timeline.material_item');
                    $url = $material->downloadUrl();
                    $courseTitle = $material->lecture?->course?->title;
                    $lectureTitle = $material->lecture?->title;
                @endphp
                <article class="st-mat-card st-mat-card--{{ $tone }}" role="listitem">
                    <div class="st-mat-card__top">
                        <span class="st-mat-card__badge" aria-hidden="true">
                            <i class="{{ $meta['fa'] }}"></i>
                        </span>
                        <span class="st-mat-card__ext">{{ $meta['label'] }}</span>
                    </div>
                    <h3 title="{{ $title }}">{{ $title }}</h3>
                    @if($courseTitle)
                        <p class="st-mat-card__course">{{ $courseTitle }}</p>
                    @endif
                    @if($lectureTitle)
                        <p class="st-mat-card__lecture">{{ $lectureTitle }}</p>
                    @endif
                    <div class="st-mat-card__foot">
                        @if($url)
                            <a href="{{ $url }}" class="st-mat-card__btn">
                                <i class="fas fa-download" aria-hidden="true"></i>
                                {{ __('student_timeline.open_file') }}
                            </a>
                        @else
                            <span class="st-lib-card__missing">{{ __('student_timeline.file_unavailable') }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if(method_exists($materials, 'hasPages') && $materials->hasPages())
            <div class="st-pager">
                {{ $materials->links() }}
            </div>
        @endif
    @endif
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

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
