<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentTutoringSubscription;
use App\Services\TutoringGroupAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutoringSubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $subscriptions = StudentTutoringSubscription::query()
            ->where('user_id', $request->user()->id)
            ->with(['tutoringGroup:id,title,slug,type', 'package'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('student.tutoring-subscriptions.index', compact('subscriptions'));
    }

    public function show(Request $request, StudentTutoringSubscription $subscription): View
    {
        abort_unless((int) $subscription->user_id === (int) $request->user()->id, 403);
        $subscription->load(['tutoringGroup.instructor:id,name', 'package', 'bookings' => fn ($q) => $q->orderByDesc('starts_at')->limit(20)]);

        $slots = collect();
        if ($subscription->hasSessionsLeft() && $subscription->tutoringGroup) {
            $slots = TutoringGroupAvailabilityService::availableSlots($subscription->tutoringGroup)->take(40);
        }

        return view('student.tutoring-subscriptions.show', compact('subscription', 'slots'));
    }
}
