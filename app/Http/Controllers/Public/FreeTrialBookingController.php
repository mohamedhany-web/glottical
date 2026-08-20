<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\FreeTrialBookingService;
use App\Support\AppTimezone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FreeTrialBookingController extends Controller
{
    public function slots(Request $request): JsonResponse
    {
        $days = min(21, max(7, (int) $request->input('days', 14)));
        $viewerTz = AppTimezone::normalize($request->input('timezone'))
            ?? AppTimezone::timezoneForUsState($request->input('us_state'))
            ?? AppTimezone::academy();

        $from = now();
        $to = now()->addDays($days)->endOfDay();

        $slots = FreeTrialBookingService::availableSlots($from, $to, $viewerTz);

        $byDate = $slots->groupBy('date')->map(function ($group) {
            return $group->values()->map(fn (array $slot) => [
                'starts_at' => $slot['starts_at']->toIso8601String(),
                'date' => $slot['date'],
                'time' => $slot['time'],
                'time_academy' => $slot['time_academy'],
                'quality' => $slot['quality'],
                'quality_label' => $slot['quality_label'],
                'label' => $slot['label'],
                'duration' => $slot['duration'],
            ])->all();
        })->all();

        return response()->json([
            'duration_minutes' => FreeTrialBookingService::DURATION_MINUTES,
            'viewer_timezone' => $viewerTz,
            'academy_timezone' => AppTimezone::academy(),
            'dates' => array_keys($byDate),
            'slots_by_date' => $byDate,
            'total' => $slots->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $goalKeys = array_keys(\App\Models\FreeTrialBooking::goalOptions());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'country_code' => ['nullable', 'string', 'max:12'],
            'goal' => ['required', 'string', 'in:'.implode(',', $goalKeys)],
            'starts_at' => ['required', 'string', 'max:64'],
            'timezone' => AppTimezone::inputRules(false),
            'us_state' => ['nullable', 'string', 'max:64'],
        ]);

        try {
            $booking = FreeTrialBookingService::book($data, $request->user()?->id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $viewerTz = $booking->timezone
            ?: AppTimezone::normalize($data['timezone'] ?? null)
            ?: AppTimezone::academy();
        $dual = AppTimezone::dualLabel($booking->starts_at, $viewerTz, app()->getLocale(), 'l d F Y — g:i A');

        return response()->json([
            'message' => 'تم حجز حصتك المجانية بنجاح. سنتواصل معك لتأكيد التفاصيل.',
            'booking' => [
                'id' => $booking->id,
                'starts_at' => $booking->starts_at->toIso8601String(),
                'label' => $dual['primary'],
                'label_secondary' => $dual['secondary'],
                'timezone' => $viewerTz,
                'duration_minutes' => $booking->duration_minutes,
                'goal' => $booking->goal,
                'goal_label' => $booking->goalLabel(),
            ],
        ], 201);
    }
}
