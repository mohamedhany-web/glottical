<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\TutoringGroupAvailabilityService;
use App\Support\AppTimezone;
use App\Support\WeeklyScheduleTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorWorkScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $instructorId = $request->user()->id;
        $rules = TutoringGroupAvailabilityService::rulesForInstructor($instructorId);
        $dayLabels = TutoringGroupAvailabilityService::dayLabels();

        $grouped = collect($dayLabels)->map(function ($label, $day) use ($rules) {
            return [
                'day' => $day,
                'label' => $label,
                'rules' => $rules->where('day_of_week', $day)->values(),
            ];
        });

        return view('instructor.tutor-work-schedule.index', [
            'grouped' => $grouped,
            'dayLabels' => $dayLabels,
            'rules' => $rules,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(array_merge(
            WeeklyScheduleTime::slotTimeRules(240, true),
            ['timezone' => AppTimezone::inputRules()]
        ));

        AppTimezone::persistForUser(
            $request->user(),
            is_string($data['timezone'] ?? null) ? $data['timezone'] : null
        );

        TutoringGroupAvailabilityService::syncRules(
            $request->user()->id,
            $data['slots'] ?? []
        );

        return back()->with('success', 'تم حفظ جدول عمل المجموعات.');
    }
}
