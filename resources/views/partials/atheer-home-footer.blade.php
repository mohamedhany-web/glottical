@php $isRtl = app()->getLocale() === 'ar'; @endphp
<footer class="mt-24 border-t border-line bg-ink text-white">
  <div class="container-wide py-16 md:py-20">
    <div class="grid gap-12 lg:grid-cols-[1.15fr_2fr]">
      <div class="space-y-5">
        <p class="text-3xl font-bold">Glottical</p>
        <p class="max-w-sm text-sm leading-8 text-white/70">{{ __('landing.academy.identity_sub') }}</p>
        <button type="button" data-open-free-trial class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-medium transition hover:bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-metal" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
          {{ __('landing.academy.free_trial_cta') }}
        </button>
      </div>
      <div class="grid gap-10 sm:grid-cols-3">
        <div>
          <p class="mb-4 text-sm font-semibold">{{ $isRtl ? 'تعلّم' : 'Learn' }}</p>
          <nav class="space-y-3 text-sm text-white/65" aria-label="{{ $isRtl ? 'روابط التعلم' : 'Learn links' }}">
            <a class="block transition hover:text-white" href="{{ route('public.learning-paths.index') }}">{{ __('landing.nav.learning_paths') }}</a>
            <a class="block transition hover:text-white" href="{{ route('public.courses') }}">{{ __('landing.nav.courses') }}</a>
            <a class="block transition hover:text-white" href="{{ route('public.groups') }}">{{ __('landing.nav.groups') }}</a>
            <a class="block transition hover:text-white" href="{{ route('public.categories') }}">{{ __('landing.nav.categories') }}</a>
            <a class="block transition hover:text-white" href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a>
          </nav>
        </div>
        <div>
          <p class="mb-4 text-sm font-semibold">{{ $isRtl ? 'للطلاب' : 'Students' }}</p>
          <nav class="space-y-3 text-sm text-white/65">
            <a class="block transition hover:text-white" href="{{ route('register') }}">{{ $isRtl ? 'إنشاء حساب' : 'Sign up' }}</a>
            <a class="block transition hover:text-white" href="{{ route('login') }}">{{ $isRtl ? 'تسجيل الدخول' : 'Login' }}</a>
            <button type="button" data-open-free-trial class="block transition hover:text-white text-start">{{ __('landing.academy.free_trial_cta') }}</button>
            <a class="block transition hover:text-white" href="{{ route('public.contact') }}">{{ $isRtl ? 'تواصل معنا' : 'Contact' }}</a>
          </nav>
        </div>
        <div>
          <p class="mb-4 text-sm font-semibold">Glottical</p>
          <nav class="space-y-3 text-sm text-white/65">
            <a class="block transition hover:text-white" href="{{ route('public.about') }}">{{ $isRtl ? 'من نحن' : 'About' }}</a>
            <a class="block transition hover:text-white" href="{{ route('public.contact') }}">{{ $isRtl ? 'الدعم' : 'Support' }}</a>
            @auth
              <a class="block transition hover:text-white" href="{{ url('/dashboard') }}">{{ $isRtl ? 'لوحتي' : 'Dashboard' }}</a>
            @endauth
          </nav>
        </div>
      </div>
    </div>
  </div>
  <div class="border-t border-white/10">
    <div class="container-wide flex flex-wrap items-center justify-between gap-4 py-6 text-xs text-white/50">
      <p>© {{ date('Y') }} Glottical. {{ $isRtl ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
      <div class="flex flex-wrap gap-4">
        <a href="{{ route('public.contact') }}" class="transition hover:text-white/80">{{ $isRtl ? 'الخصوصية' : 'Privacy' }}</a>
        <a href="{{ route('public.contact') }}" class="transition hover:text-white/80">{{ $isRtl ? 'الشروط' : 'Terms' }}</a>
      </div>
    </div>
  </div>
</footer>
<script>
document.getElementById('nav-toggle')?.addEventListener('click', function () {
  var nav = document.getElementById('mobile-nav');
  if (!nav) return;
  var open = !nav.classList.contains('hidden');
  nav.classList.toggle('hidden', open);
  this.setAttribute('aria-expanded', open ? 'false' : 'true');
});
</script>
@include('partials.unregister-service-worker')
