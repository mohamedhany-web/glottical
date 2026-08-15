<article class="st-mat-card st-mat-card--{{ $tone }}" role="listitem">
    <div class="st-mat-card__top">
        <span class="st-mat-card__badge" aria-hidden="true">
            <i class="{{ $meta['fa'] }}"></i>
        </span>
        <span class="st-mat-card__ext">{{ $meta['label'] }}</span>
    </div>
    <h3 title="{{ $title }}">{{ $title }}</h3>
    <p class="st-mat-card__source">
        <span class="st-lib-badge {{ $isTeacher ? 'st-lib-badge--teacher' : 'st-lib-badge--academy' }}">
            {{ $isTeacher ? __('student_timeline.lib_badge_teacher') : __('student_timeline.lib_badge_academy') }}
        </span>
        @if($teacherName)
            <span>{{ $teacherName }}</span>
        @endif
    </p>
    @if($material->content_theme)
        <p class="st-mat-card__theme">{{ $material->themeLabel($locale) }}</p>
    @endif
    @if($folderTitle)
        <p class="st-mat-card__course">{{ $folderTitle }}</p>
    @elseif($courseTitle)
        <p class="st-mat-card__course">{{ $courseTitle }}</p>
    @endif
    @if($lectureTitle)
        <p class="st-mat-card__lecture">{{ $lectureTitle }}</p>
    @endif
    <div class="st-mat-card__foot">
        @if($playable)
            <a href="{{ $experienceUrl }}" class="st-mat-card__btn">
                <i class="fas fa-play" aria-hidden="true"></i>
                {{ __('student_timeline.open_in_platform') }}
            </a>
        @endif
        @if($downloadUrl)
            <a href="{{ $downloadUrl }}" class="st-mat-card__btn {{ $playable ? 'st-mat-card__btn--ghost' : '' }}">
                <i class="fas fa-download" aria-hidden="true"></i>
                {{ __('student_timeline.open_file') }}
            </a>
        @elseif(! $playable)
            <span class="st-lib-card__missing">{{ __('student_timeline.file_unavailable') }}</span>
        @endif
    </div>
</article>
