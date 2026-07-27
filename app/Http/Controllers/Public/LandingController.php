<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Certificate;
use App\Models\InstructorProfile;
use App\Models\PopupAd;
use App\Models\SiteTestimonial;
use App\Models\SiteService;
use App\Models\User;
use App\Services\CourseSubscriptionService;
use App\Services\SeoAssets;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * الصفحة الرئيسية (Landing).
 * اللغة تُحدد عبر Middleware SetLandingLocale من ?lang= أو الجلسة.
 */
class LandingController extends Controller
{
    public function index(): View
    {
        $popupAd = null;
        $ad = PopupAd::activeNow()->first();
        if ($ad) {
            $key = 'popup_ad_' . $ad->id . '_views';
            $views = (int) session($key, 0);
            if ($views < $ad->max_views_per_visitor) {
                session([$key => $views + 1]);
                $popupAd = $ad;
            }
        }

        $locale = app()->getLocale();
        $buildHomePayload = function () {
            $featuredCourses = AdvancedCourse::query()
                ->where('is_active', true)
                ->with(['instructor:id,name', 'courseCategory:id,name'])
                ->withCount('lessons')
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->limit(12)
                ->get();

            $oneToOneCourses = AdvancedCourse::query()
                ->where('is_active', true)
                ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
                ->with(['instructor:id,name', 'courseCategory:id,name'])
                ->withCount('lessons')
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            $homeCategories = $this->buildHomeCategories();
            $homeInstructors = $this->approvedActiveInstructorProfiles(8);

            $homeTestimonials = SiteTestimonial::query()
                ->active()
                ->ordered()
                ->limit(12)
                ->get();

            $realLearners = User::query()->where('role', 'student')->where('is_active', true)->count();
            $learnersMin = (int) config('platform.homepage_stats.learners_min', 5000);

            $homeStats = [
                'learners' => max($learnersMin, $realLearners),
                'learners_real' => $realLearners,
                'learners_show_plus' => (bool) config('platform.homepage_stats.learners_show_plus', true)
                    && max($learnersMin, $realLearners) >= $learnersMin,
                'courses' => AdvancedCourse::query()->where('is_active', true)->count(),
                'certificates' => Certificate::query()
                    ->where(function ($q) {
                        $q->where('status', 'issued')->orWhere('is_verified', true);
                    })
                    ->count(),
                'services' => SiteService::active()->count(),
            ];

            // سليدر خلفيات فقط (هوية ثابتة + زرّان فوقه)
            $heroSlides = [
                SeoAssets::optimizedRemoteImage('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1600&q=72', 1600, 72),
                SeoAssets::optimizedRemoteImage('https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&w=1600&q=72', 1600, 72),
                SeoAssets::optimizedRemoteImage('https://images.unsplash.com/photo-1571260899304-425eee4c7efc?auto=format&fit=crop&w=1600&q=72', 1600, 72),
                SeoAssets::optimizedRemoteImage('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1600&q=72', 1600, 72),
            ];

            return compact(
                'featuredCourses',
                'oneToOneCourses',
                'homeCategories',
                'homeInstructors',
                'homeTestimonials',
                'homeStats',
                'heroSlides'
            );
        };

        // في وضع التطوير: بدون كاش حتى تظهر تحديثات التصميم فوراً
        $payload = config('app.debug')
            ? $buildHomePayload()
            : Cache::remember('landing.home.v10.'.$locale, 180, $buildHomePayload);

        return view('welcome', array_merge($payload, compact('popupAd')));
    }

    /**
     * مدربون معتمدون ونشطون للعرض العام.
     *
     * @return \Illuminate\Support\Collection<int, InstructorProfile>
     */
    private function approvedActiveInstructorProfiles(int $limit = 8): \Illuminate\Support\Collection
    {
        return InstructorProfile::query()
            ->approved()
            ->whereHas('user', function ($q) {
                $q->whereIn('role', ['instructor', 'teacher'])
                    ->where('is_active', true);
            })
            ->with(['user:id,name,role,is_active'])
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * بطاقات تميّز مجالات الخدمة على الصفحة الرئيسية — جميعها تؤدي إلى صفحة الخدمات.
     *
     * @return \Illuminate\Support\Collection<int, array{name: string, description: string, icon: string, url: string}>
     */
    private function buildHomeCategories(): \Illuminate\Support\Collection
    {
        $servicesUrl = route('public.services.index');

        return collect(range(1, 6))->map(function (int $i) use ($servicesUrl) {
            $iconKey = 'public.home_category_fallback_'.$i.'_icon';
            $iconRaw = __($iconKey);
            $icon = is_string($iconRaw) && preg_match('/\bfa-[a-z0-9-]+\b/i', $iconRaw, $m)
                ? strtolower($m[0])
                : 'fa-circle';

            return [
                'name' => __('public.home_category_fallback_'.$i.'_name'),
                'description' => __('public.home_category_fallback_'.$i.'_desc'),
                'icon' => $icon,
                'url' => $servicesUrl,
            ];
        });
    }
}
