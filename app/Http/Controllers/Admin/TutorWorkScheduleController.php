<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutorWorkSchedule;
use App\Models\User;
use App\Services\TutoringGroupAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorWorkScheduleController extends Controller
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
        $instructors = User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $instructorId = (int) ($request->input('instructor_id') ?: ($instructors->first()?->id ?? 0));
        $rules = $instructorId
            ? TutoringGroupAvailabilityService::rulesForInstructor($instructorId)
            : collect();

        $dayLabels = TutoringGroupAvailabilityService::dayLabels();
        $grouped = collect($dayLabels)->map(function ($label, $day) use ($rules) {
            return [
                'day' => $day,
                'label' => $label,
                'rules' => $rules->where('day_of_week', $day)->values(),
            ];
        });

        $stats = [
            'instructors' => $instructors->count(),
            'windows' => TutorWorkSchedule::count(),
            'active' => TutorWorkSchedule::where('is_active', true)->count(),
            'selected' => $rules->count(),
        ];

        return view('admin.tutor-work-schedules.index', compact(
            'instructors',
            'instructorId',
            'grouped',
            'dayLabels',
            'rules',
            'stats'
        ));
    }

    public function sync(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'instructor_id' => ['required', 'exists:users,id'],
            'slots' => ['nullable', 'array'],
            'slots.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'slots.*.start_time' => ['required', 'date_format:H:i'],
            'slots.*.end_time' => ['required', 'date_format:H:i'],
            'slots.*.slot_duration_minutes' => ['nullable', 'integer', 'min:30', 'max:240'],
            'slots.*.applies_to' => ['nullable', 'in:individual,collective,both'],
            'slots.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        TutoringGroupAvailabilityService::syncRules(
            (int) $data['instructor_id'],
            $data['slots'] ?? []
        );

        return redirect()
            ->route('admin.tutor-work-schedules.index', ['instructor_id' => $data['instructor_id']])
            ->with('success', 'تم حفظ جدول عمل المدرب.');
    }
}
