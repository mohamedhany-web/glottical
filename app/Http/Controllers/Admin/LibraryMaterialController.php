<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\LibraryFolder;
use App\Services\LectureMaterialStorage;
use App\Support\FamilyLibraryThemes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LibraryMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.lectures') && ! $user->hasPermission('manage.courses'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = LectureMaterial::query()
            ->with([
                'lecture:id,title,course_id,instructor_id',
                'lecture.course:id,title',
                'lecture.instructor:id,name',
                'folder:id,name_ar,name_en,instructor_id,academic_year_id',
                'folder.instructor:id,name',
                'folder.academicYear:id,name',
            ])
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('file_name', 'like', "%{$s}%")
                    ->orWhereHas('lecture', fn ($lq) => $lq->where('title', 'like', "%{$s}%"))
                    ->orWhereHas('folder', fn ($fq) => $fq->where('name_ar', 'like', "%{$s}%")->orWhere('name_en', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('course_id')) {
            $courseId = (int) $request->course_id;
            $query->whereHas('lecture', fn ($q) => $q->where('course_id', $courseId));
        }
        if ($request->filled('lecture_id')) {
            $query->where('lecture_id', (int) $request->lecture_id);
        }
        if ($request->filled('folder_id')) {
            $query->where('library_folder_id', (int) $request->folder_id);
        }
        if ($request->filled('visibility')) {
            $query->where('is_visible_to_student', $request->visibility === 'visible');
        }
        if ($request->filled('content_theme')) {
            $query->where('content_theme', $request->content_theme);
        }

        $materials = $query->paginate(25)->withQueryString();

        $stats = [
            'total' => LectureMaterial::query()->count(),
            'visible' => LectureMaterial::query()->where('is_visible_to_student', true)->count(),
            'hidden' => LectureMaterial::query()->where('is_visible_to_student', false)->count(),
            'courses' => LectureMaterial::query()
                ->whereNotNull('lecture_id')
                ->join('lectures', 'lectures.id', '=', 'lecture_materials.lecture_id')
                ->distinct('lectures.course_id')
                ->count('lectures.course_id'),
            'folders' => Schema::hasColumn('lecture_materials', 'library_folder_id')
                ? LectureMaterial::query()->whereNotNull('library_folder_id')->distinct('library_folder_id')->count('library_folder_id')
                : 0,
            'storage_disk' => LectureMaterialStorage::resolvedDisk(),
        ];

        $folders = Schema::hasTable('library_folders')
            ? LibraryFolder::query()->ofKind(LibraryFolder::KIND_MATERIALS)->ordered()->get(['id', 'name_ar', 'name_en'])
            : collect();

        return view('admin.libraries.materials.index', [
            'materials' => $materials,
            'stats' => $stats,
            'courses' => AdvancedCourse::query()->orderBy('title')->get(['id', 'title']),
            'lectures' => Lecture::query()->orderByDesc('id')->limit(400)->get(['id', 'title', 'course_id']),
            'folders' => $folders,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.libraries.materials.form', [
            'material' => new LectureMaterial([
                'is_visible_to_student' => true,
                'sort_order' => 0,
                'lecture_id' => (int) $request->integer('lecture_id') ?: null,
                'library_folder_id' => (int) $request->integer('folder_id') ?: null,
                'content_theme' => FamilyLibraryThemes::GENERAL,
                'experience_mode' => FamilyLibraryThemes::MODE_DOWNLOAD,
            ]),
            'mode' => 'create',
            'storageDisk' => LectureMaterialStorage::resolvedDisk(),
            'themes' => FamilyLibraryThemes::labels('ar'),
            'lectures' => Lecture::query()
                ->with('course:id,title')
                ->orderByDesc('id')
                ->limit(500)
                ->get(['id', 'title', 'course_id']),
            'folders' => Schema::hasTable('library_folders')
                ? LibraryFolder::query()->ofKind(LibraryFolder::KIND_MATERIALS)->ordered()->get(['id', 'name_ar', 'name_en'])
                : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lecture_id' => ['nullable', 'exists:lectures,id'],
            'library_folder_id' => ['nullable', 'exists:library_folders,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content_theme' => ['nullable', Rule::in(FamilyLibraryThemes::keys())],
            'experience_mode' => ['nullable', Rule::in([FamilyLibraryThemes::MODE_DOWNLOAD, FamilyLibraryThemes::MODE_VIEW, FamilyLibraryThemes::MODE_PLAY])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_visible_to_student' => ['nullable', 'boolean'],
            'file' => ['required', 'file', 'max:51200', 'mimes:'.FamilyLibraryThemes::materialMimes()],
        ]);

        $lectureId = isset($data['lecture_id']) && $data['lecture_id'] !== '' ? (int) $data['lecture_id'] : null;
        $folderId = isset($data['library_folder_id']) && $data['library_folder_id'] !== '' ? (int) $data['library_folder_id'] : null;
        if (! $lectureId && ! $folderId) {
            return back()->withErrors(['lecture_id' => 'اختر محاضرة أو مجلد ماتريال.'])->withInput();
        }

        $file = $request->file('file');
        $disk = LectureMaterialStorage::resolvedDisk();
        $path = $folderId
            ? LectureMaterialStorage::storeForFolder($file, $folderId)
            : LectureMaterialStorage::store($file, (int) $lectureId);

        $theme = $data['content_theme'] ?? FamilyLibraryThemes::detectThemeFromFilename($file->getClientOriginalName());
        $mode = $data['experience_mode'] ?? FamilyLibraryThemes::detectExperienceMode($file->getClientOriginalName(), $theme);

        $material = LectureMaterial::create([
            'lecture_id' => $lectureId,
            'library_folder_id' => $folderId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'storage_disk' => $disk,
            'title' => $data['title'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'description' => $data['description'] ?? null,
            'content_theme' => $theme,
            'experience_mode' => $mode,
            'is_visible_to_student' => $request->boolean('is_visible_to_student', true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('admin.libraries.materials.index')
            ->with('success', 'تم رفع الماتريال #'.$material->id.' على '.($disk === 'r2' ? 'Cloudflare R2' : $disk).'.');
    }

    public function edit(LectureMaterial $material): View
    {
        $material->load(['lecture.course', 'folder']);

        return view('admin.libraries.materials.form', [
            'material' => $material,
            'mode' => 'edit',
            'storageDisk' => LectureMaterialStorage::resolvedDisk(),
            'themes' => FamilyLibraryThemes::labels('ar'),
            'lectures' => Lecture::query()
                ->with('course:id,title')
                ->orderByDesc('id')
                ->limit(500)
                ->get(['id', 'title', 'course_id']),
            'folders' => Schema::hasTable('library_folders')
                ? LibraryFolder::query()->ofKind(LibraryFolder::KIND_MATERIALS)->ordered()->get(['id', 'name_ar', 'name_en'])
                : collect(),
        ]);
    }

    public function update(Request $request, LectureMaterial $material): RedirectResponse
    {
        $data = $request->validate([
            'lecture_id' => ['nullable', 'exists:lectures,id'],
            'library_folder_id' => ['nullable', 'exists:library_folders,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content_theme' => ['nullable', Rule::in(FamilyLibraryThemes::keys())],
            'experience_mode' => ['nullable', Rule::in([FamilyLibraryThemes::MODE_DOWNLOAD, FamilyLibraryThemes::MODE_VIEW, FamilyLibraryThemes::MODE_PLAY])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_visible_to_student' => ['nullable', 'boolean'],
            'file' => ['nullable', 'file', 'max:51200', 'mimes:'.FamilyLibraryThemes::materialMimes()],
        ]);

        $lectureId = isset($data['lecture_id']) && $data['lecture_id'] !== '' ? (int) $data['lecture_id'] : null;
        $folderId = isset($data['library_folder_id']) && $data['library_folder_id'] !== '' ? (int) $data['library_folder_id'] : null;
        if (! $lectureId && ! $folderId) {
            return back()->withErrors(['lecture_id' => 'اختر محاضرة أو مجلد ماتريال.'])->withInput();
        }

        $fileName = $material->file_name;
        $payload = [
            'lecture_id' => $lectureId,
            'library_folder_id' => $folderId,
            'title' => $data['title'] ?: $material->title,
            'description' => $data['description'] ?? $material->description,
            'is_visible_to_student' => $request->boolean('is_visible_to_student', true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            LectureMaterialStorage::delete($material->file_path, $material->storage_disk);
            $disk = LectureMaterialStorage::resolvedDisk();
            $payload['file_path'] = $folderId
                ? LectureMaterialStorage::storeForFolder($file, $folderId)
                : LectureMaterialStorage::store($file, (int) $lectureId);
            $payload['storage_disk'] = $disk;
            $payload['file_name'] = $file->getClientOriginalName();
            $fileName = $file->getClientOriginalName();
            if (empty($data['title'])) {
                $payload['title'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            }
        }

        $theme = $data['content_theme'] ?? FamilyLibraryThemes::detectThemeFromFilename($fileName, $material->content_theme);
        $payload['content_theme'] = $theme;
        $payload['experience_mode'] = $data['experience_mode']
            ?? FamilyLibraryThemes::detectExperienceMode($fileName, $theme);

        $material->update($payload);

        return redirect()
            ->route('admin.libraries.materials.index')
            ->with('success', 'تم تحديث الماتريال.');
    }

    public function destroy(LectureMaterial $material): RedirectResponse
    {
        LectureMaterialStorage::delete($material->file_path, $material->storage_disk);
        $material->delete();

        return back()->with('success', 'تم حذف الماتريال.');
    }

    public function toggleVisibility(LectureMaterial $material): RedirectResponse
    {
        $material->update(['is_visible_to_student' => ! $material->is_visible_to_student]);

        return back()->with('success', $material->is_visible_to_student ? 'الماتريال ظاهر للطلاب.' : 'الماتريال مخفي عن الطلاب.');
    }

    public function download(LectureMaterial $material): StreamedResponse
    {
        return LectureMaterialStorage::download($material);
    }

    public function bulkVisibility(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:lecture_materials,id'],
            'visible' => ['required', 'boolean'],
        ]);

        LectureMaterial::query()
            ->whereIn('id', $data['ids'])
            ->update(['is_visible_to_student' => (bool) $data['visible']]);

        return back()->with('success', 'تم تحديث ظهور '.count($data['ids']).' ملف.');
    }
}
