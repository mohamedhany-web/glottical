@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $inputClass = 'h-12 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted/80 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-2 block text-sm font-medium text-ink';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.contact_page_title') }} — {{ $brand }}</title>
  <meta name="description" content="{{ $isRtl ? 'تواصل مع فريق '.$brand.' — استفسارات، دعم، واقتراحات. نرد في أقرب وقت.' : 'Contact the '.$brand.' team — questions, support, and suggestions. We reply promptly.' }}">
  <link rel="canonical" href="{{ route('public.contact') }}">
  @include('partials.favicon-links')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  @include('partials.atheer-head')
  <meta name="theme-color" content="#0f5c57">
</head>
<body class="font-sans antialiased">
@include('partials.atheer-home-header')

<main class="page-enter">
  <section class="container-wide py-10 md:py-14">
    <nav class="mb-6" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
      <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
        <li><a href="{{ url('/') }}" class="transition hover:text-ink">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a></li>
        <li aria-hidden="true" class="text-line">/</li>
        <li class="font-medium text-ink" aria-current="page">{{ __('public.contact_page_title') }}</li>
      </ol>
    </nav>
    <div class="max-w-2xl space-y-3">
      <p class="text-sm font-medium text-accent">{{ $brand }}</p>
      <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-4xl">{{ __('public.contact_page_title') }}</h1>
      <p class="text-base leading-8 text-muted">
        {{ $isRtl
          ? 'املأ النموذج وسيتواصل فريقنا معك قريباً — أو استخدم البريد والهاتف للوصول المباشر.'
          : 'Fill in the form and our team will get back to you soon — or reach us directly by email or phone.' }}
      </p>
    </div>
  </section>

  <section class="container-wide pb-20 md:pb-24">
    <div class="grid gap-8 lg:grid-cols-12 lg:gap-10 lg:items-start">
      {{-- Form (interaction surface) --}}
      <div class="lg:col-span-7">
        <div class="rounded-2xl border border-line bg-surface p-6 shadow-soft sm:p-8">
          @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-accent/20 bg-accent-soft px-4 py-3 text-sm font-medium text-accent" role="status">
              <svg class="mt-0.5 size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
              <span>{{ session('success') }}</span>
            </div>
          @endif

          <form method="post" action="{{ route('public.contact.store') }}" class="space-y-5">
            @csrf
            <div>
              <label for="name" class="{{ $labelClass }}">{{ $isRtl ? 'الاسم الكامل' : 'Full name' }}</label>
              <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255" class="{{ $inputClass }}" placeholder="{{ $isRtl ? 'اسمك' : 'Your name' }}" autocomplete="name">
              @error('name')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
              <div>
                <label for="email" class="{{ $labelClass }}">{{ $isRtl ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="255" class="{{ $inputClass }}" placeholder="you@example.com" autocomplete="email">
                @error('email')
                  <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label for="phone" class="{{ $labelClass }}">
                  {{ $isRtl ? 'رقم الجوال' : 'Phone' }}
                  <span class="font-normal text-muted">({{ $isRtl ? 'اختياري' : 'optional' }})</span>
                </label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" maxlength="20" class="{{ $inputClass }}" placeholder="{{ $isRtl ? '05xxxxxxxx' : '+20…' }}" autocomplete="tel">
                @error('phone')
                  <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div>
              <label for="subject" class="{{ $labelClass }}">{{ $isRtl ? 'الموضوع' : 'Subject' }}</label>
              <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required maxlength="255" class="{{ $inputClass }}" placeholder="{{ $isRtl ? 'موجز لطلبك' : 'Brief summary' }}">
              @error('subject')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="message" class="{{ $labelClass }}">{{ $isRtl ? 'الرسالة' : 'Message' }}</label>
              <textarea name="message" id="message" rows="5" required maxlength="5000" class="min-h-[140px] w-full resize-y rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted/80 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="{{ $isRtl ? 'اكتب تفاصيل رسالتك…' : 'Write your message…' }}">{{ old('message') }}</textarea>
              @error('message')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <button type="submit" class="btn-press inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-accent px-8 text-sm font-medium text-white transition hover:bg-[#0d4f4a] sm:w-auto">
              <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
              {{ $isRtl ? 'إرسال الرسالة' : 'Send message' }}
            </button>
          </form>
        </div>
      </div>

      {{-- Channels + help --}}
      <aside class="space-y-6 lg:col-span-5">
        <div class="space-y-5">
          <div class="space-y-2">
            <p class="text-sm font-medium text-accent">{{ $isRtl ? 'معلومات التواصل' : 'Contact details' }}</p>
            <h2 class="text-xl font-semibold text-ink">{{ $isRtl ? 'تواصل مباشر' : 'Reach us directly' }}</h2>
            <p class="text-sm leading-7 text-muted">
              {{ $isRtl
                ? 'منصة '.$brand.' — راسلنا أو اتصل بنا عبر القنوات التالية.'
                : $brand.' — email or call us through the channels below.' }}
            </p>
          </div>

          @if($supportEmail !== '' || $supportPhone !== '')
            <ul class="space-y-3">
              @if($supportEmail !== '')
                <li>
                  <a href="mailto:{{ $supportEmail }}" class="group flex items-start gap-3 rounded-2xl border border-line bg-surface p-4 shadow-soft transition hover:border-accent/30 hover:bg-accent-soft">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-accent text-white" aria-hidden="true">
                      <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                    <span>
                      <span class="block text-xs font-medium text-muted">{{ $isRtl ? 'البريد' : 'Email' }}</span>
                      <span class="break-all text-sm font-semibold text-ink group-hover:text-accent">{{ $supportEmail }}</span>
                    </span>
                  </a>
                </li>
              @endif
              @if($supportPhone !== '')
                <li>
                  <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" class="group flex items-start gap-3 rounded-2xl border border-line bg-surface p-4 shadow-soft transition hover:border-accent/30 hover:bg-accent-soft">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-ink text-metal" aria-hidden="true">
                      <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <span>
                      <span class="block text-xs font-medium text-muted">{{ $isRtl ? 'الهاتف' : 'Phone' }}</span>
                      <span class="block text-sm font-semibold text-ink dir-ltr {{ $isRtl ? 'text-right' : 'text-left' }} group-hover:text-accent">{{ $supportPhone }}</span>
                    </span>
                  </a>
                </li>
              @endif
            </ul>
          @else
            <p class="rounded-xl border border-dashed border-line px-4 py-3 text-sm text-muted">{{ __('public.contact_channels_empty_hint') }}</p>
          @endif
        </div>

        <div class="rounded-2xl border border-line bg-canvas px-5 py-6 sm:px-6">
          <p class="mb-1 text-sm font-medium text-accent">{{ $isRtl ? 'أسئلة سريعة؟' : 'Quick answers?' }}</p>
          <h3 class="mb-2 text-lg font-semibold text-ink">{{ $isRtl ? 'قد تجد إجابتك فوراً' : 'You may find your answer instantly' }}</h3>
          <p class="mb-5 text-sm leading-7 text-muted">{{ $isRtl ? 'تصفّح الأسئلة الشائعة أو مركز المساعدة قبل إرسال رسالة.' : 'Browse the FAQ or help center before sending a message.' }}</p>
          <div class="flex flex-wrap gap-3">
            @if(Route::has('public.faq'))
              <a href="{{ route('public.faq') }}" class="inline-flex h-10 items-center rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">
                {{ $isRtl ? 'الأسئلة الشائعة' : 'FAQ' }}
              </a>
            @endif
            @if(Route::has('public.help'))
              <a href="{{ route('public.help') }}" class="inline-flex h-10 items-center rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">
                {{ $isRtl ? 'مركز المساعدة' : 'Help center' }}
              </a>
            @endif
            <a href="{{ route('public.about') }}" class="inline-flex h-10 items-center rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">
              {{ __('public.about_page_title') }}
            </a>
          </div>
        </div>
      </aside>
    </div>
  </section>
</main>

@include('partials.atheer-home-footer')
<script>
  document.querySelectorAll('[data-open-free-trial]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      window.location.href = {{ \Illuminate\Support\Js::from(url('/?open_trial=1')) }};
    });
  });
</script>
</body>
</html>
