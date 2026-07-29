<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentTutoringSubscription extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'tutoring_group_id',
        'tutoring_group_package_id',
        'sessions_total',
        'sessions_used',
        'starts_at',
        'expires_at',
        'status',
        'order_id',
    ];

    protected function casts(): array
    {
        return [
            'sessions_total' => 'integer',
            'sessions_used' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tutoringGroup(): BelongsTo
    {
        return $this->belongsTo(TutoringGroup::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupPackage::class, 'tutoring_group_package_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TutoringGroupBooking::class, 'student_tutoring_subscription_id');
    }

    public function sessionsLeft(): int
    {
        return max(0, (int) $this->sessions_total - (int) $this->sessions_used);
    }

    public function hasSessionsLeft(): bool
    {
        return $this->isActive() && $this->sessionsLeft() > 0;
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

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'نشط',
            self::STATUS_EXPIRED => 'منتهٍ',
            self::STATUS_CANCELLED => 'ملغي',
            default => $this->status,
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }
}
