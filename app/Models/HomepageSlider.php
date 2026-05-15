<?php

namespace App\Models;

use App\Services\HomepageSliderImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class HomepageSlider extends Model
{
    public const SOURCE_COURSE = 'course';

    public const SOURCE_PATH = 'path';

    public const SOURCE_CUSTOM = 'custom';

    protected $fillable = [
        'source_type',
        'advanced_course_id',
        'academic_year_id',
        'kicker',
        'title',
        'subtitle',
        'image_path',
        'primary_label',
        'primary_url',
        'secondary_label',
        'secondary_url',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function scopeActiveNow($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function publicImageUrl(): ?string
    {
        return HomepageSliderImageStorage::publicUrl($this->image_path);
    }

    public function sourceTypeLabel(): string
    {
        return match ($this->source_type) {
            self::SOURCE_COURSE => 'كورس',
            self::SOURCE_PATH => 'مسار تعليمي',
            default => 'مخصص',
        };
    }

    public function resolvedTitle(): string
    {
        if (filled($this->title)) {
            return (string) $this->title;
        }

        if ($this->source_type === self::SOURCE_COURSE && $this->course) {
            return (string) $this->course->title;
        }

        if ($this->source_type === self::SOURCE_PATH && $this->academicYear) {
            return (string) $this->academicYear->name;
        }

        return '';
    }

    public function resolvedSubtitle(): string
    {
        if (filled($this->subtitle)) {
            return (string) $this->subtitle;
        }

        if ($this->source_type === self::SOURCE_COURSE && $this->course) {
            return Str::limit(strip_tags((string) ($this->course->description ?? '')), 190);
        }

        if ($this->source_type === self::SOURCE_PATH && $this->academicYear) {
            return Str::limit(strip_tags((string) ($this->academicYear->description ?? '')), 190);
        }

        return '';
    }

    public function resolvedBackgroundUrl(): string
    {
        $custom = $this->publicImageUrl();
        if ($custom) {
            return $custom;
        }

        if ($this->source_type === self::SOURCE_COURSE && $this->course?->thumbnail) {
            return asset('storage/'.str_replace('\\', '/', $this->course->thumbnail));
        }

        if ($this->source_type === self::SOURCE_PATH && $this->academicYear?->thumbnail) {
            return asset('storage/'.str_replace('\\', '/', $this->academicYear->thumbnail));
        }

        return '';
    }

    public function toHeroSpotlightArray(int $index = 0): array
    {
        $a = 'landing.academy';
        $kicker = $this->kicker;
        if (! filled($kicker)) {
            $kicker = match ($this->source_type) {
                self::SOURCE_PATH => __($a.'.stream_badge_series'),
                self::SOURCE_COURSE => $index === 0
                    ? __($a.'.stream_badge_course')
                    : __($a.'.stream_badge_trending'),
                default => __($a.'.stream_badge_course'),
            };
        }

        $primaryUrl = $this->primary_url;
        $primaryLabel = $this->primary_label;
        $secondaryUrl = $this->secondary_url;
        $secondaryLabel = $this->secondary_label;

        if ($this->source_type === self::SOURCE_COURSE && $this->course) {
            $primaryUrl = $primaryUrl ?: route('public.course.show', $this->course->id);
            $primaryLabel = $primaryLabel ?: __($a.'.stream_primary_play');
            $secondaryUrl = $secondaryUrl ?: ($index === 0 ? url('/').'#stream-paths' : route('public.courses'));
            $secondaryLabel = $secondaryLabel ?: __($a.'.stream_explore_paths');
        } elseif ($this->source_type === self::SOURCE_PATH) {
            $primaryUrl = $primaryUrl ?: route('public.courses');
            $primaryLabel = $primaryLabel ?: __($a.'.stream_continue');
            $secondaryUrl = $secondaryUrl ?: route('register');
            $secondaryLabel = $secondaryLabel ?: __($a.'.stream_join');
        } else {
            $primaryUrl = $primaryUrl ?: route('public.courses');
            $primaryLabel = $primaryLabel ?: __($a.'.stream_primary_play');
            $secondaryUrl = $secondaryUrl ?: route('register');
            $secondaryLabel = $secondaryLabel ?: __($a.'.stream_join');
        }

        $sub = $this->resolvedSubtitle();

        return [
            'kicker' => $kicker,
            'title' => $this->resolvedTitle(),
            'sub' => $sub !== '' ? $sub : __($a.'.stream_fallback_sub'),
            'bg' => $this->resolvedBackgroundUrl(),
            'primary_url' => $primaryUrl,
            'primary_label' => $primaryLabel,
            'secondary_url' => $secondaryUrl,
            'secondary_label' => $secondaryLabel,
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (HomepageSlider $row) {
            if ($row->image_path) {
                HomepageSliderImageStorage::delete($row->image_path);
            }
        });
    }
}
