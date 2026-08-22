<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ConsultationRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAYMENT_REPORTED = 'payment_reported';
    /** دُفع من المحفظة — بانتظار مراجعة الإدارة وقبول الطلب */
    public const STATUS_AWAITING_VERIFICATION = 'awaiting_verification';
    public const STATUS_PAID = 'paid';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'instructor_id',
        'student_id',
        'price_amount',
        'duration_minutes',
        'student_message',
        'payment_reference',
        'status',
        'payment_reported_at',
        'paid_confirmed_at',
        'paid_confirmed_by',
        'scheduled_at',
        'admin_notes',
        'classroom_meeting_id',
        'wallet_transaction_id',
        'platform_wallet_id',
        'payment_method',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'price_amount' => 'decimal:2',
            'duration_minutes' => 'integer',
            'payment_reported_at' => 'datetime',
            'paid_confirmed_at' => 'datetime',
            'scheduled_at' => 'datetime',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'بانتظار الدفع',
            self::STATUS_PAYMENT_REPORTED => 'أبلغ الطالب عن التحويل',
            self::STATUS_AWAITING_VERIFICATION => 'دفع من محفظة رصيد الطالب — بانتظار مراجعة الإدارة',
            self::STATUS_PAID => 'تم قبول الطلب والدفع',
            self::STATUS_SCHEDULED => 'مجدولة',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغاة',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function paidConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_confirmed_by');
    }

    public function classroomMeeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id');
    }

    /** محفظة المنصة (حساب فودافون كاش / إنستا باي / بنك) التي حوّل إليها الطالب */
    public function platformWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'platform_wallet_id');
    }

    /** دفع قديم: خصم من محفظة رصيد الطالب */
    public function paidViaWallet(): bool
    {
        return $this->wallet_transaction_id !== null;
    }

    /** دفع عبر حسابات المنصة + إيصال */
    public function paidViaPlatformAccounts(): bool
    {
        return $this->wallet_transaction_id === null && $this->payment_proof !== null;
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED
            && $this->scheduled_at
            && $this->classroom_meeting_id;
    }

    /**
     * @param  \Carbon\Carbon|string|null  $startDate
     * @param  \Carbon\Carbon|string|null  $endDate
     * @param  'student'|'instructor'  $perspective
     */
    public static function calendarItemsForUser(User $user, $startDate, $endDate, string $perspective): Collection
    {
        $q = static::query()
            ->where('status', self::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at');

        if ($perspective === 'student') {
            $q->where('student_id', $user->id);
        } else {
            $q->where('instructor_id', $user->id);
        }

        if ($startDate) {
            $q->where('scheduled_at', '>=', $startDate);
        }
        if ($endDate) {
            $q->where('scheduled_at', '<=', $endDate);
        }

        return $q->with(['instructor', 'student', 'classroomMeeting'])->get()->map(function (self $cr) use ($perspective) {
            $end = $cr->scheduled_at->copy()->addMinutes($cr->duration_minutes ?? 30);
            $joinUrl = $cr->classroomMeeting
                ? \App\Services\ClassroomMeetingAccessService::platformEnterUrl($cr->classroomMeeting)
                : null;
            $isStudent = $perspective === 'student';

            $title = $isStudent
                ? ('استشارة مع: '.($cr->instructor->name ?? ''))
                : ('استشارة: '.($cr->student->name ?? ''));

            return (object) [
                'calendar_id' => 'consultation_'.$cr->id,
                'id' => $cr->id,
                'title' => $title,
                'description' => $joinUrl ? ('رابط الغرفة: '.$joinUrl) : null,
                'start_date' => $cr->scheduled_at,
                'end_date' => $end,
                'is_all_day' => false,
                'type' => 'consultation',
                'color' => '#059669',
                'priority' => 'high',
                'url' => $isStudent ? route('consultations.show', $cr) : route('instructor.consultations.show', $cr),
                'location' => $joinUrl,
            ];
        });
    }
}
