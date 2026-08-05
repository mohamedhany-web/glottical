@php
  $locale = app()->getLocale();
  $isRtl = $locale === 'ar';
  $brand = config('app.name', 'Glottical');
  $footer = \App\Services\PublicFooterSettings::payload();
  $waUrl = $footer['whatsapp_url'] ?? '#';
  $perMonth = $package->sessionsPerMonth();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ $package->name }} — {{ $brand }}</title>
  <meta name="robots" content="noindex">
  <meta name="theme-color" content="#0B3D91">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'pricing']])
  <style>
    /* .sana-cat-page already offsets the fixed navbar; drop the pricing-page offset. */
    body.sana-pricing-page { padding-top: 0; }
    .gl-co { padding: clamp(24px, 4vw, 40px) 0 72px; }
    .gl-co-grid { display: grid; gap: 1.25rem; }
    @media (min-width: 960px) { .gl-co-grid { grid-template-columns: 1fr .85fr; align-items: start; } }
    .gl-co-card { background: #fff; border: 1.5px solid #D7DDE6; border-radius: 20px; box-shadow: 0 14px 34px -24px rgba(11,61,145,.4); overflow: hidden; }
    .gl-co-card__head { padding: 1.15rem 1.25rem; border-bottom: 1px dashed #E4E9F2; }
    .gl-co-card__head h1, .gl-co-card__head h2 { margin: 0; font-family: Cairo, Tajawal, sans-serif; font-weight: 900; color: #0B1220; font-size: 1.2rem; }
    .gl-co-card__body { padding: 1.15rem 1.25rem 1.35rem; }

    .gl-co-specs { list-style: none; margin: 0; padding: 0; display: grid; }
    .gl-co-specs li { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .6rem 0; border-bottom: 1px solid #F1F4F9; font-size: .86rem; }
    .gl-co-specs li:last-child { border-bottom: 0; }
    .gl-co-specs__k { display: inline-flex; align-items: center; gap: .45rem; color: #5B6577; font-weight: 700; }
    .gl-co-specs__k i { color: #0B3D91; width: 1rem; text-align: center; font-size: .78rem; }
    .gl-co-specs__v { color: #0B1220; font-weight: 900; text-align: end; }
    .gl-co-specs__v small { display: block; font-size: .72rem; font-weight: 700; color: #5B6577; }

    .gl-co-total { margin-top: 1rem; padding: .9rem 1rem; border-radius: 14px; background: #F4F7FC; border: 1.5px solid #DCE5F5; display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
    .gl-co-total span { font-weight: 800; color: #5B6577; font-size: .85rem; }
    .gl-co-total strong { font-family: Cairo, sans-serif; font-size: 1.6rem; font-weight: 900; color: #0B3D91; line-height: 1; }

    .gl-co-label { display: block; margin-bottom: .35rem; font-size: .76rem; font-weight: 800; color: #5B6577; }
    .gl-co-select { width: 100%; border: 1.5px solid #D7DDE6; border-radius: 12px; padding: .7rem .85rem; font-size: .9rem; background: #fff; color: #0B1220; margin-bottom: .95rem; font-weight: 700; }
    .gl-co-hint { font-size: .76rem; color: #5B6577; line-height: 1.7; margin: 0 0 1rem; }
    .gl-co-alert { padding: .75rem 1rem; border-radius: 12px; margin-bottom: 1rem; font-size: .85rem; font-weight: 700; }
    .gl-co-alert--err { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .gl-co-steps { list-style: none; margin: 1rem 0 0; padding: 0; display: grid; gap: .5rem; }
    .gl-co-steps li { display: flex; gap: .5rem; font-size: .8rem; color: #5B6577; line-height: 1.65; }
    .gl-co-steps i { color: #0B3D91; margin-top: .25rem; }
  </style>
</head>
<body class="sana-home sana-courses-page sana-pricing-page">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'packages', 'navSolid' => true, 'navHero' => false])

<main class="sana-cat-page">
  <section class="sana-cat-hero" style="padding-bottom:.5rem">
    <div class="sana-container sana-cat-hero__inner sana-reveal">
      <nav class="sana-cat-hero__breadcrumb" aria-label="breadcrumb">
        <a href="{{ route('home') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('public.service-packages.index') }}">{{ $isRtl ? 'باقات الحصص' : 'Packages' }}</a>
        <span aria-hidden="true">/</span>
        <span>{{ $package->name }}</span>
      </nav>
      <h1 class="sana-cat-hero__title" style="margin-top:.6rem">{{ $isRtl ? 'تأكيد طلب' : 'Confirm' }} <span class="hl">{{ $package->name }}</span></h1>
      <p class="sana-cat-hero__sub">{{ $isRtl ? 'راجع تفاصيل الباقة بالأرقام قبل الدفع.' : 'Review the pack details before paying.' }}</p>
    </div>
  </section>

  <div class="sana-container gl-co">
    @if($errors->any())
      <div class="gl-co-alert gl-co-alert--err">{{ $errors->first() }}</div>
    @endif

    <div class="gl-co-grid">
      <article class="gl-co-card sana-reveal">
        <div class="gl-co-card__head">
          <h1>{{ $isRtl ? 'ماذا ستحصل عليه' : 'What you get' }}</h1>
        </div>
        <div class="gl-co-card__body">
          @if($package->description)
            <p class="gl-co-hint">{{ $package->description }}</p>
          @endif

          <ul class="gl-co-specs">
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-layer-group"></i> {{ $isRtl ? 'عدد الحصص' : 'Sessions' }}</span>
              <span class="gl-co-specs__v">{{ $package->units_count }} {{ $isRtl ? 'حصة' : 'sessions' }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-hourglass-half"></i> {{ $isRtl ? 'مدة الحصة' : 'Session length' }}</span>
              <span class="gl-co-specs__v">{{ $package->sessionMinutes() }} {{ $isRtl ? 'دقيقة' : 'min' }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-clock"></i> {{ $isRtl ? 'إجمالي وقت التعلّم' : 'Total learning time' }}</span>
              <span class="gl-co-specs__v">{{ $package->totalHoursLabel() }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-coins"></i> {{ $isRtl ? 'سعر الحصة الواحدة' : 'Price per session' }}</span>
              <span class="gl-co-specs__v">{{ $package->formattedPricePerUnit() }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-calendar-day"></i> {{ $isRtl ? 'صلاحية الرصيد' : 'Validity' }}</span>
              <span class="gl-co-specs__v">
                {{ $package->validityLabel() }}
                @if($perMonth)
                  <small>{{ $isRtl ? 'بمعدل' : 'about' }} {{ rtrim(rtrim(number_format($perMonth, 1), '0'), '.') }} {{ $isRtl ? 'حصة/شهر' : 'sessions/mo' }}</small>
                @endif
              </span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-chalkboard-user"></i> {{ $isRtl ? 'تُستخدم في' : 'Valid for' }}</span>
              <span class="gl-co-specs__v">
                {{ $package->label() }}
                <small>{{ $package->scopeUsageHint() }}</small>
              </span>
            </li>
            @if($package->formattedOriginalPrice())
              <li>
                <span class="gl-co-specs__k"><i class="fas fa-tag"></i> {{ $isRtl ? 'السعر قبل الخصم' : 'Before discount' }}</span>
                <span class="gl-co-specs__v" style="color:#94A3B8;text-decoration:line-through">{{ $package->formattedOriginalPrice() }}</span>
              </li>
              <li>
                <span class="gl-co-specs__k"><i class="fas fa-piggy-bank"></i> {{ $isRtl ? 'التوفير' : 'You save' }}</span>
                <span class="gl-co-specs__v" style="color:#047857">
                  ${{ number_format($package->savingsAmount(), 2) }} USD ({{ $package->savingsPercent() }}%)
                </span>
              </li>
            @endif
          </ul>

          <div class="gl-co-total">
            <span>{{ $isRtl ? 'الإجمالي المطلوب' : 'Total due' }}</span>
            <strong>{{ $package->formattedPrice() }}</strong>
          </div>

          <ul class="gl-co-steps">
            <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'بعد تأكيد الدفع من الإدارة يُضاف الرصيد لحسابك مباشرة.' : 'Credits are added right after the admin approves your payment.' }}</span></li>
            <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'تُخصم حصة واحدة فقط عند اكتمال الدرس، وليس عند الحجز.' : 'One unit is deducted when the lesson completes, not at booking.' }}</span></li>
            <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'يمكنك متابعة رصيدك من صفحة «رصيد الحصص».' : 'Track your balance from the credits page.' }}</span></li>
          </ul>
        </div>
      </article>

      <article class="gl-co-card sana-reveal">
        <div class="gl-co-card__head">
          <h2>{{ $isRtl ? 'بيانات الدفع' : 'Payment details' }}</h2>
        </div>
        <div class="gl-co-card__body">
          <form method="POST" action="{{ route('public.service-packages.store', $package) }}">
            @csrf
            <label class="gl-co-label" for="payment_method">{{ $isRtl ? 'طريقة الدفع' : 'Payment method' }}</label>
            <select id="payment_method" name="payment_method" class="gl-co-select" required>
              @if($wallets->isNotEmpty())
                <option value="bank_transfer">{{ $isRtl ? 'تحويل بنكي / محفظة (مراجعة يدوية)' : 'Bank transfer / wallet (manual review)' }}</option>
                <option value="wallet">{{ $isRtl ? 'محفظة المنصة' : 'Platform wallet' }}</option>
              @endif
              <option value="online" @selected($wallets->isEmpty())>{{ $isRtl ? 'دفع إلكتروني' : 'Online payment' }}</option>
              <option value="cash">{{ $isRtl ? 'نقدي في المقر' : 'Cash at the office' }}</option>
            </select>

            @if($wallets->isNotEmpty())
              <label class="gl-co-label" for="wallet_id">{{ $isRtl ? 'حساب الاستلام (مطلوب للتحويل/المحفظة)' : 'Receiving account (required for transfer/wallet)' }}</label>
              <select id="wallet_id" name="wallet_id" class="gl-co-select">
                <option value="">—</option>
                @foreach($wallets as $w)
                  <option value="{{ $w->id }}">{{ $w->name }}</option>
                @endforeach
              </select>
            @endif

            <p class="gl-co-hint">
              {{ $isRtl
                ? 'سيتم إنشاء طلب بحالة «بانتظار المراجعة»، وبعد تأكيد الدفع يُفعّل رصيد الحصص تلقائياً.'
                : 'An order will be created as pending; credits activate automatically once payment is approved.' }}
            </p>

            <button type="submit" class="sana-btn sana-btn--yellow" style="width:100%;justify-content:center">
              <i class="fas fa-circle-check"></i>
              {{ $isRtl ? 'تأكيد الطلب' : 'Confirm order' }} · {{ $package->formattedPrice() }}
            </button>
          </form>

          <div style="margin-top:1rem;display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('public.service-packages.index') }}" class="sana-btn sana-btn--white-outline" style="flex:1;justify-content:center;min-width:9rem">
              {{ $isRtl ? 'باقة أخرى' : 'Other packages' }}
            </a>
            <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa" style="flex:1;justify-content:center;min-width:9rem" target="_blank" rel="noopener">
              <i class="fab fa-whatsapp"></i> {{ $isRtl ? 'استفسار' : 'Ask us' }}
            </a>
          </div>
        </div>
      </article>
    </div>
  </div>
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
