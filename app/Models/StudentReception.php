<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * استقبال الطالب على المنصة بعد التسكين/الشراء في كورسات بريفيت.
 */
class StudentReception extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_WELCOMED = 'welcomed';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'student_id',
        'instructor_id',
        'handled_by',
        'channel',
        'status',
        'source',
        'notes',
        'checklist',
        'welcomed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'welcomed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
