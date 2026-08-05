@php
  $locale = app()->getLocale();
  $isRtl = $locale === 'ar';
  $brand = config('app.name', 'Glottical');
  $footer = \App\Services\PublicFooterSettings::payload();
  $waUrl = $footer['whatsapp_url'] ?? '#';
  $pricingRules = $pricingRules ?? collect();
  $wallets = $wallets ?? collect();
  $cheapestPerUnit = $packages->isNotEmpty() ? $packages->min(fn ($p) => $p->pricePerUnit()) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ $isRtl ? 'باقات الحصص' : 'Session packages' }} — {{ $brand }}</title>
  <meta name="description" content="{{ $isRtl ? 'باقات حصص الأكاديمية بالدولار: عدد الحصص، مدة الحصة، سعر الحصة الواحدة، ومدة الصلاحية بوضوح، مع إمكانية تخصيص باقتك.' : 'Academy session packages in USD with clear sessions, length, price per session and validity, plus a custom package builder.' }}">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="{{ route('public.service-packages.index') }}">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'pricing']])
  <style>
    /* ---------- shared ---------- */
    /* .sana-cat-page already offsets the fixed navbar; drop the pricing-page offset. */
    body.sana-pricing-page { padding-top: 0; }
    .gl-pk-empty { text-align: center; padding: 3rem 1rem; color: #5B6577; font-weight: 700; }
    .gl-pk-anchors { display: flex; flex-wrap: wrap; gap: .6rem; justify-content: center; margin-top: 1.1rem; }

    /* ---------- ready packages ---------- */
    .gl-pk-grid { display: grid; gap: 1.25rem; grid-template-columns: 1fr; }
    @media (min-width: 760px) { .gl-pk-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1080px) { .gl-pk-grid { grid-template-columns: repeat(3, 1fr); } }

    .gl-pk-card {
      position: relative; display: flex; flex-direction: column;
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 20px;
      box-shadow: 0 14px 34px -24px rgba(11,61,145,.4);
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
      overflow: hidden;
    }
    .gl-pk-card:hover { transform: translateY(-4px); box-shadow: 0 24px 46px -22px rgba(11,61,145,.45); }
    .gl-pk-card.is-featured { border-color: #F5B800; box-shadow: 0 20px 44px -20px rgba(245,184,0,.5); }
    .gl-pk-card__ribbon {
      position: absolute; inset-inline-end: -46px; top: 16px; transform: rotate(45deg);
      background: linear-gradient(180deg,#FFD24D,#F5B800); color: #0B1220;
      font-size: .68rem; font-weight: 900; padding: .3rem 3.2rem; letter-spacing: .02em;
    }
    .gl-pk-card__head { padding: 1.25rem 1.25rem .85rem; border-bottom: 1px dashed #E4E9F2; }
    .gl-pk-badge {
      display: inline-flex; align-items: center; gap: .3rem; padding: .28rem .7rem; border-radius: 999px;
      background: #EEF3FF; color: #0B3D91; font-size: .68rem; font-weight: 900;
    }
    .gl-pk-card__name { margin: .6rem 0 .25rem; font-family: Cairo, Tajawal, sans-serif; font-size: 1.25rem; font-weight: 900; color: #0B1220; }
    .gl-pk-card__desc { margin: 0; font-size: .84rem; line-height: 1.7; color: #5B6577; }

    .gl-pk-price { display: flex; align-items: flex-end; gap: .5rem; flex-wrap: wrap; margin-top: .85rem; }
    .gl-pk-price__now { font-family: Cairo, sans-serif; font-size: 2rem; font-weight: 900; color: #0B3D91; line-height: 1; direction: ltr; }
    .gl-pk-price__cur { font-size: .85rem; font-weight: 800; color: #5B6577; }
    .gl-pk-price__old { font-size: .85rem; color: #94A3B8; text-decoration: line-through; direction: ltr; }
    .gl-pk-save { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 999px; background: #ECFDF5; color: #047857; font-size: .7rem; font-weight: 900; }
    .gl-pk-unit {
      margin-top: .6rem; display: inline-flex; align-items: center; gap: .4rem;
      background: #FFF8E1; border: 1px solid #F7E3A1; color: #7A5C00;
      border-radius: 10px; padding: .4rem .65rem; font-size: .78rem; font-weight: 800;
    }

    .gl-pk-specs { list-style: none; margin: 0; padding: .85rem 1.25rem 1rem; display: grid; gap: .1rem; flex: 1; }
    .gl-pk-specs li {
      display: flex; align-items: center; justify-content: space-between; gap: .75rem;
      padding: .55rem 0; border-bottom: 1px solid #F1F4F9; font-size: .84rem;
    }
    .gl-pk-specs li:last-child { border-bottom: 0; }
    .gl-pk-specs__k { display: inline-flex; align-items: center; gap: .45rem; color: #5B6577; font-weight: 700; }
    .gl-pk-specs__k i { color: #0B3D91; font-size: .78rem; width: 1rem; text-align: center; }
    .gl-pk-specs__v { color: #0B1220; font-weight: 900; text-align: end; }
    .gl-pk-specs__v small { display: block; font-weight: 700; color: #5B6577; font-size: .72rem; }

    .gl-pk-card__foot { padding: 0 1.25rem 1.25rem; display: grid; gap: .5rem; }
    .gl-pk-note { font-size: .74rem; color: #5B6577; text-align: center; }

    .gl-pk-tablewrap { overflow-x: auto; border: 1.5px solid #D7DDE6; border-radius: 18px; background: #fff; }
    .gl-pk-table { width: 100%; border-collapse: collapse; min-width: 620px; font-size: .85rem; }
    .gl-pk-table th, .gl-pk-table td { padding: .8rem 1rem; text-align: start; border-bottom: 1px solid #EEF2F8; white-space: nowrap; }
    .gl-pk-table thead th { background: #F4F7FC; color: #0B3D91; font-weight: 900; font-size: .78rem; }
    .gl-pk-table tbody tr:last-child td { border-bottom: 0; }
    .gl-pk-table tbody tr.is-featured { background: #FFFCF2; }
    .gl-pk-table td strong { color: #0B1220; }

    /* ---------- custom builder ---------- */
    .gl-bd { display: grid; gap: 1.25rem; align-items: start; }
    @media (min-width: 1000px) { .gl-bd { grid-template-columns: minmax(0,1.35fr) minmax(320px,.65fr); } }

    .gl-bd-steps { display: grid; gap: 1rem; }
    .gl-bd-step {
      background: #fff; border: 1.5px solid #E1E7F0; border-radius: 18px;
      padding: 1.1rem 1.15rem 1.25rem; box-shadow: 0 12px 30px -26px rgba(11,61,145,.5);
    }
    .gl-bd-step__head { display: flex; align-items: center; gap: .65rem; margin-bottom: .95rem; }
    .gl-bd-step__n {
      flex-shrink: 0; width: 28px; height: 28px; border-radius: 50%;
      background: #0B3D91; color: #fff; font-size: .8rem; font-weight: 900;
      display: flex; align-items: center; justify-content: center;
    }
    .gl-bd-step__title { margin: 0; font-family: Cairo,Tajawal,sans-serif; font-size: 1rem; font-weight: 900; color: #0B1220; }
    .gl-bd-step__hint { margin: .1rem 0 0; font-size: .76rem; color: #64748B; font-weight: 600; }

    .gl-bd-tiles { display: grid; gap: .65rem; }
    @media (min-width: 620px) { .gl-bd-tiles { grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); } }
    .gl-bd-tile {
      position: relative; display: block; cursor: pointer;
      border: 1.5px solid #E1E7F0; border-radius: 14px; padding: .85rem .9rem;
      background: #FBFCFE; transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
    }
    .gl-bd-tile:hover { border-color: #B9C7E4; }
    .gl-bd-tile input { position: absolute; opacity: 0; pointer-events: none; }
    .gl-bd-tile:has(input:checked) { border-color: #0B3D91; background: #F2F6FF; box-shadow: 0 0 0 3px rgba(11,61,145,.1); }
    .gl-bd-tile__top { display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
    .gl-bd-tile__name { font-size: .92rem; font-weight: 900; color: #0B1220; }
    .gl-bd-tile__check {
      width: 18px; height: 18px; flex-shrink: 0; border-radius: 50%; border: 2px solid #C6D0E4; background: #fff;
      display: flex; align-items: center; justify-content: center;
    }
    .gl-bd-tile:has(input:checked) .gl-bd-tile__check { border-color: #0B3D91; background: #0B3D91; }
    .gl-bd-tile__check::after { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #fff; opacity: 0; }
    .gl-bd-tile:has(input:checked) .gl-bd-tile__check::after { opacity: 1; }
    .gl-bd-tile__price { margin-top: .35rem; font-size: .82rem; font-weight: 800; color: #0B3D91; direction: ltr; text-align: start; }
    .gl-bd-tile__meta { margin-top: .15rem; font-size: .74rem; color: #64748B; font-weight: 600; }

    .gl-bd-counter { display: flex; align-items: center; justify-content: center; gap: .75rem; margin-bottom: .9rem; }
    .gl-bd-counter__btn {
      width: 44px; height: 44px; border-radius: 50%; border: 1.5px solid #C6D0E4; background: #fff;
      color: #0B3D91; font-size: 1.05rem; font-weight: 900; cursor: pointer; transition: all .15s ease;
      display: flex; align-items: center; justify-content: center;
    }
    .gl-bd-counter__btn:hover:not(:disabled) { border-color: #0B3D91; background: #F2F6FF; }
    .gl-bd-counter__btn:disabled { opacity: .35; cursor: not-allowed; }
    .gl-bd-counter__value { text-align: center; min-width: 96px; }
    .gl-bd-counter__value input {
      width: 100%; height: 54px; border: 0; background: transparent; text-align: center;
      font-family: Cairo, sans-serif; font-size: 2.1rem; font-weight: 900; color: #0B1220; outline: none;
      -moz-appearance: textfield;
    }
    .gl-bd-counter__value input::-webkit-outer-spin-button,
    .gl-bd-counter__value input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .gl-bd-counter__value span { display: block; font-size: .72rem; font-weight: 800; color: #64748B; margin-top: -.35rem; }

    .gl-bd-range { width: 100%; accent-color: #0B3D91; }
    .gl-bd-range-legend { display: flex; justify-content: space-between; margin-top: .25rem; font-size: .7rem; color: #94A3B8; font-weight: 700; }
    .gl-bd-presets { display: flex; flex-wrap: wrap; gap: .45rem; margin-top: .9rem; }
    .gl-bd-preset {
      border: 1.5px solid #E1E7F0; background: #fff; color: #0B1220; cursor: pointer;
      border-radius: 999px; padding: .38rem .8rem; font-size: .78rem; font-weight: 800; transition: all .15s ease;
    }
    .gl-bd-preset:hover { border-color: #B9C7E4; }
    .gl-bd-preset.is-active { border-color: #0B3D91; background: #0B3D91; color: #fff; }
    .gl-bd-preset small { display: inline-block; margin-inline-start: .3rem; color: #047857; font-weight: 900; }
    .gl-bd-preset.is-active small { color: #FFD24D; }

    .gl-bd-upsell {
      margin-top: .9rem; display: none; align-items: center; gap: .5rem;
      background: #FFF8E1; border: 1px solid #F7E3A1; color: #7A5C00;
      border-radius: 12px; padding: .55rem .8rem; font-size: .78rem; font-weight: 800;
    }
    .gl-bd-upsell.is-visible { display: flex; }

    .gl-bd-pay { display: grid; gap: .6rem; }
    @media (min-width: 620px) { .gl-bd-pay { grid-template-columns: repeat(2, 1fr); } }
    .gl-bd-pay .gl-bd-tile__name { font-size: .86rem; }
    .gl-bd-field { margin-top: .85rem; }
    .gl-bd-field label { display: block; margin-bottom: .35rem; font-size: .76rem; font-weight: 800; color: #64748B; }
    .gl-bd-select {
      width: 100%; height: 46px; border: 1.5px solid #E1E7F0; border-radius: 12px;
      padding: 0 .85rem; background: #fff; color: #0B1220; font-size: .88rem; font-weight: 700; outline: none;
    }
    .gl-bd-select:focus { border-color: #0B3D91; box-shadow: 0 0 0 3px rgba(11,61,145,.1); }

    /* summary */
    .gl-bd-summary {
      background: #fff; border: 1.5px solid #E1E7F0; border-radius: 20px; overflow: hidden;
      box-shadow: 0 20px 44px -28px rgba(11,61,145,.55);
    }
    @media (min-width: 1000px) { .gl-bd-summary { position: sticky; top: 92px; } }
    .gl-bd-summary__head { padding: 1.1rem 1.15rem; background: linear-gradient(135deg,#0B3D91,#072A66); color: #fff; }
    .gl-bd-summary__label { font-size: .74rem; font-weight: 800; color: rgba(255,255,255,.7); }
    .gl-bd-summary__total { margin: .3rem 0 .1rem; font-family: Cairo, sans-serif; font-size: clamp(2rem,4.5vw,2.6rem); font-weight: 900; line-height: 1; direction: ltr; text-align: start; }
    .gl-bd-summary__total small { font-size: .95rem; font-weight: 800; opacity: .8; }
    .gl-bd-summary__old { min-height: 1.15rem; font-size: .8rem; color: rgba(255,255,255,.6); text-decoration: line-through; direction: ltr; text-align: start; }
    .gl-bd-summary__save {
      display: none; width: fit-content; margin-top: .4rem; border-radius: 999px;
      background: #F5B800; color: #0B1220; padding: .22rem .65rem; font-size: .72rem; font-weight: 900;
    }
    .gl-bd-summary__save.is-visible { display: inline-flex; }
    .gl-bd-summary__body { padding: 1.05rem 1.15rem 1.2rem; }
    .gl-bd-lines { list-style: none; margin: 0 0 1rem; padding: 0; display: grid; gap: .5rem; }
    .gl-bd-lines li { display: flex; justify-content: space-between; gap: .75rem; font-size: .82rem; color: #64748B; padding-bottom: .5rem; border-bottom: 1px solid #F1F4F9; }
    .gl-bd-lines li:last-child { border-bottom: 0; padding-bottom: 0; }
    .gl-bd-lines strong { color: #0B1220; font-weight: 900; text-align: end; }
    .gl-bd-lines .is-discount strong { color: #047857; }
    .gl-bd-trust { list-style: none; margin: .9rem 0 0; padding: 0; display: grid; gap: .4rem; }
    .gl-bd-trust li { display: flex; gap: .45rem; align-items: flex-start; font-size: .75rem; color: #64748B; line-height: 1.6; }
    .gl-bd-trust i { color: #047857; margin-top: .18rem; font-size: .72rem; }

    /* ---------- how it works ---------- */
    .gl-pk-steps { display: grid; gap: .9rem; grid-template-columns: 1fr; }
    @media (min-width: 800px) { .gl-pk-steps { grid-template-columns: repeat(4, 1fr); } }
    .gl-pk-step { background: #fff; border: 1.5px solid #D7DDE6; border-radius: 16px; padding: 1rem; text-align: center; }
    .gl-pk-step__n {
      width: 34px; height: 34px; margin: 0 auto .6rem; border-radius: 50%;
      background: #0B3D91; color: #fff; font-weight: 900; display: flex; align-items: center; justify-content: center; font-size: .85rem;
    }
    .gl-pk-step strong { display: block; font-size: .92rem; color: #0B1220; margin-bottom: .25rem; }
    .gl-pk-step span { font-size: .8rem; color: #5B6577; line-height: 1.65; }
  </style>
</head>
<body class="sana-home sana-courses-page sana-pricing-page">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'packages', 'navSolid' => true, 'navHero' => false])

<main class="sana-cat-page">
  <section class="sana-cat-hero" style="padding-bottom:1.25rem">
    <div class="sana-container sana-cat-hero__inner sana-reveal">
      <nav class="sana-cat-hero__breadcrumb" aria-label="breadcrumb">
        <a href="{{ route('home') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
        <span aria-hidden="true">/</span>
        <span>{{ $isRtl ? 'باقات الحصص' : 'Session packages' }}</span>
      </nav>
      <h1 class="sana-cat-hero__title" style="margin-top:.75rem">
        {{ $isRtl ? 'باقات حصص' : 'Session' }}
        <span class="hl">{{ $isRtl ? 'واضحة بالأرقام' : 'packages by the numbers' }}</span>
      </h1>
      <p class="sana-cat-hero__sub">
        {{ $isRtl
          ? 'اختر باقة جاهزة، أو فصّل باقتك بعدد الحصص الذي يناسبك. كل الأسعار بالدولار وبدون رسوم مخفية.'
          : 'Pick a ready-made pack, or build your own with the exact number of sessions. All prices in USD, no hidden fees.' }}
      </p>
      @if($cheapestPerUnit)
        <p class="sana-prx-pricing-note" style="margin-top:.5rem">
          {{ $isRtl ? 'يبدأ سعر الحصة من' : 'Sessions start from' }}
          <strong style="direction:ltr;display:inline-block">${{ number_format((float) $cheapestPerUnit, 2) }} USD</strong>
          {{ $isRtl ? 'للحصة الواحدة.' : 'per session.' }}
        </p>
      @endif
      <div class="gl-pk-anchors">
        <a href="#ready-packages" class="sana-btn sana-btn--yellow"><i class="fas fa-box-open"></i> {{ $isRtl ? 'الباقات الجاهزة' : 'Ready packages' }}</a>
        @if($pricingRules->isNotEmpty())
          <a href="#custom-package" class="sana-btn sana-btn--white-outline"><i class="fas fa-sliders"></i> {{ $isRtl ? 'خصص باقتك' : 'Build your own' }}</a>
        @endif
      </div>
    </div>
  </section>

  @if(session('success') || session('error'))
    <div class="sana-container" style="padding-top:1.25rem">
      @if(session('success'))
        <div style="background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;padding:.75rem 1rem;border-radius:12px;margin-bottom:.75rem;font-weight:700">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div style="background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;padding:.75rem 1rem;border-radius:12px;font-weight:700">{{ session('error') }}</div>
      @endif
    </div>
  @endif

  {{-- 1) الباقات الجاهزة --}}
  <section class="sana-section" id="ready-packages" style="padding-top:clamp(28px,4vw,44px)">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:26px">
        <span class="sana-head__eyebrow">{{ $isRtl ? 'الخطوة الأولى' : 'Step one' }}</span>
        <h2 class="sana-head__title">{{ $isRtl ? 'الباقات الجاهزة' : 'Ready-made packages' }}</h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub">{{ $isRtl ? 'أسرع طريقة للبدء — كل باقة موضح فيها كل رقم ومقابله.' : 'The fastest way to start — every number is spelled out.' }}</p>
      </div>

      @if($packages->isEmpty())
        <p class="gl-pk-empty">
          {{ $isRtl ? 'لا توجد باقات جاهزة حالياً — يمكنك تفصيل باقتك من الأسفل.' : 'No ready packages right now — build your own below.' }}
        </p>
      @else
        <div class="gl-pk-grid">
          @foreach($packages as $package)
            @php
              $perMonth = $package->sessionsPerMonth();
              $savings = $package->savingsPercent();
            @endphp
            <article @class(['gl-pk-card sana-reveal', 'is-featured' => $package->is_featured])>
              @if($package->is_featured)
                <span class="gl-pk-card__ribbon">{{ $isRtl ? 'الأفضل' : 'Best' }}</span>
              @endif

              <div class="gl-pk-card__head">
                @if($package->badge)
                  <span class="gl-pk-badge"><i class="fas fa-star"></i> {{ $package->badge }}</span>
                @endif
                <h3 class="gl-pk-card__name">{{ $package->name }}</h3>
                @if($package->description)
                  <p class="gl-pk-card__desc">{{ $package->description }}</p>
                @endif

                <div class="gl-pk-price">
                  <span class="gl-pk-price__now">${{ number_format((float) $package->price, 2) }}</span>
                  <span class="gl-pk-price__cur">USD</span>
                  @if($package->formattedOriginalPrice())
                    <span class="gl-pk-price__old">{{ $package->formattedOriginalPrice() }}</span>
                  @endif
                  @if($savings > 0)
                    <span class="gl-pk-save"><i class="fas fa-tag"></i> {{ $isRtl ? 'وفر' : 'Save' }} {{ $savings }}%</span>
                  @endif
                </div>

                <span class="gl-pk-unit">
                  <i class="fas fa-divide"></i>
                  {{ $isRtl ? 'أي' : 'That is' }} {{ $package->formattedPricePerUnit() }}
                  {{ $isRtl ? 'للحصة الواحدة' : 'per session' }}
                </span>
              </div>

              <ul class="gl-pk-specs">
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-layer-group"></i> {{ $isRtl ? 'عدد الحصص' : 'Sessions' }}</span>
                  <span class="gl-pk-specs__v">{{ $package->units_count }} {{ $isRtl ? 'حصة' : 'sessions' }}</span>
                </li>
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-hourglass-half"></i> {{ $isRtl ? 'مدة الحصة' : 'Session length' }}</span>
                  <span class="gl-pk-specs__v">{{ $package->sessionMinutes() }} {{ $isRtl ? 'دقيقة' : 'min' }}</span>
                </li>
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-clock"></i> {{ $isRtl ? 'إجمالي وقت التعلّم' : 'Total learning time' }}</span>
                  <span class="gl-pk-specs__v">{{ $package->totalHoursLabel() }}</span>
                </li>
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-coins"></i> {{ $isRtl ? 'سعر الحصة' : 'Price / session' }}</span>
                  <span class="gl-pk-specs__v">{{ $package->formattedPricePerUnit() }}</span>
                </li>
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-calendar-day"></i> {{ $isRtl ? 'صلاحية الرصيد' : 'Validity' }}</span>
                  <span class="gl-pk-specs__v">
                    {{ $package->validityLabel() }}
                    @if($perMonth)
                      <small>{{ $isRtl ? 'بمعدل' : 'about' }} {{ rtrim(rtrim(number_format($perMonth, 1), '0'), '.') }} {{ $isRtl ? 'حصة/شهر' : 'sessions/mo' }}</small>
                    @endif
                  </span>
                </li>
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-chalkboard-user"></i> {{ $isRtl ? 'تُستخدم في' : 'Valid for' }}</span>
                  <span class="gl-pk-specs__v">
                    {{ $package->label() }}
                    <small>{{ $package->scopeUsageHint() }}</small>
                  </span>
                </li>
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-video"></i> {{ $isRtl ? 'غرفة Live' : 'Live room' }}</span>
                  <span class="gl-pk-specs__v">{{ $isRtl ? 'مع كل حجز' : 'With every booking' }}</span>
                </li>
                <li>
                  <span class="gl-pk-specs__k"><i class="fas fa-arrows-rotate"></i> {{ $isRtl ? 'الخصم من الرصيد' : 'Credit deduction' }}</span>
                  <span class="gl-pk-specs__v">{{ $isRtl ? 'حصة واحدة بعد اكتمال الدرس' : '1 unit after the lesson' }}</span>
                </li>
              </ul>

              <div class="gl-pk-card__foot">
                <a href="{{ route('public.service-packages.checkout', $package) }}"
                   class="sana-btn {{ $package->is_featured ? 'sana-btn--yellow' : 'sana-btn--purple' }}"
                   style="justify-content:center">
                  <i class="fas fa-cart-shopping"></i>
                  {{ $isRtl ? 'اشترك الآن' : 'Subscribe now' }}
                </a>
                <p class="gl-pk-note">
                  {{ $isRtl ? 'الرصيد يُضاف بعد تأكيد الدفع من الإدارة.' : 'Credits are added once payment is approved.' }}
                </p>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  {{-- 2) جدول المقارنة --}}
  @if($packages->isNotEmpty())
    <section class="sana-section" style="padding-top:8px">
      <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:22px">
          <span class="sana-head__eyebrow">{{ $isRtl ? 'مقارنة سريعة' : 'Quick compare' }}</span>
          <h2 class="sana-head__title">{{ $isRtl ? 'كل رقم ومقابله' : 'Every number explained' }}</h2>
          <span class="sana-head__line"></span>
        </div>
        <div class="gl-pk-tablewrap sana-reveal">
          <table class="gl-pk-table">
            <thead>
              <tr>
                <th>{{ $isRtl ? 'الباقة' : 'Package' }}</th>
                <th>{{ $isRtl ? 'الحصص' : 'Sessions' }}</th>
                <th>{{ $isRtl ? 'مدة الحصة' : 'Length' }}</th>
                <th>{{ $isRtl ? 'إجمالي الساعات' : 'Total hours' }}</th>
                <th>{{ $isRtl ? 'السعر' : 'Price' }}</th>
                <th>{{ $isRtl ? 'سعر الحصة' : 'Per session' }}</th>
                <th>{{ $isRtl ? 'الصلاحية' : 'Validity' }}</th>
                <th>{{ $isRtl ? 'النطاق' : 'Scope' }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($packages as $package)
                <tr @class(['is-featured' => $package->is_featured])>
                  <td><strong>{{ $package->name }}</strong></td>
                  <td>{{ $package->units_count }}</td>
                  <td>{{ $package->sessionMinutes() }} {{ $isRtl ? 'د' : 'min' }}</td>
                  <td>{{ $package->totalHoursLabel() }}</td>
                  <td><strong>{{ $package->formattedPrice() }}</strong></td>
                  <td>{{ $package->formattedPricePerUnit() }}</td>
                  <td>{{ $package->validityLabel() }}</td>
                  <td>{{ $package->label() }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </section>
  @endif

  {{-- 3) خصص باقتك --}}
  @if($pricingRules->isNotEmpty())
    <section class="sana-section sana-section--soft" id="custom-package">
      <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:26px">
          <span class="sana-head__eyebrow">{{ $isRtl ? 'الخطوة الثانية' : 'Step two' }}</span>
          <h2 class="sana-head__title">{{ $isRtl ? 'خصص باقتك' : 'Build your own package' }}</h2>
          <span class="sana-head__line"></span>
          <p class="sana-head__sub">
            {{ $isRtl
              ? 'لم تجد ما يناسبك؟ اختر الخدمة وعدد الحصص، وسيظهر السعر النهائي فوراً قبل الطلب.'
              : 'Nothing fits? Choose the service and the number of sessions and see your final price instantly.' }}
          </p>
        </div>

        <form method="POST" action="{{ route('public.service-packages.custom.store') }}" id="custom-package-form" class="gl-bd sana-reveal">
          @csrf

          <div class="gl-bd-steps">
            <div class="gl-bd-step">
              <div class="gl-bd-step__head">
                <span class="gl-bd-step__n">1</span>
                <div>
                  <h3 class="gl-bd-step__title">{{ $isRtl ? 'اختر نوع الخدمة' : 'Choose the service' }}</h3>
                  <p class="gl-bd-step__hint">{{ $isRtl ? 'سعر الحصة يختلف حسب نوع التدريس.' : 'Session price depends on the teaching type.' }}</p>
                </div>
              </div>
              <div class="gl-bd-tiles">
                @foreach($pricingRules as $index => $rule)
                  <label class="gl-bd-tile">
                    <input type="radio" name="pricing_rule_id" value="{{ $rule->id }}"
                      @checked($index === 0)
                      data-name="{{ $rule->name }}"
                      data-price="{{ (float) $rule->price_per_session }}"
                      data-min="{{ $rule->min_sessions }}"
                      data-max="{{ $rule->max_sessions }}"
                      data-step="{{ $rule->session_step }}"
                      data-minutes="{{ $rule->session_minutes }}"
                      data-days="{{ $rule->duration_days }}"
                      data-scope="{{ $rule->scopeLabel() }}"
                      data-tiers='@json($rule->discount_tiers ?? [])'>
                    <span class="gl-bd-tile__top">
                      <span class="gl-bd-tile__name">{{ $rule->name }}</span>
                      <span class="gl-bd-tile__check" aria-hidden="true"></span>
                    </span>
                    <span class="gl-bd-tile__price">${{ number_format((float) $rule->price_per_session, 2) }} / {{ $isRtl ? 'حصة' : 'session' }}</span>
                    <span class="gl-bd-tile__meta">
                      {{ $rule->session_minutes }} {{ $isRtl ? 'دقيقة' : 'min' }} ·
                      {{ $isRtl ? 'من' : 'from' }} {{ $rule->min_sessions }} {{ $isRtl ? 'إلى' : 'to' }} {{ $rule->max_sessions }} {{ $isRtl ? 'حصة' : 'sessions' }}
                    </span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="gl-bd-step">
              <div class="gl-bd-step__head">
                <span class="gl-bd-step__n">2</span>
                <div>
                  <h3 class="gl-bd-step__title">{{ $isRtl ? 'حدد عدد الحصص' : 'Set the sessions' }}</h3>
                  <p class="gl-bd-step__hint">{{ $isRtl ? 'كلما زاد العدد، قل سعر الحصة.' : 'The more sessions, the lower the price per session.' }}</p>
                </div>
              </div>

              <div class="gl-bd-counter">
                <button type="button" class="gl-bd-counter__btn" id="builder-dec" aria-label="{{ $isRtl ? 'إنقاص' : 'Decrease' }}">−</button>
                <div class="gl-bd-counter__value">
                  <input id="builder-sessions" name="sessions" type="number" inputmode="numeric" required
                         aria-label="{{ $isRtl ? 'عدد الحصص' : 'Number of sessions' }}">
                  <span>{{ $isRtl ? 'حصة' : 'sessions' }}</span>
                </div>
                <button type="button" class="gl-bd-counter__btn" id="builder-inc" aria-label="{{ $isRtl ? 'زيادة' : 'Increase' }}">+</button>
              </div>

              <input id="builder-range" type="range" class="gl-bd-range" aria-label="{{ $isRtl ? 'عدد الحصص' : 'Number of sessions' }}">
              <div class="gl-bd-range-legend">
                <span id="builder-min"></span>
                <span id="builder-max"></span>
              </div>

              <div class="gl-bd-presets" id="builder-presets"></div>

              <div class="gl-bd-upsell" id="builder-upsell">
                <i class="fas fa-arrow-trend-up"></i>
                <span id="builder-upsell-text"></span>
              </div>
            </div>

            <div class="gl-bd-step">
              <div class="gl-bd-step__head">
                <span class="gl-bd-step__n">3</span>
                <div>
                  <h3 class="gl-bd-step__title">{{ $isRtl ? 'طريقة الدفع' : 'Payment method' }}</h3>
                  <p class="gl-bd-step__hint">{{ $isRtl ? 'يتفعل الرصيد بعد تأكيد الدفع من الإدارة.' : 'Credits activate after the payment is approved.' }}</p>
                </div>
              </div>

              <div class="gl-bd-pay">
                @php $hasWallets = $wallets->isNotEmpty(); @endphp
                @if($hasWallets)
                  <label class="gl-bd-tile">
                    <input type="radio" name="payment_method" value="bank_transfer" checked>
                    <span class="gl-bd-tile__top">
                      <span class="gl-bd-tile__name"><i class="fas fa-building-columns" style="color:#0B3D91"></i> {{ $isRtl ? 'تحويل بنكي / محفظة' : 'Bank transfer / wallet' }}</span>
                      <span class="gl-bd-tile__check" aria-hidden="true"></span>
                    </span>
                    <span class="gl-bd-tile__meta">{{ $isRtl ? 'مراجعة يدوية من الإدارة' : 'Manual review by admin' }}</span>
                  </label>
                @endif
                <label class="gl-bd-tile">
                  <input type="radio" name="payment_method" value="online" @checked(! $hasWallets)>
                  <span class="gl-bd-tile__top">
                    <span class="gl-bd-tile__name"><i class="fas fa-credit-card" style="color:#0B3D91"></i> {{ $isRtl ? 'دفع إلكتروني' : 'Online payment' }}</span>
                    <span class="gl-bd-tile__check" aria-hidden="true"></span>
                  </span>
                  <span class="gl-bd-tile__meta">{{ $isRtl ? 'بطاقة أو بوابة دفع' : 'Card or payment gateway' }}</span>
                </label>
                @if($hasWallets)
                  <label class="gl-bd-tile">
                    <input type="radio" name="payment_method" value="wallet">
                    <span class="gl-bd-tile__top">
                      <span class="gl-bd-tile__name"><i class="fas fa-wallet" style="color:#0B3D91"></i> {{ $isRtl ? 'محفظة المنصة' : 'Platform wallet' }}</span>
                      <span class="gl-bd-tile__check" aria-hidden="true"></span>
                    </span>
                    <span class="gl-bd-tile__meta">{{ $isRtl ? 'فودافون كاش / إنستاباي' : 'Vodafone Cash / InstaPay' }}</span>
                  </label>
                @endif
                <label class="gl-bd-tile">
                  <input type="radio" name="payment_method" value="cash">
                  <span class="gl-bd-tile__top">
                    <span class="gl-bd-tile__name"><i class="fas fa-money-bill-wave" style="color:#0B3D91"></i> {{ $isRtl ? 'نقدي في المقر' : 'Cash at office' }}</span>
                    <span class="gl-bd-tile__check" aria-hidden="true"></span>
                  </span>
                  <span class="gl-bd-tile__meta">{{ $isRtl ? 'الدفع عند الحضور' : 'Pay on site' }}</span>
                </label>
              </div>

              @if($hasWallets)
                <div class="gl-bd-field" id="builder-wallet-wrap">
                  <label for="builder-wallet">{{ $isRtl ? 'حساب الاستلام على المنصة' : 'Receiving account' }}</label>
                  <select id="builder-wallet" name="wallet_id" class="gl-bd-select">
                    <option value="">{{ $isRtl ? 'اختر حساب التحويل' : 'Choose receiving account' }}</option>
                    @foreach($wallets as $wallet)
                      <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                    @endforeach
                  </select>
                </div>
              @endif
            </div>
          </div>

          <aside class="gl-bd-summary">
            <div class="gl-bd-summary__head">
              <span class="gl-bd-summary__label">{{ $isRtl ? 'سعر باقتك النهائي' : 'Your final price' }}</span>
              <div class="gl-bd-summary__total">$<span id="builder-total">0.00</span> <small>USD</small></div>
              <div class="gl-bd-summary__old" id="builder-old"></div>
              <span class="gl-bd-summary__save" id="builder-discount"></span>
            </div>
            <div class="gl-bd-summary__body">
              <ul class="gl-bd-lines">
                <li><span>{{ $isRtl ? 'الخدمة' : 'Service' }}</span><strong id="builder-service">—</strong></li>
                <li><span>{{ $isRtl ? 'عدد الحصص' : 'Sessions' }}</span><strong id="builder-summary-sessions">—</strong></li>
                <li><span>{{ $isRtl ? 'مدة الحصة' : 'Session length' }}</span><strong id="builder-minutes">—</strong></li>
                <li><span>{{ $isRtl ? 'إجمالي وقت التعلّم' : 'Total learning time' }}</span><strong id="builder-hours">—</strong></li>
                <li><span>{{ $isRtl ? 'الإجمالي قبل الخصم' : 'Subtotal' }}</span><strong id="builder-subtotal">—</strong></li>
                <li class="is-discount"><span>{{ $isRtl ? 'خصم الكمية' : 'Volume discount' }}</span><strong id="builder-discount-amount">—</strong></li>
                <li><span>{{ $isRtl ? 'سعر الحصة بعد الخصم' : 'Final price / session' }}</span><strong id="builder-per-unit">—</strong></li>
                <li><span>{{ $isRtl ? 'صلاحية الرصيد' : 'Validity' }}</span><strong id="builder-days">—</strong></li>
                <li><span>{{ $isRtl ? 'نطاق الاستخدام' : 'Usage scope' }}</span><strong id="builder-scope">—</strong></li>
              </ul>

              @auth
                <button type="submit" class="sana-btn sana-btn--yellow" style="width:100%;justify-content:center">
                  <i class="fas fa-cart-shopping"></i> {{ $isRtl ? 'اطلب باقتك المخصصة' : 'Order your custom package' }}
                </button>
              @else
                <a href="{{ route('login') }}" class="sana-btn sana-btn--yellow" style="width:100%;justify-content:center">
                  <i class="fas fa-right-to-bracket"></i> {{ $isRtl ? 'سجّل الدخول لإتمام الطلب' : 'Log in to order' }}
                </a>
              @endauth

              <ul class="gl-bd-trust">
                <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'السعر بالدولار ويُعاد التحقق منه على الخادم عند الطلب.' : 'USD price is revalidated on the server at checkout.' }}</span></li>
                <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'تُخصم حصة واحدة بعد اكتمال الدرس فقط.' : 'One credit is deducted only after the lesson completes.' }}</span></li>
                <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'غرفة Live تُنشأ تلقائياً مع كل حجز.' : 'A Live room is created automatically for every booking.' }}</span></li>
              </ul>
            </div>
          </aside>
        </form>
      </div>
    </section>
  @endif

  {{-- 4) كيف تعمل الباقة --}}
  <section class="sana-section">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:22px">
        <span class="sana-head__eyebrow">{{ $isRtl ? 'كيف تعمل الباقة' : 'How it works' }}</span>
        <h2 class="sana-head__title">{{ $isRtl ? 'من الدفع حتى التجديد' : 'From payment to renewal' }}</h2>
        <span class="sana-head__line"></span>
      </div>
      <div class="gl-pk-steps sana-reveal">
        <div class="gl-pk-step">
          <div class="gl-pk-step__n">1</div>
          <strong>{{ $isRtl ? 'اختر الباقة وادفع' : 'Pick & pay' }}</strong>
          <span>{{ $isRtl ? 'باقة جاهزة أو مخصصة، تحويل بنكي أو محفظة أو دفع إلكتروني.' : 'Ready or custom pack, via transfer, wallet or online.' }}</span>
        </div>
        <div class="gl-pk-step">
          <div class="gl-pk-step__n">2</div>
          <strong>{{ $isRtl ? 'يُضاف الرصيد' : 'Credits added' }}</strong>
          <span>{{ $isRtl ? 'بعد تأكيد الدفع تظهر الحصص في حسابك.' : 'Sessions appear in your account after approval.' }}</span>
        </div>
        <div class="gl-pk-step">
          <div class="gl-pk-step__n">3</div>
          <strong>{{ $isRtl ? 'احجز وادخل Live' : 'Book & join Live' }}</strong>
          <span>{{ $isRtl ? 'اختر الموعد وتُنشأ غرفة الحصة تلقائياً.' : 'Pick a slot and the Live room is created.' }}</span>
        </div>
        <div class="gl-pk-step">
          <div class="gl-pk-step__n">4</div>
          <strong>{{ $isRtl ? 'خصم وتجديد' : 'Deduct & renew' }}</strong>
          <span>{{ $isRtl ? 'تُخصم حصة بعد اكتمال الدرس، وعند النفاد اشحن من جديد.' : 'One unit per completed lesson; recharge when empty.' }}</span>
        </div>
      </div>
    </div>
  </section>

  {{-- 5) CTA --}}
  <section class="sana-section" style="padding-top:0">
    <div class="sana-container">
      <div class="sana-ab-final__box sana-reveal" style="text-align:center;padding:clamp(1.5rem,3vw,2.25rem);border-radius:22px;background:linear-gradient(135deg,#0B3D91,#072A66);color:#fff">
        <h2 style="font-family:Cairo,sans-serif;font-weight:900;font-size:clamp(1.2rem,2.5vw,1.7rem);margin:0 0 .6rem">
          {{ $isRtl ? 'محتار في اختيار الباقة؟' : 'Not sure which pack fits?' }}
        </h2>
        <p style="opacity:.78;max-width:36rem;margin:0 auto 1.15rem;line-height:1.7">
          {{ $isRtl ? 'كلّمنا وسنحسب معك عدد الحصص المناسب لهدفك ومستواك.' : 'Talk to us and we will size the right number of sessions for your goal.' }}
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:.65rem;justify-content:center">
          @auth
            <a href="{{ route('student.service-entitlements.index') }}" class="sana-btn sana-btn--yellow"><i class="fas fa-wallet"></i> {{ $isRtl ? 'رصيدي' : 'My credits' }}</a>
          @else
            <a href="{{ route('register') }}" class="sana-btn sana-btn--yellow">{{ $isRtl ? 'سجّل مجاناً' : 'Register free' }}</a>
          @endauth
          <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--white-outline">{{ $isRtl ? 'تواصل معنا' : 'Contact us' }}</a>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('custom-package-form');
  if (!form) return;

  const rules = Array.from(form.querySelectorAll('input[name="pricing_rule_id"]'));
  const range = document.getElementById('builder-range');
  const count = document.getElementById('builder-sessions');
  const presets = document.getElementById('builder-presets');
  if (!rules.length || !range || !count) return;

  const money = value => Number(value).toFixed(2);
  const text = {
    session: @json($isRtl ? 'حصة' : 'sessions'),
    minute: @json($isRtl ? 'دقيقة' : 'min'),
    hour: @json($isRtl ? 'ساعة' : 'hours'),
    day: @json($isRtl ? 'يوم' : 'days'),
    save: @json($isRtl ? 'وفرت' : 'You save'),
    from: @json($isRtl ? 'الحد الأدنى' : 'Minimum'),
    to: @json($isRtl ? 'الحد الأقصى' : 'Maximum'),
    none: @json($isRtl ? 'لا يوجد' : 'None'),
    upsell: @json($isRtl ? 'أضف :count :unit فقط لتحصل على خصم :percent%' : 'Add just :count more :unit to unlock :percent% off')
  };

  const selectedRule = () => rules.find(rule => rule.checked) || rules[0];

  function normalize(value, rule) {
    const min = Number(rule.dataset.min);
    const max = Number(rule.dataset.max);
    const step = Number(rule.dataset.step) || 1;
    const bounded = Math.min(max, Math.max(min, Number(value) || min));
    return Math.min(max, min + Math.round((bounded - min) / step) * step);
  }

  function tiersOf(rule) {
    try {
      return JSON.parse(rule.dataset.tiers || '[]')
        .map(tier => ({ min: Number(tier.min_sessions || 0), percent: Number(tier.discount_percent || 0) }))
        .filter(tier => tier.min > 0)
        .sort((a, b) => a.min - b.min);
    } catch (_) {
      return [];
    }
  }

  function discountFor(rule, sessions) {
    return tiersOf(rule).reduce((acc, tier) => (sessions >= tier.min ? Math.max(acc, tier.percent) : acc), 0);
  }

  function buildPresets(rule, sessions) {
    if (!presets) return;
    const min = Number(rule.dataset.min);
    const max = Number(rule.dataset.max);
    const values = [min, ...tiersOf(rule).map(tier => tier.min), max]
      .map(value => normalize(value, rule))
      .filter(value => value >= min && value <= max);

    presets.innerHTML = '';
    Array.from(new Set(values)).sort((a, b) => a - b).forEach(value => {
      const discount = discountFor(rule, value);
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'gl-bd-preset' + (value === sessions ? ' is-active' : '');
      button.dataset.value = value;
      button.innerHTML = value + ' ' + text.session + (discount > 0 ? '<small>−' + discount + '%</small>' : '');
      button.addEventListener('click', () => {
        count.value = value;
        render(false);
      });
      presets.appendChild(button);
    });
  }

  function render(resetCount) {
    const rule = selectedRule();
    const min = Number(rule.dataset.min);
    const max = Number(rule.dataset.max);
    const step = Number(rule.dataset.step) || 1;

    range.min = count.min = min;
    range.max = count.max = max;
    range.step = count.step = step;

    const sessions = normalize(resetCount ? min : count.value, rule);
    range.value = sessions;
    count.value = sessions;

    const unitPrice = Number(rule.dataset.price);
    const subtotal = sessions * unitPrice;
    const discount = discountFor(rule, sessions);
    const discountAmount = subtotal * discount / 100;
    const total = subtotal - discountAmount;
    const minutes = Number(rule.dataset.minutes);
    const hours = sessions * minutes / 60;

    document.getElementById('builder-min').textContent = text.from + ': ' + min;
    document.getElementById('builder-max').textContent = text.to + ': ' + max;
    document.getElementById('builder-total').textContent = money(total);
    document.getElementById('builder-service').textContent = rule.dataset.name;
    document.getElementById('builder-summary-sessions').textContent = sessions + ' ' + text.session;
    document.getElementById('builder-minutes').textContent = minutes + ' ' + text.minute;
    document.getElementById('builder-hours').textContent = (Number.isInteger(hours) ? hours : hours.toFixed(1)) + ' ' + text.hour;
    document.getElementById('builder-subtotal').textContent = '$' + money(subtotal);
    document.getElementById('builder-discount-amount').textContent = discount > 0 ? '−$' + money(discountAmount) + ' (' + discount + '%)' : text.none;
    document.getElementById('builder-per-unit').textContent = '$' + money(total / sessions);
    document.getElementById('builder-days').textContent = rule.dataset.days + ' ' + text.day;
    document.getElementById('builder-scope').textContent = rule.dataset.scope;

    const oldPrice = document.getElementById('builder-old');
    const badge = document.getElementById('builder-discount');
    oldPrice.textContent = discount > 0 ? '$' + money(subtotal) + ' USD' : '';
    badge.classList.toggle('is-visible', discount > 0);
    badge.textContent = text.save + ' $' + money(discountAmount) + ' (' + discount + '%)';

    document.getElementById('builder-dec').disabled = sessions <= min;
    document.getElementById('builder-inc').disabled = sessions >= max;

    const nextTier = tiersOf(rule).find(tier => tier.min > sessions && tier.min <= max && tier.percent > discount);
    const upsell = document.getElementById('builder-upsell');
    if (nextTier) {
      document.getElementById('builder-upsell-text').textContent = text.upsell
        .replace(':count', nextTier.min - sessions)
        .replace(':unit', text.session)
        .replace(':percent', nextTier.percent);
      upsell.classList.add('is-visible');
    } else {
      upsell.classList.remove('is-visible');
    }

    buildPresets(rule, sessions);
  }

  function nudge(direction) {
    const rule = selectedRule();
    const step = Number(rule.dataset.step) || 1;
    count.value = normalize(Number(count.value) + direction * step, rule);
    render(false);
  }

  rules.forEach(rule => rule.addEventListener('change', () => render(true)));
  range.addEventListener('input', () => { count.value = range.value; render(false); });
  count.addEventListener('input', () => render(false));
  count.addEventListener('change', () => render(false));
  document.getElementById('builder-dec').addEventListener('click', () => nudge(-1));
  document.getElementById('builder-inc').addEventListener('click', () => nudge(1));

  const walletWrap = document.getElementById('builder-wallet-wrap');
  const wallet = document.getElementById('builder-wallet');
  function toggleWallet() {
    if (!walletWrap || !wallet) return;
    const method = form.querySelector('input[name="payment_method"]:checked');
    const needed = method && (method.value === 'bank_transfer' || method.value === 'wallet');
    walletWrap.hidden = !needed;
    wallet.required = !!needed;
    if (!needed) wallet.value = '';
  }
  form.querySelectorAll('input[name="payment_method"]').forEach(input => input.addEventListener('change', toggleWallet));

  toggleWallet();
  render(true);
});
</script>
</body>
</html>
