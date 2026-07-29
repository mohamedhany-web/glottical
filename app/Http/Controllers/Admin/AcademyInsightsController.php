<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AcademyInsightsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AcademyInsightsController extends Controller
{
    public function index(AcademyInsightsService $insights): View
    {
        $snapshot = $insights->snapshot();

        return view('admin.academy-insights.index', [
            'snapshot' => $snapshot,
            'pollUrl' => route('admin.academy-insights.poll'),
        ]);
    }

    public function poll(AcademyInsightsService $insights): JsonResponse
    {
        return response()->json($insights->snapshot());
    }
}
