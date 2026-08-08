@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $name = $profile->user->name ?? __('public.instructor_fallback');
    $headline = $profile->headline_clean ?: __('public.instructor_fallback');
    $bioClean = $profile->bio_clean;
    $skills = $profile->skills_list ?? [];
    $experiences = $profile->experience_list ?? [];
    $instrPageTitle = $name.' — '.$headline.' | '.$brand;
    $instrPageDesc = \Illuminate\Support\Str::limit($bioClean ?: $headline, 160);
    $instrPageImg = ($profile->photo_url ?? null) ?: asset('images/og-image.jpg');
    $instrPageUrl = route('public.instructors.show', $profile->user);
    $weeklyCalendar = $weeklyCalendar ?? [];
    $canBook = (bool) ($canBook ?? false);
    $unitsLeft = (int) ($unitsLeft ?? 0);
    $bookableSlots = $bookableSlots ?? collect();
    $packagesUrl = $packagesUrl ?? route('public.service-packages.index');
    $introEmbedUrl = $introEmbedUrl ?? null;
    $introDirectVideo = $introDirectVideo ?? null;
    $hasIntroVideo = filled($introEmbedUrl) || filled($introDirectVideo);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $instrPageTitle }}</title>
  <meta name="description" content="{{ $instrPageDesc }}">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="{{ $instrPageUrl }}">
  <meta property="og:type" content="profile">
  <meta property="og:url" content="{{ $instrPageUrl }}">
  <meta property="og:title" content="{{ $instrPageTitle }}">
  <meta property="og:description" content="{{ $instrPageDesc }}">
  <meta property="og:image" content="{{ $instrPageImg }}">
  <meta property="og:site_name" content="{{ $brand }}">
  @include('partials.favicon-links')
  @include('partials.seo-jsonld', ['jsonldType' => 'instructor', 'profile' => $profile])
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'instructor-profile']])
</head>
<body class="sana-home sana-courses-page sana-instructors-page gl-tp">
@include('partials.landing.navbar', ['navActive' => 'instructors'])

