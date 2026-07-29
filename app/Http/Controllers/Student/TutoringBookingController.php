<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TutoringGroupBooking;
use App\Services\TutoringGroupCheckoutService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class TutoringBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = TutoringGroupBooking::query()
            ->where('user_id', $request->user()->id)
            ->with(['tutoringGroup:id,title,slug,type', 'instructor:id,name', 'classroomMeeting:id,code,scheduled_for', 'cohort:id,title'])
            ->orderByDesc('starts_at')
            ->paginate(15);

        $upcoming = TutoringGroupBooking::query()
            ->where('user_id', $request->user()->id)
            ->where('status', TutoringGroupBooking::STATUS_CONFIRMED)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->first();

        return view('student.tutoring-bookings.index', compact('bookings', 'upcoming'));
    }

    public function show(Request $request, TutoringGroupBooking $booking): View
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
        $booking->load(['tutoringGroup', 'instructor', 'classroomMeeting', 'cohort', 'package', 'subscription']);

        return view('student.tutoring-bookings.show', compact('booking'));
    }

    public function bookFromSubscription(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subscription_id' => ['required', 'integer', 'exists:student_tutoring_subscriptions,id'],
            'starts_at' => ['required', 'date'],
        ]);

        $subscription = \App\Models\StudentTutoringSubscription::query()
            ->where('id', $data['subscription_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $booking = TutoringGroupCheckoutService::bookFromSubscription(
                $subscription,
                Carbon::parse($data['starts_at'])
            );
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['starts_at' => $e->getMessage()]);
        }

        return redirect()
            ->route('student.tutoring-bookings.show', $booking)
            ->with('success', 'تم تأكيد الحجز وإنشاء غرفة Live.');
    }
}
