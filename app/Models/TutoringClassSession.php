<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutoringClassSession extends Model
{
    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_LIVE = 'live';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'tutoring_group_cohort_id',
        'tutoring_group_id',
        'session_number',
        'title',
        'starts_at',
        'ends_at',
        'status',
        'classroom_meeting_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'session_number' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupCohort::class, 'tutoring_group_cohort_id');
    }

    public function tutoringGroup(): BelongsTo
    {
        return $this->belongsTo(TutoringGroup::class, 'tutoring_group_id');
    }

    public function classroomMeeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TutoringClassAttendance::class, 'tutoring_class_session_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => 'مجدولة',
            self::STATUS_LIVE => 'مباشرة الآن',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغاة',
            default => $this->status,
        };
    }

    public function isJoinable(): bool
    {
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED], true)) {
            return false;
        }

        if (! $this->starts_at) {
            return false;
        }

        // يُسمح بالانضمام قبل 30 دقيقة وحتى نهاية الحصة
        $openFrom = $this->starts_at->copy()->subMinutes(30);
        $openUntil = $this->ends_at ?: $this->starts_at->copy()->addHours(2);

        return now()->between($openFrom, $openUntil) || $this->status === self::STATUS_LIVE;
    }

    public function joinUrl(): ?string
    {
        $meeting = $this->classroomMeeting;
        if (! $meeting?->code) {
            return null;
        }

        return \App\Services\ClassroomMeetingAccessService::platformEnterUrl($meeting);
    }

    public function displayTitle(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return 'الحصة '.$this->session_number;
    }
}
