<?php

namespace App\Models;

use App\Services\CurriculumPresentationConversionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CurriculumLibraryMaterial extends Model
{
    protected $fillable = [
        'curriculum_library_section_id',
        'title',
        'path',
        'storage_disk',
        'original_name',
        'file_kind',
        'view_in_platform',
        'allow_download',
        'order',
        'is_active',
        'animation_video_path',
        'animation_video_disk',
        'animation_video_original_name',
        'animation_video_mime',
        'animation_video_size',
        'animation_video_uploaded_at',
    ];

    protected $casts = [
        'view_in_platform' => 'boolean',
        'allow_download' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
        'animation_video_size' => 'integer',
        'animation_video_uploaded_at' => 'datetime',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CurriculumLibrarySection::class, 'curriculum_library_section_id');
    }

    public function presentationDerivative(): HasOne
    {
        return $this->hasOne(CurriculumPresentationDerivative::class, 'source_id')
            ->where('source_type', CurriculumPresentationDerivative::SOURCE_MATERIAL);
    }

    /** يفرض منع تحميل HTML وعروض PPTX حتى لو خُيّر خطأ في لوحة التحكم */
    public function effectiveAllowDownload(): bool
    {
        if (in_array($this->file_kind, ['html', 'pptx'], true)) {
            return false;
        }

        return $this->allow_download;
    }

    public function effectiveAllowViewInPlatform(): bool
    {
        return $this->view_in_platform;
    }

    public function displayTitle(): string
    {
        return $this->title ?: $this->original_name ?: basename($this->path);
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->path) {
            return null;
        }
        $disk = $this->storage_disk ?: 'r2';

        try {
            if ($disk === 'r2') {
                return Storage::disk('r2')->temporaryUrl($this->path, now()->addHours(2));
            }

            return Storage::disk($disk)->url($this->path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function fileKindFromExtension(string $ext): string
    {
        $ext = strtolower($ext);

        return match ($ext) {
            'html', 'htm' => 'html',
            'pdf' => 'pdf',
            'ppt', 'pptx' => 'pptx',
            default => 'other',
        };
    }

    public function hasReadyPresentationDerivative(): bool
    {
        if (! $this->derivativesTableExists()) {
            return false;
        }

        $derivative = $this->relationLoaded('presentationDerivative')
            ? $this->presentationDerivative
            : $this->presentationDerivative()->first();

        return $derivative !== null && $derivative->isReady();
    }

    /**
     * Delete derived slide artifacts only (never the original path object).
     */
    public function deletePresentationDerivatives(): void
    {
        if (! $this->derivativesTableExists()) {
            return;
        }

        try {
            app(CurriculumPresentationConversionService::class)->deleteDerivativesForMaterial($this);
        } catch (Throwable) {
            // Best-effort cleanup; original file deletion is handled separately.
        }
    }

    /**
     * Optional companion MP4/WebM that preserves PowerPoint animations (sidecar only).
     */
    public function hasAnimationVideo(): bool
    {
        if (! $this->animationVideoColumnsReady()) {
            return false;
        }

        return filled($this->animation_video_path);
    }

    /**
     * Delete animation video sidecar only — never the original PPT/PPTX path object.
     */
    public function deleteAnimationVideoFromStorage(): void
    {
        if (! $this->animationVideoColumnsReady() || ! $this->animation_video_path) {
            return;
        }

        $disk = $this->animation_video_disk ?: 'r2';
        $path = $this->animation_video_path;

        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        } catch (Throwable) {
            // Best-effort sidecar cleanup.
        }
    }

    /**
     * Clear animation video DB columns without touching the original source path.
     */
    public function clearAnimationVideoAttributes(): void
    {
        if (! $this->animationVideoColumnsReady()) {
            return;
        }

        $this->forceFill([
            'animation_video_path' => null,
            'animation_video_disk' => null,
            'animation_video_original_name' => null,
            'animation_video_mime' => null,
            'animation_video_size' => null,
            'animation_video_uploaded_at' => null,
        ])->save();
    }

    /**
     * Explicit material deletion: remove derivatives + animation sidecar, then original, then the row.
     */
    public function deleteWithStorage(): void
    {
        $this->deletePresentationDerivatives();
        $this->deleteAnimationVideoFromStorage();

        $disk = $this->storage_disk ?: 'r2';
        if ($this->path) {
            try {
                if (Storage::disk($disk)->exists($this->path)) {
                    Storage::disk($disk)->delete($this->path);
                }
            } catch (Throwable) {
            }
        }

        $this->delete();
    }

    protected function derivativesTableExists(): bool
    {
        try {
            return Schema::hasTable('curriculum_presentation_derivatives');
        } catch (Throwable) {
            return false;
        }
    }

    protected function animationVideoColumnsReady(): bool
    {
        try {
            return Schema::hasColumn($this->getTable(), 'animation_video_path');
        } catch (Throwable) {
            return false;
        }
    }
}
