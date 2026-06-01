@php
    $publicLocale = app()->getLocale();
    $publicRtl = $publicLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $publicLocale }}" dir="{{ $publicRtl ? 'rtl' : 'ltr' }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $seoTitle = trim($__env->yieldContent('title')) ?: (config('app.name') . ' - ' . __('landing.nav.brand'));
        $seoDescription = trim($__env->yieldContent('meta_description')) ?: __('landing.meta.description');
        $seoKeywords = trim($__env->yieldContent('meta_keywords')) ?: 'تعليم ألماني, تعليم إنجليزي, كول سنتر, سوق العمل, ألمانيا, Glottical';
        $seoImage = trim($__env->yieldContent('meta_image')) ?: asset('images/og-image.jpg');
        $seoType = trim($__env->yieldContent('meta_type')) ?: 'website';
        $seoCanonical = trim($__env->yieldContent('canonical_url')) ?: url()->current();
        $seoAltBase = url()->current();
    @endphp
    @include('components.seo-meta', [
        'title' => $seoTitle,
        'description' => $seoDescription,
        'keywords' => $seoKeywords,
        'image' => $seoImage,
        'type' => $seoType,
        'url' => $seoCanonical,
    ])
    <link rel="alternate" hreflang="ar" href="{{ $seoAltBase }}?lang=ar">
    <link rel="alternate" hreflang="en" href="{{ $seoAltBase }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ $seoAltBase }}">
    <meta name="theme-color" content="{{ config('academy-theme.navy') }}">

    @include('partials.favicon-links')

    @php
        $r2PublicBase = config('filesystems.r2_public_url');
        $navLogoPreload = \App\Services\AdminPanelBranding::logoPublicUrl();
    @endphp
    @if(is_string($r2PublicBase) && $r2PublicBase !== '')
        @php
            $r2Host = parse_url($r2PublicBase, PHP_URL_SCHEME).'://'.parse_url($r2PublicBase, PHP_URL_HOST);
        @endphp
        <link rel="dns-prefetch" href="{{ $r2Host }}">
        <link rel="preconnect" href="{{ $r2Host }}" crossorigin>
    @endif
    @if(is_string($navLogoPreload) && $navLogoPreload !== '' && ! str_starts_with($navLogoPreload, 'data:'))
        <link rel="preload" as="image" href="{{ $navLogoPreload }}" fetchpriority="high">
    @endif

    <!-- الخطوط العربية - تحميل غير معطل للرسم (تحسين FCP/LCP) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Resource Hints للأداء -->
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Tailwind CSS (ألوان موحّدة مع الصفحة الرئيسية) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        acad: {
                            blue: '#0B3D91',
                            blueDark: '#072a66',
                            blueSoft: '#E8EEF8',
                            cyan: '#00A3C4',
                            yellow: '#F5B800',
                            yellowSoft: '#FFF8E1',
                            gray: '#F4F6FA',
                            ink: '#1a2d4d',
                            navy: '{{ config('academy-theme.navy') }}',
                            navyMid: '{{ config('academy-theme.navy_mid') }}',
                            neon: '#00d4ff',
                        },
                    },
                    fontFamily: {
                        sans: ['Cairo', 'Tajawal', 'IBM Plex Sans Arabic', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome - محسّن -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    @include('partials.academy-theme-vars')
    @include('partials.public-academy-surface')
    <!-- Custom Styles from welcome.blade.php -->
    @include('layouts.public-styles')
    @stack('styles')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
</head>

<body class="page-academy font-sans antialiased text-white"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">

    <div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>

    @include('components.unified-navbar')

    <!-- Main Content -->
    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- Footer - نفس فوتر الصفحة الرئيسية -->
    @include('components.unified-footer')

    <script>
    (function () {
        function scrollProgress() {
            var s = window.pageYOffset || document.documentElement.scrollTop;
            var h = document.documentElement.scrollHeight - window.innerHeight;
            var p = h > 0 ? (s / h) * 100 : 0;
            var b = document.getElementById('scroll-progress');
            if (b) b.style.width = p + '%';
        }
        window.addEventListener('scroll', scrollProgress, { passive: true });
        scrollProgress();
    })();
    </script>
    @stack('scripts')
</body>
</html>

