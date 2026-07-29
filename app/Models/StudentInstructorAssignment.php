<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentInstructorAssignment extends Model
{
    public const SCOPE_GENERAL = 'general';

    public const SCOPE_COLLECTIVE = 'collective';

    public const SCOPE_INDIVIDUAL = 'individual';

    public const SCOPE_COURSES = 'courses';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_ENDED = 'ended';

    protected $fillable = [
        'student_id',
        'instructor_id',
        'academic_year_id',
        'scope',
        'status',
        'notes',
        'assigned_by',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopeLabel(): string
    {
        return match ($this->scope) {
            self::SCOPE_COLLECTIVE => 'مجموعات جماعية',
            self::SCOPE_INDIVIDUAL => 'مجموعات فردية',
            self::SCOPE_COURSES => 'كورسات',
            default => 'عام',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PAUSED => 'موقوف',
            self::STATUS_ENDED => 'منتهٍ',
            default => 'نشط',
        };
    }
}
