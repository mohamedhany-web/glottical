@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $packages = $packages ?? collect();
    $tutoringGroups = $tutoringGroups ?? collect();
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.pricing_page_title') }} — {{ $brand }}</title>
  <meta name="description" content="{{ __('public.pricing_meta_description') }}">
  <link rel="canonical" href="{{ route('public.pricing') }}">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'pricing']])
  <style>
    .gl-prx-grid {
      display: grid;
      gap: 1.15rem;
      grid-template-columns: 1fr;
    }
    @media (min-width: 700px) {
      .gl-prx-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1024px) {
      .gl-prx-grid { grid-template-columns: repeat(3, 1fr); }
    }
    .gl-prx-card {
      display: flex;
      flex-direction: column;
      border-radius: 18px;
      border: 1.5px solid rgba(11, 61, 145, .12);
      background: #fff;
      box-shadow: 0 14px 36px -22px rgba(11, 61, 145, .35);
      overflow: hidden;
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .gl-prx-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 22px 44px -18px rgba(11, 61, 145, .4);
    }
    .gl-prx-card.is-popular {
      border-color: rgba(245, 184, 0, .55);
      box-shadow: 0 18px 40px -16px rgba(245, 184, 0, .35);
    }
    .gl-prx-card__body {
      padding: 1.25rem 1.2rem 1.35rem;
      display: flex;
      flex-direction: column;
      flex: 1;
      gap: .75rem;
    }
    .gl-prx-card__badge {
      align-self: flex-start;
      font-size: .68rem;
      font-weight: 800;
      padding: .28rem .65rem;
      border-radius: 999px;
      background: var(--gold, #F5B800);
      color: var(--p-deep, #051F4D);
    }
    .gl-prx-card__title {
      font-family: Cairo, Tajawal, sans-serif;
      font-weight: 900;
      font-size: 1.15rem;
      color: var(--p-deep, #051F4D);
      line-height: 1.35;
      margin: 0;
    }
    .gl-prx-card__meta {
      font-size: .78rem;
      color: rgba(5, 31, 77, .55);
      margin: 0;
    }
    .gl-prx-card__price {
      font-family: Cairo, sans-serif;
      font-weight: 900;
      font-size: 1.65rem;
      color: var(--p, #0B3D91);
      margin: 0;
      line-height: 1.2;
    }
    .gl-prx-card__price span { font-size: .9rem; font-weight: 700; color: rgba(5, 31, 77, .45); }
    .gl-prx-card__old {
      font-size: .8rem;
      color: rgba(5, 31, 77, .4);
      text-decoration: line-through;
      margin: 0;
    }
    .gl-prx-card__desc {
      font-size: .84rem;
      line-height: 1.7;
      color: rgba(5, 31, 77, .62);
      margin: 0;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .gl-prx-card__list {
      list-style: none;
      margin: 0;
      padding: 0;
      display: grid;
      gap: .4rem;
      flex: 1;
    }
    .gl-prx-card__list li {
      display: flex;
      gap: .45rem;
      align-items: flex-start;
      font-size: .8rem;
      color: rgba(5, 31, 77, .72);
    }
    .gl-prx-card__list i { color: #10b981; margin-top: .2rem; font-size: .7rem; }
    .gl-prx-empty {
      text-align: center;
      padding: 2.5rem 1rem;
      color: rgba(5, 31, 77, .5);
      font-size: .95rem;
    }
    .gl-prx-thumb {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      overflow: hidden;
      flex-shrink: 0;
      background: rgba(11, 61, 145, .08);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--p, #0B3D91);
    }
    .gl-prx-thumb img { width: 100%; height: 100%; object-fit: cover; }
  </style>
</head>
<body class="sana-home sana-courses-page sana-pricing-page">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'pricing', 'navSolid' => true, 'navHero' => false])

<main>
  <section class="sana-prx-hero">
    <div class="sana-container sana-prx-hero__inner sana-reveal">
      <nav class="sana-cat-hero__breadcrumb" aria-label="breadcrumb" style="justify-content:center;margin-bottom:1rem">
        <a href="{{ route('home') }}">{{ __('public.home') }}</a>
        <span aria-hidden="true">/</span>
        <span>{{ __('public.pricing_page_title') }}</span>
      </nav>
      <span class="sana-prx-hero__eyebrow"><i class="fas fa-tags"></i> {{ __('public.pricing_hero_kicker') }}</span>
      <h1 class="sana-prx-hero__title">
        {{ __('public.pricing_hero_title') }}
        <span class="hl">{{ __('public.pricing_hero_accent') }}</span>
      </h1>
      <p class="sana-prx-hero__sub">{{ __('public.pricing_hero_sub') }}</p>
      <p class="sana-prx-pricing-note">{{ __('public.pricing_hero_note') }}</p>
      <div class="sana-prx-hero__actions">
        <a href="#packages" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-box-open"></i> {{ __('public.pricing_packages_title') }}</a>
        <a href="#tutoring-groups" class="sana-btn sana-btn--white-outline sana-btn--lg"><i class="fas fa-users"></i> {{ __('public.pricing_groups_title') }}</a>
      </div>
    </div>
  </section>

  <section class="sana-section" id="packages">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
        <span class="sana-head__eyebrow">{{ __('public.pricing_packages_badge') }}</span>
        <h2 class="sana-head__title">{{ __('public.pricing_packages_title') }}</h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub">{{ __('public.pricing_packages_sub') }}</p>
      </div>

      @if($packages->isNotEmpty())
        <div class="gl-prx-grid">
          @foreach($packages as $package)
            @php
              $cardBody = trim((string) ($package->card_summary ?? '')) !== ''
                  ? $package->card_summary
                  : ($package->description ?? '');
              $cardFeatures = collect($package->features ?? [])->map(fn ($f) => trim((string) $f))->filter()->values();
              $isPopular = (bool) $package->is_popular;
            @endphp
            <article @class(['gl-prx-card sana-reveal', 'is-popular' => $isPopular])>
              <div class="gl-prx-card__body">
                @if($isPopular)
                  <span class="gl-prx-card__badge">{{ __('public.pricing_package_popular') }}</span>
                @endif
                <div style="display:flex;gap:.85rem;align-items:flex-start">
                  <div class="gl-prx-thumb">
                    @if($package->thumbnail)
                      <img src="{{ storage_asset($package->thumbnail) }}" alt="" loading="lazy">
                    @else
                      <i class="fas fa-graduation-cap"></i>
                    @endif
                  </div>
                  <div>
                    <h3 class="gl-prx-card__title">{{ $package->name }}</h3>
                    @if(($package->courses_count ?? 0) > 0)
                      <p class="gl-prx-card__meta">
                        <i class="fas fa-book-open"></i>
                        {{ __('public.path_courses_count', ['count' => $package->courses_count]) }}
                      </p>
                    @endif
                  </div>
                </div>

                <div>
                  @if($package->original_price && $package->original_price > $package->price)
                    <p class="gl-prx-card__old">{{ number_format($package->original_price, 0) }} {{ __('public.currency_egp') }}</p>
                  @endif
                  <p class="gl-prx-card__price">
                    @if($package->price > 0)
                      {{ number_format($package->price, 0) }} <span>{{ __('public.currency_egp') }}</span>
                    @else
                      {{ __('public.free_price') }}
                    @endif
                  </p>
                </div>

                @if($cardBody !== '')
                  <p class="gl-prx-card__desc">{{ $cardBody }}</p>
                @endif

                @if($cardFeatures->isNotEmpty())
                  <ul class="gl-prx-card__list">
                    @foreach($cardFeatures->take(5) as $feature)
                      <li><i class="fas fa-check"></i><span>{{ $feature }}</span></li>
                    @endforeach
                  </ul>
                @endif

                <a href="{{ route('public.package.show', $package->slug) }}"
                   class="sana-btn {{ $isPopular ? 'sana-btn--yellow' : 'sana-btn--purple' }}"
                   style="margin-top:auto;justify-content:center">
                  <i class="fas fa-{{ $package->price > 0 ? 'shopping-cart' : 'eye' }}"></i>
                  {{ $package->price > 0 ? __('public.pricing_package_buy') : __('public.view_details') }}
                </a>
              </div>
            </article>
          @endforeach
        </div>
      @else
        <p class="gl-prx-empty sana-reveal">{{ __('public.pricing_no_packages') }}</p>
      @endif
    </div>
  </section>

  <section class="sana-section sana-section--soft" id="tutoring-groups">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
        <span class="sana-head__eyebrow">{{ __('public.pricing_groups_badge') }}</span>
        <h2 class="sana-head__title">{{ __('public.pricing_groups_title') }}</h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub">{{ __('public.pricing_groups_sub') }}</p>
      </div>

      @if($tutoringGroups->isNotEmpty())
        <div class="gl-prx-grid">
          @foreach($tutoringGroups as $group)
            @php
              $img = $group->imageUrl();
              $isFeatured = (bool) $group->is_featured;
            @endphp
            <article @class(['gl-prx-card sana-reveal', 'is-popular' => $isFeatured])>
              <div class="gl-prx-card__body">
                @if($isFeatured)
                  <span class="gl-prx-card__badge">{{ __('public.pricing_package_popular') }}</span>
                @endif
                <div style="display:flex;gap:.85rem;align-items:flex-start">
                  <div class="gl-prx-thumb">
                    @if($img)
                      <img src="{{ $img }}" alt="" loading="lazy">
                    @else
                      <i class="fas fa-{{ $group->isIndividual() ? 'user' : 'users' }}"></i>
                    @endif
                  </div>
                  <div>
                    <h3 class="gl-prx-card__title">{{ $group->title }}</h3>
                    <p class="gl-prx-card__meta">
                      {{ $group->typeLabel() }}
                      @if($group->instructor)
                        · {{ $group->instructor->name }}
                      @endif
                    </p>
                  </div>
                </div>

                <p class="gl-prx-card__price">
                  @if($group->price !== null && (float) $group->price > 0)
                    {{ number_format((float) $group->price, 0) }}
                    <span>{{ $group->currency ?: __('public.currency_egp') }}</span>
                  @else
                    {{ __('public.pricing_groups_price_contact') }}
                  @endif
                </p>

                @if(filled($group->description))
                  <p class="gl-prx-card__desc">{{ $group->description }}</p>
                @endif

                <ul class="gl-prx-card__list">
                  @if($group->duration_minutes)
                    <li><i class="fas fa-clock"></i><span>{{ __('public.pricing_groups_duration', ['minutes' => $group->duration_minutes]) }}</span></li>
                  @endif
                  @if($group->capacity)
                    <li><i class="fas fa-user-group"></i><span>{{ __('public.pricing_groups_capacity', ['count' => $group->capacity]) }}</span></li>
                  @endif
                </ul>

                <a href="{{ route('public.groups.show', $group->slug) }}"
                   class="sana-btn {{ $isFeatured ? 'sana-btn--yellow' : 'sana-btn--purple' }}"
                   style="margin-top:auto;justify-content:center">
                  <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i>
                  {{ __('public.pricing_groups_cta') }}
                </a>
              </div>
            </article>
          @endforeach
        </div>
      @else
        <p class="gl-prx-empty sana-reveal">{{ __('public.pricing_no_groups') }}</p>
      @endif
    </div>
  </section>

  <section class="sana-section">
    <div class="sana-container">
      <div class="sana-ab-final__box sana-reveal" style="text-align:center;padding:clamp(1.5rem,3vw,2.25rem);border-radius:22px;background:linear-gradient(135deg,#0B3D91,#072A66);color:#fff">
        <h2 style="font-family:Cairo,sans-serif;font-weight:900;font-size:clamp(1.25rem,2.5vw,1.75rem);margin:0 0 .65rem">{{ __('public.pricing_footer_cta_title') }}</h2>
        <p style="opacity:.75;max-width:36rem;margin:0 auto 1.15rem;line-height:1.7">{{ __('public.pricing_footer_cta_sub') }}</p>
        <div style="display:flex;flex-wrap:wrap;gap:.65rem;justify-content:center">
          <a href="{{ route('register') }}" class="sana-btn sana-btn--yellow">{{ __('public.register_free') }}</a>
          <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--white-outline">{{ __('public.pricing_footer_contact') }}</a>
          <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        </div>
      </div>
    </div>
  </section>
</main>

@include('partials.landing.footer')
@php
    $landingJsFile = resource_path('js/landing/site.js');
    if (! is_file($landingJsFile)) {
        $landingJsFile = public_path('js/landing/site.js');
    }
    $landingJsVer = is_file($landingJsFile) ? (string) filemtime($landingJsFile) : (string) time();
@endphp
<script src="{{ route('assets.landing.js', ['file' => 'site']) }}?v={{ $landingJsVer }}" defer></script>
</body>
</html>
