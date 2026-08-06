<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentServiceEntitlement extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'service_package_id',
        'order_id',
        'scope',
        'plan_type',
        'term_months',
        'weekly_group_sessions',
        'weekly_private_sessions',
        'includes_community',
        'includes_libraries',
        'tutoring_group_id',
        'academic_year_id',
        'academic_subject_id',
        'units_total',
        'units_used',
        'starts_at',
        'expires_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'units_total' => 'integer',
            'units_used' => 'integer',
            'term_months' => 'integer',
            'weekly_group_sessions' => 'integer',
            'weekly_private_sessions' => 'integer',
            'includes_community' => 'boolean',
            'includes_libraries' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function tutoringGroup(): BelongsTo
    {
        return $this->belongsTo(TutoringGroup::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TutoringGroupBooking::class, 'student_service_entitlement_id');
    }

    public function unitsLeft(): int
    {
        return max(0, (int) $this->units_total - (int) $this->units_used);
    }

    public function hasUnitsLeft(): bool
    {
        return $this->isActive() && $this->unitsLeft() > 0;
    }

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
