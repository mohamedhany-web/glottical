<?php

namespace App\Http\Controllers\Public;

use App\Helpers\VideoHelper;
use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\ConsultationSetting;
use App\Models\InstructorProfile;
use App\Models\OneToOneWeeklyAvailability;
use App\Models\ServicePackage;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
use App\Services\StudentEntitlementService;
use Illuminate\Support\Collection;

class InstructorController extends Controller
{
    public function index()
    {
        $profiles = InstructorProfile::query()
            ->approved()
            ->whereHas('user', function ($q) {
                $q->whereIn('role', ['instructor', 'teacher'])
                    ->where('is_active', true);
            })
            ->with(['user:id,name,role,is_active'])
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();

        $courseCounts = AdvancedCourse::query()
            ->where('is_active', true)
            ->whereIn('instructor_id', $profiles->pluck('user_id')->filter()->unique()->values())
            ->selectRaw('instructor_id, COUNT(*) as aggregate')
            ->groupBy('instructor_id')
            ->pluck('aggregate', 'instructor_id');

        $profiles->each(function (InstructorProfile $profile) use ($courseCounts) {
            $profile->setAttribute('courses_count', (int) ($courseCounts[$profile->user_id] ?? 0));
        });

        $consultationSetting = ConsultationSetting::current();

        $instructorIds = $profiles->pluck('user_id')->filter()->unique()->values();
        $featuredCourses = $instructorIds->isEmpty()
            ? collect()
            : AdvancedCourse::query()
                ->where('is_active', true)
                ->whereIn('instructor_id', $instructorIds)
                ->with(['instructor:id,name', 'courseCategory:id,name'])
                ->withCount('lessons')
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

        return view('instructors.index', compact('profiles', 'consultationSetting', 'featuredCourses'));
    }

    public function show(User $instructor)
    {
        if (! $instructor->isInstructor()) {
            abort(404);
        }
        $profile = InstructorProfile::where('user_id', $instructor->id)->approved()->with('user')->firstOrFail();
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->withCount('lessons')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        $groupCourses = $courses->filter(fn ($c) => ! $c->isOneToOne())->values();
        $oneToOneCourses = $courses->filter(fn ($c) => $c->isOneToOne())->values();

        $consultationSetting = ConsultationSetting::current();

        $weeklyRules = OneToOneWeeklyAvailability::query()
            ->where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $weeklyCalendar = $this->buildWeeklyCalendar($weeklyRules);

        $introVideoUrl = trim((string) ($instructor->portfolio_intro_video_url ?? ''));
        $introEmbedUrl = VideoHelper::getEmbedUrl($introVideoUrl);
        $introDirectVideo = VideoHelper::getDirectVideoUrl($introVideoUrl);

        $canBook = false;
        $unitsLeft = 0;
        if (auth()->check() && auth()->user()->isStudent()) {
            $entitlement = StudentEntitlementService::availableFor(
                (int) auth()->id(),
                ServicePackage::SCOPE_PRIVATE_LESSONS
            );
            if ($entitlement) {
                $unitsLeft = StudentEntitlementService::bookableUnitsLeft($entitlement);
                $canBook = $unitsLeft > 0;
            }
        }

        $bookableSlots = collect();
        if ($canBook) {
            $bookableSlots = OneToOneAvailabilityService::availableSlots(
                (int) $instructor->id,
                now()->addHour(),
                now()->addWeeks(3),
                \App\Models\OneToOneSession::defaultDurationMinutes()
            )->take(24);
        }

        $packagesUrl = route('public.service-packages.index');

        return view('instructors.show', compact(
            'profile',
            'courses',
            'groupCourses',
            'oneToOneCourses',
            'consultationSetting',
            'weeklyCalendar',
            'introVideoUrl',
            'introEmbedUrl',
            'introDirectVideo',
            'canBook',
            'unitsLeft',
            'bookableSlots',
            'packagesUrl'
        ));
    }

    /**
     * @param  Collection<int, OneToOneWeeklyAvailability>  $rules
     * @return array<int, array{day:int,label:string,times:list<string>}>
     */
    private function buildWeeklyCalendar(Collection $rules): array
    {
        $duration = (int) config('private_lessons.lesson_duration_minutes', 50);
        $isRtl = app()->getLocale() === 'ar';
        $labels = $isRtl
            ? [1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت', 7 => 'الأحد']
            : [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];

        $days = [];
        foreach ([1, 2, 3, 4, 5, 6, 7] as $day) {
            $dayRules = $rules->where('day_of_week', $day);
            $times = [];
            foreach ($dayRules as $rule) {
                $start = substr((string) $rule->start_time, 0, 5);
                $end = substr((string) $rule->end_time, 0, 5);
                $slotMins = (int) ($rule->slot_duration_minutes ?: $duration);
                if ($slotMins < 30) {
                    $slotMins = $duration;
                }
                $cursor = strtotime('1970-01-01 '.$start.':00');
                $limit = strtotime('1970-01-01 '.$end.':00');
                while ($cursor !== false && $limit !== false && ($cursor + ($slotMins * 60)) <= $limit) {
                    $times[] = date('g:i A', $cursor);
                    $cursor += max($slotMins, 60) * 60;
                    if (count($times) >= 6) {
                        break;
                    }
                }
                if (count($times) >= 6) {
                    break;
                }
            }
            if ($times !== []) {
                $days[] = [
                    'day' => $day,
                    'label' => $labels[$day] ?? (string) $day,
                    'times' => array_values(array_unique($times)),
                ];
            }
        }

        return $days;
    }
}
