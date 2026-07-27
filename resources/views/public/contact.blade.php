@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $supportEmail = trim((string) ($supportEmail ?? ($footer['email'] ?? '')));
    $supportPhone = trim((string) ($supportPhone ?? ($footer['phone'] ?? '')));
    $socials = $footer['socials'] ?? [];
    $hasSocials = is_array($socials) && count($socials) > 0;
    $phoneHref = $supportPhone !== ''
        ? 'tel:'.preg_replace('/[^\d+]/', '', $supportPhone)
        : null;
    $defaultSubject = $isRtl ? 'طلب تقييم مستوى مجاني' : 'Free level assessment request';
    $categories = [
        ['icon' => 'fas fa-clipboard-check', 'label' => $isRtl ? 'تقييم مستوى مجاني' : 'Free assessment', 'subject' => $defaultSubject],
        ['icon' => 'fas fa-screwdriver-wrench', 'label' => $isRtl ? 'دعم فني' : 'Technical support', 'subject' => $isRtl ? 'دعم فني' : 'Technical support'],
        ['icon' => 'fas fa-book-open', 'label' => $isRtl ? 'أسئلة الدورات' : 'Course questions', 'subject' => $isRtl ? 'استفسار عن الدورات' : 'Course inquiry'],
        ['icon' => 'fas fa-chalkboard-user', 'label' => $isRtl ? 'طلبات المعلّمين' : 'Tutor requests', 'subject' => $isRtl ? 'طلب انضمام كمعلّم' : 'Tutor application'],
        ['icon' => 'fas fa-handshake', 'label' => $isRtl ? 'شراكات' : 'Partnerships', 'subject' => $isRtl ? 'شراكة / تعاون' : 'Partnership'],
        ['icon' => 'fas fa-credit-card', 'label' => $isRtl ? 'الفوترة والدفع' : 'Billing', 'subject' => $isRtl ? 'الفوترة والدفع' : 'Billing & payment'],
        ['icon' => 'fas fa-comments', 'label' => $isRtl ? 'استفسار عام' : 'General', 'subject' => $isRtl ? 'استفسار عام' : 'General inquiry'],
    ];
    $oldSubject = old('subject', $defaultSubject);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __('public.contact_page_title') }} — {{ $brand }}</title>
  <meta name="description" content="{{ $isRtl ? 'تواصل مع '.$brand.' — واتساب، بريد، أو نموذج رسالة. تقييم مستوى مجاني ودعم للعائلات والمعلّمين.' : 'Contact '.$brand.' — WhatsApp, email, or message form. Free assessment and support for families and tutors.' }}">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="{{ route('public.contact') }}">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'contact']])
  <style>
    .gl-ct-page .sana-ct-hero {
      padding: clamp(36px, 6vw, 60px) 0 clamp(40px, 6.5vw, 68px);
    }
    .gl-ct-page .sana-ct-hero__title {
      font-size: clamp(1.55rem, 3.6vw, 2.25rem);
      margin-bottom: 10px;
    }
    .gl-ct-page .sana-ct-hero__sub {
      font-size: .88rem;
      margin-bottom: 1.05rem;
    }
    .gl-ct-page .sana-ct-hero__actions .sana-btn,
    .gl-ct-page .sana-ct-final__actions .sana-btn {
      padding: .7rem 1.15rem;
      font-size: .84rem;
    }
    .gl-ct-page .sana-ct-hero__trust { margin-top: 1.05rem; gap: 8px; }
    .gl-ct-page .sana-ct-hero__trust span { padding: 6px 11px; font-size: .68rem; }
    .gl-ct-page .sana-section { padding: clamp(32px, 5vw, 52px) 0; }
    .gl-ct-page .sana-head { margin-bottom: 1.35rem !important; }
    .gl-ct-page .sana-head__title { font-size: clamp(1.2rem, 2.5vw, 1.55rem); }
    .gl-ct-page .sana-head__sub { font-size: .84rem; }
    .gl-ct-page .sana-ct-channel { padding: 1.05rem .95rem; border-radius: 16px; }
    .gl-ct-page .sana-ct-channel__icon { width: 42px; height: 42px; border-radius: 12px; margin-bottom: .65rem; font-size: 1rem; }
    .gl-ct-page .sana-ct-channel strong { font-size: .86rem; }
    .gl-ct-page .sana-ct-channel p { font-size: .74rem; }
    .gl-ct-page .sana-ct-channel__btn { padding: 8px 12px; font-size: .72rem; }
    .gl-ct-page .sana-ct-form-wrap { gap: 1.35rem; }
    @media (min-width: 992px) {
      .gl-ct-page .sana-ct-form-wrap { gap: 1.65rem; }
    }
    .gl-ct-page .sana-ct-cat { padding: .75rem .65rem; border-radius: 12px; }
    .gl-ct-page .sana-ct-cat i { font-size: .95rem; margin-bottom: 6px; }
    .gl-ct-page .sana-ct-cat span { font-size: .7rem; }
    .gl-ct-page .sana-ct-form-card {
      padding: 1.15rem 1.15rem 1.25rem;
      border-radius: 18px;
    }
    .gl-ct-page .sana-ct-field { margin-bottom: .85rem; }
    .gl-ct-page .sana-ct-field input,
    .gl-ct-page .sana-ct-field textarea {
      padding: 18px 14px 8px;
      border-radius: 12px;
      font-size: .86rem;
    }
    .gl-ct-page .sana-ct-field textarea { min-height: 120px; padding-top: 22px; }
    .gl-ct-page .sana-ct-field label { top: 14px; inset-inline-end: 14px; font-size: .8rem; }
    .gl-ct-page .sana-ct-field input:focus + label,
    .gl-ct-page .sana-ct-field input:not(:placeholder-shown) + label,
    .gl-ct-page .sana-ct-field textarea:focus + label,
    .gl-ct-page .sana-ct-field textarea:not(:placeholder-shown) + label {
      top: 6px; font-size: .64rem;
    }
    .gl-ct-page .sana-ct-submit {
      padding: .75rem 1.15rem;
      font-size: .86rem;
      min-height: 0;
      margin-top: .25rem;
    }
    .gl-ct-page .sana-ct-response__card { padding: 1.05rem .75rem; border-radius: 14px; }
    .gl-ct-page .sana-ct-response__card em { font-size: .95rem; }
    .gl-ct-page .sana-ct-final { padding: clamp(40px, 6vw, 64px) 0; }
    .gl-ct-page .sana-ct-final__box {
      padding: clamp(1.25rem, 3vw, 1.75rem);
      border-radius: 18px;
    }
    .gl-ct-page .sana-ct-final__box h2 { font-size: clamp(1.15rem, 2.4vw, 1.45rem); }
    .gl-ct-page .sana-ct-final__box p { font-size: .84rem; margin-bottom: 1rem; }
    .gl-ct-page .sana-ct-scene { max-width: 300px; min-height: 240px; }
  </style>
