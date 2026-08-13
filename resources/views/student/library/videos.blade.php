@extends('layouts.student-timeline')

@section('title', __('student_timeline.nav_library_videos'))

@section('content')
@php
    $locale = app()->getLocale();
    $videos = $videos ?? collect();
    $folders = $folders ?? collect();
    $activeFolder = $activeFolder ?? null;
    $uncategorizedCount = (int) ($uncategorizedCount ?? 0);
    $searchQuery = $searchQuery ?? '';
    $academyCount = (int) ($academyCount ?? 0);
    $teacherCount = (int) ($teacherCount ?? 0);
    $themeFilter = $themeFilter ?? '';
    $familyThemes = $familyThemes ?? \App\Support\FamilyLibraryThemes::all();
    $tones = ['blue', 'pink', 'orange', 'purple', 'green'];
    $videosEmpty = $videos instanceof \Illuminate\Contracts\Pagination\Paginator
        ? $videos->isEmpty()
        : collect($videos)->isEmpty();
    $videoCount = method_exists($videos, 'total')
        ? $videos->total()
        : collect($videos)->count();

    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        if ($seconds <= 0) {
            return null;
        }
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;

        return sprintf('%d:%02d', $m, $s);
    };

    $folderLabel = null;
    if ($activeFolder) {
        $folderLabel = ($activeFolder->is_uncategorized ?? false)
            ? __('student_timeline.folder_uncategorized')
            : $activeFolder->displayName($locale);
    }

    $crumbs = [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.family_library_title'), 'url' => route('student.library.home')],
        ['label' => __('student_timeline.nav_library_videos'), 'url' => ($activeFolder || $themeFilter) ? route('student.library.videos') : null],
    ];
    if ($themeFilter && isset($familyThemes[$themeFilter])) {
        $crumbs[] = ['label' => $locale === 'en' ? $familyThemes[$themeFilter]['en'] : $familyThemes[$themeFilter]['ar'], 'url' => null];
    }
    if ($folderLabel) {
        $crumbs[] = ['label' => $folderLabel, 'url' => null];
    }

    $pageItems = $videos instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($videos->items())
        : collect($videos);
    $academyVideos = $pageItems->filter(fn ($v) => ! $v->isTeacherPrivate())->values();
    $teacherVideos = $pageItems->filter(fn ($v) => $v->isTeacherPrivate())->values();
    $showSections = ! $activeFolder && ! $videosEmpty && ($academyVideos->isNotEmpty() || $teacherVideos->isNotEmpty());
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.nav_library_videos'),
    'crumbs' => $crumbs,
    'toolbarView' => 'student.library._videos-toolbar',
    'toolbarData' => [
        'searchQuery' => $searchQuery,
        'videoCount' => $videoCount,
        'activeFolder' => $activeFolder,
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
        <h2>{{ $folderLabel ?: __('student_timeline.videos_title') }}</h2>
        <p>{{ $activeFolder && !($activeFolder->is_uncategorized ?? false) && method_exists($activeFolder, 'displayDescription') && $activeFolder->displayDescription($locale)
            ? $activeFolder->displayDescription($locale)
            : __('student_timeline.family_videos_hint') }}</p>
    </div>
    <div class="st-msg-intro__actions">
        @if(Route::has('student.library.home'))
            <a href="{{ route('student.library.home') }}" class="st-pill st-pill--outline">{{ __('student_timeline.family_library_title') }}</a>
        @endif
        @if(Route::has('student.library.materials'))
            <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_materials') }}</a>
        @endif
    </div>
</section>

@if(!empty($familyThemes))
    <section class="st-theme-strip" aria-label="{{ __('student_timeline.family_themes') }}">
        <a href="{{ route('student.library.videos', array_filter(['q' => $searchQuery ?: null, 'folder' => $activeFolder ? (($activeFolder->is_uncategorized ?? false) ? 'none' : ($activeFolder->slug ?: $activeFolder->id)) : null])) }}"
           class="st-theme-chip {{ $themeFilter === '' ? 'is-active' : '' }}">{{ __('student_timeline.materials_filter_all') }}</a>
        @foreach(['kids','islamic','general'] as $themeKey)
            @continue(!isset($familyThemes[$themeKey]))
            @php $meta = $familyThemes[$themeKey]; @endphp
            <a href="{{ route('student.library.videos', array_filter(['theme' => $themeKey, 'q' => $searchQuery ?: null, 'folder' => $activeFolder ? (($activeFolder->is_uncategorized ?? false) ? 'none' : ($activeFolder->slug ?: $activeFolder->id)) : null])) }}"
               class="st-theme-chip st-theme-chip--{{ $meta['tone'] }} {{ $themeFilter === $themeKey ? 'is-active' : '' }}">
                <i class="{{ $meta['icon'] }}" aria-hidden="true"></i>
                {{ $locale === 'en' ? $meta['en'] : $meta['ar'] }}
            </a>
        @endforeach
    </section>
@endif

@if(! $activeFolder && ($academyCount > 0 || $teacherCount > 0))
    <div class="st-lib-source-strip" aria-label="{{ $locale === 'ar' ? 'مصادر المكتبة' : 'Library sources' }}">
        <div class="st-lib-source-chip st-lib-source-chip--academy">
            <i class="fas fa-university" aria-hidden="true"></i>
            <div>
                <strong>{{ $locale === 'ar' ? 'من الأكاديمية' : 'From academy' }}</strong>
                <span>{{ $academyCount }} {{ $locale === 'ar' ? 'فيديو' : 'videos' }}</span>
            </div>
        </div>
        <div class="st-lib-source-chip st-lib-source-chip--teacher">
            <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
            <div>
                <strong>{{ $locale === 'ar' ? 'من معلميك' : 'From your teachers' }}</strong>
                <span>{{ $teacherCount }} {{ $locale === 'ar' ? 'فيديو' : 'videos' }}</span>
            </div>
        </div>
    </div>
@endif

@if($folders->isNotEmpty() || $uncategorizedCount > 0)
    <section class="st-folder-grid" aria-label="{{ __('student_timeline.folders_title') }}">
        <a href="{{ route('student.library.videos', array_filter(['q' => $searchQuery ?: null])) }}"
           class="st-folder-card st-folder-card--all {{ ! $activeFolder ? 'is-active' : '' }}">
            <span class="st-folder-card__icon"><i class="fas fa-th-large" aria-hidden="true"></i></span>
            <span class="st-folder-card__body">
                <strong>{{ __('student_timeline.folder_all') }}</strong>
                <em>{{ __('student_timeline.folder_all_hint') }}</em>
            </span>
        </a>

        @foreach($folders as $i => $folder)
            @php $tone = $folder->color ?: $tones[$i % count($tones)]; @endphp
            <a href="{{ route('student.library.videos', array_filter(['folder' => $folder->slug ?: $folder->id, 'q' => $searchQuery ?: null])) }}"
               class="st-folder-card st-folder-card--{{ $tone }} {{ ($activeFolder && !($activeFolder->is_uncategorized ?? false) && (int) $activeFolder->id === (int) $folder->id) ? 'is-active' : '' }}">
                <span class="st-folder-card__icon"><i class="{{ $folder->icon ?: 'fas fa-folder' }}" aria-hidden="true"></i></span>
                <span class="st-folder-card__body">
                    <strong>{{ $folder->displayName($locale) }}</strong>
                    <em>
                        {{ trans_choice('student_timeline.folder_videos_count', (int) ($folder->library_videos_count ?? 0), ['count' => (int) ($folder->library_videos_count ?? 0)]) }}
                        @if($folder->instructor_id)
                            · {{ $locale === 'ar' ? 'من معلمك' : 'Teacher' }}
                        @else
                            · {{ $locale === 'ar' ? 'أكاديمية' : 'Academy' }}
                        @endif
                    </em>
                </span>
            </a>
        @endforeach

        @if($uncategorizedCount > 0)
            <a href="{{ route('student.library.videos', array_filter(['folder' => 'none', 'q' => $searchQuery ?: null])) }}"
               class="st-folder-card st-folder-card--purple {{ ($activeFolder->is_uncategorized ?? false) ? 'is-active' : '' }}">
                <span class="st-folder-card__icon"><i class="fas fa-inbox" aria-hidden="true"></i></span>
                <span class="st-folder-card__body">
                    <strong>{{ __('student_timeline.folder_uncategorized') }}</strong>
                    <em>{{ trans_choice('student_timeline.folder_videos_count', $uncategorizedCount, ['count' => $uncategorizedCount]) }}</em>
                </span>
            </a>
        @endif
    </section>
@endif

@if($videosEmpty)
    <div class="st-empty-panel">
        <h3>{{ __('student_timeline.no_videos') }}</h3>
        <p>{{ $locale === 'ar'
            ? 'لا توجد فيديوهات متاحة لك حالياً من الأكاديمية أو معلميك.'
            : 'No videos are available from the academy or your teachers yet.' }}</p>
        <div class="st-biz-banner__actions">
            @if($activeFolder)
                <a href="{{ route('student.library.videos') }}" class="st-pill st-pill--solid">{{ __('student_timeline.folder_all') }}</a>
            @endif
            @if(Route::has('student.library.materials'))
                <a href="{{ route('student.library.materials') }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_library_materials') }}</a>
            @endif
            <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
        </div>
    </div>
@else
    @php
        $renderCard = function ($video, $i) use ($tones, $locale, $formatDuration) {
            $tone = $tones[$i % count($tones)];
            $isTeacher = $video->isTeacherPrivate();
            $course = $isTeacher
                ? ($video->instructor->name ?? ($locale === 'ar' ? 'معلمك' : 'Your teacher'))
                : ($video->folder ? $video->folder->displayName($locale) : ($locale === 'ar' ? 'الأكاديمية' : 'Academy'));
            $duration = $formatDuration($video->duration_seconds ?? 0);
            $watchUrl = route('student.library.videos.show', $video);
            $thumb = $video->external_url ? \App\Helpers\VideoHelper::getThumbnail($video->external_url) : null;
            return compact('tone', 'isTeacher', 'course', 'duration', 'watchUrl', 'thumb');
        };
    @endphp

    @if($showSections && $academyVideos->isNotEmpty())
        <div class="st-lib-section-head">
            <h3><i class="fas fa-university" aria-hidden="true"></i> {{ $locale === 'ar' ? 'من الأكاديمية' : 'From the academy' }}</h3>
            <p>{{ $locale === 'ar' ? 'محتوى عام تنشره الإدارة في المكتبة.' : 'General content published by the academy.' }}</p>
        </div>
        <section class="st-video-grid" aria-label="{{ $locale === 'ar' ? 'فيديوهات الأكاديمية' : 'Academy videos' }}">
            @foreach($academyVideos as $i => $video)
                @php extract($renderCard($video, $i)); @endphp
                <article class="st-video-card st-video-card--{{ $tone }}">
                    <div class="st-video-card__thumb" @if($thumb) style="background-image:url('{{ $thumb }}')" @endif aria-hidden="true">
                        <i class="fas fa-play"></i>
                        @if($duration)<span class="st-video-card__dur">{{ $duration }}</span>@endif
                    </div>
                    <div class="st-video-card__body">
                        <p class="st-video-card__course">{{ $course }}</p>
                        <h3>{{ $video->title }}</h3>
                        @if($video->series_title || $video->content_theme)
                            <p class="st-video-card__meta">
                                @if($video->content_theme){{ $video->themeLabel($locale) }}@endif
                                @if($video->series_title) · {{ $video->series_title }}@endif
                                @if($video->age_label) · {{ $video->age_label }}@endif
                            </p>
                        @endif
                        <p class="st-video-card__meta"><span class="st-lib-badge st-lib-badge--academy">{{ $locale === 'ar' ? 'أكاديمية' : 'Academy' }}</span> · {{ $video->sourceLabel() }}</p>
                    </div>
                    <div class="st-video-card__foot">
                        <a href="{{ $watchUrl }}" class="st-pill st-pill--solid"><i class="fas fa-play" aria-hidden="true"></i> {{ __('student_timeline.watch_video') }}</a>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    @if($showSections && $teacherVideos->isNotEmpty())
        <div class="st-lib-section-head">
            <h3><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> {{ $locale === 'ar' ? 'من معلميك' : 'From your teachers' }}</h3>
            <p>{{ $locale === 'ar' ? 'محتوى خاص أرسله معلموك إليك.' : 'Private content your teachers shared with you.' }}</p>
        </div>
        <section class="st-video-grid" aria-label="{{ $locale === 'ar' ? 'فيديوهات المعلمين' : 'Teacher videos' }}">
            @foreach($teacherVideos as $i => $video)
                @php extract($renderCard($video, $i)); @endphp
                <article class="st-video-card st-video-card--{{ $tone }}">
                    <div class="st-video-card__thumb" @if($thumb) style="background-image:url('{{ $thumb }}')" @endif aria-hidden="true">
                        <i class="fas fa-play"></i>
                        @if($duration)<span class="st-video-card__dur">{{ $duration }}</span>@endif
                    </div>
                    <div class="st-video-card__body">
                        <p class="st-video-card__course">{{ $course }}</p>
                        <h3>{{ $video->title }}</h3>
                        @if($video->series_title || $video->content_theme)
                            <p class="st-video-card__meta">
                                @if($video->content_theme){{ $video->themeLabel($locale) }}@endif
                                @if($video->series_title) · {{ $video->series_title }}@endif
                                @if($video->age_label) · {{ $video->age_label }}@endif
                            </p>
                        @endif
                        <p class="st-video-card__meta"><span class="st-lib-badge st-lib-badge--teacher">{{ $locale === 'ar' ? 'معلمك' : 'Teacher' }}</span> · {{ $video->sourceLabel() }}</p>
                    </div>
                    <div class="st-video-card__foot">
                        <a href="{{ $watchUrl }}" class="st-pill st-pill--solid"><i class="fas fa-play" aria-hidden="true"></i> {{ __('student_timeline.watch_video') }}</a>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    @if(! $showSections)
        <section class="st-video-grid" aria-label="{{ __('student_timeline.videos_title') }}">
            @foreach($videos as $i => $video)
                @php extract($renderCard($video, $i)); @endphp
                <article class="st-video-card st-video-card--{{ $tone }}">
                    <div class="st-video-card__thumb" @if($thumb) style="background-image:url('{{ $thumb }}')" @endif aria-hidden="true">
                        <i class="fas fa-play"></i>
                        @if($duration)<span class="st-video-card__dur">{{ $duration }}</span>@endif
                    </div>
                    <div class="st-video-card__body">
                        <p class="st-video-card__course">{{ $course }}</p>
                        <h3>{{ $video->title }}</h3>
                        @if($video->series_title || $video->content_theme)
                            <p class="st-video-card__meta">
                                @if($video->content_theme){{ $video->themeLabel($locale) }}@endif
                                @if($video->series_title) · {{ $video->series_title }}@endif
                                @if($video->age_label) · {{ $video->age_label }}@endif
                            </p>
                        @endif
                        <p class="st-video-card__meta">
                            <span class="st-lib-badge {{ $isTeacher ? 'st-lib-badge--teacher' : 'st-lib-badge--academy' }}">
                                {{ $isTeacher ? ($locale === 'ar' ? 'معلمك' : 'Teacher') : ($locale === 'ar' ? 'أكاديمية' : 'Academy') }}
                            </span>
                            · {{ $video->sourceLabel() }}
                        </p>
                    </div>
                    <div class="st-video-card__foot">
                        <a href="{{ $watchUrl }}" class="st-pill st-pill--solid"><i class="fas fa-play" aria-hidden="true"></i> {{ __('student_timeline.watch_video') }}</a>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    @if(method_exists($videos, 'hasPages') && $videos->hasPages())
        <div class="st-pager">
            {{ $videos->links() }}
        </div>
    @endif
@endif
@endsection
