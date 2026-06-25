<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use App\Models\OneToOneSession;
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
        $events = ConsultationRequest::calendarItemsForUser(
            $user,
            now()->subMonths(1),
            now()->addMonths(3),
            'instructor'
        )->merge(OneToOneSession::calendarItemsForUser(
            $user,
            now()->subMonths(1),
            now()->addMonths(3),
            'instructor'
        ));

        $stats = [
            'total' => $events->count(),
            'upcoming' => $events->where('start_date', '>=', now())->count(),
        ];

        return view('instructor.calendar.index', compact('events', 'stats'));
    }

    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $start = $request->get('start');
        $end = $request->get('end');

        $events = ConsultationRequest::calendarItemsForUser($user, $start, $end, 'instructor')
            ->merge(OneToOneSession::calendarItemsForUser($user, $start, $end, 'instructor'));

        $calendarEvents = $events->map(function ($event) {
            return [
                'id' => $event->calendar_id,
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date ? $event->end_date->toIso8601String() : null,
                'allDay' => $event->is_all_day ?? false,
                'color' => $event->color ?? '#059669',
                'type' => $event->type,
                'url' => $event->url ?? null,
                'description' => $event->description ?? null,
                'extendedProps' => [
                    'priority' => $event->priority ?? 'high',
                    'location' => $event->location ?? null,
                ],
            ];
        });

        return response()->json($calendarEvents);
    }
}
