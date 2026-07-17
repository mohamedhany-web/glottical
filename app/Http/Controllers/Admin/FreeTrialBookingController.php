<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreeTrialBooking;
use App\Models\FreeTrialWeeklyAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FreeTrialBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.free-trial-bookings'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = FreeTrialBooking::query()->with('user:id,name,email');

        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('goal', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, [
            FreeTrialBooking::STATUS_CONFIRMED,
            FreeTrialBooking::STATUS_CANCELLED,
            FreeTrialBooking::STATUS_COMPLETED,
        ], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('starts_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('starts_at', '<=', $request->input('to'));
        }

        $bookings = $query->orderByDesc('starts_at')->paginate(20)->withQueryString();

        $stats = [
            'total' => FreeTrialBooking::count(),
            'confirmed' => FreeTrialBooking::where('status', FreeTrialBooking::STATUS_CONFIRMED)->count(),
            'upcoming' => FreeTrialBooking::where('status', FreeTrialBooking::STATUS_CONFIRMED)
                ->where('starts_at', '>=', now())->count(),
            'today' => FreeTrialBooking::whereDate('starts_at', today())->count(),
            'cancelled' => FreeTrialBooking::where('status', FreeTrialBooking::STATUS_CANCELLED)->count(),
            'completed' => FreeTrialBooking::where('status', FreeTrialBooking::STATUS_COMPLETED)->count(),
        ];

        return view('admin.free-trial-bookings.index', compact('bookings', 'stats'));
    }

    public function show(FreeTrialBooking $freeTrialBooking): View
    {
        $freeTrialBooking->load('user:id,name,email,phone');

        return view('admin.free-trial-bookings.show', [
            'booking' => $freeTrialBooking,
        ]);
    }

    public function updateStatus(Request $request, FreeTrialBooking $freeTrialBooking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled,completed'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $freeTrialBooking->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? $freeTrialBooking->notes,
        ]);

        return redirect()
            ->route('admin.free-trial-bookings.show', $freeTrialBooking)
            ->with('success', 'تم تحديث حالة الحجز.');
    }

    public function destroy(FreeTrialBooking $freeTrialBooking): RedirectResponse
    {
        $freeTrialBooking->delete();

        return redirect()
            ->route('admin.free-trial-bookings.index')
            ->with('success', 'تم حذف الحجز.');
    }

    public function availability(): View
    {
        $windows = FreeTrialWeeklyAvailability::query()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $dayNames = [
            1 => 'الاثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            7 => 'الأحد',
        ];

        return view('admin.free-trial-bookings.availability', compact('windows', 'dayNames'));
    }

    public function storeAvailability(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        FreeTrialWeeklyAvailability::create([
            'day_of_week' => (int) $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'slot_duration_minutes' => (int) $data['slot_duration_minutes'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->bustLandingCache();

        return redirect()
            ->route('admin.free-trial-bookings.availability')
            ->with('success', 'تمت إضافة نافذة التوفر.');
    }

    public function updateAvailability(Request $request, FreeTrialWeeklyAvailability $window): RedirectResponse
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $window->update([
            'day_of_week' => (int) $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'slot_duration_minutes' => (int) $data['slot_duration_minutes'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->bustLandingCache();

        return redirect()
            ->route('admin.free-trial-bookings.availability')
            ->with('success', 'تم تحديث نافذة التوفر.');
    }

    public function destroyAvailability(FreeTrialWeeklyAvailability $window): RedirectResponse
    {
        $window->delete();
        $this->bustLandingCache();

        return redirect()
            ->route('admin.free-trial-bookings.availability')
            ->with('success', 'تم حذف نافذة التوفر.');
    }

    private function bustLandingCache(): void
    {
        foreach (['ar', 'en'] as $locale) {
            Cache::forget('landing.home.v5.'.$locale);
        }
    }
}
