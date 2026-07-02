<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesLead extends Model
{
    use SoftDeletes;

    public const STATUS_NEW = 'new_lead';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_INTERESTED = 'interested';

    public const STATUS_PLACEMENT_TEST = 'placement_test';

    public const STATUS_OFFER_SENT = 'offer_sent';

    public const STATUS_PAYMENT_PENDING = 'payment_pending';

    public const STATUS_PAYMENT_CONFIRMED = 'payment_confirmed';

    public const STATUS_ENROLLED = 'enrolled';

    public const STATUS_COURSE_ACTIVE = 'course_active';

    public const STATUS_RENEWAL = 'renewal';

    public const STATUS_CLOSED_WON = 'closed_won';

    public const STATUS_CLOSED_LOST = 'closed_lost';

    // Legacy aliases (for backward compat in raw queries)
    public const STATUS_QUALIFIED = self::STATUS_INTERESTED;

    public const STATUS_CONVERTED = self::STATUS_CLOSED_WON;

    public const STATUS_LOST = self::STATUS_CLOSED_LOST;

    public const SOURCE_WEBSITE = 'website';

    public const SOURCE_PHONE = 'phone';

    public const SOURCE_SOCIAL = 'social';

    public const SOURCE_REFERRAL = 'referral';

    public const SOURCE_EVENT = 'event';

    public const SOURCE_WALK_IN = 'walk_in';

    public const SOURCE_OTHER = 'other';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'source',
        'status',
        'notes',
        'interested_advanced_course_id',
        'assigned_to',
        'crm_group_id',
        'assigned_at',
        'created_by',
        'marketing_owner_id',
        'linked_user_id',
        'converted_order_id',
        'order_id',
        'enrollment_id',
        'converted_at',
        'lost_reason',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (SalesLead $lead) {
            if ($lead->isDirty('marketing_owner_id') && $lead->getOriginal('marketing_owner_id')) {
                $lead->marketing_owner_id = $lead->getOriginal('marketing_owner_id');
            }
        });
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function marketingOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketing_owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function linkedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function crmGroup(): BelongsTo
    {
        return $this->belongsTo(CrmGroup::class, 'crm_group_id');
    }

    public function interestedCourse(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'interested_advanced_course_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(CrmCommission::class, 'sales_lead_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(CrmAuditLog::class, 'sales_lead_id')->orderByDesc('created_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getSourceLabelAttribute(): string
    {
        return self::sourceLabels()[$this->source] ?? $this->source;
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [self::STATUS_CLOSED_WON, self::STATUS_CLOSED_LOST], true);
    }

    public function isConverted(): bool
    {
        return $this->status === self::STATUS_CLOSED_WON;
    }

    public function isLost(): bool
    {
        return $this->status === self::STATUS_CLOSED_LOST;
    }

    public function isAssignedToSales(): bool
    {
        return $this->assigned_to !== null && $this->status !== self::STATUS_NEW;
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CLOSED_WON, self::STATUS_CLOSED_LOST]);
    }

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_NEW => 'Lead جديد',
            self::STATUS_ASSIGNED => 'مُعيَّن لسيلز',
            self::STATUS_CONTACTED => 'تم التواصل',
            self::STATUS_INTERESTED => 'مهتم',
            self::STATUS_PLACEMENT_TEST => 'اختبار تحديد مستوى',
            self::STATUS_OFFER_SENT => 'عرض مُرسل',
            self::STATUS_PAYMENT_PENDING => 'بانتظار الدفع',
            self::STATUS_PAYMENT_CONFIRMED => 'دفع مؤكد',
            self::STATUS_ENROLLED => 'مسجّل',
            self::STATUS_COURSE_ACTIVE => 'كورس نشط',
            self::STATUS_RENEWAL => 'تجديد',
            self::STATUS_CLOSED_WON => 'مغلق — ناجح',
            self::STATUS_CLOSED_LOST => 'مغلق — خاسر',
        ];
    }

    /** @return array<string, string> */
    public static function sourceLabels(): array
    {
        return [
            self::SOURCE_WEBSITE => 'الموقع',
            self::SOURCE_PHONE => 'هاتف',
            self::SOURCE_SOCIAL => 'سوشيال',
            self::SOURCE_REFERRAL => 'إحالة',
            self::SOURCE_EVENT => 'فعالية',
            self::SOURCE_WALK_IN => 'زيارة',
            self::SOURCE_OTHER => 'أخرى',
        ];
    }

    /** @return array<string, list<string>> */
    public static function allowedTransitions(): array
    {
        return [
            self::STATUS_NEW => [self::STATUS_ASSIGNED, self::STATUS_CLOSED_LOST],
            self::STATUS_ASSIGNED => [self::STATUS_CONTACTED, self::STATUS_CLOSED_LOST],
            self::STATUS_CONTACTED => [self::STATUS_INTERESTED, self::STATUS_CLOSED_LOST],
            self::STATUS_INTERESTED => [self::STATUS_PLACEMENT_TEST, self::STATUS_CLOSED_LOST],
            self::STATUS_PLACEMENT_TEST => [self::STATUS_OFFER_SENT, self::STATUS_CLOSED_LOST],
            self::STATUS_OFFER_SENT => [self::STATUS_PAYMENT_PENDING, self::STATUS_CLOSED_LOST],
            self::STATUS_PAYMENT_PENDING => [self::STATUS_PAYMENT_CONFIRMED, self::STATUS_CLOSED_LOST],
            self::STATUS_PAYMENT_CONFIRMED => [self::STATUS_ENROLLED],
            self::STATUS_ENROLLED => [self::STATUS_COURSE_ACTIVE],
            self::STATUS_COURSE_ACTIVE => [self::STATUS_RENEWAL, self::STATUS_CLOSED_WON],
            self::STATUS_RENEWAL => [self::STATUS_PAYMENT_PENDING, self::STATUS_CLOSED_WON, self::STATUS_CLOSED_LOST],
        ];
    }

    public function canTransitionTo(string $status): bool
    {
        $allowed = self::allowedTransitions()[$this->status] ?? [];

        return in_array($status, $allowed, true);
    }
}
