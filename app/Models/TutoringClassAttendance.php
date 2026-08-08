<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutoringClassAttendance extends Model
{
    public const STATUS_PRESENT = 'present';

    public const STATUS_ABSENT = 'absent';

    public const STATUS_LATE = 'late';

    public const STATUS_EXCUSED = 'excused';

    protected $fillable = [
        'tutoring_class_session_id',
        'user_id',
        'status',
        'joined_at',
        'left_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TutoringClassSession::class, 'tutoring_class_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'حاضر',
            self::STATUS_ABSENT => 'غائب',
            self::STATUS_LATE => 'متأخر',
            self::STATUS_EXCUSED => 'بعذر',
            default => $this->status,
        };
    }
}
