<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmCommission extends Model
{
    public const TYPE_MARKETING = 'marketing';

    public const TYPE_SALES = 'sales';

    public const TYPE_TEAM_LEADER = 'team_leader';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'sales_lead_id',
        'order_id',
        'user_id',
        'type',
        'base_amount_egp',
        'commission_percent',
        'commission_amount_egp',
        'status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'base_amount_egp' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_amount_egp' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function typeLabels(): array
    {
        return [
            self::TYPE_MARKETING => 'تسويق',
            self::TYPE_SALES => 'مبيعات',
            self::TYPE_TEAM_LEADER => 'قائد فريق',
        ];
    }

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }
}
