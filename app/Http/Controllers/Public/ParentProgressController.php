<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ParentProgressReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentProgressController extends Controller
{
    public function show(Request $request, ParentProgressReportService $reports): View
    {
        $studentId = (int) $request->query('student_id', 0);
        $result = null;

        if ($studentId > 0) {
            $result = $reports->lookup($studentId);
        }

        return view('public.parent-progress', [
            'studentId' => $studentId > 0 ? $studentId : null,
            'result' => $result,
        ]);
    }
}
