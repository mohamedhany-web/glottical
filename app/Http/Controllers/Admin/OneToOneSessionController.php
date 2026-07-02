<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OneToOneSession;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OneToOneSessionController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->get('status', 'all');
        $instructorId = (int) $request->get('instructor_id', 0);

        $query = OneToOneSession::query()
            ->with(['student', 'instructor', 'course', 'classroomMeeting', 'bookedBy'])
            ->orderByDesc('created_at');

        if (in_array($status, array_keys(OneToOneSession::statusLabels()), true)) {
            $query->where('status', $status);
        }
        if ($instructorId > 0) {
            $query->where('instructor_id', $instructorId);
        }

        $sessions = $query->paginate(30)->withQueryString();

        $stats = [
            'pending' => OneToOneSession::where('status', OneToOneSession::STATUS_PENDING)->count(),
            'scheduled' => OneToOneSession::where('status', OneToOneSession::STATUS_SCHEDULED)->count(),
            'completed' => OneToOneSession::where('status', OneToOneSession::STATUS_COMPLETED)->count(),
        ];

        $instructors = User::query()
            ->whereIn('id', OneToOneSession::query()->distinct()->pluck('instructor_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.one-to-one-sessions.index', compact('sessions', 'stats', 'status', 'instructors', 'instructorId'));
    }

    public function show(OneToOneSession $oneToOneSession): View
    {
        $oneToOneSession->load(['student', 'instructor', 'course', 'classroomMeeting', 'enrollment', 'bookedBy']);

        $availability = OneToOneAvailabilityService::rulesForInstructor((int) $oneToOneSession->instructor_id);
        $upcomingSlots = OneToOneAvailabilityService::availableSlots(
            (int) $oneToOneSession->instructor_id,
            now(),
            now()->addWeeks(2),
            (int) ($oneToOneSession->duration_minutes ?? 60),
            $oneToOneSession->id
        );

        return view('admin.one-to-one-sessions.show', [
            'session' => $oneToOneSession,
            'availability' => $availability,
            'upcomingSlots' => $upcomingSlots,
            'dayLabels' => OneToOneAvailabilityService::dayLabels(),
        ]);
    }
}
