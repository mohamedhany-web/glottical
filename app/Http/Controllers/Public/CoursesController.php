<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseCategory;
use App\Models\Package;
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

        $coursesCollection = $coursesQuery
            ->with(['academicSubject', 'academicYear', 'instructor:id,name', 'courseCategory'])
            ->withCount('lectures')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        $courseFilterCategories = CourseCategory::active()->ordered()->get(['id', 'name']);

        $courses = $coursesCollection
            ->map(fn (AdvancedCourse $course) => $course->toPublicCatalogArray())
            ->values()
            ->toArray();

        $packages = Package::active()
            ->with(['courses' => fn ($q) => $q->where('is_active', true)])
            ->withCount('courses')
            ->orderByDesc('is_featured')
            ->orderByDesc('is_popular')
            ->orderBy('order')
            ->get();

        $oneToOneCount = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
            ->count();

        return view('courses', compact(
            'courses',
            'packages',
            'courseFilterCategories',
            'categoryId',
            'delivery',
            'oneToOneCount'
        ));
    }
}
