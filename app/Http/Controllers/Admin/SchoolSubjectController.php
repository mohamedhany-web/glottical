<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolSubjectController extends Controller
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
        $subjects = SchoolSubject::query()
            ->withCount(['tutoringGroups as groups_count'])
            ->ordered()
            ->get();

        $stats = [
            'total' => $subjects->count(),
            'active' => $subjects->where('is_active', true)->count(),
            'inactive' => $subjects->where('is_active', false)->count(),
            'linked' => $subjects->sum('groups_count'),
        ];

        return view('admin.school-subjects.index', compact('subjects', 'stats'));
    }

    public function create(): View
    {
        return view('admin.school-subjects.form', [
            'subject' => new SchoolSubject([
                'is_active' => true,
                'icon' => 'book-open',
                'sort_order' => (int) SchoolSubject::query()->max('sort_order') + 1,
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: SchoolSubject::uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        SchoolSubject::create($data);

        return redirect()->route('admin.school-subjects.index')->with('success', 'تم إنشاء المادة.');
    }

    public function edit(SchoolSubject $schoolSubject): View
    {
        return view('admin.school-subjects.form', [
            'subject' => $schoolSubject,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, SchoolSubject $schoolSubject): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug']
            ? SchoolSubject::uniqueSlug($data['slug'], $schoolSubject->id)
            : SchoolSubject::uniqueSlug($data['name'], $schoolSubject->id);
        $data['is_active'] = $request->boolean('is_active');

        $schoolSubject->update($data);

        return redirect()->route('admin.school-subjects.index')->with('success', 'تم تحديث المادة.');
    }

    public function destroy(SchoolSubject $schoolSubject): RedirectResponse
    {
        $schoolSubject->delete();

        return redirect()->route('admin.school-subjects.index')->with('success', 'تم حذف المادة.');
    }

    public function toggleStatus(SchoolSubject $schoolSubject): RedirectResponse
    {
        $schoolSubject->update(['is_active' => ! $schoolSubject->is_active]);

        return back()->with('success', $schoolSubject->is_active ? 'تم تفعيل المادة.' : 'تم إيقاف المادة.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:64'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
