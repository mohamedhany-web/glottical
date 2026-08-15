<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumLibrarySection;
use App\Models\CurriculumPresentationDerivative;
use App\Services\CurriculumLibraryR2MultipartService;
use App\Services\CurriculumPresentationConversionService;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CurriculumLibraryStructureController extends Controller
{
    public function __construct(
        protected CurriculumLibraryR2MultipartService $r2Multipart,
        protected CurriculumPresentationConversionService $presentationConversion
    ) {}

    /** امتدادات مسموحة لمواد المناهج (رفع لوحة التحكم) — متوافقة تقريباً مع FileUploadSecurityMiddleware */
    private const CURRICULUM_MATERIAL_EXTENSIONS = [
        'pdf', 'html', 'htm', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'zip', 'rar',
        'png', 'jpg', 'jpeg', 'gif', 'webp',
    ];

    private const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phar', 'exe', 'bat', 'cmd', 'com', 'msi',
        'sh', 'js', 'jsp', 'asp', 'aspx', 'dll', 'sys',
    ];

    public function show(CurriculumLibraryItem $item)
    {
        $tree = CurriculumLibrarySection::treeForItem($item, false);
        $flatSections = $item->sections()->withCount('materials')->orderBy('order')->orderBy('id')->get();
        $materialDirectUpload = $this->curriculumMaterialDiskSupportsDirectUpload();

        return view('admin.curriculum-library.structure', compact('item', 'tree', 'flatSections', 'materialDirectUpload'));
    }

    public function storeSection(Request $request, CurriculumLibraryItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:curriculum_library_sections,id',
            'order' => 'nullable|integer|min:0',
        ]);

        if (! empty($validated['parent_id'])) {
            CurriculumLibrarySection::where('id', $validated['parent_id'])
                ->where('curriculum_library_item_id', $item->id)
                ->firstOrFail();
        }

        CurriculumLibrarySection::create([
            'curriculum_library_item_id' => $item->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => (int) ($validated['order'] ?? 0),
            'is_active' => true,
        ]);

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم إنشاء القسم.');
    }

    public function updateSection(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:curriculum_library_sections,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $newParentId = $validated['parent_id'] ?? null;
        if ($newParentId !== null && (int) $newParentId === (int) $section->id) {
            return back()->with('error', 'لا يمكن جعل القسم أباً لنفسه.');
        }

        if ($newParentId !== null) {
            CurriculumLibrarySection::where('id', $newParentId)
                ->where('curriculum_library_item_id', $item->id)
                ->firstOrFail();

            if ($this->wouldCreateCycle($section, (int) $newParentId)) {
                return back()->with('error', 'لا يمكن نقل القسم تحت أحد أبنائه.');
            }
        }

        $section->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'parent_id' => $newParentId,
            'order' => (int) ($validated['order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم تحديث القسم.');
    }

    public function destroySection(CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);
        $section->deleteWithStorage();

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم حذف القسم وما بداخله.');
    }

    public function storeMaterial(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);

        $maxKb = (int) config('upload_limits.curriculum_material_max_kb', 150 * 1024);
        $maxMbLabel = max(1, (int) round($maxKb / 1024));

        $request->validate([
            'file' => 'required|file|max:'.$maxKb,
            'title' => 'nullable|string|max:255',
            'view_in_platform' => 'nullable|boolean',
            'allow_download' => 'nullable|boolean',
        ], [
            'file.max' => 'حجم الملف يتجاوز الحد المسموح لمادة المنهج ('.$maxMbLabel.' ميجابايت كحد أقصى).',
        ]);

        @ini_set('max_input_time', '7200');
        @set_time_limit(0);

        try {
            $upload = $request->file('file');
            $ext = strtolower((string) $upload->getClientOriginalExtension());
            $fileKind = CurriculumLibraryMaterial::fileKindFromExtension($ext);

            $viewIn = $request->boolean('view_in_platform');
            $allowDl = $request->boolean('allow_download');
            [$viewIn, $allowDl] = $this->applyCurriculumMaterialKindRules($fileKind, $viewIn, $allowDl);

            $path = $upload->store('curriculum-library/materials/'.$section->id, 'r2');
            $order = ((int) ($section->materials()->max('order') ?? 0)) + 1;

            $material = CurriculumLibraryMaterial::create([
                'curriculum_library_section_id' => $section->id,
                'title' => $request->input('title') ?: null,
                'path' => $path,
                'storage_disk' => 'r2',
                'original_name' => $upload->getClientOriginalName(),
                'file_kind' => $fileKind,
                'view_in_platform' => $viewIn,
                'allow_download' => $allowDl,
                'order' => $order,
                'is_active' => true,
            ]);

            $this->queuePresentationConversionAfterCreate($material);
        } catch (\Throwable $e) {
            Log::error('Curriculum material upload failed', [
                'item_id' => $item->id,
                'section_id' => $section->id,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'تعذّر رفع الملف. تحقّق من نوع الملف والاتصال ثم أعد المحاولة.');
        }

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم رفع المادة بنجاح.');
    }

    public function updateMaterial(Request $request, CurriculumLibraryItem $item, CurriculumLibraryMaterial $material)
    {
        $this->assertMaterialBelongs($item, $material);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'view_in_platform' => 'nullable|boolean',
            'allow_download' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $fileKind = $material->file_kind;

        $viewIn = $request->boolean('view_in_platform');
        $allowDl = $request->boolean('allow_download');
        [$viewIn, $allowDl] = $this->applyCurriculumMaterialKindRules($fileKind, $viewIn, $allowDl);

        $material->update([
            'title' => $validated['title'] ?? $material->title,
            'view_in_platform' => $viewIn,
            'allow_download' => $allowDl,
            'order' => (int) ($validated['order'] ?? $material->order),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم تحديث المادة.');
    }

    public function destroyMaterial(CurriculumLibraryItem $item, CurriculumLibraryMaterial $material)
    {
        $this->assertMaterialBelongs($item, $material);

        $material->deleteWithStorage();

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم حذف المادة.');
    }

    /**
     * إعادة محاولة تحويل عرض PPT/PPTX إلى شرائح مشتقة (لا يمس الملف الأصلي).
     */
    public function retryMaterialPresentationConversion(CurriculumLibraryItem $item, CurriculumLibraryMaterial $material)
    {
        $this->assertMaterialBelongs($item, $material);

        if (! Schema::hasTable('curriculum_presentation_derivatives')) {
            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'جدول التحويل غير جاهز بعد. نفّذ الترحيلات أولاً.');
        }

        if (! $this->presentationConversion->materialIsConvertible($material)) {
            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'هذه المادة ليست عرض PPT/PPTX قابلاً للتحويل.');
        }

        $derivative = $material->presentationDerivative()->first();
        if ($derivative && in_array($derivative->status, [
            CurriculumPresentationDerivative::STATUS_PENDING,
            CurriculumPresentationDerivative::STATUS_PROCESSING,
        ], true)) {
            if (request()->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'التحويل قيد التنفيذ بالفعل.',
                ], 409);
            }

            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'التحويل قيد التنفيذ بالفعل.');
        }

        $this->presentationConversion->queueMaterialIfEligible($material);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'تمت جدولة إعادة التحويل.']);
        }

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تمت جدولة إعادة تحويل العرض.');
    }

    /**
     * حالة تحويل مختصرة للوحة التحكم؛ لا تكشف مسارات التخزين أو روابط المصدر.
     */
    public function materialPresentationConversionStatus(
        CurriculumLibraryItem $item,
        CurriculumLibraryMaterial $material
    ): JsonResponse {
        $this->assertMaterialBelongs($item, $material);

        if (! $this->presentationConversion->materialIsConvertible($material)) {
            abort(404);
        }

        if (! Schema::hasTable('curriculum_presentation_derivatives')) {
            return response()->json([
                'status' => CurriculumPresentationDerivative::STATUS_UNAVAILABLE,
                'slide_count' => null,
                'error_summary' => 'خدمة التحويل غير جاهزة حالياً.',
            ]);
        }

        $derivative = $material->presentationDerivative()->first();
        $status = $derivative?->status;
        $allowedStatuses = [
            CurriculumPresentationDerivative::STATUS_PENDING,
            CurriculumPresentationDerivative::STATUS_PROCESSING,
            CurriculumPresentationDerivative::STATUS_READY,
            CurriculumPresentationDerivative::STATUS_FAILED,
            CurriculumPresentationDerivative::STATUS_UNAVAILABLE,
            CurriculumPresentationDerivative::STATUS_STALE,
        ];

        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'none';
        }

        return response()->json([
            'status' => $status,
            'slide_count' => $status === CurriculumPresentationDerivative::STATUS_READY
                ? max(0, (int) ($derivative?->slide_count ?? 0))
                : null,
            'error_summary' => CurriculumPresentationDerivative::safeErrorSummary($derivative?->error_message),
        ]);
    }

    /**
     * Upload/replace companion animation video (MP4/WebM). Never touches original PPT/PPTX path.
     */
    public function storeMaterialAnimationVideo(
        Request $request,
        CurriculumLibraryItem $item,
        CurriculumLibraryMaterial $material
    ) {
        $this->assertMaterialBelongs($item, $material);

        if ($material->file_kind !== 'pptx') {
            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'فيديو الحركات متاح فقط لمواد PPT/PPTX.');
        }

        $maxBytes = (int) config('curriculum_presentation.animation_video_max_bytes', 500 * 1024 * 1024);
        $maxKb = max(1, (int) ceil($maxBytes / 1024));
        $maxMbLabel = max(1, (int) round($maxBytes / 1024 / 1024));
        $allowedExt = config('curriculum_presentation.animation_video_allowed_extensions', ['mp4', 'webm']);
        $allowedMimes = config('curriculum_presentation.animation_video_allowed_mimes', [
            'video/mp4', 'video/webm', 'application/mp4', 'application/octet-stream',
        ]);

        $request->validate([
            'animation_video' => 'required|file|max:'.$maxKb,
        ], [
            'animation_video.required' => 'يرجى اختيار ملف فيديو MP4 أو WebM.',
            'animation_video.max' => 'حجم فيديو الحركات يتجاوز الحد المسموح ('.$maxMbLabel.' ميجابايت كحد أقصى).',
        ]);

        @ini_set('max_input_time', '7200');
        @set_time_limit(0);

        $upload = $request->file('animation_video');
        $ext = strtolower((string) $upload->getClientOriginalExtension());
        $mime = strtolower((string) ($upload->getMimeType() ?: ''));

        if (! in_array($ext, $allowedExt, true)) {
            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'يُسمح فقط بملفات MP4 أو WebM لفيديو الحركات.');
        }

        if ($mime !== '' && ! in_array($mime, $allowedMimes, true)) {
            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'نوع ملف الفيديو غير مسموح. استخدم تصدير PowerPoint إلى MP4 أو WebM.');
        }

        $sourcePathBefore = $material->path;
        $sourceDiskBefore = $material->storage_disk;
        $oldVideoPath = $material->animation_video_path;
        $oldVideoDisk = $material->animation_video_disk ?: 'r2';

        $uuid = (string) Str::uuid();
        $filename = $uuid.'.'.$ext;
        $directory = 'curriculum-library/animations/material/'.$material->id;
        $newPath = null;

        try {
            $newPath = $upload->storeAs($directory, $filename, 'r2');
            if (! $newPath) {
                throw new \RuntimeException('Failed to store animation video sidecar.');
            }

            $material->forceFill([
                'animation_video_path' => $newPath,
                'animation_video_disk' => 'r2',
                'animation_video_original_name' => $upload->getClientOriginalName(),
                'animation_video_mime' => $mime !== '' ? $mime : ($ext === 'webm' ? 'video/webm' : 'video/mp4'),
                'animation_video_size' => (int) $upload->getSize(),
                'animation_video_uploaded_at' => now(),
            ])->save();
        } catch (Throwable $e) {
            if ($newPath) {
                try {
                    Storage::disk('r2')->delete($newPath);
                } catch (Throwable) {
                }
            }

            Log::error('Curriculum animation video upload failed', [
                'item_id' => $item->id,
                'material_id' => $material->id,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'تعذّر رفع فيديو الحركات. تحقّق من الملف والاتصال ثم أعد المحاولة.');
        }

        // Source invariant: never mutate original PPT/PPTX object or path.
        $material->refresh();
        if ($material->path !== $sourcePathBefore || $material->storage_disk !== $sourceDiskBefore) {
            Log::critical('Animation video upload mutated source path — refusing to continue cleanup', [
                'material_id' => $material->id,
            ]);
        }

        if ($oldVideoPath && $oldVideoPath !== $newPath) {
            try {
                if (Storage::disk($oldVideoDisk)->exists($oldVideoPath)) {
                    Storage::disk($oldVideoDisk)->delete($oldVideoPath);
                }
            } catch (Throwable) {
                // Best-effort old sidecar cleanup only.
            }
        }

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم حفظ فيديو الحركات بنجاح (الملف الأصلي لم يُمس).');
    }

    /**
     * Remove companion animation video sidecar only — never the original PPT/PPTX.
     */
    public function destroyMaterialAnimationVideo(
        CurriculumLibraryItem $item,
        CurriculumLibraryMaterial $material
    ) {
        $this->assertMaterialBelongs($item, $material);

        if ($material->file_kind !== 'pptx') {
            return redirect()->route('admin.curriculum-library.items.structure', $item)
                ->with('error', 'فيديو الحركات متاح فقط لمواد PPT/PPTX.');
        }

        $sourcePathBefore = $material->path;
        $sourceDiskBefore = $material->storage_disk;

        $material->deleteAnimationVideoFromStorage();
        $material->clearAnimationVideoAttributes();

        $material->refresh();
        if ($material->path !== $sourcePathBefore || $material->storage_disk !== $sourceDiskBefore) {
            Log::critical('Animation video delete mutated source path', [
                'material_id' => $material->id,
            ]);
        }

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم حذف فيديو الحركات. العرض الأصلي والشرائح المشتقة لم تُمس.');
    }

    /**
     * تجهيز رابط PUT موقّت: الرفع من المتصفح مباشرة إلى R2 (لا يمر بحدود PHP).
     */
    public function presignMaterialUpload(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);
        @set_time_limit(120);

        if (! $this->curriculumMaterialDiskSupportsDirectUpload()) {
            return response()->json([
                'direct_upload' => false,
                'message' => 'الرفع السريع غير متاح حالياً. جرّب لاحقاً أو استخدم الرفع التقليدي.',
            ]);
        }

        $maxBytes = (int) config('upload_limits.curriculum_material_max_bytes', 150 * 1024 * 1024);

        $validated = $request->validate([
            'content_type' => ['nullable', 'string', 'max:191'],
            'original_name' => ['required', 'string', 'max:255'],
            'file_size' => ['required', 'integer', 'min:1', 'max:'.$maxBytes],
        ]);

        $originalName = basename(str_replace(["\0", '\\'], '', $validated['original_name']));
        if ($originalName === '' || $originalName === '.' || $originalName === '..') {
            return response()->json(['message' => 'اسم الملف غير صالح.'], 422);
        }

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext === '' || in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
            return response()->json(['message' => 'امتداد الملف غير مسموح.'], 422);
        }
        if (! in_array($ext, self::CURRICULUM_MATERIAL_EXTENSIONS, true)) {
            return response()->json(['message' => 'امتداد الملف غير مسموح لمادة المنهج.'], 422);
        }

        $mime = $this->normalizeCurriculumMaterialMime(
            (string) ($validated['content_type'] ?? ''),
            $originalName,
            $ext
        );

        $diskName = 'r2';
        $disk = Storage::disk($diskName);
        $baseDir = 'curriculum-library/materials/'.$section->id;
        $newPath = $baseDir.'/'.Str::uuid()->toString().'.'.$ext;

        $uploadToken = Str::random(64);
        Cache::put(
            'curriculum_library_mat_presign:'.$uploadToken,
            [
                'path' => $newPath,
                'curriculum_library_item_id' => (int) $item->id,
                'curriculum_library_section_id' => (int) $section->id,
                'user_id' => (int) auth()->id(),
                'mime' => $mime,
                'disk' => $diskName,
                'original_name' => $originalName,
                'max_bytes' => $maxBytes,
            ],
            now()->addMinutes(75)
        );

        try {
            $signed = $disk->temporaryUploadUrl(
                $newPath,
                now()->addMinutes(70),
                [
                    'ContentType' => $mime,
                ]
            );
        } catch (\Throwable $e) {
            Cache::forget('curriculum_library_mat_presign:'.$uploadToken);
            Log::error('Curriculum material presign failed', [
                'item_id' => $item->id,
                'section_id' => $section->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'direct_upload' => false,
                'message' => 'تعذّر تجهيز الرفع. أعد المحاولة بعد قليل.',
            ], 503);
        }

        return response()->json([
            'direct_upload' => true,
            'upload_url' => $signed['url'],
            'upload_token' => $uploadToken,
            'content_type' => $mime,
            'headers' => CurriculumLibraryR2MultipartService::filterPresignedUploadHeadersForBrowser($signed['headers'] ?? []),
        ]);
    }

    /**
     * بعد PUT الناجح إلى R2: إنشاء سجل المادة في قاعدة البيانات.
     */
    public function completeMaterialDirectUpload(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);
        @set_time_limit(120);

        if (! $this->curriculumMaterialDiskSupportsDirectUpload()) {
            return response()->json(['message' => 'الرفع الموثوق غير متاح حالياً.'], 503);
        }

        $validated = $request->validate([
            'upload_token' => ['required', 'string', 'size:64'],
            'title' => ['nullable', 'string', 'max:255'],
            'view_in_platform' => ['nullable', 'boolean'],
            'allow_download' => ['nullable', 'boolean'],
        ]);

        $cacheKey = 'curriculum_library_mat_presign:'.$validated['upload_token'];
        $payload = Cache::pull($cacheKey);
        if (! is_array($payload)
            || (int) ($payload['curriculum_library_item_id'] ?? 0) !== (int) $item->id
            || (int) ($payload['curriculum_library_section_id'] ?? 0) !== (int) $section->id
            || (int) ($payload['user_id'] ?? 0) !== (int) auth()->id()) {
            return response()->json([
                'message' => 'انتهت صلاحية الرفع أو أنه غير صالح. أعد المحاولة.',
            ], 422);
        }

        $path = (string) ($payload['path'] ?? '');
        $diskName = (string) ($payload['disk'] ?? 'r2');
        $originalName = (string) ($payload['original_name'] ?? '');
        $maxBytes = (int) ($payload['max_bytes'] ?? config('upload_limits.curriculum_material_max_bytes', 150 * 1024 * 1024));

        if ($path === '' || str_contains($path, '..') || $diskName !== 'r2') {
            return response()->json(['message' => 'مسار التخزين غير صالح.'], 422);
        }

        $fail = $this->persistCurriculumMaterialFromR2Path(
            $item,
            $section,
            $path,
            $originalName,
            $validated['title'] ?? null,
            $request->boolean('view_in_platform'),
            $request->boolean('allow_download'),
            $maxBytes,
            $diskName
        );
        if ($fail !== null) {
            return $fail;
        }

        session()->flash('success', 'تم رفع المادة بنجاح.');

        return response()->json([
            'ok' => true,
            'redirect' => route('admin.curriculum-library.items.structure', $item),
        ]);
    }

    public function multipartInitMaterial(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);
        @set_time_limit(120);

        if (! $this->curriculumMaterialDiskSupportsDirectUpload()) {
            return response()->json(['message' => 'الرفع الموثوق غير متاح حالياً.'], 503);
        }

        $maxBytes = (int) config('upload_limits.curriculum_material_max_bytes', 150 * 1024 * 1024);
        $validated = $request->validate([
            'content_type' => ['nullable', 'string', 'max:191'],
            'original_name' => ['required', 'string', 'max:255'],
            'file_size' => ['required', 'integer', 'min:1', 'max:'.$maxBytes],
        ]);

        $originalName = basename(str_replace(["\0", '\\'], '', $validated['original_name']));
        if ($originalName === '' || $originalName === '.' || $originalName === '..') {
            return response()->json(['message' => 'اسم الملف غير صالح.'], 422);
        }

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext === '' || in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
            return response()->json(['message' => 'امتداد الملف غير مسموح.'], 422);
        }
        if (! in_array($ext, self::CURRICULUM_MATERIAL_EXTENSIONS, true)) {
            return response()->json(['message' => 'امتداد الملف غير مسموح لمادة المنهج.'], 422);
        }

        $mime = $this->normalizeCurriculumMaterialMime(
            (string) ($validated['content_type'] ?? ''),
            $originalName,
            $ext
        );

        $fileSize = (int) $validated['file_size'];
        $partSize = (int) config('upload_limits.curriculum_r2_multipart_part_bytes', 8 * 1024 * 1024);
        $minPart = 5 * 1024 * 1024;
        if ($partSize < $minPart) {
            $partSize = $minPart;
        }
        $totalParts = (int) max(1, (int) ceil($fileSize / $partSize));
        if ($totalParts > 10000) {
            return response()->json(['message' => 'عدد أجزاء الرفع كبير جداً. قلّل حجم الملف أو زد حجم الجزء.'], 422);
        }

        $baseDir = 'curriculum-library/materials/'.$section->id;
        $newPath = $baseDir.'/'.Str::uuid()->toString().'.'.$ext;

        try {
            $created = $this->r2Multipart->createMultipartUpload($newPath, $mime);
        } catch (Throwable $e) {
            Log::error('Curriculum material multipart init failed', [
                'item_id' => $item->id,
                'section_id' => $section->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'تعذّر بدء الرفع. أعد المحاولة بعد قليل.'], 503);
        }

        $token = Str::random(64);
        Cache::put(
            'curriculum_mat_mpu:'.$token,
            [
                'upload_id' => $created['UploadId'],
                'bucket' => $created['Bucket'],
                'key' => $created['Key'],
                'path' => $newPath,
                'curriculum_library_item_id' => (int) $item->id,
                'curriculum_library_section_id' => (int) $section->id,
                'user_id' => (int) auth()->id(),
                'mime' => $mime,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'part_size' => $partSize,
                'total_parts' => $totalParts,
                'max_bytes' => $maxBytes,
            ],
            now()->addMinutes(90)
        );

        return response()->json([
            'multipart' => true,
            'upload_session_token' => $token,
            'part_size' => $partSize,
            'total_parts' => $totalParts,
        ]);
    }

    public function multipartSignPartMaterial(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);
        @set_time_limit(60);

        $validated = $request->validate([
            'upload_session_token' => ['required', 'string', 'size:64'],
            'part_number' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $cacheKey = 'curriculum_mat_mpu:'.$validated['upload_session_token'];
        $payload = Cache::get($cacheKey);
        if (! is_array($payload)
            || (int) ($payload['curriculum_library_item_id'] ?? 0) !== (int) $item->id
            || (int) ($payload['curriculum_library_section_id'] ?? 0) !== (int) $section->id
            || (int) ($payload['user_id'] ?? 0) !== (int) auth()->id()) {
            return response()->json(['message' => 'جلسة الرفع غير صالحة أو منتهية.'], 422);
        }

        $partNumber = (int) $validated['part_number'];
        $totalParts = (int) ($payload['total_parts'] ?? 0);
        if ($partNumber < 1 || $partNumber > $totalParts) {
            return response()->json(['message' => 'رقم الجزء غير صالح.'], 422);
        }

        try {
            $signed = $this->r2Multipart->presignedUploadPart(
                (string) $payload['bucket'],
                (string) $payload['key'],
                (string) $payload['upload_id'],
                $partNumber
            );
        } catch (Throwable $e) {
            Log::error('Curriculum material multipart sign failed', [
                'item_id' => $item->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'تعذّر المتابعة. أعد المحاولة بعد قليل.'], 503);
        }

        return response()->json([
            'url' => $signed['url'],
            'headers' => $signed['headers'] ?? [],
        ]);
    }

    /**
     * رفع جزء multipart من الخادم إلى R2 (نفس المنشأ) لتجاوز أخطاء CORS/الشبكة بين المتصفح والتخزين.
     */
    public function multipartProxyUploadPartMaterial(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);
        @set_time_limit(0);

        if (! $this->curriculumMaterialDiskSupportsDirectUpload()) {
            return response()->json(['message' => 'الرفع الموثوق غير متاح حالياً.'], 503);
        }

        $token = (string) $request->header('X-Upload-Session-Token', '');
        $partHdr = $request->header('X-Part-Number');
        if (strlen($token) !== 64 || $partHdr === null || $partHdr === '' || ! ctype_digit((string) $partHdr)) {
            return response()->json(['message' => 'بيانات الجزء غير صالحة.'], 422);
        }
        $partNumber = (int) $partHdr;

        $cacheKey = 'curriculum_mat_mpu:'.$token;
        $payload = Cache::get($cacheKey);
        if (! is_array($payload)
            || (int) ($payload['curriculum_library_item_id'] ?? 0) !== (int) $item->id
            || (int) ($payload['curriculum_library_section_id'] ?? 0) !== (int) $section->id
            || (int) ($payload['user_id'] ?? 0) !== (int) auth()->id()) {
            return response()->json(['message' => 'جلسة الرفع غير صالحة أو منتهية.'], 422);
        }

        $totalParts = (int) ($payload['total_parts'] ?? 0);
        $fileSize = (int) ($payload['file_size'] ?? 0);
        $partSize = (int) ($payload['part_size'] ?? 0);
        if ($partNumber < 1 || $partNumber > $totalParts || $totalParts < 1 || $fileSize < 1 || $partSize < 1) {
            return response()->json(['message' => 'رقم الجزء غير صالح.'], 422);
        }

        $expectedLen = ($partNumber === $totalParts)
            ? ($fileSize - (($totalParts - 1) * $partSize))
            : $partSize;
        if ($expectedLen < 1) {
            return response()->json(['message' => 'حجم الجزء المتوقع غير صالح.'], 422);
        }

        $raw = $request->getContent();
        if (strlen($raw) !== $expectedLen) {
            return response()->json([
                'message' => 'حجم الجزء غير متطابق مع الجلسة (مستلم '.strlen($raw).' بايت، متوقع '.$expectedLen.').',
            ], 422);
        }

        try {
            $signed = $this->r2Multipart->presignedUploadPart(
                (string) $payload['bucket'],
                (string) $payload['key'],
                (string) $payload['upload_id'],
                $partNumber
            );
            $etag = $this->r2Multipart->relayPresignedPut(
                (string) $signed['url'],
                $signed['headers'] ?? [],
                $raw
            );
        } catch (Throwable $e) {
            Log::error('Curriculum material multipart proxy part failed', [
                'item_id' => $item->id,
                'section_id' => $section->id,
                'part' => $partNumber,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'تعذّر رفع الجزء عبر الخادم. أعد المحاولة بعد قليل.'], 502);
        }

        return response()->json(['etag' => $etag]);
    }

    public function multipartCompleteMaterial(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);
        @set_time_limit(300);

        if (! $this->curriculumMaterialDiskSupportsDirectUpload()) {
            return response()->json(['message' => 'الرفع الموثوق غير متاح حالياً.'], 503);
        }

        $validated = $request->validate([
            'upload_session_token' => ['required', 'string', 'size:64'],
            'parts' => ['required', 'array', 'min:1', 'max:10000'],
            'parts.*.PartNumber' => ['required', 'integer', 'min:1', 'max:10000'],
            'parts.*.ETag' => ['required', 'string', 'max:512'],
            'title' => ['nullable', 'string', 'max:255'],
            'view_in_platform' => ['nullable', 'boolean'],
            'allow_download' => ['nullable', 'boolean'],
        ]);

        $cacheKey = 'curriculum_mat_mpu:'.$validated['upload_session_token'];
        $payload = Cache::pull($cacheKey);
        if (! is_array($payload)
            || (int) ($payload['curriculum_library_item_id'] ?? 0) !== (int) $item->id
            || (int) ($payload['curriculum_library_section_id'] ?? 0) !== (int) $section->id
            || (int) ($payload['user_id'] ?? 0) !== (int) auth()->id()) {
            return response()->json(['message' => 'جلسة الرفع غير صالحة أو منتهية.'], 422);
        }

        $bucket = (string) ($payload['bucket'] ?? '');
        $key = (string) ($payload['key'] ?? '');
        $uploadId = (string) ($payload['upload_id'] ?? '');
        $path = (string) ($payload['path'] ?? '');
        $originalName = (string) ($payload['original_name'] ?? '');
        $totalParts = (int) ($payload['total_parts'] ?? 0);
        $maxBytes = (int) ($payload['max_bytes'] ?? config('upload_limits.curriculum_material_max_bytes', 150 * 1024 * 1024));

        if ($bucket === '' || $key === '' || $uploadId === '' || $path === '' || str_contains($path, '..')) {
            return response()->json(['message' => 'بيانات الرفع غير صالحة.'], 422);
        }

        $parts = $this->normalizeMultipartCompleteParts($validated['parts']);
        if (count($parts) !== $totalParts) {
            $this->r2Multipart->abortMultipartUpload($bucket, $key, $uploadId);

            return response()->json(['message' => 'عدد الأجزاء لا يطابق المطلوب ('.$totalParts.').'], 422);
        }

        try {
            $this->r2Multipart->completeMultipartUpload($bucket, $key, $uploadId, $parts);
        } catch (Throwable $e) {
            Log::error('Curriculum material multipart complete S3 failed', [
                'item_id' => $item->id,
                'message' => $e->getMessage(),
            ]);
            $this->r2Multipart->abortMultipartUpload($bucket, $key, $uploadId);

            return response()->json(['message' => 'تعذّر إتمام الرفع. أعد المحاولة.'], 422);
        }

        $fail = $this->persistCurriculumMaterialFromR2Path(
            $item,
            $section,
            $path,
            $originalName,
            $validated['title'] ?? null,
            $request->boolean('view_in_platform'),
            $request->boolean('allow_download'),
            $maxBytes,
            'r2'
        );
        if ($fail !== null) {
            try {
                Storage::disk('r2')->delete($path);
            } catch (Throwable) {
            }

            return $fail;
        }

        session()->flash('success', 'تم رفع المادة بنجاح.');

        return response()->json([
            'ok' => true,
            'redirect' => route('admin.curriculum-library.items.structure', $item),
        ]);
    }

    public function multipartAbortMaterial(Request $request, CurriculumLibraryItem $item, CurriculumLibrarySection $section)
    {
        $this->assertSectionBelongs($item, $section);

        $validated = $request->validate([
            'upload_session_token' => ['required', 'string', 'size:64'],
        ]);

        $cacheKey = 'curriculum_mat_mpu:'.$validated['upload_session_token'];
        $payload = Cache::pull($cacheKey);
        if (! is_array($payload)
            || (int) ($payload['curriculum_library_item_id'] ?? 0) !== (int) $item->id
            || (int) ($payload['curriculum_library_section_id'] ?? 0) !== (int) $section->id
            || (int) ($payload['user_id'] ?? 0) !== (int) auth()->id()) {
            return response()->json(['message' => 'لا توجد جلسة لإلغائها.'], 422);
        }

        $this->r2Multipart->abortMultipartUpload(
            (string) ($payload['bucket'] ?? ''),
            (string) ($payload['key'] ?? ''),
            (string) ($payload['upload_id'] ?? '')
        );

        return response()->json(['ok' => true, 'message' => 'تم إلغاء الرفع.']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $parts
     * @return array<int, array{PartNumber: int, ETag: string}>
     */
    private function normalizeMultipartCompleteParts(array $parts): array
    {
        $byNum = [];
        foreach ($parts as $p) {
            $n = (int) ($p['PartNumber'] ?? 0);
            $e = trim((string) ($p['ETag'] ?? ''));
            if ($n < 1 || $e === '') {
                continue;
            }
            if ($e[0] !== '"') {
                $e = '"'.trim($e, '"').'"';
            }
            $byNum[$n] = $e;
        }
        ksort($byNum, SORT_NUMERIC);
        $out = [];
        foreach ($byNum as $n => $e) {
            $out[] = ['PartNumber' => (int) $n, 'ETag' => $e];
        }

        return $out;
    }

    /**
     * التحقق من الملف على R2 وإنشاء سجل المادة.
     */
    private function persistCurriculumMaterialFromR2Path(
        CurriculumLibraryItem $item,
        CurriculumLibrarySection $section,
        string $path,
        string $originalName,
        ?string $title,
        bool $viewIn,
        bool $allowDl,
        int $maxBytes,
        string $diskName = 'r2'
    ): ?JsonResponse {
        if ($path === '' || str_contains($path, '..') || $diskName !== 'r2') {
            return response()->json(['message' => 'مسار التخزين غير صالح.'], 422);
        }

        $disk = Storage::disk($diskName);
        if (! $disk->exists($path)) {
            return response()->json([
                'message' => 'الملف غير ظاهر بعد على التخزين. انتظر قليلاً ثم أعد المحاولة.',
            ], 422);
        }

        $size = (int) $disk->size($path);
        if ($size <= 0) {
            try {
                $disk->delete($path);
            } catch (Throwable) {
            }

            return response()->json(['message' => 'الملف فارغ.'], 422);
        }
        if ($size > $maxBytes) {
            try {
                $disk->delete($path);
            } catch (Throwable) {
            }

            return response()->json(['message' => 'حجم الملف يتجاوز الحد المسموح.'], 422);
        }

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $fileKind = CurriculumLibraryMaterial::fileKindFromExtension($ext);
        [$viewIn, $allowDl] = $this->applyCurriculumMaterialKindRules($fileKind, $viewIn, $allowDl);

        try {
            $order = ((int) ($section->materials()->max('order') ?? 0)) + 1;

            $material = CurriculumLibraryMaterial::create([
                'curriculum_library_section_id' => $section->id,
                'title' => $title ?: null,
                'path' => $path,
                'storage_disk' => 'r2',
                'original_name' => $originalName,
                'file_kind' => $fileKind,
                'view_in_platform' => $viewIn,
                'allow_download' => $allowDl,
                'order' => $order,
                'is_active' => true,
            ]);

            $this->queuePresentationConversionAfterCreate($material);
        } catch (Throwable $e) {
            try {
                $disk->delete($path);
            } catch (Throwable) {
            }
            Log::error('Curriculum material persist after R2 failed', [
                'item_id' => $item->id,
                'section_id' => $section->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'فشل حفظ المادة بعد الرفع. أعد المحاولة.'], 500);
        }

        return null;
    }

    /**
     * Queue slide conversion only after the material row exists. Schema-guarded for lightweight tests.
     */
    protected function queuePresentationConversionAfterCreate(CurriculumLibraryMaterial $material): void
    {
        if (! Schema::hasTable('curriculum_presentation_derivatives')) {
            return;
        }

        try {
            $this->presentationConversion->queueMaterialIfEligible($material);
        } catch (Throwable $e) {
            Log::warning('Curriculum presentation conversion queue skipped', [
                'material_id' => $material->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function assertSectionBelongs(CurriculumLibraryItem $item, CurriculumLibrarySection $section): void
    {
        if ((int) $section->curriculum_library_item_id !== (int) $item->id) {
            abort(404);
        }
    }

    protected function assertMaterialBelongs(CurriculumLibraryItem $item, CurriculumLibraryMaterial $material): void
    {
        $material->loadMissing('section');
        if (! $material->section || (int) $material->section->curriculum_library_item_id !== (int) $item->id) {
            abort(404);
        }
    }

    protected function wouldCreateCycle(CurriculumLibrarySection $section, int $newParentId): bool
    {
        $walk = $newParentId;
        $guard = 0;
        while ($walk && $guard++ < 200) {
            if ((int) $walk === (int) $section->id) {
                return true;
            }
            $walk = (int) (CurriculumLibrarySection::where('id', $walk)->value('parent_id') ?? 0);
        }

        return false;
    }

    /**
     * @return array{0: bool, 1: bool} [view_in_platform, allow_download]
     */
    protected function applyCurriculumMaterialKindRules(string $fileKind, bool $viewIn, bool $allowDl): array
    {
        if ($fileKind === 'html') {
            return [true, false];
        }
        if ($fileKind === 'pptx') {
            return [$viewIn, false];
        }
        if ($fileKind === 'other') {
            return [false, $allowDl];
        }

        return [$viewIn, $allowDl];
    }

    /**
     * قرص r2 يستخدم AwsS3V3Adapter ويدعم temporaryUploadUrl (Put موقّت) وخدمة multipart لدينا.
     * لا تعتمد على providesTemporaryUploadUrls() — مع Flysystem 3 قد تُرجع false رغم أن R2/S3 يعملان.
     */
    protected function curriculumMaterialDiskSupportsDirectUpload(): bool
    {
        try {
            $disk = Storage::disk('r2');

            return $disk instanceof AwsS3V3Adapter;
        } catch (Throwable) {
            return false;
        }
    }

    private function normalizeCurriculumMaterialMime(string $contentType, string $originalName, string $ext): string
    {
        $contentType = strtolower(trim($contentType));
        $fromExt = $this->curriculumMimeFromExtension($ext);

        if ($contentType === '' || $contentType === 'application/octet-stream' || $contentType === 'binary/octet-stream') {
            return $fromExt;
        }

        return $fromExt;
    }

    private function curriculumMimeFromExtension(string $ext): string
    {
        $ext = strtolower($ext);

        return match ($ext) {
            'pdf' => 'application/pdf',
            'html', 'htm' => 'text/html',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'rar' => 'application/vnd.rar',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
    }
}
