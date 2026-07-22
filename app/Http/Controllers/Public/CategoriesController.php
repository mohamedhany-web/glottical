<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Services\NavbarCategoryResolver;
use Illuminate\View\View;

class CategoriesController extends Controller
{
    public function index(): View
    {
        $categories = NavbarCategoryResolver::catalogPageItems(48);

        $featuredCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->with(['instructor:id,name', 'courseCategory:id,name'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('public.categories', compact('categories', 'featuredCourses'));
    }
}
