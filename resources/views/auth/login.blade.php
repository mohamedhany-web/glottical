@extends('layouts.auth-atheer')

@section('title', __('auth.login'))

@section('body_attrs', 'x-data="{ showPassword: false }"')

@section('brand_kicker', __('auth.welcome_back'))
@section('brand_title', __('auth.visual_title'))
@section('brand_lead', __('auth.visual_desc'))

@section('content')
  <div class="space-y-2 fade-up">
    <p class="text-sm font-medium text-accent">{{ __('auth.login') }}</p>
    <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-[2rem]">{{ __('auth.welcome_back') }}</h1>
    <p class="text-sm leading-7 text-muted">{{ __('auth.enter_credentials') }}</p>
  </div>

  <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5 fade-up fade-up-delay-1">
    @csrf

    @if (session('status'))
      <div class="flex items-start gap-3 rounded-2xl border border-line bg-success/10 px-4 py-3.5 text-sm font-medium text-success">
        <span class="mt-0.5" aria-hidden="true">✓</span>
        <p>{{ session('status') }}</p>
      </div>
    @endif
    @if (session('warning'))
      <div class="flex items-start gap-3 rounded-2xl border border-line bg-[#f4eadc] px-4 py-3.5 text-sm font-medium text-[#7a5c2e]">
        <span class="mt-0.5" aria-hidden="true">!</span>
        <p>{{ session('warning') }}</p>
      </div>
    @endif

    <div class="hidden" aria-hidden="true">
      <input type="text" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div>
      <label for="email" class="mb-2 block text-sm font-semibold text-ink">{{ __('auth.email') }}</label>
      <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-3.5' : 'left-3.5' }} flex items-center text-muted" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        </span>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email') }}"
          required
          autocomplete="email"
          autofocus
          dir="ltr"
          placeholder="you@example.com"
          class="auth-input auth-input-icon @error('email') has-error @enderror"
        >
      </div>
      @error('email')
        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <div class="mb-2 flex items-center justify-between gap-3">
        <label for="password" class="block text-sm font-semibold text-ink">{{ __('auth.password') }}</label>
        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-accent transition hover:text-ink">{{ __('auth.forgot_password') }}</a>
      </div>
      <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-3.5' : 'left-3.5' }} flex items-center text-muted" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </span>
        <input
          :type="showPassword ? 'text' : 'password'"
          name="password"
          id="password"
          required
          autocomplete="current-password"
          placeholder="••••••••"
          class="auth-input auth-input-icon-both @error('password') has-error @enderror"
        >
        <button
          type="button"
          @click="showPassword = !showPassword"
          class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-1.5' : 'right-1.5' }} my-auto flex size-9 items-center justify-center rounded-lg text-muted transition hover:bg-canvas hover:text-accent"
          :aria-label="showPassword ? '{{ app()->getLocale() === 'ar' ? 'إخفاء كلمة المرور' : 'Hide password' }}' : '{{ app()->getLocale() === 'ar' ? 'إظهار كلمة المرور' : 'Show password' }}'"
        >
          <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M10.7 5.1A10.5 10.5 0 0 1 12 5c6.5 0 10 7 10 7a18.4 18.4 0 0 1-2.1 3.1"/><path d="M6.6 6.6C3.8 8.5 2 12 2 12s3.5 7 10 7a10.4 10.4 0 0 0 4.4-1"/><path d="m2 2 20 20"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
        </button>
      </div>
      @error('password')
        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
      @enderror
    </div>

    <label class="flex items-center gap-2.5 cursor-pointer select-none">
      <input type="checkbox" name="remember" class="size-4 rounded border-line text-accent focus:ring-accent/30">
      <span class="text-sm text-muted">{{ __('auth.remember') }}</span>
    </label>

    <button type="submit" class="btn-press inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-semibold text-white shadow-[0_10px_24px_rgba(15,92,87,0.22)] transition hover:bg-[#0d4f4a]">
      <span>{{ __('auth.login') }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </button>
  </form>

  <div class="mt-8 border-t border-line pt-6 text-center fade-up fade-up-delay-2">
    <p class="text-sm text-muted">
      {{ __('auth.no_account_question') }}
      <a href="{{ route('register') }}" class="font-semibold text-accent transition hover:text-ink">{{ __('auth.no_account_register_now') }}</a>
    </p>
  </div>

  <div class="mt-6 flex items-center justify-center gap-2 text-center text-xs text-muted fade-up fade-up-delay-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0 text-accent" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
    <span>{{ __('auth.visual_desc') }}</span>
  </div>
@endsection
