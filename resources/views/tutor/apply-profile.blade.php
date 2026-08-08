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
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $isRtl ? 'إكمال بيانات المعلم' : 'Complete teacher profile' }} — {{ $brand }}</title>
  <meta name="theme-color" content="#0B3D91">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'instructor-profile']])
</head>
<body class="sana-home sana-courses-page ta-page">
@include('partials.landing.navbar', ['navActive' => null, 'navSolid' => true, 'navHero' => false])

<main class="ta-wrap">
  <p class="ta-chip">{{ $isRtl ? 'الخطوة 2 من 2' : 'Step 2 of 2' }}</p>
  <h1 class="ta-title">{{ $isRtl ? 'أكمل بياناتك الشخصية' : 'Complete your personal details' }}</h1>
  <p class="ta-sub">
    {{ $isRtl
      ? 'حسابك جاهز: '.($user->email ?? '').' — أضف بياناتك والمستندات والفيديو التعريفي لإرسالها للمراجعة.'
      : 'Your account is ready: '.($user->email ?? '').' — add your details, documents, and intro video for review.' }}
  </p>

  @if(session('success'))
    <div class="ta-ok">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="ta-note" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B">{{ session('error') }}</div>
  @endif

  <form method="POST" action="{{ route('public.tutor.apply.profile.store') }}" enctype="multipart/form-data">
    @csrf

    <section class="ta-card">
      <h2>{{ $isRtl ? '١. البيانات الشخصية' : '1. Personal details' }}</h2>
      <div class="ta-grid ta-grid--2">
        <div class="ta-field">
          <label for="full_name">{{ $isRtl ? 'الاسم الكامل' : 'Full name' }} <span class="req">*</span></label>
          <input id="full_name" type="text" name="full_name" value="{{ old('full_name', $application->full_name) }}" required>
          @error('full_name')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="phone">{{ $isRtl ? 'الجوال / واتساب' : 'Phone / WhatsApp' }} <span class="req">*</span></label>
          <input id="phone" type="text" name="phone" value="{{ old('phone', $application->phone) }}" required dir="ltr">
          @error('phone')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="gender">{{ $isRtl ? 'النوع' : 'Gender' }}</label>
          <select id="gender" name="gender">
            <option value="">{{ $isRtl ? '— اختياري —' : '— optional —' }}</option>
            <option value="male" @selected(old('gender', $application->gender) === 'male')>{{ $isRtl ? 'ذكر' : 'Male' }}</option>
            <option value="female" @selected(old('gender', $application->gender) === 'female')>{{ $isRtl ? 'أنثى' : 'Female' }}</option>
          </select>
        </div>
        <div class="ta-field">
          <label for="nationality">{{ $isRtl ? 'الجنسية' : 'Nationality' }}</label>
          <input id="nationality" type="text" name="nationality" value="{{ old('nationality', $application->nationality) }}">
        </div>
        <div class="ta-field">
          <label for="city">{{ $isRtl ? 'الدولة / المدينة' : 'Country / city' }}</label>
          <input id="city" type="text" name="city" value="{{ old('city', $application->city) }}">
        </div>
        <div class="ta-field">
          <label>{{ $isRtl ? 'البريد (للدخول)' : 'Login email' }}</label>
          <input type="email" value="{{ $user->email }}" disabled dir="ltr" style="opacity:.7">
        </div>
      </div>
    </section>

    <section class="ta-card">
      <h2>{{ $isRtl ? '٢. العنوان والسيرة' : '2. Title & bio' }}</h2>
      <div class="ta-grid">
        <div class="ta-field">
          <label for="headline">{{ $isRtl ? 'عنوان مختصر' : 'Headline' }} <span class="req">*</span></label>
          <input id="headline" type="text" name="headline" value="{{ old('headline', $application->headline) }}" required>
          @error('headline')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="bio">{{ $isRtl ? 'نبذة / سيرة ذاتية' : 'Bio / CV' }} <span class="req">*</span></label>
          <textarea id="bio" name="bio" required>{{ old('bio', $application->bio) }}</textarea>
          @error('bio')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="experience">{{ $isRtl ? 'الخبرات' : 'Experience' }} <span class="req">*</span></label>
          <textarea id="experience" name="experience" required>{{ old('experience', $application->experience) }}</textarea>
          @error('experience')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-grid ta-grid--2">
          <div class="ta-field">
            <label for="education">{{ $isRtl ? 'المؤهل' : 'Education' }}</label>
            <input id="education" type="text" name="education" value="{{ old('education', $application->education) }}">
          </div>
          <div class="ta-field">
            <label for="years_experience">{{ $isRtl ? 'سنوات الخبرة' : 'Years of experience' }}</label>
            <input id="years_experience" type="number" min="0" max="60" name="years_experience" value="{{ old('years_experience', $application->years_experience) }}">
          </div>
        </div>
      </div>
    </section>

    <section class="ta-card">
      <h2>{{ $isRtl ? '٣. المستندات والفيديو' : '3. Documents & video' }}</h2>
      <div class="ta-grid">
        <div class="ta-field">
          <label for="photo">{{ $isRtl ? 'صورة شخصية' : 'Personal photo' }} @unless($application->photo_path)<span class="req">*</span>@endunless</label>
          @if($application->photoUrl())
            <p class="ta-hint">{{ $isRtl ? 'مرفوعة مسبقاً — ارفع بديلاً للتغيير' : 'Already uploaded — replace to change' }}</p>
          @endif
          <input id="photo" type="file" name="photo" accept="image/*" @if(!$application->photo_path) required @endif>
          @error('photo')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="id_document">{{ $isRtl ? 'البطاقة أو جواز السفر' : 'ID / passport' }} @unless($application->id_document_path)<span class="req">*</span>@endunless</label>
          <input id="id_document" type="file" name="id_document" accept="image/*,.pdf" @if(!$application->id_document_path) required @endif>
          @error('id_document')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="certificate">{{ $isRtl ? 'الشهادة أو الإجازة' : 'Certificate / license' }} @unless($application->certificate_path)<span class="req">*</span>@endunless</label>
          <input id="certificate" type="file" name="certificate" accept="image/*,.pdf" @if(!$application->certificate_path) required @endif>
          @error('certificate')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="intro_video">{{ $isRtl ? 'فيديو تعريفي (ملف)' : 'Intro video (file)' }}</label>
          <input id="intro_video" type="file" name="intro_video" accept="video/mp4,video/webm,video/quicktime">
          @error('intro_video')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="intro_video_url">{{ $isRtl ? 'أو رابط الفيديو' : 'Or video URL' }}</label>
          <input id="intro_video_url" type="url" name="intro_video_url" value="{{ old('intro_video_url', $application->intro_video_url) }}" dir="ltr">
          @error('intro_video_url')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </section>

    <button type="submit" class="sana-btn sana-btn--yellow sana-btn--lg" style="width:100%;justify-content:center">
      {{ $isRtl ? 'إرسال للمراجعة' : 'Submit for review' }}
    </button>
  </form>
</main>

@include('partials.landing.footer')
</body>
</html>
