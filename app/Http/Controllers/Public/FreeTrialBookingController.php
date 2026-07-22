<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\FreeTrialBookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FreeTrialBookingController extends Controller
{
    public function slots(Request $request): JsonResponse
    {
        $days = min(21, max(7, (int) $request->input('days', 14)));
        $from = now();
        $to = now()->addDays($days);

        $slots = FreeTrialBookingService::availableSlots($from, $to);

        $byDate = $slots->groupBy('date')->map(function ($group) {
            return $group->values()->map(fn (array $slot) => [
                'starts_at' => $slot['starts_at'],
                'date' => $slot['date'],
                'time' => $slot['time'],
                'label' => $slot['label'],
                'duration' => $slot['duration'],
            ])->all();
        })->all();

        return response()->json([
            'duration_minutes' => FreeTrialBookingService::DURATION_MINUTES,
            'dates' => array_keys($byDate),
            'slots_by_date' => $byDate,
            'total' => $slots->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'goal' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
        ]);

        try {
            $booking = FreeTrialBookingService::book($data, $request->user()?->id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'تم حجز حصتك المجانية بنجاح. سنتواصل معك لتأكيد التفاصيل.',
            'booking' => [
                'id' => $booking->id,
                'starts_at' => $booking->starts_at->toIso8601String(),
                'label' => $booking->starts_at->locale(app()->getLocale())->translatedFormat('l d F Y — H:i'),
                'duration_minutes' => $booking->duration_minutes,
            ],
        ], 201);
    }
}
