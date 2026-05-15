<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\HomepageSlider;
use App\Services\HomepageSliderImageStorage;
use Illuminate\Http\Request;
use Throwable;

class HomepageSliderController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $u = $request->user();
            if ($u && ($u->hasPermission('manage.homepage-sliders') || $u->hasPermission('manage.site-services'))) {
                return $next($request);
            }
            abort(403);
        });
    }

    public function index(Request $request)
    {
        $query = HomepageSlider::query()
            ->with(['course:id,title,thumbnail,is_active', 'academicYear:id,name,thumbnail,is_active'])
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        $rows = $query->get();

        return view('admin.homepage-sliders.index', compact('rows'));
    }

    public function create()
    {
        return view('admin.homepage-sliders.create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $validated = $this->validateSlide($request);
        $imagePath = $this->handleImageUpload($request, null);

        $payload = collect($validated)->except(['image', 'remove_image'])->all();
        HomepageSlider::create(array_merge($payload, [
            'image_path' => $imagePath,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? ((int) (HomepageSlider::max('sort_order') ?? 0) + 1),
        ]));

        return redirect()->route('admin.homepage-sliders.index')
            ->with('success', 'تم إضافة السلايدر بنجاح.');
    }

    public function edit(HomepageSlider $homepageSlider)
    {
        $homepageSlider->load(['course', 'academicYear']);

        return view('admin.homepage-sliders.edit', array_merge(
            ['slide' => $homepageSlider],
            $this->formOptions()
        ));
    }

    public function update(Request $request, HomepageSlider $homepageSlider)
    {
        $validated = $this->validateSlide($request, $homepageSlider);
        $imagePath = $this->handleImageUpload($request, $homepageSlider->image_path, $homepageSlider);

        if ($request->boolean('remove_image') && $homepageSlider->image_path) {
            HomepageSliderImageStorage::delete($homepageSlider->image_path);
            $imagePath = null;
        }

        $payload = collect($validated)->except(['image', 'remove_image'])->all();
        $homepageSlider->update(array_merge($payload, [
            'image_path' => $imagePath,
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('admin.homepage-sliders.index')
            ->with('success', 'تم تحديث السلايدر بنجاح.');
    }

    public function destroy(HomepageSlider $homepageSlider)
    {
        $homepageSlider->delete();

        return redirect()->route('admin.homepage-sliders.index')
            ->with('success', 'تم حذف السلايدر.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:homepage_sliders,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            HomepageSlider::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب السلايدرات.',
        ]);
    }

    public function toggleActive(HomepageSlider $homepageSlider)
    {
        $homepageSlider->update(['is_active' => ! $homepageSlider->is_active]);

        return redirect()->back()->with('success', $homepageSlider->is_active ? 'تم تفعيل السلايدر.' : 'تم إيقاف السلايدر.');
    }

    private function formOptions(): array
    {
        return [
            'courses' => AdvancedCourse::query()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('title')
                ->get(['id', 'title', 'thumbnail', 'is_featured']),
            'paths' => AcademicYear::query()
                ->where('is_active', true)
                ->orderBy('order')
                ->orderBy('name')
                ->get(['id', 'name', 'thumbnail']),
            'sourceTypes' => [
                HomepageSlider::SOURCE_COURSE => 'من كورس',
                HomepageSlider::SOURCE_PATH => 'من مسار تعليمي',
                HomepageSlider::SOURCE_CUSTOM => 'مخصص (نص وصورة يدوياً)',
            ],
        ];
    }

    private function validateSlide(Request $request, ?HomepageSlider $existing = null): array
    {
        $type = $request->input('source_type', $existing?->source_type ?? HomepageSlider::SOURCE_COURSE);

        $rules = [
            'source_type' => 'required|in:'.HomepageSlider::SOURCE_COURSE.','.HomepageSlider::SOURCE_PATH.','.HomepageSlider::SOURCE_CUSTOM,
            'kicker' => 'nullable|string|max:120',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:4000',
            'primary_label' => 'nullable|string|max:120',
            'primary_url' => 'nullable|string|max:500',
            'secondary_label' => 'nullable|string|max:120',
            'secondary_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:12288',
            'remove_image' => 'boolean',
            'advanced_course_id' => 'nullable|exists:advanced_courses,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ];

        if ($type === HomepageSlider::SOURCE_COURSE) {
            $rules['advanced_course_id'] = 'required|exists:advanced_courses,id';
        } elseif ($type === HomepageSlider::SOURCE_PATH) {
            $rules['academic_year_id'] = 'required|exists:academic_years,id';
        } else {
            $rules['title'] = 'required|string|max:255';
            $rules['primary_url'] = 'required|string|max:500';
            if (! $request->hasFile('image') && ! $existing?->image_path) {
                $rules['image'] = 'required|image|mimes:jpeg,jpg,png,webp,gif|max:12288';
            }
        }

        $validated = $request->validate($rules, [
            'advanced_course_id.required' => 'اختر الكورس المرتبط بالسلايدر.',
            'academic_year_id.required' => 'اختر المسار التعليمي.',
            'title.required' => 'العنوان مطلوب للسلايدر المخصص.',
            'primary_url.required' => 'رابط الزر الرئيسي مطلوب للسلايدر المخصص.',
            'image.required' => 'ارفع صورة خلفية للسلايدر المخصص.',
        ]);

        if ($type === HomepageSlider::SOURCE_COURSE) {
            $validated['academic_year_id'] = null;
        } elseif ($type === HomepageSlider::SOURCE_PATH) {
            $validated['advanced_course_id'] = null;
        } else {
            $validated['advanced_course_id'] = null;
            $validated['academic_year_id'] = null;
        }

        return $validated;
    }

    private function handleImageUpload(Request $request, ?string $oldPath, ?HomepageSlider $existing = null): ?string
    {
        if (! $request->hasFile('image')) {
            return $oldPath;
        }

        try {
            return HomepageSliderImageStorage::store($request->file('image'), $oldPath);
        } catch (Throwable $e) {
            report($e);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'image' => 'تعذّر رفع الصورة. تحقق من إعدادات التخزين.',
            ]);
        }
    }
}
