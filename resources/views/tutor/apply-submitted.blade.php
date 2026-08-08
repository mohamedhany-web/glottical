@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
  <title>{{ $isRtl ? 'تم إرسال الطلب' : 'Application submitted' }} — {{ $brand }}</title>
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'instructor-profile']])
</head>
<body class="sana-home ta-page">
@include('partials.landing.navbar', ['navActive' => null])
<main class="ta-wrap">
  @if(session('success'))
    <div class="ta-ok">{{ session('success') }}</div>
  @endif
  <div class="ta-card" style="text-align:center">
    <h1 class="ta-title">{{ $isRtl ? 'طلبك قيد المراجعة' : 'Your application is under review' }}</h1>
    <p class="ta-sub">
      {{ $isRtl
        ? 'حسابك يعمل الآن. بعد موافقة الإدارة يظهر ملفك للطلاب. يمكنك تحديث ملفك من لوحة المعلم.'
        : 'Your account works now. After admin approval your profile appears to students. You can update branding from the instructor panel.' }}
    </p>
    <div style="display:grid;gap:.5rem;max-width:360px;margin:1.25rem auto 0">
      <a href="{{ route('instructor.dashboard') }}" class="sana-btn sana-btn--yellow" style="justify-content:center">{{ $isRtl ? 'لوحة المعلم' : 'Instructor dashboard' }}</a>
      <a href="{{ route('instructor.personal-branding.edit') }}" class="sana-btn sana-btn--purple-outline" style="justify-content:center">{{ $isRtl ? 'الملف التعريفي' : 'Personal branding' }}</a>
    </div>
  </div>
</main>
@include('partials.landing.footer')
</body>
</html>
