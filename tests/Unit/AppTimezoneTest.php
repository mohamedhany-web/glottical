<?php

namespace Tests\Unit;

use App\Support\AppTimezone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

class AppTimezoneTest extends TestCase
{
    public function test_academy_defaults_to_cairo(): void
    {
        config(['platform.academy_timezone' => 'Africa/Cairo']);
        $this->assertSame('Africa/Cairo', AppTimezone::academy());
    }

    public function test_parse_egypt_evening_to_utc(): void
    {
        config(['platform.academy_timezone' => 'Africa/Cairo']);
        // Egypt is UTC+2 (no DST currently)
        $utc = AppTimezone::parseLocalToUtc('2026-01-15 18:00:00', 'Africa/Cairo');
        $this->assertSame('UTC', $utc->timezoneName);
        $this->assertSame('16:00', $utc->format('H:i'));
    }

    public function test_wall_clock_to_utc_and_dual_label_for_new_york(): void
    {
        config(['platform.academy_timezone' => 'Africa/Cairo']);
        $utc = AppTimezone::wallClockToUtc('2026-01-15', '18:00', 'Africa/Cairo');

        $ny = AppTimezone::formatFor($utc, 'America/New_York', 'Y-m-d H:i');
        $this->assertSame('2026-01-15 11:00', $ny);

        $parts = AppTimezone::dualLabel($utc, 'America/New_York', 'en', 'g:i A');
        $this->assertSame('11:00 AM', $parts['primary']);
        $this->assertNotNull($parts['secondary']);
        $this->assertStringContainsString('بتوقيت', $parts['secondary']);
        $this->assertStringContainsString('بتوقيت مصر', $parts['secondary']);
    }

    public function test_dual_label_omits_secondary_when_same_zone(): void
    {
        $utc = AppTimezone::wallClockToUtc('2026-01-15', '18:00', 'Africa/Cairo');
        $parts = AppTimezone::dualLabel($utc, 'Africa/Cairo', 'ar', 'g:i A');
        $this->assertNull($parts['secondary']);
    }

    public function test_parse_datetime_local_in_new_york(): void
    {
        config(['platform.academy_timezone' => 'Africa/Cairo']);
        $utc = AppTimezone::parseAppointmentInput('2026-01-15T18:00', 'America/New_York');
        $this->assertSame('23:00', $utc->format('H:i'));
        $this->assertSame('UTC', $utc->timezoneName);
    }

    public function test_parse_iso_z_ignores_requested_timezone(): void
    {
        $utc = AppTimezone::parseAppointmentInput('2026-01-15T16:00:00Z', 'America/New_York');
        $this->assertSame('16:00', $utc->format('H:i'));
    }

    public function test_shift_request_datetime_uses_selected_zone(): void
    {
        $request = Request::create('/schedule', 'POST', [
            'scheduled_at' => '2026-01-15T18:00',
            'timezone' => 'America/New_York',
        ]);
        $out = AppTimezone::shiftRequestDateTime($request, ['scheduled_at' => '2026-01-15T18:00'], 'scheduled_at');
        $this->assertInstanceOf(Carbon::class, $out['scheduled_at']);
        $this->assertSame('23:00', $out['scheduled_at']->format('H:i'));
    }

    public function test_normalize_rejects_invalid(): void
    {
        $this->assertNull(AppTimezone::normalize('Not/AZone'));
        $this->assertTrue(AppTimezone::isValid('America/Chicago'));
    }

    public function test_same_wall_clock_is_different_utc_in_egypt_vs_new_york(): void
    {
        $cairo = AppTimezone::parseAppointmentInput('2026-01-15T18:00', 'Africa/Cairo');
        $ny = AppTimezone::parseAppointmentInput('2026-01-15T18:00', 'America/New_York');

        $this->assertSame('16:00', $cairo->format('H:i'));
        $this->assertSame('23:00', $ny->format('H:i'));
        $this->assertFalse($cairo->equalTo($ny));
    }

    public function test_datetime_local_round_trip_keeps_new_york_wall_clock(): void
    {
        $utc = AppTimezone::parseAppointmentInput('2026-01-15T18:00', 'America/New_York');
        $this->assertSame('2026-01-15T18:00', AppTimezone::datetimeLocalValue($utc, 'America/New_York'));
        $this->assertSame('2026-01-16T01:00', AppTimezone::datetimeLocalValue($utc, 'Africa/Cairo'));
    }

    public function test_us_summer_time_uses_eastern_daylight(): void
    {
        // 15 Jul 2026 — America/New_York is UTC-4
        $utc = AppTimezone::parseAppointmentInput('2026-07-15T18:00', 'America/New_York');
        $this->assertSame('22:00', $utc->format('H:i'));
        $this->assertSame('18:00', $utc->copy()->timezone('America/New_York')->format('H:i'));
    }

    public function test_legacy_utc_sql_string_is_not_shifted_by_selected_zone(): void
    {
        $utc = AppTimezone::parseAppointmentInput('2026-01-15 16:00:00', 'America/New_York');
        $this->assertSame('16:00', $utc->format('H:i'));
    }
}
