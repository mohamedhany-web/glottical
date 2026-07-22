<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Services\CourseSubscriptionService;
use Illuminate\View\View;

class GroupsController extends Controller
{
    public function index(): View
    {
        $groupCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('delivery_type')
                    ->orWhere('delivery_type', CourseSubscriptionService::DELIVERY_GROUP);
            })
            ->with(['instructor:id,name', 'courseCategory:id,name'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $oneToOneCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
            ->with(['instructor:id,name', 'courseCategory:id,name'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $groupCount = AdvancedCourse::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('delivery_type')
                    ->orWhere('delivery_type', CourseSubscriptionService::DELIVERY_GROUP);
            })
            ->count();

        $oneToOneCount = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
            ->count();

        return view('public.groups', compact(
            'groupCourses',
            'oneToOneCourses',
            'groupCount',
            'oneToOneCount'
        ));
    }
}
