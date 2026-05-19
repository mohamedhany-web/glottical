<?php

namespace App\Providers;

use App\Services\AdminPanelBranding;
use App\Services\PublicFooterSettings;
use App\Support\ErrorPageContext;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** مسار صورة خلفية صفحات تسجيل الدخول/إنشاء الحساب في التخزين (نفس أسلوب مسارات التعلم) */
    public const AUTH_BACKGROUND_STORAGE_PATH = 'auth-pages/brainstorm-meeting.jpg';

    /** مسار لوجو المنصة في التخزين (يُعرض من /storage/ مثل الكورسات والصور) */
    public const SITE_LOGO_STORAGE_PATH = 'site/logo.png';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         | مهم: وسيط throttle الافتراضي (مثل throttle:90,1) يستخدم نفس مفتاح العداد لكل المسارات
         | للمستخدم المسجّل (معرّف المستخدم فقط). طلبات poll الإشعارات كل 5 ثوانٍ تملأ ذلك العداد
         | فتُرفض مسارات أخرى ذات حد أقل (مثل throttle:10,1) وتظهر 429 بلا سبب واضح.
         | نفصل poll الشريط عن باقي الحدود بمحددات مسمّاة.
         */
        RateLimiter::for('admin-nav-poll', function (Request $request) {
            $id = $request->user()?->getAuthIdentifier() ?? $request->ip();

            return Limit::perMinute(120)->by('admin-nav-poll:'.$id);
        });

        RateLimiter::for('employee-nav-poll', function (Request $request) {
            $id = $request->user()?->getAuthIdentifier() ?? $request->ip();

            return Limit::perMinute(120)->by('employee-nav-poll:'.$id);
        });

        RateLimiter::for('admin-employee-notification-store', function (Request $request) {
            $id = $request->user()?->getAuthIdentifier() ?? $request->ip();

            return Limit::perMinute(30)->by('admin-employee-notification-store:'.$id);
        });

        // تحميل دوال المساعدة (تُحمّل من هنا لضمان توفرها حتى قبل composer dump-autoload)
        $filesystemHelper = app_path('Helpers/FilesystemHelper.php');
        if (file_exists($filesystemHelper)) {
            require_once $filesystemHelper;
        }

        // ضمان وجود صورة الخلفية في التخزين (نفس مسار صور المسارات) لتعمل على السيرفر عبر /storage/
        $authStoragePath = self::AUTH_BACKGROUND_STORAGE_PATH;
        $disk = Storage::disk('public');
        if (! $disk->exists($authStoragePath)) {
            $sources = ['images/brainstorm-meeting.jpg', 'images/brainstorm-meeting.png'];
            foreach ($sources as $source) {
                $publicPath = public_path($source);
                if (File::isFile($publicPath)) {
                    $dir = dirname($authStoragePath);
                    if (! $disk->exists($dir)) {
                        $disk->makeDirectory($dir);
                    }
                    $disk->put($authStoragePath, File::get($publicPath));
                    break;
                }
            }
        }

        // صورة خلفية صفحات تسجيل الدخول وإنشاء الحساب: دائماً من التخزين (نفس عرض صور المسارات)
        View::composer(['auth.login', 'auth.register', 'auth.forgot-password'], function ($view) {
            $path = self::AUTH_BACKGROUND_STORAGE_PATH;
            if (Storage::disk('public')->exists($path)) {
                $view->with('authBackgroundUrl', storage_public_url($path));
            } else {
                $view->with('authBackgroundUrl', asset('images/brainstorm-meeting.jpg'));
            }
            $view->with('adminPanelLogoUrl', AdminPanelBranding::logoPublicUrl());
        });

        // لوجو المنصة: نسخ إلى التخزين إن لم يكن موجوداً (نفس أسلوب صورة تسجيل الدخول)
        $logoPath = self::SITE_LOGO_STORAGE_PATH;
        if (! $disk->exists($logoPath)) {
            $logoSource = public_path('logo-removebg-preview.png');
            if (File::isFile($logoSource)) {
                $dir = dirname($logoPath);
                if (! $disk->exists($dir)) {
                    $disk->makeDirectory($dir);
                }
                $disk->put($logoPath, File::get($logoSource));
            }
        }
        // حساب رابط اللوجو عند عرض الصفحة (مثل authBackgroundUrl) لضمان ظهور الصورة مع الطلب الحالي
        View::composer(['layouts.instructor-sidebar', 'layouts.student-sidebar', 'layouts.app', 'layouts.admin'], function ($view) use ($disk, $logoPath) {
            $url = AdminPanelBranding::logoPublicUrl() ?: asset('logo-removebg-preview.png');
            $view->with('platformLogoUrl', $url);
        });

        // إجبار روابط الموقع على HTTPS في الإنتاج (حل مشكلة عدم ظهور الصور عند Mixed Content)
        if ($this->app->environment('production') && config('app.url')) {
            URL::forceScheme('https');
            $publicUrl = config('filesystems.disks.public.url');
            if ($publicUrl && str_starts_with($publicUrl, 'http://')) {
                config(['filesystems.disks.public.url' => 'https://'.substr($publicUrl, 7)]);
            }
        }

        /*
         | مهم (محلي/تطوير): إذا كان APP_URL يختلف عن Host الحقيقي في المتصفح (مثلاً localhost مقابل 127.0.0.1)
         | فقد لا يُعاد إرسال كوكي الجلسة بشكل صحيح في بعض الحالات ويظهر 419 عند إرسال النماذج.
         | نُجبر Laravel على استخدام نفس أصل الطلب الحالي في بيئة التطوير فقط.
         */
        $this->app->booted(function (): void {
            if ($this->app->runningInConsole()) {
                return;
            }

            try {
                $request = request();
                if (! $request) {
                    return;
                }

                $root = rtrim($request->root(), '/');
                URL::forceRootUrl($root);
                config(['filesystems.disks.public.url' => $root.'/storage']);
            } catch (\Throwable $e) {
            }
        });

        View::composer('*', function ($view): void {
            if (app()->runningInConsole()) {
                return;
            }
            $view->with('storageBaseUrl', storage_base_url());
        });

        // Observers للنماذج - مع تحسينات الأداء
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\StudentCourseEnrollment::observe(\App\Observers\EnrollmentObserver::class);
        \App\Models\Exam::observe(\App\Observers\ExamObserver::class);
        \App\Models\AdvancedCourse::observe(\App\Observers\AdvancedCourseObserver::class);
        \App\Models\ExamAttempt::observe(\App\Observers\ExamAttemptObserver::class);

        // Observers للتقويم والإشعارات
        \App\Models\Lecture::observe(\App\Observers\LectureObserver::class);
        \App\Models\Assignment::observe(\App\Observers\AssignmentObserver::class);
        \App\Models\LectureAssignment::observe(\App\Observers\LectureAssignmentObserver::class);

        // تفعيل Event Listeners لتسجيل النشاطات
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogLoginActivity::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            \App\Listeners\LogLogoutActivity::class
        );

        // Security Event Listeners
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Failed::class,
            [\App\Listeners\SecurityEventListener::class, 'handleFailedLogin']
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            [\App\Listeners\SecurityEventListener::class, 'handleSuccessfulLogin']
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            [\App\Listeners\SecurityEventListener::class, 'handleLogout']
        );

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ? true : null;
            }
        });

        View::composer(
            [
                'welcome',
                'public.services.index',
                'public.services.show',
                'public.pricing',
                'public.portfolio.index',
            ],
            function ($view) {
                $view->with('publicFooter', PublicFooterSettings::payload());
            }
        );

        View::composer('layouts.admin', function ($view) {
            $view->with('adminPanelLogoUrl', AdminPanelBranding::logoPublicUrl());
        });

        // تحميل أدوار الموظف وصلاحياتها مرة واحدة لعرض السايدبار (موظف + أدمن) بشكل موثوق بعد تعديل الدور
        View::composer(['layouts.admin', 'layouts.employee'], function () {
            if (Auth::check()) {
                Auth::user()->loadMissing(['roles.permissions']);
            }
        });

        View::composer('components.unified-navbar', function ($view) {
            $view->with([
                'navbarLogoUrl' => AdminPanelBranding::logoPublicUrl(),
                'navbarBrandTagline' => PublicFooterSettings::payload()['brand_tagline'],
            ]);
        });

        View::composer('errors.*', function ($view) {
            $view->with([
                'errorHomeUrl' => ErrorPageContext::homeUrl(),
                'errorHomeLabel' => ErrorPageContext::homeLabel(),
            ]);
        });
    }
}
