
<?php
    $title       = $title       ?? 'Muallimx — منصة تأهيل المعلمين للعمل أونلاين باحتراف';
    $description = $description ?? 'Muallimx منصة عربية متخصصة في تأهيل وتطوير المعلمين للعمل أونلاين — تدريب تطبيقي، أدوات AI للتحضير، مناهج جاهزة، وبناء بروفايل يفتح فرص عمل حقيقية.';
    $keywords    = $keywords    ?? 'تأهيل المعلمين, تدريب المعلمين أونلاين, أدوات AI للمعلم, مولد خطة الدرس, بناء بروفايل المعلم, توظيف المعلمين, دبلومات تعليمية, مناهج تفاعلية, Muallimx';
    $image       = $image       ?? asset('images/og-image.jpg');
    $imageAlt    = $imageAlt    ?? $title;
    $url         = $url         ?? url()->current();
    $type        = $type        ?? 'website';
    $locale      = app()->getLocale();
    $ogLocale    = $locale === 'ar' ? 'ar_AR' : 'en_US';
    $ogLocaleAlt = $locale === 'ar' ? 'en_US' : 'ar_AR';
    $langCode    = $locale === 'ar' ? 'Arabic' : 'English';
?>

<!-- ═══ Primary Meta Tags ═══ -->
<title><?php echo e($title); ?></title>
<meta name="title"          content="<?php echo e($title); ?>">
<meta name="description"    content="<?php echo e($description); ?>">
<meta name="keywords"       content="<?php echo e($keywords); ?>">
<meta name="author"         content="Muallimx">
<meta name="robots"         content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="language"       content="<?php echo e($langCode); ?>">
<meta name="revisit-after"  content="7 days">
<meta name="rating"         content="general">

<!-- ═══ Canonical URL ═══ -->
<link rel="canonical" href="<?php echo e($url); ?>">

<!-- ═══ Open Graph / Facebook ═══ -->
<meta property="og:type"              content="<?php echo e($type); ?>">
<meta property="og:url"               content="<?php echo e($url); ?>">
<meta property="og:title"             content="<?php echo e($title); ?>">
<meta property="og:description"       content="<?php echo e($description); ?>">
<meta property="og:image"             content="<?php echo e($image); ?>">
<meta property="og:image:alt"         content="<?php echo e($imageAlt); ?>">
<meta property="og:image:width"       content="1200">
<meta property="og:image:height"      content="630">
<meta property="og:locale"            content="<?php echo e($ogLocale); ?>">
<meta property="og:locale:alternate"  content="<?php echo e($ogLocaleAlt); ?>">
<meta property="og:site_name"         content="Muallimx">

<!-- ═══ Twitter / X Card ═══ -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@Muallimx">
<meta name="twitter:creator"     content="@Muallimx">
<meta name="twitter:url"         content="<?php echo e($url); ?>">
<meta name="twitter:title"       content="<?php echo e($title); ?>">
<meta name="twitter:description" content="<?php echo e($description); ?>">
<meta name="twitter:image"       content="<?php echo e($image); ?>">
<meta name="twitter:image:alt"   content="<?php echo e($imageAlt); ?>">
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\components\seo-meta.blade.php ENDPATH**/ ?>