<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutoringGroupCohortController extends Controller
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

    public function index(TutoringGroup $tutoringGroup): View
    {
        abort_unless($tutoringGroup->isCollective(), 404);

        $cohorts = $tutoringGroup->cohorts()->orderByDesc('starts_at')->paginate(20);

        return view('admin.tutoring-groups.cohorts.index', [
            'group' => $tutoringGroup,
            'cohorts' => $cohorts,
            'type' => $tutoringGroup->type,
        ]);
    }

    public function create(TutoringGroup $tutoringGroup): View
    {
        abort_unless($tutoringGroup->isCollective(), 404);

        return view('admin.tutoring-groups.cohorts.form', [
            'group' => $tutoringGroup,
            'cohort' => new TutoringGroupCohort([
                'tutoring_group_id' => $tutoringGroup->id,
                'capacity' => $tutoringGroup->capacity ?: 8,
                'min_enrollment' => 3,
                'timezone' => 'Africa/Cairo',
                'status' => TutoringGroupCohort::STATUS_OPEN,
                'is_visible' => true,
                'study_days' => [6, 2],
                'study_time' => '18:00',
                'sessions_count' => $tutoringGroup->sessions_per_month ?: 8,
                'session_duration_minutes' => $tutoringGroup->duration_minutes ?: 60,
            ]),
            'mode' => 'create',
            'type' => $tutoringGroup->type,
        ]);
    }

    public function store(Request $request, TutoringGroup $tutoringGroup): RedirectResponse
    {
        abort_unless($tutoringGroup->isCollective(), 404);
        $data = $this->validated($request);
        $data['tutoring_group_id'] = $tutoringGroup->id;
        $data['slug'] = TutoringGroupCohort::uniqueSlug($tutoringGroup->id, $data['slug'] ?: $data['title']);
        $data['is_visible'] = $request->boolean('is_visible', true);
        $data['study_days'] = array_map('intval', $data['study_days'] ?? []);

        TutoringGroupCohort::create($data);

        $cohort = TutoringGroupCohort::query()
            ->where('tutoring_group_id', $tutoringGroup->id)
            ->latest('id')
            ->first();

        if ($cohort
            && filled($cohort->study_days)
            && filled($cohort->study_time)
            && $cohort->starts_at) {
            try {
                \App\Services\TutoringClassService::generateSchedule($cohort);
                \App\Services\TutoringClassService::ensureAllMeetings($cohort->fresh());
            } catch (\Throwable) {
                // يمكن التوليد لاحقاً من صفحة الفصل
            }
        }

        return redirect()
            ->route($cohort ? 'admin.tutoring-groups.classes.show' : 'admin.tutoring-groups.cohorts.index', $cohort ? [$tutoringGroup, $cohort] : $tutoringGroup)
            ->with('success', 'تم إنشاء الدفعة'.($cohort && $cohort->classSessions()->exists() ? ' وتوليد جدول الحصص.' : '.'));
    }

    public function edit(TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): View
    {
        abort_unless($tutoringGroup->isCollective() && (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);

        return view('admin.tutoring-groups.cohorts.form', [
            'group' => $tutoringGroup,
            'cohort' => $cohort,
            'mode' => 'edit',
            'type' => $tutoringGroup->type,
        ]);
    }

    public function update(Request $request, TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): RedirectResponse
    {
        abort_unless($tutoringGroup->isCollective() && (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);
        $data = $this->validated($request);
        $data['slug'] = TutoringGroupCohort::uniqueSlug($tutoringGroup->id, $data['slug'] ?: $data['title'], $cohort->id);
        $data['is_visible'] = $request->boolean('is_visible', true);
        $data['study_days'] = array_map('intval', $data['study_days'] ?? []);

        $cohort->update($data);

        return redirect()
            ->route('admin.tutoring-groups.cohorts.index', $tutoringGroup)
            ->with('success', 'تم تحديث الدفعة.');
    }

    public function destroy(TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): RedirectResponse
    {
        abort_unless((int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);
        $cohort->delete();

        return redirect()
            ->route('admin.tutoring-groups.cohorts.index', $tutoringGroup)
            ->with('success', 'تم حذف الدفعة.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'study_days' => ['nullable', 'array'],
            'study_days.*' => ['integer', 'min:1', 'max:7'],
            'study_time' => ['nullable', 'date_format:H:i'],
            'sessions_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'session_duration_minutes' => ['nullable', 'integer', 'min:15', 'max:300'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'min_enrollment' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:open,full,closed,postponed,completed'],
            'postponed_to' => ['nullable', 'date'],
            'whatsapp_group_url' => ['nullable', 'url', 'max:500'],
            'enrollment_closes_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
