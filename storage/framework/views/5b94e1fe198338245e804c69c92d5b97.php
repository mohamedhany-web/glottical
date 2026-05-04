<?php
/*
 * Muallimx — JSON-LD Structured Data
 * Usage: @include('partials.seo-jsonld', ['jsonldType' => 'website|course|instructor|about'])
 */
$_jldType    = $jsonldType ?? 'website';
$_siteUrl    = url('/');
$_logoUrl    = asset('images/og-image.jpg');
$_siteName   = 'Muallimx';

// ── Base: WebSite + EducationalOrganization ──────────────────────────────
$_baseGraph = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'         => 'WebSite',
            '@id'           => $_siteUrl . '/#website',
            'url'           => $_siteUrl,
            'name'          => $_siteName,
            'description'   => 'منصة عربية متخصصة في تأهيل وتطوير المعلمين للعمل أونلاين باحتراف',
            'inLanguage'    => ['ar', 'en'],
            'potentialAction' => [
                '@type'        => 'SearchAction',
                'target'       => ['@type' => 'EntryPoint', 'urlTemplate' => url('/courses') . '?search={search_term_string}'],
                'query-input'  => 'required name=search_term_string',
            ],
        ],
        [
            '@type'  => 'EducationalOrganization',
            '@id'    => $_siteUrl . '/#organization',
            'name'   => $_siteName,
            'url'    => $_siteUrl,
            'logo'   => ['@type' => 'ImageObject', 'url' => $_logoUrl, 'width' => 1200, 'height' => 630],
            'sameAs' => [
                'https://twitter.com/Muallimx',
                'https://www.facebook.com/Muallimx',
                'https://www.linkedin.com/company/muallimx',
                'https://www.youtube.com/@Muallimx',
            ],
            'contactPoint' => ['@type' => 'ContactPoint', 'contactType' => 'customer support', 'availableLanguage' => ['Arabic', 'English']],
        ],
    ],
];
$_outputScripts = '<script type="application/ld+json">' . json_encode($_baseGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

// ── Course Page ───────────────────────────────────────────────────────────
if ($_jldType === 'course' && isset($course)) {
    $_thumbnail = !empty($course->thumbnail)
        ? asset('storage/' . str_replace('\\', '/', $course->thumbnail))
        : $_logoUrl;

    $_courseData = [
        '@context'  => 'https://schema.org',
        '@type'     => 'Course',
        '@id'       => url('/course/' . ($course->id ?? 0)) . '#course',
        'name'      => (string)($course->title ?? ''),
        'description' => \Illuminate\Support\Str::limit(strip_tags((string)($course->description ?? '')), 300),
        'url'       => url('/course/' . ($course->id ?? 0)),
        'image'     => $_thumbnail,
        'inLanguage'=> 'ar',
        'courseLanguage' => 'Arabic',
        'provider'  => ['@type' => 'EducationalOrganization', '@id' => $_siteUrl . '/#organization', 'name' => $_siteName],
        'hasCourseInstance' => ['@type' => 'CourseInstance', 'courseMode' => 'online', 'inLanguage' => 'ar'],
    ];
    $catName = $course->courseCategory?->name ?? null;
    if (! empty($catName)) {
        $_courseData['about'] = ['@type' => 'Thing', 'name' => (string) $catName];
    }
    if (isset($course->price) && $course->effectivePurchasePrice() > 0) {
        $_courseData['offers'] = [
            '@type' => 'Offer', 'price' => (string) $course->effectivePurchasePrice(),
            'priceCurrency' => 'USD', 'availability' => 'https://schema.org/InStock',
        ];
    }
    if (!empty($course->instructor) && !empty($course->instructor->name)) {
        $_courseData['instructor'] = ['@type' => 'Person', 'name' => $course->instructor->name];
    }

    $_breadcrumbCourse = [
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'الرئيسية', 'item' => $_siteUrl],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'الكورسات',  'item' => url('/courses')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => (string)($course->title ?? 'كورس'), 'item' => url('/course/' . ($course->id ?? 0))],
        ],
    ];

    $_outputScripts .= '<script type="application/ld+json">' . json_encode($_courseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    $_outputScripts .= '<script type="application/ld+json">' . json_encode($_breadcrumbCourse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

// ── Instructor Profile Page ───────────────────────────────────────────────
if ($_jldType === 'instructor' && isset($profile)) {
    $_instrName = (string)($profile->user->name ?? 'مدرب');
    $_instrBio  = \Illuminate\Support\Str::limit(strip_tags((string)($profile->bio ?? $profile->headline ?? '')), 300);
    $_instrImg  = !empty($profile->user->profile_image ?? null) ? ($profile->user->profile_image_url ?? $_logoUrl) : $_logoUrl;

    try {
        $_instrUrl = route('public.instructors.show', $profile->user ?? $profile);
    } catch (\Exception $_e) {
        $_instrUrl = url('/instructors');
    }

    $_personData = [
        '@type'       => 'Person',
        '@id'         => $_instrUrl . '#person',
        'name'        => $_instrName,
        'description' => $_instrBio,
        'image'       => $_instrImg,
        'url'         => $_instrUrl,
        'jobTitle'    => (string)($profile->headline ?? 'مدرب'),
        'worksFor'    => ['@type' => 'EducationalOrganization', '@id' => $_siteUrl . '/#organization', 'name' => $_siteName],
    ];

    $_instrPageData = [
        '@context'   => 'https://schema.org',
        '@type'      => 'ProfilePage',
        '@id'        => $_instrUrl . '#profile',
        'url'        => $_instrUrl,
        'name'       => $_instrName . ' — مدرب على ' . $_siteName,
        'mainEntity' => $_personData,
    ];

    $_breadcrumbInstr = [
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'الرئيسية', 'item' => $_siteUrl],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'المدربون',  'item' => url('/instructors')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $_instrName,  'item' => $_instrUrl],
        ],
    ];

    $_outputScripts .= '<script type="application/ld+json">' . json_encode($_instrPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    $_outputScripts .= '<script type="application/ld+json">' . json_encode($_breadcrumbInstr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

// ── FAQ Page ──────────────────────────────────────────────────────────────
if ($_jldType === 'faq' && isset($faqs) && method_exists($faqs, 'count') && $faqs->count() > 0) {
    $_faqItems = [];
    foreach ($faqs as $_faqItem) {
        $_faqItems[] = [
            '@type' => 'Question',
            'name'  => strip_tags((string)($_faqItem->question ?? '')),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => \Illuminate\Support\Str::limit(strip_tags((string)($_faqItem->answer ?? '')), 400),
            ],
        ];
    }
    $_faqData = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $_faqItems];
    $_outputScripts .= '<script type="application/ld+json">' . json_encode($_faqData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

// ── About Page ────────────────────────────────────────────────────────────
if ($_jldType === 'about') {
    $_aboutData = [
        '@context'    => 'https://schema.org',
        '@type'       => 'AboutPage',
        'url'         => url('/about'),
        'name'        => 'من نحن — ' . $_siteName,
        'description' => 'تعرف على منصة Muallimx، رسالتنا وقيمنا في تأهيل المعلمين للعمل أونلاين باحتراف',
        'mainEntity'  => [
            '@type'       => 'EducationalOrganization',
            '@id'         => $_siteUrl . '/#organization',
            'name'        => $_siteName,
            'url'         => $_siteUrl,
            'foundingDate'=> '2023',
            'description' => 'منصة عربية متخصصة في تأهيل وتطوير المعلمين للعمل أونلاين',
            'areaServed'  => ['@type' => 'Place', 'name' => 'العالم العربي'],
            'knowsAbout'  => ['تعليم إلكتروني', 'تأهيل المعلمين', 'أدوات AI للتعليم', 'منصات التدريس الأونلاين'],
        ],
    ];
    $_outputScripts .= '<script type="application/ld+json">' . json_encode($_aboutData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}
?>
<?php echo $_outputScripts; ?>

<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\partials\seo-jsonld.blade.php ENDPATH**/ ?>