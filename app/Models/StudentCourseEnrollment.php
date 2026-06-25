<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'advanced_course_id',
        'enrolled_at',
        'activated_at',
        'activated_by',
        'status',
        'progress',
        'notes',
        'invoice_id',
        'payment_id',
        'payment_method',
        'enrollment_type',
        'access_type',
        'expires_at',
        'auto_renew',
        'final_price',
        'original_price',
        'discount_amount',
        'coupon_id',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'auto_renew' => 'boolean',
        'progress' => 'decimal:2',
    ];

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة مع المستخدم (alias للتوافق)
     */
    public function user(): BelongsTo
    {
        return $this->student();
    }

    /**
     * علاقة مع الكورس
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * علاقة مع المستخدم الذي فعل التسجيل
     */
    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function installmentAgreements()
    {
        return $this->hasMany(InstallmentAgreement::class, 'student_course_enrollment_id');
    }

    /**
     * تسجيلات نشطة ولم ينتهِ اشتراكها (إن وُجد تاريخ انتهاء).
     */
    public function scopeGrantingAccess($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * تحديد ما إذا كان التسجيل نشط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * تحديد ما إذا كان التسجيل مكتمل
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * الحصول على لون حالة التسجيل
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'active' => 'green',
            'completed' => 'blue',
            'suspended' => 'red',
            'expired' => 'rose',
            default => 'gray'
        };
    }

    /**
     * هل هذا التسجيل اشتراك شهري (وليس شراء لمرة واحدة)؟
     */
    public function isSubscriptionBased(): bool
    {
        if (in_array($this->access_type, ['subscription', 'limited'], true)
            || $this->enrollment_type === 'subscription') {
            return true;
        }

        return $this->relationLoaded('course')
            ? ($this->course?->isMonthlyBilling() ?? false)
            : AdvancedCourse::query()->whereKey($this->advanced_course_id)->value('billing_mode') === 'monthly';
    }

    public function subscriptionIsActive(): bool
    {
        return \App\Services\CourseSubscriptionService::enrollmentGrantsAccess($this);
    }

    public function subscriptionIsExpired(): bool
    {
        if (! $this->isSubscriptionBased()) {
            return false;
        }

        return $this->status === 'expired'
            || ($this->expires_at !== null && $this->expires_at->isPast());
    }

    public function subscriptionExpiringSoon(int $withinDays = 7): bool
    {
        if (! $this->subscriptionIsActive() || $this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isFuture()
            && $this->expires_at->lte(now()->addDays($withinDays));
    }

    public function daysUntilExpiry(): ?int
    {
        if ($this->expires_at === null) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false);
    }

    public function renewalCheckoutUrl(): ?string
    {
        if (! $this->advanced_course_id) {
            return null;
        }

        return route('public.course.checkout', $this->advanced_course_id);
    }

    /**
     * الحصول على نص حالة التسجيل
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'suspended' => 'معلق',
            'expired' => 'منتهٍ',
            default => 'غير معروف'
        };
    }
}
