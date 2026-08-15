<?php

namespace App\Services;

use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryItemFile;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumPresentationDerivative;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class CurriculumPresentationViewerService
{
    /**
     * رابط عام/مؤقت يمكن لعارض Microsoft جلب الملف منه (كما كان قبل التعديل).
     */
    public function absoluteStorageUrl(string $diskName, string $path): string
    {
        $disk = Storage::disk($diskName);

        if ($diskName === 'r2') {
            return $disk->temporaryUrl($path, now()->addHours(2));
        }

        $rel = $disk->url($path);
        if (str_starts_with($rel, 'http://') || str_starts_with($rel, 'https://')) {
            return $rel;
        }

        $host = request()->getSchemeAndHttpHost();
        if (str_starts_with($rel, '/')) {
            return rtrim($host, '/').$rel;
        }

        return rtrim($host, '/').'/'.ltrim($rel, '/');
    }

    /**
     * @return array{
     *   canUseOfficeViewer: bool,
     *   fileUrl: string,
     *   embedUrl: ?string
     * }
     */
    public function officeViewerPayload(string $fileUrl): array
    {
        $canUse = $this->isOfficeViewerSupportedUrl($fileUrl);
        if (! $canUse) {
            return [
                'canUseOfficeViewer' => false,
                'fileUrl' => $fileUrl,
                'embedUrl' => null,
            ];
        }

        $encoded = rawurlencode($fileUrl);

        return [
            'canUseOfficeViewer' => true,
            'fileUrl' => $fileUrl,
            'embedUrl' => 'https://view.officeapps.live.com/op/embed.aspx?src='.$encoded,
        ];
    }

    public function isOfficeViewerSupportedUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($host === '' || $host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
            return false;
        }

        if (str_ends_with($host, '.local') || str_ends_with($host, '.test')) {
            return false;
        }

        return true;
    }

    /**
     * Player payload for hierarchical curriculum materials.
     *
     * @return array{
     *   mode: 'native'|'office',
     *   manifestUrl: ?string,
     *   slideCount: ?int,
     *   width: ?int,
     *   height: ?int,
     *   version: ?int,
     *   publicUrl: string,
     *   embedUrl: ?string,
     *   canUseOfficeViewer: bool,
     *   playerConfig: array<string, mixed>
     * }
     */
    public function playerPayloadForMaterial(CurriculumLibraryItem $item, CurriculumLibraryMaterial $material): array
    {
        $diskName = $material->storage_disk ?: 'r2';
        $publicUrl = $this->absoluteStorageUrl($diskName, (string) $material->path);
        $office = $this->officeViewerPayload($publicUrl);

        $derivative = $this->resolveReadyDerivative(
            CurriculumPresentationDerivative::SOURCE_MATERIAL,
            (int) $material->id
        );
        $manifest = $derivative ? $this->loadAndValidateManifest($derivative) : null;

        if ($derivative && $manifest) {
            return [
                'mode' => 'native',
                'manifestUrl' => route('curriculum-library.material.slides.manifest', [
                    'item' => $item,
                    'material' => $material,
                ]),
                'slideCount' => (int) ($manifest['slide_count'] ?? $derivative->slide_count),
                'width' => isset($manifest['width']) ? (int) $manifest['width'] : ($derivative->width ? (int) $derivative->width : null),
                'height' => isset($manifest['height']) ? (int) $manifest['height'] : ($derivative->height ? (int) $derivative->height : null),
                'version' => (int) ($manifest['version'] ?? $derivative->version),
                'publicUrl' => $publicUrl,
                'embedUrl' => $office['embedUrl'],
                'canUseOfficeViewer' => $office['canUseOfficeViewer'],
                'playerConfig' => $this->defaultPlayerConfig(),
            ];
        }

        return [
            'mode' => 'office',
            'manifestUrl' => null,
            'slideCount' => null,
            'width' => null,
            'height' => null,
            'version' => null,
            'publicUrl' => $publicUrl,
            'embedUrl' => $office['embedUrl'],
            'canUseOfficeViewer' => $office['canUseOfficeViewer'],
            'playerConfig' => $this->defaultPlayerConfig(),
        ];
    }

    /**
     * Player payload for legacy CurriculumLibraryItemFile presentations.
     * Native mode only when a ready derivative exists for source_type=file.
     *
     * @return array{
     *   mode: 'native'|'office',
     *   manifestUrl: ?string,
     *   slideCount: ?int,
     *   width: ?int,
     *   height: ?int,
     *   version: ?int,
     *   publicUrl: string,
     *   embedUrl: ?string,
     *   canUseOfficeViewer: bool,
     *   playerConfig: array<string, mixed>
     * }
     */
    public function playerPayloadForFile(CurriculumLibraryItem $item, CurriculumLibraryItemFile $file): array
    {
        $diskName = $file->storage_disk ?: 'public';
        $publicUrl = $this->absoluteStorageUrl($diskName, (string) $file->path);
        $office = $this->officeViewerPayload($publicUrl);

        $derivative = $this->resolveReadyDerivative(
            CurriculumPresentationDerivative::SOURCE_FILE,
            (int) $file->id
        );
        $manifest = $derivative ? $this->loadAndValidateManifest($derivative) : null;

        if ($derivative && $manifest) {
            return [
                'mode' => 'native',
                'manifestUrl' => route('curriculum-library.file.slides.manifest', [
                    'item' => $item,
                    'file' => $file,
                ]),
                'slideCount' => (int) ($manifest['slide_count'] ?? $derivative->slide_count),
                'width' => isset($manifest['width']) ? (int) $manifest['width'] : ($derivative->width ? (int) $derivative->width : null),
                'height' => isset($manifest['height']) ? (int) $manifest['height'] : ($derivative->height ? (int) $derivative->height : null),
                'version' => (int) ($manifest['version'] ?? $derivative->version),
                'publicUrl' => $publicUrl,
                'embedUrl' => $office['embedUrl'],
                'canUseOfficeViewer' => $office['canUseOfficeViewer'],
                'playerConfig' => $this->defaultPlayerConfig(),
            ];
        }

        return [
            'mode' => 'office',
            'manifestUrl' => null,
            'slideCount' => null,
            'width' => null,
            'height' => null,
            'version' => null,
            'publicUrl' => $publicUrl,
            'embedUrl' => $office['embedUrl'],
            'canUseOfficeViewer' => $office['canUseOfficeViewer'],
            'playerConfig' => $this->defaultPlayerConfig(),
        ];
    }

    public function resolveReadyDerivative(string $sourceType, int $sourceId): ?CurriculumPresentationDerivative
    {
        if (! $this->derivativesTableReady()) {
            return null;
        }

        if (! in_array($sourceType, [
            CurriculumPresentationDerivative::SOURCE_MATERIAL,
            CurriculumPresentationDerivative::SOURCE_FILE,
        ], true)) {
            return null;
        }

        try {
            $derivative = CurriculumPresentationDerivative::query()
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->first();
        } catch (Throwable) {
            return null;
        }

        if (! $derivative || ! $derivative->isReady()) {
            return null;
        }

        if (! $derivative->manifest_path) {
            return null;
        }

        return $derivative;
    }

    /**
     * Load and validate a derivative manifest. Returns null if invalid/unreadable.
     * Internal shape may include storage keys; never return this array to clients as-is.
     *
     * @return array<string, mixed>|null
     */
    public function loadAndValidateManifest(CurriculumPresentationDerivative $derivative): ?array
    {
        $diskName = $derivative->storage_disk ?: 'r2';
        $manifestPath = (string) $derivative->manifest_path;

        if ($manifestPath === '' || ! $this->isSafeDerivativeKey($derivative, $manifestPath)) {
            return null;
        }

        try {
            $disk = Storage::disk($diskName);
            if (! $disk->exists($manifestPath)) {
                return null;
            }
            $raw = $disk->get($manifestPath);
        } catch (Throwable) {
            return null;
        }

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $data = json_decode($raw, true);
        if (! is_array($data)) {
            return null;
        }

        $slides = $data['slides'] ?? null;
        if (! is_array($slides) || $slides === []) {
            return null;
        }

        $normalizedSlides = [];
        $expectedIndex = 1;
        foreach ($slides as $slide) {
            if (! is_array($slide)) {
                return null;
            }
            $index = (int) ($slide['index'] ?? 0);
            if ($index !== $expectedIndex) {
                return null;
            }

            $imageKey = $this->resolveSlideStorageKey($derivative, $slide, 'image');
            if ($imageKey === null) {
                return null;
            }

            $thumbKey = $this->resolveSlideStorageKey($derivative, $slide, 'thumb');

            $normalizedSlides[] = [
                'index' => $index,
                'path' => $imageKey,
                'thumb' => $thumbKey,
            ];
            $expectedIndex++;
        }

        $slideCount = (int) ($data['slide_count'] ?? count($normalizedSlides));
        if ($slideCount !== count($normalizedSlides)) {
            return null;
        }

        return [
            'version' => (int) ($data['version'] ?? $derivative->version ?? 1),
            'slide_count' => $slideCount,
            'width' => isset($data['width']) ? (int) $data['width'] : ($derivative->width ? (int) $derivative->width : null),
            'height' => isset($data['height']) ? (int) $data['height'] : ($derivative->height ? (int) $derivative->height : null),
            'format' => $this->normalizeImageFormat((string) ($data['format'] ?? 'png')),
            'engine' => isset($data['engine']) ? (string) $data['engine'] : null,
            'generated_at' => isset($data['generated_at']) ? (string) $data['generated_at'] : null,
            'slides' => $normalizedSlides,
        ];
    }

    /**
     * Client-facing manifest: authenticated Laravel URLs only — no original path or raw R2 keys.
     *
     * @return array<string, mixed>
     */
    public function sanitizedManifestPayload(
        CurriculumLibraryItem $item,
        string $kind,
        CurriculumLibraryMaterial|CurriculumLibraryItemFile $source,
        CurriculumPresentationDerivative $derivative,
        array $validatedManifest
    ): array {
        $slides = [];
        foreach ($validatedManifest['slides'] as $slide) {
            $index = (int) $slide['index'];
            $entry = [
                'index' => $index,
                'image_url' => $this->slideAssetRoute($item, $kind, $source, $index, 'image'),
            ];
            if (! empty($slide['thumb'])) {
                $entry['thumb_url'] = $this->slideAssetRoute($item, $kind, $source, $index, 'thumb');
            } else {
                $entry['thumb_url'] = null;
            }
            $slides[] = $entry;
        }

        return [
            'version' => (int) ($validatedManifest['version'] ?? $derivative->version ?? 1),
            'slide_count' => (int) $validatedManifest['slide_count'],
            'width' => $validatedManifest['width'] ?? null,
            'height' => $validatedManifest['height'] ?? null,
            'format' => $validatedManifest['format'] ?? 'png',
            'generated_at' => $validatedManifest['generated_at'] ?? null,
            'slides' => $slides,
            'player' => $this->defaultPlayerConfig(),
        ];
    }

    /**
     * Stream a known slide/thumb asset by 1-based index from the validated manifest.
     * Never accepts an arbitrary path from the request.
     */
    public function streamSlideAsset(
        CurriculumPresentationDerivative $derivative,
        array $validatedManifest,
        int $slideIndex,
        string $assetKind
    ): StreamedResponse|BinaryFileResponse {
        if (! in_array($assetKind, ['image', 'thumb'], true)) {
            abort(404);
        }

        $slides = $validatedManifest['slides'] ?? [];
        $count = count($slides);
        if ($slideIndex < 1 || $slideIndex > $count) {
            abort(404);
        }

        $slide = $slides[$slideIndex - 1] ?? null;
        if (! is_array($slide) || (int) ($slide['index'] ?? 0) !== $slideIndex) {
            abort(404);
        }

        $key = $assetKind === 'thumb'
            ? ($slide['thumb'] ?? null)
            : ($slide['path'] ?? null);

        if (! is_string($key) || $key === '' || ! $this->isSafeDerivativeKey($derivative, $key)) {
            abort(404);
        }

        $diskName = $derivative->storage_disk ?: 'r2';
        $disk = Storage::disk($diskName);

        if (! $disk->exists($key)) {
            abort(404);
        }

        $format = $this->normalizeImageFormat((string) ($validatedManifest['format'] ?? 'png'));
        $mime = $format === 'jpeg' ? 'image/jpeg' : 'image/png';
        $ext = pathinfo($key, PATHINFO_EXTENSION);
        if ($ext !== '') {
            $mime = match (strtolower($ext)) {
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                default => 'image/png',
            };
        }

        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ];

        if ($diskName === 'public' || $diskName === 'local') {
            $fullPath = $disk->path($key);
            if (! is_file($fullPath)) {
                abort(404);
            }

            return response()->file($fullPath, $headers);
        }

        return response()->stream(function () use ($disk, $key) {
            $stream = $disk->readStream($key);
            if ($stream === false) {
                return;
            }
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }

    public function streamPresentation(CurriculumLibraryItem $item, string $kind, int $id): StreamedResponse|BinaryFileResponse
    {
        [$diskName, $path, $filename] = match ($kind) {
            'file' => $this->resolveItemFile($item, $id),
            'material' => $this->resolveMaterial($item, $id),
            default => abort(404),
        };

        $disk = Storage::disk($diskName);
        if (! $path || ! $disk->exists($path)) {
            abort(404);
        }

        $mime = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        $safeName = preg_replace('/[^\p{L}\p{N}\-_. ]/u', '_', $filename) ?: 'presentation.pptx';
        if (! str_ends_with(strtolower($safeName), '.pptx') && ! str_ends_with(strtolower($safeName), '.ppt')) {
            $safeName .= '.pptx';
        }

        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$safeName.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ];

        if ($diskName === 'public' || $diskName === 'local') {
            $fullPath = $disk->path($path);
            if (! is_file($fullPath)) {
                abort(404);
            }

            return response()->file($fullPath, $headers);
        }

        return response()->stream(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            if ($stream === false) {
                return;
            }
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultPlayerConfig(): array
    {
        return [
            'allow_autoplay' => true,
            'allow_laser' => true,
            'allow_download' => false,
            'rtl_chrome' => true,
            'transition' => 'fade',
            'autoplay_ms' => 0,
            'min_zoom' => 1,
            'max_zoom' => 3.5,
        ];
    }

    public function derivativesTableReady(): bool
    {
        try {
            return Schema::hasTable('curriculum_presentation_derivatives');
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Resolve storage key for a slide image or thumb from raw manifest entry.
     */
    protected function resolveSlideStorageKey(CurriculumPresentationDerivative $derivative, array $slide, string $kind): ?string
    {
        $raw = null;
        if ($kind === 'image') {
            $raw = $slide['path'] ?? $slide['image'] ?? null;
        } else {
            $raw = $slide['thumb'] ?? null;
            if ($raw === null || $raw === '') {
                return null;
            }
        }

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        // Absolute key under derivatives prefix.
        if (str_starts_with($raw, 'curriculum-library/derivatives/')) {
            return $this->isSafeDerivativeKey($derivative, $raw) ? $raw : null;
        }

        // Relative key (design-doc style): resolve against version prefix.
        $prefix = rtrim($derivative->derivativePrefix(), '/');
        $relative = ltrim(str_replace('\\', '/', $raw), '/');
        if ($relative === '' || str_contains($relative, '..')) {
            return null;
        }
        $full = $prefix.'/'.$relative;

        return $this->isSafeDerivativeKey($derivative, $full) ? $full : null;
    }

    protected function isSafeDerivativeKey(CurriculumPresentationDerivative $derivative, string $key): bool
    {
        $key = str_replace('\\', '/', $key);
        if ($key === '' || str_contains($key, '..') || str_starts_with($key, '/')) {
            return false;
        }

        $allowedPrefix = sprintf(
            'curriculum-library/derivatives/%s/%d/',
            $derivative->source_type,
            (int) $derivative->source_id
        );

        return str_starts_with($key, $allowedPrefix);
    }

    protected function normalizeImageFormat(string $format): string
    {
        $format = strtolower($format);

        return in_array($format, ['jpg', 'jpeg'], true) ? 'jpeg' : (in_array($format, ['webp', 'gif'], true) ? $format : 'png');
    }

    protected function slideAssetRoute(
        CurriculumLibraryItem $item,
        string $kind,
        CurriculumLibraryMaterial|CurriculumLibraryItemFile $source,
        int $index,
        string $assetKind
    ): string {
        if ($kind === 'file') {
            $name = $assetKind === 'thumb'
                ? 'curriculum-library.file.slides.thumb'
                : 'curriculum-library.file.slides.image';

            return route($name, [
                'item' => $item,
                'file' => $source,
                'slide' => $index,
            ]);
        }

        $name = $assetKind === 'thumb'
            ? 'curriculum-library.material.slides.thumb'
            : 'curriculum-library.material.slides.image';

        return route($name, [
            'item' => $item,
            'material' => $source,
            'slide' => $index,
        ]);
    }

    /**
     * @return array{0: string, 1: string, 2: string} [disk, path, filename]
     */
    private function resolveItemFile(CurriculumLibraryItem $item, int $fileId): array
    {
        $file = CurriculumLibraryItemFile::query()
            ->where('curriculum_library_item_id', $item->id)
            ->whereKey($fileId)
            ->firstOrFail();

        if ($file->file_type !== 'presentation') {
            abort(404);
        }

        $diskName = $file->storage_disk ?: 'public';
        $filename = $file->label ?: basename((string) $file->path);

        return [$diskName, (string) $file->path, $filename];
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    private function resolveMaterial(CurriculumLibraryItem $item, int $materialId): array
    {
        $material = CurriculumLibraryMaterial::query()
            ->with('section')
            ->whereKey($materialId)
            ->firstOrFail();

        if ($material->file_kind !== 'pptx') {
            abort(404);
        }

        $material->loadMissing('section');
        if (! $material->section || (int) $material->section->curriculum_library_item_id !== (int) $item->id) {
            abort(404);
        }

        $diskName = $material->storage_disk ?: 'r2';

        return [$diskName, (string) $material->path, $material->displayTitle()];
    }
}
