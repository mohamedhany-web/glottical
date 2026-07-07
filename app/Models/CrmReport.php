<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmReport extends Model
{
    public const TYPE_WEEKLY = 'weekly';

    public const TYPE_MONTHLY = 'monthly';

    public const TYPE_AD_HOC = 'ad_hoc';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_REVIEWED = 'reviewed';

    protected $fillable = [
        'user_id',
        'crm_group_id',
        'type',
        'period_start',
        'period_end',
        'title',
        'summary',
        'file_path',
        'file_name',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public static function typeLabels(): array
    {
        return [
            self::TYPE_WEEKLY => 'أسبوعي',
            self::TYPE_MONTHLY => 'شهري',
            self::TYPE_AD_HOC => 'خاص',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_SUBMITTED => 'مُرسل للإدارة',
            self::STATUS_REVIEWED => 'تمت المراجعة',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CrmGroup::class, 'crm_group_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }
}