<main>
  <div class="sana-container gl-tp-wrap">
    <nav class="gl-tp-crumb" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
      <a href="{{ url('/') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
      <span aria-hidden="true">/</span>
      <a href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ $name }}</span>
    </nav>

    @if($errors->any())
      <div class="gl-tp-note is-err" style="margin-bottom:1rem">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
      <div class="gl-tp-note is-ok" style="margin-bottom:1rem">{{ session('success') }}</div>
    @endif

    <div class="gl-tp-layout">
      <div>
        <div class="gl-tp-video">
          @if($introEmbedUrl)
            <iframe src="{{ $introEmbedUrl }}" title="{{ $name }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
          @elseif($introDirectVideo)
            <video controls playsinline preload="metadata" poster="{{ $profile->photo_url }}">
              <source src="{{ $introDirectVideo }}">
            </video>
          @elseif($profile->photo_url)
            <img src="{{ $profile->photo_url }}" alt="{{ $name }}">
          @else
            <div class="gl-tp-video__empty"><i class="fas fa-chalkboard-teacher"></i></div>
          @endif
        </div>

        <div class="gl-tp-head">
          <div class="gl-tp-head__row">
            @if($profile->photo_url)
              <img class="gl-tp-photo" src="{{ $profile->photo_url }}" alt="{{ $name }}">
            @endif
            <div>
              <span class="gl-tp-chip"><i class="fas fa-badge-check"></i> {{ __('public.instructors_verified') }}</span>
              <h1 class="gl-tp-name">{{ $name }}</h1>
              <p class="gl-tp-title">{{ $headline }}</p>
            </div>
          </div>
        </div>

        @if($bioClean)
          <article class="gl-tp-card" style="margin-top:1rem">
            <h2>{{ __('public.instructor_bio_title') }}</h2>
            <p class="gl-tp-bio">{{ $bioClean }}</p>
          </article>
        @endif

        @if(count($experiences) > 0 || $profile->experience)
          <article class="gl-tp-card" style="margin-top:1rem">
            <h2>{{ __('public.experience') }}</h2>
            @if(count($experiences) > 0)
              <ul class="gl-tp-exp">
                @foreach($experiences as $item)
                  <li>{{ $item }}</li>
                @endforeach
              </ul>
            @else
              <p class="gl-tp-bio">{{ $profile->sanitizedText($profile->experience) }}</p>
            @endif
          </article>
        @endif

        @if(count($skills) > 0)
          <article class="gl-tp-card" style="margin-top:1rem">
            <h2>{{ __('public.skills') }}</h2>
            <div class="gl-tp-skills">
              @foreach($skills as $skill)
                <span class="gl-tp-skill">{{ $skill }}</span>
              @endforeach
            </div>
          </article>
        @endif
      </div>

      <aside class="gl-tp-aside">
        <div class="gl-tp-card">
          <h3>{{ __('public.private_weekly_slots') }}</h3>
          @if(!empty($weeklyCalendar))
            <div class="gl-tp-cal">
              @foreach($weeklyCalendar as $col)
                <div class="gl-tp-cal__day">
                  <span class="gl-tp-cal__label">{{ $col['label'] }}</span>
                  <div class="gl-tp-cal__times">
                    @foreach($col['times'] as $t)
                      <span class="gl-tp-cal__t">{{ $t }}</span>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="gl-tp-bio">{{ $isRtl ? 'لم يُحدَّد جدول توافر بعد.' : 'No weekly availability published yet.' }}</p>
          @endif

          @unless($canBook)
            <p class="gl-tp-note" style="margin-top:1rem">
              {{ $isRtl
                ? 'يمكنك مشاهدة الفيديو والجدول. حجز الموعد يتاح بعد الاشتراك في باقة.'
                : 'You can watch the intro video and browse the schedule. Booking unlocks after you subscribe to a package.' }}
            </p>
            <div class="gl-tp-actions" style="margin-top:.75rem">
              <a href="{{ $packagesUrl }}" class="sana-btn sana-btn--yellow">
                {{ $isRtl ? 'اشترك في باقة للحجز' : 'Subscribe to book' }}
              </a>
              @guest
                <a href="{{ route('login', ['redirect' => $instrPageUrl]) }}" class="sana-btn sana-btn--purple-outline">
                  {{ $isRtl ? 'تسجيل الدخول' : 'Log in' }}
                </a>
              @endguest
            </div>
          @else
            <p class="gl-tp-note is-ok" style="margin-top:1rem">
              {{ $isRtl ? ('رصيدك المتاح: '.$unitsLeft.' حصة — اختر موعداً للحجز.') : ('Available credits: '.$unitsLeft.' — pick a slot to book.') }}
            </p>
            @if($bookableSlots->isNotEmpty())
              <form method="POST" action="{{ route('student.one-to-one-sessions.book-instructor', $profile->user) }}" class="gl-tp-slots" style="margin-top:.75rem">
                @csrf
                @foreach($bookableSlots as $slot)
                  @php
                    $starts = is_array($slot) ? ($slot['starts_at'] ?? null) : ($slot->starts_at ?? null);
                    $label = is_array($slot) ? ($slot['label'] ?? null) : ($slot->label ?? null);
                    if ($starts instanceof \Carbon\Carbon) {
                      $value = $starts->toDateTimeString();
                      $label = $label ?: $starts->translatedFormat('D j M — g:i A');
                    } else {
                      continue;
                    }
                  @endphp
                  <button type="submit" name="scheduled_at" value="{{ $value }}" class="gl-tp-slot">
                    <span>{{ $label }}</span>
                    <i class="fas fa-calendar-plus" style="color:#0B3D91"></i>
                  </button>
                @endforeach
              </form>
            @else
              <p class="gl-tp-bio" style="margin-top:.75rem">{{ $isRtl ? 'لا توجد مواعيد مفتوحة خلال الأسابيع القادمة.' : 'No open slots in the coming weeks.' }}</p>
            @endif
          @endunless
        </div>

        <div class="gl-tp-card">
          <h3>{{ $isRtl ? 'روابط سريعة' : 'Quick links' }}</h3>
          <div class="gl-tp-actions">
            <a href="{{ route('public.courses', ['delivery' => 'one_to_one']) }}" class="sana-btn sana-btn--purple">{{ __('public.nav_one_to_one_courses') ?? ($isRtl ? 'دروس فردية' : '1:1 courses') }}</a>
            <a href="{{ route('public.instructors.index') }}" class="sana-btn sana-btn--purple-outline">{{ __('public.all_instructors_link') }}</a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

@include('partials.landing.footer')
</body>
</html>
