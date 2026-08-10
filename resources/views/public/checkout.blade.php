@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $itemTitle = $course->title ?? ($isRtl ? 'الحصة' : 'Lesson');
    $thumbUrl = null;
    if (isset($course) && ($course->thumbnail ?? null)) {
        $thumbUrl = storage_asset(str_replace('\\', '/', $course->thumbnail));
    }
    $appName = config('app.name');
    $isMonthlyCheckout = $course->isMonthlyBilling();
    $baseCoursePrice = (float) $course->effectiveCheckoutPrice();
    $studentBal = isset($studentWalletBalance) ? (float) $studentWalletBalance : 0;
    $checkoutHasWalletBalance = isset($studentWalletBalance) && (float) $studentWalletBalance > 0;
    $fawaterakActive = !empty($fawaterakUseGateway);
    $fawaterakMis = !empty($fawaterakMisconfigured);
    $fawaterakIntegration = $fawaterakIntegration ?? 'iframe';
    $isOneToOne = method_exists($course, 'isOneToOne') && $course->isOneToOne();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.checkout_page_label') }} — {{ $itemTitle }} — {{ $appName }}</title>
  <meta name="theme-color" content="#0B3D91">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme']])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
    .gl-ck{background:var(--bg,#F4F7FC);padding:0 0 4rem}
    .gl-ck-hero{
      padding:clamp(88px,11vw,110px) 0 1.25rem;
      background:linear-gradient(175deg,#051F4D 0%,#072A66 45%,#0B3D91 100%);
      color:#fff;
    }
    .gl-ck-crumb{display:flex;flex-wrap:wrap;gap:6px;align-items:center;font:700 .75rem Tajawal,sans-serif;color:rgba(255,255,255,.7);margin-bottom:.85rem}
    .gl-ck-crumb a{color:#F5B800;text-decoration:none!important}
    .gl-ck-hero h1{margin:0 0 .4rem;font:900 clamp(1.35rem,3vw,1.85rem)/1.3 Cairo,Tajawal,sans-serif}
    .gl-ck-hero p{margin:0;font:600 .9rem/1.6 Tajawal,sans-serif;color:rgba(255,255,255,.85);max-width:40rem}
    .gl-ck-steps{display:flex;flex-wrap:wrap;gap:.55rem;margin-top:1rem}
    .gl-ck-step{display:inline-flex;align-items:center;gap:8px;padding:.4rem .75rem;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.14);font:800 .72rem Tajawal,sans-serif}
    .gl-ck-step.is-done{background:rgba(16,185,129,.18);border-color:rgba(16,185,129,.35)}
    .gl-ck-step.is-on{background:rgba(245,184,0,.95);color:#072A66;border-color:transparent}
    .gl-ck-wrap{margin-top:-1.25rem;position:relative;z-index:2}
    .gl-ck-grid{display:grid;gap:1rem}
    @media(min-width:992px){.gl-ck-grid{grid-template-columns:minmax(0,1.15fr) minmax(280px,.85fr);align-items:start}}
    .gl-ck-card{
      background:#fff;border:1.5px solid #D7DDE6;border-radius:18px;
      box-shadow:0 14px 36px -22px rgba(11,61,145,.35);padding:1.15rem 1.2rem 1.3rem;
    }
    .gl-ck-card h2{margin:0 0 .35rem;font:900 1.1rem/1.35 Cairo,Tajawal,sans-serif;color:#0B1220}
    .gl-ck-card__sub{margin:0 0 1rem;font:600 .82rem/1.5 Tajawal,sans-serif;color:#5B6577}
    .gl-ck-alert{border-radius:14px;padding:.85rem 1rem;display:flex;gap:.65rem;align-items:flex-start;font:600 .84rem/1.5 Tajawal,sans-serif;margin-bottom:.9rem}
    .gl-ck-alert--err{background:#FEF2F2;border:1px solid #FECACA;color:#991B1B}
    .gl-ck-alert--ok{background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46}
    .gl-ck-alert--info{background:#FFF8E6;border:1px solid #F5D56B;color:#8A6A00}
    .gl-ck-alert--sky{background:#E8EEF8;border:1px solid #C5D4EF;color:#072A66}
    .gl-ck-field{margin-bottom:.85rem}
    .gl-ck-field label{display:block;margin:0 0 .35rem;font:800 .78rem Tajawal,sans-serif;color:#5B6577}
    .gl-ck-input,.input-checkout{
      width:100%;border-radius:12px;border:1.5px solid #D7DDE6;background:#F4F7FC;color:#0B1220;
      padding:.8rem .95rem;font:600 .9rem Tajawal,sans-serif;
    }
    .gl-ck-input:focus,.input-checkout:focus{outline:none;border-color:#0B3D91;box-shadow:0 0 0 3px rgba(11,61,145,.12);background:#fff}
    .gl-ck-panel{border:1px solid #E8EEF8;background:#F8FAFD;border-radius:14px;padding:1rem;margin-bottom:1rem}
    .gl-ck-panel h3{margin:0 0 .35rem;font:800 .95rem Tajawal,sans-serif;color:#0B1220;display:flex;align-items:center;gap:8px}
    .gl-ck-sum-row{display:flex;justify-content:space-between;gap:10px;font:700 .84rem Tajawal,sans-serif;color:#5B6577;margin:.35rem 0}
    .gl-ck-sum-row strong,.gl-ck-sum-row #sum-original,#sum-final{color:#0B3D91;font-weight:900}
    .gl-ck-sum-total{border-top:1px solid #E8EEF8;padding-top:.75rem;margin-top:.55rem;display:flex;justify-content:space-between;align-items:center}
    .gl-ck-sum-total span{font:800 .9rem Tajawal,sans-serif;color:#0B1220}
    .gl-ck-sum-total #sum-final{font:900 1.35rem Cairo,Tajawal,sans-serif;color:#0B3D91}
    .gl-ck-item{display:flex;gap:12px;align-items:flex-start;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid #E8EEF8}
    .gl-ck-item img,.gl-ck-item__ph{width:64px;height:64px;border-radius:14px;object-fit:cover;flex-shrink:0;background:#E8EEF8}
    .gl-ck-item__ph{display:grid;place-items:center;color:#0B3D91;font-size:1.25rem}
    .gl-ck-item h3{margin:0;font:800 .95rem/1.4 Tajawal,sans-serif;color:#0B1220}
    .gl-ck-item p{margin:.25rem 0 0;font:600 .78rem Tajawal,sans-serif;color:#5B6577}
    .gl-ck-benefits{list-style:none;margin:0;padding:0;display:grid;gap:.45rem}
    .gl-ck-benefits li{display:flex;gap:8px;align-items:center;font:700 .78rem Tajawal,sans-serif;color:#5B6577}
    .gl-ck-benefits i{color:#059669}
    .btn-acad-primary,.gl-ck-btn{
      display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
      padding:.85rem 1.35rem;border-radius:14px;border:0;cursor:pointer;
      background:#F5B800;color:#072A66;font:800 .9rem Tajawal,sans-serif;text-decoration:none!important;
    }
    .btn-acad-primary:disabled{opacity:.55;cursor:not-allowed}
    .btn-acad-ghost,.gl-ck-btn--ghost{
      display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
      padding:.85rem 1.35rem;border-radius:14px;border:1.5px solid #D7DDE6;background:#fff;
      color:#0B3D91;font:800 .85rem Tajawal,sans-serif;text-decoration:none!important;
    }
    #fawaterkDivId{min-height:480px;width:100%;border-radius:14px;border:1.5px solid #D7DDE6;background:#fff;overflow:hidden}
    .hidden{display:none!important}
    .flex{display:flex}.items-center{align-items:center}.justify-center{justify-content:center}
    .gap-3{gap:.75rem}.p-3{padding:.75rem}.rounded-xl{border-radius:12px}
    .border-2{border-width:2px;border-style:solid}.bg-white{background:#fff}
    .text-start{text-align:start}.font-bold{font-weight:800}.min-w-0{min-width:0}.flex-1{flex:1}
    .shrink-0{flex-shrink:0}.h-10{height:2.5rem}.w-10{width:2.5rem}.w-auto{width:auto}
    .object-contain{object-fit:contain}.ring-2{box-shadow:0 0 0 2px rgba(245,184,0,.3)}
    .transition-colors{transition:border-color .15s ease,box-shadow .15s ease}
    </style>
</head>
<body class="sana-home" x-data="{ isSubmitting: false }">
<div id="sana-scroll-progress"></div>
<div id="scroll-progress" style="display:none"></div>
@include('partials.landing.navbar', ['navActive' => 'courses', 'navSolid' => false, 'navHero' => true])

<main class="gl-ck">
  <section class="gl-ck-hero">
    <div class="sana-container">
      <nav class="gl-ck-crumb" aria-label="breadcrumb">
        <a href="{{ route('home') }}">{{ __('public.home') }}</a>
        <span>/</span>
        <a href="{{ route('public.courses') }}">{{ __('landing.nav.courses') }}</a>
        <span>/</span>
        <a href="{{ route('public.course.show', $course->id) }}">{{ \Illuminate\Support\Str::limit($itemTitle, 28) }}</a>
        <span>/</span>
        <span>{{ __('public.checkout_breadcrumb_current') }}</span>
                </nav>
      <h1>{{ $isOneToOne ? ($isRtl ? 'خطتك التعليمية' : 'Your Learning Plan') : __('public.checkout_page_label') }}</h1>
      <p>{{ $isRtl ? 'أكمل الاشتراك بأمان — المعلم والمواعيد والباقة في خطوة واحدة.' : 'Complete your plan securely — teacher, schedule, and package in one step.' }}</p>
      <div class="gl-ck-steps" aria-hidden="true">
        <span class="gl-ck-step is-done">1 · {{ $isRtl ? 'اختر المعلم' : 'Choose teacher' }}</span>
        <span class="gl-ck-step is-on">2 · {{ $isRtl ? 'الدفع' : 'Checkout' }}</span>
        <span class="gl-ck-step">3 · {{ $isRtl ? 'ابدأ الحصص' : 'Start lessons' }}</span>
                </div>
            </div>
        </section>

  <section class="sana-container gl-ck-wrap">
    <div class="gl-ck-grid">
      <div class="gl-ck-card">
        <h2>{{ __('public.checkout_payment_section_title') }}</h2>
        <p class="gl-ck-card__sub">{{ __('public.checkout_payment_section_desc') }}</p>

                            @if(session('error'))
          <div class="gl-ck-alert gl-ck-alert--err"><i class="fas fa-exclamation-circle"></i><p style="margin:0">{{ session('error') }}</p></div>
                            @endif
                            @if($errors->any())
          <div class="gl-ck-alert gl-ck-alert--err"><ul style="margin:0;padding-inline-start:1.1rem">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                            @endif
                            @if(session('success'))
          <div class="gl-ck-alert gl-ck-alert--ok"><i class="fas fa-check-circle"></i><p style="margin:0">{{ session('success') }}</p></div>
                            @endif
                            @if(session('info'))
          <div class="gl-ck-alert gl-ck-alert--info"><i class="fas fa-info-circle"></i><p style="margin:0">{{ session('info') }}</p></div>
                            @endif

        <div class="gl-ck-panel" id="checkout-discount-panel"
                                     data-quote-url="{{ route('public.course.checkout.quote', $course->id) }}"
                                     data-has-wallet="{{ $checkoutHasWalletBalance ? '1' : '0' }}">
          <h3><i class="fas fa-tags" style="color:#F5B800"></i> {{ $checkoutHasWalletBalance ? ($isRtl ? 'كوبون ورصيد المحفظة' : 'Coupon & wallet') : ($isRtl ? 'كوبون الخصم' : 'Discount coupon') }}</h3>
          <p style="margin:0 0 .75rem;font:600 .78rem Tajawal,sans-serif;color:#5B6577">
            {{ $checkoutHasWalletBalance
              ? ($isRtl ? 'أضف كوبوناً و/أو استخدم رصيد محفظتك. الكوبون أولاً ثم المحفظة.' : 'Apply a coupon and/or wallet credit. Coupon first, then wallet.')
              : ($isRtl ? 'أدخل كوبوناً صالحاً إن وُجد، ثم حدّث السعر.' : 'Enter a valid coupon if you have one, then update the price.') }}
          </p>
          @if($isMonthlyCheckout)
            <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer;margin-bottom:.75rem;padding:.65rem .75rem;border-radius:12px;background:#fff;border:1px solid #E8EEF8">
              <input type="checkbox" name="auto_renew" value="1" form="manual-checkout-form" {{ old('auto_renew', '1') ? 'checked' : '' }} style="margin-top:3px">
              <span>
                <strong style="display:block;color:#0B1220;font:800 .82rem Tajawal,sans-serif">{{ __('public.checkout_auto_renew_label') }}</strong>
                <span style="font:600 .72rem Tajawal,sans-serif;color:#5B6577">{{ __('public.checkout_auto_renew_hint') }}</span>
              </span>
            </label>
                                        @endif
                                        @if($checkoutHasWalletBalance)
            <p style="margin:0 0 .75rem;font:800 .78rem Tajawal,sans-serif;color:#0B3D91">{{ $isRtl ? 'رصيدك:' : 'Balance:' }} {{ number_format($studentWalletBalance, 2) }} {{ __('public.currency_egp') }}</p>
                                        @endif
          <div style="display:grid;gap:.75rem;grid-template-columns:{{ $checkoutHasWalletBalance ? '1fr 1fr' : '1fr' }}">
            <div class="gl-ck-field" style="margin:0">
              <label for="checkout_coupon_code">{{ $isRtl ? 'كود الكوبون' : 'Coupon code' }}</label>
              <input type="text" id="checkout_coupon_code" dir="ltr" autocomplete="off" class="input-checkout" placeholder="SAVE10">
                                        </div>
                                        @if($checkoutHasWalletBalance)
              <div class="gl-ck-field" style="margin:0">
                <label for="checkout_wallet_credit">{{ $isRtl ? 'من المحفظة' : 'From wallet' }}</label>
                <input type="number" id="checkout_wallet_credit" step="0.01" min="0" value="0" max="{{ max(0, $studentWalletBalance ?? 0) }}" class="input-checkout">
                                            </div>
                                        @endif
                                    </div>
          <div style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-top:.85rem">
            <button type="button" id="checkout_apply_pricing" class="btn-acad-ghost"><i class="fas fa-rotate"></i> {{ $isRtl ? 'تحديث السعر' : 'Update price' }}</button>
            <span id="checkout_pricing_msg" class="hidden" style="font:700 .8rem Tajawal,sans-serif;color:#059669"></span>
                                    </div>
                                </div>

                            @if($fawaterakMis)
          <div class="gl-ck-alert gl-ck-alert--err">
                                        <i class="fas fa-exclamation-triangle"></i>
            <div>
              <strong>{{ $isRtl ? 'إعدادات الدفع غير مكتملة' : 'Payment settings incomplete' }}</strong>
              <p style="margin:.35rem 0 0;font-weight:600">{{ $isRtl ? 'تم تفعيل فواتيرك لكن الربط غير مكتمل على الخادم.' : 'Fawaterak is enabled but server credentials are incomplete.' }}</p>
            </div>
                                </div>
          <a href="{{ route('orders.index') }}" class="btn-acad-ghost"><i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ $isRtl ? 'رجوع' : 'Back' }}</a>
                            @elseif($fawaterakActive && $fawaterakIntegration === 'api')
          <div class="gl-ck-alert gl-ck-alert--sky"><i class="fas fa-lock"></i><div><strong>{{ $isRtl ? 'الدفع الإلكتروني' : 'Online payment' }}</strong><p style="margin:.25rem 0 0">{{ $isRtl ? 'اختر وسيلة الدفع ثم تابع.' : 'Choose a payment method and continue.' }}</p></div></div>
          <div id="fawaterk-api-error" class="hidden gl-ck-alert gl-ck-alert--err"></div>
          <div id="fawaterk-api-loading" style="margin-bottom:1rem;font:700 .85rem Tajawal,sans-serif;color:#5B6577"><i class="fas fa-spinner fa-spin" style="color:#0B3D91"></i> {{ $isRtl ? 'جاري تحميل وسائل الدفع...' : 'Loading payment methods…' }}</div>
          <div id="fawaterk-api-methods" class="hidden" style="display:grid;gap:.55rem;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));margin-bottom:1rem"></div>
          <div id="fawaterk-api-wallet-wrap" class="hidden gl-ck-field">
            <label for="fawaterk-api-wallet">{{ $isRtl ? 'رقم المحفظة' : 'Wallet number' }}</label>
                                    <input type="text" id="fawaterk-api-wallet" dir="ltr" class="input-checkout" placeholder="01xxxxxxxxx" autocomplete="tel">
                                </div>
          <div id="fawaterk-api-result" class="hidden gl-ck-panel" style="font:600 .85rem Tajawal,sans-serif;color:#0B1220"></div>
          <button type="button" id="fawaterk-api-pay-btn" disabled class="btn-acad-primary" style="width:100%"><i class="fas fa-lock"></i> {{ $isRtl ? 'متابعة الدفع' : 'Continue payment' }}</button>
                            @elseif($fawaterakActive)
          <div class="gl-ck-alert gl-ck-alert--sky"><i class="fas fa-lock"></i><div><strong>{{ $isRtl ? 'الدفع عبر فواتيرك' : 'Pay with Fawaterak' }}</strong><p style="margin:.25rem 0 0">{{ $isRtl ? 'اختر وسيلة الدفع داخل الإطار. بعد النجاح يُفعَّل الاشتراك تلقائياً.' : 'Choose a method below. Access activates automatically after success.' }}</p></div></div>
          <div id="fawaterk-checkout-error" class="hidden gl-ck-alert gl-ck-alert--err"></div>
          <div id="fawaterkDivId"></div>
          <a href="{{ route('orders.index') }}" class="btn-acad-ghost" style="margin-top:1rem"><i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}"></i> {{ $isRtl ? 'رجوع' : 'Back' }}</a>
                            @else
          <div class="gl-ck-alert gl-ck-alert--info"><i class="fas fa-circle-info"></i><div><strong>{{ $isRtl ? 'الدفع اليدوي' : 'Manual payment' }}</strong><p style="margin:.25rem 0 0">{{ $isRtl ? 'ارفع إيصال التحويل — يُراجع الطلب ثم يُفعَّل.' : 'Upload your transfer receipt — we review, then activate.' }}</p></div></div>
          <form action="{{ route('public.course.checkout.complete', $course->id) }}" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" x-data="{paymentMethod:'bank_transfer'}" id="manual-checkout-form">
                                    @csrf
                                        <input type="hidden" name="coupon_code" id="form_coupon_code" value="{{ old('coupon_code', '') }}">
                                        <input type="hidden" name="wallet_credit" id="form_wallet_credit" value="{{ old('wallet_credit', '0') }}">
            <div class="gl-ck-field">
              <label>{{ $isRtl ? 'طريقة الدفع' : 'Payment method' }}</label>
                                            <select name="payment_method" x-model="paymentMethod" class="input-checkout" required>
                <option value="bank_transfer">{{ $isRtl ? 'تحويل بنكي / محفظة' : 'Bank / wallet transfer' }}</option>
                <option value="cash">{{ $isRtl ? 'دفع نقدي' : 'Cash' }}</option>
                <option value="other">{{ $isRtl ? 'طريقة أخرى' : 'Other' }}</option>
                                            </select>
                                        </div>
            <div class="gl-ck-field" x-show="paymentMethod === 'bank_transfer'" x-cloak>
              <label>{{ $isRtl ? 'حساب التحويل' : 'Transfer account' }}</label>
                                            <select name="wallet_id" class="input-checkout" :required="paymentMethod === 'bank_transfer'">
                <option value="">{{ $isRtl ? 'اختر الحساب' : 'Select account' }}</option>
                                                @foreach(($wallets ?? []) as $wallet)
                  <option value="{{ $wallet->id }}">{{ $wallet->name ?? ($isRtl ? 'حساب منصة' : 'Platform account') }} — {{ $wallet->account_number ?? $wallet->phone ?? '—' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
            <div class="gl-ck-field">
              <label>{{ $isRtl ? 'إيصال الدفع' : 'Payment proof' }}</label>
              <input type="file" name="payment_proof" accept="image/*" required class="input-checkout" style="padding:.65rem">
                                        </div>
            <div class="gl-ck-field">
              <label>{{ $isRtl ? 'ملاحظات (اختياري)' : 'Notes (optional)' }}</label>
              <textarea name="notes" rows="3" class="input-checkout" placeholder="{{ $isRtl ? 'تفاصيل التحويل' : 'Transfer details' }}"></textarea>
                                        </div>
            <div style="display:flex;flex-wrap:wrap;gap:.65rem">
              <button type="submit" :disabled="isSubmitting" class="btn-acad-primary" style="flex:1">
                                            <i class="fas fa-file-upload" x-show="!isSubmitting"></i>
                                            <i class="fas fa-spinner fa-spin" x-show="isSubmitting" x-cloak></i>
                <span x-text="isSubmitting ? '{{ $isRtl ? 'جاري الإرسال...' : 'Submitting…' }}' : '{{ $isRtl ? 'إرسال الطلب' : 'Submit order' }}'"></span>
                                        </button>
              <a href="{{ route('orders.index') }}" class="btn-acad-ghost">{{ $isRtl ? 'إلغاء' : 'Cancel' }}</a>
                                    </div>
                                </form>
                            @endif
                        </div>

      <aside class="gl-ck-card" style="position:sticky;top:1rem">
        <div class="gl-ck-item">
          @if($thumbUrl)
            <img src="{{ $thumbUrl }}" alt="">
          @else
            <div class="gl-ck-item__ph"><i class="fas fa-chalkboard-teacher"></i></div>
          @endif
          <div>
            <h3>{{ $course->title }}</h3>
            <p>
              {{ $course->instructor->name ?? '' }}
              @if($course->academicSubject)
                · {{ $course->academicSubject->name }}
              @endif
            </p>
            @if($isOneToOne)
              <p style="color:#0B3D91;font-weight:800">{{ __('public.private_lesson_duration') }}</p>
            @endif
          </div>
        </div>

        <h2 style="font-size:1rem;margin-bottom:.75rem">{{ $isOneToOne ? __('public.private_packages_label') : __('public.checkout_order_summary_title') }}</h2>

        <div id="checkout-pricing-summary"
             data-base-price="{{ $baseCoursePrice }}"
             data-student-balance="{{ $studentBal }}"
             data-has-course="1"
             data-is-monthly="{{ $isMonthlyCheckout ? '1' : '0' }}">
          @if($isMonthlyCheckout)
            <p class="gl-ck-alert gl-ck-alert--sky" style="margin-bottom:.75rem;padding:.65rem .8rem">{{ __('public.checkout_monthly_notice') }}</p>
          @endif
          <div class="gl-ck-sum-row">
            <span>{{ $isMonthlyCheckout ? __('public.checkout_monthly_price_label') : __('public.checkout_base_price_label') }}</span>
            <strong id="sum-original">{{ number_format($baseCoursePrice, 2) }} <span style="font-size:.75rem;font-weight:700;color:#8A94A6">{{ __('public.currency_egp') }}@if($isMonthlyCheckout)/{{ __('public.per_month') }}@endif</span></strong>
          </div>
          <div class="gl-ck-sum-row hidden" id="sum-coupon-row" style="color:#059669">
            <span>{{ $isRtl ? 'خصم الكوبون' : 'Coupon' }}</span>
            <span id="sum-coupon">—</span>
          </div>
          <div class="gl-ck-sum-row hidden" id="sum-wallet-row" style="color:#0B3D91">
            <span>{{ $isRtl ? 'رصيد المحفظة' : 'Wallet' }}</span>
            <span id="sum-wallet">—</span>
          </div>
          <div class="gl-ck-sum-total">
            <span>{{ $isRtl ? 'المستحق' : 'Due now' }}</span>
            <span id="sum-final">{{ number_format($baseCoursePrice, 2) }} <span style="font-size:.8rem;font-weight:700;color:#8A94A6">{{ __('public.currency_egp') }}</span></span>
                    </div>
                </div>

        <ul class="gl-ck-benefits" style="margin-top:1rem">
          @if($isOneToOne)
            <li><i class="fas fa-check"></i> {{ __('public.private_package_1m_sub') }}</li>
            <li><i class="fas fa-check"></i> {{ $isRtl ? 'معلم خاص ومواعيد مرنة' : 'Private teacher & flexible times' }}</li>
          @elseif($isMonthlyCheckout)
            <li><i class="fas fa-check"></i> {{ __('public.checkout_benefit_monthly_access') }}</li>
          @else
            <li><i class="fas fa-check"></i> {{ __('public.checkout_benefit_lifetime') }}</li>
          @endif
          <li><i class="fas fa-check"></i> {{ __('public.checkout_benefit_support') }}</li>
        </ul>
      </aside>
            </div>
        </section>
    </main>

@include('partials.landing.footer')
@include('public.partials.checkout-scripts')
</body>
</html>
