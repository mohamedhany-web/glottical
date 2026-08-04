<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\CourseCategory;
use App\Models\OneToOneWeeklyAvailability;
use App\Services\CourseSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CoursesController extends Controller
{
    public function index(Request $request): View
    {
        $delivery = $request->query('delivery', 'one_to_one');
        if (! in_array($delivery, ['group', 'one_to_one', 'all'], true)) {
            $delivery = 'one_to_one';
        }
        if ($delivery === 'all') {
            $delivery = null;
        }

        $searchQuery = trim((string) $request->query('q', ''));
        $filters = [
            'subject' => trim((string) $request->query('subject', '')),
            'age' => trim((string) $request->query('age', '')),
            'gender' => trim((string) $request->query('gender', '')),
            'language' => trim((string) $request->query('language', '')),
            'specialty' => trim((string) $request->query('specialty', '')),
            'availability' => trim((string) $request->query('availability', '')),
        ];

        $coursesQuery = AdvancedCourse::query()->where('is_active', true);

        if ($delivery === 'one_to_one') {
            $coursesQuery->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE);
        } elseif ($delivery === 'group') {
            $coursesQuery->where(function ($q) {
                $q->whereNull('delivery_type')
                    ->orWhere('delivery_type', CourseSubscriptionService::DELIVERY_GROUP);
            });
        }

        $pathId = (int) $request->query('path', 0);
        if ($pathId > 0) {
            $coursesQuery->where('academic_year_id', $pathId);
        }

        $categoryId = (int) $request->query('category', 0);
        if ($categoryId > 0) {
            $coursesQuery->where('course_category_id', $categoryId);
        }

        if ($searchQuery !== '') {
            $coursesQuery->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', '%'.$searchQuery.'%')
                    ->orWhere('description', 'like', '%'.$searchQuery.'%')
                    ->orWhereHas('instructor', function ($iq) use ($searchQuery) {
                        $iq->where('name', 'like', '%'.$searchQuery.'%');
                    })
                    ->orWhereHas('academicSubject', function ($sq) use ($searchQuery) {
                        $sq->where('name', 'like', '%'.$searchQuery.'%');
                    });
            });
        }

        if ($filters['subject'] !== '') {
            $subjectKey = $filters['subject'];
            $labelEn = (string) (config("private_lessons.subjects.$subjectKey.en") ?? $subjectKey);
            $labelAr = (string) (config("private_lessons.subjects.$subjectKey.ar") ?? $subjectKey);
            $coursesQuery->where(function ($q) use ($subjectKey, $labelEn, $labelAr) {
                $q->where('title', 'like', '%'.$labelEn.'%')
                    ->orWhere('title', 'like', '%'.$labelAr.'%')
                    ->orWhereHas('academicSubject', function ($sq) use ($labelEn, $labelAr) {
                        $sq->where('name', 'like', '%'.$labelEn.'%')
                            ->orWhere('name', 'like', '%'.$labelAr.'%');
                    })
                    ->orWhereHas('instructor', function ($iq) use ($subjectKey) {
                        $iq->whereJsonContains('private_teaching_meta->subjects', $subjectKey);
                    });
            });
        }

        if ($filters['gender'] !== '' && in_array($filters['gender'], ['male', 'female'], true)) {
            $coursesQuery->whereHas('instructor', fn ($q) => $q->where('gender', $filters['gender']));
        }

        foreach (['age' => 'age_groups', 'language' => 'languages', 'specialty' => 'specializations'] as $param => $metaKey) {
            if ($filters[$param] !== '') {
                $value = $filters[$param];
                $coursesQuery->whereHas('instructor', function ($q) use ($metaKey, $value) {
                    $q->whereJsonContains("private_teaching_meta->$metaKey", $value);
                });
            }
        }

        $sort = (string) $request->query('sort', '');
        if ($sort === 'newest') {
            $coursesQuery->orderByDesc('created_at');
        } elseif ($sort === 'featured') {
            $coursesQuery->orderByDesc('is_featured')->orderByDesc('created_at');
        } else {
            $coursesQuery->orderByDesc('is_featured')->orderByDesc('created_at');
        }

        $courseModels = $coursesQuery
            ->with([
                'academicSubject',
                'academicYear',
                'instructor:id,name,gender,profile_image,portfolio_intro_video_url,private_teaching_meta,bio',
                'courseCategory',
            ])
            ->withCount(['lectures', 'lessons'])
            ->get();

        $instructorIds = $courseModels->pluck('instructor_id')->filter()->unique()->values();
        $weeklyByInstructor = OneToOneWeeklyAvailability::query()
            ->whereIn('instructor_id', $instructorIds)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('instructor_id');

        if ($filters['availability'] !== '') {
            $courseModels = $courseModels->filter(function ($course) use ($weeklyByInstructor, $filters) {
                $rules = $weeklyByInstructor[$course->instructor_id] ?? collect();
                if ($rules->isEmpty()) {
                    return true;
                }

                return $this->matchesAvailabilityFilter($rules, $filters['availability']);
            })->values();
        }

        $calendarByInstructor = $instructorIds->mapWithKeys(function ($id) use ($weeklyByInstructor) {
            return [$id => $this->buildWeeklyCalendar($weeklyByInstructor[$id] ?? collect())];
        });

        $courseFilterCategories = CourseCategory::active()->ordered()->get(['id', 'name']);
        $learningPaths = AcademicYear::query()
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn('academic_years', 'is_active'),
                fn ($q) => $q->where('is_active', true)
            )
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn('academic_years', 'order'),
                fn ($q) => $q->orderBy('order'),
                fn ($q) => $q->orderBy('id')
            )
            ->orderBy('name')
            ->get(['id', 'name']);

        $oneToOneCount = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
            ->count();

        $filterCatalog = config('private_lessons');
        $lessonDuration = (int) config('private_lessons.lesson_duration_minutes', 50);
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';

        return view('courses', compact(
            'courseModels',
            'courseFilterCategories',
            'learningPaths',
            'categoryId',
            'pathId',
            'delivery',
            'oneToOneCount',
            'searchQuery',
            'sort',
            'weeklyByInstructor',
            'calendarByInstructor',
            'filters',
            'filterCatalog',
            'lessonDuration',
            'locale'
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
                    // Hourly grid preferred for parent UX (6:00, 7:00…) when duration is 50
                    $cursor += max($slotMins, 60) * 60;
                    if (count($times) >= 4) {
                        break;
                    }
                }
                if (count($times) >= 4) {
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

        return array_slice($days, 0, 4);
    }

    /**
     * @param  Collection<int, OneToOneWeeklyAvailability>  $rules
     */
    private function matchesAvailabilityFilter(Collection $rules, string $availability): bool
    {
        if ($availability === 'weekend') {
            return $rules->contains(fn ($r) => in_array((int) $r->day_of_week, [6, 7], true));
        }

        foreach ($rules as $rule) {
            $hour = (int) substr((string) $rule->start_time, 0, 2);
            if ($availability === 'morning' && $hour < 12) {
                return true;
            }
            if ($availability === 'afternoon' && $hour >= 12 && $hour < 17) {
                return true;
            }
            if ($availability === 'evening' && $hour >= 17) {
                return true;
            }
        }

        return false;
    }
}
