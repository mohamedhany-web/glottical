@extends('layouts.auth-landing')

@section('title', __('auth.login'))

@section('body_attrs', 'x-data="{ showPassword: false }"')

@section('nav_action')
  <a href="{{ route('register') }}" class="gl-auth-nav-link">{{ __('auth.register') }}</a>
@endsection

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="gl-auth-card">
  <div class="gl-auth-brand">{{ config('app.name', 'Glottical') }}</div>
  <h1 class="gl-auth-title">{{ __('auth.welcome_back') }}<br><em>{{ $isRtl ? 'إلى Glottical' : 'to Glottical' }}</em></h1>
  <p class="gl-auth-lead">{{ __('auth.enter_credentials') }}</p>

  <form action="{{ route('login') }}" method="POST" novalidate>
    @csrf

    @if (session('status'))
      <div class="gl-auth-alert gl-auth-alert--ok">{{ session('status') }}</div>
    @endif
    @if (session('warning'))
      <div class="gl-auth-alert gl-auth-alert--warn">{{ session('warning') }}</div>
    @endif
    @if ($errors->any())
      <div class="gl-auth-alert gl-auth-alert--err">{{ $errors->first() }}</div>
    @endif

    <div class="gl-auth-sr-only" aria-hidden="true">
      <label>website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <div class="gl-auth-field">
      <label for="email">{{ __('auth.email') }}</label>
      <div class="gl-auth-input-wrap">
        <span class="gl-auth-icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
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
          class="gl-auth-input has-icon @error('email') has-error @enderror"
        >
      </div>
      @error('email')<p class="gl-auth-error">{{ $message }}</p>@enderror
    </div>

    <div class="gl-auth-field">
      <label for="password">{{ __('auth.password') }}</label>
      <div class="gl-auth-input-wrap">
        <span class="gl-auth-icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
        <input
          :type="showPassword ? 'text' : 'password'"
          name="password"
          id="password"
          required
          autocomplete="current-password"
          placeholder="••••••••"
          class="gl-auth-input has-icon @error('password') has-error @enderror"
          style="padding-inline-end:3rem"
        >
        <button type="button" class="gl-auth-pw-btn" @click="showPassword = !showPassword">
          <span x-text="showPassword ? '{{ $isRtl ? 'إخفاء' : 'Hide' }}' : '{{ $isRtl ? 'إظهار' : 'Show' }}'"></span>
        </button>
      </div>
      @error('password')<p class="gl-auth-error">{{ $message }}</p>@enderror
    </div>

    <div class="gl-auth-row">
      <label class="gl-auth-check">
        <input type="checkbox" name="remember">
        <span>{{ __('auth.remember') }}</span>
      </label>
      <a href="{{ route('password.request') }}" class="gl-auth-link">{{ __('auth.forgot_password') }}</a>
    </div>

    <button type="submit" class="gl-auth-submit">
      <span>{{ __('auth.login') }}</span>
      <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
    </button>
  </form>

  <div class="gl-auth-foot">
    {{ __('auth.no_account_question') }}
    <a href="{{ route('register') }}" class="gl-auth-link">{{ __('auth.no_account_register_now') }}</a>
  </div>

  <div class="gl-auth-trust">
    <span><i class="fas fa-video"></i> {{ $isRtl ? 'حصص مباشرة' : 'Live sessions' }}</span>
    <span><i class="fas fa-shield-halved"></i> {{ $isRtl ? 'دخول آمن' : 'Secure login' }}</span>
    <span><i class="fas fa-graduation-cap"></i> {{ $isRtl ? 'حتى الشهادة' : 'To certification' }}</span>
  </div>
</div>
@endsection
