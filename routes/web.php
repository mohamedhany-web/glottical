<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storage files (صور وملفات) - يجب أن يكون أول Route لضمان عدم اعتراضه
| يعمل عند عدم وجود symlink public/storage على الاستضافة
|--------------------------------------------------------------------------
*/
Route::get('/media/{path}', [\App\Http\Controllers\StorageFileController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.media')
    ->middleware('web');

Route::get('/storage/{path}', [\App\Http\Controllers\StorageFileController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.file')
    ->middleware('web');

Route::post('/webhooks/fawaterak', [\App\Http\Controllers\Webhooks\FawaterakWebhookController::class, 'handle'])
    ->name('webhooks.fawaterak')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Muallimx Whiteboard (أصول اللوحة) — تمرير عبر Laravel
| يعمل عندما لا يُخدم public/vendor مباشرة (جذر الموقع ليس public أو قواعد .htaccess)
|--------------------------------------------------------------------------
*/
Route::get('/mx-vendor/excalidraw/{path}', function (string $path) {
    $path = rawurldecode($path);
    $path = str_replace('..', '', $path);
    $path = ltrim(str_replace('\\', '/', $path), '/');

    $basePath = public_path('vendor/excalidraw');
    $filePath = $basePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

    if (! @file_exists($filePath) || ! @is_file($filePath)) {
        abort(404, 'Whiteboard asset not found');
    }

    $realPath = @realpath($filePath) ?: $filePath;
    $allowedPath = @realpath($basePath) ?: $basePath;
    $normalizedRealPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath);
    $normalizedAllowedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $allowedPath);

    if ($allowedPath === '' || strpos($normalizedRealPath, $normalizedAllowedPath) !== 0) {
        abort(404, 'Access denied');
    }

    if (! @is_readable($realPath)) {
        abort(403, 'File not readable');
    }

    $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
    // أولوية للامتداد: على Windows/بعض الاستضافات يعيد mime_content_type لـ .js قيمة text/plain
    // ومع X-Content-Type-Options: nosniff لا يُنفَّذ السكربت ويبقى مكتبة اللوحة غير معرّفة.
    $mimeType = match ($extension) {
        'js', 'mjs' => 'application/javascript; charset=utf-8',
        'css' => 'text/css; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'map' => 'application/json; charset=utf-8',
        'txt' => 'text/plain; charset=utf-8',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        default => null,
    };
    if ($mimeType === null) {
        $mimeType = @mime_content_type($realPath) ?: 'application/octet-stream';
    }

    return response()->file($realPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('path', '.*')->name('mx.vendor.excalidraw')->middleware('web');

// Sitemap Route — Muallimx SEO
Route::get('/sitemap.xml', function () {
    $xmlEscape = static fn (?string $value): string => htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $urls = [];

    // الصفحة الرئيسية
    $urls[] = ['loc' => url('/'), 'lastmod' => now()->toDateString(), 'changefreq' => 'daily', 'priority' => '1.0'];

    // الصفحات العامة الثابتة
    $staticPages = [
        ['url' => '/courses',      'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => '/instructors',  'priority' => '0.8', 'changefreq' => 'weekly'],
        ['url' => '/pricing',      'priority' => '0.8', 'changefreq' => 'weekly'],
        ['url' => '/about',        'priority' => '0.8', 'changefreq' => 'monthly'],
        ['url' => '/contact',      'priority' => '0.7', 'changefreq' => 'monthly'],
        ['url' => '/portfolio',    'priority' => '0.7', 'changefreq' => 'weekly'],
        ['url' => '/services',     'priority' => '0.75', 'changefreq' => 'weekly'],
        ['url' => '/faq',          'priority' => '0.7', 'changefreq' => 'monthly'],
        ['url' => '/team',         'priority' => '0.6', 'changefreq' => 'monthly'],
        ['url' => '/events',       'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => '/testimonials', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['url' => '/partners',     'priority' => '0.6', 'changefreq' => 'monthly'],
        ['url' => '/media',        'priority' => '0.6', 'changefreq' => 'weekly'],
        ['url' => '/help',         'priority' => '0.6', 'changefreq' => 'monthly'],
        ['url' => '/certificates', 'priority' => '0.5', 'changefreq' => 'weekly'],
        ['url' => '/terms',        'priority' => '0.4', 'changefreq' => 'yearly'],
        ['url' => '/privacy',      'priority' => '0.4', 'changefreq' => 'yearly'],
        ['url' => '/refund',       'priority' => '0.4', 'changefreq' => 'yearly'],
    ];

    foreach ($staticPages as $page) {
        $urls[] = [
            'loc' => url($page['url']),
            'lastmod' => now()->toDateString(),
            'changefreq' => $page['changefreq'],
            'priority' => $page['priority'],
        ];
    }

    // الكورسات النشطة مع صورة (Image Sitemap)
    try {
        $courses = \App\Models\AdvancedCourse::where('is_active', true)
            ->select('id', 'title', 'thumbnail', 'description', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($courses as $course) {
            $entry = [
                'loc' => url('/course/'.$course->id),
                'lastmod' => optional($course->updated_at)->format('Y-m-d') ?: now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
            if ($course->thumbnail) {
                $entry['image_loc'] = storage_asset(str_replace('\\', '/', $course->thumbnail));
                $entry['image_title'] = $course->title ?? '';
                $entry['image_caption'] = \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 100);
            }
            $urls[] = $entry;
        }
    } catch (\Exception $e) {
    }

    // المدربون النشطون مع صورة
    try {
        $instructors = \App\Models\User::whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->select('id', 'name', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get();

        foreach ($instructors as $instructor) {
            $urls[] = [
                'loc' => route('public.instructors.show', $instructor),
                'lastmod' => optional($instructor->updated_at)->format('Y-m-d') ?: now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }
    } catch (\Exception $e) {
    }

    // مقالات Media المنشورة
    try {
        $mediaItems = \App\Models\Media::where('is_published', true)
            ->select('id', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(500)
            ->get();

        foreach ($mediaItems as $item) {
            $urls[] = [
                'loc' => route('public.media.show', $item),
                'lastmod' => optional($item->updated_at)->format('Y-m-d') ?: now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        }
    } catch (\Exception $e) {
    }

    // صفحات الخدمات النشطة
    try {
        $siteServices = \App\Models\SiteService::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderBy('sort_order')
            ->get();
        foreach ($siteServices as $svc) {
            $urls[] = [
                'loc' => route('public.services.show', $svc->slug),
                'lastmod' => optional($svc->updated_at)->format('Y-m-d') ?: now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.65',
            ];
        }
    } catch (\Exception $e) {
    }

    // بناء XML مع دعم Image Sitemap
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.PHP_EOL;
    $sitemap .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'.PHP_EOL;

    foreach ($urls as $entry) {
        $sitemap .= '  <url>'.PHP_EOL;
        $sitemap .= '    <loc>'.$xmlEscape($entry['loc']).'</loc>'.PHP_EOL;
        $sitemap .= '    <lastmod>'.$xmlEscape($entry['lastmod']).'</lastmod>'.PHP_EOL;
        $sitemap .= '    <changefreq>'.$xmlEscape($entry['changefreq']).'</changefreq>'.PHP_EOL;
        $sitemap .= '    <priority>'.$xmlEscape($entry['priority']).'</priority>'.PHP_EOL;
        if (! empty($entry['image_loc'])) {
            $sitemap .= '    <image:image>'.PHP_EOL;
            $sitemap .= '      <image:loc>'.$xmlEscape($entry['image_loc']).'</image:loc>'.PHP_EOL;
            if (! empty($entry['image_title'])) {
                $sitemap .= '      <image:title>'.$xmlEscape($entry['image_title']).'</image:title>'.PHP_EOL;
            }
            if (! empty($entry['image_caption'])) {
                $sitemap .= '      <image:caption>'.$xmlEscape($entry['image_caption']).'</image:caption>'.PHP_EOL;
            }
            $sitemap .= '    </image:image>'.PHP_EOL;
        }
        $sitemap .= '  </url>'.PHP_EOL;
    }
    $sitemap .= '</urlset>';

    return response($sitemap, 200)
        ->header('Content-Type', 'application/xml')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('sitemap');

// الصفحة الرئيسية (Home) - الترجمة عبر SetLocale في مجموعة web
Route::get('/', [\App\Http\Controllers\Public\LandingController::class, 'index'])->name('home');

// الصفحات العامة
Route::get('/about', [\App\Http\Controllers\Public\PageController::class, 'about'])->name('public.about');
Route::get('/faq', [\App\Http\Controllers\Public\PageController::class, 'faq'])->name('public.faq');
Route::get('/terms', [\App\Http\Controllers\Public\PageController::class, 'terms'])->name('public.terms');
Route::get('/privacy', [\App\Http\Controllers\Public\PageController::class, 'privacy'])->name('public.privacy');
Route::get('/pricing', [\App\Http\Controllers\Public\PageController::class, 'pricing'])->name('public.pricing');
Route::get('/pricing/checkout/{plan}', [\App\Http\Controllers\Public\SubscriptionCheckoutController::class, 'show'])->name('public.subscription.checkout')->where('plan', 'teacher_starter|teacher_pro');
Route::post('/pricing/checkout', [\App\Http\Controllers\Public\SubscriptionCheckoutController::class, 'store'])->name('public.subscription.checkout.store');
Route::post('/pricing/checkout/{plan}/fawaterak/prepare', [\App\Http\Controllers\Public\SubscriptionCheckoutController::class, 'fawaterakPrepare'])
    ->middleware('auth')
    ->name('public.subscription.checkout.fawaterak.prepare')
    ->where('plan', 'teacher_starter|teacher_pro');
Route::get('/pricing/checkout/{plan}/fawaterak/methods', [\App\Http\Controllers\Public\SubscriptionCheckoutController::class, 'fawaterakPaymentMethods'])
    ->middleware('auth')
    ->name('public.subscription.checkout.fawaterak.methods')
    ->where('plan', 'teacher_starter|teacher_pro');
Route::post('/pricing/checkout/{plan}/fawaterak/pay', [\App\Http\Controllers\Public\SubscriptionCheckoutController::class, 'fawaterakPay'])
    ->middleware('auth')
    ->name('public.subscription.checkout.fawaterak.pay')
    ->where('plan', 'teacher_starter|teacher_pro');
Route::get('/team', [\App\Http\Controllers\Public\PageController::class, 'team'])->name('public.team');
Route::get('/certificates', [\App\Http\Controllers\Public\PageController::class, 'certificates'])->name('public.certificates');
Route::get('/certificates/verify', [\App\Http\Controllers\Public\CertificateVerificationController::class, 'verify'])->name('public.certificates.verify');
Route::get('/certificates/verify/{code}', [\App\Http\Controllers\Public\CertificateVerificationController::class, 'verify'])->name('public.certificates.verify.code');
Route::get('/help', [\App\Http\Controllers\Public\PageController::class, 'help'])->name('public.help');
Route::get('/refund', [\App\Http\Controllers\Public\PageController::class, 'refund'])->name('public.refund');
Route::get('/testimonials', [\App\Http\Controllers\Public\PageController::class, 'testimonials'])->name('public.testimonials');
Route::get('/events', [\App\Http\Controllers\Public\PageController::class, 'events'])->name('public.events');
Route::get('/partners', [\App\Http\Controllers\Public\PageController::class, 'partners'])->name('public.partners');

// Portfolio (معرض أعمال الطلاب)
Route::get('/portfolio', [\App\Http\Controllers\Public\PortfolioController::class, 'index'])->name('public.portfolio.index');
Route::get('/portfolio/teacher/{id}', [\App\Http\Controllers\Public\PortfolioController::class, 'showTeacher'])->name('public.portfolio.teacher')->where('id', '[0-9]+');
Route::get('/portfolio/{id}', [\App\Http\Controllers\Public\PortfolioController::class, 'show'])->name('public.portfolio.show')->where('id', '[0-9]+');

// صفحة الخدمات (محتوى من لوحة الإدارة)
Route::get('/services', [\App\Http\Controllers\Public\SiteServiceController::class, 'index'])->name('public.services.index');
Route::get('/services/{siteService}', [\App\Http\Controllers\Public\SiteServiceController::class, 'show'])->name('public.services.show');

// تم إيقاف مجتمع البيانات والذكاء الاصطناعي (مسابقات، داتاسيت، مجتمع) بالكامل، لذا أزيلت جميع مساراته.

// Muallimx Classroom — دخول الضيوف برابط/كود (بدون تسجيل دخول)
Route::get('/classroom/join/{code}', [\App\Http\Controllers\ClassroomJoinController::class, 'show'])->name('classroom.join')->where('code', '[A-Za-z0-9]+');
Route::post('/classroom/join/{code}/enter', [\App\Http\Controllers\ClassroomJoinController::class, 'enter'])->name('classroom.join.enter')->where('code', '[A-Za-z0-9]+');
Route::post('/classroom/join/{code}/heartbeat', [\App\Http\Controllers\ClassroomJoinController::class, 'heartbeat'])->name('classroom.join.heartbeat')->where('code', '[A-Za-z0-9]+');
Route::post('/classroom/join/{code}/leave', [\App\Http\Controllers\ClassroomJoinController::class, 'leave'])->name('classroom.join.leave')->where('code', '[A-Za-z0-9]+');
Route::post('/classroom/join/{code}/share-annotation', [\App\Http\Controllers\ClassroomJoinController::class, 'pushShareAnnotation'])
    ->middleware('throttle:90,1')
    ->name('classroom.join.share-annotation')
    ->where('code', '[A-Za-z0-9]+');

// التواصل
Route::get('/contact', [\App\Http\Controllers\Public\ContactController::class, 'index'])->name('public.contact');
Route::post('/contact', [\App\Http\Controllers\Public\ContactController::class, 'store'])->name('public.contact.store');

// معرض الصور والفيديوهات
Route::get('/media', [\App\Http\Controllers\Public\MediaController::class, 'index'])->name('public.media.index');
Route::get('/media/{media}', [\App\Http\Controllers\Public\MediaController::class, 'show'])->name('public.media.show');

// صفحة الكورسات العامة (?subject=id & ?delivery=one_to_one|group)
Route::get('/courses', [\App\Http\Controllers\Public\CoursesController::class, 'index'])->name('public.courses');

// صفحة المدربين (الملفات التعريفية المعتمدة)
Route::get('/instructors', [\App\Http\Controllers\Public\InstructorController::class, 'index'])->name('public.instructors.index');
Route::get('/instructors/{instructor}', [\App\Http\Controllers\Public\InstructorController::class, 'show'])->name('public.instructors.show');

// صفحة تفاصيل الكورس العامة
Route::get('/course/{id}', [\App\Http\Controllers\Public\CourseShowController::class, 'show'])
    ->where('id', '[0-9]+')
    ->name('public.course.show');

// سكربت الدفع عبر نطاق الموقع — مسار محايد (قوائم الحجب تعرّف غالباً /fawaterk/)
Route::redirect('/fawaterk/plugin.min.js', '/js/checkout-pay-widget.v1.js', 301);
Route::get('/js/checkout-pay-widget.v1.js', \App\Http\Controllers\Public\FawaterkPluginController::class)
    ->middleware('throttle:240,1')
    ->withoutMiddleware([
        \App\Http\Middleware\CheckActiveStatus::class,
        \App\Http\Middleware\SetLocale::class,
    ])
    ->name('public.fawaterk.plugin');

// صفحة إتمام الطلب (Checkout)
Route::get('/course/{courseId}/checkout', [\App\Http\Controllers\Public\CheckoutController::class, 'show'])
    ->middleware('auth')
    ->name('public.course.checkout');

Route::post('/course/{courseId}/checkout/complete', [\App\Http\Controllers\Public\CheckoutController::class, 'complete'])
    ->middleware('auth')
    ->name('public.course.checkout.complete');

Route::post('/course/{courseId}/checkout/quote', [\App\Http\Controllers\Public\CheckoutController::class, 'quoteCourseCheckout'])
    ->middleware('auth')
    ->name('public.course.checkout.quote');

// التوجيه لبوابة الدفع كاشير (كورس)
Route::post('/course/{courseId}/checkout/kashier', [\App\Http\Controllers\Public\CheckoutController::class, 'redirectToKashier'])
    ->middleware('auth')
    ->name('public.course.checkout.kashier');

Route::post('/course/{courseId}/checkout/fawaterak/prepare', [\App\Http\Controllers\Public\CheckoutController::class, 'fawaterakPrepare'])
    ->middleware('auth')
    ->name('public.course.checkout.fawaterak.prepare');

Route::get('/course/{courseId}/checkout/fawaterak/methods', [\App\Http\Controllers\Public\CheckoutController::class, 'fawaterakPaymentMethods'])
    ->middleware('auth')
    ->name('public.course.checkout.fawaterak.methods');

Route::post('/course/{courseId}/checkout/fawaterak/pay', [\App\Http\Controllers\Public\CheckoutController::class, 'fawaterakPay'])
    ->middleware('auth')
    ->name('public.course.checkout.fawaterak.pay');

// تسجيل مجاني للكورسات المجانية
Route::post('/course/{courseId}/enroll-free', [\App\Http\Controllers\Public\CheckoutController::class, 'enrollFree'])
    ->middleware('auth')
    ->name('public.course.enroll.free');

// المسارات التعليمية
Route::get('/learning-paths', [\App\Http\Controllers\Public\LearningPathController::class, 'index'])->name('public.learning-paths.index');
Route::get('/learning-path/{slug}', [\App\Http\Controllers\Public\LearningPathController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.show');
Route::get('/learning-path/{slug}/checkout', [\App\Http\Controllers\Public\CheckoutController::class, 'showLearningPath'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.checkout');
Route::post('/learning-path/{slug}/checkout/complete', [\App\Http\Controllers\Public\CheckoutController::class, 'completeLearningPath'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.checkout.complete');
Route::post('/learning-path/{slug}/checkout/kashier', [\App\Http\Controllers\Public\CheckoutController::class, 'redirectToKashierLearningPath'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.checkout.kashier');

Route::get('/checkout/kashier/callback', [\App\Http\Controllers\Public\CheckoutController::class, 'kashierCallback'])
    ->name('public.checkout.kashier.callback');

Route::get('/checkout/fawaterak/{status}', [\App\Http\Controllers\Public\CheckoutController::class, 'fawaterakReturn'])
    ->middleware('auth')
    ->where('status', 'success|fail|pending')
    ->name('public.checkout.fawaterak.return');

Route::post('/learning-path/{slug}/enroll-free', [\App\Http\Controllers\Public\CheckoutController::class, 'enrollFreeLearningPath'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.enroll.free');
Route::post('/learning-path/{slug}/enroll', [\App\Http\Controllers\Public\LearningPathController::class, 'enroll'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.enroll');

// صفحة تفاصيل الباقة (للتوافق مع الروابط القديمة)
Route::get('/package/{slug}', function ($slug) {
    $package = \App\Models\Package::where('slug', $slug)
        ->where('is_active', true)
        ->with(['courses' => function ($query) {
            $query->where('is_active', true)
                ->with(['academicSubject', 'academicYear'])
                ->withCount('lessons');
        }])
        ->firstOrFail();

    // باقات ذات صلة
    $relatedPackages = \App\Models\Package::where('is_active', true)
        ->where('id', '!=', $package->id)
        ->withCount('courses')
        ->limit(3)
        ->get();

    return view('package-show', compact('package', 'relatedPackages'));
})->name('public.package.show');

// مسارات المصادقة - محمية بحيث لا يمكن الوصول إليها إذا كان المستخدم مسجل دخول
Route::middleware(['guest', 'guest-only'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,15'); // 20 طلب كل 15 دقيقة — يتضمن الدخول + إعادة المحاولة مع 2FA
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // Rate limiting للتسجيل: 5 محاولات في الدقيقة من نفس IP
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    // نسيت كلمة المرور: طلب رابط إعادة التعيين + صفحة تعيين كلمة مرور جديدة
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
    // نفس جلسة «ضيف» مثل الدخول حتى لا تُفقد بيانات خطوة 2FA بعد إعادة التوجيه
    Route::get('/2fa/challenge', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showChallenge'])
        ->middleware('throttle:60,1')
        ->name('two-factor.challenge');
    Route::post('/2fa/verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verifyChallenge'])
        ->middleware('throttle:30,1')
        ->name('two-factor.verify');
});

// تسجيل الخروج - يجب أن يكون المستخدم مسجل دخول
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// إعداد المصادقة الثنائية (TOTP) — يظهر فمن يُشمَّل بـ requiresTwoFactor (حالياً أدمن عند تفعيل الإلزام)
Route::middleware(['auth'])->prefix('2fa')->name('two-factor.')->group(function () {
    Route::get('/setup', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showSetup'])->name('setup');
    Route::post('/enable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('enable');
    Route::post('/disable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('disable');
});

// =========================
// Public Webhooks / API callbacks (no auth, no CSRF)
// =========================
// ويب هوك تسجيل جلسات البث (Jibri يرفع إلى R2 ثم يستدعي هذا الرابط مع X-Webhook-Token)
Route::post('/api/live-recordings/register', [\App\Http\Controllers\Api\LiveRecordingWebhookController::class, 'register'])
    ->name('api.live-recordings.register');

// Callback من n8n لتحديث تقرير الجلسة (يتطلب X-N8N-Token)
Route::patch('/api/n8n/live-session-reports/{report}', [\App\Http\Controllers\Api\N8nLiveSessionReportController::class, 'update'])
    ->name('api.n8n.live-session-reports.update');
Route::post('/api/n8n/live-session-reports/{report}', [\App\Http\Controllers\Api\N8nLiveSessionReportController::class, 'update'])
    ->name('api.n8n.live-session-reports.update.post');

// Callback من n8n لتقرير اجتماع Classroom (يتطلب X-N8N-Token)
Route::patch('/api/n8n/classroom-meeting-reports/{report}', [\App\Http\Controllers\Api\N8nClassroomMeetingReportController::class, 'update'])
    ->name('api.n8n.classroom-meeting-reports.update');
Route::post('/api/n8n/classroom-meeting-reports/{report}', [\App\Http\Controllers\Api\N8nClassroomMeetingReportController::class, 'update'])
    ->name('api.n8n.classroom-meeting-reports.update.post');

// مسارات لوحة التحكم - محمية بالتأكد من تسجيل الدخول ومنع الجلسات المتزامنة
Route::middleware(['auth', 'prevent-concurrent'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // مسارات الطلاب
    Route::get('/academic-years', [\App\Http\Controllers\Student\AcademicYearController::class, 'index'])->name('academic-years');
    Route::get('/academic-years/{academicYear}/subjects', [\App\Http\Controllers\Student\AcademicYearController::class, 'subjects'])->name('academic-years.subjects');
    Route::get('/subjects/{academicSubject}/courses', [\App\Http\Controllers\Student\SubjectController::class, 'courses'])->name('subjects.courses');
    Route::get('/courses/{advancedCourse}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');

    // كورساتي المفعلة - محمية للطلاب فقط
    Route::middleware(['role:student'])->group(function () {
        Route::get('/my-courses', [\App\Http\Controllers\Student\MyCourseController::class, 'index'])->name('my-courses.index');
        Route::get('/my-courses/{course}', [\App\Http\Controllers\Student\MyCourseController::class, 'show'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.show');

        // المسار التعليمي ملغى — توجيه إلى لوحة التحكم
        Route::get('/student/learning-path/{slug}', function () {
            return redirect()->route('dashboard', [], 302);
        })->name('student.learning-path.show');
        Route::get('/my-courses/{course}/learn', [\App\Http\Controllers\Student\MyCourseController::class, 'learn'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.learn');
        Route::get('/my-courses/{course}/lectures/{lecture}', [\App\Http\Controllers\Student\MyCourseController::class, 'getLectureData'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.show');
        Route::get('/my-courses/{course}/lectures/{lecture}/materials/{material}/download', [\App\Http\Controllers\Student\MyCourseController::class, 'downloadLectureMaterial'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.material.download');
        Route::post('/my-courses/{course}/lectures/{lecture}/video-questions/{videoQuestion}/answer', [\App\Http\Controllers\Student\MyCourseController::class, 'submitLectureVideoQuestionAnswer'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.video-question.answer');
        Route::post('/my-courses/{course}/lectures/{lecture}/progress', [\App\Http\Controllers\Student\MyCourseController::class, 'updateLectureProgress'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lectures.progress');
        Route::get('/my-courses/{course}/lessons/{lesson}/watch', [\App\Http\Controllers\Student\MyCourseController::class, 'watchLesson'])
            ->middleware([\App\Http\Middleware\VideoProtectionMiddleware::class, 'ownership:course,course'])
            ->name('my-courses.lesson.watch');
        Route::post('/my-courses/{course}/lessons/{lesson}/progress', [\App\Http\Controllers\Student\MyCourseController::class, 'updateLessonProgress'])
            ->middleware(['ownership:course,course'])
            ->name('my-courses.lesson.progress');

    });

    // الإحالات (طلاب فقط)
    Route::middleware(['role:student'])->group(function () {
        Route::get('/referrals', [\App\Http\Controllers\Student\ReferralController::class, 'index'])->name('referrals.index');
        Route::post('/referrals/copy-link', [\App\Http\Controllers\Student\ReferralController::class, 'copyLink'])->name('referrals.copy-link');
    });

    // API للتحقق من الكوبون
    Route::post('/api/validate-coupon', [\App\Http\Controllers\Student\CouponController::class, 'validateCoupon'])->name('api.validate-coupon');

    // API لمعلومات الفيديو
    Route::post('/api/video/info', [\App\Http\Controllers\Api\VideoInfoController::class, 'getInfo'])->name('api.video.info');

    // API للدروس - محمية بالتأكد من التسجيل
    Route::get('/api/lessons/{lesson}', function (\App\Models\CourseLesson $lesson) {
        $user = auth()->user();

        // التحقق من أن المستخدم طالب
        if (! $user->isStudent()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        // التحقق من أن المستخدم مسجل في الكورس
        if (! $user->isEnrolledIn($lesson->advanced_course_id)) {
            return response()->json(['error' => 'غير مصرح - غير مسجل في الكورس'], 403);
        }

        $progress = $lesson->progress()->where('user_id', $user->id)->first();

        return response()->json([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->description,
            'content' => $lesson->content,
            'type' => $lesson->type,
            'video_url' => $lesson->video_url ? trim($lesson->video_url) : null,
            'duration_minutes' => $lesson->duration_minutes,
            'attachments' => $lesson->attachments ? json_decode($lesson->attachments, true) : null,
            'progress' => $progress ? [
                'is_completed' => (bool) $progress->is_completed,
                'progress_percent' => (int) ($progress->progress_percent ?? 0),
                'watch_time' => (int) ($progress->watch_time ?? 0),
            ] : null,
        ]);
    });

    // API للطلاب المسجلين في الكورس - محمية بـ role middleware
    Route::get('/api/courses/{course}/students', function ($course) {
        $instructor = auth()->user();

        // التحقق من أن المستخدم مدرب
        if (! $instructor->isInstructor()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        // التحقق من أن الكورس يخص المدرب
        $advancedCourse = \App\Models\AdvancedCourse::where('id', $course)
            ->where('instructor_id', $instructor->id)
            ->firstOrFail();

        // جلب الطلاب المسجلين في الكورس
        $enrollments = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $course)
            ->where('status', 'active')
            ->with('user')
            ->get();

        $students = $enrollments->map(function ($enrollment) {
            $user = $enrollment->user;

            return [
                'id' => $user->id,
                'name' => $user->name ?? $user->full_name ?? ($user->first_name.' '.$user->last_name),
                'full_name' => $user->full_name ?? ($user->first_name.' '.$user->last_name),
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'email' => $user->email,
            ];
        });

        return response()->json([
            'students' => $students,
            'count' => $students->count(),
        ]);
    })->middleware(['auth', 'role:instructor|teacher']);

    // نظام الطلبات - محمي للطلاب فقط
    Route::middleware(['role:student'])->group(function () {
        Route::post('/courses/{advancedCourse}/order', [\App\Http\Controllers\Student\OrderController::class, 'store'])->name('courses.order');
        Route::get('/orders', [\App\Http\Controllers\Student\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Student\OrderController::class, 'show'])
            ->middleware(['ownership:order,order'])
            ->name('orders.show');
    });

    // امتحانات الطلاب - محمية للطلاب فقط
    Route::prefix('exams')->name('student.exams.')->middleware(['role:student'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('index');
        Route::get('/{exam}', [\App\Http\Controllers\Student\ExamController::class, 'show'])->name('show');
        Route::post('/{exam}/start', [\App\Http\Controllers\Student\ExamController::class, 'start'])->name('start');
        Route::get('/{exam}/attempts/{attempt}/take', [\App\Http\Controllers\Student\ExamController::class, 'take'])
            ->middleware(\App\Http\Middleware\VideoProtectionMiddleware::class)
            ->name('take');
        Route::post('/{exam}/attempts/{attempt}/save-answer', [\App\Http\Controllers\Student\ExamController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/{exam}/attempts/{attempt}/submit', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('submit');
        Route::get('/{exam}/attempts/{attempt}/result', [\App\Http\Controllers\Student\ExamController::class, 'result'])->name('result');
        Route::post('/{exam}/attempts/{attempt}/tab-switch', [\App\Http\Controllers\Student\ExamController::class, 'logTabSwitch'])->name('tab-switch');
    });

    // صفحات الطلاب - محمية للطلاب فقط
    Route::middleware(['role:student'])->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/settings', [\App\Http\Controllers\Student\SettingsController::class, 'index'])->name('settings');
        Route::get('/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{notification}/go', [\App\Http\Controllers\Student\NotificationController::class, 'go'])
            ->name('notifications.go');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'show'])
            ->middleware(['ownership:notification,notification'])
            ->name('notifications.show');
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])
            ->middleware(['ownership:notification,notification'])
            ->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'destroy'])
            ->middleware(['ownership:notification,notification'])
            ->name('notifications.destroy');
        Route::post('/notifications/cleanup', [\App\Http\Controllers\Student\NotificationController::class, 'cleanup'])->name('notifications.cleanup');
        Route::get('/api/notifications/unread-count', [\App\Http\Controllers\Student\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/api/notifications/recent', [\App\Http\Controllers\Student\NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::get('/calendar', [\App\Http\Controllers\Student\CalendarController::class, 'index'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Student\CalendarController::class, 'getEvents'])->name('calendar.events');

        Route::get('/consultations', [\App\Http\Controllers\Student\ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/request/{instructor}', [\App\Http\Controllers\Student\ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('/consultations/request/{instructor}', [\App\Http\Controllers\Student\ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/{consultation}', [\App\Http\Controllers\Student\ConsultationController::class, 'show'])->name('consultations.show');
        Route::post('/consultations/{consultation}/report-payment', [\App\Http\Controllers\Student\ConsultationController::class, 'reportPayment'])->name('consultations.report-payment');
        // التسويق الشخصي / البورتفوليو للطالب (إدارة المشاريع)
        Route::prefix('my-portfolio')->name('student.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'index'])->name('portfolio.index');
            Route::get('/profile', [\App\Http\Controllers\Student\PortfolioProfileController::class, 'edit'])->name('portfolio.profile.edit');
            Route::put('/profile', [\App\Http\Controllers\Student\PortfolioProfileController::class, 'update'])->name('portfolio.profile.update');
            Route::get('/create', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'create'])->name('portfolio.create');
            Route::post('/', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'store'])->name('portfolio.store');
            Route::get('/{project}', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'show'])
                ->name('portfolio.show');
            Route::get('/{project}/edit', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'edit'])
                ->name('portfolio.edit');
            Route::put('/{project}', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'update'])
                ->name('portfolio.update');
            Route::delete('/{project}', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'destroy'])
                ->name('portfolio.destroy');
            Route::delete('/{project}/images/{image}', [\App\Http\Controllers\Student\PortfolioProjectController::class, 'destroyImage'])
                ->name('portfolio.images.destroy');
        });
        // صفحة اشتراكي (عرض الباقة الحالية ومدة التفاعيل والانتهاء)
        Route::get('/my-subscription', [\App\Http\Controllers\Student\MySubscriptionController::class, 'show'])->name('student.my-subscription');
        Route::get('/my-course-subscriptions', [\App\Http\Controllers\Student\MyCourseSubscriptionController::class, 'index'])->name('student.my-course-subscriptions');
        Route::get('/one-to-one-sessions', [\App\Http\Controllers\Student\OneToOneSessionController::class, 'index'])->name('student.one-to-one-sessions.index');
        Route::get('/one-to-one-sessions/{oneToOneSession}', [\App\Http\Controllers\Student\OneToOneSessionController::class, 'show'])->name('student.one-to-one-sessions.show');
        Route::post('/one-to-one-sessions/{oneToOneSession}/book', [\App\Http\Controllers\Student\OneToOneSessionController::class, 'book'])->name('student.one-to-one-sessions.book');
        Route::get('/ai-usages', [\App\Http\Controllers\Student\StudentAiUsageController::class, 'index'])->name('student.ai-usages.index');
        Route::post('/ai-usages/saved-games', [\App\Http\Controllers\Student\StudentAiUsageController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('student.ai-usages.saved-games.store');
        Route::delete('/ai-usages/saved-games/{game}', [\App\Http\Controllers\Student\StudentAiUsageController::class, 'destroy'])
            ->whereNumber('game')
            ->name('student.ai-usages.saved-games.destroy');
        // Muallimx Classroom — بديل Zoom للمعلم (رابط/كود للضيوف بدون اشتراك)
        Route::get('/classroom', [\App\Http\Controllers\Student\ClassroomController::class, 'index'])->name('student.classroom.index');
        Route::get('/classroom/create', [\App\Http\Controllers\Student\ClassroomController::class, 'create'])->name('student.classroom.create');
        Route::post('/classroom', [\App\Http\Controllers\Student\ClassroomController::class, 'store'])->name('student.classroom.store');
        Route::get('/classroom/whiteboard', [\App\Http\Controllers\Student\ClassroomController::class, 'whiteboardStandalone'])->name('student.classroom.whiteboard');
        Route::get('/classroom/{meeting}', [\App\Http\Controllers\Student\ClassroomController::class, 'show'])->name('student.classroom.show');
        Route::get('/classroom/{meeting}/edit', [\App\Http\Controllers\Student\ClassroomController::class, 'edit'])->name('student.classroom.edit');
        Route::put('/classroom/{meeting}', [\App\Http\Controllers\Student\ClassroomController::class, 'update'])->name('student.classroom.update');
        Route::delete('/classroom/{meeting}', [\App\Http\Controllers\Student\ClassroomController::class, 'destroy'])->name('student.classroom.destroy');
        Route::post('/classroom/start', [\App\Http\Controllers\Student\ClassroomController::class, 'start'])->name('student.classroom.start');
        Route::post('/classroom/{meeting}/start', [\App\Http\Controllers\Student\ClassroomController::class, 'startMeeting'])->name('student.classroom.start-meeting');
        Route::get('/classroom/room/{meeting}', [\App\Http\Controllers\Student\ClassroomController::class, 'room'])->name('student.classroom.room');
        Route::get('/classroom/room/{meeting}/recording-upload', [\App\Http\Controllers\Student\ClassroomController::class, 'recordingUploadTab'])->name('student.classroom.recording.upload-tab');
        Route::post('/classroom/{meeting}/participant-whiteboard', [\App\Http\Controllers\Student\ClassroomController::class, 'updateParticipantWhiteboard'])->name('student.classroom.participant-whiteboard');
        Route::get('/classroom/{meeting}/share-annotations', [\App\Http\Controllers\Student\ClassroomController::class, 'shareAnnotations'])->name('student.classroom.share-annotations');
        Route::post('/classroom/room/{meeting}/end', [\App\Http\Controllers\Student\ClassroomController::class, 'end'])->name('student.classroom.end');
        Route::post('/classroom/{meeting}/recording/upload', [\App\Http\Controllers\Student\ClassroomController::class, 'uploadRecording'])->name('student.classroom.recording.upload');
        Route::post('/classroom/{meeting}/recording/presign', [\App\Http\Controllers\Student\ClassroomController::class, 'presignRecordingUpload'])->name('student.classroom.recording.presign');
        Route::post('/classroom/{meeting}/recording/complete', [\App\Http\Controllers\Student\ClassroomController::class, 'completeDirectRecordingUpload'])->name('student.classroom.recording.complete');
        Route::post('/classroom/{meeting}/recording-audio/presign', [\App\Http\Controllers\Student\ClassroomController::class, 'presignAudioUpload'])->name('student.classroom.recording-audio.presign');
        Route::post('/classroom/{meeting}/recording-audio/upload', [\App\Http\Controllers\Student\ClassroomController::class, 'uploadAudioRecording'])->name('student.classroom.recording-audio.upload');
        Route::post('/classroom/{meeting}/recording-audio/complete', [\App\Http\Controllers\Student\ClassroomController::class, 'completeDirectAudioUpload'])->name('student.classroom.recording-audio.complete');
        Route::post('/classroom/{meeting}/ai-report', [\App\Http\Controllers\Student\ClassroomController::class, 'generateAiReport'])->name('student.classroom.ai-report');
    });

    // مزايا اشتراك Muallimx (دعم، مكتبة، صفحات المزايا، …) — للطالب والمدرب حسب التحقق داخل المتحكم
    Route::middleware(['role:student|instructor|teacher'])->group(function () {
        Route::get('/support', [\App\Http\Controllers\Student\SupportTicketController::class, 'index'])->name('student.support.index');
        Route::post('/support', [\App\Http\Controllers\Student\SupportTicketController::class, 'store'])->name('student.support.store');
        Route::get('/support/{ticket}', [\App\Http\Controllers\Student\SupportTicketController::class, 'show'])->name('student.support.show');
        Route::post('/support/{ticket}/reply', [\App\Http\Controllers\Student\SupportTicketController::class, 'reply'])->name('student.support.reply');
        Route::get('/academies/visibility', [\App\Http\Controllers\Student\AcademiesVisibilityController::class, 'index'])->name('student.academies.visibility');
        Route::post('/academies/opportunities/{opportunity}/apply', [\App\Http\Controllers\Student\AcademiesVisibilityController::class, 'apply'])->name('student.academies.opportunities.apply');
        Route::get('/teaching-opportunities', [\App\Http\Controllers\Student\TeachingOpportunityController::class, 'index'])->name('student.opportunities.index');
        Route::post('/teaching-opportunities/{opportunity}/apply', [\App\Http\Controllers\Student\TeachingOpportunityController::class, 'apply'])->name('student.opportunities.apply');
        Route::get('/features/{feature}', [\App\Http\Controllers\Student\SubscriptionFeatureController::class, 'show'])
            ->name('student.features.show')
            ->where('feature', 'library_access|ai_tools|classroom_access|support|visible_to_academies|can_apply_opportunities|full_ai_suite|teacher_evaluation|recommended_to_academies|priority_opportunities|direct_support');
        Route::post('/features/full-ai-suite/preview', [\App\Http\Controllers\Student\SubscriptionFeatureController::class, 'previewFullAiSuite'])
            ->middleware('throttle:20,1')
            ->name('student.features.full-ai-suite.preview');
        Route::get('/curriculum-library', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'index'])->name('curriculum-library.index');
        Route::get('/curriculum-library/{item:slug}', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'show'])->name('curriculum-library.show');
        Route::get('/curriculum-library/{item:slug}/m/{material}/download', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'downloadMaterial'])->name('curriculum-library.material.download');
        Route::get('/curriculum-library/{item:slug}/m/{material}/html', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'viewMaterialHtml'])->name('curriculum-library.material.html');
        Route::get('/curriculum-library/{item:slug}/m/{material}/pdf', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'viewMaterialPdf'])->name('curriculum-library.material.pdf');
        Route::get('/curriculum-library/{item:slug}/m/{material}/presentation', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'viewMaterialPresentation'])->name('curriculum-library.material.presentation');
        Route::get('/curriculum-library/{item:slug}/file/{file}/download', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'download'])->name('curriculum-library.file.download');
        Route::get('/curriculum-library/{item:slug}/file/{file}/view', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'viewHtml'])->name('curriculum-library.file.view');
        Route::get('/curriculum-library/{item:slug}/file/{file}/pdf', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'viewPdf'])->name('curriculum-library.file.pdf');
        Route::get('/curriculum-library/{item:slug}/file/{file}/presentation', [\App\Http\Controllers\Student\CurriculumLibraryController::class, 'viewPresentation'])->name('curriculum-library.file.presentation');
    });

    // لوحة الموظفين — عناصر القائمة تُحدَّد بصلاحيات الوظيفة (employee_jobs.permissions)
    Route::prefix('employee')->name('employee.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Employee\EmployeeController::class, 'dashboard'])->middleware('employee.can:dashboard')->name('dashboard');

        Route::get('/desk/accountant', [\App\Http\Controllers\Employee\EmployeeAccountantDeskController::class, 'index'])->middleware('employee.can:desk_accountant')->name('accountant-desk.index');
        Route::prefix('sales')->name('sales.')->middleware('employee.can:sales_desk')->group(function () {
            Route::get('/desk', [\App\Http\Controllers\Employee\EmployeeSalesWorkspaceController::class, 'desk'])->name('desk');
            Route::get('/orders', [\App\Http\Controllers\Employee\EmployeeSalesWorkspaceController::class, 'ordersIndex'])->name('orders.index');
            Route::get('/orders/{order}', [\App\Http\Controllers\Employee\EmployeeSalesWorkspaceController::class, 'orderShow'])->name('orders.show');
            Route::post('/orders/{order}/notes', [\App\Http\Controllers\Employee\EmployeeSalesWorkspaceController::class, 'storeNote'])->name('orders.notes.store');
            Route::post('/orders/{order}/claim', [\App\Http\Controllers\Employee\EmployeeSalesWorkspaceController::class, 'claim'])->name('orders.claim');

            Route::get('/leads', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'index'])->name('leads.index');
            Route::get('/leads/create', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'create'])->name('leads.create');
            Route::post('/leads', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'store'])->name('leads.store');
            Route::get('/leads/{salesLead}', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'show'])->name('leads.show');
            Route::get('/leads/{salesLead}/edit', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'edit'])->name('leads.edit');
            Route::put('/leads/{salesLead}', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'update'])->name('leads.update');
            Route::post('/leads/{salesLead}/assign-me', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'assignToMe'])->name('leads.assign-me');
            Route::post('/leads/{salesLead}/convert', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'convert'])->name('leads.convert');
            Route::post('/leads/{salesLead}/lost', [\App\Http\Controllers\Employee\EmployeeSalesLeadController::class, 'markLost'])->name('leads.lost');
        });

        Route::prefix('crm')->name('crm.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'dashboard'])->name('dashboard');
            Route::get('/leads', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'leadsIndex'])->name('leads.index');
            Route::get('/leads/create', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'leadsCreate'])->name('leads.create');
            Route::post('/leads', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'leadsStore'])->name('leads.store');
            Route::get('/leads/{salesLead}', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'leadsShow'])->name('leads.show');
            Route::put('/leads/{salesLead}', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'leadsUpdate'])->name('leads.update');
            Route::post('/leads/{salesLead}/transition', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'transition'])->name('leads.transition');
            Route::post('/leads/{salesLead}/note', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'addNote'])->name('leads.note');
            Route::post('/leads/{salesLead}/confirm-payment', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'confirmPayment'])->name('leads.confirm-payment');
            Route::get('/commissions', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'commissions'])->name('commissions');
            Route::post('/commissions/{commission}/approve', [\App\Http\Controllers\Employee\CrmWorkspaceController::class, 'approveCommission'])->name('commissions.approve');
        });

        Route::get('/desk/hr', [\App\Http\Controllers\Employee\EmployeeHrDeskController::class, 'index'])->middleware('employee.can:hr_desk')->name('hr-desk.index');
        Route::prefix('hr')->name('hr.')->middleware('employee.can:hr_desk')->group(function () {
            Route::get('/leaves', [\App\Http\Controllers\Employee\EmployeeHrLeaveController::class, 'index'])->name('leaves.index');
            Route::get('/leaves/{leave}', [\App\Http\Controllers\Employee\EmployeeHrLeaveController::class, 'show'])->name('leaves.show');
            Route::post('/leaves/{leave}/approve', [\App\Http\Controllers\Employee\EmployeeHrLeaveController::class, 'approve'])->name('leaves.approve');
            Route::post('/leaves/{leave}/reject', [\App\Http\Controllers\Employee\EmployeeHrLeaveController::class, 'reject'])->name('leaves.reject');
            Route::get('/employees', [\App\Http\Controllers\Employee\EmployeeHrDirectoryController::class, 'index'])->name('employees.index');
            Route::get('/employees/{employee}', [\App\Http\Controllers\Employee\EmployeeHrDirectoryController::class, 'show'])->name('employees.show');
            Route::post('/employees/{employee}/hr-events', [\App\Http\Controllers\Employee\EmployeeHrDirectoryController::class, 'storeEvent'])->name('employees.hr-events.store');

            Route::prefix('recruitment')->name('recruitment.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Employee\EmployeeHrRecruitmentController::class, 'index'])->name('index');
                Route::resource('openings', \App\Http\Controllers\Employee\EmployeeHrJobOpeningController::class);
                Route::resource('candidates', \App\Http\Controllers\Employee\EmployeeHrCandidateController::class);
                Route::get('/applications/{hr_job_application}', [\App\Http\Controllers\Employee\EmployeeHrJobApplicationController::class, 'show'])->name('applications.show');
                Route::patch('/applications/{hr_job_application}', [\App\Http\Controllers\Employee\EmployeeHrJobApplicationController::class, 'update'])->name('applications.update');
                Route::post('/applications', [\App\Http\Controllers\Employee\EmployeeHrJobApplicationController::class, 'store'])->name('applications.store');
                Route::post('/applications/{hr_job_application}/interviews', [\App\Http\Controllers\Employee\EmployeeHrInterviewController::class, 'store'])->name('applications.interviews.store');
                Route::put('/applications/{hr_job_application}/interviews/{hr_interview}', [\App\Http\Controllers\Employee\EmployeeHrInterviewController::class, 'update'])->name('applications.interviews.update');
                Route::delete('/applications/{hr_job_application}/interviews/{hr_interview}', [\App\Http\Controllers\Employee\EmployeeHrInterviewController::class, 'destroy'])->name('applications.interviews.destroy');
            });
        });
        Route::get('/desk/supervision', [\App\Http\Controllers\Employee\EmployeeSupervisionDeskController::class, 'index'])->middleware('employee.can:supervision_desk')->name('supervision-desk.index');

        Route::get('/desk/academic-supervision', [\App\Http\Controllers\Employee\AcademicSupervisionController::class, 'index'])->middleware('employee.can:academic_supervision_desk')->name('academic-supervision.index');
        Route::get('/desk/academic-supervision/students/{student}', [\App\Http\Controllers\Employee\AcademicSupervisionController::class, 'show'])->middleware('employee.can:academic_supervision_desk')->name('academic-supervision.show');
        Route::get('/desk/academic-supervision/meetings/{meeting}/observe', [\App\Http\Controllers\Employee\AcademicSupervisionController::class, 'observerRoom'])->middleware('employee.can:academic_supervision_desk')->name('academic-supervision.meeting.observe');

        Route::get('/tasks', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'index'])->middleware('employee.can:tasks')->name('tasks.index');
        Route::get('/tasks/{task}', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'show'])->middleware('employee.can:tasks')->name('tasks.show');
        Route::put('/tasks/{task}/status', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'updateStatus'])->middleware('employee.can:tasks')->name('tasks.update-status');
        Route::post('/tasks/{task}/deliverables', [\App\Http\Controllers\Employee\EmployeeTaskController::class, 'submitDeliverable'])->middleware('employee.can:tasks')->name('tasks.submit-deliverable');

        Route::resource('leaves', \App\Http\Controllers\Employee\EmployeeLeaveController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->middleware('employee.can:leaves');

        Route::get('/accounting', [\App\Http\Controllers\Employee\AccountingController::class, 'index'])->middleware('employee.can:accounting')->name('accounting.index');
        Route::post('/accounting/bank-account', [\App\Http\Controllers\Employee\AccountingController::class, 'updateBankAccount'])->middleware('employee.can:accounting')->name('accounting.update-bank');

        Route::get('/agreements', [\App\Http\Controllers\Employee\AgreementController::class, 'index'])->middleware('employee.can:agreements')->name('agreements.index');
        Route::get('/agreements/{agreement}', [\App\Http\Controllers\Employee\AgreementController::class, 'show'])->middleware('employee.can:agreements')->name('agreements.show');

        Route::get('/profile', [\App\Http\Controllers\Employee\EmployeeProfileController::class, 'index'])->middleware('employee.can:profile')->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Employee\EmployeeProfileController::class, 'update'])->middleware('employee.can:profile')->name('profile.update');

        Route::get('/notifications', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'index'])->middleware('employee.can:notifications')->name('notifications');
        Route::get('/notifications/{notification}/go', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'go'])->middleware('employee.can:notifications')->name('notifications.go');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'show'])->middleware('employee.can:notifications')->name('notifications.show');
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAsRead'])->middleware('employee.can:notifications')->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAllAsRead'])->middleware('employee.can:notifications')->name('notifications.mark-all-read');

        Route::get('/calendar', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'index'])->middleware('employee.can:calendar')->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'getEvents'])->middleware('employee.can:calendar')->name('calendar.events');

        Route::get('/reports', [\App\Http\Controllers\Employee\EmployeeReportController::class, 'index'])->middleware('employee.can:reports')->name('reports');

        Route::get('/settings', [\App\Http\Controllers\Employee\EmployeeSettingsController::class, 'index'])->middleware('employee.can:settings')->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Employee\EmployeeSettingsController::class, 'update'])->middleware('employee.can:settings')->name('settings.update');

        Route::get('/api/nav-notifications', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'navPoll'])
            ->middleware(ThrottleRequests::using('employee-nav-poll'))
            ->name('api.nav-notifications');

        Route::get('/api/notifications/unread', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'getUnread'])->middleware('employee.can:notifications')->name('notifications.unread');
        Route::post('/api/notifications/{notification}/mark-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAsRead'])->middleware('employee.can:notifications')->name('notifications.api.mark-read');
    });

    // مسارات الإدارة - محمية بصلاحية admin.access (مع تجاوز super_admin داخل EnsurePermission)
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'permission:admin.access', 'rbac.strict.admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/api/nav-notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'navPoll'])
            ->middleware(ThrottleRequests::using('admin-nav-poll'))
            ->name('api.nav-notifications');

        // بروفايل الأدمن
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        // إدارة المستخدمين
        Route::get('/users', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users.index');
        Route::get('/students-accounts', [\App\Http\Controllers\Admin\AdminController::class, 'studentsAccounts'])->name('students-accounts.index');
        Route::get('/students-control/paid-features', [\App\Http\Controllers\Admin\StudentControlController::class, 'paidFeatures'])->name('students-control.paid-features');
        Route::get('/students-control/paid-features/{featureKey}', [\App\Http\Controllers\Admin\StudentControlController::class, 'featureUsers'])->name('students-control.paid-features.show');
        Route::get('/students-control/consumption', [\App\Http\Controllers\Admin\StudentControlController::class, 'consumption'])->name('students-control.consumption');
        Route::get('/students-control/consumption/user/{user}', [\App\Http\Controllers\Admin\StudentControlController::class, 'userConsumption'])->name('students-control.consumption.user');

        // مراجعة الملف التعريفي التسويقي للطالب (قبل مسارات portfolio/{project} لتفادي أي التباس)
        Route::get('/portfolio-marketing-profiles', [\App\Http\Controllers\Admin\PortfolioMarketingProfileController::class, 'index'])->name('portfolio-marketing-profiles.index');
        Route::get('/portfolio-marketing-profiles/{user}', [\App\Http\Controllers\Admin\PortfolioMarketingProfileController::class, 'show'])->name('portfolio-marketing-profiles.show');
        Route::post('/portfolio-marketing-profiles/{user}/approve', [\App\Http\Controllers\Admin\PortfolioMarketingProfileController::class, 'approve'])->name('portfolio-marketing-profiles.approve');
        Route::post('/portfolio-marketing-profiles/{user}/reject', [\App\Http\Controllers\Admin\PortfolioMarketingProfileController::class, 'reject'])->name('portfolio-marketing-profiles.reject');

        Route::get('/users/create', [\App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\AdminController::class, 'storeUser'])
            ->middleware('throttle:20,1')
            ->name('users.store');
        Route::get('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'showUser'])->name('users.show')->where('id', '[0-9]+');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit')->where('id', '[0-9]+');
        // دعم fallback لـ POST في حالة فشل method spoof (_method=PUT) على بعض البيئات/المتصفحات
        Route::post('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->where('id', '[0-9]+');
        Route::put('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update')->where('id', '[0-9]+');
        Route::delete('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete')->where('id', '[0-9]+');

        // مسارات التعلم (السنوات الدراسية)
        Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class)->except(['show']);
        Route::post('/academic-years/{academicYear}/toggle-status', [\App\Http\Controllers\Admin\AcademicYearController::class, 'toggleStatus'])->name('academic-years.toggle-status');
        Route::post('/academic-years/reorder', [\App\Http\Controllers\Admin\AcademicYearController::class, 'reorder'])->name('academic-years.reorder');
        Route::post('/academic-years/{academicYear}/add-course', [\App\Http\Controllers\Admin\AcademicYearController::class, 'addCourse'])->name('academic-years.add-course');
        Route::delete('/academic-years/{academicYear}/remove-course/{course}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'removeCourse'])->name('academic-years.remove-course');
        Route::post('/academic-years/{academicYear}/add-instructor', [\App\Http\Controllers\Admin\AcademicYearController::class, 'addInstructor'])->name('academic-years.add-instructor');
        Route::delete('/academic-years/{academicYear}/remove-instructor/{instructor}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'removeInstructor'])->name('academic-years.remove-instructor');

        Route::prefix('learning-paths')->name('learning-paths.')->group(function () {
            Route::get('/courses', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesIndex'])->name('courses.index');
            Route::get('/courses/{academicYear}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesManage'])->name('courses.manage');
            Route::post('/courses/{academicYear}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesStore'])->name('courses.store');
            Route::delete('/courses/{academicYear}/{course}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesDestroy'])->name('courses.destroy');
            Route::post('/courses/{academicYear}/reorder', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'coursesUpdateOrder'])->name('courses.update-order');

            Route::get('/instructors', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsIndex'])->name('instructors.index');
            Route::get('/instructors/{academicYear}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsManage'])->name('instructors.manage');
            Route::post('/instructors/{academicYear}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsStore'])->name('instructors.store');
            Route::delete('/instructors/{academicYear}/{instructor}', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsDestroy'])->name('instructors.destroy');
            Route::put('/instructors/{academicYear}/{instructor}/courses', [\App\Http\Controllers\Admin\LearningPathManagementController::class, 'instructorsUpdateCourses'])->name('instructors.update-courses');
        });

        Route::resource('academic-subjects', \App\Http\Controllers\Admin\AcademicSubjectController::class);
        Route::post('/academic-subjects/{academicSubject}/toggle-status', [\App\Http\Controllers\Admin\AcademicSubjectController::class, 'toggleStatus'])->name('academic-subjects.toggle-status');
        Route::post('/academic-subjects/reorder', [\App\Http\Controllers\Admin\AcademicSubjectController::class, 'reorder'])->name('academic-subjects.reorder');

        // مسارات الكورسات (تصفية صفحة /courses العامة)
        Route::resource('course-categories', \App\Http\Controllers\Admin\CourseCategoryController::class)->except(['show', 'create']);

        // إدارة الكورسات المتطورة
        Route::resource('advanced-courses', \App\Http\Controllers\Admin\AdvancedCourseController::class);
        Route::post('/advanced-courses/{advancedCourse}/activate-student', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'activateStudent'])->name('advanced-courses.activate-student');
        Route::get('/advanced-courses/{advancedCourse}/students', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'students'])->name('advanced-courses.students');
        Route::post('/advanced-courses/{advancedCourse}/toggle-status', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'toggleStatus'])->name('advanced-courses.toggle-status');
        Route::post('/advanced-courses/{advancedCourse}/toggle-featured', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'toggleFeatured'])->name('advanced-courses.toggle-featured');
        Route::get('/advanced-courses/{advancedCourse}/orders', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'orders'])->name('advanced-courses.orders');
        Route::get('/advanced-courses/{advancedCourse}/statistics', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'statistics'])->name('advanced-courses.statistics');
        Route::post('/advanced-courses/{advancedCourse}/duplicate', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'duplicate'])->name('advanced-courses.duplicate');
        Route::get('/get-subjects-by-year', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'getSubjectsByYear'])->name('advanced-courses.get-subjects-by-year');
        Route::get('/courses/{course}/lessons-list', function (\App\Models\AdvancedCourse $course) {
            return response()->json($course->lessons()->active()->select('id', 'title')->get());
        });

        // إدارة دروس الكورسات
        Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CourseLessonController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CourseLessonController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CourseLessonController::class, 'store'])->name('store');
            Route::get('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'show'])->name('show');
            Route::get('/{lesson}/edit', [\App\Http\Controllers\Admin\CourseLessonController::class, 'edit'])->name('edit');
            Route::put('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'update'])->name('update');
            Route::delete('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'destroy'])->name('destroy');
            Route::post('/{lesson}/toggle-status', [\App\Http\Controllers\Admin\CourseLessonController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/reorder', [\App\Http\Controllers\Admin\CourseLessonController::class, 'reorder'])->name('reorder');
        });

        // إدارة بنك الأسئلة
        Route::prefix('question-bank')->name('question-bank.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuestionBankController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\QuestionBankController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\QuestionBankController::class, 'store'])->name('store');
            Route::get('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'show'])->name('show');
            Route::get('/{question}/edit', [\App\Http\Controllers\Admin\QuestionBankController::class, 'edit'])->name('edit');
            Route::put('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'update'])->name('update');
            Route::delete('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'destroy'])->name('destroy');
            Route::post('/{question}/duplicate', [\App\Http\Controllers\Admin\QuestionBankController::class, 'duplicate'])->name('duplicate');
            Route::post('/export', [\App\Http\Controllers\Admin\QuestionBankController::class, 'export'])->name('export');
            Route::post('/import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'import'])->name('import');
        });

        // إدارة تصنيفات الأسئلة
        Route::prefix('question-categories')->name('question-categories.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'store'])->name('store');
            Route::get('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'show'])->name('show');
            Route::get('/{questionCategory}/edit', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'edit'])->name('edit');
            Route::put('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'update'])->name('update');
            Route::delete('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'reorder'])->name('reorder');
            Route::get('/subjects-by-year/{year}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'getSubjectsByYear'])->name('subjects-by-year');
        });

        // إدارة الامتحانات (مسار الكورس قبل المسارات الأخرى لتفادي التعارض)
        Route::prefix('exams')->name('exams.')->group(function () {
            Route::get('/course/{course}', [\App\Http\Controllers\Admin\ExamController::class, 'indexByCourse'])->name('by-course');
            Route::get('/', [\App\Http\Controllers\Admin\ExamController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ExamController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ExamController::class, 'store'])->name('store');
            Route::get('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'show'])->name('show');
            Route::get('/{exam}/edit', [\App\Http\Controllers\Admin\ExamController::class, 'edit'])->name('edit');
            Route::put('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'update'])->name('update');
            Route::delete('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'destroy'])->name('destroy');
            Route::get('/{exam}/questions', [\App\Http\Controllers\Admin\ExamController::class, 'manageQuestions'])->name('questions.manage');
            Route::post('/{exam}/questions', [\App\Http\Controllers\Admin\ExamController::class, 'addQuestion'])->name('questions.add');
            Route::delete('/{exam}/questions/{examQuestion}', [\App\Http\Controllers\Admin\ExamController::class, 'removeQuestion'])->name('questions.remove');
            Route::post('/{exam}/questions/reorder', [\App\Http\Controllers\Admin\ExamController::class, 'reorderQuestions'])->name('questions.reorder');
            Route::post('/{exam}/toggle-publish', [\App\Http\Controllers\Admin\ExamController::class, 'togglePublish'])->name('toggle-publish');
            Route::post('/{exam}/toggle-status', [\App\Http\Controllers\Admin\ExamController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{exam}/statistics', [\App\Http\Controllers\Admin\ExamController::class, 'statistics'])->name('statistics');
            Route::get('/{exam}/preview', [\App\Http\Controllers\Admin\ExamController::class, 'preview'])->name('preview');
            Route::post('/{exam}/duplicate', [\App\Http\Controllers\Admin\ExamController::class, 'duplicate'])->name('duplicate');
        });

        // إدارة المواد الدراسية القديمة
        Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);

        // إدارة الكورسات القديمة
        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);

        // سجل النشاطات
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log');
        Route::get('/activity-log/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::post('/activity-log/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('activity-log.destroy');

        // سجلات التحقق الثنائي (2FA)
        Route::get('/two-factor-logs', [\App\Http\Controllers\Admin\TwoFactorLogController::class, 'index'])->name('two-factor-logs.index');

        // الإحصائيات
        Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/users', [\App\Http\Controllers\Admin\StatisticsController::class, 'users'])->name('statistics.users');
        Route::get('/statistics/courses', [\App\Http\Controllers\Admin\StatisticsController::class, 'courses'])->name('statistics.courses');

        // Glottical CRM
        Route::prefix('crm')->name('crm.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CrmDashboardController::class, 'index'])->name('dashboard');
            Route::get('/leads', [\App\Http\Controllers\Admin\CrmLeadController::class, 'index'])->name('leads.index');
            Route::get('/leads/{salesLead}', [\App\Http\Controllers\Admin\CrmLeadController::class, 'show'])->name('leads.show');
            Route::post('/leads/{salesLead}/assign', [\App\Http\Controllers\Admin\CrmLeadController::class, 'assign'])->name('leads.assign');
            Route::post('/leads/{salesLead}/transition', [\App\Http\Controllers\Admin\CrmLeadController::class, 'transition'])->name('leads.transition');
            Route::post('/leads/{salesLead}/note', [\App\Http\Controllers\Admin\CrmLeadController::class, 'addNote'])->name('leads.note');
            Route::get('/commissions', [\App\Http\Controllers\Admin\CrmCommissionController::class, 'index'])->name('commissions.index');
            Route::post('/commissions/{commission}/approve', [\App\Http\Controllers\Admin\CrmCommissionController::class, 'approve'])->name('commissions.approve');
            Route::get('/audit', [\App\Http\Controllers\Admin\CrmAuditLogController::class, 'index'])->name('audit.index');
            Route::get('/groups', [\App\Http\Controllers\Admin\CrmGroupController::class, 'index'])->name('groups.index');
            Route::get('/groups/create', [\App\Http\Controllers\Admin\CrmGroupController::class, 'create'])->name('groups.create');
            Route::post('/groups', [\App\Http\Controllers\Admin\CrmGroupController::class, 'store'])->name('groups.store');
            Route::get('/groups/{group}/edit', [\App\Http\Controllers\Admin\CrmGroupController::class, 'edit'])->name('groups.edit');
            Route::put('/groups/{group}', [\App\Http\Controllers\Admin\CrmGroupController::class, 'update'])->name('groups.update');
            Route::post('/groups/{group}/members', [\App\Http\Controllers\Admin\CrmGroupController::class, 'addMember'])->name('groups.members.store');
            Route::delete('/groups/{group}/members/{member}', [\App\Http\Controllers\Admin\CrmGroupController::class, 'removeMember'])->name('groups.members.destroy');
        });

        // العملاء المحتملون ثم تحليلات المبيعات (مسارات تحت /sales)
        Route::get('/sales/leads', [\App\Http\Controllers\Admin\SalesLeadController::class, 'index'])->name('sales.leads.index');
        Route::get('/sales/leads/{salesLead}', [\App\Http\Controllers\Admin\SalesLeadController::class, 'show'])->name('sales.leads.show');
        Route::get('/sales', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'index'])->name('sales.index');

        // إدارة الطلبات
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/sales-assign', [\App\Http\Controllers\Admin\OrderController::class, 'assignSalesOwner'])->name('orders.sales-assign');
        Route::post('/orders/{order}/sales-notes', [\App\Http\Controllers\Admin\OrderController::class, 'storeSalesNote'])->name('orders.sales-notes.store');
        Route::patch('/orders/{order}/receiving-wallet', [\App\Http\Controllers\Admin\OrderController::class, 'updateReceivingWallet'])->name('orders.receiving-wallet');
        Route::post('/orders/{order}/approve', [\App\Http\Controllers\Admin\OrderController::class, 'approve'])
            ->middleware('throttle:10,1')
            ->name('orders.approve');
        Route::post('/orders/{order}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'reject'])
            ->middleware('throttle:10,1')
            ->name('orders.reject');

        // إدارة الصلاحيات والأدوار
        Route::middleware('permission:users.permissions')->group(function () {
            Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
            Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
            Route::post('/roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('roles.update-permissions');

            // إدارة صلاحيات المستخدمين
            Route::get('/user-permissions', [\App\Http\Controllers\Admin\UserPermissionController::class, 'index'])->name('user-permissions.index');
            Route::get('/user-permissions/{user}', [\App\Http\Controllers\Admin\UserPermissionController::class, 'show'])->name('user-permissions.show');
            Route::put('/user-permissions/{user}', [\App\Http\Controllers\Admin\UserPermissionController::class, 'update'])->name('user-permissions.update');
            Route::put('/user-permissions/{user}/roles', [\App\Http\Controllers\Admin\UserPermissionController::class, 'updateRoles'])->name('user-permissions.update-roles');
            Route::post('/user-permissions/{user}/attach', [\App\Http\Controllers\Admin\UserPermissionController::class, 'attachPermission'])->name('user-permissions.attach');
            Route::post('/user-permissions/{user}/detach', [\App\Http\Controllers\Admin\UserPermissionController::class, 'detachPermission'])->name('user-permissions.detach');
        });

        // إدارة المحافظ الذكية
        Route::resource('wallets', \App\Http\Controllers\Admin\WalletController::class);
        Route::post('/wallets/transfer', [\App\Http\Controllers\Admin\WalletController::class, 'transfer'])->name('wallets.transfer');
        Route::get('/wallets/{wallet}/transactions', [\App\Http\Controllers\Admin\WalletController::class, 'transactions'])->name('wallets.transactions');
        Route::get('/wallets/{wallet}/reports', [\App\Http\Controllers\Admin\WalletController::class, 'reports'])->name('wallets.reports');
        Route::post('/wallets/{wallet}/generate-report', [\App\Http\Controllers\Admin\WalletController::class, 'generateReport'])->name('wallets.generate-report');

        // إدارة المحاضرات والجروبات
        Route::resource('lectures', \App\Http\Controllers\Admin\LectureController::class);
        Route::post('/lectures/{lecture}/sync-teams-attendance', [\App\Http\Controllers\Admin\LectureController::class, 'syncTeamsAttendance'])->name('lectures.sync-teams-attendance');
        // إدارة الواجبات والمشاريع (مسار الكورس قبل المسارات الأخرى لتفادي التعارض)
        Route::get('/assignments/course/{course}', [\App\Http\Controllers\Admin\AssignmentController::class, 'indexByCourse'])->name('assignments.by-course');
        Route::resource('assignments', \App\Http\Controllers\Admin\AssignmentController::class);
        Route::get('/assignments/{assignment}/submissions', [\App\Http\Controllers\Admin\AssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::post('/assignments/{assignment}/grade/{submission}', [\App\Http\Controllers\Admin\AssignmentController::class, 'grade'])->name('assignments.grade');

        // إدارة المهام
        Route::resource('tasks', \App\Http\Controllers\Admin\TaskController::class);
        Route::post('/tasks/{task}/complete', [\App\Http\Controllers\Admin\TaskController::class, 'complete'])->name('tasks.complete');
        Route::post('/tasks/{task}/comments', [\App\Http\Controllers\Admin\TaskController::class, 'addComment'])->name('tasks.add-comment');
        Route::post('/tasks/{task}/deliverables/{deliverable}/review', [\App\Http\Controllers\Admin\TaskController::class, 'reviewDeliverable'])->name('tasks.review-deliverable');

        // البورتفوليو - الرقابة والجودة (الأدمن يرى الكل ويمكنه إخفاء مشروع)
        Route::get('portfolio', [\App\Http\Controllers\Admin\PortfolioController::class, 'index'])->name('portfolio.index');
        Route::get('portfolio/{project}', [\App\Http\Controllers\Admin\PortfolioController::class, 'show'])->name('portfolio.show');
        Route::post('portfolio/{project}/approve', [\App\Http\Controllers\Admin\PortfolioController::class, 'approve'])->name('portfolio.approve');
        Route::post('portfolio/{project}/reject', [\App\Http\Controllers\Admin\PortfolioController::class, 'reject'])->name('portfolio.reject');
        Route::post('portfolio/{project}/publish', [\App\Http\Controllers\Admin\PortfolioController::class, 'publish'])->name('portfolio.publish');
        Route::post('portfolio/{project}/toggle-visibility', [\App\Http\Controllers\Admin\PortfolioController::class, 'toggleVisibility'])->name('portfolio.toggle-visibility');

        // تم إيقاف مجتمع البيانات والذكاء الاصطناعي في لوحة الإدارة، لذا أزيلت جميع مساراته.

        // الإدارة العليا (من نحن وغيرها)
        Route::get('about', [\App\Http\Controllers\Admin\AboutPageController::class, 'index'])->name('about.index');
        Route::get('about/view', [\App\Http\Controllers\Admin\AboutPageController::class, 'viewPublic'])->name('about.view-public');

        Route::resource('contact-messages', \App\Http\Controllers\Admin\ContactMessageController::class);
        Route::post('/contact-messages/{contactMessage}/mark-as-read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-as-read');
        Route::post('/contact-messages/{contactMessage}/mark-as-unread', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsUnread'])->name('contact-messages.mark-as-unread');

        Route::resource('faq', \App\Http\Controllers\Admin\FAQController::class);

        Route::resource('site-services', \App\Http\Controllers\Admin\SiteServiceController::class)->except(['show']);
        Route::resource('site-testimonials', \App\Http\Controllers\Admin\SiteTestimonialController::class)->except(['show']);

        Route::post('homepage-sliders/reorder', [\App\Http\Controllers\Admin\HomepageSliderController::class, 'reorder'])->name('homepage-sliders.reorder');
        Route::post('homepage-sliders/{homepageSlider}/toggle-active', [\App\Http\Controllers\Admin\HomepageSliderController::class, 'toggleActive'])->name('homepage-sliders.toggle-active');
        Route::resource('homepage-sliders', \App\Http\Controllers\Admin\HomepageSliderController::class)->except(['show']);

        Route::get('/system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'edit'])->name('system-settings.edit');
        Route::put('/system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('system-settings.update');
        Route::post('/system-settings/two-factor/enable-request', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'requestTwoFactorEnable'])
            ->middleware('throttle:10,1')
            ->name('system-settings.two-factor.enable-request');
        Route::get('/system-settings/two-factor/confirm', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'showTwoFactorConfirm'])
            ->name('system-settings.two-factor.confirm');
        Route::post('/system-settings/two-factor/confirm', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'confirmTwoFactorEnable'])
            ->middleware('throttle:20,1')
            ->name('system-settings.two-factor.confirm.submit');
        Route::post('/system-settings/two-factor/resend', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'resendTwoFactorEnableCode'])
            ->middleware('throttle:5,1')
            ->name('system-settings.two-factor.resend');
        Route::post('/system-settings/two-factor/disable', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'disablePlatformTwoFactor'])
            ->middleware('throttle:10,1')
            ->name('system-settings.two-factor.disable');

        // إدارة الأسعار والباقات
        Route::resource('packages', \App\Http\Controllers\Admin\PackageController::class);
        Route::post('/packages/{course}/update-price', [\App\Http\Controllers\Admin\PackageController::class, 'updatePrice'])->name('packages.update-price');
        Route::post('/packages/bulk-update', [\App\Http\Controllers\Admin\PackageController::class, 'updateBulkPrices'])->name('packages.bulk-update');

        // إدارة الإشعارات
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
            Route::get('/inbox', [\App\Http\Controllers\Admin\NotificationController::class, 'inbox'])->name('inbox');
            Route::post('/inbox/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'inboxMarkAllRead'])
                ->middleware('throttle:30,1')
                ->name('inbox.mark-all-read');
            Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
            Route::get('/{notification}/open-support-ticket', [\App\Http\Controllers\Admin\NotificationController::class, 'openSupportTicket'])
                ->middleware('throttle:60,1')
                ->name('open-support-ticket');
            Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])
                ->middleware('throttle:20,5')
                ->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
            Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])
                ->middleware('throttle:30,1')
                ->name('destroy');
            Route::post('/quick-send', [\App\Http\Controllers\Admin\NotificationController::class, 'quickSend'])
                ->middleware('throttle:30,5')
                ->name('quick-send');
            Route::get('/target-count', [\App\Http\Controllers\Admin\NotificationController::class, 'getTargetCount'])
                ->middleware('throttle:60,1')
                ->name('target-count');
            Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])
                ->middleware('throttle:10,1')
                ->name('mark-all-read');
            Route::post('/cleanup', [\App\Http\Controllers\Admin\NotificationController::class, 'cleanup'])
                ->middleware('throttle:5,10')
                ->name('cleanup');
            Route::get('/statistics', [\App\Http\Controllers\Admin\NotificationController::class, 'statistics'])->name('statistics');
        });

        // إشعارات الموظفين
        Route::prefix('employee-notifications')->name('employee-notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'store'])
                ->middleware(ThrottleRequests::using('admin-employee-notification-store'))
                ->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'show'])->name('show');
        });

        // إشعارات البريد (Gmail) — حملات بريدية
        Route::prefix('email-notifications')->name('email-broadcasts.')->group(function () {
            Route::get('/{audience}', [\App\Http\Controllers\Admin\EmailBroadcastController::class, 'index'])->name('index');
            Route::get('/{audience}/create', [\App\Http\Controllers\Admin\EmailBroadcastController::class, 'create'])->name('create');
            Route::post('/{audience}', [\App\Http\Controllers\Admin\EmailBroadcastController::class, 'store'])->name('store');
            Route::get('/{audience}/{email_broadcast}', [\App\Http\Controllers\Admin\EmailBroadcastController::class, 'show'])->name('show');
        });

        // إدارة تسجيل الطلاب في الكورسات الأونلاين
        Route::prefix('online-enrollments')->name('online-enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'store'])->name('store');
            Route::post('/quick-activate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'quickActivate'])->name('quick-activate');
            Route::get('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'show'])->name('show');
            Route::post('/{enrollment}/activate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'activate'])->name('activate');
            Route::post('/{enrollment}/deactivate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'deactivate'])->name('deactivate');
            Route::post('/{enrollment}/update-progress', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateProgress'])->name('update-progress');
            Route::post('/{enrollment}/update-notes', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateNotes'])->name('update-notes');
            Route::delete('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'destroy'])->name('destroy');
            Route::get('/search/by-phone', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'searchStudentByPhone'])->name('search-by-phone');
        });

        // إدارة مصادر الفيديو (Bunny وغيرها)
        Route::prefix('video-providers')->name('video-providers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\VideoProviderController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\VideoProviderController::class, 'store'])->name('store');
            Route::put('/{videoProvider}', [\App\Http\Controllers\Admin\VideoProviderController::class, 'update'])->name('update');
        });

        Route::get('/learning-path-enrollments', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'index'])->name('learning-path-enrollments.index');
        Route::get('/learning-path-enrollments/create', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'create'])->name('learning-path-enrollments.create');
        Route::post('/learning-path-enrollments', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'store'])->name('learning-path-enrollments.store');
        Route::post('/learning-path-enrollments/{enrollment}/toggle-status', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'toggleStatus'])->name('learning-path-enrollments.toggle-status');
        Route::delete('/learning-path-enrollments/{enrollment}', [\App\Http\Controllers\Admin\LearningPathEnrollmentController::class, 'destroy'])->name('learning-path-enrollments.destroy');

        // إدارة الموظفين
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
        Route::resource('employee-jobs', \App\Http\Controllers\Admin\EmployeeJobController::class);
        Route::prefix('academic-supervision')->name('academic-supervision.')->middleware('permission:academic_supervision.manage')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AcademicSupervisionController::class, 'index'])->name('index');
            Route::get('/search-students', [\App\Http\Controllers\Admin\AcademicSupervisionController::class, 'searchStudents'])->name('search-students');
            Route::get('/supervisors/{supervisor}', [\App\Http\Controllers\Admin\AcademicSupervisionController::class, 'show'])->name('supervisors.show');
            Route::post('/supervisors/{supervisor}/students', [\App\Http\Controllers\Admin\AcademicSupervisionController::class, 'attachStudent'])->name('supervisors.students.attach');
            Route::delete('/supervisors/{supervisor}/students/{student}', [\App\Http\Controllers\Admin\AcademicSupervisionController::class, 'detachStudent'])->name('supervisors.students.detach');
            Route::get('/supervisors/{supervisor}/students/{student}', [\App\Http\Controllers\Admin\AcademicSupervisionController::class, 'studentShow'])->name('supervisors.students.show');
            Route::get('/supervisors/{supervisor}/meetings/{meeting}/observe', [\App\Http\Controllers\Admin\AcademicSupervisionController::class, 'observeMeeting'])->name('supervisors.meetings.observe');
        });
        Route::resource('employee-tasks', \App\Http\Controllers\Admin\EmployeeTaskController::class);

        // إدارة الإجازات
        Route::get('/leaves', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/{leave}', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'show'])->name('leaves.show');
        Route::post('/leaves/{leave}/approve', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('/leaves/{leave}/reject', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'reject'])->name('leaves.reject');

        // طلبات المدربين للإدارة
        Route::prefix('instructor-requests')->name('instructor-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\InstructorRequestController::class, 'index'])->name('index');
            Route::get('/{instructorRequest}', [\App\Http\Controllers\Admin\InstructorRequestController::class, 'show'])->name('show');
            Route::post('/{instructorRequest}/respond', [\App\Http\Controllers\Admin\InstructorRequestController::class, 'respond'])->name('respond');
        });

        // الرقابة والجودة
        Route::prefix('quality-control')->name('quality-control.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QualityControlController::class, 'index'])->name('index');
            Route::get('/students', [\App\Http\Controllers\Admin\QualityControlController::class, 'students'])->name('students');
            Route::get('/instructors', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructors'])->name('instructors');
            Route::get('/instructors/{instructor}', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructorShow'])->name('instructors.show');
            Route::get('/instructors/{instructor}/export', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructorExport'])->name('instructors.export');
            Route::get('/employees', [\App\Http\Controllers\Admin\QualityControlController::class, 'employees'])->name('employees');
            Route::get('/operations', [\App\Http\Controllers\Admin\QualityControlController::class, 'operations'])->name('operations');
        });

        // إدارة الرسائل والتقارير
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MessagesController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\MessagesController::class, 'create'])->name('create');
            Route::post('/send-single', [\App\Http\Controllers\Admin\MessagesController::class, 'sendSingle'])->name('send-single');
            Route::post('/send-bulk', [\App\Http\Controllers\Admin\MessagesController::class, 'sendBulk'])->name('send-bulk');

            // التقارير الشهرية
            Route::get('/monthly-reports', [\App\Http\Controllers\Admin\MessagesController::class, 'monthlyReports'])->name('monthly-reports');
            Route::post('/generate-monthly-reports', [\App\Http\Controllers\Admin\MessagesController::class, 'generateMonthlyReports'])->name('generate-monthly-reports');

            // قوالب الرسائل
            Route::get('/templates', [\App\Http\Controllers\Admin\MessagesController::class, 'templates'])->name('templates');
            Route::post('/templates', [\App\Http\Controllers\Admin\MessagesController::class, 'storeTemplate'])->name('templates.store');
            Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\MessagesController::class, 'destroyTemplate'])->name('templates.destroy');

            // إعدادات WhatsApp API
            Route::get('/settings', [\App\Http\Controllers\Admin\WhatsAppSettingsController::class, 'settings'])->name('settings');
            Route::post('/save-api-settings', [\App\Http\Controllers\Admin\WhatsAppSettingsController::class, 'saveApiSettings'])->name('save-api-settings');
            Route::post('/test-api', [\App\Http\Controllers\Admin\WhatsAppSettingsController::class, 'testApi'])->name('test-api');

            // مسارات عرض الرسائل الفردية يجب أن تأتي في النهاية حتى لا تعترض المسارات الثابتة
            Route::get('/{message}', [\App\Http\Controllers\Admin\MessagesController::class, 'show'])->name('show');
            Route::post('/{message}/resend', [\App\Http\Controllers\Admin\MessagesController::class, 'resend'])->name('resend');
            Route::delete('/{message}', [\App\Http\Controllers\Admin\MessagesController::class, 'destroy'])->name('destroy');
        });

        // إدارة المحاسبة
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::match(['post', 'put', 'patch'], '/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->middleware('throttle:20,5')->name('invoices.update');
        Route::delete('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->middleware('throttle:10,1')->name('invoices.destroy');

        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::post('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'update'])->middleware('throttle:20,5')->name('payments.update');
        Route::delete('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->middleware('throttle:10,1')->name('payments.destroy');

        Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::post('/transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'update'])->middleware('throttle:20,5')->name('transactions.update');
        Route::delete('/transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->middleware('throttle:10,1')->name('transactions.destroy');

        Route::resource('wallets', \App\Http\Controllers\Admin\WalletController::class)
            ->middleware('throttle:60,1')
            ->except(['update', 'destroy']);
        Route::post('/wallets/transfer', [\App\Http\Controllers\Admin\WalletController::class, 'transfer'])->middleware('throttle:20,5')->name('wallets.transfer');
        Route::post('/wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'update'])->middleware('throttle:20,5')->name('wallets.update');
        Route::delete('/wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'destroy'])->middleware('throttle:10,1')->name('wallets.destroy');

        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class)->except(['destroy']);
        Route::post('/expenses/{expense}/approve', [\App\Http\Controllers\Admin\ExpenseController::class, 'approve'])->middleware('throttle:10,1')->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [\App\Http\Controllers\Admin\ExpenseController::class, 'reject'])->middleware('throttle:10,1')->name('expenses.reject');
        Route::post('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'update'])->middleware('throttle:20,5')->name('expenses.update');
        Route::delete('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->middleware('throttle:10,1')->name('expenses.destroy');

        Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class)
            ->middleware('throttle:60,1');
        Route::get('/subscriptions/{subscription}/consumption', [\App\Http\Controllers\Admin\SubscriptionController::class, 'consumption'])->name('subscriptions.consumption');
        Route::get('/teacher-features', [\App\Http\Controllers\Admin\TeacherFeaturesController::class, 'index'])
            ->name('teacher-features.index');
        Route::post('/teacher-features', [\App\Http\Controllers\Admin\TeacherFeaturesController::class, 'update'])
            ->name('teacher-features.update');
        // نظام الدعم الفني
        Route::get('/support-tickets', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('support-tickets.index');
        Route::get('/support-tickets/{ticket}', [\App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('support-tickets.show');
        Route::post('/support-tickets/{ticket}/status', [\App\Http\Controllers\Admin\SupportTicketController::class, 'updateStatus'])->name('support-tickets.status');
        Route::post('/support-tickets/{ticket}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('support-tickets.reply');
        Route::get('/support-inquiry-categories', [\App\Http\Controllers\Admin\SupportInquiryCategoryController::class, 'index'])->name('support-inquiry-categories.index');
        Route::post('/support-inquiry-categories', [\App\Http\Controllers\Admin\SupportInquiryCategoryController::class, 'store'])->name('support-inquiry-categories.store');
        Route::put('/support-inquiry-categories/{support_inquiry_category}', [\App\Http\Controllers\Admin\SupportInquiryCategoryController::class, 'update'])->name('support-inquiry-categories.update');
        Route::delete('/support-inquiry-categories/{support_inquiry_category}', [\App\Http\Controllers\Admin\SupportInquiryCategoryController::class, 'destroy'])->name('support-inquiry-categories.destroy');

        // استشارات المدربين (مدفوعة)
        Route::get('/consultations', [\App\Http\Controllers\Admin\ConsultationController::class, 'index'])->name('consultations.index');
        Route::post('/consultations/settings', [\App\Http\Controllers\Admin\ConsultationController::class, 'updateSettings'])->name('consultations.settings');
        Route::get('/consultations/requests/{consultation}', [\App\Http\Controllers\Admin\ConsultationController::class, 'show'])->name('consultations.show');
        Route::post('/consultations/requests/{consultation}/confirm-payment', [\App\Http\Controllers\Admin\ConsultationController::class, 'confirmPayment'])->name('consultations.confirm-payment');
        Route::post('/consultations/requests/{consultation}/schedule', [\App\Http\Controllers\Admin\ConsultationController::class, 'schedule'])->name('consultations.schedule');
        Route::post('/consultations/requests/{consultation}/notes', [\App\Http\Controllers\Admin\ConsultationController::class, 'updateNotes'])->name('consultations.notes');
        Route::post('/consultations/requests/{consultation}/cancel', [\App\Http\Controllers\Admin\ConsultationController::class, 'cancel'])->name('consultations.cancel');
        Route::post('/consultations/requests/{consultation}/complete', [\App\Http\Controllers\Admin\ConsultationController::class, 'markCompleted'])->name('consultations.complete');
        Route::get('/one-to-one-sessions', [\App\Http\Controllers\Admin\OneToOneSessionController::class, 'index'])->name('one-to-one-sessions.index');
        Route::get('/one-to-one-sessions/{oneToOneSession}', [\App\Http\Controllers\Admin\OneToOneSessionController::class, 'show'])->name('one-to-one-sessions.show');

        // أكاديميات التوظيف (عملاء المنصة)
        Route::resource('hiring-academies', \App\Http\Controllers\Admin\HiringAcademyController::class);

        // فرص الأكاديميات
        Route::resource('academy-opportunities', \App\Http\Controllers\Admin\AcademyOpportunityController::class)->except(['show']);
        Route::get('/academy-opportunities/{academy_opportunity}/applications', [\App\Http\Controllers\Admin\AcademyOpportunityController::class, 'applications'])->name('academy-opportunities.applications');
        Route::post('/academy-opportunities/{academy_opportunity}/applications/{application}/status', [\App\Http\Controllers\Admin\AcademyOpportunityController::class, 'updateApplicationStatus'])->name('academy-opportunities.applications.status');
        // مكتب التوظيف: عروض المعلمين المعتمدة للأكاديميات
        Route::get('/academy-opportunities/{academy_opportunity}/recruitment', [\App\Http\Controllers\Admin\RecruitmentDeskController::class, 'show'])->name('academy-opportunities.recruitment');
        Route::get('/academy-opportunities/{academy_opportunity}/recruitment/instructors/search', [\App\Http\Controllers\Admin\RecruitmentDeskController::class, 'searchInstructors'])->name('academy-opportunities.recruitment.instructors.search');
        Route::post('/academy-opportunities/{academy_opportunity}/recruitment/presentations', [\App\Http\Controllers\Admin\RecruitmentDeskController::class, 'storePresentation'])->name('academy-opportunities.recruitment.presentations.store');
        Route::put('/academy-opportunities/{academy_opportunity}/recruitment/presentations/{presentation}', [\App\Http\Controllers\Admin\RecruitmentDeskController::class, 'updatePresentation'])->name('academy-opportunities.recruitment.presentations.update');
        Route::delete('/academy-opportunities/{academy_opportunity}/recruitment/presentations/{presentation}', [\App\Http\Controllers\Admin\RecruitmentDeskController::class, 'destroyPresentation'])->name('academy-opportunities.recruitment.presentations.destroy');
        Route::get('/academy-opportunities/{academy_opportunity}/recruitment/presentations/{presentation}/print', [\App\Http\Controllers\Admin\RecruitmentDeskController::class, 'printForAcademy'])->name('academy-opportunities.recruitment.presentations.print');
        // مكتبة المناهج التفاعلية (إدارة)
        Route::get('/curriculum-library', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'index'])->name('curriculum-library.index');
        Route::get('/curriculum-library/categories', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'categories'])->name('curriculum-library.categories');
        Route::get('/curriculum-library/categories/create', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'createCategory'])->name('curriculum-library.categories.create');
        Route::post('/curriculum-library/categories', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'storeCategory'])->name('curriculum-library.categories.store');
        Route::get('/curriculum-library/categories/{category}/edit', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'editCategory'])->name('curriculum-library.categories.edit');
        Route::put('/curriculum-library/categories/{category}', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'updateCategory'])->name('curriculum-library.categories.update');
        Route::delete('/curriculum-library/categories/{category}', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'destroyCategory'])->name('curriculum-library.categories.destroy');
        Route::get('/curriculum-library/items/create', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'createItem'])->name('curriculum-library.items.create');
        Route::post('/curriculum-library/items', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'storeItem'])->name('curriculum-library.items.store');
        Route::get('/curriculum-library/items/{item}/structure', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'show'])->name('curriculum-library.items.structure');
        Route::post('/curriculum-library/items/{item}/sections', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'storeSection'])->name('curriculum-library.items.sections.store');
        Route::put('/curriculum-library/items/{item}/sections/{section}', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'updateSection'])->name('curriculum-library.items.sections.update');
        Route::delete('/curriculum-library/items/{item}/sections/{section}', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'destroySection'])->name('curriculum-library.items.sections.destroy');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials/presign-upload', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'presignMaterialUpload'])->name('curriculum-library.items.materials.presign-upload');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials/complete-direct', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'completeMaterialDirectUpload'])->name('curriculum-library.items.materials.complete-direct');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials/multipart-init', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'multipartInitMaterial'])->name('curriculum-library.items.materials.multipart-init');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials/multipart-sign-part', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'multipartSignPartMaterial'])->name('curriculum-library.items.materials.multipart-sign-part');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials/multipart-complete', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'multipartCompleteMaterial'])->name('curriculum-library.items.materials.multipart-complete');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials/multipart-abort', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'multipartAbortMaterial'])->name('curriculum-library.items.materials.multipart-abort');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials/multipart-proxy-part', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'multipartProxyUploadPartMaterial'])->name('curriculum-library.items.materials.multipart-proxy-part');
        Route::post('/curriculum-library/items/{item}/sections/{section}/materials', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'storeMaterial'])->name('curriculum-library.items.materials.store');
        Route::put('/curriculum-library/items/{item}/materials/{material}', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'updateMaterial'])->name('curriculum-library.items.materials.update');
        Route::delete('/curriculum-library/items/{item}/materials/{material}', [\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class, 'destroyMaterial'])->name('curriculum-library.items.materials.destroy');
        Route::get('/curriculum-library/items/{item}/edit', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'editItem'])->name('curriculum-library.items.edit');
        // دعم fallback لـ POST في حالة فشل method spoof (_method=PUT) على بعض البيئات
        Route::post('/curriculum-library/items/{item}', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'updateItem']);
        Route::put('/curriculum-library/items/{item}', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'updateItem'])->name('curriculum-library.items.update');
        Route::delete('/curriculum-library/items/{item}', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'destroyItem'])->name('curriculum-library.items.destroy');
        Route::delete('/curriculum-library/items/{item}/files/{file}', [\App\Http\Controllers\Admin\CurriculumLibraryController::class, 'destroyFile'])->name('curriculum-library.items.files.destroy');
        Route::post('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'update'])->middleware('throttle:20,5')->name('subscriptions.update');
        Route::delete('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'destroy'])->middleware('throttle:10,1')->name('subscriptions.destroy');
        Route::post('/subscription-requests/{subscriptionRequest}/approve', [\App\Http\Controllers\Admin\SubscriptionController::class, 'approveRequest'])->name('subscription-requests.approve');
        Route::post('/subscription-requests/{subscriptionRequest}/reject', [\App\Http\Controllers\Admin\SubscriptionController::class, 'rejectRequest'])->name('subscription-requests.reject');
        Route::get('/accounting/instructor-accounts', [\App\Http\Controllers\Admin\InstructorAccountController::class, 'index'])->name('accounting.instructor-accounts.index');
        Route::get('/accounting/instructor-accounts/{instructor}', [\App\Http\Controllers\Admin\InstructorAccountController::class, 'show'])->name('accounting.instructor-accounts.show');

        Route::get('/accounting/reports', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'index'])->name('accounting.reports');
        Route::get('/accounting/reports/export', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'export'])->name('accounting.reports.export');
        Route::get('/accounting/reports/invoices', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'invoices'])->name('accounting.reports.invoices');
        Route::get('/accounting/reports/payments', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'payments'])->name('accounting.reports.payments');
        Route::get('/accounting/reports/transactions', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'transactions'])->name('accounting.reports.transactions');
        Route::get('/accounting/reports/expenses', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'expenses'])->name('accounting.reports.expenses');
        Route::get('/accounting/reports/wallets', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'wallets'])->name('accounting.reports.wallets');
        Route::get('/accounting/reports/orders', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'orders'])->name('accounting.reports.orders');
        Route::get('/accounting/reports/payment-gateway', [\App\Http\Controllers\Admin\AccountingReportsController::class, 'paymentGateway'])->name('accounting.reports.payment-gateway');

        // الماليات الخاصة بالمدربين (قائمة المدربين ثم المطلوب دفعه لكل مدرب)
        Route::prefix('salaries')->name('salaries.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SalaryController::class, 'index'])->name('index');
            Route::get('/instructor/{instructor}', [\App\Http\Controllers\Admin\SalaryController::class, 'instructor'])->name('instructor');
            Route::post('/instructor/{instructor}/pay-now/{agreement}', [\App\Http\Controllers\Admin\SalaryController::class, 'payNowFromAgreement'])->name('pay-now-from-agreement');
            Route::get('/pay/{payment}', [\App\Http\Controllers\Admin\SalaryController::class, 'pay'])->name('pay');
            Route::post('/pay/{payment}', [\App\Http\Controllers\Admin\SalaryController::class, 'markPaid'])->name('mark-paid');
        });

        // التقارير الشاملة
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
            Route::get('/users', [\App\Http\Controllers\Admin\ReportsController::class, 'users'])->name('users');
            Route::get('/courses', [\App\Http\Controllers\Admin\ReportsController::class, 'courses'])->name('courses');
            Route::get('/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'financial'])->name('financial');
            Route::get('/academic', [\App\Http\Controllers\Admin\ReportsController::class, 'academic'])->name('academic');
            Route::get('/activities', [\App\Http\Controllers\Admin\ReportsController::class, 'activities'])->name('activities');
            Route::get('/comprehensive', [\App\Http\Controllers\Admin\ReportsController::class, 'comprehensive'])->name('comprehensive');

            // تصدير التقارير
            Route::get('/export/users', [\App\Http\Controllers\Admin\ReportsController::class, 'exportUsers'])
                ->middleware('throttle:10,5')
                ->name('export.users');
            Route::get('/export/courses', [\App\Http\Controllers\Admin\ReportsController::class, 'exportCourses'])
                ->middleware('throttle:10,5')
                ->name('export.courses');
            Route::get('/export/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'exportFinancial'])
                ->middleware('throttle:10,5')
                ->name('export.financial');
            Route::get('/export/academic', [\App\Http\Controllers\Admin\ReportsController::class, 'exportAcademic'])
                ->middleware('throttle:10,5')
                ->name('export.academic');
            Route::get('/export/comprehensive', [\App\Http\Controllers\Admin\ReportsController::class, 'exportComprehensive'])
                ->middleware('throttle:5,10')
                ->name('export.comprehensive');
        });

        // تقارير n8n للبث المباشر
        Route::prefix('n8n')->name('n8n.')->group(function () {
            Route::get('/live-session-reports', [\App\Http\Controllers\Admin\N8nLiveReportsController::class, 'index'])
                ->name('live-session-reports.index');
            Route::get('/settings', [\App\Http\Controllers\Admin\N8nSettingsController::class, 'index'])
                ->name('settings');
            Route::post('/settings', [\App\Http\Controllers\Admin\N8nSettingsController::class, 'update'])
                ->name('settings.update');
            Route::post('/settings/test-connection', [\App\Http\Controllers\Admin\N8nSettingsController::class, 'testConnection'])
                ->middleware('throttle:10,1')
                ->name('settings.test-connection');
        });
        Route::prefix('installments')->name('installments.')->group(function () {
            Route::resource('plans', \App\Http\Controllers\Admin\InstallmentPlanController::class);
            Route::resource('agreements', \App\Http\Controllers\Admin\InstallmentAgreementController::class);
            Route::post('/agreements/payments/{payment}/mark', [\App\Http\Controllers\Admin\InstallmentAgreementController::class, 'markPayment'])
                ->name('agreements.mark-payment');
        });

        // نظام الاتفاقيات للمدربين
        Route::prefix('agreements')->name('agreements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'store'])
                ->middleware('throttle:20,5')
                ->name('store');
            Route::get('/{agreement}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'show'])->name('show');
            Route::get('/{agreement}/edit', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'edit'])->name('edit');
            Route::put('/{agreement}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'update'])
                ->middleware('throttle:20,5')
                ->name('update');
            Route::delete('/{agreement}', [\App\Http\Controllers\Admin\InstructorAgreementController::class, 'destroy'])
                ->middleware('throttle:10,1')
                ->name('destroy');
        });

        // نظام اتفاقيات الموظفين
        Route::prefix('employee-agreements')->name('employee-agreements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'store'])
                ->middleware('throttle:20,5')
                ->name('store');
            Route::post('payments/{payment}/mark-paid', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'markPaymentPaid'])->name('payments.mark-paid');
            Route::post('{employeeAgreement}/payments', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'storePayment'])->name('payments.store');
            Route::get('/{employeeAgreement}', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'show'])->name('show');
            Route::get('/{employeeAgreement}/edit', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'edit'])->name('edit');
            Route::put('/{employeeAgreement}', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'update'])
                ->middleware('throttle:20,5')
                ->name('update');
            Route::delete('/{employeeAgreement}', [\App\Http\Controllers\Admin\EmployeeAgreementController::class, 'destroy'])
                ->middleware('throttle:10,1')
                ->name('destroy');
        });

        // إدارة طلبات السحب للمدربين
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'index'])->name('index');
            Route::get('/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'show'])->name('show');
            Route::post('/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'approve'])
                ->middleware('throttle:10,1')
                ->name('approve');
            Route::post('/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'reject'])
                ->middleware('throttle:10,1')
                ->name('reject');
            Route::post('/{withdrawal}/complete', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'complete'])
                ->middleware('throttle:10,1')
                ->name('complete');
        });

        // إدارة التسويق
        Route::get('/personal-branding', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'index'])->name('personal-branding.index');
        Route::resource('popup-ads', \App\Http\Controllers\Admin\PopupAdController::class)->except(['show']);
        Route::get('/personal-branding/{personal_branding}/edit', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'edit'])->name('personal-branding.edit');
        Route::put('/personal-branding/{personal_branding}', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'update'])
            ->middleware('throttle:30,1')
            ->name('personal-branding.update');
        Route::delete('/personal-branding/{personal_branding}', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'destroy'])
            ->middleware('throttle:20,1')
            ->name('personal-branding.destroy');
        Route::get('/personal-branding/{personal_branding}', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'show'])->name('personal-branding.show');
        Route::post('/personal-branding/{personal_branding}/approve', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'approve'])->name('personal-branding.approve');
        Route::post('/personal-branding/{personal_branding}/reject', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'reject'])->name('personal-branding.reject');
        Route::post('/personal-branding/{personal_branding}/send-back', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'sendBackForReview'])->name('personal-branding.send-back');
        Route::post('/personal-branding/{personal_branding}/consultation-pricing', [\App\Http\Controllers\Admin\InstructorPersonalBrandingController::class, 'updateConsultationPricing'])->name('personal-branding.consultation-pricing');
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        Route::get('/marketing/student-wallet-credit', [\App\Http\Controllers\Admin\StudentWalletCreditController::class, 'create'])
            ->name('marketing.student-wallet-credit.create');
        Route::post('/marketing/student-wallet-credit', [\App\Http\Controllers\Admin\StudentWalletCreditController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('marketing.student-wallet-credit.store');
        Route::get('/coupon-commissions', [\App\Http\Controllers\Admin\CouponCommissionController::class, 'index'])->name('coupon-commissions.index');
        Route::post('/coupon-commissions/{accrual}/expense', [\App\Http\Controllers\Admin\CouponCommissionController::class, 'storeExpense'])
            ->middleware('throttle:20,1')
            ->name('coupon-commissions.store-expense');
        // إدارة برامج الإحالات
        Route::resource('referral-programs', \App\Http\Controllers\Admin\ReferralProgramController::class);
        Route::post('/referral-programs/{referralProgram}/set-default', [\App\Http\Controllers\Admin\ReferralProgramController::class, 'setDefault'])
            ->middleware('throttle:30,1')
            ->name('referral-programs.set-default');

        // إدارة الإحالات
        Route::prefix('referrals')->name('referrals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReferralController::class, 'index'])->name('index');
            Route::get('/{referral}', [\App\Http\Controllers\Admin\ReferralController::class, 'show'])->name('show');
        });
        Route::prefix('loyalty')->name('loyalty.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LoyaltyController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\LoyaltyController::class, 'store'])->name('store');
            Route::get('/{loyaltyProgram}', [\App\Http\Controllers\Admin\LoyaltyController::class, 'show'])->name('show');
            Route::put('/{loyaltyProgram}', [\App\Http\Controllers\Admin\LoyaltyController::class, 'update'])->name('update');
        });

        // إدارة الشهادات والإنجازات (مسارات محددة قبل الـ resource)
        Route::get('certificates/user/{user}/courses', [\App\Http\Controllers\Admin\CertificateController::class, 'userCourses'])
            ->name('certificates.user-courses');
        Route::get('certificates/design', [\App\Http\Controllers\Admin\CertificateController::class, 'design'])
            ->name('certificates.design');
        Route::get('certificates/preview-sample', [\App\Http\Controllers\Admin\CertificateController::class, 'previewSample'])
            ->name('certificates.preview-sample');
        Route::get('certificates/preview-draft', [\App\Http\Controllers\Admin\CertificateController::class, 'previewDraft'])
            ->name('certificates.preview-draft');
        Route::get('certificates/prefill-data', [\App\Http\Controllers\Admin\CertificateController::class, 'prefillData'])
            ->name('certificates.prefill-data');
        Route::get('certificates/{certificate}/file', [\App\Http\Controllers\Admin\CertificateController::class, 'file'])
            ->name('certificates.file');
        Route::get('certificates/{certificate}/download', [\App\Http\Controllers\Admin\CertificateController::class, 'download'])
            ->name('certificates.download');
        Route::resource('certificates', \App\Http\Controllers\Admin\CertificateController::class);
        Route::resource('achievements', \App\Http\Controllers\Admin\AchievementController::class);
        Route::resource('badges', \App\Http\Controllers\Admin\BadgeController::class);
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class);

        // إدارة المحاضرات (مسار الكورس قبل الـ resource لتفادي التعارض)
        Route::get('/lectures/course/{course}', [\App\Http\Controllers\Admin\LectureController::class, 'indexByCourse'])->name('lectures.by-course');
        Route::resource('lectures', \App\Http\Controllers\Admin\LectureController::class);

        // إدارة الحضور
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
            Route::get('/lecture/{lecture}', [\App\Http\Controllers\Admin\AttendanceController::class, 'showLectureAttendance'])->name('lecture');
            Route::post('/lecture/{lecture}/upload-teams', [\App\Http\Controllers\Admin\AttendanceController::class, 'uploadTeamsFile'])->name('upload-teams');
        });

        // إدارة الأداء
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('index');
            Route::post('/clear-cache', [\App\Http\Controllers\Admin\PerformanceController::class, 'clearCache'])
                ->middleware('throttle:10,1')
                ->name('clear-cache');
            Route::post('/optimize-cache', [\App\Http\Controllers\Admin\PerformanceController::class, 'optimizeCache'])
                ->middleware('throttle:5,5')
                ->name('optimize-cache');
            Route::post('/clear-temp-files', [\App\Http\Controllers\Admin\PerformanceController::class, 'clearTempFiles'])
                ->middleware('throttle:5,5')
                ->name('clear-temp-files');
            Route::post('/optimize-database', [\App\Http\Controllers\Admin\PerformanceController::class, 'optimizeDatabase'])
                ->middleware('throttle:3,10')
                ->name('optimize-database');
        });

        // ===== نظام البث المباشر (Admin) =====
        Route::prefix('live-sessions')->name('live-sessions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LiveSessionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\LiveSessionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\LiveSessionController::class, 'store'])->name('store');
            Route::get('/{liveSession}', [\App\Http\Controllers\Admin\LiveSessionController::class, 'show'])->name('show');
            Route::get('/{liveSession}/edit', [\App\Http\Controllers\Admin\LiveSessionController::class, 'edit'])->name('edit');
            Route::put('/{liveSession}', [\App\Http\Controllers\Admin\LiveSessionController::class, 'update'])->name('update');
            Route::delete('/{liveSession}', [\App\Http\Controllers\Admin\LiveSessionController::class, 'destroy'])->name('destroy');
            Route::post('/{liveSession}/force-end', [\App\Http\Controllers\Admin\LiveSessionController::class, 'forceEnd'])->name('force-end');
            Route::post('/{liveSession}/cancel', [\App\Http\Controllers\Admin\LiveSessionController::class, 'cancel'])->name('cancel');
        });

        Route::prefix('live-servers')->name('live-servers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LiveServerController::class, 'index'])->name('index');
            Route::get('/control', [\App\Http\Controllers\Admin\LiveServerController::class, 'control'])->name('control');
            Route::get('/create', [\App\Http\Controllers\Admin\LiveServerController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\LiveServerController::class, 'store'])->name('store');
            Route::get('/{liveServer}/edit', [\App\Http\Controllers\Admin\LiveServerController::class, 'edit'])->name('edit');
            Route::get('/{liveServer}/ssh-browse', [\App\Http\Controllers\Admin\LiveServerController::class, 'sshBrowse'])->name('ssh-browse');
            Route::get('/{liveServer}/ssh-file', [\App\Http\Controllers\Admin\LiveServerController::class, 'sshFile'])->name('ssh-file');
            Route::put('/{liveServer}', [\App\Http\Controllers\Admin\LiveServerController::class, 'update'])->name('update');
            Route::delete('/{liveServer}', [\App\Http\Controllers\Admin\LiveServerController::class, 'destroy'])->name('destroy');
            Route::post('/{liveServer}/toggle-status', [\App\Http\Controllers\Admin\LiveServerController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{liveServer}/test-connection', [\App\Http\Controllers\Admin\LiveServerController::class, 'testConnection'])->name('test-connection');
            Route::post('/{liveServer}/set-default', [\App\Http\Controllers\Admin\LiveServerController::class, 'setAsDefault'])->name('set-default');
        });

        Route::prefix('live-recordings')->name('live-recordings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LiveRecordingController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\LiveRecordingController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\LiveRecordingController::class, 'store'])->name('store');
            Route::get('/{liveRecording}', [\App\Http\Controllers\Admin\LiveRecordingController::class, 'show'])->name('show');
            Route::put('/{liveRecording}', [\App\Http\Controllers\Admin\LiveRecordingController::class, 'update'])->name('update');
            Route::post('/{liveRecording}/toggle-publish', [\App\Http\Controllers\Admin\LiveRecordingController::class, 'togglePublish'])->name('toggle-publish');
            Route::delete('/{liveRecording}', [\App\Http\Controllers\Admin\LiveRecordingController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('classroom-recordings')->name('classroom-recordings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ClassroomRecordingController::class, 'index'])->name('index');
        });

        Route::prefix('live-settings')->name('live-settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LiveSettingController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\LiveSettingController::class, 'update'])->name('update');
        });

    });

    // المهام (للجميع)
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);

    // مسارات الطلاب - محمية للطلاب فقط
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        Route::resource('invoices', \App\Http\Controllers\Student\InvoiceController::class)->only(['index', 'show']);
        Route::resource('wallet', \App\Http\Controllers\Student\WalletController::class)->only(['index', 'show']);
        Route::resource('certificates', \App\Http\Controllers\Student\CertificateController::class)->only(['index', 'show']);
        Route::get('certificates/{certificate}/file', [\App\Http\Controllers\Student\CertificateController::class, 'file'])
            ->name('certificates.file');
        Route::resource('achievements', \App\Http\Controllers\Student\AchievementController::class)->only(['index', 'show']);
        Route::resource('assignments', \App\Http\Controllers\Student\AssignmentController::class)->only(['index', 'show']);
        Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\Student\AssignmentController::class, 'submit'])
            ->middleware(['ownership:assignment,assignment'])
            ->name('assignments.submit');
        Route::post('/assignments/{assignment}/submission/presign-upload', [\App\Http\Controllers\Student\AssignmentController::class, 'presignSubmissionUpload'])
            ->middleware(['ownership:assignment,assignment', 'throttle:45,1'])
            ->name('assignments.submission.presign-upload');
        Route::post('/assignments/{assignment}/submission/complete-upload', [\App\Http\Controllers\Student\AssignmentController::class, 'completeSubmissionDirectUpload'])
            ->middleware(['ownership:assignment,assignment', 'throttle:90,1'])
            ->name('assignments.submission.complete-upload');
        Route::post('/assignments/{assignment}/submission/abandon-upload', [\App\Http\Controllers\Student\AssignmentController::class, 'abandonSubmissionDirectUpload'])
            ->middleware(['ownership:assignment,assignment', 'throttle:60,1'])
            ->name('assignments.submission.abandon-upload');
        Route::delete('/assignments/{assignment}/submission', [\App\Http\Controllers\Student\AssignmentController::class, 'destroySubmission'])
            ->middleware(['ownership:assignment,assignment'])
            ->name('assignments.submission.destroy');
        Route::resource('tasks', \App\Http\Controllers\Student\TaskController::class);

        // ===== البث المباشر (Student) =====
        Route::prefix('live-sessions')->name('live-sessions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\LiveSessionController::class, 'index'])->name('index');
            Route::get('/{liveSession}', [\App\Http\Controllers\Student\LiveSessionController::class, 'show'])->name('show');
            Route::post('/{liveSession}/join', [\App\Http\Controllers\Student\LiveSessionController::class, 'join'])->name('join');
            Route::post('/{liveSession}/leave', [\App\Http\Controllers\Student\LiveSessionController::class, 'leave'])->name('leave');
            Route::get('/{liveSession}/status', [\App\Http\Controllers\Student\LiveSessionController::class, 'status'])->name('status');
            Route::post('/{liveSession}/share-annotation', [\App\Http\Controllers\Student\LiveSessionController::class, 'pushShareAnnotation'])
                ->middleware('throttle:90,1')
                ->name('share-annotation');
        });
        // تسجيلات الجلسات (R2 — عرض للمنشور فقط)
        Route::get('/live-recordings', [\App\Http\Controllers\Student\LiveRecordingController::class, 'index'])->name('live-recordings.index');
        Route::get('/live-recordings/{liveRecording}', [\App\Http\Controllers\Student\LiveRecordingController::class, 'show'])->name('live-recordings.show');
    });

    // مسارات المدرسين
    Route::prefix('instructor')->name('instructor.')->middleware(['auth', 'role:instructor|teacher'])->group(function () {
        Route::get('/calendar', [\App\Http\Controllers\Instructor\CalendarController::class, 'index'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Instructor\CalendarController::class, 'getEvents'])->name('calendar.events');
        Route::get('/consultations', [\App\Http\Controllers\Instructor\ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/{consultation}', [\App\Http\Controllers\Instructor\ConsultationController::class, 'show'])->name('consultations.show');
        Route::get('/one-to-one-sessions', [\App\Http\Controllers\Instructor\OneToOneSessionController::class, 'index'])->name('one-to-one-sessions.index');
        Route::get('/one-to-one-availability', [\App\Http\Controllers\Instructor\OneToOneAvailabilityController::class, 'index'])->name('one-to-one-availability.index');
        Route::post('/one-to-one-availability', [\App\Http\Controllers\Instructor\OneToOneAvailabilityController::class, 'update'])->name('one-to-one-availability.update');
        Route::get('/one-to-one-sessions/{oneToOneSession}', [\App\Http\Controllers\Instructor\OneToOneSessionController::class, 'show'])->name('one-to-one-sessions.show');
        Route::post('/one-to-one-sessions/{oneToOneSession}/schedule', [\App\Http\Controllers\Instructor\OneToOneSessionController::class, 'schedule'])->name('one-to-one-sessions.schedule');
        Route::post('/one-to-one-sessions/{oneToOneSession}/complete', [\App\Http\Controllers\Instructor\OneToOneSessionController::class, 'complete'])->name('one-to-one-sessions.complete');
        Route::get('/classroom/{meeting}', [\App\Http\Controllers\Student\ClassroomController::class, 'show'])->name('classroom.show');
        Route::post('/classroom/{meeting}/start', [\App\Http\Controllers\Student\ClassroomController::class, 'startMeeting'])->name('classroom.start-meeting');
        Route::get('/classroom/room/{meeting}', [\App\Http\Controllers\Student\ClassroomController::class, 'room'])->name('classroom.room');
        Route::get('/classroom/room/{meeting}/recording-upload', [\App\Http\Controllers\Student\ClassroomController::class, 'recordingUploadTab'])->name('classroom.recording.upload-tab');
        Route::post('/classroom/{meeting}/participant-whiteboard', [\App\Http\Controllers\Student\ClassroomController::class, 'updateParticipantWhiteboard'])->name('classroom.participant-whiteboard');
        Route::get('/classroom/{meeting}/share-annotations', [\App\Http\Controllers\Student\ClassroomController::class, 'shareAnnotations'])->name('classroom.share-annotations');
        Route::post('/classroom/room/{meeting}/end', [\App\Http\Controllers\Student\ClassroomController::class, 'end'])->name('classroom.end');
        Route::post('/classroom/{meeting}/recording/upload', [\App\Http\Controllers\Student\ClassroomController::class, 'uploadRecording'])->name('classroom.recording.upload');
        Route::post('/classroom/{meeting}/recording/presign', [\App\Http\Controllers\Student\ClassroomController::class, 'presignRecordingUpload'])->name('classroom.recording.presign');
        Route::post('/classroom/{meeting}/recording/complete', [\App\Http\Controllers\Student\ClassroomController::class, 'completeDirectRecordingUpload'])->name('classroom.recording.complete');
        Route::post('/classroom/{meeting}/recording-audio/presign', [\App\Http\Controllers\Student\ClassroomController::class, 'presignAudioUpload'])->name('classroom.recording-audio.presign');
        Route::post('/classroom/{meeting}/recording-audio/upload', [\App\Http\Controllers\Student\ClassroomController::class, 'uploadAudioRecording'])->name('classroom.recording-audio.upload');
        Route::post('/classroom/{meeting}/recording-audio/complete', [\App\Http\Controllers\Student\ClassroomController::class, 'completeDirectAudioUpload'])->name('classroom.recording-audio.complete');
        Route::post('/classroom/{meeting}/ai-report', [\App\Http\Controllers\Student\ClassroomController::class, 'generateAiReport'])->name('classroom.ai-report');

        // بروفايل المدرب
        Route::get('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'update'])->name('profile.update');

        // التسويق الشخصي (البراندينغ) — ملف تعريفي للمدرب للمراجعة والنشر
        Route::get('/personal-branding', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'edit'])->name('personal-branding.edit');
        Route::put('/personal-branding', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'update'])->name('personal-branding.update');
        Route::post('/personal-branding/submit', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'submit'])->name('personal-branding.submit');

        Route::resource('courses', \App\Http\Controllers\Instructor\CourseController::class)->only(['index', 'show']);
        Route::get('courses/{course}/curriculum', [\App\Http\Controllers\Instructor\CurriculumController::class, 'index'])->name('courses.curriculum');
        Route::post('courses/{course}/curriculum/exams', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeExamFromCurriculum'])->name('courses.curriculum.exams.store');
        Route::post('courses/{course}/curriculum/assignments', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeAssignmentFromCurriculum'])->name('courses.curriculum.assignments.store');
        Route::post('courses/{course}/sections', [\App\Http\Controllers\Instructor\CurriculumController::class, 'storeSection'])->name('courses.sections.store');
        Route::put('sections/{section}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateSection'])->name('sections.update');
        Route::delete('sections/{section}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'destroySection'])->name('sections.destroy');
        Route::post('sections/{section}/items', [\App\Http\Controllers\Instructor\CurriculumController::class, 'addItem'])->name('sections.items.store');
        Route::delete('curriculum-items/{item}', [\App\Http\Controllers\Instructor\CurriculumController::class, 'removeItem'])->name('curriculum-items.destroy');
        Route::post('curriculum-items/{item}/move', [\App\Http\Controllers\Instructor\CurriculumController::class, 'moveItem'])->name('curriculum-items.move');
        Route::post('courses/{course}/sections/order', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateSectionsOrder'])->name('courses.sections.order');
        Route::post('sections/{section}/items/order', [\App\Http\Controllers\Instructor\CurriculumController::class, 'updateItemsOrder'])->name('sections.items.order');
        Route::get('lectures/{lecture}/video-questions', [\App\Http\Controllers\Instructor\LectureVideoQuestionController::class, 'index'])->name('lectures.video-questions.index');
        Route::post('lectures/{lecture}/video-questions', [\App\Http\Controllers\Instructor\LectureVideoQuestionController::class, 'store'])->name('lectures.video-questions.store');
        Route::delete('lectures/{lecture}/video-questions/{videoQuestion}', [\App\Http\Controllers\Instructor\LectureVideoQuestionController::class, 'destroy'])->name('lectures.video-questions.destroy');

        // تم إلغاء نظام الدروس — الاعتماد على المحاضرات فقط (إعادة توجيه الروابط القديمة)
        Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
            Route::get('/', fn ($course) => redirect()->route('instructor.courses.curriculum', $course))->name('index');
            Route::get('/create', fn ($course) => redirect()->route('instructor.lectures.index'))->name('create');
            Route::post('/', fn ($course) => redirect()->route('instructor.courses.curriculum', $course)->with('info', 'تم إلغاء نظام الدروس؛ استخدم المحاضرات.'))->name('store');
            Route::get('/{lesson}', fn ($course) => redirect()->route('instructor.lectures.index'))->name('show');
            Route::get('/{lesson}/edit', fn ($course) => redirect()->route('instructor.lectures.index'))->name('edit');
            Route::put('/{lesson}', fn ($course) => redirect()->route('instructor.courses.curriculum', $course))->name('update');
            Route::delete('/{lesson}', fn ($course) => redirect()->route('instructor.courses.curriculum', $course))->name('destroy');
            Route::post('/{lesson}/toggle-status', fn ($course) => redirect()->route('instructor.courses.curriculum', $course))->name('toggle-status');
            Route::post('/reorder', fn ($course) => redirect()->route('instructor.courses.curriculum', $course))->name('reorder');
        });

        Route::get('/api/courses/{course}/lessons-list', fn ($course) => response()->json([]));

        // API لدروس الكورس للمدرب
        Route::resource('lectures', \App\Http\Controllers\Instructor\LectureController::class);
        Route::post('/lectures/{lecture}/sync-teams-attendance', [\App\Http\Controllers\Instructor\LectureController::class, 'syncTeamsAttendance'])->name('lectures.sync-teams-attendance');
        Route::post('/lectures/{lecture}/update-attendance', [\App\Http\Controllers\Instructor\LectureController::class, 'updateAttendance'])->name('lectures.update-attendance');

        // المسار التعليمي ملغى — التوجيه إلى لوحة المدرب
        Route::get('/learning-path', function () {
            return redirect()->route('instructor.dashboard', [], 302);
        })->name('learning-path.index');
        Route::get('/learning-path/{slug}', function () {
            return redirect()->route('instructor.dashboard', [], 302);
        })->name('learning-path.show');
        Route::post('/lectures/{lecture}/update-status', [\App\Http\Controllers\Instructor\LectureController::class, 'updateStatus'])->name('lectures.update-status');
        Route::resource('assignments', \App\Http\Controllers\Instructor\AssignmentController::class);
        Route::get('/assignments/{assignment}/submissions', [\App\Http\Controllers\Instructor\AssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::post('/assignments/{assignment}/grade/{submission}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'grade'])->name('assignments.grade');
        Route::resource('exams', \App\Http\Controllers\Instructor\ExamController::class);
        Route::get('exams/{exam}/questions', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'manage'])->name('exams.questions.manage');
        Route::post('exams/{exam}/questions/from-bank', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'addFromBank'])->name('exams.questions.add-from-bank');
        Route::post('exams/{exam}/questions/new', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'createNew'])->name('exams.questions.create-new');
        Route::delete('exams/{exam}/questions/{question}', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'remove'])->name('exams.questions.remove');
        Route::post('exams/{exam}/questions/reorder', [\App\Http\Controllers\Instructor\ExamQuestionController::class, 'reorder'])->name('exams.questions.reorder');

        // بنك الأسئلة
        Route::resource('question-banks', \App\Http\Controllers\Instructor\QuestionBankController::class);
        Route::post('question-banks/{questionBank}/questions', [\App\Http\Controllers\Instructor\QuestionController::class, 'store'])->name('question-banks.questions.store');
        Route::get('question-banks/{questionBank}/questions/create', [\App\Http\Controllers\Instructor\QuestionController::class, 'create'])->name('question-banks.questions.create');
        Route::resource('questions', \App\Http\Controllers\Instructor\QuestionController::class)->except(['create', 'store']);
        Route::get('/attendance', [\App\Http\Controllers\Instructor\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/lecture/{lecture}', [\App\Http\Controllers\Instructor\AttendanceController::class, 'showLecture'])->name('attendance.lecture');
        Route::resource('tasks', \App\Http\Controllers\Instructor\TaskController::class);
        Route::get('/tasks/lectures', [\App\Http\Controllers\Instructor\TaskController::class, 'getLectures'])->name('tasks.lectures');
        Route::post('/tasks/{task}/deliverables', [\App\Http\Controllers\Instructor\TaskController::class, 'submitDeliverable'])->name('tasks.submit-deliverable');
        Route::put('/tasks/{task}/progress', [\App\Http\Controllers\Instructor\TaskController::class, 'updateProgress'])->name('tasks.update-progress');

        // تقديم طلبات للإدارة
        Route::prefix('management-requests')->name('management-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'store'])->name('store');
            Route::get('/{managementRequest}', [\App\Http\Controllers\Instructor\ManagementRequestController::class, 'show'])->name('show');
        });

        // نظام الاتفاقيات للمدرب
        Route::prefix('agreements')->name('agreements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\AgreementController::class, 'index'])->name('index');
            Route::get('/{agreement}/export-activations', [\App\Http\Controllers\Instructor\AgreementController::class, 'exportActivations'])->name('export-activations');
            Route::get('/{agreement}', [\App\Http\Controllers\Instructor\AgreementController::class, 'show'])->name('show');
        });

        // حساب التحويل (بيانات استلام المبالغ)
        Route::get('/transfer-account', [\App\Http\Controllers\Instructor\TransferAccountController::class, 'index'])->name('transfer-account.index');
        Route::post('/transfer-account', [\App\Http\Controllers\Instructor\TransferAccountController::class, 'store'])->name('transfer-account.store');

        // طلبات السحب للمدرب
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'store'])->name('store');
            Route::get('/{withdrawal}', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'show'])->name('show');
            Route::post('/{withdrawal}/cancel', [\App\Http\Controllers\Instructor\WithdrawalRequestController::class, 'cancel'])->name('cancel');
        });

        // ===== البث المباشر (Instructor) =====
        Route::prefix('live-sessions')->name('live-sessions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'store'])->name('store');
            Route::get('/{liveSession}', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'show'])->name('show');
            Route::post('/{liveSession}/start', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'start'])->name('start');
            Route::get('/{liveSession}/room', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'room'])->name('room');
            Route::post('/{liveSession}/student-whiteboard', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'updateStudentWhiteboard'])->name('student-whiteboard');
            Route::get('/{liveSession}/share-annotations', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'shareAnnotations'])->name('share-annotations');
            Route::post('/{liveSession}/audio/presign', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'presignAudioUpload'])->name('audio.presign');
            Route::post('/{liveSession}/audio/complete', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'completeAudioUpload'])->name('audio.complete');
            Route::post('/{liveSession}/ai-report', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'generateAiReport'])->name('ai-report');
            Route::post('/{liveSession}/end', [\App\Http\Controllers\Instructor\LiveSessionController::class, 'end'])->name('end');
        });

    });
});
