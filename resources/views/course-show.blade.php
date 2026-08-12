@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $thumbUrl = $course->thumbnail_url;
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    $introDirectVideo = \App\Helpers\VideoHelper::getDirectVideoUrl($introVideoUrl);
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $isMonthly = $course->isMonthlyBilling();
    $checkoutPrice = $course->effectiveCheckoutPrice();
    $isPaid = $checkoutPrice > 0 && ! ($course->is_free ?? false);
    $hasPromo = $isPaid && $course->hasPromotionalPrice();
    $listPrice = $hasPromo ? $course->listPriceAmount() : 0;
    $savedAmount = $hasPromo ? max(0, $listPrice - $checkoutPrice) : 0;
    $discountPct = ($hasPromo && $listPrice > 0)
        ? (int) round((1 - ($checkoutPrice / $listPrice)) * 100)
        : 0;
    $instructorApproved = $course->instructor
        && \App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists();
    $subjectName = $course->academicSubject->name ?? __('public.course_category_not_set');
    $learnPoints = $course->what_you_learn
        ? array_values(array_filter(array_map('trim', explode("\n", $course->what_you_learn))))
        : [];
    $courseOgImg = $thumbUrl ?? asset('images/og-image.jpg');
    $courseDesc = \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 160);
    $courseTitle = ($course->title ?? __('public.course_detail_title')).' | '.$brand;
    $courseUrl = url('/course/'.($course->id ?? ''));
    $isOneToOne = $course->isOneToOne();
    $from = $from ?? ($isOneToOne ? 'one_to_one' : 'groups');
    $groupsListUrl = $isOneToOne
        ? route('public.instructors.index')
        : route('public.groups.courses');
    $groupsListLabel = $isOneToOne
        ? __('landing.nav.instructors')
        : __('landing.groups_page.catalog_group_title');
    $deliveryLabel = $isOneToOne
        ? ($isRtl ? 'فردي 1:1' : '1:1 private')
        : ($isRtl ? 'جماعي' : 'Group');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>{{ $courseTitle }}</title>
    <meta name="description" content="{{ $courseDesc }}">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="{{ $courseUrl }}">
  <meta property="og:type" content="article">
  <meta property="og:url" content="{{ $courseUrl }}">
  <meta property="og:title" content="{{ $courseTitle }}">
  <meta property="og:description" content="{{ $courseDesc }}">
  <meta property="og:image" content="{{ $courseOgImg }}">
  <meta property="og:site_name" content="{{ $brand }}">
    @include('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course])
    @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog']])
    <style>
    .gl-cs { background: var(--bg,#F4F7FC); }
    .gl-cs-wrap.sana-container {
      max-width: 1180px;
      padding-top: 14px;
      padding-bottom: 40px;
    }
    .gl-cs-crumb {
      display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
      font-size: .72rem; font-weight: 700; color: #5B6577; margin-bottom: .65rem;
    }
    .gl-cs-crumb a { color: #0B3D91; text-decoration: none !important; }
    .gl-cs-crumb a:hover { text-decoration: underline !important; }
    .gl-cs-layout {
      display: grid; gap: .85rem;
    }
    @media (min-width: 992px) {
      .gl-cs-layout {
        grid-template-columns: minmax(0, 1.35fr) minmax(300px, 360px);
        gap: 1rem;
        align-items: start;
      }
    }
    .gl-cs-media {
      position: relative; overflow: hidden; border-radius: 14px;
      background: #0B1220; aspect-ratio: 16/9;
      box-shadow: 0 10px 24px -16px rgba(11,61,145,.32);
    }
    .gl-cs-media iframe,
    .gl-cs-media video,
    .gl-cs-media img {
      position: absolute; inset: 0; width: 100%; height: 100%;
      border: 0; object-fit: cover; display: block;
    }
    .gl-cs-media video { object-fit: contain; background: #0B1220; }
    .gl-cs-media__empty {
      position: absolute; inset: 0; display: grid; place-items: center;
      background: linear-gradient(145deg,#051F4D,#0B3D91); color: rgba(255,255,255,.35); font-size: 2.2rem;
    }
    .gl-cs-badge {
      position: absolute; top: 10px; inset-inline-start: 10px; z-index: 2;
      display: inline-flex; align-items: center; gap: 5px;
      padding: 4px 9px; border-radius: 999px;
      background: rgba(255,255,255,.95); color: #0B3D91;
      font-size: .68rem; font-weight: 800;
    }
    .gl-cs-panel {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 14px;
      padding: .8rem .85rem .9rem;
      box-shadow: 0 8px 22px -16px rgba(11,61,145,.28);
    }
    @media (min-width: 992px) {
      .gl-cs-panel { position: sticky; top: 84px; }
    }
    .gl-cs-instructor {
      display: inline-flex; align-items: center; gap: 6px;
      color: #0B3D91; font-size: .74rem; font-weight: 800;
      text-decoration: none !important; margin-bottom: .3rem;
    }
    .gl-cs-title {
      margin: 0 0 .4rem; font-family: Cairo,Tajawal,sans-serif;
      font-size: clamp(1.05rem, 1.8vw, 1.22rem); font-weight: 900;
      color: #0B1220; line-height: 1.3;
    }
    .gl-cs-tags { display: flex; flex-wrap: wrap; gap: .3rem; margin-bottom: .4rem; }
    .gl-cs-tag {
      display: inline-flex; align-items: center; padding: 3px 8px;
      border-radius: 999px; font-size: .66rem; font-weight: 800;
      background: #E8EEF8; color: #0B3D91;
    }
    .gl-cs-tag--gold { background: #FFF6D6; color: #9A7200; }
    .gl-cs-tag--green { background: #D1FAE5; color: #047857; }
    .gl-cs-tag--red { background: #FEE2E2; color: #B91C1C; }
    .gl-cs-lead {
      margin: 0 0 .55rem; font-size: .76rem; line-height: 1.55; color: #5B6577; font-weight: 600;
      display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .gl-cs-price-box {
      background: #F4F7FC; border: 1.5px solid #C5D4F0; border-radius: 10px;
      padding: .6rem .7rem; margin-bottom: .55rem;
    }
    .gl-cs-price {
      margin: 0; font-size: 1.2rem; font-weight: 900; color: #0B3D91; line-height: 1.2;
    }
    .gl-cs-price small { font-size: .8rem; font-weight: 700; color: #5B6577; }
    .gl-cs-price-old {
      margin: .25rem 0 0; font-size: .82rem; color: #94A3B8;
      text-decoration: line-through; font-weight: 700;
    }
    .gl-cs-save {
      display: inline-flex; margin-top: .35rem; padding: 3px 8px; border-radius: 999px;
      background: #D1FAE5; color: #047857; font-size: .66rem; font-weight: 800;
    }
    .gl-cs-note { margin: .4rem 0 0; font-size: .72rem; color: #5B6577; font-weight: 600; }
    .gl-cs-access {
      display: flex; align-items: center; gap: 6px; margin: .4rem 0 0;
      font-size: .7rem; font-weight: 800; color: #047857;
    }
    .gl-cs-access i { width: 7px; height: 7px; border-radius: 50%; background: #10B981; display: block; }
    .gl-cs-specs {
      display: grid; grid-template-columns: 1fr 1fr; gap: .4rem; margin-bottom: .55rem;
    }
    .gl-cs-spec {
      background: #F4F7FC; border: 1px solid #E2E8F0; border-radius: 8px; padding: .45rem .55rem;
    }
    .gl-cs-spec dt { margin: 0; font-size: .6rem; font-weight: 700; color: #5B6577; }
    .gl-cs-spec dd { margin: .1rem 0 0; font-size: .72rem; font-weight: 800; color: #0B1220; }
    .gl-cs-actions { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: .5rem; }
    .gl-cs-actions .sana-btn {
      flex: 1; min-width: 120px; justify-content: center; text-align: center;
      border: 0; cursor: pointer; font-family: inherit;
      padding: .55rem .85rem; font-size: .78rem; min-height: 0;
    }
    .gl-cs-actions form { flex: 1; min-width: 120px; display: flex; }
    .gl-cs-actions form .sana-btn { width: 100%; }
    .gl-cs-back {
      display: inline-flex; align-items: center; gap: 6px;
      color: #0B3D91; font-size: .76rem; font-weight: 800; text-decoration: none !important;
    }
    .gl-cs-back:hover { color: #072A66; }
    .gl-cs-trust {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: .35rem; margin-top: .55rem;
    }
    .gl-cs-trust__item {
      display: flex; flex-direction: column; align-items: center; text-align: center; gap: 4px;
      padding: .45rem .3rem; border-radius: 8px; background: #F4F7FC; border: 1px solid #E8EEF8;
    }
    .gl-cs-trust__icon {
      width: 24px; height: 24px; border-radius: 7px;
      display: grid; place-items: center; background: #E8EEF8; color: #0B3D91; font-size: .7rem;
    }
    .gl-cs-trust__item strong { display: block; font-size: .62rem; font-weight: 900; color: #0B1220; line-height: 1.3; }
    .gl-cs-trust__item span { display: none; }
    .gl-cs-sections {
      display: grid; gap: .75rem; margin-top: 1rem;
    }
    @media (min-width: 992px) {
      .gl-cs-sections { grid-template-columns: 1.2fr .8fr; gap: .85rem; }
    }
    .gl-cs-card {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 14px;
      padding: .95rem 1rem 1.05rem;
      box-shadow: 0 8px 22px -18px rgba(11,61,145,.28);
    }
    .gl-cs-card h2 {
      margin: 0 0 .65rem; font-family: Cairo,Tajawal,sans-serif;
      font-size: 1rem; font-weight: 900; color: #0B1220;
    }
    .gl-cs-card h3 {
      margin: .9rem 0 .4rem; font-size: .84rem; font-weight: 900; color: #0B1220;
    }
    .gl-cs-card p {
      margin: 0; font-size: .8rem; line-height: 1.7; color: #5B6577; font-weight: 600; white-space: pre-line;
    }
    .gl-cs-card .box {
      margin-top: .3rem; background: #F4F7FC; border-radius: 10px; padding: .7rem .8rem;
      font-size: .78rem; line-height: 1.7; color: #5B6577; font-weight: 600; white-space: pre-line;
    }
    .gl-cs-learn { list-style: none; margin: 0; padding: 0; display: grid; gap: .4rem; }
    .gl-cs-learn li {
      display: flex; gap: 8px; align-items: flex-start;
      font-size: .78rem; color: #5B6577; font-weight: 600; line-height: 1.5;
    }
    .gl-cs-learn li i { color: #0B3D91; margin-top: 2px; flex-shrink: 0; font-size: .8rem; }
    .gl-cs-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
    .gl-cs-table th, .gl-cs-table td { padding: .55rem 0; border-bottom: 1px solid #E8EEF8; text-align: start; vertical-align: top; }
    .gl-cs-table tr:last-child th, .gl-cs-table tr:last-child td { border-bottom: 0; }
    .gl-cs-table th { width: 42%; color: #5B6577; font-weight: 700; }
    .gl-cs-table td { color: #0B1220; font-weight: 800; }
    .gl-cs-table a { color: #0B3D91; text-decoration: none !important; }
    .gl-cs-related { margin-top: 1.5rem; }
    .gl-cs-related__head {
      display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
      gap: .55rem; margin-bottom: .75rem;
    }
    .gl-cs-related__head h2 {
      margin: 0; font-family: Cairo,Tajawal,sans-serif; font-size: 1.05rem; font-weight: 900; color: #0B1220;
    }
    .gl-cs-related__head p { margin: .15rem 0 0; font-size: .74rem; color: #5B6577; font-weight: 600; }
    .gl-cs-related__grid {
      display: grid; gap: .7rem; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
    .gl-cs-rel {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 14px; overflow: hidden;
      text-decoration: none !important; color: inherit;
      transition: transform .2s ease, border-color .2s ease;
    }
    .gl-cs-rel:hover { transform: translateY(-2px); border-color: rgba(11,61,145,.3); }
    .gl-cs-rel__media { aspect-ratio: 16/10; background: #E8EEF8; overflow: hidden; }
    .gl-cs-rel__media img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .gl-cs-rel__body { padding: .65rem .75rem .8rem; }
    .gl-cs-rel__body h3 { margin: 0 0 .2rem; font-size: .82rem; font-weight: 900; color: #0B1220; line-height: 1.35; }
    .gl-cs-rel__body p { margin: 0; font-size: .7rem; color: #5B6577; font-weight: 600; }
    .gl-cs-cta {
      margin-top: 1.5rem;
      border-radius: 16px; padding: 1.1rem 1.25rem;
      background:
        radial-gradient(circle at 90% 0%, rgba(245,184,0,.2), transparent 42%),
        linear-gradient(145deg, #051F4D 0%, #0B3D91 55%, #1A56B0 100%);
      color: #fff; box-shadow: 0 14px 36px -18px rgba(11,61,145,.4);
    }
    .gl-cs-cta__inner {
      display: flex; flex-wrap: wrap; gap: .85rem 1.15rem; align-items: center; justify-content: space-between;
    }
    .gl-cs-cta h2 { margin: 0 0 .25rem; font-family: Cairo,Tajawal,sans-serif; font-size: 1.1rem; font-weight: 900; }
    .gl-cs-cta p { margin: 0; color: rgba(255,255,255,.82); font-size: .78rem; line-height: 1.6; font-weight: 600; max-width: 30rem; }
    .gl-cs-cta__actions { display: flex; flex-wrap: wrap; gap: .5rem; }
    .gl-cs-cta__actions .sana-btn { padding: .65rem 1rem; font-size: .82rem; }
    .gl-cs-flash {
      display: flex; align-items: flex-start; gap: .6rem;
      margin-bottom: .75rem; padding: .7rem .85rem; border-radius: 12px;
      background: #fff; border: 1.5px solid #D7DDE6; font-size: .8rem; font-weight: 700; color: #0B1220;
    }
    .gl-cs-flash--ok { border-color: #A7F3D0; background: #ECFDF5; }
    .gl-cs-flash--err { border-color: #FECACA; background: #FEF2F2; }
    .gl-cs-flash--info { border-color: #C5D4F0; background: #E8EEF8; }
    .gl-cs-delivery {
      margin: 0 0 .85rem;
      padding: .7rem .8rem;
      border-radius: 12px;
      background: #EEF3FB;
      border: 1.5px solid #C5D4F0;
    }
    .gl-cs-delivery--solo {
      background: #FFF8E6;
      border-color: #F5D76E;
    }
    .gl-cs-delivery__title {
      display: flex; align-items: center; gap: 8px;
      margin: 0 0 .35rem;
      font-size: .78rem; font-weight: 900; color: #0B3D91;
    }
    .gl-cs-delivery--solo .gl-cs-delivery__title { color: #9A7200; }
    .gl-cs-delivery p {
      margin: 0;
      font-size: .74rem; line-height: 1.55; font-weight: 600; color: #5B6577;
    }
    .gl-cs-flash button {
      margin-inline-start: auto; border: 0; background: transparent; cursor: pointer;
      font-size: 1rem; color: #5B6577; line-height: 1;
    }
    </style>
</head>
<body class="sana-home sana-courses-page gl-cs">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'courses', 'navSolid' => true, 'navHero' => false])

<main class="sana-cat-page">
  <div class="sana-container gl-cs-wrap">

    @if (session('success'))
      <div class="gl-cs-flash gl-cs-flash--ok" data-flash>
        <span>✓</span>
        <p style="margin:0;flex:1">{{ session('success') }}</p>
        <button type="button" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
            </div>
            @endif
    @if (session('info'))
      <div class="gl-cs-flash gl-cs-flash--info" data-flash>
        <span>i</span>
        <p style="margin:0;flex:1">{{ session('info') }}</p>
        <button type="button" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
                                </div>
                                    @endif
    @if (session('error'))
      <div class="gl-cs-flash gl-cs-flash--err" data-flash>
        <span>!</span>
        <p style="margin:0;flex:1">{{ session('error') }}</p>
        <button type="button" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
                            </div>
                        @endif

    <nav class="gl-cs-crumb" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
      <a href="{{ route('home') }}">{{ __('public.home') }}</a>
      <span>/</span>
      @if ($isOneToOne)
        <a href="{{ route('public.courses') }}">{{ __('landing.nav.courses') }}</a>
                                @else
        <a href="{{ route('public.groups') }}">{{ __('landing.nav.groups') }}</a>
        <span>/</span>
        <a href="{{ $groupsListUrl }}">{{ $groupsListLabel }}</a>
                                @endif
      <span>/</span>
      <span>{{ \Illuminate\Support\Str::limit($course->title ?? '', 42) }}</span>
    </nav>

    <div class="gl-cs-layout">
      <div>
        <div class="gl-cs-media">
          @if ($introEmbedUrl)
            <iframe src="{{ $introEmbedUrl }}" title="{{ __('public.course_intro_video') }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
          @elseif ($introDirectVideo)
            <video src="{{ $introDirectVideo }}" controls playsinline preload="metadata" poster="{{ $thumbUrl }}">{{ __('public.course_intro_video_unsupported') }}</video>
          @elseif ($thumbUrl)
            <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" width="900" height="560">
                                @else
            <div class="gl-cs-media__empty">✦</div>
                                @endif
          @if ($course->is_featured ?? false)
            <span class="gl-cs-badge">{{ __('public.featured_course_badge') }}</span>
          @else
            <span class="gl-cs-badge">{{ $deliveryLabel }}</span>
                                @endif
                            </div>
                        </div>

      <aside class="gl-cs-panel">
        @if ($course->instructor)
          @if ($instructorApproved)
            <a href="{{ route('public.instructors.show', $course->instructor) }}" class="gl-cs-instructor">
              <i class="fas fa-chalkboard-user"></i> {{ $course->instructor->name }}
            </a>
          @else
            <p class="gl-cs-instructor" style="margin:0 0 .55rem"><i class="fas fa-chalkboard-user"></i> {{ $course->instructor->name }}</p>
          @endif
                        @endif

        <h1 class="gl-cs-title">{{ $course->title ?? __('public.course_title_fallback') }}</h1>

        <div class="gl-cs-tags">
          <span class="gl-cs-tag {{ $isOneToOne ? 'gl-cs-tag--gold' : '' }}">{{ $deliveryLabel }}</span>
          @if ($discountPct > 0)
            <span class="gl-cs-tag gl-cs-tag--red">{{ $isRtl ? "خصم {$discountPct}%" : "{$discountPct}% off" }}</span>
          @endif
          @if ($course->is_featured ?? false)
            <span class="gl-cs-tag gl-cs-tag--gold">{{ __('public.featured_course_badge') }}</span>
          @endif
          @if (! $isPaid)
            <span class="gl-cs-tag gl-cs-tag--green">{{ __('public.free_price') }}</span>
                        @endif
                    </div>

        <div class="gl-cs-delivery {{ $isOneToOne ? 'gl-cs-delivery--solo' : '' }}">
          <p class="gl-cs-delivery__title">
            <i class="fas {{ $isOneToOne ? 'fa-user' : 'fa-users' }}"></i>
            {{ $isOneToOne ? __('landing.groups_page.solo_label') : __('landing.groups_page.group_label') }}
          </p>
          <p>
            @if ($isOneToOne)
              {{ $isRtl
                ? 'حصة خاصة 50 دقيقة مع معلم مؤهل — اختر خطتك التعليمية والمواعيد المناسبة لطفلك.'
                : '50-minute private lessons with a qualified teacher — choose your learning plan and times that fit your child.' }}
              @if ($course->instructor)
                {{ $isRtl ? ' المعلم:' : ' Teacher:' }} <strong>{{ $course->instructor->name }}</strong>.
              @endif
              @if ($isMonthly)
                {{ $isRtl ? ' الاشتراك كخطة تعليمية شهرية.' : ' Billed as a monthly learning plan.' }}
              @endif
                                        @else
              {{ $isRtl
                ? 'تعلّم جماعي منظّم مع زملاء ومدرّس — جلسات مشتركة ومسار موحّد.'
                : 'Organised group learning with peers and a tutor — shared sessions and a clear path.' }}
              @if ($course->instructor)
                {{ $isRtl ? ' المدرّس المسؤول:' : ' Lead tutor:' }} <strong>{{ $course->instructor->name }}</strong>.
              @endif
            @endif
          </p>
        </div>

        @if ($course->description)
          <p class="gl-cs-lead">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 160) }}</p>
        @endif

        <div class="gl-cs-price-box">
          @if ($isOneToOne)
            <p class="gl-cs-note" style="margin:0 0 .55rem;font-weight:800;color:#0B3D91">{{ __('public.private_packages_label') }}</p>
            @php $selectedPlan = (int) request('plan', $isMonthly ? 1 : 0); @endphp
            <div style="display:grid;gap:8px;margin-bottom:.85rem">
              <a href="{{ request()->url() }}?plan=1" style="text-decoration:none;border:1.5px solid {{ $selectedPlan === 1 ? '#0B3D91' : '#D7DDE6' }};background:{{ $selectedPlan === 1 ? '#E8EEF8' : '#fff' }};border-radius:12px;padding:10px 12px;display:block">
                <strong style="display:block;color:#0B1220">{{ __('public.private_package_1m') }}</strong>
                <span style="font-size:.78rem;color:#5B6577">{{ __('public.private_package_1m_sub') }}</span>
              </a>
              <a href="{{ request()->url() }}?plan=3" style="text-decoration:none;border:1.5px solid {{ $selectedPlan === 3 ? '#0B3D91' : '#D7DDE6' }};background:{{ $selectedPlan === 3 ? '#E8EEF8' : '#fff' }};border-radius:12px;padding:10px 12px;display:block">
                <strong style="display:block;color:#0B1220">{{ __('public.private_package_3m') }}</strong>
                <span style="font-size:.78rem;color:#5B6577">{{ __('public.private_package_3m_sub') }}</span>
              </a>
            </div>
            <p class="gl-cs-access" style="margin-bottom:.65rem"><i class="far fa-clock"></i> {{ __('public.private_lesson_duration') }}</p>
          @endif
          @if ($isPaid)
            <p class="gl-cs-price">
              {{ number_format($checkoutPrice, 0) }}
              <small>{{ __('public.currency_egp') }}@if ($isMonthly) / {{ __('public.per_month') }}@endif</small>
            </p>
            @if ($hasPromo)
              <p class="gl-cs-price-old">{{ number_format($listPrice, 0) }} {{ __('public.currency_egp') }}</p>
              @if ($savedAmount > 0)
                <span class="gl-cs-save">{{ $isRtl ? 'وفّرت '.number_format($savedAmount, 0).' '.__('public.currency_egp') : 'Save '.number_format($savedAmount, 0).' '.__('public.currency_egp') }}</span>
              @endif
            @endif
            @if ($isMonthly && $course->isOneToOne() && $course->instructor)
              <p class="gl-cs-note">{{ __('public.one_to_one_with') }} {{ $course->instructor->name }}</p>
            @elseif ($isMonthly)
              <p class="gl-cs-note">{{ __('public.checkout_monthly_notice') }}</p>
                                        @endif
                                    @else
            <p class="gl-cs-price" style="color:#047857">{{ __('public.free_price') }}</p>
                                    @endif
          <p class="gl-cs-access"><i></i> {{ $isRtl ? 'وصول فوري بعد التفعيل' : 'Instant access after activation' }}</p>
        </div>

        <dl class="gl-cs-specs">
          <div class="gl-cs-spec">
            <dt>{{ $isRtl ? 'نوع التعلّم' : 'Delivery' }}</dt>
            <dd>{{ $deliveryLabel }}</dd>
                                </div>
          <div class="gl-cs-spec">
            <dt>{{ __('public.duration') }}</dt>
            <dd>@if($isOneToOne){{ __('public.private_lesson_duration') }}@else{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}@endif</dd>
                                        </div>
          <div class="gl-cs-spec">
            <dt>{{ __('public.lectures_count_label') }}</dt>
            <dd>{{ $course->lessons_count ?? 0 }}</dd>
                                        </div>
          <div class="gl-cs-spec">
            <dt>{{ __('public.course_category_label') }}</dt>
            <dd>{{ $categoryDisplay }}</dd>
                                        </div>
                                    </dl>

        <div class="gl-cs-actions">
                                    @auth
            @if ($isEnrolled ?? false)
              <a href="{{ route('my-courses.show', $course) }}" class="sana-btn sana-btn--yellow">{{ __('public.start_learning_now') }}</a>
            @elseif ($isPaid)
              <a href="{{ route('public.course.checkout', $course->id) }}" class="sana-btn sana-btn--yellow">{{ __('public.buy_now') }}</a>
                                        @else
                                            <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST">
                                                @csrf
                <button type="submit" class="sana-btn sana-btn--yellow">{{ __('public.register_free') }}</button>
                                            </form>
                                        @endif
                                    @endauth
                                    @guest
            @if ($isPaid)
              <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="sana-btn sana-btn--yellow">{{ __('public.buy_now') }}</a>
                                        @else
              <a href="{{ route('register', ['redirect' => route('public.course.show', $course->id)]) }}" class="sana-btn sana-btn--yellow">{{ __('public.register_free') }}</a>
                                        @endif
                                    @endguest
          <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> {{ $isRtl ? 'واتساب' : 'WhatsApp' }}</a>
        </div>

        <a href="{{ $groupsListUrl }}" class="gl-cs-back">
          <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}"></i>
          {{ $groupsListLabel }}
        </a>

        <div class="gl-cs-trust">
          <div class="gl-cs-trust__item">
            <span class="gl-cs-trust__icon"><i class="fas fa-shield-halved"></i></span>
            <div>
              <strong>{{ __('public.checkout_trust_secure') }}</strong>
              <span>{{ __('public.secure_checkout_badge') }}</span>
            </div>
          </div>
          <div class="gl-cs-trust__item">
            <span class="gl-cs-trust__icon"><i class="fas fa-bolt"></i></span>
            <div>
              <strong>{{ __('public.checkout_trust_fast') }}</strong>
              <span>{{ $isRtl ? 'يُفعَّل الوصول بعد إتمام الطلب' : 'Access unlocks after order completion' }}</span>
            </div>
          </div>
          <div class="gl-cs-trust__item">
            <span class="gl-cs-trust__icon"><i class="fas fa-certificate"></i></span>
            <div>
              <strong>{{ __('public.checkout_benefit_certificate') }}</strong>
              <span>{{ $isRtl ? 'عند إتمام متطلبات الكورس' : 'Upon completing course requirements' }}</span>
            </div>
                                </div>
                            </div>
      </aside>
    </div>

    <div class="gl-cs-sections">
      <article class="gl-cs-card">
        <h2>{{ __('public.about_course') }}</h2>
        <p>{{ $course->description ?? __('public.course_desc_fallback') }}</p>

        @if ($course->objectives)
          <h3>{{ __('public.course_objectives') }}</h3>
          <div class="box">{{ $course->objectives }}</div>
        @endif

        @if (count($learnPoints))
          <h3>{{ __('public.what_you_learn') }}</h3>
          <ul class="gl-cs-learn">
            @foreach ($learnPoints as $point)
              <li><i class="fas fa-check-circle"></i><span>{{ $point }}</span></li>
            @endforeach
          </ul>
        @endif
      </article>

      <div style="display:grid;gap:1rem">
        <article class="gl-cs-card">
          <h2>{{ $isRtl ? 'تفاصيل الكورس' : 'Course details' }}</h2>
          <table class="gl-cs-table">
            <tbody>
              <tr>
                <th>{{ $isRtl ? 'نوع التعلّم' : 'Delivery' }}</th>
                <td>{{ $deliveryLabel }}</td>
              </tr>
              <tr>
                <th>{{ __('public.course_category_label') }}</th>
                <td>{{ $categoryDisplay }}</td>
              </tr>
              <tr>
                <th>{{ __('public.subject_label') }}</th>
                <td>{{ $subjectName }}</td>
              </tr>
              <tr>
                <th>{{ __('public.duration') }}</th>
                <td>{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}</td>
              </tr>
              <tr>
                <th>{{ __('public.lectures_count_label') }}</th>
                <td>{{ $course->lessons_count ?? 0 }}</td>
              </tr>
              @if ($course->instructor)
                <tr>
                  <th>{{ __('public.instructor_label') }}</th>
                  <td>
                    @if ($instructorApproved)
                      <a href="{{ route('public.instructors.show', $course->instructor) }}">{{ $course->instructor->name }}</a>
                    @else
                      {{ $course->instructor->name }}
                    @endif
                  </td>
                </tr>
              @endif
              <tr>
                <th>{{ $isRtl ? 'نوع الاشتراك' : 'Billing' }}</th>
                <td>
                  @if (! $isPaid)
                    {{ __('public.free_price') }}
                  @elseif ($isMonthly)
                    {{ __('public.checkout_monthly_price_label') }}
                                                @else
                    {{ __('public.checkout_benefit_lifetime') }}
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </article>

        @if ($course->requirements)
          <article class="gl-cs-card">
            <h2>{{ __('public.requirements') }}</h2>
            <div class="box">{{ $course->requirements }}</div>
          </article>
                                                @endif
                                            </div>
    </div>

    @if (isset($relatedCourses) && $relatedCourses->isNotEmpty())
      <section class="gl-cs-related">
        <div class="gl-cs-related__head">
          <div>
            <p>{{ $isRtl ? 'قد يعجبك أيضاً' : 'You may also like' }}</p>
            <h2>{{ $isRtl ? 'كورسات ذات صلة' : 'Related courses' }}</h2>
          </div>
          <a href="{{ route('public.courses') }}" class="sana-link-more">{{ __('public.all_courses') }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></a>
                                                </div>
        <div class="gl-cs-related__grid">
          @foreach ($relatedCourses->take(3) as $related)
            @php
              $rThumb = $related->thumbnail_url ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80';
            @endphp
            <a href="{{ route('public.course.show', $related->id) }}" class="gl-cs-rel">
              <div class="gl-cs-rel__media"><img src="{{ $rThumb }}" alt="{{ $related->title }}" loading="lazy"></div>
              <div class="gl-cs-rel__body">
                <h3>{{ $related->title }}</h3>
                <p>{{ $related->instructor->name ?? ($isRtl ? 'معلّم على المنصة' : 'Platform tutor') }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
      </section>
                            @endif

    <section class="gl-cs-cta">
      <div class="gl-cs-cta__inner">
        <div>
          <h2>{{ $isRtl ? 'جاهز للانطلاق؟' : 'Ready to start?' }}</h2>
          <p>{{ $isRtl ? 'سجّل الآن وابدأ التعلّم بخطوات واضحة — أو احجز تقييم مستوى مجاني إن كنت غير متأكد.' : 'Enroll now and start with clear steps — or book a free level assessment if you’re unsure.' }}</p>
                        </div>
        <div class="gl-cs-cta__actions">
                    @auth
            @if ($isEnrolled ?? false)
              <a href="{{ route('my-courses.show', $course) }}" class="sana-btn sana-btn--yellow sana-btn--lg">{{ __('public.start_learning_now') }}</a>
            @elseif ($isPaid)
              <a href="{{ route('public.course.checkout', $course->id) }}" class="sana-btn sana-btn--yellow sana-btn--lg">{{ __('public.buy_now') }}</a>
            @else
              <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST">
                @csrf
                <button type="submit" class="sana-btn sana-btn--yellow sana-btn--lg">{{ __('public.register_free') }}</button>
              </form>
            @endif
          @else
            <a href="{{ route('register', ['redirect' => $isPaid ? route('public.course.checkout', $course->id) : route('public.course.show', $course->id)]) }}" class="sana-btn sana-btn--yellow sana-btn--lg">
              {{ $isPaid ? __('public.buy_now') : __('public.register_free_now') }}
                        </a>
                    @endauth
          <a href="{{ route('home') }}?open_trial=1" class="sana-btn sana-btn--wa sana-btn--lg"><i class="fas fa-clipboard-check"></i> {{ __('landing.academy.free_trial_cta') }}</a>
                </div>
            </div>
        </section>
  </div>
    </main>

@include('partials.landing.footer')
    <script>
  document.querySelectorAll('[data-flash-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var wrap = btn.closest('[data-flash]');
      if (wrap) wrap.remove();
    });
  });
    </script>
</body>
</html>
