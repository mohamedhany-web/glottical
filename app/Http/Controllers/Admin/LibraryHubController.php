<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\LiveRecording;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryHubController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.lectures') && ! $user->hasPermission('manage.courses') && ! $user->hasPermission('manage.live-sessions') && ! $user->hasPermission('manage.academic-years'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        $materialsTotal = LectureMaterial::query()->count();
        $materialsVisible = LectureMaterial::query()->where('is_visible_to_student', true)->count();
        $videosReady = LiveRecording::query()->where('status', 'ready')->count();
        $videosPublished = LiveRecording::query()->where('is_published', true)->count();
        $lectureVideos = Lecture::query()
            ->where(function ($q) {
                $q->whereNotNull('recording_url')->where('recording_url', '!=', '')
                    ->orWhereNotNull('recording_file_path')->where('recording_file_path', '!=', '');
            })
            ->count();
        $years = AcademicYear::query()->count();
        $subjects = AcademicSubject::query()->count();
        $courses = AdvancedCourse::query()->count();
        $sections = CourseSection::query()->count();
        $curriculumItems = CurriculumItem::query()->count();

        $recentMaterials = LectureMaterial::query()
            ->with(['lecture:id,title,course_id', 'lecture.course:id,title'])
            ->latest('id')
            ->take(6)
            ->get();

        $recentVideos = LiveRecording::query()
            ->with(['session:id,title'])
            ->latest('id')
            ->take(6)
            ->get();

        $stats = [
            'materials_total' => $materialsTotal,
            'materials_visible' => $materialsVisible,
            'videos_ready' => $videosReady,
            'videos_published' => $videosPublished,
            'lecture_videos' => $lectureVideos,
            'years' => $years,
            'subjects' => $subjects,
            'courses' => $courses,
            'sections' => $sections,
            'curriculum_items' => $curriculumItems,
        ];

        return view('admin.libraries.index', compact('stats', 'recentMaterials', 'recentVideos'));
    }
}
