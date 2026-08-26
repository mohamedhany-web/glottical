<?php

namespace Tests\Feature;

use App\Models\OneToOneSession;
use App\Models\OneToOneWeeklyAvailability;
use App\Models\ServicePackage;
use App\Models\StudentServiceEntitlement;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
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

    public function test_admin_places_manual_time_without_teacher_availability_windows(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'timezone' => 'America/Los_Angeles',
            'password' => Hash::make('password'),
        ]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'timezone' => 'Africa/Cairo',
            'password' => Hash::make('password'),
        ]);
        $entitlement = StudentEntitlementService::grantManual(
            (int) $student->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            3,
            null,
            90,
            'manual placement credit'
        );
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $response = $this->withoutMiddleware()->actingAs($admin)->post(route('admin.placement.store'), [
            'mode' => 'private',
            'booking_style' => 'single',
            'student_id' => $student->id,
            'student_service_entitlement_id' => $entitlement->id,
            'instructor_id' => $instructor->id,
            'timezone' => 'Africa/Cairo',
            'manual_scheduled_at' => '2026-08-20T12:00',
            'notes' => 'واتساب',
        ]);

        $response->assertRedirect();
        $session = OneToOneSession::query()->where('student_id', $student->id)->first();
        $this->assertNotNull($session);
        $this->assertNotNull($session->scheduled_at);
        $this->assertSame('12:00', $session->scheduled_at->copy()->timezone('Africa/Cairo')->format('H:i'));
        $this->assertNotSame('12:00', $session->scheduled_at->copy()->utc()->format('H:i'));
        $this->assertSame(
            Carbon::parse('2026-08-20 12:00:00', 'Africa/Cairo')->timezone('America/Los_Angeles')->format('H:i'),
            $session->scheduled_at->copy()->timezone('America/Los_Angeles')->format('H:i')
        );
        $this->assertSame(OneToOneSession::STATUS_SCHEDULED, $session->status);
    }

    public function test_admin_monthly_places_without_teacher_availability_and_saves_windows(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $instructor = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'timezone' => 'Africa/Cairo',
            'password' => Hash::make('password'),
        ]);
        $this->assertSame(0, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());

        $entitlement = StudentEntitlementService::grantManual(
            (int) $student->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            20,
            null,
            90,
            'admin monthly without windows'
        );
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
            'timezone' => 'Africa/Cairo',
            'weeks' => 8,
            'weekly_per_week' => 2,
            'weekly_slots' => [
                ['day_of_week' => 1, 'time' => '13:00:00'],
                ['day_of_week' => 2, 'time' => '13:00'],
            ],
            'save_as_teacher_schedule' => '1',
            'notes' => 'تسكين يدوي بدون جدول معلم',
        ]);

        $response->assertRedirect();
        $this->assertTrue(session()->has('success'));
        $this->assertSame(16, OneToOneSession::query()->where('student_id', $student->id)->count());
        $this->assertTrue(
            OneToOneSession::query()->where('student_id', $student->id)->get()
                ->every(fn ($s) => $s->status === OneToOneSession::STATUS_SCHEDULED)
        );
        $this->assertSame(2, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
        $this->assertSame(
            ['13:00', '13:00'],
            OneToOneWeeklyAvailability::query()
                ->where('instructor_id', $instructor->id)
                ->orderBy('day_of_week')
                ->get()
                ->map(fn ($r) => substr((string) $r->start_time, 0, 5))
                ->all()
        );
        $this->assertSame(4, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));
    }

    public function test_admin_placement_slots_json_works_with_and_without_windows(): void
    {
        [, $instructor] = $this->seedActors(4);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $bareTeacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'timezone' => 'Africa/Cairo',
            'password' => Hash::make('password'),
        ]);

        $withWindows = $this->withoutMiddleware()->actingAs($admin)->getJson(
            route('admin.placement.slots', ['mode' => 'private', 'instructor_id' => $instructor->id])
        );
        $withWindows->assertOk()->assertJsonPath('ok', true);
        $this->assertNotEmpty($withWindows->json('weekly_windows'));

        $without = $this->withoutMiddleware()->actingAs($admin)->getJson(
            route('admin.placement.slots', ['mode' => 'private', 'instructor_id' => $bareTeacher->id])
        );
        $without->assertOk()->assertJsonPath('ok', true);
        $this->assertSame([], $without->json('weekly_windows'));
        $this->assertNotEmpty($without->json('empty_hint'));
    }

    public function test_admin_placement_create_page_lets_admin_type_times_without_teacher_schedule(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \App\Http\Middleware\EnsurePermission::class,
            \App\Http\Middleware\RestrictRbacEmployeeAdminRoutes::class,
            \App\Http\Middleware\CheckActiveStatus::class,
        ])->get(route('admin.placement.create', ['mode' => 'private']));

        $response->assertOk();
        $response->assertSee('تأكيد التسكين', false);
        $response->assertSee('احفظ هذه المواعيد في جدول المعلم', false);
        $response->assertSee('لا يشترط أن يكون المعلم قد ضبط جدوله', false);
        $response->assertDontSee('اختر معلماً من جدول توافره فقط', false);
        $html = $response->getContent();
        $this->assertDoesNotMatchRegularExpression('/id="submitBtn"[^>]*\sdisabled/', $html);
        $this->assertDoesNotMatchRegularExpression('/id="entitlementSelect"[^>]*\sdisabled/', $html);
        $this->assertStringContainsString('if (!v) return;', $html);
        $this->assertStringNotContainsString("dayEl.value = ''; timeEl.value = '';", $html);
    }

    public function test_admin_student_context_returns_private_credit(): void
    {
        [$student, , $entitlement] = $this->seedActors(6);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $response = $this->withoutMiddleware()->actingAs($admin)->getJson(
            route('admin.placement.student-context', ['student_id' => $student->id])
        );

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('has_package', true)
            ->assertJsonPath('private_units', 6);
        $this->assertSame($entitlement->id, (int) $response->json('bookable_entitlements.0.id'));
    }

    public function test_admin_monthly_rejects_when_credit_is_not_enough_for_generated_sessions(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $instructor = User::factory()->create(['role' => 'teacher', 'is_active' => true, 'timezone' => 'Africa/Cairo']);
        $entitlement = StudentEntitlementService::grantManual(
            (int) $student->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            10,
            null,
            90,
            'short credit'
        );
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $response = $this->from(route('admin.placement.create'))
            ->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.placement.store'), [
                'mode' => 'private',
                'booking_style' => 'monthly',
                'student_id' => $student->id,
                'student_service_entitlement_id' => $entitlement->id,
                'instructor_id' => $instructor->id,
                'weeks' => 8,
                'weekly_slots' => [
                    ['day_of_week' => 1, 'time' => '13:00'],
                    ['day_of_week' => 2, 'time' => '13:00'],
                ],
                'save_as_teacher_schedule' => '0',
            ]);

        $response->assertRedirect(route('admin.placement.create'));
        $this->assertTrue(session()->has('error'));
        $this->assertMatchesRegularExpression('/الرصيد غير كاف/', (string) session('error'));
        $this->assertSame(0, OneToOneSession::query()->count());
        $this->assertSame(0, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
        $this->assertSame(10, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));
    }

    public function test_admin_monthly_does_not_save_teacher_windows_when_unchecked(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $instructor = User::factory()->create(['role' => 'teacher', 'is_active' => true, 'timezone' => 'Africa/Cairo']);
        $entitlement = StudentEntitlementService::grantManual(
            (int) $student->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            4,
            null,
            90,
            'no persist'
        );
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->withoutMiddleware()->actingAs($admin)->post(route('admin.placement.store'), [
            'mode' => 'private',
            'booking_style' => 'monthly',
            'student_id' => $student->id,
            'student_service_entitlement_id' => $entitlement->id,
            'instructor_id' => $instructor->id,
            'weeks' => 1,
            'weekly_slots' => [
                ['day_of_week' => 1, 'time' => '13:00'],
            ],
            'save_as_teacher_schedule' => '0',
        ])->assertRedirect();

        $this->assertSame(1, OneToOneSession::query()->where('student_id', $student->id)->count());
        $this->assertSame(0, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
    }

    public function test_admin_still_blocks_overlapping_times_for_the_same_teacher(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(8);
        $other = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $otherCredit = StudentEntitlementService::grantManual(
            (int) $other->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            8,
            null,
            90,
            'second student'
        );
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $payload = [
            'mode' => 'private',
            'booking_style' => 'monthly',
            'instructor_id' => $instructor->id,
            'weeks' => 4,
            'weekly_slots' => [
                ['day_of_week' => 1, 'time' => '18:00'],
                ['day_of_week' => 3, 'time' => '18:00'],
            ],
            'save_as_teacher_schedule' => '0',
        ];

        $this->withoutMiddleware()->actingAs($admin)->post(route('admin.placement.store'), $payload + [
            'student_id' => $student->id,
            'student_service_entitlement_id' => $entitlement->id,
        ])->assertRedirect();
        $this->assertSame(8, OneToOneSession::query()->where('student_id', $student->id)->count());

        $this->from(route('admin.placement.create'))
            ->withoutMiddleware()
            ->actingAs($admin)
            ->post(route('admin.placement.store'), $payload + [
                'student_id' => $other->id,
                'student_service_entitlement_id' => $otherCredit->id,
            ])
            ->assertRedirect(route('admin.placement.create'));

        $this->assertMatchesRegularExpression('/متعارض/', (string) session('error'));
        $this->assertSame(0, OneToOneSession::query()->where('student_id', $other->id)->count());
        $this->assertSame(8, StudentEntitlementService::bookableUnitsLeft($otherCredit->fresh()));
    }

    public function test_student_can_book_after_admin_sets_teacher_windows(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $instructor = User::factory()->create(['role' => 'instructor', 'is_active' => true, 'timezone' => 'Africa/Cairo']);
        StudentEntitlementService::grantManual(
            (int) $student->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            8,
            null,
            90,
            'after admin windows'
        );

        $this->assertSame(0, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
        $this->assertSame(2, OneToOneAvailabilityService::ensureWindows((int) $instructor->id, [
            ['day_of_week' => 1, 'time' => '18:00'],
            ['day_of_week' => 3, 'time' => '18:00'],
        ], 50));
        $this->assertSame(0, OneToOneAvailabilityService::ensureWindows((int) $instructor->id, [
            ['day_of_week' => 1, 'time' => '18:00'],
            ['day_of_week' => 3, 'time' => '18:00'],
        ], 50));

        $this->actingAs($student)->post(
            route('student.one-to-one-sessions.book-instructor', $instructor),
            [
                'booking_style' => 'monthly',
                'weeks' => 4,
                'weekly_slots' => [
                    ['day_of_week' => 1, 'time' => '18:00'],
                    ['day_of_week' => 3, 'time' => '18:00'],
                ],
            ]
        )->assertRedirect(route('student.one-to-one-sessions.index'));

        $this->assertSame(8, OneToOneSession::query()->where('student_id', $student->id)->count());
        $this->assertSame(2, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
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
        $meeting = $session->classroomMeeting;
        $this->assertNotNull($meeting);
        $meeting->update(['started_at' => now()]);
        $session->update(['scheduled_at' => now()->subMinutes(5)]);
        OneToOneSessionService::markCompleted($session->fresh(['classroomMeeting', 'entitlement']));

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

    public function test_instructor_cannot_complete_session_without_entering_room(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(2);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [['day_of_week' => 1, 'time' => '18:00']],
            1,
            $entitlement,
            $instructor
        );
        $session = $sessions->first();
        $this->assertNotNull($session->classroomMeeting);
        $this->assertNull($session->classroomMeeting->started_at);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('قبل دخول الغرفة');

        OneToOneSessionService::markCompleted($session);
    }

    public function test_instructor_cannot_complete_session_before_scheduled_time(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(2);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [['day_of_week' => 1, 'time' => '18:00']],
            1,
            $entitlement,
            $instructor
        );
        $session = $sessions->first();
        $session->classroomMeeting->update(['started_at' => now()]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('قبل موعدها');

        OneToOneSessionService::markCompleted($session->fresh(['classroomMeeting']));
    }

    public function test_instructor_complete_route_does_not_deduct_without_room_start(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(2);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [['day_of_week' => 1, 'time' => '18:00']],
            1,
            $entitlement,
            $instructor
        );
        $session = $sessions->first();

        $this->actingAs($instructor)
            ->from(route('instructor.one-to-one-sessions.show', $session))
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->post(route('instructor.one-to-one-sessions.complete', $session))
            ->assertRedirect();

        $this->assertSame(OneToOneSession::STATUS_SCHEDULED, $session->fresh()->status);
        $this->assertSame(0, (int) $entitlement->fresh()->units_used);
        $this->assertSame(1, StudentEntitlementService::bookableUnitsLeft($entitlement->fresh()));
    }

    public function test_admin_can_reschedule_private_placement_from_placement_page(): void
    {
        [$student, $instructor, $entitlement] = $this->seedActors(4);
        $instructor->update(['timezone' => 'Africa/Cairo']);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
            $student,
            $instructor,
            [['day_of_week' => 1, 'time' => '18:00']],
            1,
            $entitlement,
            $admin
        );
        $session = $sessions->first()->fresh(['classroomMeeting']);
        $this->assertNotNull($session->scheduled_at);
        $this->assertNotNull($session->classroomMeeting);
        $originalMeetingId = $session->classroom_meeting_id;

        $newLocal = '2026-08-25T14:30';

        $this->actingAs($admin)
            ->withoutMiddleware([
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
                \App\Http\Middleware\EnsurePermission::class,
                \App\Http\Middleware\RestrictRbacEmployeeAdminRoutes::class,
                \App\Http\Middleware\CheckActiveStatus::class,
            ])
            ->from(route('admin.placement.index'))
            ->patch(route('admin.placement.update-private-schedule', $session), [
                'scheduled_at' => $newLocal,
                'duration_minutes' => 55,
                'timezone' => 'Africa/Cairo',
            ])
            ->assertRedirect(route('admin.placement.index'))
            ->assertSessionHas('success');

        $session->refresh();
        $meeting = $session->classroomMeeting?->fresh();

        $this->assertSame($originalMeetingId, $session->classroom_meeting_id, 'Reschedule must update existing meeting, not create a new one.');
        $this->assertSame(OneToOneSession::STATUS_SCHEDULED, $session->status);
        $this->assertSame(55, (int) $session->duration_minutes);
        $this->assertNotNull($meeting);
        $this->assertSame(55, (int) $meeting->planned_duration_minutes);

        $expectedUtc = \App\Support\AppTimezone::parseAppointmentInput($newLocal, 'Africa/Cairo');
        $this->assertNotNull($expectedUtc);
        $this->assertTrue($session->scheduled_at->equalTo($expectedUtc));
        $this->assertTrue($meeting->scheduled_for->equalTo($expectedUtc));

        $this->actingAs($admin)
            ->withoutMiddleware([
                \App\Http\Middleware\EnsurePermission::class,
                \App\Http\Middleware\RestrictRbacEmployeeAdminRoutes::class,
                \App\Http\Middleware\CheckActiveStatus::class,
            ])
            ->get(route('admin.placement.index'))
            ->assertOk()
            ->assertSee('حفظ الموعد', false)
            ->assertSee(route('admin.placement.update-private-schedule', $session), false);
    }
}
