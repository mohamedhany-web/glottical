<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutoringCohortEnrollment extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'tutoring_group_cohort_id',
        'user_id',
        'status',
        'enrolled_at',
        'order_id',
        'student_service_entitlement_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
        ];
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupCohort::class, 'tutoring_group_cohort_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function entitlement(): BelongsTo
    {
        return $this->belongsTo(StudentServiceEntitlement::class, 'student_service_entitlement_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'نشط',
            self::STATUS_CANCELLED => 'ملغى',
            self::STATUS_COMPLETED => 'مكتمل',
            default => $this->status,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
