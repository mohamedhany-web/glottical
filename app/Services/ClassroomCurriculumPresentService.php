<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumLibraryPreviewOpen;
use App\Models\CurriculumPresentationDerivative;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ClassroomCurriculumPresentService
{
    public function __construct(
        protected CurriculumPresentationViewerService $viewer
    ) {}

    public static function cacheKey(ClassroomMeeting $meeting): string
    {
        return 'mx_classroom_curriculum_'.$meeting->id;
    }

    /**
     * Active presentation session for a meeting (no storage keys / original paths).
     *
     * @return array{
     *   session_id: string,
     *   item_id: int,
     *   material_id: int,
     *   title: string,
     *   current_slide: int,
     *   slide_count: int,
     *   started_at: int,
     *   updated_at: int
     * }|null
     */
    public function getSession(ClassroomMeeting $meeting): ?array
    {
        $payload = Cache::get(self::cacheKey($meeting));
        if (! is_array($payload)) {
            return null;
        }

        $sessionId = (string) ($payload['session_id'] ?? '');
        $itemId = (int) ($payload['item_id'] ?? 0);
        $materialId = (int) ($payload['material_id'] ?? 0);
        $slideCount = (int) ($payload['slide_count'] ?? 0);
        $current = (int) ($payload['current_slide'] ?? 1);

        if ($sessionId === '' || $itemId < 1 || $materialId < 1 || $slideCount < 1) {
            return null;
        }

        if ($current < 1) {
            $current = 1;
        }
        if ($current > $slideCount) {
            $current = $slideCount;
        }

        return [
            'session_id' => $sessionId,
            'item_id' => $itemId,
            'material_id' => $materialId,
            'title' => mb_substr((string) ($payload['title'] ?? 'عرض'), 0, 200),
            'current_slide' => $current,
            'slide_count' => $slideCount,
            'started_at' => (int) ($payload['started_at'] ?? now()->timestamp),
            'updated_at' => (int) ($payload['updated_at'] ?? now()->timestamp),
        ];
    }

    public function clearSession(ClassroomMeeting $meeting): void
    {
        Cache::forget(self::cacheKey($meeting));
    }

    /**
     * @return array{session_id: string, item_id: int, material_id: int, title: string, current_slide: int, slide_count: int, started_at: int, updated_at: int}
     */
    public function startPresent(
        ClassroomMeeting $meeting,
        User $host,
        int $itemId,
        int $materialId
    ): array {
        [$item, $material, $derivative, $manifest] = $this->resolvePresentable($host, $itemId, $materialId);

        $session = [
            'session_id' => (string) Str::uuid(),
            'item_id' => (int) $item->id,
            'material_id' => (int) $material->id,
            'title' => mb_substr($material->displayTitle(), 0, 200),
            'current_slide' => 1,
            'slide_count' => (int) ($manifest['slide_count'] ?? $derivative->slide_count),
            'started_at' => now()->timestamp,
            'updated_at' => now()->timestamp,
        ];

        Cache::put(self::cacheKey($meeting), $session, $this->ttl($meeting));

        return $session;
    }

    /**
     * @return array{session_id: string, item_id: int, material_id: int, title: string, current_slide: int, slide_count: int, started_at: int, updated_at: int}|null
     */
    public function updateSlide(ClassroomMeeting $meeting, string $sessionId, int $slideIndex): ?array
    {
        $session = $this->getSession($meeting);
        if (! $session || $session['session_id'] !== $sessionId) {
            return null;
        }

        if ($slideIndex < 1 || $slideIndex > $session['slide_count']) {
            return null;
        }

        $session['current_slide'] = $slideIndex;
        $session['updated_at'] = now()->timestamp;
        Cache::put(self::cacheKey($meeting), $session, $this->ttl($meeting));

        return $session;
    }

    /**
     * Catalog of accessible active PPTX materials with ready valid derivatives, grouped by item.
     *
     * @return list<array{item_id: int, item_title: string, materials: list<array{id: int, title: string, slide_count: int, status: string, section_title: string, has_animation_video: bool}>}>
     */
    public function catalogForHost(User $host): array
    {
        if (! $this->viewer->derivativesTableReady()) {
            return [];
        }

        $hasLibrary = $host->hasCurriculumLibraryAccess();
        $previewItemId = null;
        if (! $hasLibrary) {
            try {
                $preview = CurriculumLibraryPreviewOpen::where('user_id', $host->id)->first();
                $previewItemId = $preview ? (int) $preview->curriculum_library_item_id : null;
            } catch (Throwable) {
                return [];
            }
            if (! $previewItemId) {
                return [];
            }
        }

        $itemsQuery = CurriculumLibraryItem::query()
            ->active()
            ->with(['category', 'sections.materials' => function ($q) {
                $q->where('is_active', true)
                    ->where('file_kind', 'pptx')
                    ->where('view_in_platform', true)
                    ->orderBy('order')
                    ->orderBy('id')
                    ->with('presentationDerivative');
            }])
            ->orderBy('order')
            ->orderBy('title');

        if (! $hasLibrary && $previewItemId) {
            $itemsQuery->whereKey($previewItemId);
        }

        $groups = [];
        foreach ($itemsQuery->get() as $item) {
            if (! $item->isAccessibleByViewer($host)) {
                continue;
            }

            $materials = [];
            foreach ($item->sections as $section) {
                foreach ($section->materials as $material) {
                    if (! $material->effectiveAllowViewInPlatform()) {
                        continue;
                    }
                    if (! $material->hasReadyPresentationDerivative()) {
                        continue;
                    }

                    $derivative = $material->presentationDerivative;
                    if (! $derivative || ! $derivative->isReady()) {
                        continue;
                    }

                    $manifest = $this->viewer->loadAndValidateManifest($derivative);
                    if (! $manifest) {
                        continue;
                    }

                    $materials[] = [
                        'id' => (int) $material->id,
                        'title' => $material->displayTitle(),
                        'slide_count' => (int) ($manifest['slide_count'] ?? $derivative->slide_count ?? 0),
                        'status' => 'ready',
                        'section_title' => (string) ($section->title ?? ''),
                        'has_animation_video' => $material->hasAnimationVideo(),
                    ];
                }
            }

            if ($materials === []) {
                continue;
            }

            $groups[] = [
                'item_id' => (int) $item->id,
                'item_title' => (string) $item->title,
                'materials' => $materials,
            ];
        }

        return $groups;
    }

    /**
     * Public state + sanitized meeting-scoped manifest. Never includes original path / R2 keys.
     *
     * @param  'host'|'guest'  $audience
     * @return array<string, mixed>|null
     */
    public function publicState(ClassroomMeeting $meeting, string $audience = 'host'): ?array
    {
        $session = $this->getSession($meeting);
        if (! $session) {
            return null;
        }

        $built = $this->buildSanitizedManifest($meeting, $session, $audience);
        if (! $built) {
            return null;
        }

        return [
            'active' => true,
            'session_id' => $session['session_id'],
            'item_id' => $session['item_id'],
            'material_id' => $session['material_id'],
            'title' => $session['title'],
            'current_slide' => $session['current_slide'],
            'slide_count' => $session['slide_count'],
            'started_at' => $session['started_at'],
            'updated_at' => $session['updated_at'],
            'manifest' => $built,
        ];
    }

    /**
     * Stream a slide/thumb for the active session only (bounded index via validated manifest).
     */
    public function streamSessionAsset(
        ClassroomMeeting $meeting,
        string $sessionId,
        int $slideIndex,
        string $assetKind
    ): StreamedResponse|BinaryFileResponse {
        $session = $this->getSession($meeting);
        if (! $session || $session['session_id'] !== $sessionId) {
            abort(404);
        }

        if (! in_array($assetKind, ['image', 'thumb'], true)) {
            abort(404);
        }

        if ($slideIndex < 1 || $slideIndex > $session['slide_count']) {
            abort(404);
        }

        $resolved = $this->resolveSessionDerivative($session);
        if (! $resolved) {
            abort(404);
        }

        [$derivative, $manifest] = $resolved;

        return $this->viewer->streamSlideAsset($derivative, $manifest, $slideIndex, $assetKind);
    }

    /**
     * Host may present this material: access + ready validated derivative.
     *
     * @return array{0: CurriculumLibraryItem, 1: CurriculumLibraryMaterial, 2: CurriculumPresentationDerivative, 3: array<string, mixed>}
     */
    public function resolvePresentable(User $host, int $itemId, int $materialId): array
    {
        $item = CurriculumLibraryItem::query()->active()->with('category')->find($itemId);
        if (! $item) {
            abort(404, 'المنهج غير موجود.');
        }

        if (! $this->hostCanAccessItem($host, $item)) {
            abort(403, 'ليس لديك صلاحية عرض هذا المنهج.');
        }

        if (! $item->isAccessibleByViewer($host)) {
            abort(403, 'هذا المنهج غير متاح لحسابك.');
        }

        $material = CurriculumLibraryMaterial::query()
            ->with(['section', 'presentationDerivative'])
            ->whereKey($materialId)
            ->first();

        if (! $material || ! $material->is_active) {
            abort(404, 'المادة غير موجودة.');
        }

        $material->loadMissing('section');
        if (! $material->section || (int) $material->section->curriculum_library_item_id !== (int) $item->id) {
            abort(404, 'المادة لا تنتمي لهذا المنهج.');
        }

        if ($material->file_kind !== 'pptx') {
            abort(422, 'يُسمح بعروض PPTX المحوّلة فقط.');
        }

        if (! $material->effectiveAllowViewInPlatform()) {
            abort(422, 'عرض هذه المادة داخل المنصة غير مفعّل.');
        }

        $derivative = $this->viewer->resolveReadyDerivative(
            CurriculumPresentationDerivative::SOURCE_MATERIAL,
            (int) $material->id
        );
        if (! $derivative) {
            abort(422, 'العرض غير جاهز للبث داخل الاجتماع.');
        }

        $manifest = $this->viewer->loadAndValidateManifest($derivative);
        if (! $manifest) {
            abort(422, 'ملف الشرائح غير صالح.');
        }

        return [$item, $material, $derivative, $manifest];
    }

    public function hostCanAccessItem(User $host, CurriculumLibraryItem $item): bool
    {
        if ($host->hasCurriculumLibraryAccess()) {
            return true;
        }

        try {
            $preview = CurriculumLibraryPreviewOpen::where('user_id', $host->id)->first();

            return $preview && (int) $preview->curriculum_library_item_id === (int) $item->id;
        } catch (Throwable) {
            return false;
        }
    }

    public function assertMeetingLive(ClassroomMeeting $meeting): void
    {
        if (! $meeting->started_at || $meeting->ended_at) {
            abort(422, 'الاجتماع غير نشط.');
        }
    }

    /**
     * @param  array{session_id: string, item_id: int, material_id: int, title: string, current_slide: int, slide_count: int, started_at: int, updated_at: int}  $session
     * @param  'host'|'guest'  $audience
     * @return array<string, mixed>|null
     */
    protected function buildSanitizedManifest(ClassroomMeeting $meeting, array $session, string $audience): ?array
    {
        $resolved = $this->resolveSessionDerivative($session);
        if (! $resolved) {
            return null;
        }

        [$derivative, $manifest] = $resolved;
        $slides = [];
        foreach ($manifest['slides'] as $slide) {
            $index = (int) $slide['index'];
            $entry = [
                'index' => $index,
                'image_url' => $this->meetingAssetUrl($meeting, $session['session_id'], $index, 'image', $audience),
            ];
            if (! empty($slide['thumb'])) {
                $entry['thumb_url'] = $this->meetingAssetUrl($meeting, $session['session_id'], $index, 'thumb', $audience);
            } else {
                $entry['thumb_url'] = null;
            }
            $slides[] = $entry;
        }

        return [
            'version' => (int) ($manifest['version'] ?? $derivative->version ?? 1),
            'slide_count' => (int) $manifest['slide_count'],
            'width' => $manifest['width'] ?? null,
            'height' => $manifest['height'] ?? null,
            'format' => $manifest['format'] ?? 'png',
            'slides' => $slides,
        ];
    }

    /**
     * @param  array{session_id: string, item_id: int, material_id: int, title: string, current_slide: int, slide_count: int, started_at: int, updated_at: int}  $session
     * @return array{0: CurriculumPresentationDerivative, 1: array<string, mixed>}|null
     */
    protected function resolveSessionDerivative(array $session): ?array
    {
        $material = CurriculumLibraryMaterial::query()
            ->with('section')
            ->whereKey($session['material_id'])
            ->first();

        if (! $material || ! $material->is_active || $material->file_kind !== 'pptx') {
            return null;
        }

        $material->loadMissing('section');
        if (! $material->section || (int) $material->section->curriculum_library_item_id !== (int) $session['item_id']) {
            return null;
        }

        $derivative = $this->viewer->resolveReadyDerivative(
            CurriculumPresentationDerivative::SOURCE_MATERIAL,
            (int) $material->id
        );
        if (! $derivative) {
            return null;
        }

        $manifest = $this->viewer->loadAndValidateManifest($derivative);
        if (! $manifest) {
            return null;
        }

        if ((int) $manifest['slide_count'] !== (int) $session['slide_count']) {
            // Session slide_count is authoritative for bounds; still allow stream if index in both ranges.
        }

        return [$derivative, $manifest];
    }

    /**
     * @param  'host'|'guest'  $audience
     */
    protected function meetingAssetUrl(
        ClassroomMeeting $meeting,
        string $sessionId,
        int $index,
        string $assetKind,
        string $audience
    ): string {
        if ($audience === 'guest') {
            $name = $assetKind === 'thumb'
                ? 'classroom.join.curriculum.thumb'
                : 'classroom.join.curriculum.slide';

            return route($name, [
                'code' => $meeting->code,
                'sessionId' => $sessionId,
                'slide' => $index,
            ]);
        }

        $prefix = request()->routeIs('instructor.*') ? 'instructor.' : 'student.';
        $name = $assetKind === 'thumb'
            ? $prefix.'classroom.curriculum.thumb'
            : $prefix.'classroom.curriculum.slide';

        return route($name, [
            'meeting' => $meeting,
            'sessionId' => $sessionId,
            'slide' => $index,
        ]);
    }

    protected function ttl(ClassroomMeeting $meeting): \DateTimeInterface
    {
        $minutes = max(30, (int) ($meeting->planned_duration_minutes ?: 120));
        // Cap to meeting window + buffer, never longer than 12h.
        $minutes = min($minutes + 30, 12 * 60);

        return now()->addMinutes($minutes);
    }
}
