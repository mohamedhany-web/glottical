<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TutoringGroupCohort extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_FULL = 'full';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_POSTPONED = 'postponed';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'tutoring_group_id',
        'title',
        'slug',
        'starts_at',
        'study_days',
        'study_time',
        'timezone',
        'capacity',
        'enrolled_count',
        'min_enrollment',
        'status',
        'postponed_to',
        'whatsapp_group_url',
        'enrollment_closes_at',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'postponed_to' => 'datetime',
            'enrollment_closes_at' => 'datetime',
            'study_days' => 'array',
            'capacity' => 'integer',
            'enrolled_count' => 'integer',
            'min_enrollment' => 'integer',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function tutoringGroup(): BelongsTo
    {
        return $this->belongsTo(TutoringGroup::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TutoringGroupBooking::class, 'cohort_id');
    }

    public function seatsLeft(): int
    {
        return max(0, (int) $this->capacity - (int) $this->enrolled_count);
    }

    public function isEnrollmentOpen(): bool
    {
        if (! $this->is_visible) {
            return false;
        }

        if (! in_array($this->status, [self::STATUS_OPEN, self::STATUS_POSTPONED], true)) {
            return false;
        }

        if ($this->seatsLeft() <= 0) {
            return false;
        }

        if ($this->enrollment_closes_at && $this->enrollment_closes_at->isPast()) {
            return false;
        }

        return true;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'مفتوحة للاشتراك',
            self::STATUS_FULL => 'مكتملة العدد',
            self::STATUS_CLOSED => 'مغلقة',
            self::STATUS_POSTPONED => 'مؤجّلة',
            self::STATUS_COMPLETED => 'مكتملة',
            default => $this->status,
        };
    }

    public function studyDaysLabels(): array
    {
        $map = [
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            7 => 'الأحد',
        ];

        return collect($this->study_days ?? [])
            ->map(fn ($d) => $map[(int) $d] ?? (string) $d)
            ->values()
            ->all();
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public static function uniqueSlug(int $groupId, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'cohort-'.Str::random(6);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('tutoring_group_id', $groupId)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
