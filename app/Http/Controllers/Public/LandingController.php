<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\Certificate;
use App\Models\PopupAd;
use App\Models\SiteTestimonial;
use App\Models\SiteService;
use App\Models\User;
use App\Services\CourseSubscriptionService;
use App\Services\HomepageSliderResolver;
use App\Services\InstructorMarketingRankingService;
use Illuminate\Support\Str;
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

        // نفس مسارات صفحة المسارات التعليمية بكل بياناتها (سعر المسار المستقل، عدد الكورسات، الصورة، إلخ)
        $landingPaths = $this->getPublicLearningPaths(12);

        // باقات المعلمين من إعدادات مزايا اشتراك المعلمين (نفس بيانات /admin/teacher-features وصفحة الأسعار)
        $featuresController = new \App\Http\Controllers\Admin\TeacherFeaturesController();
        $teacherPlans = $featuresController->getSettings();

        $featuredCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->with(['instructor:id,name', 'courseCategory:id,name'])
            ->withCount('lessons')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit(24)
            ->get();

        $oneToOneCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
            ->with(['instructor:id,name', 'courseCategory:id,name'])
            ->withCount('lessons')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        $homeCategories = $this->buildHomeCategories();

        $homeInstructors = InstructorMarketingRankingService::rankApprovedProfiles();

        $homeTestimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->limit(24)
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

        $heroSpotlight = app(HomepageSliderResolver::class)->resolve(
            collect($featuredCourses),
            collect($landingPaths)
        );

        return view('welcome', compact(
            'popupAd',
            'landingPaths',
            'teacherPlans',
            'featuredCourses',
            'oneToOneCourses',
            'homeCategories',
            'homeInstructors',
            'homeTestimonials',
            'homeStats',
            'heroSpotlight'
        ));
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

    /**
     * جلب المسارات التعليمية بنفس منطق صفحة المسارات (للاستخدام في الصفحة الرئيسية أو أي عرض عام).
     * @param int|null $limit عدد المسارات (null = بدون حد)
     */
    public static function getPublicLearningPaths(?int $limit = null): \Illuminate\Support\Collection
    {
        $query = AcademicYear::where('is_active', true)
            ->with(['linkedCourses' => function ($q) {
                $q->where('is_active', true);
            }, 'academicSubjects' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['linkedCourses', 'academicSubjects'])
            ->orderBy('order');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $academicYears = $query->get();

        return $academicYears->map(function ($year) {
            $linkedCourses = $year->linkedCourses ?? collect();
            $subjectCourses = collect();
            if ($year->academicSubjects && $year->academicSubjects->isNotEmpty()) {
                $subjectIds = $year->academicSubjects->pluck('id')->toArray();
                if (!empty($subjectIds)) {
                    $subjectCourses = AdvancedCourse::where('is_active', true)
                        ->whereIn('academic_subject_id', $subjectIds)
                        ->get();
                }
            }
            $courses = $linkedCourses->merge($subjectCourses)->unique('id');
            $slug = Str::slug($year->name);
            $thumb = $year->thumbnail ? str_replace('\\', '/', $year->thumbnail) : null;
            $imageUrl = $thumb ? storage_public_url($thumb) : null;

            return (object) [
                'id' => $year->id,
                'name' => $year->name,
                'description' => $year->description,
                'slug' => $slug,
                'url' => $slug !== '' ? route('public.learning-path.show', $slug) : route('public.learning-paths.index'),
                'price' => (float) ($year->price ?? 0),
                'courses_count' => $courses->count(),
                'thumbnail' => $year->thumbnail,
                'image_url' => $imageUrl,
                'icon' => $year->icon,
                'color' => $year->color,
                'code' => $year->code,
            ];
        });
    }
}
