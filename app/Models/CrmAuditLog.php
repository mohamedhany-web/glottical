<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'sales_lead_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public static function actionLabels(): array
    {
        return [
            'lead_created' => 'إنشاء Lead',
            'lead_updated' => 'تعديل Lead',
            'lead_assigned' => 'تعيين لسيلز',
            'lead_status_changed' => 'تغيير الحالة',
            'lead_note_added' => 'إضافة ملاحظة',
            'payment_confirmed' => 'تأكيد الدفع',
            'course_activated' => 'تفعيل الكورس',
            'commission_calculated' => 'احتساب عمولة',
            'commission_approved' => 'اعتماد عمولة',
            'lead_closed_won' => 'إغلاق ناجح',
            'lead_closed_lost' => 'إغلاق خاسر',
            'team_member_added' => 'إضافة عضو للفريق',
            'team_member_removed' => 'إزالة عضو من الفريق',
            'crm_report_submitted' => 'رفع تقرير CRM',
            'crm_report_reviewed' => 'مراجعة تقرير CRM',
        ];
    }

    public function actionLabel(): string
    {
        return self::actionLabels()[$this->action] ?? $this->action;
    }
}
