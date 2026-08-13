<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\LectureMaterial;
use App\Models\LibraryFolder;
use App\Services\LectureMaterialStorage;
use App\Support\FamilyLibraryThemes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * ماتريال فولدرات معلم×سنة — رفع للمعلمين المعتمدين فقط.
 */
class MaterialLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! $user->isAcademyWorkingInstructor()) {
                abort(403, 'مكتبة الماتريال متاحة للمعلمين المعتمدين فقط.');
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        $user = request()->user();
        $folders = LibraryFolder::query()
            ->ofKind(LibraryFolder::KIND_MATERIALS)
            ->where(function ($q) use ($user) {
                $q->where('instructor_id', $user->id)->orWhereNull('instructor_id');
            })
            ->with(['academicYear:id,name'])
            ->withCount(['materials' => fn ($q) => $q->where('is_visible_to_student', true)])
            ->ordered()
            ->get();

        $years = Schema::hasTable('academic_years')
            ? AcademicYear::query()->ordered()->get(['id', 'name'])
            : collect();

        return view('instructor.libraries.materials.index', compact('folders', 'years'));
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'description_ar' => ['nullable', 'string', 'max:255'],
            'content_theme' => ['nullable', Rule::in(FamilyLibraryThemes::keys())],
        ]);

        $theme = $data['content_theme'] ?? FamilyLibraryThemes::GENERAL;
        $meta = FamilyLibraryThemes::meta($theme);

        LibraryFolder::create([
            'instructor_id' => $user->id,
            'academic_year_id' => (int) $data['academic_year_id'],
            'kind' => LibraryFolder::KIND_MATERIALS,
            'content_theme' => $theme,
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'icon' => $meta['icon'] ?? 'fas fa-folder-open',
            'color' => $meta['tone'] ?? 'blue',
            'sort_order' => 0,
            'is_active' => true,
            'requires_library_entitlement' => false,
        ]);

        return back()->with('success', 'تم إنشاء مجلد الماتريال — يظهر لطلابك فقط (والإدارة).');
    }

    public function show(LibraryFolder $folder): View
    {
        $this->assertCanViewFolder($folder);

        $folder->load(['academicYear:id,name', 'materials' => fn ($q) => $q->orderBy('sort_order')->latest('id')]);
        $canManage = (int) $folder->instructor_id === (int) request()->user()->id;

        return view('instructor.libraries.materials.show', compact('folder', 'canManage'));
    }

    public function upload(Request $request, LibraryFolder $folder): RedirectResponse
    {
        $this->assertOwnsFolder($folder);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content_theme' => ['nullable', Rule::in(FamilyLibraryThemes::keys())],
            'experience_mode' => ['nullable', Rule::in([FamilyLibraryThemes::MODE_DOWNLOAD, FamilyLibraryThemes::MODE_VIEW, FamilyLibraryThemes::MODE_PLAY])],
            'file' => ['required', 'file', 'max:51200', 'mimes:'.FamilyLibraryThemes::materialMimes()],
            'is_visible_to_student' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('file');
        $path = LectureMaterialStorage::storeForFolder($file, (int) $folder->id);
        $theme = $data['content_theme']
            ?? ($folder->content_theme ?: FamilyLibraryThemes::detectThemeFromFilename($file->getClientOriginalName()));
        $mode = $data['experience_mode'] ?? FamilyLibraryThemes::detectExperienceMode($file->getClientOriginalName(), $theme);

        LectureMaterial::create([
            'lecture_id' => null,
            'library_folder_id' => $folder->id,
            'title' => $data['title'] ?: $file->getClientOriginalName(),
            'description' => $data['description'] ?? null,
            'content_theme' => $theme,
            'experience_mode' => $mode,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'storage_disk' => LectureMaterialStorage::resolvedDisk(),
            'is_visible_to_student' => $request->boolean('is_visible_to_student', true),
            'sort_order' => 0,
        ]);

        return back()->with('success', 'تم رفع الملف إلى المجلد.');
    }

    public function destroyMaterial(LibraryFolder $folder, LectureMaterial $material): RedirectResponse
    {
        $this->assertOwnsFolder($folder);
        abort_unless((int) $material->library_folder_id === (int) $folder->id, 404);

        LectureMaterialStorage::delete($material->file_path, $material->storage_disk);
        $material->delete();

        return back()->with('success', 'تم حذف الملف.');
    }

    private function assertCanViewFolder(LibraryFolder $folder): void
    {
        $user = request()->user();
        abort_unless(
            $folder->kind === LibraryFolder::KIND_MATERIALS || $folder->kind === LibraryFolder::KIND_BOTH,
            404
        );
        abort_unless(
            (int) $folder->instructor_id === (int) $user->id || $folder->instructor_id === null,
            403
        );
    }

    private function assertOwnsFolder(LibraryFolder $folder): void
    {
        $user = request()->user();
        abort_unless(
            $folder->kind === LibraryFolder::KIND_MATERIALS || $folder->kind === LibraryFolder::KIND_BOTH,
            404
        );
        abort_unless((int) $folder->instructor_id === (int) $user->id, 403, 'هذا المجلد إداري — رفع الملفات للمعلم المالك فقط.');
    }
}
