<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TutoringGroup;
use App\Services\TutoringGroupAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class GroupsController extends Controller
{
    public function index(): View
    {
        $groupCourses = $this->collectiveQuery()->limit(8)->get();
        $oneToOneCourses = $this->individualQuery()->limit(8)->get();
        $groupCount = $this->collectiveQuery()->count();
        $oneToOneCount = $this->individualQuery()->count();

        return view('public.groups', compact(
            'groupCourses',
            'oneToOneCourses',
            'groupCount',
            'oneToOneCount'
        ));
    }

    public function groupCourses(): View
    {
        $groups = $this->collectiveQuery()->paginate(12);
        $groupCount = $groups->total();

        return view('public.groups-courses', [
            'groups' => $groups,
            'courses' => $groups, // backward-compatible alias for any leftover references
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
            ->with('instructor:id,name')
            ->firstOrFail();

        $slots = TutoringGroupAvailabilityService::availableSlots($group);
        $slotsByDate = $slots->groupBy('date');

        return view('public.groups-show', compact('group', 'slots', 'slotsByDate'));
    }

    public function book(Request $request, string $slug): RedirectResponse
    {
        $group = TutoringGroup::query()->active()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'starts_at' => ['required', 'date'],
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
            ->with('instructor:id,name')
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
