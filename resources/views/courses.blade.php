@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $activeDelivery = $delivery ?? null;
    $activeCategoryId = (int) ($categoryId ?? 0);
    $searchQuery = $searchQuery ?? '';
    $courses = $courseModels ?? collect();
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
      margin:0 0 1rem;padding-bottom:1rem;border-bottom:1px solid #E8EEF8;
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
    .gl-filter-block{display:grid;gap:.55rem}
    .gl-filter-block + .gl-filter-block{margin-top:.85rem;padding-top:.85rem;border-top:1px solid #E8EEF8}
    .gl-filter-block__label{
      margin:0;font:800 .72rem/1 Tajawal,sans-serif;color:#5B6577;
      letter-spacing:.02em;text-transform:none;
    }
    .gl-filter-block__chips{
      display:flex;flex-wrap:wrap;align-items:center;gap:.45rem .5rem;
    }
    .gl-chip{
      display:inline-flex;align-items:center;justify-content:center;
      min-height:2.35rem;padding:.4rem .95rem;border-radius:999px;
      border:1.5px solid #D7DDE6;background:#fff;
      font:700 .8rem/1.2 Tajawal,sans-serif;text-decoration:none!important;color:#0B1220;
      transition:background .15s ease,border-color .15s ease,color .15s ease;
    }
    .gl-chip:hover{border-color:rgba(11,61,145,.35);color:#0B3D91}
    .gl-chip.is-on{background:#0B3D91;border-color:#0B3D91;color:#fff}
    .gl-chip.is-on:hover{color:#fff;background:#072A66;border-color:#072A66}
    .gl-grid{display:grid;gap:1.1rem;grid-template-columns:repeat(auto-fill,minmax(230px,1fr))}
    .gl-grid .card-lift,.gl-grid article{background:#fff}
    @media (max-width:520px){
      .gl-search{grid-template-columns:1fr}
      .gl-search button{width:100%}
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
      <h1 class="sana-cat-hero__title">{{ __('public.courses_page_title') }}</h1>
      <p class="sana-cat-hero__desc">{{ __('public.courses_subtitle') }}</p>
      <p class="sana-cat-hero__stats"><span class="sana-cat-hero__stat"><i class="fas fa-book-open"></i> {{ number_format($courses->count()) }} {{ $isRtl ? 'كورساً' : 'courses' }}</span></p>
    </div>
  </section>

  <section class="sana-container" style="padding-bottom:4rem">
    <div class="gl-catalog-panel sana-reveal">
      <form action="{{ route('public.courses') }}" method="get" class="gl-search" role="search">
        @if($activeDelivery)<input type="hidden" name="delivery" value="{{ $activeDelivery }}">@endif
        @if($activeCategoryId > 0)<input type="hidden" name="category" value="{{ $activeCategoryId }}">@endif
        <input type="search" name="q" value="{{ $searchQuery }}" placeholder="{{ __('public.search_course_placeholder') ?? ($isRtl ? 'ابحث عن كورس…' : 'Search…') }}" aria-label="{{ $isRtl ? 'بحث الكورسات' : 'Search courses' }}">
        <button type="submit">{{ $isRtl ? 'بحث' : 'Search' }}</button>
      </form>

      <div class="gl-filter-block">
        <p class="gl-filter-block__label">{{ $isRtl ? 'نوع التعلّم' : 'Delivery type' }}</p>
        <div class="gl-filter-block__chips" role="group" aria-label="{{ $isRtl ? 'نوع التعلّم' : 'Delivery type' }}">
          <a href="{{ route('public.courses', array_filter(['q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null])) }}" class="gl-chip {{ ! $activeDelivery ? 'is-on' : '' }}">{{ __('public.courses_filter_all') ?? ($isRtl ? 'الكل' : 'All') }}</a>
          <a href="{{ route('public.courses', array_filter(['delivery' => 'group', 'q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null])) }}" class="gl-chip {{ $activeDelivery === 'group' ? 'is-on' : '' }}">{{ __('public.courses_filter_group') ?? ($isRtl ? 'جماعي' : 'Group') }}</a>
          <a href="{{ route('public.courses', array_filter(['delivery' => 'one_to_one', 'q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null])) }}" class="gl-chip {{ $activeDelivery === 'one_to_one' ? 'is-on' : '' }}">{{ __('public.courses_filter_one_to_one') ?? ($isRtl ? 'فردي' : '1:1') }}</a>
        </div>
      </div>

      @if(($courseFilterCategories ?? collect())->isNotEmpty())
        <div class="gl-filter-block">
          <p class="gl-filter-block__label">{{ $isRtl ? 'التصنيف' : 'Category' }}</p>
          <div class="gl-filter-block__chips" role="group" aria-label="{{ $isRtl ? 'التصنيف' : 'Category' }}">
            <a href="{{ route('public.courses', array_filter(['delivery' => $activeDelivery, 'q' => $searchQuery ?: null])) }}" class="gl-chip {{ $activeCategoryId === 0 ? 'is-on' : '' }}">{{ $isRtl ? 'كل التصنيفات' : 'All categories' }}</a>
            @foreach($courseFilterCategories as $cat)
              <a href="{{ route('public.courses', array_filter(['category' => $cat->id, 'delivery' => $activeDelivery, 'q' => $searchQuery ?: null])) }}" class="gl-chip {{ $activeCategoryId === (int) $cat->id ? 'is-on' : '' }}">{{ $cat->name }}</a>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="gl-grid">
      @forelse($courses as $course)
        @php
          $thumb = $course->thumbnail_url ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80';
          $url = route('public.course.show', $course->id);
          $price = $course->is_free ? (__('landing.free') ?? 'مجاني') : (number_format((float) ($course->price_after_discount ?? $course->price ?? 0), 0).' '.__('landing.currency'));
        @endphp
        <a href="{{ $url }}" class="sana-reveal" style="text-decoration:none;color:inherit;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 14px 40px -18px rgba(11,61,145,.28);display:flex;flex-direction:column">
          <div style="aspect-ratio:4/5;background:#e8eef8;overflow:hidden"><img src="{{ $thumb }}" alt="{{ $course->title }}" style="width:100%;height:100%;object-fit:cover" loading="lazy"></div>
          <div style="padding:14px 16px 18px;display:flex;flex-direction:column;gap:6px;flex:1">
            <p style="margin:0;font-size:.75rem;color:#64748b">{{ $course->instructor->name ?? '' }}</p>
            <h3 style="margin:0;font:800 .92rem/1.45 Cairo,Tajawal,sans-serif;color:#0B1220">{{ $course->title }}</h3>
            <p style="margin:auto 0 0;font:800 .95rem Tajawal,sans-serif;color:var(--p)">{{ $price }}</p>
          </div>
        </a>
      @empty
        <p style="grid-column:1/-1;color:var(--muted);font-weight:700">{{ $isRtl ? 'لا توجد كورسات مطابقة.' : 'No matching courses.' }}</p>
      @endforelse
    </div>

    <div class="sana-reveal" style="margin-top:2.5rem;text-align:center">
      <a href="{{ url('/?open_trial=1') }}" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-clipboard-check"></i> {{ __('landing.academy.free_trial_cta') }}</a>
    </div>
  </section>
</main>

@include('partials.landing.footer')
</body>
</html>
