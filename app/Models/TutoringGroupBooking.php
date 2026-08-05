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

    public const PAYMENT_NONE = 'none';

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'tutoring_group_id',
        'cohort_id',
        'tutoring_group_package_id',
        'student_tutoring_subscription_id',
        'student_service_entitlement_id',
        'classroom_meeting_id',
        'order_id',
        'payment_status',
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

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupCohort::class, 'cohort_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupPackage::class, 'tutoring_group_package_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(StudentTutoringSubscription::class, 'student_tutoring_subscription_id');
    }

    public function entitlement(): BelongsTo
    {
        return $this->belongsTo(StudentServiceEntitlement::class, 'student_service_entitlement_id');
    }

    public function classroomMeeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
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

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_NONE => 'بدون دفع',
            self::PAYMENT_PENDING => 'بانتظار الدفع',
            self::PAYMENT_PAID => 'مدفوع',
            self::PAYMENT_REFUNDED => 'مسترد',
            default => $this->payment_status ?? '—',
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

    public function joinUrl(): ?string
    {
        if (! $this->classroomMeeting) {
            return null;
        }

        return url('classroom/join/'.$this->classroomMeeting->code);
    }
}
