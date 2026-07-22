@extends('layouts.auth-atheer')

@section('title', __('auth.register'))
@section('form_max', 'max-w-[32rem]')

@section('body_attrs', 'x-data="{ showPassword: false, showPasswordConfirm: false }"')

@section('brand_kicker', __('auth.join_us'))
@section('brand_title', __('auth.create_account_desc'))
@section('brand_lead', __('auth.register_subtitle'))

@section('content')
  @php $isRtl = app()->getLocale() === 'ar'; @endphp

  <div class="space-y-2 fade-up">
    <p class="text-sm font-medium text-accent">{{ __('auth.register') }}</p>
    <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-[2rem]">{{ __('auth.create_account_btn') }}</h1>
    <p class="text-sm leading-7 text-muted">{{ __('auth.register_subtitle') }}</p>
  </div>

  <div class="mt-6 flex gap-3 rounded-2xl border border-line bg-accent-soft/60 px-4 py-3.5 fade-up fade-up-delay-1">
    <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl bg-surface text-accent shadow-soft" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
    </span>
    <p class="text-sm leading-7 text-ink-soft">{{ __('auth.register_portal_note') }}</p>
  </div>

  @if (! empty($pendingReferralCode))
    <div class="mt-4 flex gap-3 rounded-2xl border border-line bg-success/10 px-4 py-3.5 fade-up fade-up-delay-1">
      <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl bg-success text-white" aria-hidden="true">✦</span>
      <div class="min-w-0 text-sm">
        <p class="font-semibold text-success">{{ __('auth.referral_invite_title') }}</p>
        <p class="mt-1 leading-7 text-ink-soft">{{ __('auth.referral_invite_body', ['code' => $pendingReferralCode]) }}</p>
      </div>
    </div>
  @endif

  <form action="{{ route('register') }}" method="POST" class="mt-7 fade-up fade-up-delay-2">
    @csrf
    <input type="hidden" name="referral_code" value="{{ old('referral_code', $pendingReferralCode ?? '') }}">
    @php
      $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
      $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-x-4 sm:gap-y-5">
      <div>
        <label for="name" class="mb-2 block text-sm font-semibold text-ink">{{ __('auth.full_name') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 {{ $isRtl ? 'right-3.5' : 'left-3.5' }} flex items-center text-muted" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name"
                 class="auth-input auth-input-icon @error('name') has-error @enderror"
                 placeholder="{{ __('auth.enter_full_name') }}">
        </div>
        @error('name')<p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="mb-2 block text-sm font-semibold text-ink">{{ __('auth.phone_number') }}</label>
        <div class="auth-phone @error('phone') has-error @enderror">
          <select name="country_code" required dir="ltr" aria-label="{{ __('auth.country_code_aria') }}">
            @foreach ($phoneCountries ?? [] as $c)
              <option value="{{ $c['dial_code'] }}" {{ old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code'] ? 'selected' : '' }}>
                {{ $c['dial_code'] }} {{ $isRtl ? ($c['name_ar'] ?? '') : ($c['name_en'] ?? $c['name_ar'] ?? '') }}
              </option>
            @endforeach
          </select>
          <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="5xxxxxxxx" dir="ltr" autocomplete="tel-national" aria-label="{{ __('auth.phone_aria') }}">
        </div>
        @error('phone')<p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>@enderror
      </div>

      <div class="sm:col-span-2">
        <label for="email" class="mb-2 block text-sm font-semibold text-ink">{{ __('auth.email') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 {{ $isRtl ? 'right-3.5' : 'left-3.5' }} flex items-center text-muted" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          </span>
          <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" dir="ltr"
                 class="auth-input auth-input-icon @error('email') has-error @enderror"
                 placeholder="you@example.com">
        </div>
        @error('email')<p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>@enderror
      </div>

      <div>
        <label for="password" class="mb-2 block text-sm font-semibold text-ink">{{ __('auth.password') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 {{ $isRtl ? 'right-3.5' : 'left-3.5' }} flex items-center text-muted" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required autocomplete="new-password"
                 class="auth-input auth-input-icon-both @error('password') has-error @enderror"
                 placeholder="{{ __('auth.enter_strong_password') }}">
          <button type="button" @click="showPassword = !showPassword"
                  class="absolute inset-y-0 {{ $isRtl ? 'left-1.5' : 'right-1.5' }} my-auto flex size-9 items-center justify-center rounded-lg text-muted transition hover:bg-canvas hover:text-accent"
                  aria-label="{{ $isRtl ? 'إظهار/إخفاء كلمة المرور' : 'Toggle password' }}">
            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M10.7 5.1A10.5 10.5 0 0 1 12 5c6.5 0 10 7 10 7a18.4 18.4 0 0 1-2.1 3.1"/><path d="M6.6 6.6C3.8 8.5 2 12 2 12s3.5 7 10 7a10.4 10.4 0 0 0 4.4-1"/><path d="m2 2 20 20"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
          </button>
        </div>
        @error('password')<p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>@enderror
      </div>

      <div>
        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-ink">{{ __('auth.password_confirmation') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 {{ $isRtl ? 'right-3.5' : 'left-3.5' }} flex items-center text-muted" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                 class="auth-input auth-input-icon-both"
                 placeholder="{{ __('auth.reenter_password') }}">
          <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                  class="absolute inset-y-0 {{ $isRtl ? 'left-1.5' : 'right-1.5' }} my-auto flex size-9 items-center justify-center rounded-lg text-muted transition hover:bg-canvas hover:text-accent"
                  aria-label="{{ $isRtl ? 'إظهار/إخفاء التأكيد' : 'Toggle confirmation' }}">
            <svg x-show="!showPasswordConfirm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg x-show="showPasswordConfirm" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M10.7 5.1A10.5 10.5 0 0 1 12 5c6.5 0 10 7 10 7a18.4 18.4 0 0 1-2.1 3.1"/><path d="M6.6 6.6C3.8 8.5 2 12 2 12s3.5 7 10 7a10.4 10.4 0 0 0 4.4-1"/><path d="m2 2 20 20"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
          </button>
        </div>
      </div>
    </div>

    <label class="mt-5 flex items-start gap-3 cursor-pointer select-none">
      <input type="checkbox" id="terms" required class="mt-1 size-4 shrink-0 rounded border-line text-accent focus:ring-accent/30">
      <span class="text-sm leading-7 text-muted">
        {{ __('auth.agree_terms') }}
        <a href="{{ route('public.terms') }}" class="font-semibold text-accent transition hover:text-ink">{{ __('auth.terms_of_use') }}</a>
        {{ __('auth.and') }}
        <a href="{{ route('public.privacy') }}" class="font-semibold text-accent transition hover:text-ink">{{ __('auth.privacy_policy') }}</a>
      </span>
    </label>

    <button type="submit" class="btn-press mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-semibold text-white shadow-[0_10px_24px_rgba(15,92,87,0.22)] transition hover:bg-[#0d4f4a]">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>
      <span>{{ __('auth.create_account_btn') }}</span>
    </button>
  </form>

  <div class="mt-8 border-t border-line pt-6 text-center fade-up fade-up-delay-3">
    <p class="text-sm text-muted">
      {{ __('auth.already_have_account') }}
      <a href="{{ route('login') }}" class="font-semibold text-accent transition hover:text-ink">{{ __('auth.login') }}</a>
    </p>
  </div>
@endsection
