<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LibraryVideo extends Model
{
    public const AUDIENCE_GENERAL = 'general';

    public const AUDIENCE_TEACHER_STUDENTS = 'teacher_students';

    protected $fillable = [
        'library_folder_id',
        'created_by',
        'audience',
        'instructor_id',
        'content_theme',
        'title',
        'series_title',
        'age_label',
        'description',
        'external_url',
        'file_path',
        'storage_disk',
        'file_size',
        'duration_seconds',
        'mime_type',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'duration_seconds' => 'integer',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(LibraryFolder::class, 'library_folder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function scopeGeneral($query)
    {
        return $query->where(function ($q) {
            $q->where('audience', self::AUDIENCE_GENERAL)
                ->orWhereNull('audience');
        });
    }

    public function scopeForTeacherStudents($query, int $instructorId)
    {
        return $query->where('audience', self::AUDIENCE_TEACHER_STUDENTS)
            ->where('instructor_id', $instructorId);
    }

    public function isTeacherPrivate(): bool
    {
        return ($this->audience ?: self::AUDIENCE_GENERAL) === self::AUDIENCE_TEACHER_STUDENTS;
    }

    public function hasPlayableSource(): bool
    {
        return filled($this->external_url) || filled($this->file_path);
    }

    public function getUrl(): ?string
    {
        if (filled($this->external_url)) {
            return trim((string) $this->external_url);
        }

        if (! $this->file_path) {
            return null;
        }

        $diskName = $this->storage_disk ?: 'r2';

        try {
            $disk = Storage::disk($diskName);
            if (method_exists($disk, 'temporaryUrl') || $diskName === 'r2' || $diskName === 'live_recordings_r2' || $diskName === 's3') {
                return $disk->temporaryUrl($this->file_path, now()->addHours(2));
            }
        } catch (\Throwable $e) {
            // fall through
        }

        try {
            if ($diskName === 'public') {
                return Storage::disk('public')->url($this->file_path);
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    public function sourceLabel(): string
    {
        if (filled($this->external_url)) {
            return 'رابط';
        }
        if (filled($this->file_path)) {
            return 'Cloudflare';
        }

        return '—';
    }

    public function audienceLabel(): string
    {
        return $this->isTeacherPrivate()
            ? 'طلاب المعلم فقط'
            : 'عام (أكاديمية)';
    }

    public function themeLabel(?string $locale = null): string
    {
        return \App\Support\FamilyLibraryThemes::label(
            $this->content_theme ?: \App\Support\FamilyLibraryThemes::GENERAL,
            $locale ?: app()->getLocale()
        );
    }

    public function scopeOfTheme($query, string $theme)
    {
        return $query->where('content_theme', $theme);
    }

    public function getFileSizeForHumansAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) {
            return '—';
        }
        if ($bytes < 1024) {
            return "{$bytes} B";
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }
        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1).' MB';
        }

        return round($bytes / 1073741824, 2).' GB';
    }

    public function getDurationForHumansAttribute(): ?string
    {
        $s = (int) $this->duration_seconds;
        if ($s <= 0) {
            return null;
        }
        if ($s < 60) {
            return "{$s} ث";
        }
        $m = intdiv($s, 60);
        if ($m < 60) {
            return sprintf('%d:%02d', $m, $s % 60);
        }
        $h = intdiv($m, 60);

        return sprintf('%d:%02d:%02d', $h, $m % 60, $s % 60);
    }
}
