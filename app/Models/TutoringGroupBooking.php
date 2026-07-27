<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutoringGroupBooking extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'tutoring_group_id',
        'instructor_id',
        'user_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'starts_at',
        'ends_at',
        'status',
        'admin_notes',
        'student_notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function tutoringGroup(): BelongsTo
    {
        return $this->belongsTo(TutoringGroup::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBlocking(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_CONFIRMED => 'مؤكد',
            self::STATUS_CANCELLED => 'ملغي',
            self::STATUS_COMPLETED => 'مكتمل',
            default => $this->status,
        };
    }

    public function contactName(): string
    {
        return $this->user?->name
            ?: ($this->guest_name ?: '—');
    }

    public function contactPhone(): ?string
    {
        return $this->guest_phone ?: ($this->user?->phone ?? null);
    }

    public function contactEmail(): ?string
    {
        return $this->guest_email ?: ($this->user?->email ?? null);
    }
}
