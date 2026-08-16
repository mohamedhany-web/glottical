<?php

namespace Tests\Feature;

use App\Models\ClassFeedPost;
use App\Models\OneToOneSession;
use App\Models\PrivateLessonMessage;
use App\Models\PrivateLessonThread;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\TutoringClassService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class StudentTeacherCommunityTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->createCommunityTables();
        Mail::fake();
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
    }

    public function test_inbox_opens_thread_after_one_to_one_session_exists(): void
    {
        [$student, $instructor] = $this->seedStudentAndTeacher();
        OneToOneSession::create([
            'instructor_id' => $instructor->id,
            'student_id' => $student->id,
            'session_number' => 1,
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 50,
            'status' => OneToOneSession::STATUS_SCHEDULED,
        ]);

        $this->assertSame(0, PrivateLessonThread::query()->count());

        $this->actingAs($student)
            ->get(route('student.private-messages.index'))
            ->assertOk()
            ->assertSee($instructor->name, false);

        $thread = PrivateLessonThread::query()->first();
        $this->assertNotNull($thread);
        $this->assertSame((int) $student->id, (int) $thread->student_id);
        $this->assertSame((int) $instructor->id, (int) $thread->instructor_id);
    }

    public function test_student_and_teacher_can_exchange_messages(): void
    {
        [$student, $instructor] = $this->seedStudentAndTeacher();
        OneToOneSession::create([
            'instructor_id' => $instructor->id,
            'student_id' => $student->id,
            'session_number' => 1,
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 50,
            'status' => OneToOneSession::STATUS_SCHEDULED,
        ]);

        $this->actingAs($student)
            ->get(route('student.private-messages.with', $instructor))
            ->assertRedirect();

        $thread = PrivateLessonThread::query()->first();
        $this->assertNotNull($thread);

        $this->actingAs($student)
            ->post(route('student.private-messages.send', $thread), ['body' => 'موعد الإثنين تمام؟'])
            ->assertRedirect();

        $this->assertSame(1, PrivateLessonMessage::query()->where('sender_id', $student->id)->count());

        $this->actingAs($instructor)
            ->from(route('instructor.private-messages.show', $thread))
            ->post(route('instructor.private-messages.send', $thread), ['body' => 'تمام الساعة 1'])
            ->assertRedirect();

        $this->assertSame(2, PrivateLessonMessage::query()->where('private_lesson_thread_id', $thread->id)->count());
    }

    public function test_class_enrollment_opens_teacher_chat_and_community_posts(): void
    {
        [$student, $instructor] = $this->seedStudentAndTeacher();
        [, $cohort] = $this->seedClass($instructor, $student, enrollDirectly: false);

        TutoringClassService::enrollStudent($cohort, $student, countSeat: true);

        $this->assertSame(1, PrivateLessonThread::query()
            ->where('student_id', $student->id)
            ->where('instructor_id', $instructor->id)
            ->count());

        $this->actingAs($student)
            ->get(route('student.classes.community', $cohort))
            ->assertOk()
            ->assertSee('مجتمع الفصل', false);

        $this->actingAs($student)
            ->post(route('student.classes.feed.store', $cohort), [
                'body' => 'حد فاهم الواجب؟',
                'post_type' => 'question',
            ])
            ->assertRedirect(route('student.classes.community', $cohort));

        $this->assertDatabaseHas('class_feed_posts', [
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
            'body' => 'حد فاهم الواجب؟',
        ]);

        $post = ClassFeedPost::query()->first();
        $this->actingAs($instructor)
            ->from(route('instructor.tutoring-cohorts.community', $cohort))
            ->post(route('instructor.class-feed.comment', $post), ['body' => 'نراجعها في الحصة'])
            ->assertRedirect();

        $this->assertDatabaseHas('class_feed_comments', [
            'class_feed_post_id' => $post->id,
            'user_id' => $instructor->id,
        ]);
    }

    public function test_cannot_message_unrelated_teacher(): void
    {
        [$student] = $this->seedStudentAndTeacher();
        $other = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($student)
            ->get(route('student.private-messages.with', $other))
            ->assertForbidden();
    }

    /**
     * @return array{0: User, 1: User}
     */
    protected function seedStudentAndTeacher(): array
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'name' => 'طالب المجتمع',
            'password' => Hash::make('password'),
        ]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'name' => 'معلم المجتمع',
            'password' => Hash::make('password'),
        ]);

        return [$student, $instructor];
    }

    /**
     * @return array{0: TutoringGroup, 1: TutoringGroupCohort}
     */
    protected function seedClass(User $instructor, User $student, bool $enrollDirectly = true): array
    {
        $group = TutoringGroup::create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'فصل المجتمع',
            'slug' => 'g-'.uniqid(),
            'instructor_id' => $instructor->id,
            'price' => 0,
            'capacity' => 20,
            'duration_minutes' => 60,
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $cohort = TutoringGroupCohort::create([
            'tutoring_group_id' => $group->id,
            'title' => 'دفعة المجتمع',
            'slug' => 'c-'.uniqid(),
            'starts_at' => now()->subDay(),
            'study_days' => [1],
            'study_time' => '18:00',
            'sessions_count' => 8,
            'session_duration_minutes' => 60,
            'timezone' => 'Africa/Cairo',
            'capacity' => 20,
            'enrolled_count' => $enrollDirectly ? 1 : 0,
            'min_enrollment' => 1,
            'status' => TutoringGroupCohort::STATUS_OPEN,
            'is_visible' => true,
            'sort_order' => 0,
        ]);
        if ($enrollDirectly) {
            TutoringCohortEnrollment::create([
                'tutoring_group_cohort_id' => $cohort->id,
                'user_id' => $student->id,
                'status' => TutoringCohortEnrollment::STATUS_ACTIVE,
                'enrolled_at' => now()->subDays(2),
            ]);
        }

        return [$group, $cohort];
    }

    protected function createCommunityTables(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->nullable();
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('delivery_type')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('one_to_one_sessions')) {
            Schema::create('one_to_one_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_course_enrollment_id')->nullable();
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
                $table->unsignedBigInteger('advanced_course_id')->nullable();
                $table->foreignId('instructor_id');
                $table->foreignId('student_id');
                $table->unsignedInteger('session_number')->default(1);
                $table->timestamp('scheduled_at')->nullable();
                $table->unsignedSmallInteger('duration_minutes')->default(50);
                $table->boolean('is_private_lecture')->default(false);
                $table->string('system_channel')->nullable();
                $table->string('status', 32)->default('pending_schedule');
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
                $table->unsignedBigInteger('booked_by_user_id')->nullable();
                $table->text('notes')->nullable();
                $table->string('series_id', 36)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('private_lesson_threads')) {
            Schema::create('private_lesson_threads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id');
                $table->foreignId('instructor_id');
                $table->unsignedBigInteger('student_instructor_assignment_id')->nullable();
                $table->unsignedBigInteger('advanced_course_id')->nullable();
                $table->unsignedBigInteger('tutoring_group_id')->nullable();
                $table->string('subject')->nullable();
                $table->string('status', 32)->default('open');
                $table->boolean('admin_visible')->default(true);
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('private_lesson_messages')) {
            Schema::create('private_lesson_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('private_lesson_thread_id');
                $table->foreignId('sender_id');
                $table->string('sender_role', 32);
                $table->text('body');
                $table->boolean('is_internal_note')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('notifications') && ! Schema::hasColumn('notifications', 'target_type')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('target_type')->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
            });
        }

        $migration = require database_path('migrations/2026_08_10_010000_create_school_gamification_tables.php');
        $migration->up();
    }
}
