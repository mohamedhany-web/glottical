<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PrivateLessonMessage;
use App\Models\PrivateLessonThread;
use App\Models\StudentInstructorAssignment;
use App\Models\StudentReception;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * خدمات كورسات بريفيت: تواصل طالب↔معلم + استقبال + إشعار التسكين.
 */
class PrivateCoursesCoreService
{
    public static function ensureThread(
        int $studentId,
        int $instructorId,
        ?int $assignmentId = null,
        ?string $subject = null
    ): PrivateLessonThread {
        $thread = PrivateLessonThread::query()
            ->where('student_id', $studentId)
            ->where('instructor_id', $instructorId)
            ->whereIn('status', [PrivateLessonThread::STATUS_OPEN, PrivateLessonThread::STATUS_PENDING_ADMIN])
            ->first();

        if ($thread) {
            if ($assignmentId && ! $thread->student_instructor_assignment_id) {
                $thread->update(['student_instructor_assignment_id' => $assignmentId]);
            }

            return $thread;
        }

        return PrivateLessonThread::create([
            'student_id' => $studentId,
            'instructor_id' => $instructorId,
            'student_instructor_assignment_id' => $assignmentId,
            'subject' => $subject ?: 'تواصل كورسات بريفيت',
            'status' => PrivateLessonThread::STATUS_OPEN,
            'admin_visible' => true,
        ]);
    }

    public static function postMessage(
        PrivateLessonThread $thread,
        User $sender,
        string $body,
        bool $internalNote = false
    ): PrivateLessonMessage {
        $role = match (true) {
            $sender->isAdmin() => PrivateLessonMessage::ROLE_ADMIN,
            (int) $sender->id === (int) $thread->instructor_id => PrivateLessonMessage::ROLE_INSTRUCTOR,
            default => PrivateLessonMessage::ROLE_STUDENT,
        };

        return DB::transaction(function () use ($thread, $sender, $body, $internalNote, $role) {
            $message = PrivateLessonMessage::create([
                'private_lesson_thread_id' => $thread->id,
                'sender_id' => $sender->id,
                'sender_role' => $role,
                'body' => trim($body),
                'is_internal_note' => $internalNote,
            ]);

            $thread->update([
                'last_message_at' => now(),
                'status' => $internalNote
                    ? PrivateLessonThread::STATUS_PENDING_ADMIN
                    : PrivateLessonThread::STATUS_OPEN,
            ]);

            return $message;
        });
    }

    public static function ensureReception(int $studentId, ?int $instructorId = null, string $source = 'assignment'): StudentReception
    {
        $reception = StudentReception::query()->firstOrCreate(
            ['student_id' => $studentId],
            [
                'instructor_id' => $instructorId,
                'status' => StudentReception::STATUS_PENDING,
                'source' => $source,
                'channel' => 'platform',
                'checklist' => [
                    'welcome_message' => false,
                    'explain_private_lectures' => false,
                    'share_first_slot' => false,
                    'open_messaging' => false,
                ],
            ]
        );

        if ($instructorId && ! $reception->instructor_id) {
            $reception->update(['instructor_id' => $instructorId]);
        }

        return $reception;
    }

    public static function notifyAssignment(StudentInstructorAssignment $assignment): void
    {
        $student = $assignment->student;
        $instructor = $assignment->instructor;
        if (! $student || ! $instructor) {
            return;
        }

        if (! $assignment->instructor_notified_at) {
            Notification::create([
                'user_id' => $instructor->id,
                'title' => '🎉 New Student Assigned',
                'message' => $student->name.' has been assigned to you for private lessons. Review schedule, lesson count, and parent notes.',
                'type' => 'assignment',
                'target_type' => StudentInstructorAssignment::class,
                'target_id' => $assignment->id,
                'is_read' => false,
                'priority' => 'high',
            ]);
            $assignment->instructor_notified_at = now();
        }

        if (! $assignment->student_notified_at) {
            Notification::create([
                'user_id' => $student->id,
                'title' => 'Your private teacher is ready',
                'message' => 'Teacher «'.$instructor->name.'» has been assigned for your Private Lessons. You can message them and join upcoming sessions.',
                'type' => 'assignment',
                'target_type' => StudentInstructorAssignment::class,
                'target_id' => $assignment->id,
                'is_read' => false,
                'priority' => 'high',
            ]);
            $assignment->student_notified_at = now();
        }

        $assignment->save();

        self::ensureThread($student->id, $instructor->id, $assignment->id);
        self::ensureReception($student->id, $instructor->id, 'assignment');
    }
}
