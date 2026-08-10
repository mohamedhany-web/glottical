<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CurriculumHubController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.academic-years') && ! $user->hasPermission('manage.academic-subjects') && ! $user->hasPermission('manage.courses'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $years = AcademicYear::query()
            ->with([
                'subjects' => fn ($q) => $q->ordered()->with([
                    'courses' => fn ($cq) => $cq->orderBy('title')->select('id', 'title', 'academic_subject_id'),
                ]),
            ])
            ->withCount('subjects')
            ->ordered()
            ->get();

        $subjectsQuery = AcademicSubject::query()
            ->with(['academicYear:id,name'])
            ->withCount('courses')
            ->ordered();

        if ($request->filled('year_id')) {
            $subjectsQuery->where('academic_year_id', (int) $request->year_id);
        }
        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $subjectsQuery->where('name', 'like', "%{$s}%");
        }

        $subjects = $subjectsQuery->paginate(30, ['*'], 'subjects_page')->withQueryString();

        $coursesQuery = AdvancedCourse::query()
            ->with(['academicSubject:id,name,academic_year_id', 'academicSubject.academicYear:id,name'])
            ->withCount(['sections', 'lectures'])
            ->orderByDesc('id');

        if ($request->filled('year_id') && Schema::hasColumn('advanced_courses', 'academic_subject_id')) {
            $yearId = (int) $request->year_id;
            $coursesQuery->whereHas('academicSubject', fn ($q) => $q->where('academic_year_id', $yearId));
        }
        if ($request->filled('subject_id')) {
            $coursesQuery->where('academic_subject_id', (int) $request->subject_id);
        }
        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $coursesQuery->where('title', 'like', "%{$s}%");
        }

        $courses = $coursesQuery->paginate(20, ['*'], 'courses_page')->withQueryString();

        $stats = [
            'years' => AcademicYear::query()->count(),
            'subjects' => AcademicSubject::query()->count(),
            'courses' => AdvancedCourse::query()->count(),
            'sections' => CourseSection::query()->count(),
            'items' => CurriculumItem::query()->count(),
            'active_years' => AcademicYear::query()->where('is_active', true)->count(),
        ];

        return view('admin.libraries.curriculum.index', compact('years', 'subjects', 'courses', 'stats'));
    }

    public function showCourse(AdvancedCourse $course): View
    {
        $course->load([
            'academicSubject:id,name,academic_year_id',
            'academicSubject.academicYear:id,name',
            'sections' => fn ($q) => $q->orderBy('order')->with([
                'items' => fn ($iq) => $iq->orderBy('order')->with('item'),
            ]),
            'lectures' => fn ($q) => $q->orderBy('scheduled_at')->limit(50),
        ]);

        $itemTypeCounts = CurriculumItem::query()
            ->whereHas('section', fn ($q) => $q->where('advanced_course_id', $course->id))
            ->selectRaw('item_type, count(*) as c')
            ->groupBy('item_type')
            ->pluck('c', 'item_type');

        return view('admin.libraries.curriculum.course', compact('course', 'itemTypeCounts'));
    }
}
