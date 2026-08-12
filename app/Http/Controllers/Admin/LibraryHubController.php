<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\LectureMaterial;
use App\Models\LibraryFolder;
use App\Models\LibraryVideo;
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
        $videosTotal = LibraryVideo::query()->count();
        $videosPublished = LibraryVideo::query()->where('is_published', true)->count();
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

        $recentVideos = LibraryVideo::query()
            ->with(['folder:id,name_ar,name_en'])
            ->latest('id')
            ->take(6)
            ->get();

        $stats = [
            'materials_total' => $materialsTotal,
            'materials_visible' => $materialsVisible,
            'videos_ready' => $videosTotal,
            'videos_published' => $videosPublished,
            'lecture_videos' => 0,
            'video_folders' => LibraryFolder::query()->ofKind(LibraryFolder::KIND_VIDEOS)->count(),
            'years' => $years,
            'subjects' => $subjects,
            'courses' => $courses,
            'sections' => $sections,
            'curriculum_items' => $curriculumItems,
        ];

        return view('admin.libraries.index', compact('stats', 'recentMaterials', 'recentVideos'));
    }
}
