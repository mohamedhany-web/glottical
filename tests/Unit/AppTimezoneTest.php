<?php

namespace Tests\Unit;

use App\Support\AppTimezone;
use Carbon\Carbon;
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
        $this->assertStringContainsString('بتوقيت مصر', $parts['secondary']);
    }

    public function test_dual_label_omits_secondary_when_same_zone(): void
    {
        $utc = AppTimezone::wallClockToUtc('2026-01-15', '18:00', 'Africa/Cairo');
        $parts = AppTimezone::dualLabel($utc, 'Africa/Cairo', 'ar', 'g:i A');
        $this->assertNull($parts['secondary']);
    }

    public function test_normalize_rejects_invalid(): void
    {
        $this->assertNull(AppTimezone::normalize('Not/AZone'));
        $this->assertTrue(AppTimezone::isValid('America/Chicago'));
    }
}
