<?php
    $publicLocale = app()->getLocale();
    $publicRtl = $publicLocale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($publicLocale); ?>" dir="<?php echo e($publicRtl ? 'rtl' : 'ltr'); ?>" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        $seoTitle = trim($__env->yieldContent('title')) ?: (config('app.name') . ' - ' . __('landing.nav.brand'));
        $seoDescription = trim($__env->yieldContent('meta_description')) ?: __('landing.meta.description');
        $seoKeywords = trim($__env->yieldContent('meta_keywords')) ?: 'تعليم ألماني, تعليم إنجليزي, كول سنتر, سوق العمل, ألمانيا, Glottical';
        $seoImage = trim($__env->yieldContent('meta_image')) ?: asset('images/og-image.jpg');
        $seoType = trim($__env->yieldContent('meta_type')) ?: 'website';
        $seoCanonical = trim($__env->yieldContent('canonical_url')) ?: url()->current();
        $seoAltBase = url()->current();
    ?>
    <?php echo $__env->make('components.seo-meta', [
        'title' => $seoTitle,
        'description' => $seoDescription,
        'keywords' => $seoKeywords,
        'image' => $seoImage,
        'type' => $seoType,
        'url' => $seoCanonical,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="alternate" hreflang="ar" href="<?php echo e($seoAltBase); ?>?lang=ar">
    <link rel="alternate" hreflang="en" href="<?php echo e($seoAltBase); ?>?lang=en">
    <link rel="alternate" hreflang="x-default" href="<?php echo e($seoAltBase); ?>">
    <meta name="theme-color" content="<?php echo e(config('academy-theme.navy')); ?>">

    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $r2PublicBase = config('filesystems.r2_public_url');
        $navLogoPreload = \App\Services\AdminPanelBranding::logoPublicUrl();
    ?>
    <?php if(is_string($r2PublicBase) && $r2PublicBase !== ''): ?>
        <?php
            $r2Host = parse_url($r2PublicBase, PHP_URL_SCHEME).'://'.parse_url($r2PublicBase, PHP_URL_HOST);
        ?>
        <link rel="dns-prefetch" href="<?php echo e($r2Host); ?>">
        <link rel="preconnect" href="<?php echo e($r2Host); ?>" crossorigin>
    <?php endif; ?>
    <?php if(is_string($navLogoPreload) && $navLogoPreload !== '' && ! str_starts_with($navLogoPreload, 'data:')): ?>
        <link rel="preload" as="image" href="<?php echo e($navLogoPreload); ?>" fetchpriority="high">
    <?php endif; ?>

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
                            navy: '<?php echo e(config('academy-theme.navy')); ?>',
                            navyMid: '<?php echo e(config('academy-theme.navy_mid')); ?>',
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

    <?php echo $__env->make('partials.academy-theme-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.public-academy-surface', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- Custom Styles from welcome.blade.php -->
    <?php echo $__env->make('layouts.public-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body class="page-academy font-sans antialiased text-white"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">

    <div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>

    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <main class="flex-1 w-full">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer - نفس فوتر الصفحة الرئيسية -->
    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\glottical\resources\views\layouts\public.blade.php ENDPATH**/ ?>