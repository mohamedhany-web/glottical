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
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $instructorId = (int) ($request->input('instructor_id') ?: ($instructors->first()?->id ?? 0));
        $rules = $instructorId
            ? TutoringGroupAvailabilityService::rulesForInstructor($instructorId)
            : collect();

        $dayLabels = TutoringGroupAvailabilityService::dayLabels();
        $grouped = collect($dayLabels)->map(function ($label, $day) use ($rules) {
            return [
                'day' => (int) $day,
                'label' => $label,
                'rules' => $rules->where('day_of_week', $day)->values(),
            ];
        })->values();

        $hourStart = 8;
        $hourEnd = 22;
        foreach ($rules as $rule) {
            $start = (int) substr($rule->startTimeString(), 0, 2);
            $end = (int) substr($rule->endTimeString(), 0, 2);
            $endMinute = (int) substr($rule->endTimeString(), 3, 2);
            if ($endMinute > 0) {
                $end++;
            }
            $hourStart = min($hourStart, max(0, $start));
            $hourEnd = max($hourEnd, min(24, $end));
        }
        if ($hourEnd <= $hourStart) {
            $hourEnd = $hourStart + 1;
        }

        $hours = range($hourStart, $hourEnd - 1);
        $totalMinutes = max(60, ($hourEnd - $hourStart) * 60);

        $gridDays = $grouped->map(function (array $day) use ($hourStart, $totalMinutes) {
            $blocks = collect($day['rules'])->map(function ($rule) use ($hourStart, $totalMinutes) {
                [$sh, $sm] = array_map('intval', explode(':', $rule->startTimeString()));
                [$eh, $em] = array_map('intval', explode(':', $rule->endTimeString()));
                $startMin = ($sh * 60 + $sm) - ($hourStart * 60);
                $endMin = ($eh * 60 + $em) - ($hourStart * 60);
                $startMin = max(0, $startMin);
                $endMin = min($totalMinutes, max($startMin + 15, $endMin));

                return [
                    'start' => $rule->startTimeString(),
                    'end' => $rule->endTimeString(),
                    'duration' => (int) $rule->slot_duration_minutes,
                    'applies_to' => (string) $rule->applies_to,
                    'applies_label' => match ($rule->applies_to) {
                        TutorWorkSchedule::APPLIES_INDIVIDUAL => 'فردي',
                        TutorWorkSchedule::APPLIES_COLLECTIVE => 'جماعي',
                        default => 'فردي + جماعي',
                    },
                    'note' => (string) ($rule->note ?? ''),
                    'top' => round(($startMin / $totalMinutes) * 100, 2),
                    'height' => round((($endMin - $startMin) / $totalMinutes) * 100, 2),
                ];
            })->values();

            return [
                'day' => $day['day'],
                'label' => $day['label'],
                'blocks' => $blocks,
                'count' => $blocks->count(),
            ];
        });

        $stats = [
            'instructors' => $instructors->count(),
            'windows' => TutorWorkSchedule::count(),
            'active' => TutorWorkSchedule::where('is_active', true)->count(),
            'selected' => $rules->count(),
        ];

        $selectedInstructor = $instructors->firstWhere('id', $instructorId);

        return view('admin.tutor-work-schedules.index', compact(
            'instructors',
            'instructorId',
            'selectedInstructor',
            'grouped',
            'gridDays',
            'dayLabels',
            'rules',
            'stats',
            'hourStart',
            'hourEnd',
            'hours',
            'totalMinutes'
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
