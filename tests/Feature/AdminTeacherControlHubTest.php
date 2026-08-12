<?php

namespace Tests\Feature;

use App\Models\OneToOneSession;
use App\Models\OneToOneWeeklyAvailability;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class AdminTeacherControlHubTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildExtra();
    }

    protected function buildExtra(): void
    {
        if (! Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->string('status')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('instructor_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tutoring_groups')) {
            Schema::create('tutoring_groups', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('type')->default('individual');
                $table->unsignedBigInteger('instructor_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tutoring_group_bookings')) {
            Schema::create('tutoring_group_bookings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('tutoring_group_id')->nullable();
                $table->unsignedBigInteger('instructor_id')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tutor_work_schedules')) {
            Schema::create('tutor_work_schedules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('instructor_id');
                $table->unsignedTinyInteger('day_of_week');
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedInteger('slot_duration_minutes')->default(60);
                $table->string('applies_to')->default('both');
                $table->string('note')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('one_to_one_weekly_availability')) {
            Schema::create('one_to_one_weekly_availability', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('instructor_id');
                $table->unsignedTinyInteger('day_of_week');
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedInteger('slot_duration_minutes')->default(50);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('one_to_one_sessions')) {
            Schema::create('one_to_one_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('instructor_id');
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
                $table->unsignedBigInteger('booked_by_user_id')->nullable();
                $table->unsignedInteger('session_number')->default(1);
                $table->unsignedInteger('duration_minutes')->default(50);
                $table->string('status')->default('pending_schedule');
                $table->timestamp('scheduled_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('classroom_meetings')) {
            Schema::create('classroom_meetings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('one_to_one_session_id')->nullable();
                $table->string('code')->nullable();
                $table->string('room_name')->nullable();
                $table->string('title')->nullable();
                $table->timestamp('scheduled_for')->nullable();
                $table->unsignedInteger('planned_duration_minutes')->nullable();
                $table->unsignedInteger('max_participants')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->string('type')->nullable();
                $table->string('priority')->nullable();
                $table->string('audience')->nullable();
                $table->string('action_url')->nullable();
                $table->string('action_text')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_admin_can_update_teacher_profile(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'name' => 'معلم قديم',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.teachers.update-profile', $teacher), [
                'name' => 'معلم محدّث',
                'email' => $teacher->email,
                'phone' => '0500000001',
                'bio' => 'نبذة جديدة',
                'role' => 'instructor',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.teachers.show', ['teacher' => $teacher, 'tab' => 'profile']));

        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'name' => 'معلم محدّث',
            'phone' => '0500000001',
            'bio' => 'نبذة جديدة',
        ]);
    }

    public function test_admin_can_sync_one_to_one_availability(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.teachers.sync-oto-availability', $teacher), [
                'slots' => [
                    [
                        'day_of_week' => 1,
                        'start_time' => '10:00',
                        'end_time' => '14:00',
                        'slot_duration_minutes' => 50,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.teachers.show', ['teacher' => $teacher, 'tab' => 'schedule']));

        $this->assertSame(1, OneToOneWeeklyAvailability::query()->where('instructor_id', $teacher->id)->count());
    }

    public function test_admin_can_cancel_one_to_one_session(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $session = OneToOneSession::create([
            'student_id' => $student->id,
            'instructor_id' => $teacher->id,
            'session_number' => 1,
            'duration_minutes' => 50,
            'status' => OneToOneSession::STATUS_PENDING,
            'scheduled_at' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.teachers.sessions.cancel', [$teacher, $session]))
            ->assertRedirect(route('admin.teachers.show', ['teacher' => $teacher, 'tab' => 'sessions']));

        $this->assertSame(OneToOneSession::STATUS_CANCELLED, $session->fresh()->status);
    }

    public function test_teacher_hub_index_lists_teachers(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'name' => 'معلم الاختبار',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.teachers.index'))
            ->assertOk()
            ->assertSee('معلم الاختبار', false)
            ->assertSee('مركز تحكم المعلمين', false);

        $this->actingAs($admin)
            ->get(route('admin.teachers.show', $teacher))
            ->assertOk()
            ->assertSee('بيانات الحساب', false);
    }
}
