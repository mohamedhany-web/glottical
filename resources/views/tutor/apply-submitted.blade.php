@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $application = $application ?? null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
  <title>{{ $isRtl ? 'طلبك قيد المراجعة' : 'Application under review' }} — {{ $brand }}</title>
  <meta name="theme-color" content="#0B3D91">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'instructor-profile']])
</head>
<body class="sana-home ta-page ta-page--status">
@include('partials.landing.navbar', ['navActive' => null, 'navSolid' => true, 'navHero' => false])

<main class="ta-status">
  <div class="ta-status__glow" aria-hidden="true"></div>

  @if(session('success'))
    <div class="ta-ok ta-status__flash">{{ session('success') }}</div>
  @endif

  <section class="ta-status__panel">
    <div class="ta-status__icon" aria-hidden="true">
      <svg viewBox="0 0 64 64" width="56" height="56" fill="none">
        <circle cx="32" cy="32" r="30" stroke="#F5B800" stroke-width="3" opacity=".35"/>
        <circle cx="32" cy="32" r="22" fill="#FFF8E6"/>
        <path d="M32 18v16l10 6" stroke="#0B3D91" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <p class="ta-status__chip">{{ $isRtl ? 'توظيف المعلمين' : 'Teacher hiring' }}</p>
    <h1 class="ta-status__title">{{ $isRtl ? 'طلبك قيد المراجعة' : 'Your application is under review' }}</h1>
    <p class="ta-status__lead">
      {{ $isRtl
        ? 'استلمنا بياناتك بنجاح. حسابك يعمل الآن، وبعد موافقة الإدارة يظهر ملفك للطلاب.'
        : 'We received your details. Your account works now, and after admin approval your profile appears to students.' }}
    </p>

    @if($application)
      <div class="ta-status__meta">
        <span>{{ $application->full_name }}</span>
        <span dir="ltr">{{ $application->email }}</span>
        <span class="ta-status__badge">{{ $isRtl ? 'قيد المراجعة' : 'Pending review' }}</span>
      </div>
    @endif

    <ol class="ta-status__steps">
      <li class="is-done">
        <span class="ta-status__step-dot" aria-hidden="true"></span>
        <div>
          <strong>{{ $isRtl ? 'تم إرسال الطلب' : 'Application sent' }}</strong>
          <p>{{ $isRtl ? 'البيانات والمستندات وصلت للإدارة.' : 'Details and documents reached the admin team.' }}</p>
        </div>
      </li>
      <li class="is-current">
        <span class="ta-status__step-dot" aria-hidden="true"></span>
        <div>
          <strong>{{ $isRtl ? 'المراجعة الآن' : 'Under review' }}</strong>
          <p>{{ $isRtl ? 'نراجع الملف قبل القبول والتفعيل.' : 'We review your profile before approval and activation.' }}</p>
        </div>
      </li>
      <li>
        <span class="ta-status__step-dot" aria-hidden="true"></span>
        <div>
          <strong>{{ $isRtl ? 'التفعيل والظهور للطلاب' : 'Activation & public profile' }}</strong>
          <p>{{ $isRtl ? 'بعد القبول يظهر ملفك في صفحة المعلمين.' : 'After approval your profile appears on the instructors page.' }}</p>
        </div>
      </li>
    </ol>

    <div class="ta-status__actions">
      <a href="{{ route('dashboard') }}" class="sana-btn sana-btn--yellow sana-btn--lg">
        {{ $isRtl ? 'الذهاب إلى لوحة المعلم' : 'Go to instructor dashboard' }}
      </a>
      <a href="{{ route('instructor.personal-branding.edit') }}" class="sana-btn sana-btn--outline sana-btn--lg">
        {{ $isRtl ? 'تحديث الملف التعريفي' : 'Update personal branding' }}
      </a>
    </div>
  </section>
</main>

@include('partials.landing.footer')
</body>
</html>
