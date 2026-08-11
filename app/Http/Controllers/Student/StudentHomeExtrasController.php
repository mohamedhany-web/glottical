<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\LibraryFolder;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use App\Models\OneToOneSession;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Services\LectureMaterialStorage;
use App\Services\StudentScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentHomeExtrasController extends Controller
{
    public function join(Request $request, string $type, int $id): RedirectResponse
    {
        $url = StudentScheduleService::resolveJoinUrl($request->user(), $type, $id);

        if (! $url) {
            return back()->with('error', 'لا يمكن دخول هذه الحصة الآن. تأكد من الاشتراك أو أن الغرفة جاهزة.');
        }

        return redirect()->away($url);
    }

    public function materials(Request $request): View
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));
        $courseId = (int) $request->query('course', 0);
        $lectureId = (int) $request->query('lecture', 0);
        $type = strtolower(trim((string) $request->query('type', 'all')));
        $sort = (string) $request->query('sort', 'newest');

        $allowedTypes = ['all', 'pdf', 'doc', 'ppt', 'sheet', 'zip', 'image', 'audio', 'video', 'other'];
        if (! in_array($type, $allowedTypes, true)) {
            $type = 'all';
        }
        if (! in_array($sort, ['newest', 'oldest', 'title', 'lecture'], true)) {
            $sort = 'newest';
        }

        $materials = LectureMaterial::query()->whereRaw('1=0')->paginate(24);
        $courses = collect();
        $lectures = collect();
        $typeCounts = [];

        if (Schema::hasTable('lecture_materials') && Schema::hasTable('student_course_enrollments')) {
            $courseIds = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($query) => $query->where('status', 'active'))
                ->pluck('advanced_course_id');

            if ($courseIds->isNotEmpty() && Schema::hasTable('lectures')) {
                $lectureQuery = Lecture::query()
                    ->whereIn('course_id', $courseIds)
                    ->with('course:id,title');

                $allLectureIds = (clone $lectureQuery)->pluck('id');

                $courses = \App\Models\AdvancedCourse::query()
                    ->whereIn('id', $courseIds)
                    ->orderBy('title')
                    ->get(['id', 'title']);

                $lectures = Lecture::query()
                    ->whereIn('course_id', $courseIds)
                    ->when($courseId > 0, fn ($query) => $query->where('course_id', $courseId))
                    ->orderBy('title')
                    ->get(['id', 'title', 'course_id']);

                if ($lectureId > 0 && ! $lectures->contains('id', $lectureId)) {
                    $lectureId = 0;
                }

                $base = LectureMaterial::query()
                    ->whereIn('lecture_id', $allLectureIds)
                    ->where(function ($query) {
                        $query->where('is_visible_to_student', true)
                            ->orWhereNull('is_visible_to_student');
                    });

                // عدّادات الأنواع من مجموعة الطالبة الكاملة (قبل فلاتر البحث/النوع)
                $typeCounts = $this->materialTypeCounts(
                    (clone $base)->get(['file_name', 'file_path'])
                );

                $materialsQuery = LectureMaterial::query()
                    ->with(['lecture:id,title,course_id', 'lecture.course:id,title'])
                    ->whereIn('lecture_id', $allLectureIds)
                    ->where(function ($query) {
                        $query->where('is_visible_to_student', true)
                            ->orWhereNull('is_visible_to_student');
                    })
                    ->when($courseId > 0, function ($query) use ($courseId) {
                        $query->whereHas('lecture', fn ($lq) => $lq->where('course_id', $courseId));
                    })
                    ->when($lectureId > 0, fn ($query) => $query->where('lecture_id', $lectureId))
                    ->when($q !== '', function ($query) use ($q) {
                        $query->where(function ($inner) use ($q) {
                            $inner->where('title', 'like', '%'.$q.'%')
                                ->orWhere('file_name', 'like', '%'.$q.'%')
                                ->orWhereHas('lecture', function ($lq) use ($q) {
                                    $lq->where('title', 'like', '%'.$q.'%')
                                        ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'));
                                });
                        });
                    });

                if ($type !== 'all') {
                    $exts = $this->materialTypeExtensions($type);
                    if ($type === 'other') {
                        $known = ['pdf', 'doc', 'docx', 'docm', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'mp3', 'wav', 'm4a', 'mp4', 'mov', 'webm'];
                        $materialsQuery->where(function ($query) use ($known) {
                            foreach ($known as $ext) {
                                $query->where(function ($inner) use ($ext) {
                                    $inner->where(function ($w) use ($ext) {
                                        $w->whereNull('file_name')->orWhere('file_name', 'not like', '%.'.$ext);
                                    })->where(function ($w) use ($ext) {
                                        $w->whereNull('file_path')->orWhere('file_path', 'not like', '%.'.$ext);
                                    });
                                });
                            }
                        });
                    } elseif ($exts !== []) {
                        $materialsQuery->where(function ($query) use ($exts) {
                            foreach ($exts as $ext) {
                                $query->orWhere('file_name', 'like', '%.'.$ext)
                                    ->orWhere('file_path', 'like', '%.'.$ext);
                            }
                        });
                    }
                }

                match ($sort) {
                    'oldest' => $materialsQuery->orderBy('id'),
                    'title' => $materialsQuery->orderByRaw('COALESCE(NULLIF(title, ""), file_name) asc'),
                    'lecture' => $materialsQuery->orderBy('lecture_id')->orderBy('sort_order'),
                    default => $materialsQuery->orderBy('sort_order')->latest('id'),
                };

                $materials = $materialsQuery->paginate(24)->withQueryString();
            }
        }

        return view('student.library.materials', [
            'materials' => $materials,
            'searchQuery' => $q,
            'courseId' => $courseId,
            'lectureId' => $lectureId,
            'typeFilter' => $type,
            'sort' => $sort,
            'courses' => $courses,
            'lectures' => $lectures,
            'typeCounts' => $typeCounts,
        ]);
    }

    /**
     * @return list<string>
     */
    private function materialTypeExtensions(string $type): array
    {
        return match ($type) {
            'pdf' => ['pdf'],
            'doc' => ['doc', 'docx', 'docm'],
            'ppt' => ['ppt', 'pptx'],
            'sheet' => ['xls', 'xlsx'],
            'zip' => ['zip', 'rar'],
            'image' => ['png', 'jpg', 'jpeg', 'webp', 'gif'],
            'audio' => ['mp3', 'wav', 'm4a'],
            'video' => ['mp4', 'mov', 'webm'],
            default => [],
        };
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $rows
     * @return array<string, int>
     */
    private function materialTypeCounts($rows): array
    {
        $counts = [
            'all' => $rows->count(),
            'pdf' => 0,
            'doc' => 0,
            'ppt' => 0,
            'sheet' => 0,
            'zip' => 0,
            'image' => 0,
            'audio' => 0,
            'video' => 0,
            'other' => 0,
        ];

        foreach ($rows as $row) {
            $name = (string) ($row->file_name ?: $row->file_path ?: '');
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $bucket = match (true) {
                $ext === 'pdf' => 'pdf',
                in_array($ext, ['doc', 'docx', 'docm'], true) => 'doc',
                in_array($ext, ['ppt', 'pptx'], true) => 'ppt',
                in_array($ext, ['xls', 'xlsx'], true) => 'sheet',
                in_array($ext, ['zip', 'rar'], true) => 'zip',
                in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'], true) => 'image',
                in_array($ext, ['mp3', 'wav', 'm4a'], true) => 'audio',
                in_array($ext, ['mp4', 'mov', 'webm'], true) => 'video',
                default => 'other',
            };
            $counts[$bucket]++;
        }

        return $counts;
    }

    public function downloadMaterial(Request $request, LectureMaterial $material): StreamedResponse
    {
        abort_unless((bool) $material->is_visible_to_student, 404);

        $user = $request->user();
        $material->loadMissing('lecture:id,course_id');
        $courseId = $material->lecture?->course_id;
        abort_unless($courseId, 404);

        $enrolled = false;
        if (Schema::hasTable('student_course_enrollments')) {
            $enrolled = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->where('advanced_course_id', $courseId)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($query) => $query->where('status', 'active'))
                ->exists();
        }

        abort_unless($enrolled, 403, 'ليس لديك صلاحية تحميل هذا الملف.');

        return LectureMaterialStorage::download($material);
    }

    public function videos(Request $request): View|RedirectResponse
    {
        $q = trim((string) $request->query('q', ''));
        $folderId = $request->query('folder');
        $activeFolder = null;

        if (! Schema::hasTable('live_recordings') || ! Schema::hasTable('live_sessions')) {
            return view('student.library.videos', [
                'videos' => collect(),
                'folders' => collect(),
                'activeFolder' => null,
                'uncategorizedCount' => 0,
                'searchQuery' => $q,
            ]);
        }

        $user = $request->user();
        $enrolledCourseIds = collect();
        if (Schema::hasTable('student_course_enrollments')) {
            $enrolledCourseIds = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($query) => $query->where('status', 'active'))
                ->pluck('advanced_course_id');
        }

        $sessionIds = LiveSession::query()
            ->where('status', 'ended')
            ->where(function ($query) use ($enrolledCourseIds) {
                $query->whereIn('course_id', $enrolledCourseIds)
                    ->orWhere('require_enrollment', false)
                    ->orWhereNull('course_id');
            })
            ->pluck('id');

        $baseVideoQuery = LiveRecording::query()
            ->whereIn('session_id', $sessionIds)
            ->where('status', 'ready')
            ->where('is_published', true);

        $folders = collect();
        $uncategorizedCount = 0;
        if (Schema::hasTable('library_folders')) {
            $folders = LibraryFolder::query()
                ->active()
                ->ordered()
                ->withCount(['recordings' => function ($query) use ($sessionIds) {
                    $query->whereIn('session_id', $sessionIds)
                        ->where('status', 'ready')
                        ->where('is_published', true);
                }])
                ->get();

            $uncategorizedCount = (clone $baseVideoQuery)->whereNull('library_folder_id')->count();

            if ($folderId === 'none') {
                $activeFolder = (object) [
                    'id' => 'none',
                    'slug' => 'none',
                    'is_uncategorized' => true,
                ];
            } elseif (filled($folderId)) {
                $activeFolder = LibraryFolder::query()
                    ->active()
                    ->where(function ($query) use ($folderId) {
                        if (ctype_digit((string) $folderId)) {
                            $query->where('id', (int) $folderId);
                        } else {
                            $query->where('slug', (string) $folderId);
                        }
                    })
                    ->first();
            }
        }

        $videos = (clone $baseVideoQuery)
            ->with(['session.course', 'session.instructor', 'folder'])
            ->when($activeFolder && ! ($activeFolder->is_uncategorized ?? false), function ($query) use ($activeFolder) {
                $query->where('library_folder_id', $activeFolder->id);
            })
            ->when($activeFolder && ($activeFolder->is_uncategorized ?? false), function ($query) {
                $query->whereNull('library_folder_id');
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhereHas('session', function ($sq) use ($q) {
                            $sq->where('title', 'like', '%'.$q.'%')
                                ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                                ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', '%'.$q.'%'));
                        });
                });
            })
            ->latest('id')
            ->paginate(24)
            ->withQueryString();

        return view('student.library.videos', [
            'videos' => $videos,
            'folders' => $folders,
            'activeFolder' => $activeFolder,
            'uncategorizedCount' => $uncategorizedCount,
            'searchQuery' => $q,
        ]);
    }

    public function lectures(Request $request): View
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));
        $filter = (string) $request->query('filter', 'all');
        if (! in_array($filter, ['all', 'private', 'classes'], true)) {
            $filter = 'all';
        }

        $private = collect();
        $classes = collect();

        if (Schema::hasTable('one_to_one_sessions')) {
            $private = OneToOneSession::query()
                ->with(['course:id,title', 'instructor:id,name,profile_image', 'classroomMeeting'])
                ->where('student_id', $user->id)
                ->whereIn('status', [OneToOneSession::STATUS_SCHEDULED, OneToOneSession::STATUS_COMPLETED])
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->whereHas('course', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                            ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', '%'.$q.'%'));
                    });
                })
                ->orderByDesc('scheduled_at')
                ->limit(40)
                ->get();
        }

        if (Schema::hasTable('tutoring_class_sessions') && Schema::hasTable('tutoring_cohort_enrollments')) {
            $cohortIds = TutoringCohortEnrollment::query()
                ->where('user_id', $user->id)
                ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
                ->pluck('tutoring_group_cohort_id');

            $classes = TutoringClassSession::query()
                ->with(['cohort:id,title', 'tutoringGroup:id,title', 'classroomMeeting'])
                ->whereIn('tutoring_group_cohort_id', $cohortIds)
                ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->where('title', 'like', '%'.$q.'%')
                            ->orWhereHas('cohort', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                            ->orWhereHas('tutoringGroup', fn ($gq) => $gq->where('title', 'like', '%'.$q.'%'));
                    });
                })
                ->orderByDesc('starts_at')
                ->limit(40)
                ->get();
        }

        $nextJoinable = null;
        foreach ($classes as $session) {
            if (method_exists($session, 'isJoinable') && $session->isJoinable()) {
                $nextJoinable = (object) [
                    'kind' => 'class',
                    'title' => $session->displayTitle(),
                    'meta' => $session->cohort?->title,
                    'at' => $session->starts_at,
                    'join_url' => route('student.schedule.join', ['type' => 'class', 'id' => $session->id]),
                ];
                break;
            }
        }
        if (! $nextJoinable) {
            foreach ($private as $session) {
                $canJoin = $session->status === OneToOneSession::STATUS_SCHEDULED
                    && $session->scheduled_at
                    && $session->scheduled_at->lte(now()->addMinutes(30))
                    && $session->scheduled_at->gte(now()->subMinutes(50));
                if ($canJoin) {
                    $nextJoinable = (object) [
                        'kind' => 'private',
                        'title' => $session->course?->title ?: (__('student_timeline.private_lesson')),
                        'meta' => $session->instructor?->name,
                        'at' => $session->scheduled_at,
                        'join_url' => route('student.schedule.join', ['type' => 'private', 'id' => $session->id]),
                    ];
                    break;
                }
            }
        }

        return view('student.library.lectures', [
            'private' => $private,
            'classes' => $classes,
            'searchQuery' => $q,
            'filter' => $filter,
            'nextJoinable' => $nextJoinable,
        ]);
    }
}
