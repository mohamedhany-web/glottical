{{--
  شعار الرابط للرئيسية في صفحات المصادقة.
  $size = 'lg'|'sm'، $fallback = 'accent'|'gradient'|'orange'، $variant = 'light'|'dark'
--}}
@php
    $logoUrl = $adminPanelLogoUrl ?? null;
    $size = $size ?? 'lg';
    $fallback = $fallback ?? 'accent';
    $variant = $variant ?? 'light';
    $isDark = $variant === 'dark';
    $isSm = $size === 'sm';
    $box = $isSm ? 'size-10' : 'size-12';
    $brandText = $isSm ? 'text-xl' : 'text-2xl';
    $mText = $isSm ? 'text-lg' : 'text-xl';
    $mb = $mb ?? ($isSm ? 'mb-8' : 'mb-10');
    $nameClass = $isDark ? 'font-semibold text-white' : 'font-semibold text-ink';
    $logoWrap = $isDark
        ? 'bg-white/10 border border-white/15 shadow-soft ring-1 ring-white/10'
        : 'bg-surface border border-line shadow-soft';
@endphp
<a href="{{ route('home') }}" class="inline-flex items-center gap-3 group {{ $mb }}">
    @if ($logoUrl)
        <div class="{{ $box }} rounded-xl flex items-center justify-center overflow-hidden {{ $logoWrap }} transition group-hover:opacity-95">
            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="h-full w-full object-contain p-1" width="48" height="48" loading="eager" decoding="async" fetchpriority="high" onerror="this.onerror=null;this.src={{ \Illuminate\Support\Js::from(\App\Services\AdminPanelBranding::inlineFallbackDataUri()) }};">
        </div>
    @else
        <div class="{{ $box }} flex items-center justify-center rounded-xl bg-accent text-white shadow-[0_10px_24px_rgba(15,92,87,0.28)] transition group-hover:bg-[#0d4f4a]">
            <span class="font-bold {{ $mText }}">G</span>
        </div>
    @endif
    <span class="{{ $nameClass }} {{ $brandText }} tracking-tight">{{ config('app.name') }}</span>
</a>
