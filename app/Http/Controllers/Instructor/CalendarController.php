<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\TeachingCalendarService;
use App\Support\AppTimezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor|teacher']);
    }

    public function index()
    {
        $user = Auth::user();
        $events = TeachingCalendarService::forInstructor(
            $user,
            now()->subMonths(1),
            now()->addMonths(3)
        );
        $viewerTz = AppTimezone::forUser($user);

        $stats = [
            'total' => $events->count(),
            'upcoming' => $events->filter(fn ($event) => ($event->start_date ?? now()) >= now())->count(),
        ];

        return view('instructor.calendar.index', compact('events', 'stats', 'viewerTz'));
    }

    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $events = TeachingCalendarService::forInstructor(
            $user,
            $request->get('start'),
            $request->get('end')
        );

        return response()->json(TeachingCalendarService::toFullCalendar($events));
    }
}
