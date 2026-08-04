<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SchoolSubject;
use App\Models\SchoolYear;
use App\Models\TutoringGroup;
use App\Services\TutoringGroupAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use InvalidArgumentException;

class GroupsController extends Controller
{
    public function index(): View
    {
        $schoolYears = collect();
        $schoolSubjects = collect();

        if (Schema::hasTable('school_years')) {
            $schoolYears = SchoolYear::query()
                ->active()
                ->ordered()
                ->withCount([
                    'tutoringGroups as open_classes_count' => fn ($q) => $q->active()->collective(),
                ])
                ->get();
        }

        if (Schema::hasTable('school_subjects')) {
            $schoolSubjects = SchoolSubject::query()->active()->ordered()->get();
        }

        return view('public.groups', compact('schoolYears', 'schoolSubjects'));
    }

    public function year(string $slug): View
    {
        abort_unless(Schema::hasTable('school_years'), 404);

        $year = SchoolYear::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $classes = TutoringGroup::query()
            ->active()
            ->collective()
            ->where('school_year_id', $year->id)
            ->with([
                'instructor:id,name',
                'schoolSubject:id,name',
                'cohorts' => fn ($q) => $q->visible()->orderByDesc('starts_at'),
            ])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return view('public.school-year', compact('year', 'classes'));
    }

    public function groupCourses(): View
    {
        $groups = $this->collectiveQuery()->paginate(12);
        $groupCount = $groups->total();

        return view('public.groups-courses', [
            'groups' => $groups,
            'courses' => $groups,
            'groupCount' => $groupCount,
        ]);
    }

    public function oneToOneCourses(): View
    {
        $groups = $this->individualQuery()->paginate(12);
        $oneToOneCount = $groups->total();

        return view('public.groups-one-to-one', [
            'groups' => $groups,
            'courses' => $groups,
            'oneToOneCount' => $oneToOneCount,
        ]);
    }

    public function show(string $slug): View
    {
        $group = TutoringGroup::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'instructor:id,name',
                'schoolYear:id,name,slug,level_number',
                'schoolSubject:id,name',
                'cohorts' => fn ($q) => $q->visible()->orderByDesc('starts_at'),
                'packages' => fn ($q) => $q->active()->orderBy('sort_order')->orderBy('duration_months'),
            ])
            ->firstOrFail();

        $slots = TutoringGroupAvailabilityService::availableSlots($group);
        $slotsByDate = $slots->groupBy('date');
        $cohorts = $group->cohorts;
        $packages = $group->packages;

        return view('public.groups-show', compact('group', 'slots', 'slotsByDate', 'cohorts', 'packages'));
    }

    public function book(Request $request, string $slug): RedirectResponse
    {
        $group = TutoringGroup::query()->active()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'starts_at' => ['required', 'date'],
            'cohort_id' => ['nullable', 'integer', 'exists:tutoring_group_cohorts,id'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:40'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'student_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        if (! $user) {
            $data['guest_name'] = $data['guest_name'] ?? null;
            if (empty($data['guest_name'])) {
                return back()->withInput()->withErrors(['guest_name' => 'الاسم مطلوب لغير المسجّلين.']);
            }
        } else {
            $data['guest_name'] = $data['guest_name'] ?? $user->name;
            $data['guest_email'] = $data['guest_email'] ?? $user->email;
            $data['guest_phone'] = $data['guest_phone'] ?? ($user->phone ?? null);
        }

        try {
            TutoringGroupAvailabilityService::book($group, $data, $user?->id);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['starts_at' => $e->getMessage()]);
        }

        return redirect()
            ->route('public.groups.show', $group->slug)
            ->with('success', 'تم إرسال طلب الحجز بنجاح. سنتواصل معك بعد المراجعة.');
    }

    protected function baseQuery()
    {
        return TutoringGroup::query()
            ->active()
            ->with(['instructor:id,name', 'schoolYear:id,name', 'schoolSubject:id,name'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }

    protected function collectiveQuery()
    {
        return $this->baseQuery()->collective();
    }

    protected function individualQuery()
    {
        return $this->baseQuery()->individual();
    }
}
