<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteService;
use App\Services\SiteServiceImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class SiteServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.site-services');
    }

    public function index(Request $request)
    {
        $query = SiteService::query()->orderBy('sort_order')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%'.$s.'%')
                    ->orWhere('summary', 'like', '%'.$s.'%')
                    ->orWhere('slug', 'like', '%'.$s.'%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $services = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => SiteService::count(),
            'active' => SiteService::where('is_active', true)->count(),
            'inactive' => SiteService::where('is_active', false)->count(),
            'with_image' => SiteService::whereNotNull('image_path')->where('image_path', '!=', '')->count(),
        ];

        return view('admin.site-services.index', compact('services', 'stats'));
    }

    public function create()
    {
        return view('admin.site-services.create');
    }

    public function store(Request $request)
    {
        if (! $request->filled('slug')) {
            $request->merge(['slug' => null]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'summary' => 'nullable|string|max:2000',
            'body' => 'required|string|max:65000',
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:10240',
        ], [
            'name.required' => 'اسم الخدمة مطلوب',
            'body.required' => 'تفاصيل الخدمة مطلوبة',
            'slug.regex' => 'الرابط المختصر يقبل أحرف إنجليزية صغيرة وأرقام وشرطة فقط',
        ]);

        $slug = $this->resolveUniqueSlug(
            $validated['slug'] ?? null,
            $validated['name'],
            null
        );

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $imagePath = SiteServiceImageStorage::store($request->file('image'), null);
            } catch (Throwable $e) {
                report($e);

                return back()->withErrors([
                    'image' => 'تعذّر رفع الصورة. إن كنت تستخدم Cloudflare R2 فتأكد من AWS_* و AWS_ENDPOINT و AWS_URL ثم نفّذ php artisan config:clear.',
                ])->withInput();
            }
        }

        SiteService::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'image_path' => $imagePath,
            'summary' => $validated['summary'] ?? null,
            'body' => $validated['body'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.site-services.index')
            ->with('success', 'تم إضافة الخدمة بنجاح');
    }

    public function edit(SiteService $siteService)
    {
        return view('admin.site-services.edit', compact('siteService'));
    }

    public function update(Request $request, SiteService $siteService)
    {
        if (! $request->filled('slug')) {
            $request->merge(['slug' => null]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'summary' => 'nullable|string|max:2000',
            'body' => 'required|string|max:65000',
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:10240',
            'remove_image' => 'boolean',
        ], [
            'name.required' => 'اسم الخدمة مطلوب',
            'body.required' => 'تفاصيل الخدمة مطلوبة',
            'slug.regex' => 'الرابط المختصر يقبل أحرف إنجليزية صغيرة وأرقام وشرطة فقط',
        ]);

        $slug = $this->resolveUniqueSlug(
            $validated['slug'] ?? null,
            $validated['name'],
            $siteService->id
        );

        $newImagePath = $siteService->image_path;
        if ($request->hasFile('image')) {
            try {
                $newImagePath = SiteServiceImageStorage::store($request->file('image'), $siteService->image_path);
            } catch (Throwable $e) {
                report($e);

                return back()->withErrors([
                    'image' => 'تعذّر رفع الصورة. إن كنت تستخدم Cloudflare R2 فتأكد من AWS_* و AWS_ENDPOINT و AWS_URL ثم نفّذ php artisan config:clear.',
                ])->withInput();
            }
        } elseif ($request->boolean('remove_image')) {
            SiteServiceImageStorage::delete($siteService->image_path);
            $newImagePath = null;
        }

        $siteService->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'image_path' => $newImagePath,
            'summary' => $validated['summary'] ?? null,
            'body' => $validated['body'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.site-services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    public function destroy(SiteService $siteService)
    {
        $siteService->delete();

        return redirect()->route('admin.site-services.index')
            ->with('success', 'تم حذف الخدمة');
    }

    private function resolveUniqueSlug(?string $slugInput, string $name, ?int $exceptId): string
    {
        $base = $slugInput !== null && $slugInput !== ''
            ? Str::slug($slugInput, '-', 'en')
            : Str::slug($name, '-', app()->getLocale());

        if ($base === '') {
            $base = 'service';
        }

        $slug = $base;
        $n = 1;
        while (SiteService::where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }
}
