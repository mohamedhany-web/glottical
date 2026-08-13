<?php

namespace App\Http\Controllers;

use App\Support\AppTimezone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountTimezoneController extends Controller
{
    /**
     * حفظ/تحديث منطقة المستخدم من المتصفح أو النموذج.
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'timezone' => ['required', 'string', 'max:64'],
            'force' => ['sometimes', 'boolean'],
        ]);

        $timezone = AppTimezone::normalize($validated['timezone']);
        if (! $timezone) {
            return response()->json(['ok' => false, 'message' => 'منطقة زمنية غير صالحة.'], 422);
        }

        $force = $request->boolean('force');
        $user = $request->user();

        if ($user) {
            $shouldUpdate = $force || blank($user->timezone);
            if ($shouldUpdate) {
                $user->forceFill(['timezone' => $timezone])->save();

                return response()->json([
                    'ok' => true,
                    'timezone' => $timezone,
                    'updated' => true,
                ]);
            }

            return response()->json([
                'ok' => true,
                'timezone' => $user->timezone,
                'updated' => false,
            ]);
        }

        $request->session()->put('pending_timezone', $timezone);

        return response()->json([
            'ok' => true,
            'timezone' => $timezone,
            'updated' => false,
            'pending' => true,
        ]);
    }
}
