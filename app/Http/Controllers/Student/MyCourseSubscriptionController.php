<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentCourseEnrollment;
use App\Services\CourseSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyCourseSubscriptionController extends Controller
{
    /**
     * اشتراكات الكورسات الشهرية للطالب — تاريخ الانتهاء والتجديد.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $enrollments = StudentCourseEnrollment::query()
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('access_type', 'subscription')
                    ->orWhere('enrollment_type', 'subscription')
                    ->orWhereHas('course', fn ($c) => $c->where('billing_mode', CourseSubscriptionService::BILLING_MONTHLY));
            })
            ->with(['course.instructor', 'course.courseCategory'])
            ->orderByRaw('CASE WHEN expires_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expires_at')
            ->get();

        $active = $enrollments->filter(fn (StudentCourseEnrollment $e) => $e->subscriptionIsActive());
        $expired = $enrollments->filter(fn (StudentCourseEnrollment $e) => $e->subscriptionIsExpired());
        $expiringSoon = $active->filter(fn (StudentCourseEnrollment $e) => $e->subscriptionExpiringSoon(7));

        return view('student.my-course-subscriptions.index', [
            'enrollments' => $enrollments,
            'active' => $active,
            'expired' => $expired,
            'expiringSoon' => $expiringSoon,
            'stats' => [
                'total' => $enrollments->count(),
                'active' => $active->count(),
                'expired' => $expired->count(),
                'expiring_soon' => $expiringSoon->count(),
            ],
        ]);
    }
}
