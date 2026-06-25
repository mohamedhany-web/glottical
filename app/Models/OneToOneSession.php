<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class OneToOneSession extends Model
{
    public const STATUS_PENDING = 'pending_schedule';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'student_course_enrollment_id',
        'advanced_course_id',
        'instructor_id',
        'student_id',
        'session_number',
        'scheduled_at',
        'duration_minutes',
        'status',
        'classroom_meeting_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'session_number' => 'integer',
            'scheduled_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'بانتظار الجدولة',
            self::STATUS_SCHEDULED => 'مجدولة',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغاة',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentCourseEnrollment::class, 'student_course_enrollment_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classroomMeeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function joinUrl(): ?string
    {
        $meeting = $this->classroomMeeting;

        return $meeting ? url('classroom/join/'.$meeting->code) : null;
    }

    /**
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

        return $q->with(['instructor', 'student', 'course', 'classroomMeeting'])->get()->map(function (self $session) use ($perspective) {
            $end = $session->scheduled_at->copy()->addMinutes($session->duration_minutes ?? 60);
            $isStudent = $perspective === 'student';
            $courseTitle = $session->course->title ?? 'كورس فردي';

            $title = $isStudent
                ? ('حصة 1:1: '.$courseTitle.' — '.($session->instructor->name ?? ''))
                : ('حصة 1:1: '.$courseTitle.' — '.($session->student->name ?? ''));

            return (object) [
                'calendar_id' => 'one_to_one_'.$session->id,
                'id' => $session->id,
                'title' => $title,
                'description' => $session->joinUrl(),
                'start_date' => $session->scheduled_at,
                'end_date' => $end,
                'is_all_day' => false,
                'type' => 'one_to_one',
                'color' => '#7c3aed',
                'priority' => 'high',
                'url' => $isStudent
                    ? route('student.one-to-one-sessions.show', $session)
                    : route('instructor.one-to-one-sessions.show', $session),
                'location' => $session->joinUrl(),
            ];
        });
    }
}
