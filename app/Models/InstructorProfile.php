<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorProfile extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'photo_path',
        'experience',
        'skills',
        'social_links',
        'status',
        'rejection_reason',
        'reviewed_at',
        'reviewed_by',
        'submitted_at',
        'consultation_price_egp',
        'consultation_duration_minutes',
    ];

    protected $casts = [
        'social_links' => 'array',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'consultation_price_egp' => 'decimal:2',
        'consultation_duration_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    /**
     * سعر الاستشارة بالجنيه المصري: خاص بالمدرب إن وُجد، وإلا السعر الافتراضي من إعدادات المنصة.
     */
    public function effectiveConsultationPriceEgp(): float
    {
        if ($this->consultation_price_egp !== null) {
            return (float) $this->consultation_price_egp;
        }

        return (float) ConsultationSetting::current()->default_price;
    }

    /**
     * مدة الاستشارة بالدقائق: خاصة بالمدرب إن وُجدت، وإلا الافتراضي من الإعدادات.
     */
    public function effectiveConsultationDurationMinutes(): int
    {
        if ($this->consultation_duration_minutes !== null && (int) $this->consultation_duration_minutes > 0) {
            return (int) $this->consultation_duration_minutes;
        }

        return (int) ConsultationSetting::current()->default_duration_minutes;
    }

    public function usesCustomConsultationPrice(): bool
    {
        return $this->consultation_price_egp !== null;
    }

    /**
     * رابط صورة الملف التعريفي — باستخدام asset('storage/...') لضمان ظهور الصورة في كل الصفحات.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($this->photo_path));
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return storage_public_url($path);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_PENDING_REVIEW => 'قيد المراجعة',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            default => $status,
        };
    }

    /**
     * المهارات كقائمة مرتبة (سطر لكل مهارة أو مفصولة بفاصلة)
     */
    public function getSkillsListAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }
        $raw = preg_split('/[\r\n,،]+/u', $this->skills, -1, PREG_SPLIT_NO_EMPTY);
        $list = array_map('trim', $raw);
        return array_values(array_filter($list));
    }

    /**
     * الخبرات كقائمة (كل سطر = نقطة/فقرة للعرض المنظم)
     */
    public function getExperienceListAttribute(): array
    {
        if (empty($this->experience)) {
            return [];
        }
        $lines = preg_split('/\r\n|\r|\n/', $this->experience, -1, PREG_SPLIT_NO_EMPTY);
        $list = array_map('trim', $lines);
        return array_values(array_filter($list));
    }
}
