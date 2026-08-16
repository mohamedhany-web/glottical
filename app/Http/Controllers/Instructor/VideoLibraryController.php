<?php

namespace App\Http\Controllers\Instructor;

use App\Helpers\VideoHelper;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\LibraryFolder;
use App\Models\LibraryVideo;
use App\Services\CloudflareR2;
use App\Services\LibraryVideoUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * فيديوهات المعلم لطلابه + فيديوهات الأكاديمية للعرض فقط.
 */
class VideoLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! $user->isAcademyWorkingInstructor()) {
                abort(403, 'مكتبة الفيديو متاحة للمعلمين المعتمدين فقط.');
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $videos = LibraryVideo::query()
            ->where('instructor_id', $user->id)
            ->where('audience', LibraryVideo::AUDIENCE_TEACHER_STUDENTS)
            ->with(['folder:id,name_ar,name_en'])
            ->ordered()
            ->paginate(24)
            ->withQueryString();

        $academyVideos = LibraryVideo::query()
            ->published()
            ->general()
            ->whereNull('instructor_id')
            ->with(['folder:id,name_ar,name_en'])
            ->ordered()
            ->limit(48)
            ->get();

        $folders = LibraryFolder::query()
            ->ofKind(LibraryFolder::KIND_VIDEOS)
            ->where('instructor_id', $user->id)
            ->with(['academicYear:id,name'])
            ->withCount(['libraryVideos' => fn ($q) => $q->where('instructor_id', $user->id)])
            ->ordered()
            ->get();

        $years = Schema::hasTable('academic_years')
            ? AcademicYear::query()->ordered()->get(['id', 'name'])
            : collect();

