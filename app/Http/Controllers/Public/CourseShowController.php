<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use Illuminate\View\View;

class CourseShowController extends Controller
{
    public function show(int $id): View
    {
        $course = AdvancedCourse::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->with(['academicSubject', 'academicYear', 'instructor', 'courseCategory'])
            ->withCount('lessons')
            ->firstOrFail();

        $isEnrolled = auth()->check() && auth()->user()->isEnrolledIn($course->id);

        $relatedCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('id', '!=', $course->id)
            ->where(function ($query) use ($course) {
                if ($course->course_category_id) {
                    $query->where('course_category_id', $course->course_category_id);
                }
                $query->orWhere('academic_subject_id', $course->academic_subject_id)
                    ->orWhere('is_featured', true);
            })
            ->with(['academicSubject'])
            ->withCount('lessons')
            ->limit(3)
            ->get();

        return view('course-show', compact('course', 'relatedCourses', 'isEnrolled'));
    }
}
