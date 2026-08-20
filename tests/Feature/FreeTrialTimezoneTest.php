<?php

namespace Tests\Feature;

use App\Models\FreeTrialBooking;
use App\Models\FreeTrialWeeklyAvailability;
use App\Services\FreeTrialBookingService;
use App\Support\AppTimezone;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class FreeTrialTimezoneTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->extendFreeTrialSchema();
        config(['app.timezone' => 'UTC', 'platform.academy_timezone' => 'Africa/Cairo']);
        Carbon::setTestNow(Carbon::parse('2026-03-10 12:00:00', 'UTC')); // Tuesday
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function extendFreeTrialSchema(): void
    {
        Schema::create('free_trial_weekly_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('free_trial_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('country_code', 12)->nullable();
            $table->string('goal')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('status', 32)->default('confirmed');
            $table->text('notes')->nullable();
            $table->string('timezone', 64)->nullable();
            $table->string('us_state', 64)->nullable();
            $table->unsignedBigInteger('recommended_academic_year_id')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // Tuesday 18:00–19:00 Cairo → 16:00–17:00 UTC → 11:00–12:00 NY (March EST still? Mar 10 2026 is after DST start Mar 8 → EDT UTC-4 → 12:00 NY)
        FreeTrialWeeklyAvailability::create([
            'day_of_week' => 2, // Tuesday
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);
    }

    public function test_slots_use_cairo_wall_clock_and_convert_for_viewer(): void
    {
        $from = Carbon::parse('2026-03-10 00:00:00', 'UTC');
        $to = Carbon::parse('2026-03-10 23:59:59', 'UTC');
        $slots = FreeTrialBookingService::availableSlots($from, $to, 'America/New_York');

        $this->assertTrue($slots->isNotEmpty());
        $slot = $slots->first();

        $this->assertSame('16:00', $slot['starts_at']->copy()->utc()->format('H:i'));
        $this->assertSame('18:00', $slot['time_academy']);
        // 10 Mar 2026 America/New_York is EDT (UTC-4)
        $this->assertSame('12:00', $slot['time']);
        $this->assertSame(AppTimezone::QUALITY_GOOD, $slot['quality']);
        $this->assertSame('America/New_York', $slot['viewer_timezone']);
        $this->assertSame('Africa/Cairo', $slot['academy_timezone']);
    }

    public function test_http_slots_accept_timezone_query(): void
    {
        $response = $this->getJson('/free-trial/slots?days=7&timezone=America/New_York');
        $response->assertOk()
            ->assertJsonPath('viewer_timezone', 'America/New_York')
            ->assertJsonPath('academy_timezone', 'Africa/Cairo');

        $this->assertGreaterThan(0, $response->json('total'));
        $dates = $response->json('dates');
        $this->assertNotEmpty($dates);
        $first = $response->json('slots_by_date.'.$dates[0].'.0');
        $this->assertArrayHasKey('quality', $first);
        $this->assertArrayHasKey('time_academy', $first);
        $this->assertArrayHasKey('starts_at', $first);
    }

    public function test_book_stores_timezone_and_blocks_slot(): void
    {
        $from = Carbon::parse('2026-03-10 00:00:00', 'UTC');
        $to = Carbon::parse('2026-03-10 23:59:59', 'UTC');
        $slot = FreeTrialBookingService::availableSlots($from, $to, 'America/New_York')->first();
        $this->assertNotNull($slot);

        $booking = FreeTrialBookingService::book([
            'name' => 'Parent Test',
            'email' => 'parent-tz@example.com',
            'phone' => '512345678',
            'country_code' => '+966',
            'goal' => 'trial',
            'starts_at' => $slot['starts_at']->toIso8601String(),
            'timezone' => 'America/New_York',
            'us_state' => 'نيويورك',
        ]);

        $this->assertSame('America/New_York', $booking->timezone);
        $this->assertSame('نيويورك', $booking->us_state);
        $this->assertSame('16:00', $booking->starts_at->copy()->utc()->format('H:i'));

        $after = FreeTrialBookingService::availableSlots($from, $to, 'America/New_York');
        $this->assertFalse(
            $after->contains(fn (array $s) => $s['starts_at']->equalTo($slot['starts_at']))
        );
    }

    public function test_http_book_with_timezone(): void
    {
        $slots = $this->getJson('/free-trial/slots?days=7&timezone=America/Chicago');
        $slots->assertOk();
        $dates = $slots->json('dates');
        $this->assertNotEmpty($dates);
        $start = $slots->json('slots_by_date.'.$dates[0].'.0.starts_at');

        $response = $this->postJson('/free-trial/book', [
            'name' => 'HTTP Parent',
            'email' => 'http-parent@example.com',
            'phone' => '512345678',
            'country_code' => '+966',
            'goal' => 'trial',
            'starts_at' => $start,
            'timezone' => 'America/Chicago',
            'us_state' => 'تكساس',
        ]);

        $response->assertCreated()
            ->assertJsonPath('booking.timezone', 'America/Chicago');

        $this->assertDatabaseHas('free_trial_bookings', [
            'email' => 'http-parent@example.com',
            'timezone' => 'America/Chicago',
            'us_state' => 'تكساس',
        ]);
    }

    public function test_us_state_query_selects_timezone(): void
    {
        $response = $this->getJson('/free-trial/slots?days=7&us_state='.urlencode('كاليفورنيا'));
        $response->assertOk()
            ->assertJsonPath('viewer_timezone', 'America/Los_Angeles');
    }
}
