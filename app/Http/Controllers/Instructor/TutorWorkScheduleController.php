<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\TutoringGroupAvailabilityService;
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
        $data = $request->validate([
            'slots' => ['nullable', 'array'],
            'slots.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'slots.*.start_time' => ['required', 'date_format:H:i'],
            'slots.*.end_time' => ['required', 'date_format:H:i'],
            'slots.*.slot_duration_minutes' => ['nullable', 'integer', 'min:30', 'max:240'],
            'slots.*.applies_to' => ['nullable', 'in:individual,collective,both'],
        ]);

        TutoringGroupAvailabilityService::syncRules(
            $request->user()->id,
            $data['slots'] ?? []
        );

        return back()->with('success', 'تم حفظ جدول عمل المجموعات.');
    }
}
