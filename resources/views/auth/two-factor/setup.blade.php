@extends('layouts.auth-landing')

@section('title', __('auth.two_factor_setup_title'))

@section('nav_action')
  <a href="{{ route('home') }}" class="gl-auth-nav-link">{{ __('auth.back_to_home') }}</a>
@endsection

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $user = auth()->user();
    $cancelRoute = route('home');
    if ($user) {
        if (method_exists($user, 'isEmployee') && $user->isEmployee()) {
            $cancelRoute = route('employee.dashboard');
        } elseif (in_array($user->role ?? '', ['admin', 'super_admin'], true)) {
            $cancelRoute = route('admin.dashboard');
        } else {
            try {
                $cancelRoute = route('instructor.courses.index');
            } catch (\Throwable $e) {
                $cancelRoute = route('home');
            }
        }
    }
@endphp

<div class="gl-auth-card gl-auth-card--wide">
  <div class="gl-auth-brand">{{ config('app.name', 'Glottical') }}</div>

  <div class="gl-auth-badge" aria-hidden="true">
    <i class="fas fa-mobile-screen-button"></i>
  </div>

  <h1 class="gl-auth-title">{{ __('auth.two_factor_setup_title') }}</h1>
  <p class="gl-auth-lead">{{ __('auth.two_factor_setup_lead') }}</p>

  @if (session('warning'))
    <div class="gl-auth-alert gl-auth-alert--warn">{{ session('warning') }}</div>
  @endif
  @if ($errors->has('code'))
    <div class="gl-auth-alert gl-auth-alert--err">{{ $errors->first('code') }}</div>
  @endif

  <div class="gl-auth-qr">
    <img
      src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}"
      alt="QR Code"
      width="200"
      height="200"
      loading="lazy"
    >
  </div>

  <p class="gl-auth-secret">
    {{ __('auth.two_factor_manual_key') }}
    <code dir="ltr">{{ $secret }}</code>
  </p>

  <form action="{{ route('two-factor.enable') }}" method="POST" novalidate>
    @csrf
    <div class="gl-auth-field">
      <label for="code">{{ __('auth.two_factor_code_label') }}</label>
      <div class="gl-auth-input-wrap">
        <span class="gl-auth-icon" aria-hidden="true"><i class="fas fa-key"></i></span>
        <input
          type="text"
          name="code"
          id="code"
          inputmode="numeric"
          pattern="[0-9]*"
          maxlength="6"
          autocomplete="one-time-code"
          autofocus
          required
          dir="ltr"
          placeholder="000000"
          class="gl-auth-input gl-auth-input--otp has-icon @error('code') has-error @enderror"
        >
      </div>
      @error('code')<p class="gl-auth-error">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="gl-auth-submit">
      <i class="fas fa-check" aria-hidden="true"></i>
      <span>{{ __('auth.two_factor_enable_cta') }}</span>
    </button>
  </form>

  <div class="gl-auth-foot">
    <a href="{{ $cancelRoute }}" class="gl-auth-link">
      <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
      {{ __('auth.two_factor_cancel') }}
    </a>
  </div>
</div>
@endsection

@push('head')
<style>
  .gl-auth-badge {
    width: 3.25rem; height: 3.25rem; margin: 0 auto .9rem;
    border-radius: 16px; display: grid; place-items: center;
    background: linear-gradient(145deg, #0B3D91, #072A66);
    color: #fff; font-size: 1.15rem;
    box-shadow: 0 12px 28px -10px rgba(11,61,145,.55);
  }
  .gl-auth-qr {
    display: grid; place-items: center;
    margin: .2rem auto 1rem; padding: .85rem;
    width: fit-content; border-radius: 16px;
    background: #F4F7FC; border: 1.5px solid #D7DDE6;
  }
  .gl-auth-qr img { display: block; border-radius: 10px; }
  .gl-auth-secret {
    margin: 0 0 1rem; text-align: center;
    font: 600 .8rem/1.65 Tajawal, sans-serif; color: #5B6577;
  }
  .gl-auth-secret code {
    display: inline-block; margin-top: .35rem;
    padding: .35rem .65rem; border-radius: 8px;
    background: #E8EEF8; color: #0B3D91;
    font: 800 .78rem/1 ui-monospace, monospace;
  }
  .gl-auth-input--otp {
    letter-spacing: .42em;
    font-weight: 800;
    font-size: 1.2rem;
    text-align: center;
    padding-inline: 2.55rem !important;
  }
</style>
@endpush
