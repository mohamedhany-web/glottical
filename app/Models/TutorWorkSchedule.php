<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorWorkSchedule extends Model
{
    public const APPLIES_INDIVIDUAL = 'individual';

    public const APPLIES_COLLECTIVE = 'collective';

    public const APPLIES_BOTH = 'both';

    protected $fillable = [
        'instructor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'applies_to',
        'is_active',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'slot_duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where(function ($q) use ($type) {
            $q->where('applies_to', self::APPLIES_BOTH)
                ->orWhere('applies_to', $type);
        });
    }

    public function startTimeString(): string
    {
        $t = $this->start_time;

        return is_string($t) ? substr($t, 0, 5) : $t->format('H:i');
    }

    public function endTimeString(): string
    {
        $t = $this->end_time;

        return is_string($t) ? substr($t, 0, 5) : $t->format('H:i');
    }
}
