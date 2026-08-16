<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CurriculumItem;
use App\Models\CurriculumLibraryCategory;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibrarySection;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * مناهج X للمعلم المعتمد: عرض كامل بدون باقة وبدون رفع.
 */
class CurriculumLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! $user->isAcademyWorkingInstructor()) {
                abort(403, 'مكتبة المناهج متاحة فقط للمعلمين المعتمدين والشغّالين مع الأكاديمية.');
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = CurriculumLibraryItem::query()
            ->active()
            ->with('category')
            ->ordered();

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->category_id);
        }
        if ($request->filled('language') && in_array($request->language, ['ar', 'en', 'fr'], true)) {
            $query->byLanguage($request->language);
        }
        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(12)->withQueryString();
        $categories = CurriculumLibraryCategory::query()->active()->ordered()->get();

        $teachingCourses = collect();
        if ($user && $user->hasTeachingCourses()) {
            $ids = $user->teachingAdvancedCourseIds();
            $teachingCourses = AdvancedCourse::query()
                ->whereIn('id', $ids)
                ->with(['academicSubject:id,name,academic_year_id', 'academicSubject.academicYear:id,name'])
                ->withCount('sections')
                ->orderBy('title')
                ->get();
        }

        return view('instructor.libraries.curriculum.index', compact('items', 'categories', 'teachingCourses'));
    }

    public function show(CurriculumLibraryItem $item): View
    {
        if (! $item->is_active) {
            abort(404);
        }

        $item->load(['category', 'files']);
        $sectionTree = CurriculumLibrarySection::treeForItem($item);

        return view('instructor.libraries.curriculum.show', compact('item', 'sectionTree'));
    }

    public function showCourse(AdvancedCourse $course): View
    {
        $user = request()->user();
        abort_unless(
            $user && $user->teachingAdvancedCourseIds()->contains((int) $course->id),
            403,
            'هذا الكورس غير مسند لك.'
        );

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

        return view('instructor.libraries.curriculum.course', compact('course', 'itemTypeCounts'));
    }
}
