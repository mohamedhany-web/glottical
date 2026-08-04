<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * محادثة/تذكرة تواصل بين طالب ومعلم ضمن نظام كورسات بريفيت (مرئية للإدارة).
 */
class PrivateLessonThread extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_PENDING_ADMIN = 'pending_admin';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'student_id',
        'instructor_id',
        'student_instructor_assignment_id',
        'advanced_course_id',
        'tutoring_group_id',
        'subject',
        'status',
        'admin_visible',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'admin_visible' => 'boolean',
            'last_message_at' => 'datetime',
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

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(StudentInstructorAssignment::class, 'student_instructor_assignment_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function tutoringGroup(): BelongsTo
    {
        return $this->belongsTo(TutoringGroup::class, 'tutoring_group_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PrivateLessonMessage::class)->orderBy('created_at');
    }
}
