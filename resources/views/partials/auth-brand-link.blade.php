{{--
  شعار الرابط للرئيسية في صفحات المصادقة: من إعدادات النظام (لوحة التحكم) أو احتياطي حرف M.
  المتغيرات: $size = 'lg'|'sm'، $fallback = 'orange'|'gradient' (gradient لشاشة صغيرة تسجيل الدخول فقط)، $variant = 'light'|'dark' (لون النص والإطار حول الشعار)
--}}
@php
    $logoUrl = $adminPanelLogoUrl ?? null;
    $size = $size ?? 'lg';
    $fallback = $fallback ?? 'orange';
    $variant = $variant ?? 'light';
    $isDark = $variant === 'dark';
    $isSm = $size === 'sm';
    $box = $isSm ? 'w-10 h-10' : 'w-12 h-12';
    $brandText = $isSm ? 'text-xl' : 'text-2xl';
    $mText = $isSm ? 'text-lg' : 'text-xl';
    $mb = $mb ?? ($isSm ? 'mb-8' : 'mb-10');
    $nameClass = $isDark ? 'text-white font-extrabold' : 'text-mx-indigo font-extrabold';
    $logoWrap = $isDark
        ? 'bg-white/10 border border-white/20 shadow-lg shadow-black/20 ring-1 ring-white/10'
        : 'bg-white border border-slate-200/80 shadow-lg shadow-slate-200/40 ring-1 ring-slate-100';
@endphp
<a href="{{ route('home') }}" class="inline-flex items-center gap-3 group {{ $mb }}">
    @if($logoUrl)
        <div class="{{ $box }} rounded-xl flex items-center justify-center overflow-hidden {{ $logoWrap }} group-hover:brightness-110 transition-all">
            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="w-full h-full object-contain p-1" width="48" height="48" loading="eager" decoding="async" fetchpriority="high" onerror="this.onerror=null;this.src='{{ \App\Services\AdminPanelBranding::inlineFallbackDataUri() }}';">
        </div>
    @elseif($fallback === 'gradient' && $isSm)
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-acad-cyan to-acad-blue flex items-center justify-center shadow-lg shadow-cyan-900/30">
            <span class="text-white font-black text-lg">G</span>
        </div>
    @else
        <div class="{{ $box }} rounded-xl {{ $isDark ? 'bg-gradient-to-br from-acad-yellow to-amber-400' : 'bg-[#FB5607]' }} flex items-center justify-center shadow-lg {{ $isDark ? 'shadow-black/30' : 'shadow-orange-500/25' }} group-hover:opacity-95 transition-opacity">
            <span class="text-{{ $isDark ? '[#0B3D91]' : 'white' }} font-black {{ $mText }}">G</span>
        </div>
    @endif
    <span class="{{ $nameClass }} {{ $brandText }}" @if($isSm) style="font-family:Tajawal,sans-serif" @endif>{{ config('app.name', 'Muallimx') }}</span>
</a>
