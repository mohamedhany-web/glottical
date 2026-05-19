<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrCandidate extends Model
{
    public const SOURCE_WEBSITE = 'website';

    public const SOURCE_REFERRAL = 'referral';

    public const SOURCE_LINKEDIN = 'linkedin';

    public const SOURCE_JOB_BOARD = 'job_board';

    public const SOURCE_OTHER = 'other';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'cv_path',
        'portfolio_url',
        'source',
        'notes',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(HrJobApplication::class, 'hr_candidate_id');
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_WEBSITE => 'الموقع',
            self::SOURCE_REFERRAL => 'إحالة',
            self::SOURCE_LINKEDIN => 'لينكدإن',
            self::SOURCE_JOB_BOARD => 'موقع وظائف',
            self::SOURCE_OTHER => 'أخرى',
            default => $this->source,
        };
    }

    public function cvUrl(): ?string
    {
        if (! $this->cv_path) {
            return null;
        }

        return storage_asset($this->cv_path);
    }

    /** @return array<string, string> */
    public static function sourceLabels(): array
    {
        return [
            self::SOURCE_WEBSITE => 'الموقع',
            self::SOURCE_REFERRAL => 'إحالة',
            self::SOURCE_LINKEDIN => 'لينكدإن',
            self::SOURCE_JOB_BOARD => 'موقع وظائف',
            self::SOURCE_OTHER => 'أخرى',
        ];
    }
}
