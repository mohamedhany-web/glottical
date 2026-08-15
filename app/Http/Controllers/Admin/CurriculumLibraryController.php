<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumLibraryCategory;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryItemFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CurriculumLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = CurriculumLibraryItem::with('category')->ordered();
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }
        $items = $query->paginate(20)->withQueryString();
        $categories = CurriculumLibraryCategory::ordered()->get();

        return view('admin.curriculum-library.index', compact('items', 'categories'));
    }

    public function categories()
    {
        $categories = CurriculumLibraryCategory::withCount(['items', 'restrictedUsers'])->ordered()->get();
        return view('admin.curriculum-library.categories', compact('categories'));
    }

    public function createCategory()
    {
        $users = User::where('role', 'student')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.curriculum-library.categories-form', ['category' => null, 'users' => $users]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:curriculum_library_categories,slug',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_restricted' => 'nullable|boolean',
            'restricted_user_ids' => 'nullable|array',
            'restricted_user_ids.*' => 'exists:users,id',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_restricted'] = $request->boolean('is_restricted');
        $validated['order'] = (int) ($validated['order'] ?? 0);

        $restrictIds = array_values(array_unique(array_filter(array_map('intval', $request->input('restricted_user_ids', []) ?? []))));

        $category = CurriculumLibraryCategory::create($validated);

        if ($validated['is_restricted']) {
            $category->restrictedUsers()->sync($restrictIds);
        }

        return redirect()->route('admin.curriculum-library.categories')->with('success', 'تم إنشاء التصنيف بنجاح.');
    }

    public function editCategory(CurriculumLibraryCategory $category)
    {
        $category->load('restrictedUsers');
        $users = User::where('role', 'student')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.curriculum-library.categories-form', ['category' => $category, 'users' => $users]);
    }

    public function updateCategory(Request $request, CurriculumLibraryCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:curriculum_library_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_restricted' => 'nullable|boolean',
            'restricted_user_ids' => 'nullable|array',
            'restricted_user_ids.*' => 'exists:users,id',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_restricted'] = $request->boolean('is_restricted');
        $validated['order'] = (int) ($validated['order'] ?? 0);

        $restrictIds = array_values(array_unique(array_filter(array_map('intval', $request->input('restricted_user_ids', []) ?? []))));

        $category->update($validated);

        if ($validated['is_restricted']) {
            $category->restrictedUsers()->sync($restrictIds);
        } else {
            $category->restrictedUsers()->detach();
        }

        return redirect()->route('admin.curriculum-library.categories')->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    public function destroyCategory(CurriculumLibraryCategory $category)
    {
        $category->restrictedUsers()->detach();
        $category->items()->update(['category_id' => null]);
        $category->delete();
        return redirect()->route('admin.curriculum-library.categories')->with('success', 'تم حذف التصنيف.');
    }

    public function createItem()
    {
        $categories = CurriculumLibraryCategory::active()->ordered()->get();
        return view('admin.curriculum-library.items-form', ['item' => null, 'categories' => $categories]);
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:curriculum_library_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:curriculum_library_items,slug',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'grade_level' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:100',
            'language' => 'nullable|string|in:ar,en,fr',
            'item_type' => 'nullable|string|in:presentation,assignment',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_free_preview' => 'nullable|boolean',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_free_preview'] = $request->boolean('is_free_preview');
        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['language'] = $validated['language'] ?? 'ar';
        $validated['item_type'] = $validated['item_type'] ?? 'presentation';
        $item = CurriculumLibraryItem::create($validated);

        return redirect()->route('admin.curriculum-library.items.structure', $item)
            ->with('success', 'تم إضافة المنهج. أضف الأقسام والمواد أدناه (التخزين على Cloudflare R2).');
    }

    public function editItem(CurriculumLibraryItem $item)
    {
        $item->load('files');
        $categories = CurriculumLibraryCategory::active()->ordered()->get();
        return view('admin.curriculum-library.items-form', ['item' => $item, 'categories' => $categories]);
    }

    public function updateItem(Request $request, CurriculumLibraryItem $item)
    {
        Log::info('curriculum_library.updateItem: start', [
            'item_id' => $item->id,
            'item_slug' => $item->slug,
            'method' => $request->method(),
            'has_method_override' => $request->has('_method'),
            'override_value' => $request->input('_method'),
            'content_type' => $request->header('Content-Type'),
            'has_files' => $request->files->count() > 0,
            'user_id' => auth()->id(),
        ]);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:curriculum_library_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:curriculum_library_items,slug,' . $item->id,
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'grade_level' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:100',
            'language' => 'nullable|string|in:ar,en,fr',
            'item_type' => 'nullable|string|in:presentation,assignment',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_free_preview' => 'nullable|boolean',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_free_preview'] = $request->boolean('is_free_preview');
        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['language'] = $validated['language'] ?? 'ar';
        $validated['item_type'] = $validated['item_type'] ?? 'presentation';
        $item->update($validated);

        return redirect()->route('admin.curriculum-library.index')->with('success', 'تم تحديث بيانات المنهج.');
    }

    public function destroyItem(CurriculumLibraryItem $item)
    {
        foreach ($item->sections()->whereNull('parent_id')->get() as $root) {
            $root->deleteWithStorage();
        }
        foreach ($item->files as $file) {
            $diskName = $file->storage_disk ?: 'public';
            if ($file->path && Storage::disk($diskName)->exists($file->path)) {
                Storage::disk($diskName)->delete($file->path);
            }
        }
        $item->delete();
        return redirect()->route('admin.curriculum-library.index')->with('success', 'تم حذف عنصر المنهج.');
    }

    public function destroyFile(CurriculumLibraryItem $item, CurriculumLibraryItemFile $file)
    {
        if ($file->curriculum_library_item_id !== $item->id) {
            abort(404);
        }
        $diskName = $file->storage_disk ?: 'public';
        if ($file->path && Storage::disk($diskName)->exists($file->path)) {
            Storage::disk($diskName)->delete($file->path);
        }
        $file->delete();
        return redirect()->route('admin.curriculum-library.items.edit', $item)->with('success', 'تم حذف الملف.');
    }
}
