<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseCategory;
use App\Services\CourseSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoursesController extends Controller
{
    public function index(Request $request): View
    {
        $delivery = $request->query('delivery');
        if (! in_array($delivery, ['group', 'one_to_one'], true)) {
            $delivery = null;
        }

        $searchQuery = trim((string) $request->query('q', ''));

        $coursesQuery = AdvancedCourse::query()->where('is_active', true);

        if ($delivery === 'one_to_one') {
            $coursesQuery->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE);
        } elseif ($delivery === 'group') {
            $coursesQuery->where(function ($q) {
                $q->whereNull('delivery_type')
                    ->orWhere('delivery_type', CourseSubscriptionService::DELIVERY_GROUP);
            });
        }

        $subjectId = (int) $request->query('subject', 0);
        if ($subjectId > 0) {
            $coursesQuery->where('academic_subject_id', $subjectId);
        }

        $categoryId = (int) $request->query('category', 0);
        if ($categoryId > 0) {
            $coursesQuery->where('course_category_id', $categoryId);
        }

        if ($searchQuery !== '') {
            $coursesQuery->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', '%'.$searchQuery.'%')
                    ->orWhere('description', 'like', '%'.$searchQuery.'%');
            });
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
            ->with(['academicSubject', 'academicYear', 'instructor:id,name', 'courseCategory'])
            ->withCount(['lectures', 'lessons'])
            ->get();

        $courseFilterCategories = CourseCategory::active()->ordered()->get(['id', 'name']);

        $oneToOneCount = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
            ->count();

        return view('courses', compact(
            'courseModels',
            'courseFilterCategories',
            'categoryId',
            'delivery',
            'oneToOneCount',
            'searchQuery',
            'sort'
        ));
    }
}