</head>
<body class="sana-home sana-courses-page gl-ct-page">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'contact', 'navHero' => true])

<main class="sana-contact-page">

  <section class="sana-ct-hero">
    <div class="sana-container">
      <div class="sana-ct-hero__grid sana-reveal">
        <div class="sana-ct-hero__content">
          <span class="sana-ct-hero__eyebrow"><i class="fas fa-headset"></i> {{ $isRtl ? 'مركز الدعم' : 'Support center' }}</span>
          <h1 class="sana-ct-hero__title">
            {{ $isRtl ? 'نحن هنا' : 'We’re here' }}
            <span class="hl">{{ $isRtl ? 'لمساعدتك' : 'to help' }}</span>
          </h1>
          <p class="sana-ct-hero__sub">
            {{ $isRtl
              ? 'سواء كنت طالباً، وليّ أمر، أو معلّماً — فريق '.$brand.' جاهز للإجابة عبر واتساب أو النموذج.'
              : 'Whether you’re a student, parent, or tutor — the '.$brand.' team is ready via WhatsApp or the form.' }}
          </p>
          <div class="sana-ct-hero__actions">
            <a href="#contact-form" class="sana-btn sana-btn--yellow"><i class="fas fa-paper-plane"></i> {{ $isRtl ? 'أرسل رسالة' : 'Send a message' }}</a>
            <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> {{ $isRtl ? 'واتساب' : 'WhatsApp' }}</a>
          </div>
          <div class="sana-ct-hero__trust">
            <span><i class="fas fa-bolt"></i> {{ $isRtl ? 'رد خلال 24 ساعة' : 'Reply within 24h' }}</span>
            <span><i class="fas fa-shield-halved"></i> {{ $isRtl ? 'بياناتك محمية' : 'Your data is protected' }}</span>
            <span><i class="fas fa-clipboard-check"></i> {{ $isRtl ? 'تقييم مستوى مجاني' : 'Free assessment' }}</span>
          </div>
        </div>
        <div class="sana-ct-hero__visual">
          <div class="sana-ct-scene" aria-hidden="true">
            <div class="sana-ct-scene__deco">
              <span class="sana-ct-scene__ring"></span>
              <span class="sana-ct-scene__blob sana-ct-scene__blob--1"></span>
              <span class="sana-ct-scene__blob sana-ct-scene__blob--2"></span>
              <span class="sana-ct-scene__spark">✦</span>
            </div>
            <svg class="sana-ct-scene__main" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ctGrad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#1A56B0"/><stop offset="100%" stop-color="#0B3D91"/></linearGradient>
                <linearGradient id="ctGold" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#FFD24D"/><stop offset="100%" stop-color="#F5B800"/></linearGradient>
              </defs>
              <g transform="translate(180 155)">
                <circle cx="0" cy="0" r="72" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"/>
                <path d="M-36 -8 C-36 -32 36 -32 36 -8 V16 C36 28 24 36 12 36 H-12 C-24 36 -36 28 -36 16 Z" fill="url(#ctGrad)"/>
                <rect x="-44" y="-4" width="16" height="28" rx="8" fill="rgba(255,255,255,0.9)"/>
                <rect x="28" y="-4" width="16" height="28" rx="8" fill="rgba(255,255,255,0.9)"/>
              </g>
              <g transform="translate(68 108)">
                <rect x="0" y="0" width="72" height="44" rx="14" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.25)"/>
              </g>
              <g transform="translate(248 210) rotate(8)">
                <rect x="0" y="0" width="64" height="44" rx="10" fill="url(#ctGold)" opacity="0.95"/>
              </g>
            </svg>
            <div class="sana-ct-scene__chip"><i class="fas fa-clock"></i> {{ $isRtl ? '9 ص – 9 م' : '9 AM – 9 PM' }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <span class="sana-head__eyebrow">{{ $brand }}</span>
        <h2 class="sana-head__title">{{ $isRtl ? 'اختر' : 'Choose' }} <span class="hl">{{ $isRtl ? 'طريقة التواصل' : 'how to reach us' }}</span></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub">{{ $isRtl ? 'قنوات متعددة — اختر الأنسب لك.' : 'Multiple channels — pick what works for you.' }}</p>
      </div>

      @if ($supportEmail !== '')
        <p class="sana-ct-official-email sana-reveal" role="note">
          <i class="fas fa-envelope-circle-check"></i>
          <span>{{ $isRtl ? 'البريد الرسمي:' : 'Official email:' }} <strong dir="ltr">{{ $supportEmail }}</strong></span>
        </p>
      @elseif ($supportPhone === '')
        <p class="sana-ct-channels-empty sana-reveal">{{ __('public.contact_channels_empty_hint') }}</p>
      @endif

      <div class="sana-ct-channels" id="social-links">
        <a href="{{ $waUrl }}" class="sana-ct-channel sana-reveal" target="_blank" rel="noopener">
          <span class="sana-ct-channel__icon sana-ct-channel__icon--wa"><i class="fab fa-whatsapp"></i></span>
          <strong>{{ $isRtl ? 'واتساب' : 'WhatsApp' }}</strong>
          <p>{{ $isRtl ? 'رد سريع عبر المحادثة — الأفضل لأولياء الأمور.' : 'Quick chat replies — great for parents.' }}</p>
          <span class="sana-ct-channel__btn">{{ $isRtl ? 'افتح واتساب' : 'Open WhatsApp' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></span>
        </a>

        @if ($supportEmail !== '')
          <a href="mailto:{{ $supportEmail }}" class="sana-ct-channel sana-reveal">
            <span class="sana-ct-channel__icon sana-ct-channel__icon--email"><i class="fas fa-envelope"></i></span>
            <strong>{{ $isRtl ? 'البريد الإلكتروني' : 'Email' }}</strong>
            <p>{{ $isRtl ? 'أرسل تفاصيل طلبك — نرد عادة خلال 24 ساعة.' : 'Send details — we usually reply within 24 hours.' }}</p>
            <span class="sana-ct-channel__info" dir="ltr">{{ $supportEmail }}</span>
            <span class="sana-ct-channel__btn">{{ $isRtl ? 'أرسل بريداً' : 'Send email' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></span>
          </a>
        @endif

        @if ($phoneHref)
          <a href="{{ $phoneHref }}" class="sana-ct-channel sana-reveal" dir="ltr">
            <span class="sana-ct-channel__icon sana-ct-channel__icon--phone"><i class="fas fa-phone"></i></span>
            <strong>{{ $isRtl ? 'الهاتف' : 'Phone' }}</strong>
            <p>{{ $isRtl ? 'تواصل هاتفي في أوقات العمل.' : 'Call us during support hours.' }}</p>
            <span class="sana-ct-channel__info">{{ $supportPhone }}</span>
            <span class="sana-ct-channel__btn">{{ $isRtl ? 'اتصل الآن' : 'Call now' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></span>
          </a>
        @endif

        <a href="#contact-form" class="sana-ct-channel sana-reveal">
          <span class="sana-ct-channel__icon sana-ct-channel__icon--chat"><i class="fas fa-comments"></i></span>
          <strong>{{ $isRtl ? 'نموذج الرسالة' : 'Message form' }}</strong>
          <p>{{ $isRtl ? 'اكتب استفسارك وسنوجّهه للفريق المناسب.' : 'Write your question and we’ll route it to the right team.' }}</p>
          <span class="sana-ct-channel__btn">{{ $isRtl ? 'املأ النموذج' : 'Fill the form' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></span>
        </a>

        @if ($hasSocials)
          <div class="sana-ct-channel sana-reveal">
            <span class="sana-ct-channel__icon sana-ct-channel__icon--social"><i class="fas fa-share-nodes"></i></span>
            <strong>{{ $isRtl ? 'وسائل التواصل' : 'Social media' }}</strong>
            <p>{{ $isRtl ? 'تابعنا وتواصل معنا على منصاتنا.' : 'Follow and reach us on our social channels.' }}</p>
            <div class="sana-ct-socials">
              @foreach ($socials as $social)
                <a href="{{ $social['url'] }}" target="_blank" rel="noopener" aria-label="{{ $social['label'] }}"><i class="{{ $social['icon'] }}"></i></a>
              @endforeach
            </div>
          </div>
        @endif

        <a href="{{ route('public.faq') }}" class="sana-ct-channel sana-reveal">
          <span class="sana-ct-channel__icon sana-ct-channel__icon--help"><i class="fas fa-circle-question"></i></span>
          <strong>{{ $isRtl ? 'الأسئلة الشائعة' : 'FAQ' }}</strong>
          <p>{{ $isRtl ? 'قد تجد إجابتك فوراً دون انتظار.' : 'You may find your answer instantly.' }}</p>
          <span class="sana-ct-channel__btn">{{ $isRtl ? 'تصفّح الأسئلة' : 'Browse FAQ' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></span>
        </a>
      </div>
    </div>
  </section>

  <section class="sana-section sana-section--soft" id="contact-form">
    <div class="sana-container">
      <div class="sana-ct-form-wrap">
        <div class="sana-reveal">
          <span class="sana-head__eyebrow">{{ $brand }}</span>
          <h2 class="sana-head__title" style="text-align:{{ $isRtl ? 'right' : 'left' }};margin-bottom:8px">
            {{ $isRtl ? 'ما' : 'What' }} <span class="hl">{{ $isRtl ? 'نوع استفسارك؟' : 'is your inquiry?' }}</span>
          </h2>
          <p class="sana-head__sub" style="margin:0 0 16px;text-align:{{ $isRtl ? 'right' : 'left' }};max-width:none">
            {{ $isRtl ? 'اختر التصنيف الأقرب — لنوجّه رسالتك للفريق المناسب.' : 'Pick the closest category — we’ll route your message correctly.' }}
          </p>
          <div class="sana-ct-categories" data-ct-cats>
            @foreach ($categories as $i => $cat)
              <button type="button"
                      class="sana-ct-cat{{ ($oldSubject === $cat['subject'] || ($i === 0 && ! old('subject'))) ? ' is-active' : '' }}"
                      data-subject="{{ $cat['subject'] }}">
                <i class="{{ $cat['icon'] }}"></i>
                <span>{{ $cat['label'] }}</span>
              </button>
            @endforeach
          </div>
          <div style="margin-top:1.15rem">
            <a href="{{ route('home') }}?open_trial=1" class="sana-btn sana-btn--outline-purple" style="width:100%;justify-content:center">
              <i class="fas fa-clipboard-check"></i> {{ __('landing.academy.free_trial_cta') }}
            </a>
          </div>
        </div>

        <div class="sana-ct-form-card sana-reveal">
          @if (session('success') || session('status'))
            <div class="sana-ct-alert" role="status">
              <i class="fas fa-circle-check"></i>
              <span>{{ session('success') ?? session('status') }}</span>
            </div>
          @endif

          <div class="sana-head" style="margin-bottom:1rem;text-align:{{ $isRtl ? 'right' : 'left' }}">
            <h2 class="sana-head__title" style="font-size:1.15rem;margin:0">
              {{ $isRtl ? 'أرسل' : 'Send' }} <span class="hl">{{ $isRtl ? 'رسالتك' : 'your message' }}</span>
            </h2>
            <p class="sana-head__sub" style="margin:6px 0 0;text-align:{{ $isRtl ? 'right' : 'left' }};max-width:none">
              {{ $isRtl ? 'املأ النموذج وسيتواصل معك أحد أعضاء فريقنا قريباً.' : 'Fill the form and a team member will get back to you soon.' }}
            </p>
          </div>

          <form method="post" action="{{ route('public.contact.store') }}" novalidate>
            @csrf
            <div class="sana-ct-field">
              <input type="text" id="ct-name" name="name" value="{{ old('name') }}" required maxlength="255" placeholder=" " autocomplete="name" class="{{ $errors->has('name') ? 'is-error' : '' }}">
              <label for="ct-name">{{ $isRtl ? 'الاسم الكامل *' : 'Full name *' }}</label>
              @error('name')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
            </div>

            <div class="sana-ct-form-row">
              <div class="sana-ct-field">
                <input type="email" id="ct-email" name="email" value="{{ old('email') }}" required maxlength="255" placeholder=" " dir="ltr" autocomplete="email" class="{{ $errors->has('email') ? 'is-error' : '' }}">
                <label for="ct-email">{{ $isRtl ? 'البريد الإلكتروني *' : 'Email *' }}</label>
                @error('email')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
              </div>
              <div class="sana-ct-field">
                <input type="tel" id="ct-phone" name="phone" value="{{ old('phone') }}" maxlength="20" placeholder=" " dir="ltr" autocomplete="tel" class="{{ $errors->has('phone') ? 'is-error' : '' }}">
                <label for="ct-phone">{{ $isRtl ? 'رقم الجوال (اختياري)' : 'Phone (optional)' }}</label>
                @error('phone')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
              </div>
            </div>

            <div class="sana-ct-field">
              <input type="text" id="ct-subject" name="subject" value="{{ $oldSubject }}" required maxlength="255" placeholder=" " class="{{ $errors->has('subject') ? 'is-error' : '' }}">
              <label for="ct-subject">{{ $isRtl ? 'الموضوع *' : 'Subject *' }}</label>
              @error('subject')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
            </div>

            <div class="sana-ct-field">
              <textarea id="ct-message" name="message" required maxlength="5000" placeholder=" " class="{{ $errors->has('message') ? 'is-error' : '' }}">{{ old('message') }}</textarea>
              <label for="ct-message">{{ $isRtl ? 'الرسالة *' : 'Message *' }}</label>
              @error('message')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="sana-btn sana-btn--purple sana-ct-submit">
              <i class="fas fa-paper-plane"></i> {{ $isRtl ? 'إرسال الرسالة' : 'Send message' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <h2 class="sana-head__title">{{ $isRtl ? 'ماذا' : 'What to' }} <span class="hl">{{ $isRtl ? 'تتوقع؟' : 'expect' }}</span></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub">{{ $isRtl ? 'شفافية في أوقات الرد والدعم.' : 'Clear expectations on response times and support.' }}</p>
      </div>
      <div class="sana-ct-response">
        <div class="sana-ct-response__card sana-reveal">
          <i class="fas fa-bolt"></i>
          <strong>{{ $isRtl ? 'متوسط الرد' : 'Average reply' }}</strong>
          <em>{{ $isRtl ? 'خلال 24 ساعة' : 'Within 24h' }}</em>
          <span>{{ $isRtl ? 'لبريد النموذج — واتساب أسرع في أوقات العمل.' : 'For the form — WhatsApp is faster during hours.' }}</span>
        </div>
        <div class="sana-ct-response__card sana-reveal">
          <i class="fas fa-clock"></i>
          <strong>{{ $isRtl ? 'أوقات الدعم' : 'Support hours' }}</strong>
          <em>{{ $isRtl ? '9 ص – 9 م' : '9 AM – 9 PM' }}</em>
          <span>{{ $isRtl ? 'الأحد – الخميس (GMT+3).' : 'Sun–Thu (GMT+3).' }}</span>
        </div>
        <div class="sana-ct-response__card sana-reveal">
          <i class="fas fa-calendar-week"></i>
          <strong>{{ $isRtl ? 'أيام العمل' : 'Work days' }}</strong>
          <em>{{ $isRtl ? 'الأحد – الخميس' : 'Sun – Thu' }}</em>
          <span>{{ $isRtl ? 'دعم محدود في عطلة نهاية الأسبوع.' : 'Limited weekend support.' }}</span>
        </div>
        <div class="sana-ct-response__card sana-reveal">
          <i class="fas fa-headset"></i>
          <strong>{{ $isRtl ? 'قنوات الدعم' : 'Support channels' }}</strong>
          <em>{{ $isRtl ? 'واتساب + نموذج' : 'WhatsApp + form' }}</em>
          <span>{{ $isRtl ? 'وبريد رسمي عند التوفر.' : 'Plus official email when available.' }}</span>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-ct-final">
    <div class="sana-container sana-reveal">
      <div class="sana-ct-final__box">
        <h2>{{ $isRtl ? 'جاهز تبدأ؟' : 'Ready to start?' }}</h2>
        <p>{{ $isRtl ? 'احجز تقييم مستوى مجاني، أو راسلنا الآن — فريق '.$brand.' معك خطوة بخطوة.' : 'Book a free assessment, or message us now — the '.$brand.' team walks with you step by step.' }}</p>
        <div class="sana-ct-final__actions">
          <a href="{{ route('home') }}?open_trial=1" class="sana-btn sana-btn--yellow"><i class="fas fa-clipboard-check"></i> {{ __('landing.academy.free_trial_cta') }}</a>
          <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> {{ $isRtl ? 'واتساب' : 'WhatsApp' }}</a>
        </div>
      </div>
    </div>
  </section>

</main>

@include('partials.landing.footer')
<script>
  (function () {
    var cats = document.querySelector('[data-ct-cats]');
    var subject = document.getElementById('ct-subject');
    if (!cats || !subject) return;
    cats.addEventListener('click', function (e) {
      var btn = e.target.closest('.sana-ct-cat');
      if (!btn) return;
      cats.querySelectorAll('.sana-ct-cat').forEach(function (el) { el.classList.remove('is-active'); });
      btn.classList.add('is-active');
      var value = btn.getAttribute('data-subject') || '';
      subject.value = value;
      subject.dispatchEvent(new Event('input', { bubbles: true }));
    });
  })();
</script>
</body>
</html>
