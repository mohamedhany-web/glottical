<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\OneToOneSession;
use App\Models\PrivateLessonMessage;
use App\Models\PrivateLessonThread;
use App\Models\StudentInstructorAssignment;
use App\Models\StudentReception;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * خدمات كورسات بريفيت: تواصل طالب↔معلم + استقبال + إشعار التسكين.
 */
class PrivateCoursesCoreService
{
    public static function threadsReady(): bool
    {
        return Schema::hasTable('private_lesson_threads')
            && Schema::hasTable('private_lesson_messages');
    }

    /**
     * معلمو الطالب من التسكين / الفصل / التعيين — لفتح المحادثة حتى بدون assignment قديم.
     *
     * @return array<int, int>
     */
    public static function instructorIdsForStudent(int $studentId): array
    {
        $ids = collect();

        if (Schema::hasTable('one_to_one_sessions')) {
            $ids = $ids->merge(
                OneToOneSession::query()
                    ->where('student_id', $studentId)
                    ->where('status', '!=', OneToOneSession::STATUS_CANCELLED)
                    ->pluck('instructor_id')
            );
        }

        if (Schema::hasTable('tutoring_cohort_enrollments')) {
            $ids = $ids->merge(
                TutoringCohortEnrollment::query()
                    ->where('user_id', $studentId)
                    ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
                    ->with('cohort.tutoringGroup')
                    ->get()
                    ->map(fn (TutoringCohortEnrollment $row) => $row->cohort?->tutoringGroup?->instructor_id)
            );
        }

        if (Schema::hasTable('student_instructor_assignments')) {
            $ids = $ids->merge(
                StudentInstructorAssignment::query()
                    ->where('student_id', $studentId)
                    ->where('status', StudentInstructorAssignment::STATUS_ACTIVE)
                    ->pluck('instructor_id')
            );
        }

        if (Schema::hasTable('tutoring_group_bookings')) {
            $ids = $ids->merge(
                TutoringGroupBooking::query()
                    ->where('user_id', $studentId)
                    ->whereIn('status', [
                        TutoringGroupBooking::STATUS_PENDING,
                        TutoringGroupBooking::STATUS_CONFIRMED,
                    ])
                    ->pluck('instructor_id')
            );
        }

        return $ids
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, int>
     */
    public static function studentIdsForInstructor(int $instructorId): array
    {
        $ids = collect();

        if (Schema::hasTable('one_to_one_sessions')) {
            $ids = $ids->merge(
                OneToOneSession::query()
                    ->where('instructor_id', $instructorId)
                    ->where('status', '!=', OneToOneSession::STATUS_CANCELLED)
                    ->pluck('student_id')
            );
        }

        if (Schema::hasTable('tutoring_groups') && Schema::hasTable('tutoring_cohort_enrollments')) {
            $groupIds = TutoringGroup::query()->where('instructor_id', $instructorId)->pluck('id');
            $cohortIds = \App\Models\TutoringGroupCohort::query()
                ->whereIn('tutoring_group_id', $groupIds)
                ->pluck('id');
            $ids = $ids->merge(
                TutoringCohortEnrollment::query()
                    ->whereIn('tutoring_group_cohort_id', $cohortIds)
                    ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
                    ->pluck('user_id')
            );
        }

        if (Schema::hasTable('student_instructor_assignments')) {
            $ids = $ids->merge(
                StudentInstructorAssignment::query()
                    ->where('instructor_id', $instructorId)
                    ->where('status', StudentInstructorAssignment::STATUS_ACTIVE)
                    ->pluck('student_id')
            );
        }

        if (Schema::hasTable('tutoring_group_bookings')) {
            $ids = $ids->merge(
                TutoringGroupBooking::query()
                    ->where('instructor_id', $instructorId)
                    ->whereIn('status', [
                        TutoringGroupBooking::STATUS_PENDING,
                        TutoringGroupBooking::STATUS_CONFIRMED,
                    ])
                    ->pluck('user_id')
            );
        }

        return $ids
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public static function studentCanMessageInstructor(int $studentId, int $instructorId): bool
    {
        return in_array($instructorId, self::instructorIdsForStudent($studentId), true);
    }

    public static function syncThreadsForStudent(int $studentId): void
    {
        if (! self::threadsReady() || $studentId < 1) {
            return;
        }

        foreach (self::instructorIdsForStudent($studentId) as $instructorId) {
            if ($instructorId === $studentId) {
                continue;
            }
            self::ensureThread($studentId, $instructorId, null, 'تواصل مع المعلم');
        }
    }

    public static function syncThreadsForInstructor(int $instructorId): void
    {
        if (! self::threadsReady() || $instructorId < 1) {
            return;
        }

        foreach (self::studentIdsForInstructor($instructorId) as $studentId) {
            if ($studentId === $instructorId) {
                continue;
            }
            self::ensureThread($studentId, $instructorId, null, 'تواصل مع المعلم');
        }
    }

    public static function ensureThread(
        int $studentId,
        int $instructorId,
        ?int $assignmentId = null,
        ?string $subject = null
    ): PrivateLessonThread {
        if (! self::threadsReady()) {
            throw new \InvalidArgumentException('محادثات المعلمين غير مفعّلة بعد.');
        }

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
            'subject' => $subject ?: 'تواصل مع المعلم',
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

        $message = DB::transaction(function () use ($thread, $sender, $body, $internalNote, $role) {
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

        if (! $internalNote) {
            self::notifyMessageRecipient($thread, $sender, $message);
        }

        return $message;
    }

    /**
     * إشعار الطرف الآخر (طالب↔معلم) — يظهر داخل المنصة + بريد إن لم يكن متصلاً (MAIL_NOTIFY_IN_APP).
     */
    public static function notifyMessageRecipient(
        PrivateLessonThread $thread,
        User $sender,
        PrivateLessonMessage $message
    ): void {
        $thread->loadMissing(['student:id,name,email', 'instructor:id,name,email']);

        $recipientId = null;
        $audience = null;
        $actionUrl = null;

        if ((int) $sender->id === (int) $thread->student_id) {
            $recipientId = (int) $thread->instructor_id;
            $audience = 'instructor';
            $actionUrl = self::messageActionUrl('instructor', $thread);
        } elseif ((int) $sender->id === (int) $thread->instructor_id) {
            $recipientId = (int) $thread->student_id;
            $audience = 'student';
            $actionUrl = self::messageActionUrl('student', $thread);
        } elseif ($sender->isAdmin()) {
            foreach ([
                [(int) $thread->student_id, 'student', self::messageActionUrl('student', $thread)],
                [(int) $thread->instructor_id, 'instructor', self::messageActionUrl('instructor', $thread)],
            ] as [$uid, $aud, $url]) {
                if ($uid > 0 && $uid !== (int) $sender->id) {
                    self::createMessageNotification($uid, $sender, $message, $aud, $url, $thread);
                }
            }

            return;
        }

        if (! $recipientId || $recipientId === (int) $sender->id) {
            return;
        }

        self::createMessageNotification($recipientId, $sender, $message, $audience, $actionUrl, $thread);
    }

    private static function messageActionUrl(string $audience, PrivateLessonThread $thread): string
    {
        if ($audience === 'instructor') {
            if (\Illuminate\Support\Facades\Route::has('instructor.private-messages.show')) {
                return route('instructor.private-messages.show', $thread);
            }

            return url('/instructor/private-messages/'.$thread->id);
        }

        if (\Illuminate\Support\Facades\Route::has('student.private-messages.show')) {
            return route('student.private-messages.show', $thread);
        }

        return url('/private-messages/'.$thread->id);
    }

    private static function createMessageNotification(
        int $recipientId,
        User $sender,
        PrivateLessonMessage $message,
        string $audience,
        string $actionUrl,
        PrivateLessonThread $thread
    ): void {
        $preview = \Illuminate\Support\Str::limit(trim((string) $message->body), 160);
        $isAr = app()->getLocale() === 'ar';

        // `notifications.type` / `target_type` are MySQL ENUMs — `message` and class names truncate and 500.
        try {
            Notification::create([
                'user_id' => $recipientId,
                'sender_id' => $sender->id,
                'title' => $isAr
                    ? ('رسالة جديدة من '.$sender->name)
                    : ('New message from '.$sender->name),
                'message' => $preview !== ''
                    ? $preview
                    : ($isAr ? 'لديك رسالة جديدة في المحادثة الخاصة.' : 'You have a new private message.'),
                'type' => 'general',
                'action_url' => $actionUrl,
                'action_text' => $isAr ? 'افتح المحادثة' : 'Open chat',
                'priority' => 'high',
                'target_type' => 'individual',
                'target_id' => $thread->id,
                'audience' => $audience,
                'is_read' => false,
                'data' => [
                    'kind' => 'private_lesson_message',
                    'message_id' => $message->id,
                    'thread_id' => $thread->id,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('private lesson message notification failed', [
                'thread_id' => $thread->id,
                'recipient_id' => $recipientId,
                'error' => $e->getMessage(),
            ]);
        }
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
                'target_type' => 'individual',
                'target_id' => $assignment->id,
                'is_read' => false,
                'priority' => 'high',
                'audience' => 'instructor',
                'data' => [
                    'kind' => 'private_lesson_assignment',
                    'assignment_id' => $assignment->id,
                ],
            ]);
            $assignment->instructor_notified_at = now();
        }

        if (! $assignment->student_notified_at) {
            Notification::create([
                'user_id' => $student->id,
                'title' => 'Your private teacher is ready',
                'message' => 'Teacher «'.$instructor->name.'» has been assigned for your Private Lessons. You can message them and join upcoming sessions.',
                'type' => 'assignment',
                'target_type' => 'individual',
                'target_id' => $assignment->id,
                'is_read' => false,
                'priority' => 'high',
                'audience' => 'student',
                'data' => [
                    'kind' => 'private_lesson_assignment',
                    'assignment_id' => $assignment->id,
                ],
            ]);
            $assignment->student_notified_at = now();
        }

        $assignment->save();

        if (self::threadsReady()) {
            self::ensureThread($student->id, $instructor->id, $assignment->id);
        }
        self::ensureReception($student->id, $instructor->id, 'assignment');
    }
}
