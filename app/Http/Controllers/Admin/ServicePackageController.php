<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\TutoringGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->with('tutoringGroup:id,title')
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
        return view('admin.service-packages.form', [
            'package' => new ServicePackage([
                'scope' => ServicePackage::SCOPE_GLOBAL,
                'units_count' => 8,
                'session_minutes' => 60,
                'duration_days' => 60,
                'currency' => 'USD',
                'is_active' => true,
                'sort_order' => (int) ServicePackage::query()->max('sort_order') + 1,
            ]),
            'groups' => TutoringGroup::query()->orderBy('title')->get(['id', 'title', 'type']),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['currency'] = 'USD';
        $data['slug'] = $data['slug'] ?: ServicePackage::uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        if (empty($data['tutoring_group_id'])) {
            $data['tutoring_group_id'] = null;
        }

        ServicePackage::create($data);

        return redirect()->route('admin.service-packages.index')->with('success', 'تم إنشاء باقة الخدمات.');
    }

    public function edit(ServicePackage $servicePackage): View
    {
        return view('admin.service-packages.form', [
            'package' => $servicePackage,
            'groups' => TutoringGroup::query()->orderBy('title')->get(['id', 'title', 'type']),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, ServicePackage $servicePackage): RedirectResponse
    {
        $data = $this->validated($request, $servicePackage->id);
        $data['currency'] = 'USD';
        $data['slug'] = $data['slug']
            ? ServicePackage::uniqueSlug($data['slug'], $servicePackage->id)
            : ServicePackage::uniqueSlug($data['name'], $servicePackage->id);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        if (empty($data['tutoring_group_id'])) {
            $data['tutoring_group_id'] = null;
        }

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

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:5000'],
            'badge' => ['nullable', 'string', 'max:64'],
            'scope' => ['required', 'in:global,tutoring_individual,tutoring_collective,private_lessons'],
            'tutoring_group_id' => ['nullable', 'exists:tutoring_groups,id'],
            'units_count' => ['required', 'integer', 'min:1', 'max:500'],
            'session_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:730'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
