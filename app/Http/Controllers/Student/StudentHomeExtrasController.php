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
use App\Services\LibraryFolderAccessService;
use App\Services\StudentScheduleService;
use App\Helpers\VideoHelper;
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
        $folderParam = $request->query('folder');
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
        $libraryFolders = collect();
        $activeFolder = null;
        $uncategorizedCount = 0;

        $folderIds = collect();
        if (Schema::hasTable('library_folders') && Schema::hasColumn('library_folders', 'kind')) {
            $libraryFolders = LibraryFolderAccessService::foldersVisibleTo($user, LibraryFolder::KIND_MATERIALS)
                ->with(['academicYear:id,name', 'instructor:id,name'])
                ->withCount(['materials' => function ($query) {
                    $query->where(function ($q) {
                        $q->where('is_visible_to_student', true)->orWhereNull('is_visible_to_student');
                    });
                }])
                ->get();
            $folderIds = $libraryFolders->pluck('id');

            if ($folderParam === 'none') {
                $activeFolder = (object) [
                    'id' => 'none',
                    'slug' => 'none',
                    'is_uncategorized' => true,
                ];
            } elseif (filled($folderParam)) {
                $activeFolder = LibraryFolderAccessService::resolveFolderFromParam($folderParam);
                abort_unless($activeFolder && $folderIds->contains((int) $activeFolder->id), 403, 'يلزم اشتراك باقة المكتبات لهذه السنة.');
                abort_unless(LibraryFolderAccessService::canAccessFolder($user, $activeFolder), 403, 'يلزم اشتراك باقة المكتبات لهذه السنة.');
            }
        }

        if (Schema::hasTable('lecture_materials') && Schema::hasTable('student_course_enrollments')) {
            $courseIds = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($query) => $query->where('status', 'active'))
                ->pluck('advanced_course_id');

            $allLectureIds = collect();
            if ($courseIds->isNotEmpty() && Schema::hasTable('lectures')) {
                $allLectureIds = Lecture::query()->whereIn('course_id', $courseIds)->pluck('id');

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
            }

            $uncategorizedCount = $allLectureIds->isNotEmpty()
                ? LectureMaterial::query()
                    ->where(function ($query) {
                        $query->where('is_visible_to_student', true)->orWhereNull('is_visible_to_student');
                    })
                    ->whereIn('lecture_id', $allLectureIds)
                    ->whereNull('library_folder_id')
                    ->count()
                : 0;

            if ($allLectureIds->isNotEmpty() || $folderIds->isNotEmpty()) {
                $scopeMaterials = function ($query) use ($allLectureIds, $folderIds, $activeFolder, $courseId, $lectureId) {
                    $query->where(function ($visible) {
                        $visible->where('is_visible_to_student', true)->orWhereNull('is_visible_to_student');
                    });

                    if ($activeFolder && ($activeFolder->is_uncategorized ?? false)) {
                        $query->whereIn('lecture_id', $allLectureIds)->whereNull('library_folder_id');
                    } elseif ($activeFolder) {
                        $query->where('library_folder_id', $activeFolder->id);
                    } else {
                        $query->where(function ($inner) use ($allLectureIds, $folderIds) {
                            if ($allLectureIds->isNotEmpty()) {
                                $inner->whereIn('lecture_id', $allLectureIds);
                            }
                            if ($folderIds->isNotEmpty()) {
                                $method = $allLectureIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                                $inner->{$method}('library_folder_id', $folderIds);
                            }
                        });
                    }

                    if (! $activeFolder || ($activeFolder->is_uncategorized ?? false)) {
                        if ($courseId > 0) {
                            $query->whereHas('lecture', fn ($lq) => $lq->where('course_id', $courseId));
                        }
                        if ($lectureId > 0) {
                            $query->where('lecture_id', $lectureId);
                        }
                    }
                };

                $base = LectureMaterial::query()->where($scopeMaterials);
                $typeCounts = $this->materialTypeCounts((clone $base)->get(['file_name', 'file_path']));

                $materialsQuery = LectureMaterial::query()
                    ->with([
                        'lecture:id,title,course_id',
                        'lecture.course:id,title',
                        'folder:id,name_ar,name_en,academic_year_id,instructor_id',
                        'folder.academicYear:id,name',
                        'folder.instructor:id,name',
                    ])
                    ->where($scopeMaterials)
                    ->when($q !== '', function ($query) use ($q) {
                        $query->where(function ($inner) use ($q) {
                            $inner->where('title', 'like', '%'.$q.'%')
                                ->orWhere('file_name', 'like', '%'.$q.'%')
                                ->orWhereHas('folder', function ($fq) use ($q) {
                                    $fq->where('name_ar', 'like', '%'.$q.'%')
                                        ->orWhere('name_en', 'like', '%'.$q.'%');
                                })
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
            'libraryFolders' => $libraryFolders,
            'activeFolder' => $activeFolder,
            'uncategorizedCount' => $uncategorizedCount,
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
        abort_unless((bool) $material->is_visible_to_student || $material->is_visible_to_student === null, 404);

        $user = $request->user();
        $material->loadMissing(['lecture:id,course_id', 'folder']);

        if ($material->library_folder_id && $material->folder) {
            abort_unless(
                LibraryFolderAccessService::canAccessFolder($user, $material->folder),
                403,
                'يلزم اشتراك باقة المكتبات لهذه السنة لتحميل الملف.'
            );

            return LectureMaterialStorage::download($material);
        }

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
        $source = (string) $request->query('source', 'all');
        if (! in_array($source, ['all', 'live', 'lectures'], true)) {
            $source = 'all';
        }
        $activeFolder = null;

        $user = $request->user();
        $enrolledCourseIds = collect();
        if (Schema::hasTable('student_course_enrollments')) {
            $enrolledCourseIds = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($query) => $query->where('status', 'active'))
                ->pluck('advanced_course_id');
        }

        $folders = collect();
        $uncategorizedCount = 0;
        $videos = collect();
        $lectureRecordings = collect();

        if (Schema::hasTable('library_folders')) {
            $folders = LibraryFolderAccessService::foldersVisibleTo($user, LibraryFolder::KIND_VIDEOS)
                ->with(['academicYear:id,name', 'instructor:id,name'])
                ->get();
        }

        $allowedFolderIds = $folders->pluck('id');

        if (Schema::hasTable('live_recordings') && Schema::hasTable('live_sessions') && $source !== 'lectures') {
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
                ->where('is_published', true)
                ->where(function ($query) use ($allowedFolderIds) {
                    $query->whereNull('library_folder_id');
                    if ($allowedFolderIds->isNotEmpty()) {
                        $query->orWhereIn('library_folder_id', $allowedFolderIds);
                    }
                });

            if (Schema::hasTable('library_folders')) {
                $folders = $folders->map(function ($folder) use ($sessionIds) {
                    $folder->recordings_count = LiveRecording::query()
                        ->where('library_folder_id', $folder->id)
                        ->whereIn('session_id', $sessionIds)
                        ->where('status', 'ready')
                        ->where('is_published', true)
                        ->count();

                    return $folder;
                });

                $uncategorizedCount = (clone $baseVideoQuery)->whereNull('library_folder_id')->count();

                if ($folderId === 'none') {
                    $activeFolder = (object) [
                        'id' => 'none',
                        'slug' => 'none',
                        'is_uncategorized' => true,
                    ];
                } elseif (filled($folderId)) {
                    $activeFolder = LibraryFolderAccessService::resolveFolderFromParam($folderId);
                    abort_unless(
                        $activeFolder && $allowedFolderIds->contains((int) $activeFolder->id),
                        403,
                        'يلزم اشتراك باقة المكتبات لهذه السنة.'
                    );
                    abort_unless(
                        LibraryFolderAccessService::canAccessFolder($user, $activeFolder),
                        403,
                        'يلزم اشتراك باقة المكتبات لهذه السنة.'
                    );
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
        } elseif (! Schema::hasTable('live_recordings')) {
            $videos = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 24);
        }

        // تسجيلات محاضرات الكورسات المسجّل بها الطالب (رابط أو ملف)
        if ($source !== 'live' && ! $activeFolder && Schema::hasTable('lectures') && $enrolledCourseIds->isNotEmpty()) {
            $lectureRecordings = Lecture::query()
                ->with(['course:id,title', 'instructor:id,name'])
                ->whereIn('course_id', $enrolledCourseIds)
                ->where(function ($query) {
                    $query->where(function ($u) {
                        $u->whereNotNull('recording_url')->where('recording_url', '!=', '');
                    });
                    if (Schema::hasColumn('lectures', 'recording_file_path')) {
                        $query->orWhere(function ($f) {
                            $f->whereNotNull('recording_file_path')->where('recording_file_path', '!=', '');
                        });
                    }
                })
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->where('title', 'like', '%'.$q.'%')
                            ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                            ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', '%'.$q.'%'));
                    });
                })
                ->orderByDesc('scheduled_at')
                ->orderByDesc('id')
                ->limit(40)
                ->get();
        }

        if (! ($videos instanceof \Illuminate\Contracts\Pagination\Paginator)) {
            $videos = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 24);
        }

        return view('student.library.videos', [
            'videos' => $videos,
            'lectureRecordings' => $lectureRecordings,
            'folders' => $folders,
            'activeFolder' => $activeFolder,
            'uncategorizedCount' => $uncategorizedCount,
            'searchQuery' => $q,
            'sourceFilter' => $source,
        ]);
    }

    /**
     * مشاهدة تسجيل محاضرة داخل مكتبة الفيديو (للطالب المسجّل في الكورس).
     */
    public function watchLectureRecording(Request $request, Lecture $lecture): View
    {
        $user = $request->user();
        $courseId = (int) $lecture->course_id;
        abort_unless($courseId > 0, 404);

        $enrolled = false;
        if (Schema::hasTable('student_course_enrollments')) {
            $enrolled = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->where('advanced_course_id', $courseId)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($query) => $query->where('status', 'active'))
                ->exists();
        }
        abort_unless($enrolled, 403, 'يلزم التسجيل في الكورس لمشاهدة هذا التسجيل.');

        $lecture->load(['course:id,title', 'instructor:id,name']);

        $url = $lecture->recording_url ? trim((string) $lecture->recording_url) : null;
        $fileUrl = null;
        if (! $url && Schema::hasColumn('lectures', 'recording_file_path') && $lecture->recording_file_path) {
            $fileUrl = route('my-courses.lectures.recording-stream', [$courseId, $lecture->id]);
            $url = $fileUrl;
        }

        abort_unless($url, 404, 'لا يوجد تسجيل لهذه المحاضرة.');

        $embedUrl = $fileUrl ? null : VideoHelper::getEmbedUrl($url);
        $directUrl = $fileUrl ?: ($embedUrl ? null : (VideoHelper::getDirectVideoUrl($url) ?: $url));
        $source = $fileUrl ? 'direct' : VideoHelper::getVideoSource($url);
        $thumbnail = $fileUrl ? null : VideoHelper::getThumbnail($url);

        return view('student.library.lecture-recording-show', [
            'lecture' => $lecture,
            'url' => $url,
            'embedUrl' => $embedUrl,
            'directUrl' => $directUrl,
            'source' => $source,
            'thumbnail' => $thumbnail,
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
