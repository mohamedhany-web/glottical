<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Services\LectureMaterialStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->with(['lecture:id,title,course_id,instructor_id', 'lecture.course:id,title', 'lecture.instructor:id,name'])
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('file_name', 'like', "%{$s}%")
                    ->orWhereHas('lecture', fn ($lq) => $lq->where('title', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('course_id')) {
            $courseId = (int) $request->course_id;
            $query->whereHas('lecture', fn ($q) => $q->where('course_id', $courseId));
        }
        if ($request->filled('lecture_id')) {
            $query->where('lecture_id', (int) $request->lecture_id);
        }
        if ($request->filled('visibility')) {
            $query->where('is_visible_to_student', $request->visibility === 'visible');
        }

        $materials = $query->paginate(25)->withQueryString();

        $stats = [
            'total' => LectureMaterial::query()->count(),
            'visible' => LectureMaterial::query()->where('is_visible_to_student', true)->count(),
            'hidden' => LectureMaterial::query()->where('is_visible_to_student', false)->count(),
            'courses' => LectureMaterial::query()
                ->join('lectures', 'lectures.id', '=', 'lecture_materials.lecture_id')
                ->distinct('lectures.course_id')
                ->count('lectures.course_id'),
            'storage_disk' => LectureMaterialStorage::resolvedDisk(),
        ];

        return view('admin.libraries.materials.index', [
            'materials' => $materials,
            'stats' => $stats,
            'courses' => AdvancedCourse::query()->orderBy('title')->get(['id', 'title']),
            'lectures' => Lecture::query()->orderByDesc('id')->limit(400)->get(['id', 'title', 'course_id']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.libraries.materials.form', [
            'material' => new LectureMaterial([
                'is_visible_to_student' => true,
                'sort_order' => 0,
                'lecture_id' => (int) $request->integer('lecture_id') ?: null,
            ]),
            'mode' => 'create',
            'storageDisk' => LectureMaterialStorage::resolvedDisk(),
            'lectures' => Lecture::query()
                ->with('course:id,title')
                ->orderByDesc('id')
                ->limit(500)
                ->get(['id', 'title', 'course_id']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lecture_id' => ['required', 'exists:lectures,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_visible_to_student' => ['nullable', 'boolean'],
            'file' => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docm,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg,webp,mp3,mp4'],
        ]);

        $lectureId = (int) $data['lecture_id'];
        $file = $request->file('file');
        $disk = LectureMaterialStorage::resolvedDisk();
        $path = LectureMaterialStorage::store($file, $lectureId);

        $material = LectureMaterial::create([
            'lecture_id' => $lectureId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'storage_disk' => $disk,
            'title' => $data['title'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'is_visible_to_student' => $request->boolean('is_visible_to_student', true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('admin.libraries.materials.index')
            ->with('success', 'تم رفع الماتريال #'.$material->id.' على '.($disk === 'r2' ? 'Cloudflare R2' : $disk).'.');
    }

    public function edit(LectureMaterial $material): View
    {
        $material->load(['lecture.course']);

        return view('admin.libraries.materials.form', [
            'material' => $material,
            'mode' => 'edit',
            'storageDisk' => LectureMaterialStorage::resolvedDisk(),
            'lectures' => Lecture::query()
                ->with('course:id,title')
                ->orderByDesc('id')
                ->limit(500)
                ->get(['id', 'title', 'course_id']),
        ]);
    }

    public function update(Request $request, LectureMaterial $material): RedirectResponse
    {
        $data = $request->validate([
            'lecture_id' => ['required', 'exists:lectures,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_visible_to_student' => ['nullable', 'boolean'],
            'file' => ['nullable', 'file', 'max:51200', 'mimes:pdf,doc,docm,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg,webp,mp3,mp4'],
        ]);

        $payload = [
            'lecture_id' => (int) $data['lecture_id'],
            'title' => $data['title'] ?: $material->title,
            'is_visible_to_student' => $request->boolean('is_visible_to_student', true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            LectureMaterialStorage::delete($material->file_path, $material->storage_disk);
            $disk = LectureMaterialStorage::resolvedDisk();
            $payload['file_path'] = LectureMaterialStorage::store($file, $payload['lecture_id']);
            $payload['storage_disk'] = $disk;
            $payload['file_name'] = $file->getClientOriginalName();
            if (empty($data['title'])) {
                $payload['title'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            }
        }

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
