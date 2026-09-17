{{-- Preply-style tutor card — public landing only --}}
@php
    use App\Helpers\VideoHelper;
    use Illuminate\Support\Str;

    $isRtl = app()->getLocale() === 'ar';
    $profile = $profile ?? null;
    if (! $profile) {
        return;
    }
    $user = $profile->user;
    $name = $user->name ?? __('public.instructor_fallback');
    $url = route('public.instructors.show', $user);
    $photo = $profile->photo_url;
    $initial = mb_substr($name, 0, 1);
    $headline = $profile->headline_clean ?: __('public.instructor_fallback');
    $bio = $profile->bio_clean;
    $skills = array_slice($profile->skills_list ?? [], 0, 3);
    $languages = [];
    if (method_exists($user, 'privateTeachingMeta')) {
        $metaLangs = $user->privateTeachingMeta()['languages'] ?? [];
        $languages = is_array($metaLangs) ? array_slice(array_values(array_filter($metaLangs)), 0, 3) : [];
    }
    $coursesCount = (int) ($profile->courses_count ?? 0);
    $price = null;
    $duration = null;
    try {
        $price = $profile->effectiveConsultationPriceEgp();
        $duration = $profile->effectiveConsultationDurationMinutes();
    } catch (\Throwable) {
        $price = null;
    }
    $videoUrl = $user->portfolio_intro_video_url ?? null;
    $videoThumb = $videoUrl ? VideoHelper::getThumbnail($videoUrl) : null;
    $compact = ! empty($compact);
@endphp
<article class="gl-tutor-card sana-reveal{{ $compact ? ' gl-tutor-card--compact' : '' }}">
  <div class="gl-tutor-card__main">
    <a href="{{ $url }}" class="gl-tutor-card__photo" tabindex="-1" aria-hidden="true">
      @if($photo)
        <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy" width="120" height="120">
      @else
        <span class="gl-tutor-card__av">{{ $initial }}</span>
      @endif
    </a>

    <div class="gl-tutor-card__info">
      <div class="gl-tutor-card__name-row">
        <h3 class="gl-tutor-card__name">
          <a href="{{ $url }}">{{ $name }}</a>
        </h3>
        <span class="gl-tutor-card__verified" title="{{ __('public.instructors_verified') }}">
          <i class="fas fa-shield-alt" aria-hidden="true"></i>
          <span class="sr-only">{{ __('public.instructors_verified') }}</span>
        </span>
      </div>

      @if(count($skills) > 0 || count($languages) > 0)
        <ul class="gl-tutor-card__meta">
          @foreach($skills as $skill)
            <li><i class="fas fa-graduation-cap" aria-hidden="true"></i> {{ $skill }}</li>
          @endforeach
          @foreach($languages as $lang)
            <li><i class="fas fa-comment" aria-hidden="true"></i> {{ $lang }}</li>
          @endforeach
        </ul>
      @endif

      <p class="gl-tutor-card__headline">{{ $headline }}</p>
      @if($bio !== '')
        <p class="gl-tutor-card__bio">
          {{ Str::limit($bio, $compact ? 110 : 160) }}
          <a href="{{ $url }}" class="gl-tutor-card__more">{{ __('public.tutor_card_learn_more') }}</a>
        </p>
      @endif
    </div>

    <div class="gl-tutor-card__pricing">
      @if($price !== null && $price > 0)
        <div class="gl-tutor-card__price">
          <strong>{{ __('public.tutor_card_currency') }}{{ number_format((float) $price, 0) }}</strong>
          <span>{{ __('public.tutor_card_lesson_minutes', ['min' => $duration ?: 50]) }}</span>
        </div>
      @else
        <div class="gl-tutor-card__price gl-tutor-card__price--soft">
          <strong>{{ __('public.tutor_card_view_pricing') }}</strong>
          <span>{{ __('public.instructors_consult_label') }}</span>
        </div>
      @endif

      <ul class="gl-tutor-card__stats">
        <li><i class="fas fa-book-open" aria-hidden="true"></i> {{ $coursesCount }} {{ __('public.instructors_course_many') }}</li>
        <li><i class="fas fa-check-circle" aria-hidden="true"></i> {{ __('public.instructors_verified') }}</li>
      </ul>

      <div class="gl-tutor-card__actions">
        <a href="{{ $url }}#book" class="gl-tutor-card__btn gl-tutor-card__btn--primary">
          {{ __('public.tutor_card_book_trial') }}
        </a>
        <a href="{{ $url }}" class="gl-tutor-card__btn gl-tutor-card__btn--ghost">
          {{ __('public.tutor_card_message') }}
        </a>
      </div>
    </div>
  </div>

  <aside class="gl-tutor-card__rail">
    @if($videoUrl)
      <a href="{{ $url }}#intro-video" class="gl-tutor-card__video" aria-label="{{ __('public.tutor_card_watch_video') }}">
        @if($videoThumb)
          <img src="{{ $videoThumb }}" alt="" loading="lazy">
        @else
          <span class="gl-tutor-card__video-ph" aria-hidden="true"></span>
        @endif
        <span class="gl-tutor-card__play"><i class="fas fa-play" aria-hidden="true"></i></span>
      </a>
    @else
      <a href="{{ $url }}" class="gl-tutor-card__video gl-tutor-card__video--empty" aria-label="{{ __('public.view_instructor_profile') }}">
        <span class="gl-tutor-card__video-ph" aria-hidden="true"><i class="fas fa-user"></i></span>
      </a>
    @endif
    <a href="{{ $url }}#schedule" class="gl-tutor-card__rail-btn">{{ __('public.tutor_card_view_schedule') }}</a>
    <a href="{{ $url }}" class="gl-tutor-card__rail-btn">{{ __('public.tutor_card_see_profile', ['name' => Str::before($name, ' ') ?: $name]) }}</a>
  </aside>
</article>
