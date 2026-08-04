<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateLessonMessage extends Model
{
    public const ROLE_STUDENT = 'student';

    public const ROLE_INSTRUCTOR = 'instructor';

    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'private_lesson_thread_id',
        'sender_id',
        'sender_role',
        'body',
        'is_internal_note',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_internal_note' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(PrivateLessonThread::class, 'private_lesson_thread_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
