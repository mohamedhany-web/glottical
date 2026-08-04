<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolYearController extends Controller
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

    public function index(): View
    {
        $years = SchoolYear::query()
            ->withCount(['tutoringGroups as groups_count'])
            ->ordered()
            ->get();

        $stats = [
            'total' => $years->count(),
            'active' => $years->where('is_active', true)->count(),
            'inactive' => $years->where('is_active', false)->count(),
            'linked' => $years->sum('groups_count'),
        ];

        return view('admin.school-years.index', compact('years', 'stats'));
    }

    public function create(): View
    {
        return view('admin.school-years.form', [
            'year' => new SchoolYear([
                'is_active' => true,
                'sort_order' => (int) SchoolYear::query()->max('sort_order') + 1,
                'level_number' => (int) SchoolYear::query()->max('level_number') + 1,
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: SchoolYear::uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('school-years', 'public');
        }
        unset($data['image']);

        SchoolYear::create($data);

        return redirect()->route('admin.school-years.index')->with('success', 'تم إنشاء السنة الدراسية.');
    }

    public function edit(SchoolYear $schoolYear): View
    {
        return view('admin.school-years.form', [
            'year' => $schoolYear,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, SchoolYear $schoolYear): RedirectResponse
    {
        $data = $this->validated($request, $schoolYear->id);
        $data['slug'] = $data['slug']
            ? SchoolYear::uniqueSlug($data['slug'], $schoolYear->id)
            : SchoolYear::uniqueSlug($data['name'], $schoolYear->id);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($schoolYear->image_path && ! str_starts_with($schoolYear->image_path, 'http')) {
                Storage::disk('public')->delete($schoolYear->image_path);
            }
            $data['image_path'] = $request->file('image')->store('school-years', 'public');
        }
        unset($data['image']);

        $schoolYear->update($data);

        return redirect()->route('admin.school-years.index')->with('success', 'تم تحديث السنة الدراسية.');
    }

    public function destroy(SchoolYear $schoolYear): RedirectResponse
    {
        if ($schoolYear->image_path && ! str_starts_with($schoolYear->image_path, 'http')) {
            Storage::disk('public')->delete($schoolYear->image_path);
        }
        $schoolYear->delete();

        return redirect()->route('admin.school-years.index')->with('success', 'تم حذف السنة الدراسية.');
    }

    public function toggleStatus(SchoolYear $schoolYear): RedirectResponse
    {
        $schoolYear->update(['is_active' => ! $schoolYear->is_active]);

        return back()->with('success', $schoolYear->is_active ? 'تم تفعيل السنة.' : 'تم إيقاف السنة.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'level_number' => [
                'required',
                'integer',
                'min:1',
                'max:20',
                Rule::unique('school_years', 'level_number')->ignore($ignoreId),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);
    }
}
