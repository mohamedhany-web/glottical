<?php

namespace Tests\Feature;

use App\Models\OneToOneSession;
use App\Models\OneToOneWeeklyAvailability;
use App\Models\ServicePackage;
use App\Models\StudentServiceEntitlement;
use App\Models\User;
use App\Services\OneToOneSessionService;
use App\Services\StudentEntitlementService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class OneToOneMonthlyBookingTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildOneToOneSchema();
        Carbon::setTestNow(Carbon::parse('2026-08-12 10:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function buildOneToOneSchema(): void
    {
        Schema::create('advanced_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('delivery_type')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->unsignedBigInteger('service_package_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('scope', 64);
                $table->string('plan_type')->nullable();
                $table->unsignedInteger('term_months')->nullable();
                $table->unsignedInteger('weekly_group_sessions')->nullable();
                $table->unsignedInteger('weekly_private_sessions')->nullable();
                $table->boolean('includes_community')->default(false);
                $table->boolean('includes_libraries')->default(false);
                $table->unsignedBigInteger('tutoring_group_id')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->unsignedBigInteger('academic_subject_id')->nullable();
                $table->unsignedInteger('units_total')->default(0);
                $table->unsignedInteger('units_used')->default(0);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('status', 32)->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::create('one_to_one_weekly_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

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

        if (! Schema::hasTable('tutoring_group_bookings')) {
            Schema::create('tutoring_group_bookings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tutoring_group_id')->nullable();
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('payment_status', 32)->nullable();
                $table->unsignedBigInteger('instructor_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->string('status', 32)->default('pending');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * @return array{0: User, 1: User, 2: StudentServiceEntitlement}
     */
    protected function seedActors(int $units = 10): array
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        // Mon 18:00-20:00, Wed 18:00-20:00
        foreach ([1, 3] as $day) {
            OneToOneWeeklyAvailability::create([
                'instructor_id' => $instructor->id,
                'day_of_week' => $day,
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'slot_duration_minutes' => 50,
                'is_active' => true,
            ]);
        }

        $entitlement = StudentEntitlementService::grantManual(
            (int) $student->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            $units,
            null,
            90,
            'test credit'
        );

        return [$student, $instructor, $entitlement];
    }

    public function test_expand_weekly_pattern_creates_expected_dates(): void
    {
        $from = Carbon::parse('2026-08-12 10:00:00'); // Wednesday
        $dates = OneToOneSessionService::expandWeeklyPattern([
            ['day_of_week' => 1, 'time' => '18:00'],
            ['day_of_week' => 3, 'time' => '18:00'],
        ], 4, $from);

        $this->assertCount(8, $dates);
        $this->assertTrue(collect($dates)->every(fn (Carbon $d) => $d->gt($from)));
        $this->assertSame(
            [1, 3],
            collect($dates)->map(fn (Carbon $d) => $d->dayOfWeekIso)->unique()->sort()->values()->all()
        );
    }

    public function test_monthly_series_books_eight_sessions_and_reserves_credit(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(10);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [
                ['day_of_week' => 1, 'time' => '18:00'],
                ['day_of_week' => 3, 'time' => '18:00'],
            ],
            4,
            $entitlement,
            $student,
            'اختبار تثبيت شهري'
        );

        $this->assertCount(8, $sessions);
        $this->assertTrue($sessions->every(fn ($s) => $s->status === OneToOneSession::STATUS_SCHEDULED));
        $this->assertTrue($sessions->every(fn ($s) => filled($s->series_id)));
        $this->assertTrue($sessions->every(fn ($s) => filled($s->classroom_meeting_id)));
        $this->assertCount(1, $sessions->pluck('series_id')->unique());

        $left = StudentEntitlementService::bookableUnitsLeft($entitlement->fresh());
        $this->assertSame(2, $left); // 10 - 8 reserved
    }

    public function test_multi_book_with_same_teacher(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(5);

        $ats = [
            \App\Support\AppTimezone::wallClockToUtc('2026-08-17', '18:00'), // Mon
            \App\Support\AppTimezone::wallClockToUtc('2026-08-19', '18:00'), // Wed
            \App\Support\AppTimezone::wallClockToUtc('2026-08-24', '18:00'), // Mon
        ];

        $sessions = OneToOneSessionService::bookMultipleWithInstructor(
            $student,
            $instructor,
            $ats,
            $entitlement
        );

        $this->assertCount(3, $sessions);
        $this->assertSame(2, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));
    }

    public function test_rejects_when_credit_insufficient(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(3);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/الرصيد غير كاف/');

        OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [
                ['day_of_week' => 1, 'time' => '18:00'],
                ['day_of_week' => 3, 'time' => '18:00'],
            ],
            4,
            $entitlement
        );
    }

    public function test_rejects_unavailable_slot(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/غير متاح/');

        OneToOneSessionService::bookMultipleWithInstructor(
            $student,
            $instructor,
            [Carbon::parse('2026-08-17 09:00:00')], // outside window
            $entitlement
        );
    }

    public function test_student_can_post_monthly_booking(): void
    {
        [$student, $instructor] = $this->seedActors(10);

        $response = $this->actingAs($student)->post(
            route('student.one-to-one-sessions.book-instructor', $instructor),
            [
                'booking_style' => 'monthly',
                'weeks' => 4,
                'weekly_slots' => [
                    ['day_of_week' => 1, 'time' => '18:00'],
                    ['day_of_week' => 3, 'time' => '18:00'],
                ],
            ]
        );

        $response->assertRedirect(route('student.one-to-one-sessions.index'));
        $this->assertSame(8, OneToOneSession::query()->where('student_id', $student->id)->count());
    }

    public function test_admin_placement_monthly_store(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(10);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.placement.store'), [
            'mode' => 'private',
            'booking_style' => 'monthly',
            'student_id' => $student->id,
            'student_service_entitlement_id' => $entitlement->id,
            'instructor_id' => $instructor->id,
            'weeks' => 4,
            'weekly_slots' => [
                ['day_of_week' => 1, 'time' => '18:00'],
                ['day_of_week' => 3, 'time' => '18:00'],
            ],
            'notes' => 'اختبار أدمن',
        ]);

        $response->assertRedirect();
        $this->assertTrue(session()->has('success') || $response->isRedirect());
        $this->assertSame(8, OneToOneSession::query()->where('student_id', $student->id)->count());
    }

    public function test_admin_can_delete_registered_monthly_placement_and_restore_credit(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(10);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [
                ['day_of_week' => 1, 'time' => '18:00'],
                ['day_of_week' => 3, 'time' => '18:00'],
            ],
            4,
            $entitlement,
            $admin
        );

        $this->assertSame(2, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));

        $response = $this->actingAs($admin)->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \App\Http\Middleware\EnsurePermission::class,
            \App\Http\Middleware\RestrictRbacEmployeeAdminRoutes::class,
            \App\Http\Middleware\CheckActiveStatus::class,
        ])->delete(route('admin.placement.destroy-private', $sessions->first()));

        $response->assertRedirect(route('admin.placement.index'));
        $this->assertSame(
            8,
            OneToOneSession::query()
                ->where('student_id', $student->id)
                ->where('status', OneToOneSession::STATUS_CANCELLED)
                ->count()
        );
        $this->assertSame(10, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));
        $this->assertTrue(
            $sessions->every(fn ($s) => filled($s->fresh()->classroomMeeting?->ended_at))
        );
    }

    public function test_admin_can_cancel_one_session_in_a_series_without_removing_the_rest(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(10);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [
                ['day_of_week' => 1, 'time' => '18:00'],
                ['day_of_week' => 3, 'time' => '18:00'],
            ],
            4,
            $entitlement,
            $admin
        );

        $response = $this->actingAs($admin)->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \App\Http\Middleware\EnsurePermission::class,
            \App\Http\Middleware\RestrictRbacEmployeeAdminRoutes::class,
            \App\Http\Middleware\CheckActiveStatus::class,
        ])->delete(route('admin.one-to-one-sessions.destroy', $sessions->first()));

        $response->assertRedirect(route('admin.one-to-one-sessions.index'));
        $this->assertSame(1, OneToOneSession::query()->where('status', OneToOneSession::STATUS_CANCELLED)->count());
        $this->assertSame(7, OneToOneSession::query()->where('status', OneToOneSession::STATUS_SCHEDULED)->count());
        $this->assertSame(3, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));
    }

    public function test_admin_cannot_delete_completed_placement(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(2);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [
                ['day_of_week' => 1, 'time' => '18:00'],
            ],
            1,
            $entitlement,
            $admin
        );
        $session = $sessions->first();
        OneToOneSessionService::markCompleted($session);

        $response = $this->actingAs($admin)->from(route('admin.placement.index'))->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \App\Http\Middleware\EnsurePermission::class,
            \App\Http\Middleware\RestrictRbacEmployeeAdminRoutes::class,
            \App\Http\Middleware\CheckActiveStatus::class,
        ])->delete(route('admin.placement.destroy-private', $session));

        $response->assertRedirect(route('admin.placement.index'));
        $this->assertSame(OneToOneSession::STATUS_COMPLETED, $session->fresh()->status);
        $this->assertSame(1, (int) $entitlement->fresh()->units_used);
        $this->assertSame(1, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));
    }
}