        return view('instructor.libraries.videos.index', compact('videos', 'academyVideos', 'folders', 'years'));
    }

    public function create(): View
    {
        $user = request()->user();

        return view('instructor.libraries.videos.form', [
            'mode' => 'create',
            'video' => new LibraryVideo([
                'is_published' => true,
                'sort_order' => 0,
                'duration_seconds' => 0,
            ]),
            'folders' => LibraryFolder::query()
                ->ofKind(LibraryFolder::KIND_VIDEOS)
                ->where('instructor_id', $user->id)
                ->active()
                ->ordered()
                ->get(),
            'uploadDisk' => LibraryVideoUploadService::uploadDiskName(),
            'presignRoute' => route('instructor.libraries.videos.presign'),
            'completeRoute' => route('instructor.libraries.videos.complete'),
            'proxyRoute' => route('instructor.libraries.videos.proxy'),
            'storeRoute' => route('instructor.libraries.videos.store'),
            'indexRoute' => route('instructor.libraries.videos.index'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $this->validatedMeta($request);

        if (empty($data['external_url']) && empty($data['file_path'])) {
            return back()->withInput()->with('error', 'أدخل رابط فيديو أو ارفع ملفاً إلى Cloudflare.');
        }

        if (! empty($data['external_url']) && ! VideoHelper::isValidVideoUrl($data['external_url'])) {
            return back()->withInput()->withErrors(['external_url' => 'الرابط غير مدعوم (YouTube / Vimeo / Bunny / رابط مباشر).']);
        }

        $folderId = $data['library_folder_id'] ?? null;
        if ($folderId) {
            $this->assertOwnsFolder((int) $folderId);
        }

        LibraryVideo::create([
            'library_folder_id' => $folderId,
            'created_by' => $user->id,
            'audience' => LibraryVideo::AUDIENCE_TEACHER_STUDENTS,
            'instructor_id' => $user->id,
            'title' => $data['title'],
            'series_title' => $data['series_title'] ?? null,
            'age_label' => $data['age_label'] ?? null,
            'content_theme' => $data['content_theme'] ?? \App\Support\FamilyLibraryThemes::KIDS,
            'description' => $data['description'] ?? null,
            'external_url' => $data['external_url'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'storage_disk' => ! empty($data['file_path']) ? ($data['storage_disk'] ?? LibraryVideoUploadService::uploadDiskName()) : null,
            'file_size' => (int) ($data['file_size'] ?? 0),
            'duration_seconds' => (int) ($data['duration_seconds'] ?? 0),
            'mime_type' => $data['mime_type'] ?? null,
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('instructor.libraries.videos.index')
            ->with('success', 'تم إضافة الفيديو — يظهر لطلابك فقط (والإدارة).');
    }

    public function watch(LibraryVideo $libraryVideo): View
    {
        $this->assertCanWatch($libraryVideo);

        $url = $libraryVideo->getUrl();
        abort_unless($url, 404, 'رابط الفيديو غير متوفر حالياً');

        $embedUrl = VideoHelper::getEmbedUrl($url);
        $directUrl = $embedUrl ? null : (VideoHelper::getDirectVideoUrl($url) ?: $url);
        $source = VideoHelper::getVideoSource($url);
        $thumbnail = VideoHelper::getThumbnail($url);

        return view('instructor.libraries.videos.watch', [
            'libraryVideo' => $libraryVideo,
            'embedUrl' => $embedUrl,
            'directUrl' => $directUrl,
            'source' => $source,
            'thumbnail' => $thumbnail,
            'isOwn' => $libraryVideo->isTeacherPrivate()
                && (int) $libraryVideo->instructor_id === (int) request()->user()->id,
        ]);
    }

    public function edit(LibraryVideo $libraryVideo): View
    {
        $this->assertOwnsVideo($libraryVideo);
        $user = request()->user();

        return view('instructor.libraries.videos.form', [
            'mode' => 'edit',
            'video' => $libraryVideo,
            'folders' => LibraryFolder::query()
                ->ofKind(LibraryFolder::KIND_VIDEOS)
                ->where('instructor_id', $user->id)
                ->ordered()
                ->get(),
            'uploadDisk' => LibraryVideoUploadService::uploadDiskName(),
            'presignRoute' => route('instructor.libraries.videos.presign'),
            'completeRoute' => route('instructor.libraries.videos.complete'),
            'proxyRoute' => route('instructor.libraries.videos.proxy'),
            'storeRoute' => route('instructor.libraries.videos.update', $libraryVideo),
            'indexRoute' => route('instructor.libraries.videos.index'),
        ]);
    }

    public function update(Request $request, LibraryVideo $libraryVideo): RedirectResponse
    {
        $this->assertOwnsVideo($libraryVideo);
        $data = $this->validatedMeta($request);

        if (empty($data['external_url']) && empty($data['file_path']) && ! $libraryVideo->file_path) {
            return back()->withInput()->with('error', 'أدخل رابط فيديو أو ارفع ملفاً إلى Cloudflare.');
        }

        if (! empty($data['external_url']) && ! VideoHelper::isValidVideoUrl($data['external_url'])) {
            return back()->withInput()->withErrors(['external_url' => 'الرابط غير مدعوم (YouTube / Vimeo / Bunny / رابط مباشر).']);
        }

        $folderId = $data['library_folder_id'] ?? null;
        if ($folderId) {
            $this->assertOwnsFolder((int) $folderId);
        }

        $payload = [
            'library_folder_id' => $folderId,
            'audience' => LibraryVideo::AUDIENCE_TEACHER_STUDENTS,
            'instructor_id' => $request->user()->id,
            'title' => $data['title'],
            'series_title' => $data['series_title'] ?? null,
            'age_label' => $data['age_label'] ?? null,
            'content_theme' => $data['content_theme'] ?? \App\Support\FamilyLibraryThemes::KIDS,
            'description' => $data['description'] ?? null,
            'external_url' => $data['external_url'] ?? null,
            'duration_seconds' => (int) ($data['duration_seconds'] ?? 0),
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if (! empty($data['file_path'])) {
            if ($libraryVideo->file_path && $libraryVideo->file_path !== $data['file_path']) {
                LibraryVideoUploadService::deleteStored($libraryVideo->file_path, $libraryVideo->storage_disk);
            }
            $payload['file_path'] = $data['file_path'];
            $payload['storage_disk'] = $data['storage_disk'] ?? LibraryVideoUploadService::uploadDiskName();
            $payload['file_size'] = (int) ($data['file_size'] ?? 0);
            $payload['mime_type'] = $data['mime_type'] ?? $libraryVideo->mime_type;
        }

        if ($request->boolean('clear_file') && $libraryVideo->file_path) {
            LibraryVideoUploadService::deleteStored($libraryVideo->file_path, $libraryVideo->storage_disk);
            $payload['file_path'] = null;
            $payload['storage_disk'] = null;
            $payload['file_size'] = 0;
            $payload['mime_type'] = null;
        }

        $libraryVideo->update($payload);

        return redirect()
            ->route('instructor.libraries.videos.index')
            ->with('success', 'تم تحديث الفيديو.');
    }

    public function destroy(LibraryVideo $libraryVideo): RedirectResponse
    {
        $this->assertOwnsVideo($libraryVideo);
        LibraryVideoUploadService::deleteStored($libraryVideo->file_path, $libraryVideo->storage_disk);
        $libraryVideo->delete();

        return back()->with('success', 'تم حذف الفيديو.');
    }

    public function togglePublish(LibraryVideo $libraryVideo): RedirectResponse
    {
        $this->assertOwnsVideo($libraryVideo);
        $libraryVideo->update(['is_published' => ! $libraryVideo->is_published]);

        return back()->with('success', $libraryVideo->is_published ? 'تم نشر الفيديو لطلابك.' : 'تم إلغاء نشر الفيديو.');
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'description_ar' => ['nullable', 'string', 'max:255'],
            'content_theme' => ['nullable', Rule::in(\App\Support\FamilyLibraryThemes::keys())],
        ]);

        $theme = $data['content_theme'] ?? \App\Support\FamilyLibraryThemes::KIDS;
        $meta = \App\Support\FamilyLibraryThemes::meta($theme);

        LibraryFolder::create([
            'instructor_id' => $user->id,
            'academic_year_id' => isset($data['academic_year_id']) ? (int) $data['academic_year_id'] : null,
            'kind' => LibraryFolder::KIND_VIDEOS,
            'content_theme' => $theme,
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'icon' => $meta['icon'] ?? 'fas fa-video',
            'color' => $meta['tone'] ?? 'blue',
            'sort_order' => 0,
            'is_active' => true,
            'requires_library_entitlement' => false,
        ]);

        return back()->with('success', 'تم إنشاء مجلد الفيديو — يظهر لطلابك فقط.');
    }

    public function presignUpload(Request $request): JsonResponse
    {
        $request->validate([
            'content_type' => ['nullable', 'string', 'max:191'],
            'filename' => ['nullable', 'string', 'max:255'],
        ]);

        CloudflareR2::ensureBrowserUploadCors([$request->getSchemeAndHttpHost()]);

        return LibraryVideoUploadService::presign(
            (int) $request->user()->id,
            $request->input('content_type'),
            $request->input('filename')
        );
    }

    public function proxyUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_token' => ['required', 'string', 'size:64'],
            'file' => ['required', 'file', 'max:204800'],
        ]);

        return LibraryVideoUploadService::proxy(
            (int) $request->user()->id,
            $validated['upload_token'],
            $request->file('file')
        );
    }

    public function completeUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_token' => ['required', 'string', 'size:64'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        return LibraryVideoUploadService::complete(
            (int) $request->user()->id,
            $validated['upload_token'],
            isset($validated['duration_seconds']) ? (int) $validated['duration_seconds'] : null
        );
    }

    private function validatedMeta(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content_theme' => ['nullable', Rule::in(\App\Support\FamilyLibraryThemes::keys())],
            'series_title' => ['nullable', 'string', 'max:255'],
            'age_label' => ['nullable', 'string', 'max:40'],
            'library_folder_id' => ['nullable', 'exists:library_folders,id'],
            'external_url' => ['nullable', 'string', 'max:2000'],
            'file_path' => ['nullable', 'string', 'max:1000'],
            'storage_disk' => ['nullable', 'string', 'max:40'],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'mime_type' => ['nullable', 'string', 'max:120'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
            'clear_file' => ['nullable', 'boolean'],
        ]);
    }

    private function assertCanWatch(LibraryVideo $video): void
    {
        $user = request()->user();
        if ($video->isTeacherPrivate()) {
            abort_unless(
                (int) $video->instructor_id === (int) $user->id,
                403,
                'هذا الفيديو ليس لك.'
            );

            return;
        }

        abort_unless(
            $video->is_published && ($video->audience === LibraryVideo::AUDIENCE_GENERAL || $video->audience === null),
            403,
            'هذا الفيديو غير متاح للعرض.'
        );
    }

    private function assertOwnsVideo(LibraryVideo $video): void
    {
        $user = request()->user();
        abort_unless(
            $video->isTeacherPrivate() && (int) $video->instructor_id === (int) $user->id,
            403,
            'هذا الفيديو ليس لك.'
        );
    }

    private function assertOwnsFolder(int $folderId): void
    {
        $user = request()->user();
        $ok = LibraryFolder::query()
            ->ofKind(LibraryFolder::KIND_VIDEOS)
            ->where('id', $folderId)
            ->where('instructor_id', $user->id)
            ->exists();
        abort_unless($ok, 403, 'المجلد غير تابع لك.');
    }
}
