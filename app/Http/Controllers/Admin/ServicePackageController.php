<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\ServicePackage;
use App\Models\TutoringGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ServicePackageController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.packages') && ! $user->hasPermission('manage.tutoring-groups'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        $packages = ServicePackage::query()
            ->with([
                'tutoringGroup:id,title',
                'academicYear:id,name',
                'academicSubject:id,name',
            ])
            ->withCount('entitlements')
            ->ordered()
            ->get();

        $stats = [
            'total' => $packages->count(),
            'active' => $packages->where('is_active', true)->count(),
            'global' => $packages->where('scope', ServicePackage::SCOPE_GLOBAL)->count(),
            'sold' => $packages->sum('entitlements_count'),
        ];

        return view('admin.service-packages.index', compact('packages', 'stats'));
    }

    public function create(): View
    {
        return view('admin.service-packages.form', $this->formData(new ServicePackage([
            'scope' => ServicePackage::SCOPE_GLOBAL,
            'units_count' => 8,
            'session_minutes' => 60,
            'duration_days' => 60,
            'currency' => 'USD',
            'is_active' => true,
            'sort_order' => (int) ServicePackage::query()->max('sort_order') + 1,
        ]), 'create'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data = $this->normalizeSchoolLinks($data);
        $data = $this->normalizeCommercialPlan($data, $request);
        $data['currency'] = 'USD';
        $data['slug'] = $data['slug'] ?: ServicePackage::uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');

        ServicePackage::create($data);

        return redirect()->route('admin.service-packages.index')->with('success', 'تم إنشاء باقة الخدمات.');
    }

    public function edit(ServicePackage $servicePackage): View
    {
        return view('admin.service-packages.form', $this->formData($servicePackage, 'edit'));
    }

    public function update(Request $request, ServicePackage $servicePackage): RedirectResponse
    {
        $data = $this->validated($request, $servicePackage->id);
        $data = $this->normalizeSchoolLinks($data);
        $data = $this->normalizeCommercialPlan($data, $request);
        $data['currency'] = 'USD';
        $data['slug'] = $data['slug']
            ? ServicePackage::uniqueSlug($data['slug'], $servicePackage->id)
            : ServicePackage::uniqueSlug($data['name'], $servicePackage->id);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $servicePackage->update($data);

        return redirect()->route('admin.service-packages.index')->with('success', 'تم تحديث الباقة.');
    }

    public function destroy(ServicePackage $servicePackage): RedirectResponse
    {
        if ($servicePackage->entitlements()->exists()) {
            return back()->with('error', 'لا يمكن حذف باقة لها أرصدة طلاب. أوقفها بدل الحذف.');
        }
        $servicePackage->delete();

        return redirect()->route('admin.service-packages.index')->with('success', 'تم حذف الباقة.');
    }

    public function toggleStatus(ServicePackage $servicePackage): RedirectResponse
    {
        $servicePackage->update(['is_active' => ! $servicePackage->is_active]);

        return back()->with('success', $servicePackage->is_active ? 'تم تفعيل الباقة.' : 'تم إيقاف الباقة.');
    }

    protected function formData(ServicePackage $package, string $mode): array
    {
        return [
            'package' => $package,
            'groups' => TutoringGroup::query()->orderBy('title')->get(['id', 'title', 'type', 'academic_year_id', 'academic_subject_id']),
            'years' => Schema::hasTable('academic_years')
                ? AcademicYear::query()->ordered()->get(['id', 'name', 'level_number'])
                : collect(),
            'subjects' => Schema::hasTable('academic_subjects')
                ? AcademicSubject::query()->ordered()->get(['id', 'name', 'academic_year_id'])
                : collect(),
            'mode' => $mode,
        ];
    }

    protected function normalizeSchoolLinks(array $data): array
    {
        foreach (['tutoring_group_id', 'academic_year_id', 'academic_subject_id'] as $key) {
            if (empty($data[$key])) {
                $data[$key] = null;
            } else {
                $data[$key] = (int) $data[$key];
            }
        }

        if ($data['tutoring_group_id']) {
            $group = TutoringGroup::query()->find($data['tutoring_group_id']);
            if ($group) {
                $data['academic_year_id'] = $data['academic_year_id'] ?: $group->academic_year_id;
                $data['academic_subject_id'] = $data['academic_subject_id'] ?: $group->academic_subject_id;
            }
        }

        if ($data['academic_subject_id'] && ! $data['academic_year_id']) {
            $subject = AcademicSubject::query()->find($data['academic_subject_id']);
            if ($subject?->academic_year_id) {
                $data['academic_year_id'] = (int) $subject->academic_year_id;
            }
        }

        return $data;
    }

    protected function normalizeCommercialPlan(array $data, Request $request): array
    {
        foreach (['plan_type', 'term_months'] as $key) {
            if (empty($data[$key])) {
                $data[$key] = null;
            }
        }

        $data['weekly_group_sessions'] = (int) ($data['weekly_group_sessions'] ?? 0);
        $data['weekly_private_sessions'] = (int) ($data['weekly_private_sessions'] ?? 0);
        $data['includes_community'] = $request->boolean('includes_community');
        $data['includes_libraries'] = $request->boolean('includes_libraries');

        if (! empty($data['features_text'])) {
            $data['features'] = collect(preg_split('/\r\n|\r|\n/', (string) $data['features_text']))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        } else {
            $data['features'] = null;
        }
        unset($data['features_text']);

        if (! empty($data['gifts_text'])) {
            $data['gifts'] = collect(preg_split('/\r\n|\r|\n/', (string) $data['gifts_text']))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        } else {
            $data['gifts'] = null;
        }
        unset($data['gifts_text']);

        if ($data['plan_type'] && $data['term_months']) {
            $weekly = $data['weekly_group_sessions'] + $data['weekly_private_sessions'];
            if ($weekly > 0) {
                $data['units_count'] = $weekly * 4 * (int) $data['term_months'];
            }
            if (empty($data['duration_days'])) {
                $data['duration_days'] = (int) $data['term_months'] * 30;
            }

            $data['scope'] = match ($data['plan_type']) {
                ServicePackage::PLAN_SCHOOL => ServicePackage::SCOPE_TUTORING_COLLECTIVE,
                ServicePackage::PLAN_PRIVATE => ServicePackage::SCOPE_PRIVATE_LESSONS,
                ServicePackage::PLAN_PREMIER => ServicePackage::SCOPE_GLOBAL,
                default => $data['scope'],
            };
        }

        return $data;
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:5000'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:64'],
            'scope' => ['required', 'in:global,tutoring_individual,tutoring_collective,private_lessons'],
            'plan_type' => ['nullable', 'in:school,private,premier'],
            'term_months' => ['nullable', 'integer', 'in:1,3,6'],
            'weekly_group_sessions' => ['nullable', 'integer', 'min:0', 'max:14'],
            'weekly_private_sessions' => ['nullable', 'integer', 'min:0', 'max:14'],
            'features_text' => ['nullable', 'string', 'max:5000'],
            'gifts_text' => ['nullable', 'string', 'max:2000'],
            'tutoring_group_id' => ['nullable', 'exists:tutoring_groups,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'academic_subject_id' => ['nullable', 'exists:academic_subjects,id'],
            'units_count' => ['required', 'integer', 'min:1', 'max:500'],
            'session_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:730'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
