<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Services\TutoringGroupOrchestrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutoringGroupBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.tutoring-groups'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = TutoringGroupBooking::query()
            ->with([
                'tutoringGroup:id,title,type,slug,school_year_id',
                'tutoringGroup.schoolYear:id,name,level_number',
                'instructor:id,name',
                'user:id,name,email,phone',
                'cohort:id,title',
                'classroomMeeting:id,code',
            ]);

        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('guest_name', 'like', "%{$s}%")
                    ->orWhere('guest_email', 'like', "%{$s}%")
                    ->orWhere('guest_phone', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
                    ->orWhereHas('tutoringGroup', fn ($g) => $g->where('title', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status') && in_array($request->status, [
            TutoringGroupBooking::STATUS_PENDING,
            TutoringGroupBooking::STATUS_CONFIRMED,
            TutoringGroupBooking::STATUS_CANCELLED,
            TutoringGroupBooking::STATUS_COMPLETED,
        ], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && in_array($request->type, [
            TutoringGroup::TYPE_INDIVIDUAL,
            TutoringGroup::TYPE_COLLECTIVE,
        ], true)) {
            $query->whereHas('tutoringGroup', fn ($q) => $q->where('type', $request->type));
        }

        if ($request->filled('from')) {
            $query->whereDate('starts_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('starts_at', '<=', $request->input('to'));
        }

        $bookings = $query->orderByDesc('starts_at')->paginate(20)->withQueryString();

        $stats = [
            'total' => TutoringGroupBooking::count(),
            'pending' => TutoringGroupBooking::where('status', TutoringGroupBooking::STATUS_PENDING)->count(),
            'confirmed' => TutoringGroupBooking::where('status', TutoringGroupBooking::STATUS_CONFIRMED)->count(),
            'upcoming' => TutoringGroupBooking::where('status', TutoringGroupBooking::STATUS_CONFIRMED)
                ->where('starts_at', '>=', now())->count(),
        ];

        return view('admin.tutoring-group-bookings.index', compact('bookings', 'stats'));
    }

    public function show(TutoringGroupBooking $tutoringGroupBooking): View
    {
        $tutoringGroupBooking->load([
            'tutoringGroup',
            'instructor:id,name,email,phone',
            'user:id,name,email,phone',
            'cohort',
            'package',
            'classroomMeeting',
            'order',
        ]);

        return view('admin.tutoring-group-bookings.show', [
            'booking' => $tutoringGroupBooking,
        ]);
    }

    public function updateStatus(Request $request, TutoringGroupBooking $tutoringGroupBooking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $tutoringGroupBooking->update([
            'admin_notes' => $data['admin_notes'] ?? $tutoringGroupBooking->admin_notes,
        ]);

        try {
            match ($data['status']) {
                TutoringGroupBooking::STATUS_CONFIRMED => TutoringGroupOrchestrationService::confirmBooking($tutoringGroupBooking),
                TutoringGroupBooking::STATUS_CANCELLED => TutoringGroupOrchestrationService::cancelBooking($tutoringGroupBooking),
                TutoringGroupBooking::STATUS_COMPLETED => TutoringGroupOrchestrationService::completeBooking($tutoringGroupBooking),
                default => $tutoringGroupBooking->update(['status' => $data['status']]),
            };
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.tutoring-group-bookings.show', $tutoringGroupBooking)
            ->with('success', 'تم تحديث حالة الحجز'.($data['status'] === 'confirmed' ? ' وإنشاء غرفة Live.' : '.'));
    }

    public function destroy(TutoringGroupBooking $tutoringGroupBooking): RedirectResponse
    {
        $tutoringGroupBooking->delete();

        return redirect()
            ->route('admin.tutoring-group-bookings.index')
            ->with('success', 'تم حذف الحجز.');
    }
}
