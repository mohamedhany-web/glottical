<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StudentLearnHubService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearnHubController extends Controller
{
    public function index(Request $request, StudentLearnHubService $learn): View
    {
        $payload = $learn->hub(
            $request->user(),
            (string) $request->query('tab', 'private'),
            [
                'q' => $request->query('q'),
                'subject_id' => $request->query('subject_id'),
                'year_id' => $request->query('year_id'),
                'type' => $request->query('type'),
                'bookable' => $request->query('bookable'),
            ]
        );

        return view('student.learn.index', $payload);
    }

    public function teacher(Request $request, User $instructor, StudentLearnHubService $learn): View
    {
        $payload = $learn->teacherPage($request->user(), $instructor);

        return view('student.learn.teacher', $payload);
    }
}
