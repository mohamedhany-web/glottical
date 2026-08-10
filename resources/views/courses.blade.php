@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $langKey = $isRtl ? 'ar' : 'en';
    $activeDelivery = $delivery ?? 'one_to_one';
    $activeCategoryId = (int) ($categoryId ?? 0);
    $pathId = (int) ($pathId ?? 0);
    $searchQuery = $searchQuery ?? '';
    $courses = $courseModels ?? collect();
    $filters = $filters ?? ['subject'=>'','age'=>'','gender'=>'','language'=>'','specialty'=>'','availability'=>''];
    $filterCatalog = $filterCatalog ?? config('private_lessons');
    $calendarByInstructor = $calendarByInstructor ?? collect();
    $lessonDuration = (int) ($lessonDuration ?? 50);
    $baseQuery = array_filter([
        'delivery' => $activeDelivery ?: 'one_to_one',
        'q' => $searchQuery !== '' ? $searchQuery : null,
        'path' => $pathId ?: null,
        'category' => $activeCategoryId ?: null,
        'subject' => $filters['subject'] ?: null,
        'age' => $filters['age'] ?: null,
        'gender' => $filters['gender'] ?: null,
        'language' => $filters['language'] ?: null,
        'specialty' => $filters['specialty'] ?: null,
        'availability' => $filters['availability'] ?: null,
    ]);

    $filterUrl = function (array $overrides = []) use ($baseQuery) {
        return route('public.courses', array_filter(array_merge($baseQuery, $overrides), fn ($v) => $v !== null && $v !== ''));
    };

    $chipOn = function (string $key, string $value) use ($filters) {
        return ($filters[$key] ?? '') === $value ? 'is-on' : '';
    };
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.courses_page_title') }} — Glottical</title>
    <meta name="description" content="{{ __('public.courses_subtitle') }}">
  <link rel="canonical" href="{{ route('public.courses') }}">
  <link rel="alternate" hreflang="ar" href="{{ url('/courses') }}?lang=ar">
  <link rel="alternate" hreflang="en" href="{{ url('/courses') }}?lang=en">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog']])
  <style>
    .gl-catalog-panel{
      margin:1.15rem 0 1.65rem;padding:1rem 1.05rem 1.1rem;
      background:#fff;border:1.5px solid #D7DDE6;border-radius:18px;
      box-shadow:0 10px 28px -20px rgba(11,61,145,.28);
    }
    .gl-search{
      display:grid;grid-template-columns:minmax(0,1fr) auto;gap:.65rem;align-items:stretch;
      margin:0 0 .45rem;
    }
    .gl-search input{
      width:100%;min-width:0;height:3rem;border-radius:14px;border:1.5px solid #D7DDE6;
      padding:0 1rem;font:600 .95rem Tajawal,sans-serif;background:#F4F7FC;color:#0B1220;
    }
    .gl-search input:focus{outline:none;border-color:#0B3D91;box-shadow:0 0 0 3px rgba(11,61,145,.12);background:#fff}
    .gl-search button{
      height:3rem;padding:0 1.35rem;border:none;border-radius:14px;
      background:var(--gold,#F5B800);color:var(--p-dark,#072A66);
      font:800 .9rem Tajawal,sans-serif;cursor:pointer;white-space:nowrap;
    }
    .gl-search-hint{margin:0 0 .85rem;font:600 .75rem/1.4 Tajawal,sans-serif;color:#8A94A6}
    .gl-active-filters{display:flex;flex-wrap:wrap;gap:.4rem;align-items:center;margin:0 0 .75rem}
    .gl-active-filters:empty{display:none}
    .gl-active-pill{
      display:inline-flex;align-items:center;gap:6px;padding:.35rem .7rem;border-radius:999px;
      background:#E8EEF8;color:#0B3D91;font:800 .72rem Tajawal,sans-serif;text-decoration:none!important;
    }
    .gl-active-pill i{font-size:.65rem;opacity:.7}
    .gl-filters-shell{border-top:1px solid #E8EEF8;padding-top:.65rem}
    .gl-filters-toggle{
      width:100%;display:flex;align-items:center;justify-content:space-between;gap:12px;
      padding:.55rem .15rem;border:0;background:transparent;cursor:pointer;font:inherit;color:#0B1220;
    }
    .gl-filters-toggle strong{font:800 .88rem Tajawal,sans-serif}
    .gl-filters-toggle span{font:700 .72rem Tajawal,sans-serif;color:#5B6577}
    .gl-filters-toggle__icon{
      width:32px;height:32px;border-radius:10px;display:grid;place-items:center;
      background:#F4F7FC;border:1px solid #E8EEF8;color:#0B3D91;transition:transform .2s ease;
    }
    .gl-filters-shell.is-open .gl-filters-toggle__icon{transform:rotate(180deg);background:#0B3D91;color:#fff;border-color:#0B3D91}
    .gl-filters-body{display:none;padding-top:.35rem}
    .gl-filters-shell.is-open .gl-filters-body{display:block}
    .gl-acc{border:1px solid #E8EEF8;border-radius:14px;background:#F8FAFD;margin-bottom:.5rem;overflow:hidden}
    .gl-acc summary{
      list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:10px;
      padding:.75rem .9rem;font:800 .82rem Tajawal,sans-serif;color:#0B1220;user-select:none;
    }
    .gl-acc summary::-webkit-details-marker{display:none}
    .gl-acc summary::after{
      content:'+';width:22px;height:22px;border-radius:8px;display:grid;place-items:center;
      background:#fff;border:1px solid #D7DDE6;color:#0B3D91;font:800 .85rem/1 Tajawal,sans-serif;
    }
    .gl-acc[open] summary::after{content:'−'}
    .gl-acc__meta{font:700 .68rem Tajawal,sans-serif;color:#8A94A6;margin-inline-start:auto;margin-inline-end:.35rem}
    .gl-acc__meta.has-val{color:#0B3D91}
    .gl-acc__panel{padding:0 .9rem .85rem;display:flex;flex-wrap:wrap;gap:.4rem}
    .gl-chip{
      display:inline-flex;align-items:center;justify-content:center;
      min-height:2.1rem;padding:.35rem .85rem;border-radius:999px;
      border:1.5px solid #D7DDE6;background:#fff;
      font:700 .76rem/1.2 Tajawal,sans-serif;text-decoration:none!important;color:#0B1220;
      transition:background .15s ease,border-color .15s ease,color .15s ease;
    }
    .gl-chip:hover{border-color:rgba(11,61,145,.35);color:#0B3D91}
    .gl-chip.is-on{background:#0B3D91;border-color:#0B3D91;color:#fff}
    .gl-chip.is-on:hover{color:#fff;background:#072A66;border-color:#072A66}
    .gl-filters-actions{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:flex-end;margin-top:.55rem}
    .gl-grid{display:grid;gap:1.25rem;grid-template-columns:repeat(auto-fill,minmax(280px,1fr))}
    .gl-teacher{
      background:#fff;border-radius:20px;overflow:hidden;
      box-shadow:0 14px 40px -18px rgba(11,61,145,.28);
      display:flex;flex-direction:column;border:1px solid #E8EEF8;
    }
    .gl-teacher__media{aspect-ratio:16/10;background:#0B1220;overflow:hidden;position:relative}
    .gl-teacher__media img{width:100%;height:100%;object-fit:cover}
    .gl-teacher__play{
      position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
      background:linear-gradient(135deg,rgba(11,61,145,.55),rgba(7,42,102,.65));
    }
    .gl-teacher__play span{
      width:56px;height:56px;border-radius:50%;background:rgba(245,184,0,.95);
      display:flex;align-items:center;justify-content:center;color:#072A66;font-size:1.25rem;
    }
    .gl-teacher__body{padding:14px 16px 16px;display:flex;flex-direction:column;gap:10px;flex:1}
    .gl-teacher__name{margin:0;font:800 1.05rem/1.35 Cairo,Tajawal,sans-serif;color:#0B1220}
    .gl-teacher__role{margin:0;font:700 .8rem/1.4 Tajawal,sans-serif;color:#0B3D91}
    .gl-teacher__title{margin:0;font:800 .92rem/1.45 Cairo,Tajawal,sans-serif;color:#0B1220}
    .gl-badges{display:flex;flex-wrap:wrap;gap:6px}
    .gl-badge{
      display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:999px;
      background:#F4F7FC;border:1px solid #E8EEF8;font:700 .68rem Tajawal,sans-serif;color:#0B1220;
    }
    .gl-dur{
      display:inline-flex;align-items:center;gap:6px;font:800 .78rem Tajawal,sans-serif;color:#072A66;
      background:#FFF6D6;border:1px solid rgba(245,184,0,.45);border-radius:10px;padding:6px 10px;width:fit-content;
    }
    .gl-cal{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:6px}
    .gl-cal__col{border:1px solid #E8EEF8;border-radius:10px;padding:6px 4px;background:#F4F7FC;text-align:center}
    .gl-cal__day{display:block;font:800 .65rem Tajawal,sans-serif;color:#0B3D91;margin-bottom:4px}
    .gl-cal__t{display:block;font:700 .62rem/1.35 Tajawal,sans-serif;color:#0B1220}
    .gl-plans{display:grid;grid-template-columns:1fr 1fr;gap:8px}
    .gl-plan{
      text-align:center;text-decoration:none!important;border:1.5px solid #D7DDE6;border-radius:12px;
      padding:10px 8px;color:#0B1220;background:#fff;
    }
    .gl-plan strong{display:block;font:800 .8rem Tajawal,sans-serif}
    .gl-plan span{display:block;margin-top:3px;font:600 .68rem Tajawal,sans-serif;color:#5B6577}
    .gl-plan.is-featured{border-color:#0B3D91;background:#E8EEF8;color:#0B3D91}
    .gl-plan.is-featured span{color:#0B3D91}
    @media (max-width:520px){
      .gl-search{grid-template-columns:1fr}
      .gl-search button{width:100%}
      .gl-cal{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    </style>
</head>
<body class="sana-home sana-courses-page">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'courses', 'navSolid' => true, 'navHero' => false])

<main class="sana-cat-page">
  <section class="sana-cat-hero" id="cat-hero">
    <div class="sana-container sana-cat-hero__inner sana-reveal">
      <div class="sana-cat-hero__breadcrumb">
        <a href="{{ route('home') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
        <span>/</span>
        <span>{{ __('landing.nav.courses') }}</span>
      </div>
      <h1 class="sana-cat-hero__title">{{ __('public.courses_hero') }}</h1>
      <p class="sana-cat-hero__desc">{{ __('public.courses_subtitle') }}</p>
      <p class="sana-cat-hero__stats">
        <span class="sana-cat-hero__stat">
          <i class="fas fa-chalkboard-user"></i>
          {{ number_format($courses->count()) }} {{ __('public.private_teachers_count') }}
        </span>
      </p>
    </div>
  </section>

  <section class="sana-container" style="padding-bottom:4rem">
    <div class="gl-catalog-panel sana-reveal">
      <form action="{{ route('public.courses') }}" method="get" class="gl-search" role="search">
        <input type="hidden" name="delivery" value="{{ $activeDelivery ?: 'one_to_one' }}">
        @foreach(['path','category','subject','age','gender','language','specialty','availability'] as $hiddenKey)
          @if(!empty($filters[$hiddenKey] ?? ($hiddenKey === 'path' ? $pathId : ($hiddenKey === 'category' ? $activeCategoryId : ''))))
            <input type="hidden" name="{{ $hiddenKey }}" value="{{ $filters[$hiddenKey] ?? ($hiddenKey === 'path' ? $pathId : $activeCategoryId) }}">
          @endif
        @endforeach
        <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('public.search_course_placeholder') }}" aria-label="{{ __('public.search_course_placeholder') }}">
        <button type="submit">{{ $isRtl ? 'بحث' : 'Search' }}</button>
      </form>
      <p class="gl-search-hint">{{ __('public.private_search_examples') }}</p>

      @php
        $filterGroups = [
          ['key' => 'subject', 'label' => __('public.private_filter_subject'), 'options' => $filterCatalog['subjects'] ?? []],
          ['key' => 'age', 'label' => __('public.private_filter_age'), 'options' => $filterCatalog['age_groups'] ?? []],
          ['key' => 'gender', 'label' => __('public.private_filter_teacher'), 'options' => $filterCatalog['genders'] ?? []],
          ['key' => 'language', 'label' => __('public.private_filter_language'), 'options' => $filterCatalog['languages'] ?? []],
          ['key' => 'specialty', 'label' => __('public.private_filter_specialty'), 'options' => $filterCatalog['specializations'] ?? []],
          ['key' => 'availability', 'label' => __('public.private_filter_availability'), 'options' => $filterCatalog['availability'] ?? []],
        ];
        $activeCount = 0;
        foreach ($filterGroups as $g) {
          if (!empty($filters[$g['key']])) { $activeCount++; }
        }
        if (!empty($pathId)) { $activeCount++; }
        $filtersOpenByDefault = $activeCount > 0;
      @endphp

      <div class="gl-active-filters">
        @foreach($filterGroups as $group)
          @if(!empty($filters[$group['key']]))
            @php $val = $filters[$group['key']]; $lab = $group['options'][$val][$langKey] ?? $val; @endphp
            <a href="{{ $filterUrl([$group['key'] => null]) }}" class="gl-active-pill">
              {{ $group['label'] }}: {{ $lab }} <i class="fas fa-times"></i>
            </a>
          @endif
        @endforeach
        @if(!empty($pathId))
          @php $pathName = optional(($learningPaths ?? collect())->firstWhere('id', $pathId))->name ?? $pathId; @endphp
          <a href="{{ $filterUrl(['path' => null]) }}" class="gl-active-pill">
            {{ __('public.courses_filter_paths') }}: {{ $pathName }} <i class="fas fa-times"></i>
          </a>
        @endif
        @if($activeCount > 0)
          <a href="{{ route('public.courses', array_filter(['delivery' => $activeDelivery ?: 'one_to_one', 'q' => $searchQuery ?: null])) }}" class="gl-active-pill" style="background:#FFF6D6;color:#8A6A00">
            {{ $isRtl ? 'مسح الكل' : 'Clear all' }}
          </a>
        @endif
      </div>

      <div class="gl-filters-shell {{ $filtersOpenByDefault ? 'is-open' : '' }}" id="gl-filters-shell">
        <button type="button" class="gl-filters-toggle" id="gl-filters-toggle" aria-expanded="{{ $filtersOpenByDefault ? 'true' : 'false' }}" aria-controls="gl-filters-body">
          <div>
            <strong>{{ $isRtl ? 'تصفية المعلمين' : 'Filter teachers' }}</strong>
            <span>
              @if($activeCount > 0)
                {{ $isRtl ? ($activeCount.' فلتر نشط') : ($activeCount.' active') }}
              @else
                {{ $isRtl ? 'المادة · العمر · اللغة · التوفر…' : 'Subject · age · language · availability…' }}
              @endif
            </span>
          </div>
          <span class="gl-filters-toggle__icon" aria-hidden="true"><i class="fas fa-chevron-down"></i></span>
        </button>

        <div class="gl-filters-body" id="gl-filters-body">
          @foreach($filterGroups as $group)
            @php
              $selected = $filters[$group['key']] ?? '';
              $selectedLabel = $selected !== '' ? ($group['options'][$selected][$langKey] ?? $selected) : __('public.private_filter_all');
            @endphp
            <details class="gl-acc" @if($selected !== '') open @endif>
              <summary>
                <span>{{ $group['label'] }}</span>
                <span class="gl-acc__meta {{ $selected !== '' ? 'has-val' : '' }}">{{ $selectedLabel }}</span>
              </summary>
              <div class="gl-acc__panel" role="group" aria-label="{{ $group['label'] }}">
                <a href="{{ $filterUrl([$group['key'] => null]) }}" class="gl-chip {{ $selected === '' ? 'is-on' : '' }}">{{ __('public.private_filter_all') }}</a>
                @foreach($group['options'] as $optKey => $labels)
                  <a href="{{ $filterUrl([$group['key'] => $optKey]) }}" class="gl-chip {{ $chipOn($group['key'], $optKey) }}">{{ $labels[$langKey] ?? $optKey }}</a>
                @endforeach
              </div>
            </details>
          @endforeach

          @if(($learningPaths ?? collect())->isNotEmpty())
            <details class="gl-acc" @if(!empty($pathId)) open @endif>
              <summary>
                <span>{{ __('public.courses_filter_paths') }}</span>
                <span class="gl-acc__meta {{ !empty($pathId) ? 'has-val' : '' }}">
                  {{ !empty($pathId) ? (optional($learningPaths->firstWhere('id', $pathId))->name ?? __('public.courses_filter_paths')) : __('public.courses_filter_all_paths') }}
                </span>
              </summary>
              <div class="gl-acc__panel" role="group">
                <a href="{{ $filterUrl(['path' => null]) }}" class="gl-chip {{ empty($pathId) ? 'is-on' : '' }}">{{ __('public.courses_filter_all_paths') }}</a>
                @foreach($learningPaths as $path)
                  <a href="{{ $filterUrl(['path' => $path->id]) }}" class="gl-chip {{ (int)$pathId === (int)$path->id ? 'is-on' : '' }}">{{ $path->name }}</a>
                @endforeach
              </div>
            </details>
          @endif
        </div>
      </div>
                </div>

    <div class="gl-grid">
      @forelse($courses as $course)
        @php
          $instructor = $course->instructor;
          $thumb = $instructor?->profile_image_url
            ?? $course->thumbnail_url
            ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80';
          $url = route('public.course.show', $course->id);
          $teacherProfileUrl = $instructor
            ? route('public.instructors.show', $instructor)
            : $url;
          $teacher = $instructor->name ?? ($isRtl ? 'معلم متخصص' : 'Specialist teacher');
          $subject = $course->academicSubject->name ?? $course->title;
          $cardTitle = __('public.private_course_with_teacher', ['subject' => $subject, 'teacher' => $teacher]);
          $role = $isRtl
            ? (($course->academicSubject->name ?? 'تعليم إسلامي').' — معلم/ة')
            : (($course->academicSubject->name ?? 'Islamic Studies').' Teacher');
          $meta = is_array($instructor?->private_teaching_meta) ? $instructor->private_teaching_meta : [];
          $langs = collect($meta['languages'] ?? [])->map(fn ($k) => $filterCatalog['languages'][$k][$langKey] ?? null)->filter();
          $langLabel = $langs->isNotEmpty()
            ? $langs->implode(' & ')
            : ($isRtl ? 'English & Arabic' : 'English & Arabic');
          $hasChildren = in_array('children', $meta['specializations'] ?? [], true);
          $calendar = $calendarByInstructor[$course->instructor_id] ?? [];
          $hasVideo = filled($instructor?->portfolio_intro_video_url ?? null) || filled($course->video_url);
        @endphp
        <article class="gl-teacher sana-reveal">
          <a href="{{ $teacherProfileUrl }}" style="text-decoration:none;color:inherit;display:block">
            <div class="gl-teacher__media">
              @if($hasVideo)
                <div class="gl-teacher__play"><span><i class="fas fa-play"></i></span></div>
                <img src="{{ $thumb }}" alt="{{ $teacher }}" style="opacity:.5" loading="lazy">
              @else
                <img src="{{ $thumb }}" alt="{{ $teacher }}" loading="lazy">
              @endif
                        </div>
          </a>
          <div class="gl-teacher__body">
            <div>
              <h3 class="gl-teacher__name">{{ $teacher }}</h3>
              <p class="gl-teacher__role">{{ $role }}</p>
              <p class="gl-teacher__title" style="margin-top:6px">{{ $cardTitle }}</p>
                      </div>
            <div class="gl-badges">
              <span class="gl-badge">⭐ {{ __('public.private_badge_qualified') }}</span>
              @if($hasChildren)
                <span class="gl-badge">👧 {{ __('public.private_badge_children') }}</span>
              @endif
              <span class="gl-badge">🌎 {{ $langLabel }}</span>
                        </div>
            <div class="gl-dur"><i class="far fa-clock"></i> {{ __('public.private_lesson_duration') }}</div>
            <div>
              <p style="margin:0 0 6px;font:800 .68rem Tajawal,sans-serif;color:#8A94A6">{{ __('public.private_weekly_slots') }}</p>
              @if(!empty($calendar))
                <div class="gl-cal">
                  @foreach($calendar as $col)
                    <div class="gl-cal__col">
                      <span class="gl-cal__day">{{ $col['label'] }}</span>
                      @foreach($col['times'] as $t)
                        <span class="gl-cal__t">{{ $t }}</span>
                      @endforeach
                      </div>
                  @endforeach
                </div>
              @else
                <p style="margin:0;font:600 .75rem Tajawal,sans-serif;color:#8A94A6">{{ $isRtl ? 'اختر المعلم لعرض التقويم الكامل' : 'Open the teacher page for the full calendar' }}</p>
              @endif
            </div>
            <div>
              <p style="margin:0 0 6px;font:800 .68rem Tajawal,sans-serif;color:#8A94A6">{{ __('public.private_packages_label') }}</p>
              <div class="gl-plans">
                <a href="{{ route('public.service-packages.index') }}" class="gl-plan">
                  <strong>{{ __('public.private_package_1m') }}</strong>
                  <span>{{ __('public.private_package_1m_sub') }}</span>
                </a>
                <a href="{{ route('public.service-packages.index') }}" class="gl-plan is-featured">
                  <strong>{{ __('public.private_package_3m') }}</strong>
                  <span>{{ __('public.private_package_3m_sub') }}</span>
                </a>
              </div>
            </div>
            <a href="{{ $teacherProfileUrl }}" class="sana-btn sana-btn--yellow" style="justify-content:center;margin-top:2px">
              {{ $isRtl ? 'عرض المعلم والحجز' : 'View teacher & book' }}
            </a>
          </div>
        </article>
      @empty
        <p style="grid-column:1/-1;color:var(--muted);font-weight:700">{{ __('public.private_empty') }}</p>
      @endforelse
    </div>

    <div class="sana-reveal" style="margin-top:2.5rem;text-align:center">
      <a href="{{ url('/?open_trial=1') }}" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-clipboard-check"></i> {{ __('landing.academy.free_trial_cta') }}</a>
    </div>
  </section>
</main>

@include('partials.landing.footer')
<script>
(function () {
  var shell = document.getElementById('gl-filters-shell');
  var btn = document.getElementById('gl-filters-toggle');
  if (!shell || !btn) return;
  btn.addEventListener('click', function () {
    var open = shell.classList.toggle('is-open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
})();
</script>
</body>
</html>
